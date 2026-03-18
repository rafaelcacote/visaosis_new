@extends('layouts.app')

@section('title', 'Consulta - ' . $patient['nome'])

@section('content')
    <div class="d-xl-flex justify-content-between align-items-start mb-4">
        <div>
            <h2 class="text-dark font-weight-bold mb-2">
                <i class="mdi mdi-account-card-details me-2"></i>
                Consulta - {{ $patient['nome'] }}
            </h2>
            <p class="text-muted mb-0">Atendimento do paciente</p>
        </div>
        <a href="{{ route('professional.index') }}" class="btn btn-outline-secondary">
            <i class="mdi mdi-arrow-left me-2"></i>
            Voltar à Fila
        </a>
    </div>
    <div class="row">
        <!-- Dados do Paciente -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-account text-primary me-2"></i>
                        Dados do Paciente
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="avatar-circle bg-primary text-white mx-auto"
                            style="width: 80px; height: 80px; font-size: 2rem;">
                            {{ substr($patient['nome'], 0, 1) }}
                        </div>
                        <h5 class="mt-2 mb-0">{{ $patient['nome'] }}</h5>
                        <small class="text-muted">{{ $patient['idade'] }} anos</small>
                    </div>

                    <div class="mb-2">
                        <strong>CPF:</strong> {{ $patient['cpf'] }}
                    </div>
                    <div class="mb-2">
                        <strong>Telefone:</strong> {{ $patient['telefone'] }}
                    </div>
                    <div class="mb-2">
                        <strong>Email:</strong> {{ $patient['email'] }}
                    </div>
                    <div class="mb-3">
                        <strong>Endereço:</strong> {{ $patient['endereco'] }}
                    </div>

                    <div class="alert alert-info">
                        <strong>Motivo da Consulta:</strong><br>
                        {{ $patient['motivo'] }}
                    </div>

                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-info" onclick="viewFullHistory()">
                            <i class="mdi mdi-history me-2"></i>
                            Histórico Completo
                        </button>
                        <button class="btn btn-outline-secondary" onclick="editPatientData()">
                            <i class="mdi mdi-pencil me-2"></i>
                            Editar Dados
                        </button>
                    </div>
                </div>
            </div>

            <!-- Última Receita -->
            @if ($patient['ultima_receita'])
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="mdi mdi-file-document text-success me-2"></i>
                            Última Receita
                            ({{ $patient['ultima_receita']['data']->format('d/m/Y') }})
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center mb-2">
                            <div class="col-6">
                                <small class="text-muted">OD (Direito)</small>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">OE (Esquerdo)</small>
                            </div>
                        </div>
                        <div class="row text-center mb-2">
                            <div class="col-6">
                                <strong>{{ $patient['ultima_receita']['od_esferico'] }}</strong>
                            </div>
                            <div class="col-6">
                                <strong>{{ $patient['ultima_receita']['oe_esferico'] }}</strong>
                            </div>
                        </div>
                        <div class="row text-center mb-2">
                            <div class="col-6">
                                <small>{{ $patient['ultima_receita']['od_cilindrico'] }} x
                                    {{ $patient['ultima_receita']['od_eixo'] }}°</small>
                            </div>
                            <div class="col-6">
                                <small>{{ $patient['ultima_receita']['oe_cilindrico'] }} x
                                    {{ $patient['ultima_receita']['oe_eixo'] }}°</small>
                            </div>
                        </div>
                        <div class="row text-center mb-2">
                            <div class="col-4">
                                <small class="text-muted">Adição:</small>
                            </div>

                            <div class="col-4 ">
                                <strong>{{ $patient['ultima_receita']['od_adicao'] }}</strong>
                            </div>
                            <div class="col-4">
                                <strong>{{ $patient['ultima_receita']['oe_adicao'] }}</strong>
                            </div>
                        </div>
                        <div class="row text-center mb-2">
                            <div class="col-4">
                                <small class="text-muted">DNP:</small>
                            </div>

                            <div class="col-4 ">
                                <strong>{{ $patient['ultima_receita']['od_dnp'] }}</strong>
                            </div>
                            <div class="col-4">
                                <strong>{{ $patient['ultima_receita']['oe_dnp'] }}</strong>
                            </div>
                        </div>
                        <div class="mt-2">
                            <small><strong>Obs:</strong> {{ $patient['ultima_receita']['observacoes'] }}</small>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Área de Consulta -->
        <div class="col-lg-8">
            <!-- Tabs de Navegação -->
            @php $activeTab = session('active_tab', 'prescription'); @endphp
            <ul class="nav nav-tabs mb-3" id="consultationTabs" role="tablist">

                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'prescription' ? 'active' : '' }}" id="prescription-tab"
                        data-bs-toggle="tab" data-bs-target="#prescription" type="button" role="tab">
                        <i class="mdi mdi-file-document me-2"></i>
                        Receita
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'examination' ? 'active' : '' }}" id="examination-tab"
                        data-bs-toggle="tab" data-bs-target="#examination" type="button" role="tab">
                        <i class="mdi mdi-eye me-2"></i>
                        Exame
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'referral' ? 'active' : '' }}" id="referral-tab"
                        data-bs-toggle="tab" data-bs-target="#referral" type="button" role="tab">
                        <i class="mdi mdi-arrow-right-circle me-2"></i>
                        Encaminhamento
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'finish' ? 'active' : '' }}" id="finish-tab"
                        data-bs-toggle="tab" data-bs-target="#finish" type="button" role="tab">
                        <i class="mdi mdi-check-circle me-2"></i>
                        Finalizar
                    </button>
                </li>
            </ul>

            <!-- Conteúdo das Tabs -->
            <div class="tab-content" id="consultationTabsContent">


                <!-- Tab Receita -->
                <div class="tab-pane fade {{ $activeTab === 'prescription' ? 'show active' : '' }}" id="prescription"
                    role="tabpanel">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Prescrição de Óculos</h5>
                            <button class="btn btn-sm btn-outline-info" onclick="copyLastPrescription()">
                                <i class="mdi mdi-content-copy me-1"></i>
                                Copiar Última Receita
                            </button>
                        </div>
                        <div class="card-body">

                            <form id="prescriptionForm"
                                action="/professional/save-prescription-draft/{{ $consulta['id'] }}" method="POST">
                                @csrf
                                @php
                                    $prescricaoValores = $patient['prescricao'] ?? ($patient['ultima_receita'] ?? []);
                                @endphp
                                <!-- Tabela de Prescrição -->
                                <div class="table-responsive mb-3">
                                    <table class="table table-bordered prescription-table">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="50">Olho</th>
                                                <th>Esférico</th>
                                                <th>Cilíndrico</th>
                                                <th>Eixo</th>
                                                <th>DNP</th>
                                                <th>Altura</th>
                                                <th>Adição</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-center fw-bold">OD</td>
                                                <td>
                                                    <input type="text" class="form-control" name="od_esferico"
                                                        placeholder="+/-0.00"
                                                        value="{{ old('od_esferico', $prescricaoValores['od_esferico'] ?? '') }}">
                                                    @error('od_esferico')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" name="od_cilindrico"
                                                        placeholder="+/-0.00"
                                                        value="{{ old('od_cilindrico', $prescricaoValores['od_cilindrico'] ?? '') }}">
                                                    @error('od_cilindrico')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" name="od_eixo"
                                                        placeholder="0-180°"
                                                        value="{{ old('od_eixo', $prescricaoValores['od_eixo'] ?? '') }}">
                                                    @error('od_eixo')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" name="od_dnp"
                                                        placeholder="62°"
                                                        value="{{ old('od_dnp', $prescricaoValores['od_dnp'] ?? '') }}">
                                                    @error('od_dnp')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" name="od_altura"
                                                        placeholder="100 cm"
                                                        value="{{ old('od_altura', $prescricaoValores['od_altura'] ?? '') }}">
                                                    @error('od_altura')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" name="od_adicao"
                                                        placeholder="+0.00"
                                                        value="{{ old('od_adicao', $prescricaoValores['od_adicao'] ?? '') }}">
                                                    @error('od_adicao')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-center fw-bold">OE</td>
                                                <td>
                                                    <input type="text" class="form-control" name="oe_esferico"
                                                        placeholder="+/-0.00"
                                                        value="{{ old('oe_esferico', $prescricaoValores['oe_esferico'] ?? '') }}">
                                                    @error('oe_esferico')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" name="oe_cilindrico"
                                                        placeholder="+/-0.00"
                                                        value="{{ old('oe_cilindrico', $prescricaoValores['oe_cilindrico'] ?? '') }}">
                                                    @error('oe_cilindrico')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" name="oe_eixo"
                                                        placeholder="0-180°"
                                                        value="{{ old('oe_eixo', $prescricaoValores['oe_eixo'] ?? '') }}">
                                                    @error('oe_eixo')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" name="oe_dnp"
                                                        placeholder="62°"
                                                        value="{{ old('oe_dnp', $prescricaoValores['oe_dnp'] ?? '') }}">
                                                    @error('oe_dnp')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" name="oe_altura"
                                                        placeholder="100 cm"
                                                        value="{{ old('oe_altura', $prescricaoValores['oe_altura'] ?? '') }}">
                                                    @error('oe_altura')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" name="oe_adicao"
                                                        placeholder="+0.00"
                                                        value="{{ old('oe_adicao', $prescricaoValores['oe_adicao'] ?? '') }}">
                                                    @error('oe_adicao')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-8">
                                        <label class="form-label">Tipo de Lente</label>
                                        <select class="form-select" name="tipo_lente">
                                            @php $tipo = old('tipo_lente', $prescricaoValores['tipo_lente'] ?? '') @endphp
                                            <option value="Monofocal" {{ $tipo === 'Monofocal' ? 'selected' : '' }}>
                                                Monofocal</option>
                                            <option value="Bifocal" {{ $tipo === 'Bifocal' ? 'selected' : '' }}>Bifocal
                                            </option>
                                            <option value="Multifocal" {{ $tipo === 'Multifocal' ? 'selected' : '' }}>
                                                Multifocal</option>
                                        </select>
                                        @error('tipo_lente')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Validade da Lente</label>
                                        <select class="form-select" name="validade_dias">
                                            @php $val = (string) old('validade_dias', $prescricaoValores['validade_dias'] ?? '') @endphp
                                            <option value="365" {{ $val === '365' ? 'selected' : '' }}>1 Ano</option>
                                            <option value="180" {{ $val === '180' ? 'selected' : '' }}>6 Meses</option>
                                            <option value="90" {{ $val === '90' ? 'selected' : '' }}>3 Meses</option>
                                        </select>
                                        @error('validade_dias')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Diagnóstico</label>
                                    <input type="text" class="form-control" name="diagnostico"
                                        placeholder="Ex: Miopia com astigmatismo"
                                        value="{{ old('diagnostico', $prescricaoValores['diagnostico'] ?? '') }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Observações da Receita</label>
                                    <textarea class="form-control" name="observacoes_receita" rows="3"
                                        placeholder="Ex: Uso contínuo, retorno em 1 ano...">{{ old('observacoes_receita', $prescricaoValores['observacoes_receita'] ?? ($prescricaoValores['observacoes'] ?? '')) }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Recomendações</label>
                                    <textarea class="form-control" name="recomendacoes" rows="2" placeholder="Recomendações adicionais...">{{ old('recomendacoes', $prescricaoValores['recomendacoes'] ?? '') }}</textarea>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-success" onclick="generatePrescription()"
                                        {{ empty($prescricaoValores) ? 'disabled' : '' }}>
                                        <i class="mdi mdi-file-document me-2"></i>
                                        Gerar Receita
                                    </button>
                                    <button type="button" class="btn btn-outline-primary"
                                        onclick="previewPrescription()" {{ empty($prescricaoValores) ? 'disabled' : '' }}>
                                        <i class="mdi mdi-eye me-2"></i>
                                        Visualizar
                                    </button>
                                    <!-- Update: add action and method POST + CSRF -->
                                    <button type="submit" class="btn btn-outline-secondary">
                                        <i class="mdi mdi-content-save me-2"></i>
                                        Salvar Rascunho
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Tab Exame -->
                <div class="tab-pane fade {{ $activeTab === 'examination' ? 'show active' : '' }}" id="examination"
                    role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Exame</h5>
                        </div>
                        <div class="card-body">
                            <form id="exameForm" action="/professional/save-exame/{{ $consulta['id'] }}" method="POST">
                                @csrf
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Acuidade Visual OD</label>
                                        <input type="text" class="form-control" name="av_od" required
                                            placeholder="Ex: 20/20"
                                            value="{{ old('av_od', $patient['exame']['av_od'] ?? '') }}">
                                        @error('av_od')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Acuidade Visual OE</label>
                                        <input type="text" class="form-control" name="av_oe"
                                            placeholder="Ex: 20/25"
                                            value="{{ old('av_oe', $patient['exame']['av_oe'] ?? '') }}">
                                        @error('av_oe')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Pressão Intraocular OD</label>
                                        <input type="text" class="form-control" name="pio_od" required
                                            placeholder="Ex: 14 mmHg"
                                            value="{{ old('pio_od', $patient['exame']['pio_od'] ?? '') }}">
                                        @error('pio_od')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Pressão Intraocular OE</label>
                                        <input type="text" class="form-control" name="pio_oe" required
                                            placeholder="Ex: 15 mmHg"
                                            value="{{ old('pio_oe', $patient['exame']['pio_oe'] ?? '') }}">
                                        @error('pio_oe')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Fundoscopia</label>
                                    <textarea class="form-control" name="fundoscopia" rows="3"
                                        placeholder="Descreva os achados da fundoscopia...">{{ old('fundoscopia', $patient['exame']['fundoscopia'] ?? '') }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Anamnese</label>
                                    <textarea class="form-control" name="anamnese" rows="3" placeholder="Anamnese...">{{ old('anamnese', $patient['exame']['anamnese'] ?? '') }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Observações do Exame</label>
                                    <textarea class="form-control" name="observacoes" rows="3" placeholder="Observações adicionais...">{{ old('observacoes', $patient['exame']['observacoes'] ?? '') }}</textarea>
                                </div>

                                <div class="d-flex gap-2">

                                    <!-- Update: add action and method POST + CSRF -->
                                    <button type="submit" class="btn btn-outline-secondary">
                                        <i class="mdi mdi-content-save me-2"></i>
                                        Salvar Exame
                                    </button>
                                    <button type="button" class="btn btn-outline-info" onclick="printExame()"
                                        {{ !$patient['exame'] ? 'disabled' : '' }}>
                                        <i class="mdi mdi-printer me-2"></i>
                                        Imprimir Exame
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Tab Encaminhamento -->
                <div class="tab-pane fade {{ $activeTab === 'referral' ? 'show active' : '' }}" id="referral"
                    role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Encaminhamento</h5>
                        </div>
                        <div class="card-body">
                            <form id="referralForm" action="{{ route('professional.referPatient', $consulta->id) }}"
                                method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Especialidade de Destino</label>
                                    <select class="form-select" name="especialidade" required>
                                        <option value="">Selecione a especialidade</option>
                                        @foreach ($especialidades ?? [] as $esp)
                                            <option value="{{ $esp->id }}"
                                                {{ isset($encaminhamento) && $encaminhamento->especialidade_id == $esp->id ? 'selected' : '' }}>
                                                {{ $esp->descricao }}
                                            </option>
                                        @endforeach
                                    </select>

                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Usuário de Óculos</label>
                                    <radio-group>
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" name="usuario_ocular"
                                                value="sim"
                                                {{ isset($encaminhamento) && (int) $encaminhamento->usuario_oculos === 1 ? 'checked' : '' }}>
                                            Sim
                                        </label>
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" name="usuario_ocular"
                                                value="nao"
                                                {{ isset($encaminhamento) && (int) $encaminhamento->usuario_oculos === 0 ? 'checked' : '' }}>
                                            Não
                                        </label>
                                    </radio-group>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Data da Última Avaliação</label>
                                    <input type="date" class="form-control" name="data" required
                                        value="{{ isset($encaminhamento) && $encaminhamento->ultima_avaliacao_em ? \Carbon\Carbon::parse($encaminhamento->ultima_avaliacao_em)->format('Y-m-d') : '' }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Hipótese do Encaminhamento</label>
                                    <textarea class="form-control" name="motivo" rows="3" placeholder="Descreva o motivo do encaminhamento..."
                                        required>{{ old('motivo', isset($encaminhamento) ? $encaminhamento->hipotese : '') }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Urgência</label>
                                    <select class="form-select" name="urgencia">
                                        @php $urg = isset($encaminhamento) ? $encaminhamento->urgencia : 'normal'; @endphp
                                        <option value="normal" {{ $urg === 'normal' ? 'selected' : '' }}>Normal</option>
                                        <option value="urgente" {{ $urg === 'urgente' ? 'selected' : '' }}>Urgente
                                        </option>
                                        <option value="emergencia" {{ $urg === 'emergencia' ? 'selected' : '' }}>
                                            Emergência</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Observações</label>
                                    <textarea class="form-control" name="observacoes" rows="3" placeholder="Observações adicionais...">{{ old('observacoes', isset($encaminhamento) ? $encaminhamento->observacoes : '') }}</textarea>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="mdi mdi-arrow-right-circle me-2"></i>
                                        Encaminhar Paciente
                                    </button>
                                    <button type="button" class="btn btn-outline-info" onclick="printReferral()"
                                        {{ !isset($encaminhamento) ? 'disabled' : '' }}>
                                        <i class="mdi mdi-printer me-2"></i>
                                        Imprimir Encaminhamento
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Tab Finalizar -->
                <div class="tab-pane fade {{ $activeTab === 'finish' ? 'show active' : '' }}" id="finish"
                    role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Finalizar Consulta</h5>
                        </div>
                        <div class="card-body">
                            <form id="finishForm">
                                <div class="mb-3">
                                    <label class="form-label">Resumo da Consulta</label>
                                    @php
                                        $resumoParts = [];
                                        $p = $patient['prescricao'] ?? null;
                                        if ($p) {
                                            $resumoParts[] =
                                                'Prescrição: ' .
                                                'Tipo de lente: ' .
                                                ($p['tipo_lente'] ?? '-') .
                                                '; ' .
                                                'Diagnóstico: ' .
                                                ($p['diagnostico'] ?? '-') .
                                                '; ' .
                                                'Observações: ' .
                                                ($p['observacoes_receita'] ?? '-') .
                                                '; ' .
                                                'Recomendações: ' .
                                                ($p['recomendacoes'] ?? '-');
                                        }

                                        $e = $patient['exame'] ?? null;
                                        if ($e) {
                                            $resumoParts[] =
                                                'Exame: ' .
                                                'Anamnese: ' .
                                                ($e['anamnese'] ?? '-') .
                                                '; ' .
                                                'AV OD: ' .
                                                ($e['av_od'] ?? '-') .
                                                '; ' .
                                                'AV OE: ' .
                                                ($e['av_oe'] ?? '-') .
                                                '; ' .
                                                'PIO OD: ' .
                                                ($e['pio_od'] ?? '-') .
                                                '; ' .
                                                'PIO OE: ' .
                                                ($e['pio_oe'] ?? '-') .
                                                '; ' .
                                                'Fundoscopia: ' .
                                                ($e['fundoscopia'] ?? '-') .
                                                '; ' .
                                                'Observações: ' .
                                                ($e['observacoes'] ?? '-');
                                        }

                                        if (isset($encaminhamento) && $encaminhamento) {
                                            $espDesc = optional($encaminhamento->especialidade)->descricao;
                                            $ultimaAval = $encaminhamento->ultima_avaliacao_em
                                                ? \Carbon\Carbon::parse($encaminhamento->ultima_avaliacao_em)->format(
                                                    'd/m/Y H:i',
                                                )
                                                : null;
                                            $resumoParts[] =
                                                'Encaminhamento: ' .
                                                'Especialidade: ' .
                                                ($espDesc ?? '-') .
                                                '; ' .
                                                'Hipótese: ' .
                                                ($encaminhamento->hipotese ?? '-') .
                                                '; ' .
                                                'Urgência: ' .
                                                ($encaminhamento->urgencia ?? '-') .
                                                '; ' .
                                                'Usuário de Óculos: ' .
                                                ((int) $encaminhamento->usuario_oculos === 1 ? 'Sim' : 'Não') .
                                                '; ' .
                                                'Última Avaliação: ' .
                                                ($ultimaAval ?? '-') .
                                                '; ' .
                                                'Observações: ' .
                                                ($encaminhamento->observacoes ?? '-');
                                        }

                                        $resumoDefault = implode("\n\n", $resumoParts);
                                    @endphp
                                    <textarea class="form-control" name="resumo" rows="4"
                                        placeholder="Resumo dos principais achados e condutas...">{{ old('resumo', $resumoDefault) }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Próximo Retorno</label>
                                    @php
                                        $validade = $patient['prescricao']['validade_dias'] ?? null;
                                        $retSel = '';
                                        if ($validade == 30) {
                                            $retSel = '1_mes';
                                        } elseif ($validade == 90) {
                                            $retSel = '3_meses';
                                        } elseif ($validade == 180) {
                                            $retSel = '6_meses';
                                        } elseif ($validade == 365) {
                                            $retSel = '1_ano';
                                        }
                                    @endphp
                                    <select class="form-select" name="retorno">
                                        <option value="" {{ $retSel === '' ? 'selected' : '' }}>Não necessário
                                        </option>
                                        <option value="30" {{ $retSel === '1_mes' ? 'selected' : '' }}>1 mês</option>
                                        <option value="90" {{ $retSel === '3_meses' ? 'selected' : '' }}>3 meses
                                        </option>
                                        <option value="180" {{ $retSel === '6_meses' ? 'selected' : '' }}>6 meses
                                        </option>
                                        <option value="365" {{ $retSel === '1_ano' ? 'selected' : '' }}>1 ano</option>
                                        <option value="outro" {{ $retSel === 'outro' ? 'selected' : '' }}>Outro período
                                        </option>
                                    </select>
                                </div>
                                <div id="retornoOutroContainer" class="mt-2"
                                    style="{{ $retSel === 'outro' ? '' : 'display:none' }}">
                                    <input type="number" class="form-control" name="retorno_outro_dias" min="1"
                                        placeholder="Informe o retorno em dias" value="{{ old('retorno_outro_dias') }}"
                                        {{ $retSel === 'outro' ? 'required' : '' }}>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="receita_gerada"
                                            name="receita_gerada"
                                            {{ isset($patient['prescricao']) && $patient['prescricao'] ? 'checked' : '' }}>
                                        <label class="form-check-label" for="receita_gerada">
                                            Receita foi gerada
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="exame_feito"
                                            name="exame_feito"
                                            {{ isset($patient['exame']) && $patient['exame'] ? 'checked' : '' }}>
                                        <label class="form-check-label" for="exame_feito">
                                            Exame foi realizado
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="encaminhamento_feito"
                                            name="encaminhamento_feito"
                                            {{ isset($encaminhamento) && $encaminhamento ? 'checked' : '' }}>
                                        <label class="form-check-label" for="encaminhamento_feito">
                                            Encaminhamento foi realizado
                                        </label>
                                    </div>
                                </div>

                                <div class="alert alert-info">
                                    <i class="mdi mdi-information me-2"></i>
                                    <strong>Atenção:</strong> Ao finalizar a consulta, o paciente será marcado como atendido
                                    e removido da fila de espera.
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-success" onclick="finishConsultation()">
                                        <i class="mdi mdi-check-circle me-2"></i>
                                        Finalizar Consulta
                                    </button>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Receita Gerada -->
    <div class="modal fade" id="prescriptionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Receita Gerada</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="prescriptionContent">
                    <!-- Conteúdo da receita -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-primary" onclick="printPrescription()">
                        <i class="mdi mdi-printer me-2"></i>
                        Imprimir
                    </button>
                    <button type="button" class="btn btn-success" onclick="sendWhatsApp()">
                        <i class="mdi mdi-whatsapp me-2"></i>
                        Enviar WhatsApp
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal de Encaminhamento -->
    <div class="modal fade" id="referralModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Termo de Encaminhamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="referralContent">
                    <div class="referral-preview">
                        <h6 class="text-primary mb-3">Dados do Paciente</h6>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>Nome:</strong> {{ $patient['nome'] }}</p>
                                <p><strong>Idade:</strong> {{ $patient['idade'] }} anos</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Usuário de Óculos:</strong> <span id="referralGlasses"></span></p>
                                <p><strong>Última Avaliação:</strong> <span id="referralLastEval"></span></p>
                            </div>
                        </div>

                        <h6 class="text-primary mb-3">Informações do Encaminhamento</h6>
                        <div class="mb-3">
                            <p><strong>Especialidade:</strong> <span id="referralSpecialty"></span></p>
                            <p><strong>Hipótese de Encaminhamento:</strong></p>
                            <p id="referralHypothesis" class="border-start border-primary ps-3"></p>

                            <div id="referralObsContainer" class="d-none">
                                <p><strong>Observações:</strong></p>
                                <p id="referralObs" class="border-start border-primary ps-3"></p>
                            </div>
                        </div>

                        <div class="text-end text-muted">
                            <small>Data de Emissão: <span id="referralDate"></span></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-primary" onclick="printReferralDoc()">
                        <i class="mdi mdi-printer me-2"></i>
                        Imprimir
                    </button>
                    <button type="button" class="btn btn-success" onclick="sendReferralWhatsApp()">
                        <i class="mdi mdi-whatsapp me-2"></i>
                        Enviar WhatsApp
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal de Exame -->
    <div class="modal fade" id="exameModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Exame de Vista</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="exameContent">
                    <div class="referral-preview">
                        <h6 class="text-primary mb-3">Dados do Paciente</h6>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>Nome:</strong> {{ $patient['nome'] }}</p>
                                <p><strong>Idade:</strong> {{ $patient['idade'] }} anos</p>
                            </div>

                        </div>

                        <h6 class="text-primary mb-3">Informações do Exame</h6>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="80">Olho</th>
                                            <th>Acuidade Visual</th>
                                            <th>Pressão</th>
                                        </tr>
                                        <tr>
                                            <td>OD</td>
                                            <td><span id="exameAvOd"></span></td>
                                            <td><span id="examePioOd"></span></td>
                                        </tr>
                                        <tr>
                                            <td>OE</td>
                                            <td><span id="exameAvOe"></span></td>
                                            <td><span id="examePioOe"></span></td>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="mb-3">
                            <p><strong>Fundoscopia:</strong> <span id="exameFundoscopia"></span></p>
                            <p><strong>Anamnese:</strong></p>
                            <p id="exameAnamnese" class="border-start border-primary ps-3"></p>

                            <div id="exameObsContainer" class="d-none">
                                <p><strong>Observações:</strong></p>
                                <p id="exameObs" class="border-start border-primary ps-3"></p>
                            </div>
                        </div>

                        <div class="text-end text-muted">
                            <small>Data de Emissão: <span id="exameDate"></span></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-primary" onclick="printExamDoc()">
                        <i class="mdi mdi-printer me-2"></i>
                        Imprimir
                    </button>
                    <button type="button" class="btn btn-success" onclick="sendExamWhatsApp()">
                        <i class="mdi mdi-whatsapp me-2"></i>
                        Enviar WhatsApp
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="finishConfirmModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Confirmar Finalização</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Tem certeza que deseja finalizar a consulta?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" id="confirmFinishBtn">Finalizar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="finishFeedbackModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Finalização</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="finishFeedbackMessage"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="finishFeedbackOk">OK</button>
                </div>
            </div>
        </div>
    </div>

    </div>
    </div>
@endsection

@push('styles')
    <style>
        .avatar-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #dee2e6;
        }

        .nav-tabs .nav-link {
            border: none;
            border-bottom: 2px solid transparent;
        }

        .nav-tabs .nav-link.active {
            border-bottom-color: #4f46e5;
            background: none;
        }

        .prescription-table input {
            text-align: center;
            font-family: monospace;
        }

        .prescription-table th,
        .prescription-table td {
            padding: 0.55rem 0.4rem;
            vertical-align: middle;
        }

        .prescription-table th {
            font-size: 0.85rem;
            font-weight: 600;
        }

        .prescription-table td {
            font-size: 0.85rem;
        }

        .prescription-table .form-control {
            font-size: 0.85rem;
            padding: 0.45rem 0.55rem;
            height: 2.25rem;
            line-height: 1.2;
            color: #111827;
            background-color: #ffffff;
        }

        .prescription-table .form-control::placeholder {
            font-size: 0.75rem;
            color: #9ca3af;
        }
    </style>
@endpush

@push('scripts')
    <script>
        let currentPrescription = null;

        function copyLastPrescription() {
            // Copiar dados da última receita
            @if ($patient['ultima_receita'])
                document.querySelector('input[name="od_esferico"]').value =
                    '{{ $patient['ultima_receita']['od_esferico'] }}';
                document.querySelector('input[name="od_cilindrico"]').value =
                    '{{ $patient['ultima_receita']['od_cilindrico'] }}';
                document.querySelector('input[name="od_eixo"]').value = '{{ $patient['ultima_receita']['od_eixo'] }}';
                document.querySelector('input[name="oe_esferico"]').value =
                    '{{ $patient['ultima_receita']['oe_esferico'] }}';
                document.querySelector('input[name="oe_cilindrico"]').value =
                    '{{ $patient['ultima_receita']['oe_cilindrico'] }}';
                document.querySelector('input[name="oe_eixo"]').value = '{{ $patient['ultima_receita']['oe_eixo'] }}';
                document.querySelector('input[name="od_adicao"]').value = '{{ $patient['ultima_receita']['od_adicao'] }}';
                document.querySelector('input[name="oe_adicao"]').value = '{{ $patient['ultima_receita']['oe_adicao'] }}';
                document.querySelector('input[name="od_dnp"]').value = '{{ $patient['ultima_receita']['od_dnp'] }}';
                document.querySelector('input[name="oe_dnp"]').value = '{{ $patient['ultima_receita']['oe_dnp'] }}';
                document.querySelector('input[name="od_altura"]').value = '{{ $patient['ultima_receita']['od_altura'] }}';
                document.querySelector('input[name="oe_altura"]').value = '{{ $patient['ultima_receita']['oe_altura'] }}';
                document.querySelector('select[name="tipo_lente"]').value =
                    '{{ $patient['ultima_receita']['tipo_lente'] ?? '' }}';
                document.querySelector('select[name="validade_dias"]').value =
                    '{{ $patient['ultima_receita']['validade_dias'] ?? '' }}';
                document.querySelector('input[name="diagnostico"]').value =
                    '{{ $patient['ultima_receita']['diagnostico'] ?? '' }}';
                document.querySelector('textarea[name="observacoes_receita"]').value =
                    '{{ $patient['ultima_receita']['observacoes'] ?? '' }}';
                document.querySelector('textarea[name="recomendacoes"]').value =
                    '{{ $patient['ultima_receita']['recomendacoes'] ?? '' }}';
            @endif

            alert('Dados da última receita copiados!');
        }

        function generatePrescription() {
            const formData = new FormData(document.getElementById('prescriptionForm'));

            // Validar campos obrigatórios
            if (!formData.get('od_esferico') || !formData.get('oe_esferico')) {
                alert('Preencha pelo menos os campos esféricos!');
                return;
            }

            // Simular geração da receita
            fetch('{{ route('professional.generatePrescription', $consulta->id) }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.text())
                .then(html => {
                    document.getElementById('prescriptionContent').innerHTML = html;
                    const modal = new bootstrap.Modal(document.getElementById('prescriptionModal'));
                    modal.show();
                    currentPrescription = formData;
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao gerar receita');
                });
        }

        function previewPrescription() {
            const formData = new FormData(document.getElementById('prescriptionForm'));

            // Criar preview simples
            const preview = `
        <div class="prescription-preview">
            <h6>Preview da Receita</h6>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <td>Olho</td>
                    <td>Esférico</td>
                    <td>Cilíndrico</td>
                    <td>Eixo</td>
                    <td>DNP</td>
                    <td>Altura</td>
                    <td>Adição</td>
                </tr>
                </thead>

                <tr>
                    <td>OD:</td>
                    <td>${formData.get('od_esferico') || '0.00'}</td>
                    <td>${formData.get('od_cilindrico') || '0.00'}</td>
                    <td>${formData.get('od_eixo') || '0'}°</td>
                    <td>${formData.get('od_dnp') || 'N/A'}°</td>
                    <td>${formData.get('od_altura') || '100 cm'}</td>
                    <td>${formData.get('od_adicao') || '0.00'}</td>
                </tr>
                <tr>
                    <td>OE:</td>
                    <td>${formData.get('oe_esferico') || '0.00'}</td>
                    <td>${formData.get('oe_cilindrico') || '0.00'}</td>
                    <td>${formData.get('oe_eixo') || '0'}°</td>
                    <td>${formData.get('oe_dnp') || 'N/A'}°</td>
                    <td>${formData.get('oe_altura') || '100 cm'}</td>
                    <td>${formData.get('oe_adicao') || '0.00'}</td>
                </tr>
            </table>
             <p><strong>Diagnóstico:</strong> ${formData.get('diagnostico') || 'N/A'}</p>
        </div>
    `;

            document.getElementById('prescriptionContent').innerHTML = preview;
            const modal = new bootstrap.Modal(document.getElementById('prescriptionModal'));
            modal.show();
        }

        function printPrescription() {
            window.open('/professional/print-prescription/{{ $consulta['id'] }}', '_blank');
        }

        function sendWhatsApp() {
            const phone = '{{ $patient['telefone'] }}';
            const consultaId = '{{ $consulta['id'] }}';

            fetch('/professional/send-whatsapp', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        phone: phone,
                        consulta_id: consultaId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Abrir WhatsApp Web (tenta reutilizar a aba 'whatsappWindow')
                        if (data.whatsapp_url) {
                            const link = document.createElement('a');
                            link.href = data.whatsapp_url;
                            link.target = 'whatsappWindow';
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                        }
                        //alert(data.message);
                        // Fechar modal
                        bootstrap.Modal.getInstance(document.getElementById('prescriptionModal')).hide();
                    } else {
                        alert('Erro ao gerar link do WhatsApp');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao processar solicitação');
                });
        }


        function submitReferral() {
            const formData = new FormData(document.getElementById('referralForm'));

            if (!formData.get('especialidade') || !formData.get('motivo')) {
                alert('Preencha os campos obrigatórios!');
                return;
            }

            fetch('/professional/refer-patient/{{ $consulta->id }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        document.getElementById('referralForm').reset();
                    } else {
                        alert('Erro ao encaminhar paciente');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao encaminhar paciente');
                });
        }

        function finishConsultation() {
            const modal = new bootstrap.Modal(document.getElementById('finishConfirmModal'));
            modal.show();

            const confirmBtn = document.getElementById('confirmFinishBtn');
            confirmBtn.onclick = function() {
                modal.hide();
                const formData = new FormData(document.getElementById('finishForm'));
                formData.append('inicio', new Date().toISOString());

                fetch('/professional/finish-consultation/{{ $consulta->id }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        }
                    })
                    .then(response => {
                        if (response.ok) {
                            showFinishFeedback('Consulta finalizada com sucesso!', true);
                        } else {
                            showFinishFeedback('Erro ao finalizar consulta', false);
                        }
                    })
                    .catch(error => {
                        showFinishFeedback('Erro ao finalizar consulta', false);
                    });
            };
        }

        function showFinishFeedback(message, success) {
            const msgEl = document.getElementById('finishFeedbackMessage');
            msgEl.textContent = message;
            const feedbackModal = new bootstrap.Modal(document.getElementById('finishFeedbackModal'));
            feedbackModal.show();
            const okBtn = document.getElementById('finishFeedbackOk');
            okBtn.onclick = function() {
                feedbackModal.hide();
                if (success) {
                    window.location.href = '/professional';
                }
            };
        }


        function saveProgress() {
            alert('Progresso salvo com sucesso!');
        }

        (function() {
            const sel = document.querySelector('select[name="retorno"]');
            const outro = document.getElementById('retornoOutroContainer');

            function toggle() {
                if (!sel || !outro) return;
                const isOutro = sel.value === 'outro';
                outro.style.display = isOutro ? '' : 'none';
                const input = outro.querySelector('input[name="retorno_outro_dias"]');
                if (input) input.required = isOutro;
            }
            if (sel) {
                sel.addEventListener('change', toggle);
                toggle();
            }
        })();

        function printReferral() {
            const formData = new FormData(document.getElementById('referralForm'));

            // Fill modal with form data
            document.getElementById('referralGlasses').textContent =
                formData.get('usuario_ocular') === 'sim' ? 'Sim' : 'Não';
            document.getElementById('referralLastEval').textContent =
                new Date(formData.get('data')).toLocaleDateString('pt-BR');
            document.getElementById('referralSpecialty').textContent =
                formData.get('especialidade');
            document.getElementById('referralHypothesis').textContent =
                formData.get('motivo');

            const obs = formData.get('observacoes');
            if (obs) {
                document.getElementById('referralObsContainer').classList.remove('d-none');
                document.getElementById('referralObs').textContent = obs;
            } else {
                document.getElementById('referralObsContainer').classList.add('d-none');
            }

            document.getElementById('referralDate').textContent =
                new Date().toLocaleDateString('pt-BR');

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('referralModal'));
            modal.show();
        }

        function printExame() {
            const formData = new FormData(document.getElementById('exameForm'));

            // Fill modal with form data

            document.getElementById('exameAvOd').textContent =
                formData.get('av_od');

            document.getElementById('exameAvOe').textContent =
                formData.get('av_oe');
            document.getElementById('examePioOd').textContent =
                formData.get('pio_od');
            document.getElementById('examePioOe').textContent =
                formData.get('pio_oe');
            document.getElementById('exameFundoscopia').textContent =
                formData.get('fundoscopia');
            document.getElementById('exameAnamnese').textContent =
                formData.get('anamnese');
            const obs = formData.get('observacoes');
            if (obs) {
                document.getElementById('exameObsContainer').classList.remove('d-none');
                document.getElementById('exameObs').textContent = obs;
            } else {
                document.getElementById('exameObsContainer').classList.add('d-none');
            }



            document.getElementById('exameDate').textContent =
                new Date().toLocaleDateString('pt-BR');

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('exameModal'));
            modal.show();
        }

        function printReferralDoc() {
            window.open('{{ route('professional.print-referral', $consulta->id) }}', '_blank');
        }

        function sendReferralWhatsApp() {
            const phone = '{{ $patient['telefone'] }}';

            fetch('{{ route('professional.sendReferralWhatsApp') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        phone: phone,
                        consulta_id: '{{ $consulta->id }}'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.open(data.whatsapp_url, '_blank');
                    } else {
                        alert('Erro ao enviar WhatsApp: ' + (data.message || 'Erro desconhecido'));
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao enviar WhatsApp');
                });
        }

        function emergencyCall() {
            alert('Chamada de emergência ativada!');
        }

        function viewFullHistory() {
            window.location.href =
                "{{ route('professional.patientHistoryFull', $patient['id']) }}?return_to=consultation&consulta_id={{ $consulta->id }}";
        }

        function editPatientData() {
            window.location.href =
                "{{ route('pessoas.edit', $consulta->paciente->id) }}?from=consultation&pid={{ $consulta->paciente->id }}";
        }

        function printExamDoc() {
            window.open('{{ route('professional.print-exame', $consulta->id) }}', '_blank');
        }

        function sendExamWhatsApp() {
            const phone = '{{ $patient['telefone'] }}';

            fetch('{{ route('professional.sendExamWhatsApp') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        phone: phone,
                        consulta_id: '{{ $consulta->id }}'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.open(data.whatsapp_url, '_blank');
                    } else {
                        alert('Erro ao enviar WhatsApp: ' + (data.message || 'Erro desconhecido'));
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao enviar WhatsApp');
                });
        }

        // Salvar automaticamente a cada 5 minutos
        setInterval(function() {
            console.log('Auto-salvando progresso...');
            // Implementar auto-save
        }, 300000);
    </script>
@endpush
