@extends('layouts.app')

@section('title', 'Painel do Profissional')

@push('plugin-css')
<link rel="stylesheet" href="{{ asset('assets/css/users.css') }}">
@endpush

@section('content')
<div class="d-xl-flex justify-content-between align-items-start mb-4">
    <div>
        <h2 class="text-dark font-weight-bold mb-2">
            <i class="mdi mdi-account-tie me-2"></i>
            Painel do Profissional
        </h2>
        <p class="text-muted mb-0">Gerencie seus atendimentos do dia</p>
    </div>
    <button class="btn btn-outline-info" onclick="refreshQueue()">
        <i class="mdi mdi-refresh me-2"></i>
        Atualizar Fila
    </button>
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
                    <i class="mdi mdi-calendar me-1" aria-hidden="true"></i> Consultas do dia
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
                        <i class="mdi mdi-account-check text-info icon-lg"></i>
                    </div>
                    <div class="float-end">
                        <p class="mb-0 text-right text-dark">Em Atendimento</p>
                        <div class="fluid-container">
                            <h3 class="font-weight-medium text-right mb-0 text-dark">{{ $stats['in_service'] }}</h3>
                        </div>
                    </div>
                </div>
                <p class="text-muted mt-3 mb-0">
                    <i class="mdi mdi-stethoscope me-1" aria-hidden="true"></i> Sendo atendidos
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
                        <i class="mdi mdi-close-circle text-danger icon-lg"></i>
                    </div>
                    <div class="float-end">
                        <p class="mb-0 text-right text-dark">Cancelados</p>
                        <div class="fluid-container">
                            <h3 class="font-weight-medium text-right mb-0 text-dark">{{ $stats['cancelled'] }}</h3>
                        </div>
                    </div>
                </div>
                <p class="text-muted mt-3 mb-0">
                    <i class="mdi mdi-cancel me-1" aria-hidden="true"></i> Cancelados hoje
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
                    <i class="mdi mdi-account-group text-primary me-2"></i>
                    Fila de Atendimento
                </h5>
                <div class="d-flex gap-2 align-items-center">
                    <select class="form-select form-select-sm" id="statusFilter" onchange="filterByStatus()" style="width: auto;">
                        <option value="">Todos</option>
                        <option value="Aguardando">Aguardando</option>
                        <option value="Em Atendimento">Em Atendimento</option>
                        <option value="Atendido">Atendido</option>
                        <option value="Cancelado">Cancelado</option>
                    </select>
                    <span class="tag" style="background-color: #e0f0ff; color: #1d7dd6;">{{ $patients->count() }} pacientes</span>
                </div>
            </div>
            <div class="card-body p-0">
                @if($patients->isEmpty())
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
                                    <th>Motivo</th>
                                    <th>Status</th>
                                    <th width="120">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="patientsTable">
                                @foreach ($patients as $patient)
                                    <tr data-status="{{ $patient->status }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-size: 16px; font-weight: 600;">
                                                    {{ substr($patient->paciente->nome ?? 'N', 0, 1) }}
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $patient->paciente->nome ?? 'N/A' }}</h6>
                                                    <small class="text-muted">
                                                        @if($patient->paciente)
                                                            {{ $patient->paciente->idade ?? 'N/A' }} anos
                                                            @if($patient->paciente->telefone_formatado)
                                                                • {{ $patient->paciente->telefone_formatado }}
                                                            @endif
                                                        @endif
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-bold">
                                                {{ $patient->agendado_em ? \Carbon\Carbon::parse($patient->agendado_em)->format('d/m/Y H:i') : '-' }}
                                            </span>
                                            @if($patient->chegada_em || $patient->agendado_em)
                                                @php
                                                    $horaReferencia = $patient->chegada_em ? \Carbon\Carbon::parse($patient->chegada_em) : \Carbon\Carbon::parse($patient->agendado_em);
                                                    $tempoEspera = $horaReferencia->diffForHumans(\Carbon\Carbon::now(), true);
                                                    $tempoEsperaMinutos = $horaReferencia->diffInMinutes(\Carbon\Carbon::now());
                                                @endphp
                                                <br>
                                                <small class="text-muted">
                                                    <i class="mdi mdi-clock-outline me-1"></i>
                                                    Esperando: 
                                                    @if($tempoEsperaMinutos < 60)
                                                        {{ $tempoEsperaMinutos }} min
                                                    @else
                                                        {{ floor($tempoEsperaMinutos / 60) }}h {{ $tempoEsperaMinutos % 60 }}min
                                                    @endif
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            @switch($patient->tipo)
                                                @case(\App\Models\Consulta::TIPO_CONSULTA)
                                                    <span class="tag" style="background-color: #e0f0ff; color: #1d7dd6;">
                                                        <i class="mdi mdi-eye"></i>
                                                        Consulta
                                                    </span>
                                                    @break
                                                @case(\App\Models\Consulta::TIPO_RETORNO)
                                                    <span class="tag tag-status tag-status-ativo">
                                                        <i class="mdi mdi-refresh"></i>
                                                        Retorno
                                                    </span>
                                                    @break
                                                @case(\App\Models\Consulta::TIPO_CONFERENCIA)
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
                                            @switch($patient->status)
                                                @case(\App\Models\Consulta::STATUS_AGUARDANDO)
                                                    <span class="tag" style="background-color: #fff8e6; color: #d97706;">
                                                        <i class="mdi mdi-clock"></i>
                                                        Aguardando
                                                    </span>
                                                    @break
                                                @case(\App\Models\Consulta::STATUS_EM_ATENDIMENTO)
                                                    <span class="tag" style="background-color: #e0f0ff; color: #1d7dd6;">
                                                        <i class="mdi mdi-account-check"></i>
                                                        Em Atendimento
                                                    </span>
                                                    @break
                                                @case(\App\Models\Consulta::STATUS_ATENDIDO)
                                                    <span class="tag tag-status tag-status-ativo">
                                                        <i class="mdi mdi-check-circle"></i>
                                                        Atendido
                                                    </span>
                                                    @break
                                                @case(\App\Models\Consulta::STATUS_CANCELADO)
                                                    <span class="tag tag-status tag-status-inativo">
                                                        <i class="mdi mdi-close-circle"></i>
                                                        Cancelado
                                                    </span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                @if($patient->status == \App\Models\Consulta::STATUS_AGUARDANDO)
                                                    <button class="btn btn-sm btn-primary" title="Iniciar Atendimento"
                                                        onclick="showStartConsultationModal({{ $patient->id }}, '{{ addslashes($patient->paciente->nome ?? 'Paciente') }}')">
                                                        <i class="mdi mdi-play"></i>
                                                    </button>
                                                @elseif($patient->status == \App\Models\Consulta::STATUS_EM_ATENDIMENTO)
                                                    <a href="{{ route('professional.consultation', $patient->id) }}" class="btn btn-sm btn-info" title="Continuar Atendimento">
                                                        <i class="mdi mdi-arrow-right"></i>
                                                    </a>
                                                @endif
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

    <!-- Painel de Controle -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="mdi mdi-account-tie text-success me-2"></i>
                    Profissionais em Atendimento
                </h5>
            </div>
            <div class="card-body">
                @php
                    $profissionais = $patients->pluck('profissional')->unique('id')->filter();
                @endphp
                @forelse ($profissionais as $profissional)
                    <div class="mb-3 pb-2 border-bottom">
                        <div class="d-flex justify-content-between">
                            <span><strong>{{ $profissional->nome ?? 'N/A' }}</strong></span>
                            @if($profissional && $profissional->pausar_atendimento)
                                <span class="tag" style="background-color: #fff8e6; color: #d97706;">Pausado</span>
                            @elseif($profissional && !$patients->where('profissional_id', $profissional->id)->where('status', \App\Models\Consulta::STATUS_EM_ATENDIMENTO)->count())
                                <span class="tag tag-status tag-status-ativo">Disponível</span>
                            @else
                                <span class="tag" style="background-color: #e0f0ff; color: #1d7dd6;">Em atendimento</span>
                            @endif
                        </div>
                        @if($profissional)
                            <div class="d-flex justify-content-between mt-2">
                                <span>Especialidade:</span>
                                <strong>{{ $profissional->especialidade->descricao ?? '-' }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mt-1">
                                <span>Atendimentos Hoje:</span>
                                <strong>
                                    {{ $patients->where('profissional_id', $profissional->id)->where('status', \App\Models\Consulta::STATUS_ATENDIDO)->count() }}
                                </strong>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="alert alert-info">Nenhum profissional vinculado à fila de atendimento.</div>
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
                    <button class="btn btn-outline-success" onclick="newPrescription()">
                        <i class="mdi mdi-file-document-outline me-2"></i>
                        Nova Receita
                    </button>
                    <button class="btn btn-outline-info" onclick="searchPatient()">
                        <i class="mdi mdi-magnify me-2"></i>
                        Buscar Paciente
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Iniciar Atendimento -->
<div class="modal fade" id="startConsultationModal" tabindex="-1" aria-labelledby="startConsultationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="startConsultationModalLabel">
                    <i class="mdi mdi-play-circle me-2"></i>Iniciar Atendimento
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="mdi mdi-information-outline me-2"></i>
                    Tem certeza que deseja iniciar o atendimento para <strong id="startConsultationPatientName"></strong>?
                </div>
                <p>O status da consulta será alterado para "Em Atendimento".</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmStartConsultationBtn">
                    <i class="mdi mdi-check-circle me-2"></i>Confirmar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Busca de Paciente -->
<div class="modal fade" id="searchPatientModal" tabindex="-1" aria-labelledby="searchPatientModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="searchPatientModalLabel">
                    <i class="mdi mdi-magnify me-2"></i>Buscar Paciente
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="input-group mb-3">
                    <span class="input-group-text bg-white"><i class="mdi mdi-magnify"></i></span>
                    <input type="text" class="form-control" id="patientSearchInput" placeholder="Digite nome, CPF ou telefone..." autocomplete="off">
                </div>
                <div id="patientSearchResults" class="list-group shadow-sm" style="max-height: 300px; overflow-y: auto;"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function refreshQueue() {
        window.location.reload();
    }

    function filterByStatus() {
        const status = document.getElementById('statusFilter').value;
        const rows = document.querySelectorAll('#patientsTable tr');

        rows.forEach(row => {
            const rowStatus = row.getAttribute('data-status');
            let statusText = '';
            
            if(rowStatus == {{ \App\Models\Consulta::STATUS_AGUARDANDO }}) {
                statusText = 'Aguardando';
            } else if(rowStatus == {{ \App\Models\Consulta::STATUS_EM_ATENDIMENTO }}) {
                statusText = 'Em Atendimento';
            } else if(rowStatus == {{ \App\Models\Consulta::STATUS_ATENDIDO }}) {
                statusText = 'Atendido';
            } else if(rowStatus == {{ \App\Models\Consulta::STATUS_CANCELADO }}) {
                statusText = 'Cancelado';
            }
            
            if (status === '' || statusText === status) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    let selectedConsultationId = null;

    function showStartConsultationModal(consultationId, patientName) {
        selectedConsultationId = consultationId;
        document.getElementById('startConsultationPatientName').innerText = patientName;
        const modal = new bootstrap.Modal(document.getElementById('startConsultationModal'));
        modal.show();

        document.getElementById('confirmStartConsultationBtn').onclick = function() {
            startConsultationConfirmed();
        };
    }

    function startConsultationConfirmed() {
        if (!selectedConsultationId) {
            alert('Erro: ID da consulta não encontrado.');
            return;
        }

        // Atualizar status para "Em Atendimento" e redirecionar
        fetch(`/professional/update-status/${selectedConsultationId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                status: {{ \App\Models\Consulta::STATUS_EM_ATENDIMENTO }}
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Redirecionar para a tela de consulta
                window.location.href = `/professional/consultation/${selectedConsultationId}`;
            } else {
                alert('Erro ao iniciar atendimento.');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao iniciar atendimento.');
        });
    }

    function newPrescription() {
        window.location.href = '{{ route('professional.newPrescription') }}';
    }

    function searchPatient() {
        const modal = new bootstrap.Modal(document.getElementById('searchPatientModal'));
        modal.show();
        setTimeout(() => {
            document.getElementById('patientSearchInput').focus();
        }, 500);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('patientSearchInput');
        const resultsContainer = document.getElementById('patientSearchResults');
        let debounceTimer;

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                const term = this.value.trim();

                if (term.length < 3) {
                    resultsContainer.style.display = 'none';
                    resultsContainer.innerHTML = '';
                    return;
                }

                debounceTimer = setTimeout(() => {
                    fetch(`{{ route('professional.searchPatient') }}?term=${term}`)
                        .then(response => response.json())
                        .then(data => {
                            resultsContainer.innerHTML = '';
                            if (data.found && data.pacientes.length > 0) {
                                data.pacientes.forEach(patient => {
                                    const item = document.createElement('div');
                                    item.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';
                                    item.innerHTML = `
                                        <div>
                                            <strong>${patient.nome}</strong><br>
                                            <small class="text-muted">CPF: ${patient.cpf_formatado || 'N/A'} • Tel: ${patient.telefone_formatado || 'N/A'}</small>
                                        </div>
                                        <i class="mdi mdi-chevron-right text-muted"></i>
                                    `;
                                    resultsContainer.appendChild(item);
                                });
                                resultsContainer.style.display = 'block';
                            } else {
                                resultsContainer.innerHTML = '<div class="list-group-item text-muted">Nenhum paciente encontrado</div>';
                                resultsContainer.style.display = 'block';
                            }
                        })
                        .catch(error => {
                            console.error('Erro na busca:', error);
                            resultsContainer.innerHTML = '<div class="list-group-item text-danger">Erro ao buscar pacientes</div>';
                            resultsContainer.style.display = 'block';
                        });
                }, 500);
            });
        }
    });
</script>
@endpush
