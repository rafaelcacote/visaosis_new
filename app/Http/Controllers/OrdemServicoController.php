<?php

namespace App\Http\Controllers;

use App\Models\OrdemServico;
use App\Models\PedidoVenda;
use App\Models\Laboratorio;
use App\Models\Pessoa;
use App\Models\ItemPedido;
use App\Models\ItemOrdem;
use App\Models\Prescricao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class OrdemServicoController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status', 'todos');
        $prioridade = $request->get('prioridade', 'todas');

        $tenantId = session('tenant_id');
        $locationId = session('location_id');

        $query = OrdemServico::with(['pedido.cliente', 'fornecedor', 'itensOrdem'])
            ->where('ativo', true);

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        if ($locationId) {
            $query->where('location_id', $locationId);
        }

        // Aplicar filtros
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('pedido.cliente', function ($subQ) use ($search) {
                    $subQ->where('nome', 'LIKE', "%{$search}%");
                })->orWhereHas('fornecedor', function ($subQ) use ($search) {
                    $subQ->where('razao_social', 'LIKE', "%{$search}%")
                        ->orWhere('nome_fantasia', 'LIKE', "%{$search}%");
                })->orWhere('observacoes', 'LIKE', "%{$search}%");
            });
        }

        if ($status !== 'todos') {
            $query->where('status', $status);
        }

        if ($prioridade !== 'todas') {
            $query->where('prioridade', $prioridade);
        }

        $ordensServico = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('ordens-servico.index', compact(
            'ordensServico',
            'search',
            'status',
            'prioridade'
        ));
    }

    public function create()
    {
        $tenantId = session('tenant_id');
        $locationId = session('location_id');

        $query = Laboratorio::where('ativo', true);

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        if ($locationId) {
            $query->where('location_id', $locationId);
        }

        $fornecedores = $query->orderBy('razao_social')->get();

        return view('ordens-servico.create', compact('fornecedores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pedido_id' => 'required|exists:pedido_venda,id',
            'fornecedor_id' => 'required|exists:fornecedor,id',
            'itens_selecionados' => 'required|array|min:1',
            'itens_selecionados.*' => 'required|exists:item_pedido,id',
            'quantidade' => 'required|integer|min:1',
            'preco_unit' => 'required|numeric|min:0',
            'desconto' => 'nullable|numeric|min:0',
            'entrega_em' => 'nullable|date|after:today',
            'prioridade' => 'required|in:normal,urgente,expressa',
            'observacoes' => 'nullable|string|max:1000'
        ], [
            'itens_selecionados.required' => 'Você deve selecionar pelo menos um item da venda.',
            'itens_selecionados.min' => 'Você deve selecionar pelo menos um item da venda.',
            'itens_selecionados.*.exists' => 'Um ou mais itens selecionados são inválidos.'
        ]);

        $desconto = $request->desconto ?? 0;
        $total_linha = ($request->preco_unit * $request->quantidade) - $desconto;

        // Usar transação para garantir consistência
        DB::beginTransaction();
        try {
            $tenantId = session('tenant_id');
            $locationId = session('location_id');
            $userId = auth()->id();

            $ordemServico = OrdemServico::create([
                'tenant_id' => $tenantId,
                'location_id' => $locationId,
                'user_id' => $userId,
                'pedido_id' => $request->pedido_id,
                'prescricao_id' => $request->prescricao_id,
                'fornecedor_id' => $request->fornecedor_id,
                'quantidade' => $request->quantidade,
                'preco_unit' => $request->preco_unit,
                'desconto' => $desconto,
                'total_linha' => $total_linha,
                'entrega_em' => $request->entrega_em,
                'prioridade' => $request->prioridade,
                'status' => OrdemServico::STATUS_PENDENTE,
                'observacoes' => $request->observacoes,
                'ativo' => true
            ]);

            // Criar registros na tabela item_ordem para cada item selecionado
            foreach ($request->itens_selecionados as $itemId) {
                ItemOrdem::create([
                    'tenant_id' => $tenantId,
                    'location_id' => $locationId,
                    'user_id' => $userId,
                    'ordem_id' => $ordemServico->id,
                    'item_id' => $itemId
                ]);
            }

            DB::commit();

            return redirect()->route('ordens-servico.show', $ordemServico)
                ->with('success', 'Ordem de serviço criada com sucesso com ' . count($request->itens_selecionados) . ' item(s)!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erro ao criar ordem de serviço: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        // Buscar ordem de serviço com relacionamentos
        try {
            $ordemServico = OrdemServico::findOrFail($id);

            // Carrega relacionamentos
            $ordemServico->load([
                'pedido.cliente',
                'fornecedor',
                'user',
                'prescricao.paciente',
                'itensOrdem.item.produto.categoria'
            ]);

            return view('ordens-servico.show', compact('ordemServico'));
        } catch (\Exception $e) {
            abort(404, 'Ordem de serviço não encontrada.');
        }
    }

    public function buscarClientes(Request $request)
    {
        $search = $request->get('q');
        $cpfLimpo = preg_replace('/[^0-9]/', '', $search);

        $tenantId = session('tenant_id');
        $locationId = session('location_id');

        // Se for busca por ID de venda (numérico), buscar diretamente a venda
        if (is_numeric($search)) {
            $query = PedidoVenda::with('cliente')
                ->where('id', $search)
                ->where('status', PedidoVenda::STATUS_FATURADO);

            if ($tenantId) {
                $query->where('tenant_id', $tenantId);
            }

            if ($locationId) {
                $query->where('location_id', $locationId);
            }

            $venda = $query->first();

            if ($venda && $venda->cliente) {
                return response()->json([[
                    'type' => 'venda',
                    'id' => $venda->cliente->id,
                    'nome' => $venda->cliente->nome,
                    'cpf' => $venda->cliente->cpf ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $venda->cliente->cpf) : 'Não informado',
                    'telefone' => $venda->cliente->telefone ?? '',
                    'email' => $venda->cliente->email ?? '',
                    'venda_id' => $venda->id,
                    'venda_numero' => $venda->numero,
                    'valor_total' => $venda->valor_total_formatado,
                    'data_pedido' => $venda->data_pedido_formatada
                ]]);
            }
        }

        // Busca por clientes (nome, email, CPF)
        $query = Pessoa::where('ativo', true);

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        if ($search) {
            $query->where(function ($q) use ($search, $cpfLimpo) {
                $q->where('nome', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");

                if ($cpfLimpo && strlen($cpfLimpo) >= 3) {
                    $q->orWhere('cpf', 'LIKE', "%{$cpfLimpo}%");
                }
            });
        }

        $clientes = $query->orderBy('nome')
            ->limit(10)
            ->get()
            ->map(function ($cliente) {
                return [
                    'type' => 'cliente',
                    'id' => $cliente->id,
                    'nome' => $cliente->nome,
                    'cpf' => $cliente->cpf ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cliente->cpf) : 'Não informado',
                    'telefone' => $cliente->telefone ?? '',
                    'email' => $cliente->email ?? ''
                ];
            });

        return response()->json($clientes);
    }

    public function buscarVendasCliente(Request $request)
    {
        $clienteId = $request->get('cliente_id');
        $termo = $request->get('termo', '');

        $tenantId = session('tenant_id');
        $locationId = session('location_id');

        $query = PedidoVenda::with(['cliente', 'itens.produto'])
            ->where('pessoa_cliente_id', $clienteId)
            ->where('status', PedidoVenda::STATUS_FATURADO);

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        if ($locationId) {
            $query->where('location_id', $locationId);
        }

        if ($termo) {
            $query->where('id', 'LIKE', "%{$termo}%");
        }

        $vendas = $query->orderBy('data_pedido', 'desc')
            ->limit(10)
            ->get();

        return response()->json($vendas->map(function ($venda) {
            // Formatar itens da venda
            $itens = $venda->itens->map(function ($item) {
                return [
                    'id' => $item->id,
                    'produto_id' => $item->produto_id,
                    'produto_nome' => $item->produto ? $item->produto->nome : 'Produto não encontrado',
                    'quantidade' => $item->quantidade,
                    'preco_unit' => 'R$ ' . number_format($item->preco_unit, 2, ',', '.'),
                    'preco_unit_raw' => (float) $item->preco_unit,
                    'desconto' => 'R$ ' . number_format($item->desconto, 2, ',', '.'),
                    'desconto_raw' => (float) $item->desconto,
                    'total_linha' => 'R$ ' . number_format($item->total_linha, 2, ',', '.'),
                    'total_linha_raw' => (float) $item->total_linha
                ];
            });

            return [
                'id' => $venda->id,
                'numero' => $venda->numero,
                'data_pedido' => $venda->data_pedido_formatada,
                'data_pedido_raw' => $venda->data_pedido->format('Y-m-d'),
                'valor_total' => $venda->valor_total_formatado,
                'valor_total_raw' => (float) $venda->valor_total,
                'status' => $venda->status_label,
                'status_raw' => $venda->status,
                'forma_pagamento' => $venda->forma_pagamento ?? 'Não informado',
                'cliente_nome' => $venda->cliente->nome ?? 'N/A',
                'quantidade_itens' => $venda->itens->count(),
                'quantidade_produtos' => $venda->itens->sum('quantidade'),
                'observacoes' => $venda->observacoes,
                'itens' => $itens
            ];
        }));
    }

    public function buscarPrescricoes(Request $request)
    {
        $search = $request->get('q');

        $tenantId = session('tenant_id');
        $locationId = session('location_id');

        $query = Prescricao::with('paciente')
            ->where('ativo', true)
            ->whereHas('paciente', function ($q) {
                $q->where('ativo', true);
            });

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        if ($locationId) {
            $query->where('location_id', $locationId);
        }

        if ($search) {
            // Buscar por ID da prescrição ou nome do paciente
            $query->where(function ($q) use ($search) {
                // Se for numérico, buscar por ID também
                if (is_numeric($search)) {
                    $q->where('id', $search);
                }

                // Sempre incluir busca por nome do paciente
                $q->orWhereHas('paciente', function ($subQ) use ($search) {
                    $subQ->where('nome', 'ILIKE', "%{$search}%");
                });
            });
        }

        $prescricoes = $query->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($prescricao) {
                return [
                    'id' => $prescricao->id,
                    'data_criacao' => $prescricao->created_at ? $prescricao->created_at->format('d/m/Y H:i') : '',
                    'paciente_id' => $prescricao->paciente ? $prescricao->paciente->id : null,
                    'paciente_nome' => $prescricao->paciente ? $prescricao->paciente->nome : 'N/A',
                    'paciente_cpf' => $prescricao->paciente && $prescricao->paciente->cpf ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $prescricao->paciente->cpf) : 'Não informado',
                    'paciente_telefone' => $prescricao->paciente ? $prescricao->paciente->telefone : '',
                    'observacoes' => $prescricao->observacoes,
                    'validade_dias' => $prescricao->validade_dias,
                    'longe' => [
                        'od' => [
                            'esfera' => $prescricao->esfera_od,
                            'cilindro' => $prescricao->cilindro_od,
                            'eixo' => $prescricao->eixo_od,
                            'av' => $prescricao->acuidade_od,
                            'dnp' => $prescricao->dnp_od,
                            'altura' => $prescricao->altura_od,
                            'adicao' => $prescricao->adicao_od,
                        ],
                        'oe' => [
                            'esfera' => $prescricao->esfera_oe,
                            'cilindro' => $prescricao->cilindro_oe,
                            'eixo' => $prescricao->eixo_oe,
                            'av' => $prescricao->acuidade_oe,
                            'dnp' => $prescricao->dnp_oe,
                            'altura' => $prescricao->altura_oe,
                            'adicao' => $prescricao->adicao_oe,
                        ],
                    ],
                    'perto' => [
                        'od' => [
                            'esfera' => $prescricao->esfera_od_perto,
                            'cilindro' => $prescricao->cilindro_od_perto,
                            'eixo' => $prescricao->eixo_od_perto,
                            'av' => $prescricao->acuidade_od_perto,
                            'dnp' => $prescricao->dnp_od_perto,
                            'altura' => $prescricao->altura_od_perto,
                            'adicao' => $prescricao->adicao_od_perto,
                        ],
                        'oe' => [
                            'esfera' => $prescricao->esfera_oe_perto,
                            'cilindro' => $prescricao->cilindro_oe_perto,
                            'eixo' => $prescricao->eixo_oe_perto,
                            'av' => $prescricao->acuidade_oe_perto,
                            'dnp' => $prescricao->dnp_oe_perto,
                            'altura' => $prescricao->altura_oe_perto,
                            'adicao' => $prescricao->adicao_oe_perto,
                        ],
                    ],
                ];
            });

        return response()->json($prescricoes);
    }

    public function edit(OrdemServico $ordemServico)
    {
        $tenantId = session('tenant_id');
        $locationId = session('location_id');

        $query = Laboratorio::where('ativo', true);

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        if ($locationId) {
            $query->where('location_id', $locationId);
        }

        $fornecedores = $query->orderBy('razao_social')->get();

        // Carrega relacionamentos necessários para exibição completa
        $ordemServico->load([
            'pedido.cliente',
            'pedido.itens.produto',
            'prescricao',
            'prescricao.consulta.paciente' => function ($query) {
                $query->select('id', 'nome', 'cpf');
            },
            'fornecedor',
            'itensOrdem.item.produto.categoria',
        ]);

        // Se prescrição não tem consulta, mas tem pessoa_paciente_id, carrega o paciente diretamente
        if ($ordemServico->prescricao && $ordemServico->prescricao->pessoa_paciente_id && !$ordemServico->prescricao->consulta) {
            $ordemServico->prescricao->load([
                'paciente' => function ($query) {
                    $query->select('id', 'nome', 'cpf');
                }
            ]);
        }

        return view('ordens-servico.edit', compact('ordemServico', 'fornecedores'));
    }

    public function update(Request $request, OrdemServico $ordemServico)
    {
        $request->validate([
            'fornecedor_id' => 'required|exists:fornecedor,id',
            'quantidade' => 'required|integer|min:1',
            'preco_unit' => 'required|numeric|min:0',
            'desconto' => 'nullable|numeric|min:0',
            'entrega_em' => 'nullable|date',
            'prioridade' => 'required|in:normal,urgente,expressa',
            'status' => 'required|in:pendente,enviado,em_producao,pronto,entregue,cancelado',
            'observacoes' => 'nullable|string|max:1000'
        ]);

        $desconto = $request->desconto ?? 0;
        $total_linha = ($request->preco_unit * $request->quantidade) - $desconto;

        $ordemServico->update([
            'fornecedor_id' => $request->fornecedor_id,
            'quantidade' => $request->quantidade,
            'preco_unit' => $request->preco_unit,
            'desconto' => $desconto,
            'total_linha' => $total_linha,
            'entrega_em' => $request->entrega_em,
            'prioridade' => $request->prioridade,
            'status' => $request->status,
            'observacoes' => $request->observacoes
        ]);

        return redirect()->route('ordens-servico.show', $ordemServico)
            ->with('success', 'Ordem de serviço atualizada com sucesso!');
    }

    public function destroy(OrdemServico $ordemServico)
    {
        $ordemServico->delete();
        return redirect()->route('ordens-servico.index')
            ->with('success', 'Ordem de serviço excluída com sucesso!');
    }

    public function updateStatus(Request $request, OrdemServico $ordemServico)
    {
        $validatedData = $request->validate([
            'status' => 'required|string|in:pendente,enviado,em_producao,pronto,entregue,cancelado'
        ]);

        $statusAnterior = $ordemServico->status;
        $novoStatus = $validatedData['status'];

        // Atualizar o status
        $ordemServico->update([
            'status' => $novoStatus
        ]);

        // Mapear labels para exibição
        $statusLabels = [
            'pendente' => 'Pendente',
            'enviado' => 'Enviado',
            'em_producao' => 'Em Produção',
            'pronto' => 'Pronto',
            'entregue' => 'Entregue',
            'cancelado' => 'Cancelado'
        ];

        $labelAnterior = $statusLabels[$statusAnterior] ?? $statusAnterior;
        $labelNovo = $statusLabels[$novoStatus] ?? $novoStatus;

        return redirect()->route('ordens-servico.index')
            ->with('success', "Status da ordem #{$ordemServico->id} alterado de '{$labelAnterior}' para '{$labelNovo}' com sucesso!");
    }

    public function pdf(OrdemServico $ordemServico)
    {
        // Carrega relacionamentos necessários
        $ordemServico->load([
            'pedido.cliente',
            'fornecedor',
            'user',
            'prescricao.paciente',
            'itensOrdem.item.produto.categoria'
        ]);

        // Preparar status para o PDF
        $currentStatus = [
            'text' => $ordemServico->status_label,
            'class' => match ($ordemServico->status) {
                'pendente' => 'warning',
                'enviado' => 'info',
                'em_producao' => 'primary',
                'pronto' => 'success',
                'entregue' => 'success',
                'cancelado' => 'danger',
                default => 'secondary'
            }
        ];

        $pdf = Pdf::loadView('ordens-servico.pdf', compact('ordemServico', 'currentStatus'));

        $filename = 'ordem_servico_' . str_pad($ordemServico->id, 6, '0', STR_PAD_LEFT) . '.pdf';

        return $pdf->stream($filename);
    }
}
