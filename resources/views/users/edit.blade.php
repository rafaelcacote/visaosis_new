@extends('layouts.app')

@section('title', 'Editar Usuário')

@section('content')
<div class="d-xl-flex justify-content-between align-items-start mb-4">
    <div>
        <h2 class="text-dark font-weight-bold mb-2">
            <i class="mdi mdi-account-edit me-2"></i>
            Editar Usuário
        </h2>
        <p class="text-muted mb-0">Editar dados do usuário</p>
    </div>
    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
        <i class="mdi mdi-arrow-left me-2"></i>
        Voltar
    </a>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Nome Completo <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $user->name) }}" 
                                       placeholder="Digite o nome completo do usuário"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email <span class="text-danger">*</span></label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', $user->email) }}" 
                                       placeholder="exemplo@email.com"
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cpf_cnpj">CPF/CNPJ</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="cpf_cnpj" 
                                       value="{{ $user->cpf_cnpj ?? 'N/A' }}" 
                                       disabled>
                                <small class="form-text text-muted">O CPF não pode ser alterado</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="form-check mt-4">
                                    <label class="form-check-label">
                                        <input type="checkbox" 
                                               class="form-check-input" 
                                               id="status" 
                                               name="status" 
                                               value="1" 
                                               {{ old('status', $user->status) == 1 ? 'checked' : '' }}>
                                        Usuário ativo <i class="input-helper"></i>
                                    </label>
                                    <small class="form-text text-muted d-block">
                                        Desmarque para desativar o usuário sem excluí-lo do sistema.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                <i class="mdi mdi-key me-2"></i>
                                Alterar Senha
                            </button>
                            <small class="form-text text-muted d-block mt-2">
                                Clique no botão acima para alterar a senha do usuário.
                            </small>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Seção de Perfis/Sistemas/Lojas -->
                    @php
                        $profiles = $profiles ?? collect([]);
                        $systems = $systems ?? collect([]);
                    @endphp
                    <div class="row mb-3">
                        <div class="col-12">
                            <h5 class="text-dark font-weight-bold mb-3">
                                <i class="mdi mdi-account-settings me-2"></i>
                                Permissões e Acessos
                            </h5>
                            <p class="text-muted mb-4">Selecione os perfis, sistemas e lojas que este usuário terá acesso.</p>
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-12 col-lg-4">
                            <div class="card h-100">
                                <div class="card-header bg-light border-bottom">
                                    <h5 class="mb-0 text-dark">
                                        <i class="mdi mdi-account-card-details text-primary me-2"></i>
                                        Perfis
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if($profiles->isEmpty())
                                        <div class="text-center py-4">
                                            <i class="mdi mdi-account-off text-muted" style="font-size: 2.5rem;"></i>
                                            <p class="text-muted mt-2 mb-0">Nenhum perfil disponível.</p>
                                        </div>
                                    @else
                                        <div style="max-height: 300px; overflow-y: auto;">
                                            @foreach($profiles as $profile)
                                                <div class="form-check mb-3">
                                                    <label class="form-check-label">
                                                        <input type="checkbox" 
                                                               class="form-check-input profile-checkbox" 
                                                               id="profile_{{ $profile->id }}" 
                                                               name="profiles[]" 
                                                               value="{{ $profile->id }}" 
                                                               {{ in_array($profile->id, old('profiles', $userProfileIds ?? [])) ? 'checked' : '' }}>
                                                        <i class="input-helper"></i>
                                                        <strong>{{ $profile->name }}</strong>
                                                        @if(isset($profile->short_name) && $profile->short_name)
                                                            <br><small class="text-muted">{{ $profile->short_name }}</small>
                                                        @endif
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-4">
                            <div class="card h-100">
                                <div class="card-header bg-light border-bottom">
                                    <h5 class="mb-0 text-dark">
                                        <i class="mdi mdi-desktop-classic text-info me-2"></i>
                                        Sistemas
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if($systems->isEmpty())
                                        <div class="text-center py-4">
                                            <i class="mdi mdi-desktop-classic-off text-muted" style="font-size: 2.5rem;"></i>
                                            <p class="text-muted mt-2 mb-0">Nenhum sistema disponível.</p>
                                        </div>
                                    @else
                                        <div style="max-height: 300px; overflow-y: auto;">
                                            @foreach($systems as $system)
                                                <div class="form-check mb-3">
                                                    <label class="form-check-label">
                                                        <input type="checkbox" 
                                                               class="form-check-input system-checkbox" 
                                                               id="system_{{ $system->id }}" 
                                                               name="systems[]" 
                                                               value="{{ $system->id }}" 
                                                               {{ in_array($system->id, old('systems', $userSystemIds ?? [])) ? 'checked' : '' }}>
                                                        <i class="input-helper"></i>
                                                        <strong>{{ $system->name }}</strong>
                                                        @if(isset($system->short_name) && $system->short_name)
                                                            <br><small class="text-muted">{{ $system->short_name }}</small>
                                                        @elseif(isset($system->system_key) && $system->system_key)
                                                            <br><small class="text-muted">{{ $system->system_key }}</small>
                                                        @endif
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-4">
                            <div class="card h-100">
                                <div class="card-header bg-light border-bottom">
                                    <h5 class="mb-0 text-dark">
                                        <i class="mdi mdi-store text-success me-2"></i>
                                        Lojas <span class="text-danger">*</span>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if($locations->isEmpty())
                                        <div class="text-center py-4">
                                            <i class="mdi mdi-store-off text-muted" style="font-size: 2.5rem;"></i>
                                            <p class="text-muted mt-2 mb-0">Nenhuma loja disponível para o tenant atual.</p>
                                        </div>
                                    @else
                                        <div style="max-height: 300px; overflow-y: auto;">
                                            @foreach($locations as $location)
                                                <div class="form-check mb-3">
                                                    <label class="form-check-label">
                                                        <input type="checkbox" 
                                                               class="form-check-input location-checkbox" 
                                                               id="location_{{ $location->id }}" 
                                                               name="locations[]" 
                                                               value="{{ $location->id }}" 
                                                               {{ in_array($location->id, old('locations', $userLocationIds)) ? 'checked' : '' }}>
                                                        <i class="input-helper"></i>
                                                        <strong>{{ $location->name }}</strong>
                                                        @if($location->short_name)
                                                            <br><small class="text-muted">({{ $location->short_name }})</small>
                                                        @endif
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                    @error('locations')
                                        <div class="text-danger small mt-2">
                                            <i class="mdi mdi-alert-circle-outline me-1"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="mdi mdi-information-outline me-2"></i>
                                <strong>Informação:</strong> Os campos marcados com
                                <span class="text-danger">*</span> são obrigatórios.
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                            <i class="mdi mdi-close me-2"></i>
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-check me-2"></i>
                            Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Alterar Senha -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changePasswordModalLabel">Alterar Senha</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('users.change-password', $user->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="password">Nova Senha <span class="text-danger">*</span></label>
                        <input type="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               id="password" 
                               name="password" 
                               placeholder="Digite a nova senha"
                               required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Mínimo de 6 caracteres</small>
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Confirmar Senha <span class="text-danger">*</span></label>
                        <input type="password" 
                               class="form-control @error('password_confirmation') is-invalid @enderror" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               placeholder="Confirme a nova senha"
                               required>
                        @error('password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Alterar Senha</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validação de locations
    const form = document.querySelector('form[action*="update"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            const locationCheckboxes = document.querySelectorAll('input[name="locations[]"]:checked');
            if (locationCheckboxes.length === 0) {
                e.preventDefault();
                alert('Selecione pelo menos uma loja.');
                return false;
            }
        });
    }
});
</script>
@endpush