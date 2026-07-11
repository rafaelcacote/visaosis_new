@extends('layouts.app')

@section('title', 'Dashboard')

@push('plugin-js')
    <script src="{{ asset('assets/vendors/chart.js/Chart.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/jquery-circle-progress/js/circle-progress.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.cookie.js') }}" type="text/javascript"></script>
@endpush

@push('scripts')
    <script>
        window.DASHBOARD_VENDAS_POR_MES = @json($vendasPorMes ?? ['labels' => [], 'valores' => []]);
        window.DASHBOARD_CLIENTES_POR_MES = @json($clientesPorMes ?? ['labels' => [], 'valores' => []]);
    </script>
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>
@endpush

@section('content')
    <h2 class="text-dark font-weight-bold mb-2"><i class="mdi mdi-view-dashboard me-2"></i>Dashboard</h2>
    <div class="row">
        {{-- Card: Clientes últimos 12 meses --}}
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
            <div class="card card-statistics">
                <div class="card-body">
                    <div class="clearfix">
                        <div class="float-start">
                            <i class="mdi mdi-hospital-building text-primary icon-lg"></i>
                        </div>
                        <div class="float-end">
                            <p class="mb-0 text-right text-dark">Clientes últimos 12 meses</p>
                            <div class="fluid-container">
                                <h3 class="font-weight-medium text-right mb-0 text-dark">{{ $clientesUltimos12Meses ?? 0 }}
                                </h3>
                            </div>
                        </div>
                    </div>
                    <p class="text-muted mt-3 mb-0">
                        <i class="mdi mdi-calendar-month-outline me-1" aria-hidden="true"></i> Clientes nos últimos 12
                        meses
                    </p>
                </div>
            </div>
        </div>

        {{-- Card: Receitas vencidas --}}
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
            <div class="card card-statistics">
                <div class="card-body">
                    <div class="clearfix">
                        <div class="float-start">
                            <i class="mdi mdi-timer-sand text-warning icon-lg"></i>
                        </div>
                        <div class="float-end">
                            <p class="mb-0 text-right text-dark">Receitas Vencidas</p>
                            <div class="fluid-container">
                                <h3 class="font-weight-medium text-right mb-0 text-dark">
                                    {{ $clientesComReceitasVencidas ?? 0 }}</h3>
                            </div>
                        </div>
                    </div>
                    <p class="text-muted mt-3 mb-0">
                        <i class="mdi mdi-account-clock-outline me-1" aria-hidden="true"></i> Total de clientes com
                        receitas vencidas
                    </p>
                </div>
            </div>
        </div>

        {{-- Card: Pagamentos em atraso --}}
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
            <div class="card card-statistics">
                <div class="card-body">
                    <div class="clearfix">
                        <div class="float-start">
                            <i class="mdi mdi-cash-remove text-success icon-lg"></i>
                        </div>
                        <div class="float-end">
                            <p class="mb-0 text-right text-dark">Pagamento em Atraso</p>
                            <div class="fluid-container">
                                <h3 class="font-weight-medium text-right mb-0 text-dark">
                                    {{ $clientesComPagamentoAtraso ?? 0 }}</h3>
                            </div>
                        </div>
                    </div>
                    <p class="text-muted mt-3 mb-0">
                        <i class="mdi mdi-hospital-marker me-1" aria-hidden="true"></i> Total de clientes com pagamento em
                        atraso
                    </p>
                </div>
            </div>
        </div>

        {{-- Card: Vendas hoje --}}
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
            <div class="card card-statistics">
                <div class="card-body">
                    <div class="clearfix">
                        <div class="float-start">
                            <i class="mdi mdi-cash-multiple text-info icon-lg"></i>
                        </div>
                        <div class="float-end">
                            <p class="mb-0 text-right text-dark">Vendas hoje</p>
                            <div class="fluid-container">
                                <h3 class="font-weight-medium text-right mb-0 text-dark">0</h3>
                            </div>
                        </div>
                    </div>
                    <p class="text-muted mt-3 mb-0">
                        <i class="mdi mdi-cash-register me-1" aria-hidden="true"></i> Valor total das vendas do dia
                    </p>
                </div>
            </div>
        </div>

        {{-- Card: Evolução de Vendas --}}
        @php
            $vendasPorMes = $vendasPorMes ?? [
                'labels' => [],
                'valores' => [],
                'total_atual' => 0,
                'mes_anterior' => 0,
                'mes_atual' => 0,
            ];
        @endphp
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Evolução de Vendas</h4>
                    <div class="d-lg-flex justify-content-between align-items-center">
                        <h1 class="text-dark mb-0">R$ {{ number_format($vendasPorMes['total_atual'] ?? 0, 2, ',', '.') }}
                        </h1>
                        @php
                            $mesAtual = $vendasPorMes['mes_atual'] ?? 0;
                            $mesAnterior = $vendasPorMes['mes_anterior'] ?? 0;
                            $variacao = $mesAnterior > 0 ? (($mesAtual - $mesAnterior) / $mesAnterior) * 100 : 0;
                            $diferenca = $mesAtual - $mesAnterior;
                        @endphp
                        <p class="mb-0 font-weight-medium {{ $variacao >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $variacao >= 0 ? '+' : '' }}{{ number_format($variacao, 2, ',', '.') }}% (R$
                            {{ number_format($diferenca, 2, ',', '.') }})
                        </p>
                    </div>
                    <p class="mb-5 pb-1">Últimos 12 meses</p>
                    <div class="chart-container" style="position: relative; height: 280px; width: 100%;">
                        <canvas id="evolucao-vendas-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
