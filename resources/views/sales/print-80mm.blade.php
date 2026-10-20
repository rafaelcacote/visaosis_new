<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprovante 80 mm {{ $sale['numero'] }}</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 4mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f3f4f6;
            color: #111827;
        }

        .screen-toolbar {
            position: sticky;
            top: 0;
            z-index: 10;
            display: flex;
            gap: 8px;
            justify-content: center;
            padding: 12px;
            background: #111827;
        }

        .screen-toolbar button {
            border: 0;
            border-radius: 6px;
            padding: 8px 12px;
            color: #fff;
            background: #2563eb;
            cursor: pointer;
            font-size: 13px;
        }

        .screen-toolbar button.secondary {
            background: #4b5563;
        }

        .receipt {
            width: 72mm;
            margin: 12px auto;
            background: #fff;
            padding: 4mm;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        }

        .center {
            text-align: center;
        }

        .company {
            font-size: 15px;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .title {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            margin-top: 8px;
            margin-bottom: 4px;
        }

        .subtitle,
        .muted {
            font-size: 11px;
            color: #4b5563;
        }

        .divider {
            border-top: 1px dashed #9ca3af;
            margin: 8px 0;
        }

        .section-title {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .field {
            margin-bottom: 4px;
            font-size: 11px;
            line-height: 1.35;
            word-break: break-word;
        }

        .field strong {
            display: inline-block;
            min-width: 62px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        th,
        td {
            border-bottom: 1px solid #e5e7eb;
            padding: 4px 0;
            vertical-align: top;
        }

        th {
            text-align: left;
            font-size: 9px;
            text-transform: uppercase;
            color: #6b7280;
        }

        .text-right {
            text-align: right;
        }

        .totals {
            font-size: 11px;
            line-height: 1.6;
        }

        .totals .line {
            display: flex;
            justify-content: space-between;
            gap: 12px;
        }

        .totals .line.total {
            font-size: 13px;
            font-weight: 700;
            padding-top: 4px;
            border-top: 1px solid #111827;
        }

        .note-box {
            border: 1px solid #d1d5db;
            padding: 6px;
            border-radius: 4px;
            font-size: 11px;
            white-space: pre-wrap;
            word-break: break-word;
            min-height: 24px;
        }

        @media print {
            body {
                background: #fff;
            }

            .screen-toolbar {
                display: none !important;
            }

            .receipt {
                width: 72mm;
                margin: 0 auto;
                padding: 0;
                box-shadow: none;
            }
        }
    </style>
</head>

<body>
    <div class="screen-toolbar">
        <button type="button" onclick="window.print()">Imprimir</button>
        <button type="button" class="secondary" onclick="window.close()">Fechar</button>
    </div>

    <div class="receipt">
        <div class="center">
            <div class="company">{{ $tenantData['nome'] ?? 'Empresa' }}</div>
            <div class="title">Comprovante de Venda</div>
            <div class="subtitle">{{ $sale['numero'] }}</div>
            <div class="subtitle">
                {{ $sale['data_formatada'] }}{{ !empty($sale['hora_formatada']) ? ' ' . $sale['hora_formatada'] : '' }}
            </div>
        </div>

        <div class="divider"></div>

        <div class="section-title">Venda</div>
        <div class="field"><strong>Status:</strong> {{ ucfirst((string) ($sale['status'] ?? '-')) }}</div>
        <div class="field"><strong>Vendedor:</strong> {{ $vendedor['nome'] ?? 'Nao informado' }}</div>

        <div class="divider"></div>

        <div class="section-title">Cliente</div>
        <div class="field"><strong>Nome:</strong> {{ $sale['cliente']['nome'] ?? 'Nao informado' }}</div>
        @if (!empty($sale['cliente']['telefone']))
            <div class="field"><strong>Tel:</strong> {{ $sale['cliente']['telefone'] }}</div>
        @endif
        @if (!empty($sale['cliente']['cpf']))
            <div class="field"><strong>CPF:</strong> {{ $sale['cliente']['cpf'] }}</div>
        @endif

        <div class="divider"></div>

        <div class="section-title">Produtos</div>
        <table>
            <thead>
                <tr>
                    <th>Descricao</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sale['produtos'] as $produto)
                    <tr>
                        <td>
                            <div><strong>{{ $produto['nome'] }}</strong></div>
                            <div class="muted">
                                {{ (int) $produto['quantidade'] }} x R$
                                {{ number_format((float) $produto['preco_unitario'], 2, ',', '.') }}
                                @if (!empty($produto['desconto']) && $produto['desconto'] > 0)
                                    | Desc: R$ {{ number_format((float) $produto['desconto'], 2, ',', '.') }}
                                @endif
                            </div>
                        </td>
                        <td class="text-right">
                            R$ {{ number_format((float) $produto['subtotal'], 2, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @php
            $descontoAplicado = max(0, (float) $sale['subtotal'] - (float) $sale['total']);
        @endphp
        <div class="divider"></div>
        <div class="totals">
            <div class="line">
                <span>Subtotal</span>
                <span>R$ {{ number_format((float) $sale['subtotal'], 2, ',', '.') }}</span>
            </div>
            <div class="line">
                <span>Desconto</span>
                <span>R$ {{ number_format($descontoAplicado, 2, ',', '.') }}</span>
            </div>
            <div class="line total">
                <span>Total</span>
                <span>R$ {{ number_format((float) $sale['total'], 2, ',', '.') }}</span>
            </div>
        </div>

        <div class="divider"></div>

        <div class="section-title">Pagamento</div>
        @if (!empty($sale['pagamentos']))
            @foreach ($sale['pagamentos'] as $pagamento)
                <div class="field">
                    <strong>{{ $pagamento['forma_pagamento'] }}:</strong>
                    R$ {{ number_format((float) ($pagamento['valor'] ?? 0), 2, ',', '.') }}
                    @if (!empty($pagamento['parcelas']) && (int) $pagamento['parcelas'] > 1)
                        ({{ (int) $pagamento['parcelas'] }}x)
                    @endif
                </div>
            @endforeach
        @else
            <div class="field"><strong>Forma:</strong> {{ $sale['forma_pagamento'] ?? 'Nao informado' }}</div>
        @endif

        @if (collect($sale['parcelas_detalhes'] ?? [])->count() > 0)
            <div class="divider"></div>
            <div class="section-title">Parcelas</div>
            @foreach ($sale['parcelas_detalhes'] as $parcela)
                <div class="field">
                    <strong>{{ $parcela['parcela'] }}:</strong>
                    R$ {{ number_format((float) ($parcela['valor_parcela'] ?? 0), 2, ',', '.') }}
                    | {{ ucfirst(str_replace('_', ' ', (string) ($parcela['status'] ?? ''))) }}
                    @if (!empty($parcela['vencimento']))
                        | Venc. {{ \Carbon\Carbon::parse($parcela['vencimento'])->format('d/m/Y') }}
                    @endif
                </div>
            @endforeach
        @endif

        @if (!empty($sale['observacoes']))
            <div class="divider"></div>
            <div class="section-title">Observacoes</div>
            <div class="note-box">{{ $sale['observacoes'] }}</div>
        @endif

        <div class="divider"></div>
        <div class="center muted">
            Emitido em {{ now('America/Sao_Paulo')->format('d/m/Y H:i') }}
        </div>
    </div>

    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 300);
        });
    </script>
</body>

</html>
