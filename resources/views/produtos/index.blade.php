@extends('layouts.app')

@section('title', 'Produtos')

@push('plugin-css')
<link rel="stylesheet" href="{{ asset('assets/css/users.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/produtos.css') }}">
@endpush

@section('content')
<div class="d-xl-flex justify-content-between align-items-start mb-4">
    <div>
        <h2 class="text-dark font-weight-bold mb-2">
            <i class="mdi mdi-package-variant me-2"></i>
            Produtos
        </h2>
        <p class="text-muted mb-0">Gerencie os produtos do sistema</p>
    </div>
    <a href="{{ route('produtos.create') }}" class="btn btn-primary btn-icon-text">
        <i class="mdi mdi-plus me-2"></i> Novo Produto
    </a>
</div>

@include('produtos.components.produto-filters')
@include('produtos.components.produtos-table')

@include('components.modals-produtos')
@endsection
