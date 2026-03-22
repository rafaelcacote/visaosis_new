@extends('layouts.app')

@section('title', 'Visualizar Categoria')

@section('content')
<div class="page-show">
<div class="d-xl-flex justify-content-between align-items-start mb-4">
    <div>
        <h2 class="text-dark font-weight-bold mb-2">
            <i class="mdi mdi-tag me-2"></i>
            Visualizar Categoria
        </h2>
        <p class="text-muted mb-0">Detalhes da categoria</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('categorias.edit', $categoria->id) }}" class="btn btn-primary">
            <i class="mdi mdi-pencil me-2"></i>
            Editar
        </a>
        <a href="{{ route('categorias.index') }}" class="btn btn-outline-secondary">
            <i class="mdi mdi-arrow-left me-2"></i>
            Voltar
        </a>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="text-muted">Descrição</label>
                            <p class="font-weight-medium">{{ $categoria->descricao }}</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="text-muted">Status</label>
                            <div>
                                @if($categoria->ativo)
                                    <span class="tag tag-status tag-status-ativo">
                                        <i class="mdi mdi-check-circle"></i>
                                        Ativo
                                    </span>
                                @else
                                    <span class="tag tag-status tag-status-inativo">
                                        <i class="mdi mdi-close-circle"></i>
                                        Inativo
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="text-muted">Data de Cadastro</label>
                            <p class="font-weight-medium">{{ $categoria->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                @if($categoria->updated_at && $categoria->updated_at != $categoria->created_at)
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="text-muted">Última Atualização</label>
                            <p class="font-weight-medium">{{ $categoria->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
</div>
@endsection
