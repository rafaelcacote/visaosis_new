<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Receita para Óculos</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 14px;
            line-height: 1.5;
            color: #333;
        }

        .text-center {
            text-align: center;
        }

        .text-end {
            text-align: right;
        }

        .mb-4 {
            margin-bottom: 0.5rem;
        }

        .mb-3 {
            margin-bottom: 0.5rem;
        }

        .mb-2 {
            margin-bottom: 0.5rem;
        }

        .mb-1 {
            margin-bottom: 0.25rem;
        }

        .mb-0 {
            margin-bottom: 0;
        }

        .mt-3 {
            margin-top: 1rem;
        }

        .mt-4 {
            margin-top: 1.5rem;
        }

        .fw-bold {
            font-weight: bold;
        }

        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
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

        /* Grid system simulation for PDF */
        .row {
            width: 100%;
            clear: both;
        }

        .col-md-6 {
            width: 48%;
            float: left;
        }

        .col-md-6:first-child {
            margin-right: 4%;
        }

        /* Specific overrides */
        .prescription-document {
            padding: 0 !important;
        }

        .bg-light {
            background-color: #f8f9fa !important;
        }

        .card-header {
            padding: 0.5rem 1rem;
            margin-bottom: 0;
            background-color: rgba(0, 0, 0, 0.03);
            border-bottom: 1px solid rgba(0, 0, 0, 0.125);
        }

        .card-header h6 {
            font-size: 16px;
            font-weight: bold;
            margin: 0;
        }

        /* Estilos para títulos de seções (Prescrição, Diagnóstico, etc) */
        .prescription-table h6,
        .diagnosis h6,
        .observations h6,
        .recommendations h6 {
            font-size: 16px;
            font-weight: bold;
            margin-top: 20px;
            /* Pular linha */
            margin-bottom: 10px;
            text-align: left;
            /* Garantir alinhamento à esquerda */
            padding-left: 0;
        }

        /* Garantir que as seções limpem os floats anteriores (cards) */
        .prescription-table,
        .diagnosis,
        .observations,
        .recommendations {
            clear: both;
            width: 100%;
            display: block;
        }

        /* Reduzir espaçamento entre parágrafos */
        p {
            margin-bottom: 5px;
            line-height: 1.2;
        }

        /* Ajustes da área de assinatura */
        .signature-area {
            margin-top: 20px;
            /* Reduzir margem superior da área */
        }

        .signature-area hr {
            margin-bottom: 5px;
            /* Aproximar o nome da linha */
            border: 0;
            border-top: 1px solid #000;
        }

        .signature-area p {
            margin-top: 0;
            margin-bottom: 0;
            /* Remover margem inferior */
            line-height: 1.1;
            /* Reduzir altura da linha */
        }

        .border {
            border: 1px solid #dee2e6 !important;
        }

        .p-3 {
            padding: 1rem !important;
        }

        .d-flex {
            display: block;
            /* DomPDF has poor flex support */
        }

        .justify-content-between {
            /* Fallback for flex justify-content-between */
        }

        .d-flex>div {
            display: inline-block;
            vertical-align: top;
        }

        .rx-side__text {
            writing-mode: initial !important;
            transform: rotate(-90deg) !important;
            transform-origin: center;
            white-space: nowrap;
        }
    </style>
</head>

<body>
    @include('professional.prescription')
</body>

</html>
