<?php

namespace App\Http\Controllers;

use App\Models\ContaReceber;
use Illuminate\Http\Request;

class FinancialController extends Controller
{
    public function index()
    {
        // Dashboard financeiro principal
        $financialData = [
            'total_receber' => 45800.00,
            'vencidas' => 8900.00,
            'vence_hoje' => 2400.00,
            'vence_semana' => 6700.00,
            'recebido_mes' => 28500.00,
            'inadimplencia' => 12.5,
            'ticket_medio' => 850.00,
            'total_clientes_credito' => 156
        ];

        return view('financial.index', compact('financialData'));
    }

    public function receivables(Request $request)
    {
        $tenantId = session('tenant_id');
        $statusFilter = $request->get('status');
        $searchFilter = trim((string) $request->get('search', ''));
        $startDateFilter = $request->get('start_date');
        $endDateFilter = $request->get('end_date');
        $orderByFilter = $request->get('order_by', 'vencimento');
        $today = now()->startOfDay();

        $query = ContaReceber::with(['cliente', 'pedidoVenda'])
            ->where('ativo', true);

        // Por padrão, exibe somente parcelas em aberto.
        if ($statusFilter === 'pago') {
            $query->where('status', 'pago');
        } else {
            $query->where('status', '!=', 'pago');
        }

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        if (!empty($startDateFilter)) {
            $query->whereDate('data_vencimento', '>=', $startDateFilter);
        }

        if (!empty($endDateFilter)) {
            $query->whereDate('data_vencimento', '<=', $endDateFilter);
        }

        $receivables = $query
            ->orderBy('data_vencimento')
            ->orderBy('numero_parcela')
            ->get()
            ->map(function (ContaReceber $receivable) {
                $today = now()->startOfDay();
                $dueDate = optional($receivable->data_vencimento)->copy()->startOfDay();
                $diasAtraso = 0;
                $status = $receivable->status ?? 'pendente';

                if ($status !== 'pago' && $dueDate) {
                    if ($dueDate->lt($today)) {
                        $status = 'vencida';
                        $diasAtraso = $dueDate->diffInDays($today);
                    } elseif ($dueDate->isSameDay($today)) {
                        $status = 'vence_hoje';
                    } elseif ($dueDate->lte($today->copy()->addDays(7))) {
                        $status = 'vence_semana';
                    } else {
                        $status = 'em_dia';
                    }
                }

                $vendaId = $receivable->pedidoVenda
                    ? 'VD-' . $receivable->pedidoVenda->data_pedido->format('Y') . '-' . str_pad($receivable->pedidoVenda->id, 4, '0', STR_PAD_LEFT)
                    : 'VD-' . str_pad($receivable->pedido_venda_id, 4, '0', STR_PAD_LEFT);

                return [
                    'id' => $receivable->id,
                    'cliente' => optional($receivable->cliente)->nome ?? 'Cliente não informado',
                    'telefone' => optional($receivable->cliente)->telefone_formatado ?? (optional($receivable->cliente)->telefone ?? '-'),
                    'cpf' => optional($receivable->cliente)->cpf_formatado ?? (optional($receivable->cliente)->cpf ?? ''),
                    'venda_id' => $vendaId,
                    'parcela' => $receivable->numero_parcela . '/' . $receivable->total_parcelas,
                    'valor_parcela' => (float) $receivable->valor_parcela,
                    'valor_total' => (float) $receivable->valor_total_venda,
                    'vencimento' => optional($receivable->data_vencimento)->format('Y-m-d') ?? now()->format('Y-m-d'),
                    'status' => $status,
                    'forma_pagamento' => $receivable->forma_pagamento ?? '-',
                    'data_pagamento' => optional($receivable->data_pagamento)->format('d/m/Y H:i'),
                    'dias_atraso' => $diasAtraso,
                    'juros' => 0.00,
                    'valor_atualizado' => (float) $receivable->valor_parcela,
                ];
            })
            ->values()
            ->toArray();

        if ($searchFilter !== '') {
            $normalizedSearch = mb_strtolower($searchFilter, 'UTF-8');
            $receivables = collect($receivables)
                ->filter(function (array $item) use ($normalizedSearch) {
                    $cliente = mb_strtolower((string) ($item['cliente'] ?? ''), 'UTF-8');
                    $telefone = mb_strtolower((string) ($item['telefone'] ?? ''), 'UTF-8');
                    $vendaId = mb_strtolower((string) ($item['venda_id'] ?? ''), 'UTF-8');

                    return str_contains($cliente, $normalizedSearch)
                        || str_contains($telefone, $normalizedSearch)
                        || str_contains($vendaId, $normalizedSearch);
                })
                ->values()
                ->toArray();
        }

        // Dashboard: considera somente parcelas em aberto.
        $openReceivables = collect($receivables)->where('status', '!=', 'pago');
        $financialData = [
            'vencidas_count' => $openReceivables->where('status', 'vencida')->count(),
            'vencidas_total' => (float) $openReceivables->where('status', 'vencida')->sum('valor_atualizado'),
            'vence_hoje_count' => $openReceivables->where('status', 'vence_hoje')->count(),
            'vence_hoje_total' => (float) $openReceivables->where('status', 'vence_hoje')->sum('valor_atualizado'),
            'vence_semana_count' => $openReceivables->where('status', 'vence_semana')->count(),
            'vence_semana_total' => (float) $openReceivables->where('status', 'vence_semana')->sum('valor_atualizado'),
            'em_dia_count' => $openReceivables->where('status', 'em_dia')->count(),
            'em_dia_total' => (float) $openReceivables->where('status', 'em_dia')->sum('valor_atualizado'),
            'total_receber' => (float) $openReceivables->sum('valor_atualizado'),
            'total_registros' => $openReceivables->count(),
            'updated_at' => $today->format('d/m/Y'),
        ];

        // Filtros por status derivados de vencimento (aplicados após map).
        if (in_array($statusFilter, ['vencida', 'vence_hoje', 'vence_semana', 'em_dia'], true)) {
            $receivables = collect($receivables)
                ->where('status', $statusFilter)
                ->values()
                ->toArray();
        }

        $receivablesCollection = collect($receivables);
        $receivables = match ($orderByFilter) {
            'valor' => $receivablesCollection->sortByDesc('valor_atualizado')->values()->toArray(),
            'cliente' => $receivablesCollection->sortBy('cliente')->values()->toArray(),
            'atraso' => $receivablesCollection->sortByDesc('dias_atraso')->values()->toArray(),
            default => $receivablesCollection->sortBy('vencimento')->values()->toArray(),
        };

        return view('financial.receivables', [
            'receivables' => $receivables,
            'activeStatusFilter' => $statusFilter,
            'financialData' => $financialData,
            'filters' => [
                'search' => $searchFilter,
                'start_date' => $startDateFilter,
                'end_date' => $endDateFilter,
                'order_by' => $orderByFilter,
            ],
        ]);
    }

