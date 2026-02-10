{{-- Modais da página de pacientes: desativar/ativar e excluir --}}

<!-- Modal de Confirmação de Desativar/Ativar Paciente -->
<div class="modal fade" id="toggleStatusPacienteModal" tabindex="-1" aria-labelledby="toggleStatusPacienteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center px-4 pb-4">
                <div class="mb-3">
                    <div id="toggleStatusPacienteIconWrapper" class="toggle-status-icon-wrapper">
                        <i id="toggleStatusPacienteIcon" class="mdi mdi-close-circle-outline"></i>
                    </div>
                </div>
                <h5 class="modal-title mb-3" id="toggleStatusPacienteModalLabel">Confirmar Desativação</h5>
                <p class="text-muted mb-0">
                    Tem certeza que deseja <span id="toggleStatusPacienteActionText">desativar</span> o paciente
                    <strong id="toggleStatusPacienteNome"></strong>?
                </p>
                <p class="text-muted mt-2 mb-0" style="font-size: 0.875rem;" id="toggleStatusPacienteHint">
                    O paciente ficará inativo até ser reativado.
                </p>
                <input type="hidden" id="toggleStatusPacienteId" value="">
                <input type="hidden" id="toggleStatusPacienteNovoStatus" value="">
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="mdi mdi-close me-2"></i>
                    Cancelar
                </button>
                <button type="button" id="toggleStatusPacienteConfirmBtn" class="btn btn-warning">
                    <i class="mdi mdi-close-circle me-2"></i>
                    Desativar Paciente
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão de Paciente -->
<div class="modal fade" id="deletePacienteModal" tabindex="-1" aria-labelledby="deletePacienteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center px-4 pb-4">
                <div class="mb-3">
                    <div class="delete-icon-wrapper delete-icon-wrapper-paciente">
                        <i class="mdi mdi-alert-circle-outline"></i>
                    </div>
                </div>
                <h5 class="modal-title mb-3" id="deletePacienteModalLabel">Confirmar Exclusão</h5>
                <p class="text-muted mb-0">
                    Tem certeza que deseja excluir o paciente <strong id="deletePacienteNome"></strong>?
                </p>
                <p class="text-muted mt-2 mb-0" style="font-size: 0.875rem;">
                    Esta ação não pode ser desfeita.
                </p>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="mdi mdi-close me-2"></i>
                    Cancelar
                </button>
                <form id="deletePacienteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="mdi mdi-delete me-2"></i>
                        Excluir Paciente
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Modal Desativar/Ativar Paciente */
.toggle-status-icon-wrapper {
    width: 80px;
    height: 80px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}

.toggle-status-icon-wrapper.desativar {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
}

.toggle-status-icon-wrapper.desativar i {
    font-size: 48px;
    color: #d97706;
}

.toggle-status-icon-wrapper.ativar {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
}

.toggle-status-icon-wrapper.ativar i {
    font-size: 48px;
    color: #059669;
}

#toggleStatusPacienteModal .modal-content {
    border-radius: 12px;
    border: none;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
}

/* Modal Excluir Paciente */
.delete-icon-wrapper-paciente {
    width: 80px;
    height: 80px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    border-radius: 50%;
    margin-bottom: 1rem;
}

.delete-icon-wrapper-paciente i {
    font-size: 48px;
    color: #dc2626;
}

#deletePacienteModal .modal-content {
    border-radius: 12px;
    border: none;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Modal de exclusão
    const deleteModal = document.getElementById('deletePacienteModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const pessoaId = button.getAttribute('data-pessoa-id');
            const pessoaNome = button.getAttribute('data-pessoa-nome');
            const form = deleteModal.querySelector('#deletePacienteForm');

            if (form) {
                form.action = `/pessoas/${pessoaId}`;
            }
            const nomeElement = deleteModal.querySelector('#deletePacienteNome');
            if (nomeElement) {
                nomeElement.textContent = pessoaNome;
            }
        });
    }

    // Modal de Desativar/Ativar
    const toggleStatusModal = document.getElementById('toggleStatusPacienteModal');
    if (toggleStatusModal) {
        toggleStatusModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            if (!button || !button.hasAttribute('data-toggle-status')) return;

            const pessoaId = button.getAttribute('data-pessoa-id');
            const novoStatus = button.getAttribute('data-novo-status') === 'true';
            const nome = button.getAttribute('data-pessoa-nome');

            document.getElementById('toggleStatusPacienteId').value = pessoaId;
            document.getElementById('toggleStatusPacienteNovoStatus').value = novoStatus ? 'true' : 'false';
            document.getElementById('toggleStatusPacienteNome').textContent = nome;

            const iconWrapper = document.getElementById('toggleStatusPacienteIconWrapper');
            const icon = document.getElementById('toggleStatusPacienteIcon');
            const title = document.getElementById('toggleStatusPacienteModalLabel');
            const actionText = document.getElementById('toggleStatusPacienteActionText');
            const hint = document.getElementById('toggleStatusPacienteHint');
            const confirmBtn = document.getElementById('toggleStatusPacienteConfirmBtn');

            if (novoStatus) {
                iconWrapper.className = 'toggle-status-icon-wrapper ativar';
                icon.className = 'mdi mdi-check-circle-outline';
                title.textContent = 'Confirmar Ativação';
                actionText.textContent = 'ativar';
                hint.textContent = 'O paciente voltará a estar ativo no sistema.';
                confirmBtn.className = 'btn btn-success';
                confirmBtn.innerHTML = '<i class="mdi mdi-check-circle me-2"></i> Ativar Paciente';
            } else {
                iconWrapper.className = 'toggle-status-icon-wrapper desativar';
                icon.className = 'mdi mdi-close-circle-outline';
                title.textContent = 'Confirmar Desativação';
                actionText.textContent = 'desativar';
                hint.textContent = 'O paciente ficará inativo até ser reativado.';
                confirmBtn.className = 'btn btn-warning';
                confirmBtn.innerHTML = '<i class="mdi mdi-close-circle me-2"></i> Desativar Paciente';
            }
        });
    }

    // Botão confirmar do modal de toggle status
    const toggleStatusConfirmBtn = document.getElementById('toggleStatusPacienteConfirmBtn');
    if (toggleStatusConfirmBtn) {
        toggleStatusConfirmBtn.addEventListener('click', function() {
            const pessoaId = document.getElementById('toggleStatusPacienteId').value;
            const btn = this;
            const originalHtml = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processando...';

            fetch(`/pessoas/${pessoaId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('toggleStatusPacienteModal'));
                    if (modal) modal.hide();
                    window.location.reload();
                } else {
                    alert('Erro ao atualizar status: ' + (data.message || 'Tente novamente.'));
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao atualizar status do paciente.');
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            });
        });
    }

    // Configura botão para abrir modal de toggle status
    document.querySelectorAll('[data-toggle-status][data-pessoa-id]').forEach(button => {
        button.setAttribute('data-bs-toggle', 'modal');
        button.setAttribute('data-bs-target', '#toggleStatusPacienteModal');
    });
});
</script>
@endpush

