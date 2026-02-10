@extends('layouts.app')

@section('title', 'Editar Produto')

@section('content')
<div class="d-xl-flex justify-content-between align-items-start mb-4">
    <div>
        <h2 class="text-dark font-weight-bold mb-2">
            <i class="mdi mdi-package-variant-edit me-2"></i>
            Editar Produto
        </h2>
        <p class="text-muted mb-0">Editar dados do produto</p>
    </div>
    <a href="{{ route('produtos.index') }}" class="btn btn-outline-secondary">
        <i class="mdi mdi-arrow-left me-2"></i>
        Voltar
    </a>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('produtos.update', $produto->id) }}" method="POST" id="produtoForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nome">Nome <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control @error('nome') is-invalid @enderror"
                                       id="nome"
                                       name="nome"
                                       value="{{ old('nome', $produto->nome) }}"
                                       placeholder="Nome do produto"
                                       maxlength="160"
                                       required>
                                @error('nome')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="categoria_id">Categoria <span class="text-danger">*</span></label>
                                <select class="form-select @error('categoria_id') is-invalid @enderror"
                                        id="categoria_id"
                                        name="categoria_id"
                                        required>
                                    <option value="">Selecione a categoria</option>
                                    @foreach($categorias as $categoria)
                                        <option value="{{ $categoria->id }}" {{ old('categoria_id', $produto->categoria_id) == $categoria->id ? 'selected' : '' }}>
                                            {{ $categoria->descricao }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('categoria_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="marca">Marca</label>
                                <input type="text"
                                       class="form-control @error('marca') is-invalid @enderror"
                                       id="marca"
                                       name="marca"
                                       value="{{ old('marca', $produto->marca) }}"
                                       placeholder="Marca"
                                       maxlength="100">
                                @error('marca')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="preco_custo">Preço de custo</label>
                                <input type="number"
                                       step="0.01"
                                       min="0"
                                       class="form-control @error('preco_custo') is-invalid @enderror"
                                       id="preco_custo"
                                       name="preco_custo"
                                       value="{{ old('preco_custo', $produto->preco_custo) }}"
                                       placeholder="0,00">
                                @error('preco_custo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="preco_venda">Preço de venda <span class="text-danger">*</span></label>
                                <input type="number"
                                       step="0.01"
                                       min="0"
                                       class="form-control @error('preco_venda') is-invalid @enderror"
                                       id="preco_venda"
                                       name="preco_venda"
                                       value="{{ old('preco_venda', $produto->preco_venda) }}"
                                       placeholder="0,00"
                                       required>
                                @error('preco_venda')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">Atributos do produto</label>
                            <div id="attributes-container">
                                @if($produto->atributos && is_array($produto->atributos))
                                    @foreach($produto->atributos as $key => $value)
                                        <div class="row mb-2">
                                            <div class="col-md-5">
                                                <input type="text" class="form-control" name="attr_keys[]" value="{{ $key }}" placeholder="Nome do atributo">
                                            </div>
                                            <div class="col-md-5">
                                                <input type="text" class="form-control" name="attr_values[]" value="{{ $value }}" placeholder="Valor do atributo">
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-outline-danger btn-remove-attribute">
                                                    <i class="mdi mdi-trash-can"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                                <div class="row mb-2">
                                    <div class="col-md-5">
                                        <input type="text" class="form-control" name="attr_keys[]" placeholder="Nome do atributo">
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" class="form-control" name="attr_values[]" placeholder="Valor do atributo">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-outline-primary btn-add-attribute">
                                            <i class="mdi mdi-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @include('produtos.partials.image-manager', ['product' => $produto])

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-check mt-3">
                                <label class="form-check-label">
                                    <input type="checkbox"
                                           class="form-check-input"
                                           id="ativo"
                                           name="ativo"
                                           value="1"
                                           {{ old('ativo', $produto->ativo) ? 'checked' : '' }}>
                                    Produto ativo <i class="input-helper"></i>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('produtos.index') }}" class="btn btn-outline-secondary">
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('attributes-container');
    if (!container) return;

    container.addEventListener('click', function(e) {
        if (e.target.closest('.btn-add-attribute')) {
            const newRow = document.createElement('div');
            newRow.className = 'row mb-2';
            newRow.innerHTML = `
                <div class="col-md-5">
                    <input type="text" class="form-control" name="attr_keys[]" placeholder="Nome do atributo">
                </div>
                <div class="col-md-5">
                    <input type="text" class="form-control" name="attr_values[]" placeholder="Valor do atributo">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-danger btn-remove-attribute">
                        <i class="mdi mdi-trash-can"></i>
                    </button>
                </div>
            `;
            container.appendChild(newRow);
        }
        if (e.target.closest('.btn-remove-attribute')) {
            e.target.closest('.row').remove();
        }
    });
});
</script>
@endpush
