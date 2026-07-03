@php
    use App\Helpers\AuthHelper;

    $pedido = $ordemServico->pedido;
    $cliente = $pedido?->cliente;
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

        .info-grid td {
            padding: 3px 8px 3px 0;
        }

        .muted {
            color: #6b7280;
            font-weight: 400;
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

        .pill-priority-normal {
            background: #eff6ff;
            color: #1d4ed8;
            border-color: #bfdbfe;
        }

        .pill-priority-urgente,
        .pill-priority-expressa {
            background: #fff7ed;
            color: #c2410c;
            border-color: #fed7aa;
        }

        .pill-status-warning {
            background: #fff7ed;
            color: #9a3412;
            border-color: #fdba74;
        }

        .pill-status-info,
        .pill-status-primary {
            background: #eff6ff;
            color: #1e40af;
            border-color: #bfdbfe;
        }

        .pill-status-success {
            background: #ecfdf5;
            color: #166534;
            border-color: #bbf7d0;
        }

        .pill-status-danger {
            background: #fef2f2;
            color: #b91c1c;
            border-color: #fecaca;
        }

        .pill-status-secondary {
            background: #f8fafc;
            color: #334155;
            border-color: #cbd5e1;
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

        .note-box {
            border: 1px solid #dbe3ee;
            background: #fafcff;
            padding: 8px;
            font-size: 10px;
            color: #0f172a;
            min-height: 30px;
        }

        .signatures {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
        }

        .signatures td {
            width: 50%;
            padding: 0 12px;
            text-align: center;
            vertical-align: top;
        }

        .signature-line {
            border-top: 1px solid #64748b;
            margin-top: 28px;
            padding-top: 5px;
            font-size: 10px;
            color: #334155;
        }

        .pt-6 {
            padding-top: 6px;
        }
    </style>
</head>

<body>
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
                    <h2 class="doc-title">ORDEM DE SERVICO #{{ str_pad($ordemServico->id, 6, '0', STR_PAD_LEFT) }}</h2>
                    <p class="doc-subtitle">Emissao: {{ now('America/Sao_Paulo')->format('d/m/Y H:i') }}</p>
                </td>
            </tr>
        </table>
    </header>

    <main>
        <section class="section">
            <h3 class="section-title">Resumo da Ordem</h3>
            <table class="info-grid">
                <tr>
                    <td>
                        <span class="label">Numero da Ordem:</span>
                        <span class="value">#{{ str_pad($ordemServico->id, 6, '0', STR_PAD_LEFT) }}</span>
                    </td>
                    <td>
                        <span class="label">Data de Criacao:</span>
                        <span
                            class="value">{{ $ordemServico->created_at?->format('d/m/Y H:i') ?? 'Nao informado' }}</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="label">Entrega Prevista:</span>
                        <span
                            class="value">{{ $ordemServico->entrega_em?->format('d/m/Y H:i') ?? 'Nao definida' }}</span>
                    </td>
                    <td>
                        <span class="label">Responsavel:</span>
                        <span class="value">{{ $ordemServico->user?->name ?? 'Nao informado' }}</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="label">Cliente:</span>
                        <span class="value">{{ $cliente->nome ?? 'Nao informado' }}</span>
                    </td>
                    <td>
                        <span class="label">Data da Venda:</span>
                        <span class="value">{{ $pedido?->data_pedido_formatada ?? 'Nao informado' }}</span>
                    </td>
                </tr>
                <tr>

                    <td>
                        <span class="label">Status da Venda:</span>
                        <span class="value">{{ $pedido?->status_label ?? 'Nao informado' }}</span>
                    </td>
                    <td>
                        <span class="label">Total da Venda:</span>
                        <span class="value">{{ $pedido?->valor_total_formatado ?? 'R$ 0,00' }}</span>
                    </td>
                </tr>

            </table>
        </section>

        <section class="section">
            <h3 class="section-title">Fornecedor / Laboratorio</h3>
            <table class="info-grid">
                <tr>

                    <td>
                        <span class="label">Nome Fantasia:</span>
                        <span class="value">{{ $fornecedor->nome_fantasia ?? 'Nao informado' }}</span>
                    </td>
                    <td>
                        <span class="label">CNPJ:</span>
                        <span class="value">{{ $fornecedor->cnpj_formatado ?? 'Nao informado' }}</span>
                    </td>
                </tr>
                <tr>

                    <td>
                        <span class="label">Telefone:</span>
                        <span
                            class="value">{{ $fornecedor->telefone_formatado ?? ($fornecedor->telefone ?? 'Nao informado') }}</span>
                    </td>
                    <td>
                        <span class="label">E-mail:</span>
                        <span class="value">{{ $fornecedor->email ?? 'Nao informado' }}</span>
                    </td>
                </tr>

            </table>
        </section>

        <section class="section">
            <h3 class="section-title">Itens da Ordem de Serviço</h3>
            <table class="table-data">
                <thead>
                    <tr>
                        <th style="width: 46%;">Produto</th>
                        <th style="width: 8%;" class="text-center">Qtd</th>
                        <th style="width: 17%;" class="text-right">Valor Unit.</th>
                        <th style="width: 14%;" class="text-right">Desconto</th>
                        <th style="width: 15%;" class="text-right">Total</th>

                    </tr>
                </thead>
                <tbody>
                    @forelse ($itensOrdem as $itemOrdem)
                        @php
                            $itemPedido = $itemOrdem->item;
                            $produto = $itemPedido?->produto;
                            $totalLinha = (float) ($itemPedido->total_linha ?? 0);
                            $precoUnit = (float) ($itemPedido->preco_unit ?? 0);
                            $desconto = (float) ($itemPedido->desconto ?? 0);
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $produto->nome ?? 'Produto nao identificado' }}</strong><br>

                            </td>
                            <td class="text-center">{{ $itemPedido->quantidade ?? 0 }}</td>
                            <td class="text-right">R$ {{ number_format($precoUnit, 2, ',', '.') }}</td>
                            <td class="text-right">R$ {{ number_format($desconto, 2, ',', '.') }}</td>
                            <td class="text-right"><strong>R$ {{ number_format($totalLinha, 2, ',', '.') }}</strong>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td><strong>Ordem de Servico Geral</strong></td>
                            <td class="text-center">{{ $ordemServico->quantidade ?? 1 }}</td>
                            <td class="text-right">{{ $ordemServico->preco_unit_formatado ?? 'R$ 0,00' }}</td>
                            <td class="text-right">{{ $ordemServico->desconto_formatado ?? 'R$ 0,00' }}</td>
                            <td class="text-right"><strong>{{ $ordemServico->total_formatado ?? 'R$ 0,00' }}</strong>
                            </td>
                            <td class="text-center">-</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="summary-block">
                <p class="summary-line"><strong>Subtotal:</strong> R$ {{ number_format($subtotalItens, 2, ',', '.') }}
                </p>
                <p class="summary-line"><strong>Desconto:</strong> R$ {{ number_format($descontoOS, 2, ',', '.') }}</p>
                <p class="summary-total-line"><strong>TOTAL DA ORDEM: R$
                        {{ number_format($totalOS, 2, ',', '.') }}</strong></p>
            </div>
        </section>

        @if (isset($ordemServico->prescricao) && $ordemServico->prescricao)
            <section class="section">
                <h3 class="section-title">Prescrição Médica</h3>
                <table class="info-grid">
                    <tr>
                        <td>
                            <span class="label">Número da Prescrição:</span>
                            <span
                                class="value">#{{ str_pad($ordemServico->prescricao->id, 6, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td>
                            <span class="label">Data da Prescrição:</span>
                            <span
                                class="value">{{ $ordemServico->prescricao->created_at?->format('d/m/Y') ?? 'Não informado' }}</span>
                        </td>
                    </tr>
                </table>

                @php $rx = $ordemServico->prescricao; @endphp
                <table class="table-data pt-6">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 9%;">Dist.</th>
                            <th class="text-center" style="width: 6%;">Olho</th>
                            <th class="text-center" style="width: 9%;">Esferico</th>
                            <th class="text-center" style="width: 9%;">Cilindrico</th>
                            <th class="text-center" style="width: 7%;">Eixo</th>
                            <th class="text-center" style="width: 7%;">AV</th>
                            <th class="text-center" style="width: 8%;">DNP</th>
                            <th class="text-center" style="width: 8%;">Altura</th>
                            <th class="text-center" style="width: 9%;">Adicao</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- LONGE OD --}}
                        <tr>
                            <td rowspan="2" class="text-center" style="background:#f8fafc;"><strong>Longe</strong>
                            </td>
                            <td class="text-center"><strong>OD</strong></td>
                            <td class="text-center">{{ $rx->esfera_od ?? '-' }}</td>
                            <td class="text-center">{{ $rx->cilindro_od ?? '-' }}</td>
                            <td class="text-center">{{ !is_null($rx->eixo_od) ? $rx->eixo_od . '°' : '-' }}</td>
                            <td class="text-center">{{ $rx->acuidade_od ?? '-' }}</td>
                            <td class="text-center">{{ !is_null($rx->dnp_od) ? $rx->dnp_od . 'mm' : '-' }}</td>
                            <td class="text-center">{{ !is_null($rx->altura_od) ? $rx->altura_od . 'mm' : '-' }}</td>
                            <td class="text-center">{{ $rx->adicao_od ?? '-' }}</td>
                        </tr>
                        {{-- LONGE OE --}}
                        <tr>
                            <td class="text-center"><strong>OE</strong></td>
                            <td class="text-center">{{ $rx->esfera_oe ?? '-' }}</td>
                            <td class="text-center">{{ $rx->cilindro_oe ?? '-' }}</td>
                            <td class="text-center">{{ !is_null($rx->eixo_oe) ? $rx->eixo_oe . '°' : '-' }}</td>
                            <td class="text-center">{{ $rx->acuidade_oe ?? '-' }}</td>
                            <td class="text-center">{{ !is_null($rx->dnp_oe) ? $rx->dnp_oe . 'mm' : '-' }}</td>
                            <td class="text-center">{{ !is_null($rx->altura_oe) ? $rx->altura_oe . 'mm' : '-' }}</td>
                            <td class="text-center">{{ $rx->adicao_oe ?? '-' }}</td>
                        </tr>
                        {{-- PERTO OD --}}
                        <tr>
                            <td rowspan="2" class="text-center" style="background:#f8fafc;"><strong>Perto</strong>
                            </td>
                            <td class="text-center"><strong>OD</strong></td>
                            <td class="text-center">{{ $rx->esfera_od_perto ?? '-' }}</td>
                            <td class="text-center">{{ $rx->cilindro_od_perto ?? '-' }}</td>
                            <td class="text-center">
                                {{ !is_null($rx->eixo_od_perto) ? $rx->eixo_od_perto . '°' : '-' }}</td>
                            <td class="text-center">{{ $rx->acuidade_od_perto ?? '-' }}</td>
                            <td class="text-center">{{ !is_null($rx->dnp_od_perto) ? $rx->dnp_od_perto . 'mm' : '-' }}
                            </td>
                            <td class="text-center">
                                {{ !is_null($rx->altura_od_perto) ? $rx->altura_od_perto . 'mm' : '-' }}</td>
                            <td class="text-center">{{ $rx->adicao_od_perto ?? '-' }}</td>
                        </tr>
                        {{-- PERTO OE --}}
                        <tr>
                            <td class="text-center"><strong>OE</strong></td>
                            <td class="text-center">{{ $rx->esfera_oe_perto ?? '-' }}</td>
                            <td class="text-center">{{ $rx->cilindro_oe_perto ?? '-' }}</td>
                            <td class="text-center">
                                {{ !is_null($rx->eixo_oe_perto) ? $rx->eixo_oe_perto . '°' : '-' }}</td>
                            <td class="text-center">{{ $rx->acuidade_oe_perto ?? '-' }}</td>
                            <td class="text-center">{{ !is_null($rx->dnp_oe_perto) ? $rx->dnp_oe_perto . 'mm' : '-' }}
                            </td>
                            <td class="text-center">
                                {{ !is_null($rx->altura_oe_perto) ? $rx->altura_oe_perto . 'mm' : '-' }}</td>
                            <td class="text-center">{{ $rx->adicao_oe_perto ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </section>
        @endif

        <table class="info-grid">
            <tr>
                <td>
                    <span class="label">Observações:</span>
                    <span class="value">
                        {{ $ordemServico->observacoes ?: $pedido->observacoes ?? 'Sem observações registradas para esta ordem.' }}</span>
                </td>
            </tr>
        </table>




        <section class="section">
            <table class="signatures">
                <tr>
                    <td>
                        <div class="signature-line">
                            <strong>Responsável da Ótica</strong><br>
                            Data: ____ / ____ / ______
                        </div>
                    </td>
                    <td>
                        <div class="signature-line">
                            <strong>Responsável do Laboratorio</strong><br>
                            Data: ____ / ____ / ______
                        </div>
                    </td>
                </tr>
            </table>
        </section>
    </main>

    <footer class="pdf-footer">
        <table class="pdf-footer-table">
            <tr>
                <td class="pdf-footer-left">
                    Documento gerado por {{ AuthHelper::tenantName() ?? 'VisaoSis' }}
                </td>
                <td class="pdf-footer-right">
                    Ordem #{{ str_pad($ordemServico->id, 6, '0', STR_PAD_LEFT) }}
                </td>
            </tr>
        </table>
    </footer>
</body>

</html>
