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
                            <h3 class="font-weight-medium text-right mb-0 text-dark">{{ $financialData['vencidas_count'] ?? 0 }}</h3>
                        </div>
                        <small class="text-danger">R$ {{ number_format($financialData['vencidas_total'] ?? 0, 2, ',', '.') }}</small>
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
                            <h3 class="font-weight-medium text-right mb-0 text-dark">{{ $financialData['vence_hoje_count'] ?? 0 }}</h3>
                        </div>
                        <small class="text-warning">R$ {{ number_format($financialData['vence_hoje_total'] ?? 0, 2, ',', '.') }}</small>
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
                            <h3 class="font-weight-medium text-right mb-0 text-dark">{{ $financialData['vence_semana_count'] ?? 0 }}</h3>
                        </div>
                        <small class="text-info">R$ {{ number_format($financialData['vence_semana_total'] ?? 0, 2, ',', '.') }}</small>
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
                            <h3 class="font-weight-medium text-right mb-0 text-dark">{{ $financialData['em_dia_count'] ?? 0 }}</h3>
                        </div>
                        <small class="text-success">R$ {{ number_format($financialData['em_dia_total'] ?? 0, 2, ',', '.') }}</small>
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
            <div class="card-body financial-filters">
                <form class="row g-3 align-items-end form-aligned-sm js-list-filter-form" onsubmit="event.preventDefault(); applyFilters();">
                <div class="col-12 col-sm-6 col-md-2">
                        <label for="searchInput" class="form-label">Buscar</label>
                        <input type="text" class="form-control form-control-sm" placeholder="Cliente, venda..." id="searchInput"
                            value="{{ $filters['search'] ?? '' }}">
                    </div>    
                <div class="col-12 col-sm-6 col-md-2">
                        <label for="statusFilter" class="form-label">Status</label>
                        <select class="form-select form-select-sm" id="statusFilter">
                            <option value="" {{ empty($activeStatusFilter) ? 'selected' : '' }}>Em Aberto</option>
                            <option value="vencida" {{ ($activeStatusFilter ?? '') === 'vencida' ? 'selected' : '' }}>Vencidas</option>
                            <option value="vence_hoje" {{ ($activeStatusFilter ?? '') === 'vence_hoje' ? 'selected' : '' }}>Vence Hoje</option>
                            <option value="vence_semana" {{ ($activeStatusFilter ?? '') === 'vence_semana' ? 'selected' : '' }}>Vence na Semana</option>
                            <option value="em_dia" {{ ($activeStatusFilter ?? '') === 'em_dia' ? 'selected' : '' }}>Em Dia</option>
                            <option value="pago" {{ ($activeStatusFilter ?? '') === 'pago' ? 'selected' : '' }}>Pagos</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-6 col-md-2">
                        <label for="startDate" class="form-label">Data Inicial</label>
                        <input type="date" class="form-control form-control-sm" id="startDate"
                            value="{{ $filters['start_date'] ?? '' }}">
                    </div>
                    <div class="col-12 col-sm-6 col-md-2">
                        <label for="endDate" class="form-label">Data Final</label>
                        <input type="date" class="form-control form-control-sm" id="endDate"
                            value="{{ $filters['end_date'] ?? '' }}">
                    </div>
                    
                    <div class="col-12 col-sm-6 col-md-2">
                        <label for="orderBy" class="form-label">Ordenar Por</label>
                        <select class="form-select form-select-sm" id="orderBy">
                            <option value="vencimento" {{ ($filters['order_by'] ?? 'vencimento') === 'vencimento' ? 'selected' : '' }}>Vencimento</option>
                            <option value="valor" {{ ($filters['order_by'] ?? '') === 'valor' ? 'selected' : '' }}>Valor</option>
                            <option value="cliente" {{ ($filters['order_by'] ?? '') === 'cliente' ? 'selected' : '' }}>Cliente</option>
                            <option value="atraso" {{ ($filters['order_by'] ?? '') === 'atraso' ? 'selected' : '' }}>Dias Atraso</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-6 col-md-2">
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                <i class="mdi mdi-magnify me-1"></i>
                                Buscar
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearFilters()">
                                <i class="mdi mdi-refresh me-1"></i>
                                Limpar
                            </button>
                        </div>
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
                    Parcelas a Receber ({{ count($receivables) }} de {{ $financialData['total_registros'] ?? count($receivables) }})
                </h6>
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
                                <th width="220">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($receivables as $receivable)
                                <tr class="@if ($receivable['status'] == 'vencida') table-danger @elseif($receivable['status'] == 'vence_hoje') table-warning @elseif($receivable['status'] == 'vence_semana') table-info @endif">
                                    <td>
                                        <input type="checkbox" class="form-check-input receivable-checkbox" value="{{ $receivable['id'] }}">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm @if ($receivable['status'] == 'vencida') bg-danger @elseif($receivable['status'] == 'vence_hoje') bg-warning @elseif($receivable['status'] == 'vence_semana') bg-info @else bg-success @endif text-white rounded-circle me-3 d-flex align-items-center justify-content-center">
                                                {{ substr($receivable['cliente'], 0, 2) }}
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
                                        @elseif($receivable['status'] == 'pago')
                                            <span class="badge bg-success">
                                                <i class="mdi mdi-check-circle me-1"></i>Pago
                                            </span>
                                        @else
                                            <span class="badge bg-success">
                                                <i class="mdi mdi-check-circle me-1"></i>Em Dia
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $paymentPayload = base64_encode(json_encode([
                                                'id' => $receivable['id'],
                                                'cliente' => $receivable['cliente'],
                                                'cpf' => $receivable['cpf'] ?: 'N/A',
                                                'vendaId' => $receivable['venda_id'],
                                                'valorParcela' => (float) $receivable['valor_parcela'],
                                                'juros' => (float) $receivable['juros'],
                                                'valorAtualizado' => (float) $receivable['valor_atualizado'],
                                                'parcela' => $receivable['parcela'],
                                                'vencimento' => \Carbon\Carbon::parse($receivable['vencimento'])->format('d/m/Y'),
                                                'status' => $receivable['status'],
                                            ]));
                                            $detailsPayload = base64_encode(json_encode([
                                                'id' => $receivable['id'],
                                                'cliente' => $receivable['cliente'],
                                                'cpf' => $receivable['cpf'] ?: '-',
                                                'telefone' => $receivable['telefone'],
                                                'vendaId' => $receivable['venda_id'],
                                                'parcela' => $receivable['parcela'],
                                                'vencimento' => \Carbon\Carbon::parse($receivable['vencimento'])->format('d/m/Y'),
                                                'status' => $receivable['status'],
                                                'formaPagamento' => $receivable['forma_pagamento'] ?? '-',
                                                'dataPagamento' => $receivable['data_pagamento'] ?? null,
                                                'valorParcela' => (float) $receivable['valor_parcela'],
                                                'valorTotal' => (float) $receivable['valor_total'],
                                                'valorAtualizado' => (float) $receivable['valor_atualizado'],
                                                'diasAtraso' => (int) $receivable['dias_atraso'],
                                                'juros' => (float) $receivable['juros'],
                                            ]));
                                        @endphp
                                        <div class="actions">
                                            <button type="button" class="btn-action btn-action-whatsapp" title="WhatsApp" onclick="sendWhatsApp({{ $receivable['id'] }})">
                                                <i class="mdi mdi-whatsapp"></i>
                                            </button>
                                            <button type="button" class="btn-action btn-action-boleto" title="Gerar Boleto" onclick="generateBoleto({{ $receivable['id'] }})">
                                                <i class="mdi mdi-file-document-outline"></i>
                                            </button>
                                            @if ($receivable['status'] !== 'pago')
                                                <button type="button" class="btn-action btn-action-pay" title="Receber Pagamento" onclick="openPaymentModalFromPayload('{{ $paymentPayload }}')">
                                                    <i class="mdi mdi-cash"></i>
                                                </button>
                                            @endif
                                            <button type="button" class="btn-action btn-action-view" title="Ver Detalhes" onclick="openDetailsModalFromPayload('{{ $detailsPayload }}')">
                                                <i class="mdi mdi-eye"></i>
                                            </button>
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
                    <small class="text-muted">Mostrando {{ count($receivables) }} de {{ $financialData['total_registros'] ?? count($receivables) }} parcelas</small>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            <li class="page-item active">
                                <span class="page-link">1</span>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#">2</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#">3</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Detalhes -->
