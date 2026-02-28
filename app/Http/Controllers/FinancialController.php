<?php

namespace App\Http\Controllers;

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

    public function receivables()
    {
        // Contas a receber
        $receivables = [
            [
                'id' => 1,
                'cliente' => 'Maria Silva Santos',
                'telefone' => '(11) 99999-9999',
                'venda_id' => 'VD-2024-045',
                'parcela' => '3/6',
                'valor_parcela' => 142.50,
                'valor_total' => 850.00,
                'vencimento' => '2024-08-30',
                'status' => 'vencida',
                'dias_atraso' => 5,
                'juros' => 7.12,
                'valor_atualizado' => 149.62
            ],
            [
                'id' => 2,
                'cliente' => 'João Silva',
                'telefone' => '(11) 88888-8888',
                'venda_id' => 'VD-2024-048',
                'parcela' => '1/4',
                'valor_parcela' => 300.00,
                'valor_total' => 1200.00,
                'vencimento' => '2024-08-29',
                'status' => 'vence_hoje',
                'dias_atraso' => 0,
                'juros' => 0.00,
                'valor_atualizado' => 300.00
            ],
            [
                'id' => 3,
                'cliente' => 'Carlos Lima',
                'telefone' => '(11) 77777-7777',
                'venda_id' => 'VD-2024-052',
                'parcela' => '2/5',
                'valor_parcela' => 130.00,
                'valor_total' => 650.00,
                'vencimento' => '2024-09-05',
                'status' => 'em_dia',
                'dias_atraso' => 0,
                'juros' => 0.00,
                'valor_atualizado' => 130.00
            ],
            [
                'id' => 4,
                'cliente' => 'Ana Paula Costa',
                'telefone' => '(11) 66666-6666',
                'venda_id' => 'VD-2024-041',
                'parcela' => '4/6',
                'valor_parcela' => 200.00,
                'valor_total' => 1200.00,
                'vencimento' => '2024-08-25',
                'status' => 'vencida',
                'dias_atraso' => 10,
                'juros' => 20.00,
                'valor_atualizado' => 220.00
            ],
            [
                'id' => 5,
                'cliente' => 'Roberto Santos',
                'telefone' => '(11) 55555-5555',
                'venda_id' => 'VD-2024-039',
                'parcela' => '1/3',
                'valor_parcela' => 250.00,
                'valor_total' => 750.00,
                'vencimento' => '2024-09-01',
                'status' => 'vence_semana',
                'dias_atraso' => 0,
                'juros' => 0.00,
                'valor_atualizado' => 250.00
            ]
        ];

        return view('financial.receivables', compact('receivables'));
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
        // Simula recebimento de pagamento
        return response()->json([
            'success' => true,
            'message' => 'Pagamento registrado com sucesso!',
            'valor_recebido' => $request->valor,
            'data_recebimento' => now()->format('d/m/Y H:i:s')
        ]);
    }
}
