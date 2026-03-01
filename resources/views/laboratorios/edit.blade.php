@extends('layouts.app')

@section('title', 'Editar Laboratório')

@section('content')
<div class="d-xl-flex justify-content-between align-items-start mb-4">
    <div>
        <h2 class="text-dark font-weight-bold mb-2">
            <i class="mdi mdi-flask-edit me-2"></i>
            Editar Laboratório
        </h2>
        <p class="text-muted mb-0">Editar dados do laboratório</p>
    </div>
    <a href="{{ route('laboratorios.index') }}" class="btn btn-outline-secondary">
        <i class="mdi mdi-arrow-left me-2"></i>
        Voltar
    </a>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('laboratorios.update', $laboratorio->id) }}" method="POST" id="laboratorioForm">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="cnpj">CNPJ <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control @error('cnpj') is-invalid @enderror"
                                       id="cnpj"
                                       name="cnpj"
                                       value="{{ old('cnpj', $laboratorio->cnpj_formatado) }}"
                                       placeholder="00.000.000/0001-00"
                                       maxlength="14"
                                       required>
                                @error('cnpj')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="razao_social">Razão social <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control @error('razao_social') is-invalid @enderror"
                                       id="razao_social"
                                       name="razao_social"
                                       value="{{ old('razao_social', $laboratorio->razao_social) }}"
                                       placeholder="Razão social"
                                       required>
                                @error('razao_social')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nome_fantasia">Nome fantasia <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control @error('nome_fantasia') is-invalid @enderror"
                                       id="nome_fantasia"
                                       name="nome_fantasia"
                                       value="{{ old('nome_fantasia', $laboratorio->nome_fantasia) }}"
                                       placeholder="Nome fantasia"
                                       required>
                                @error('nome_fantasia')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="telefone">Telefone</label>
                                <input type="text"
                                       class="form-control phone-mask @error('telefone') is-invalid @enderror"
                                       id="telefone"
                                       name="telefone"
                                       value="{{ old('telefone', $laboratorio->telefone) }}"
                                       placeholder="(00) 00000-0000">
                                @error('telefone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">E-mail</label>
                                <input type="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       id="email"
                                       name="email"
                                       value="{{ old('email', $laboratorio->email) }}"
                                       placeholder="email@laboratorio.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="chave_pix">Chave PIX</label>
                                <input type="text"
                                       class="form-control @error('chave_pix') is-invalid @enderror"
                                       id="chave_pix"
                                       name="chave_pix"
                                       value="{{ old('chave_pix', $laboratorio->chave_pix) }}"
                                       placeholder="E-mail, CPF/CNPJ ou chave aleatória">
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
                                           {{ old('ativo', $laboratorio->ativo) ? 'checked' : '' }}>
                                    Laboratório ativo <i class="input-helper"></i>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('laboratorios.index') }}" class="btn btn-outline-secondary">
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

@push('scripts')
<script src="{{ asset('assets/js/phone-mask.js') }}"></script>
@endpush
@endsection
