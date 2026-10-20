@php
    use App\Helpers\AuthHelper;

    $pedido = $ordemServico->pedido;
    $cliente = $ordemServico->prescricao?->paciente ?? $pedido?->cliente;
    $fornecedor = $ordemServico->fornecedor;
    $itensOrdem = $ordemServico->itensOrdem ?? collect();
    $rx = $ordemServico->prescricao;

    $subtotalItens = $itensOrdem->sum(function ($itemOrdem) {
        return (float) ($itemOrdem->item->total_linha ?? 0);
    });

    $descontoOS = (float) ($ordemServico->desconto ?? 0);
    $totalOS =
        $itensOrdem->count() > 0 ? max(0, $subtotalItens - $descontoOS) : (float) ($ordemServico->total_linha ?? 0);

    $osNumero = str_pad($ordemServico->id, 8, '0', STR_PAD_LEFT);
    $dataOS = $ordemServico->created_at?->format('d/m/Y H:i') ?? 'Nao informado';
    $entrega = $ordemServico->entrega_em?->format('d/m/Y') ?? 'Nao definida';
    $viaLabel = $via === 'laboratorio' ? 'Via do Laboratorio' : 'Via da Otica';
    $statusLabel = $currentStatus['text'] ?? ($ordemServico->status_label ?? ucfirst((string) $ordemServico->status));
