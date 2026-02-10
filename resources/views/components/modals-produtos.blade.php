{{-- Modais da página de produtos: desativar/ativar e excluir --}}

<!-- Modal de Confirmação de Desativar/Ativar Produto -->
<div class="modal fade" id="toggleStatusProdutoModal" tabindex="-1" aria-labelledby="toggleStatusProdutoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center px-4 pb-4">
                <div class="mb-3">
                    <div id="toggleStatusProdIconWrapper" class="toggle-status-icon-wrapper">
                        <i id="toggleStatusProdIcon" class="mdi mdi-close-circle-outline"></i>
                    </div>
                </div>
                <h5 class="modal-title mb-3" id="toggleStatusProdutoModalLabel">Confirmar Desativação</h5>
                <p class="text-muted mb-0">
                    Tem certeza que deseja <span id="toggleStatusProdActionText">desativar</span> o produto <strong id="toggleStatusProdutoNome"></strong>?
                </p>
                <p class="text-muted mt-2 mb-0" style="font-size: 0.875rem;" id="toggleStatusProdHint">
                    O produto não poderá ser utilizado até ser reativado.
                </p>
                <input type="hidden" id="toggleStatusProdutoId" value="">
                <input type="hidden" id="toggleStatusProdNovoStatus" value="">
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="mdi mdi-close me-2"></i>
                    Cancelar
                </button>
                <button type="button" id="toggleStatusProdutoConfirmBtn" class="btn btn-warning">
                    <i class="mdi mdi-close-circle me-2"></i>
                    Desativar Produto
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão de Produto -->
<div class="modal fade" id="deleteProdutoModal" tabindex="-1" aria-labelledby="deleteProdutoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center px-4 pb-4">
                <div class="mb-3">
                    <div class="delete-icon-wrapper delete-icon-wrapper-produto">
                        <i class="mdi mdi-alert-circle-outline"></i>
                    </div>
                </div>
                <h5 class="modal-title mb-3" id="deleteProdutoModalLabel">Confirmar Exclusão</h5>
                <p class="text-muted mb-0">
                    Tem certeza que deseja excluir o produto <strong id="deleteProdutoNome"></strong>?
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
                <form id="deleteProdutoForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="mdi mdi-delete me-2"></i>
                        Excluir Produto
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

#toggleStatusProdutoModal .modal-content {
    border-radius: 12px;
    border: none;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
}

.delete-icon-wrapper-produto {
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

.delete-icon-wrapper-produto i {
    font-size: 48px;
    color: #dc2626;
}

#deleteProdutoModal .modal-content {
    border-radius: 12px;
    border: none;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
}

#deleteProdutoModal .btn-danger {
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
    const deleteModal = document.getElementById('deleteProdutoModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const produtoId = button.getAttribute('data-produto-id');
            const produtoNome = button.getAttribute('data-produto-nome');
            const form = deleteModal.querySelector('#deleteProdutoForm');
            if (form) {
                form.action = '/produtos/' + produtoId;
            }
            const nomeElement = deleteModal.querySelector('#deleteProdutoNome');
            if (nomeElement) {
                nomeElement.textContent = produtoNome;
            }
        });
    }

    const toggleStatusModal = document.getElementById('toggleStatusProdutoModal');
    if (toggleStatusModal) {
        toggleStatusModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            if (!button || !button.hasAttribute('data-toggle-status')) return;

            const produtoId = button.getAttribute('data-produto-id');
            const novoStatus = button.getAttribute('data-novo-status') === 'true';
            const nome = button.getAttribute('data-produto-nome');

            document.getElementById('toggleStatusProdutoId').value = produtoId;
            document.getElementById('toggleStatusProdNovoStatus').value = novoStatus ? 'true' : 'false';
            document.getElementById('toggleStatusProdutoNome').textContent = nome;

            const iconWrapper = document.getElementById('toggleStatusProdIconWrapper');
            const icon = document.getElementById('toggleStatusProdIcon');
            const title = document.getElementById('toggleStatusProdutoModalLabel');
            const actionText = document.getElementById('toggleStatusProdActionText');
            const hint = document.getElementById('toggleStatusProdHint');
            const confirmBtn = document.getElementById('toggleStatusProdutoConfirmBtn');

            if (novoStatus) {
                iconWrapper.className = 'toggle-status-icon-wrapper ativar';
                icon.className = 'mdi mdi-check-circle-outline';
                title.textContent = 'Confirmar Ativação';
                actionText.textContent = 'ativar';
                hint.textContent = 'O produto voltará a estar disponível para uso.';
                confirmBtn.className = 'btn btn-success';
                confirmBtn.innerHTML = '<i class="mdi mdi-check-circle me-2"></i> Ativar Produto';
            } else {
                iconWrapper.className = 'toggle-status-icon-wrapper desativar';
                icon.className = 'mdi mdi-close-circle-outline';
                title.textContent = 'Confirmar Desativação';
                actionText.textContent = 'desativar';
                hint.textContent = 'O produto não poderá ser utilizado até ser reativado.';
                confirmBtn.className = 'btn btn-warning';
                confirmBtn.innerHTML = '<i class="mdi mdi-close-circle me-2"></i> Desativar Produto';
            }
        });
    }

    const toggleStatusConfirmBtn = document.getElementById('toggleStatusProdutoConfirmBtn');
    if (toggleStatusConfirmBtn) {
        toggleStatusConfirmBtn.addEventListener('click', function() {
            const produtoId = document.getElementById('toggleStatusProdutoId').value;
            const btn = this;
            const originalHtml = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processando...';

            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            fetch('/produtos/' + produtoId + '/toggle-status', {
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
                    const modal = bootstrap.Modal.getInstance(document.getElementById('toggleStatusProdutoModal'));
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
                alert('Erro ao atualizar status do produto.');
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            });
        });
    }

    document.querySelectorAll('[data-produto-id][data-toggle-status]').forEach(button => {
        button.setAttribute('data-bs-toggle', 'modal');
        button.setAttribute('data-bs-target', '#toggleStatusProdutoModal');
    });
});
</script>
@endpush
