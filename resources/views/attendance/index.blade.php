@extends('layouts.app')

@section('title', 'Atendimento')

@push('plugin-css')
<link rel="stylesheet" href="{{ asset('assets/css/users.css') }}">
@endpush

@section('content')
<div class="d-xl-flex justify-content-between align-items-start mb-4">
    <div>
        <h2 class="text-dark font-weight-bold mb-2">
            <i class="mdi mdi-account-group me-2"></i>
            Atendimento Recepção
        </h2>
        <p class="text-muted mb-0">Gerencie a fila de atendimento do dia</p>
    </div>
    <a href="{{ route('attendance.triage') }}" class="btn btn-primary btn-icon-text">
        <i class="mdi mdi-plus me-2"></i>
        Nova Triagem
    </a>
</div>

<!-- Cards de Estatísticas -->
<div class="row mb-4">
    <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 grid-margin stretch-card">
        <div class="card card-statistics">
            <div class="card-body">
                <div class="clearfix">
                    <div class="float-start">
                        <i class="mdi mdi-calendar-multiple text-primary icon-lg"></i>
                    </div>
                    <div class="float-end">
                        <p class="mb-0 text-right text-dark">Total Hoje</p>
                        <div class="fluid-container">
                            <h3 class="font-weight-medium text-right mb-0 text-dark">{{ $stats['total_today'] }}</h3>
                        </div>
                    </div>
                </div>
                <p class="text-muted mt-3 mb-0">
                    <i class="mdi mdi-calendar me-1" aria-hidden="true"></i> Total do dia
                </p>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 grid-margin stretch-card">
        <div class="card card-statistics">
            <div class="card-body">
                <div class="clearfix">
                    <div class="float-start">
                        <i class="mdi mdi-clock-outline text-warning icon-lg"></i>
                    </div>
                    <div class="float-end">
                        <p class="mb-0 text-right text-dark">Aguardando</p>
                        <div class="fluid-container">
                            <h3 class="font-weight-medium text-right mb-0 text-dark">{{ $stats['waiting'] }}</h3>
                        </div>
                    </div>
                </div>
                <p class="text-muted mt-3 mb-0">
                    <i class="mdi mdi-account-clock me-1" aria-hidden="true"></i> Na fila de espera
                </p>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 grid-margin stretch-card">
        <div class="card card-statistics">
            <div class="card-body">
                <div class="clearfix">
                    <div class="float-start">
                        <i class="mdi mdi-refresh text-info icon-lg"></i>
                    </div>
                    <div class="float-end">
                        <p class="mb-0 text-right text-dark">Retornos</p>
                        <div class="fluid-container">
                            <h3 class="font-weight-medium text-right mb-0 text-dark">{{ $stats['in_service'] }}</h3>
                        </div>
                    </div>
                </div>
                <p class="text-muted mt-3 mb-0">
                    <i class="mdi mdi-arrow-left-right me-1" aria-hidden="true"></i> Consultas de retorno
                </p>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 grid-margin stretch-card">
        <div class="card card-statistics">
            <div class="card-body">
                <div class="clearfix">
                    <div class="float-start">
                        <i class="mdi mdi-check-circle text-success icon-lg"></i>
                    </div>
                    <div class="float-end">
                        <p class="mb-0 text-right text-dark">Atendidos</p>
                        <div class="fluid-container">
                            <h3 class="font-weight-medium text-right mb-0 text-dark">{{ $stats['completed'] }}</h3>
                        </div>
                    </div>
                </div>
                <p class="text-muted mt-3 mb-0">
                    <i class="mdi mdi-check-all me-1" aria-hidden="true"></i> Finalizados
                </p>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 grid-margin stretch-card">
        <div class="card card-statistics">
            <div class="card-body">
                <div class="clearfix">
                    <div class="float-start">
                        <i class="mdi mdi-arrow-right text-warning icon-lg"></i>
                    </div>
                    <div class="float-end">
                        <p class="mb-0 text-right text-dark">Encaminhados</p>
                        <div class="fluid-container">
                            <h3 class="font-weight-medium text-right mb-0 text-dark">{{ $stats['cancelled'] }}</h3>
                        </div>
                    </div>
                </div>
                <p class="text-muted mt-3 mb-0">
                    <i class="mdi mdi-arrow-right-bold me-1" aria-hidden="true"></i> Encaminhados hoje
                </p>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 grid-margin stretch-card">
        <div class="card card-statistics">
            <div class="card-body">
                <div class="clearfix">
                    <div class="float-start">
                        <i class="mdi mdi-clock-time-four text-secondary icon-lg"></i>
                    </div>
                    <div class="float-end">
                        <p class="mb-0 text-right text-dark">Hora Atual</p>
                        <div class="fluid-container">
                            <h3 class="font-weight-medium text-right mb-0 text-dark">{{ now()->format('H:i') }}</h3>
                        </div>
                    </div>
                </div>
                <p class="text-muted mt-3 mb-0">
                    <i class="mdi mdi-calendar-clock me-1" aria-hidden="true"></i> {{ now()->format('d/m/Y') }}
                </p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Fila de Atendimento -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="mdi mdi-format-list-bulleted text-primary me-2"></i>
                    Fila de Atendimento
                </h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-primary" onclick="refreshQueue()">
                        <i class="mdi mdi-refresh"></i>
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Filtros
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#" onclick="filterByStatus('all')">Todos</a></li>
                            <li><a class="dropdown-item" href="#" onclick="filterByStatus('waiting')">Aguardando</a></li>
                            <li><a class="dropdown-item" href="#" onclick="filterByStatus('in_service')">Em Atendimento</a></li>
                            <li><a class="dropdown-item" href="#" onclick="filterByStatus('completed')">Atendidos</a></li>
                            <li><a class="dropdown-item" href="#" onclick="filterByStatus('retorno')">Retorno</a></li>
                            <li><a class="dropdown-item" href="#" onclick="filterByStatus('encaminhado')">Encaminhado</a></li>
                            <li><a class="dropdown-item" href="#" onclick="filterByStatus('cancelled')">Cancelado</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                @if(empty($patients) || count($patients) == 0)
                    <div class="text-center py-5">
                        <i class="mdi mdi-account-off text-muted" style="font-size: 3rem;"></i>
                        <h5 class="mt-3 text-muted">Nenhum paciente na fila</h5>
                        <p class="text-muted">Não há pacientes aguardando atendimento hoje.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Paciente</th>
                                    <th>Chegada</th>
                                    <th>Tipo</th>
                                    <th>Status</th>
                                    <th width="120">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="patientsQueue">
                                @foreach ($patients as $patient)
                                    <tr data-status="{{ $patient['status'] }}" data-priority="{{ $patient['priority'] ?? 'normal' }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3 {{ ($patient['priority'] ?? 'normal') == 'urgent' ? 'bg-danger' : '' }}" style="width: 40px; height: 40px; font-size: 16px; font-weight: 600;">
                                                    {{ strtoupper(mb_substr($patient['name'] ?? 'N', 0, 1, 'UTF-8')) }}
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $patient['name'] ?? 'N/A' }}</h6>
                                                    @if (($patient['priority'] ?? 'normal') == 'urgent')
                                                        <small class="text-danger">
                                                            <i class="mdi mdi-alert me-1"></i>
                                                            Prioridade
                                                        </small>
                                                    @endif
                                                    @if (isset($patient['professional']))
                                                        <small class="text-muted d-block">{{ $patient['professional'] }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-bold">
                                                {{ isset($patient['arrival_time']) ? \Carbon\Carbon::parse($patient['arrival_time'])->format('H:i') : '-' }}
                                            </span>
                                            @if (isset($patient['arrival_time']))
                                                <br>
                                                <small class="text-muted">
                                                    @php
                                                        $arrival = \Carbon\Carbon::parse($patient['arrival_time']);
                                                        $hours = $arrival->diffInHours(now());
                                                        $minutes = $arrival->copy()->addHours(-$hours)->diffInMinutes(now());
                                                    @endphp
                                                    <i class="mdi mdi-clock-outline me-1"></i>
                                                    {{ $hours > 0 ? $hours . 'h ' : '' }}{{ $minutes }}min
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            @switch($patient['type'] ?? 'consulta')
                                                @case('consulta')
                                                    <span class="tag" style="background-color: #e0f0ff; color: #1d7dd6;">
                                                        <i class="mdi mdi-eye"></i>
                                                        Consulta
                                                    </span>
                                                    @break
                                                @case('retorno')
                                                    <span class="tag tag-status tag-status-ativo">
                                                        <i class="mdi mdi-refresh"></i>
                                                        Retorno
                                                    </span>
                                                    @break
                                                @case('emergencia')
                                                    <span class="tag" style="background-color: #ffe6e6; color: #dc2626;">
                                                        <i class="mdi mdi-alert"></i>
                                                        Emergência
                                                    </span>
                                                    @break
                                                @case('conferencia')
                                                    <span class="tag" style="background-color: #f3f4f6; color: #6b7280;">
                                                        <i class="mdi mdi-clipboard-check"></i>
                                                        Conferência
                                                    </span>
                                                    @break
                                                @default
                                                    <span class="tag" style="background-color: #e0f0ff; color: #1d7dd6;">
                                                        <i class="mdi mdi-eye"></i>
                                                        Consulta
                                                    </span>
                                            @endswitch
                                        </td>
                                        <td>
                                            @switch($patient['status'] ?? 'waiting')
                                                @case('waiting')
                                                    <span class="tag" style="background-color: #fff8e6; color: #d97706;">
                                                        <i class="mdi mdi-clock"></i>
                                                        Aguardando
                                                    </span>
                                                    @break
                                                @case('in_service')
                                                    <span class="tag" style="background-color: #e0f0ff; color: #1d7dd6;">
                                                        <i class="mdi mdi-account-check"></i>
                                                        Em Atendimento
                                                    </span>
                                                    @break
                                                @case('completed')
                                                    <span class="tag tag-status tag-status-ativo">
                                                        <i class="mdi mdi-check-circle"></i>
                                                        Atendido
                                                    </span>
                                                    @break
                                                @case('cancelled')
                                                    <span class="tag tag-status tag-status-inativo">
                                                        <i class="mdi mdi-close-circle"></i>
                                                        Cancelado
                                                    </span>
                                                    @break
                                                @default
                                                    <span class="tag" style="background-color: #fff8e6; color: #d97706;">
                                                        <i class="mdi mdi-clock"></i>
                                                        Aguardando
                                                    </span>
                                            @endswitch
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                @if (($patient['status'] ?? 'waiting') == 'waiting')
                                                    <button class="btn btn-sm btn-primary" onclick="startService({{ $patient['id'] }})" title="Iniciar Atendimento">
                                                        <i class="mdi mdi-play"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" onclick="showCancelModal({{ $patient['id'] }}, '{{ addslashes($patient['name'] ?? 'Paciente') }}')" title="Cancelar">
                                                        <i class="mdi mdi-close"></i>
                                                    </button>
                                                @elseif(($patient['status'] ?? '') == 'in_service')
                                                    <button class="btn btn-sm btn-success" onclick="completeService({{ $patient['id'] }})" title="Finalizar Atendimento">
                                                        <i class="mdi mdi-check"></i>
                                                    </button>
                                                @endif
                                                <button class="btn btn-sm btn-outline-secondary" onclick="viewPatient({{ $patient['id'] }})" title="Visualizar">
                                                    <i class="mdi mdi-eye"></i>
                                                </button>
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

    <!-- Profissionais -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="mdi mdi-account-group text-success me-2"></i>
                    Profissionais
                </h5>
            </div>
            <div class="card-body">
                @forelse ($professionals ?? [] as $professional)
                    <div class="d-flex align-items-center mb-3 p-3 border rounded">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3 {{ ($professional['status'] ?? 'available') == 'available' ? 'bg-success' : 'bg-warning' }}" style="width: 40px; height: 40px; font-size: 16px; font-weight: 600;">
                            {{ strtoupper(mb_substr($professional['name'] ?? 'N', 0, 1, 'UTF-8')) }}
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $professional['name'] ?? 'N/A' }}</h6>
                            <small class="text-muted">{{ $professional['specialty'] ?? 'Especialidade não informada' }}</small>
                            @if (isset($professional['current_patient']))
                                <div class="mt-1">
                                    <small class="text-primary">
                                        <i class="mdi mdi-account me-1"></i>
                                        {{ $professional['current_patient'] }}
                                    </small>
                                </div>
                            @endif
                        </div>
                        <div class="text-end">
                            @if (($professional['status'] ?? 'available') == 'available')
                                <span class="tag tag-status tag-status-ativo">Disponível</span>
                            @else
                                <span class="tag" style="background-color: #fff8e6; color: #d97706;">Ocupado</span>
                            @endif
                            <div class="mt-1">
                                <small class="text-muted">{{ $professional['patients_today'] ?? 0 }} pacientes hoje</small>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted text-center">Nenhum profissional cadastrado</p>
                @endforelse
            </div>
        </div>

        <!-- Ações Rápidas -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="mdi mdi-lightning-bolt text-warning me-2"></i>
                    Ações Rápidas
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('attendance.triage') }}" class="btn btn-primary">
                        <i class="mdi mdi-plus-circle me-2"></i>
                        Nova Triagem
                    </a>
                    <button class="btn btn-outline-info" onclick="callNextPatient()">
                        <i class="mdi mdi-bullhorn me-2"></i>
                        Chamar Próximo
                    </button>
                    <button class="btn btn-outline-warning" onclick="showEmergencyAlert()">
                        <i class="mdi mdi-alert me-2"></i>
                        Emergência
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Cancelamento -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="cancelModalLabel">
                    <i class="mdi mdi-close-circle me-2"></i>
                    Cancelar Atendimento
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="mdi mdi-alert me-2"></i>
                    Tem certeza que deseja cancelar o atendimento de <strong id="patientName"></strong>?
                </div>
                <div class="mb-3">
                    <label for="cancelReason" class="form-label">Motivo do Cancelamento</label>
                    <textarea class="form-control" id="cancelReason" rows="3" placeholder="Descreva o motivo do cancelamento..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-danger" onclick="confirmCancel()">
                    <i class="mdi mdi-check-circle me-2"></i>
                    Confirmar Cancelamento
                </button>
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

    tr[data-priority="urgent"] {
        background-color: rgba(220, 53, 69, 0.05);
        border-left: 4px solid #dc3545;
    }

    .card-body .border {
        transition: all 0.2s ease;
    }

    .card-body .border:hover {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush

@push('scripts')
<script>
    function refreshQueue() {
        location.reload();
    }

    function filterByStatus(status) {
        const rows = document.querySelectorAll('#patientsQueue tr');
        rows.forEach(row => {
            if (status === 'all' || row.dataset.status === status) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function startService(patientId) {
        if (confirm('Iniciar atendimento deste paciente?')) {
            fetch(`{{ url('/attendance/status') }}/${patientId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    status: 'in_service'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Erro ao iniciar atendimento.');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao iniciar atendimento.');
            });
        }
    }

    function completeService(patientId) {
        if (confirm('Finalizar atendimento deste paciente?')) {
            fetch(`{{ url('/attendance/status') }}/${patientId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    status: 'completed'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Erro ao finalizar atendimento.');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao finalizar atendimento.');
            });
        }
    }

    function viewPatient(patientId) {
        window.location.href = `/pessoas/${patientId}`;
    }

    function callNextPatient() {
        alert('Próximo paciente chamado!');
    }

    function showEmergencyAlert() {
        alert('Protocolo de emergência ativado!');
    }

    // Auto-refresh a cada 30 segundos
    setInterval(function() {
        console.log('Auto-refresh...');
    }, 30000);

    let currentPatientId = null;
    const cancelModal = new bootstrap.Modal(document.getElementById('cancelModal'));

    function showCancelModal(patientId, patientName) {
        currentPatientId = patientId;
        document.getElementById('patientName').textContent = patientName;
        document.getElementById('cancelReason').value = '';
        cancelModal.show();
    }

    function confirmCancel() {
        const reason = document.getElementById('cancelReason').value.trim();
        if (!reason) {
            alert('Por favor, informe o motivo do cancelamento.');
            return;
        }
        fetch(`{{ url('/attendance/status') }}/${currentPatientId}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                status: 'cancelled',
                reason: reason
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                cancelModal.hide();
                location.reload();
            } else {
                alert('Erro ao cancelar atendimento.');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao cancelar atendimento.');
        });
    }
</script>
@endpush
