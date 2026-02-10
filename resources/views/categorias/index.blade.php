@extends('layouts.app')

@section('title', 'Categorias')

@push('plugin-css')
<link rel="stylesheet" href="{{ asset('assets/css/users.css') }}">
@endpush

@section('content')
<div class="d-xl-flex justify-content-between align-items-start mb-4">
    <div>
        <h2 class="text-dark font-weight-bold mb-2">
            <i class="mdi mdi-tag-multiple me-2"></i>
            Categorias
        </h2>
        <p class="text-muted mb-0">Gerencie as categorias do sistema</p>
    </div>
    <a href="{{ route('categorias.create') }}" class="btn btn-primary btn-icon-text">
        <i class="mdi mdi-plus me-2"></i> Nova Categoria
    </a>
</div>

<!-- Filtros -->
@include('categorias.components.categoria-filters')

<!-- Lista de categorias -->
@include('categorias.components.categorias-table')

@include('components.modals-categorias')
@endsection
