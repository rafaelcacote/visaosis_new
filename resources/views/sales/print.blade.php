<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprovante de Venda {{ $sale['numero'] }}</title>
    <style>
        {!! file_get_contents(resource_path('views/pdf/styles/header-footer.css')) !!} body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
            line-height: 1.45;
            color: #111827;
        }

        .section {
            margin-bottom: 10px;
            page-break-inside: avoid;
        }

        .section-title {
            margin: 0 0 6px;
            padding: 5px 8px;
            background: #f1f5f9;
            border-left: 3px solid #1d4ed8;
            font-size: 11px;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .info-grid {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .info-grid td {
            width: 50%;
            vertical-align: top;
            padding: 3px 8px 3px 0;
        }

        .label {
            color: #475569;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.2px;
            font-weight: 400;
        }

        .value {
            display: inline;
            color: #111827;
            font-weight: 600;
            word-wrap: break-word;
        }

        .pill {
            display: inline-block;
            border-radius: 999px;
            padding: 2px 8px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-right: 4px;
            border: 1px solid transparent;
        }

        .pill-faturado {
            background: #ecfdf5;
            color: #166534;
            border-color: #bbf7d0;
        }

        .pill-aberto {
            background: #fff7ed;
            color: #9a3412;
            border-color: #fdba74;
        }

        .pill-cancelado {
            background: #fef2f2;
            color: #b91c1c;
            border-color: #fecaca;
        }

        .table-data {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .table-data th,
        .table-data td {
            border: 1px solid #dbe3ee;
            padding: 6px 7px;
            font-size: 10px;
            vertical-align: top;
        }

        .table-data th {
            background: #f8fafc;
            color: #334155;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 0.3px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .summary-block {
            margin-top: 6px;
            text-align: right;
            font-size: 10px;
            line-height: 1.5;
        }

        .summary-line {
            margin: 0;
            padding: 1px 0;
        }

        .summary-total-line {
            margin: 3px 0 0;
            padding: 4px 0 0;
            border-top: 1px solid #94a3b8;
            font-size: 11px;
            color: #0f172a;
        }

        .status-label {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 999px;
            font-size: 9px;
            font-weight: 700;
            border: 1px solid transparent;
        }

        .status-vencida {
            background: #fef2f2;
            color: #b91c1c;
            border-color: #fecaca;
        }

        .status-vence-hoje {
            background: #fffbeb;
            color: #b45309;
            border-color: #fde68a;
        }

        .status-vence-semana {
            background: #eff6ff;
            color: #1d4ed8;
            border-color: #bfdbfe;
        }

        .status-paga,
        .status-em-dia {
            background: #ecfdf5;
            color: #166534;
            border-color: #bbf7d0;
        }

        .note-box {
            border: 1px solid #dbe3ee;
            background: #fafcff;
            padding: 8px;
            font-size: 10px;
            color: #0f172a;
            min-height: 30px;
        }
    </style>
</head>

<body>
    <header class="pdf-header">
        <table class="pdf-header-top">
            <tr>
                <td class="brand-wrap">
                    @if (!empty($tenantData['logo_base64']))
                        <img src="{{ $tenantData['logo_base64'] }}" alt="{{ $tenantData['nome'] }}" class="brand-logo">
                    @elseif (!empty($tenantData['logo_url']))
                        <img src="{{ $tenantData['logo_url'] }}" alt="{{ $tenantData['nome'] }}" class="brand-logo">
                    @else
                        <span class="brand-logo-fallback">LOGO</span>
                    @endif
                    <span class="brand-block">
                        <h1 class="brand-title">{{ $tenantData['nome'] }}</h1>
                        <p class="brand-subtitle">
                            Sistema de Gestao Otica
                        </p>
                    </span>
                </td>
                <td class="title-wrap">
                    <h2 class="doc-title">COMPROVANTE DE VENDA</h2>
                    <p class="doc-subtitle">{{ $sale['numero'] }} &nbsp;|&nbsp; Emissao:
                        {{ now('America/Sao_Paulo')->format('d/m/Y H:i') }}</p>
                </td>
            </tr>
        </table>
    </header>

    <main>
        <section class="section">
            <h3 class="section-title">Dados da Venda</h3>
            <table class="info-grid">
                <tr>
                    <td>
                        <span class="label">Numero da Venda:</span>
                        <span class="value">{{ $sale['numero'] }}</span>
                    </td>
                    <td>
                        <span class="label">Data da Venda:</span>
                        <span class="value">{{ $sale['data_formatada'] }}
                            @if (!empty($sale['hora_formatada']))
                                {{ $sale['hora_formatada'] }}
                            @endif
                        </span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="label">Vendedor:</span>
                        <span class="value">{{ $vendedor['nome'] ?? 'Nao informado' }}</span>
                    </td>

                    <td>
                        <span class="label">Forma de Pagamento:</span>
                        @if (!empty($sale['pagamentos']))
                            @foreach ($sale['pagamentos'] as $pag)
                                <span class="value">
                                    {{ $pag['forma_pagamento'] }}

                                </span><br>
                            @endforeach
                        @else
                            <span class="value">{{ $sale['forma_pagamento'] ?? 'Nao informado' }}</span>
                        @endif
                    </td>
                </tr>
                @if (!empty($sale['parcelas']) && $sale['parcelas'] > 1)
                    <tr>
                        <td>
                            <span class="label">Parcelas:</span>
                            <span class="value">{{ $sale['parcelas'] }}x de R$
                                {{ number_format($sale['valor_parcela'], 2, ',', '.') }}</span>
                        </td>
                        <td></td>
                    </tr>
                @endif

            </table>
        </section>

        <section class="section">
            <h3 class="section-title">Dados do Cliente</h3>
            @if (!empty($sale['cliente']))
                <table class="info-grid">
                    <tr>
                        <td colspan="2">
                            <span class="label">Nome:</span>
                            <span class="value">{{ $sale['cliente']['nome'] ?? 'Nao informado' }}</span>
                        </td>

                    </tr>
                    <tr>
                        <td>
                            <span class="label">Telefone:</span>
                            <span class="value">{{ $sale['cliente']['telefone'] ?? 'Nao informado' }}</span>
                        </td>
                        <td>
                            <span class="label">E-mail:</span>
                            <span class="value">{{ $sale['cliente']['email'] ?? 'Nao informado' }}</span>
                        </td>
                    </tr>
                    @if (!empty($sale['cliente']['endereco']))
                        <tr>
                            <td colspan="2">
                                <span class="label">Endereco:</span>
                                <span class="value">{{ $sale['cliente']['endereco'] }}</span>
                            </td>
                        </tr>
                    @endif
                </table>
            @else
                <p style="font-size:10px; color:#6b7280;">Cliente nao informado.</p>
            @endif
        </section>

        <section class="section">
            <h3 class="section-title">Produtos</h3>
            <table class="table-data">
                <thead>
                    <tr>
                        <th style="width: 5%;" class="text-center">#</th>
                        <th style="width: 43%;">Descricao</th>
                        <th style="width: 10%;" class="text-center">Qtd</th>
                        <th style="width: 15%;" class="text-right">Preco Unit.</th>
                        <th style="width: 12%;" class="text-right">Desconto</th>
                        <th style="width: 15%;" class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sale['produtos'] as $index => $produto)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td><strong>{{ $produto['nome'] }}</strong></td>
                            <td class="text-center">{{ $produto['quantidade'] }}</td>
                            <td class="text-right">R$ {{ number_format($produto['preco_unitario'], 2, ',', '.') }}</td>
                            <td class="text-right">
                                @if (!empty($produto['desconto']) && $produto['desconto'] > 0)
                                    R$ {{ number_format($produto['desconto'], 2, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-right"><strong>R$
                                    {{ number_format($produto['subtotal'], 2, ',', '.') }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @php $descontoAplicado = max(0, $sale['subtotal'] - $sale['total']); @endphp
            <div class="summary-block">
                <p class="summary-line"><strong>Subtotal:</strong> R$
                    {{ number_format($sale['subtotal'], 2, ',', '.') }}</p>
                <p class="summary-line"><strong>Desconto sobre total:</strong> R$
                    {{ number_format($descontoAplicado, 2, ',', '.') }}</p>
                <p class="summary-total-line"><strong>TOTAL: R$
                        {{ number_format($sale['total'], 2, ',', '.') }}</strong></p>
            </div>
        </section>

        @if (collect($sale['parcelas_detalhes'] ?? [])->count() > 0)
            <section class="section">
                <h3 class="section-title">Parcelas da Venda</h3>
                <table class="table-data">
                    <thead>
                        <tr>
                            <th style="width: 16%;">Parcela</th>
                            <th style="width: 20%;">Vencimento</th>
                            <th style="width: 22%;">Valor</th>
                            <th style="width: 20%;">Status</th>
                            <th style="width: 22%;">Pago em</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sale['parcelas_detalhes'] as $parcela)
                            @php
                                $statusLabel = 'Em Dia';
                                $statusClass = 'status-em-dia';

                                if ($parcela['status'] === 'vencida') {
                                    $statusLabel = 'Vencida';
                                    $statusClass = 'status-vencida';
                                } elseif ($parcela['status'] === 'vence_hoje') {
                                    $statusLabel = 'Vence Hoje';
                                    $statusClass = 'status-vence-hoje';
                                } elseif ($parcela['status'] === 'vence_semana') {
                                    $statusLabel = 'Vence na Semana';
                                    $statusClass = 'status-vence-semana';
                                } elseif ($parcela['status'] === 'paga') {
                                    $statusLabel = 'Paga';
                                    $statusClass = 'status-paga';
                                }
                            @endphp
                            <tr>
                                <td style="width: 16%;">
                                    <strong>{{ $parcela['parcela'] }}</strong>
                                    @if (!empty($parcela['forma_pagamento']))
                                        <br><span
                                            style="font-size: 9px; color: #64748b;">{{ $parcela['forma_pagamento'] }}</span>
                                    @endif
                                </td>
                                <td style="width: 20%;">
                                    @if (!empty($parcela['vencimento']))
                                        {{ \Carbon\Carbon::parse($parcela['vencimento'])->format('d/m/Y') }}
                                    @else
                                        <span style="color: #64748b;">Nao informado</span>
                                    @endif

                                    @if (($parcela['dias_atraso'] ?? 0) > 0)
                                        <br><span style="font-size: 9px; color: #b91c1c;">{{ $parcela['dias_atraso'] }}
                                            dias atraso</span>
                                    @endif
                                </td>
                                <td style="width: 22%;">
                                    @if (($parcela['juros'] ?? 0) > 0)
                                        <span style="text-decoration: line-through; color: #64748b;">
                                            R$ {{ number_format($parcela['valor_parcela'], 2, ',', '.') }}
                                        </span>
                                        <br>
                                        <strong style="color: #b91c1c;">R$
                                            {{ number_format($parcela['valor_atualizado'], 2, ',', '.') }}</strong>
                                        <br><span style="font-size: 9px; color: #b91c1c;">
                                            +R$ {{ number_format($parcela['juros'], 2, ',', '.') }} juros
                                        </span>
                                    @else
                                        <strong>R$
                                            {{ number_format($parcela['valor_atualizado'], 2, ',', '.') }}</strong>
                                    @endif
                                </td>
                                <td style="width: 20%;">
                                    <span class="status-label {{ $statusClass }}">{{ $statusLabel }}</span>
                                </td>
                                <td style="width: 22%;">
                                    @if (!empty($parcela['pago_em']))
                                        {{ $parcela['pago_em']->format('d/m/Y H:i') }}
                                    @else
                                        <span style="color: #64748b;">Pendente</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>
        @endif

        @if (!empty($sale['observacoes']))
            <table class="info-grid">
                <tr>
                    <td colspan="2">
                        <span class="label">Observações:</span>
                        <span class="value">{{ $sale['observacoes'] ?? 'Nao informado' }}</span>
                    </td>

                </tr>
            </table>
        @endif
    </main>

    <footer class="pdf-footer">
        <table class="pdf-footer-table">
            <tr>
                <td class="pdf-footer-left">Documento gerado por {{ $tenantData['nome'] }}</td>
                <td class="pdf-footer-right">Venda {{ $sale['numero'] }}</td>
            </tr>
        </table>
    </footer>
</body>

</html>
