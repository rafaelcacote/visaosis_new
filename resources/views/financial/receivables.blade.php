@extends('layouts.app')

@section('title', 'Contas a Receber - Connect Plus')

@push('plugin-css')
    <link rel="stylesheet" href="{{ asset('assets/css/list-actions.css') }}">
@endpush

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
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-6 grid-margin stretch-card">
            <div class="card card-statistics">
                <div class="card-body">
                    <div class="clearfix">
                        <div class="float-start">
                            <i class="mdi mdi-alert-circle text-danger icon-lg"></i>
                        </div>
                        <div class="float-end">
                            <p class="mb-0 text-right text-dark">Parcelas Vencidas</p>
                            <div class="fluid-container">
                                <h3 class="font-weight-medium text-right mb-0 text-dark">
                                    {{ (int) ($summary['vencidas']['count'] ?? 0) }}</h3>
                            </div>
                            <small class="text-danger">R$
                                {{ number_format((float) ($summary['vencidas']['valor'] ?? 0), 2, ',', '.') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-6 grid-margin stretch-card">
            <div class="card card-statistics">
                <div class="card-body">
                    <div class="clearfix">
                        <div class="float-start">
                            <i class="mdi mdi-calendar-clock text-warning icon-lg"></i>
                        </div>
                        <div class="float-end">
                            <p class="mb-0 text-right text-dark">Vencem Hoje</p>
                            <div class="fluid-container">
                                <h3 class="font-weight-medium text-right mb-0 text-dark">
                                    {{ (int) ($summary['vence_hoje']['count'] ?? 0) }}</h3>
                            </div>
                            <small class="text-warning">R$
                                {{ number_format((float) ($summary['vence_hoje']['valor'] ?? 0), 2, ',', '.') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-6 grid-margin stretch-card">
            <div class="card card-statistics">
                <div class="card-body">
                    <div class="clearfix">
                        <div class="float-start">
                            <i class="mdi mdi-calendar-week text-info icon-lg"></i>
                        </div>
                        <div class="float-end">
                            <p class="mb-0 text-right text-dark">Vencem Esta Semana</p>
                            <div class="fluid-container">
                                <h3 class="font-weight-medium text-right mb-0 text-dark">
                                    {{ (int) ($summary['vence_semana']['count'] ?? 0) }}</h3>
                            </div>
                            <small class="text-info">R$
                                {{ number_format((float) ($summary['vence_semana']['valor'] ?? 0), 2, ',', '.') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-6 grid-margin stretch-card">
            <div class="card card-statistics">
                <div class="card-body">
                    <div class="clearfix">
                        <div class="float-start">
                            <i class="mdi mdi-check-circle text-success icon-lg"></i>
                        </div>
                        <div class="float-end">
                            <p class="mb-0 text-right text-dark">Em Dia</p>
                            <div class="fluid-container">
                                <h3 class="font-weight-medium text-right mb-0 text-dark">
                                    {{ (int) ($summary['em_dia']['count'] ?? 0) }}</h3>
                            </div>
                            <small class="text-success">R$
                                {{ number_format((float) ($summary['em_dia']['valor'] ?? 0), 2, ',', '.') }}</small>
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
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="mdi mdi-filter me-2"></i>
                        Filtros
                    </h6>
                </div>
                <div class="card-body">
                    <form class="row g-3 align-items-end form-aligned-sm js-list-filter-form" id="filtersForm"
                        method="GET" action="{{ route('financial.receivables') }}">
                        <div class="col-md-3 col-sm-6">
                            <label for="searchInput" class="form-label">Buscar</label>
                            <input type="text" class="form-control form-control-sm" placeholder="cliente, venda..."
                                id="searchInput" name="q" value="{{ $filters['q'] ?? '' }}">
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <label for="statusFilter" class="form-label">Status</label>
                            <select class="form-select form-select-sm" id="statusFilter" name="status"
                                onchange="applyFilters()">
                                <option value="">Todos</option>
                                <option value="vencida" {{ request('status') === 'vencida' ? 'selected' : '' }}>Vencidas
                                </option>
                                <option value="vence_hoje" {{ request('status') === 'vence_hoje' ? 'selected' : '' }}>
                                    Vence Hoje</option>
                                <option value="vence_semana" {{ request('status') === 'vence_semana' ? 'selected' : '' }}>
                                    Vence na Semana</option>
                                <option value="em_dia" {{ request('status') === 'em_dia' ? 'selected' : '' }}>Em Dia
                                </option>
                                <option value="paga" {{ request('status') === 'paga' ? 'selected' : '' }}>Pagas
                                </option>
                                <option value="pagamento_parcial"
                                    {{ request('status') === 'pagamento_parcial' ? 'selected' : '' }}>Pagamento
                                    Parcial</option>
                                <option value="saldo_remanescente"
                                    {{ request('status') === 'saldo_remanescente' ? 'selected' : '' }}>Saldo
                                    Remanescente</option>
                            </select>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <label for="startDate" class="form-label">Data Inicial</label>
                            <input type="date" class="form-control form-control-sm" id="startDate" name="start_date"
                                value="{{ $filters['start_date'] ?? '' }}">
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <label for="endDate" class="form-label">Data Final</label>
                            <input type="date" class="form-control form-control-sm" id="endDate" name="end_date"
                                value="{{ $filters['end_date'] ?? '' }}">
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <label for="orderBy" class="form-label">Ordenar Por</label>
                            <select class="form-select form-select-sm" id="orderBy" name="order_by"
                                onchange="applyFilters()">
                                <option value="vencimento"
                                    {{ request('order_by', 'vencimento') === 'vencimento' ? 'selected' : '' }}>
                                    Vencimento</option>
                                <option value="valor" {{ request('order_by') === 'valor' ? 'selected' : '' }}>Valor
                                </option>
                                <option value="cliente" {{ request('order_by') === 'cliente' ? 'selected' : '' }}>
                                    Cliente</option>
                                <option value="atraso" {{ request('order_by') === 'atraso' ? 'selected' : '' }}>Dias
                                    Atraso</option>
                            </select>
                        </div>
                        <div class="col-md-1 col-sm-6 d-sm-none d-md-block">
                            <label class="form-label d-block">&nbsp;</label>
                            <button type="button" class="btn btn-primary btn-sm w-100" onclick="applyFilters()">
                                <i class="mdi mdi-magnify"></i>
                            </button>
                        </div>
                        <div class="col-md-1 col-sm-6">
                            <label for="perPage" class="form-label">Por página</label>
                            <select class="form-select form-select-sm" id="perPage" name="per_page"
                                onchange="applyFilters()">
                                <option value="10"
                                    {{ (int) ($filters['per_page'] ?? ($perPage ?? 10)) === 10 ? 'selected' : '' }}>10
                                </option>
                                <option value="25"
                                    {{ (int) ($filters['per_page'] ?? ($perPage ?? 10)) === 25 ? 'selected' : '' }}>25
                                </option>
                                <option value="50"
                                    {{ (int) ($filters['per_page'] ?? ($perPage ?? 10)) === 50 ? 'selected' : '' }}>50
                                </option>
                                <option value="100"
                                    {{ (int) ($filters['per_page'] ?? ($perPage ?? 10)) === 100 ? 'selected' : '' }}>100
                                </option>
                            </select>
                        </div>
                        <div class="col-12 d-md-none d-sm-block">
                            <button type="button" class="btn btn-primary btn-sm w-100" onclick="applyFilters()">
                                <i class="mdi mdi-magnify me-1"></i>Aplicar Filtros
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
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h6 class="mb-0">
                            <i class="mdi mdi-format-list-bulleted me-2"></i>
                            Parcelas a Receber
                        </h6>
                        <span class="badge bg-primary">{{ $receivablesPaginator->total() }} parcelas</span>
                    </div>
                    <div class="mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <small class="text-muted">
                            Página {{ $receivablesPaginator->currentPage() }} de
                            {{ max(1, $receivablesPaginator->lastPage()) }}
                        </small>
                        <div>
                            {{ $receivablesPaginator->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive list-actions-table-wrap">
                        <table class="table table-hover mb-0 list-actions-table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="list-actions-col-descricao">Cliente</th>
                                    <th class="d-none d-md-table-cell">Venda</th>
                                    <th class="d-none d-md-table-cell">Parcela</th>
                                    <th>Vencimento</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                    <th class="list-actions-col-acoes">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($receivables as $receivable)
                                    @php
                                        $statusPagamentoParcial = $receivable['status'] === 'pagamento_parcial';
                                        $statusSaldoRemanescente = $receivable['status'] === 'saldo_remanescente';
                                        $vendaIdPk =
                                            (int) ($receivable['venda_id_numero'] ?? ($receivable['pedido_id'] ?? 0));
                                        $showUrl =
                                            $vendaIdPk > 0
                                                ? route('sales.show', [
                                                    'sale' => $vendaIdPk,
                                                    'from_history' => 1,
                                                    'return_url' => url()->full(),
                                                ])
                                                : null;
                                    @endphp
                                    <tr @if ($showUrl) onclick="window.location='{{ $showUrl }}'" style="cursor: pointer;" @endif
                                        class="@if ($receivable['status'] == 'vencida') table-danger @elseif($receivable['status'] == 'vence_hoje') table-warning @elseif($receivable['status'] == 'vence_semana') table-info @elseif($receivable['status'] == 'paga') table-success @elseif($statusPagamentoParcial || $statusSaldoRemanescente) table-light @endif">

                                        <td class="list-actions-col-descricao">
                                            <div class="d-flex align-items-center">
                                                <div
                                                    class="avatar-circle @if ($receivable['status'] == 'vencida') bg-danger @elseif($receivable['status'] == 'vence_hoje') bg-warning @elseif($receivable['status'] == 'vence_semana') bg-info @elseif($receivable['status'] == 'paga') bg-success @elseif($statusPagamentoParcial) bg-warning @elseif($statusSaldoRemanescente) bg-secondary @else bg-success @endif text-white me-3 d-none d-sm-flex">
                                                    {{ mb_substr($receivable['cliente'], 0, 2) }}
                                                </div>
                                                <div class="list-actions-desc-text">
                                                    <h6 class="mb-0">{{ $receivable['cliente'] }}</h6>
                                                    <small class="text-muted">{{ $receivable['telefone'] }}</small>
                                                    <small class="text-muted d-sm-none d-block">
                                                        Venda: <strong>{{ $receivable['venda_id'] }}</strong>
                                                        ·
                                                        Parcela: <span
                                                            class="badge bg-secondary">{{ $receivable['parcela'] }}</span>
                                                    </small>
                                                    <small class="text-muted d-sm-none d-block">
                                                        Total: R$
                                                        {{ number_format($receivable['valor_total'], 2, ',', '.') }}
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="d-none d-md-table-cell" data-label="Venda">
                                            <strong>{{ $receivable['venda_id'] }}</strong>
                                            <br><small class="text-muted">Total: R$
                                                {{ number_format($receivable['valor_total'], 2, ',', '.') }}</small>
                                        </td>
                                        <td class="d-none d-md-table-cell" data-label="Parcela">
                                            <span class="badge bg-secondary">{{ $receivable['parcela'] }}</span>
                                            <br><small class="text-muted">R$
                                                {{ number_format($receivable['valor_parcela'], 2, ',', '.') }}</small>
                                            @if ($statusPagamentoParcial && ($receivable['valor_recebido'] ?? 0) > 0)
                                                <br><small class="text-warning">Pago: R$
                                                    {{ number_format($receivable['valor_recebido'], 2, ',', '.') }}</small>
                                            @endif
                                        </td>
                                        <td data-label="Vencimento">
                                            <span
                                                class="@if ($receivable['status'] == 'vencida') text-danger @elseif($receivable['status'] == 'vence_hoje') text-warning @elseif($receivable['status'] == 'vence_semana') text-info @elseif($statusPagamentoParcial || $statusSaldoRemanescente) text-muted @else text-success @endif fw-bold">
                                                {{ \Carbon\Carbon::parse($receivable['vencimento'])->format('d/m/Y') }}
                                            </span>
                                            @if ($receivable['dias_atraso'] > 0)
                                                <br><small class="text-danger">{{ $receivable['dias_atraso'] }} dias
                                                    atraso</small>
                                            @endif
                                        </td>
                                        <td data-label="Valor">
                                            @if ($receivable['juros'] > 0)
                                                <span class="text-decoration-line-through text-muted">R$
                                                    {{ number_format($receivable['valor_parcela'], 2, ',', '.') }}</span>
                                                <br><strong class="text-danger">R$
                                                    {{ number_format($receivable['valor_atualizado'], 2, ',', '.') }}</strong>
                                                <br><small class="text-danger">+R$
                                                    {{ number_format($receivable['juros'], 2, ',', '.') }} juros</small>
                                            @else
                                                <strong class="text-success">R$
                                                    {{ number_format($receivable['valor_atualizado'], 2, ',', '.') }}</strong>
                                            @endif
                                            @if ($statusPagamentoParcial)
                                                <br><small class="text-warning">
                                                    Saldo: R$
                                                    {{ number_format(max(0, ($receivable['valor_parcela'] ?? 0) - ($receivable['valor_recebido'] ?? 0) - ($receivable['valor_desconto'] ?? 0)), 2, ',', '.') }}
                                                </small>
                                            @endif
                                        </td>
                                        <td data-label="Status">
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
                                            @elseif($statusPagamentoParcial)
                                                <span class="badge bg-warning text-dark">
                                                    <i class="mdi mdi-cash-multiple me-1"></i>Pagamento Parcial
                                                </span>
                                            @elseif($statusSaldoRemanescente)
                                                <span class="badge bg-secondary">
                                                    <i class="mdi mdi-receipt-text me-1"></i>Saldo Remanescente
                                                </span>
                                            @else
                                                <span class="badge bg-success">
                                                    <i class="mdi mdi-check-circle me-1"></i>Em Dia
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" onclick="event.stopPropagation();">
                                                <button class="btn btn-outline-secondary dropdown-toggle"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="mdi mdi-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    @if ($receivable['status'] == 'paga')
                                                        <li>
                                                            <button type="button" class="dropdown-item text-success"
                                                                disabled>
                                                                <i class="mdi mdi-check me-2"></i>Pagamento confirmado
                                                            </button>
                                                        </li>
                                                    @endif

                                                    @if ($showUrl)
                                                        <li>
                                                            <a class="dropdown-item" href="{{ $showUrl }}">
                                                                <i class="mdi mdi-eye me-2"></i>Ver Venda
                                                            </a>
                                                        </li>
                                                    @endif

                                                    @if ($receivable['status'] == 'vencida')
                                                        <li>
                                                            <button type="button" class="dropdown-item text-danger"
                                                                onclick="sendUrgentReminder({{ $receivable['id'] }})">
                                                                <i class="mdi mdi-phone me-2"></i>Cobrar Urgente
                                                            </button>
                                                        </li>
                                                    @else
                                                        <li>
                                                            <button type="button" class="dropdown-item"
                                                                onclick='sendReceivableBoleto(@json($receivable['boleto_secure_url'] ?? null), @json($receivable['telefone'] ?? null));'
                                                                @if (empty($receivable['telefone'])) disabled @endif>
                                                                <i class="mdi mdi-whatsapp me-2"></i>Enviar WhatsApp
                                                            </button>
                                                        </li>
                                                    @endif

                                                    <li>
                                                        <button type="button" class="dropdown-item"
                                                            onclick='generateBoleto(@json($receivable['boleto_secure_url'] ?? null))'>
                                                            <i class="mdi mdi-file-document-outline me-2"></i>Gerar
                                                            Boleto
                                                        </button>
                                                    </li>

                                                    @if ($receivable['status'] !== 'paga')
                                                        <li>
                                                            <a href="{{ route('financial.receivables.payment', ['id' => $receivable['id'], 'return_url' => url()->full()]) }}"
                                                                class="dropdown-item">
                                                                <i class="mdi mdi-cash me-2"></i>Dar Baixa
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <button type="button" class="dropdown-item text-warning"
                                                                onclick="openRenegotiateModal({{ $receivable['id'] }})">
                                                                <i class="mdi mdi-refresh me-2"></i>Renegociar
                                                            </button>
                                                        </li>
                                                    @endif

                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <li>
                                                        <button type="button" class="dropdown-item"
                                                            onclick="viewDetails({{ $receivable['id'] }})">
                                                            <i class="mdi mdi-eye me-2"></i>Ver Detalhes
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <button type="button" class="dropdown-item"
                                                            onclick="viewHistory({{ $receivable['id'] }})">
                                                            <i class="mdi mdi-history me-2"></i>Histórico
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center flex-column flex-sm-row gap-2">
                        <small class="text-muted text-center text-sm-start w-100">
                            Mostrando {{ $receivablesPaginator->firstItem() ?? 0 }} a
                            {{ $receivablesPaginator->lastItem() ?? 0 }}
                            de {{ $receivablesPaginator->total() }} parcelas
                        </small>
                        <small class="text-muted text-center text-sm-end w-100">
                            Total da página: <span class="fw-bold text-success">R$
                                {{ number_format(collect($receivables->items())->sum('valor_atualizado'), 2, ',', '.') }}</span>
                        </small>
                    </div>
                    <div class="mt-3 d-flex justify-content-center">
                        {{ $receivablesPaginator->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="receivableDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalhes da Parcela</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="text-muted small">Cliente</div>
                            <div class="fw-semibold" id="rdCliente">-</div>
                            <div class="text-muted small" id="rdTelefone">-</div>
                            <div class="text-muted small" id="rdCpf">-</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Venda</div>
                            <div class="fw-semibold" id="rdVendaId">-</div>
                            <div class="text-muted small" id="rdDataPedido">-</div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="text-muted small">Parcela</div>
                            <div class="fw-semibold" id="rdParcela">-</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Vencimento</div>
                            <div class="fw-semibold" id="rdVencimento">-</div>
                            <div class="text-muted small" id="rdDiasAtraso"></div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Valor</div>
                            <div class="fw-semibold" id="rdValor">-</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Status</div>
                            <div class="fw-semibold" id="rdStatus">-</div>
                            <div class="text-muted small" id="rdPagoEm"></div>
                        </div>
                        <div class="col-12">
                            <a class="btn btn-outline-primary" href="#" id="rdBoletoLink" target="_blank">
                                <i class="mdi mdi-file-document me-2"></i>Abrir boleto
                            </a>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="receivableHistoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Histórico de Parcelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="text-muted small">Cliente</div>
                            <div class="fw-semibold" id="rhCliente">-</div>
                            <div class="text-muted small" id="rhTelefone">-</div>
                            <div class="text-muted small" id="rhCpf">-</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Venda</div>
                            <div class="fw-semibold" id="rhVendaId">-</div>
                            <div class="text-muted small" id="rhDataPedido">-</div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Parcela</th>
                                    <th>Vencimento</th>
                                    <th>Pago em</th>
                                    <th class="text-end">Valor</th>
                                    <th>Status</th>
                                    <th class="text-end">Boleto</th>
                                    <th class="text-end">Recibo</th>
                                </tr>
                            </thead>
                            <tbody id="rhRows">
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-3">Carregando...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="renegotiateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Renegociar Parcela</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="renegotiateId">
                    <div class="mb-3">
                        <div class="text-muted small">Cliente</div>
                        <div class="fw-semibold" id="renegotiateClientName">-</div>
                        <div class="text-muted small" id="renegotiateInstallmentLabel">-</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Novo vencimento</label>
                        <input type="date" class="form-control" id="renegotiateDueDate" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Novo valor</label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="number" class="form-control" id="renegotiateValue" step="0.01"
                                min="0.01" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-warning" onclick="confirmRenegotiation()">Salvar</button>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .avatar-sm {
                width: 40px;
                height: 40px;
                font-size: 14px;
                font-weight: bold;
            }

            .avatar-circle {
                width: 40px;
                height: 40px;
                font-size: 14px;
                font-weight: bold;
                border-radius: 50%;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            .table-hover tbody tr:hover {
                background-color: rgba(0, 123, 255, 0.05);
            }

            @media (max-width: 767.98px) {
                .page-title-wrapper h2 {
                    font-size: 1.25rem;
                }

                .page-title-wrapper p {
                    font-size: 0.875rem;
                }

                .card-statistics .card-body {
                    padding: 0.75rem;
                }

                .card-statistics p.mb-0.text-right {
                    font-size: 0.75rem;
                }

                .card-statistics h3 {
                    font-size: 1.1rem;
                }

                .card-statistics small {
                    font-size: 0.7rem;
                }

                .list-actions-table-wrap {
                    border: 0;
                    background: transparent;
                }

                .list-actions-table {
                    border: 0;
                }

                .list-actions-table thead {
                    display: none;
                }

                .list-actions-table tbody {
                    display: block;
                }

                .list-actions-table tbody tr {
                    display: block;
                    background: #ffffff;
                    border: 1px solid #e9ecef;
                    border-radius: 0.5rem;
                    padding: 0.85rem;
                    margin-bottom: 0.75rem;
                    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
                    position: relative;
                }

                .list-actions-table tbody tr.table-danger {
                    background-color: #fff5f5 !important;
                    border-color: #fecaca;
                }

                .list-actions-table tbody tr.table-warning {
                    background-color: #fffbeb !important;
                    border-color: #fde68a;
                }

                .list-actions-table tbody tr.table-info {
                    background-color: #eff6ff !important;
                    border-color: #bfdbfe;
                }

                .list-actions-table tbody tr.table-success {
                    background-color: #f0fdf4 !important;
                    border-color: #bbf7d0;
                }

                .list-actions-table tbody tr.table-light {
                    background-color: #f8fafc !important;
                    border-color: #e2e8f0;
                }

                .list-actions-table tbody td {
                    display: block;
                    border: 0 !important;
                    padding: 0.25rem 0 !important;
                    background: transparent !important;
                    text-align: left !important;
                }

                .list-actions-table tbody td::before {
                    content: attr(data-label);
                    font-weight: 600;
                    color: #475569;
                    font-size: 0.75rem;
                    margin-right: 0.4rem;
                }

                .list-actions-table tbody td.list-actions-col-descricao::before,
                .list-actions-table tbody td.list-actions-col-acoes::before {
                    content: none;
                }

                .list-actions-table tbody td.list-actions-col-descricao {
                    padding-bottom: 0.5rem !important;
                    border-bottom: 1px dashed #e5e7eb !important;
                    margin-bottom: 0.4rem;
                }

                .list-actions-desc-text h6 {
                    font-size: 0.95rem;
                    margin-bottom: 0.1rem !important;
                }

                .list-actions-desc-text small {
                    font-size: 0.75rem;
                    display: block;
                }

                .list-actions-table tbody td.list-actions-col-acoes {
                    padding-top: 0.5rem !important;
                    border-top: 1px dashed #e5e7eb !important;
                    margin-top: 0.35rem;
                    text-align: right !important;
                }

                .list-actions-col-acoes .btn-group-sm>.btn,
                .list-actions-col-acoes .btn-sm {
                    padding: 0.35rem 0.55rem;
                    font-size: 0.85rem;
                }

                .pagination {
                    flex-wrap: wrap;
                    justify-content: center;
                }

                .pagination .page-link {
                    padding: 0.4rem 0.6rem;
                    font-size: 0.85rem;
                }
            }

            @media (max-width: 575.98px) {
                .card-header h6 {
                    font-size: 0.95rem;
                }

                .badge {
                    font-size: 0.7rem;
                    padding: 0.25em 0.55em;
                }

                .list-actions-table tbody tr {
                    padding: 0.7rem;
                }

                .list-actions-desc-text h6 {
                    font-size: 0.9rem;
                }

                .list-actions-table tbody td {
                    font-size: 0.85rem;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            let selectedReceivableId = null;
            let currentHistoryWaPhone = null;

            function normalizeWhatsappPhone(raw) {
                const digits = (raw || '').toString().replace(/\D+/g, '');
                if (!digits) return null;
                if (digits.startsWith('55')) return digits;
                if (digits.length === 10 || digits.length === 11) return '55' + digits;
                return digits;
            }

            function openReceivableReceipt(parcelaId) {
                const urlTemplate = @json(route('financial.recibo-pdf', ['id' => '__ID__']));
                const url = urlTemplate.replace('__ID__', String(parcelaId));
                window.open(url, '_blank');
            }

            function sendReceivableReceiptWhatsapp(reciboSecureUrl) {
                if (!reciboSecureUrl) {
                    window.showAppModalMessage?.('Link do recibo inválido.', 'Atenção', 'warning');
                    return;
                }
                if (!currentHistoryWaPhone) {
                    window.showAppModalMessage?.('Telefone do cliente não informado.', 'Atenção', 'warning');
                    return;
                }

                const message = 'Segue o recibo de pagamento da sua parcela: ' + reciboSecureUrl;

                const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
                const waUrl = isMobile ?
                    ('https://wa.me/' + currentHistoryWaPhone + '?text=' + encodeURIComponent(message)) :
                    ('https://web.whatsapp.com/send?phone=' + currentHistoryWaPhone + '&text=' + encodeURIComponent(message));
                window.open(waUrl, 'whatsapp_web');
            }

            function exportReceivables() {
                window.showAppModalMessage?.('Exportando contas a receber...', 'Info', 'info');
            }

            function sendBulkReminders() {
                const selected = document.querySelectorAll('.receivable-checkbox:checked');
                if (selected.length === 0) {
                    window.showAppModalMessage?.('Selecione pelo menos uma parcela para enviar lembretes.', 'Atenção',
                        'warning');
                    return;
                }
                window.showAppModalMessage?.(`Enviando lembretes para ${selected.length} clientes via WhatsApp...`, 'Info',
                    'info');
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
                    window.showAppModalMessage?.('Selecione pelo menos uma parcela.', 'Atenção', 'warning');
                    return;
                }
                window.showAppModalMessage?.(`Enviando lembretes para ${selected.length} parcelas selecionadas...`, 'Info',
                    'info');
            }

            function sendUrgentReminder(id) {
                window.showAppModalMessage?.(`Enviando cobrança urgente para parcela ${id}...`, 'Info', 'info');
            }

            function sendReceivableBoleto(boletoUrl, telefone) {
                if (!boletoUrl) {
                    window.showAppModalMessage?.('Link do boleto inválido.', 'Atenção', 'warning');
                    return;
                }

                const digits = (telefone || '').toString().replace(/\D+/g, '');
                let waPhone = digits;
                if (!waPhone) {
                    window.showAppModalMessage?.('Telefone do cliente não informado.', 'Atenção', 'warning');
                    return;
                }
                if (!waPhone.startsWith('55') && (waPhone.length === 10 || waPhone.length === 11)) {
                    waPhone = '55' + waPhone;
                }

                const message = 'Segue o boleto da sua parcela: ' + boletoUrl;

                const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
                const waUrl = isMobile ?
                    ('https://wa.me/' + waPhone + '?text=' + encodeURIComponent(message)) :
                    ('https://web.whatsapp.com/send?phone=' + waPhone + '&text=' + encodeURIComponent(message));
                window.open(waUrl, 'whatsapp_web');
            }

            function generateBoleto(boletoUrl) {
                if (!boletoUrl) {
                    window.showAppModalMessage?.('Link do boleto inválido.', 'Atenção', 'warning');
                    return;
                }
                window.open(boletoUrl, '_blank');
            }

            async function openRenegotiateModal(id) {
                if (!id) {
                    window.showAppModalMessage?.('Parcela inválida.', 'Atenção', 'warning');
                    return;
                }

                const urlTemplate = @json(route('financial.receivables.details', ['id' => '__ID__']));
                const url = urlTemplate.replace('__ID__', String(id));

                try {
                    const res = await fetch(url, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const payload = await res.json().catch(() => ({}));
                    if (!res.ok) {
                        window.showAppModalMessage?.(payload.message || 'Erro ao carregar dados da parcela.', 'Erro',
                            'danger');
                        return;
                    }

                    const data = payload.data || {};
                    const cliente = data.cliente || {};
                    const parcela = data.parcela || {};

                    document.getElementById('renegotiateId').value = parcela.id || id;
                    document.getElementById('renegotiateClientName').textContent = cliente.nome || '-';
                    document.getElementById('renegotiateInstallmentLabel').textContent =
                        (parcela.numero && parcela.total) ? ('Parcela: ' + String(parcela.numero) + '/' + String(parcela
                            .total)) : '-';
                    document.getElementById('renegotiateDueDate').value = parcela.vencimento || '';
                    document.getElementById('renegotiateValue').value = typeof parcela.valor === 'number' ? parcela.valor :
                        (parseFloat(parcela.valor) || 0);

                    const modalEl = document.getElementById('renegotiateModal');
                    if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        bootstrap.Modal.getOrCreateInstance(modalEl).show();
                    } else {
                        window.showAppModalMessage?.('Não foi possível abrir a janela de renegociação.', 'Erro', 'danger');
                    }
                } catch (e) {
                    window.showAppModalMessage?.('Erro ao carregar dados da parcela.', 'Erro', 'danger');
                }
            }

            function confirmRenegotiation() {
                const id = document.getElementById('renegotiateId').value;
                const vencimento = document.getElementById('renegotiateDueDate').value;
                const valor = document.getElementById('renegotiateValue').value;

                if (!id || !vencimento || !valor) {
                    window.showAppModalMessage?.('Preencha vencimento e valor.', 'Atenção', 'warning');
                    return;
                }

                const urlTemplate = @json(route('financial.receivables.renegotiate', ['id' => '__ID__']));
                const url = urlTemplate.replace('__ID__', String(id));

                fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            vencimento_em: vencimento,
                            valor: parseFloat(valor)
                        })
                    })
                    .then(async (res) => {
                        const data = await res.json().catch(() => ({}));
                        if (!res.ok) {
                            throw new Error(data.message || 'Erro ao renegociar parcela.');
                        }
                        return data;
                    })
                    .then(() => {
                        window.showAppModalMessage?.('Parcela renegociada com sucesso!', 'Sucesso', 'success');
                        const modal = bootstrap.Modal.getInstance(document.getElementById('renegotiateModal'));
                        modal.hide();
                        location.reload();
                    })
                    .catch((err) => {
                        window.showAppModalMessage?.(err?.message || 'Erro ao renegociar parcela.', 'Erro', 'danger');
                    });
            }

            async function viewDetails(id) {
                if (!id) {
                    return false;
                }

                const urlTemplate = @json(route('financial.receivables.details', ['id' => '__ID__']));
                const url = urlTemplate.replace('__ID__', String(id));
                try {
                    const res = await fetch(url, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const payload = await res.json().catch(() => ({}));
                    if (!res.ok) {
                        window.showAppModalMessage?.(payload.message || 'Erro ao carregar detalhes.', 'Erro', 'danger');
                        return false;
                    }

                    const data = payload.data || {};
                    const cliente = data.cliente || {};
                    const pedido = data.pedido || {};
                    const parcela = data.parcela || {};

                    const setText = (id, value) => {
                        const el = document.getElementById(id);
                        if (el) el.textContent = value ?? '-';
                    };

                    setText('rdCliente', cliente.nome || '-');
                    setText('rdTelefone', cliente.telefone ? ('Tel: ' + cliente.telefone) : '-');
                    setText('rdCpf', cliente.cpf ? ('CPF: ' + cliente.cpf) : '-');
                    setText('rdVendaId', pedido.venda_id || '-');
                    setText('rdDataPedido', pedido.data_pedido ? ('Data: ' + pedido.data_pedido) : '-');
                    setText('rdParcela', (parcela.numero ? String(parcela.numero) : '-') + '/' + (parcela.total ?
                        String(
                            parcela.total) : '-'));
                    setText('rdVencimento', parcela.vencimento || '-');
                    setText('rdDiasAtraso', parcela.dias_atraso ? (String(parcela.dias_atraso) + ' dias atraso') : '');
                    setText('rdValor', typeof parcela.valor === 'number' ? ('R$ ' + parcela.valor.toFixed(2).replace(
                        '.',
                        ',')) : '-');
                    setText('rdStatus', parcela.status || '-');
                    setText('rdPagoEm', parcela.pago_em ? ('Pago em: ' + parcela.pago_em) : '');

                    const boletoLink = document.getElementById('rdBoletoLink');
                    if (boletoLink) {
                        boletoLink.href = parcela.boleto_secure_url || '#';
                        boletoLink.classList.toggle('disabled', !parcela.boleto_secure_url);
                        boletoLink.setAttribute('aria-disabled', parcela.boleto_secure_url ? 'false' : 'true');
                    }

                    const modalEl = document.getElementById('receivableDetailsModal');
                    if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        bootstrap.Modal.getOrCreateInstance(modalEl).show();
                    } else {
                        window.showAppModalMessage?.('Não foi possível abrir a janela de detalhes.', 'Erro', 'danger');
                    }
                } catch (e) {
                    window.showAppModalMessage?.('Erro ao carregar detalhes.', 'Erro', 'danger');
                }

                return false;
            }

            async function viewHistory(id) {
                if (!id) {
                    return false;
                }

                const urlTemplate = @json(route('financial.receivables.history', ['id' => '__ID__']));
                const url = urlTemplate.replace('__ID__', String(id));

                const tbody = document.getElementById('rhRows');
                if (tbody) {
                    tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-3">Carregando...</td></tr>';
                }

                try {
                    const res = await fetch(url, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const payload = await res.json().catch(() => ({}));
                    if (!res.ok) {
                        window.showAppModalMessage?.(payload.message || 'Erro ao carregar histórico.', 'Erro', 'danger');
                        return false;
                    }

                    const data = payload.data || {};
                    const cliente = data.cliente || {};
                    const pedido = data.pedido || {};
                    const parcelas = Array.isArray(data.parcelas) ? data.parcelas : [];

                    const setText = (id, value) => {
                        const el = document.getElementById(id);
                        if (el) el.textContent = value ?? '-';
                    };

                    setText('rhCliente', cliente.nome || '-');
                    setText('rhTelefone', cliente.telefone ? ('Tel: ' + cliente.telefone) : '-');
                    setText('rhCpf', cliente.cpf ? ('CPF: ' + cliente.cpf) : '-');
                    setText('rhVendaId', pedido.venda_id || '-');
                    setText('rhDataPedido', pedido.data_pedido ? ('Data: ' + pedido.data_pedido) : '-');
                    currentHistoryWaPhone = normalizeWhatsappPhone(cliente.telefone || null);

                    if (tbody) {
                        const rows = parcelas.map(function(p) {
                            const parcelaLabel = (p.numero ? String(p.numero) : '-') + '/' + (p.total ? String(p
                                .total) : '-');
                            const venc = p.vencimento || '-';
                            const pagoEm = p.pago_em || '-';
                            const valor = typeof p.valor === 'number' ? ('R$ ' + p.valor.toFixed(2).replace('.',
                                ',')) : '-';

                            let badge = '<span class="badge bg-secondary">-</span>';
                            if (p.status === 'paga') badge = '<span class="badge bg-success">Paga</span>';
                            else if (p.status === 'vencida') badge =
                                '<span class="badge bg-danger">Vencida</span>';
                            else if (p.status === 'a_vencer') badge =
                                '<span class="badge bg-info">A vencer</span>';
                            else if (p.status === 'pagamento_parcial') badge =
                                '<span class="badge bg-warning text-dark">Pagamento Parcial</span>';
                            else if (p.status === 'saldo_remanescente') badge =
                                '<span class="badge bg-secondary">Saldo Remanescente</span>';

                            const boletoBtn = p.boleto_secure_url ?
                                ('<a class="btn btn-sm btn-outline-primary" href="' + p.boleto_secure_url +
                                    '" target="_blank"><i class="mdi mdi-file-document-outline"></i></a>') :
                                (
                                    '<button class="btn btn-sm btn-outline-secondary" disabled><i class="mdi mdi-file-document-outline"></i></button>'
                                );

                            const secureUrl = (p.recibo_secure_url || '').replace(/'/g, "\\'");
                            const reciboBtn = p.status === 'paga' ?
                                ('<div class="btn-group btn-group-sm" role="group">' +
                                    '<button type="button" class="btn btn-sm btn-outline-secondary" onclick="openReceivableReceipt(' +
                                    String(p.id) + ')"><i class="mdi mdi-receipt"></i></button>' +
                                    '<button type="button" class="btn btn-sm btn-outline-success" onclick="sendReceivableReceiptWhatsapp(\'' +
                                    secureUrl + '\')"><i class="mdi mdi-whatsapp"></i></button>' +
                                    '</div>') :
                                (
                                    '<button class="btn btn-sm btn-outline-secondary" disabled><i class="mdi mdi-receipt"></i></button>'
                                );

                            return '<tr>' +
                                '<td>' + parcelaLabel + '</td>' +
                                '<td>' + venc + (p.dias_atraso ? ('<div class="text-danger small">' + String(p
                                    .dias_atraso) + ' dias atraso</div>') : '') + '</td>' +
                                '<td>' + pagoEm + '</td>' +
                                '<td class="text-end">' + valor + '</td>' +
                                '<td>' + badge + '</td>' +
                                '<td class="text-end">' + boletoBtn + '</td>' +
                                '<td class="text-end">' + reciboBtn + '</td>' +
                                '</tr>';
                        }).join('');

                        tbody.innerHTML = rows ||
                            '<tr><td colspan="7" class="text-center text-muted py-3">Nenhuma parcela encontrada.</td></tr>';
                    }

                    const modalEl = document.getElementById('receivableHistoryModal');
                    if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        bootstrap.Modal.getOrCreateInstance(modalEl).show();
                    } else {
                        window.showAppModalMessage?.('Não foi possível abrir a janela de histórico.', 'Erro', 'danger');
                    }
                } catch (e) {
                    window.showAppModalMessage?.('Erro ao carregar histórico.', 'Erro', 'danger');
                }

                return false;
            }

            function renegotiate(id) {
                window.showAppModalMessage?.(`Iniciando renegociação da parcela ${id}...`, 'Info', 'info');
            }

            document.getElementById('selectAllCheckbox')?.addEventListener('change', selectAll);

            document.getElementById('statusFilter').value = "{{ $filters['status'] ?? '' }}";
            document.getElementById('orderBy').value = "{{ $filters['order_by'] ?? 'vencimento' }}";
        </script>
    @endpush
@endsection
