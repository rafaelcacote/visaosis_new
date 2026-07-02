@extends('layouts.app')

@section('title', 'Visualizar Especialidade')

@section('content')
<div class="page-show">
<div class="d-xl-flex justify-content-between align-items-start mb-4">
    <div>
        <h2 class="text-dark font-weight-bold mb-2">
            <i class="mdi mdi-stethoscope me-2"></i>
            Visualizar Especialidade
        </h2>
        <p class="text-muted mb-0">Detalhes da especialidade</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('especialidades.edit', $especialidade->id) }}" class="btn btn-primary">
            <i class="mdi mdi-pencil me-2"></i>
            Editar
        </a>
        <a href="{{ route('especialidades.index') }}" class="btn btn-outline-secondary">
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
                            <p class="font-weight-medium">{{ $especialidade->descricao }}</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="text-muted">Data de Cadastro</label>
                            <p class="font-weight-medium">{{ $especialidade->created_at?->format('d/m/Y H:i') ?? '-' }}</p>
                        </div>
                    </div>
                    @if($especialidade->updated_at && $especialidade->updated_at != $especialidade->created_at)
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="text-muted">Última Atualização</label>
                            <p class="font-weight-medium">{{ $especialidade->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
