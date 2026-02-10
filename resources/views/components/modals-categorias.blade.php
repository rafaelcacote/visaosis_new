{{-- Modais da página de categorias: desativar/ativar e excluir --}}

<!-- Modal de Confirmação de Desativar/Ativar Categoria -->
<div class="modal fade" id="toggleStatusModal" tabindex="-1" aria-labelledby="toggleStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center px-4 pb-4">
                <div class="mb-3">
                    <div id="toggleStatusIconWrapper" class="toggle-status-icon-wrapper">
                        <i id="toggleStatusIcon" class="mdi mdi-close-circle-outline"></i>
                    </div>
                </div>
                <h5 class="modal-title mb-3" id="toggleStatusModalLabel">Confirmar Desativação</h5>
                <p class="text-muted mb-0">
                    Tem certeza que deseja <span id="toggleStatusActionText">desativar</span> a categoria <strong id="toggleStatusCategoriaDescricao"></strong>?
                </p>
                <p class="text-muted mt-2 mb-0" style="font-size: 0.875rem;" id="toggleStatusHint">
                    A categoria não poderá ser utilizada até ser reativada.
                </p>
                <input type="hidden" id="toggleStatusCategoriaId" value="">
                <input type="hidden" id="toggleStatusNovoStatus" value="">
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="mdi mdi-close me-2"></i>
                    Cancelar
                </button>
                <button type="button" id="toggleStatusConfirmBtn" class="btn btn-warning">
                    <i class="mdi mdi-close-circle me-2"></i>
                    Desativar Categoria
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão de Categoria -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center px-4 pb-4">
                <div class="mb-3">
                    <div class="delete-icon-wrapper delete-icon-wrapper-categoria">
                        <i class="mdi mdi-alert-circle-outline"></i>
                    </div>
                </div>
                <h5 class="modal-title mb-3" id="deleteModalLabel">Confirmar Exclusão</h5>
                <p class="text-muted mb-0">
                    Tem certeza que deseja excluir a categoria <strong id="deleteCategoriaDescricao"></strong>?
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
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="mdi mdi-delete me-2"></i>
                        Excluir Categoria
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Modal Desativar/Ativar Categoria */
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

#toggleStatusModal .modal-content {
    border-radius: 12px;
    border: none;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
}

#toggleStatusModal .modal-header {
    padding: 1.5rem 1.5rem 0.5rem;
}

#toggleStatusModal .modal-body {
    padding: 0 1.5rem 1rem;
}

#toggleStatusModal .modal-footer {
    padding: 0.5rem 1.5rem 1.5rem;
}

#toggleStatusModal .btn-light {
    border: 1px solid #dee2e6;
    color: #6c757d;
    padding: 0.625rem 1.5rem;
    font-weight: 500;
    transition: all 0.2s ease;
}

#toggleStatusModal .btn-light:hover {
    background: #f8f9fa;
    border-color: #adb5bd;
    color: #495057;
}

#toggleStatusModal .btn-warning {
    padding: 0.625rem 1.5rem;
    font-weight: 500;
    transition: all 0.2s ease;
    background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
    border: none;
    color: #fff;
}

#toggleStatusModal .btn-warning:hover {
    background: linear-gradient(135deg, #b45309 0%, #92400e 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(217, 119, 6, 0.3);
    color: #fff;
}

#toggleStatusModal .btn-success {
    padding: 0.625rem 1.5rem;
    font-weight: 500;
    transition: all 0.2s ease;
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    border: none;
    color: #fff;
}

#toggleStatusModal .btn-success:hover {
    background: linear-gradient(135deg, #047857 0%, #065f46 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
    color: #fff;
}

/* Modal Excluir Categoria */
.delete-icon-wrapper-categoria {
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

.delete-icon-wrapper-categoria i {
    font-size: 48px;
    color: #dc2626;
}

#deleteModal .modal-content {
    border-radius: 12px;
    border: none;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
}

#deleteModal .modal-header {
    padding: 1.5rem 1.5rem 0.5rem;
}

#deleteModal .modal-body {
    padding: 0 1.5rem 1rem;
}

#deleteModal .modal-footer {
    padding: 0.5rem 1.5rem 1.5rem;
}

