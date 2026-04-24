<!-- resources/views/professional/new-prescription.blade.php -->
@extends('layouts.app')
@section('title', 'Nova Receita')

@section('content')
    <div class="d-xl-flex justify-content-between align-items-start mb-4">
        <div>
            <h2 class="text-dark font-weight-bold mb-2">
                <i class="mdi mdi-file-document-plus me-2"></i>
                Nova Receita
            </h2>
            <p class="text-muted mb-0">Criar uma nova receita para paciente</p>
        </div>
        <a href="{{ route('professional.index') }}" class="btn btn-outline-secondary">
            <i class="mdi mdi-arrow-left me-2"></i>
            Voltar
        </a>
    </div>

    <div class="container-fluid py-3">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0"><i class="mdi mdi-magnify me-2"></i>Buscar Paciente</h5>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-md-6">
                        <input type="text" class="form-control" id="searchTerm" placeholder="Nome, CPF ou Telefone">
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-primary w-100" id="btnSearch">
                            <i class="mdi mdi-magnify me-2"></i>Buscar
                        </button>
                    </div>
                </div>
                <div class="mt-3" id="searchFeedback" style="display:none"></div>

                <div class="card mt-3" id="searchResults" style="display:none;">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="mdi mdi-account-group me-2"></i>Resultados da Busca</h6>
                    </div>
                    <div class="card-body">
                        <div id="patientsList" class="list-group"></div>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('professional.storeNewPrescription') }}" method="POST">
            @csrf

            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="mdi mdi-account me-2"></i>Dados do Paciente</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nome</label>
                            <input type="text" class="form-control @error('nome') is-invalid @enderror" name="nome"
                                value="{{ old('nome') }}" required>
                            @error('nome')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">CPF</label>
                            <input type="text" class="form-control @error('cpf') is-invalid @enderror" name="cpf"
                                value="{{ old('cpf') }}" placeholder="000.000.000-00" maxlength="14">
                            @error('cpf')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Telefone</label>
                            <input type="text" class="form-control @error('telefone') is-invalid @enderror"
                                name="telefone" value="{{ old('telefone') }}" placeholder="(00) 00000-0000" maxlength="15">
                            @error('telefone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">E-mail</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                                value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="mdi mdi-file-document me-2"></i>Prescrição</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-sm align-middle">
                                    <thead>
                                        <tr>
                                            <th style="width:12%">OD Esférico</th>
                                            <th style="width:12%">OD Cilíndrico</th>
                                            <th style="width:12%">OD Eixo</th>
                                            <th style="width:12%">OD AV</th>
                                            <th style="width:12%">OD DNP</th>
                                            <th style="width:12%">OD Altura</th>
                                            <th style="width:12%">OD Adição</th>
                                            <th style="width:18%">Tipo de Lente</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <input type="text"
                                                    class="form-control @error('od_esferico') is-invalid @enderror"
                                                    name="od_esferico" value="{{ old('od_esferico') }}"
                                                    placeholder="+/-0.00">
                                                @error('od_esferico')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="text"
                                                    class="form-control @error('od_cilindrico') is-invalid @enderror"
                                                    name="od_cilindrico" value="{{ old('od_cilindrico') }}"
                                                    placeholder="-0.00">
                                                @error('od_cilindrico')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="text"
                                                    class="form-control @error('od_eixo') is-invalid @enderror"
                                                    name="od_eixo" value="{{ old('od_eixo') }}" placeholder="0-180">
                                                @error('od_eixo')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="text"
                                                    class="form-control @error('od_acuidade') is-invalid @enderror"
                                                    name="od_acuidade" value="{{ old('od_acuidade') }}"
                                                    placeholder="20/20">
                                                @error('od_acuidade')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="text"
                                                    class="form-control @error('od_dnp') is-invalid @enderror"
                                                    name="od_dnp" value="{{ old('od_dnp') }}" placeholder="62">
                                                @error('od_dnp')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="text"
                                                    class="form-control @error('od_altura') is-invalid @enderror"
                                                    name="od_altura" value="{{ old('od_altura') }}" placeholder="0.00">
                                                @error('od_altura')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="text"
                                                    class="form-control @error('od_adicao') is-invalid @enderror"
                                                    name="od_adicao" value="{{ old('od_adicao') }}" placeholder="+0.00">
                                                @error('od_adicao')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                @php $tipo = old('tipo_lente') @endphp
                                                <select class="form-select @error('tipo_lente') is-invalid @enderror"
                                                    name="tipo_lente">
                                                    <option value="">Selecione</option>
                                                    <option value="Monofocal"
                                                        {{ $tipo === 'Monofocal' ? 'selected' : '' }}>Monofocal</option>
                                                    <option value="Bifocal" {{ $tipo === 'Bifocal' ? 'selected' : '' }}>
                                                        Bifocal</option>
                                                    <option value="Multifocal"
                                                        {{ $tipo === 'Multifocal' ? 'selected' : '' }}>Multifocal</option>
                                                </select>
                                                @error('tipo_lente')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-sm align-middle">
                                    <thead>
                                        <tr>
                                            <th style="width:12%">OE Esférico</th>
                                            <th style="width:12%">OE Cilíndrico</th>
                                            <th style="width:12%">OE Eixo</th>
                                            <th style="width:12%">OE AV</th>
                                            <th style="width:12%">OE DNP</th>
                                            <th style="width:12%">OE Altura</th>
                                            <th style="width:12%">OE Adição</th>
                                            <th style="width:18%">Validade</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <input type="text"
                                                    class="form-control @error('oe_esferico') is-invalid @enderror"
                                                    name="oe_esferico" value="{{ old('oe_esferico') }}"
                                                    placeholder="+/-0.00">
                                                @error('oe_esferico')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="text"
                                                    class="form-control @error('oe_cilindrico') is-invalid @enderror"
                                                    name="oe_cilindrico" value="{{ old('oe_cilindrico') }}"
                                                    placeholder="-0.00">
                                                @error('oe_cilindrico')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="text"
                                                    class="form-control @error('oe_eixo') is-invalid @enderror"
                                                    name="oe_eixo" value="{{ old('oe_eixo') }}" placeholder="0-180">
                                                @error('oe_eixo')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="text"
                                                    class="form-control @error('oe_acuidade') is-invalid @enderror"
                                                    name="oe_acuidade" value="{{ old('oe_acuidade') }}"
                                                    placeholder="20/20">
                                                @error('oe_acuidade')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="text"
                                                    class="form-control @error('oe_dnp') is-invalid @enderror"
                                                    name="oe_dnp" value="{{ old('oe_dnp') }}" placeholder="62">
                                                @error('oe_dnp')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="text"
                                                    class="form-control @error('oe_altura') is-invalid @enderror"
                                                    name="oe_altura" value="{{ old('oe_altura') }}" placeholder="0.00">
                                                @error('oe_altura')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="text"
                                                    class="form-control @error('oe_adicao') is-invalid @enderror"
                                                    name="oe_adicao" value="{{ old('oe_adicao') }}" placeholder="+0.00">
                                                @error('oe_adicao')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                @php $val = (string) old('validade_dias') @endphp
                                                <select class="form-select @error('validade_dias') is-invalid @enderror"
                                                    name="validade_dias">
                                                    <option value="">Selecione</option>
                                                    <option value="365" {{ $val === '365' ? 'selected' : '' }}>1 Ano
                                                    </option>
                                                    <option value="180" {{ $val === '180' ? 'selected' : '' }}>6 Meses
                                                    </option>

                                                </select>
                                                @error('validade_dias')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Diagnóstico</label>
                            <input type="text" class="form-control @error('diagnostico') is-invalid @enderror"
                                name="diagnostico" value="{{ old('diagnostico') }}">
                            @error('diagnostico')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Recomendações</label>
                            <input type="text" class="form-control @error('recomendacoes') is-invalid @enderror"
                                name="recomendacoes" value="{{ old('recomendacoes') }}">
                            @error('recomendacoes')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Observações da Receita</label>
                            <textarea class="form-control @error('observacoes_receita') is-invalid @enderror" name="observacoes_receita"
                                rows="3">{{ old('observacoes_receita') }}</textarea>
                            @error('observacoes_receita')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('professional.index') }}" class="btn btn-outline-secondary">
                    <i class="mdi mdi-close-circle me-2"></i>Cancelar
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="mdi mdi-check-circle me-2"></i>Salvar Receita
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        window.searchUrl = '{{ route('recepcao.patients.search') }}';

        function fillPatientForm(p) {
            document.querySelector('input[name="nome"]').value = p.nome || '';
            document.querySelector('input[name="cpf"]').value = p.cpf_formatado || p.cpf || '';
            document.querySelector('input[name="telefone"]').value = p.telefone_formatado || p.telefone || '';
            document.querySelector('input[name="email"]').value = p.email || '';
        }

        function showPatientsList(pacientes) {
            const results = document.getElementById('searchResults');
            const list = document.getElementById('patientsList');
            list.innerHTML = '';
            pacientes.forEach(function(p) {
                const a = document.createElement('a');
                a.href = '#';
                a.className = 'list-group-item list-group-item-action';
                a.innerHTML =
                    `<div class="d-flex justify-content-between"><div><strong>${p.nome||''}</strong><div class="text-muted">${p.cpf_formatado||''} ${p.telefone_formatado? ' • '+p.telefone_formatado: ''}</div></div></div>`;
                a.onclick = function(e) {
                    e.preventDefault();
                    fillPatientForm(p);
                    results.style.display = 'none';
                };
                list.appendChild(a);
            });
            results.style.display = '';
        }

        function searchPatient() {
            const term = document.getElementById('searchTerm').value.trim();
            const feedback = document.getElementById('searchFeedback');
            if (!term) {
                feedback.style.display = '';
                feedback.className = 'alert alert-warning mt-2';
                feedback.textContent = 'Digite nome, CPF ou telefone para buscar.';
                return;
            }
            fetch(`${window.searchUrl}?term=${encodeURIComponent(term)}`)
                .then(r => r.json())
                .then(data => {
                    if (data.found) {
                        if (data.multiple) {
                            showPatientsList(data.pacientes || []);
                            feedback.style.display = '';
                            feedback.className = 'alert alert-info mt-2';
                            feedback.textContent =
                                `${data.count||0} pacientes encontrados. Selecione o paciente desejado.`;
                        } else {
                            fillPatientForm(data.paciente || {});
                            feedback.style.display = '';
                            feedback.className = 'alert alert-success mt-2';
                            feedback.textContent = 'Paciente encontrado. Os dados foram carregados.';
                            document.getElementById('searchResults').style.display = 'none';
                        }
                    } else {
                        document.getElementById('searchResults').style.display = 'none';
                        feedback.style.display = '';
                        feedback.className = 'alert alert-warning mt-2';
                        feedback.textContent = 'Nenhum paciente encontrado. Informe os dados para novo cadastro.';
                    }
                })
                .catch(() => {
                    feedback.style.display = '';
                    feedback.className = 'alert alert-danger mt-2';
                    feedback.textContent = 'Erro na busca do paciente.';
                });
        }

        document.getElementById('btnSearch').addEventListener('click', searchPatient);
    </script>
@endpush