<div class="modal fade" id="receivableDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="mdi mdi-eye me-2"></i>Detalhes da Parcela
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted">Cliente</label>
                        <div class="fw-semibold" id="detailsCliente">-</div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted">CPF</label>
                        <div id="detailsCpf">-</div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted">Telefone</label>
                        <div id="detailsTelefone">-</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted">Venda</label>
                        <div class="fw-semibold" id="detailsVendaId">-</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted">Parcela</label>
                        <div id="detailsParcela">-</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted">Status</label>
                        <div id="detailsStatus">-</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted">Vencimento</label>
                        <div id="detailsVencimento">-</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted">Forma de Pagamento</label>
                        <div id="detailsFormaPagamento">-</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted">Data de Pagamento</label>
                        <div id="detailsDataPagamento">-</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted">Valor da Parcela</label>
                        <div class="fw-semibold" id="detailsValorParcela">R$ 0,00</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted">Valor Atualizado</label>
                        <div class="fw-semibold text-success" id="detailsValorAtualizado">R$ 0,00</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted">Total da Venda</label>
                        <div class="fw-semibold" id="detailsValorTotal">R$ 0,00</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="detailsReceivePaymentBtn">
                    <i class="mdi mdi-cash me-2"></i>Receber Pagamento
                </button>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
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

.actions {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}