    public function boletos()
    {
        // Gestão de boletos
        $boletos = [
            [
                'id' => 'BOL-2024-001',
                'cliente' => 'Maria Silva Santos',
                'cpf' => '123.456.789-00',
                'venda_id' => 'VD-2024-045',
                'parcela' => '3/6',
                'valor' => 142.50,
                'juros' => 7.12,
                'valor_total' => 149.62,
                'vencimento' => '2024-08-30',
                'status' => 'vencido',
                'codigo_barras' => '34191.09008 61207.954566 00000.142508 1 95470000014962',
                'linha_digitavel' => '34191.09008 61207.954566 00000.142508 1 95470000014962',
                'gerado_em' => '2024-08-25',
                'enviado_whatsapp' => true
            ],
            [
                'id' => 'BOL-2024-002',
                'cliente' => 'João Silva',
                'cpf' => '987.654.321-00',
                'venda_id' => 'VD-2024-048',
                'parcela' => '1/4',
                'valor' => 300.00,
                'juros' => 0.00,
                'valor_total' => 300.00,
                'vencimento' => '2024-08-29',
                'status' => 'pendente',
                'codigo_barras' => '34191.09008 61207.954566 00000.300008 2 95470000030000',
                'linha_digitavel' => '34191.09008 61207.954566 00000.300008 2 95470000030000',
                'gerado_em' => '2024-08-24',
                'enviado_whatsapp' => false
            ]
        ];

        return view('financial.boletos', compact('boletos'));
    }

