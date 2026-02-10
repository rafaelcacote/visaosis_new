@extends('layouts.app')

@section('title', 'Laboratórios')

@push('plugin-css')
<link rel="stylesheet" href="{{ asset('assets/css/users.css') }}">
@endpush

@section('content')
<div class="d-xl-flex justify-content-between align-items-start mb-4">
    <div>
        <h2 class="text-dark font-weight-bold mb-2">
            <i class="mdi mdi-flask-outline me-2"></i>
            Laboratórios
        </h2>
        <p class="text-muted mb-0">Gerencie os laboratórios do sistema</p>
    </div>
    <a href="{{ route('laboratorios.create') }}" class="btn btn-primary btn-icon-text">
        <i class="mdi mdi-plus me-2"></i> Novo Laboratório
    </a>
</div>

@include('laboratorios.components.laboratorio-filters')
@include('laboratorios.components.laboratorios-table')

@include('components.modals-laboratorios')
@endsection
