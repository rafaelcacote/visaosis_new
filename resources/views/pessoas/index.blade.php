@extends('layouts.app')

@section('title', 'Clientes')

@push('plugin-css')
    <link rel="stylesheet" href="{{ asset('assets/css/users.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/list-actions.css') }}">
@endpush

@section('content')
    <div class="d-xl-flex justify-content-between align-items-start mb-4">
        <div>
            <h2 class="text-dark font-weight-bold mb-2">
                <i class="mdi mdi-account-multiple-outline me-2"></i>
                Clientes
            </h2>
            <p class="text-muted mb-0">Gerencie os clientes do sistema</p>
        </div>
        <a href="{{ route('pessoas.create') }}" class="btn btn-primary btn-icon-text">
            <i class="mdi mdi-plus me-2"></i> Novo Cliente
        </a>
    </div>

    <!-- Filtros -->
    @include('pessoas.components.pessoa-filters')

    <!-- Lista de clientes -->
    @include('pessoas.components.pessoas-table')

    @include('components.modals-pessoas')
@endsection
