@extends('layouts.app')

@section('title', 'Pacientes')

@push('plugin-css')
<link rel="stylesheet" href="{{ asset('assets/css/users.css') }}">
@endpush

@section('content')
<div class="d-xl-flex justify-content-between align-items-start mb-4">
    <div>
        <h2 class="text-dark font-weight-bold mb-2">
            <i class="mdi mdi-account-multiple-outline me-2"></i>
            Pacientes
        </h2>
        <p class="text-muted mb-0">Gerencie os pacientes do sistema</p>
    </div>
    <a href="{{ route('pessoas.create') }}" class="btn btn-primary btn-icon-text">
        <i class="mdi mdi-plus me-2"></i> Novo Paciente
    </a>
</div>

<!-- Filtros -->
@include('pessoas.components.pessoa-filters')

<!-- Lista de pacientes -->
@include('pessoas.components.pessoas-table')

@include('components.modals-pessoas')
@endsection

