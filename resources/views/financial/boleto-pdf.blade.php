<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boleto Bancário - {{ $boleto['id'] }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.2;
            color: #000;
            background: white;
        }
        
        .boleto-container {
            width: 210mm;
            margin: 0 auto;
            padding: 10mm;
            background: white;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #000;
        }
        
        .logo-banco {
            font-size: 18px;
            font-weight: bold;
            color: #003366;
        }
        
        .codigo-banco {
            font-size: 16px;
            font-weight: bold;
            border: 2px solid #000;
            padding: 5px 10px;
        }
        
        .linha-digitavel {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin: 15px 0;
            letter-spacing: 2px;
            font-family: 'Courier New', monospace;
        }
        
        .info-section {
            display: grid;
            grid-template-columns: 1fr 200px;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .beneficiario, .pagador {
            border: 1px solid #000;
            padding: 10px;
        }
        
        .vencimento-valor {
            border: 1px solid #000;
            padding: 10px;
            text-align: center;
        }
        
        .label {
            font-weight: bold;
            font-size: 8px;
            color: #666;
            margin-bottom: 3px;
        }
        
        .valor {
            font-size: 12px;
            font-weight: bold;
        }
        
        .vencimento {
            font-size: 16px;
            font-weight: bold;
            color: #d00;
            margin-bottom: 10px;
        }
        
        .valor-documento {
            font-size: 20px;
            font-weight: bold;
            color: #006600;
        }
        
        .detalhes-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1px;
            margin-bottom: 20px;
            border: 1px solid #000;
        }
        
        .detalhe-item {
            border-right: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 8px;
            background: #f9f9f9;
        }
        
        .detalhe-item:nth-child(4n) {
            border-right: none;
        }
        
        .instrucoes {
            border: 1px solid #000;
            padding: 15px;
            margin-bottom: 20px;
            min-height: 80px;
        }
        
        .codigo-barras {
            text-align: center;
            margin: 20px 0;
            padding: 10px;
            background: #000;
            color: white;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            letter-spacing: 1px;
        }
        
        .recibo-section {
            border-top: 2px dashed #000;
            margin-top: 30px;
            padding-top: 20px;
        }
        
        .recibo-header {
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        
        .recibo-info {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }
        
        .autenticacao {
            text-align: right;
            font-size: 8px;
            color: #666;
            margin-top: 20px;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            
            .boleto-container {
                width: 100%;
                margin: 0;
                padding: 5mm;
            }
            
            .no-print {
                display: none;
            }
        }
        
        .btn-actions {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        
        .btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            margin: 5px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
        }
        
        .btn:hover {
            background: #0056b3;
        }
        
        .btn-success {
            background: #28a745;
        }
        
        .btn-success:hover {
            background: #1e7e34;
        }
    </style>
</head>
<body>
    <div class="btn-actions no-print">
        <button class="btn" onclick="window.print()">🖨️ Imprimir</button>
        <button class="btn btn-success" onclick="downloadPDF()">📄 Download PDF</button>
        <button class="btn" onclick="window.close()">❌ Fechar</button>
    </div>

    <div class="boleto-container">
        <!-- Cabeçalho do Banco -->
        <div class="header">
            <div class="logo-banco">
                BANCO DO BRASIL S.A.
            </div>
            <div class="codigo-banco">
                001-9
            </div>
        </div>
        
        <!-- Linha Digitável -->
        <div class="linha-digitavel">
            {{ $boleto['linha_digitavel'] ?? '34191.09008 61207.954566 00000.142508 1 95470000014962' }}
        </div>
        
        <!-- Informações Principais -->
        <div class="info-section">
            <div>
                <!-- Beneficiário -->
                <div class="beneficiario">
                    <div class="label">BENEFICIÁRIO</div>
                    <div class="valor">{{ $boleto['beneficiario'] ?? 'Beneficiário' }}</div>
                    @if(!empty($boleto['beneficiario_cpf_cnpj']))
                        <div>CPF/CNPJ: {{ $boleto['beneficiario_cpf_cnpj'] }}</div>
                    @endif
                </div>
                
                <!-- Pagador -->
                <div class="pagador" style="margin-top: 15px;">
                    <div class="label">PAGADOR</div>
                    <div class="valor">{{ $boleto['cliente'] ?? 'Maria Silva Santos' }}</div>
                    <div>CPF: {{ $boleto['cpf'] ?? '123.456.789-00' }}</div>
                    <div>{{ $boleto['endereco'] ?? 'Rua das Palmeiras, 456 - Jardim América' }}</div>
                    <div>{{ $boleto['cidade'] ?? 'São Paulo/SP - CEP: 04567-890' }}</div>
                    <div>Tel: {{ $boleto['telefone'] ?? '(11) 99999-9999' }}</div>
                </div>
            </div>
            
            <!-- Vencimento e Valor -->
            <div class="vencimento-valor">
                <div class="label">VENCIMENTO</div>
                <div class="vencimento">{{ \Carbon\Carbon::parse($boleto['vencimento'] ?? '2024-08-30')->format('d/m/Y') }}</div>
                
                <div class="label">VALOR DO DOCUMENTO</div>
                <div class="valor-documento">R$ {{ number_format($boleto['valor_total'] ?? 149.62, 2, ',', '.') }}</div>
                
                @if(isset($boleto['juros']) && $boleto['juros'] > 0)
                <div style="margin-top: 10px; font-size: 10px;">
                    <div class="label">VALOR ORIGINAL</div>
                    <div>R$ {{ number_format($boleto['valor'] ?? 142.50, 2, ',', '.') }}</div>
                    <div class="label">JUROS/MULTA</div>
                    <div style="color: #d00;">R$ {{ number_format($boleto['juros'] ?? 7.12, 2, ',', '.') }}</div>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Detalhes do Documento -->
        <div class="detalhes-grid">
            <div class="detalhe-item">
                <div class="label">NOSSO NÚMERO</div>
                <div class="valor">{{ $boleto['nosso_numero'] ?? '00000142508' }}</div>
            </div>
            <div class="detalhe-item">
                <div class="label">NÚMERO DO DOCUMENTO</div>
                <div class="valor">{{ $boleto['id'] ?? 'BOL-2024-001' }}</div>
            </div>
            <div class="detalhe-item">
                <div class="label">DATA DOCUMENTO</div>
                <div class="valor">{{ \Carbon\Carbon::parse($boleto['gerado_em'] ?? now())->format('d/m/Y') }}</div>
            </div>
            <div class="detalhe-item">
                <div class="label">ESPÉCIE DOC</div>
                <div class="valor">DM</div>
            </div>
            
            <div class="detalhe-item">
                <div class="label">ACEITE</div>
                <div class="valor">N</div>
            </div>
            <div class="detalhe-item">
                <div class="label">DATA PROCESSAMENTO</div>
                <div class="valor">{{ now()->format('d/m/Y') }}</div>
            </div>
            <div class="detalhe-item">
                <div class="label">CARTEIRA</div>
                <div class="valor">18</div>
            </div>
            <div class="detalhe-item">
                <div class="label">ESPÉCIE</div>
                <div class="valor">R$</div>
            </div>
        </div>
        
        <!-- Instruções -->
        <div class="instrucoes">
            <div class="label">INSTRUÇÕES (TEXTO DE RESPONSABILIDADE DO BENEFICIÁRIO)</div>
            <div style="margin-top: 10px; line-height: 1.4;">
                <strong>INSTRUÇÕES DE PAGAMENTO:</strong><br>
                @if(!empty($boleto['pix_key']) && !empty($boleto['pix_payload']))
                    - PIX (copia e cola):<br>
                    <div style="margin: 6px 0; padding: 8px; border: 1px solid #000; background: #fff; font-family: 'Courier New', monospace; font-size: 9px; word-break: break-all;">
                        {{ $boleto['pix_payload'] }}
                    </div>
                    - Chave PIX do beneficiário: {{ $boleto['pix_key'] }}<br>
                @endif
                - Pagável em qualquer banco até o vencimento<br>
                - Após o vencimento, sujeito a juros de 1% ao mês e multa de 2%<br>
                - Referente à {{ $boleto['descricao'] ?? 'Venda #VND-2024-001 - Parcela 1/3' }}<br>
                @if(isset($boleto['observacoes']))
                - {{ $boleto['observacoes'] }}
                @endif
            </div>
        </div>
        
        <!-- Código de Barras -->
        <div class="codigo-barras">
            {{ $boleto['codigo_barras'] ?? '34191954700000149620000061207954560000014250' }}
        </div>
        
        <!-- Seção de Recibo -->
        <div class="recibo-section">
            <div class="recibo-header">
                RECIBO DO PAGADOR - COMPROVANTE DE PAGAMENTO
            </div>
            
            <div class="recibo-info">
                <div>
                    <div class="label">PAGADOR</div>
                    <div class="valor">{{ $boleto['cliente'] ?? 'Maria Silva Santos' }}</div>
                    <div>CPF: {{ $boleto['cpf'] ?? '123.456.789-00' }}</div>
                    
                    <div style="margin-top: 10px;">
                        <div class="label">BENEFICIÁRIO</div>
                        <div class="valor">ÓTICA VISÃO CLARA LTDA</div>
                        <div>CNPJ: 12.345.678/0001-90</div>
                    </div>
                </div>
                
                <div style="text-align: right;">
                    <div class="label">VENCIMENTO</div>
                    <div class="valor">{{ \Carbon\Carbon::parse($boleto['vencimento'] ?? '2024-08-30')->format('d/m/Y') }}</div>
                    
                    <div style="margin-top: 10px;">
                        <div class="label">VALOR PAGO</div>
                        <div class="valor-documento">R$ {{ number_format($boleto['valor_total'] ?? 149.62, 2, ',', '.') }}</div>
                    </div>
                    
                    <div style="margin-top: 15px;">
                        <div class="label">DATA PAGAMENTO</div>
                        <div style="border-bottom: 1px solid #000; width: 100px; margin-left: auto; height: 20px;"></div>
                    </div>
                </div>
            </div>
            
            <div class="autenticacao">
                Autenticação Mecânica: ________________________
            </div>
        </div>
    </div>
    
    <script>
        function downloadPDF() {
            alert('Download do PDF iniciado!\n\nEm produção, o arquivo seria gerado automaticamente.');
        }
        
        // Auto-print quando abrir em nova janela
        if (window.location.search.includes('print=1')) {
            window.onload = function() {
                setTimeout(function() {
                    window.print();
                }, 500);
            };
        }
    </script>
</body>
</html>
