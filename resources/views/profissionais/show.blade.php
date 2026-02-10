@extends('layouts.app')

@section('title', 'Detalhes do Profissional')

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">Detalhes do Profissional</h3>
        <nav aria-label="breadcrumb">
            <div class="d-flex">
                <a href="{{ route('profissionais.index') }}" class="btn btn-outline-secondary btn-sm me-2">
                    <i class="mdi mdi-arrow-left"></i> Voltar
                </a>
                <a href="{{ route('profissionais.edit', $profissional->id) }}" class="btn btn-primary btn-sm">
                    <i class="mdi mdi-pencil"></i> Editar
                </a>
            </div>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="img-lg rounded-circle bg-primary d-flex align-items-center justify-content-center mx-auto" style="width: 100px; height: 100px;">
                            <h2 class="text-white mb-0">{{ strtoupper(substr($profissional->nome, 0, 1)) }}</h2>
                        </div>
                    </div>
                    <h4>{{ $profissional->nome }}</h4>
                    @if($profissional->especialidade)
                        <p class="text-muted">{{ $profissional->especialidade->descricao }}</p>
                    @endif
                    @if($profissional->ativo)
                        <span class="badge badge-success">Ativo</span>
                    @else
                        <span class="badge badge-danger">Inativo</span>
                    @endif

                    <hr>

                    <div class="text-start mt-4">
                        @if($profissional->email)
                            <p class="text-muted mb-2"><i class="mdi mdi-email-outline me-2 text-primary"></i>{{ $profissional->email }}</p>
                        @endif
                        @if($profissional->telefone_formatado)
                            <p class="text-muted mb-2"><i class="mdi mdi-phone me-2 text-primary"></i>{{ $profissional->telefone_formatado }}</p>
                        @endif
                        @if($profissional->chave_pix)
                            <p class="text-muted mb-0"><i class="mdi mdi-qrcode me-2 text-primary"></i>{{ $profissional->chave_pix }}</p>
                        @endif
                        @if(!$profissional->email && !$profissional->telefone_formatado && !$profissional->chave_pix)
                            <p class="text-muted mb-0 small">Nenhuma informação de contato cadastrada.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-primary"><i class="mdi mdi-account-card-details me-2"></i>Informações Completas</h4>
                    <p class="card-description">Dados cadastrais do profissional no sistema</p>

                    <div class="row mt-4">
                        <div class="col-sm-6 mb-3">
                            <label class="text-muted d-block">CPF</label>
                            <span class="font-weight-bold">{{ $profissional->cpf_formatado ?: 'Não informado' }}</span>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="text-muted d-block">Especialidade</label>
                            <span class="font-weight-bold">{{ $profissional->especialidade->descricao ?? 'Não informado' }}</span>
                        </div>
                        @if($profissional->sexo)
                        <div class="col-sm-6 mb-3">
                            <label class="text-muted d-block">Sexo</label>
                            <span class="font-weight-bold">{{ $profissional->sexo_texto }}</span>
                        </div>
                        @endif
                        @if($profissional->nascimento_em)
                        <div class="col-sm-6 mb-3">
                            <label class="text-muted d-block">Data de Nascimento</label>
                            <span class="font-weight-bold">{{ $profissional->nascimento_em->format('d/m/Y') }}@if($profissional->idade) ({{ $profissional->idade }} anos)@endif</span>
                        </div>
                        @endif
                        @if($profissional->registro_conselho)
                        <div class="col-sm-6 mb-3">
                            <label class="text-muted d-block">Registro do Conselho</label>
                            <span class="font-weight-bold">{{ $profissional->registro_conselho }}</span>
                        </div>
                        @endif
                    </div>

                    <hr>

                    <h4 class="card-title text-muted mt-4"><i class="mdi mdi-clock-outline me-2"></i>Histórico do Registro</h4>
                    <div class="row mt-3">
                        <div class="col-sm-6 mb-3">
                            <label class="text-muted d-block small">Criado em</label>
                            <span class="font-weight-bold">
                                <i class="mdi mdi-calendar-plus me-1 text-muted"></i>
                                {{ $profissional->created_at ? $profissional->created_at->format('d/m/Y') : 'N/A' }}
                                @if($profissional->created_at)
                                    <small class="text-muted">às {{ $profissional->created_at->format('H:i') }}</small>
                                @endif
                            </span>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="text-muted d-block small">Última atualização</label>
                            <span class="font-weight-bold">
                                <i class="mdi mdi-calendar-edit me-1 text-muted"></i>
                                {{ $profissional->updated_at ? $profissional->updated_at->format('d/m/Y') : 'N/A' }}
                                @if($profissional->updated_at)
                                    <small class="text-muted">às {{ $profissional->updated_at->format('H:i') }}</small>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.badge-success {
    border: 1px solid #44ce42;
    color: #ffffff;
    background: #44ce42;
}

.badge-danger {
    border: 1px solid #fc5a5a;
    color: #ffffff;
    background: #fc5a5a;
}
</style>
@endpush
@endsection
