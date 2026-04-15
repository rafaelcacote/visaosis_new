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
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            background-color: #fff;
            box-shadow: none;
        }

        .card-header {
            padding: 0.2rem 1rem;
            margin-bottom: 0;
            background-color: #0d6efd;
            border-bottom: 1px solid #dee2e6;
            border-top-left-radius: calc(0.375rem - 1px);
            border-top-right-radius: calc(0.375rem - 1px);
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
            margin-bottom: 4px;
        }

        .supplier-section {
            page-break-inside: avoid;
            margin-bottom: 4px;
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
        <div class="card-header bg-primary text-white d-print-block">
            <div class="row align-items-center">
                <div class="col-6">
                    <h5 class="mb-0">ORDEM DE SERVIÇO</h5>
                    <small>#{{ str_pad($ordemServico->id, 6, '0', STR_PAD_LEFT) }}</small>
                </div>
                <div class="col-6 text-end">
                    <h6 class="mb-0">VisaoSis</h6>
                    <small>Sistema de Gestão Ótica</small>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- Cabeçalho -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6>DADOS DO CLIENTE</h6>
                    @if ($ordemServico->pedido && $ordemServico->pedido->cliente)
                        <strong>{{ $ordemServico->pedido->cliente->nome }}</strong><br>
                        CPF: {{ $ordemServico->pedido->cliente->cpf_formatado ?? 'N/A' }}<br>
                        Telefone: {{ $ordemServico->pedido->cliente->telefone_formatado ?? 'N/A' }}<br>
                        @if ($ordemServico->pedido->cliente->endereco_completo)
                            Endereço: {{ $ordemServico->pedido->cliente->endereco_completo }}
                        @endif
                    @else
                        <em class="text-muted">Cliente não informado</em>
                    @endif
                </div>
                <div class="col-md-6">
                    <h6>DADOS DA ORDEM</h6>
                    <strong>Número:</strong> #{{ str_pad($ordemServico->id ?? 0, 6, '0', STR_PAD_LEFT) }}<br>
                    <strong>Data Criação:</strong>
                    {{ $ordemServico->created_at ? $ordemServico->created_at->format('d/m/Y H:i') : 'N/A' }}<br>
                    <strong>Venda:</strong>
                    @if ($ordemServico->pedido_id && $ordemServico->pedido)
                        #{{ str_pad($ordemServico->pedido->id, 6, '0', STR_PAD_LEFT) }}
                    @else
                        N/A
                    @endif
                    <br>
                    @if ($ordemServico->entrega_em)
                        <strong>Data Entrega:</strong> {{ $ordemServico->entrega_em->format('d/m/Y H:i') }}<br>
                    @endif
                    <strong>Prioridade:</strong>
                    <span class="badge bg-{{ $ordemServico->prioridade_class ?? 'secondary' }}">
                        {{ $ordemServico->prioridade_label ?? 'N/A' }}
                    </span><br>
                    <strong>Status:</strong>
                    <span class="badge bg-{{ $currentStatus['class'] }}">
                        {{ $currentStatus['text'] }}
                    </span>
                </div>
            </div>

            <!-- Fornecedor -->
            @if ($ordemServico->fornecedor_id && $ordemServico->fornecedor)
                <div class="supplier-section">
                    <div class="col-12">
                        <h6>FORNECEDOR/LABORATÓRIO RESPONSÁVEL</h6>
                        <div class="border p-3 rounded">
                            <strong>{{ $ordemServico->fornecedor->razao_social ?? 'N/A' }}</strong>
                            @if ($ordemServico->fornecedor->nome_fantasia)
                                <br><em>{{ $ordemServico->fornecedor->nome_fantasia }}</em>
                            @endif
                            <br>
                            @if (isset($ordemServico->fornecedor->endereco_completo) && $ordemServico->fornecedor->endereco_completo)
                                {{ $ordemServico->fornecedor->endereco_completo }}<br>
                            @endif
                            @if ($ordemServico->fornecedor->telefone)
                                Telefone:
                                {{ $ordemServico->fornecedor->telefone_formatado ?? $ordemServico->fornecedor->telefone }}<br>
                            @endif
                            @if ($ordemServico->fornecedor->email)
                                Email: {{ $ordemServico->fornecedor->email }}
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div class="supplier-section">
                    <div class="col-12">
                        <h6>FORNECEDOR/LABORATÓRIO RESPONSÁVEL</h6>
                        <div class="alert alert-warning">
                            Nenhum fornecedor associado a esta ordem.
                        </div>
                    </div>
                </div>
            @endif


            <!-- Produtos -->
            <div class="col-12" style="page-break-inside: avoid; margin-bottom: 8px;">
                <h6>PRODUTOS E ESPECIFICAÇÕES</h6>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Produto</th>
                                <th>Qtd</th>
                                <th>Especificações</th>
                                <th>Observações</th>
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

            <!-- Receita Médica -->
            @if (isset($ordemServico->prescricao) && $ordemServico->prescricao)
                <div class="prescription-section">
                    <div class="col-12">
                        <h6>PRESCRIÇÃO MÉDICA</h6>
                        <div class="border p-3 rounded">
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
                                <table class="table table-sm table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 15%;">Olho</th>
                                            <th style="width: 15%;">Esférico</th>
                                            <th style="width: 15%;">Cilíndrico</th>
                                            <th style="width: 15%;">Eixo</th>
                                            <th style="width: 15%;">DNP</th>
                                            <th style="width: 15%;">Adição</th>
                                            <th style="width: 15%;">Altura</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>OD</strong><br><small>(Olho Direito)</small></td>
                                            <td>{{ $ordemServico->prescricao->esfera_od ?? '0.00' }}</td>
                                            <td>{{ $ordemServico->prescricao->cilindro_od ?? '0.00' }}</td>
                                            <td>{{ $ordemServico->prescricao->eixo_od ?? '0' }}°</td>
                                            <td>{{ $ordemServico->prescricao->dnp_od ? $ordemServico->prescricao->dnp_od . 'mm' : '-' }}
                                            </td>
                                            <td>{{ $ordemServico->prescricao->adicao_od ?? '-' }}</td>
                                            <td>{{ $ordemServico->prescricao->altura_od ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>OE</strong><br><small>(Olho Esquerdo)</small></td>
                                            <td>{{ $ordemServico->prescricao->esfera_oe ?? '0.00' }}</td>
                                            <td>{{ $ordemServico->prescricao->cilindro_oe ?? '0.00' }}</td>
                                            <td>{{ $ordemServico->prescricao->eixo_oe ?? '0' }}°</td>
                                            <td>{{ $ordemServico->prescricao->dnp_oe ? $ordemServico->prescricao->dnp_oe . 'mm' : '-' }}
                                            </td>
                                            <td>{{ $ordemServico->prescricao->adicao_oe ?? '-' }}</td>
                                            <td>{{ $ordemServico->prescricao->altura_oe ?? '-' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Informações Adicionais -->
                            @if ($ordemServico->prescricao->tipo_lente)
                                <div class="mt-2">
                                    <strong>Tipo de Lente:</strong> {{ $ordemServico->prescricao->tipo_lente }}
                                </div>
                            @endif
                            @if ($ordemServico->prescricao->diagnostico)
                                <div class="mt-2">
                                    <strong>Diagnóstico:</strong> {{ $ordemServico->prescricao->diagnostico }}
                                </div>
                            @endif
                            @if ($ordemServico->prescricao->recomendacoes)
                                <div class="mt-2">
                                    <strong>Recomendações:</strong> {{ $ordemServico->prescricao->recomendacoes }}
                                </div>
                            @endif
                            @if ($ordemServico->prescricao->observacoes)
                                <div class="mt-2">
                                    <strong>Observações:</strong> {{ $ordemServico->prescricao->observacoes }}
                                </div>
                            @endif
                            @if ($ordemServico->prescricao->validade_dias)
                                <div class="mt-2">
                                    <strong>Validade:</strong> {{ $ordemServico->prescricao->validade_dias }} dias
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Observações -->
            @if ($ordemServico->observacoes)
                <div class="col-12" style="page-break-inside: avoid; margin-bottom: 8px;">
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
