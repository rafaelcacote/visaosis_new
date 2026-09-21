@php
    use App\Helpers\AuthHelper;
@endphp
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Contas a Receber</title>
    <style>
        {!! file_get_contents(resource_path('views/pdf/styles/header-footer.css')) !!} body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-size: 10px;
            line-height: 1.5;
            color: #212529;
            background: white;
            margin: 0;
            padding: 4px;
        }

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

        .col-md-3 {
            display: table-cell !important;
            width: 25% !important;
            vertical-align: top !important;
            padding: 4px 8px !important;
            box-sizing: border-box !important;
        }

        .p-3 {
            padding: 0.4rem !important;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            margin: 15px 0 8px 0;
            color: #212529;
        }

        .stats-row {
            display: table;
            width: 100%;
            table-layout: fixed;
            margin-bottom: 15px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }

        .stat-column {
            display: table-cell;
            width: 33.333%;
            text-align: center;
            padding: 12px 8px;
            border-right: 1px solid #dee2e6;
            vertical-align: middle;
        }

        .stat-column:last-child {
            border-right: none;
        }

        .stat-number {
            display: block;
            font-size: 18px;
            font-weight: bold;
            color: #212529;
            margin-bottom: 2px;
        }

        .stat-label {
            display: block;
            font-size: 9px;
            color: #6c757d;
            margin: 0;
        }

        .stat-sublabel {
            display: block;
            font-size: 9px;
            font-weight: bold;
            margin-top: 2px;
        }

        .stat-number-danger,
        .stat-sublabel-danger {
            color: #b91c1c;
        }

        .stat-number-warning,
        .stat-sublabel-warning {
            color: #b45309;
        }

        .stat-number-info,
        .stat-sublabel-info {
            color: #1d4ed8;
        }

        .stat-number-success,
        .stat-sublabel-success {
            color: #166534;
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

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #dee2e6;
        }

        .text-end {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        .status-vencida {
            color: #b91c1c;
            font-weight: bold;
        }

        .status-vence_hoje {
            color: #b45309;
            font-weight: bold;
        }

        .status-vence_semana {
            color: #1d4ed8;
            font-weight: bold;
        }

        .status-paga,
        .status-em_dia {
            color: #166534;
            font-weight: bold;
        }

        .status-pagamento_parcial,
        .status-saldo_remanescente {
            color: #6c757d;
            font-weight: bold;
        }

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
    @php
        $statusLabelsMap = [
            'vencida' => 'Vencida',
            'vence_hoje' => 'Vence Hoje',
            'vence_semana' => 'Vence na Semana',
            'em_dia' => 'Em Dia',
            'paga' => 'Paga',
            'pagamento_parcial' => 'Pagamento Parcial',
            'saldo_remanescente' => 'Saldo Remanescente',
        ];

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

    <header class="pdf-header">
        <table class="pdf-header-top">
            <tr>
                <td class="brand-wrap">
                    @if ($logoBase64)
                        <img src="{{ $logoBase64 }}" alt="{{ AuthHelper::tenantName() ?? 'Empresa' }}"
                            class="brand-logo">
                    @else
                        <span class="brand-logo-fallback">LOGO</span>
                    @endif
                    <span class="brand-block">
                        <h1 class="brand-title">{{ AuthHelper::tenantName() ?? 'VisaoSis' }}</h1>
                        <p class="brand-subtitle">
                            Sistema de Gestao Otica
                            @if (AuthHelper::locationName())
                                | {{ AuthHelper::locationName() }}
                            @endif
                        </p>
                    </span>
                </td>
                <td class="title-wrap">
                    <h2 class="doc-title">RELATORIO DE CONTAS A RECEBER</h2>
                    <p class="doc-subtitle">Emissao: {{ now('America/Sao_Paulo')->format('d/m/Y H:i') }}</p>
                </td>
            </tr>
        </table>
    </header>

    <!-- Filtros aplicados -->
    <div class="col-12" style="page-break-inside: avoid; margin-bottom: 1px;">
        <h3 class="section-title">FILTROS APLICADOS</h3>
        <div class="p-3">
            <div class="row mb-2">
                <div class="col-md-3">
                    <p><strong>Busca:</strong> {{ $filters['q'] !== '' ? $filters['q'] : 'Todos' }}</p>
                </div>
                <div class="col-md-3">
                    <p><strong>Status:</strong> {{ $filters['status_label'] }}</p>
                </div>
                <div class="col-md-3">
                    <p><strong>Período:</strong>
                        @if ($filters['start_date'] || $filters['end_date'])
                            {{ $filters['start_date'] ? \Carbon\Carbon::parse($filters['start_date'])->format('d/m/Y') : '...' }}
                            até
                            {{ $filters['end_date'] ? \Carbon\Carbon::parse($filters['end_date'])->format('d/m/Y') : '...' }}
                        @else
                            Todo o período
                        @endif
                    </p>
                </div>
                <div class="col-md-3">
                    <p><strong>Ordenado por:</strong> {{ $filters['order_by_label'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumo Estatístico -->
    <div class="col-12" style="page-break-inside: avoid; margin-bottom: 1px;">
        <h3 class="section-title">RESUMO ESTATÍSTICO</h3>
        <div class="p-3">
            <div class="stats-row" style="margin-bottom: 8px;">
                <div class="stat-column" style="width: 20%;">
                    <span class="stat-number">{{ $totalCount }}</span>
                    <span class="stat-label">Parcelas Encontradas</span>
                </div>
                <div class="stat-column" style="width: 20%;">
                    <span class="stat-number">{{ (int) ($summary['vencidas']['count'] ?? 0) }}</span>
                    <span class="stat-label">Parcelas Vencidas</span>
                    <span class="stat-sublabel">R$
                        {{ number_format((float) ($summary['vencidas']['valor'] ?? 0), 2, ',', '.') }}</span>
                </div>
                <div class="stat-column" style="width: 20%;">
                    <span class="stat-number">{{ (int) ($summary['vence_hoje']['count'] ?? 0) }}</span>
                    <span class="stat-label">Vencem Hoje</span>
                    <span class="stat-sublabel">R$
                        {{ number_format((float) ($summary['vence_hoje']['valor'] ?? 0), 2, ',', '.') }}</span>
                </div>
                <div class="stat-column" style="width: 20%;">
                    <span class="stat-number">{{ (int) ($summary['vence_semana']['count'] ?? 0) }}</span>
                    <span class="stat-label">Vencem Esta Semana</span>
                    <span class="stat-sublabel">R$
                        {{ number_format((float) ($summary['vence_semana']['valor'] ?? 0), 2, ',', '.') }}</span>
                </div>
                <div class="stat-column" style="width: 20%;">
                    <span class="stat-number">{{ (int) ($summary['em_dia']['count'] ?? 0) }}</span>
                    <span class="stat-label">Em Dia</span>
                    <span class="stat-sublabel">R$
                        {{ number_format((float) ($summary['em_dia']['valor'] ?? 0), 2, ',', '.') }}</span>
                </div>
            </div>

        </div>
    </div>

    <!-- Lista de Parcelas -->
    <div class="col-12" style="margin-bottom: 1px;">
        <h3 class="section-title">PARCELAS A RECEBER</h3>
        <div class="p-3">
            @if ($receivables->isEmpty())
                <p style="text-align: center; color: #666; font-style: italic; margin: 20px 0;">
                    Nenhuma parcela encontrada para os filtros selecionados.
                </p>
            @else
                <table class="table table-bordered table-sm" style="font-size: 9px;">
                    <thead>
                        <tr>
                            <th style="background-color: #f8f9fa;">Cliente</th>
                            <th style="background-color: #f8f9fa;">Venda</th>
                            <th style="background-color: #f8f9fa;">Parcela</th>
                            <th style="background-color: #f8f9fa;">Vencimento</th>
                            <th style="background-color: #f8f9fa;" class="text-end">Valor</th>
                            <th style="background-color: #f8f9fa;" class="text-center">Status</th>
                            <th style="background-color: #f8f9fa;" class="text-center">Dias Atraso</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($receivables as $item)
                            <tr>
                                <td>{{ $item['cliente'] }}</td>
                                <td>{{ $item['venda_id'] }}</td>
                                <td>{{ $item['parcela'] }}</td>
                                <td>{{ \Carbon\Carbon::parse($item['vencimento'])->format('d/m/Y') }}</td>
                                <td class="text-end">R$ {{ number_format($item['valor_parcela'], 2, ',', '.') }}</td>
                                <td class="text-center status-{{ $item['status'] }}">
                                    {{ $statusLabelsMap[$item['status']] ?? $item['status'] }}
                                </td>
                                <td class="text-center">{{ $item['dias_atraso'] > 0 ? $item['dias_atraso'] : '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end"><strong>Total</strong></td>
                            <td class="text-end"><strong>R$ {{ number_format($totalValor, 2, ',', '.') }}</strong></td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            @endif
        </div>
    </div>

    <footer class="pdf-footer">
        <table class="pdf-footer-table">
            <tr>
                <td class="pdf-footer-left">Documento gerado por {{ AuthHelper::tenantName() ?? 'VisaoSis' }}</td>
                <td class="pdf-footer-center"></td>
                <td class="pdf-footer-right">Relatorio de Contas a Receber</td>
            </tr>
        </table>
    </footer>
</body>

</html>
