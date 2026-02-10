@extends('layouts.app')

@section('title', 'Visualizar Laboratório')

@section('content')
<div class="d-xl-flex justify-content-between align-items-start mb-4">
    <div>
        <h2 class="text-dark font-weight-bold mb-2">
            <i class="mdi mdi-flask me-2"></i>
            Visualizar Laboratório
        </h2>
        <p class="text-muted mb-0">Detalhes do laboratório</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('laboratorios.edit', $laboratorio->id) }}" class="btn btn-primary">
            <i class="mdi mdi-pencil me-2"></i>
            Editar
        </a>
        <a href="{{ route('laboratorios.index') }}" class="btn btn-outline-secondary">
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
                            <label class="text-muted">Razão social</label>
                            <p class="font-weight-medium">{{ $laboratorio->razao_social }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="text-muted">Nome fantasia</label>
                            <p class="font-weight-medium">{{ $laboratorio->nome_fantasia ?? '—' }}</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="text-muted">CNPJ</label>
                            <p class="font-weight-medium">{{ $laboratorio->cnpj_formatado }}</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="text-muted">Telefone</label>
                            <p class="font-weight-medium">{{ $laboratorio->telefone ?? '—' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="text-muted">E-mail</label>
                            <p class="font-weight-medium">{{ $laboratorio->email ?? '—' }}</p>
                        </div>
                    </div>
                </div>

                @if($laboratorio->chave_pix)
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="text-muted">Chave PIX</label>
                            <p class="font-weight-medium">{{ $laboratorio->chave_pix }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="text-muted">Status</label>
                            <div>
                                @if($laboratorio->ativo)
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
                            <p class="font-weight-medium">{{ $laboratorio->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                @if($laboratorio->updated_at && $laboratorio->updated_at != $laboratorio->created_at)
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="text-muted">Última Atualização</label>
                            <p class="font-weight-medium">{{ $laboratorio->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
