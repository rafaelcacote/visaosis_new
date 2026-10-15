<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receita 80 mm</title>
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

        .title {
            font-size: 15px;
            font-weight: 700;
            text-transform: uppercase;
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
        }

        .field strong {
            display: inline-block;
            min-width: 56px;
        }

        .rx-block {
            margin-bottom: 8px;
        }

        .rx-block table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        .rx-block th,
        .rx-block td {
            border: 1px solid #d1d5db;
            padding: 3px 2px;
            text-align: center;
        }

        .rx-block th {
            background: #f9fafb;
            font-weight: 700;
        }

        .text-box {
            border: 1px solid #d1d5db;
            padding: 6px;
            border-radius: 4px;
            font-size: 11px;
            min-height: 28px;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .signature-wrap {
            text-align: center;
            margin-top: 12px;
        }

        .signature-image {
            max-width: 46mm;
            max-height: 18mm;
            object-fit: contain;
            display: block;
            margin: 0 auto 4px;
        }

        .signature-line {
            border-top: 1px solid #111827;
            margin-top: 4px;
            padding-top: 4px;
            font-size: 11px;
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
            <div class="title">Receita para Oculos</div>
            <div class="subtitle">{{ $prescription['numero'] }}</div>
            <div class="subtitle">{{ \Carbon\Carbon::parse($prescription['data'])->format('d/m/Y') }}</div>
        </div>

        <div class="divider"></div>

        <div class="section-title">Paciente</div>
        <div class="field"><strong>Nome:</strong> {{ $prescription['paciente']['nome'] ?: '-' }}</div>
        <div class="field"><strong>Idade:</strong> {{ $prescription['paciente']['idade'] ? $prescription['paciente']['idade'] . ' anos' : '-' }}</div>
        <div class="field"><strong>Tel:</strong> {{ $prescription['paciente']['telefone'] ?: '-' }}</div>

        <div class="divider"></div>

        <div class="section-title">Profissional</div>
        <div class="field"><strong>Nome:</strong> {{ $prescription['profissional']['nome'] ?: '-' }}</div>
        <div class="field"><strong>Esp.:</strong> {{ $prescription['profissional']['especialidade'] ?: '-' }}</div>
        <div class="field">
            @php
                $espDesc = $prescription['profissional']['especialidade'] ?? null;
                $label = $espDesc === 'Optometrista' ? 'CBOO' : 'CRM';
            @endphp
            <strong>{{ $label }}:</strong> {{ $prescription['profissional']['registro_conselho'] ?: '-' }}
        </div>

        <div class="divider"></div>

        <div class="section-title">Longe</div>
        <div class="rx-block">
            <table>
                <thead>
                    <tr>
                        <th>Olho</th>
                        <th>Esf</th>
                        <th>Cil</th>
                        <th>Eixo</th>
                        <th>AV</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>OD</td>
                        <td>{{ $prescription['prescricao']['od_esferico'] ?? '-' }}</td>
                        <td>{{ $prescription['prescricao']['od_cilindrico'] ?? '-' }}</td>
                        <td>{{ $prescription['prescricao']['od_eixo'] ?? '-' }}</td>
                        <td>{{ $prescription['prescricao']['od_acuidade'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>OE</td>
                        <td>{{ $prescription['prescricao']['oe_esferico'] ?? '-' }}</td>
                        <td>{{ $prescription['prescricao']['oe_cilindrico'] ?? '-' }}</td>
                        <td>{{ $prescription['prescricao']['oe_eixo'] ?? '-' }}</td>
                        <td>{{ $prescription['prescricao']['oe_acuidade'] ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="rx-block">
            <table>
                <thead>
                    <tr>
                        <th>Olho</th>
                        <th>Add</th>
                        <th>DNP</th>
                        <th>Alt</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>OD</td>
                        <td>{{ $prescription['prescricao']['od_adicao'] ?? '-' }}</td>
                        <td>{{ $prescription['prescricao']['od_dnp'] ?? '-' }}</td>
                        <td>{{ $prescription['prescricao']['od_altura'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>OE</td>
                        <td>{{ $prescription['prescricao']['oe_adicao'] ?? '-' }}</td>
                        <td>{{ $prescription['prescricao']['oe_dnp'] ?? '-' }}</td>
                        <td>{{ $prescription['prescricao']['oe_altura'] ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="section-title">Perto</div>
        <div class="rx-block">
            <table>
                <thead>
                    <tr>
                        <th>Olho</th>
                        <th>Esf</th>
                        <th>Cil</th>
                        <th>Eixo</th>
                        <th>AV</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>OD</td>
                        <td>{{ $prescription['prescricao']['od_esferico_perto'] ?? '-' }}</td>
                        <td>{{ $prescription['prescricao']['od_cilindrico_perto'] ?? '-' }}</td>
                        <td>{{ $prescription['prescricao']['od_eixo_perto'] ?? '-' }}</td>
                        <td>{{ $prescription['prescricao']['od_acuidade_perto'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>OE</td>
                        <td>{{ $prescription['prescricao']['oe_esferico_perto'] ?? '-' }}</td>
                        <td>{{ $prescription['prescricao']['oe_cilindrico_perto'] ?? '-' }}</td>
                        <td>{{ $prescription['prescricao']['oe_eixo_perto'] ?? '-' }}</td>
                        <td>{{ $prescription['prescricao']['oe_acuidade_perto'] ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="rx-block">
            <table>
                <thead>
                    <tr>
                        <th>Olho</th>
                        <th>Add</th>
                        <th>DNP</th>
                        <th>Alt</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>OD</td>
                        <td>{{ $prescription['prescricao']['od_adicao_perto'] ?? '-' }}</td>
                        <td>{{ $prescription['prescricao']['od_dnp_perto'] ?? '-' }}</td>
                        <td>{{ $prescription['prescricao']['od_altura_perto'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>OE</td>
                        <td>{{ $prescription['prescricao']['oe_adicao_perto'] ?? '-' }}</td>
                        <td>{{ $prescription['prescricao']['oe_dnp_perto'] ?? '-' }}</td>
                        <td>{{ $prescription['prescricao']['oe_altura_perto'] ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="divider"></div>

        <div class="field"><strong>Lente:</strong> {{ $prescription['prescricao']['tipo_lente'] ?: '-' }}</div>
        <div class="field"><strong>Validade:</strong> {{ $prescription['prescricao']['validade_dias'] ? $prescription['prescricao']['validade_dias'] . ' dias' : '-' }}</div>

        <div class="section-title" style="margin-top:8px;">Diagnostico</div>
        <div class="text-box">{{ $prescription['diagnostico'] ?: '-' }}</div>

        @if (!empty($prescription['recomendacoes']))
            <div class="section-title" style="margin-top:8px;">Recomendacoes</div>
            <div class="text-box">{{ $prescription['recomendacoes'] }}</div>
        @endif

        @if (!empty($prescription['observacoes']))
            <div class="section-title" style="margin-top:8px;">Observacoes</div>
            <div class="text-box">{{ $prescription['observacoes'] }}</div>
        @endif

        <div class="signature-wrap">
            @if (!empty($prescription['profissional']['assinatura_data_uri']))
                <img src="{{ $prescription['profissional']['assinatura_data_uri'] }}" alt="Assinatura do profissional"
                    class="signature-image">
            @endif
            <div class="signature-line">
                <div><strong>{{ $prescription['profissional']['nome'] ?: '-' }}</strong></div>
                <div class="muted">{{ $label }}: {{ $prescription['profissional']['registro_conselho'] ?: '-' }}</div>
            </div>
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
