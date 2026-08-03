@extends('layouts.app')

@section('title', 'Especialidades')

@push('plugin-css')
    <link rel="stylesheet" href="{{ asset('assets/css/users.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/list-actions.css') }}">
@endpush

@section('content')
    <div class="d-xl-flex justify-content-between align-items-start mb-4">
        <div>
            <h2 class="text-dark font-weight-bold mb-2">
                <i class="mdi mdi-stethoscope me-2"></i>
                Especialidades
            </h2>
            <p class="text-muted mb-0">Gerencie as especialidades do sistema</p>
        </div>
        <a href="{{ route('especialidades.create') }}" class="btn btn-primary btn-icon-text">
            <i class="mdi mdi-plus me-2"></i> Nova Especialidade
        </a>
    </div>

    @include('especialidades.components.especialidade-filters')

    @include('especialidades.components.especialidades-table')

    @include('components.modals-especialidades')
@endsection
