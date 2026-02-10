<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center px-4 pb-4">
                <div class="mb-3">
                    <div class="delete-icon-wrapper">
                        <i class="mdi mdi-alert-circle-outline"></i>
                    </div>
                </div>
                <h5 class="modal-title mb-3" id="deleteUserModalLabel">Confirmar Exclusão</h5>
                <p class="text-muted mb-0">
                    Tem certeza que deseja excluir o usuário <strong id="delete-user-name"></strong>?
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
                <button type="button" class="btn btn-danger" id="confirm-delete-btn">
                    <i class="mdi mdi-delete me-2"></i>
                    Excluir Usuário
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Estilos para o modal de exclusão */
.delete-icon-wrapper {
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

.delete-icon-wrapper i {
    font-size: 48px;
    color: #dc2626;
}

#deleteUserModal .modal-content {
    border-radius: 12px;
    border: none;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
}

#deleteUserModal .modal-header {
    padding: 1.5rem 1.5rem 0.5rem;
}

#deleteUserModal .modal-body {
    padding: 0 1.5rem 1rem;
}

#deleteUserModal .modal-footer {
    padding: 0.5rem 1.5rem 1.5rem;
}

#deleteUserModal .btn-light {
    border: 1px solid #dee2e6;
    color: #6c757d;
    padding: 0.625rem 1.5rem;
    font-weight: 500;
    transition: all 0.2s ease;
}

#deleteUserModal .btn-light:hover {
    background: #f8f9fa;
    border-color: #adb5bd;
    color: #495057;
}

#deleteUserModal .btn-danger {
    padding: 0.625rem 1.5rem;
    font-weight: 500;
    transition: all 0.2s ease;
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    border: none;
}

#deleteUserModal .btn-danger:hover {
    background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
}
</style>
@endpush

@push('scripts')
<script>
(function($) {
    'use strict';
    
    let deleteFormId = null;
    
    // Quando o modal é aberto, capturar os dados do botão
    $('#deleteUserModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var userId = button.data('user-id');
        var userName = button.data('user-name');
        var formId = button.data('form-id');
        
        // Armazenar o ID do formulário para usar no botão de confirmação
        deleteFormId = formId;
        
        // Atualizar o conteúdo do modal
        var modal = $(this);
        modal.find('#delete-user-name').text(userName);
    });
    
    // Quando o botão de confirmar exclusão é clicado
    $('#confirm-delete-btn').on('click', function() {
        if (deleteFormId) {
            // Encontrar e submeter o formulário correspondente
            var form = $('#' + deleteFormId);
            if (form.length) {
                form.submit();
            }
        }
    });
    
    // Limpar o formId quando o modal é fechado
    $('#deleteUserModal').on('hidden.bs.modal', function() {
        deleteFormId = null;
    });
    
})(jQuery);
</script>
@endpush
