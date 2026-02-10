@extends('layouts.app')

@section('title', 'Editar Categoria')

@section('content')
<div class="d-xl-flex justify-content-between align-items-start mb-4">
    <div>
        <h2 class="text-dark font-weight-bold mb-2">
            <i class="mdi mdi-tag-edit me-2"></i>
            Editar Categoria
        </h2>
        <p class="text-muted mb-0">Editar dados da categoria</p>
    </div>
    <a href="{{ route('categorias.index') }}" class="btn btn-outline-secondary">
        <i class="mdi mdi-arrow-left me-2"></i>
        Voltar
    </a>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('categorias.update', $categoria->id) }}" method="POST" id="categoriaForm">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="descricao">Descrição <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('descricao') is-invalid @enderror" 
                                       id="descricao" 
                                       name="descricao" 
                                       value="{{ old('descricao', $categoria->descricao) }}" 
                                       placeholder="Digite a descrição da categoria"
                                       required>
                                @error('descricao')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-check mt-3">
                                <label class="form-check-label">
                                    <input type="checkbox" 
                                           class="form-check-input" 
                                           id="ativo" 
                                           name="ativo" 
                                           value="1" 
                                           {{ old('ativo', $categoria->ativo) ? 'checked' : '' }}>
                                    Categoria ativa <i class="input-helper"></i>
                                </label>
                                <small class="form-text text-muted d-block">
                                    Desmarque para desativar a categoria sem excluí-la do sistema.
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="mdi mdi-information-outline me-2"></i>
                                <strong>Informação:</strong> Os campos marcados com <span class="text-danger">*</span> são obrigatórios.
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('categorias.index') }}" class="btn btn-outline-secondary">
                            <i class="mdi mdi-close me-2"></i>
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-check me-2"></i>
                            Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
