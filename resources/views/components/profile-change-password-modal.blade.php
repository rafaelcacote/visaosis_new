<!-- Modal de Alteração de Senha do Perfil -->
<div class="modal fade" id="profileChangePasswordModal" tabindex="-1" aria-labelledby="profileChangePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" id="profileChangePasswordModalLabel">
                    <i class="mdi mdi-key me-2"></i>
                    Alterar Senha
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="profile-change-password-form" action="{{ route('profile.change-password') }}" method="POST">
                @csrf
                <div class="modal-body px-4 pb-4">
                    <p class="text-muted mb-4">
                        Alterar senha de <strong>{{ Auth::user()->name }}</strong>
                    </p>

                    <div class="mb-3">
                        <label for="profile_current_password" class="form-label">
                            Senha Atual <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="mdi mdi-lock"></i>
                            </span>
                            <input type="password"
                                   class="form-control @error('current_password') is-invalid @enderror"
                                   id="profile_current_password"
                                   name="current_password"
                                   placeholder="Digite sua senha atual"
                                   required>
                            <button class="btn btn-outline-secondary" type="button" id="toggle-profile-current-password">
                                <i class="mdi mdi-eye" id="toggle-profile-current-password-icon"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="profile_password" class="form-label">
                            Nova Senha <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="mdi mdi-lock-outline"></i>
                            </span>
                            <input type="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   id="profile_password"
                                   name="password"
                                   placeholder="Digite a nova senha"
                                   required
                                   minlength="6">
                            <button class="btn btn-outline-secondary" type="button" id="toggle-profile-password">
                                <i class="mdi mdi-eye" id="toggle-profile-password-icon"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Mínimo de 6 caracteres</small>
                    </div>

                    <div class="mb-3">
                        <label for="profile_password_confirmation" class="form-label">
                            Confirmar Nova Senha <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="mdi mdi-lock-check"></i>
                            </span>
                            <input type="password"
                                   class="form-control"
                                   id="profile_password_confirmation"
                                   name="password_confirmation"
                                   placeholder="Confirme a nova senha"
                                   required
                                   minlength="6">
                            <button class="btn btn-outline-secondary" type="button" id="toggle-profile-password-confirmation">
                                <i class="mdi mdi-eye" id="toggle-profile-password-confirmation-icon"></i>
                            </button>
                        </div>
                    </div>

                    <div id="profile-password-error" class="alert alert-danger d-none" role="alert">
                        <i class="mdi mdi-alert-circle me-2"></i>
                        <span id="profile-password-error-message"></span>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 justify-content-center">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="mdi mdi-close me-2"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" id="confirm-profile-change-password-btn">
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
#profileChangePasswordModal .modal-content {
    border-radius: 12px;
    border: none;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
}

#profileChangePasswordModal .modal-header {
    padding: 1.5rem 1.5rem 0.5rem;
}

#profileChangePasswordModal .modal-body {
    padding: 0 1.5rem 1rem;
}

#profileChangePasswordModal .modal-footer {
    padding: 0.5rem 1.5rem 1.5rem;
}

#profileChangePasswordModal .input-group-text {
    background: #f8f9fa;
    border-right: none;
}

#profileChangePasswordModal .form-control {
    border-left: none;
}

#profileChangePasswordModal .form-control:focus {
    border-color: #ced4da;
    box-shadow: none;
}

#profileChangePasswordModal .btn-light {
    border: 1px solid #dee2e6;
    color: #6c757d;
    padding: 0.625rem 1.5rem;
    font-weight: 500;
}

#profileChangePasswordModal .btn-primary {
    padding: 0.625rem 1.5rem;
    font-weight: 500;
}
</style>
@endpush

@push('scripts')
<script>
(function($) {
    'use strict';

    function togglePasswordVisibility(inputSelector, iconSelector) {
        var passwordInput = $(inputSelector);
        var icon = $(iconSelector);

        if (passwordInput.attr('type') === 'password') {
            passwordInput.attr('type', 'text');
            icon.removeClass('mdi-eye').addClass('mdi-eye-off');
        } else {
            passwordInput.attr('type', 'password');
            icon.removeClass('mdi-eye-off').addClass('mdi-eye');
        }
    }

    $('#toggle-profile-current-password').on('click', function() {
        togglePasswordVisibility('#profile_current_password', '#toggle-profile-current-password-icon');
    });

    $('#toggle-profile-password').on('click', function() {
        togglePasswordVisibility('#profile_password', '#toggle-profile-password-icon');
    });

    $('#toggle-profile-password-confirmation').on('click', function() {
        togglePasswordVisibility('#profile_password_confirmation', '#toggle-profile-password-confirmation-icon');
    });

    $('#profile-change-password-form').on('submit', function(e) {
        var password = $('#profile_password').val();
        var passwordConfirmation = $('#profile_password_confirmation').val();
        var errorDiv = $('#profile-password-error');
        var errorMessage = $('#profile-password-error-message');

        errorDiv.addClass('d-none');

        if (password.length < 6) {
            e.preventDefault();
            errorMessage.text('A senha deve ter no mínimo 6 caracteres.');
            errorDiv.removeClass('d-none');
            return;
        }

        if (password !== passwordConfirmation) {
            e.preventDefault();
            errorMessage.text('As senhas não conferem.');
            errorDiv.removeClass('d-none');
        }
    });

    $('#profileChangePasswordModal').on('hidden.bs.modal', function() {
        $('#profile-change-password-form')[0].reset();
        $('#profile-password-error').addClass('d-none');
        $('#profile_current_password, #profile_password, #profile_password_confirmation').attr('type', 'password');
        $('#toggle-profile-current-password-icon, #toggle-profile-password-icon, #toggle-profile-password-confirmation-icon')
            .removeClass('mdi-eye-off').addClass('mdi-eye');
    });

    @if($errors->has('current_password') || $errors->has('password'))
    document.addEventListener('DOMContentLoaded', function() {
        var modal = new bootstrap.Modal(document.getElementById('profileChangePasswordModal'));
        modal.show();
    });
    @endif
})(jQuery);
</script>
@endpush
