@php
    use App\Helpers\AuthHelper;
@endphp
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ordem de Serviço #{{ str_pad($ordemServico->id, 6, '0', STR_PAD_LEFT) }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-size: 10px;
            line-height: 1.5;
            color: #212529;
            background: white;
            margin: 0;
            padding: 8px;
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
            padding: 0.4rem;
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
            padding: 0.4rem !important;
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
            margin-bottom: 2px !important;
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
            margin-bottom: 3px !important;
        }

        /* Evitar quebras indevidas */
        .prescription-section {
            page-break-inside: avoid;
            margin-bottom: 2px;
        }

        .supplier-section {
            page-break-inside: avoid;
            margin-bottom: 2px;
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
    <!-- Documento da OS -->
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
                        <h5 style="margin: 0; font-weight: 600; color: #000;">ORDEM DE SERVIÇO</h5>
                        <small style="color: #6c757d;">#{{ str_pad($ordemServico->id, 6, '0', STR_PAD_LEFT) }}</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- Cabeçalho -->

            <div class="col-12" style="page-break-inside: avoid; margin-bottom: 2px;">

                <div class="row mb-2">
                    <div class="col-md-6">
                        <h6>DADOS DO CLIENTE</h6>
                    </div>
                    <div class="col-md-6">
                        <h6>DADOS DA ORDEM</h6>
                    </div>
                </div>

                <div class="p-3 header-data-section">

                    <div class="row mb-2">
                        <div class="col-md-6">
                            @if ($ordemServico->pedido && $ordemServico->pedido->cliente)
                                <strong>{{ $ordemServico->pedido->cliente->nome }}</strong><br>
                            @endif

                        </div>
                        <div class="col-md-6">
                            <strong>Número:</strong> #{{ str_pad($ordemServico->id ?? 0, 6, '0', STR_PAD_LEFT) }}<br>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-6">
                            @if ($ordemServico->pedido && $ordemServico->pedido->cliente)
                                <strong>CPF:</strong> {{ $ordemServico->pedido->cliente->cpf_formatado ?? 'N/A' }}<br>
                            @endif

                        </div>
                        <div class="col-md-6">
                            <strong>Data Criação:</strong>
                            {{ $ordemServico->created_at ? $ordemServico->created_at->format('d/m/Y H:i') : 'N/A' }}
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            @if ($ordemServico->pedido && $ordemServico->pedido->cliente)
                                <strong>Telefone:</strong>
                                {{ $ordemServico->pedido->cliente->telefone_formatado ?? '-' }}
                            @endif
                        </div>
                        <div class="col-md-6">
                            <strong>Venda:</strong>
                            @if ($ordemServico->pedido_id && $ordemServico->pedido)
                                #{{ str_pad($ordemServico->pedido->id, 6, '0', STR_PAD_LEFT) }}
                            @else
                                N/A
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            @if ($ordemServico->pedido && $ordemServico->pedido->cliente)
                                @if ($ordemServico->pedido->cliente->endereco_completo)
                                    <strong>Endereço:</strong> {{ $ordemServico->pedido->cliente->endereco_completo }}
                                @endif
                            @endif
                        </div>
                        <div class="col-md-6">
                            <strong>Prioridade:</strong>
                            <span class="badge bg-{{ $ordemServico->prioridade_class ?? 'secondary' }}">
                                {{ $ordemServico->prioridade_label ?? 'N/A' }}
                            </span>
                            &nbsp;&nbsp;&nbsp;
                            <strong>Status:</strong>
                            <span class="badge bg-{{ $currentStatus['class'] }}">
                                {{ $currentStatus['text'] }}
                            </span>
                        </div>
                    </div>

                </div>
            </div>
            <!-- Fornecedor -->
            <div class="col-12" style="page-break-inside: avoid; margin-bottom: 2px;">
                <h6>FORNECEDOR/LABORATÓRIO RESPONSÁVEL</h6>
                <div class="p-3">

                    <div class="row mb-2">
                        <div class="col-md-6">
                            @if ($ordemServico->fornecedor->cnpj)
                                <strong>{{ $ordemServico->fornecedor->razao_social ?? 'N/A' }}</strong>
                                @if ($ordemServico->fornecedor->nome_fantasia)
                                    <br><em>{{ $ordemServico->fornecedor->nome_fantasia }}</em>
                                @endif
                            @endif
                        </div>
                        <div class="col-md-6">

                            @if ($ordemServico->fornecedor->cnpj)
                                <strong> CNPJ:</strong>
                                {{ preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $ordemServico->fornecedor->cnpj) }}
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            @if ($ordemServico->fornecedor->telefone)
                                <strong> Telefone:</strong>
                                {{ $ordemServico->fornecedor->telefone_formatado ?? $ordemServico->fornecedor->telefone }}<br>
                            @endif
                        </div>
                        <div class="col-md-6">

                            @if ($ordemServico->fornecedor->email)
                                <strong> Email:</strong> {{ $ordemServico->fornecedor->email }}
                            @endif
                        </div>
                    </div>
                </div>

            </div>



            <!-- Produtos -->
            <div class="col-12" style="page-break-inside: avoid; margin-bottom: 2px;">
                <h6>PRODUTOS E ESPECIFICAÇÕES</h6>
                <div class="p-3">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">Produto</th>
                                    <th class="text-center">Qtd</th>
                                    <th class="text-center">Especificações</th>
                                    <th class="text-center">Observações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($ordemServico->itensOrdem && $ordemServico->itensOrdem->count() > 0)
                                    @foreach ($ordemServico->itensOrdem as $itemOrdem)
                                        @if ($itemOrdem->item && $itemOrdem->item->produto)
                                            <tr>
                                                <td><strong>{{ $itemOrdem->item->produto->nome ?? 'N/A' }}</strong>
                                                </td>
                                                <td class="text-center">{{ $itemOrdem->item->quantidade ?? 0 }}</td>
                                                <td>{{ $itemOrdem->item->produto->categoria->descricao ?? '-' }}</td>
                                                <td>{{ $ordemServico->observacoes ?? '-' }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @else
                                    <tr>
                                        <td><strong>Ordem de Serviço Geral</strong></td>
                                        <td class="text-center">{{ $ordemServico->quantidade ?? 1 }}</td>
                                        <td>{{ $ordemServico->preco_unit_formatado ?? 'N/A' }}</td>
                                        <td>{{ $ordemServico->observacoes ?? '-' }}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Receita Médica -->
            @if (isset($ordemServico->prescricao) && $ordemServico->prescricao)
                <div class="prescription-section">
                    <div class="col-12" style="page-break-inside: avoid; margin-bottom: 2px;">
                        <h6>PRESCRIÇÃO MÉDICA</h6>
                        <div class="p-3">
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <strong>Prescrição:</strong>
                                    #{{ str_pad($ordemServico->prescricao->id, 6, '0', STR_PAD_LEFT) }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Data:</strong>
                                    {{ $ordemServico->prescricao->created_at ? $ordemServico->prescricao->created_at->format('d/m/Y') : 'N/A' }}
                                </div>
                            </div>
                            @if ($ordemServico->prescricao->consulta && $ordemServico->prescricao->consulta->paciente)
                                <div class="row mb-2">
                                    <div class="col-12">
                                        <strong>Paciente:</strong>
                                        {{ $ordemServico->prescricao->consulta->paciente->nome }}
                                        @if ($ordemServico->prescricao->consulta->paciente->cpf)
                                            - CPF:
                                            {{ preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $ordemServico->prescricao->consulta->paciente->cpf) }}
                                        @endif
                                    </div>
                                </div>
                            @elseif ($ordemServico->prescricao->paciente)
                                <div class="row mb-2">
                                    <div class="col-12">
                                        <strong>Paciente:</strong> {{ $ordemServico->prescricao->paciente->nome }}
                                        @if ($ordemServico->prescricao->paciente->cpf)
                                            - CPF:
                                            {{ preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $ordemServico->prescricao->paciente->cpf) }}
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Graduação Principal -->
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center">Olho</th>
                                            <th class="text-center">Esférico</th>
                                            <th class="text-center">Cilíndrico</th>
                                            <th class="text-center">Eixo</th>
                                            <th class="text-center">DNP</th>
                                            <th class="text-center">Adição</th>
                                            <th class="text-center">Altura</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center"><strong>OD</strong><br><small>(Olho
                                                    Direito)</small>
                                            </td>
                                            <td class="text-center">
                                                {{ $ordemServico->prescricao->esfera_od ?? '0.00' }}</td>
                                            <td class="text-center">
                                                {{ $ordemServico->prescricao->cilindro_od ?? '0.00' }}</td>
                                            <td class="text-center">{{ $ordemServico->prescricao->eixo_od ?? '0' }}°
                                            </td>
                                            <td class="text-center">
                                                {{ $ordemServico->prescricao->dnp_od ? $ordemServico->prescricao->dnp_od . 'mm' : '-' }}
                                            </td>
                                            <td class="text-center">{{ $ordemServico->prescricao->adicao_od ?? '-' }}
                                            </td>
                                            <td class="text-center">{{ $ordemServico->prescricao->altura_od ?? '-' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-center"><strong>OE</strong><br><small>(Olho
                                                    Esquerdo)</small></td>
                                            <td class="text-center">
                                                {{ $ordemServico->prescricao->esfera_oe ?? '0.00' }}</td>
                                            <td class="text-center">
                                                {{ $ordemServico->prescricao->cilindro_oe ?? '0.00' }}</td>
                                            <td class="text-center">{{ $ordemServico->prescricao->eixo_oe ?? '0' }}°
                                            </td>
                                            <td class="text-center">
                                                {{ $ordemServico->prescricao->dnp_oe ? $ordemServico->prescricao->dnp_oe . 'mm' : '-' }}
                                            </td>
                                            <td class="text-center">{{ $ordemServico->prescricao->adicao_oe ?? '-' }}
                                            </td>
                                            <td class="text-center">{{ $ordemServico->prescricao->altura_oe ?? '-' }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Informações Adicionais -->
                            <div class="row mb-2">
                                @if ($ordemServico->prescricao->tipo_lente)
                                    <div class="col-md-6">
                                        <strong>Tipo de Lente:</strong> {{ $ordemServico->prescricao->tipo_lente }}
                                    </div>
                                @endif
                                <div class="col-md-6">
                                    <strong>Validade:</strong> {{ $ordemServico->prescricao->validade_dias }} dias
                                </div>
                            </div>

                            <div class="row mb-2">
                                @if ($ordemServico->prescricao->diagnostico)
                                    <div class="col-md-6">
                                        <strong>Diagnóstico:</strong> {{ $ordemServico->prescricao->diagnostico }}
                                    </div>
                                @endif
                                <div class="col-md-6">
                                    <strong>Recomendações:</strong> {{ $ordemServico->prescricao->recomendacoes }}
                                </div>
                            </div>
                            @if ($ordemServico->prescricao->observacoes)
                                <div class="mt-2">
                                    <strong>Observações:</strong> {{ $ordemServico->prescricao->observacoes }}
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            @endif

            <!-- Observações -->
            @if ($ordemServico->observacoes)
                <div class="col-12" style="page-break-inside: avoid; margin-bottom: 2px;">
                    <h6>OBSERVAÇÕES GERAIS</h6>
                    <div>
                        {{ $ordemServico->observacoes }}
                    </div>
                </div>
            @endif

            <!-- Assinaturas -->
            <div class="row">
                <div class="col-md-6">
                    <div class="text-center">
                        <div class="signature-line">
                            <strong>Responsável pela Ótica</strong><br>
                            <small>Data: ___/___/______</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="text-center">
                        <div class="signature-line">
                            <strong>Responsável do Laboratório</strong><br>
                            <small>Data: ___/___/______</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
