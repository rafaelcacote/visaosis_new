@extends('layouts.app')

@section('title', 'Contas a Receber - Connect Plus')

@section('content')
<div class="d-xl-flex justify-content-between align-items-start mb-4">
    <div>
        <h2 class="text-dark font-weight-bold mb-2">
            <i class="mdi mdi-currency-usd me-2"></i>
            Contas a Receber
        </h2>
        <p class="text-muted mb-0">Gestão completa de parcelas e pagamentos</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-success" onclick="exportReceivables()">
            <i class="mdi mdi-download me-2"></i>Exportar
        </button>
        <button class="btn btn-warning" onclick="sendBulkReminders()">
            <i class="mdi mdi-whatsapp me-2"></i>Enviar Lembretes
        </button>
    </div>
</div>

<!-- Resumo Rápido -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card card-statistics">
            <div class="card-body">
                <div class="clearfix">
                    <div class="float-start">
                        <i class="mdi mdi-alert-circle text-danger icon-lg"></i>
                    </div>
                    <div class="float-end">
                        <p class="mb-0 text-right text-dark">Parcelas Vencidas</p>
                        <div class="fluid-container">
                            <h3 class="font-weight-medium text-right mb-0 text-dark">{{ (int) ($summary['vencidas']['count'] ?? 0) }}</h3>
                        </div>
                        <small class="text-danger">R$ {{ number_format((float) ($summary['vencidas']['valor'] ?? 0), 2, ',', '.') }}</small>
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
                        <p class="mb-0 text-right text-dark">Vencem Hoje</p>
                        <div class="fluid-container">
                            <h3 class="font-weight-medium text-right mb-0 text-dark">{{ (int) ($summary['vence_hoje']['count'] ?? 0) }}</h3>
                        </div>
                        <small class="text-warning">R$ {{ number_format((float) ($summary['vence_hoje']['valor'] ?? 0), 2, ',', '.') }}</small>
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
                        <i class="mdi mdi-calendar-week text-info icon-lg"></i>
                    </div>
                    <div class="float-end">
                        <p class="mb-0 text-right text-dark">Vencem Esta Semana</p>
                        <div class="fluid-container">
                            <h3 class="font-weight-medium text-right mb-0 text-dark">{{ (int) ($summary['vence_semana']['count'] ?? 0) }}</h3>
                        </div>
                        <small class="text-info">R$ {{ number_format((float) ($summary['vence_semana']['valor'] ?? 0), 2, ',', '.') }}</small>
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
                        <i class="mdi mdi-check-circle text-success icon-lg"></i>
                    </div>
                    <div class="float-end">
                        <p class="mb-0 text-right text-dark">Em Dia</p>
                        <div class="fluid-container">
                            <h3 class="font-weight-medium text-right mb-0 text-dark">{{ (int) ($summary['em_dia']['count'] ?? 0) }}</h3>
                        </div>
                        <small class="text-success">R$ {{ number_format((float) ($summary['em_dia']['valor'] ?? 0), 2, ',', '.') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="mdi mdi-filter me-2"></i>
                    Filtros
                </h6>
            </div>
            <div class="card-body">
                <form class="row g-3" id="filtersForm" method="GET" action="{{ route('financial.receivables') }}">
                    <div class="col-md-2">
                        <select class="form-select" id="statusFilter" name="status">
                            <option value="">Todos Status</option>
                            <option value="vencida">Vencidas</option>
                            <option value="vence_hoje">Vence Hoje</option>
                            <option value="vence_semana">Vence na Semana</option>
                            <option value="em_dia">Em Dia</option>
                            <option value="paga">Pagas</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" id="startDate" name="start_date"
                            placeholder="Data Inicial" value="{{ $filters['start_date'] ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" id="endDate" name="end_date" placeholder="Data Final"
                            value="{{ $filters['end_date'] ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control" placeholder="Buscar cliente, venda..." id="searchInput"
                            name="q" value="{{ $filters['q'] ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" id="orderBy" name="order_by">
                            <option value="vencimento">Vencimento</option>
                            <option value="valor">Valor</option>
                            <option value="cliente">Cliente</option>
                            <option value="atraso">Dias Atraso</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-primary w-100" onclick="applyFilters()">
                            <i class="mdi mdi-magnify"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Tabela de Contas a Receber -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="mdi mdi-format-list-bulleted me-2"></i>
                    Parcelas ({{ $receivablesPaginator->count() }} de {{ $receivablesPaginator->total() }})
                </h6>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                        <i class="mdi mdi-check-all me-2"></i>Selecionar Todas
                    </button>
                    <button class="btn btn-sm btn-outline-warning" onclick="sendSelectedReminders()">
                        <i class="mdi mdi-whatsapp me-2"></i>WhatsApp Selecionadas
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="40">
                                    <input type="checkbox" class="form-check-input" id="selectAllCheckbox">
                                </th>
                                <th>Cliente</th>
                                <th>Venda</th>
                                <th>Parcela</th>
                                <th>Vencimento</th>
                                <th>Valor</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($receivables as $receivable)
                                <tr class="@if ($receivable['status'] == 'vencida') table-danger @elseif($receivable['status'] == 'vence_hoje') table-warning @elseif($receivable['status'] == 'vence_semana') table-info @elseif($receivable['status'] == 'paga') table-success @endif">
                                    <td>
                                        <input type="checkbox" class="form-check-input receivable-checkbox" value="{{ $receivable['id'] }}">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm @if ($receivable['status'] == 'vencida') bg-danger @elseif($receivable['status'] == 'vence_hoje') bg-warning @elseif($receivable['status'] == 'vence_semana') bg-info @elseif($receivable['status'] == 'paga') bg-success @else bg-success @endif text-white rounded-circle me-3 d-flex align-items-center justify-content-center">
                                                {{ mb_substr($receivable['cliente'], 0, 2) }}
                                            </div>
                                            <div>
                                                <strong>{{ $receivable['cliente'] }}</strong>
                                                <br><small class="text-muted">{{ $receivable['telefone'] }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <strong>{{ $receivable['venda_id'] }}</strong>
                                        <br><small class="text-muted">Total: R$ {{ number_format($receivable['valor_total'], 2, ',', '.') }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $receivable['parcela'] }}</span>
                                        <br><small class="text-muted">R$ {{ number_format($receivable['valor_parcela'], 2, ',', '.') }}</small>
                                    </td>
                                    <td>
                                        <span class="@if ($receivable['status'] == 'vencida') text-danger @elseif($receivable['status'] == 'vence_hoje') text-warning @elseif($receivable['status'] == 'vence_semana') text-info @else text-success @endif">
                                            {{ \Carbon\Carbon::parse($receivable['vencimento'])->format('d/m/Y') }}
                                        </span>
                                        @if ($receivable['dias_atraso'] > 0)
                                            <br><small class="text-danger">{{ $receivable['dias_atraso'] }} dias atraso</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($receivable['juros'] > 0)
                                            <span class="text-decoration-line-through text-muted">R$ {{ number_format($receivable['valor_parcela'], 2, ',', '.') }}</span>
                                            <br><strong class="text-danger">R$ {{ number_format($receivable['valor_atualizado'], 2, ',', '.') }}</strong>
                                            <br><small class="text-danger">+R$ {{ number_format($receivable['juros'], 2, ',', '.') }} juros</small>
                                        @else
                                            <strong>R$ {{ number_format($receivable['valor_atualizado'], 2, ',', '.') }}</strong>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($receivable['status'] == 'vencida')
                                            <span class="badge bg-danger">
                                                <i class="mdi mdi-alert-circle me-1"></i>Vencida
                                            </span>
                                        @elseif($receivable['status'] == 'vence_hoje')
                                            <span class="badge bg-warning">
                                                <i class="mdi mdi-calendar-clock me-1"></i>Vence Hoje
                                            </span>
                                        @elseif($receivable['status'] == 'vence_semana')
                                            <span class="badge bg-info">
                                                <i class="mdi mdi-calendar-week me-1"></i>Vence na Semana
                                            </span>
                                        @elseif($receivable['status'] == 'paga')
                                            <span class="badge bg-success">
                                                <i class="mdi mdi-check-circle me-1"></i>Paga
                                            </span>
                                        @else
                                            <span class="badge bg-success">
                                                <i class="mdi mdi-check-circle me-1"></i>Em Dia
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            @if ($receivable['status'] == 'paga')
                                                <button class="btn btn-outline-success" title="Pagamento confirmado" disabled>
                                                    <i class="mdi mdi-check"></i>
                                                </button>
                                            @elseif ($receivable['status'] == 'vencida')
                                                <button class="btn btn-danger" title="Cobrar Urgente" onclick="sendUrgentReminder({{ $receivable['id'] }})">
                                                    <i class="mdi mdi-phone"></i>
                                                </button>
                                            @else
                                                <button class="btn btn-outline-success" title="WhatsApp" onclick="sendWhatsApp({{ $receivable['id'] }})">
                                                    <i class="mdi mdi-whatsapp"></i>
                                                </button>
                                            @endif
                                            <button class="btn btn-outline-primary" title="Gerar Boleto" onclick="generateBoleto({{ $receivable['id'] }})">
                                                <i class="mdi mdi-file-document-outline"></i>
                                            </button>
                                            @if ($receivable['status'] !== 'paga')
                                                <button class="btn btn-outline-success btn-sm" title="Dar baixa / Receber Pagamento" onclick="openPaymentModal({{ $receivable['id'] }}, '{{ $receivable['cliente'] }}', '{{ $receivable['cpf'] ?? '' }}', '{{ $receivable['venda_id'] }}', {{ (float) $receivable['valor_parcela'] }}, {{ (float) $receivable['juros'] }}, {{ (float) $receivable['valor_atualizado'] }})">
                                                    <i class="mdi mdi-cash"></i>
                                                </button>
                                            @endif
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                                    <i class="mdi mdi-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#" onclick="viewDetails({{ $receivable['id'] }})">
                                                        <i class="mdi mdi-eye me-2"></i>Ver Detalhes
                                                    </a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="viewHistory({{ $receivable['id'] }})">
                                                        <i class="mdi mdi-history me-2"></i>Histórico
                                                    </a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item text-warning" href="#" onclick="renegotiate({{ $receivable['id'] }})">
                                                        <i class="mdi mdi-refresh me-2"></i>Renegociar
                                                    </a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">Mostrando {{ $receivablesPaginator->count() }} de {{ $receivablesPaginator->total() }} parcelas</small>
                    {{ $receivablesPaginator->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

@include('financial.payment-modal')

@push('styles')
<style>
.avatar-sm {
    width: 40px;
    height: 40px;
    font-size: 14px;
    font-weight: bold;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}
</style>
@endpush

@push('scripts')
<script>
let selectedReceivableId = null;

function exportReceivables() {
    alert('Exportando contas a receber...');
}

function sendBulkReminders() {
    const selected = document.querySelectorAll('.receivable-checkbox:checked');
    if (selected.length === 0) {
        alert('Selecione pelo menos uma parcela para enviar lembretes.');
        return;
    }
    alert(`Enviando lembretes para ${selected.length} clientes via WhatsApp...`);
}

function applyFilters() {
    document.getElementById('filtersForm')?.submit();
}

function selectAll() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const checkboxes = document.querySelectorAll('.receivable-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
}

function sendSelectedReminders() {
    const selected = document.querySelectorAll('.receivable-checkbox:checked');
    if (selected.length === 0) {
        alert('Selecione pelo menos uma parcela.');
        return;
    }
    alert(`Enviando lembretes para ${selected.length} parcelas selecionadas...`);
}

function sendUrgentReminder(id) {
    alert(`Enviando cobrança urgente para parcela ${id}...`);
}

function sendWhatsApp(id) {
    alert(`Enviando lembrete via WhatsApp para parcela ${id}...`);
}

function generateBoleto(id) {
    alert(`Gerando boleto para parcela ${id}...`);
}

function viewDetails(id) {
    alert(`Visualizando detalhes da parcela ${id}...`);
}

function viewHistory(id) {
    alert(`Visualizando histórico da parcela ${id}...`);
}

function renegotiate(id) {
    alert(`Iniciando renegociação da parcela ${id}...`);
}

document.getElementById('selectAllCheckbox').addEventListener('change', selectAll);

document.getElementById('statusFilter').value = "{{ $filters['status'] ?? '' }}";
document.getElementById('orderBy').value = "{{ $filters['order_by'] ?? 'vencimento' }}";
</script>
@endpush
@endsection
