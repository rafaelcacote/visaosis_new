@extends('layouts.app')

@section('title', 'Novo Usuário')

@section('content')
<div class="d-xl-flex justify-content-between align-items-start mb-4">
    <div>
        <h2 class="text-dark font-weight-bold mb-2">
            <i class="mdi mdi-account-plus me-2"></i>
            Novo Usuário
        </h2>
        <p class="text-muted mb-0">Criar um novo usuário para acesso ao sistema</p>
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
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    @php
                        $hasOldInput = count(session()->getOldInput()) > 0;
                        $profiles = $profiles ?? collect([]);
                        $systems = $systems ?? collect([]);
                    @endphp

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Nome Completo <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}" 
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
                                       value="{{ old('email') }}" 
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cpf_cnpj">CPF/CNPJ <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('cpf_cnpj') is-invalid @enderror" 
                                       id="cpf_cnpj" 
                                       name="cpf_cnpj" 
                                       value="{{ old('cpf_cnpj') }}" 
                                       placeholder="000.000.000-00 ou 00.000.000/0000-00"
                                       required>
                                @error('cpf_cnpj')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
                                               {{ ($hasOldInput ? old('status') : 1) ? 'checked' : '' }}>
                                        Usuário ativo <i class="input-helper"></i>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password">Senha <span class="text-danger">*</span></label>
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Mínimo de 6 caracteres</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password_confirmation">Confirmar Senha <span class="text-danger">*</span></label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       required>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row g-4 mb-4">
                        <div class="col-12 col-lg-4">
                            <div class="card border-primary h-100">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <i class="mdi mdi-account-card-details me-2"></i>
                                        Perfis
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if($profiles->isEmpty())
                                        <div class="text-muted">Nenhum perfil disponível.</div>
                                    @else
                                        <div class="row" style="max-height: 300px; overflow-y: auto;">
                                            @foreach($profiles as $profile)
                                                <div class="col-12 mb-2">
                                                    <div class="form-check">
                                                        <label class="form-check-label">
                                                            <input type="checkbox" 
                                                                   class="form-check-input profile-checkbox" 
                                                                   id="profile_{{ $profile->id }}" 
                                                                   name="profiles[]" 
                                                                   value="{{ $profile->id }}" 
                                                                   {{ in_array($profile->id, old('profiles', [])) ? 'checked' : '' }}>
                                                            <i class="input-helper"></i>
                                                            <strong>{{ $profile->name }}</strong>
                                                            @if(isset($profile->short_name) && $profile->short_name)
                                                                <br><small class="text-muted">{{ $profile->short_name }}</small>
                                                            @endif
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-4">
                            <div class="card border-info h-100">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">
                                        <i class="mdi mdi-desktop-classic me-2"></i>
                                        Sistemas
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if($systems->isEmpty())
                                        <div class="text-muted">Nenhum sistema disponível.</div>
                                    @else
                                        <div class="row" style="max-height: 300px; overflow-y: auto;">
                                            @foreach($systems as $system)
                                                <div class="col-12 mb-2">
                                                    <div class="form-check">
                                                        <label class="form-check-label">
                                                            <input type="checkbox" 
                                                                   class="form-check-input system-checkbox" 
                                                                   id="system_{{ $system->id }}" 
                                                                   name="systems[]" 
                                                                   value="{{ $system->id }}" 
                                                                   {{ in_array($system->id, old('systems', [])) ? 'checked' : '' }}>
                                                            <i class="input-helper"></i>
                                                            <strong>{{ $system->name }}</strong>
                                                            @if(isset($system->short_name) && $system->short_name)
                                                                <br><small class="text-muted">{{ $system->short_name }}</small>
                                                            @elseif(isset($system->system_key) && $system->system_key)
                                                                <br><small class="text-muted">{{ $system->system_key }}</small>
                                                            @endif
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-4">
                            <div class="card border-success h-100">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">
                                        <i class="mdi mdi-store me-2"></i>
                                        Lojas <span class="text-danger">*</span>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if($locations->isEmpty())
                                        <div class="text-muted">Nenhuma loja disponível para o tenant atual.</div>
                                    @else
                                        <div class="row" style="max-height: 300px; overflow-y: auto;">
                                            @foreach($locations as $location)
                                                <div class="col-12 mb-2">
                                                    <div class="form-check">
                                                        <label class="form-check-label">
                                                            <input type="checkbox" 
                                                                   class="form-check-input location-checkbox" 
                                                                   id="location_{{ $location->id }}" 
                                                                   name="locations[]" 
                                                                   value="{{ $location->id }}" 
                                                                   {{ in_array($location->id, old('locations', [])) ? 'checked' : '' }}>
                                                            <i class="input-helper"></i>
                                                            <strong>{{ $location->name }}</strong>
                                                            @if($location->short_name)
                                                                <br><small class="text-muted">({{ $location->short_name }})</small>
                                                            @endif
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                    @error('locations')
                                        <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                            <i class="mdi mdi-close me-2"></i>
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-check me-2"></i>
                            Criar Usuário
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Máscara para CPF
    const cpfField = document.getElementById('cpf_cnpj');
    if (cpfField) {
        cpfField.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            
            if (value.length > 14) {
                value = value.substring(0, 14);
            }
            
            // CPF: 000.000.000-00 | CNPJ: 00.000.000/0000-00
            if (value.length <= 11) {
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            } else {
                value = value.replace(/^(\d{2})(\d)/, '$1.$2');
                value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
                value = value.replace(/^(\d{2})\.(\d{3})\.(\d{3})(\d)/, '$1.$2.$3/$4');
                value = value.replace(/(\d{4})(\d{1,2})$/, '$1-$2');
            }
            
            this.value = value;
        });
    }

    // Validação de locations
    const form = document.querySelector('form');
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