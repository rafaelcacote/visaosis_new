@php
    use App\Helpers\AuthHelper;

    $pedido = $ordemServico->pedido;
    $cliente = $ordemServico->prescricao?->paciente ?? $pedido?->cliente;
    $fornecedor = $ordemServico->fornecedor;
    $itensOrdem = $ordemServico->itensOrdem ?? collect();

    $subtotalItens = $itensOrdem->sum(function ($itemOrdem) {
        return (float) ($itemOrdem->item->total_linha ?? 0);
    });

    $descontoOS = (float) ($ordemServico->desconto ?? 0);
    $totalOS =
        $itensOrdem->count() > 0 ? max(0, $subtotalItens - $descontoOS) : (float) ($ordemServico->total_linha ?? 0);

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
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ordem de Servico #{{ str_pad($ordemServico->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 6mm;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            line-height: 1.25;
            color: #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .os-sheet {
            border: 1px solid #000;
            padding: 5px 6px;
            page-break-inside: avoid;
        }

        .os-row {
            width: 100%;
        }

        .os-row td {
            vertical-align: top;
            padding: 2px 3px;
        }

        .os-title {
            font-size: 12.5px;
            font-weight: 700;
        }

        .os-brand {
            width: auto;
        }

        .os-brand td {
            padding: 0;
            vertical-align: middle;
        }

        .os-logo-wrap {
            width: 30px;
            padding-right: 6px;
        }

        .os-logo {
            max-width: 28px;
            max-height: 28px;
            display: block;
        }

        .os-copy-label {
            font-size: 10px;
            font-weight: 700;
            text-align: center;
            margin-top: 3px;
        }

        .os-kv {
            font-size: 9.6px;
        }

        .os-kv b {
            font-weight: 700;
        }

        .os-barcode {
            height: 12px;
            width: 140px;
            border: 1px solid #000;
            background: repeating-linear-gradient(90deg, #000 0, #000 2px, #fff 2px, #fff 4px);
        }

        .os-sep {
            border-top: 1px solid #000;
            margin: 5px 0;
        }

        .os-table th,
        .os-table td {
            border: 1px solid #000;
            padding: 2px 3px;
            font-size: 8.8px;
        }

        .os-table th {
            background: #eee;
            font-weight: 700;
        }

        .os-table-outer {
            border: 1px solid #000;
        }

        .os-table-outer th,
        .os-table-outer td {
            border-left: 1px solid #000;
            border-right: 1px solid #000;
        }

        .os-table-outer tr:last-child td {
            border-bottom: 1px solid #000;
        }

        .os-table-no-outer thead th {
            border: none;
            border-bottom: 1px solid #000;
        }

        .os-table-no-outer tbody td {
            border: none;
        }

        .os-right {
            text-align: right;
        }

        .os-center {
            text-align: center;
        }

        .os-small {
            font-size: 8.6px;
        }

        .os-note {
            border: 1px solid #000;
            padding: 3px 4px;
            font-size: 9.4px;
        }

        .os-footer {
            border-top: 1px solid #000;
            margin-top: 4px;
            padding-top: 3px;
            font-size: 8.8px;
        }

        .os-block {
            margin-top: 5px;
        }

        .cut-line {
            margin: 4px 0;
            border-top: 1px dashed #000;
            height: 0;
        }
    </style>
</head>

<body>
    @php
        $osNumero = str_pad($ordemServico->id, 8, '0', STR_PAD_LEFT);
        $osTipo = 'Ótica';
        $atendente = $ordemServico->user?->name ?? 'Não informado';
        $dataVenda =
            $pedido?->data_pedido_formatada ?? ($ordemServico->created_at?->format('d/m/Y') ?? 'Não informado');
        $dataOS = $ordemServico->created_at?->format('d/m/Y') ?? 'Não informado';
        $entrega = $ordemServico->entrega_em?->format('d/m/Y') ?? 'Não definido';
        $cpfCliente = $cliente->cpf_formatado ?? ($cliente->cpf ?? null);
        $clienteVenda = $pedido?->cliente;
        $cpfClienteVenda = $clienteVenda->cpf_formatado ?? ($clienteVenda->cpf ?? null);
        $rx = $ordemServico->prescricao;
        $adiantamento = 0.0;
        $aReceber = max(0, $totalOS - $adiantamento);
    @endphp

    @for ($copy = 0; $copy < 2; $copy++)
        <div class="os-sheet">
            <table class="os-row">
                <tr>
                    <td style="width: 55%;">
                        <table class="os-brand">
                            <tr>
                                @if ($logoBase64)
                                    <td class="os-logo-wrap">
                                        <img src="{{ $logoBase64 }}" alt="Logo da ótica" class="os-logo">
                                    </td>
                                @endif
                                <td>
                                    <div class="os-title">{{ AuthHelper::tenantName() ?? 'VisaoSis' }}</div>
                                    <div class="os-kv os-small">
                                        {{ AuthHelper::locationName() ?? '' }}
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td style="width: 25%;" class="os-center">
                        <div class="os-kv"><b>O.S.:</b> {{ $osNumero }}</div>
                        <div class="os-kv"><b>Tipo:</b> {{ $osTipo }}</div>
                    </td>

                </tr>
            </table>

            <div class="os-copy-label">{{ $copy === 0 ? 'Via da Ótica' : 'Via do Laboratório' }}</div>

            <div class="os-sep"></div>

            <table class="os-row">
                <tr>
                    <td style="width: 45%;" class="os-kv">
                        <b>Atendente:</b> {{ $atendente }}
                    </td>
                    <td style="width: 55%;" class="os-kv">
                        <b>Data:</b> {{ $dataOS }} <b>Prev. Entrega:</b> {{ $entrega }}
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="os-kv">
                        <b>Cliente:</b> {{ $cliente->nome ?? 'Não informado' }}
                    </td>
                </tr>
            </table>

            <div class="os-sep"></div>

            <table class="os-table os-table-no-outer">
                <thead>
                    <tr>
                        <th style="width: {{ $copy === 0 ? '18%' : '20%' }};">Ref.</th>
                        <th style="width: {{ $copy === 0 ? '42%' : '65%' }};">Produto</th>
                        <th style="width: {{ $copy === 0 ? '8%' : '15%' }};" class="os-center">Qtde</th>
                        @if ($copy === 0)
                            <th style="width: 14%;" class="os-right">Val. Unit.</th>
                            <th style="width: 8%;" class="os-right">Desc.</th>
                            <th style="width: 10%;" class="os-right">Valor Total</th>
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
                                $precoUnit = (float) ($itemPedido->preco_unit ?? 0);
                                $desconto = (float) ($itemPedido->desconto ?? 0);
                            @endphp
                            <tr>
                                <td class="os-center">{{ $produto->id ?? '-' }}</td>
                                <td>{{ $produto->nome ?? 'Produto não identificado' }}</td>
                                <td class="os-center">{{ $itemPedido->quantidade ?? 0 }}</td>
                                @if ($copy === 0)
                                    <td class="os-right">R$ {{ number_format($precoUnit, 2, ',', '.') }}</td>
                                    <td class="os-right">R$ {{ number_format($desconto, 2, ',', '.') }}</td>
                                    <td class="os-right">R$ {{ number_format($totalLinha, 2, ',', '.') }}</td>
                                @endif
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="os-center">-</td>
                            <td>Ordem de Serviço Geral</td>
                            <td class="os-center">{{ $ordemServico->quantidade ?? 1 }}</td>
                            @if ($copy === 0)
                                <td class="os-right">{{ $ordemServico->preco_unit_formatado ?? 'R$ 0,00' }}</td>
                                <td class="os-right">{{ $ordemServico->desconto_formatado ?? 'R$ 0,00' }}</td>
                                <td class="os-right">{{ $ordemServico->total_formatado ?? 'R$ 0,00' }}</td>
                            @endif
                        </tr>
                    @endif
                </tbody>
            </table>

            <div class="os-sep"></div>

            <table class="os-row">
                <tr>
                    <td style="width: 100%; padding-right: 4px;">
                        <table class="os-table">
                            <thead>
                                <tr>
                                    <th style="width: 10%;" class="os-center"></th>
                                    <th style="width: 10%;" class="os-center">Olho</th>
                                    <th style="width: 16%;" class="os-center">Esférico</th>
                                    <th style="width: 16%;" class="os-center">Cilíndrico</th>
                                    <th style="width: 12%;" class="os-center">Eixo</th>
                                    <th style="width: 12%;" class="os-center">Adição</th>
                                    <th style="width: 12%;" class="os-center">DNP</th>
                                    <th style="width: 12%;" class="os-center">Altura</th>

                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td rowspan="2" class="os-center"><b>Longe</b></td>
                                    <td class="os-center"><b>OD</b></td>
                                    <td class="os-center">{{ $rx?->esfera_od ?? '-' }}</td>
                                    <td class="os-center">{{ $rx?->cilindro_od ?? '-' }}</td>
                                    <td class="os-center">{{ !is_null($rx?->eixo_od) ? $rx->eixo_od : '-' }}</td>
                                    <td class="os-center">{{ $rx?->adicao_od ?? '-' }}</td>
                                    <td class="os-center">{{ !is_null($rx?->dnp_od) ? $rx->dnp_od : '-' }}</td>
                                    <td class="os-center">{{ !is_null($rx?->altura_od) ? $rx->altura_od : '-' }}</td>

                                </tr>
                                <tr>
                                    <td class="os-center"><b>OE</b></td>
                                    <td class="os-center">{{ $rx?->esfera_oe ?? '-' }}</td>
                                    <td class="os-center">{{ $rx?->cilindro_oe ?? '-' }}</td>
                                    <td class="os-center">{{ !is_null($rx?->eixo_oe) ? $rx->eixo_oe : '-' }}</td>
                                    <td class="os-center">{{ $rx?->adicao_oe ?? '-' }}</td>
                                    <td class="os-center">{{ !is_null($rx?->dnp_oe) ? $rx->dnp_oe : '-' }}</td>
                                    <td class="os-center">{{ !is_null($rx?->altura_oe) ? $rx->altura_oe : '-' }}</td>

                                </tr>
                                <tr>
                                    <td rowspan="2" class="os-center"><b>Perto</b></td>
                                    <td class="os-center"><b>OD</b></td>
                                    <td class="os-center">{{ $rx?->esfera_od_perto ?? '-' }}</td>
                                    <td class="os-center">{{ $rx?->cilindro_od_perto ?? '-' }}</td>
                                    <td class="os-center">
                                        {{ !is_null($rx?->eixo_od_perto) ? $rx->eixo_od_perto : '-' }}</td>
                                    <td class="os-center">{{ $rx?->adicao_od_perto ?? '-' }}</td>
                                    <td class="os-center">{{ !is_null($rx?->dnp_od_perto) ? $rx->dnp_od_perto : '-' }}
                                    </td>
                                    <td class="os-center">
                                        {{ !is_null($rx?->altura_od_perto) ? $rx->altura_od_perto : '-' }}</td>

                                </tr>
                                <tr>
                                    <td class="os-center"><b>OE</b></td>
                                    <td class="os-center">{{ $rx?->esfera_oe_perto ?? '-' }}</td>
                                    <td class="os-center">{{ $rx?->cilindro_oe_perto ?? '-' }}</td>
                                    <td class="os-center">
                                        {{ !is_null($rx?->eixo_oe_perto) ? $rx->eixo_oe_perto : '-' }}</td>
                                    <td class="os-center">{{ $rx?->adicao_oe_perto ?? '-' }}</td>
                                    <td class="os-center">{{ !is_null($rx?->dnp_oe_perto) ? $rx->dnp_oe_perto : '-' }}
                                    </td>
                                    <td class="os-center">
                                        {{ !is_null($rx?->altura_oe_perto) ? $rx->altura_oe_perto : '-' }}</td>

                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="width: 100%;  padding-right: 4px;">
                        <table class="os-table">
                            <thead>
                                <tr>
                                    <th colspan="3" class="os-center">LENTE</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="width: 10%;"><b>Tipo</b></td>
                                    <td colspan="2">{{ $rx?->tipo_lente ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><b>Material</b></td>
                                    <td colspan="2">-</td>
                                </tr>
                                <tr>
                                    <td><b>Tratamento</b></td>
                                    <td colspan="2">{{ $rx?->recomendacoes ?? '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="width: 100%;  padding-right: 4px;">
                        <table class="os-table os-table-outer">
                            <thead>
                                <tr>
                                    <th colspan="3" class="os-center">OBSERVAÇÃO</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="width: 100%; height: 16px;">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td style="width: 100%; height: 16px;">&nbsp;</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>

            @if ($copy === 0)
                <table class="os-row os-block">
                    <tr>
                        <td class="os-kv os-right">
                            <b>Total:</b> R$ {{ number_format($totalOS, 2, ',', '.') }}
                        </td>
                    </tr>
                    <tr>
                        <td class="os-kv os-right">
                            <b>Adiantamento:</b> R$ {{ number_format($adiantamento, 2, ',', '.') }}
                        </td>
                    </tr>
                    <tr>
                        <td class="os-kv os-right">
                            <b>A Receber:</b> R$ {{ number_format($aReceber, 2, ',', '.') }}
                        </td>
                    </tr>
                </table>
            @endif

            <div class="os-footer">
                @if ($copy === 0)
                    <div class="os-kv">
                        <b>Cliente Venda:</b> {{ $clienteVenda->nome ?? 'Não informado' }}
                        @if ($cpfClienteVenda)
                            - <b>CPF:</b> {{ $cpfClienteVenda }}
                        @endif
                    </div>
                @endif
                <div class="os-kv os-right">{{ $dataVenda }}</div>
            </div>
        </div>

        @if ($copy === 0)
            <div class="cut-line"></div>
        @endif
    @endfor
</body>

</html>
