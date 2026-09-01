<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pessoa;
use App\Models\Produto;
use App\Models\Categoria;
use App\Models\PedidoVenda;
use App\Models\ItemPedido;
use App\Models\PedidoVendaParcela;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Helpers\AuthHelper;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 10);
        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $tenantId = session('tenant_id');
        $locationId = session('location_id');
        $userLocations = session('user_locations', []);

        $locationIds = [];
        if ($tenantId) {
            $locationIds = collect($userLocations)
                ->where('tenant_id', $tenantId)
                ->pluck('location_id')
                ->toArray();
        } elseif ($locationId) {
            $locationIds = [$locationId];
        }

        $query = PedidoVenda::with(['cliente', 'itens', 'parcelas'])
            ->where('ativo', true);
        $paidStatuses = ['pago', 'paga', 'cancelado', 'cancelada'];

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        if (!empty($locationIds)) {
            $query->where(function ($q) use ($locationIds) {
                $q->whereIn('location_id', $locationIds)
                    ->orWhereNull('location_id');
            });
        }

        $query->orderBy('data_pedido', 'desc');

        // Filtros
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('cliente', function ($q2) use ($search) {
                    $q2->where('nome', 'ILIKE', "%{$search}%");
                })->orWhere('id', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $statusPagamento = $request->get('status');

            if ($statusPagamento === 'pendente') {
                $query->whereDoesntHave('parcelas', function ($q) use ($paidStatuses) {
                    $q->whereNotNull('pago_em')
                        ->orWhereIn(DB::raw('lower(status)'), $paidStatuses);
                });
            } elseif ($statusPagamento === 'pagamento_parcial') {
                $query->whereHas('parcelas', function ($q) use ($paidStatuses) {
                    $q->whereNotNull('pago_em')
                        ->orWhereIn(DB::raw('lower(status)'), $paidStatuses);
                })->whereHas('parcelas', function ($q) use ($paidStatuses) {
                    $q->whereNull('pago_em')
                        ->where(function ($subQ) use ($paidStatuses) {
                            $subQ->whereNull('status')
                                ->orWhereNotIn(DB::raw('lower(status)'), $paidStatuses);
                        });
                });
            } elseif ($statusPagamento === 'quitada') {
                $query->whereHas('parcelas')
                    ->whereDoesntHave('parcelas', function ($q) use ($paidStatuses) {
                        $q->whereNull('pago_em')
                            ->where(function ($subQ) use ($paidStatuses) {
                                $subQ->whereNull('status')
                                    ->orWhereNotIn(DB::raw('lower(status)'), $paidStatuses);
                            });
                    });
            }
        }

        if ($request->filled('pagamento')) {
            $query->where('forma_pagamento', $request->get('pagamento'));
        }

        if ($request->filled('periodo')) {
            $periodo = $request->get('periodo');
            switch ($periodo) {
                case 'hoje':
                    $query->whereDate('data_pedido', today());
                    break;
                case 'semana':
                    $query->whereBetween('data_pedido', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'mes':
                    $query->whereMonth('data_pedido', now()->month)
                        ->whereYear('data_pedido', now()->year);
                    break;
            }
        }

        $pedidosStats = (clone $query)->get();
        $pedidos = $query->paginate($perPage)->appends($request->query());

        // Formatar dados para a view
        $sales = $pedidos->through(function ($pedido) use ($paidStatuses) {
            $clienteNome = $pedido->cliente ? $pedido->cliente->nome : 'Cliente não informado';
            $quantidadeItens = $pedido->itens->sum('quantidade');
            $quantidadeProdutos = $pedido->itens->count();
            $totalParcelas = $pedido->parcelas->count();
            $parcelasPagas = $pedido->parcelas->filter(function ($parcela) use ($paidStatuses) {
                return !empty($parcela->pago_em) || in_array(strtolower((string) ($parcela->status ?? '')), $paidStatuses, true);
            })->count();

            if ($totalParcelas > 0 && $parcelasPagas === $totalParcelas) {
                $statusPagamento = 'quitada';
            } elseif ($parcelasPagas > 0) {
                $statusPagamento = 'pagamento_parcial';
            } else {
                $statusPagamento = 'pendente';
            }

            // Gerar número da venda baseado no ID
            $numero = 'VD-' . date('Y', strtotime($pedido->data_pedido)) . '-' . str_pad($pedido->id, 4, '0', STR_PAD_LEFT);

            // Mapear status
            $statusMap = [
                'aberto' => 'pendente',
                'faturado' => 'finalizada',
                'cancelado' => 'cancelada'
            ];
            $status = $statusMap[$pedido->status] ?? $pedido->status;

            // Forma de pagamento do banco
            $formaPagamento = $pedido->forma_pagamento ?? 'Não informado';
            $parcelas = 1; // Pode ser adicionado na tabela se necessário

            return [
                'id' => $pedido->id,
                'numero' => $numero,
                'cliente' => $clienteNome,
                'data' => $pedido->data_pedido->format('Y-m-d'),
                'total' => (float) $pedido->valor_total,
                'status' => $status,
                'status_pagamento' => $statusPagamento,
                'forma_pagamento' => $formaPagamento,
                'parcelas' => $parcelas,
                'produtos' => $quantidadeProdutos,
                'itens_total' => $quantidadeItens
            ];
        });

        // Calcular estatísticas
        $vendasHoje = $pedidosStats->filter(function ($pedido) {
            return $pedido->data_pedido->format('Y-m-d') === today()->format('Y-m-d');
        })->sum('valor_total');

        $totalVendas = $pedidosStats->count();
        $ticketMedio = $totalVendas > 0 ? $pedidosStats->sum('valor_total') / $totalVendas : 0;
        $pendentes = $pedidosStats->where('status', 'aberto')->count();

        return view('sales.index', compact('sales', 'vendasHoje', 'totalVendas', 'ticketMedio', 'pendentes', 'perPage'));
    }

    /**
     * Quantidade de produtos retornados por página no catálogo da tela de venda.
     */
    private const PRODUCTS_PAGE_SIZE = 20;

    /**
     * Ids das localizações que o usuário logado pode visualizar, considerando tenant/location da sessão.
     */
    private function currentLocationIds(): array
    {
        $tenantId = session('tenant_id');
        $locationId = session('location_id');
        $userLocations = session('user_locations', []);

        if ($tenantId) {
            return collect($userLocations)
                ->where('tenant_id', $tenantId)
                ->pluck('location_id')
                ->toArray();
        }

        if ($locationId) {
            return [$locationId];
        }

        return [];
    }

    /**
     * Query base de produtos ativos disponíveis para venda, já filtrada por tenant/location.
     */
    private function saleableProductsQuery()
    {
        $tenantId = session('tenant_id');
        $locationIds = $this->currentLocationIds();

        $query = Produto::with([
            'categoria',
            'images' => function ($query) {
                $query->whereNull('deleted_at')
                    ->orderByDesc('principal')
                    ->orderBy('ordem')
                    ->orderBy('id');
            },
        ])
            ->where('ativo', true);

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        if (!empty($locationIds)) {
            $query->where(function ($q) use ($locationIds) {
                $q->whereIn('location_id', $locationIds)
                    ->orWhereNull('location_id');
            });
        }

        return $query;
    }

    /**
     * Converte um Produto no array usado pelo catálogo/carrinho da tela de venda.
     */
    private function mapProductForCatalog(Produto $product): array
    {
        $stockFromAttributes = null;

        if (is_array($product->atributos) && array_key_exists('estoque', $product->atributos)) {
            $stockFromAttributes = (int) $product->atributos['estoque'];
        } else {
            $stockColumn = $product->getAttribute('estoque');
            if (!is_null($stockColumn)) {
                $stockFromAttributes = (int) $stockColumn;
            }
        }

        $primaryImage = $product->images->first();
        $imageUrl = null;

        if ($primaryImage && $primaryImage->caminho_arquivo) {
            $storagePath = ltrim($primaryImage->caminho_arquivo, '/');

            if (Storage::disk('public')->exists($storagePath)) {
                $imageUrl = asset('storage/' . $storagePath);
            }
        }

        $atributos = [];
        if (is_array($product->atributos)) {
            foreach ($product->atributos as $key => $value) {
                $key = trim((string) $key);
                if ($key === '') {
                    continue;
                }
                $atributos[$key] = $value;
            }
        }

        return [
            'id' => $product->id,
            'nome' => $product->nome,
            'marca' => $product->marca,
            'categoria' => optional($product->categoria)->descricao ?? 'Sem categoria',
            'categoria_id' => $product->categoria_id,
            'preco' => $product->preco_venda !== null ? (float) $product->preco_venda : null,
            'stock' => $stockFromAttributes,
            'image_url' => $imageUrl,
            'atributos' => $atributos,
        ];
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $firstPage = $this->searchProductsData();

        $categoriesQuery = Categoria::where('ativo', true);
        if ($tenantId = session('tenant_id')) {
            $categoriesQuery->where('tenant_id', $tenantId);
        }
        $categories = $categoriesQuery->orderBy('descricao')
            ->get(['id', 'descricao']);

        $canApplyDiscountWithoutAuth = AuthHelper::canApplyDiscountWithoutAuthorization();

        $preselectedClient = null;
        $clienteId = (int) $request->query('cliente_id');

        if ($clienteId > 0) {
            $locationIds = $this->currentLocationIds();
            $locationId = session('location_id');

            $clientQuery = Pessoa::query()
                ->where('id', $clienteId)
                ->where('ativo', true);

            if ($tenantId) {
                $clientQuery->where('tenant_id', $tenantId);
            }

            if (!empty($locationIds)) {
                $clientQuery->whereIn('location_id', $locationIds);
            } elseif ($locationId) {
                $clientQuery->where('location_id', $locationId);
            }

            $cliente = $clientQuery->first();

            if ($cliente) {
                $preselectedClient = [
                    'id' => $cliente->id,
                    'nome' => $cliente->nome,
                    'cpf' => $cliente->cpf_formatado,
                    'telefone' => $cliente->telefone_formatado,
                    'email' => $cliente->email,
                    'endereco' => $cliente->endereco_completo,
                ];
            }
        }

        return view('sales.create', [
            'initialProducts' => $firstPage['data'],
            'productsTotal' => $firstPage['meta']['total'],
            'productsHasMore' => $firstPage['meta']['has_more'],
            'categories' => $categories,
            'canApplyDiscountWithoutAuth' => $canApplyDiscountWithoutAuth,
            'preselectedClient' => $preselectedClient,
        ]);
    }

    /**
     * Monta o payload paginado de produtos (usado tanto no carregamento inicial da tela
     * quanto no endpoint de busca AJAX), filtrando por estoque, termo de busca e categoria.
     */
    private function searchProductsData(?string $search = null, ?int $categoryId = null, int $page = 1): array
    {
        $page = max(1, $page);
        $perPage = self::PRODUCTS_PAGE_SIZE;
        $like = DB::connection()->getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';

        $baseQuery = $this->saleableProductsQuery();

        if ($categoryId) {
            $baseQuery->where('categoria_id', $categoryId);
        }

        if ($search) {
            $baseQuery->where(function ($q) use ($search, $like) {
                $q->where('nome', $like, "%{$search}%")
                    ->orWhere('marca', $like, "%{$search}%");
            });
        }

        // Paginação é feita no banco (não carregamos o catálogo inteiro em memória).
        // O estoque mora em uma coluna dinâmica (JSON `atributos`), então o filtro de
        // "sem estoque" só é possível em memória, dentro do lote já paginado.
        $total = (clone $baseQuery)->count();

        $items = (clone $baseQuery)->orderBy('nome')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get()
            ->map(fn(Produto $product) => $this->mapProductForCatalog($product))
            ->filter(fn(array $product) => $product['stock'] === null || $product['stock'] > 0)
            ->values();

        return [
            'data' => $items->toArray(),
            'meta' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'has_more' => ($page * $perPage) < $total,
            ],
        ];
    }

    /**
     * Endpoint AJAX de busca/paginação de produtos para o catálogo da tela de venda.
     */
    public function searchProducts(Request $request)
    {
        $search = trim((string) $request->get('q', ''));
        $categoryId = $request->filled('category_id') ? (int) $request->get('category_id') : null;
        $page = (int) $request->get('page', 1);

        return response()->json(
            $this->searchProductsData($search !== '' ? $search : null, $categoryId, $page)
        );
    }

    /**
     * Autoriza desconto com credenciais de supervisor (admin/gerente).
     */
    public function authorizeDiscount(Request $request)
    {
        if (AuthHelper::canApplyDiscountWithoutAuthorization()) {
            return response()->json([
                'authorized' => true,
                'token' => null,
                'message' => 'Usuário já possui permissão para conceder desconto.',
            ]);
        }

        try {
            $validated = $request->validate([
                'supervisor_email' => 'required|email',
                'supervisor_password' => 'required|string',
                'desconto_valor' => 'required|numeric|gt:0',
                'desconto_percentual' => 'nullable|numeric|min:0|max:100',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Dados inválidos',
                'errors' => $e->errors(),
            ], 422);
        }

        $supervisor = User::where('email', $validated['supervisor_email'])
            ->where('status', 1)
            ->first();

        if (!$supervisor || !Hash::check($validated['supervisor_password'], $supervisor->password)) {
            return response()->json([
                'message' => 'E-mail ou senha do supervisor inválidos.',
            ], 422);
        }

        if (!AuthHelper::userHasPrivilegedProfile($supervisor)) {
            return response()->json([
                'message' => 'Este usuário não possui perfil para autorizar descontos.',
            ], 422);
        }

        $discountValue = round((float) $validated['desconto_valor'], 2);
        $discountPercent = round((float) ($validated['desconto_percentual'] ?? 0), 2);
        $token = (string) Str::uuid();
        $ttlMinutes = (int) config('sales.discount_authorization_ttl_minutes', 30);

        Cache::put($this->discountAuthorizationCacheKey($token), [
            'authorized_by' => $supervisor->id,
            'authorized_by_name' => $supervisor->name,
            'seller_id' => auth()->id(),
            'desconto_valor' => $discountValue,
            'desconto_percentual' => $discountPercent,
        ], now()->addMinutes($ttlMinutes));

        return response()->json([
            'authorized' => true,
            'token' => $token,
            'authorized_by' => $supervisor->id,
            'authorized_by_name' => $supervisor->name,
            'message' => 'Desconto autorizado com sucesso.',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validação dos dados
            $validated = $request->validate([
                'cliente_id' => 'required|integer|exists:pessoa,id',
                'produtos' => 'required|array|min:1',
                'produtos.*.produto_id' => 'required|integer|exists:produto,id',
                'produtos.*.quantidade' => 'required|integer|min:1',
                'produtos.*.preco_unitario' => 'required|numeric|gt:0',
                'produtos.*.subtotal' => 'required|numeric|gt:0',
                'pagamentos' => 'required|array|min:1',
                'pagamentos.*.forma_pagamento' => 'required|string|in:dinheiro,cartao_debito,cartao_credito,crediario,pix',
                'pagamentos.*.valor' => 'required|numeric|gt:0',
                'pagamentos.*.parcelas' => 'required|integer|min:1|max:12',
                'pagamentos.*.primeiro_vencimento' => 'nullable|date',
                'desconto_percentual' => 'nullable|numeric|min:0|max:100',
                'desconto_valor' => 'nullable|numeric|min:0',
                'desconto_autorizacao_token' => 'nullable|string|max:100',
                'subtotal' => 'required|numeric|min:0',
                'total' => 'required|numeric|min:0',
                'observacoes' => 'nullable|string|max:1000'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Dados inválidos',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        // Verificar se o cliente está ativo
        $pessoa = \App\Models\Pessoa::findOrFail($validated['cliente_id']);
        if (!$pessoa->ativo) {
            return response()->json([
                'message' => 'O cliente selecionado está inativo.'
            ], 422);
        }

        // Verificar estoque dos produtos
        foreach ($validated['produtos'] as $item) {
            $product = Produto::findOrFail($item['produto_id']);

            // Verificar se o produto está ativo
            if (!$product->ativo) {
                return response()->json([
                    'message' => "O produto '{$product->nome}' está inativo."
                ], 422);
            }

            // Verificar estoque se houver controle
            $stock = null;
            if (is_array($product->atributos) && array_key_exists('estoque', $product->atributos)) {
                $stock = (int) $product->atributos['estoque'];
            } else {
                $stockColumn = $product->getAttribute('estoque');
                if (!is_null($stockColumn)) {
                    $stock = (int) $stockColumn;
                }
            }

            if ($stock !== null && $item['quantidade'] > $stock) {
                return response()->json([
                    'message' => "Estoque insuficiente para o produto '{$product->nome}'. Disponível: {$stock}, Solicitado: {$item['quantidade']}"
                ], 422);
            }
        }

        $discountValue = round((float) ($validated['desconto_valor'] ?? 0), 2);
        $discountPercent = round((float) ($validated['desconto_percentual'] ?? 0), 2);

        if ($discountValue > $validated['subtotal']) {
            return response()->json([
                'message' => 'O desconto não pode ser maior que o subtotal da venda.',
            ], 422);
        }

        $expectedTotal = round($validated['subtotal'] - $discountValue, 2);
        if (abs($expectedTotal - round((float) $validated['total'], 2)) > 0.02) {
            return response()->json([
                'message' => 'Total da venda inconsistente com subtotal e desconto informados.',
            ], 422);
        }

        $discountAuthorizedBy = null;

        if ($discountValue > 0) {
            if (AuthHelper::canApplyDiscountWithoutAuthorization()) {
                $discountAuthorizedBy = auth()->id();
            } else {
                $token = $request->input('desconto_autorizacao_token');
                if (!$token) {
                    return response()->json([
                        'message' => 'É necessária autorização de supervisor para aplicar desconto.',
                    ], 422);
                }

                $authData = Cache::get($this->discountAuthorizationCacheKey($token));
                if (
                    !$authData
                    || (int) ($authData['seller_id'] ?? 0) !== (int) auth()->id()
                    || abs((float) ($authData['desconto_valor'] ?? 0) - $discountValue) > 0.02
                ) {
                    return response()->json([
                        'message' => 'Autorização de desconto inválida ou expirada. Solicite novamente.',
                    ], 422);
                }

                $discountAuthorizedBy = (int) $authData['authorized_by'];
                Cache::forget($this->discountAuthorizationCacheKey($token));
            }
        }

        // Verificar que a soma dos pagamentos é igual ao total da venda
        $totalPagamentos = round(collect($validated['pagamentos'])->sum('valor'), 2);
        $totalVenda = round((float) $validated['total'], 2);
        if (abs($totalPagamentos - $totalVenda) > 0.02) {
            return response()->json([
                'message' => 'A soma das formas de pagamento (R$ ' . number_format($totalPagamentos, 2, ',', '.') . ') deve ser igual ao total da venda (R$ ' . number_format($totalVenda, 2, ',', '.') . ').',
            ], 422);
        }

        foreach ($validated['pagamentos'] as $pagamento) {
            if (($pagamento['forma_pagamento'] ?? null) === 'crediario' && empty($pagamento['primeiro_vencimento'])) {
                return response()->json([
                    'message' => 'Informe o primeiro vencimento para pagamentos no crediário.',
                ], 422);
            }
        }

        // Criar o pedido de venda e seus itens em uma transação
        try {
            DB::beginTransaction();

            $tenantId = session('tenant_id');
            $locationId = session('location_id');
            $userId = auth()->id();

            // Mapear forma de pagamento para nome amigável
            $paymentMethods = [
                'dinheiro' => 'Dinheiro',
                'cartao_debito' => 'Cartão de Débito',
                'cartao_credito' => 'Cartão de Crédito',
                'crediario' => 'Crediário',
                'pix' => 'PIX'
            ];

            // Resumo legível das formas de pagamento (ex: "Dinheiro + PIX")
            $formaPagamentoNome = collect($validated['pagamentos'])
                ->map(fn($p) => $paymentMethods[$p['forma_pagamento']] ?? $p['forma_pagamento'])
                ->unique()
                ->join(' + ');

            // Criar o pedido de venda
            $pedidoVenda = PedidoVenda::create([
                'tenant_id' => $tenantId,
                'location_id' => $locationId,
                'user_id' => $userId,
                'pessoa_cliente_id' => $validated['cliente_id'],
                'status' => 'faturado', // Status inicial como 'faturado' quando finaliza a venda
                'data_pedido' => now(),
                'valor_total' => $validated['total'],
                'desconto_valor' => $discountValue,
                'desconto_percentual' => $discountPercent,
                'desconto_autorizado_por' => $discountAuthorizedBy,
                'forma_pagamento' => $formaPagamentoNome,
                'observacoes' => $validated['observacoes'] ?? null,
                'ativo' => true
            ]);

            // Criar os itens do pedido
            foreach ($validated['produtos'] as $item) {
                ItemPedido::create([
                    'tenant_id' => $tenantId,
                    'location_id' => $locationId,
                    'user_id' => $userId,
                    'pedido_id' => $pedidoVenda->id,
                    'produto_id' => $item['produto_id'],
                    'quantidade' => $item['quantidade'],
                    'preco_unit' => $item['preco_unitario'],
                    'desconto' => 0.00, // Desconto por item (pode ser calculado se necessário)
                    'total_linha' => $item['subtotal'],
                    'ativo' => true
                ]);

                // Atualizar estoque se houver controle
                $product = Produto::find($item['produto_id']);
                if ($product) {
                    $stock = null;
                    if (is_array($product->atributos) && array_key_exists('estoque', $product->atributos)) {
                        $stock = (int) $product->atributos['estoque'];
                        $newStock = max(0, $stock - $item['quantidade']);
                        $atributos = $product->atributos;
                        $atributos['estoque'] = $newStock;
                        $product->update(['atributos' => $atributos]);
                    } else {
                        $stockColumn = $product->getAttribute('estoque');
                        if (!is_null($stockColumn)) {
                            $stock = (int) $stockColumn;
                            $newStock = max(0, $stock - $item['quantidade']);
                            // Se o estoque está em uma coluna separada, você precisaria atualizar aqui
                            // Por enquanto, assumimos que está em atributos
                        }
                    }
                }
            }

            $tz = 'America/Manaus';
            $agora = Carbon::now($tz);

            // Calcular total global de parcelas para numeração contínua
            $totalParcelasGlobal = collect($validated['pagamentos'])->sum(function ($p) {
                return $p['forma_pagamento'] === 'crediario' ? (int) $p['parcelas'] : 1;
            });

            $parcelaNumeroGlobal = 0; // contador global, evita duplicidade no unique constraint

            foreach ($validated['pagamentos'] as $pagamento) {
                $metodoPagamento = $pagamento['forma_pagamento'];
                $valorPagamento  = (float) $pagamento['valor'];
                $nParcelasPag    = (int) $pagamento['parcelas'];
                $nomeMetodo      = $paymentMethods[$metodoPagamento] ?? $metodoPagamento;

                if ($metodoPagamento === 'crediario') {
                    $totalCents   = (int) round($valorPagamento * 100);
                    $parcelaCents = (int) floor($totalCents / $nParcelasPag);
                    $lastCents    = $totalCents - ($parcelaCents * ($nParcelasPag - 1));

                    $primeiroVencimento = !empty($pagamento['primeiro_vencimento'])
                        ? Carbon::parse($pagamento['primeiro_vencimento'], $tz)->startOfDay()
                        : Carbon::today($tz)->addMonthNoOverflow();
                    for ($n = 1; $n <= $nParcelasPag; $n++) {
                        $parcelaNumeroGlobal++;
                        PedidoVendaParcela::create([
                            'tenant_id'       => $tenantId,
                            'location_id'     => $locationId,
                            'pedido_venda_id' => $pedidoVenda->id,
                            'numero_parcela'  => $parcelaNumeroGlobal,
                            'total_parcelas'  => $totalParcelasGlobal,
                            'valor'           => ($n === $nParcelasPag ? $lastCents : $parcelaCents) / 100,
                            'vencimento_em'   => $primeiroVencimento->copy()->addMonthsNoOverflow($n - 1),
                            'status'          => 'aberta',
                            'forma_pagamento' => $nomeMetodo,
                        ]);
                    }
                } else {
                    $parcelaNumeroGlobal++;
                    PedidoVendaParcela::create([
                        'tenant_id'       => $tenantId,
                        'location_id'     => $locationId,
                        'pedido_venda_id' => $pedidoVenda->id,
                        'numero_parcela'  => $parcelaNumeroGlobal,
                        'total_parcelas'  => $totalParcelasGlobal,
                        'valor'           => $valorPagamento,
                        'vencimento_em'   => $agora->toDateString(),
                        'pago_em'         => $agora,
                        'status'          => 'pago',
                        'forma_pagamento' => $nomeMetodo,
                    ]);
                }
            }

            DB::commit();

            // Preparar dados de resposta
            $pedidoVenda->load('itens.produto', 'cliente');

            $responseData = [
                'message' => 'Venda realizada com sucesso!',
                'pedido_id' => $pedidoVenda->id,
                'data' => [
                    'pedido' => $pedidoVenda->toArray(),
                    'itens' => $pedidoVenda->itens->toArray()
                ]
            ];

            // Retornar sucesso
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json($responseData, 201);
            }

            return redirect()->route('sales.index')->with('success', 'Venda realizada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Erro ao processar a venda: ' . $e->getMessage()
                ], 500);
            }

            throw $e;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tenantId = session('tenant_id');
        $tz = 'America/Manaus';

        $pedidoVenda = PedidoVenda::with(['cliente', 'itens.produto'])
            ->where('ativo', true);

        if ($tenantId) {
            $pedidoVenda->where('tenant_id', $tenantId);
        }

        $pedidoVenda = $pedidoVenda->findOrFail($id);

        // Gerar número da venda
        $numero = 'VD-' . date('Y', strtotime($pedidoVenda->data_pedido)) . '-' . str_pad($pedidoVenda->id, 4, '0', STR_PAD_LEFT);

        // Mapear status
        $statusMap = [
            'aberto' => 'pendente',
            'faturado' => 'finalizada',
            'cancelado' => 'cancelada'
        ];
        $status = $statusMap[$pedidoVenda->status] ?? $pedidoVenda->status;

        // Formatar produtos
        $produtos = $pedidoVenda->itens->map(function ($item) {
            return [
                'id' => $item->produto_id,
                'nome' => $item->produto ? $item->produto->nome : 'Produto não encontrado',
                'quantidade' => $item->quantidade,
                'preco_unitario' => (float) $item->preco_unit,
                'desconto' => (float) $item->desconto,
                'subtotal' => (float) $item->total_linha
            ];
        });

        // Calcular subtotal (soma dos itens antes do desconto)
        $subtotal = $pedidoVenda->itens->sum(function ($item) {
            return ($item->preco_unit * $item->quantidade);
        });

        // Total
        $total = (float) $pedidoVenda->valor_total;

        // Desconto total
        $descontoTotal = $this->resolvePedidoDesconto($pedidoVenda, $subtotal, $total);

        // Formatar dados do cliente
        $cliente = null;
        if ($pedidoVenda->cliente) {
            $clienteData = $pedidoVenda->cliente;
            $cliente = [
                'id' => $clienteData->id,
                'nome' => $clienteData->nome,
                'cpf' => $clienteData->cpf_formatado ?? $clienteData->cpf,
                'telefone' => $clienteData->telefone_formatado ?? $clienteData->telefone,
                'email' => $clienteData->email,
                'endereco' => $clienteData->endereco_completo ?? null
            ];
        }

        // Forma de pagamento do banco
        $formaPagamento = $pedidoVenda->forma_pagamento ?? 'Não informado';

        // Formas de pagamento detalhadas a partir das parcelas
        $pagamentosDetalhados = PedidoVendaParcela::where('pedido_venda_id', $pedidoVenda->id)
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->get()
            ->groupBy(fn($parcela) => strtolower(trim((string) $parcela->forma_pagamento)))
            ->map(function ($parcelasGrupo) {
                $primeiraParcela = $parcelasGrupo->first();

                return [
                    'forma_pagamento' => $primeiraParcela->forma_pagamento ?? 'Não informado',
                    'valor' => (float) $parcelasGrupo->sum(fn($parcela) => (float) ($parcela->valor ?? 0)),
                    'parcelas' => (int) $parcelasGrupo->count(),
                ];
            })
            ->values()
            ->toArray();

        $today = Carbon::today($tz)->startOfDay();
        $tomorrow = $today->copy()->addDay();
        $weekEnd = $today->copy()->addDays(7);
        $paidStatuses = ['pago', 'paga', 'cancelado', 'cancelada'];

        $parcelas = PedidoVendaParcela::query()
            ->where('pedido_venda_id', $pedidoVenda->id)
            ->when($tenantId, fn($query) => $query->where('tenant_id', $tenantId))
            ->orderBy('numero_parcela')
            ->get()
            ->map(function ($parcela) use ($today, $tomorrow, $weekEnd, $tz, $paidStatuses) {
                $venc = $parcela->vencimento_em ? Carbon::parse($parcela->vencimento_em, $tz)->startOfDay() : null;
                $isPaid = !empty($parcela->pago_em) || in_array(strtolower((string) ($parcela->status ?? '')), $paidStatuses, true);

                $status = 'em_dia';
                if ($isPaid) {
                    $status = 'paga';
                } elseif ($venc && $venc->lt($today)) {
                    $status = 'vencida';
                } elseif ($venc && $venc->equalTo($today)) {
                    $status = 'vence_hoje';
                } elseif ($venc && $venc->gte($tomorrow) && $venc->lte($weekEnd)) {
                    $status = 'vence_semana';
                }

                $diasAtraso = 0;
                if (!$isPaid && $venc && $venc->lt($today)) {
                    $diasAtraso = $venc->diffInDays($today);
                }

                $valor = (float) ($parcela->valor ?? 0);
                $valorRecebido = (float) ($parcela->valor_recebido ?? 0);
                $juros = 0.0;

                return [
                    'id' => $parcela->id,
                    'parcela' => (int) $parcela->numero_parcela . '/' . (int) $parcela->total_parcelas,
                    'numero_parcela' => (int) $parcela->numero_parcela,
                    'total_parcelas' => (int) $parcela->total_parcelas,
                    'valor_parcela' => $valor,
                    'valor_atualizado' => $valor + $juros,
                    'valor_recebido' => $valorRecebido,
                    'juros' => $juros,
                    'vencimento' => $venc ? $venc->toDateString() : null,
                    'pago_em' => $parcela->pago_em ? Carbon::parse($parcela->pago_em, $tz) : null,
                    'status' => $status,
                    'status_original' => $parcela->status,
                    'dias_atraso' => $diasAtraso,
                    'forma_pagamento' => $parcela->forma_pagamento,
                ];
            });

        // Informações do tenant/empresa para detalhe da venda / WhatsApp
        $tenant = AuthHelper::tenant();
        $nomeEmpresa = null;
        if (is_object($tenant) && property_exists($tenant, 'trade_name')) {
            $nomeEmpresa = trim((string) ($tenant->trade_name ?? ($tenant->name ?? '')));
        } elseif (is_array($tenant)) {
            $nomeEmpresa = trim((string) ($tenant['trade_name'] ?? ($tenant['name'] ?? '')));
        }
        $nomeEmpresa = $nomeEmpresa !== '' ? $nomeEmpresa : AuthHelper::tenantCompanyName();

        $pixDados = AuthHelper::tenantPixData();
        $pixFooter = [
            'chave' => $pixDados ? trim((string) ($pixDados->chave ?? '')) : '',
            'nome_titular' => $pixDados ? trim((string) ($pixDados->nome_titular ?? '')) : '',
            'banco' => $pixDados ? trim((string) ($pixDados->banco ?? '')) : '',
            'tipo_chave' => $pixDados ? trim((string) ($pixDados->tipo_chave ?? '')) : '',
        ];

        $sale = [
            'id' => $pedidoVenda->id,
            'numero' => $numero,
            'data' => $pedidoVenda->data_pedido->format('Y-m-d'),
            'data_formatada' => $pedidoVenda->data_pedido->format('d/m/Y'),
            'cliente' => $cliente,
            'produtos' => $produtos,
            'subtotal' => $subtotal,
            'desconto' => $descontoTotal,
            'total' => $total,
            'forma_pagamento' => $formaPagamento,
            'pagamentos' => $pagamentosDetalhados,
            'parcelas' => $parcelas->count() > 0 ? $parcelas->count() : 1,
            'valor_parcela' => $parcelas->count() > 0 ? (float) $parcelas->first()['valor_parcela'] : $total,
            'valor_recebido' => $parcelas->count() > 0 ? (float) $parcelas->first()['valor_recebido'] : ($totalRecebido ?? 0),
            'parcelas_detalhes' => $parcelas,
            'status' => $status,
            'status_original' => $pedidoVenda->status,
            'observacoes' => $pedidoVenda->observacoes,
            'created_at' => $pedidoVenda->created_at,
            'updated_at' => $pedidoVenda->updated_at,
            'nome_empresa' => $nomeEmpresa,
            'pix_footer' => $pixFooter,
        ];

        return view('sales.show', compact('sale'));
    }

    /**
     * Gerar PDF da venda para impressão
     */
    public function print(string $id)
    {
        $tenantId = session('tenant_id');

        $pedidoVenda = PedidoVenda::with(['cliente', 'itens.produto'])
            ->where('ativo', true);

        if ($tenantId) {
            $pedidoVenda->where('tenant_id', $tenantId);
        }

        $pedidoVenda = $pedidoVenda->findOrFail($id);

        // Gerar número da venda
        $numero = 'VD-' . date('Y', strtotime($pedidoVenda->data_pedido)) . '-' . str_pad($pedidoVenda->id, 4, '0', STR_PAD_LEFT);

        // Mapear status
        $statusMap = [
            'aberto' => 'pendente',
            'faturado' => 'finalizada',
            'cancelado' => 'cancelada'
        ];
        $status = $statusMap[$pedidoVenda->status] ?? $pedidoVenda->status;

        // Formatar produtos
        $produtos = $pedidoVenda->itens->map(function ($item) {
            return [
                'id' => $item->produto_id,
                'nome' => $item->produto ? $item->produto->nome : 'Produto não encontrado',
                'quantidade' => $item->quantidade,
                'preco_unitario' => (float) $item->preco_unit,
                'desconto' => (float) $item->desconto,
                'subtotal' => (float) $item->total_linha
            ];
        });

        // Calcular subtotal
        $subtotal = $pedidoVenda->itens->sum(function ($item) {
            return ($item->preco_unit * $item->quantidade);
        });

        // Total
        $total = (float) $pedidoVenda->valor_total;

        // Desconto total
        $descontoTotal = $this->resolvePedidoDesconto($pedidoVenda, $subtotal, $total);

        // Formatar dados do cliente
        $cliente = null;
        if ($pedidoVenda->cliente) {
            $clienteData = $pedidoVenda->cliente;
            $cliente = [
                'id' => $clienteData->id,
                'nome' => $clienteData->nome,
                'cpf' => $clienteData->cpf_formatado ?? $clienteData->cpf,
                'telefone' => $clienteData->telefone_formatado ?? $clienteData->telefone,
                'email' => $clienteData->email,
                'endereco' => $clienteData->endereco_completo ?? null
            ];
        }

        // Dados do tenant (empresa)
        $tenant = AuthHelper::tenant();
        $logoUrl = AuthHelper::tenantLogoUrl();
        $logoBase64 = null;

        // Converter logo para base64 para garantir que apareça no PDF
        if (!empty($logoUrl)) {
            try {
                // Tentar buscar a imagem via HTTP usando Http facade do Laravel
                $response = \Illuminate\Support\Facades\Http::timeout(10)
                    ->withOptions(['verify' => false])
                    ->get($logoUrl);

                if ($response->successful()) {
                    $imageContent = $response->body();
                    if (!empty($imageContent)) {
                        $imageInfo = @getimagesizefromstring($imageContent);
                        if ($imageInfo !== false) {
                            $mimeType = $imageInfo['mime'];
                            $logoBase64 = 'data:' . $mimeType . ';base64,' . base64_encode($imageContent);
                        }
                    }
                } else {
                    // Fallback: tentar com file_get_contents
                    $imageContent = @file_get_contents($logoUrl);
                    if ($imageContent !== false && !empty($imageContent)) {
                        $imageInfo = @getimagesizefromstring($imageContent);
                        if ($imageInfo !== false) {
                            $mimeType = $imageInfo['mime'];
                            $logoBase64 = 'data:' . $mimeType . ';base64,' . base64_encode($imageContent);
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::warning('Erro ao converter logo para base64: ' . $e->getMessage(), [
                    'logo_url' => $logoUrl
                ]);
            }
        }

        $tenantData = [
            'nome' => $tenant && property_exists($tenant, 'name') ? $tenant->name : 'Empresa',
            'logo_url' => $logoUrl,
            'logo_base64' => $logoBase64,
            'cnpj' => $tenant && property_exists($tenant, 'cnpj') ? $tenant->cnpj : null,
            'telefone' => $tenant && property_exists($tenant, 'telefone') ? $tenant->telefone : null,
            'email' => $tenant && property_exists($tenant, 'email') ? $tenant->email : null,
            'endereco' => $tenant && property_exists($tenant, 'endereco') ? $tenant->endereco : null
        ];

        // Dados do vendedor (usuário)
        $user = AuthHelper::user();
        $vendedor = [
            'nome' => $user ? $user->name : 'Não informado',
            'email' => $user ? $user->email : null
        ];

        $pagamentosImpressao = PedidoVendaParcela::where('pedido_venda_id', $pedidoVenda->id)
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->get()
            ->groupBy(fn($parcela) => strtolower(trim((string) $parcela->forma_pagamento)))
            ->map(function ($parcelasGrupo) {
                $primeiraParcela = $parcelasGrupo->first();

                return [
                    'forma_pagamento' => $primeiraParcela->forma_pagamento ?? 'Não informado',
                    'valor' => (float) $parcelasGrupo->sum(fn($parcela) => (float) ($parcela->valor ?? 0)),
                    'parcelas' => (int) $parcelasGrupo->count(),
                ];
            })
            ->values()
            ->toArray();

        $tz = 'America/Manaus';
        $today = Carbon::today($tz)->startOfDay();
        $tomorrow = $today->copy()->addDay();
        $weekEnd = $today->copy()->addDays(7);
        $paidStatuses = ['pago', 'paga', 'cancelado', 'cancelada'];

        $parcelasDetalhes = PedidoVendaParcela::query()
            ->where('pedido_venda_id', $pedidoVenda->id)
            ->when($tenantId, fn($query) => $query->where('tenant_id', $tenantId))
            ->orderBy('numero_parcela')
            ->get()
            ->map(function ($parcela) use ($today, $tomorrow, $weekEnd, $tz, $paidStatuses) {
                $venc = $parcela->vencimento_em ? Carbon::parse($parcela->vencimento_em, $tz)->startOfDay() : null;
                $isPaid = !empty($parcela->pago_em) || in_array(strtolower((string) ($parcela->status ?? '')), $paidStatuses, true);

                $status = 'em_dia';
                if ($isPaid) {
                    $status = 'paga';
                } elseif ($venc && $venc->lt($today)) {
                    $status = 'vencida';
                } elseif ($venc && $venc->equalTo($today)) {
                    $status = 'vence_hoje';
                } elseif ($venc && $venc->gte($tomorrow) && $venc->lte($weekEnd)) {
                    $status = 'vence_semana';
                }

                $diasAtraso = 0;
                if (!$isPaid && $venc && $venc->lt($today)) {
                    $diasAtraso = $venc->diffInDays($today);
                }

                $valor = (float) ($parcela->valor ?? 0);
                $juros = 0.0;

                return [
                    'id' => $parcela->id,
                    'parcela' => (int) $parcela->numero_parcela . '/' . (int) $parcela->total_parcelas,
                    'numero_parcela' => (int) $parcela->numero_parcela,
                    'total_parcelas' => (int) $parcela->total_parcelas,
                    'valor_parcela' => $valor,
                    'valor_atualizado' => $valor + $juros,
                    'juros' => $juros,
                    'vencimento' => $venc ? $venc->toDateString() : null,
                    'pago_em' => $parcela->pago_em ? Carbon::parse($parcela->pago_em, $tz) : null,
                    'status' => $status,
                    'status_original' => $parcela->status,
                    'dias_atraso' => $diasAtraso,
                    'forma_pagamento' => $parcela->forma_pagamento,
                ];
            });

        $sale = [
            'id' => $pedidoVenda->id,
            'numero' => $numero,
            'data' => $pedidoVenda->data_pedido->format('Y-m-d'),
            'data_formatada' => $pedidoVenda->data_pedido->format('d/m/Y'),
            'hora_formatada' => $pedidoVenda->data_pedido->format('H:i'),
            'cliente' => $cliente,
            'produtos' => $produtos,
            'subtotal' => $subtotal,
            'desconto' => $descontoTotal,
            'total' => $total,
            'forma_pagamento' => $pedidoVenda->forma_pagamento ?? 'Não informado',
            'pagamentos' => $pagamentosImpressao,
            'parcelas' => $parcelasDetalhes->count() > 0 ? $parcelasDetalhes->count() : 1,
            'valor_parcela' => $parcelasDetalhes->count() > 0 ? (float) $parcelasDetalhes->first()['valor_parcela'] : $total,
            'parcelas_detalhes' => $parcelasDetalhes,
            'status' => $status,
            'observacoes' => $pedidoVenda->observacoes,
            'created_at' => $pedidoVenda->created_at
        ];

        // Gerar PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('sales.print', compact('sale', 'tenantData', 'vendedor'));

        // Configurar papel A4 em modo retrato
        $pdf->setPaper('A4', 'portrait');

        // Configurar opções do DOMPDF
        $pdf->setOption('enable-remote', true);
        $pdf->setOption('enable-local-file-access', true);
        $pdf->setOption('defaultFont', 'DejaVu Sans');

        // IMPORTANTE: Configurar margens usando a sintaxe correta do DOMPDF
        $pdf->setOption('margin-top', '15mm');
        $pdf->setOption('margin-bottom', '15mm');
        $pdf->setOption('margin-left', '20mm');
        $pdf->setOption('margin-right', '20mm');

        $nomeArquivo = 'venda-' . $numero . '.pdf';

        return $pdf->stream($nomeArquivo);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Implementação futura
        return redirect()->route('sales.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Implementação futura
        return redirect()->route('sales.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $tenantId = session('tenant_id');
        $locationId = session('location_id');
        $userLocations = session('user_locations', []);

        $locationIds = [];
        if ($tenantId) {
            $locationIds = collect($userLocations)
                ->where('tenant_id', $tenantId)
                ->pluck('location_id')
                ->toArray();
        } elseif ($locationId) {
            $locationIds = [$locationId];
        }

        if (! $tenantId) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant não informado na sessão.',
                ], 403);
            }

            return redirect()->route('sales.index')->with('error', 'Tenant não informado.');
        }

        $validated = $request->validate([
            'motivo' => 'nullable|string|max:1000',
            'return_url' => 'nullable|url|max:500',
        ], [
            'max.string' => 'O campo :attribute não pode ter mais que :max caracteres.',
            'max.url' => 'A URL de retorno não pode ter mais que :max caracteres.',
        ], [
            'motivo' => 'Motivo',
        ]);

        $pedidoVenda = PedidoVenda::query()
            ->with(['parcelas'])
            ->where('ativo', true)
            ->where('tenant_id', $tenantId)
            ->where('id', (int) $id)
            ->when(! empty($locationIds), function ($q) use ($locationIds) {
                $q->where(function ($q2) use ($locationIds) {
                    $q2->whereIn('location_id', $locationIds)->orWhereNull('location_id');
                });
            })
            ->first();

        if (! $pedidoVenda) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Venda não encontrada.',
                ], 404);
            }

            return redirect()->route('sales.index')->with('error', 'Venda não encontrada.');
        }

        $statusPedidoRaw = strtolower((string) ($pedidoVenda->status ?? ''));
        $statusPedidoMap = [
            'aberto'   => 'pendente',
            'faturado' => 'finalizada',
            'cancelado' => 'cancelada',
        ];
        $statusPedido = $statusPedidoMap[$statusPedidoRaw] ?? $statusPedidoRaw;

        if ($statusPedido === 'cancelada') {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta venda já está cancelada.',
                ], 422);
            }

            return redirect()->back()->with('error', 'Esta venda já está cancelada.');
        }

        $tz = 'America/Manaus';
        $paidStatuses = ['pago', 'paga', 'cancelado', 'cancelada'];

        try {
            DB::beginTransaction();

            $parcelas = $pedidoVenda->parcelas ? $pedidoVenda->parcelas : collect([]);
            $temParcelaPaga = false;
            $parcelasPagasInfo = [];

            foreach ($parcelas as $parcela) {
                $rawStatusParcela = strtolower((string) ($parcela->status ?? ''));
                $parcelaIsPaga = ! empty($parcela->pago_em)
                    || in_array($rawStatusParcela, $paidStatuses, true);

                if ($parcelaIsPaga && ! in_array($rawStatusParcela, ['cancelado', 'cancelada'], true)) {
                    $temParcelaPaga = true;
                    $parcelasPagasInfo[] = $parcela;
                }
            }

            if ($temParcelaPaga) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Não é possível cancelar a venda: existem parcelas já pagas. Reabra as parcelas primeiro.',
                    ], 422);
                }

                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'Não é possível cancelar a venda: existem parcelas já pagas. Reabra as parcelas primeiro.');
            }

            $historicoVenda = "Venda cancelada em " . Carbon::now($tz)->format('d/m/Y H:i') . ".";
            $historicoVenda .= " Status anterior: '{$statusPedido}'.";
            if (! empty($validated['motivo'])) {
                $historicoVenda .= " Motivo: " . trim($validated['motivo']) . ".";
            }

            $pedidoVenda->status = 'cancelado';
            $pedidoVenda->ativo = false;

            if (! empty($pedidoVenda->observacoes)) {
                $pedidoVenda->observacoes = rtrim($pedidoVenda->observacoes) . "\n\n" . $historicoVenda;
            } else {
                $pedidoVenda->observacoes = $historicoVenda;
            }

            $pedidoVenda->save();

            foreach ($parcelas as $parcela) {
                $rawStatusParcela = strtolower((string) ($parcela->status ?? ''));
                $parcelaIsPaga = ! empty($parcela->pago_em)
                    || in_array($rawStatusParcela, $paidStatuses, true);

                if ($parcelaIsPaga) {
                    continue;
                }

                $obsParcela = "Parcela cancelada automaticamente em " . Carbon::now($tz)->format('d/m/Y H:i') . " em razão do cancelamento da venda #{$pedidoVenda->id}.";
                if (! empty($validated['motivo'])) {
                    $obsParcela .= " Motivo do cancelamento da venda: " . trim($validated['motivo']) . ".";
                }

                if (! empty($parcela->observacoes)) {
                    $parcela->observacoes = rtrim($parcela->observacoes) . "\n\n" . $obsParcela;
                } else {
                    $parcela->observacoes = $obsParcela;
                }

                $parcela->status = 'cancelada';
                $parcela->save();
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao cancelar venda: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Erro ao cancelar venda: ' . $e->getMessage());
        }

        $returnUrl = ! empty($validated['return_url'])
            ? $validated['return_url']
            : route('sales.index');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Venda cancelada com sucesso.',
                'return_url' => $returnUrl,
                'venda' => [
                    'id' => $pedidoVenda->id,
                    'status' => (string) $pedidoVenda->status,
                    'ativo' => (bool) $pedidoVenda->ativo,
                ],
            ]);
        }

        return redirect()->to($returnUrl)
            ->with('success', 'Venda cancelada com sucesso.');
    }

    public function parcelaDetails(string $id)
    {
        $tenantId = session('tenant_id');
        $locationId = session('location_id');
        $userLocations = session('user_locations', []);

        $locationIds = [];
        if ($tenantId) {
            $locationIds = collect($userLocations)
                ->where('tenant_id', $tenantId)
                ->pluck('location_id')
                ->toArray();
        } elseif ($locationId) {
            $locationIds = [$locationId];
        }

        if (! $tenantId) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant não informado na sessão.',
            ], 403);
        }

        $parcela = PedidoVendaParcela::query()
            ->with(['pedido.cliente'])
            ->whereNull('deleted_at')
            ->where('tenant_id', $tenantId)
            ->where('id', (int) $id)
            ->when(! empty($locationIds), function ($q) use ($locationIds) {
                $q->where(function ($q2) use ($locationIds) {
                    $q2->whereIn('location_id', $locationIds)->orWhereNull('location_id');
                });
            })
            ->first();

        if (! $parcela) {
            return response()->json([
                'success' => false,
                'message' => 'Parcela não encontrada.',
            ], 404);
        }

        $tz = 'America/Manaus';
        $pedido = $parcela->pedido;
        $cliente = $pedido?->cliente;

        return response()->json([
            'success' => true,
            'data' => [
                'parcela' => [
                    'id' => $parcela->id,
                    'pedido_venda_id' => $parcela->pedido_venda_id,
                    'numero_parcela' => (int) $parcela->numero_parcela,
                    'total_parcelas' => (int) $parcela->total_parcelas,
                    'valor' => (float) ($parcela->valor ?? 0),
                    'vencimento_em' => $parcela->vencimento_em
                        ? Carbon::parse($parcela->vencimento_em, $tz)->toDateString()
                        : null,
                    'status' => (string) ($parcela->status ?? ''),
                    'forma_pagamento' => (string) ($parcela->forma_pagamento ?? ''),
                    'pago_em' => $parcela->pago_em
                        ? Carbon::parse($parcela->pago_em, $tz)->format('Y-m-d H:i:s')
                        : null,
                    'valor_recebido' => (float) ($parcela->valor_recebido ?? 0),
                    'valor_desconto' => (float) ($parcela->valor_desconto ?? 0),
                    'observacoes' => (string) ($parcela->observacoes ?? ''),
                ],
                'pedido' => $pedido ? [
                    'id' => $pedido->id,
                    'valor_total' => (float) ($pedido->valor_total ?? 0),
                    'status' => (string) ($pedido->status ?? ''),
                ] : null,
                'cliente' => $cliente ? [
                    'id' => $cliente->id,
                    'nome' => (string) $cliente->nome,
                    'cpf' => (string) ($cliente->cpf ?? ''),
                ] : null,
            ],
        ]);
    }

    public function updateParcela(Request $request, string $id)
    {
        $tenantId = session('tenant_id');
        $locationId = session('location_id');
        $userLocations = session('user_locations', []);

        $locationIds = [];
        if ($tenantId) {
            $locationIds = collect($userLocations)
                ->where('tenant_id', $tenantId)
                ->pluck('location_id')
                ->toArray();
        } elseif ($locationId) {
            $locationIds = [$locationId];
        }

        if (! $tenantId) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant não informado na sessão.',
                ], 403);
            }

            return redirect()->route('sales.index')->with('error', 'Tenant não informado.');
        }

        $validated = $request->validate([
            'vencimento_em' => 'required|date_format:Y-m-d',
            'valor' => 'required|numeric|min:0',
            'forma_pagamento' => 'nullable|string|max:60',
            'observacoes' => 'nullable|string|max:1000',
        ], [
            'required' => 'O campo :attribute é obrigatório.',
            'date_format' => 'O campo :attribute deve estar no formato YYYY-MM-DD.',
            'numeric' => 'O campo :attribute deve ser numérico.',
            'min.numeric' => 'O campo :attribute deve ser maior ou igual a zero.',
            'max.string' => 'O campo :attribute não pode ter mais que :max caracteres.',
        ], [
            'vencimento_em' => 'Novo vencimento',
            'valor' => 'Novo valor',
        ]);

        $parcela = PedidoVendaParcela::query()
            ->with(['pedido'])
            ->whereNull('deleted_at')
            ->where('tenant_id', $tenantId)
            ->where('id', (int) $id)
            ->when(! empty($locationIds), function ($q) use ($locationIds) {
                $q->where(function ($q2) use ($locationIds) {
                    $q2->whereIn('location_id', $locationIds)->orWhereNull('location_id');
                });
            })
            ->first();

        if (! $parcela) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parcela não encontrada.',
                ], 404);
            }

            return redirect()->route('sales.index')->with('error', 'Parcela não encontrada.');
        }

        $tz = 'America/Manaus';
        $paidStatuses = ['pago', 'paga', 'cancelado', 'cancelada'];
        $isPaid = ! empty($parcela->pago_em)
            || in_array(strtolower((string) ($parcela->status ?? '')), $paidStatuses, true);

        if ($isPaid) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não é possível editar uma parcela já paga ou cancelada.',
                ], 422);
            }

            return redirect()->back()->withInput()
                ->with('error', 'Não é possível editar uma parcela já paga ou cancelada.');
        }

        try {
            $parcela->vencimento_em = Carbon::createFromFormat('Y-m-d', $validated['vencimento_em'], $tz)->startOfDay();
            $parcela->valor = (float) $validated['valor'];

            if (! empty($validated['forma_pagamento'])) {
                $parcela->forma_pagamento = (string) $validated['forma_pagamento'];
            }

            if (! empty($validated['observacoes'])) {
                $parcela->observacoes = (string) $validated['observacoes'];
            }

            $rawStatus = strtolower((string) ($parcela->status ?? ''));
            $isPartialOrRemaining = in_array($rawStatus, ['pagamento_parcial', 'saldo_remanescente'], true);

            if (! $isPartialOrRemaining && empty($parcela->status)) {
                $parcela->status = 'renegociado';
            } elseif (! $isPartialOrRemaining && ! in_array($rawStatus, $paidStatuses, true)) {
                $parcela->status = 'renegociado';
            }

            $parcela->save();
        } catch (\Throwable $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao salvar alterações: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->withInput()
                ->with('error', 'Erro ao salvar alterações: ' . $e->getMessage());
        }

        $pedidoId = $parcela->pedido_venda_id;
        $returnUrl = route('sales.show', ['id' => $pedidoId]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Parcela atualizada com sucesso.',
                'return_url' => $returnUrl,
                'parcela' => [
                    'id' => $parcela->id,
                    'vencimento_em' => $parcela->vencimento_em
                        ? Carbon::parse($parcela->vencimento_em, $tz)->toDateString()
                        : null,
                    'valor' => (float) $parcela->valor,
                    'status' => (string) $parcela->status,
                ],
            ]);
        }

        return redirect()->to($returnUrl)
            ->with('success', 'Parcela atualizada com sucesso.');
    }

    public function reopenParcela(Request $request, string $id)
    {
        $tenantId = session('tenant_id');
        $locationId = session('location_id');
        $userLocations = session('user_locations', []);

        $locationIds = [];
        if ($tenantId) {
            $locationIds = collect($userLocations)
                ->where('tenant_id', $tenantId)
                ->pluck('location_id')
                ->toArray();
        } elseif ($locationId) {
            $locationIds = [$locationId];
        }

        if (! $tenantId) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant não informado na sessão.',
                ], 403);
            }

            return redirect()->route('sales.index')->with('error', 'Tenant não informado.');
        }

        $validated = $request->validate([
            'motivo' => 'nullable|string|max:1000',
            'return_url' => 'nullable|url|max:500',
        ], [
            'max.string' => 'O campo :attribute não pode ter mais que :max caracteres.',
            'max.url' => 'A URL de retorno não pode ter mais que :max caracteres.',
        ], [
            'motivo' => 'Motivo',
        ]);

        $parcela = PedidoVendaParcela::query()
            ->with(['pedido'])
            ->whereNull('deleted_at')
            ->where('tenant_id', $tenantId)
            ->where('id', (int) $id)
            ->when(! empty($locationIds), function ($q) use ($locationIds) {
                $q->where(function ($q2) use ($locationIds) {
                    $q2->whereIn('location_id', $locationIds)->orWhereNull('location_id');
                });
            })
            ->first();

        if (! $parcela) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parcela não encontrada.',
                ], 404);
            }

            return redirect()->route('sales.index')->with('error', 'Parcela não encontrada.');
        }

        $tz = 'America/Manaus';
        $paidStatuses = ['pago', 'paga', 'cancelado', 'cancelada'];
        $rawStatus = strtolower((string) ($parcela->status ?? ''));
        $isPaidOrCanceled = ! empty($parcela->pago_em)
            || in_array($rawStatus, $paidStatuses, true);

        if (! $isPaidOrCanceled) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta parcela não está paga ou cancelada, não há necessidade de reabrir.',
                ], 422);
            }

            return redirect()->back()->with('error', 'Esta parcela não está paga ou cancelada.');
        }

        $valorRecebidoAnterior = (float) ($parcela->valor_recebido ?? 0);
        $valorDescontoAnterior = (float) ($parcela->valor_desconto ?? 0);
        $formaPagamentoAnterior = (string) ($parcela->forma_pagamento ?? '');
        $pagoEmAnterior = $parcela->pago_em
            ? Carbon::parse($parcela->pago_em, $tz)->format('d/m/Y H:i')
            : null;
        $statusAnterior = (string) ($parcela->status ?? '');

        try {
            DB::beginTransaction();

            $parcela->pago_em = null;
            $parcela->valor_recebido = 0;
            $parcela->valor_desconto = 0;

            $hoje = Carbon::now($tz)->startOfDay();
            $vencimento = $parcela->vencimento_em
                ? Carbon::parse($parcela->vencimento_em, $tz)->startOfDay()
                : null;

            $novoStatus = 'pendente';
            if ($vencimento) {
                if ($vencimento->lt($hoje)) {
                    $novoStatus = 'vencida';
                } elseif ($vencimento->eq($hoje)) {
                    $novoStatus = 'vence_hoje';
                } elseif ($vencimento->diffInDays($hoje, false) <= 7) {
                    $novoStatus = 'vence_semana';
                }
            }

            $parcela->status = $novoStatus;

            $historico = "Parcela reaberta em " . Carbon::now($tz)->format('d/m/Y H:i') . ".";
            $historico .= " Status anterior: '{$statusAnterior}'.";
            if ($pagoEmAnterior) {
                $historico .= " Pago em: {$pagoEmAnterior}.";
            }
            if ($valorRecebidoAnterior > 0) {
                $historico .= " Valor recebido anterior: R$ " . number_format($valorRecebidoAnterior, 2, ',', '.') . ".";
            }
            if ($valorDescontoAnterior > 0) {
                $historico .= " Desconto anterior: R$ " . number_format($valorDescontoAnterior, 2, ',', '.') . ".";
            }
            if (! empty($formaPagamentoAnterior)) {
                $historico .= " Forma de pagamento anterior: {$formaPagamentoAnterior}.";
            }
            if (! empty($validated['motivo'])) {
                $historico .= " Motivo: " . trim($validated['motivo']) . ".";
            }

            if (! empty($parcela->observacoes)) {
                $parcela->observacoes = rtrim($parcela->observacoes) . "\n\n" . $historico;
            } else {
                $parcela->observacoes = $historico;
            }

            $parcela->save();

            $parcelaRemanescenteVinculada = PedidoVendaParcela::query()
                ->whereNull('deleted_at')
                ->where('tenant_id', $tenantId)
                ->where('pedido_venda_id', (int) $parcela->pedido_venda_id)
                ->where('numero_parcela', (int) $parcela->numero_parcela)
                ->where('id', '!=', (int) $parcela->id)
                ->where('status', 'saldo_remanescente')
                ->orderBy('id', 'desc')
                ->first();

            if ($parcelaRemanescenteVinculada) {
                $parcelaRemanescenteVinculada->pago_em = null;
                $parcelaRemanescenteVinculada->valor_recebido = 0;
                $parcelaRemanescenteVinculada->valor_desconto = 0;
                $statusRem = 'pendente';
                $vencRem = $parcelaRemanescenteVinculada->vencimento_em
                    ? Carbon::parse($parcelaRemanescenteVinculada->vencimento_em, $tz)->startOfDay()
                    : null;
                if ($vencRem) {
                    if ($vencRem->lt($hoje)) {
                        $statusRem = 'vencida';
                    } elseif ($vencRem->eq($hoje)) {
                        $statusRem = 'vence_hoje';
                    } elseif ($vencRem->diffInDays($hoje, false) <= 7) {
                        $statusRem = 'vence_semana';
                    }
                }
                $parcelaRemanescenteVinculada->status = $statusRem;

                $obsRem = "Parcela reaberta automaticamente em " . Carbon::now($tz)->format('d/m/Y H:i') . " em conjunto com a parcela pai #{$parcela->id}.";
                if (! empty($parcelaRemanescenteVinculada->observacoes)) {
                    $parcelaRemanescenteVinculada->observacoes = rtrim($parcelaRemanescenteVinculada->observacoes) . "\n\n" . $obsRem;
                } else {
                    $parcelaRemanescenteVinculada->observacoes = $obsRem;
                }

                $parcelaRemanescenteVinculada->save();
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao reabrir parcela: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Erro ao reabrir parcela: ' . $e->getMessage());
        }

        $pedidoId = $parcela->pedido_venda_id;
        $returnUrl = ! empty($validated['return_url'])
            ? $validated['return_url']
            : route('sales.show', ['id' => $pedidoId]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Parcela reaberta com sucesso.',
                'return_url' => $returnUrl,
                'parcela' => [
                    'id' => $parcela->id,
                    'vencimento_em' => $parcela->vencimento_em
                        ? Carbon::parse($parcela->vencimento_em, $tz)->toDateString()
                        : null,
                    'valor' => (float) $parcela->valor,
                    'status' => (string) $parcela->status,
                ],
            ]);
        }

        return redirect()->to($returnUrl)
            ->with('success', 'Parcela reaberta com sucesso.');
    }

    private function discountAuthorizationCacheKey(string $token): string
    {
        return 'sale_discount_auth:' . $token;
    }

    private function resolvePedidoDesconto(PedidoVenda $pedidoVenda, float $subtotal, float $total): float
    {
        $descontoPersistido = (float) ($pedidoVenda->desconto_valor ?? 0);

        if ($descontoPersistido > 0) {
            return $descontoPersistido;
        }

        return max(0, round($subtotal - $total, 2));
    }
}
