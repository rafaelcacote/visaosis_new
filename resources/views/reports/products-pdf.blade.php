@php
    use App\Helpers\AuthHelper;
@endphp
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Produtos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        {!! file_get_contents(resource_path('views/pdf/styles/header-footer.css')) !!} body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-size: 10px;
            line-height: 1.5;
            color: #212529;
            background: white;
            margin: 0;
            padding: 4px;
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

        .card-body {
            flex: 1 1 auto;
            padding: 0.3rem;
            border: none;
        }

        .row {
            display: table !important;
            width: 100% !important;
            table-layout: fixed !important;
            margin-bottom: 1px !important;
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
            margin-bottom: 1px !important;
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
            padding: 0.2rem !important;
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

        .text-end {
            text-align: right !important;
        }

        .text-muted {
            color: #6c757d !important;
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

        .bg-success {
            background-color: #198754 !important;
        }

        .bg-danger {
            background-color: #dc3545 !important;
        }

        .bg-info {
            background-color: #0dcaf0 !important;
        }

        .bg-warning {
            background-color: #ffc107 !important;
            color: #000 !important;
        }

        .bg-primary {
            background-color: #0d6efd !important;
        }

        .bg-secondary {
            background-color: #6c757d !important;
        }

        .text-white {
            color: #fff !important;
        }

        .stats-row {
            display: table;
            width: 100%;
            table-layout: fixed;
            margin-bottom: 15px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }

        .stat-column {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 12px 8px;
            border-right: 1px solid #dee2e6;
            vertical-align: middle;
        }

        .stat-column:last-child {
            border-right: none;
        }

        .stat-number {
            display: block;
            font-size: 18px;
            font-weight: bold;
            color: #212529;
            margin-bottom: 2px;
        }

        .stat-label {
            display: block;
            font-size: 9px;
            color: #6c757d;
            margin: 0;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            margin: 15px 0 8px 0;
            color: #212529;
        }

        .produto-block {
            page-break-inside: avoid;
            margin-bottom: 8px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }

        .produto-header {
            background-color: #f8f9fa;
            padding: 5px 8px;
            border-bottom: 1px solid #dee2e6;
        }

        .produto-body {
            padding: 5px 8px;
        }

        .status-ativo {
            color: #198754;
            font-weight: bold;
        }

        .status-inativo {
            color: #dc3545;
            font-weight: bold;
        }

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
                    <h2 class="doc-title">RELATORIO DE PRODUTOS</h2>
                    <p class="doc-subtitle">Emissao: {{ now('America/Sao_Paulo')->format('d/m/Y H:i') }}</p>
                </td>
            </tr>
        </table>
    </header>

    <!-- Filtros aplicados -->
    <div class="col-12" style="page-break-inside: avoid; margin-bottom: 1px;">
        <h3 class="section-title">FILTROS APLICADOS</h3>
        <div class="p-3">
            <div class="row mb-2">
                <div class="col-md-6">
                    @if ($search)
                        <p><strong>Busca:</strong> {{ $search }}</p>
                    @else
                        <p><strong>Busca:</strong> Todos os produtos</p>
                    @endif
                </div>
                <div class="col-md-6">
                    @if ($categoriaId)
                        @php $catNome = $categorias->where('id', $categoriaId)->first()?->descricao ?? 'N/A'; @endphp
                        <p><strong>Categoria:</strong> {{ $catNome }}</p>
                    @else
                        <p><strong>Categoria:</strong> Todas</p>
                    @endif
                    @if ($status !== '')
                        <p><strong>Status:</strong> {{ $status === '1' ? 'Ativo' : 'Inativo' }}</p>
                    @else
                        <p><strong>Status:</strong> Todos</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Resumo Estatístico -->
    <div class="col-12" style="page-break-inside: avoid; margin-bottom: 1px;">
        <h3 class="section-title">RESUMO ESTATÍSTICO</h3>
        <div class="p-3">
            <div class="stats-row">
                <div class="stat-column">
                    <span class="stat-number">{{ $stats['total'] }}</span>
                    <span class="stat-label">Total de Produtos</span>
                </div>
                <div class="stat-column">
                    <span class="stat-number">{{ $stats['ativos'] }}</span>
                    <span class="stat-label">Ativos</span>
                </div>
                <div class="stat-column">
                    <span class="stat-number">{{ $stats['inativos'] }}</span>
                    <span class="stat-label">Inativos</span>
                </div>
                <div class="stat-column">
                    <span class="stat-number">{{ $stats['com_atributos'] }}</span>
                    <span class="stat-label">Com Atributos</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Produtos -->
    <div class="col-12" style="margin-bottom: 1px;">
        <h3 class="section-title">PRODUTOS</h3>
        <div class="p-3">
            @if ($produtos->isEmpty())
                <p style="text-align: center; color: #666; font-style: italic; margin: 20px 0;">
                    Nenhum produto encontrado para os filtros selecionados.
                </p>
            @else
                <table class="table table-bordered table-sm" style="font-size: 9px;">
                    <thead>
                        <tr>
                            <th style="background-color: #f8f9fa;">Nome</th>
                            <th style="background-color: #f8f9fa;">Marca</th>
                            <th style="background-color: #f8f9fa;">Categoria</th>
                            <th style="background-color: #f8f9fa;">Preço Custo</th>
                            <th style="background-color: #f8f9fa;">Preço Venda</th>
                            <th style="background-color: #f8f9fa;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($produtos as $produto)
                            <tr>
                                <td>{{ $produto->nome }}</td>
                                <td>{{ $produto->marca ?? '—' }}</td>
                                <td>{{ $produto->categoria->descricao ?? 'Sem Categoria' }}</td>
                                <td>{{ $produto->preco_custo_formatado }}</td>
                                <td>{{ $produto->preco_venda_formatado }}</td>
                                <td>
                                    @if ($produto->ativo)
                                        <span class="status-ativo"> Ativo</span>
                                    @else
                                        <span class="status-inativo"> Inativo</span>
                                    @endif
                                </td>
                            </tr>
                            @if (!empty($produto->atributos) && count($produto->atributos) > 0)
                                <tr>
                                    <td colspan="6" style="background-color: #f8f9fa; padding: 4px 8px;">
                                        <strong>Atributos:</strong>
                                        @foreach ($produto->atributos as $key => $value)
                                            <span>{{ $key }}: {{ $value }}</span>
                                            @if (!$loop->last)
                                                <span> | </span>
                                            @endif
                                        @endforeach
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <footer class="pdf-footer">
        <table class="pdf-footer-table">
            <tr>
                <td class="pdf-footer-left">Documento gerado por {{ AuthHelper::tenantName() ?? 'VisaoSis' }}</td>
                <td class="pdf-footer-right">Relatorio de Produtos</td>
            </tr>
        </table>
    </footer>
</body>

</html>
