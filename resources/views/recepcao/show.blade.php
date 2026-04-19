@extends('layouts.app')

@section('title', 'Consulta #' . $consulta->id)

@section('content')
    <div class="page-show">
        <div class="d-xl-flex justify-content-between align-items-start mb-4">
            <div>
                <h2 class="text-dark font-weight-bold mb-2">
                    <i class="mdi mdi-clipboard-text me-2"></i>
                    Consulta #{{ $consulta->id }}
                </h2>
                <p class="text-muted mb-0">Detalhes da consulta</p>
            </div>
            <a href="{{ route('recepcao.index') }}" class="btn btn-outline-secondary">
                <i class="mdi mdi-arrow-left me-2"></i>
                Voltar
            </a>
        </div>

        <div class="row">
            <div class="col-md-8">
                <!-- Informações do Paciente -->
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="mdi mdi-account-circle me-2"></i>
                            Dados do Paciente
                        </h5>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="text-muted small">Nome</label>
                                <div class="fw-bold">{{ $consulta->paciente->nome ?? 'Não informado' }}</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="text-muted small">CPF</label>
                                <div>{{ $consulta->paciente->cpf_formatado ?? 'Não informado' }}</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="text-muted small">Data de Nascimento</label>
                                <div>
                                    @if ($consulta->paciente && $consulta->paciente->nascimento_em)
                                        {{ $consulta->paciente->nascimento_em->format('d/m/Y') }}
                                        @php
                                            $idade = $consulta->paciente->idade;
                                        @endphp
                                        @if ($idade !== null)
                                            <small class="text-muted">({{ $idade }} anos)</small>
                                        @endif
                                    @else
                                        Não informado
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="text-muted small">Telefone</label>
                                <div>
                                    @if ($consulta->paciente->telefone)
                                        {{ $consulta->paciente->telefone_formatado }}
                                    @else
                                        Não informado
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="text-muted small">E-mail</label>
                                <div>
                                    @if ($consulta->paciente->email)
                                        {{ $consulta->paciente->email }}
                                    @else
                                        Não informado
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informações da Consulta -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="mdi mdi-calendar-check me-2"></i>
                            Dados da Consulta
                        </h5>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="text-muted small">Status</label>
                                <div>{{ $consulta->status_label }}</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="text-muted small">Tipo</label>
                                <div>{{ $consulta->tipo_label }}</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="text-muted small">Prioridade</label>
                                <div>{{ $consulta->prioridade_label }}</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="text-muted small">Profissional</label>
                                <div>{{ $consulta->profissional->nome ?? 'Não informado' }}</div>
                                @if ($consulta->profissional && $consulta->profissional->especialidade)
                                    <small
                                        class="text-muted">{{ $consulta->profissional->especialidade->descricao }}</small>
                                @endif
                            </div>
                            @if ($consulta->atendido_em)
                                <div class="col-md-4 mb-3">
                                    <label class="text-muted small">Atendido em</label>
                                    <div>{{ $consulta->atendido_em->format('d/m/Y H:i') }}</div>
                                </div>
                            @endif
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="text-muted small">Agendado em</label>
                                <div>
                                    @if ($consulta->agendado_em)
                                        {{ $consulta->agendado_em->format('d/m/Y H:i') }}
                                    @else
                                        Não informado
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="text-muted small">Chegada</label>
                                <div>
                                    @if ($consulta->chegada_em)
                                        {{ $consulta->chegada_em->format('d/m/Y H:i') }}
                                    @else
                                        Não informado
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if ($consulta->observacoes)
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="text-muted small">Observações</label>
                                    <div>{{ $consulta->observacoes }}</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Informações do Sistema -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="mdi mdi-information-outline me-2"></i>
                            Informações do Sistema
                        </h5>

                        <div class="mb-2">
                            <label class="text-muted small">Cadastrado em</label>
                            <div class="small">{{ $consulta->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                        @if ($consulta->updated_at != $consulta->created_at)
                            <div class="mb-2">
                                <label class="text-muted small">Última atualização</label>
                                <div class="small">{{ $consulta->updated_at->format('d/m/Y H:i') }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
