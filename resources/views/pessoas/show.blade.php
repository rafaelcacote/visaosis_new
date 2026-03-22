@extends('layouts.app')

@section('title', $pessoa->nome . ' - Paciente')

@section('content')
<div class="page-show">
<div class="d-xl-flex justify-content-between align-items-start mb-4">
    <div>
        <h2 class="text-dark font-weight-bold mb-2">
            <i class="mdi mdi-account-circle-outline me-2"></i>
            Paciente
        </h2>
        <p class="text-muted mb-0">Detalhes do paciente</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('pessoas.edit', $pessoa) }}" class="btn btn-primary">
            <i class="mdi mdi-pencil-outline me-2"></i>
            Editar
        </a>
        <a href="{{ route('pessoas.index') }}" class="btn btn-outline-secondary">
            <i class="mdi mdi-arrow-left me-2"></i>
            Voltar
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Dados pessoais -->
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="mdi mdi-account-outline me-2"></i>
                    Dados Pessoais
                </h5>

                <div class="row">
                    <div class="col-md-7 mb-3">
                        <label class="text-muted small">Nome</label>
                        <div class="fw-bold">{{ $pessoa->nome }}</div>
                    </div>
                    <div class="col-md-5 mb-3">
                        <label class="text-muted small">Status</label>
                        <div>
                            @if($pessoa->ativo)
                                <span class="badge bg-success">
                                    <i class="mdi mdi-check-circle-outline me-1"></i>
                                    Ativo
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    <i class="mdi mdi-pause-circle-outline me-1"></i>
                                    Inativo
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="text-muted small">CPF</label>
                        <div>{{ $pessoa->cpf_formatado ?? 'Não informado' }}</div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="text-muted small">Sexo</label>
                        <div>{{ $pessoa->sexo_label }}</div>
                    </div>
                    <div class="col-md-5 mb-3">
                        <label class="text-muted small">Nascimento</label>
                        <div>
                            @if($pessoa->nascimento_em)
                                {{ $pessoa->nascimento_em->format('d/m/Y') }}
                                @if($pessoa->idade)
                                    <small class="text-muted">({{ $pessoa->idade }} anos)</small>
                                @endif
                            @else
                                Não informado
                            @endif
                        </div>
                    </div>
                </div>

                @if($pessoa->nome_mae || $pessoa->nome_pai)
                    <div class="row">
                        @if($pessoa->nome_mae)
                            <div class="col-md-7 mb-3">
                                <label class="text-muted small">Nome da Mãe</label>
                                <div>{{ $pessoa->nome_mae }}</div>
                            </div>
                        @endif
                        @if($pessoa->nome_pai)
                            <div class="col-md-5 mb-3">
                                <label class="text-muted small">Nome do Pai</label>
                                <div>{{ $pessoa->nome_pai }}</div>
                            </div>
                        @endif
                    </div>
                @endif

                @if($pessoa->deficiencia)
                    <div class="row">
                        <div class="col-md-12 mb-2">
                            <label class="text-muted small">Deficiência</label>
                            <div>{{ $pessoa->deficiencia }}</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Endereço -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="mdi mdi-map-marker-outline me-2"></i>
                    Endereço
                </h5>

                <div class="row">
                    <div class="col-md-7 mb-3">
                        <label class="text-muted small">Logradouro</label>
                        <div>{{ $pessoa->logradouro }}, {{ $pessoa->numero }}</div>
                    </div>
                    <div class="col-md-5 mb-3">
                        <label class="text-muted small">CEP</label>
                        <div>{{ $pessoa->cep_formatado ?? 'Não informado' }}</div>
                    </div>
                </div>

                @if($pessoa->complemento)
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="text-muted small">Complemento</label>
                            <div>{{ $pessoa->complemento }}</div>
                        </div>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-7 mb-3">
                        <label class="text-muted small">Bairro</label>
                        <div>{{ $pessoa->bairro }}</div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="text-muted small">Cidade</label>
                        <div>{{ $pessoa->localidade }}</div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="text-muted small">UF</label>
                        <div>{{ $pessoa->uf }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Contato -->
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="mdi mdi-phone-outline me-2"></i>
                    Contato
                </h5>

                @if($pessoa->telefone_formatado)
                    <div class="mb-3">
                        <label class="text-muted small">Telefone</label>
                        <div>{{ $pessoa->telefone_formatado }}</div>
                    </div>
                @endif

                @if($pessoa->email)
                    <div class="mb-2">
                        <label class="text-muted small">E-mail</label>
                        <div>{{ $pessoa->email }}</div>
                    </div>
                @endif

                @if(!$pessoa->telefone_formatado && !$pessoa->email)
                    <div class="text-muted">
                        Nenhuma informação de contato cadastrada.
                    </div>
                @endif
            </div>
        </div>

        <!-- Sistema -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="mdi mdi-information-outline me-2"></i>
                    Informações do Sistema
                </h5>

                <div class="mb-2">
                    <label class="text-muted small">Cadastrado em</label>
                    <div class="small">{{ $pessoa->created_at?->format('d/m/Y H:i') }}</div>
                </div>

                @if($pessoa->updated_at && $pessoa->updated_at != $pessoa->created_at)
                    <div class="mb-2">
                        <label class="text-muted small">Última atualização</label>
                        <div class="small">{{ $pessoa->updated_at->format('d/m/Y H:i') }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
</div>
@endsection

