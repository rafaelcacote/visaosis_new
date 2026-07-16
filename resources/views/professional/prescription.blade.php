<div class="prescription-document">
    <div class="header text-center mb-4">
        <h4 class="mb-1">RECEITA PARA ÓCULOS</h4>
        <p class="mb-0">{{ $prescription['numero'] }} -
            {{ \Carbon\Carbon::parse($prescription['data'])->format('d/m/Y') }}</p>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Dados do Paciente</h6>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Nome:</strong> {{ $prescription['paciente']['nome'] }}</p>
                    <p class="mb-1"><strong>Idade:</strong> {{ $prescription['paciente']['idade'] }} anos</p>
                    <p class="mb-0"><strong>Telefone:</strong> {{ $prescription['paciente']['telefone'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Dados do Profissional</h6>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Nome:</strong> {{ $prescription['profissional']['nome'] }}</p>
                    <p class="mb-1"><strong>
                            {{ $prescription['profissional']['especialidade'] == 'Optometrista' ? 'CBOO:' : 'CRM:' }}</strong>
                        {{ $prescription['profissional']['registro_conselho'] }}</p>
                    <p class="mb-0"><strong>Especialidade:</strong>
                        {{ $prescription['profissional']['especialidade'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="prescription-table mb-4">
        <h6 class="mb-3">Prescrição</h6>

        <div class="table-responsive">
            <table class="table table-bordered table-sm align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 44px;"></th>
                        <th style="width: 90px;"></th>
                        <th class="text-center">Esférico</th>
                        <th class="text-center">Cilíndrico</th>
                        <th class="text-center">Eixo</th>
                        <th class="text-center">AV</th>
                        <th class="text-center">Adição</th>
                        <th class="text-center">DNP</th>
                        <th class="text-center">Altura</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td rowspan="2" class="text-center fw-bold rx-side rx-side--longe">
                            <span class="rx-side__text">LONGE</span>
                        </td>
                        <td class="text-center fw-semibold" style="white-space: nowrap;">
                            <i class="mdi mdi-eye-outline me-1"></i>OD
                        </td>
                        <td class="text-center">{{ $prescription['prescricao']['od_esferico'] ?? '-' }}</td>
                        <td class="text-center">{{ $prescription['prescricao']['od_cilindrico'] ?? '-' }}</td>
                        <td class="text-center">{{ $prescription['prescricao']['od_eixo'] ?? '-' }}</td>
                        <td class="text-center">{{ $prescription['prescricao']['od_acuidade'] ?? '-' }}</td>
                        <td class="text-center">{{ $prescription['prescricao']['od_adicao'] ?? '-' }}</td>
                        <td class="text-center">{{ $prescription['prescricao']['od_dnp'] ?? '-' }}</td>
                        <td class="text-center">{{ $prescription['prescricao']['od_altura'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-center fw-semibold" style="white-space: nowrap;">
                            <i class="mdi mdi-eye-outline me-1"></i>OE
                        </td>
                        <td class="text-center">{{ $prescription['prescricao']['oe_esferico'] ?? '-' }}</td>
                        <td class="text-center">{{ $prescription['prescricao']['oe_cilindrico'] ?? '-' }}</td>
                        <td class="text-center">{{ $prescription['prescricao']['oe_eixo'] ?? '-' }}</td>
                        <td class="text-center">{{ $prescription['prescricao']['oe_acuidade'] ?? '-' }}</td>
                        <td class="text-center">{{ $prescription['prescricao']['oe_adicao'] ?? '-' }}</td>
                        <td class="text-center">{{ $prescription['prescricao']['oe_dnp'] ?? '-' }}</td>
                        <td class="text-center">{{ $prescription['prescricao']['oe_altura'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td rowspan="2" class="text-center fw-bold rx-side rx-side--perto">
                            <span class="rx-side__text">PERTO</span>
                        </td>
                        <td class="text-center fw-semibold" style="white-space: nowrap;">
                            <i class="mdi mdi-eye-outline me-1"></i>OD
                        </td>
                        <td class="text-center">{{ $prescription['prescricao']['od_esferico_perto'] ?? '-' }}</td>
                        <td class="text-center">{{ $prescription['prescricao']['od_cilindrico_perto'] ?? '-' }}</td>
                        <td class="text-center">{{ $prescription['prescricao']['od_eixo_perto'] ?? '-' }}</td>
                        <td class="text-center">{{ $prescription['prescricao']['od_acuidade_perto'] ?? '-' }}</td>
                        <td class="text-center">{{ $prescription['prescricao']['od_adicao_perto'] ?? '-' }}</td>
                        <td class="text-center">{{ $prescription['prescricao']['od_dnp_perto'] ?? '-' }}</td>
                        <td class="text-center">{{ $prescription['prescricao']['od_altura_perto'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-center fw-semibold" style="white-space: nowrap;">
                            <i class="mdi mdi-eye-outline me-1"></i>OE
                        </td>
                        <td class="text-center">{{ $prescription['prescricao']['oe_esferico_perto'] ?? '-' }}</td>
                        <td class="text-center">{{ $prescription['prescricao']['oe_cilindrico_perto'] ?? '-' }}</td>
                        <td class="text-center">{{ $prescription['prescricao']['oe_eixo_perto'] ?? '-' }}</td>
                        <td class="text-center">{{ $prescription['prescricao']['oe_acuidade_perto'] ?? '-' }}</td>
                        <td class="text-center">{{ $prescription['prescricao']['oe_adicao_perto'] ?? '-' }}</td>
                        <td class="text-center">{{ $prescription['prescricao']['oe_dnp_perto'] ?? '-' }}</td>
                        <td class="text-center">{{ $prescription['prescricao']['oe_altura_perto'] ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="row mt-3">
            <div class="col-md-6">
                <p><strong>Tipo de Lente:</strong> {{ $prescription['prescricao']['tipo_lente'] }}</p>
            </div>
        </div>
    </div>

    <div class="diagnosis mb-4">
        <h6>Diagnóstico</h6>
        <p class="border p-3 bg-light">{{ $prescription['diagnostico'] }}</p>
    </div>

    @if ($prescription['observacoes'])
        <div class="observations mb-4">
            <h6>Observações</h6>
            <p class="border p-3 bg-light">{{ $prescription['observacoes'] }}</p>
        </div>
    @endif

    @if ($prescription['recomendacoes'])
        <div class="recommendations mb-4">
            <h6>Recomendações</h6>
            <p class="border p-3 bg-light">{{ $prescription['recomendacoes'] }}</p>
        </div>
    @endif

    <div class="footer mt-5">
        <div class="row">
            <div class="col-md-6">
                <p class="text-muted">
                    <small>
                        Data de emissão: {{ \Carbon\Carbon::parse($prescription['data'])->format('d/m/Y') }}<br>
                        Receita válida por {{ $prescription['prescricao']['validade_dias'] }} dias.<br>
                    </small>
                </p>
            </div>
            <div class="col-md-6 text-end">
                <div class="signature-area">
                    <hr style="width: 300px; margin-left: auto;">
                    <p class="mb-0">
                        <strong>{{ $prescription['profissional']['nome'] ?? ($prescription['profissional']->nome ?? '') }}</strong>
                    </p>
                    <p class="text-muted"><small>
                            @php
                                $prof = $prescription['profissional'];
                                $espDesc = is_array($prof)
                                    ? $prof['especialidade'] ?? null
                                    : optional($prof->especialidade)->descricao ?? null;
                                $label = $espDesc === 'Optometrista' ? 'CBOO:' : 'CRM:';
                                $reg = is_array($prof)
                                    ? $prof['registro_conselho'] ?? ($prof['crm'] ?? null)
                                    : $prof->registro_conselho ?? null;
                            @endphp
                            <span>{{ $label }}</span>{{ $reg }}
                        </small></p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .prescription-document {
        background: white;
        padding: 30px;
        font-family: 'Arial', sans-serif;
        line-height: 1.6;
    }

    .prescription-table table {
        font-size: 14px;
    }

    .prescription-table th,
    .prescription-table td {
        padding: 12px 8px;
        vertical-align: middle;
    }

    .signature-area {
        margin-top: 40px;
    }

    .rx-side__text {
        display: inline-block;
        writing-mode: vertical-rl;
        transform: rotate(180deg);
        letter-spacing: 0.12em;
    }

    .rx-side--longe {
        color: #0d6efd;
        border: 1px solid rgba(13, 110, 253, 0.45) !important;
    }

    .rx-side--perto {
        color: #dc3545;
        border: 1px solid rgba(220, 53, 69, 0.45) !important;
    }

    @media print {
        .prescription-document {
            padding: 20px;
            box-shadow: none;
        }

        .card {
            border: 1px solid #dee2e6 !important;
            box-shadow: none !important;
        }
    }
</style>