#deleteModal .btn-light {
    border: 1px solid #dee2e6;
    color: #6c757d;
    padding: 0.625rem 1.5rem;
    font-weight: 500;
    transition: all 0.2s ease;
}

#deleteModal .btn-light:hover {
    background: #f8f9fa;
    border-color: #adb5bd;
    color: #495057;
}

#deleteModal .btn-danger {
    padding: 0.625rem 1.5rem;
    font-weight: 500;
    transition: all 0.2s ease;
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    border: none;
}

#deleteModal .btn-danger:hover {
    background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configurar modal de exclusão
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const categoriaId = button.getAttribute('data-categoria-id');
            const categoriaDescricao = button.getAttribute('data-categoria-descricao');
            const form = deleteModal.querySelector('#deleteForm');
            
            if (form) {
                form.action = `/categorias/${categoriaId}`;
            }
            const descricaoElement = deleteModal.querySelector('#deleteCategoriaDescricao');
            if (descricaoElement) {
                descricaoElement.textContent = categoriaDescricao;
            }
        });
    }

    // Modal de Desativar/Ativar Categoria
    const toggleStatusModal = document.getElementById('toggleStatusModal');
    if (toggleStatusModal) {
        toggleStatusModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            if (!button || !button.hasAttribute('data-toggle-status')) return;

            const categoriaId = button.getAttribute('data-categoria-id');
            const novoStatus = button.getAttribute('data-novo-status') === 'true';
            const descricao = button.getAttribute('data-categoria-descricao');

            document.getElementById('toggleStatusCategoriaId').value = categoriaId;
            document.getElementById('toggleStatusNovoStatus').value = novoStatus ? 'true' : 'false';
            document.getElementById('toggleStatusCategoriaDescricao').textContent = descricao;

            const iconWrapper = document.getElementById('toggleStatusIconWrapper');
            const icon = document.getElementById('toggleStatusIcon');
            const title = document.getElementById('toggleStatusModalLabel');
            const actionText = document.getElementById('toggleStatusActionText');
            const hint = document.getElementById('toggleStatusHint');
            const confirmBtn = document.getElementById('toggleStatusConfirmBtn');

            if (novoStatus) {
                iconWrapper.className = 'toggle-status-icon-wrapper ativar';
                icon.className = 'mdi mdi-check-circle-outline';
                title.textContent = 'Confirmar Ativação';
                actionText.textContent = 'ativar';
                hint.textContent = 'A categoria voltará a estar disponível para uso.';
                hint.style.display = 'block';
                confirmBtn.className = 'btn btn-success';
                confirmBtn.innerHTML = '<i class="mdi mdi-check-circle me-2"></i> Ativar Categoria';
            } else {
                iconWrapper.className = 'toggle-status-icon-wrapper desativar';
                icon.className = 'mdi mdi-close-circle-outline';
                title.textContent = 'Confirmar Desativação';
                actionText.textContent = 'desativar';
                hint.textContent = 'A categoria não poderá ser utilizada até ser reativada.';
                hint.style.display = 'block';
                confirmBtn.className = 'btn btn-warning';
                confirmBtn.innerHTML = '<i class="mdi mdi-close-circle me-2"></i> Desativar Categoria';
            }
        });
    }

    // Botão confirmar do modal de toggle status
    const toggleStatusConfirmBtn = document.getElementById('toggleStatusConfirmBtn');
    if (toggleStatusConfirmBtn) {
        toggleStatusConfirmBtn.addEventListener('click', function() {
            const categoriaId = document.getElementById('toggleStatusCategoriaId').value;
            const btn = this;
            const originalHtml = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processando...';

            fetch(`/categorias/${categoriaId}/toggle-status`, {
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
                    const modal = bootstrap.Modal.getInstance(document.getElementById('toggleStatusModal'));
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
                alert('Erro ao atualizar status da categoria.');
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            });
        });
    }

    document.querySelectorAll('[data-toggle-status]').forEach(button => {
        button.setAttribute('data-bs-toggle', 'modal');
        button.setAttribute('data-bs-target', '#toggleStatusModal');
    });
});
</script>
@endpush
