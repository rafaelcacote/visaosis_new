@extends('layouts.app')

@section('title', 'Meu Plano')

@push('plugin-css')
    <link rel="stylesheet" href="{{ asset('assets/css/meu-plano.css') }}">
@endpush

@section('content')
<div class="meu-plano-page">
    <div class="d-xl-flex justify-content-between align-items-start mb-4">
        <div>
            <h2 class="text-dark font-weight-bold mb-2">
                <i class="mdi mdi-file-document-outline me-2"></i>
                Meu Plano
            </h2>
            <p class="text-muted mb-0">Acompanhe seu plano de assinatura e o histórico de mensalidades</p>
        </div>
    </div>

    @if (!$plano)
        <div class="card extrato-card">
            <div class="card-body empty-state">
                <div class="empty-state-icon">
                    <i class="mdi mdi-package-variant-closed"></i>
                </div>
                <h5>Nenhum plano encontrado</h5>
                <p>Não há plano cadastrado para este cliente no momento. Entre em contato com o suporte se acredita que isso é um erro.</p>
            </div>
        </div>
    @else
        {{-- Cards do plano --}}
        <div class="row mb-4">
            <div class="col-md-4 grid-margin stretch-card">
                <div class="card plano-stat-card">
                    <div class="card-body">
                        <div class="plano-stat-icon icon-plano">
                            <i class="mdi mdi-crown"></i>
                        </div>
                        <div>
                            <div class="plano-stat-label">Plano</div>
                            <p class="plano-stat-value">{{ $plano->descricao }}</p>
                            <div class="plano-stat-sub">Assinatura ativa</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 grid-margin stretch-card">
                <div class="card plano-stat-card">
                    <div class="card-body">
                        <div class="plano-stat-icon icon-valor">
                            <i class="mdi mdi-currency-brl"></i>
                        </div>
                        <div>
                            <div class="plano-stat-label">Valor Mensal</div>
                            <p class="plano-stat-value valor-destaque">R$ {{ number_format((float) $plano->valor, 2, ',', '.') }}</p>
                            <div class="plano-stat-sub">Cobrança recorrente</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 grid-margin stretch-card">
                <div class="card plano-stat-card">
                    <div class="card-body">
                        <div class="plano-stat-icon icon-periodo">
                            <i class="mdi mdi-calendar-range"></i>
                        </div>
                        <div>
                            <div class="plano-stat-label">Período</div>
                            <p class="plano-stat-value">
                                {{ \Carbon\Carbon::parse($plano->data_inicio)->format('d/m/Y') }}
                            </p>
                            <div class="plano-stat-sub">
                                @if ($plano->data_termino)
                                    até {{ \Carbon\Carbon::parse($plano->data_termino)->format('d/m/Y') }}
                                @else
                                    <i class="mdi mdi-infinity me-1"></i> Plano vigente
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Extrato --}}
        <div class="card extrato-card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h5>
                    <i class="mdi mdi-format-list-bulleted me-2 text-primary"></i>
                    Extrato de Mensalidades
                </h5>
                @if (!$extrato->isEmpty())
                    <span class="text-muted small">
                        <i class="mdi mdi-information-outline me-1"></i>
                        {{ $resumo['total'] }} {{ $resumo['total'] === 1 ? 'mês' : 'meses' }} no período
                    </span>
                @endif
            </div>
            <div class="card-body">
                @if ($extrato->isEmpty())
                    <div class="empty-state py-4">
                        <div class="empty-state-icon">
                            <i class="mdi mdi-receipt-text-outline"></i>
                        </div>
                        <h5>Nenhuma mensalidade registrada</h5>
                        <p>O extrato será exibido aqui conforme os meses forem gerados a partir da data de início do plano.</p>
                    </div>
                @else
                    {{-- Resumo rápido --}}
                    <div class="extrato-resumo">
                        <span class="extrato-resumo-item resumo-total">
                            <i class="mdi mdi-calendar-multiple"></i>
                            {{ $resumo['total'] }} {{ $resumo['total'] === 1 ? 'mês' : 'meses' }}
                        </span>
                        @if ($resumo['pagas'] > 0)
                            <span class="extrato-resumo-item resumo-pago">
                                <i class="mdi mdi-check-circle"></i>
                                {{ $resumo['pagas'] }} {{ $resumo['pagas'] === 1 ? 'paga' : 'pagas' }}
                                · R$ {{ number_format($resumo['valor_pago'], 2, ',', '.') }}
                            </span>
                        @endif
                        @if ($resumo['pendentes'] > 0)
                            <span class="extrato-resumo-item resumo-pendente">
                                <i class="mdi mdi-clock-outline"></i>
                                {{ $resumo['pendentes'] }} {{ $resumo['pendentes'] === 1 ? 'pendente' : 'pendentes' }}
                            </span>
                        @endif
                        @if ($resumo['atrasadas'] > 0)
                            <span class="extrato-resumo-item resumo-atrasado">
                                <i class="mdi mdi-alert-circle"></i>
                                {{ $resumo['atrasadas'] }} {{ $resumo['atrasadas'] === 1 ? 'atrasada' : 'atrasadas' }}
                            </span>
                        @endif
                    </div>

                    <div class="table-responsive">
                        <table class="table extrato-table">
                            <thead>
                                <tr>
                                    <th><i class="mdi mdi-calendar me-1"></i> Mês</th>
                                    <th><i class="mdi mdi-tag-outline me-1"></i> Valor Esperado</th>
                                    <th><i class="mdi mdi-cash-check me-1"></i> Valor Pago</th>
                                    <th><i class="mdi mdi-calendar-check me-1"></i> Pagamento</th>
                                    <th><i class="mdi mdi-flag-outline me-1"></i> Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($extrato as $item)
                                    @php
                                        $mesCarbon = \Carbon\Carbon::parse($item->mes_referencia)->locale('pt_BR');
                                        $status = strtoupper($item->status_pagamento ?? 'PENDENTE');
                                        $isPago = in_array($status, ['PAGO', 'PAGA', 'PAGAMENTO CONFIRMADO'], true);
                                        $isAtrasado = in_array($status, ['ATRASADO', 'ATRASADA', 'VENCIDO'], true);
                                        $statusClass = $isPago ? 'status-pago' : ($isAtrasado ? 'status-atrasado' : 'status-pendente');
                                        $statusIcon = $isPago ? 'mdi-check-circle' : ($isAtrasado ? 'mdi-alert-circle' : 'mdi-clock-outline');
                                        $statusLabel = $isPago ? 'Pago' : ($isAtrasado ? 'Atrasado' : 'Pendente');
                                    @endphp
                                    <tr class="{{ $isAtrasado ? 'row-atrasado' : '' }}">
                                        <td data-label="Mês">
                                            <div class="mes-referencia">
                                                <div class="mes-referencia-icon">
                                                    <i class="mdi mdi-calendar-month"></i>
                                                </div>
                                                <div>
                                                    <div class="mes-referencia-texto">{{ ucfirst($mesCarbon->translatedFormat('F')) }}</div>
                                                    <div class="mes-referencia-ano">{{ $mesCarbon->format('Y') }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td data-label="Valor Esperado" class="valor-cell">
                                            R$ {{ number_format((float) $item->valor_esperado, 2, ',', '.') }}
                                        </td>
                                        <td data-label="Valor Pago" class="valor-cell {{ $item->valor_pagamento !== null ? 'valor-pago' : 'valor-vazio' }}">
                                            @if ($item->valor_pagamento !== null)
                                                R$ {{ number_format((float) $item->valor_pagamento, 2, ',', '.') }}
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td data-label="Pagamento">
                                            @if ($item->data_pagamento)
                                                <span class="data-pagamento-cell">
                                                    <i class="mdi mdi-calendar-check"></i>
                                                    {{ \Carbon\Carbon::parse($item->data_pagamento)->format('d/m/Y') }}
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td data-label="Status">
                                            <span class="status-badge {{ $statusClass }}">
                                                <i class="mdi {{ $statusIcon }}"></i>
                                                {{ $statusLabel }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
