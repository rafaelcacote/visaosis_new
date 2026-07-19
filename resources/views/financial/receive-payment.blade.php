@extends('layouts.app')

@section('title', 'Dar Baixa na Parcela - Connect Plus')

@section('content')
    <div class="d-xl-flex justify-content-between align-items-start mb-4">
        <div>
            <h2 class="text-dark font-weight-bold mb-2">
                <i class="mdi mdi-cash-check me-2"></i>
                Dar Baixa na Parcela
            </h2>
            <p class="text-muted mb-0">Registrar pagamento de forma detalhada em tela dedicada</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ $receivable['return_url'] }}" class="btn btn-outline-secondary">
                <i class="mdi mdi-arrow-left me-2"></i>Voltar
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('financial.receive-payment') }}" method="POST" id="paymentForm">
        @csrf
        <input type="hidden" name="receivable_id" value="{{ $receivable['id'] }}">
        <input type="hidden" name="return_url" value="{{ $receivable['return_url'] }}">

        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="mdi mdi-account me-2"></i>
                            Dados do Cliente
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Cliente</label>
                            <input type="text" class="form-control" value="{{ $receivable['cliente'] }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">CPF</label>
                            <input type="text" class="form-control" value="{{ $receivable['cpf'] ?: 'Não informado' }}"
                                readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Venda</label>
                            <input type="text" class="form-control" value="{{ $receivable['venda_id'] }}" readonly>
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Parcela</label>
                            <input type="text" class="form-control" value="{{ $receivable['parcela'] }}" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="mdi mdi-currency-usd me-2"></i>
                            Valores
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Valor Original</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="text" class="form-control"
                                    value="{{ number_format($receivable['valor_parcela'], 2, ',', '.') }}" readonly>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Juros/Multa</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="text" class="form-control text-danger"
                                    value="{{ number_format($receivable['juros'], 2, ',', '.') }}" readonly>
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label"><strong>Valor Total</strong></label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="text" class="form-control fw-bold text-success"
                                    value="{{ number_format($receivable['valor_atualizado'], 2, ',', '.') }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">Data do Pagamento *</label>
                            <input type="date" class="form-control" id="paymentDate" name="date"
                                value="{{ $receivable['data_pagamento'] }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Forma de Pagamento *</label>
                            <select class="form-select" id="paymentMethod" name="method" required onchange="updatePaymentFields()">
                                <option value="">Selecione...</option>
                                <option value="dinheiro" @selected($receivable['forma_pagamento'] === 'dinheiro')>Dinheiro</option>
                                <option value="pix" @selected($receivable['forma_pagamento'] === 'pix')>PIX</option>
                                <option value="cartao_debito" @selected($receivable['forma_pagamento'] === 'cartao_debito')>Cartão de Débito</option>
                                <option value="cartao_credito" @selected($receivable['forma_pagamento'] === 'cartao_credito')>Cartão de Crédito</option>
                                <option value="transferencia" @selected($receivable['forma_pagamento'] === 'transferencia')>Transferência Bancária</option>
                                <option value="boleto" @selected($receivable['forma_pagamento'] === 'boleto')>Boleto Bancário</option>
                                <option value="cheque" @selected($receivable['forma_pagamento'] === 'cheque')>Cheque</option>
                            </select>
                        </div>

                        <div class="mb-3" id="bankField" style="display: none;">
                            <label class="form-label">Banco</label>
                            <select class="form-select" id="paymentBank" name="bank">
                                <option value="">Selecione o banco...</option>
                                <option value="001" @selected($receivable['banco'] === '001')>Banco do Brasil</option>
                                <option value="104" @selected($receivable['banco'] === '104')>Caixa Econômica</option>
                                <option value="341" @selected($receivable['banco'] === '341')>Itaú</option>
                                <option value="033" @selected($receivable['banco'] === '033')>Santander</option>
                                <option value="237" @selected($receivable['banco'] === '237')>Bradesco</option>
                                <option value="260" @selected($receivable['banco'] === '260')>Nu Pagamentos</option>
                                <option value="323" @selected($receivable['banco'] === '323')>Mercado Pago</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">Valor Recebido *</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" class="form-control" id="paymentReceivedValue" name="received_value"
                                    step="0.01" value="{{ $receivable['valor_recebido'] }}" required
                                    onchange="calculateChange()">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Desconto</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" class="form-control" id="paymentDiscount" name="discount"
                                    step="0.01" value="{{ $receivable['desconto'] }}" onchange="calculateChange()">
                            </div>
                        </div>

                        <div class="mb-3" id="changeField">
                            <label class="form-label">Troco</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="text" class="form-control" id="paymentChange" readonly>
                            </div>
                        </div>

                        <div class="mb-3" id="referenceField" style="display: none;">
                            <label class="form-label">Referência/Comprovante</label>
                            <input type="text" class="form-control" id="paymentReference" name="reference"
                                value="{{ $receivable['referencia'] }}" placeholder="Número do comprovante, NSU, etc.">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Observações</label>
                    <textarea class="form-control" id="paymentNotes" name="notes" rows="3"
                        placeholder="Observações sobre o pagamento...">{{ $receivable['observacoes'] }}</textarea>
                </div>

                <div class="alert alert-success mb-0" id="paymentSummary">
                    <h6><i class="mdi mdi-check-circle me-2"></i>Resumo do Pagamento</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <small><strong>Valor da Parcela:</strong> R$ <span
                                    id="summaryOriginal">{{ number_format($receivable['valor_parcela'], 2, ',', '.') }}</span></small><br>
                            <small><strong>Juros/Multa:</strong> R$ <span
                                    id="summaryInterest">{{ number_format($receivable['juros'], 2, ',', '.') }}</span></small><br>
                            <small><strong>Desconto:</strong> R$ <span id="summaryDiscount">0,00</span></small>
                        </div>
                        <div class="col-md-6">
                            <small><strong>Total a Pagar:</strong> R$ <span id="summaryTotal">0,00</span></small><br>
                            <small><strong>Valor Recebido:</strong> R$ <span id="summaryReceived">0,00</span></small><br>
                            <small><strong>Troco:</strong> R$ <span id="summaryChange">0,00</span></small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-between">
                <a href="{{ $receivable['return_url'] }}" class="btn btn-outline-secondary">
                    <i class="mdi mdi-close-circle me-2"></i>Cancelar
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="mdi mdi-check-circle me-2"></i>Confirmar Pagamento
                </button>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        const currentPaymentData = {
            originalValue: {{ json_encode((float) $receivable['valor_parcela']) }},
            interest: {{ json_encode((float) $receivable['juros']) }},
            totalValue: {{ json_encode((float) $receivable['valor_atualizado']) }}
        };

        function updatePaymentFields() {
            const method = document.getElementById('paymentMethod').value;
            const bankField = document.getElementById('bankField');
            const referenceField = document.getElementById('referenceField');
            const changeField = document.getElementById('changeField');

            if (method === 'pix' || method === 'transferencia' || method === 'boleto') {
                bankField.style.display = 'block';
                referenceField.style.display = 'block';
            } else {
                bankField.style.display = 'none';
                referenceField.style.display = method === 'cartao_debito' || method === 'cartao_credito' ? 'block' : 'none';
            }

            if (method === 'dinheiro') {
                changeField.style.display = 'block';
            } else {
                changeField.style.display = 'none';
                document.getElementById('paymentChange').value = '0,00';
            }

            calculateChange();
        }

        function calculateChange() {
            const totalValue = currentPaymentData.totalValue || 0;
            const receivedValue = parseFloat(document.getElementById('paymentReceivedValue').value) || 0;
            const discount = parseFloat(document.getElementById('paymentDiscount').value) || 0;
            const finalTotal = totalValue - discount;
            const change = receivedValue - finalTotal;

            document.getElementById('paymentChange').value = Math.max(0, change).toFixed(2).replace('.', ',');

            document.getElementById('summaryDiscount').textContent = discount.toFixed(2).replace('.', ',');
            document.getElementById('summaryTotal').textContent = finalTotal.toFixed(2).replace('.', ',');
            document.getElementById('summaryReceived').textContent = receivedValue.toFixed(2).replace('.', ',');
            document.getElementById('summaryChange').textContent = Math.max(0, change).toFixed(2).replace('.', ',');
        }

        document.addEventListener('DOMContentLoaded', function() {
            updatePaymentFields();
            calculateChange();
        });
    </script>
@endpush
