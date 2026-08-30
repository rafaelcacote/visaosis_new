@extends('layouts.app')

@section('title', 'Dados PIX')

@section('content')
<div class="d-xl-flex justify-content-between align-items-start mb-4">
    <div>
        <h2 class="text-dark font-weight-bold mb-2">
            <i class="mdi mdi-qrcode me-2"></i>
            Dados PIX
        </h2>
        <p class="text-muted mb-0">Visualize e altere os dados PIX do cliente</p>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="mdi mdi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
@endif

@if ($errors->has('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="mdi mdi-alert-circle me-2"></i>{{ $errors->first('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
@endif

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.dados-pix.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="tipo_chave">Tipo da Chave <span class="text-danger">*</span></label>
                                <select class="form-select @error('tipo_chave') is-invalid @enderror"
                                        id="tipo_chave"
                                        name="tipo_chave"
                                        required>
                                    @php
                                        $tipos = ['CPF', 'CNPJ', 'EMAIL', 'TELEFONE', 'CELULAR', 'ALEATORIA'];
                                        $tipoAtual = old('tipo_chave', $dadosPix->tipo_chave ?? '');
                                    @endphp
                                    <option value="">Selecione...</option>
                                    @foreach ($tipos as $tipo)
                                        <option value="{{ $tipo }}" @selected($tipoAtual === $tipo)>{{ $tipo }}</option>
                                    @endforeach
                                </select>
                                @error('tipo_chave')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="chave">Chave PIX <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control @error('chave') is-invalid @enderror"
                                       id="chave"
                                       name="chave"
                                       value="{{ old('chave', $dadosPix->chave ?? '') }}"
                                       placeholder="Informe a chave PIX"
                                       required>
                                @error('chave')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="nome_titular">Nome do Titular <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control @error('nome_titular') is-invalid @enderror"
                                       id="nome_titular"
                                       name="nome_titular"
                                       value="{{ old('nome_titular', $dadosPix->nome_titular ?? '') }}"
                                       placeholder="Nome completo do titular"
                                       required>
                                @error('nome_titular')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="banco">Banco <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control @error('banco') is-invalid @enderror"
                                       id="banco"
                                       name="banco"
                                       value="{{ old('banco', $dadosPix->banco ?? '') }}"
                                       placeholder="Ex: Caixa Econômica Federal"
                                       required>
                                @error('banco')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-content-save me-2"></i>
                            Salvar Dados PIX
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="mdi mdi-information-outline me-2"></i>
                    Informações
                </h5>
                <p class="text-muted small mb-2">
                    Estes dados são utilizados para recebimentos via PIX vinculados ao cliente.
                </p>
                @if ($dadosPix && $dadosPix->updated_at)
                    <p class="text-muted small mb-0">
                        <strong>Última atualização:</strong><br>
                        {{ \Carbon\Carbon::parse($dadosPix->updated_at)->format('d/m/Y H:i') }}
                    </p>
                @else
                    <p class="text-muted small mb-0">
                        Nenhum dado PIX cadastrado ainda. Preencha o formulário ao lado para cadastrar.
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
