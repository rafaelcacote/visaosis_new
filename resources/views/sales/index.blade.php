@extends('layouts.app')

@section('title', 'Vendas - VisaoSis')

@section('content')
<div class="d-xl-flex justify-content-between align-items-start mb-4">
    <div>
        <h2 class="text-dark font-weight-bold mb-2">
            <i class="mdi mdi-cart me-2"></i>
            Vendas
        </h2>
        <p class="text-muted mb-0">Gestão de vendas e pedidos</p>
    </div>
    <a href="{{ route('sales.create') }}" class="btn btn-primary btn-icon-text">
        <i class="mdi mdi-plus me-2"></i>
        Nova Venda
    </a>
</div>

<!-- Cards de Estatísticas -->
<div class="row mb-4">
    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 grid-margin stretch-card">
        <div class="card card-statistics">
            <div class="card-body">
                <div class="clearfix">
                    <div class="float-start">
                        <i class="mdi mdi-cash-multiple text-success icon-lg"></i>
                    </div>
                    <div class="float-end">
                        <p class="mb-0 text-right text-dark">Vendas Hoje</p>
                        <div class="fluid-container">
                            <h3 class="font-weight-medium text-right mb-0 text-dark">R$ {{ number_format($vendasHoje ?? 0, 2, ',', '.') }}</h3>
                        </div>
                    </div>
                </div>
                <p class="text-muted mt-3 mb-0">
                    <i class="mdi mdi-calendar me-1" aria-hidden="true"></i> Faturamento do dia
                </p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 grid-margin stretch-card">
        <div class="card card-statistics">
            <div class="card-body">
                <div class="clearfix">
                    <div class="float-start">
                        <i class="mdi mdi-cart-outline text-primary icon-lg"></i>
                    </div>
                    <div class="float-end">
                        <p class="mb-0 text-right text-dark">Total de Vendas</p>
                        <div class="fluid-container">
                            <h3 class="font-weight-medium text-right mb-0 text-dark">{{ $totalVendas ?? count($sales) }}</h3>
                        </div>
                    </div>
                </div>
                <p class="text-muted mt-3 mb-0">
                    <i class="mdi mdi-chart-line me-1" aria-hidden="true"></i> Vendas realizadas
                </p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 grid-margin stretch-card">
        <div class="card card-statistics">
            <div class="card-body">
                <div class="clearfix">
                    <div class="float-start">
                        <i class="mdi mdi-currency-usd text-info icon-lg"></i>
                    </div>
                    <div class="float-end">
                        <p class="mb-0 text-right text-dark">Ticket Médio</p>
                        <div class="fluid-container">
                            <h3 class="font-weight-medium text-right mb-0 text-dark">R$ {{ number_format($ticketMedio ?? 0, 2, ',', '.') }}</h3>
                        </div>
                    </div>
                </div>
                <p class="text-muted mt-3 mb-0">
                    <i class="mdi mdi-chart-bar me-1" aria-hidden="true"></i> Média por venda
                </p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 grid-margin stretch-card">
        <div class="card card-statistics">
            <div class="card-body">
                <div class="clearfix">
                    <div class="float-start">
                        <i class="mdi mdi-clock-outline text-warning icon-lg"></i>
                    </div>
                    <div class="float-end">
                        <p class="mb-0 text-right text-dark">Pendentes</p>
                        <div class="fluid-container">
                            <h3 class="font-weight-medium text-right mb-0 text-dark">{{ $pendentes ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
                <p class="text-muted mt-3 mb-0">
                    <i class="mdi mdi-alert-circle me-1" aria-hidden="true"></i> Aguardando finalização
                </p>
            </div>
        </div>
    </div>
</div>

    <div class="row mb-4">
        <!-- Filtros -->
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form class="row g-3" method="GET" action="{{ route('sales.index') }}">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Buscar Venda</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   placeholder="Número, cliente..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Todos</option>
                                <option value="faturado" {{ request('status') == 'faturado' ? 'selected' : '' }}>Faturado</option>
                                <option value="aberto" {{ request('status') == 'aberto' ? 'selected' : '' }}>Aberto</option>
                                <option value="cancelado" {{ request('status') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="pagamento" class="form-label">Pagamento</label>
                            <select class="form-select" id="pagamento" name="pagamento">
                                <option value="">Todos</option>
                                <option value="Dinheiro" {{ request('pagamento') == 'Dinheiro' ? 'selected' : '' }}>Dinheiro</option>
                                <option value="Cartão de Débito" {{ request('pagamento') == 'Cartão de Débito' ? 'selected' : '' }}>Cartão de Débito</option>
                                <option value="Cartão de Crédito" {{ request('pagamento') == 'Cartão de Crédito' ? 'selected' : '' }}>Cartão de Crédito</option>
                                <option value="Crediário" {{ request('pagamento') == 'Crediário' ? 'selected' : '' }}>Crediário</option>
                                <option value="PIX" {{ request('pagamento') == 'PIX' ? 'selected' : '' }}>PIX</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="periodo" class="form-label">Período</label>
                            <select class="form-select" id="periodo" name="periodo">
                                <option value="">Qualquer período</option>
                                <option value="hoje" {{ request('periodo') == 'hoje' ? 'selected' : '' }}>Hoje</option>
                                <option value="semana" {{ request('periodo') == 'semana' ? 'selected' : '' }}>Esta semana</option>
                                <option value="mes" {{ request('periodo') == 'mes' ? 'selected' : '' }}>Este mês</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-outline-primary w-100">
                                <i class="mdi mdi-magnify me-1"></i>
                                Filtrar
                            </button>
                            @if(request()->anyFilled(['search', 'status', 'pagamento', 'periodo']))
                                <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">
                                    <i class="mdi mdi-close"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-cart text-primary me-2"></i>
                        Lista de Vendas
                    </h5>
                    <div class="d-flex gap-2 align-items-center">
                        <button class="btn btn-sm btn-outline-success" onclick="exportSales()">
                            <i class="mdi mdi-download me-1"></i>
                            Exportar
                        </button>
                        <span class="badge bg-primary">{{ count($sales) }} vendas</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Número</th>
                                    <th>Cliente</th>
                                    <th>Data</th>
                                    <th>Produtos</th>
                                    <th>Pagamento</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th width="140">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="salesTable">
                                @forelse ($sales as $sale)
                                    <tr data-status="{{ $sale['status'] }}"
                                        data-payment="{{ strtolower(str_replace(' ', '', $sale['forma_pagamento'])) }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle bg-primary text-white me-3">
                                                    <i class="mdi mdi-receipt"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $sale['numero'] }}</h6>
                                                    <small class="text-muted">ID: {{ $sale['id'] }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <h6 class="mb-0">{{ $sale['cliente'] }}</h6>
                                            <small class="text-muted">{{ $sale['produtos'] }} produto(s)</small>
                                        </td>
                                        <td>
                                            <span
                                                class="fw-bold">{{ \Carbon\Carbon::parse($sale['data'])->format('d/m/Y') }}</span>
                                            <br>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($sale['data'])->diffForHumans() }}
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $sale['produtos'] }} item(s)
                                            </span>
                                        </td>
                                        <td>
                                            <div>
                                                <span class="fw-bold">{{ $sale['forma_pagamento'] }}</span>
                                                @if (isset($sale['parcelas']) && $sale['parcelas'] > 1)
                                                    <br>
                                                    <small class="text-muted">{{ $sale['parcelas'] }}x de R$
                                                        {{ number_format($sale['total'] / $sale['parcelas'], 2, ',', '.') }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <h6 class="mb-0 text-success">R$
                                                {{ number_format($sale['total'], 2, ',', '.') }}</h6>
                                        </td>
                                        <td>
                                            @switch($sale['status'])
                                                @case('finalizada')
                                                    <span class="badge bg-success">
                                                        <i class="mdi mdi-check-circle me-1"></i>
                                                        Finalizada
                                                    </span>
                                                @break

                                                @case('pendente')
                                                    <span class="badge bg-warning">
                                                        <i class="mdi mdi-clock me-1"></i>
                                                        Pendente
                                                    </span>
                                                @break

                                                @case('cancelada')
                                                    <span class="badge bg-danger">
                                                        <i class="mdi mdi-close-circle me-1"></i>
                                                        Cancelada
                                                    </span>
                                                @break

                                                @default
                                                    <span class="badge bg-secondary">
                                                        {{ ucfirst($sale['status']) }}
                                                    </span>
                                            @endswitch
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('sales.show', $sale['id']) }}"
                                                    class="btn btn-sm btn-outline-primary" title="Ver Detalhes">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
                                                <a href="{{ route('sales.print', $sale['id']) }}" 
                                                    class="btn btn-sm btn-outline-success" 
                                                    title="Imprimir" 
                                                    target="_blank">
                                                    <i class="mdi mdi-printer"></i>
                                                </a>
                                                @if ($sale['status'] == 'pendente')
                                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                                        title="Cancelar" onclick="confirmCancel({{ $sale['id'] }})">
                                                        <i class="mdi mdi-close"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="mdi mdi-inbox" style="font-size: 3rem;"></i>
                                                <p class="mt-3 mb-0">Nenhuma venda encontrada.</p>
                                                <small>Comece criando uma nova venda.</small>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            Mostrando {{ count($sales) }} de {{ count($sales) }} vendas
                        </small>
                        <div class="d-flex align-items-center gap-3">
                            <small class="text-muted">
                                Total: <span class="fw-bold text-success">R$
                                    {{ number_format(collect($sales)->sum('total'), 2, ',', '.') }}</span>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação de Cancelamento -->
    <div class="modal fade" id="cancelModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Cancelamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Tem certeza que deseja cancelar esta venda?</p>
                    <p class="text-muted">Esta ação não pode ser desfeita.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form id="cancelForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Cancelar Venda</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .avatar-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .table td {
            vertical-align: middle;
        }

        .btn-group .btn {
            border-radius: 6px !important;
            margin-right: 2px;
        }

        .btn-group .btn:last-child {
            margin-right: 0;
        }

        .card-body .table th {
            border-top: none;
            font-weight: 600;
            color: #374151;
        }
    </style>
@endpush

@push('scripts')
    <script>
        function confirmCancel(saleId) {
            const form = document.getElementById('cancelForm');
            form.action = `/sales/${saleId}`;

            const modal = new bootstrap.Modal(document.getElementById('cancelModal'));
            modal.show();
        }

        function exportSales() {
            alert('Funcionalidade de exportação será implementada!');
        }

        // Filtro em tempo real
        document.getElementById('search').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#salesTable tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // Filtro por status
        document.getElementById('status').addEventListener('change', function() {
            const status = this.value;
            const rows = document.querySelectorAll('#salesTable tr');

            rows.forEach(row => {
                if (!status || row.dataset.status === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
@endpush
