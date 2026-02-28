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
                            <h3 class="font-weight-medium text-right mb-0 text-dark">R$ {{ number_format($financialData['total_receber'], 2, ',', '.') }}</h3>
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
                            <h3 class="font-weight-medium text-right mb-0 text-dark">R$ {{ number_format($financialData['vencidas'], 2, ',', '.') }}</h3>
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
                        <i class="mdi mdi-calendar-clock text-warning icon-lg"></i>
                    </div>
                    <div class="float-end">
                        <p class="mb-0 text-right text-dark">Vence Hoje</p>
                        <div class="fluid-container">
                            <h3 class="font-weight-medium text-right mb-0 text-dark">R$ {{ number_format($financialData['vence_hoje'], 2, ',', '.') }}</h3>
                        </div>
                        <small class="text-muted">Contatar clientes</small>
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
                            <h3 class="font-weight-medium text-right mb-0 text-dark">R$ {{ number_format($financialData['recebido_mes'], 2, ',', '.') }}</h3>
                        </div>
                        <small class="text-muted">Ticket médio: R$ {{ number_format($financialData['ticket_medio'], 2, ',', '.') }}</small>
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
                    Recebimentos dos Últimos 8 Meses
                </h5>
            </div>
            <div class="card-body">
                <div class="static-chart">
                    <div class="chart-container">
                        <div class="chart-bars-financial">
                            <div class="chart-month">
                                <div class="bar-financial bg-success" style="height: 60px;" title="Janeiro: R$ 25.400"></div>
                                <small>Jan</small>
                            </div>
                            <div class="chart-month">
                                <div class="bar-financial bg-success" style="height: 70px;" title="Fevereiro: R$ 28.900"></div>
                                <small>Fev</small>
                            </div>
                            <div class="chart-month">
                                <div class="bar-financial bg-success" style="height: 80px;" title="Março: R$ 31.200"></div>
                                <small>Mar</small>
                            </div>
                            <div class="chart-month">
                                <div class="bar-financial bg-success" style="height: 75px;" title="Abril: R$ 29.800"></div>
                                <small>Abr</small>
                            </div>
                            <div class="chart-month">
                                <div class="bar-financial bg-success" style="height: 85px;" title="Maio: R$ 33.100"></div>
                                <small>Mai</small>
                            </div>
                            <div class="chart-month">
                                <div class="bar-financial bg-success" style="height: 78px;" title="Junho: R$ 30.500"></div>
                                <small>Jun</small>
                            </div>
                            <div class="chart-month">
                                <div class="bar-financial bg-success" style="height: 82px;" title="Julho: R$ 32.800"></div>
                                <small>Jul</small>
                            </div>
                            <div class="chart-month">
                                <div class="bar-financial bg-warning" style="height: 72px;" title="Agosto: R$ 28.500"></div>
                                <small>Ago</small>
                            </div>
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
                        <strong>23 parcelas vencidas</strong><br>
                        <small>Total: R$ 8.900,00</small>
                    </div>
                    <button class="btn btn-sm btn-outline-danger" onclick="window.location.href='{{ route('financial.receivables') }}'">Ver</button>
                </div>
                
                <div class="alert alert-warning d-flex align-items-center mb-2 py-2">
                    <i class="mdi mdi-calendar-clock me-2"></i>
                    <div class="flex-grow-1">
                        <strong>8 parcelas vencem hoje</strong><br>
                        <small>Total: R$ 2.400,00</small>
                    </div>
                    <button class="btn btn-sm btn-outline-warning" onclick="sendReminders()">WhatsApp</button>
                </div>
                
                <div class="alert alert-info d-flex align-items-center mb-0 py-2">
                    <i class="mdi mdi-calendar-week me-2"></i>
                    <div class="flex-grow-1">
                        <strong>15 parcelas esta semana</strong><br>
                        <small>Total: R$ 6.700,00</small>
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
                        <a href="{{ route('financial.receivables') }}" class="btn btn-outline-success w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                            <i class="mdi mdi-currency-usd fs-2 mb-2"></i>
                            <span>Contas a Receber</span>
                            <small class="text-muted">Gestão completa</small>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('financial.boletos') }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                            <i class="mdi mdi-file-document-outline fs-2 mb-2"></i>
                            <span>Boletos</span>
                            <small class="text-muted">Gerar e enviar</small>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('financial.notifications') }}" class="btn btn-outline-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                            <i class="mdi mdi-whatsapp fs-2 mb-2"></i>
                            <span>WhatsApp</span>
                            <small class="text-muted">Notificações</small>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('financial.index') }}" class="btn btn-outline-info w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
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
                            <tr class="table-danger">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-xs bg-danger text-white rounded-circle me-2 d-flex align-items-center justify-content-center">
                                            MS
                                        </div>
                                        <span>Maria Silva Santos</span>
                                    </div>
                                </td>
                                <td>VD-2024-045<br><small class="text-muted">R$ 850,00</small></td>
                                <td>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-danger" style="width: 33%"></div>
                                    </div>
                                    <small>2/6 pagas</small>
                                </td>
                                <td>
                                    <span class="text-danger">30/08/2024</span><br>
                                    <small class="text-danger">5 dias atraso</small>
                                </td>
                                <td><span class="badge bg-danger">Em Atraso</span></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-danger" title="Cobrar">
                                            <i class="mdi mdi-whatsapp"></i>
                                        </button>
                                        <button class="btn btn-outline-primary" title="Boleto">
                                            <i class="mdi mdi-file-document"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr class="table-warning">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-xs bg-warning text-white rounded-circle me-2 d-flex align-items-center justify-content-center">
                                            JS
                                        </div>
                                        <span>João Silva</span>
                                    </div>
                                </td>
                                <td>VD-2024-048<br><small class="text-muted">R$ 1.200,00</small></td>
                                <td>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-warning" style="width: 0%"></div>
                                    </div>
                                    <small>0/4 pagas</small>
                                </td>
                                <td>
                                    <span class="text-warning">29/08/2024</span><br>
                                    <small class="text-warning">Vence hoje</small>
                                </td>
                                <td><span class="badge bg-warning">Vence Hoje</span></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-warning" title="Lembrar">
                                            <i class="mdi mdi-whatsapp"></i>
                                        </button>
                                        <button class="btn btn-outline-primary" title="Boleto">
                                            <i class="mdi mdi-file-document"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-xs bg-success text-white rounded-circle me-2 d-flex align-items-center justify-content-center">
                                            CL
                                        </div>
                                        <span>Carlos Lima</span>
                                    </div>
                                </td>
                                <td>VD-2024-052<br><small class="text-muted">R$ 650,00</small></td>
                                <td>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-success" style="width: 20%"></div>
                                    </div>
                                    <small>1/5 pagas</small>
                                </td>
                                <td>
                                    <span class="text-success">05/09/2024</span><br>
                                    <small class="text-success">Em dia</small>
                                </td>
                                <td><span class="badge bg-success">Em Dia</span></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-success" title="Ver">
                                            <i class="mdi mdi-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
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

.bar-financial {
    width: 25px;
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
        width: 20px;
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
