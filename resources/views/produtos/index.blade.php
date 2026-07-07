@extends('layouts.app')

@section('title', 'Produtos')

@push('plugin-css')
<<<<<<< HEAD
    <link rel="stylesheet" href="{{ asset('assets/css/users.css') }}">
=======
<link rel="stylesheet" href="{{ asset('assets/css/users.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/produtos.css') }}">
>>>>>>> 178aa6812636b10775c8bde9031eb9326f36f2bd
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
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('produtos.import') }}" class="btn btn-outline-primary btn-icon-text">
                <i class="mdi mdi-file-upload-outline me-2"></i> Importar
            </a>
            <a href="{{ route('produtos.create') }}" class="btn btn-primary btn-icon-text">
                <i class="mdi mdi-plus me-2"></i> Novo Produto
            </a>
        </div>
    </div>

    @include('produtos.components.produto-filters')
    @include('produtos.components.produtos-table')

    @include('components.modals-produtos')
@endsection
