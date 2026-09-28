@php
    use App\Helpers\AuthHelper;
@endphp
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Vendas Detalhado</title>
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

        .col-md-3 {
            display: table-cell !important;
            width: 25% !important;
            vertical-align: top !important;
            padding: 4px 8px !important;
            box-sizing: border-box !important;
        }

        .col-12 {
            display: block !important;
            width: 100% !important;
            margin-bottom: 1px !important;
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

        .venda-title {
            font-size: 10px;
            font-weight: bold;
            margin: 10px 0 4px 0;
            color: #212529;
            background-color: #f1f3f5;
            padding: 4px 6px;
        }

        .venda-header-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
        }

        .venda-header-table td {
            border: none;
            padding: 0 4px;
            vertical-align: middle;
        }

        .venda-header-table .col-cliente {
            width: 40%;
            text-align: left;
        }

        .venda-header-table .col-valor {
            width: 16%;
            text-align: right;
        }

        .venda-header-table .col-status {
            width: 12%;
            text-align: center;
        }

        .venda-header-table .col-label {
            display: block;
            font-size: 8px;
            font-weight: normal;
            color: #6c757d;
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
            width: 25%;
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
            font-size: 16px;
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

        .text-end {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        .venda-block {
            page-break-inside: avoid;
            margin-bottom: 10px;
        }

        .status-quitada {
            color: #166534;
            font-weight: bold;
        }

        .status-parcial {
            color: #b45309;
            font-weight: bold;
        }

        .status-pendente {
            color: #b91c1c;
            font-weight: bold;
        }

        .status-cancelada {
            color: #6c757d;
            font-weight: bold;
        }

        .status-paga {
            color: #166534;
            font-weight: bold;
        }

        .status-vencida {
            color: #b91c1c;
            font-weight: bold;
        }

        .status-vence_hoje {
            color: #b45309;
            font-weight: bold;
        }

        .status-pagamento_parcial {
            color: #b45309;
            font-weight: bold;
        }

        .status-em_dia {
            color: #1d4ed8;
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
        $statusRecebimentoLabels = [
            'quitada' => 'Quitada',
            'parcial' => 'Pagamento Parcial',
            'pendente' => 'Pendente',
            'cancelada' => 'Cancelada',
        ];

        $parcelaStatusLabels = [
            'paga' => 'Paga',
            'vencida' => 'Vencida',
            'vence_hoje' => 'Vence Hoje',
            'pagamento_parcial' => 'Parcial',
            'em_dia' => 'Em Dia',
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
                    <h2 class="doc-title">RELATORIO DE VENDAS DETALHADO</h2>
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
                    <p><strong>Data Início:</strong> {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</p>
                </div>
                <div class="col-md-3">
                    <p><strong>Data Fim:</strong> {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
                </div>
                <div class="col-md-3">
                    <p><strong>Status:</strong>
                        {{ $status !== '' ? $statusRecebimentoLabels[$status] ?? ucfirst($status) : 'Todos' }}</p>
                </div>
                <div class="col-md-3">
                    <p><strong>Vendedor:</strong> {{ $vendedorNome ?? 'Todos' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumo Estatístico -->
    <div class="col-12" style="page-break-inside: avoid; margin-bottom: 1px;">
        <h3 class="section-title">RESUMO ESTATÍSTICO</h3>
        <div class="p-3">
            <div class="stats-row">
                <div class="stat-column">
                    <span class="stat-number">{{ $stats['total'] }}</span>
                    <span class="stat-label">Vendas</span>
                </div>
                <div class="stat-column">
                    <span class="stat-number">R$ {{ number_format($stats['valor_total'], 2, ',', '.') }}</span>
                    <span class="stat-label">Valor Total</span>
                </div>
                <div class="stat-column">
                    <span class="stat-number">R$ {{ number_format($stats['valor_recebido'], 2, ',', '.') }}</span>
                    <span class="stat-label">Total Recebido</span>
                </div>
                <div class="stat-column">
                    <span class="stat-number">R$ {{ number_format($stats['valor_pendente'], 2, ',', '.') }}</span>
                    <span class="stat-label">A Receber</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Vendas Detalhadas -->
    <div class="col-12" style="margin-bottom: 1px;">
        <h3 class="section-title">VENDAS DETALHADAS</h3>
        <div class="p-3">
            @if ($rows->isEmpty())
                <p style="text-align: center; color: #666; font-style: italic; margin: 20px 0;">
                    Nenhuma venda encontrada para os filtros selecionados.
                </p>
            @else
                @foreach ($rows as $venda)
                    <div class="venda-block">
                        <div class="venda-title">
                            <table class="venda-header-table">
                                <tr>
                                    <td class="col-cliente">
                                        {{ $venda['numero'] }} — {{ $venda['cliente'] }}
                                        <span class="col-label">Vendedor: {{ $venda['vendedor'] }} ·
                                            {{ $venda['data_pedido']->format('d/m/Y H:i') }}</span>
                                    </td>
                                    <td class="col-valor">
                                        <span class="col-label">Total</span>
                                        R$ {{ number_format($venda['valor_total'], 2, ',', '.') }}
                                    </td>
                                    <td class="col-valor">
                                        <span class="col-label">Recebido</span>
                                        R$ {{ number_format($venda['valor_recebido'], 2, ',', '.') }}
                                    </td>
                                    <td class="col-valor">
                                        <span class="col-label">A Receber</span>
                                        R$ {{ number_format($venda['valor_pendente'], 2, ',', '.') }}
                                    </td>
                                    <td class="col-status">
                                        <span class="col-label">Situação</span>
                                        <span class="status-{{ $venda['status_recebimento'] }}">
                                            {{ $statusRecebimentoLabels[$venda['status_recebimento']] ?? $venda['status_recebimento'] }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <table class="table table-bordered table-sm" style="font-size: 9px; table-layout: fixed;">
                            <colgroup>
                                <col style="width: 12%;">
                                <col style="width: 14%;">
                                <col style="width: 16%;">
                                <col style="width: 16%;">
                                <col style="width: 16%;">
                                <col style="width: 26%;">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th style="background-color: #f8f9fa;">Parcela</th>
                                    <th style="background-color: #f8f9fa;">Vencimento</th>
                                    <th style="background-color: #f8f9fa;" class="text-end">Valor</th>
                                    <th style="background-color: #f8f9fa;" class="text-end">Recebido</th>
                                    <th style="background-color: #f8f9fa;" class="text-center">Status</th>
                                    <th style="background-color: #f8f9fa;">Forma Pgto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($venda['parcelas'] as $parcela)
                                    <tr>
                                        <td>{{ $parcela['parcela'] }}</td>
                                        <td>
                                            {{ $parcela['vencimento'] ? \Carbon\Carbon::parse($parcela['vencimento'])->format('d/m/Y') : '—' }}
                                        </td>
                                        <td class="text-end">R$ {{ number_format($parcela['valor'], 2, ',', '.') }}
                                        </td>
                                        <td class="text-end">R$
                                            {{ number_format($parcela['valor_recebido'], 2, ',', '.') }}</td>
                                        <td class="text-center status-{{ $parcela['status'] }}">
                                            {{ $parcelaStatusLabels[$parcela['status']] ?? $parcela['status'] }}
                                        </td>
                                        <td>{{ $parcela['forma_pagamento'] ?: '—' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Nenhuma parcela cadastrada.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <footer class="pdf-footer">
        <table class="pdf-footer-table">
            <tr>
                <td class="pdf-footer-left">Documento gerado por {{ AuthHelper::tenantName() ?? 'VisaoSis' }}</td>
                <td class="pdf-footer-center"></td>
                <td class="pdf-footer-right">Relatorio de Vendas Detalhado</td>
            </tr>
        </table>
    </footer>
</body>

</html>
