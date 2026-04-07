@extends('layouts.app')

@section('title', 'Recepção')

@push('plugin-css')
    <link rel="stylesheet" href="{{ asset('assets/css/users.css') }}">
@endpush

@section('content')
    <div class="d-xl-flex justify-content-between align-items-start mb-4">
        <div>
            <h2 class="text-dark font-weight-bold mb-2">
                <i class="mdi mdi-hospital-building me-2"></i>
                Recepção
            </h2>
            <p class="text-muted mb-0">Fila de atendimento do dia</p>
        </div>
        <a href="{{ route('recepcao.triage') }}" class="btn btn-primary btn-icon-text">
            <i class="mdi mdi-plus me-2"></i>
            Nova Triagem
        </a>
    </div>

    <!-- Cards de Estatísticas -->
    <div class="row mb-4">
        <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 grid-margin stretch-card">
            <div class="card card-statistics">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="mdi mdi-hospital-building text-primary icon-lg"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="mb-0 text-dark">Total Hoje</p>
                            <h3 class="font-weight-medium mb-0 text-dark">{{ $stats['total_today'] }}</h3>
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
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="mdi mdi-clock-outline text-warning icon-lg"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="mb-0 text-dark">Aguardando</p>
                            <h3 class="font-weight-medium mb-0 text-dark">{{ $stats['waiting'] }}</h3>
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
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="mdi mdi-account-check text-info icon-lg"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="mb-0 text-dark">Atendimento</p>
                            <h3 class="font-weight-medium mb-0 text-dark">{{ $stats['in_service'] }}</h3>
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
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="mdi mdi-check-circle text-success icon-lg"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="mb-0 text-dark">Atendidos</p>
                            <h3 class="font-weight-medium mb-0 text-dark">{{ $stats['completed'] }}</h3>
                        </div>
                    </div>
                    <p class="text-muted mt-3 mb-0">
                        <i class="mdi mdi-check-all me-1" aria-hidden="true"></i> Finalizados hoje
                    </p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 grid-margin stretch-card">
            <div class="card card-statistics">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="mdi mdi-close-circle text-danger icon-lg"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="mb-0 text-dark">Cancelados</p>
                            <h3 class="font-weight-medium mb-0 text-dark">{{ $stats['cancelled'] }}</h3>
                        </div>
                    </div>
                    <p class="text-muted mt-3 mb-0">
                        <i class="mdi mdi-cancel me-1" aria-hidden="true"></i> Cancelados hoje
                    </p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 grid-margin stretch-card">
            <div class="card card-statistics">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="mdi mdi-clock-time-four text-secondary icon-lg"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="mb-0 text-dark">Hora Atual</p>
                            <h3 class="font-weight-medium mb-0 text-dark">{{ now()->format('H:i') }}</h3>
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
                    <div>
                        <h5 class="card-title mb-0">
                            <i class="mdi mdi-format-list-bulleted text-primary me-2"></i>
                            Fila de Atendimento
                        </h5>
                        <small class="text-muted">Consultas agendadas e em andamento para hoje</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-primary" onclick="refreshQueue()">
                            <i class="mdi mdi-refresh"></i>
                        </button>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                data-bs-toggle="dropdown">
                                Filtros
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="#" onclick="filterByStatus('all')">
                                        Todos
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#"
                                        onclick="filterByStatus('{{ \App\Models\Consulta::STATUS_AGUARDANDO }}')">
                                        Aguardando
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#"
                                        onclick="filterByStatus('{{ \App\Models\Consulta::STATUS_EM_ATENDIMENTO }}')">
                                        Em Atendimento
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#"
                                        onclick="filterByStatus('{{ \App\Models\Consulta::STATUS_ATENDIDO }}')">
                                        Atendidos
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#"
                                        onclick="filterByStatus('{{ \App\Models\Consulta::STATUS_CANCELADO }}')">
                                        Cancelados
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if ($consultas->isEmpty())
                        <div class="text-center py-5">
                            <i class="mdi mdi-inbox text-muted" style="font-size: 3rem;"></i>
                            <h5 class="mt-3 text-muted">Nenhuma consulta encontrada para hoje</h5>
                            <p class="text-muted mb-3">
                                As consultas agendadas para hoje aparecerão aqui assim que forem registradas na recepção.
                            </p>
                            <button type="button" class="btn btn-outline-primary" onclick="refreshQueue()">
                                <i class="mdi mdi-refresh me-2"></i>
                                Atualizar fila
                            </button>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Paciente</th>
                                        <th>Chegada</th>
                                        <th>Tipo</th>
                                        <th>Status</th>
                                        <th>Profissional</th>
                                        <th width="150">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="consultasQueue">
                                    @foreach ($consultas as $consulta)
                                        <tr data-status="{{ $consulta->status }}"
                                            data-priority="{{ $consulta->prioridade }}">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="{{ $consulta->prioridade == \App\Models\Consulta::PRIORIDADE_EMERGENCIA ? 'bg-danger' : ($consulta->prioridade == \App\Models\Consulta::PRIORIDADE ? 'bg-warning' : 'bg-primary') }} text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3"
                                                        style="width: 40px; height: 40px; font-size: 16px; font-weight: 600;">
                                                        {{ strtoupper(mb_substr($consulta->paciente->nome ?? 'N/A', 0, 1, 'UTF-8')) }}
                                                    </div>
                                                    <div>
                                                        <div class="font-weight-medium">
                                                            {{ $consulta->paciente->nome ?? 'Paciente não encontrado' }}
                                                        </div>
                                                        @if ($consulta->prioridade == \App\Models\Consulta::PRIORIDADE_EMERGENCIA)
                                                            <small class="text-danger d-block mt-1">
                                                                <i class="mdi mdi-alert me-1"
                                                                    style="font-size: 14px;"></i>
                                                                Emergência
                                                            </small>
                                                        @elseif($consulta->prioridade == \App\Models\Consulta::PRIORIDADE)
                                                            <small class="text-warning d-block mt-1">
                                                                <i class="mdi mdi-star me-1" style="font-size: 14px;"></i>
                                                                Prioridade
                                                            </small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <span class="font-weight-medium">
                                                        {{ $consulta->chegada_em ? $consulta->chegada_em->format('H:i') : 'N/A' }}
                                                    </span>
                                                    @if ($consulta->chegada_em)
                                                        <br>
                                                        <small class="text-muted">
                                                            {{ $consulta->chegada_em->diffForHumans() }}
                                                        </small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @switch($consulta->tipo)
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
                                                        <span class="text-muted">N/A</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                @switch($consulta->status)
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

                                                    @default
                                                        <span class="text-muted">{{ $consulta->status_label }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                <div>
                                                    <div class="font-weight-medium" style="font-size: 0.875rem;">
                                                        {{ $consulta->profissional->nome ?? 'N/A' }}
                                                    </div>
                                                    <small class="text-muted">
                                                        {{ $consulta->profissional->especialidade->descricao ?? 'Especialidade N/A' }}
                                                    </small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="actions">
                                                    @if ($consulta->status == \App\Models\Consulta::STATUS_AGUARDANDO)
                                                        <button class="btn-action"
                                                            style="background-color: #e0f0ff; color: #1d7dd6;"
                                                            onclick="openStatusModal({{ $consulta->id }}, '{{ addslashes($consulta->paciente->nome ?? 'N/A') }}', 'iniciar', {{ $consulta->profissional_id ?: 'null' }})"
                                                            data-bs-toggle="modal" data-bs-target="#statusModal"
                                                            title="Iniciar Atendimento">
                                                            <i class="mdi mdi-play"></i>
                                                        </button>
                                                        <button class="btn-action btn-action-delete"
                                                            onclick="openStatusModal({{ $consulta->id }}, '{{ addslashes($consulta->paciente->nome ?? 'N/A') }}', 'cancelar')"
                                                            data-bs-toggle="modal" data-bs-target="#statusModal"
                                                            title="Cancelar">
                                                            <i class="mdi mdi-close"></i>
                                                        </button>
                                                    @elseif($consulta->status == \App\Models\Consulta::STATUS_EM_ATENDIMENTO)
                                                        <button class="btn-action btn-action-status-ativar"
                                                            onclick="openStatusModal({{ $consulta->id }}, '{{ addslashes($consulta->paciente->nome ?? 'N/A') }}', 'finalizar')"
                                                            data-bs-toggle="modal" data-bs-target="#statusModal"
                                                            title="Finalizar Atendimento">
                                                            <i class="mdi mdi-check"></i>
                                                        </button>
                                                    @endif

                                                    <button class="btn-action btn-action-view"
                                                        onclick="viewConsulta({{ $consulta->id }})" title="Visualizar">
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
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-account-group text-success me-2"></i>
                        Profissionais
                    </h5>
                </div>
                <div class="card-body">
                    @forelse ($profissionais as $profissional)
                        @php
                            $consultasHoje = $consultas->where('profissional_id', $profissional->id)->count();
                            $emAtendimento = $consultas
                                ->where('profissional_id', $profissional->id)
                                ->where('status', \App\Models\Consulta::STATUS_EM_ATENDIMENTO)
                                ->first();
                        @endphp
                        <div class="d-flex align-items-center mb-3 p-3 border rounded">

                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $profissional->nome }}</h6>
                                <small
                                    class="text-muted">{{ $profissional->especialidade->descricao ?? 'Especialidade não informada' }}</small>
                                @if ($emAtendimento)
                                    <div class="mt-1">
                                        <small class="text-primary">
                                            <i class="mdi mdi-account me-1"></i>
                                            {{ $emAtendimento->paciente->nome ?? 'Paciente N/A' }}
                                        </small>
                                    </div>
                                @endif
                            </div>
                            <div class="text-end">
                                @if ($emAtendimento)
                                    <span class="badge bg-warning">Ocupado</span>
                                @else
                                    <span class="badge bg-success">Disponível</span>
                                @endif
                                <div class="mt-1">
                                    <small class="text-muted">{{ $consultasHoje }} consultas</small>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center">Nenhum profissional cadastrado</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Dinâmico para Status -->
    <div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" id="modalHeader">
                    <h5 class="modal-title" id="statusModalLabel">
                        <i class="mdi" id="modalIcon"></i>
                        <span id="modalTitle"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert" id="modalAlert">
                        <i class="mdi" id="alertIcon"></i>
                        <span id="alertText"></span>
                    </div>
                    <p class="mb-3" id="modalDescription"></p>

                    <!-- Seleção de Profissional (apenas para iniciar atendimento) -->
                    <div id="profissionalSelection" style="display: none;">
                        <div class="form-group mb-3">
                            <label for="profissional_id" class="form-label">Profissional <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="profissional_id" name="profissional_id" required>
                                <option value="">Selecione o profissional...</option>
                                @foreach ($profissionais as $profissional)
                                    <option value="{{ $profissional->id }}">{{ $profissional->nome }} -
                                        {{ $profissional->especialidade->descricao ?? 'Sem especialidade' }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn" id="confirmButton" onclick="confirmStatusUpdate()">
                        <i class="mdi" id="confirmIcon"></i>
                        <span id="confirmText"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .avatar-circle {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
            }

            /* Estilos para cards de estatísticas */
            .card-statistics .card-body {
                padding: 1.5rem;
            }

            .card-statistics .flex-shrink-0 {
                width: 50px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .card-statistics .icon-lg {
                font-size: 2.5rem;
            }

            .card-statistics h3 {
                font-size: 1.75rem;
                line-height: 1.2;
            }

            .card-statistics p {
                font-size: 0.875rem;
                line-height: 1.3;
            }

            tr[data-priority="{{ \App\Models\Consulta::PRIORIDADE_EMERGENCIA }}"] {
                background-color: rgba(220, 53, 69, 0.05);
                border-left: 4px solid #dc3545;
            }

            tr[data-priority="{{ \App\Models\Consulta::PRIORIDADE }}"] {
                background-color: rgba(255, 193, 7, 0.05);
                border-left: 4px solid #ffc107;
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
                const rows = document.querySelectorAll('#consultasQueue tr');
                rows.forEach(row => {
                    if (status === 'all' || row.dataset.status == status) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            function viewConsulta(consultaId) {
                window.location.href = `/recepcao/consulta/${consultaId}`;
            }

            // Variáveis globais para o modal dinâmico
            let currentConsultaIdModal = null;
            let currentAction = null;
            let currentPacienteNome = null;

            function openStatusModal(consultaId, pacienteNome, action, profissionalId = null) {
                currentConsultaIdModal = consultaId;
                currentAction = action;
                currentPacienteNome = pacienteNome;

                const modalHeader = document.getElementById('modalHeader');
                const modalIcon = document.getElementById('modalIcon');
                const modalTitle = document.getElementById('modalTitle');
                const modalAlert = document.getElementById('modalAlert');
                const alertIcon = document.getElementById('alertIcon');
                const alertText = document.getElementById('alertText');
                const modalDescription = document.getElementById('modalDescription');
                const confirmButton = document.getElementById('confirmButton');
                const confirmIcon = document.getElementById('confirmIcon');
                const confirmText = document.getElementById('confirmText');
                const profissionalSelection = document.getElementById('profissionalSelection');

                if (action === 'iniciar') {
                    modalHeader.className = 'modal-header text-primary';
                    modalIcon.className = 'mdi mdi-play-circle me-2';
                    modalTitle.textContent = 'Iniciar Atendimento';
                    modalAlert.className = 'alert alert-info';
                    alertIcon.className = 'mdi mdi-information me-2';
                    alertText.innerHTML = `Tem certeza que deseja iniciar o atendimento para <strong>${pacienteNome}</strong>?`;
                    modalDescription.textContent = 'O status da consulta será alterado para "Em Atendimento".';
                    confirmButton.className = 'btn btn-primary';
                    confirmIcon.className = 'mdi mdi-play-circle me-2';
                    confirmText.textContent = 'Confirmar';

                    // Mostrar seleção de profissional
                    profissionalSelection.style.display = 'block';

                    // Definir profissional selecionado diretamente (sem AJAX)
                    const select = document.getElementById('profissional_id');
                    if (profissionalId) {
                        select.value = profissionalId;
                    } else {
                        select.value = '';
                    }

                } else {
                    // Esconder seleção de profissional para outras ações
                    profissionalSelection.style.display = 'none';
                }

                if (action === 'finalizar') {
                    modalHeader.className = 'modal-header text-success';
                    modalIcon.className = 'mdi mdi-check-circle me-2';
                    modalTitle.textContent = 'Finalizar Atendimento';
                    modalAlert.className = 'alert alert-success';
                    alertIcon.className = 'mdi mdi-check-circle me-2';
                    alertText.innerHTML =
                        `Tem certeza que deseja finalizar o atendimento para <strong>${pacienteNome}</strong>?`;
                    modalDescription.textContent =
                        'O status da consulta será alterado para "Atendido" e o horário de finalização será registrado.';
                    confirmButton.className = 'btn btn-success';
                    confirmIcon.className = 'mdi mdi-check-circle me-2';
                    confirmText.textContent = 'Finalizar';
                } else if (action === 'cancelar') {
                    modalHeader.className = 'modal-header text-danger';
                    modalIcon.className = 'mdi mdi-close-circle me-2';
                    modalTitle.textContent = 'Cancelar Consulta';
                    modalAlert.className = 'alert alert-danger';
                    alertIcon.className = 'mdi mdi-alert me-2';
                    alertText.innerHTML = `Tem certeza que deseja cancelar a consulta de <strong>${pacienteNome}</strong>?`;
                    modalDescription.textContent =
                        'Esta ação não pode ser desfeita. O status da consulta será alterado para "Cancelado".';
                    confirmButton.className = 'btn btn-danger';
                    confirmIcon.className = 'mdi mdi-close-circle me-2';
                    confirmText.textContent = 'Confirmar Cancelamento';
                }
            }

            function confirmStatusUpdate() {
                // Validar profissional se estiver iniciando atendimento
                if (currentAction === 'iniciar') {
                    const profissionalId = document.getElementById('profissional_id').value;
                    if (!profissionalId) {
                        showNotification('Por favor, selecione um profissional antes de iniciar o atendimento.', 'error');
                        return;
                    }
                }

                const modal = bootstrap.Modal.getInstance(document.getElementById('statusModal'));
                modal.hide();

                let status;
                let requestBody = {};

                if (currentAction === 'iniciar') {
                    status = {{ \App\Models\Consulta::STATUS_EM_ATENDIMENTO }};
                    requestBody.profissional_id = document.getElementById('profissional_id').value;
                } else if (currentAction === 'finalizar') {
                    status = {{ \App\Models\Consulta::STATUS_ATENDIDO }};
                } else if (currentAction === 'cancelar') {
                    status = {{ \App\Models\Consulta::STATUS_CANCELADO }};
                }

                requestBody.status = status;

                fetch(`/recepcao/status/${currentConsultaIdModal}`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(requestBody)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showNotification(data.message || 'Status atualizado com sucesso!', 'success');
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            showNotification(data.message || 'Erro ao atualizar status.', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('Erro ao atualizar status.', 'error');
                    });
            }

            function showNotification(message, type) {
                const colors = {
                    success: '#28a745',
                    error: '#dc3545',
                    info: '#17a2b8'
                };

                const notification = document.createElement('div');
                notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${colors[type] || colors.info};
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            z-index: 9999;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        `;
                notification.textContent = message;

                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.remove();
                }, 4000);
            }
        </script>
    @endpush
@endsection
