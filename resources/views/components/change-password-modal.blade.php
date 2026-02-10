<!-- Modal de Alteração de Senha -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" id="changePasswordModalLabel">
                    <i class="mdi mdi-key me-2"></i>
                    Alterar Senha
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="change-password-form" method="POST">
                @csrf
                <div class="modal-body px-4 pb-4">
                    <p class="text-muted mb-4">
                        Alterar senha do usuário <strong id="change-password-user-name"></strong>
                    </p>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            Nova Senha <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="mdi mdi-lock"></i>
                            </span>
                            <input type="password" 
                                   class="form-control" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Digite a nova senha"
                                   required
                                   minlength="6">
                            <button class="btn btn-outline-secondary" type="button" id="toggle-password">
                                <i class="mdi mdi-eye" id="toggle-password-icon"></i>
                            </button>
                        </div>
                        <small class="form-text text-muted">Mínimo de 6 caracteres</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">
                            Confirmar Nova Senha <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="mdi mdi-lock-check"></i>
                            </span>
                            <input type="password" 
                                   class="form-control" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   placeholder="Confirme a nova senha"
                                   required
                                   minlength="6">
                            <button class="btn btn-outline-secondary" type="button" id="toggle-password-confirmation">
                                <i class="mdi mdi-eye" id="toggle-password-confirmation-icon"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div id="password-error" class="alert alert-danger d-none" role="alert">
                        <i class="mdi mdi-alert-circle me-2"></i>
                        <span id="password-error-message"></span>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 justify-content-center">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="mdi mdi-close me-2"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" id="confirm-change-password-btn">
                        <i class="mdi mdi-check me-2"></i>
                        Alterar Senha
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Estilos para o modal de alteração de senha */
#changePasswordModal .modal-content {
    border-radius: 12px;
    border: none;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
}

#changePasswordModal .modal-header {
    padding: 1.5rem 1.5rem 0.5rem;
}

#changePasswordModal .modal-body {
    padding: 0 1.5rem 1rem;
}

#changePasswordModal .modal-footer {
    padding: 0.5rem 1.5rem 1.5rem;
}

#changePasswordModal .input-group-text {
    background: #f8f9fa;
    border-right: none;
}

#changePasswordModal .form-control {
    border-left: none;
}

#changePasswordModal .form-control:focus {
    border-color: #ced4da;
    box-shadow: none;
}

#changePasswordModal .btn-light {
    border: 1px solid #dee2e6;
    color: #6c757d;
    padding: 0.625rem 1.5rem;
    font-weight: 500;
    transition: all 0.2s ease;
}

#changePasswordModal .btn-light:hover {
    background: #f8f9fa;
    border-color: #adb5bd;
    color: #495057;
}

#changePasswordModal .btn-primary {
    padding: 0.625rem 1.5rem;
    font-weight: 500;
    transition: all 0.2s ease;
}

#changePasswordModal .btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
}
</style>
@endpush

@push('scripts')
<script>
(function($) {
    'use strict';
    
    let changePasswordUserId = null;
    
    // Quando o modal é aberto, capturar os dados do botão
    $('#changePasswordModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var userId = button.data('user-id');
        var userName = button.data('user-name');
        
        changePasswordUserId = userId;
        
        // Atualizar o conteúdo do modal
        var modal = $(this);
        modal.find('#change-password-user-name').text(userName);
        modal.find('#change-password-form').attr('action', '/users/' + userId + '/change-password');
        
        // Limpar formulário
        modal.find('#change-password-form')[0].reset();
        modal.find('#password-error').addClass('d-none');
    });
    
    // Toggle mostrar/ocultar senha
    $('#toggle-password').on('click', function() {
        var passwordInput = $('#password');
        var icon = $('#toggle-password-icon');
        
        if (passwordInput.attr('type') === 'password') {
            passwordInput.attr('type', 'text');
            icon.removeClass('mdi-eye').addClass('mdi-eye-off');
        } else {
            passwordInput.attr('type', 'password');
            icon.removeClass('mdi-eye-off').addClass('mdi-eye');
        }
    });
    
    // Toggle mostrar/ocultar confirmação de senha
    $('#toggle-password-confirmation').on('click', function() {
        var passwordInput = $('#password_confirmation');
        var icon = $('#toggle-password-confirmation-icon');
        
        if (passwordInput.attr('type') === 'password') {
            passwordInput.attr('type', 'text');
            icon.removeClass('mdi-eye').addClass('mdi-eye-off');
        } else {
            passwordInput.attr('type', 'password');
            icon.removeClass('mdi-eye-off').addClass('mdi-eye');
        }
    });
    
    // Validação do formulário
    $('#change-password-form').on('submit', function(e) {
        e.preventDefault();
        
        var password = $('#password').val();
        var passwordConfirmation = $('#password_confirmation').val();
        var errorDiv = $('#password-error');
        var errorMessage = $('#password-error-message');
        
        // Limpar erro anterior
        errorDiv.addClass('d-none');
        
        // Validações
        if (password.length < 6) {
            errorMessage.text('A senha deve ter no mínimo 6 caracteres.');
            errorDiv.removeClass('d-none');
            return;
        }
        
        if (password !== passwordConfirmation) {
            errorMessage.text('As senhas não conferem.');
            errorDiv.removeClass('d-none');
            return;
        }
        
        // Se tudo estiver ok, submeter o formulário
        this.submit();
    });
    
    // Limpar quando o modal é fechado
    $('#changePasswordModal').on('hidden.bs.modal', function() {
        changePasswordUserId = null;
        $('#change-password-form')[0].reset();
        $('#password-error').addClass('d-none');
        $('#password').attr('type', 'password');
        $('#password_confirmation').attr('type', 'password');
        $('#toggle-password-icon').removeClass('mdi-eye-off').addClass('mdi-eye');
        $('#toggle-password-confirmation-icon').removeClass('mdi-eye-off').addClass('mdi-eye');
    });
    
})(jQuery);
</script>
@endpush
