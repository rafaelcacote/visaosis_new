@extends('layouts.app')

@section('title', 'Novo Paciente')

@section('content')
<div class="d-xl-flex justify-content-between align-items-start mb-4">
    <div>
        <h2 class="text-dark font-weight-bold mb-2">
            <i class="mdi mdi-account-plus-outline me-2"></i>
            Novo Paciente
        </h2>
        <p class="text-muted mb-0">Cadastrar um novo paciente no sistema</p>
    </div>
    <a href="{{ route('pessoas.index') }}" class="btn btn-outline-secondary">
        <i class="mdi mdi-arrow-left me-2"></i>
        Voltar
    </a>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('pessoas.store') }}" method="POST" id="pessoaForm">
                    @csrf

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="nome">Nome Completo <span class="text-danger">*</span></label>
                                <input
                                    type="text"
                                    class="form-control @error('nome') is-invalid @enderror"
                                    id="nome"
                                    name="nome"
                                    value="{{ old('nome') }}"
                                    required
                                >
                                @error('nome')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="cpf">CPF <span class="text-danger">*</span></label>
                                <input
                                    type="text"
                                    class="form-control @error('cpf') is-invalid @enderror"
                                    id="cpf"
                                    name="cpf"
                                    value="{{ old('cpf') }}"
                                    placeholder="000.000.000-00"
                                    maxlength="14"
                                    required
                                >
                                @error('cpf')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nome_mae">Nome da Mãe</label>
                                <input
                                    type="text"
                                    class="form-control @error('nome_mae') is-invalid @enderror"
                                    id="nome_mae"
                                    name="nome_mae"
                                    value="{{ old('nome_mae') }}"
                                >
                                @error('nome_mae')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nome_pai">Nome do Pai</label>
                                <input
                                    type="text"
                                    class="form-control @error('nome_pai') is-invalid @enderror"
                                    id="nome_pai"
                                    name="nome_pai"
                                    value="{{ old('nome_pai') }}"
                                >
                                @error('nome_pai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="sexo">Sexo</label>
                                <select
                                    class="form-select @error('sexo') is-invalid @enderror"
                                    id="sexo"
                                    name="sexo"
                                >
                                    <option value="">Selecione</option>
                                    <option value="1" {{ old('sexo') == '1' ? 'selected' : '' }}>Masculino</option>
                                    <option value="2" {{ old('sexo') == '2' ? 'selected' : '' }}>Feminino</option>
                                </select>
                                @error('sexo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="nascimento_em">Data de Nascimento</label>
                                <input
                                    type="date"
                                    class="form-control @error('nascimento_em') is-invalid @enderror"
                                    id="nascimento_em"
                                    name="nascimento_em"
                                    value="{{ old('nascimento_em') }}"
                                >
                                @error('nascimento_em')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="deficiencia">Deficiência</label>
                                <input
                                    type="text"
                                    class="form-control @error('deficiencia') is-invalid @enderror"
                                    id="deficiencia"
                                    name="deficiencia"
                                    value="{{ old('deficiencia') }}"
                                    placeholder="Descrever se houver"
                                >
                                @error('deficiencia')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="cep">CEP</label>
                                <input
                                    type="text"
                                    class="form-control @error('cep') is-invalid @enderror"
                                    id="cep"
                                    name="cep"
                                    value="{{ old('cep') }}"
                                    placeholder="00000-000"
                                    maxlength="9"
                                >
                                @error('cep')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="logradouro">Logradouro <span class="text-danger">*</span></label>
                                <input
                                    type="text"
                                    class="form-control @error('logradouro') is-invalid @enderror"
                                    id="logradouro"
                                    name="logradouro"
                                    value="{{ old('logradouro') }}"
                                    placeholder="Rua, Avenida, etc."
                                    required
                                >
                                @error('logradouro')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <label for="numero">Número <span class="text-danger">*</span></label>
                                <input
                                    type="text"
                                    class="form-control @error('numero') is-invalid @enderror"
                                    id="numero"
                                    name="numero"
                                    value="{{ old('numero') }}"
                                    required
                                >
                                @error('numero')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="bairro">Bairro <span class="text-danger">*</span></label>
                                <input
                                    type="text"
                                    class="form-control @error('bairro') is-invalid @enderror"
                                    id="bairro"
                                    name="bairro"
                                    value="{{ old('bairro') }}"
                                    required
                                >
                                @error('bairro')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="complemento">Complemento</label>
                                <input
                                    type="text"
                                    class="form-control @error('complemento') is-invalid @enderror"
                                    id="complemento"
                                    name="complemento"
                                    value="{{ old('complemento') }}"
                                    placeholder="Apto, casa, etc."
                                >
                                @error('complemento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="localidade">Cidade <span class="text-danger">*</span></label>
                                <input
                                    type="text"
                                    class="form-control @error('localidade') is-invalid @enderror"
                                    id="localidade"
                                    name="localidade"
                                    value="{{ old('localidade') }}"
                                    required
                                >
                                @error('localidade')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <label for="uf">UF <span class="text-danger">*</span></label>
                                <input
                                    type="text"
                                    class="form-control @error('uf') is-invalid @enderror"
                                    id="uf"
                                    name="uf"
                                    value="{{ old('uf') }}"
                                    maxlength="2"
                                    required
                                >
                                @error('uf')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="telefone">Telefone</label>
                                <input
                                    type="text"
                                    class="form-control @error('telefone') is-invalid @enderror"
                                    id="telefone"
                                    name="telefone"
                                    value="{{ old('telefone') }}"
                                    placeholder="(00) 00000-0000"
                                    maxlength="15"
                                >
                                @error('telefone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="form-group">
                                <label for="email">E-mail</label>
                                <input
                                    type="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    id="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    placeholder="exemplo@email.com"
                                >
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="mdi mdi-information-outline me-2"></i>
                                <strong>Informação:</strong> Os campos marcados com
                                <span class="text-danger">*</span> são obrigatórios.
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <a href="{{ route('pessoas.index') }}" class="btn btn-outline-secondary">
                            <i class="mdi mdi-close me-2"></i>
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-check me-2"></i>
                            Cadastrar Paciente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

