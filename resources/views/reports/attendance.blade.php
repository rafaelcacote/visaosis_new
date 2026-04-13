@extends('layouts.app')

@section('title', 'Relatório de Atendimentos - VisaoSis')
@section('page-title', 'Relatório do Dia')

@push('plugin-css')
    <link rel="stylesheet" href="{{ asset('assets/css/users.css') }}">
@endpush

@section('page-actions')
    <div class="d-flex gap-2">
        <a href="{{ route('attendance.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>
            Voltar à Fila
        </a>

    </div>
@endsection

@section('content')
    <div class="row">
        <!-- Date Navigation -->
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center">
                                <i class="mdi mdi-calendar text-primary me-2" style="font-size: 1.5rem;"></i>
                                <div>
                                    <h5 class="mb-0">{{ \Carbon\Carbon::parse($selectedDate)->format('d/m/Y') }}</h5>
                                    <small
                                        class="text-muted">{{ \Carbon\Carbon::parse($selectedDate)->locale('pt-BR')->isoFormat('dddd') }}</small>
                                </div>
                            </div>
                            @if (!\Carbon\Carbon::parse($selectedDate)->isToday())
                                <span class="tag" style="background-color: #fff8e6; color: #d97706;">
                                    <i class="mdi mdi-history"></i>
                                    Data Anterior
                                </span>
                            @else
                                <span class="tag tag-status tag-status-ativo">
                                    <i class="mdi mdi-clock"></i>
                                    Hoje
                                </span>
                            @endif
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm" onclick="changeDate('previous')"
                                title="Dia Anterior">
                                <i class="mdi mdi-chevron-left"></i>
                                Anterior
                            </button>
                            <button class="btn btn-outline-primary btn-sm" onclick="goToToday()" title="Ir para Hoje">
                                <i class="mdi mdi-calendar-today"></i>
                                Hoje
                            </button>
                            <button class="btn btn-outline-primary btn-sm" onclick="changeDate('next')" title="Próximo Dia"
                                @if (\Carbon\Carbon::parse($selectedDate)->isToday()) disabled @endif>
                                Próximo
                                <i class="mdi mdi-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overview Cards -->
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center border-end">
                            <h2 class="mb-1">{{ $stats['total'] }}</h2>
                            <p class="text-muted mb-0">Total de Atendimentos</p>
                        </div>
                        <div class="col-md-3 text-center border-end">
                            <h2 class="mb-1">{{ $stats['cancelled'] }}</h2>
                            <p class="text-muted mb-0">Cancelados</p>
                        </div>
                        <div class="col-md-3 text-center border-end">
                            <h2 class="mb-1">{{ $stats['returns'] }}</h2>
                            <p class="text-muted mb-0">Retornos</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <h2 class="mb-1">{{ $stats['referrals'] }}</h2>
                            <p class="text-muted mb-0">Encaminhamentos</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Professional Stats -->
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-account-group text-primary me-2"></i>
                        Atendimentos por Profissional
                    </h5>
                    <div class="d-flex gap-2 align-items-center">
                        <span class="tag"
                            style="background-color: #e0f0ff; color: #1d7dd6;">{{ count($professionalStats) }}
                            profissionais</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if (empty($professionalStats))
                        <div class="text-center py-5">
                            <i class="mdi mdi-account-off text-muted" style="font-size: 3rem;"></i>
                            <h5 class="mt-3 text-muted">Nenhum atendimento registrado</h5>
                            <p class="text-muted mb-3">
                                Os atendimentos realizados hoje aparecerão aqui conforme forem registrados.
                            </p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Profissional</th>

                                        <th>Atendidos</th>
                                        <th>Retornos</th>
                                        <th>Encaminhados</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($professionalStats as $prof)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle bg-primary text-white me-2">
                                                        {{ substr($prof['name'], 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1">{{ $prof['name'] }}</h6>
                                                        <small class="text-muted">{{ $prof['specialty'] }}</small>
                                                    </div>
                                                </div>
                                            </td>

                                            <td>
                                                <div>
                                                    <h6 class="mb-1">{{ $prof['attended'] }}</h6>
                                                    @if ($prof['attended'] > 0)
                                                        <small class="text-success">
                                                            <i class="mdi mdi-trending-up"></i>
                                                            Realizados
                                                        </small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <h6 class="mb-1">{{ $prof['returns'] }}</h6>
                                                    @if ($prof['returns'] > 0)
                                                        <small class="text-info">
                                                            <i class="mdi mdi-refresh"></i>
                                                            Retornos
                                                        </small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <h6 class="mb-1">{{ $prof['referrals'] }}</h6>
                                                    @if ($prof['referrals'] > 0)
                                                        <small class="text-warning">
                                                            <i class="mdi mdi-account-switch"></i>
                                                            Encaminhados
                                                        </small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <h6 class="mb-1"><strong>{{ $prof['total'] }}</strong></h6>

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
        </div>

        <!-- Additional Stats -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informações Adicionais</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Tempo Médio de Espera
                            <span class="badge bg-primary rounded-pill">{{ $stats['avg_wait_time'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Tempo Médio de Atendimento
                            <span class="badge bg-success rounded-pill">{{ $stats['avg_service_time'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Pacientes Prioritários
                            <span class="badge bg-danger rounded-pill">{{ $stats['priority_patients'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Primeira Consulta
                            <span class="badge bg-info rounded-pill">{{ $stats['first_time'] }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Actions -->
            <div class="card mt-3">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" onclick="printReport()">
                            <i class="bi bi-printer me-2"></i>Imprimir Relatório
                        </button>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function changeDate(direction) {
            const currentDate = '{{ $selectedDate }}';
            const date = new Date(currentDate);

            if (direction === 'previous') {
                date.setDate(date.getDate() - 1);
            } else if (direction === 'next') {
                date.setDate(date.getDate() + 1);
            }

            const formattedDate = date.toISOString().split('T')[0];
            window.location.href = `{{ route('reports.attendance') }}?date=${formattedDate}`;
        }

        function goToToday() {
            window.location.href = '{{ route('reports.attendance') }}';
        }

        function printReport() {
            window.print();
        }

        function exportReport() {
            const selectedDate = '{{ $selectedDate }}';
            window.location.href = `{{ route('reports.attendance.export') }}?date=${selectedDate}`;
        }
    </script>
@endpush

@push('styles')
    <style>
        @media print {

            .btn,
            .navbar,
            .sidebar {
                display: none !important;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
            }
        }

        .avatar-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
    </style>
@endpush
