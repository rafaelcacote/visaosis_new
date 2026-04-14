@extends('layouts.app')

@section('title', 'Dashboard de Relatórios - VisaoSis')
@section('page-title', 'Dashboard de Relatórios')

@push('plugin-css')
    <link rel="stylesheet" href="{{ asset('assets/css/users.css') }}">
@endpush

@section('content')
    <div class="row">
        <!-- Filtros -->
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-filter text-primary me-2"></i>
                        Filtros de Relatório
                    </h5>
                </div>
                <div class="card-body">
                    <form id="reportForm" method="GET" class="row g-3 align-items-end">
                        <div class="col-12 col-sm-6 col-md-3">
                            <label for="start_date" class="form-label">Data Início</label>
                            <input type="date" class="form-control form-control-sm" id="start_date" name="start_date"
                                value="{{ now()->format('Y-m-d') }}">
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <label for="end_date" class="form-label">Data Fim</label>
                            <input type="date" class="form-control form-control-sm" id="end_date" name="end_date"
                                value="{{ now()->format('Y-m-d') }}">
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <label for="professional_id" class="form-label">Profissional</label>
                            <select class="form-select form-select-sm" id="professional_id" name="professional_id">
                                <option value="">Todos os Profissionais</option>
                                @foreach ($profissionais as $profissional)
                                    <option value="{{ $profissional->id }}">
                                        {{ $profissional->nome }} -
                                        {{ $profissional->especialidade->descricao ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>


                        <div class="col-12 col-sm-6 col-md-2">

                            <button type="button" class="btn btn-outline-primary" onclick="clearFilters()">
                                <i class="mdi mdi-filter-off me-1"></i>
                                Limpar
                            </button>

                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Cards de Relatórios -->
        <div class="col-12">
            <div class="row">
                <!-- Relatório de Atendimentos -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 hover-card">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="mdi mdi-hospital-building text-primary" style="font-size: 3rem;"></i>
                            </div>
                            <h5 class="card-title">Relatório de Atendimentos</h5>
                            <p class="card-text text-muted">
                                Visualize estatísticas detalhadas de atendimentos, profissionais, tempos médios e desempenho
                                geral.
                            </p>
                            <div class="mt-auto">
                                <button class="btn btn-primary" onclick="generateReport('attendance')">
                                    <i class="mdi mdi-chart-line me-2"></i>
                                    Gerar Relatório
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Relatório Financeiro -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 hover-card">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="mdi mdi-cash-multiple text-success" style="font-size: 3rem;"></i>
                            </div>
                            <h5 class="card-title">Relatório Financeiro</h5>
                            <p class="card-text text-muted">
                                Acompanhe receitas, despesas, fluxo de caixa e análise financeira detalhada do período.
                            </p>
                            <div class="mt-auto">
                                <button class="btn btn-success" onclick="generateReport('financial')" disabled>
                                    <i class="mdi mdi-chart-pie me-2"></i>
                                    Em Breve
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Relatório de Pacientes -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 hover-card">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="mdi mdi-account-group text-info" style="font-size: 3rem;"></i>
                            </div>
                            <h5 class="card-title">Relatório de Pacientes</h5>
                            <p class="card-text text-muted">
                                Análise demográfica, histórico de consultas e estatísticas de pacientes cadastrados.
                            </p>
                            <div class="mt-auto">
                                <button class="btn btn-info" onclick="generateReport('patients')" disabled>
                                    <i class="mdi mdi-account-search me-2"></i>
                                    Em Breve
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Relatório de Produtividade -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 hover-card">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="mdi mdi-trending-up text-warning" style="font-size: 3rem;"></i>
                            </div>
                            <h5 class="card-title">Relatório de Produtividade</h5>
                            <p class="card-text text-muted">
                                Métricas de desempenho por profissional, metas alcançadas e indicadores de produtividade.
                            </p>
                            <div class="mt-auto">
                                <button class="btn btn-warning" onclick="generateReport('productivity')" disabled>
                                    <i class="mdi mdi-trophy me-2"></i>
                                    Em Breve
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Relatório de Estoque -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 hover-card">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="mdi mdi-package-variant text-secondary" style="font-size: 3rem;"></i>
                            </div>
                            <h5 class="card-title">Relatório de Estoque</h5>
                            <p class="card-text text-muted">
                                Controle de inventário, movimentações, produtos em falta e análise de rotatividade.
                            </p>
                            <div class="mt-auto">
                                <button class="btn btn-secondary" onclick="generateReport('inventory')" disabled>
                                    <i class="mdi mdi-clipboard-list me-2"></i>
                                    Em Breve
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Relatório Personalizado -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 hover-card">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="mdi mdi-cog text-dark" style="font-size: 3rem;"></i>
                            </div>
                            <h5 class="card-title">Relatório Personalizado</h5>
                            <p class="card-text text-muted">
                                Crie relatórios customizados com métricas específicas e visualizações personalizadas.
                            </p>
                            <div class="mt-auto">
                                <button class="btn btn-dark" onclick="generateReport('custom')" disabled>
                                    <i class="mdi mdi-tools me-2"></i>
                                    Em Breve
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
@endsection

@push('scripts')
    <script>
        function generateReport(type) {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            const professionalId = document.getElementById('professional_id').value;

            if (!startDate || !endDate) {
                alert('Por favor, selecione as datas de início e fim.');
                return;
            }

            if (new Date(startDate) > new Date(endDate)) {
                alert('A data de início não pode ser maior que a data de fim.');
                return;
            }

            const params = new URLSearchParams({
                start_date: startDate,
                end_date: endDate
            });

            if (professionalId) {
                params.append('professional_id', professionalId);
            }

            let url = '';
            switch (type) {
                case 'attendance':
                    url = `{{ route('reports.attendance') }}?${params.toString()}`;
                    break;
                case 'financial':
                    url = `{{ route('reports.financial') }}?${params.toString()}`;
                    break;
                default:
                    alert('Relatório ainda não disponível. Em breve!');
                    return;
            }

            window.location.href = url;
        }

        function clearFilters() {
            document.getElementById('start_date').value = '{{ now()->format('Y-m-d') }}';
            document.getElementById('end_date').value = '{{ now()->format('Y-m-d') }}';
            document.getElementById('professional_id').value = '';
        }

        // Validação de datas em tempo real
        document.getElementById('start_date').addEventListener('change', function() {
            const startDate = new Date(this.value);
            const endDate = new Date(document.getElementById('end_date').value);

            if (startDate > endDate) {
                document.getElementById('end_date').value = this.value;
            }
        });
    </script>
@endpush

@push('styles')
    <style>
        .hover-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .hover-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .card-body {
            display: flex;
            flex-direction: column;
        }

        .mt-auto {
            margin-top: auto;
        }

        .border-end:last-child {
            border-right: none !important;
        }
    </style>
@endpush
