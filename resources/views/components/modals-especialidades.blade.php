{{-- Modais da página de especialidades: excluir --}}

<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center px-4 pb-4">
                <div class="mb-3">
                    <div class="delete-icon-wrapper delete-icon-wrapper-especialidade">
                        <i class="mdi mdi-alert-circle-outline"></i>
                    </div>
                </div>
                <h5 class="modal-title mb-3" id="deleteModalLabel">Confirmar Exclusão</h5>
                <p class="text-muted mb-0">
                    Tem certeza que deseja excluir a especialidade <strong id="deleteEspecialidadeDescricao"></strong>?
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
                        Excluir Especialidade
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.delete-icon-wrapper-especialidade {
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

.delete-icon-wrapper-especialidade i {
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
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const especialidadeId = button.getAttribute('data-especialidade-id');
            const especialidadeDescricao = button.getAttribute('data-especialidade-descricao');
            const form = deleteModal.querySelector('#deleteForm');

            if (form) {
                form.action = `/especialidades/${especialidadeId}`;
            }
            const descricaoElement = deleteModal.querySelector('#deleteEspecialidadeDescricao');
            if (descricaoElement) {
                descricaoElement.textContent = especialidadeDescricao;
            }
        });
    }
});
</script>
@endpush
