{{-- Modais da página de laboratórios: desativar/ativar e excluir --}}

<!-- Modal de Confirmação de Desativar/Ativar Laboratório -->
<div class="modal fade" id="toggleStatusLaboratorioModal" tabindex="-1" aria-labelledby="toggleStatusLaboratorioModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center px-4 pb-4">
                <div class="mb-3">
                    <div id="toggleStatusLabIconWrapper" class="toggle-status-icon-wrapper">
                        <i id="toggleStatusLabIcon" class="mdi mdi-close-circle-outline"></i>
                    </div>
                </div>
                <h5 class="modal-title mb-3" id="toggleStatusLaboratorioModalLabel">Confirmar Desativação</h5>
                <p class="text-muted mb-0">
                    Tem certeza que deseja <span id="toggleStatusLabActionText">desativar</span> o laboratório <strong id="toggleStatusLaboratorioNome"></strong>?
                </p>
                <p class="text-muted mt-2 mb-0" style="font-size: 0.875rem;" id="toggleStatusLabHint">
                    O laboratório não poderá ser utilizado até ser reativado.
                </p>
                <input type="hidden" id="toggleStatusLaboratorioId" value="">
                <input type="hidden" id="toggleStatusLabNovoStatus" value="">
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="mdi mdi-close me-2"></i>
                    Cancelar
                </button>
                <button type="button" id="toggleStatusLaboratorioConfirmBtn" class="btn btn-warning">
                    <i class="mdi mdi-close-circle me-2"></i>
                    Desativar Laboratório
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão de Laboratório -->
<div class="modal fade" id="deleteLaboratorioModal" tabindex="-1" aria-labelledby="deleteLaboratorioModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center px-4 pb-4">
                <div class="mb-3">
                    <div class="delete-icon-wrapper delete-icon-wrapper-laboratorio">
                        <i class="mdi mdi-alert-circle-outline"></i>
                    </div>
                </div>
                <h5 class="modal-title mb-3" id="deleteLaboratorioModalLabel">Confirmar Exclusão</h5>
                <p class="text-muted mb-0">
                    Tem certeza que deseja excluir o laboratório <strong id="deleteLaboratorioNome"></strong>?
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
                <form id="deleteLaboratorioForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="mdi mdi-delete me-2"></i>
                        Excluir Laboratório
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
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

#toggleStatusLaboratorioModal .modal-content {
    border-radius: 12px;
    border: none;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
}

.delete-icon-wrapper-laboratorio {
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

.delete-icon-wrapper-laboratorio i {
    font-size: 48px;
    color: #dc2626;
}

#deleteLaboratorioModal .modal-content {
    border-radius: 12px;
    border: none;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
}

#deleteLaboratorioModal .btn-danger {
    padding: 0.625rem 1.5rem;
    font-weight: 500;
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    border: none;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteModal = document.getElementById('deleteLaboratorioModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const laboratorioId = button.getAttribute('data-laboratorio-id');
            const laboratorioNome = button.getAttribute('data-laboratorio-nome');
            const form = deleteModal.querySelector('#deleteLaboratorioForm');
            if (form) {
                form.action = '/laboratorios/' + laboratorioId;
            }
            const nomeElement = deleteModal.querySelector('#deleteLaboratorioNome');
            if (nomeElement) {
                nomeElement.textContent = laboratorioNome;
            }
        });
    }

    const toggleStatusModal = document.getElementById('toggleStatusLaboratorioModal');
    if (toggleStatusModal) {
        toggleStatusModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            if (!button || !button.hasAttribute('data-toggle-status')) return;

            const laboratorioId = button.getAttribute('data-laboratorio-id');
            const novoStatus = button.getAttribute('data-novo-status') === 'true';
            const nome = button.getAttribute('data-laboratorio-nome');

            document.getElementById('toggleStatusLaboratorioId').value = laboratorioId;
            document.getElementById('toggleStatusLabNovoStatus').value = novoStatus ? 'true' : 'false';
            document.getElementById('toggleStatusLaboratorioNome').textContent = nome;

            const iconWrapper = document.getElementById('toggleStatusLabIconWrapper');
            const icon = document.getElementById('toggleStatusLabIcon');
            const title = document.getElementById('toggleStatusLaboratorioModalLabel');
            const actionText = document.getElementById('toggleStatusLabActionText');
            const hint = document.getElementById('toggleStatusLabHint');
            const confirmBtn = document.getElementById('toggleStatusLaboratorioConfirmBtn');

            if (novoStatus) {
                iconWrapper.className = 'toggle-status-icon-wrapper ativar';
                icon.className = 'mdi mdi-check-circle-outline';
                title.textContent = 'Confirmar Ativação';
                actionText.textContent = 'ativar';
                hint.textContent = 'O laboratório voltará a estar disponível para uso.';
                confirmBtn.className = 'btn btn-success';
                confirmBtn.innerHTML = '<i class="mdi mdi-check-circle me-2"></i> Ativar Laboratório';
            } else {
                iconWrapper.className = 'toggle-status-icon-wrapper desativar';
                icon.className = 'mdi mdi-close-circle-outline';
                title.textContent = 'Confirmar Desativação';
                actionText.textContent = 'desativar';
                hint.textContent = 'O laboratório não poderá ser utilizado até ser reativado.';
                confirmBtn.className = 'btn btn-warning';
                confirmBtn.innerHTML = '<i class="mdi mdi-close-circle me-2"></i> Desativar Laboratório';
            }
        });
    }

    const toggleStatusConfirmBtn = document.getElementById('toggleStatusLaboratorioConfirmBtn');
    if (toggleStatusConfirmBtn) {
        toggleStatusConfirmBtn.addEventListener('click', function() {
            const laboratorioId = document.getElementById('toggleStatusLaboratorioId').value;
            const btn = this;
            const originalHtml = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processando...';

            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            fetch('/laboratorios/' + laboratorioId + '/toggle-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken ? csrfToken.getAttribute('content') : '',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('toggleStatusLaboratorioModal'));
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
                alert('Erro ao atualizar status do laboratório.');
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            });
        });
    }

    document.querySelectorAll('[data-laboratorio-id][data-toggle-status]').forEach(button => {
        button.setAttribute('data-bs-toggle', 'modal');
        button.setAttribute('data-bs-target', '#toggleStatusLaboratorioModal');
    });
});
</script>
@endpush
