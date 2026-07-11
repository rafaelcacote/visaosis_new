@extends('layouts.app')

@section('title', 'Histórico de Vendas')

@section('content')
    <div class="d-xl-flex justify-content-between align-items-start mb-4">
        <div>
            <h2 class="text-dark font-weight-bold mb-2">
                <i class="mdi mdi-cart-outline me-2"></i>
                Histórico de Vendas
            </h2>
            <p class="text-muted mb-0">
                Vendas vinculadas ao paciente {{ $pessoa->nome }}
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('pessoas.show', $pessoa) }}" class="btn btn-outline-secondary">
                <i class="mdi mdi-arrow-left me-2"></i>
                Voltar ao Paciente
            </a>
            <a href="{{ route('sales.create', ['cliente_id' => $pessoa->id]) }}" class="btn btn-primary" target="_blank"
                rel="noopener noreferrer">
                <i class="mdi mdi-plus me-2"></i>
                Nova Venda
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 grid-margin stretch-card">
            <div class="card card-statistics">
                <div class="card-body">
                    <div class="clearfix">
                        <div class="float-start">
                            <i class="mdi mdi-cart text-primary icon-lg"></i>
                        </div>
                        <div class="float-end">
                            <p class="mb-0 text-right text-dark">Total de Vendas</p>
                            <div class="fluid-container">
                                <h3 class="font-weight-medium text-right mb-0 text-dark">{{ $vendas->count() }}</h3>
                            </div>
                        </div>
                    </div>
                    <p class="text-muted mt-3 mb-0">
                        <i class="mdi mdi-account me-1"></i> Compras registradas para o paciente
                    </p>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 grid-margin stretch-card">
            <div class="card card-statistics">
                <div class="card-body">
                    <div class="clearfix">
                        <div class="float-start">
                            <i class="mdi mdi-cash-multiple text-success icon-lg"></i>
                        </div>
                        <div class="float-end">
                            <p class="mb-0 text-right text-dark">Total Faturado</p>
                            <div class="fluid-container">
                                <h3 class="font-weight-medium text-right mb-0 text-dark">
                                    R$
                                    {{ number_format($vendas->where('status', 'faturado')->sum('valor_total'), 2, ',', '.') }}
                                </h3>
                            </div>
                        </div>
                    </div>
                    <p class="text-muted mt-3 mb-0">
                        <i class="mdi mdi-check-circle me-1"></i> Somente vendas faturadas
                    </p>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 grid-margin stretch-card">
            <div class="card card-statistics">
                <div class="card-body">
                    <div class="clearfix">
                        <div class="float-start">
                            <i class="mdi mdi-file-document-outline text-info icon-lg"></i>
                        </div>
                        <div class="float-end">
                            <p class="mb-0 text-right text-dark">Ordens de Serviço</p>
                            <div class="fluid-container">
                                <h3 class="font-weight-medium text-right mb-0 text-dark">
                                    {{ $ordensServicoPorVenda->flatten(1)->count() }}
                                </h3>
                            </div>
                        </div>
                    </div>
                    <p class="text-muted mt-3 mb-0">
                        <i class="mdi mdi-tools me-1"></i> OS geradas a partir das vendas
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @if ($vendas->isEmpty())
                <div class="text-center py-5">
                    <i class="mdi mdi-cart-off text-muted" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 text-muted">Nenhuma venda encontrada</h5>
                    <p class="text-muted mb-0">Este paciente ainda não possui vendas registradas.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Venda</th>
                                <th>Data</th>
                                <th>Pagamento</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Itens</th>
                                <th>OS</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($vendas as $venda)
                                @php
                                    $ordensDaVenda = $ordensServicoPorVenda->get($venda->id, collect());
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-semibold">Venda #{{ $venda->numero }}</div>
                                        <small class="text-muted">ID: {{ $venda->id }}</small>
                                    </td>
                                    <td>
                                        {{ $venda->data_pedido_formatada }}
                                    </td>
                                    <td>
                                        {{ $venda->forma_pagamento ?: 'Não informado' }}
                                    </td>
                                    <td class="fw-semibold text-success">
                                        {{ $venda->valor_total_formatado }}
                                    </td>
                                    <td>
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
                                    <td>
                                        <div class="fw-semibold">{{ $venda->itens->sum('quantidade') }} item(ns)</div>
                                        <small class="text-muted">{{ $venda->itens->count() }} produto(s)</small>
                                    </td>
                                    <td>
                                        @if ($ordensDaVenda->isNotEmpty())
                                            @foreach ($ordensDaVenda as $ordemServico)
                                                <div>
                                                    <a
                                                        href="{{ route('ordens-servico.show', ['ordemServico' => $ordemServico, 'from_history' => 1, 'return_url' => url()->full()]) }}">
                                                        OS #{{ $ordemServico->id }}
                                                    </a>
                                                </div>
                                            @endforeach
                                        @else
                                            <span class="text-muted">Nenhuma</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-inline-flex gap-1">
                                            <a href="{{ route('sales.show', ['sale' => $venda, 'from_history' => 1, 'return_url' => url()->full()]) }}"
                                                class="btn btn-sm btn-outline-primary" title="Visualizar venda">
                                                <i class="mdi mdi-eye"></i>
                                            </a>
                                            <a href="{{ route('sales.print', $venda) }}"
                                                class="btn btn-sm btn-outline-success" title="Imprimir venda"
                                                target="_blank">
                                                <i class="mdi mdi-printer"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
