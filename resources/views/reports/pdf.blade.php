@php
    use App\Helpers\AuthHelper;
@endphp
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-size: 10px;
            line-height: 1.5;
            color: #212529;
            background: white;
            margin: 0;
            padding: 4px;
        }

        .card {
            border: none;
            border-radius: 0;
            background-color: #fff;
            box-shadow: none;
        }

        .card-header {
            padding: 0.2rem 1rem;
            margin-bottom: 0;
            background-color: #dee2e6;
            border: 1px solid #dee2e6;
            border-bottom: 1px solid #dee2e6;
            border-top-left-radius: 0.375rem;
            border-top-right-radius: 0.375rem;
        }

        .bg-primary {
            background-color: #0d6efd !important;
        }

        .text-white {
            color: #fff !important;
        }

        .card-body {
            flex: 1 1 auto;
            padding: 0.3rem;
            border: none;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -8px;
            margin-left: -8px;
        }

        .col-6 {
            position: relative;
            width: 100%;
            padding-right: 8px;
            padding-left: 8px;
            flex: 0 0 50%;
            max-width: 50%;
        }

        .col-md-6 {
            position: relative;
            width: 100%;
            padding-right: 8px;
            padding-left: 8px;
            flex: 0 0 50%;
            max-width: 50%;
        }

        .col-12 {
            position: relative;
            width: 100%;
            padding-right: 8px;
            padding-left: 8px;
            flex: 0 0 100%;
            max-width: 100%;
        }

        .text-end {
            text-align: right !important;
        }

        .mb-0 {
            margin-bottom: 0 !important;
        }

        .mb-2 {
            margin-bottom: 0.2rem !important;
        }

        .mb-4 {
            margin-bottom: 0.2rem !important;
        }

        .mt-2 {
            margin-top: 0.5rem !important;
        }

        .mt-5 {
            margin-top: 3rem !important;
        }

        h5 {
            font-size: 1rem;
            margin-bottom: 0.1rem;
            font-weight: 500;
            line-height: 1.1;
        }

        h6 {
            font-size: 0.9rem;
            margin-bottom: 0.1rem;
            font-weight: 500;
            line-height: 1.1;
        }

        small {
            font-size: 0.875em;
        }

        .border {
            border: 1px solid #dee2e6 !important;
        }

        .rounded {
            border-radius: 0.375rem !important;
        }

        .bg-light {
            background-color: #f8f9fa !important;
        }

        .p-3 {
            padding: 0.2rem !important;
        }

        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 0.3rem;
            vertical-align: top;
            border: 1px solid #dee2e6;
        }

        .table-sm th,
        .table-sm td {
            padding: 0.15rem;
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

        .text-center {
            text-align: center !important;
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
            border-radius: 0.375rem;
        }

        .bg-warning {
            background-color: #ffc107 !important;
            color: #000 !important;
        }

        .bg-info {
            background-color: #0dcaf0 !important;
        }

        .bg-success {
            background-color: #198754 !important;
        }

        .bg-danger {
            background-color: #dc3545 !important;
        }

        .bg-dark {
            background-color: #212529 !important;
        }

        .bg-secondary {
            background-color: #6c757d !important;
        }

        .alert {
            position: relative;
            padding: 0.75rem 1.25rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-radius: 0.375rem;
        }

        .alert-warning {
            color: #664d03;
            background-color: #fff3cd;
            border-color: #ffecb5;
        }

        .text-muted {
            color: #6c757d !important;
        }

        .align-items-center {
            align-items: center !important;
        }

        .d-print-block {
            display: block !important;
        }

        /* Específico para as assinaturas */
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 25px;
            padding-top: 4px;
        }

        /* Melhor layout para PDF */
        .row {
            display: table !important;
            width: 100% !important;
            table-layout: fixed !important;
            margin-bottom: 1px !important;
        }

        .col-md-6 {
            display: table-cell !important;
            width: 50% !important;
            vertical-align: top !important;
            padding: 4px 8px !important;
            box-sizing: border-box !important;
        }

        .col-12 {
            display: block !important;
            width: 100% !important;
            margin-bottom: 1px !important;
        }

        /* Evitar quebras indevidas */
        .prescription-section {
            page-break-inside: avoid;
            margin-bottom: 1px;
        }

        .supplier-section {
            page-break-inside: avoid;
            margin-bottom: 1px;
        }

        /* Print styles */
        @media print {
            body {
                margin: 0;
                padding: 10px;
                font-size: 10px;
            }


        }
    </style>
