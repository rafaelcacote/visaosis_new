@extends('layouts.app')

@section('title', 'Importar Produtos')

@section('content')
    <div class="d-xl-flex justify-content-between align-items-start mb-4">
        <div>
            <h2 class="text-dark font-weight-bold mb-2">
                <i class="mdi mdi-file-upload-outline me-2"></i>
                Importar Produtos
            </h2>
            <p class="text-muted mb-0">Importe produtos a partir de uma planilha (XLSX/XLS/CSV)</p>
        </div>
        <a href="{{ route('produtos.index') }}" class="btn btn-outline-secondary">
            <i class="mdi mdi-arrow-left me-2"></i>
            Voltar
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="mdi mdi-database-import-outline me-2"></i>
                Dados da Importação
            </h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('produtos.import.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Categoria</label>
                        <select class="form-select @error('categoria_id') is-invalid @enderror" name="categoria_id" required>
                            <option value="">Selecione</option>
                            @foreach ($categorias as $cat)
                                <option value="{{ $cat->id }}" {{ old('categoria_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->descricao }}
                                </option>
                            @endforeach
                        </select>
                        @error('categoria_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Planilha</label>
                        <input type="file" class="form-control @error('arquivo') is-invalid @enderror" name="arquivo"
                            accept=".xlsx,.xls,.csv,.txt" required>
                        @error('arquivo')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="alert alert-info mt-3 mb-0">
                    <div class="fw-semibold mb-2">Formato esperado da planilha</div>
                    <div class="small">
                        1ª coluna: Nome | 2ª coluna: Marca | 3ª coluna: Preço de Custo | 4ª coluna: Preço de Venda
                        <br>
                        A partir da 5ª coluna: Atributos (o título da coluna será o nome do atributo)
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-3">
                    <a href="{{ route('produtos.index') }}" class="btn btn-outline-secondary">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-upload me-2"></i>
                        Importar
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

