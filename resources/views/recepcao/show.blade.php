@extends('layouts.app')

@section('title', 'Consulta #' . $consulta->id)

@section('content')
<div class="d-xl-flex justify-content-between align-items-start mb-4">
    <div>
        <h2 class="text-dark font-weight-bold mb-2">
            <i class="mdi mdi-clipboard-text me-2"></i>
            Consulta #{{ $consulta->id }}
        </h2>
        <p class="text-muted mb-0">Detalhes da consulta</p>
    </div>
    <a href="{{ route('recepcao.index') }}" class="btn btn-outline-secondary">
        <i class="mdi mdi-arrow-left me-2"></i>
        Voltar
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Informações do Paciente -->
        <div class="card mb-3">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">
                    <i class="mdi mdi-account-circle me-2"></i>
                    Dados do Paciente
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label text-muted">Nome</label>
                        <div class="fw-medium">{{ $consulta->paciente->nome ?? 'Não informado' }}</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label text-muted">CPF</label>
                        <div class="fw-medium">{{ $consulta->paciente->cpf_formatado ?? 'Não informado' }}</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label text-muted">Data de Nascimento</label>
                        <div class="fw-medium">
                            @if ($consulta->paciente && $consulta->paciente->nascimento_em)
                                {{ $consulta->paciente->nascimento_em->format('d/m/Y') }}
                                @php
                                    $idade = $consulta->paciente->idade;
                                @endphp
                                @if ($idade !== null)
                                    <small class="text-muted">({{ $idade }} anos)</small>
                                @endif
                            @else
                                Não informado
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label text-muted">Telefone</label>
                        <div class="fw-medium">
                            @if ($consulta->paciente->telefone)
                                {{ $consulta->paciente->telefone_formatado }}
                            @else
                                Não informado
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label text-muted">E-mail</label>
                        <div class="fw-medium">
                            @if ($consulta->paciente->email)
                                {{ $consulta->paciente->email }}
                            @else
                                Não informado
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informações da Consulta -->
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">
                    <i class="mdi mdi-calendar-check me-2"></i>
                    Dados da Consulta
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <label class="form-label text-muted">Status</label>
                        <div>
                            <span class="fw-medium">{{ $consulta->status_label }}</span>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <label class="form-label text-muted">Tipo</label>
                        <div class="fw-medium">{{ $consulta->tipo_label }}</div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <label class="form-label text-muted">Prioridade</label>
                        <div>
                            <span class="fw-medium">{{ $consulta->prioridade_label }}</span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8 mb-4">
                        <label class="form-label text-muted">Profissional</label>
                        <div class="fw-medium">{{ $consulta->profissional->nome ?? 'Não informado' }}</div>
                        @if ($consulta->profissional && $consulta->profissional->especialidade)
                            <small class="text-muted">{{ $consulta->profissional->especialidade->descricao }}</small>
                        @endif
                    </div>
                    @if ($consulta->atendido_em)
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Atendido em</label>
                            <div class="fw-medium">{{ $consulta->atendido_em->format('d/m/Y H:i') }}</div>
                        </div>
                    @endif
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label text-muted">Agendado em</label>
                        <div class="fw-medium">
                            @if ($consulta->agendado_em)
                                {{ $consulta->agendado_em->format('d/m/Y H:i') }}
                            @else
                                Não informado
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label text-muted">Chegada</label>
                        <div class="fw-medium">
                            @if ($consulta->chegada_em)
                                {{ $consulta->chegada_em->format('d/m/Y H:i') }}
                            @else
                                Não informado
                            @endif
                        </div>
                    </div>
                </div>

                @if ($consulta->observacoes)
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label text-muted">Observações</label>
                            <div class="fw-medium">{{ $consulta->observacoes }}</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Ações -->
        @if ($consulta->status != \App\Models\Consulta::STATUS_ATENDIDO)
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="mdi mdi-cog me-2"></i>
                        Ações
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if ($consulta->status == \App\Models\Consulta::STATUS_AGUARDANDO)
                            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#iniciarAtendimentoModal">
                                <i class="mdi mdi-play-circle me-2"></i> Iniciar Atendimento
                            </button>
                        @endif

                        @if ($consulta->status == \App\Models\Consulta::STATUS_EM_ATENDIMENTO)
                            <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#finalizarAtendimentoModal">
                                <i class="mdi mdi-check-circle me-2"></i> Finalizar Atendimento
                            </button>
                        @endif

                        @if (in_array($consulta->status, [\App\Models\Consulta::STATUS_AGUARDANDO, \App\Models\Consulta::STATUS_EM_ATENDIMENTO]))
                            <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelarConsultaModal">
                                <i class="mdi mdi-close-circle me-2"></i> Cancelar
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Informações do Sistema -->
        <div class="card {{ $consulta->status == \App\Models\Consulta::STATUS_ATENDIDO ? '' : 'mt-3' }}">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">
                    <i class="mdi mdi-information me-2"></i>
                    Informações do Sistema
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label text-muted small">Cadastrado em</label>
                    <div class="fw-medium">{{ $consulta->created_at->format('d/m/Y H:i') }}</div>
                </div>
                @if ($consulta->updated_at != $consulta->created_at)
                    <div class="mb-3">
                        <label class="form-label text-muted small">Última atualização</label>
                        <div class="fw-medium">{{ $consulta->updated_at->format('d/m/Y H:i') }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Iniciar Atendimento -->
<div class="modal fade" id="iniciarAtendimentoModal" tabindex="-1" aria-labelledby="iniciarAtendimentoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary" id="iniciarAtendimentoModalLabel">
                    <i class="mdi mdi-play-circle me-2"></i>
                    Iniciar Atendimento
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="mdi mdi-information me-2"></i>
                    Tem certeza que deseja iniciar o atendimento para <strong>{{ $consulta->paciente->nome }}</strong>?
                </div>
                <p class="mb-0">O status da consulta será alterado para "Em Atendimento".</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="confirmUpdateStatus({{ $consulta->id }}, {{ \App\Models\Consulta::STATUS_EM_ATENDIMENTO }}, 'iniciarAtendimentoModal')">
                    <i class="mdi mdi-play-circle me-2"></i>
                    Confirmar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Finalizar Atendimento -->
<div class="modal fade" id="finalizarAtendimentoModal" tabindex="-1" aria-labelledby="finalizarAtendimentoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-success" id="finalizarAtendimentoModalLabel">
                    <i class="mdi mdi-check-circle me-2"></i>
                    Finalizar Atendimento
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-success">
                    <i class="mdi mdi-check-circle me-2"></i>
                    Tem certeza que deseja finalizar o atendimento para <strong>{{ $consulta->paciente->nome }}</strong>?
                </div>
                <p class="mb-0">O status da consulta será alterado para "Atendido" e o horário de finalização será registrado.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="confirmUpdateStatus({{ $consulta->id }}, {{ \App\Models\Consulta::STATUS_ATENDIDO }}, 'finalizarAtendimentoModal')">
                    <i class="mdi mdi-check-circle me-2"></i>
                    Finalizar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cancelar Consulta -->
<div class="modal fade" id="cancelarConsultaModal" tabindex="-1" aria-labelledby="cancelarConsultaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger" id="cancelarConsultaModalLabel">
                    <i class="mdi mdi-close-circle me-2"></i>
                    Cancelar Consulta
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="mdi mdi-alert me-2"></i>
                    Tem certeza que deseja cancelar a consulta de <strong>{{ $consulta->paciente->nome }}</strong>?
                </div>
                <p class="mb-0">Esta ação não pode ser desfeita. O status da consulta será alterado para "Cancelado".</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Não Cancelar</button>
                <button type="button" class="btn btn-danger" onclick="confirmUpdateStatus({{ $consulta->id }}, {{ \App\Models\Consulta::STATUS_CANCELADO }}, 'cancelarConsultaModal')">
                    <i class="mdi mdi-close-circle me-2"></i>
                    Confirmar Cancelamento
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmUpdateStatus(consultaId, status, modalId) {
        const modal = bootstrap.Modal.getInstance(document.getElementById(modalId));
        modal.hide();

        updateStatus(consultaId, status);
    }

    function updateStatus(consultaId, status) {
        fetch(`/recepcao/status/${consultaId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    status: status
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message || 'Status atualizado com sucesso!', 'success');
                    setTimeout(() => {
                        window.location.reload();
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

@push('styles')
<style>
    .card-header.bg-secondary {
        background-color: #6c757d !important;
        border-color: #6c757d !important;
        color: white !important;
    }
</style>
@endpush
@endsection
