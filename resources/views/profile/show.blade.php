@extends('layouts.app')

@section('title', 'Meu Perfil')

@section('content')
<div class="page-show">
<div class="d-xl-flex justify-content-between align-items-start mb-4">
    <div>
        <h2 class="text-dark font-weight-bold mb-2">
            <i class="mdi mdi-account me-2"></i>
            Meu Perfil
        </h2>
        <p class="text-muted mb-0">Visualize e edite suas informações pessoais</p>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-body">
                <h4 class="card-title mb-4">
                    <i class="mdi mdi-account-outline text-primary me-2" style="font-size: 20px;"></i>
                    Informações Pessoais
                </h4>

                <div class="text-center mb-4">
                    @php
                        $nameParts = explode(' ', $user->name);
                        if (count($nameParts) >= 2) {
                            $initials = strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[count($nameParts) - 1], 0, 1));
                        } else {
                            $initials = strtoupper(substr($user->name, 0, 2));
                        }
                    @endphp
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; font-size: 32px; font-weight: 600;">
                        {{ $initials }}
                    </div>
                    <h5 class="mb-1">{{ $user->name }}</h5>
                    <p class="text-muted mb-2">{{ $user->email }}</p>
                    @if($user->status == 1)
                        <span class="badge badge-success">
                            <i class="mdi mdi-check-circle me-1"></i>
                            Ativo
                        </span>
                    @else
                        <span class="badge badge-danger">
                            <i class="mdi mdi-close-circle me-1"></i>
                            Inativo
                        </span>
                    @endif
                </div>

                <hr class="my-4">

                <div class="row mb-3">
                    <div class="col-sm-4"><strong>CPF/CNPJ:</strong></div>
                    <div class="col-sm-8">
                        @if($user->cpf_cnpj)
                            @php
                                $cpf_cnpj = preg_replace('/[^0-9]/', '', $user->cpf_cnpj);
                                if (strlen($cpf_cnpj) == 11) {
                                    $formatted = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf_cnpj);
                                } elseif (strlen($cpf_cnpj) == 14) {
                                    $formatted = preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $cpf_cnpj);
                                } else {
                                    $formatted = $user->cpf_cnpj;
                                }
                            @endphp
                            <i class="mdi mdi-identifier text-info me-2"></i>{{ $formatted }}
                        @else
                            N/A
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Membro desde:</strong></div>
                    <div class="col-sm-8">
                        <i class="mdi mdi-calendar-clock text-muted me-2"></i>
                        {{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : 'N/A' }}
                    </div>
                </div>
                @if($user->last_login)
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Último acesso:</strong></div>
                    <div class="col-sm-8">
                        <i class="mdi mdi-clock-outline text-muted me-2"></i>
                        {{ $user->last_login->format('d/m/Y H:i') }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">
                    <i class="mdi mdi-account-edit text-primary me-2" style="font-size: 20px;"></i>
                    Editar Perfil
                </h4>

                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group mb-3">
                        <label for="name">Nome Completo <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               id="name"
                               name="name"
                               value="{{ old('name', $user->name) }}"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="email">Email <span class="text-danger">*</span></label>
                        <input type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               id="email"
                               name="email"
                               value="{{ old('email', $user->email) }}"
                               required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-content-save me-2"></i>
                        Salvar Alterações
                    </button>
                </form>

                <hr class="my-4">

                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#profileChangePasswordModal">
                    <i class="mdi mdi-key me-2"></i>
                    Alterar Senha
                </button>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        @if($tenant)
        <div class="card mb-4">
            <div class="card-body">
                <h4 class="card-title mb-4">
                    <i class="mdi mdi-domain text-info me-2" style="font-size: 20px;"></i>
                    Organização
                </h4>
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Empresa:</strong></div>
                    <div class="col-sm-8">{{ $tenant->name ?? $tenant->trade_name ?? 'N/A' }}</div>
                </div>
                @if($location)
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Localidade atual:</strong></div>
                    <div class="col-sm-8">
                        <i class="mdi mdi-store text-success me-2"></i>
                        {{ $location->name ?? $location->short_name ?? 'N/A' }}
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <div class="card mb-4">
            <div class="card-body">
                <h4 class="card-title mb-4">
                    <i class="mdi mdi-store text-success me-2" style="font-size: 20px;"></i>
                    Localidades Vinculadas
                </h4>

                @if($userLocations->isEmpty())
                    <div class="text-center py-4">
                        <i class="mdi mdi-store-off text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-3 mb-0">Nenhuma localidade vinculada.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Sigla</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($userLocations as $loc)
                                    <tr>
                                        <td>
                                            <i class="mdi mdi-store text-success me-2"></i>
                                            {{ $loc->name }}
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ $loc->short_name ?? 'N/A' }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">
                    <i class="mdi mdi-shield-account text-warning me-2" style="font-size: 20px;"></i>
                    Perfis de Acesso
                </h4>

                @if(empty($profiles))
                    <div class="text-center py-4">
                        <i class="mdi mdi-shield-off text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-3 mb-0">Nenhum perfil atribuído.</p>
                    </div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach($profiles as $profile)
                            <li class="list-group-item d-flex align-items-center px-0">
                                <i class="mdi mdi-shield-check text-warning me-2"></i>
                                {{ is_array($profile) ? ($profile['name'] ?? $profile['nome'] ?? 'Perfil') : $profile }}
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>
</div>
@endsection
