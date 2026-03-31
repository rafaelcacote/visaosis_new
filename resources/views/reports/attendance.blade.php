@extends('layouts.app')

@section('title', 'Relatório de Atendimentos - VisaoSis')
@section('page-title', 'Relatório do Dia')

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
        <!-- Date Display -->
        <div class="col-12 mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4>{{ date('d/m/Y') }}</h4>
                <div class="btn-group">
                    <button class="btn btn-outline-secondary btn-sm" onclick="changeDate('previous')">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" onclick="changeDate('next')">
                        <i class="bi bi-chevron-right"></i>
                    </button>
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
                <div class="card-header">
                    <h5 class="card-title mb-0">Atendimentos por Profissional</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Profissional</th>
                                    <th class="text-center">Atendidos</th>
                                    <th class="text-center">Retornos</th>
                                    <th class="text-center">Encaminhados</th>
                                    <th class="text-center">Total</th>
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
                                                    <h6 class="mb-0">{{ $prof['name'] }}</h6>
                                                    <small class="text-muted">{{ $prof['specialty'] }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">{{ $prof['attended'] }}</td>
                                        <td class="text-center">{{ $prof['returns'] }}</td>
                                        <td class="text-center">{{ $prof['referrals'] }}</td>
                                        <td class="text-center"><strong>{{ $prof['total'] }}</strong></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
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
                        <button class="btn btn-outline-secondary" onclick="exportReport()">
                            <i class="bi bi-download me-2"></i>Exportar Excel
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
            const currentDate = new URLSearchParams(window.location.search).get('date') || '{{ date('Y-m-d') }}';
            const date = new Date(currentDate);

            if (direction === 'previous') {
                date.setDate(date.getDate() - 1);
            } else {
                date.setDate(date.getDate() + 1);
            }

            const formattedDate = date.toISOString().split('T')[0];
            window.location.href = `{{ route('reports.attendance') }}?date=${formattedDate}`;
        }

        function printReport() {
            window.print();
        }

        function exportReport() {
            window.location.href = '{{ route('reports.attendance.export') }}';
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
