@extends('layouts.app')

@section('title', 'Relatório de Vendas Detalhado - VisaoSis')
@section('page-title', 'Relatório de Vendas Detalhado')

@push('plugin-css')
    <link rel="stylesheet" href="{{ asset('assets/css/users.css') }}">
@endpush

@section('page-actions')
    <div class="d-flex gap-2">
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
            <i class="mdi mdi-arrow-left me-2"></i>
            Voltar
        </a>
    </div>
@endsection

@section('content')
    <div class="row">

        <!-- Filtros -->
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-filter text-primary me-2"></i>
                        Filtros
                    </h5>
                </div>
                <div class="card-body">
                    <form id="filterForm" method="GET" action="{{ route('reports.sales-detailed') }}"
                        class="row g-3 align-items-end">
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="start_date" class="form-label">Data Início</label>
                            <input type="date" class="form-control form-control-sm" id="start_date" name="start_date"
                                value="{{ $startDate }}">
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="end_date" class="form-label">Data Fim</label>
                            <input type="date" class="form-control form-control-sm" id="end_date" name="end_date"
                                value="{{ $endDate }}">
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select form-select-sm" id="status" name="status">
                                <option value="">Todos</option>
                                <option value="faturado" @selected($status === 'faturado')>Faturado</option>
                                <option value="aberto" @selected($status === 'aberto')>Aberto</option>
                                <option value="cancelado" @selected($status === 'cancelado')>Cancelado</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <label for="user_id" class="form-label">Vendedor</label>
                            <select class="form-select form-select-sm" id="user_id" name="user_id">
                                <option value="">Todos</option>
                                @foreach ($vendedores as $vendedor)
                                    <option value="{{ $vendedor->id }}" @selected((string) $userId === (string) $vendedor->id)>
                                        {{ $vendedor->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="q" class="form-label">Buscar</label>
                            <input type="text" class="form-control form-control-sm" id="q" name="q"
                                placeholder="cliente, número..." value="{{ $q }}">
                        </div>
                        <div class="col-12 col-md-1 d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="mdi mdi-magnify"></i>
                            </button>
                            <a href="{{ route('reports.sales-detailed') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="mdi mdi-filter-off"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Resumo Estatístico -->
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-2 border-end">
                            <h2 class="mb-1 text-primary">{{ $stats['total'] }}</h2>
                            <p class="text-muted mb-0">Vendas</p>
                        </div>
                        <div class="col-md-2 border-end">
                            <h2 class="mb-1 text-dark">R$ {{ number_format($stats['valor_total'], 2, ',', '.') }}</h2>
                            <p class="text-muted mb-0">Valor Total</p>
                        </div>
                        <div class="col-md-2 border-end">
                            <h2 class="mb-1 text-success">R$
                                {{ number_format($stats['valor_recebido'], 2, ',', '.') }}</h2>
                            <p class="text-muted mb-0">Total Recebido</p>
                        </div>
                        <div class="col-md-2 border-end">
                            <h2 class="mb-1 text-danger">R$
                                {{ number_format($stats['valor_pendente'], 2, ',', '.') }}</h2>
                            <p class="text-muted mb-0">A Receber</p>
                        </div>
                        <div class="col-md-2 border-end">
                            <h2 class="mb-1 text-info">R$ {{ number_format($stats['ticket_medio'], 2, ',', '.') }}</h2>
                            <p class="text-muted mb-0">Ticket Médio</p>
                        </div>
                        <div class="col-md-2">
                            <h2 class="mb-1 text-warning">{{ $stats['parciais'] }}</h2>
                            <p class="text-muted mb-0">Pagamento Parcial</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Vendas -->
        <div class="col-md-9 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-cart text-primary me-2"></i>
                        Vendas Detalhadas
                    </h5>
                    <span class="tag" style="background-color: #e0f0ff; color: #1d7dd6;">
                        {{ $stats['total'] }} venda(s)
                    </span>
                </div>
                <div class="card-body p-0">
                    @if ($rows->isEmpty())
                        <div class="text-center py-5">
                            <i class="mdi mdi-cart-off text-muted" style="font-size: 3rem;"></i>
                            <h5 class="mt-3 text-muted">Nenhuma venda encontrada</h5>
                            <p class="text-muted mb-3">Tente ajustar o período ou os filtros de busca.</p>
                        </div>
                    @else
                        <div class="accordion" id="vendasAccordion">
                            @foreach ($rows as $venda)
                                <div class="border-bottom">
                                    <div class="d-flex flex-wrap justify-content-between align-items-center p-3"
                                        style="cursor: pointer;" data-bs-toggle="collapse"
                                        data-bs-target="#venda-{{ $venda['id'] }}">
                                        <div class="me-3" style="min-width: 220px;">
                                            <h6 class="mb-0">{{ $venda['numero'] }} — {{ $venda['cliente'] }}</h6>
                                            <small class="text-muted">
                                                Vendedor: <strong>{{ $venda['vendedor'] }}</strong> ·
                                                {{ $venda['data_pedido']->format('d/m/Y H:i') }}
                                            </small>
                                        </div>
                                        <div class="text-end me-3">
                                            <small class="text-muted d-block">Total</small>
                                            <strong>R$ {{ number_format($venda['valor_total'], 2, ',', '.') }}</strong>
                                        </div>
                                        <div class="text-end me-3">
                                            <small class="text-muted d-block">Recebido</small>
                                            <strong class="text-success">R$
                                                {{ number_format($venda['valor_recebido'], 2, ',', '.') }}</strong>
                                        </div>
                                        <div class="text-end me-3">
                                            <small class="text-muted d-block">A Receber</small>
                                            <strong class="text-danger">R$
                                                {{ number_format($venda['valor_pendente'], 2, ',', '.') }}</strong>
                                        </div>
                                        <div class="text-center me-3">
                                            @if ($venda['status_recebimento'] === 'quitada')
                                                <span class="badge bg-success">Quitada</span>
                                            @elseif($venda['status_recebimento'] === 'parcial')
                                                <span class="badge bg-warning text-dark">Pagamento Parcial</span>
                                            @elseif($venda['status_recebimento'] === 'cancelada')
                                                <span class="badge bg-secondary">Cancelada</span>
                                            @else
                                                <span class="badge bg-danger">Pendente</span>
                                            @endif
                                        </div>
                                        <i class="mdi mdi-chevron-down"></i>
                                    </div>
                                    <div class="collapse" id="venda-{{ $venda['id'] }}">
                                        <div class="px-3 pb-3">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Parcela</th>
                                                            <th>Vencimento</th>
                                                            <th class="text-end">Valor</th>
                                                            <th class="text-end">Recebido</th>
                                                            <th class="text-center">Status</th>
                                                            <th>Forma Pgto</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse ($venda['parcelas'] as $parcela)
                                                            <tr>
                                                                <td>{{ $parcela['parcela'] }}</td>
                                                                <td>
                                                                    {{ $parcela['vencimento'] ? \Carbon\Carbon::parse($parcela['vencimento'])->format('d/m/Y') : '—' }}
                                                                </td>
                                                                <td class="text-end">R$
                                                                    {{ number_format($parcela['valor'], 2, ',', '.') }}
                                                                </td>
                                                                <td class="text-end">R$
                                                                    {{ number_format($parcela['valor_recebido'], 2, ',', '.') }}
                                                                </td>
                                                                <td class="text-center">
                                                                    @if ($parcela['status'] === 'paga')
                                                                        <span class="badge bg-success">Paga</span>
                                                                    @elseif($parcela['status'] === 'vencida')
                                                                        <span class="badge bg-danger">Vencida</span>
                                                                    @elseif($parcela['status'] === 'vence_hoje')
                                                                        <span class="badge bg-warning text-dark">Vence
                                                                            Hoje</span>
                                                                    @elseif($parcela['status'] === 'pagamento_parcial')
                                                                        <span
                                                                            class="badge bg-warning text-dark">Parcial</span>
                                                                    @else
                                                                        <span class="badge bg-info">Em Dia</span>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $parcela['forma_pagamento'] ?: '—' }}</td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="6" class="text-center text-muted">
                                                                    Nenhuma parcela cadastrada para esta venda.
                                                                </td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-3 mb-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-chart-donut text-primary me-2"></i>
                        Situação de Recebimento
                    </h5>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <small>Quitadas</small>
                        <span class="tag" style="background-color: #e3f9e5; color: #1f9d55;">
                            {{ $stats['quitadas'] }}
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <small>Pagamento Parcial</small>
                        <span class="tag" style="background-color: #fff8e6; color: #d97706;">
                            {{ $stats['parciais'] }}
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <small>Pendentes</small>
                        <span class="tag" style="background-color: #fde8e8; color: #c0392b;">
                            {{ $stats['pendentes'] }}
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <small>Canceladas</small>
                        <span class="tag" style="background-color: #f1f1f1; color: #6c757d;">
                            {{ $stats['canceladas'] }}
                        </span>
                    </li>
                </ul>
            </div>

            <!-- Ações -->
            <div class="card">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @php
                            $raw = [
                                'start_date' => $startDate ?? null,
                                'end_date' => $endDate ?? null,
                                'status' => $status ?? null,
                                'user_id' => $userId ?? null,
                                'q' => $q ?? null,
                            ];
                            $filtered = array_filter($raw, function ($v) {
                                return $v !== null && $v !== '';
                            });
                            $params = http_build_query($filtered);
                            $pdfUrl = route('reports.sales-detailed.pdf') . ($params ? '?' . $params : '');
                        @endphp
                        <a href="{{ $pdfUrl }}" class="btn btn-primary" target="_blank">
                            <i class="mdi mdi-file-pdf me-2"></i>Imprimir Relatório
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
