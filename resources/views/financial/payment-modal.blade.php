<!-- Modal de Registro de Pagamento -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
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
                    <div class="row g-2">
                        <div class="col-lg-6">
                            <div class="card bg-light mb-2">
                                <div class="card-header py-2">
                                    <h6 class="mb-0">
                                        <i class="mdi mdi-account me-2"></i>
                                        Dados do Cliente
                                    </h6>
                                </div>
                                <div class="card-body py-3">
                                    <div class="row g-2 mb-2">
                                        <div class="col-md-8">
                                            <label class="form-label mb-1">Cliente</label>
                                            <input type="text" class="form-control form-control-sm" id="paymentClient" readonly>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label mb-1">CPF</label>
                                            <input type="text" class="form-control form-control-sm" id="paymentCpf" readonly>
                                        </div>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-md-4">
                                            <label class="form-label mb-1">Venda</label>
                                            <input type="text" class="form-control form-control-sm" id="paymentSale" readonly>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label mb-1">Parcela</label>
                                            <input type="text" class="form-control form-control-sm" id="paymentInstallment" readonly>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label mb-1">Status</label>
                                            <input type="text" class="form-control form-control-sm" id="paymentStatus" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card bg-light">
                                <div class="card-header py-2">
                                    <h6 class="mb-0">
                                        <i class="mdi mdi-currency-usd me-2"></i>
                                        Valores
                                    </h6>
                                </div>
                                <div class="card-body py-3">
                                    <div class="row g-2">
                                        <div class="col-md-4">
                                            <label class="form-label mb-1">Original</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">R$</span>
                                                <input type="text" class="form-control" id="paymentOriginalValue" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label mb-1">Juros</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">R$</span>
                                                <input type="text" class="form-control text-danger" id="paymentInterest" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label mb-1"><strong>Total</strong></label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">R$</span>
                                                <input type="text" class="form-control fw-bold text-success" id="paymentTotalValue" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="row g-2">
                                <div class="col-12">
                                    <div class="card bg-light">
                                        <div class="card-header py-2">
                                            <h6 class="mb-0">
                                                <i class="mdi mdi-calendar-check me-2"></i>
                                                Dados do Pagamento
                                            </h6>
                                        </div>
                                        <div class="card-body py-3">
                                            <div class="mb-2">
                                                <label class="form-label mb-1">Data *</label>
                                                <input type="date" class="form-control form-control-sm" id="paymentDate" required>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label mb-1">Forma *</label>
                                                <select class="form-select form-select-sm" id="paymentMethod" required onchange="updatePaymentFields()">
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
                                            <div class="mb-2" id="bankField" style="display: none;">
                                                <label class="form-label mb-1">Banco</label>
                                                <select class="form-select form-select-sm" id="paymentBank">
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
                                            <div id="referenceField" style="display: none;">
                                                <label class="form-label mb-1">Referência/Comprovante</label>
                                                <input type="text" class="form-control form-control-sm" id="paymentReference" placeholder="Número do comprovante, NSU, etc.">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="card bg-light">
                                        <div class="card-header py-2">
                                            <h6 class="mb-0">
                                                <i class="mdi mdi-cash-multiple me-2"></i>
                                                Valores do Recebimento
                                            </h6>
                                        </div>
                                        <div class="card-body py-3">
                                            <div class="row g-2">
                                                <div class="col-md-4">
                                                    <label class="form-label mb-1">Valor Recebido *</label>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text">R$</span>
                                                        <input type="number" class="form-control" id="paymentReceivedValue" step="0.01" required onchange="calculateChange()">
                                                    </div>
                                                </div>
                                                <div class="col-md-4" id="discountField">
                                                    <label class="form-label mb-1">Desconto</label>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text">R$</span>
                                                        <input type="number" class="form-control" id="paymentDiscount" step="0.01" value="0.00" onchange="calculateChange()">
                                                    </div>
                                                </div>
                                                <div class="col-md-4" id="changeField">
                                                    <label class="form-label mb-1">Troco</label>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text">R$</span>
                                                        <input type="text" class="form-control" id="paymentChange" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="form-label mb-1">Observações</label>
                        <textarea class="form-control form-control-sm" id="paymentNotes" rows="" placeholder="Observações sobre o pagamento..."></textarea>
                    </div>

                    <!-- Resumo do Pagamento -->
                    <div class="alert alert-success" id="paymentSummary" style="display: none;">
                        <h6><i class="mdi mdi-check-circle me-2"></i>Resumo do Pagamento</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <small><strong>Valor da Parcela:</strong> R$ <span id="summaryOriginal">0,00</span></small><br>
                                <small><strong>Juros/Multa:</strong> R$ <span id="summaryInterest">0,00</span></small><br>
                                <small><strong>Desconto:</strong> R$ <span id="summaryDiscount">0,00</span></small>
                            </div>
                            <div class="col-md-6">
                                <small><strong>Total a Pagar:</strong> R$ <span id="summaryTotal">0,00</span></small><br>
                                <small><strong>Valor Recebido:</strong> R$ <span id="summaryReceived">0,00</span></small><br>
                                <small><strong>Troco:</strong> R$ <span id="summaryChange">0,00</span></small>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
                    <i class="mdi mdi-close-circle me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-sm btn-warning" onclick="generateReceipt()">
                    <i class="mdi mdi-printer me-2"></i>Gerar Recibo
                </button>
                <button type="button" class="btn btn-sm btn-success" onclick="confirmPayment()">
                    <i class="mdi mdi-check-circle me-2"></i>Confirmar Pagamento
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentPaymentData = {};

