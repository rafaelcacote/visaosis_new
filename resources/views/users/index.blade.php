@extends('layouts.app')

@section('title', 'Usuários')

@section('content')
    <div class="d-xl-flex justify-content-between align-items-start mb-4">
        <div>
            <h2 class="text-dark font-weight-bold mb-2">
                <i class="mdi mdi-account-multiple me-2"></i>
                Usuários
            </h2>
            <p class="text-muted mb-0">Gerencie os usuários do sistema</p>
        </div>
        <a href="{{ route('users.create') }}" class="btn btn-primary btn-icon-text">
            <i class="mdi mdi-plus me-2"></i> Novo Usuário
        </a>
    </div>

    <!-- Filtros -->
    @include('users.components.user-filters')

    <!-- Lista de usuários -->
    @include('users.components.users-table')

    <!-- Modal de Confirmação de Exclusão -->
    @include('components.delete-confirmation-modal')

    <!-- Modal de Alteração de Senha -->
    @include('components.change-password-modal')

    @push('plugin-css')
        <link rel="stylesheet" href="{{ asset('assets/css/users.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/list-actions.css') }}">
    @endpush
@endsection
