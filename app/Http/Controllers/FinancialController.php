<?php

namespace App\Http\Controllers;

use App\Jobs\SendWhatsappNotificationJob;
use App\Models\PedidoVenda;
use App\Models\PedidoVendaParcela;
use App\Models\Pessoa;
use App\Models\Prescricao;
use App\Models\WhatsappNotification;
use App\Models\WhatsappTemplate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class FinancialController extends Controller
{
    public function index()
    {
        $tenantId = session('tenant_id');
        $locationId = session('location_id');
        $userLocations = session('user_locations', []);
        $locationIds = $this->resolveLocationIdsFromSession($tenantId, $locationId, $userLocations);

        $tz = 'America/Manaus';
        $driver = DB::connection()->getDriverName();

        $dbHojeStr = Carbon::now($tz)->toDateString();
        if ($driver === 'pgsql') {
            $row = DB::selectOne("select (now() at time zone 'America/Manaus')::date as d");
            if ($row && isset($row->d)) {
                $dbHojeStr = (string) $row->d;
            }
        }

        $today = Carbon::parse($dbHojeStr, $tz)->startOfDay();
        $tomorrow = $today->copy()->addDay();
        $weekEnd = $today->copy()->addDays(7);

        $closedStatuses = ['pago', 'paga', 'cancelado', 'cancelada'];

        $baseParcelas = PedidoVendaParcela::query()
            ->whereNull('deleted_at')
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->when(! empty($locationIds), function ($q) use ($locationIds) {
                $q->where(function ($q2) use ($locationIds) {
                    $q2->whereIn('location_id', $locationIds)->orWhereNull('location_id');
                });
            });

        $unpaidParcelas = (clone $baseParcelas)
            ->whereNull('pago_em')
            ->whereNotIn('status', $closedStatuses);

        $totalReceber = (float) (clone $unpaidParcelas)->sum('valor');
        $vencidasValor = (float) (clone $unpaidParcelas)->where('vencimento_em', '<', $today->toDateString())->sum('valor');
        $venceHojeValor = (float) (clone $unpaidParcelas)->where('vencimento_em', '=', $today->toDateString())->sum('valor');
        $venceSemanaValor = (float) (clone $unpaidParcelas)->whereBetween('vencimento_em', [$tomorrow->toDateString(), $weekEnd->toDateString()])->sum('valor');

        $vencidasCount = (int) (clone $unpaidParcelas)->where('vencimento_em', '<', $today->toDateString())->count();
        $venceHojeCount = (int) (clone $unpaidParcelas)->where('vencimento_em', '=', $today->toDateString())->count();
        $venceSemanaCount = (int) (clone $unpaidParcelas)->whereBetween('vencimento_em', [$tomorrow->toDateString(), $weekEnd->toDateString()])->count();

        $monthStart = $today->copy()->startOfMonth();
        $monthEnd = $today->copy()->endOfMonth();

        $recebidoMes = (float) (clone $baseParcelas)
            ->whereNotNull('pago_em')
            ->whereBetween('pago_em', [$monthStart->copy()->startOfDay(), $monthEnd->copy()->endOfDay()])
            ->sum('valor');

        $salesMonthQuery = PedidoVenda::query()
            ->whereNull('deleted_at')
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->when(! empty($locationIds), function ($q) use ($locationIds) {
                $q->where(function ($q2) use ($locationIds) {
                    $q2->whereIn('location_id', $locationIds)->orWhereNull('location_id');
                });
            })
            ->where('status', PedidoVenda::STATUS_FATURADO)
            ->whereBetween('data_pedido', [$monthStart->copy()->startOfDay(), $monthEnd->copy()->endOfDay()]);

        $vendasMesValor = (float) (clone $salesMonthQuery)->sum('valor_total');
        $vendasMesCount = (int) (clone $salesMonthQuery)->count();
        $ticketMedio = (float) (clone $salesMonthQuery)->avg('valor_total');

        $totalClientesCredito = (int) DB::table('pedido_venda_parcela as pvp')
            ->join('pedido_venda as pv', function ($join) {
                $join->on('pv.id', '=', 'pvp.pedido_venda_id')
                    ->on('pv.tenant_id', '=', 'pvp.tenant_id');
            })
            ->whereNull('pvp.deleted_at')
            ->whereNull('pv.deleted_at')
            ->when($tenantId, fn($q) => $q->where('pvp.tenant_id', $tenantId))
            ->when(! empty($locationIds), function ($q) use ($locationIds) {
                $q->where(function ($q2) use ($locationIds) {
                    $q2->whereIn('pvp.location_id', $locationIds)->orWhereNull('pvp.location_id');
                });
            })
            ->whereNull('pvp.pago_em')
            ->whereNotIn('pvp.status', $closedStatuses)
            ->whereNotNull('pv.pessoa_cliente_id')
            ->distinct()
            ->count('pv.pessoa_cliente_id');

        $inadimplencia = 0.0;
        if ($totalReceber > 0) {
            $inadimplencia = round(($vencidasValor / $totalReceber) * 100, 1);
        }

        $financialData = [
            'total_receber' => $totalReceber,
            'vencidas' => $vencidasValor,
            'vence_hoje' => $venceHojeValor,
            'vence_semana' => $venceSemanaValor,
            'vendas_mes' => $vendasMesValor,
            'vendas_mes_count' => $vendasMesCount,
            'recebido_mes' => $recebidoMes,
            'inadimplencia' => $inadimplencia,
            'ticket_medio' => $ticketMedio,
            'total_clientes_credito' => $totalClientesCredito,
        ];

        $alertas = [
            'vencidas' => ['count' => $vencidasCount, 'valor' => $vencidasValor],
            'vence_hoje' => ['count' => $venceHojeCount, 'valor' => $venceHojeValor],
            'vence_semana' => ['count' => $venceSemanaCount, 'valor' => $venceSemanaValor],
        ];

        $chartStart = $today->copy()->startOfMonth()->subMonths(7);
        $chartEnd = $today->copy()->endOfMonth();

        $chartReceiptsTotalsByKey = collect(
            DB::table('pedido_venda_parcela as pvp')
                ->selectRaw(
                    $driver === 'pgsql'
                        ? "to_char((pvp.pago_em at time zone 'America/Manaus'), 'YYYY-MM') as m, sum(pvp.valor) as total"
                        : ($driver === 'mysql'
                            ? "date_format(pvp.pago_em, '%Y-%m') as m, sum(pvp.valor) as total"
                            : "strftime('%Y-%m', pvp.pago_em) as m, sum(pvp.valor) as total")
                )
                ->whereNull('pvp.deleted_at')
                ->when($tenantId, fn($q) => $q->where('pvp.tenant_id', $tenantId))
                ->when(! empty($locationIds), function ($q) use ($locationIds) {
                    $q->where(function ($q2) use ($locationIds) {
                        $q2->whereIn('pvp.location_id', $locationIds)->orWhereNull('pvp.location_id');
                    });
                })
                ->whereNotNull('pvp.pago_em')
                ->whereBetween('pvp.pago_em', [$chartStart->copy()->startOfDay(), $chartEnd->copy()->endOfDay()])
                ->groupBy('m')
                ->get()
        )->mapWithKeys(fn($r) => [(string) $r->m => (float) $r->total]);

        $chartSalesTotalsByKey = collect(
            DB::table('pedido_venda as pv')
                ->selectRaw(
                    $driver === 'pgsql'
                        ? "to_char((pv.data_pedido at time zone 'America/Manaus'), 'YYYY-MM') as m, sum(pv.valor_total) as total"
                        : ($driver === 'mysql'
                            ? "date_format(pv.data_pedido, '%Y-%m') as m, sum(pv.valor_total) as total"
                            : "strftime('%Y-%m', pv.data_pedido) as m, sum(pv.valor_total) as total")
                )
                ->whereNull('pv.deleted_at')
                ->when($tenantId, fn($q) => $q->where('pv.tenant_id', $tenantId))
                ->when(! empty($locationIds), function ($q) use ($locationIds) {
                    $q->where(function ($q2) use ($locationIds) {
                        $q2->whereIn('pv.location_id', $locationIds)->orWhereNull('pv.location_id');
                    });
                })
                ->where('pv.status', PedidoVenda::STATUS_FATURADO)
                ->whereBetween('pv.data_pedido', [$chartStart->copy()->startOfDay(), $chartEnd->copy()->endOfDay()])
                ->groupBy('m')
                ->get()
        )->mapWithKeys(fn($r) => [(string) $r->m => (float) $r->total]);

        $monthLabels = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
        $salesReceiptsChart = [];
        $maxChart = 0.0;

        for ($i = 0; $i < 8; $i++) {
            $m = $chartStart->copy()->addMonths($i);
            $key = $m->format('Y-m');
            $salesTotal = (float) ($chartSalesTotalsByKey[$key] ?? 0);
            $receiptsTotal = (float) ($chartReceiptsTotalsByKey[$key] ?? 0);

            $salesReceiptsChart[] = [
                'label' => $monthLabels[((int) $m->format('n')) - 1] ?? $m->format('m'),
                'sales_total' => $salesTotal,
                'receipts_total' => $receiptsTotal,
            ];

            $maxChart = max($maxChart, $salesTotal, $receiptsTotal);
        }

        $salesReceiptsChart = array_map(function ($row) use ($maxChart) {
            $salesHeight = $maxChart > 0 ? (int) round(($row['sales_total'] / $maxChart) * 100) : 0;
            $receiptsHeight = $maxChart > 0 ? (int) round(($row['receipts_total'] / $maxChart) * 100) : 0;

            $row['sales_height'] = max(3, $salesHeight);
            $row['receipts_height'] = max(3, $receiptsHeight);
            return $row;
        }, $salesReceiptsChart);

        $installmentsRaw = DB::table('pedido_venda_parcela as pvp')
            ->join('pedido_venda as pv', function ($join) {
                $join->on('pv.id', '=', 'pvp.pedido_venda_id')
                    ->on('pv.tenant_id', '=', 'pvp.tenant_id');
            })
            ->leftJoin('pessoa as pe', function ($join) {
                $join->on('pe.id', '=', 'pv.pessoa_cliente_id')
                    ->on('pe.tenant_id', '=', 'pv.tenant_id');
            })
            ->whereNull('pvp.deleted_at')
            ->whereNull('pv.deleted_at')
            ->when($tenantId, fn($q) => $q->where('pvp.tenant_id', $tenantId))
            ->when(! empty($locationIds), function ($q) use ($locationIds) {
                $q->where(function ($q2) use ($locationIds) {
                    $q2->whereIn('pvp.location_id', $locationIds)->orWhereNull('pvp.location_id');
                });
            })
            ->groupBy('pvp.pedido_venda_id', 'pv.id', 'pv.valor_total', 'pe.nome', 'pe.telefone')
            ->selectRaw('pvp.pedido_venda_id as pedido_id')
            ->selectRaw('pv.valor_total as valor_total')
            ->selectRaw('coalesce(pe.nome, ?) as cliente', ['Cliente não informado'])
            ->selectRaw('pe.telefone as telefone')
            ->selectRaw("max(pvp.total_parcelas) as total_parcelas")
            ->selectRaw("sum(case when pvp.pago_em is not null or lower(pvp.status) in ('pago','paga') then 1 else 0 end) as pagas")
            ->selectRaw("min(case when pvp.pago_em is null and lower(pvp.status) not in ('pago','paga','cancelado','cancelada') then pvp.vencimento_em else null end) as proximo_vencimento")
            ->havingRaw("sum(case when pvp.pago_em is null and lower(pvp.status) not in ('pago','paga','cancelado','cancelada') then 1 else 0 end) > 0")
            ->orderByRaw($driver === 'pgsql' ? 'proximo_vencimento asc nulls last' : 'proximo_vencimento asc')
            ->limit(15)
            ->get();

        $paidStatusSet = ['pago', 'paga', 'cancelado', 'cancelada'];

        $pedidoIds = $installmentsRaw->pluck('pedido_id')->filter()->map(fn($v) => (int) $v)->values()->all();
        $nextParcelas = [];
        if (! empty($pedidoIds)) {
            $nextParcelas = PedidoVendaParcela::query()
                ->whereNull('deleted_at')
                ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
                ->when(! empty($locationIds), function ($q) use ($locationIds) {
                    $q->where(function ($q2) use ($locationIds) {
                        $q2->whereIn('location_id', $locationIds)->orWhereNull('location_id');
                    });
                })
                ->whereIn('pedido_venda_id', $pedidoIds)
                ->whereNull('pago_em')
                ->where(function ($q) use ($paidStatusSet) {
                    $q->whereNull('status')->orWhereNotIn(DB::raw('lower(status)'), $paidStatusSet);
                })
                ->orderBy('pedido_venda_id')
                ->orderBy('vencimento_em')
                ->orderBy('numero_parcela')
                ->orderBy('id')
                ->get(['id', 'pedido_venda_id'])
                ->groupBy('pedido_venda_id')
                ->map(fn($rows) => (int) $rows->first()->id)
                ->toArray();
        }

        $installmentSummaries = $installmentsRaw->map(function ($row) use ($tenantId, $today, $tz, $nextParcelas) {
            $cliente = (string) ($row->cliente ?? 'Cliente não informado');
            $initials = collect(preg_split('/\s+/', trim($cliente)))->filter()->take(2)->map(fn($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('');
            if ($initials === '') {
                $initials = 'CL';
            }
            $telefone = $row->telefone ? (string) $row->telefone : null;

            $totalParcelas = (int) ($row->total_parcelas ?? 0);
            $pagas = (int) ($row->pagas ?? 0);
            $progressPct = $totalParcelas > 0 ? (int) floor(($pagas / $totalParcelas) * 100) : 0;

            $proximoVencStr = $row->proximo_vencimento ? (string) $row->proximo_vencimento : null;
            $proximoVenc = $proximoVencStr ? Carbon::parse($proximoVencStr, $tz)->startOfDay() : null;

            $statusLabel = 'Em Dia';
            $badgeClass = 'bg-success';
            $rowClass = '';
            $subLabel = 'Em dia';

            if ($proximoVenc) {
                if ($proximoVenc->lt($today)) {
                    $dias = $proximoVenc->diffInDays($today);
                    $statusLabel = 'Em Atraso';
                    $badgeClass = 'bg-danger';
                    $rowClass = 'table-danger';
                    $subLabel = $dias . ' dias atraso';
                } elseif ($proximoVenc->equalTo($today)) {
                    $statusLabel = 'Vence Hoje';
                    $badgeClass = 'bg-warning';
                    $rowClass = 'table-warning';
                    $subLabel = 'Vence hoje';
                }
            }

            $proximaParcelaId = isset($nextParcelas[(int) $row->pedido_id]) ? (int) $nextParcelas[(int) $row->pedido_id] : null;
            $boletoSecureUrl = null;
            if ($tenantId && $proximaParcelaId) {
                $boletoToken = Crypt::encryptString((int) $tenantId . '|' . (int) $proximaParcelaId);
                $boletoSecureUrl = URL::temporarySignedRoute(
                    'public.boleto.view',
                    now()->addDays(7),
                    ['token' => $boletoToken]
                );
            }

            return [
                'pedido_id' => (int) $row->pedido_id,
                'proxima_parcela_id' => $proximaParcelaId,
                'proxima_parcela_boleto_url' => $boletoSecureUrl,
                'cliente' => $cliente,
                'initials' => $initials,
                'telefone' => $telefone,
                'valor_total' => (float) ($row->valor_total ?? 0),
                'pagas' => $pagas,
                'total_parcelas' => $totalParcelas,
                'progress_pct' => $progressPct,
                'proximo_vencimento' => $proximoVenc ? $proximoVenc->format('d/m/Y') : '-',
                'proximo_vencimento_raw' => $proximoVenc ? $proximoVenc->toDateString() : null,
                'status_label' => $statusLabel,
                'badge_class' => $badgeClass,
                'row_class' => $rowClass,
                'sub_label' => $subLabel,
            ];
        })->values()->toArray();

        return view('financial.index', [
            'financialData' => $financialData,
            'alertas' => $alertas,
            'salesReceiptsChart' => $salesReceiptsChart,
            'installmentSummaries' => $installmentSummaries,
        ]);
    }

    public function receivables()
    {
        $tenantId = session('tenant_id');
        $locationId = session('location_id');
        $userLocations = session('user_locations', []);
        $locationIds = $this->resolveLocationIdsFromSession($tenantId, $locationId, $userLocations);

        $tz = 'America/Manaus';
        $driver = DB::connection()->getDriverName();

        $dbHojeStr = Carbon::now($tz)->toDateString();
        if ($driver === 'pgsql') {
            $row = DB::selectOne("select (now() at time zone 'America/Manaus')::date as d");
            if ($row && isset($row->d)) {
                $dbHojeStr = (string) $row->d;
            }
        }

        $today = Carbon::parse($dbHojeStr, $tz)->startOfDay();
        $tomorrow = $today->copy()->addDay();
        $weekEnd = $today->copy()->addDays(7);

        $paidStatuses = ['pago', 'paga', 'cancelado', 'cancelada'];

        $base = DB::table('pedido_venda_parcela as pvp')
            ->join('pedido_venda as pv', function ($join) {
                $join->on('pv.id', '=', 'pvp.pedido_venda_id')
                    ->on('pv.tenant_id', '=', 'pvp.tenant_id');
            })
            ->leftJoin('pessoa as pe', function ($join) {
                $join->on('pe.id', '=', 'pv.pessoa_cliente_id')
                    ->on('pe.tenant_id', '=', 'pv.tenant_id');
            })
            ->whereNull('pvp.deleted_at')
            ->whereNull('pv.deleted_at')
            ->where('pv.ativo', true)
            ->when($tenantId, fn($q) => $q->where('pvp.tenant_id', $tenantId))
            ->when(! empty($locationIds), function ($q) use ($locationIds) {
                $q->where(function ($q2) use ($locationIds) {
                    $q2->whereIn('pvp.location_id', $locationIds)->orWhereNull('pvp.location_id');
                });
            });

        $unpaid = (clone $base)
            ->whereNull('pvp.pago_em')
            ->where(function ($q) use ($paidStatuses) {
                $q->whereNull('pvp.status')->orWhereNotIn(DB::raw('lower(pvp.status)'), $paidStatuses);
            });

        $summary = [
            'vencidas' => [
                'count' => (int) (clone $unpaid)->where('pvp.vencimento_em', '<', $today->toDateString())->count(),
                'valor' => (float) (clone $unpaid)->where('pvp.vencimento_em', '<', $today->toDateString())->sum('pvp.valor'),
            ],
            'vence_hoje' => [
                'count' => (int) (clone $unpaid)->where('pvp.vencimento_em', '=', $today->toDateString())->count(),
                'valor' => (float) (clone $unpaid)->where('pvp.vencimento_em', '=', $today->toDateString())->sum('pvp.valor'),
            ],
            'vence_semana' => [
                'count' => (int) (clone $unpaid)->whereBetween('pvp.vencimento_em', [$tomorrow->toDateString(), $weekEnd->toDateString()])->count(),
                'valor' => (float) (clone $unpaid)->whereBetween('pvp.vencimento_em', [$tomorrow->toDateString(), $weekEnd->toDateString()])->sum('pvp.valor'),
            ],
            'em_dia' => [
                'count' => (int) (clone $unpaid)->where('pvp.vencimento_em', '>', $weekEnd->toDateString())->count(),
                'valor' => (float) (clone $unpaid)->where('pvp.vencimento_em', '>', $weekEnd->toDateString())->sum('pvp.valor'),
            ],
        ];

        $statusFilter = (string) request()->get('status', '');
        $startDate = (string) request()->get('start_date', '');
        $endDate = (string) request()->get('end_date', '');
        $q = trim((string) request()->get('q', ''));
        $orderBy = (string) request()->get('order_by', 'vencimento');

        $query = (clone $base)
            ->select([
                'pvp.id as parcela_id',
                'pvp.numero_parcela',
                'pvp.total_parcelas',
                'pvp.valor as valor_parcela',
                'pvp.vencimento_em',
                'pvp.pago_em',
                'pvp.status as parcela_status',
                'pv.id as pedido_id',
                'pv.valor_total',
                'pv.data_pedido',
                'pe.nome as cliente_nome',
                'pe.telefone as cliente_telefone',
                'pe.cpf as cliente_cpf',
            ]);

        if ($statusFilter === 'paga') {
            $query->where(function ($q) use ($paidStatuses) {
                $q->whereNotNull('pvp.pago_em')->orWhereIn(DB::raw('lower(pvp.status)'), $paidStatuses);
            });
        } else {
            $query->whereNull('pvp.pago_em')
                ->where(function ($q) use ($paidStatuses) {
                    $q->whereNull('pvp.status')->orWhereNotIn(DB::raw('lower(pvp.status)'), $paidStatuses);
                });

            if ($statusFilter === 'vencida') {
                $query->where('pvp.vencimento_em', '<', $today->toDateString());
            } elseif ($statusFilter === 'vence_hoje') {
                $query->where('pvp.vencimento_em', '=', $today->toDateString());
            } elseif ($statusFilter === 'vence_semana') {
                $query->whereBetween('pvp.vencimento_em', [$tomorrow->toDateString(), $weekEnd->toDateString()]);
            } elseif ($statusFilter === 'em_dia') {
                $query->where('pvp.vencimento_em', '>', $weekEnd->toDateString());
            }
        }

        if ($startDate !== '') {
            $query->where('pvp.vencimento_em', '>=', $startDate);
        }
        if ($endDate !== '') {
            $query->where('pvp.vencimento_em', '<=', $endDate);
        }

        if ($q !== '') {
            $like = $driver === 'pgsql' ? 'ilike' : 'like';
            $query->where(function ($qq) use ($q, $like) {
                $qq->where('pe.nome', $like, '%' . $q . '%')
                    ->orWhere('pv.id', $like, '%' . $q . '%');
            });
        }

        if ($orderBy === 'valor') {
            $query->orderByDesc('pvp.valor')->orderBy('pvp.vencimento_em');
        } elseif ($orderBy === 'cliente') {
            $query->orderBy('pe.nome')->orderBy('pvp.vencimento_em');
        } elseif ($orderBy === 'atraso') {
            $query->orderBy('pvp.vencimento_em');
        } else {
            $query->orderBy('pvp.vencimento_em')->orderBy('pvp.id');
        }

        $rows = $query->paginate(50)->withQueryString();

        $receivables = $rows->through(function ($row) use ($tenantId, $today, $tomorrow, $weekEnd, $tz, $paidStatuses) {
            $cliente = (string) ($row->cliente_nome ?? 'Cliente não informado');
            $telefone = $row->cliente_telefone ? (string) $row->cliente_telefone : null;
            $cpf = $row->cliente_cpf ? (string) $row->cliente_cpf : null;

            $venc = Carbon::parse((string) $row->vencimento_em, $tz)->startOfDay();
            $isPaid = ! empty($row->pago_em) || in_array(strtolower((string) ($row->parcela_status ?? '')), $paidStatuses, true);

            $status = 'em_dia';
            if ($isPaid) {
                $status = 'paga';
            } elseif ($venc->lt($today)) {
                $status = 'vencida';
            } elseif ($venc->equalTo($today)) {
                $status = 'vence_hoje';
            } elseif ($venc->gte($tomorrow) && $venc->lte($weekEnd)) {
                $status = 'vence_semana';
            }

            $diasAtraso = 0;
            if (! $isPaid && $venc->lt($today)) {
                $diasAtraso = $venc->diffInDays($today);
            }

            $dataPedido = $row->data_pedido ? Carbon::parse((string) $row->data_pedido, $tz) : null;
            $year = $dataPedido ? $dataPedido->format('Y') : $today->format('Y');
            $vendaId = 'VD-' . $year . '-' . str_pad((int) $row->pedido_id, 4, '0', STR_PAD_LEFT);

            $valorParcela = (float) ($row->valor_parcela ?? 0);
            $juros = 0.0;
            $secureToken = Crypt::encryptString((int) $tenantId . '|' . (int) $row->parcela_id);
            $boletoSecureUrl = URL::temporarySignedRoute(
                'public.boleto.view',
                now()->addDays(7),
                ['token' => $secureToken]
            );

            return [
                'id' => (int) $row->parcela_id,
                'cliente' => $cliente,
                'telefone' => $telefone,
                'cpf' => $cpf,
                'venda_id' => $vendaId,
                'parcela' => (int) $row->numero_parcela . '/' . (int) $row->total_parcelas,
                'valor_parcela' => $valorParcela,
                'valor_total' => (float) ($row->valor_total ?? 0),
                'vencimento' => $venc->toDateString(),
                'status' => $status,
                'dias_atraso' => $diasAtraso,
                'juros' => $juros,
                'valor_atualizado' => $valorParcela + $juros,
                'pago_em' => $row->pago_em,
                'boleto_secure_url' => $boletoSecureUrl,
            ];
        });

        return view('financial.receivables', [
            'receivables' => $receivables,
            'receivablesPaginator' => $rows,
            'summary' => $summary,
            'filters' => [
                'status' => $statusFilter,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'q' => $q,
                'order_by' => $orderBy,
            ],
        ]);
    }

    public function paymentForm(string $id)
    {
        $tenantId = session('tenant_id');
        $locationId = session('location_id');
        $userLocations = session('user_locations', []);
        $locationIds = $this->resolveLocationIdsFromSession($tenantId, $locationId, $userLocations);

        if (! $tenantId) {
            return redirect()->route('financial.receivables')->with('error', 'Tenant não informado na sessão.');
        }

        $parcela = $this->findAccessibleParcela((int) $id, $tenantId, $locationIds);

        if (! $parcela) {
            return redirect()->route('financial.receivables')->with('error', 'Parcela não encontrada.');
        }

        $tz = 'America/Manaus';
        $cliente = $parcela->pedido?->cliente;
        $dataPedido = $parcela->pedido?->data_pedido ? Carbon::parse($parcela->pedido->data_pedido, $tz) : null;
        $year = $dataPedido ? $dataPedido->format('Y') : now($tz)->format('Y');
        $vendaId = 'VD-' . $year . '-' . str_pad((int) $parcela->pedido_venda_id, 4, '0', STR_PAD_LEFT);
        $valorParcela = (float) ($parcela->valor ?? 0);
        $juros = 0.0;

        $receivable = [
            'id' => (int) $parcela->id,
            'cliente' => $cliente?->nome ?? 'Cliente não informado',
            'cpf' => $cliente?->cpf_formatado ?? $cliente?->cpf ?? '',
            'venda_id' => $vendaId,
            'parcela' => (int) $parcela->numero_parcela . '/' . (int) $parcela->total_parcelas,
            'valor_parcela' => $valorParcela,
            'juros' => $juros,
            'valor_atualizado' => $valorParcela + $juros,
            'data_pagamento' => old('date', now($tz)->toDateString()),
            'forma_pagamento' => old('method', ''),
            'banco' => old('bank', ''),
            'referencia' => old('reference', ''),
            'desconto' => old('discount', '0.00'),
            'valor_recebido' => old('received_value', number_format($valorParcela + $juros, 2, '.', '')),
            'observacoes' => old('notes', ''),
            'return_url' => old('return_url', request('return_url', route('financial.receivables'))),
        ];

        return view('financial.receive-payment', compact('receivable'));
    }

    public function receivableDetails(string $id)
    {
        $tenantId = session('tenant_id');
        $locationId = session('location_id');
        $userLocations = session('user_locations', []);
        $locationIds = $this->resolveLocationIdsFromSession($tenantId, $locationId, $userLocations);

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
        $driver = DB::connection()->getDriverName();

        $dbHojeStr = Carbon::now($tz)->toDateString();
        if ($driver === 'pgsql') {
            $row = DB::selectOne("select (now() at time zone 'America/Manaus')::date as d");
            if ($row && isset($row->d)) {
                $dbHojeStr = (string) $row->d;
            }
        }

        $today = Carbon::parse($dbHojeStr, $tz)->startOfDay();
        $tomorrow = $today->copy()->addDay();
        $weekEnd = $today->copy()->addDays(7);
        $paidStatuses = ['pago', 'paga', 'cancelado', 'cancelada'];

        $venc = $parcela->vencimento_em ? Carbon::parse($parcela->vencimento_em, $tz)->startOfDay() : null;
        $isPaid = ! empty($parcela->pago_em) || in_array(strtolower((string) ($parcela->status ?? '')), $paidStatuses, true);

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
        if (! $isPaid && $venc && $venc->lt($today)) {
            $diasAtraso = $venc->diffInDays($today);
        }

        $pedido = $parcela->pedido;
        $cliente = $pedido?->cliente;

        $dataPedido = $pedido?->data_pedido ? Carbon::parse($pedido->data_pedido, $tz) : null;
        $year = $dataPedido ? $dataPedido->format('Y') : $today->format('Y');
        $vendaId = $pedido ? 'VD-' . $year . '-' . str_pad((int) $pedido->id, 4, '0', STR_PAD_LEFT) : null;

        $secureToken = Crypt::encryptString((int) $parcela->tenant_id . '|' . (int) $parcela->id);
        $boletoSecureUrl = URL::temporarySignedRoute(
            'public.boleto.view',
            now()->addDays(7),
            ['token' => $secureToken]
        );

        return response()->json([
            'success' => true,
            'data' => [
                'parcela' => [
                    'id' => $parcela->id,
                    'numero' => $parcela->numero_parcela,
                    'total' => $parcela->total_parcelas,
                    'valor' => (float) ($parcela->valor ?? 0),
                    'vencimento' => $venc ? $venc->format('Y-m-d') : null,
                    'pago_em' => $parcela->pago_em ? Carbon::parse($parcela->pago_em, $tz)->format('Y-m-d H:i:s') : null,
                    'status' => $status,
                    'dias_atraso' => $diasAtraso,
                    'forma_pagamento' => $parcela->forma_pagamento,
                    'boleto_secure_url' => $boletoSecureUrl,
                ],
                'pedido' => $pedido ? [
                    'id' => $pedido->id,
                    'venda_id' => $vendaId,
                    'data_pedido' => $pedido->data_pedido ? Carbon::parse($pedido->data_pedido, $tz)->format('Y-m-d H:i:s') : null,
                    'valor_total' => (float) ($pedido->valor_total ?? 0),
                    'status' => $pedido->status,
                ] : null,
                'cliente' => $cliente ? [
                    'id' => $cliente->id,
                    'nome' => $cliente->nome,
                    'cpf' => $cliente->cpf,
                    'telefone' => $cliente->telefone_formatado ?? $cliente->telefone,
                ] : null,
            ],
        ]);
    }

    public function receivableHistory(string $id)
    {
        $tenantId = session('tenant_id');
        $locationId = session('location_id');
        $userLocations = session('user_locations', []);
        $locationIds = $this->resolveLocationIdsFromSession($tenantId, $locationId, $userLocations);

        if (! $tenantId) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant não informado na sessão.',
            ], 403);
        }

        $tz = 'America/Manaus';
        $driver = DB::connection()->getDriverName();

        $dbHojeStr = Carbon::now($tz)->toDateString();
        if ($driver === 'pgsql') {
            $row = DB::selectOne("select (now() at time zone 'America/Manaus')::date as d");
            if ($row && isset($row->d)) {
                $dbHojeStr = (string) $row->d;
            }
        }

        $today = Carbon::parse($dbHojeStr, $tz)->startOfDay();
        $paidStatuses = ['pago', 'paga', 'cancelado', 'cancelada'];

        $parcelaRef = PedidoVendaParcela::query()
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

        if (! $parcelaRef) {
            return response()->json([
                'success' => false,
                'message' => 'Parcela não encontrada.',
            ], 404);
        }

        $pedido = $parcelaRef->pedido;
        $cliente = $pedido?->cliente;

        $parcelas = PedidoVendaParcela::query()
            ->whereNull('deleted_at')
            ->where('tenant_id', $tenantId)
            ->where('pedido_venda_id', $parcelaRef->pedido_venda_id)
            ->when(! empty($locationIds), function ($q) use ($locationIds) {
                $q->where(function ($q2) use ($locationIds) {
                    $q2->whereIn('location_id', $locationIds)->orWhereNull('location_id');
                });
            })
            ->orderBy('numero_parcela')
            ->orderBy('vencimento_em')
            ->orderBy('id')
            ->get();

        $items = $parcelas->map(function (PedidoVendaParcela $p) use ($tz, $today, $paidStatuses) {
            $venc = $p->vencimento_em ? Carbon::parse($p->vencimento_em, $tz)->startOfDay() : null;
            $isPaid = ! empty($p->pago_em) || in_array(strtolower((string) ($p->status ?? '')), $paidStatuses, true);
            $diasAtraso = 0;
            if (! $isPaid && $venc && $venc->lt($today)) {
                $diasAtraso = $venc->diffInDays($today);
            }

            $secureToken = Crypt::encryptString((int) $p->tenant_id . '|' . (int) $p->id);
            $secureUrl = URL::temporarySignedRoute(
                'public.recibo.view',
                now()->addDays(7),
                ['token' => $secureToken]
            );

            $boletoToken = Crypt::encryptString((int) $p->tenant_id . '|' . (int) $p->id);
            $boletoSecureUrl = URL::temporarySignedRoute(
                'public.boleto.view',
                now()->addDays(7),
                ['token' => $boletoToken]
            );

            return [
                'id' => $p->id,
                'numero' => $p->numero_parcela,
                'total' => $p->total_parcelas,
                'valor' => (float) ($p->valor ?? 0),
                'vencimento' => $venc ? $venc->format('Y-m-d') : null,
                'pago_em' => $p->pago_em ? Carbon::parse($p->pago_em, $tz)->format('Y-m-d H:i:s') : null,
                'status' => $isPaid ? 'paga' : (($venc && $venc->lt($today)) ? 'vencida' : 'a_vencer'),
                'dias_atraso' => $diasAtraso,
                'boleto_secure_url' => $boletoSecureUrl,
                'recibo_secure_url' => $secureUrl,
            ];
        })->values()->toArray();

        $dataPedido = $pedido?->data_pedido ? Carbon::parse($pedido->data_pedido, $tz) : null;
        $year = $dataPedido ? $dataPedido->format('Y') : $today->format('Y');
        $vendaId = $pedido ? 'VD-' . $year . '-' . str_pad((int) $pedido->id, 4, '0', STR_PAD_LEFT) : null;

        return response()->json([
            'success' => true,
            'data' => [
                'pedido' => $pedido ? [
                    'id' => $pedido->id,
                    'venda_id' => $vendaId,
                    'data_pedido' => $pedido->data_pedido ? Carbon::parse($pedido->data_pedido, $tz)->format('Y-m-d H:i:s') : null,
                    'valor_total' => (float) ($pedido->valor_total ?? 0),
                    'status' => $pedido->status,
                ] : null,
                'cliente' => $cliente ? [
                    'id' => $cliente->id,
                    'nome' => $cliente->nome,
                    'cpf' => $cliente->cpf,
                    'telefone' => $cliente->telefone_formatado ?? $cliente->telefone,
                ] : null,
                'parcelas' => $items,
            ],
        ]);
    }

    public function renegotiateReceivable(Request $request, string $id)
    {
        $tenantId = session('tenant_id');
        $locationId = session('location_id');
        $userLocations = session('user_locations', []);
        $locationIds = $this->resolveLocationIdsFromSession($tenantId, $locationId, $userLocations);

        if (! $tenantId) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant não informado na sessão.',
            ], 403);
        }

        $validated = $request->validate([
            'vencimento_em' => 'required|date_format:Y-m-d',
            'valor' => 'required|numeric|min:0.01',
        ], [
            'required' => 'O campo :attribute é obrigatório.',
            'date_format' => 'O campo :attribute deve estar no formato YYYY-MM-DD.',
            'numeric' => 'O campo :attribute deve ser numérico.',
            'min.numeric' => 'O campo :attribute deve ser maior que zero.',
        ], [
            'vencimento_em' => 'Vencimento',
            'valor' => 'Valor',
        ]);

        $paidStatuses = ['pago', 'paga', 'cancelado', 'cancelada'];

        $parcela = PedidoVendaParcela::query()
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

        $isPaid = ! empty($parcela->pago_em) || in_array(strtolower((string) ($parcela->status ?? '')), $paidStatuses, true);
        if ($isPaid) {
            return response()->json([
                'success' => false,
                'message' => 'Não é possível renegociar uma parcela já paga.',
            ], 422);
        }

        $parcela->vencimento_em = $validated['vencimento_em'];
        $parcela->valor = (float) $validated['valor'];
        $parcela->status = 'renegociado';
        $parcela->save();

        return response()->json([
            'success' => true,
            'id' => $parcela->id,
            'vencimento_em' => $parcela->vencimento_em?->toDateString(),
            'valor' => (float) $parcela->valor,
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
                ->whereNotNull('pessoa_paciente_id')
                ->whereNotNull('validade_dias')
                ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
                ->when(! empty($locationIds), function ($q) use ($locationIds) {
                    $q->where(function ($q2) use ($locationIds) {
                        $q2->whereIn('location_id', $locationIds)->orWhereNull('location_id');
                    });
                })
                ->orderByDesc('data_receita')
                ->orderByDesc('created_at')
                ->get();

            $receitasVencendo = $prescricoes
                ->groupBy('pessoa_paciente_id')
                ->map(fn($items) => $items->first())
                ->filter(function (Prescricao $p) use ($hoje) {
                    $vencimento = $this->resolvePrescriptionDueDate($p);
                    if (! $vencimento) {
                        return false;
                    }

                    $dias = $hoje->diffInDays($vencimento, false);

                    return $dias <= 7;
                })
                ->values();
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

    public function clearNotificationHistory(Request $request)
    {
        $tenantId = session('tenant_id');
        $locationId = session('location_id');
        $userLocations = session('user_locations', []);
        $locationIds = $this->resolveLocationIdsFromSession($tenantId, $locationId, $userLocations);

        if (! $tenantId) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant não informado na sessão.',
            ], 403);
        }

        $deleted = WhatsappNotification::query()
            ->whereNull('deleted_at')
            ->where('tenant_id', $tenantId)
            ->when(! empty($locationIds), function ($q) use ($locationIds) {
                $q->where(function ($q2) use ($locationIds) {
                    $q2->whereIn('location_id', $locationIds)->orWhereNull('location_id');
                });
            })
            ->delete();

        return response()->json([
            'success' => true,
            'deleted' => (int) $deleted,
        ]);
    }

    public function updateNotification(Request $request, string $id)
    {
        $tenantId = session('tenant_id');
        $locationId = session('location_id');
        $userLocations = session('user_locations', []);
        $locationIds = $this->resolveLocationIdsFromSession($tenantId, $locationId, $userLocations);

        if (! $tenantId) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant não informado na sessão.',
            ], 403);
        }

        $validated = $request->validate([
            'mensagem' => 'required|string',
            'telefone' => 'nullable|string|max:40',
        ], [
            'required' => 'O campo :attribute é obrigatório.',
            'string' => 'O campo :attribute deve ser um texto.',
            'max.string' => 'O campo :attribute não pode ter mais que :max caracteres.',
        ], [
            'mensagem' => 'Mensagem',
            'telefone' => 'Telefone',
        ]);

        $notification = WhatsappNotification::query()
            ->whereNull('deleted_at')
            ->where('tenant_id', $tenantId)
            ->where('id', $id)
            ->where('status', 'programado')
            ->when(! empty($locationIds), function ($q) use ($locationIds) {
                $q->where(function ($q2) use ($locationIds) {
                    $q2->whereIn('location_id', $locationIds)->orWhereNull('location_id');
                });
            })
            ->first();

        if (! $notification) {
            return response()->json([
                'success' => false,
                'message' => 'Mensagem não encontrada ou não está mais programada.',
            ], 404);
        }

        $telefone = $validated['telefone'] ?? null;
        $telefone = is_string($telefone) ? trim($telefone) : null;
        if ($telefone === '') {
            $telefone = null;
        }

        $telefoneNormalized = $telefone ? $this->normalizeWhatsappPhone($telefone) : null;
        if ($telefone && ! $telefoneNormalized) {
            return response()->json([
                'success' => false,
                'message' => 'Telefone inválido.',
            ], 422);
        }

        $notification->mensagem = $validated['mensagem'];
        if ($telefone !== null) {
            $notification->telefone = $telefoneNormalized;
        }
        $notification->wa_url = null;
        $notification->erro = null;
        $notification->save();

        return response()->json([
            'success' => true,
            'id' => $notification->id,
        ]);
    }

    public function cancelNotification(Request $request, string $id)
    {
        $tenantId = session('tenant_id');
        $locationId = session('location_id');
        $userLocations = session('user_locations', []);
        $locationIds = $this->resolveLocationIdsFromSession($tenantId, $locationId, $userLocations);

        if (! $tenantId) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant não informado na sessão.',
            ], 403);
        }

        $notification = WhatsappNotification::query()
            ->whereNull('deleted_at')
            ->where('tenant_id', $tenantId)
            ->where('id', $id)
            ->where('status', 'programado')
            ->when(! empty($locationIds), function ($q) use ($locationIds) {
                $q->where(function ($q2) use ($locationIds) {
                    $q2->whereIn('location_id', $locationIds)->orWhereNull('location_id');
                });
            })
            ->first();

        if (! $notification) {
            return response()->json([
                'success' => false,
                'message' => 'Mensagem não encontrada ou não está mais programada.',
            ], 404);
        }

        $notification->status = 'cancelado';
        $notification->wa_url = null;
        $notification->erro = null;
        $notification->save();

        return response()->json([
            'success' => true,
            'id' => $notification->id,
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

            $dataBaseReceita = $prescricao ? $this->resolvePrescriptionBaseDate($prescricao) : null;
            $dataEmissao = $dataBaseReceita ? $dataBaseReceita->format('d/m/Y') : '';
            $dataVenc = '';
            $vencimentoReceita = $prescricao ? $this->resolvePrescriptionDueDate($prescricao) : null;
            if ($vencimentoReceita) {
                $dataVenc = $vencimentoReceita->format('d/m/Y');
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

    private function resolvePrescriptionBaseDate(Prescricao $prescricao): ?Carbon
    {
        if ($prescricao->data_receita) {
            return Carbon::parse($prescricao->data_receita)->startOfDay();
        }

        if ($prescricao->created_at) {
            return $prescricao->created_at->copy()->startOfDay();
        }

        return null;
    }

    private function resolvePrescriptionDueDate(Prescricao $prescricao): ?Carbon
    {
        $dataBase = $this->resolvePrescriptionBaseDate($prescricao);

        if (! $dataBase || ! $prescricao->validade_dias) {
            return null;
        }

        return $dataBase->copy()->addDays((int) $prescricao->validade_dias);
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

    public function generateBoletosWeek(Request $request)
    {
        $tenantId = session('tenant_id');
        $locationId = session('location_id');
        $userLocations = session('user_locations', []);
        $locationIds = $this->resolveLocationIdsFromSession($tenantId, $locationId, $userLocations);

        if (! $tenantId) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant não informado na sessão.',
            ], 403);
        }

        $tz = 'America/Manaus';
        $driver = DB::connection()->getDriverName();

        $dbHojeStr = Carbon::now($tz)->toDateString();
        if ($driver === 'pgsql') {
            $row = DB::selectOne("select (now() at time zone 'America/Manaus')::date as d");
            if ($row && isset($row->d)) {
                $dbHojeStr = (string) $row->d;
            }
        }

        $today = Carbon::parse($dbHojeStr, $tz)->startOfDay();
        $weekEnd = $today->copy()->addDays(7);

        $paidStatuses = ['pago', 'paga', 'cancelado', 'cancelada'];

        $parcelas = PedidoVendaParcela::query()
            ->whereNull('deleted_at')
            ->where('tenant_id', $tenantId)
            ->when(! empty($locationIds), function ($q) use ($locationIds) {
                $q->where(function ($q2) use ($locationIds) {
                    $q2->whereIn('location_id', $locationIds)->orWhereNull('location_id');
                });
            })
            ->whereNull('pago_em')
            ->where(function ($q) use ($paidStatuses) {
                $q->whereNull('status')->orWhereNotIn(DB::raw('lower(status)'), $paidStatuses);
            })
            ->whereBetween('vencimento_em', [$today->toDateString(), $weekEnd->toDateString()])
            ->orderBy('vencimento_em')
            ->limit(50)
            ->get();

        $boletos = $parcelas->map(function (PedidoVendaParcela $p) {
            $secureToken = Crypt::encryptString((int) $p->tenant_id . '|' . (int) $p->id);
            $secureUrl = URL::temporarySignedRoute(
                'public.boleto.view',
                now()->addDays(7),
                ['token' => $secureToken]
            );

            return [
                'parcela_id' => $p->id,
                'pdf_url' => $secureUrl,
            ];
        })->values()->toArray();

        return response()->json([
            'success' => true,
            'boletos' => $boletos,
        ]);
    }

    public function generateBoleto($id)
    {
        $tenantId = session('tenant_id');
        $locationId = session('location_id');
        $userLocations = session('user_locations', []);
        $locationIds = $this->resolveLocationIdsFromSession($tenantId, $locationId, $userLocations);

        if (! $tenantId) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant não informado na sessão.',
            ], 403);
        }

        $parcela = PedidoVendaParcela::query()
            ->with('pedido.cliente')
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

        $boleto = $this->buildBoletoFromParcela($parcela);
        $secureToken = Crypt::encryptString((int) $parcela->tenant_id . '|' . (int) $parcela->id);
        $secureUrl = URL::temporarySignedRoute(
            'public.boleto.view',
            now()->addDays(7),
            ['token' => $secureToken]
        );

        return response()->json([
            'success' => true,
            'boleto' => $boleto,
            'pdf_url' => $secureUrl,
        ]);
    }

    public function boletoPdf($id)
    {
        $tenantId = session('tenant_id');
        $locationId = session('location_id');
        $userLocations = session('user_locations', []);
        $locationIds = $this->resolveLocationIdsFromSession($tenantId, $locationId, $userLocations);

        if (! $tenantId) {
            abort(403, 'Tenant não informado na sessão.');
        }

        $parcela = PedidoVendaParcela::query()
            ->with('pedido.cliente')
            ->whereNull('deleted_at')
            ->where('tenant_id', $tenantId)
            ->where('id', (int) $id)
            ->when(! empty($locationIds), function ($q) use ($locationIds) {
                $q->where(function ($q2) use ($locationIds) {
                    $q2->whereIn('location_id', $locationIds)->orWhereNull('location_id');
                });
            })
            ->firstOrFail();

        $boleto = $this->buildBoletoFromParcela($parcela);

        return view('financial.boleto-pdf', compact('boleto'));
    }

    public function reciboPdf($id)
    {
        $tenantId = session('tenant_id');
        $locationId = session('location_id');
        $userLocations = session('user_locations', []);
        $locationIds = $this->resolveLocationIdsFromSession($tenantId, $locationId, $userLocations);

        if (! $tenantId) {
            abort(403, 'Tenant não informado na sessão.');
        }

        $parcela = PedidoVendaParcela::query()
            ->with('pedido.cliente')
            ->whereNull('deleted_at')
            ->where('tenant_id', $tenantId)
            ->where('id', (int) $id)
            ->when(! empty($locationIds), function ($q) use ($locationIds) {
                $q->where(function ($q2) use ($locationIds) {
                    $q2->whereIn('location_id', $locationIds)->orWhereNull('location_id');
                });
            })
            ->firstOrFail();

        $recibo = $this->buildReciboFromParcela($parcela);

        return view('financial.recibo-pdf', compact('recibo'));
    }

    public function publicReciboPdf(Request $request, string $token)
    {
        try {
            $decrypted = Crypt::decryptString($token);
            [$tenantId, $parcelaId] = explode('|', $decrypted, 2);
        } catch (\Throwable $e) {
            abort(403, 'Link inválido.');
        }

        $tenantId = (int) $tenantId;
        $parcelaId = (int) $parcelaId;
        if ($tenantId <= 0 || $parcelaId <= 0) {
            abort(403, 'Link inválido.');
        }

        $parcela = PedidoVendaParcela::query()
            ->with('pedido.cliente')
            ->whereNull('deleted_at')
            ->where('tenant_id', $tenantId)
            ->where('id', $parcelaId)
            ->firstOrFail();

        $recibo = $this->buildReciboFromParcela($parcela);

        return view('financial.recibo-pdf', compact('recibo'));
    }

    public function publicBoletoPdf(Request $request, string $token)
    {
        try {
            $decrypted = Crypt::decryptString($token);
            [$tenantId, $parcelaId] = explode('|', $decrypted, 2);
        } catch (\Throwable $e) {
            abort(403, 'Link inválido.');
        }

        $tenantId = (int) $tenantId;
        $parcelaId = (int) $parcelaId;
        if ($tenantId <= 0 || $parcelaId <= 0) {
            abort(403, 'Link inválido.');
        }

        $parcela = PedidoVendaParcela::query()
            ->with('pedido.cliente')
            ->whereNull('deleted_at')
            ->where('tenant_id', $tenantId)
            ->where('id', $parcelaId)
            ->firstOrFail();

        $boleto = $this->buildBoletoFromParcela($parcela);

        return view('financial.boleto-pdf', compact('boleto'));
    }

    private function buildBoletoFromParcela(PedidoVendaParcela $parcela): array
    {
        $tenant = session('tenant');
        $tenantName = null;
        $tenantCpfCnpj = null;

        if (is_array($tenant)) {
            $tenantName = $tenant['trade_name'] ?? $tenant['name'] ?? null;
            $tenantCpfCnpj = $tenant['cpf_cnpj'] ?? null;
        } elseif (is_object($tenant)) {
            $tenantName = $tenant->trade_name ?? $tenant->name ?? null;
            $tenantCpfCnpj = $tenant->cpf_cnpj ?? null;
        }

        $pixKey = $this->normalizeCpfCnpj($tenantCpfCnpj);
        $merchantName = $this->sanitizePixText($tenantName ?: config('app.name', 'VISAOSIS'), 25);
        $merchantCity = $this->sanitizePixText('MANAUS', 15);

        $pedido = $parcela->pedido;
        $cliente = $pedido?->cliente;

        $clienteNome = $cliente?->nome ?? 'Cliente não informado';
        $clienteCpf = $cliente?->cpf ?? '';
        $clienteTelefone = $cliente?->telefone_formatado ?? $cliente?->telefone ?? '';

        $valor = (float) ($parcela->valor ?? 0);
        $txid = $this->sanitizePixText('PV' . $parcela->pedido_venda_id . 'P' . $parcela->numero_parcela, 25);
        $pixPayload = $pixKey ? $this->buildPixPayload($pixKey, $merchantName, $merchantCity, $valor, $txid) : '';

        $vencimento = $parcela->vencimento_em ? Carbon::parse($parcela->vencimento_em)->toDateString() : now()->toDateString();

        return [
            'id' => 'BOL-' . $parcela->id,
            'nosso_numero' => str_pad((string) $parcela->id, 11, '0', STR_PAD_LEFT),
            'linha_digitavel' => '34191.09008 61207.954566 00000.142508 1 95470000014962',
            'codigo_barras' => '34191954700000149620000061207954560000014250',
            'beneficiario' => $tenantName ?: 'Beneficiário não informado',
            'beneficiario_cpf_cnpj' => $tenantCpfCnpj ?: '',
            'pix_key' => $pixKey ?: '',
            'pix_payload' => $pixPayload,
            'cliente' => $clienteNome,
            'cpf' => $clienteCpf ?: '',
            'endereco' => '',
            'cidade' => '',
            'telefone' => $clienteTelefone ?: '',
            'vencimento' => $vencimento,
            'valor' => $valor,
            'juros' => 0.0,
            'valor_total' => $valor,
            'gerado_em' => now()->format('Y-m-d H:i:s'),
            'descricao' => 'Venda #' . (int) $parcela->pedido_venda_id . ' - Parcela ' . (int) $parcela->numero_parcela . '/' . (int) $parcela->total_parcelas,
            'observacoes' => null,
        ];
    }

    private function buildReciboFromParcela(PedidoVendaParcela $parcela): array
    {
        $tenant = session('tenant');
        $tenantName = null;
        $tenantCpfCnpj = null;

        if (is_array($tenant)) {
            $tenantName = $tenant['trade_name'] ?? $tenant['name'] ?? null;
            $tenantCpfCnpj = $tenant['cpf_cnpj'] ?? null;
        } elseif (is_object($tenant)) {
            $tenantName = $tenant->trade_name ?? $tenant->name ?? null;
            $tenantCpfCnpj = $tenant->cpf_cnpj ?? null;
        }

        $pedido = $parcela->pedido;
        $cliente = $pedido?->cliente;

        $tz = 'America/Manaus';
        $paidAt = $parcela->pago_em ? Carbon::parse($parcela->pago_em, $tz) : null;
        $venc = $parcela->vencimento_em ? Carbon::parse($parcela->vencimento_em, $tz) : null;

        $saleDate = $pedido?->data_pedido ? Carbon::parse($pedido->data_pedido, $tz) : null;
        $year = $saleDate ? $saleDate->format('Y') : ($paidAt ? $paidAt->format('Y') : now($tz)->format('Y'));
        $vendaId = $pedido ? 'VD-' . $year . '-' . str_pad((int) $pedido->id, 4, '0', STR_PAD_LEFT) : null;

        return [
            'id' => 'RCB-' . $parcela->id,
            'emitido_em' => now($tz)->format('Y-m-d H:i:s'),
            'beneficiario' => $tenantName ?: 'Beneficiário não informado',
            'beneficiario_cpf_cnpj' => $tenantCpfCnpj ?: '',
            'cliente' => $cliente?->nome ?? 'Cliente não informado',
            'cliente_cpf' => $cliente?->cpf ?? '',
            'cliente_telefone' => $cliente?->telefone_formatado ?? $cliente?->telefone ?? '',
            'pedido_id' => $pedido?->id,
            'venda_id' => $vendaId,
            'valor' => (float) ($parcela->valor ?? 0),
            'parcela_numero' => (int) $parcela->numero_parcela,
            'parcela_total' => (int) $parcela->total_parcelas,
            'vencimento' => $venc ? $venc->toDateString() : null,
            'pago_em' => $paidAt ? $paidAt->format('Y-m-d H:i:s') : null,
            'forma_pagamento' => $parcela->forma_pagamento,
        ];
    }

    private function normalizeCpfCnpj(?string $raw): ?string
    {
        if (! $raw) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $raw);
        if (! $digits) {
            return null;
        }

        return $digits;
    }

    private function sanitizePixText(string $text, int $maxLen): string
    {
        $t = trim($text);
        $t = preg_replace('/\s+/', ' ', $t);
        $t = preg_replace('/[^A-Za-z0-9 \-\.]/u', '', $t);
        $t = strtoupper($t);

        if (mb_strlen($t) > $maxLen) {
            $t = mb_substr($t, 0, $maxLen);
        }

        return $t;
    }

    private function buildPixPayload(string $pixKey, string $merchantName, string $merchantCity, float $amount, string $txid): string
    {
        $amountStr = number_format($amount, 2, '.', '');

        $gui = $this->emv('00', 'br.gov.bcb.pix');
        $key = $this->emv('01', $pixKey);
        $merchantAccountInfo = $this->emv('26', $gui . $key);

        $payload = '';
        $payload .= $this->emv('00', '01');
        $payload .= $merchantAccountInfo;
        $payload .= $this->emv('52', '0000');
        $payload .= $this->emv('53', '986');
        $payload .= $this->emv('54', $amountStr);
        $payload .= $this->emv('58', 'BR');
        $payload .= $this->emv('59', $merchantName);
        $payload .= $this->emv('60', $merchantCity);
        $payload .= $this->emv('62', $this->emv('05', $txid));

        $payloadToCrc = $payload . '6304';
        $crc = $this->crc16($payloadToCrc);

        return $payloadToCrc . $crc;
    }

    private function emv(string $id, string $value): string
    {
        $len = str_pad((string) strlen($value), 2, '0', STR_PAD_LEFT);
        return $id . $len . $value;
    }

    private function crc16(string $payload): string
    {
        $crc = 0xFFFF;
        $length = strlen($payload);

        for ($i = 0; $i < $length; $i++) {
            $crc ^= (ord($payload[$i]) << 8);
            for ($j = 0; $j < 8; $j++) {
                if (($crc & 0x8000) !== 0) {
                    $crc = (($crc << 1) ^ 0x1021) & 0xFFFF;
                } else {
                    $crc = ($crc << 1) & 0xFFFF;
                }
            }
        }

        return strtoupper(str_pad(dechex($crc), 4, '0', STR_PAD_LEFT));
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
        $tenantId = session('tenant_id');
        $locationId = session('location_id');
        $userLocations = session('user_locations', []);
        $locationIds = $this->resolveLocationIdsFromSession($tenantId, $locationId, $userLocations);

        if (! $tenantId) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant não informado na sessão.',
            ], 403);
        }

        $validated = $request->validate([
            'receivable_id' => 'required|integer',
            'date' => 'required|date_format:Y-m-d',
            'method' => 'nullable|string|max:50',
            'bank' => 'nullable|string|max:50',
            'reference' => 'nullable|string|max:100',
            'discount' => 'nullable|numeric|min:0',
            'received_value' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ], [
            'required' => 'O campo :attribute é obrigatório.',
            'integer' => 'O campo :attribute deve ser um número inteiro.',
            'date_format' => 'O campo :attribute deve estar no formato YYYY-MM-DD.',
            'numeric' => 'O campo :attribute deve ser numérico.',
            'max.string' => 'O campo :attribute não pode ter mais que :max caracteres.',
        ], [
            'receivable_id' => 'Parcela',
            'date' => 'Data do pagamento',
        ]);

        $parcela = $this->findAccessibleParcela((int) $validated['receivable_id'], $tenantId, $locationIds);

        if (! $parcela) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parcela não encontrada.',
                ], 404);
            }

            return redirect()->route('financial.receivables')->with('error', 'Parcela não encontrada.');
        }

        $tz = 'America/Manaus';
        $agora = Carbon::now($tz);
        $paidAt = Carbon::createFromFormat('Y-m-d', $validated['date'], $tz)
            ->setTime((int) $agora->format('H'), (int) $agora->format('i'), (int) $agora->format('s'));

        $paymentMethods = [
            'dinheiro' => 'Dinheiro',
            'pix' => 'PIX',
            'cartao_debito' => 'Cartão de Débito',
            'cartao_credito' => 'Cartão de Crédito',
            'transferencia' => 'Transferência Bancária',
            'boleto' => 'Boleto Bancário',
            'cheque' => 'Cheque',
        ];

        $formaPagamento = null;
        if (! empty($validated['method'])) {
            $formaPagamento = $paymentMethods[$validated['method']] ?? $validated['method'];
        }

        $observacoes = trim((string) ($validated['notes'] ?? ''));
        $detalhesExtras = [];

        if (! empty($validated['bank'])) {
            $detalhesExtras[] = 'Banco: ' . $validated['bank'];
        }

        if (! empty($validated['reference'])) {
            $detalhesExtras[] = 'Referência: ' . $validated['reference'];
        }

        if (! empty($detalhesExtras)) {
            $observacoes = trim($observacoes . ($observacoes !== '' ? ' | ' : '') . implode(' | ', $detalhesExtras));
        }

        $parcela->pago_em = $paidAt;
        $parcela->status = 'pago';
        $parcela->forma_pagamento = $formaPagamento;
        $parcela->valor_recebido = (float) ($validated['received_value'] ?? 0);
        $parcela->valor_desconto = (float) ($validated['discount'] ?? 0);
        $parcela->observacoes = $observacoes !== '' ? $observacoes : null;
        $parcela->save();

        if (! $request->expectsJson()) {
            $returnUrl = $request->input('return_url');

            return redirect()->to($returnUrl ?: route('financial.receivables'))
                ->with('success', 'Pagamento registrado com sucesso!');
        }

        return response()->json([
            'success' => true,
            'id' => $parcela->id,
            'pago_em' => $parcela->pago_em,
        ]);
    }

    private function findAccessibleParcela(int $id, $tenantId, array $locationIds): ?PedidoVendaParcela
    {
        return PedidoVendaParcela::query()
            ->with(['pedido.cliente'])
            ->whereNull('deleted_at')
            ->where('tenant_id', $tenantId)
            ->where('id', $id)
            ->when(! empty($locationIds), function ($q) use ($locationIds) {
                $q->where(function ($q2) use ($locationIds) {
                    $q2->whereIn('location_id', $locationIds)->orWhereNull('location_id');
                });
            })
            ->first();
    }
}
