@extends('layouts.app')

@section('title', 'Editar Cliente')

@section('content')
    <div class="d-xl-flex justify-content-between align-items-start mb-4">
        <div>
            <h2 class="text-dark font-weight-bold mb-2">
                <i class="mdi mdi-account-edit-outline me-2"></i>
                Editar Cliente
            </h2>
            <p class="text-muted mb-0">Atualizar dados do cliente</p>
        </div>

        <div class="d-flex gap-2">
            @if (request('from') === 'consultation')
                <a href="{{ route('professional.consultation', request('pid', $pessoa->id)) }}"
                    class="btn btn-outline-secondary">
                    <i class="mdi mdi-arrow-left me-2"></i>
                    Voltar à Consulta
                </a>
            @else
                <a href="{{ url()->previous() ?? route('pessoas.index') }}" class="btn btn-outline-secondary">
                    <i class="mdi mdi-arrow-left me-2"></i>
                    Voltar
                </a>
            @endif
            <a href="{{ route('pessoas.show', $pessoa) }}" class="btn btn-outline-primary">
                <i class="mdi mdi-eye-outline me-2"></i>
                Visualizar
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('pessoas.update', $pessoa) }}" method="POST" id="pessoaForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nome">Nome Completo <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nome') is-invalid @enderror"
                                        id="nome" name="nome" value="{{ old('nome', $pessoa->nome) }}" required>
                                    @error('nome')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="apelido">Apelido <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('apelido') is-invalid @enderror"
                                        id="apelido" name="apelido" value="{{ old('apelido', $pessoa->apelido) }}"
                                        required>
                                    @error('apelido')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="cpf">CPF</label>
                                    <input type="text" class="form-control @error('cpf') is-invalid @enderror"
                                        id="cpf" name="cpf" value="{{ old('cpf', $pessoa->cpf_formatado) }}"
                                        placeholder="000.000.000-00" maxlength="14">
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
                                    <input type="text" class="form-control @error('nome_mae') is-invalid @enderror"
                                        id="nome_mae" name="nome_mae" value="{{ old('nome_mae', $pessoa->nome_mae) }}">
                                    @error('nome_mae')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nome_pai">Nome do Pai</label>
                                    <input type="text" class="form-control @error('nome_pai') is-invalid @enderror"
                                        id="nome_pai" name="nome_pai" value="{{ old('nome_pai', $pessoa->nome_pai) }}">
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
                                    <select class="form-select @error('sexo') is-invalid @enderror" id="sexo"
                                        name="sexo">
                                        <option value="">Selecione</option>
                                        <option value="1" {{ old('sexo', $pessoa->sexo) == '1' ? 'selected' : '' }}>
                                            Masculino</option>
                                        <option value="2" {{ old('sexo', $pessoa->sexo) == '2' ? 'selected' : '' }}>
                                            Feminino</option>
                                    </select>
                                    @error('sexo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="nascimento_em">Data de Nascimento</label>
                                    <input type="date" class="form-control @error('nascimento_em') is-invalid @enderror"
                                        id="nascimento_em" name="nascimento_em"
                                        value="{{ old('nascimento_em', optional($pessoa->nascimento_em)->format('Y-m-d')) }}">
                                    @error('nascimento_em')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="deficiencia">Deficiência</label>
                                    <input type="text" class="form-control @error('deficiencia') is-invalid @enderror"
                                        id="deficiencia" name="deficiencia"
                                        value="{{ old('deficiencia', $pessoa->deficiencia) }}"
                                        placeholder="Descrever se houver">
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
                                    <div class="input-group">
                                        <input type="text" class="form-control @error('cep') is-invalid @enderror"
                                            id="cep" name="cep"
                                            value="{{ old('cep', $pessoa->cep_formatado) }}" placeholder="00000-000"
                                            maxlength="9" autocomplete="postal-code">
                                        <span class="input-group-text" id="cep-spinner" style="display:none;">
                                            <span class="spinner-border spinner-border-sm text-primary"
                                                role="status"></span>
                                        </span>
                                    </div>
                                    @error('cep')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small id="cep-feedback" class="text-danger" style="display:none;">
                                        <i class="mdi mdi-alert-circle-outline me-1"></i>
                                        CEP não encontrado. Digite o endereço manualmente ou informe outro CEP.
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="logradouro">Logradouro</label>
                                    <input type="text" class="form-control @error('logradouro') is-invalid @enderror"
                                        id="logradouro" name="logradouro"
                                        value="{{ old('logradouro', $pessoa->logradouro) }}"
                                        placeholder="Rua, Avenida, etc.">
                                    @error('logradouro')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label for="numero">Número</label>
                                    <input type="text" class="form-control @error('numero') is-invalid @enderror"
                                        id="numero" name="numero" value="{{ old('numero', $pessoa->numero) }}">
                                    @error('numero')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="bairro">Bairro</label>
                                    <input type="text" class="form-control @error('bairro') is-invalid @enderror"
                                        id="bairro" name="bairro" value="{{ old('bairro', $pessoa->bairro) }}">
                                    @error('bairro')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="complemento">Complemento</label>
                                    <input type="text" class="form-control @error('complemento') is-invalid @enderror"
                                        id="complemento" name="complemento"
                                        value="{{ old('complemento', $pessoa->complemento) }}"
                                        placeholder="Apto, casa, etc.">
                                    @error('complemento')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="localidade">Cidade</label>
                                    <input type="text" class="form-control @error('localidade') is-invalid @enderror"
                                        id="localidade" name="localidade"
                                        value="{{ old('localidade', $pessoa->localidade) }}">
                                    @error('localidade')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label for="uf">UF</label>
                                    <input type="text" class="form-control @error('uf') is-invalid @enderror"
                                        id="uf" name="uf" value="{{ old('uf', $pessoa->uf) }}"
                                        maxlength="2">
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
                                    <input type="text" class="form-control @error('telefone') is-invalid @enderror"
                                        id="telefone" name="telefone"
                                        value="{{ old('telefone', $pessoa->telefone_formatado) }}"
                                        placeholder="(00) 00000-0000" maxlength="15">
                                    @error('telefone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label for="email">E-mail</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email', $pessoa->email) }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="ativo"
                                        name="ativo" {{ old('ativo', $pessoa->ativo) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="ativo">
                                        Cliente ativo
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    Desmarque para desativar o cliente sem excluí-lo do sistema.
                                </small>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="mdi mdi-information-outline me-2"></i>
                                    <strong>Informação:</strong> Apenas os campos
                                    <span class="text-danger">Nome</span> e
                                    <span class="text-danger">Apelido</span> são obrigatórios.
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
                                Atualizar Cliente
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cepInput = document.getElementById('cep');
            const logradouro = document.getElementById('logradouro');
            const bairro = document.getElementById('bairro');
            const localidade = document.getElementById('localidade');
            const uf = document.getElementById('uf');
            const numero = document.getElementById('numero');
            const spinner = document.getElementById('cep-spinner');
            const feedback = document.getElementById('cep-feedback');

            if (!cepInput) return;

            // ── Máscara do CEP ──────────────────────────────────────────────────────
            cepInput.addEventListener('input', async function() {
                // Aplica mascara e busca automaticamente ao completar 8 digitos
                let v = this.value.replace(/\D/g, '').substring(0, 8);
                if (v.length > 5) v = v.substring(0, 5) + '-' + v.substring(5);
                this.value = v;

                const cep = v.replace(/\D/g, '');

                // Limpa feedback anterior
                feedback.style.display = 'none';
                cepInput.classList.remove('is-invalid');

                if (cep.length !== 8) return;

                // Mostra spinner
                spinner.style.display = 'inline-flex';

                try {
                    const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
                    const data = await response.json();

                    spinner.style.display = 'none';

                    if (data.erro) {
                        // CEP não encontrado → seleciona conteúdo para o usuário substituir
                        feedback.style.display = 'block';
                        cepInput.classList.add('is-invalid');
                        cepInput.focus();
                        cepInput.select();
                    } else {
                        // Preenche campos automaticamente
                        logradouro.value = data.logradouro || '';
                        bairro.value = data.bairro || '';
                        localidade.value = data.localidade || '';
                        uf.value = data.uf || '';

                        // Foca no campo Número
                        numero.focus();
                    }
                } catch (err) {
                    spinner.style.display = 'none';
                    feedback.style.display = 'block';
                    cepInput.classList.add('is-invalid');
                    cepInput.focus();
                    cepInput.select();
                }
            });
            // Mascara de telefone: (00) 00000-0000 ou (00) 0000-0000
            const telefoneInput = document.getElementById('telefone');
            if (telefoneInput) {
                telefoneInput.addEventListener('input', function() {
                    let v = this.value.replace(/\D/g, '').substring(0, 11);
                    if (v.length > 10) {
                        // Celular: (00) 00000-0000
                        v = '(' + v.substring(0, 2) + ') ' + v.substring(2, 7) + '-' + v.substring(7);
                    } else if (v.length > 6) {
                        // Fixo parcial: (00) 0000-XXXX
                        v = '(' + v.substring(0, 2) + ') ' + v.substring(2, 6) + '-' + v.substring(6);
                    } else if (v.length > 2) {
                        v = '(' + v.substring(0, 2) + ') ' + v.substring(2);
                    } else if (v.length > 0) {
                        v = '(' + v;
                    }
                    this.value = v;
                });
            }
        });
    </script>
@endpush