function openPaymentModal(receivableId, clientName, cpf, saleId, originalValue, interest, totalValue, installment = '', dueDate = '', currentStatus = '') {
    currentPaymentData = {
        id: receivableId,
        client: clientName,
        cpf: cpf,
        sale: saleId,
        installment: installment,
        dueDate: dueDate,
        status: currentStatus,
        originalValue: parseFloat(originalValue),
        interest: parseFloat(interest),
        totalValue: parseFloat(totalValue)
    };

    document.getElementById('paymentClient').value = clientName;
    document.getElementById('paymentCpf').value = cpf;
    document.getElementById('paymentSale').value = saleId || '-';
    document.getElementById('paymentInstallment').value = installment || '-';
    document.getElementById('paymentStatus').value = currentStatus || '-';
    document.getElementById('paymentOriginalValue').value = originalValue;
    document.getElementById('paymentInterest').value = interest;
    document.getElementById('paymentTotalValue').value = totalValue;
    document.getElementById('paymentDate').value = new Date().toISOString().split('T')[0];
    document.getElementById('paymentReceivedValue').value = totalValue;
    document.getElementById('paymentMethod').value = '';
    document.getElementById('paymentDiscount').value = '0.00';
    document.getElementById('paymentNotes').value = '';

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
    alert('Recibo gerado com sucesso!\n\nEm produção, seria aberta uma nova janela com o recibo formatado para impressão.');
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

    const confirmBtn = document.querySelector('#paymentModal .btn.btn-success');
    const originalText = confirmBtn ? confirmBtn.innerHTML : '';
    if (confirmBtn) {
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<i class="mdi mdi-loading mdi-spin me-2"></i>Processando...';
    }

    fetch('{{ route('financial.receive-payment') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            receivable_id: paymentData.receivable_id,
            valor: paymentData.received_value
        })
    })
        .then(async (response) => {
            const payload = await response.json();
            if (!response.ok) {
                throw new Error(payload.message || 'Erro ao registrar pagamento.');
            }
            return payload;
        })
        .then((payload) => {
            const successMessage = payload.message || 'Pagamento registrado com sucesso!';
            document.getElementById('paymentSummary').classList.remove('alert-success');
            document.getElementById('paymentSummary').classList.add('alert-info');
            document.getElementById('paymentSummary').style.display = 'block';
            document.getElementById('paymentSummary').innerHTML = `<h6 class="mb-0"><i class="mdi mdi-check-circle me-2"></i>${successMessage}</h6>`;

            const modal = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
            setTimeout(() => {
                if (modal) {
                    modal.hide();
                }
                location.reload();
            }, 800);
        })
        .catch((error) => {
            document.getElementById('paymentSummary').classList.remove('alert-success', 'alert-info');
            document.getElementById('paymentSummary').classList.add('alert-danger');
            document.getElementById('paymentSummary').style.display = 'block';
            document.getElementById('paymentSummary').innerHTML = `<h6 class="mb-0"><i class="mdi mdi-alert-circle me-2"></i>${error.message || 'Erro ao registrar pagamento.'}</h6>`;
        })
        .finally(() => {
            if (confirmBtn) {
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = originalText;
            }
        });
}
</script>
