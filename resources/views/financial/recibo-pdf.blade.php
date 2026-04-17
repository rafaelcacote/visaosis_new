<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo {{ $recibo['id'] ?? '' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #000;
            margin: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }
        .muted {
            color: #444;
            font-size: 11px;
        }
        .box {
            border: 1px solid #000;
            padding: 10px;
            margin-bottom: 12px;
        }
        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        .label {
            font-size: 10px;
            color: #444;
            margin-bottom: 2px;
        }
        .value {
            font-weight: bold;
        }
        .amount {
            font-size: 16px;
            font-weight: bold;
        }
        .footer {
            margin-top: 20px;
            border-top: 1px solid #000;
            padding-top: 12px;
        }
        .signature {
            margin-top: 28px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .sig-line {
            border-top: 1px solid #000;
            padding-top: 6px;
            text-align: center;
        }
        @media print {
            body { margin: 0; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <p class="title">RECIBO DE PAGAMENTO</p>
            <div class="muted">Recibo: {{ $recibo['id'] ?? '-' }}</div>
        </div>
        <div class="muted" style="text-align:right;">
            <div>Emitido em: {{ $recibo['emitido_em'] ?? '-' }}</div>
        </div>
    </div>

    <div class="box">
        <div class="grid">
            <div>
                <div class="label">Recebedor</div>
                <div class="value">{{ $recibo['beneficiario'] ?? '-' }}</div>
                @if(!empty($recibo['beneficiario_cpf_cnpj']))
                    <div class="muted">CPF/CNPJ: {{ $recibo['beneficiario_cpf_cnpj'] }}</div>
                @endif
            </div>
            <div>
                <div class="label">Pagador</div>
                <div class="value">{{ $recibo['cliente'] ?? '-' }}</div>
                @if(!empty($recibo['cliente_cpf']))
                    <div class="muted">CPF: {{ $recibo['cliente_cpf'] }}</div>
                @endif
                @if(!empty($recibo['cliente_telefone']))
                    <div class="muted">Telefone: {{ $recibo['cliente_telefone'] }}</div>
                @endif
            </div>
        </div>
    </div>

    <div class="box">
        <div class="grid">
            <div>
                <div class="label">Venda</div>
                <div class="value">{{ $recibo['venda_id'] ?? '-' }}</div>
                <div class="muted">Pedido #{{ $recibo['pedido_id'] ?? '-' }}</div>
            </div>
            <div>
                <div class="label">Parcela</div>
                <div class="value">{{ (int) ($recibo['parcela_numero'] ?? 0) }}/{{ (int) ($recibo['parcela_total'] ?? 0) }}</div>
                <div class="muted">Vencimento: {{ $recibo['vencimento'] ?? '-' }}</div>
            </div>
        </div>
    </div>

    <div class="box">
        <div class="grid">
            <div>
                <div class="label">Valor recebido</div>
                <div class="amount">R$ {{ number_format((float) ($recibo['valor'] ?? 0), 2, ',', '.') }}</div>
            </div>
            <div>
                <div class="label">Pagamento</div>
                <div class="value">{{ $recibo['pago_em'] ?? '-' }}</div>
                @if(!empty($recibo['forma_pagamento']))
                    <div class="muted">Forma: {{ $recibo['forma_pagamento'] }}</div>
                @endif
            </div>
        </div>
    </div>

    <div class="footer">
        <div>Declaro, para os devidos fins, que recebi do(a) pagador(a) acima identificado(a) o valor informado neste recibo, referente à parcela indicada.</div>
        <div class="signature">
            <div class="sig-line">Recebedor</div>
            <div class="sig-line">Pagador</div>
        </div>
    </div>
</body>
</html>
