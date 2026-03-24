@extends('layouts.app')

@section('title', 'Visualizar Produto')

@section('content')
<div class="page-show">
<div class="d-xl-flex justify-content-between align-items-start mb-4">
    <div>
        <h2 class="text-dark font-weight-bold mb-2">
            <i class="mdi mdi-package-variant me-2"></i>
            Visualizar Produto
        </h2>
        <p class="text-muted mb-0">Detalhes do produto</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('produtos.edit', $produto->id) }}" class="btn btn-primary">
            <i class="mdi mdi-pencil me-2"></i>
            Editar
        </a>
        <a href="{{ route('produtos.index') }}" class="btn btn-outline-secondary">
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
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="text-muted">Nome</label>
                            <p class="font-weight-medium">{{ $produto->nome }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="text-muted">Categoria</label>
                            <p class="font-weight-medium">{{ $produto->categoria ? $produto->categoria->descricao : '—' }}</p>
                        </div>
                    </div>
                </div>

                @if($produto->marca)
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="text-muted">Marca</label>
                            <p class="font-weight-medium">{{ $produto->marca }}</p>
                        </div>
                    </div>
                </div>
                @endif

                @php
                    $imagens = $produto->images()
                        ->whereNull('deleted_at')
                        ->orderByDesc('principal')
                        ->orderBy('ordem')
                        ->orderBy('id')
                        ->get();
                @endphp

                @if($imagens->isNotEmpty())
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="form-group">
                            <label class="text-muted d-block mb-3">Imagens do produto</label>
                            <div class="row g-3">
                                @foreach($imagens as $imagem)
                                    @php
                                        $url = null;
                                        if ($imagem->caminho_arquivo && \Illuminate\Support\Facades\Storage::disk('public')->exists($imagem->caminho_arquivo)) {
                                            $url = asset('storage/' . ltrim($imagem->caminho_arquivo, '/'));
                                        }
                                    @endphp
                                    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                        <div class="card h-100 shadow-sm position-relative">
                                            @if($imagem->principal)
                                                <span class="badge bg-primary position-absolute" style="top: 0.75rem; left: 0.75rem; z-index: 2;">
                                                    <i class="mdi mdi-star me-1"></i> Principal
                                                </span>
                                            @endif
                                            @if($url)
                                                <img src="{{ $url }}" class="card-img-top" alt="{{ $imagem->nome_arquivo }}">
                                            @else
                                                <div class="d-flex align-items-center justify-content-center bg-light" style="height: 180px;">
                                                    <span class="text-muted small">Pré-visualização indisponível</span>
                                                </div>
                                            @endif
                                            <div class="card-body">
                                                <h6 class="card-title text-truncate mb-1" title="{{ $imagem->nome_arquivo }}">
                                                    {{ $imagem->nome_arquivo }}
                                                </h6>
                                                <p class="text-muted small mb-1">
                                                    Ordem: {{ $imagem->ordem ?? 0 }}
                                                </p>
                                                <p class="text-muted small mb-0">
                                                    Status:
                                                    @if($imagem->ativo)
                                                        <span class="text-success">Ativa</span>
                                                    @else
                                                        <span class="text-muted">Inativa</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="text-muted">Preço de custo</label>
                            <p class="font-weight-medium">{{ $produto->preco_custo_formatado }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="text-muted">Preço de venda</label>
                            <p class="font-weight-medium">{{ $produto->preco_venda_formatado }}</p>
                        </div>
                    </div>
                </div>

                @if($produto->atributos && count($produto->atributos) > 0)
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="text-muted">Atributos</label>
                            <ul class="list-unstyled mb-0">
                                @foreach($produto->atributos as $key => $value)
                                    <li><strong>{{ $key }}:</strong> {{ $value }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="text-muted">Status</label>
                            <div>
                                @if($produto->ativo)
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
                            <label class="text-muted">Data de cadastro</label>
                            <p class="font-weight-medium">{{ $produto->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                @if($produto->updated_at && $produto->updated_at != $produto->created_at)
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="text-muted">Última atualização</label>
                            <p class="font-weight-medium">{{ $produto->updated_at->format('d/m/Y H:i') }}</p>
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
