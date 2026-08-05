@extends('layouts.app')

@section('title', 'Profissionais')

@push('plugin-css')
    <link rel="stylesheet" href="{{ asset('assets/css/users.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/list-actions.css') }}">
@endpush

@section('content')
    <div class="d-xl-flex justify-content-between align-items-start mb-4">
        <div>
            <h2 class="text-dark font-weight-bold mb-2">
                <i class="mdi mdi-account-tie me-2"></i>
                Profissionais
            </h2>
            <p class="text-muted mb-0">Gerencie os profissionais do sistema</p>
        </div>
        <a href="{{ route('profissionais.create') }}" class="btn btn-primary btn-icon-text">
            <i class="mdi mdi-plus me-2"></i> Novo Profissional
        </a>
    </div>

    <!-- Filtros -->
    @include('profissionais.components.profissional-filters')

    <!-- Lista de profissionais -->
    @include('profissionais.components.profissionais-table')

    @include('components.modals-profissionais')
@endsection
