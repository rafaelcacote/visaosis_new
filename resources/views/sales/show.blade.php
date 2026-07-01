@extends('layouts.app')

@section('title', 'Detalhes da Venda - VisaoSis')

@section('content')
    <div class="page-show">
        <div class="d-xl-flex justify-content-between align-items-start mb-4">
            <div>
                <h2 class="text-dark font-weight-bold mb-2">
                    <i class="mdi mdi-receipt me-2"></i>
                    Venda {{ $sale['numero'] }}
                </h2>
                <p class="text-muted mb-0">Detalhes da venda #{{ $sale['id'] }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('sales.print', $sale['id']) }}" class="btn btn-outline-primary" target="_blank">
                    <i class="mdi mdi-printer me-2"></i>
                    Imprimir PDF
                </a>
                @if (request()->boolean('from_history') && request()->filled('return_url'))
                    <a href="{{ request('return_url') }}" class="btn btn-outline-info">
                        <i class="mdi mdi-history me-2"></i>
                        Voltar Historico
                    </a>
                @endif
                <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">
                    <i class="mdi mdi-arrow-left me-2"></i>
                    Voltar
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Informações da Venda -->
            <div class="col-lg-8">
                <!-- Cabeçalho da Venda -->
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="mdi mdi-information-outline text-primary me-2"></i>
                                Informações da Venda
                            </h5>
                            <div>
                                @switch($sale['status'])
                                    @case('finalizada')
                                        <span class="badge badge-success">
                                            <i class="mdi mdi-check-circle me-1"></i>
                                            Finalizada
                                        </span>
                                    @break

                                    @case('pendente')
                                        <span class="badge badge-warning">
                                            <i class="mdi mdi-clock-outline me-1"></i>
                                            Pendente
                                        </span>
                                    @break

                                    @case('cancelada')
                                        <span class="badge badge-danger">
                                            <i class="mdi mdi-close-circle me-1"></i>
                                            Cancelada
                                        </span>
                                    @break

                                    @default
                                        <span class="badge badge-secondary">
                                            {{ ucfirst($sale['status']) }}
                                        </span>
                                @endswitch
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">
                                    <i class="mdi mdi-calendar me-1"></i>
                                    Data da Venda
                                </label>
                                <div class="fw-medium">{{ $sale['data_formatada'] }}</div>
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::parse($sale['data'])->diffForHumans() }}
                                </small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">
                                    <i class="mdi mdi-credit-card me-1"></i>
                                    Forma de Pagamento
                                </label>
                                <div class="fw-medium">{{ $sale['forma_pagamento'] }}</div>
                                @if ($sale['parcelas'] > 1)
                                    <small class="text-muted">
                                        {{ $sale['parcelas'] }}x de R$
                                        {{ number_format($sale['valor_parcela'], 2, ',', '.') }}
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informações do Cliente -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="mdi mdi-account-circle text-primary me-2"></i>
                            Dados do Cliente
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($sale['cliente'])
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Nome</label>
                                    <div class="fw-medium">{{ $sale['cliente']['nome'] }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">CPF</label>
                                    <div>{{ $sale['cliente']['cpf'] ?? 'Não informado' }}</div>
                                </div>
                                @if ($sale['cliente']['telefone'])
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted">
                                            <i class="mdi mdi-phone me-1"></i>
                                            Telefone
                                        </label>
                                        <div>{{ $sale['cliente']['telefone'] }}</div>
                                    </div>
                                @endif
                                @if ($sale['cliente']['email'])
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted">
                                            <i class="mdi mdi-email me-1"></i>
                                            E-mail
                                        </label>
                                        <div>{{ $sale['cliente']['email'] }}</div>
                                    </div>
                                @endif
                                @if ($sale['cliente']['endereco'])
                                    <div class="col-12 mb-3">
                                        <label class="form-label text-muted">
                                            <i class="mdi mdi-map-marker me-1"></i>
                                            Endereço
                                        </label>
                                        <div>{{ $sale['cliente']['endereco'] }}</div>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="mdi mdi-account-remove" style="font-size: 3rem;"></i>
                                <p class="mt-2 mb-0">Cliente não informado</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Produtos Comprados -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="mdi mdi-cart-check text-success me-2"></i>
                            Produtos Comprados
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Produto</th>
                                        <th class="text-center">Quantidade</th>
                                        <th class="text-end">Preço Unitário</th>
                                        <th class="text-end">Desconto</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($sale['produtos'] as $produto)
                                        <tr>
                                            <td>
                                                <div class="fw-medium">{{ $produto['nome'] }}</div>
                                                <small class="text-muted">ID: {{ $produto['id'] }}</small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-info">{{ $produto['quantidade'] }}</span>
                                            </td>
                                            <td class="text-end">
                                                R$ {{ number_format($produto['preco_unitario'], 2, ',', '.') }}
                                            </td>
                                            <td class="text-end">
                                                @if ($produto['desconto'] > 0)
                                                    <span class="text-danger">
                                                        - R$ {{ number_format($produto['desconto'], 2, ',', '.') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <strong class="text-success">
                                                    R$ {{ number_format($produto['subtotal'], 2, ',', '.') }}
                                                </strong>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">
                                                <i class="mdi mdi-inbox" style="font-size: 2rem;"></i>
                                                <p class="mt-2 mb-0">Nenhum produto encontrado</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Parcelas da Venda -->
                @if (collect($sale['parcelas_detalhes'] ?? [])->count() > 0)
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="mdi mdi-calendar-multiple-check text-primary me-2"></i>
                                Parcelas da Venda
                            </h5>
                            <span class="badge bg-secondary">
                                {{ collect($sale['parcelas_detalhes'])->count() }} parcela(s)
                            </span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Parcela</th>
                                            <th>Vencimento</th>
                                            <th>Valor</th>
                                            <th>Status</th>
                                            <th>Pago em</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($sale['parcelas_detalhes'] as $parcela)
                                            <tr
                                                class="@if ($parcela['status'] === 'vencida') table-danger @elseif($parcela['status'] === 'vence_hoje') table-warning @elseif($parcela['status'] === 'vence_semana') table-info @elseif($parcela['status'] === 'paga') table-success @endif">
                                                <td>
                                                    <span class="badge bg-secondary">{{ $parcela['parcela'] }}</span>
                                                    @if (!empty($parcela['forma_pagamento']))
                                                        <br><small
                                                            class="text-muted">{{ $parcela['forma_pagamento'] }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (!empty($parcela['vencimento']))
                                                        <span
                                                            class="@if ($parcela['status'] === 'vencida') text-danger @elseif($parcela['status'] === 'vence_hoje') text-warning @elseif($parcela['status'] === 'vence_semana') text-info @else text-success @endif">
                                                            {{ \Carbon\Carbon::parse($parcela['vencimento'])->format('d/m/Y') }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">Nao informado</span>
                                                    @endif

                                                    @if (($parcela['dias_atraso'] ?? 0) > 0)
                                                        <br><small class="text-danger">{{ $parcela['dias_atraso'] }} dias
                                                            atraso</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (($parcela['juros'] ?? 0) > 0)
                                                        <span class="text-decoration-line-through text-muted">
                                                            R$ {{ number_format($parcela['valor_parcela'], 2, ',', '.') }}
                                                        </span>
                                                        <br>
                                                        <strong class="text-danger">
                                                            R$
                                                            {{ number_format($parcela['valor_atualizado'], 2, ',', '.') }}
                                                        </strong>
                                                        <br><small class="text-danger">
                                                            +R$ {{ number_format($parcela['juros'], 2, ',', '.') }} juros
                                                        </small>
                                                    @else
                                                        <strong>R$
                                                            {{ number_format($parcela['valor_atualizado'], 2, ',', '.') }}</strong>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($parcela['status'] === 'vencida')
                                                        <span class="badge bg-danger">
                                                            <i class="mdi mdi-alert-circle me-1"></i>Vencida
                                                        </span>
                                                    @elseif($parcela['status'] === 'vence_hoje')
                                                        <span class="badge bg-warning">
                                                            <i class="mdi mdi-calendar-clock me-1"></i>Vence Hoje
                                                        </span>
                                                    @elseif($parcela['status'] === 'vence_semana')
                                                        <span class="badge bg-info">
                                                            <i class="mdi mdi-calendar-week me-1"></i>Vence na Semana
                                                        </span>
                                                    @elseif($parcela['status'] === 'paga')
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
                                                    @if (!empty($parcela['pago_em']))
                                                        <span class="text-success">
                                                            {{ $parcela['pago_em']->format('d/m/Y H:i') }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">Pendente</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Resumo Financeiro -->
            <div class="col-lg-4">
                <div class="card sticky-top" style="top: 20px;">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="mdi mdi-calculator text-success me-2"></i>
                            Resumo Financeiro
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal:</span>
                                <span class="fw-medium">R$ {{ number_format($sale['subtotal'], 2, ',', '.') }}</span>
                            </div>
                            @if ($sale['desconto'] > 0)
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Desconto:</span>
                                    <span class="text-danger fw-medium">
                                        - R$ {{ number_format($sale['desconto'], 2, ',', '.') }}
                                    </span>
                                </div>
                            @endif
                            <hr>
                            <div class="d-flex justify-content-between">
                                <strong class="fs-5">Total:</strong>
                                <strong class="fs-4 text-success">
                                    R$ {{ number_format($sale['total'], 2, ',', '.') }}
                                </strong>
                            </div>
                        </div>

                        @if ($sale['parcelas'] > 1)
                            <div class="alert alert-info mb-3">
                                <small>
                                    <i class="mdi mdi-information-outline me-1"></i>
                                    <strong>Parcelado em {{ $sale['parcelas'] }}x</strong><br>
                                    Valor da parcela: R$ {{ number_format($sale['valor_parcela'], 2, ',', '.') }}
                                </small>
                            </div>
                        @endif

                        @if ($sale['observacoes'])
                            <div class="mb-3">
                                <h6 class="text-muted mb-2">
                                    <i class="mdi mdi-note-text me-1"></i>
                                    Observações
                                </h6>
                                <p class="mb-0 text-muted small">{{ $sale['observacoes'] }}</p>
                            </div>
                        @endif

                        <hr>

                        <div class="small text-muted">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Venda criada em:</span>
                                <span>{{ $sale['created_at']->format('d/m/Y H:i') }}</span>
                            </div>
                            @if ($sale['updated_at'] && $sale['updated_at'] != $sale['created_at'])
                                <div class="d-flex justify-content-between">
                                    <span>Última atualização:</span>
                                    <span>{{ $sale['updated_at']->format('d/m/Y H:i') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        @media print {

            .btn,
            .sticky-top {
                display: none !important;
            }

            .card {
                border: 1px solid #ddd !important;
                page-break-inside: avoid;
            }
        }

        .sticky-top {
            position: sticky;
            top: 20px;
            z-index: 1020;
        }
    </style>
@endpush