@endphp
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OS 80 mm #{{ $osNumero }} - {{ $viaLabel }}</title>
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
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .title {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            margin-top: 6px;
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
            min-width: 72px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        th,
        td {
            border: 1px solid #d1d5db;
            padding: 3px 2px;
            vertical-align: top;
        }

        th {
            background: #f9fafb;
            font-size: 9px;
            text-transform: uppercase;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
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

        .obs-lines {
            margin-top: 4px;
        }

        .obs-line {
            height: 18px;
            border-bottom: 1px solid #9ca3af;
            margin-bottom: 6px;
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
            <div class="company">{{ AuthHelper::tenantName() ?? 'VisaoSis' }}</div>
            <div class="subtitle">{{ AuthHelper::locationName() ?? '' }}</div>
            <div class="title">Ordem de Servico</div>
            <div class="subtitle">OS {{ $osNumero }}</div>
            <div class="subtitle">{{ $viaLabel }}</div>
        </div>

        <div class="divider"></div>

        <div class="field"><strong>Status:</strong> {{ $statusLabel }}</div>
        <div class="field"><strong>Data OS:</strong> {{ $dataOS }}</div>
        <div class="field"><strong>Entrega:</strong> {{ $entrega }}</div>
        <div class="field"><strong>Prioridade:</strong> {{ ucfirst((string) ($ordemServico->prioridade ?? 'normal')) }}
        </div>
        <div class="field"><strong>Atendente:</strong> {{ $ordemServico->user?->name ?? 'Nao informado' }}</div>

        <div class="divider"></div>

        <div class="section-title">Cliente</div>
        <div class="field"><strong>Nome:</strong> {{ $cliente->nome ?? 'Nao informado' }}</div>
        @if ($via !== 'laboratorio' && (!empty($cliente?->cpf_formatado) || !empty($cliente?->cpf)))
            <div class="field"><strong>CPF:</strong> {{ $cliente->cpf_formatado ?? $cliente->cpf }}</div>
        @endif
        @if (!empty($pedido?->numero) || !empty($pedido?->id))
            <div class="field"><strong>Venda:</strong> #{{ $pedido->numero ?? $pedido->id }}</div>
        @endif

        <div class="divider"></div>

        <div class="section-title">Laboratorio</div>
        <div class="field"><strong>Nome:</strong> {{ $fornecedor->razao_social ?? 'Nao informado' }}</div>
        @if (!empty($fornecedor?->nome_fantasia))
            <div class="field"><strong>Fantasia:</strong> {{ $fornecedor->nome_fantasia }}</div>
        @endif

        <div class="divider"></div>

        <div class="section-title">Itens</div>
        <table>
            <thead>
                <tr>
                    <th>Ref</th>
                    <th>Produto</th>
                    <th class="text-center">Qtd</th>
                    @if ($via === 'otica')
                        <th class="text-right">Total</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @if ($itensOrdem->count() > 0)
                    @foreach ($itensOrdem as $itemOrdem)
                        @php
                            $itemPedido = $itemOrdem->item;
                            $produto = $itemPedido?->produto;
                            $totalLinha = (float) ($itemPedido->total_linha ?? 0);
                        @endphp
                        <tr>
                            <td class="text-center">{{ $produto->id ?? '-' }}</td>
                            <td>{{ $produto->nome ?? 'Produto nao identificado' }}</td>
                            <td class="text-center">{{ $itemPedido->quantidade ?? 0 }}</td>
                            @if ($via === 'otica')
                                <td class="text-right">R$ {{ number_format($totalLinha, 2, ',', '.') }}</td>
                            @endif
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td class="text-center">-</td>
                        <td>Ordem de Servico Geral</td>
                        <td class="text-center">{{ $ordemServico->quantidade ?? 1 }}</td>
                        @if ($via === 'otica')
                            <td class="text-right">{{ $ordemServico->total_formatado ?? 'R$ 0,00' }}</td>
                        @endif
                    </tr>
                @endif
            </tbody>
        </table>

        @if ($via === 'otica')
            <div class="divider"></div>
            <div class="section-title">Valores</div>
            <div class="totals">
                <div class="line">
                    <span>Subtotal</span>
                    <span>R$ {{ number_format($subtotalItens, 2, ',', '.') }}</span>
                </div>
                <div class="line">
                    <span>Desconto</span>
                    <span>R$ {{ number_format($descontoOS, 2, ',', '.') }}</span>
                </div>
                <div class="line total">
                    <span>Total</span>
                    <span>R$ {{ number_format($totalOS, 2, ',', '.') }}</span>
                </div>
            </div>
        @endif

        @if ($rx)
            <div class="divider"></div>
            <div class="section-title">Prescricao</div>
            <table>
                <thead>
                    <tr>
                        <th>Uso</th>
                        <th>Olho</th>
                        <th>Esf</th>
                        <th>Cil</th>
                        <th>Eixo</th>
                        <th>Add</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td rowspan="2" class="text-center">L</td>
                        <td class="text-center">OD</td>
                        <td class="text-center">{{ $rx->esfera_od ?? '-' }}</td>
                        <td class="text-center">{{ $rx->cilindro_od ?? '-' }}</td>
                        <td class="text-center">{{ !is_null($rx->eixo_od) ? $rx->eixo_od : '-' }}</td>
                        <td class="text-center">{{ $rx->adicao_od ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-center">OE</td>
                        <td class="text-center">{{ $rx->esfera_oe ?? '-' }}</td>
                        <td class="text-center">{{ $rx->cilindro_oe ?? '-' }}</td>
                        <td class="text-center">{{ !is_null($rx->eixo_oe) ? $rx->eixo_oe : '-' }}</td>
                        <td class="text-center">{{ $rx->adicao_oe ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td rowspan="2" class="text-center">P</td>
                        <td class="text-center">OD</td>
                        <td class="text-center">{{ $rx->esfera_od_perto ?? '-' }}</td>
                        <td class="text-center">{{ $rx->cilindro_od_perto ?? '-' }}</td>
                        <td class="text-center">{{ !is_null($rx->eixo_od_perto) ? $rx->eixo_od_perto : '-' }}</td>
                        <td class="text-center">{{ $rx->adicao_od_perto ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-center">OE</td>
                        <td class="text-center">{{ $rx->esfera_oe_perto ?? '-' }}</td>
                        <td class="text-center">{{ $rx->cilindro_oe_perto ?? '-' }}</td>
                        <td class="text-center">{{ !is_null($rx->eixo_oe_perto) ? $rx->eixo_oe_perto : '-' }}</td>
                        <td class="text-center">{{ $rx->adicao_oe_perto ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="field" style="margin-top: 6px;"><strong>DNP/Alt:</strong>
                OD
                {{ !is_null($rx->dnp_od) ? $rx->dnp_od : '-' }}/{{ !is_null($rx->altura_od) ? $rx->altura_od : '-' }}
                | OE
                {{ !is_null($rx->dnp_oe) ? $rx->dnp_oe : '-' }}/{{ !is_null($rx->altura_oe) ? $rx->altura_oe : '-' }}
            </div>
            <div class="field"><strong>DNP/Alt P:</strong>
                OD
                {{ !is_null($rx->dnp_od_perto) ? $rx->dnp_od_perto : '-' }}/{{ !is_null($rx->altura_od_perto) ? $rx->altura_od_perto : '-' }}
                | OE
                {{ !is_null($rx->dnp_oe_perto) ? $rx->dnp_oe_perto : '-' }}/{{ !is_null($rx->altura_oe_perto) ? $rx->altura_oe_perto : '-' }}
            </div>
            @if (!empty($rx->tipo_lente))
                <div class="field"><strong>Lente:</strong> {{ $rx->tipo_lente }}</div>
            @endif
        @endif

        @if (!empty($ordemServico->observacoes))
            <div class="divider"></div>
            <div class="section-title">Observacoes</div>
            <div class="note-box">{{ $ordemServico->observacoes }}</div>
        @endif

        @if ($via === 'laboratorio')
            <div class="divider"></div>
            <div class="section-title">Observacoes</div>
            <div class="obs-lines">
                <div class="obs-line"></div>
                <div class="obs-line"></div>
                <div class="obs-line"></div>
            </div>
        @endif

        <div class="divider"></div>
        <div class="center muted">Impresso em {{ now('America/Sao_Paulo')->format('d/m/Y H:i') }}</div>
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
