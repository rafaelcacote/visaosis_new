@extends('layouts.app')

@section('title', 'Editar Profissional')

@section('content')
<div class="d-xl-flex justify-content-between align-items-start mb-4">
    <div>
        <h2 class="text-dark font-weight-bold mb-2">
            <i class="mdi mdi-account-edit me-2"></i>
            Editar Profissional
        </h2>
        <p class="text-muted mb-0">Editar dados do profissional</p>
    </div>
    <a href="{{ route('profissionais.index') }}" class="btn btn-outline-secondary">
        <i class="mdi mdi-arrow-left me-2"></i>
        Voltar
    </a>
</div>

@if (session('validation_message'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="mdi mdi-alert-circle me-2"></i>
        {{ session('validation_message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
@endif

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('profissionais.update', $profissional->id) }}" method="POST" id="profissionalForm">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="nome">Nome Completo <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('nome') is-invalid @enderror" 
                                       id="nome" 
                                       name="nome" 
                                       value="{{ old('nome', $profissional->nome) }}" 
                                       required>
                                @error('nome')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="cpf">CPF <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('cpf') is-invalid @enderror" 
                                       id="cpf" 
                                       name="cpf" 
                                       value="{{ old('cpf', $profissional->cpf_formatado) }}" 
                                       placeholder="000.000.000-00" 
                                       maxlength="14"
                                       required>
                                @error('cpf')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sexo">Sexo</label>
                                <select class="form-select @error('sexo') is-invalid @enderror" id="sexo" name="sexo">
                                    <option value="">Selecione</option>
                                    <option value="1" {{ old('sexo', $profissional->sexo) == '1' ? 'selected' : '' }}>Masculino</option>
                                    <option value="2" {{ old('sexo', $profissional->sexo) == '2' ? 'selected' : '' }}>Feminino</option>
                                </select>
                                @error('sexo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="nascimento_em">Data de Nascimento</label>
                                <input type="date" 
                                       class="form-control @error('nascimento_em') is-invalid @enderror" 
                                       id="nascimento_em" 
                                       name="nascimento_em" 
                                       value="{{ old('nascimento_em', $profissional->nascimento_em?->format('Y-m-d')) }}">
                                @error('nascimento_em')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="telefone">Telefone</label>
                                <input type="text" 
                                       class="form-control @error('telefone') is-invalid @enderror" 
                                       id="telefone" 
                                       name="telefone" 
                                       value="{{ old('telefone', $profissional->telefone_formatado) }}" 
                                       placeholder="(00) 00000-0000" 
                                       maxlength="15">
                                @error('telefone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="especialidade_id">Especialidade <span class="text-danger">*</span></label>
                                <select class="form-select @error('especialidade_id') is-invalid @enderror" 
                                        id="especialidade_id" 
                                        name="especialidade_id" 
                                        required>
                                    <option value="">Selecione uma especialidade</option>
                                    @foreach($especialidades as $especialidade)
                                        <option value="{{ $especialidade->id }}" 
                                                {{ old('especialidade_id', $profissional->especialidade_id) == $especialidade->id ? 'selected' : '' }}>
                                            {{ $especialidade->descricao }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('especialidade_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="registro_conselho">Registro do Conselho</label>
                                <input type="text" 
                                       class="form-control @error('registro_conselho') is-invalid @enderror" 
                                       id="registro_conselho" 
                                       name="registro_conselho" 
                                       value="{{ old('registro_conselho', $profissional->registro_conselho) }}" 
                                       placeholder="Ex: CRM 123456-SP">
                                @error('registro_conselho')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">E-mail</label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', $profissional->email) }}" 
                                       placeholder="exemplo@email.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="chave_pix">Chave PIX</label>
                                <input type="text" 
                                       class="form-control @error('chave_pix') is-invalid @enderror" 
                                       id="chave_pix" 
                                       name="chave_pix" 
                                       value="{{ old('chave_pix', $profissional->chave_pix) }}" 
                                       placeholder="CPF, e-mail, telefone ou chave aleatória">
                                @error('chave_pix')
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
                                           {{ old('ativo', $profissional->ativo) ? 'checked' : '' }}>
                                    Profissional ativo <i class="input-helper"></i>
                                </label>
                                <small class="form-text text-muted d-block">
                                    Desmarque para desativar o profissional sem excluí-lo do sistema.
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
                        <a href="{{ route('profissionais.index') }}" class="btn btn-outline-secondary">
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

{{-- Incluir scripts de validação e formatação --}}
@include('components.forms.cpf-script')
@include('components.forms.telefone-script')
