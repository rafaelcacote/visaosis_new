@extends('layouts.app')

@section('title', 'Detalhes do Usuário')

@section('content')
<div class="d-xl-flex justify-content-between align-items-start mb-4">
    <div>
        <h2 class="text-dark font-weight-bold mb-2">
            <i class="mdi mdi-account me-2"></i>
            Detalhes do Usuário
        </h2>
        <p class="text-muted mb-0">Informações completas do usuário</p>
    </div>
    <div>
        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary me-2">
            <i class="mdi mdi-pencil me-2" style="font-size: 18px;"></i>
            Editar
        </a>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
            <i class="mdi mdi-arrow-left me-2" style="font-size: 18px;"></i>
            Voltar
        </a>
    </div>
</div>

<div class="row">
    <!-- Coluna Esquerda: Informações do Usuário -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">
                    <i class="mdi mdi-account-outline text-primary me-2" style="font-size: 20px;"></i>
                    Informações do Usuário
                </h4>
                
                <div class="text-center mb-4">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; font-size: 32px; font-weight: 600;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
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
                    <div class="col-sm-4">
                        <strong>ID:</strong>
                    </div>
                    <div class="col-sm-8">
                        #{{ $user->id }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4">
                        <strong>Nome:</strong>
                    </div>
                    <div class="col-sm-8">
                        {{ $user->name }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4">
                        <strong>Email:</strong>
                    </div>
                    <div class="col-sm-8">
                        <i class="mdi mdi-email-outline text-primary me-2"></i>
                        {{ $user->email }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4">
                        <strong>CPF/CNPJ:</strong>
                    </div>
                    <div class="col-sm-8">
                        @if($user->cpf_cnpj)
                            @php
                                $cpf_cnpj = preg_replace('/[^0-9]/', '', $user->cpf_cnpj);
                                if(strlen($cpf_cnpj) == 11) {
                                    $formatted = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf_cnpj);
                                } elseif(strlen($cpf_cnpj) == 14) {
                                    $formatted = preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $cpf_cnpj);
                                } else {
                                    $formatted = $user->cpf_cnpj;
                                }
                            @endphp
                            <i class="mdi mdi-identifier text-info me-2"></i>
                            {{ $formatted }}
                        @else
                            N/A
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4">
                        <strong>Status:</strong>
                    </div>
                    <div class="col-sm-8">
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
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4">
                        <strong>Criado em:</strong>
                    </div>
                    <div class="col-sm-8">
                        <i class="mdi mdi-calendar-clock text-muted me-2"></i>
                        {{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : 'N/A' }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4">
                        <strong>Atualizado em:</strong>
                    </div>
                    <div class="col-sm-8">
                        <i class="mdi mdi-calendar-edit text-muted me-2"></i>
                        {{ $user->updated_at ? $user->updated_at->format('d/m/Y H:i') : 'N/A' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Coluna Direita: Localizações -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">
                    <i class="mdi mdi-store text-success me-2" style="font-size: 20px;"></i>
                    Localizações Vinculadas
                </h4>
                
                @if($userLocations->isEmpty())
                    <div class="text-center py-5">
                        <i class="mdi mdi-store-off text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-3 mb-0">Nenhuma loja vinculada a este usuário.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Sigla</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($userLocations as $location)
                                    <tr>
                                        <td>#{{ $location->id }}</td>
                                        <td>
                                            <i class="mdi mdi-store text-success me-2"></i>
                                            {{ $location->name }}
                                        </td>
                                        <td>
                                            <span class="badge badge-info">
                                                {{ $location->short_name ?? 'N/A' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.badge-info {
    border: 1px solid #a461d8;
    color: #ffffff;
    background: #a461d8;
    border-radius: 0.25rem;
    font-size: 11px;
    font-weight: initial;
    line-height: 1;
    padding: 0.375rem 0.5625rem;
    font-family: "nunito-medium", sans-serif;
}

.badge-success {
    border: 1px solid #44ce42;
    color: #ffffff;
    background: #44ce42;
}

.badge-danger {
    border: 1px solid #fc5a5a;
    color: #ffffff;
    background: #fc5a5a;
}
</style>
@endpush
@endsection