.btn-action {
    border: 1px solid transparent;
    background: #f8f9fa;
    color: #495057;
    width: 30px;
    height: 30px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-action:hover {
    transform: translateY(-1px);
}

.btn-action-whatsapp {
    background: rgba(25, 135, 84, 0.1);
    color: #198754;
    border-color: rgba(25, 135, 84, 0.2);
}

.btn-action-boleto {
    background: rgba(13, 110, 253, 0.1);
    color: #0d6efd;
    border-color: rgba(13, 110, 253, 0.2);
}

.btn-action-pay {
    background: rgba(25, 135, 84, 0.1);
    color: #198754;
    border-color: rgba(25, 135, 84, 0.2);
}

.btn-action-view {
    background: rgba(108, 117, 125, 0.1);
    color: #6c757d;
    border-color: rgba(108, 117, 125, 0.2);
}
</style>
@endpush

@push('scripts')
<script>
let selectedReceivableId = null;
let currentDetailsData = null;

function applyFilters() {
    const search = document.getElementById('searchInput').value.trim();
    const status = document.getElementById('statusFilter').value;
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    const orderBy = document.getElementById('orderBy').value;
    const params = new URLSearchParams(window.location.search);

    if (search) {
        params.set('search', search);
    } else {
        params.delete('search');
    }

    if (status) {
        params.set('status', status);
    } else {
        params.delete('status');
    }

    if (startDate) {
        params.set('start_date', startDate);
    } else {
        params.delete('start_date');
    }

    if (endDate) {
        params.set('end_date', endDate);
    } else {
        params.delete('end_date');
    }

    if (orderBy) {
        params.set('order_by', orderBy);
    } else {
        params.delete('order_by');
    }

    window.location.href = `${window.location.pathname}?${params.toString()}`;
}

function clearFilters() {
    window.location.href = window.location.pathname;
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

function formatMoney(value) {
    return `R$ ${Number(value || 0).toFixed(2).replace('.', ',')}`;
}

function renderStatusBadge(status) {
    const statusMap = {
        vencida: '<span class="badge bg-danger">Vencida</span>',
        vence_hoje: '<span class="badge bg-warning text-dark">Vence Hoje</span>',
        vence_semana: '<span class="badge bg-info text-dark">Vence na Semana</span>',
        pago: '<span class="badge bg-success">Pago</span>',
        em_dia: '<span class="badge bg-success">Em Dia</span>',
    };
    return statusMap[status] || `<span class="badge bg-secondary">${status}</span>`;
}

function decodePayload(payload) {
    try {
        return JSON.parse(atob(payload));
    } catch (e) {
        console.error('Payload inválido', e);
        return null;
    }
}

function openPaymentModalFromPayload(payload) {
    const data = decodePayload(payload);
    if (!data) return;

    openPaymentModal(
        data.id,
        data.cliente,
        data.cpf,
        data.vendaId,
        data.valorParcela,
        data.juros,
        data.valorAtualizado,
        data.parcela,
        data.vencimento,
        data.status
    );
}

function openDetailsModalFromPayload(payload) {
    const data = decodePayload(payload);
    if (!data) return;
    openDetailsModal(data);
}

function openDetailsModal(data) {
    currentDetailsData = data;
    document.getElementById('detailsCliente').textContent = data.cliente || '-';
    document.getElementById('detailsCpf').textContent = data.cpf || '-';
    document.getElementById('detailsTelefone').textContent = data.telefone || '-';
    document.getElementById('detailsVendaId').textContent = data.vendaId || '-';
    document.getElementById('detailsParcela').textContent = data.parcela || '-';
    document.getElementById('detailsVencimento').textContent = data.vencimento || '-';
    document.getElementById('detailsFormaPagamento').textContent = data.formaPagamento || '-';
    document.getElementById('detailsDataPagamento').textContent = data.dataPagamento || '-';
    document.getElementById('detailsValorParcela').textContent = formatMoney(data.valorParcela);
    document.getElementById('detailsValorAtualizado').textContent = formatMoney(data.valorAtualizado);
    document.getElementById('detailsValorTotal').textContent = formatMoney(data.valorTotal);
    document.getElementById('detailsStatus').innerHTML = renderStatusBadge(data.status);

    const receiveBtn = document.getElementById('detailsReceivePaymentBtn');
    receiveBtn.style.display = data.status === 'pago' ? 'none' : 'inline-flex';

    const modal = new bootstrap.Modal(document.getElementById('receivableDetailsModal'));
    modal.show();
}

function viewHistory(id) {
    alert(`Visualizando histórico da parcela ${id}...`);
}

function renegotiate(id) {
    alert(`Iniciando renegociação da parcela ${id}...`);
}

document.getElementById('detailsReceivePaymentBtn').addEventListener('click', function () {
    if (!currentDetailsData) return;

    const detailsModal = bootstrap.Modal.getInstance(document.getElementById('receivableDetailsModal'));
    if (detailsModal) {
        detailsModal.hide();
    }

    openPaymentModal(
        currentDetailsData.id,
        currentDetailsData.cliente,
        currentDetailsData.cpf || 'N/A',
        currentDetailsData.vendaId,
        currentDetailsData.valorParcela,
        currentDetailsData.juros || 0,
        currentDetailsData.valorAtualizado,
        currentDetailsData.parcela,
        currentDetailsData.vencimento,
        currentDetailsData.status
    );
});

</script>
@endpush
@endsection
