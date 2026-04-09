@extends('layouts.app')

@section('title', 'Dashboard Financeiro - Connect Plus')

@section('content')
    <div class="d-xl-flex justify-content-between align-items-start mb-4">
        <div>
            <h2 class="text-dark font-weight-bold mb-2">
                <i class="mdi mdi-cash-multiple me-2"></i>
                Dashboard Financeiro
            </h2>
            <p class="text-muted mb-0">Controle completo de pagamentos e crediário próprio</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-success" onclick="exportFinancial()">
                <i class="mdi mdi-download me-2"></i>Exportar
            </button>
            <a href="{{ route('financial.receivables') }}" class="btn btn-success">
                <i class="mdi mdi-currency-usd me-2"></i>Contas a Receber
            </a>
        </div>
    </div>

    <!-- Métricas Principais -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card card-statistics">
                <div class="card-body">
                    <div class="clearfix">
                        <div class="float-start">
                            <i class="mdi mdi-cash-multiple text-success icon-lg"></i>
                        </div>
                        <div class="float-end">
                            <p class="mb-0 text-right text-dark">Total a Receber</p>
                            <div class="fluid-container">
                                <h3 class="font-weight-medium text-right mb-0 text-dark">R$
                                    {{ number_format($financialData['total_receber'], 2, ',', '.') }}</h3>
                            </div>
                            <small class="text-muted">{{ $financialData['total_clientes_credito'] }} clientes</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card card-statistics">
                <div class="card-body">
                    <div class="clearfix">
                        <div class="float-start">
                            <i class="mdi mdi-alert-circle text-danger icon-lg"></i>
                        </div>
                        <div class="float-end">
                            <p class="mb-0 text-right text-dark">Vencidas</p>
                            <div class="fluid-container">
                                <h3 class="font-weight-medium text-right mb-0 text-dark">R$
                                    {{ number_format($financialData['vencidas'], 2, ',', '.') }}</h3>
                            </div>
                            <small class="text-danger">{{ $financialData['inadimplencia'] }}% inadimplência</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card card-statistics">
                <div class="card-body">
                    <div class="clearfix">
                        <div class="float-start">
                            <i class="mdi mdi-cart text-warning icon-lg"></i>
                        </div>
                        <div class="float-end">
                            <p class="mb-0 text-right text-dark">Vendas no Mês</p>
                            <div class="fluid-container">
                                <h3 class="font-weight-medium text-right mb-0 text-dark">R$
                                    {{ number_format($financialData['vendas_mes'] ?? 0, 2, ',', '.') }}</h3>
                            </div>
                            <small class="text-muted">{{ (int) ($financialData['vendas_mes_count'] ?? 0) }} vendas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card card-statistics">
                <div class="card-body">
                    <div class="clearfix">
                        <div class="float-start">
                            <i class="mdi mdi-chart-line text-info icon-lg"></i>
                        </div>
                        <div class="float-end">
                            <p class="mb-0 text-right text-dark">Recebido no Mês</p>
                            <div class="fluid-container">
                                <h3 class="font-weight-medium text-right mb-0 text-dark">R$
                                    {{ number_format($financialData['recebido_mes'], 2, ',', '.') }}</h3>
                            </div>
                            <small class="text-muted">Ticket médio: R$
                                {{ number_format($financialData['ticket_medio'], 2, ',', '.') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos e Alertas -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="mdi mdi-chart-bar me-2"></i>
                        Vendas x Recebimentos dos Últimos 8 Meses
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-3 small text-muted mb-2">
                        <div class="d-flex align-items-center gap-1">
                            <span class="badge bg-primary">&nbsp;</span>
                            <span>Vendas</span>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <span class="badge bg-success">&nbsp;</span>
                            <span>Recebimentos</span>
                        </div>
                    </div>
                    <div class="static-chart">
                        <div class="chart-container">
                            <div class="chart-bars-financial">
                                @foreach ($salesReceiptsChart ?? [] as $m)
                                    <div class="chart-month">
                                        <div class="bar-pair">
                                            <div class="bar-financial bg-primary"
                                                style="height: {{ (int) ($m['sales_height'] ?? 3) }}px;"
                                                title="{{ $m['label'] ?? '' }} | Vendas: R$ {{ number_format((float) ($m['sales_total'] ?? 0), 2, ',', '.') }} | Recebimentos: R$ {{ number_format((float) ($m['receipts_total'] ?? 0), 2, ',', '.') }}">
                                            </div>
                                            <div class="bar-financial bg-success"
                                                style="height: {{ (int) ($m['receipts_height'] ?? 3) }}px;"
                                                title="{{ $m['label'] ?? '' }} | Vendas: R$ {{ number_format((float) ($m['sales_total'] ?? 0), 2, ',', '.') }} | Recebimentos: R$ {{ number_format((float) ($m['receipts_total'] ?? 0), 2, ',', '.') }}">
                                            </div>
                                        </div>
                                        <small>{{ $m['label'] ?? '-' }}</small>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h6 class="mb-0">
                        <i class="mdi mdi-alert-circle me-2"></i>
                        Alertas Críticos
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="alert alert-danger d-flex align-items-center mb-2 py-2">
                        <i class="mdi mdi-clock-alert me-2"></i>
                        <div class="flex-grow-1">
                            <strong>{{ (int) ($alertas['vencidas']['count'] ?? 0) }} parcelas vencidas</strong><br>
                            <small>Total: R$
                                {{ number_format((float) ($alertas['vencidas']['valor'] ?? 0), 2, ',', '.') }}</small>
                        </div>
                        <button class="btn btn-sm btn-outline-danger"
                            onclick="window.location.href='{{ route('financial.receivables') }}'">Ver</button>
                    </div>

                    <div class="alert alert-warning d-flex align-items-center mb-2 py-2">
                        <i class="mdi mdi-calendar-clock me-2"></i>
                        <div class="flex-grow-1">
                            <strong>{{ (int) ($alertas['vence_hoje']['count'] ?? 0) }} parcelas vencem hoje</strong><br>
                            <small>Total: R$
                                {{ number_format((float) ($alertas['vence_hoje']['valor'] ?? 0), 2, ',', '.') }}</small>
                        </div>
                        <button class="btn btn-sm btn-outline-warning" onclick="sendReminders()">WhatsApp</button>
                    </div>

                    <div class="alert alert-info d-flex align-items-center mb-0 py-2">
                        <i class="mdi mdi-calendar-week me-2"></i>
                        <div class="flex-grow-1">
                            <strong>{{ (int) ($alertas['vence_semana']['count'] ?? 0) }} parcelas esta semana</strong><br>
                            <small>Total: R$
                                {{ number_format((float) ($alertas['vence_semana']['valor'] ?? 0), 2, ',', '.') }}</small>
                        </div>
                        <button class="btn btn-sm btn-outline-info" onclick="generateBoletos()">Boletos</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ações Rápidas -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="mdi mdi-lightning-bolt me-2"></i>
                        Ações Rápidas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="{{ route('financial.receivables') }}"
                                class="btn btn-outline-success w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                <i class="mdi mdi-currency-usd fs-2 mb-2"></i>
                                <span>Contas a Receber</span>
                                <small class="text-muted">Gestão completa</small>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('financial.boletos') }}"
                                class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                <i class="mdi mdi-file-document-outline fs-2 mb-2"></i>
                                <span>Boletos</span>
                                <small class="text-muted">Gerar e enviar</small>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('financial.notifications') }}"
                                class="btn btn-outline-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                <i class="mdi mdi-whatsapp fs-2 mb-2"></i>
                                <span>WhatsApp</span>
                                <small class="text-muted">Notificações</small>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('financial.index') }}"
                                class="btn btn-outline-info w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                <i class="mdi mdi-chart-line fs-2 mb-2"></i>
                                <span>Relatórios</span>
                                <small class="text-muted">Análises</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumo de Parcelamentos -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="mdi mdi-credit-card me-2"></i>
                        Resumo de Parcelamentos Ativos
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Cliente</th>
                                    <th>Venda</th>
                                    <th>Progresso</th>
                                    <th>Próxima Parcela</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse (($installmentSummaries ?? []) as $row)
                                    <tr class="{{ $row['row_class'] ?? '' }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div
                                                    class="avatar-xs {{ $row['badge_class'] ?? 'bg-secondary' }} text-white rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                    {{ $row['initials'] ?? 'CL' }}
                                                </div>
                                                <span>{{ $row['cliente'] ?? '-' }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            #{{ str_pad((int) ($row['pedido_id'] ?? 0), 6, '0', STR_PAD_LEFT) }}<br>
                                            <small class="text-muted">R$
                                                {{ number_format((float) ($row['valor_total'] ?? 0), 2, ',', '.') }}</small>
                                        </td>
                                        <td>
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar {{ $row['badge_class'] ?? 'bg-success' }}"
                                                    style="width: {{ (int) ($row['progress_pct'] ?? 0) }}%"></div>
                                            </div>
                                            <small>{{ (int) ($row['pagas'] ?? 0) }}/{{ (int) ($row['total_parcelas'] ?? 0) }}
                                                pagas</small>
                                        </td>
                                        <td>
                                            <span>{{ $row['proximo_vencimento'] ?? '-' }}</span><br>
                                            <small>{{ $row['sub_label'] ?? '' }}</small>
                                        </td>
                                        <td><span
                                                class="badge {{ $row['badge_class'] ?? 'bg-secondary' }}">{{ $row['status_label'] ?? '-' }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-secondary" title="WhatsApp"
                                                    onclick="sendReminders()">
                                                    <i class="mdi mdi-whatsapp"></i>
                                                </button>
                                                <button class="btn btn-outline-primary" title="Boleto"
                                                    onclick="generateBoletos()">
                                                    <i class="mdi mdi-file-document"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">Nenhum parcelamento ativo
                                            encontrado.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .static-chart {
                height: 200px;
            }

            .chart-bars-financial {
                display: flex;
                justify-content: space-around;
                align-items: end;
                height: 140px;
                padding: 0 20px;
            }

            .chart-month {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 8px;
            }

            .bar-pair {
                display: flex;
                gap: 6px;
                align-items: flex-end;
            }

            .bar-financial {
                width: 12px;
                border-radius: 4px 4px 0 0;
                transition: opacity 0.2s;
                cursor: pointer;
            }

            .bar-financial:hover {
                opacity: 0.8;
            }

            .avatar-xs {
                width: 28px;
                height: 28px;
                font-size: 11px;
                font-weight: bold;
            }

            @media (max-width: 768px) {
                .chart-bars-financial {
                    padding: 0 10px;
                }

                .bar-financial {
                    width: 10px;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            function exportFinancial() {
                alert('Exportando relatório financeiro...');
            }

            function sendReminders() {
                alert('Enviando lembretes via WhatsApp...');
            }

            function generateBoletos() {
                alert('Gerando boletos em lote...');
            }

            document.querySelectorAll('.bar-financial').forEach(bar => {
                bar.addEventListener('mouseenter', function() {
                    console.log(this.title);
                });
            });
        </script>
    @endpush
@endsection