</head>

<body>
    <div class="card">
        <div class="card-header d-print-block"
            style="background-color: #f8f9fa; color: #000; border-bottom: 1px solid #dee2e6;">
            <!-- Logo e Nome da Ótica -->
            <div class="row align-items-center">
                @php
                    $logoBase64 = null;
                    if (AuthHelper::hasTenantLogo()) {
                        try {
                            $logoUrl = AuthHelper::tenantLogoUrl();
                            if ($logoUrl) {
                                $imageData = @file_get_contents($logoUrl);
                                if ($imageData !== false) {
                                    $finfo = new finfo(FILEINFO_MIME_TYPE);
                                    $mimeType = $finfo->buffer($imageData);
                                    $logoBase64 = 'data:' . $mimeType . ';base64,' . base64_encode($imageData);
                                }
                            }
                        } catch (Exception $e) {
                            $logoBase64 = null;
                        }
                    }
                @endphp

                <div style="display: table; width: 100%;">
                    <div style="display: table-cell; vertical-align: middle; width: auto; text-align: left;">
                        @if ($logoBase64)
                            <img src="{{ $logoBase64 }}" alt="{{ AuthHelper::tenantName() ?? 'Logo' }}"
                                style="max-height: 40px; max-width: 40px; object-fit: contain; border: 1px solid #e0e0e0; border-radius: 6px; padding: 4px; background-color: #fff; margin-right: 5px;">
                        @else
                            <span style="font-size: 14px; margin-right: 5px;">👓</span>
                        @endif
                        <h6 style="font-weight: 600; margin: 0; display: inline-block; vertical-align: middle;">
                            {{ AuthHelper::tenantName() ?? 'VisaoSis' }}</h6>
                    </div>
                </div>

                <!-- Segunda linha: Sistema de Gestão e Ordem de Serviço -->
                <div style="display: table; width: 100%; margin-top: 4px;">
                    <div style="display: table-cell; vertical-align: middle; width: 40%; text-align: left;">
                        <small style="color: #6c757d;">Sistema de Gestão Ótica</small>
                        @if (AuthHelper::locationName())
                            <br><small
                                style="color: #6c757d; font-size: 0.7rem;">{{ AuthHelper::locationName() }}</small>
                        @endif
                    </div>
                    <div style="display: table-cell; vertical-align: middle; width: 60%; text-align: left;">
                        <h5 style="margin: 0; font-weight: 600; color: #000;">RELATÓRIO DE ATENDIMENTOS</h5>

                    </div>
                </div>
            </div>
        </div>
    </div>




    <!-- Título do relatório -->
    <div class="report-title">

        <p>Gerado em {{ \Carbon\Carbon::now('America/Sao_Paulo')->format('d/m/Y \à\s H:i') }}</p>
    </div>

    <!-- Informações do período -->
    <div class="period-info">
        <h4>PERÍODO ANALISADO</h4>
        @if ($startDate === $endDate)
            <p><strong>Data:</strong> {{ \Carbon\Carbon::parse($selectedDate)->format('d/m/Y') }}</p>
        @else
            <p><strong>Período:</strong> {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} até
                {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
        @endif

        @if ($selectedProfessional && $selectedProfessionalData)
            <p class="professional-filter"><strong>Profissional:</strong>
                {{ $selectedProfessionalData->nome ?? 'N/A' }}
                ({{ optional($selectedProfessionalData->especialidade)->nome ?? 'Sem especialidade' }})</p>
        @else
            <p class="professional-filter">Todos os profissionais incluídos</p>
        @endif
    </div>

    <!-- Estatísticas gerais -->
    <h3 class="section-title">RESUMO ESTATÍSTICO</h3>
    <div class="stats-grid">
        <div class="stat-card">
            <span class="number highlight-blue">{{ $stats['scheduled'] ?? 0 }}</span>
            <span class="label">Consultas Agendadas</span>
        </div>
        <div class="stat-card">
            <span class="number highlight-green">{{ $stats['attended'] ?? 0 }}</span>
            <span class="label">Atendimentos Realizados</span>
        </div>
        <div class="stat-card">
            <span class="number highlight-red">{{ $stats['cancelled'] ?? 0 }}</span>
            <span class="label">Consultas Canceladas</span>
        </div>
        <div class="stat-card">
            <span class="number highlight-orange">{{ $stats['returns'] ?? 0 }}</span>
            <span class="label">Retornos</span>
        </div>
        <div class="stat-card">
            <span class="number highlight-blue">{{ $stats['referrals'] ?? 0 }}</span>
            <span class="label">Encaminhamentos</span>
        </div>
        <div class="stat-card">
            <span class="number highlight-red">{{ $stats['priority_patients'] ?? 0 }}</span>
            <span class="label">Pacientes Prioritários</span>
        </div>
    </div>

    <!-- Tempos médios -->
    <div class="info-row">
        <div class="info-card">
            <h4>TEMPO MÉDIO DE ESPERA</h4>
            <p class="highlight-blue">{{ $stats['avg_wait_time'] ?? 'N/A' }}</p>
        </div>
        <div class="info-card">
            <h4>TEMPO MÉDIO DE ATENDIMENTO</h4>
            <p class="highlight-green">{{ $stats['avg_service_time'] ?? 'N/A' }}</p>
        </div>
        <div class="info-card">
            <h4>TAXA DE COMPARECIMENTO</h4>
            @php
                $totalScheduled = $stats['scheduled'] ?? 0;
                $totalAttended = $stats['attended'] ?? 0;
                $attendanceRate = $totalScheduled > 0 ? round(($totalAttended / $totalScheduled) * 100, 1) : 0;
            @endphp
            <p class="highlight-blue">{{ $attendanceRate }}%</p>
        </div>
    </div>

    <!-- Estatísticas por profissional -->
    <!-- Produtos -->
    <div class="col-12" style="page-break-inside: avoid; margin-bottom: 1px;">
        <h6>DESEMPENHO POR PROFISSIONAL</h6>
        <div class="p-3">
            <div class="table-responsive">
                @if (count($professionalStats) > 0)
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Profissional</th>
                                <th>Especialidade</th>
                                <th>Agendadas</th>
                                <th>Atendidas</th>
                                <th>Canceladas</th>
                                <th>Taxa Atend.</th>
                                <th>Tempo Médio</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($professionalStats as $stat)
                                <tr>
                                    <td>{{ $stat['name'] }}</td>
                                    <td>{{ $stat['specialty'] ?: 'Não informado' }}</td>
                                    <td>{{ $stat['total'] }}</td>
                                    <td>{{ $stat['attended'] }}</td>
                                    <td>{{ $stat['total'] - $stat['attended'] }}</td>
                                    <td>
                                        @if ($stat['total'] > 0)
                                            {{ round(($stat['attended'] / $stat['total']) * 100, 1) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                    <td>N/A</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p style="text-align: center; color: #666; font-style: italic; margin: 20px 0;">
                        Nenhum dado de profissional encontrado para o período selecionado.
                    </p>
                @endif
            </div>

        </div>
    </div>



    <!-- Observações -->
    <div style="margin-top: 20px; padding: 8px; background: #fef3c7; border: 1px solid #fbbf24; border-radius: 4px;">
        <h4 style="color: #92400e; font-size: 9px; margin-bottom: 4px;">OBSERVAÇÕES</h4>
        <p style="font-size: 8px; color: #92400e;">
            • Os tempos são calculados com base nos registros disponíveis no sistema<br>
            • Taxa de comparecimento = (Atendimentos / Agendamentos) x 100<br>
            • Pacientes prioritários incluem emergências e prioridades altas<br>
            • Relatório gerado automaticamente pelo sistema em
            {{ \Carbon\Carbon::now('America/Sao_Paulo')->format('d/m/Y \à\s H:i') }}
        </p>
    </div>

    <!-- Footer -->
    <div class="footer">
        <span>{{ \App\Helpers\AuthHelper::tenantName() ?? 'Sistema de Gestão' }} - Relatório de Atendimentos</span>
        <span>{{ \Carbon\Carbon::now('America/Sao_Paulo')->format('d/m/Y H:i') }}</span>
    </div>
</body>

</html>
