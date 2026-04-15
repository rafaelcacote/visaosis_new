<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produto;
use App\Models\Categoria;
use App\Models\PedidoVenda;
use App\Models\ItemPedido;
use App\Models\ContaReceber;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Helpers\AuthHelper;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
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

        $query = PedidoVenda::with(['cliente', 'itens'])
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
            $query->where('status', $request->get('status'));
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

        $pedidos = $query->get();

        // Formatar dados para a view
        $sales = $pedidos->map(function ($pedido) {
            $clienteNome = $pedido->cliente ? $pedido->cliente->nome : 'Cliente não informado';
            $quantidadeItens = $pedido->itens->sum('quantidade');
            $quantidadeProdutos = $pedido->itens->count();

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
                'forma_pagamento' => $formaPagamento,
                'parcelas' => $parcelas,
                'produtos' => $quantidadeProdutos,
                'itens_total' => $quantidadeItens
            ];
        });

        // Calcular estatísticas
        $vendasHoje = $pedidos->filter(function ($pedido) {
            return $pedido->data_pedido->format('Y-m-d') === today()->format('Y-m-d');
        })->sum('valor_total');

        $totalVendas = $pedidos->count();
        $ticketMedio = $totalVendas > 0 ? $pedidos->sum('valor_total') / $totalVendas : 0;
        $pendentes = $pedidos->where('status', 'aberto')->count();

        return view('sales.index', compact('sales', 'vendasHoje', 'totalVendas', 'ticketMedio', 'pendentes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
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

        $products = $query->orderBy('nome')
            ->get()
            ->map(function (Produto $product) {
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

                return [
                    'id' => $product->id,
                    'nome' => $product->nome,
                    'categoria' => optional($product->categoria)->descricao ?? 'Sem categoria',
                    'categoria_id' => $product->categoria_id,
                    'preco' => (float) $product->preco_venda,
                    'stock' => $stockFromAttributes,
                    'image_url' => $imageUrl,
                ];
            })
            ->filter(function (array $product) {
                if ($product['stock'] === null) {
                    return true;
                }

                return $product['stock'] > 0;
            })
            ->values()
            ->toArray();

        $categoriesQuery = Categoria::where('ativo', true);
        if ($tenantId) {
            $categoriesQuery->where('tenant_id', $tenantId);
        }
        $categories = $categoriesQuery->orderBy('descricao')
            ->get(['id', 'descricao']);

        return view('sales.create', compact('products', 'categories'));
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
                'produtos.*.preco_unitario' => 'required|numeric|min:0',
                'produtos.*.subtotal' => 'required|numeric|min:0',
                'forma_pagamento' => 'required|string|in:dinheiro,cartao_debito,cartao_credito,crediario,pix',
                'parcelas' => 'required|integer|min:1|max:12',
                'desconto_percentual' => 'nullable|numeric|min:0|max:100',
                'desconto_valor' => 'nullable|numeric|min:0',
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
            $formaPagamentoNome = $paymentMethods[$validated['forma_pagamento']] ?? $validated['forma_pagamento'];

            // Criar o pedido de venda
            $pedidoVenda = PedidoVenda::create([
                'tenant_id' => $tenantId,
                'location_id' => $locationId,
                'user_id' => $userId,
                'pessoa_cliente_id' => $validated['cliente_id'],
                'status' => 'faturado', // Status inicial como 'faturado' quando finaliza a venda
                'data_pedido' => now(),
                'valor_total' => $validated['total'],
                'forma_pagamento' => $formaPagamentoNome,
                'observacoes' => $validated['observacoes'] ?? null,
                'ativo' => true
            ]);

            $this->createReceivablesFromSale(
                $pedidoVenda,
                $validated,
                $tenantId,
                $locationId,
                $userId
            );

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

        // Desconto total
        $descontoTotal = $pedidoVenda->itens->sum('desconto');

        // Total
        $total = (float) $pedidoVenda->valor_total;

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
            'parcelas' => 1, // Pode ser adicionado na tabela se necessário
            'valor_parcela' => $total,
            'status' => $status,
            'status_original' => $pedidoVenda->status,
            'observacoes' => $pedidoVenda->observacoes,
            'created_at' => $pedidoVenda->created_at,
            'updated_at' => $pedidoVenda->updated_at
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

        // Desconto total
        $descontoTotal = $pedidoVenda->itens->sum('desconto');

        // Total
        $total = (float) $pedidoVenda->valor_total;

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
            'parcelas' => 1,
            'valor_parcela' => $total,
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
    public function destroy(string $id)
    {
        $tenantId = session('tenant_id');
        
        $pedidoVenda = PedidoVenda::where('ativo', true);
        if ($tenantId) {
            $pedidoVenda->where('tenant_id', $tenantId);
        }
        $pedidoVenda = $pedidoVenda->findOrFail($id);

        // Cancelar a venda (soft delete ou mudança de status)
        $pedidoVenda->update([
            'status' => 'cancelado',
            'ativo' => false
        ]);

        return redirect()->route('sales.index')->with('success', 'Venda cancelada com sucesso!');
    }

    private function createReceivablesFromSale(
        PedidoVenda $pedidoVenda,
        array $validated,
        $tenantId,
        $locationId,
        $userId
    ): void {
        $formaPagamento = $validated['forma_pagamento'] ?? null;
        $isCrediario = $formaPagamento === 'crediario';
        $isImmediatePayment = in_array($formaPagamento, [
            'pix',
            'dinheiro',
            'cartao_debito',
            'cartao_credito',
        ], true);

        $totalParcelas = $isCrediario
            ? max(1, (int) ($validated['parcelas'] ?? 1))
            : 1;
        $valorTotal = (float) $validated['total'];
        $valorBaseParcela = round($valorTotal / $totalParcelas, 2);
        $statusInicial = $isCrediario ? 'pendente' : 'pago';
        $dataPagamento = $isImmediatePayment ? now() : null;

        for ($parcela = 1; $parcela <= $totalParcelas; $parcela++) {
            $valorParcela = $valorBaseParcela;

            // Ajusta centavos na última parcela para fechar exatamente o total da venda.
            if ($parcela === $totalParcelas) {
                $valorParcela = round(
                    $valorTotal - ($valorBaseParcela * ($totalParcelas - 1)),
                    2
                );
            }

            $inicioVencimento = $isCrediario ? 1 : 0;
            $dataVencimento = now()
                ->copy()
                ->addMonthsNoOverflow($inicioVencimento + ($parcela - 1))
                ->startOfDay();

            ContaReceber::create([
                'tenant_id' => $tenantId,
                'location_id' => $locationId,
                'user_id' => $userId,
                'pedido_venda_id' => $pedidoVenda->id,
                'pessoa_cliente_id' => $validated['cliente_id'],
                'numero_parcela' => $parcela,
                'total_parcelas' => $totalParcelas,
                'valor_parcela' => $valorParcela,
                'valor_total_venda' => $valorTotal,
                'data_vencimento' => $dataVencimento,
                'data_pagamento' => $dataPagamento,
                'forma_pagamento' => $formaPagamento,
                'status' => $statusInicial,
                'observacoes' => $validated['observacoes'] ?? null,
                'ativo' => true,
            ]);
        }
    }
}