    public function notifications()
    {
        // Central de notificações WhatsApp
        $notifications = [
            [
                'id' => 1,
                'cliente' => 'Maria Silva Santos',
                'telefone' => '(11) 99999-9999',
                'tipo' => 'vencimento',
                'mensagem' => 'Olá Maria! Sua parcela de R$ 142,50 vence hoje (30/08). Evite juros pagando até às 23:59h.',
                'status' => 'enviado',
                'enviado_em' => '2024-08-30 08:00:00',
                'lido' => true,
                'respondido' => false
            ],
            [
                'id' => 2,
                'cliente' => 'João Silva',
                'telefone' => '(11) 88888-8888',
                'tipo' => 'lembrete',
                'mensagem' => 'Oi João! Lembrando que sua parcela de R$ 300,00 vence amanhã (29/08). Link do boleto: bit.ly/bol2024002',
                'status' => 'programado',
                'enviado_em' => null,
                'lido' => false,
                'respondido' => false
            ],
            [
                'id' => 3,
                'cliente' => 'Ana Paula Costa',
                'telefone' => '(11) 66666-6666',
                'tipo' => 'atraso',
                'mensagem' => 'Ana Paula, sua parcela está em atraso há 10 dias. Valor atualizado: R$ 220,00. Regularize para evitar negativação.',
                'status' => 'enviado',
                'enviado_em' => '2024-08-28 14:30:00',
                'lido' => true,
                'respondido' => true
            ]
        ];

        return view('financial.notifications', compact('notifications'));
    }

    public function generateBoleto($id)
    {
        // Simula geração de boleto para uma parcela específica
        $boleto = [
            'id' => 'BOL-2024-' . str_pad($id, 3, '0', STR_PAD_LEFT),
            'nosso_numero' => '00000' . $id . '508',
            'linha_digitavel' => '34191.09008 61207.954566 00000.142508 1 95470000014962',
            'codigo_barras' => '34191954700000149620000061207954560000014250',
            'cliente' => 'Maria Silva Santos',
            'cpf' => '123.456.789-00',
            'endereco' => 'Rua das Palmeiras, 456 - Jardim América',
            'cidade' => 'São Paulo/SP - CEP: 04567-890',
            'telefone' => '(11) 99999-9999',
            'vencimento' => now()->addDays(30)->format('Y-m-d'),
            'valor' => 142.50,
            'juros' => 7.12,
            'valor_total' => 149.62,
            'gerado_em' => now()->format('Y-m-d H:i:s'),
            'descricao' => 'Venda #VND-2024-001 - Parcela 1/3',
            'observacoes' => 'Cliente preferencial - desconto aplicado'
        ];
        
        return response()->json([
            'success' => true,
            'message' => 'Boleto gerado com sucesso!',
            'boleto' => $boleto,
            'pdf_url' => route('financial.boleto-pdf', $boleto['id'])
        ]);
    }
    
    public function boletoPdf($id)
    {
        // Busca dados do boleto (simulado)
        $boleto = [
            'id' => $id,
            'nosso_numero' => '00000142508',
            'linha_digitavel' => '34191.09008 61207.954566 00000.142508 1 95470000014962',
            'codigo_barras' => '34191954700000149620000061207954560000014250',
            'cliente' => 'Maria Silva Santos',
            'cpf' => '123.456.789-00',
            'endereco' => 'Rua das Palmeiras, 456 - Jardim América',
            'cidade' => 'São Paulo/SP - CEP: 04567-890',
            'telefone' => '(11) 99999-9999',
            'vencimento' => '2024-08-30',
            'valor' => 142.50,
            'juros' => 7.12,
            'valor_total' => 149.62,
            'gerado_em' => now()->format('Y-m-d H:i:s'),
            'descricao' => 'Venda #VND-2024-001 - Parcela 1/3',
            'observacoes' => 'Cliente preferencial - desconto aplicado'
        ];
        
        return view('financial.boleto-pdf', compact('boleto'));
    }

    public function sendWhatsApp($id)
    {
        // Simula envio via WhatsApp
        return response()->json([
            'success' => true,
            'message' => 'Mensagem enviada via WhatsApp com sucesso!',
            'timestamp' => now()->format('d/m/Y H:i:s')
        ]);
    }

    public function receivePayment(Request $request)
    {
        $validated = $request->validate([
            'receivable_id' => 'required|integer',
            'valor' => 'nullable|numeric|min:0',
        ]);

        $tenantId = session('tenant_id');

        $query = ContaReceber::where('id', $validated['receivable_id']);
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        $receivable = $query->first();
        if ($receivable) {
            $receivable->update([
                'status' => 'pago',
                'data_pagamento' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pagamento registrado com sucesso!',
            'valor_recebido' => $validated['valor'] ?? null,
            'data_recebimento' => now()->format('d/m/Y H:i:s')
        ]);
    }
}
