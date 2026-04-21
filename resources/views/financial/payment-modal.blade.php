<!-- Modal de Registro de Pagamento -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="mdi mdi-cash me-2"></i>
                    Registrar Pagamento
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="paymentForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="mdi mdi-account me-2"></i>
                                        Dados do Cliente
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Cliente</label>
                                        <input type="text" class="form-control" id="paymentClient" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">CPF</label>
                                        <input type="text" class="form-control" id="paymentCpf" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Venda/Parcela</label>
                                        <input type="text" class="form-control" id="paymentSale" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="mdi mdi-currency-usd me-2"></i>
                                        Valores
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Valor Original</label>
                                        <div class="input-group">
                                            <span class="input-group-text">R$</span>
                                            <input type="text" class="form-control" id="paymentOriginalValue"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Juros/Multa</label>
                                        <div class="input-group">
                                            <span class="input-group-text">R$</span>
                                            <input type="text" class="form-control text-danger" id="paymentInterest"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Valor Total</strong></label>
                                        <div class="input-group">
                                            <span class="input-group-text">R$</span>
                                            <input type="text" class="form-control fw-bold text-success"
                                                id="paymentTotalValue" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Data do Pagamento *</label>
                                <input type="date" class="form-control" id="paymentDate" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Forma de Pagamento *</label>
                                <select class="form-select" id="paymentMethod" required
                                    onchange="updatePaymentFields()">
                                    <option value="">Selecione...</option>
                                    <option value="dinheiro">Dinheiro</option>
                                    <option value="pix">PIX</option>
                                    <option value="cartao_debito">Cartão de Débito</option>
                                    <option value="cartao_credito">Cartão de Crédito</option>
                                    <option value="transferencia">Transferência Bancária</option>
                                    <option value="boleto">Boleto Bancário</option>
                                    <option value="cheque">Cheque</option>
                                </select>
                            </div>

                            <div class="mb-3" id="bankField" style="display: none;">
                                <label class="form-label">Banco</label>
                                <select class="form-select" id="paymentBank">
                                    <option value="">Selecione o banco...</option>
                                    <option value="001">Banco do Brasil</option>
                                    <option value="104">Caixa Econômica</option>
                                    <option value="341">Itaú</option>
                                    <option value="033">Santander</option>
                                    <option value="237">Bradesco</option>
                                    <option value="260">Nu Pagamentos</option>
                                    <option value="323">Mercado Pago</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Valor Recebido *</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" class="form-control" id="paymentReceivedValue"
                                        step="0.01" required onchange="calculateChange()">
                                </div>
                            </div>

                            <div class="mb-3" id="discountField">
                                <label class="form-label">Desconto</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" class="form-control" id="paymentDiscount" step="0.01"
                                        value="0.00" onchange="calculateChange()">
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
                                <input type="text" class="form-control" id="paymentReference"
                                    placeholder="Número do comprovante, NSU, etc.">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Observações</label>
                        <textarea class="form-control" id="paymentNotes" rows="3" placeholder="Observações sobre o pagamento..."></textarea>
                    </div>

                    <!-- Resumo do Pagamento -->
                    <div class="alert alert-success" id="paymentSummary" style="display: none;">
                        <h6><i class="mdi mdi-check-circle me-2"></i>Resumo do Pagamento</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <small><strong>Valor da Parcela:</strong> R$ <span
                                        id="summaryOriginal">0,00</span></small><br>
                                <small><strong>Juros/Multa:</strong> R$ <span
                                        id="summaryInterest">0,00</span></small><br>
                                <small><strong>Desconto:</strong> R$ <span id="summaryDiscount">0,00</span></small>
                            </div>
                            <div class="col-md-6">
                                <small><strong>Total a Pagar:</strong> R$ <span
                                        id="summaryTotal">0,00</span></small><br>
                                <small><strong>Valor Recebido:</strong> R$ <span
                                        id="summaryReceived">0,00</span></small><br>
                                <small><strong>Troco:</strong> R$ <span id="summaryChange">0,00</span></small>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="mdi mdi-close-circle me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-warning" onclick="generateReceipt()">
                    <i class="mdi mdi-printer me-2"></i>Gerar Recibo
                </button>
                <button type="button" class="btn btn-success" onclick="confirmPayment()">
                    <i class="mdi mdi-check-circle me-2"></i>Confirmar Pagamento
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentPaymentData = {};

    function openPaymentModal(receivableId, clientName, cpf, saleId, originalValue, interest, totalValue) {
        currentPaymentData = {
            id: receivableId,
            client: clientName,
            cpf: cpf,
            sale: saleId,
            originalValue: parseFloat(originalValue),
            interest: parseFloat(interest),
            totalValue: parseFloat(totalValue)
        };

        document.getElementById('paymentClient').value = clientName;
        document.getElementById('paymentCpf').value = cpf;
        document.getElementById('paymentSale').value = saleId;
        document.getElementById('paymentOriginalValue').value = (currentPaymentData.originalValue || 0).toFixed(2)
            .replace('.', ',');
        document.getElementById('paymentInterest').value = (currentPaymentData.interest || 0).toFixed(2).replace('.',
            ',');
        document.getElementById('paymentTotalValue').value = (currentPaymentData.totalValue || 0).toFixed(2).replace(
            '.', ',');
        document.getElementById('paymentDate').value = new Date().toLocaleDateString('en-CA');
        document.getElementById('paymentReceivedValue').value = (currentPaymentData.totalValue || 0).toFixed(2);
        document.getElementById('paymentMethod').value = '';
        document.getElementById('paymentDiscount').value = '0.00';
        document.getElementById('paymentNotes').value = '';

        updatePaymentFields();
        calculateChange();

        const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
        modal.show();
    }

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

        updatePaymentSummary();
    }

    function updatePaymentSummary() {
        const originalValue = currentPaymentData.originalValue || 0;
        const interest = currentPaymentData.interest || 0;
        const discount = parseFloat(document.getElementById('paymentDiscount').value) || 0;
        const receivedValue = parseFloat(document.getElementById('paymentReceivedValue').value) || 0;
        const totalValue = currentPaymentData.totalValue || 0;
        const finalTotal = totalValue - discount;
        const change = Math.max(0, receivedValue - finalTotal);

        document.getElementById('summaryOriginal').textContent = originalValue.toFixed(2).replace('.', ',');
        document.getElementById('summaryInterest').textContent = interest.toFixed(2).replace('.', ',');
        document.getElementById('summaryDiscount').textContent = discount.toFixed(2).replace('.', ',');
        document.getElementById('summaryTotal').textContent = finalTotal.toFixed(2).replace('.', ',');
        document.getElementById('summaryReceived').textContent = receivedValue.toFixed(2).replace('.', ',');
        document.getElementById('summaryChange').textContent = change.toFixed(2).replace('.', ',');

        document.getElementById('paymentSummary').style.display = 'block';
    }

    function generateReceipt() {
        window.showAppModalMessage?.('Recibo gerado com sucesso!', 'Sucesso', 'success');
    }

    function confirmPayment() {
        const form = document.getElementById('paymentForm');

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const paymentData = {
            receivable_id: currentPaymentData.id,
            date: document.getElementById('paymentDate').value,
            method: document.getElementById('paymentMethod').value,
            bank: document.getElementById('paymentBank').value,
            reference: document.getElementById('paymentReference').value,
            original_value: currentPaymentData.originalValue,
            interest: currentPaymentData.interest,
            discount: parseFloat(document.getElementById('paymentDiscount').value) || 0,
            received_value: parseFloat(document.getElementById('paymentReceivedValue').value) || 0,
            notes: document.getElementById('paymentNotes').value
        };

        if (confirm('Confirmar o registro deste pagamento?')) {
            fetch('{{ route('financial.receive-payment') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(paymentData)
                })
                .then(async (res) => {
                    const data = await res.json().catch(() => ({}));
                    if (!res.ok) {
                        throw new Error(data.message || 'Erro ao registrar pagamento.');
                    }
                    return data;
                })
                .then(() => {
                    window.showAppModalMessage?.('Pagamento registrado com sucesso!', 'Sucesso', 'success');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
                    modal.hide();
                    location.reload();
                })
                .catch((err) => {
                    window.showAppModalMessage?.(err?.message || 'Erro ao registrar pagamento.', 'Erro', 'danger');
                });
        }
    }
</script>
