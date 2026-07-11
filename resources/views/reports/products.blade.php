@extends('layouts.app')

@section('title', 'Relatório de Produtos - VisaoSis')
@section('page-title', 'Relatório de Produtos')

@push('plugin-css')
    <link rel="stylesheet" href="{{ asset('assets/css/users.css') }}">
@endpush

@section('page-actions')
    <div class="d-flex gap-2">
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
            <i class="mdi mdi-arrow-left me-2"></i>
            Voltar
        </a>
    </div>
@endsection

@section('content')
    <div class="row">

        <!-- Filtros -->
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-filter text-primary me-2"></i>
                        Filtros
                    </h5>
                </div>
                <div class="card-body">
                    <form id="filterForm" method="GET" action="{{ route('reports.products') }}"
                        class="row g-3 align-items-end">
                        <div class="col-12 col-sm-6 col-md-4">
                            <label for="search" class="form-label">Buscar</label>
                            <input type="text" class="form-control form-control-sm" id="search" name="search"
                                placeholder="Nome ou marca..." value="{{ $search }}">
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <label for="categoria_id" class="form-label">Categoria</label>
                            <select class="form-select form-select-sm" id="categoria_id" name="categoria_id">
                                <option value="">Todas as Categorias</option>
                                @foreach ($categorias as $cat)
                                    <option value="{{ $cat->id }}" @selected($categoriaId == $cat->id)>
                                        {{ $cat->descricao }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select form-select-sm" id="status" name="status">
                                <option value="">Todos</option>
                                <option value="1" @selected($status === '1')>Ativo</option>
                                <option value="0" @selected($status === '0')>Inativo</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="mdi mdi-magnify me-1"></i>
                                Filtrar
                            </button>
                            <a href="{{ route('reports.products') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="mdi mdi-filter-off me-1"></i>
                                Limpar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Resumo Estatístico -->
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 border-end">
                            <h2 class="mb-1 text-primary">{{ $stats['total'] }}</h2>
                            <p class="text-muted mb-0">Total de Produtos</p>
                        </div>
                        <div class="col-md-3 border-end">
                            <h2 class="mb-1 text-success">{{ $stats['ativos'] }}</h2>
                            <p class="text-muted mb-0">Ativos</p>
                        </div>
                        <div class="col-md-3 border-end">
                            <h2 class="mb-1 text-danger">{{ $stats['inativos'] }}</h2>
                            <p class="text-muted mb-0">Inativos</p>
                        </div>
                        <div class="col-md-3">
                            <h2 class="mb-1 text-info">{{ $stats['com_atributos'] }}</h2>
                            <p class="text-muted mb-0">Com Atributos</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabela de Produtos -->
        <div class="col-md-9 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-package-variant text-primary me-2"></i>
                        Lista de Produtos
                    </h5>
                    <span class="tag" style="background-color: #e0f0ff; color: #1d7dd6;">
                        {{ $stats['total'] }} produto(s)
                    </span>
                </div>
                <div class="card-body p-0">
                    @if ($produtos->isEmpty())
                        <div class="text-center py-5">
                            <i class="mdi mdi-package-variant-closed text-muted" style="font-size: 3rem;"></i>
                            <h5 class="mt-3 text-muted">Nenhum produto encontrado</h5>
                            <p class="text-muted mb-3">Tente ajustar os filtros de busca.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>

                                        <th>Nome</th>
                                        <th>Marca</th>
                                        <th>Categoria</th>
                                        <th>Preço Custo</th>
                                        <th>Preço Venda</th>
                                        <th>Atributos</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($produtos as $produto)
                                        <tr>

                                            <td>
                                                <h6 class="mb-0">{{ $produto->nome }}</h6>
                                            </td>
                                            <td>{{ $produto->marca ?? '—' }}</td>
                                            <td>{{ $produto->categoria->descricao ?? '—' }}</td>
                                            <td>{{ $produto->preco_custo_formatado }}</td>
                                            <td>{{ $produto->preco_venda_formatado }}</td>
                                            <td>
                                                @if (!empty($produto->atributos))
                                                    <span class="tag" style="background-color: #e0f0ff; color: #1d7dd6;">
                                                        {{ count($produto->atributos) }} atrib.
                                                    </span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($produto->ativo)
                                                    <span class="tag tag-status tag-status-ativo">
                                                        <i class="mdi mdi-check-circle"></i> Ativo
                                                    </span>
                                                @else
                                                    <span class="tag tag-status tag-status-inativo">
                                                        <i class="mdi mdi-close-circle"></i> Inativo
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                        @if (!empty($produto->atributos))
                                            <tr class="table-secondary">
                                                <td colspan="8" class="py-2 px-4">

                                                    <table class="table table-sm table-bordered mb-0 mt-1"
                                                        style="max-width: 500px;">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th class="py-1">Atributo</th>
                                                                <th class="py-1">Valor</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($produto->atributos as $key => $value)
                                                                <tr>
                                                                    <td class="py-1">{{ $key }}</td>
                                                                    <td class="py-1">{{ $value }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Painel Lateral -->
        <div class="col-md-3 mb-4">
            <!-- Resumo por Categoria -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-shape text-primary me-2"></i>
                        Por Categoria
                    </h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @php
                            // Agrupa por descrição da categoria e ordena as chaves alfabeticamente A-Z
                            $groups = $produtos->groupBy(fn($p) => $p->categoria->descricao ?? 'Sem Categoria')->sortKeys();
                        @endphp
                        @forelse ($groups as $catNome => $grupo)
                            <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                <small>{{ $catNome }}</small>
                                <span class="tag" style="background-color: #e0f0ff; color: #1d7dd6;">
                                    {{ $grupo->count() }}
                                </span>
                            </li>
                        @empty
                            <li class="list-group-item text-muted text-center py-3">Nenhum dado</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <!-- Ações -->
            <div class="card">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @php
                            // Preserva valores como '0' (inativo). Remover apenas null/''
                            $raw = [
                                'search' => $search ?? null,
                                'categoria_id' => $categoriaId ?? null,
                                'status' => $status ?? null,
                            ];
                            $filtered = array_filter($raw, function ($v) {
                                return $v !== null && $v !== '';
                            });
                            $params = http_build_query($filtered);
                            $pdfUrl = route('reports.products.pdf') . ($params ? '?' . $params : '');
                        @endphp
                        <a href="{{ $pdfUrl }}" class="btn btn-primary" target="_blank">
                            <i class="mdi mdi-file-pdf me-2"></i>Imprimir Relatório
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('styles')
    <style>
        .table-secondary td {
            background-color: #f8f9fa;
        }
    </style>
@endpush
