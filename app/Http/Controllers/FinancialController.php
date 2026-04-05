<?php

namespace App\Http\Controllers;

use App\Jobs\SendWhatsappNotificationJob;
use App\Models\PedidoVendaParcela;
use App\Models\Pessoa;
use App\Models\Prescricao;
use App\Models\WhatsappNotification;
use App\Models\WhatsappTemplate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $tenantId = session('tenant_id');
        $locationId = session('location_id');
        $userLocations = session('user_locations', []);

        $locationIds = $this->resolveLocationIdsFromSession($tenantId, $locationId, $userLocations);

        $defaultTemplates = [
            'crediario_vencimento_hoje' => [
                'titulo' => 'Vencimento Hoje (Crediário)',
                'mensagem' => 'Olá {cliente}! Sua parcela {parcela} de {total_parcelas} no valor de R$ {valor} vence hoje ({data}). Evite juros pagando até às 23:59h. Qualquer dúvida, estamos à disposição!',
            ],
            'crediario_atraso' => [
                'titulo' => 'Cobrança em Atraso (Crediário)',
                'mensagem' => '{cliente}, identificamos que sua parcela {parcela} de {total_parcelas} está em atraso há {dias} dias. Valor: R$ {valor}. Regularize para evitar restrições. Dúvidas? Fale conosco.',
            ],
            'crediario_lembrete_amanha' => [
                'titulo' => 'Lembrete Amanhã (Crediário)',
                'mensagem' => 'Oi {cliente}! Lembrando que sua parcela {parcela} de {total_parcelas} no valor de R$ {valor} vence amanhã ({data}). Se precisar, chame a gente por aqui.',
            ],
            'receita_validade' => [
                'titulo' => 'Validade da Receita',
                'mensagem' => 'Olá {cliente}! Sua receita emitida em {data_emissao} vence em {data}. Caso precise renovar, agende um atendimento conosco.',
            ],
        ];

        $templatesFromDb = collect();
        if ($tenantId) {
            $templatesFromDb = WhatsappTemplate::where('tenant_id', $tenantId)
                ->whereNull('deleted_at')
                ->when(! empty($locationIds), function ($q) use ($locationIds) {
                    $q->where(function ($q2) use ($locationIds) {
                        $q2->whereIn('location_id', $locationIds)->orWhereNull('location_id');
                    });
                })
                ->orderByRaw('location_id is null')
                ->orderBy('id')
                ->get();
        }

        $templates = collect($defaultTemplates)->map(function (array $t, string $tipo) use ($templatesFromDb) {
            $db = $templatesFromDb->firstWhere('tipo', $tipo);
            if (! $db) {
                return [
                    'tipo' => $tipo,
                    'titulo' => $t['titulo'],
                    'mensagem' => $t['mensagem'],
                    'ativo' => true,
                ];
            }

            return [
                'tipo' => $tipo,
                'titulo' => $db->titulo,
                'mensagem' => $db->mensagem,
                'ativo' => (bool) $db->ativo,
            ];
        })->values();

        $tz = 'America/Manaus';
        $driver = DB::connection()->getDriverName();
        $dbHojeStr = null;

        if ($driver === 'pgsql') {
            $row = DB::selectOne("select (now() at time zone 'America/Manaus')::date as d");
            $dbHojeStr = $row?->d ? (string) $row->d : null;
        }

        $hoje = $dbHojeStr ? Carbon::parse($dbHojeStr, $tz)->startOfDay() : Carbon::now($tz)->startOfDay();
        $amanha = $hoje->copy()->addDay();
        $hojeStr = $hoje->toDateString();
        $amanhaStr = $amanha->toDateString();

        $parcelasQuery = PedidoVendaParcela::with(['pedido.cliente'])
            ->whereNull('deleted_at')
            ->when($tenantId, function ($q) use ($tenantId) {
                $q->where(function ($q2) use ($tenantId) {
                    $q2->where('tenant_id', $tenantId)->orWhereNull('tenant_id');
                });
            })
            ->when(! empty($locationIds), function ($q) use ($locationIds) {
                $q->where(function ($q2) use ($locationIds) {
                    $q2->whereIn('location_id', $locationIds)->orWhereNull('location_id');
                });
            })
            ->where(function ($q) {
                $q->whereNull('status')->orWhereNotIn('status', ['paga', 'pago']);
            });

        $parcelasVencimentoHojeQuery = (clone $parcelasQuery)->whereDate('vencimento_em', $hojeStr);
        $parcelasVencemAmanhaQuery = (clone $parcelasQuery)->whereDate('vencimento_em', $amanhaStr);
        $parcelasAtrasadasQuery = (clone $parcelasQuery)->whereDate('vencimento_em', '<', $hojeStr)->orderBy('vencimento_em');

        $parcelasVencimentoHoje = $parcelasVencimentoHojeQuery->get();
        $parcelasVencemAmanha = $parcelasVencemAmanhaQuery->get();
        $parcelasAtrasadas = $parcelasAtrasadasQuery->get();
        $receitasVencendo = collect();
        if ($tenantId) {
            $prescricoes = Prescricao::with('paciente')
                ->whereNull('deleted_at')
                ->whereNotNull('validade_dias')
                ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
                ->when(! empty($locationIds), function ($q) use ($locationIds) {
                    $q->where(function ($q2) use ($locationIds) {
                        $q2->whereIn('location_id', $locationIds)->orWhereNull('location_id');
                    });
                })
                ->orderByDesc('created_at')
                ->limit(200)
                ->get();

            $receitasVencendo = $prescricoes->filter(function (Prescricao $p) use ($hoje) {
                if (! $p->created_at || ! $p->validade_dias) {
                    return false;
                }

                $vencimento = $p->created_at->copy()->startOfDay()->addDays((int) $p->validade_dias);
                $dias = $hoje->diffInDays($vencimento, false);

                return $dias >= 0 && $dias <= 7;
            })->values();
        }

        $eligibles = collect();

        $eligibles = $eligibles->concat($parcelasVencimentoHoje->map(function (PedidoVendaParcela $parcela) {
            $cliente = $parcela->pedido && $parcela->pedido->cliente ? $parcela->pedido->cliente : null;

            return [
                'tipo' => 'vencimento',
                'template_tipo' => 'crediario_vencimento_hoje',
                'pessoa_id' => $cliente?->id,
                'cliente' => $cliente?->nome ?? 'Cliente não informado',
                'telefone' => $cliente?->telefone_formatado ?? $cliente?->telefone ?? null,
                'referencia_tipo' => 'parcela',
                'referencia_id' => $parcela->id,
                'mensagem_preview' => null,
            ];
        }));

        $eligibles = $eligibles->concat($parcelasAtrasadas->map(function (PedidoVendaParcela $parcela) {
            $cliente = $parcela->pedido && $parcela->pedido->cliente ? $parcela->pedido->cliente : null;

            return [
                'tipo' => 'atraso',
                'template_tipo' => 'crediario_atraso',
                'pessoa_id' => $cliente?->id,
                'cliente' => $cliente?->nome ?? 'Cliente não informado',
                'telefone' => $cliente?->telefone_formatado ?? $cliente?->telefone ?? null,
                'referencia_tipo' => 'parcela',
                'referencia_id' => $parcela->id,
                'mensagem_preview' => null,
            ];
        }));

        $eligibles = $eligibles->concat($parcelasVencemAmanha->map(function (PedidoVendaParcela $parcela) {
            $cliente = $parcela->pedido && $parcela->pedido->cliente ? $parcela->pedido->cliente : null;

            return [
                'tipo' => 'lembrete',
                'template_tipo' => 'crediario_lembrete_amanha',
                'pessoa_id' => $cliente?->id,
                'cliente' => $cliente?->nome ?? 'Cliente não informado',
                'telefone' => $cliente?->telefone_formatado ?? $cliente?->telefone ?? null,
                'referencia_tipo' => 'parcela',
                'referencia_id' => $parcela->id,
                'mensagem_preview' => null,
            ];
        }));

        $eligibles = $eligibles->concat($receitasVencendo->map(function (Prescricao $prescricao) {
            $paciente = $prescricao->paciente;

            return [
                'tipo' => 'lembrete',
                'template_tipo' => 'receita_validade',
                'pessoa_id' => $paciente?->id,
                'cliente' => $paciente?->nome ?? 'Cliente não informado',
                'telefone' => $paciente?->telefone_formatado ?? $paciente?->telefone ?? null,
                'referencia_tipo' => 'prescricao',
                'referencia_id' => $prescricao->id,
                'mensagem_preview' => null,
            ];
        }));

        $eligibleCounts = [
            'vencimento_hoje' => $parcelasVencimentoHoje->count(),
            'atraso' => $parcelasAtrasadas->count(),
            'lembrete_amanha' => $parcelasVencemAmanha->count(),
            'receita_validade' => $receitasVencendo->count(),
        ];

        $historyQuery = WhatsappNotification::with('pessoa')
            ->whereNull('deleted_at')
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->when(! empty($locationIds), function ($q) use ($locationIds) {
                $q->where(function ($q2) use ($locationIds) {
                    $q2->whereIn('location_id', $locationIds)->orWhereNull('location_id');
                });
            })
            ->orderByDesc('id')
            ->limit(200);

        $history = $historyQuery->get();

        $notifications = $history->map(function (WhatsappNotification $n) {
            $pessoa = $n->pessoa;
            $telefone = $n->telefone ?: ($pessoa?->telefone_formatado ?? $pessoa?->telefone);

            return [
                'id' => $n->id,
                'cliente' => $pessoa?->nome ?? 'Cliente não informado',
                'telefone' => $telefone,
                'tipo' => $this->mapTipoForView($n->tipo),
                'mensagem' => $n->mensagem,
                'status' => $n->status,
                'enviado_em' => $n->enviado_em,
                'wa_url' => $n->wa_url,
                'erro' => $n->erro,
                'lido' => (bool) $n->lido_em,
                'respondido' => (bool) $n->respondido_em,
            ];
        })->toArray();

        return view('financial.notifications', [
            'notifications' => $notifications,
            'eligibles' => $eligibles->values()->toArray(),
            'eligibleCounts' => $eligibleCounts,
            'templates' => $templates->toArray(),
            'templatesPage' => false,
        ]);
    }

    public function templates()
    {
        $tenantId = session('tenant_id');
        $locationId = session('location_id');
        $userLocations = session('user_locations', []);

        $locationIds = $this->resolveLocationIdsFromSession($tenantId, $locationId, $userLocations);

        $defaultTemplates = [
            'crediario_vencimento_hoje' => [
                'titulo' => 'Vencimento Hoje (Crediário)',
                'mensagem' => 'Olá {cliente}! Sua parcela {parcela} de {total_parcelas} no valor de R$ {valor} vence hoje ({data}). Evite juros pagando até às 23:59h. Qualquer dúvida, estamos à disposição!',
            ],
            'crediario_atraso' => [
                'titulo' => 'Cobrança em Atraso (Crediário)',
                'mensagem' => '{cliente}, identificamos que sua parcela {parcela} de {total_parcelas} está em atraso há {dias} dias. Valor: R$ {valor}. Regularize para evitar restrições. Dúvidas? Fale conosco.',
            ],
            'crediario_lembrete_amanha' => [
                'titulo' => 'Lembrete Amanhã (Crediário)',
                'mensagem' => 'Oi {cliente}! Lembrando que sua parcela {parcela} de {total_parcelas} no valor de R$ {valor} vence amanhã ({data}). Se precisar, chame a gente por aqui.',
            ],
            'receita_validade' => [
                'titulo' => 'Validade da Receita',
                'mensagem' => 'Olá {cliente}! Sua receita emitida em {data_emissao} vence em {data}. Caso precise renovar, agende um atendimento conosco.',
            ],
        ];

        $templatesFromDb = collect();
        if ($tenantId) {
            $templatesFromDb = WhatsappTemplate::where('tenant_id', $tenantId)
                ->whereNull('deleted_at')
                ->when(! empty($locationIds), function ($q) use ($locationIds) {
                    $q->where(function ($q2) use ($locationIds) {
                        $q2->whereIn('location_id', $locationIds)->orWhereNull('location_id');
                    });
                })
                ->orderByRaw('location_id is null')
                ->orderBy('id')
                ->get();
        }

        $templates = collect($defaultTemplates)->map(function (array $t, string $tipo) use ($templatesFromDb) {
            $db = $templatesFromDb->firstWhere('tipo', $tipo);
            if (! $db) {
                return [
                    'tipo' => $tipo,
                    'titulo' => $t['titulo'],
                    'mensagem' => $t['mensagem'],
                    'ativo' => true,
                ];
            }

            return [
                'tipo' => $tipo,
                'titulo' => $db->titulo,
                'mensagem' => $db->mensagem,
                'ativo' => (bool) $db->ativo,
            ];
        })->values();

        return view('financial.notifications', [
            'notifications' => [],
            'eligibles' => [],
            'eligibleCounts' => [],
            'templates' => $templates->toArray(),
            'templatesPage' => true,
        ]);
    }

    public function saveTemplates(Request $request)
    {
        $tenantId = session('tenant_id');
        $locationId = session('location_id');

        $validated = $request->validate([
            'templates' => 'required|array',
            'templates.*.tipo' => 'required|string|max:50',
            'templates.*.titulo' => 'required|string|max:120',
            'templates.*.mensagem' => 'required|string',
            'templates.*.ativo' => 'nullable|boolean',
        ], [
            'required' => 'O campo :attribute é obrigatório.',
            'string' => 'O campo :attribute deve ser um texto.',
            'boolean' => 'O campo :attribute deve ser verdadeiro ou falso.',
            'max.string' => 'O campo :attribute não pode ter mais que :max caracteres.',
            'array' => 'O campo :attribute deve ser uma lista.',
        ]);

        foreach ($validated['templates'] as $t) {
            WhatsappTemplate::updateOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'location_id' => $locationId,
                    'tipo' => $t['tipo'],
                ],
                [
                    'titulo' => $t['titulo'],
                    'mensagem' => $t['mensagem'],
                    'ativo' => array_key_exists('ativo', $t) ? (bool) $t['ativo'] : true,
                ]
            );
        }

        return back()->with('success', 'Templates atualizados com sucesso!');
    }

    public function scheduleBatch(Request $request)
    {
        $tenantId = session('tenant_id');
        $locationId = session('location_id');

        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.template_tipo' => 'required|string|max:50',
            'items.*.referencia_tipo' => 'nullable|string|max:50',
            'items.*.referencia_id' => 'nullable|integer',
            'items.*.pessoa_id' => 'nullable|integer',
            'items.*.telefone' => 'nullable|string|max:40',
        ], [
            'required' => 'O campo :attribute é obrigatório.',
            'string' => 'O campo :attribute deve ser um texto.',
            'integer' => 'O campo :attribute deve ser um número inteiro.',
            'max.string' => 'O campo :attribute não pode ter mais que :max caracteres.',
            'array' => 'O campo :attribute deve ser uma lista.',
        ]);

        $created = 0;
        $queued = 0;
        $errors = 0;

        foreach ($validated['items'] as $item) {
            $payload = $this->buildWhatsappPayload(
                $tenantId,
                $locationId,
                $item['template_tipo'],
                $item['referencia_tipo'] ?? null,
                $item['referencia_id'] ?? null,
                $item['pessoa_id'] ?? null,
                $item['telefone'] ?? null
            );

            if (! $payload['ok']) {
                $errors++;

                WhatsappNotification::create([
                    'tenant_id' => $tenantId,
                    'location_id' => $locationId,
                    'user_id' => auth()->id(),
                    'pessoa_id' => $payload['pessoa_id'],
                    'telefone' => $payload['telefone'],
                    'tipo' => $item['template_tipo'],
                    'mensagem' => $payload['mensagem'],
                    'status' => 'falhou',
                    'erro' => $payload['erro'],
                    'referencia_tipo' => $item['referencia_tipo'] ?? null,
                    'referencia_id' => $item['referencia_id'] ?? null,
                ]);

                continue;
            }

            $notification = WhatsappNotification::create([
                'tenant_id' => $tenantId,
                'location_id' => $locationId,
                'user_id' => auth()->id(),
                'pessoa_id' => $payload['pessoa_id'],
                'telefone' => $payload['telefone'],
                'tipo' => $item['template_tipo'],
                'mensagem' => $payload['mensagem'],
                'status' => 'programado',
                'referencia_tipo' => $item['referencia_tipo'] ?? null,
                'referencia_id' => $item['referencia_id'] ?? null,
            ]);

            $created++;
            SendWhatsappNotificationJob::dispatch((int) $notification->id);
            $queued++;
        }

        return response()->json([
            'success' => true,
            'created' => $created,
            'queued' => $queued,
            'errors' => $errors,
        ]);
    }

    public function sendNotification(Request $request)
    {
        $tenantId = session('tenant_id');
        $locationId = session('location_id');

        $validated = $request->validate([
            'template_tipo' => 'required|string|max:50',
            'referencia_tipo' => 'nullable|string|max:50',
            'referencia_id' => 'nullable|integer',
            'pessoa_id' => 'nullable|integer',
            'telefone' => 'nullable|string|max:40',
        ], [
            'required' => 'O campo :attribute é obrigatório.',
            'string' => 'O campo :attribute deve ser um texto.',
            'integer' => 'O campo :attribute deve ser um número inteiro.',
            'max.string' => 'O campo :attribute não pode ter mais que :max caracteres.',
        ], [
            'template_tipo' => 'Template',
            'referencia_tipo' => 'Referência',
            'referencia_id' => 'ID da Referência',
            'pessoa_id' => 'Cliente',
            'telefone' => 'Telefone',
        ]);

        $payload = $this->buildWhatsappPayload(
            $tenantId,
            $locationId,
            $validated['template_tipo'],
            $validated['referencia_tipo'] ?? null,
            $validated['referencia_id'] ?? null,
            $validated['pessoa_id'] ?? null,
            $validated['telefone'] ?? null
        );

        if (! $payload['ok']) {
            return response()->json([
                'success' => false,
                'message' => $payload['erro'] ?? 'Erro ao montar mensagem.',
            ], 422);
        }

        $mensagem = $payload['mensagem'];
        $waPhone = $payload['telefone'];
        $waUrl = 'https://wa.me/' . $waPhone . '?text=' . urlencode($mensagem);

        $notification = WhatsappNotification::create([
            'tenant_id' => $tenantId,
            'location_id' => $locationId,
            'user_id' => auth()->id(),
            'pessoa_id' => $payload['pessoa_id'],
            'telefone' => $waPhone,
            'tipo' => $validated['template_tipo'],
            'mensagem' => $mensagem,
            'status' => 'enviado',
            'enviado_em' => now(),
            'referencia_tipo' => $validated['referencia_tipo'] ?? null,
            'referencia_id' => $validated['referencia_id'] ?? null,
            'wa_url' => $waUrl,
        ]);

        return response()->json([
            'success' => true,
            'id' => $notification->id,
            'wa_url' => $waUrl,
        ]);
    }

    public function resendNotification(string $id)
    {
        $tenantId = session('tenant_id');
        $locationId = session('location_id');

        if (! $tenantId) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant não informado na sessão.',
            ], 403);
        }

        $original = WhatsappNotification::with('pessoa')
            ->where('tenant_id', $tenantId)
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->first();

        if (! $original) {
            return response()->json([
                'success' => false,
                'message' => 'Mensagem não encontrada.',
            ], 404);
        }

        $telefoneRaw = $original->telefone ?: ($original->pessoa?->telefone_formatado ?? $original->pessoa?->telefone);
        $waPhone = $this->normalizeWhatsappPhone($telefoneRaw);
        if (! $waPhone) {
            return response()->json([
                'success' => false,
                'message' => 'Telefone do cliente não informado.',
            ], 422);
        }

        $mensagem = (string) ($original->mensagem ?? '');
        if ($mensagem === '') {
            return response()->json([
                'success' => false,
                'message' => 'Mensagem vazia.',
            ], 422);
        }

        $waUrl = 'https://wa.me/' . $waPhone . '?text=' . urlencode($mensagem);

        $notification = WhatsappNotification::create([
            'tenant_id' => $tenantId,
            'location_id' => $locationId,
            'user_id' => auth()->id(),
            'pessoa_id' => $original->pessoa_id,
            'telefone' => $waPhone,
            'tipo' => $original->tipo,
            'mensagem' => $mensagem,
            'status' => 'enviado',
            'enviado_em' => now(),
            'referencia_tipo' => 'whatsapp_notification',
            'referencia_id' => $original->id,
            'wa_url' => $waUrl,
        ]);

        return response()->json([
            'success' => true,
            'id' => $notification->id,
            'wa_url' => $waUrl,
        ]);
    }

    private function buildWhatsappPayload(
        $tenantId,
        $locationId,
        string $templateTipo,
        ?string $referenciaTipo,
        $referenciaId,
        $pessoaId,
        ?string $telefone
    ): array {
        $templateText = $this->getTemplateMessage($tenantId, $locationId, $templateTipo);

        $pessoa = null;
        if (! empty($pessoaId)) {
            $pessoa = Pessoa::where('tenant_id', $tenantId)->where('id', $pessoaId)->first();
        }

        $placeholders = [
            'cliente' => $pessoa?->nome ?? 'Cliente',
        ];

        if ($referenciaTipo === 'parcela' && ! empty($referenciaId)) {
            $parcela = PedidoVendaParcela::with('pedido.cliente')
                ->where('tenant_id', $tenantId)
                ->where('id', $referenciaId)
                ->first();

            $cliente = $parcela?->pedido?->cliente;
            $pessoa = $pessoa ?: $cliente;

            $diasAtraso = null;
            if ($parcela && $parcela->vencimento_em) {
                $diasAtraso = Carbon::parse($parcela->vencimento_em)->startOfDay()->diffInDays(Carbon::today(), false);
                if ($diasAtraso < 0) {
                    $diasAtraso = 0;
                }
            }

            $placeholders = array_merge($placeholders, [
                'cliente' => $pessoa?->nome ?? 'Cliente',
                'valor' => $parcela ? number_format((float) $parcela->valor, 2, ',', '.') : '0,00',
                'data' => $parcela && $parcela->vencimento_em ? Carbon::parse($parcela->vencimento_em)->format('d/m/Y') : '',
                'parcela' => $parcela?->numero_parcela ?? '',
                'total_parcelas' => $parcela?->total_parcelas ?? '',
                'dias' => (string) ($diasAtraso ?? 0),
            ]);
        }

        if ($referenciaTipo === 'prescricao' && ! empty($referenciaId)) {
            $prescricao = Prescricao::with('paciente')
                ->where('tenant_id', $tenantId)
                ->where('id', $referenciaId)
                ->first();

            $pessoa = $pessoa ?: $prescricao?->paciente;

            $dataEmissao = $prescricao?->created_at ? $prescricao->created_at->format('d/m/Y') : '';
            $dataVenc = '';
            if ($prescricao?->created_at && $prescricao?->validade_dias) {
                $dataVenc = $prescricao->created_at->copy()->startOfDay()->addDays((int) $prescricao->validade_dias)->format('d/m/Y');
            }

            $placeholders = array_merge($placeholders, [
                'cliente' => $pessoa?->nome ?? 'Cliente',
                'data_emissao' => $dataEmissao,
                'data' => $dataVenc,
            ]);
        }

        $telefoneFinal = $telefone ?? ($pessoa?->telefone ?? null);
        $waPhone = $this->normalizeWhatsappPhone($telefoneFinal);
        $mensagem = $this->renderTemplate($templateText, $placeholders);

        if (! $waPhone) {
            return [
                'ok' => false,
                'pessoa_id' => $pessoa?->id,
                'telefone' => $telefoneFinal,
                'mensagem' => $mensagem,
                'erro' => 'Telefone do cliente não informado.',
            ];
        }

        if ($mensagem === '') {
            return [
                'ok' => false,
                'pessoa_id' => $pessoa?->id,
                'telefone' => $waPhone,
                'mensagem' => $mensagem,
                'erro' => 'Mensagem vazia.',
            ];
        }

        return [
            'ok' => true,
            'pessoa_id' => $pessoa?->id,
            'telefone' => $waPhone,
            'mensagem' => $mensagem,
            'erro' => null,
        ];
    }

    private function getTemplateMessage($tenantId, $locationId, string $tipo): string
    {
        $template = null;
        if ($tenantId) {
            $template = WhatsappTemplate::where('tenant_id', $tenantId)
                ->where('location_id', $locationId)
                ->where('tipo', $tipo)
                ->whereNull('deleted_at')
                ->first();
        }

        $defaultMap = [
            'crediario_vencimento_hoje' => 'Olá {cliente}! Sua parcela {parcela} de {total_parcelas} no valor de R$ {valor} vence hoje ({data}). Evite juros pagando até às 23:59h. Qualquer dúvida, estamos à disposição!',
            'crediario_atraso' => '{cliente}, identificamos que sua parcela {parcela} de {total_parcelas} está em atraso há {dias} dias. Valor: R$ {valor}. Regularize para evitar restrições. Dúvidas? Fale conosco.',
            'crediario_lembrete_amanha' => 'Oi {cliente}! Lembrando que sua parcela {parcela} de {total_parcelas} no valor de R$ {valor} vence amanhã ({data}). Se precisar, chame a gente por aqui.',
            'receita_validade' => 'Olá {cliente}! Sua receita emitida em {data_emissao} vence em {data}. Caso precise renovar, agende um atendimento conosco.',
        ];

        return $template ? $template->mensagem : ($defaultMap[$tipo] ?? '{cliente}, segue uma mensagem.');
    }

    private function resolveLocationIdsFromSession($tenantId, $locationId, array $userLocations): array
    {
        $ids = [];

        if (! empty($tenantId)) {
            foreach ($userLocations as $row) {
                if (! is_array($row)) {
                    continue;
                }

                if (($row['tenant_id'] ?? null) != $tenantId) {
                    continue;
                }

                $candidate = $row['location_id'] ?? ($row['location']['id'] ?? null);
                if (! empty($candidate)) {
                    $ids[] = (int) $candidate;
                }
            }
        }

        if (! empty($locationId)) {
            $ids[] = (int) $locationId;
        }

        return array_values(array_unique($ids));
    }

    private function renderTemplate(string $template, array $data): string
    {
        $replaces = [];
        foreach ($data as $key => $value) {
            $replaces['{' . $key . '}'] = (string) $value;
        }

        return strtr($template, $replaces);
    }

    private function normalizeWhatsappPhone(?string $raw): ?string
    {
        if (! $raw) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $raw);
        if (! $digits) {
            return null;
        }

        if (str_starts_with($digits, '55')) {
            return $digits;
        }

        if (strlen($digits) === 10 || strlen($digits) === 11) {
            return '55' . $digits;
        }

        return $digits;
    }

    private function mapTipoForView(string $tipo): string
    {
        return match ($tipo) {
            'crediario_vencimento_hoje' => 'vencimento',
            'crediario_atraso' => 'atraso',
            'crediario_lembrete_amanha' => 'lembrete',
            'receita_validade' => 'lembrete',
            default => 'personalizada',
        };
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
