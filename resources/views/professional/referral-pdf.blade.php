<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Termo de Encaminhamento</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 14px;
            line-height: 1.5;
            color: #333;
        }

        .text-center {
            text-align: center;
        }

        .text-end {
            text-align: right;
        }

        .mb-4 {
            margin-bottom: 0.5rem;
        }

        .mb-3 {
            margin-bottom: 0.5rem;
        }

        .mb-2 {
            margin-bottom: 0.5rem;
        }

        .mb-1 {
            margin-bottom: 0.25rem;
        }

        .mb-0 {
            margin-bottom: 0;
        }

        .mt-3 {
            margin-top: 1rem;
        }

        .mt-4 {
            margin-top: 1.5rem;
        }

        .fw-bold {
            font-weight: bold;
        }

        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
        }

        .table-bordered {
            border: 1px solid #dee2e6;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #dee2e6;
        }

        .table-light {
            background-color: #f8f9fa;
        }

        /* Grid system simulation for PDF */
        .row {
            width: 100%;
            clear: both;
        }

        .col-md-6 {
            width: 48%;
            float: left;
        }

        .col-md-6:first-child {
            margin-right: 4%;
        }

        /* Specific overrides */
        .prescription-document {
            padding: 0 !important;
        }

        .bg-light {
            background-color: #f8f9fa !important;
        }

        .card-header {
            padding: 0.5rem 1rem;
            margin-bottom: 0;
            background-color: rgba(0, 0, 0, 0.03);
            border-bottom: 1px solid rgba(0, 0, 0, 0.125);
        }

        .card-header h6 {
            font-size: 16px;
            font-weight: bold;
            margin: 0;
        }

        /* Estilos para títulos de seções */
        .section-title h6 {
            font-size: 16px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
            text-align: left;
            padding-left: 0;
        }

        /* Garantir que as seções limpem os floats anteriores */
        .section-container {
            clear: both;
            width: 100%;
            display: block;
        }

        /* Reduzir espaçamento entre parágrafos */
        p {
            margin-bottom: 5px;
            line-height: 1.2;
        }

        /* Ajustes da área de assinatura */
        .signature-area {
            margin-top: 20px;
        }

        .signature-area hr {
            margin-bottom: 5px;
            border: 0;
            border-top: 1px solid #000;
        }

        .signature-area p {
            margin-top: 0;
            margin-bottom: 0;
            line-height: 1.1;
        }

        .border {
            border: 1px solid #dee2e6 !important;
        }

        .p-3 {
            padding: 1rem !important;
        }

        .badge {
            display: inline-block;
            padding: 0.35em 0.65em;
            font-size: 0.75em;
            font-weight: 700;
            line-height: 1;
            color: #fff;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
        }

        .bg-danger {
            background-color: #dc3545 !important;
        }

        .bg-warning {
            background-color: #ffc107 !important;
            color: #000 !important;
        }

        .bg-success {
            background-color: #198754 !important;
        }

        .bg-secondary {
            background-color: #6c757d !important;
        }
    </style>
</head>

<body>
    <div class="prescription-document">
        <div class="header text-center mb-4">
            <h4 class="mb-1">TERMO DE ENCAMINHAMENTO</h4>
            <p class="mb-0">
                REF-{{ \Carbon\Carbon::parse($referral['data'])->format('Y') }}-{{ str_pad($referral['id'], 4, '0', STR_PAD_LEFT) }}
                -
                {{ \Carbon\Carbon::parse($referral['data'])->format('d/m/Y') }}</p>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Dados do Paciente</h6>
                    </div>
                    <div class="card-body p-3 border">
                        <p class="mb-1"><strong>Nome:</strong> {{ $referral['paciente']['nome'] }}</p>
                        <p class="mb-1"><strong>Idade:</strong> {{ $referral['paciente']['idade'] }} anos</p>
                        <p class="mb-0"><strong>Telefone:</strong> {{ $referral['paciente']['telefone'] }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Profissional Solicitante</h6>
                    </div>
                    <div class="card-body p-3 border">
                        <p class="mb-1"><strong>Nome:</strong> {{ $referral['profissional']['nome'] }}</p>
                        <p class="mb-1"><strong>
                                {{ $referral['profissional']['especialidade'] == 'Optometrista' ? 'CBOO:' : 'CRM:' }}</strong>
                            {{ $referral['profissional']['registro_conselho'] }}</p>
                        <p class="mb-0"><strong>Especialidade:</strong>
                            {{ $referral['profissional']['especialidade'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="section-container mb-4">
            <div class="section-title">
                <h6 class="mb-3">Detalhes do Encaminhamento</h6>
            </div>

            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <td width="40%" class="bg-light fw-bold">Especialidade de Destino</td>
                        <td>{{ $referral['dados']['especialidade_destino'] }}</td>
                    </tr>
                    <tr>
                        <td class="bg-light fw-bold">Urgência</td>
                        <td>
                            <span
                                class="badge {{ $referral['dados']['urgencia'] === 'Emergência' ? 'bg-danger' : ($referral['dados']['urgencia'] === 'Urgente' ? 'bg-warning' : 'bg-success') }}">
                                {{ $referral['dados']['urgencia'] }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="bg-light fw-bold">Usuário de Óculos</td>
                        <td>{{ $referral['dados']['usuario_oculos'] ? 'Sim' : 'Não' }}</td>
                    </tr>
                    <tr>
                        <td class="bg-light fw-bold">Data da Última Avaliação</td>
                        <td>{{ $referral['dados']['ultima_avaliacao'] }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        @if ($referral['dados']['hipotese'])
            <div class="section-container mb-4">
                <div class="section-title">
                    <h6>Hipótese de Encaminhamento</h6>
                </div>
                <p class="border p-3 bg-light">{{ $referral['dados']['hipotese'] }}</p>
            </div>
        @endif

        @if ($referral['dados']['observacoes'])
            <div class="section-container mb-4">
                <div class="section-title">
                    <h6>Observações Adicionais</h6>
                </div>
                <p class="border p-3 bg-light">{{ $referral['dados']['observacoes'] }}</p>
            </div>
        @endif

        <div class="footer mt-5">
            <div class="row">
                <div class="col-md-6">
                    <p class="text-muted">
                        <small>
                            Data de emissão: {{ \Carbon\Carbon::parse($referral['data'])->format('d/m/Y') }}<br>
                        </small>
                    </p>
                </div>
                <div class="col-md-6 text-end">
                    <div class="signature-area">
                        <hr style="width: 300px; margin-left: auto;">
                        <p class="mb-0">
                            <strong>{{ $referral['profissional']['nome'] }}</strong>
                        </p>
                        <p class="text-muted"><small>
                                <span>{{ $referral['profissional']['especialidade'] == 'Optometrista' ? 'CBOO:' : 'CRM:' }}</span>
                                {{ $referral['profissional']['registro_conselho'] }}
                            </small></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
