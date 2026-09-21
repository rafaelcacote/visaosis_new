@extends('layouts.app')

@section('title', 'Relatório de Vendas - VisaoSis')
@section('page-title', 'Relatório de Vendas')

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
                    <form id="filterForm" method="GET" action="{{ route('reports.sales') }}"
                        class="row g-3 align-items-end">
                        <div class="col-12 col-sm-6 col-md-3">
                            <label for="start_date" class="form-label">Data Início</label>
                            <input type="date" class="form-control form-control-sm" id="start_date" name="start_date"
                                value="{{ $startDate }}">
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <label for="end_date" class="form-label">Data Fim</label>
                            <input type="date" class="form-control form-control-sm" id="end_date" name="end_date"
                                value="{{ $endDate }}">
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select form-select-sm" id="status" name="status">
                                <option value="">Todos</option>
                                <option value="faturado" @selected($status === 'faturado')>Faturado</option>
                                <option value="aberto" @selected($status === 'aberto')>Aberto</option>
                                <option value="cancelado" @selected($status === 'cancelado')>Cancelado</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="mdi mdi-magnify me-1"></i>
                                Filtrar
                            </button>
                            <a href="{{ route('reports.sales') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="mdi mdi-filter-off me-1"></i>
                                Limpar
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
                        <div class="col-md-3 border-end">
                            <h2 class="mb-1 text-primary">{{ $stats['total'] }}</h2>
                            <p class="text-muted mb-0">Total de Vendas</p>
                        </div>
                        <div class="col-md-3 border-end">
                            <h2 class="mb-1 text-success">R$ {{ number_format($stats['valor_total'], 2, ',', '.') }}</h2>
                            <p class="text-muted mb-0">Valor Total</p>
                        </div>
                        <div class="col-md-3 border-end">
                            <h2 class="mb-1 text-info">R$ {{ number_format($stats['ticket_medio'], 2, ',', '.') }}</h2>
                            <p class="text-muted mb-0">Ticket Médio</p>
                        </div>
                        <div class="col-md-3">
                            <h2 class="mb-1 text-danger">{{ $stats['canceladas']['count'] }}</h2>
                            <p class="text-muted mb-0">Canceladas</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabela de Vendas -->
        <div class="col-md-9 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-cart text-primary me-2"></i>
                        Lista de Vendas
                    </h5>
                    <span class="tag" style="background-color: #e0f0ff; color: #1d7dd6;">
                        {{ $stats['total'] }} venda(s)
                    </span>
                </div>
                <div class="card-body p-0">
                    @if ($vendas->isEmpty())
                        <div class="text-center py-5">
                            <i class="mdi mdi-cart-off text-muted" style="font-size: 3rem;"></i>
                            <h5 class="mt-3 text-muted">Nenhuma venda encontrada</h5>
                            <p class="text-muted mb-3">Tente ajustar o período ou os filtros de busca.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Número</th>
                                        <th>Cliente</th>
                                        <th>Data</th>
                                        <th>Vendedor</th>
                                        <th>Pagamento</th>
                                        <th class="text-end">Total</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($vendas as $venda)
                                        <tr>
                                            <td>{{ $venda->numero }}</td>
                                            <td>{{ $venda->cliente->nome ?? 'Cliente não informado' }}</td>
                                            <td>{{ $venda->data_pedido->format('d/m/Y') }}</td>
                                            <td>{{ $venda->user->name ?? 'Não informado' }}</td>
                                            <td>{{ $venda->forma_pagamento ?: 'Não informado' }}</td>
                                            <td class="text-end">{{ $venda->valor_total_formatado }}</td>
                                            <td class="text-center">
                                                @if ($venda->status === 'faturado')
                                                    <span class="badge bg-success">{{ $venda->status_label }}</span>
                                                @elseif ($venda->status === 'aberto')
                                                    <span class="badge bg-warning">{{ $venda->status_label }}</span>
                                                @elseif ($venda->status === 'cancelado')
                                                    <span class="badge bg-danger">{{ $venda->status_label }}</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $venda->status_label }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
                        Vendas por Status
                    </h5>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <small>Faturadas</small>
                        <span class="tag" style="background-color: #e3f9e5; color: #1f9d55;">
                            {{ $stats['faturadas']['count'] }} · R$
                            {{ number_format($stats['faturadas']['valor'], 2, ',', '.') }}
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <small>Abertas</small>
                        <span class="tag" style="background-color: #fff8e6; color: #d97706;">
                            {{ $stats['abertas']['count'] }} · R$
                            {{ number_format($stats['abertas']['valor'], 2, ',', '.') }}
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <small>Canceladas</small>
                        <span class="tag" style="background-color: #fde8e8; color: #c0392b;">
                            {{ $stats['canceladas']['count'] }} · R$
                            {{ number_format($stats['canceladas']['valor'], 2, ',', '.') }}
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
                            ];
                            $filtered = array_filter($raw, function ($v) {
                                return $v !== null && $v !== '';
                            });
                            $params = http_build_query($filtered);
                            $pdfUrl = route('reports.sales.pdf') . ($params ? '?' . $params : '');
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
