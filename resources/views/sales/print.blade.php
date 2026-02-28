<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Venda {{ $sale['numero'] }}</title>
    <style>
        @page {
            margin-top: 15mm;
            margin-bottom: 15mm;
            margin-left: 20mm;
            margin-right: 20mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            background: #fff;
            padding: 0;
            margin: 0;
        }

        .container-fluid {
            width: 100%;
            padding: 0;
            margin: 0;
        }

        .card {
            border: none;
            box-shadow: none;
        }

        .card-body {
            padding: 20px;
        }

        h3 {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin: 0;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        hr {
            border: 0;
            border-top: 1px solid #ddd;
            margin: 20px 0;
        }

        .d-flex {
            display: flex;
        }

        .justify-content-between {
            justify-content: space-between;
        }

        .justify-content-center {
            justify-content: center;
        }

        .col-lg-3 {
            width: 48%;
        }

        .col-lg-12 {
            width: 100%;
        }

        .ps-0 {
            padding-left: 0;
        }

        .pe-0 {
            padding-right: 0;
        }

        .mt-5 {
            margin-top: 30px;
        }

        .mt-4 {
            margin-top: 20px;
        }

        .mt-2 {
            margin-top: 10px;
        }

        .mb-2 {
            margin-bottom: 10px;
        }

        .mb-5 {
            margin-bottom: 30px;
        }

        .mb-0 {
            margin-bottom: 0;
        }

        .my-5 {
            margin-top: 30px;
            margin-bottom: 30px;
        }

        p {
            margin: 5px 0;
            line-height: 1.6;
        }

        b {
            font-weight: bold;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }

        .table thead {
            background-color: #f8f9fa;
            color: #333;
        }

        .table th {
            padding: 12px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
            border: 1px solid #dee2e6;
        }

        .table td {
            padding: 10px 12px;
            border: 1px solid #ddd;
            font-size: 11px;
        }

        .table tbody tr {
            border-bottom: 1px solid #ddd;
        }

        .table tbody tr:last-child {
            border-bottom: none;
        }

        .bg-dark {
            background-color: #f8f9fa !important;
        }

        .text-white {
            color: #333 !important;
        }

        .w-100 {
            width: 100%;
        }

        .float-end {
            float: right;
        }

        .btn {
            display: inline-block;
            padding: 8px 16px;
            margin-left: 10px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 11px;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-success {
            background-color: #28a745;
            color: white;
        }

        .ms-2 {
            margin-left: 10px;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .logo-section {
            flex: 0 0 auto;
        }

        .logo-section img {
            max-width: 60px;
            max-height: 40px;
            object-fit: contain;
            margin: 0;
            padding: 0;
            vertical-align: middle;
        }

        .title-section {
            flex: 1;
            text-align: right;
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }

        .title-section h3 {
            margin: 0;
            padding: 0;
            line-height: 1;
            display: inline-block;
            vertical-align: middle;
        }

        @media print {
            .btn {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="row">
        <div class="col-lg-12">
            <div class="card px-2">
                <div class="card-body">
                    <div class="container-fluid">
                        <!-- Primeira linha: Logo esquerda, Nome empresa direita -->
                        <div class="header-top">
                            <div class="logo-section">
                                @if (!empty($tenantData['logo_base64']))
                                    <img src="{{ $tenantData['logo_base64'] }}" alt="Logo da Empresa" style="display: inline-block; vertical-align: middle;">
                                @elseif (!empty($tenantData['logo_url']))
                                    <img src="{{ $tenantData['logo_url'] }}" alt="Logo da Empresa" style="display: inline-block; vertical-align: middle;">
                                @endif
                            </div>
                            <div class="title-section">
                                <span style="font-size: 20px; font-weight: bold; display: inline-block; vertical-align: middle; line-height: 1;">{{ $tenantData['nome'] }}</span>
                            </div>
                        </div>
                        
                        <!-- Segunda linha: Título COMPROVANTE DE VENDA e número -->
                        <div style="text-align: center; margin: 20px 0;">
                            <h2 style="margin: 0; font-size: 18px; font-weight: bold;">COMPROVANTE DE VENDA</h2>
                            <p style="margin: 5px 0; font-size: 14px; font-weight: bold;">{{ $sale['numero'] }}</p>
                        </div>
                        
                        <hr>
                        
                        <!-- Terceira linha: Dados da venda e dados do cliente alinhados à esquerda -->
                        <div class="container-fluid mt-4">
                            <table style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td style="width: 50%; vertical-align: top; text-align: left; padding-right: 20px;">
                                        <p class="mb-2"><b>Dados da Venda</b></p>
                                        <p class="mb-0">Data da Venda: {{ $sale['data_formatada'] }}</p>
                                        <p>Forma de Pagamento: {{ $sale['forma_pagamento'] }}</p>
                                        @if ($sale['parcelas'] > 1)
                                            <p>Parcelas: {{ $sale['parcelas'] }}x de R$ {{ number_format($sale['valor_parcela'], 2, ',', '.') }}</p>
                                        @endif
                                        @if ($vendedor['nome'])
                                            <p>Vendedor: {{ $vendedor['nome'] }}</p>
                                        @endif
                                    </td>
                                    <td style="width: 50%; vertical-align: top; text-align: left;">
                                        <p class="mb-2"><b>Dados do Cliente</b></p>
                                        @if ($sale['cliente'])
                                            <p class="mb-0">
                                                {{ $sale['cliente']['nome'] }}<br>
                                                @if ($sale['cliente']['cpf'])
                                                    CPF: {{ $sale['cliente']['cpf'] }}<br>
                                                @endif
                                                @if ($sale['cliente']['telefone'])
                                                    Tel: {{ $sale['cliente']['telefone'] }}<br>
                                                @endif
                                                @if ($sale['cliente']['email'])
                                                    E-mail: {{ $sale['cliente']['email'] }}<br>
                                                @endif
                                                @if ($sale['cliente']['endereco'])
                                                    {{ $sale['cliente']['endereco'] }}
                                                @endif
                                            </p>
                                        @else
                                            <p>Cliente não informado</p>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="container-fluid mt-5 d-flex justify-content-center w-100">
                        <div class="table-responsive w-100">
                            <table class="table">
                                <thead>
                                    <tr class="bg-dark text-white">
                                        <th>#</th>
                                        <th>Descrição</th>
                                        <th class="text-right">Quantidade</th>
                                        <th class="text-right">Preço Unitário</th>
                                        <th class="text-right">Desconto</th>
                                        <th class="text-right">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sale['produtos'] as $index => $produto)
                                        <tr class="text-right">
                                            <td class="text-left">{{ $index + 1 }}</td>
                                            <td class="text-left">{{ $produto['nome'] }}</td>
                                            <td>{{ $produto['quantidade'] }}</td>
                                            <td>R$ {{ number_format($produto['preco_unitario'], 2, ',', '.') }}</td>
                                            <td>
                                                @if ($produto['desconto'] > 0)
                                                    R$ {{ number_format($produto['desconto'], 2, ',', '.') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>R$ {{ number_format($produto['subtotal'], 2, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="container-fluid mt-5 w-100">
                        <p class="text-right mb-2">Subtotal: R$ {{ number_format($sale['subtotal'], 2, ',', '.') }}</p>
                        @if ($sale['desconto'] > 0)
                            <p class="text-right mb-2">Desconto: - R$ {{ number_format($sale['desconto'], 2, ',', '.') }}</p>
                        @endif
                        <h4 class="text-right mb-5">Total: R$ {{ number_format($sale['total'], 2, ',', '.') }}</h4>
                        <hr>
                    </div>
                    @if ($sale['observacoes'])
                        <div class="container-fluid mt-3 w-100">
                            <p><b>Observações:</b></p>
                            <p>{{ $sale['observacoes'] }}</p>
                        </div>
                    @endif
                    <div class="container-fluid w-100 mt-4">
                        <p class="text-right" style="font-size: 10px; color: #666;">
                            Documento gerado em {{ now()->format('d/m/Y H:i:s') }} | Sistema VisaoSis
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
