@extends('layouts.app')

@section('title', 'Detalhes da Venda - VisaoSis')

@section('content')
    @php
        $paidStatusesGlobal = ['pago', 'paga', 'cancelado', 'cancelada'];
        $parcelasNaoPagasGlobal = collect($sale['parcelas_detalhes'] ?? [])->filter(function ($parcela) use (
            $paidStatusesGlobal,
        ) {
            $statusRaw = strtolower((string) ($parcela['status'] ?? ''));
            $isPaga = !empty($parcela['pago_em']) || in_array($statusRaw, $paidStatusesGlobal, true);
            return !$isPaga;
        });
        $totalNaoPagasGlobal = (float) $parcelasNaoPagasGlobal->sum(function ($p) {
            return (float) ($p['valor_parcela'] ?? ($p['valor_atualizado'] ?? 0));
        });
        $temParcelasAbertasGlobal = $parcelasNaoPagasGlobal->count() > 0;
    @endphp
    <div class="page-show">
        <div class="d-xl-flex justify-content-between align-items-start mb-4">
            <div>
                <h2 class="text-dark font-weight-bold mb-2">
                    <i class="mdi mdi-receipt me-2"></i>
                    Venda {{ $sale['numero'] }}
                </h2>
                <p class="text-muted mb-0">Detalhes da venda #{{ $sale['id'] }}</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                @php
                    $waPhone = $sale['cliente']['telefone'] ?? null;
                    $waDigits = preg_replace('/\D+/', '', (string) $waPhone);
                    $waEnabled = !empty($waDigits);
                @endphp
                <button type="button" class="btn btn-outline-success"
                    @if (!$waEnabled) disabled title="Cliente sem telefone cadastrado" @endif
                    onclick="sendVendaWhatsapp()">
                    <i class="mdi mdi-whatsapp me-2"></i>
                    Enviar Whatsapp
                </button>
                <a href="{{ route('sales.print', $sale['id']) }}" class="btn btn-outline-primary" target="_blank">
                    <i class="mdi mdi-printer me-2"></i>
                    Imprimir PDF
                </a>
                @if (request()->boolean('from_history') && request()->filled('return_url'))
                    <a href="{{ request('return_url') }}" class="btn btn-outline-info">
                        <i class="mdi mdi-history me-2"></i>
                        Voltar Historico
                    </a>
                @endif
                <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">
                    <i class="mdi mdi-arrow-left me-2"></i>
                    Voltar
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Informações da Venda -->
            <div class="col-lg-12">
                <!-- Cabeçalho da Venda -->
                <div class="col-lg-12">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="mdi mdi-information-outline text-primary me-2"></i>
                                Informações da Venda
                            </h5>
                            <div>
                                @switch($sale['status'])
                                    @case('finalizada')
                                        <span class="badge badge-success">
                                            <i class="mdi mdi-check-circle me-1"></i>
                                            Finalizada
                                        </span>
                                    @break

                                    @case('pendente')
                                        <span class="badge badge-warning">
                                            <i class="mdi mdi-clock-outline me-1"></i>
                                            Pendente
                                        </span>
                                    @break

                                    @case('cancelada')
                                        <span class="badge badge-danger">
                                            <i class="mdi mdi-close-circle me-1"></i>
                                            Cancelada
                                        </span>
                                    @break

                                    @default
                                        <span class="badge badge-secondary">
                                            {{ ucfirst($sale['status']) }}
                                        </span>
                                @endswitch
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">
                                    <i class="mdi mdi-calendar me-1"></i>
                                    Data da Venda
                                </label>
                                <div class="fw-medium">{{ $sale['data_formatada'] }}</div>
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::parse($sale['data'])->diffForHumans() }}
                                </small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Subtotal:</span>
                                    <span class="fw-medium">R$ {{ number_format($sale['subtotal'], 2, ',', '.') }}</span>
                                </div>
                                @if ($sale['desconto'] > 0)
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Desconto:</span>
                                        <span class="text-danger fw-medium">
                                            - R$ {{ number_format($sale['desconto'], 2, ',', '.') }}
                                        </span>
                                    </div>
                                @endif
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <strong class="fs-5">Total:</strong>
                                    <strong class="fs-4 text-success">
                                        R$ {{ number_format($sale['total'], 2, ',', '.') }}
                                    </strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Informações do Cliente -->
                <div class="col-lg-12">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="mdi mdi-account-circle text-primary me-2"></i>
                            Dados do Cliente
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($sale['cliente'])
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Nome</label>
                                    <div class="fw-medium">{{ $sale['cliente']['nome'] }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">CPF</label>
                                    <div>{{ $sale['cliente']['cpf'] ?? 'Não informado' }}</div>
                                </div>
                                @if ($sale['cliente']['telefone'])
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted">
                                            <i class="mdi mdi-phone me-1"></i>
                                            Telefone
                                        </label>
                                        <div>{{ $sale['cliente']['telefone'] }}</div>
                                    </div>
                                @endif
                                @if ($sale['cliente']['email'])
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted">
                                            <i class="mdi mdi-email me-1"></i>
                                            E-mail
                                        </label>
                                        <div>{{ $sale['cliente']['email'] }}</div>
                                    </div>
                                @endif
                                @if ($sale['cliente']['endereco'])
                                    <div class="col-12 mb-3">
                                        <label class="form-label text-muted">
                                            <i class="mdi mdi-map-marker me-1"></i>
                                            Endereço
                                        </label>
                                        <div>{{ $sale['cliente']['endereco'] }}</div>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="mdi mdi-account-remove" style="font-size: 3rem;"></i>
                                <p class="mt-2 mb-0">Cliente não informado</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Produtos Comprados -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="mdi mdi-cart-check text-success me-2"></i>
                            Produtos Comprados
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Produto</th>
                                        <th class="text-center">Quantidade</th>
                                        <th class="text-end">Preço Unitário</th>
                                        <th class="text-end">Desconto</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($sale['produtos'] as $produto)
                                        <tr>
                                            <td>
                                                <div class="fw-medium">{{ $produto['nome'] }}</div>
                                                <small class="text-muted">ID: {{ $produto['id'] }}</small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-info">{{ $produto['quantidade'] }}</span>
                                            </td>
                                            <td class="text-end">
                                                R$ {{ number_format($produto['preco_unitario'], 2, ',', '.') }}
                                            </td>
                                            <td class="text-end">
                                                @if ($produto['desconto'] > 0)
                                                    <span class="text-danger">
                                                        - R$ {{ number_format($produto['desconto'], 2, ',', '.') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <strong class="text-success">
                                                    R$ {{ number_format($produto['subtotal'], 2, ',', '.') }}
                                                </strong>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">
                                                <i class="mdi mdi-inbox" style="font-size: 2rem;"></i>
                                                <p class="mt-2 mb-0">Nenhum produto encontrado</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Parcelas da Venda -->
                @if (collect($sale['parcelas_detalhes'] ?? [])->count() > 0)
                    @php
                        $paidStatuses = ['pago', 'paga', 'cancelado', 'cancelada'];
                        $parcelasNaoPagas = collect($sale['parcelas_detalhes'])->filter(function ($parcela) use (
                            $paidStatuses,
                        ) {
                            $statusRaw = strtolower((string) ($parcela['status'] ?? ''));
                            $isPaga = !empty($parcela['pago_em']) || in_array($statusRaw, $paidStatuses, true);
                            return !$isPaga;
                        });
                        $totalNaoPagas = (float) $parcelasNaoPagas->sum(
                            fn($p) => (float) ($p['valor_atualizado'] ?? 0),
                        );
                        $temParcelasAbertas = $parcelasNaoPagas->count() > 0;
                    @endphp
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <h5 class="card-title mb-0">
                                    <i class="mdi mdi-calendar-multiple-check text-primary me-2"></i>
                                    Parcelas da Venda
                                </h5>
                            </div>
                            <div class="d-flex gap-2 align-items-center">
                                <span class="badge bg-secondary">
                                    {{ collect($sale['parcelas_detalhes'])->count() }} parcela(s)
                                </span>
                                @if ($temParcelasAbertas)
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        onclick="openRefazerPagamentoModal()"
                                        title="Refazer forma de pagamento das parcelas em aberto">
                                        <i class="mdi mdi-credit-card-refresh-outline me-1"></i>
                                        Refazer Pagamento
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Parcela</th>
                                            <th>Vencimento</th>
                                            <th>Valor</th>
                                            <th>Valor Recebido</th>
                                            <th>Status</th>
                                            <th>Pago em</th>
                                            <th width="100" class="text-center">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($sale['parcelas_detalhes'] as $parcela)
                                            @php
                                                $statusRaw = strtolower((string) ($parcela['status'] ?? ''));
                                                $isCancelada = in_array($statusRaw, ['cancelada', 'cancelado'], true);
                                                $isPaga =
                                                    !$isCancelada &&
                                                    ($parcela['status'] === 'paga' || !empty($parcela['pago_em']));
                                                $isPagamentoParcial = $statusRaw === 'pagamento_parcial';
                                                $isSaldoRemanescente = $statusRaw === 'saldo_remanescente';
                                                $podeEditar = !$isPaga && !$isCancelada;
                                            @endphp
                                            <tr
                                                class="@if ($parcela['status'] === 'vencida') table-danger @elseif($parcela['status'] === 'vence_hoje') table-warning @elseif($parcela['status'] === 'vence_semana') table-info @elseif($isCancelada) table-secondary @elseif($isPaga) table-success @elseif($isPagamentoParcial || $isSaldoRemanescente) table-light @endif">
                                                <td>
                                                    <span class="badge bg-secondary">{{ $parcela['parcela'] }}</span>
                                                    @if (!empty($parcela['forma_pagamento']))
                                                        <br><small
                                                            class="text-muted">{{ $parcela['forma_pagamento'] }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (!empty($parcela['vencimento']))
                                                        <span
                                                            class="@if ($parcela['status'] === 'vencida') text-danger @elseif($parcela['status'] === 'vence_hoje') text-warning @elseif($parcela['status'] === 'vence_semana') text-info @else text-success @endif">
                                                            {{ \Carbon\Carbon::parse($parcela['vencimento'])->format('d/m/Y') }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">Nao informado</span>
                                                    @endif

                                                    @if (($parcela['dias_atraso'] ?? 0) > 0)
                                                        <br><small class="text-danger">{{ $parcela['dias_atraso'] }} dias
                                                            atraso</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (($parcela['juros'] ?? 0) > 0)
                                                        <span class="text-decoration-line-through text-muted">
                                                            R$ {{ number_format($parcela['valor_parcela'], 2, ',', '.') }}
                                                        </span>
                                                        <br>
                                                        <strong class="text-danger">
                                                            R$
                                                            {{ number_format($parcela['valor_atualizado'], 2, ',', '.') }}
                                                        </strong>
                                                        <br><small class="text-danger">
                                                            +R$ {{ number_format($parcela['juros'], 2, ',', '.') }} juros
                                                        </small>
                                                    @else
                                                        @if (($parcela['valor_recebido'] ?? 0) > 0)
                                                            <span class="text-decoration-line-through text-muted">
                                                                R$
                                                                {{ number_format($parcela['valor_atualizado'], 2, ',', '.') }}
                                                            </span>
                                                        @else
                                                            <strong>R$
                                                                {{ number_format($parcela['valor_atualizado'], 2, ',', '.') }}</strong>
                                                        @endif
                                                    @endif
                                                </td>
                                                <td>

                                                    <strong>R$
                                                        R$ {{ number_format($parcela['valor_recebido'], 2, ',', '.') }}
                                                    </strong>
                                                </td>
                                                <td>
                                                    @if ($isPagamentoParcial)
                                                        <span class="badge bg-warning text-dark">
                                                            <i class="mdi mdi-cash-multiple me-1"></i>Pagamento Parcial
                                                        </span>
                                                    @elseif($isSaldoRemanescente)
                                                        <span class="badge bg-secondary">
                                                            <i class="mdi mdi-receipt-text me-1"></i>Saldo Remanescente
                                                        </span>
                                                    @elseif ($parcela['status'] === 'vencida')
                                                        <span class="badge bg-danger">
                                                            <i class="mdi mdi-alert-circle me-1"></i>Vencida
                                                        </span>
                                                    @elseif($parcela['status'] === 'vence_hoje')
                                                        <span class="badge bg-warning">
                                                            <i class="mdi mdi-calendar-clock me-1"></i>Vence Hoje
                                                        </span>
                                                    @elseif($parcela['status'] === 'vence_semana')
                                                        <span class="badge bg-info">
                                                            <i class="mdi mdi-calendar-week me-1"></i>Vence na Semana
                                                        </span>
                                                    @elseif($isCancelada)
                                                        <span class="badge bg-secondary">
                                                            <i class="mdi mdi-close-circle me-1"></i>Cancelada
                                                        </span>
                                                    @elseif($isPaga)
                                                        <span class="badge bg-success">
                                                            <i class="mdi mdi-check-circle me-1"></i>Paga
                                                        </span>
                                                    @else
                                                        <span class="badge bg-success">
                                                            <i class="mdi mdi-check-circle me-1"></i>Em Dia
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (!empty($parcela['pago_em']))
                                                        <span class="text-success">
                                                            {{ $parcela['pago_em']->format('d/m/Y H:i') }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">Pendente</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if ($podeEditar)
                                                        <button type="button" class="btn btn-sm btn-outline-warning"
                                                            title="Editar parcela"
                                                            onclick="openEditParcelaModal({{ $parcela['id'] }})">
                                                            <i class="mdi mdi-pencil"></i>
                                                        </button>
                                                    @elseif($isPaga || $isCancelada)
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            title="Reabrir parcela (estornar pagamento ou cancelamento)"
                                                            onclick="openReopenParcelaModal({{ $parcela['id'] }}, '{{ $parcela['parcela'] }}')">
                                                            <i class="mdi mdi-lock-open-outline"></i>
                                                        </button>
                                                    @else
                                                        <span class="text-muted" title="Ação indisponível">
                                                            <i class="mdi mdi-lock-outline"></i>
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="editParcelaModal" tabindex="-1" aria-labelledby="editParcelaModalLabel"
                        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <form id="editParcelaForm">
                                    <div class="modal-header bg-warning text-white py-2 px-3">
                                        <h6 class="modal-title" id="editParcelaModalLabel">
                                            <i class="mdi mdi-pencil me-1"></i>
                                            Editar Parcela
                                            <span id="editParcelaLabel" class="ms-1"></span>
                                        </h6>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                            aria-label="Fechar"></button>
                                    </div>
                                    <div class="modal-body p-3">
                                        <input type="hidden" id="edit_parcela_id" name="edit_parcela_id"
                                            value="">

                                        <div class="mb-3">
                                            <label for="edit_vencimento" class="form-label small fw-bold">Novo Vencimento
                                                <span class="text-danger">*</span></label>
                                            <input type="date" id="edit_vencimento" name="vencimento_em"
                                                class="form-control" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="edit_valor" class="form-label small fw-bold">Novo Valor (R$) <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" id="edit_valor" name="valor" step="0.01"
                                                min="0" class="form-control" required>
                                            <small class="text-muted">Valor principal da parcela (sem juros/multa).</small>
                                        </div>

                                        <div class="mb-3">
                                            <label for="edit_forma_pagamento" class="form-label small fw-bold">Forma de
                                                Pagamento</label>
                                            <input type="text" id="edit_forma_pagamento" name="forma_pagamento"
                                                class="form-control" placeholder="Ex: Dinheiro, Cartão, Pix..."
                                                maxlength="60">
                                        </div>

                                        <div class="mb-0">
                                            <label for="edit_observacoes"
                                                class="form-label small fw-bold">Observações</label>
                                            <textarea id="edit_observacoes" name="observacoes" class="form-control" rows="3" maxlength="1000"
                                                placeholder="Motivo da alteração, observações internas..."></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer py-2 px-3">
                                        <button type="button" class="btn btn-outline-secondary btn-sm"
                                            data-bs-dismiss="modal">
                                            <i class="mdi mdi-close me-1"></i>Cancelar
                                        </button>
                                        <button type="submit" id="btnSaveEditParcela"
                                            class="btn btn-warning text-dark btn-sm">
                                            <i class="mdi mdi-content-save me-1"></i>Salvar Alterações
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="reopenParcelaModal" tabindex="-1"
                        aria-labelledby="reopenParcelaModalLabel" aria-hidden="true" data-bs-backdrop="static"
                        data-bs-keyboard="false">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <form id="reopenParcelaForm">
                                    <div class="modal-header bg-danger text-white py-2 px-3">
                                        <h6 class="modal-title" id="reopenParcelaModalLabel">
                                            <i class="mdi mdi-lock-open-outline me-1"></i>
                                            Reabrir Parcela
                                        </h6>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                            aria-label="Fechar"></button>
                                    </div>
                                    <div class="modal-body p-3">
                                        <input type="hidden" id="reopen_parcela_id" name="reopen_parcela_id"
                                            value="">

                                        <div class="alert alert-danger py-2 px-3 mb-3 small">
                                            <i class="mdi mdi-alert-outline me-1"></i>
                                            <strong>Atenção:</strong> esta ação irá
                                            <strong>zerar os dados de pagamento</strong> (data, valor recebido, desconto)
                                            e redefinir o status da parcela com base no vencimento atual.
                                            <br>
                                            Se houver parcela de <em>saldo remanescente</em> vinculada ao mesmo número
                                            desta,
                                            ela também será reaberta automaticamente.
                                        </div>

                                        <div class="mb-3">
                                            <label for="reopenParcelaInfo"
                                                class="form-label small fw-bold">Parcela</label>
                                            <input type="text" id="reopenParcelaInfo"
                                                class="form-control-plaintext fw-bold" readonly value="">
                                        </div>

                                        <div class="mb-0">
                                            <label for="reopen_motivo" class="form-label small fw-bold">Motivo
                                                (opcional)</label>
                                            <textarea id="reopen_motivo" name="motivo" rows="3" maxlength="1000" class="form-control"
                                                placeholder="Ex.: Pagamento registrado na parcela errada, cancelamento realizado indevidamente..."></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer py-2 px-3">
                                        <button type="button" class="btn btn-outline-secondary btn-sm"
                                            data-bs-dismiss="modal">
                                            <i class="mdi mdi-close me-1"></i>Cancelar
                                        </button>
                                        <button type="submit" id="btnConfirmReopen" class="btn btn-danger btn-sm">
                                            <i class="mdi mdi-lock-open-outline me-1"></i>Confirmar Reabertura
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    @if (!empty($temParcelasAbertasGlobal))
                        <div class="modal fade" id="refazerPagamentoModal" tabindex="-1"
                            aria-labelledby="refazerPagamentoModalLabel" aria-hidden="true" data-bs-backdrop="static"
                            data-bs-keyboard="false">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content">
                                    <form id="refazerPagamentoForm">
                                        <div class="modal-header bg-primary text-white py-2 px-3">
                                            <h6 class="modal-title" id="refazerPagamentoModalLabel">
                                                <i class="mdi mdi-credit-card-refresh-outline me-1"></i>
                                                Refazer Forma de Pagamento
                                            </h6>
                                            <button type="button" class="btn-close btn-close-white"
                                                data-bs-dismiss="modal" aria-label="Fechar"></button>
                                        </div>
                                        <div class="modal-body p-3">
                                            <div class="alert alert-primary py-2 px-3 mb-3 small">
                                                <i class="mdi mdi-information-outline me-1"></i>
                                                <strong>Serão refeitas</strong> as formas de pagamento de
                                                <strong>{{ $parcelasNaoPagas->count() }} parcela(s)</strong> em aberto,
                                                totalizando
                                                <strong>R$ {{ number_format($totalNaoPagas, 2, ',', '.') }}</strong>.
                                                <br>
                                                As parcelas atuais serão canceladas e novas serão geradas com base nas
                                                formas
                                                de pagamento informadas abaixo.
                                            </div>

                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <label class="form-label mb-0 fw-semibold">
                                                        <i class="mdi mdi-credit-card-multiple-outline me-1"></i>
                                                        Formas de Pagamento
                                                    </label>
                                                </div>

                                                <div id="refazer_payment_entries"></div>

                                                <button type="button" class="btn btn-outline-secondary btn-sm w-100 mt-1"
                                                    id="refazer_add_payment_btn" onclick="refazerAddPaymentEntry()">
                                                    <i class="mdi mdi-plus me-1"></i>
                                                    Adicionar outra forma de pagamento
                                                </button>

                                                <div class="mt-2 p-2 rounded" id="refazer_payment_summary_bar"
                                                    style="background:#f8f9fb; border:1px solid #e5e7eb;">
                                                    <div class="d-flex justify-content-between align-items-center small">
                                                        <span class="text-muted">Valor a alocar:</span>
                                                        <span class="fw-semibold">R$
                                                            {{ number_format($totalNaoPagas, 2, ',', '.') }}</span>
                                                    </div>
                                                    <div
                                                        class="d-flex justify-content-between align-items-center small mt-1">
                                                        <span class="text-muted">Alocado:</span>
                                                        <span id="refazer_payment_allocated_display"
                                                            class="fw-semibold">R$ 0,00</span>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center small d-none mt-1"
                                                        id="refazer_payment_remaining_row">
                                                        <span class="text-warning fw-semibold">Restante:</span>
                                                        <span id="refazer_payment_remaining_display"
                                                            class="text-warning fw-semibold">R$ 0,00</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-0">
                                                <label for="refazer_observacoes"
                                                    class="form-label small fw-bold">Observações</label>
                                                <textarea id="refazer_observacoes" name="observacoes" class="form-control" rows="3" maxlength="1000"
                                                    placeholder="Motivo da alteração, observações internas..."></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer py-2 px-3">
                                            <button type="button" class="btn btn-outline-secondary btn-sm"
                                                data-bs-dismiss="modal">
                                                <i class="mdi mdi-close me-1"></i>Cancelar
                                            </button>
                                            <button type="submit" id="btnConfirmRefazerPagamento"
                                                class="btn btn-primary btn-sm">
                                                <i class="mdi mdi-check-circle me-1"></i>Confirmar
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
                <div class="col-lg-12">

                    @if ($sale['observacoes'])
                        <div class="mb-3">
                            <h6 class="text-muted mb-2">
                                <i class="mdi mdi-note-text me-1"></i>
                                Observações
                            </h6>
                            <p class="mb-0 text-muted small">{{ $sale['observacoes'] }}</p>
                        </div>
                    @endif

                    <hr>

                    <div class="small text-muted">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Venda criada em:</span>
                            <span>{{ $sale['created_at']->format('d/m/Y H:i') }}</span>
                        </div>
                        @if ($sale['updated_at'] && $sale['updated_at'] != $sale['created_at'])
                            <div class="d-flex justify-content-between">
                                <span>Última atualização:</span>
                                <span>{{ $sale['updated_at']->format('d/m/Y H:i') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>


        </div>
    </div>
@endsection

@push('styles')
    <style>
        @media print {

            .btn,
            .sticky-top {
                display: none !important;
            }

            .card {
                border: 1px solid #ddd !important;
                page-break-inside: avoid;
            }
        }

        .sticky-top {
            position: sticky;
            top: 20px;
            z-index: 1020;
        }

        .payment-entry {
            background: #f9fafb;
            border-color: #e5e7eb !important;
            transition: border-color 0.15s;
        }

        .payment-entry:focus-within {
            border-color: #6366f1 !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function() {
            'use strict';

            const DETAILS_URL_TEMPLATE = "{{ route('sales.parcela.details', '__ID__') }}";
            const UPDATE_URL_TEMPLATE = "{{ route('sales.parcela.update', '__ID__') }}";
            const REOPEN_URL_TEMPLATE = "{{ route('sales.parcela.reopen', '__ID__') }}";
            const REFAZER_PAGAMENTO_URL = "{{ route('sales.parcelas.refazer-pagamento', $sale['id']) }}";
            const CSRF_TOKEN = "{{ csrf_token() }}";

            let editParcelaModalInstance = null;
            let reopenParcelaModalInstance = null;
            let refazerPagamentoModalInstance = null;

            @if (!empty($temParcelasAbertasGlobal))
                const REFAZER_PAGAMENTO_TOTAL = {{ json_encode(round($totalNaoPagasGlobal, 2)) }};
            @else
                const REFAZER_PAGAMENTO_TOTAL = 0;
            @endif

            const REFAZER_PAYMENT_METHODS_MAP = {
                'dinheiro': 'Dinheiro',
                'cartao_debito': 'Cartão de Débito',
                'cartao_credito': 'Cartão de Crédito',
                'crediario': 'Crediário',
                'pix': 'PIX'
            };

            let refazerPaymentEntries = [];
            let refazerPaymentEntryCounter = 0;

            function refazerFormatCurrency(value) {
                const num = Number(value);
                if (!Number.isFinite(num)) return '0,00';
                return num.toFixed(2).replace('.', ',');
            }

            function refazerGetDefaultFirstDueDate() {
                const today = new Date();
                const dueDate = new Date(today.getFullYear(), today.getMonth() + 1, today.getDate());
                return dueDate.toLocaleDateString('en-CA');
            }

            function refazerGetTotalAllocated() {
                return refazerPaymentEntries.reduce((sum, e) => sum + (parseFloat(e.value) || 0), 0);
            }

            function refazerIsPaymentValid() {
                if (refazerPaymentEntries.length === 0) return false;
                const target = REFAZER_PAGAMENTO_TOTAL;
                if (target <= 0) return false;

                for (const entry of refazerPaymentEntries) {
                    if (!entry.method || !(parseFloat(entry.value) > 0)) return false;
                    if (entry.method === 'crediario' && !entry.firstDueDate) return false;
                }

                return Math.abs(refazerGetTotalAllocated() - target) <= 0.02;
            }

            function refazerAddPaymentEntry() {
                const id = ++refazerPaymentEntryCounter;
                const target = REFAZER_PAGAMENTO_TOTAL;
                const allocated = refazerGetTotalAllocated();
                const remaining = target > 0 ? Math.max(0, target - allocated) : '';
                refazerPaymentEntries.push({
                    id,
                    method: '',
                    value: remaining > 0.001 ? parseFloat(remaining.toFixed(2)) : '',
                    installments: 1,
                    firstDueDate: refazerGetDefaultFirstDueDate(),
                    userModified: remaining > 0.001,
                });
                refazerRenderPaymentEntries();
                refazerUpdatePaymentSummary();
            }

            function refazerRemovePaymentEntry(id) {
                if (refazerPaymentEntries.length <= 1) return;
                refazerPaymentEntries = refazerPaymentEntries.filter(e => e.id !== id);
                refazerRenderPaymentEntries();
                refazerUpdatePaymentSummary();
            }

            function refazerOnPaymentMethodChange(id) {
                const entry = refazerPaymentEntries.find(e => e.id === id);
                if (!entry) return;
                entry.method = document.getElementById('refazer_payment-method-' + id).value;

                const installmentsDiv = document.getElementById('refazer_payment-installments-' + id);
                const isInstallable = entry.method === 'crediario';
                const isCrediario = entry.method === 'crediario';
                if (installmentsDiv) {
                    installmentsDiv.style.display = isInstallable ? 'block' : 'none';
                }
                if (!isInstallable) {
                    entry.installments = 1;
                }
                if (!entry.firstDueDate) {
                    entry.firstDueDate = refazerGetDefaultFirstDueDate();
                }
                const firstDueDateDiv = document.getElementById('refazer_payment-first-due-date-' + id);
                if (firstDueDateDiv) {
                    firstDueDateDiv.style.display = isCrediario ? 'block' : 'none';
                }
                refazerUpdateInstallmentHint(id);
                refazerUpdatePaymentSummary();
            }

            function refazerOnPaymentValueChange(id) {
                const entry = refazerPaymentEntries.find(e => e.id === id);
                if (!entry) return;
                entry.value = parseFloat(document.getElementById('refazer_payment-value-' + id).value) || 0;
                entry.userModified = true;
                refazerUpdateInstallmentHint(id);
                refazerUpdatePaymentSummary();
            }

            function refazerOnPaymentInstallmentsChange(id) {
                const entry = refazerPaymentEntries.find(e => e.id === id);
                if (!entry) return;
                entry.installments = parseInt(document.getElementById('refazer_payment-installments-select-' + id)
                    .value) || 1;
                refazerUpdateInstallmentHint(id);
            }

            function refazerOnPaymentFirstDueDateChange(id) {
                const entry = refazerPaymentEntries.find(e => e.id === id);
                if (!entry) return;
                entry.firstDueDate = document.getElementById('refazer_payment-first-due-date-input-' + id).value ||
                    refazerGetDefaultFirstDueDate();
            }

            function refazerUpdateInstallmentHint(id) {
                const entry = refazerPaymentEntries.find(e => e.id === id);
                if (!entry) return;
                const hintEl = document.getElementById('refazer_payment-installment-value-' + id);
                if (!hintEl) return;
                if (entry.value > 0 && entry.installments > 1) {
                    hintEl.textContent = entry.installments + 'x de R$ ' + refazerFormatCurrency(entry.value / entry
                        .installments);
                } else {
                    hintEl.textContent = '';
                }
            }

            function refazerUpdatePaymentSummary() {
                const target = REFAZER_PAGAMENTO_TOTAL;
                const allocated = refazerGetTotalAllocated();
                const remaining = target - allocated;

                const allocatedDisplay = document.getElementById('refazer_payment_allocated_display');
                const remainingRow = document.getElementById('refazer_payment_remaining_row');
                const remainingDisplay = document.getElementById('refazer_payment_remaining_display');
                const addBtn = document.getElementById('refazer_add_payment_btn');

                if (allocatedDisplay) {
                    allocatedDisplay.textContent = 'R$ ' + refazerFormatCurrency(allocated);
                }

                if (remaining > 0.02) {
                    if (remainingRow) remainingRow.classList.remove('d-none');
                    if (remainingDisplay) remainingDisplay.textContent = 'R$ ' + refazerFormatCurrency(remaining);
                    if (allocatedDisplay) allocatedDisplay.className = 'fw-semibold text-warning';
                    if (addBtn) {
                        addBtn.disabled = false;
                        addBtn.title = '';
                    }
                } else if (allocated > target + 0.02) {
                    if (remainingRow) remainingRow.classList.add('d-none');
                    if (allocatedDisplay) allocatedDisplay.className = 'fw-semibold text-danger';
                    if (addBtn) {
                        addBtn.disabled = false;
                        addBtn.title = '';
                    }
                } else {
                    if (remainingRow) remainingRow.classList.add('d-none');
                    if (allocatedDisplay) allocatedDisplay.className = 'fw-semibold text-success';
                    if (addBtn) {
                        addBtn.disabled = true;
                        addBtn.title = 'Total já totalmente alocado';
                    }
                }
            }

            function refazerRenderPaymentEntries() {
                const container = document.getElementById('refazer_payment_entries');
                if (!container) return;
                const showRemove = refazerPaymentEntries.length > 1;

                const optionsHtml = (selectedMethod) => ['dinheiro', 'cartao_debito', 'cartao_credito', 'crediario',
                        'pix'
                    ]
                    .map(v =>
                        `<option value="${v}" ${selectedMethod === v ? 'selected' : ''}>${REFAZER_PAYMENT_METHODS_MAP[v]}</option>`
                    )
                    .join('');

                const installmentsOptions = (selected) => Array.from({
                        length: 12
                    }, (_, i) => i + 1)
                    .map(n => `<option value="${n}" ${selected === n ? 'selected' : ''}>${n}x sem juros</option>`)
                    .join('');

                container.innerHTML = refazerPaymentEntries.map(entry => {
                    const isInstallable = entry.method === 'crediario';
                    const isCrediario = entry.method === 'crediario';
                    const hintText = (entry.value > 0 && entry.installments > 1) ?
                        entry.installments + 'x de R$ ' + refazerFormatCurrency(entry.value / entry
                            .installments) : '';

                    return `
                    <div class="payment-entry border rounded p-2 mb-2" id="refazer_payment-entry-${entry.id}">
                        <div class="d-flex gap-2 mb-2">
                            <select class="form-select form-select-sm" id="refazer_payment-method-${entry.id}"
                                    onchange="refazerOnPaymentMethodChange(${entry.id})">
                                <option value="">Selecione...</option>
                                ${optionsHtml(entry.method)}
                            </select>
                            ${showRemove ? `<button type="button" class="btn btn-sm btn-outline-danger flex-shrink-0"
                                                                                                                onclick="refazerRemovePaymentEntry(${entry.id})">
                                                                                                                <i class="mdi mdi-close"></i>
                                                                                                            </button>` : ''}
                        </div>
                        <div class="input-group input-group-sm mb-1">
                            <span class="input-group-text">R$</span>
                            <input type="number" class="form-control" id="refazer_payment-value-${entry.id}"
                                   value="${entry.value !== '' ? entry.value : ''}"
                                   step="0.01" min="0.01" placeholder="0,00"
                                   oninput="refazerOnPaymentValueChange(${entry.id})">
                        </div>
                        <div id="refazer_payment-installments-${entry.id}" style="display:${isInstallable ? 'block' : 'none'};">
                            <label class="form-label form-label-sm mb-1 mt-2">Quantidade de Parcelas</label>
                            <select class="form-select form-select-sm mb-1"
                                    id="refazer_payment-installments-select-${entry.id}"
                                    onchange="refazerOnPaymentInstallmentsChange(${entry.id})">
                                ${installmentsOptions(entry.installments)}
                            </select>
                            <small class="text-muted" id="refazer_payment-installment-value-${entry.id}">${hintText}</small>
                        </div>
                        <div id="refazer_payment-first-due-date-${entry.id}" class="mt-2" style="display:${isCrediario ? 'block' : 'none'};">
                            <label class="form-label form-label-sm mb-1">Primeiro Vencimento</label>
                            <input type="date" class="form-control form-control-sm"
                                   id="refazer_payment-first-due-date-input-${entry.id}"
                                   value="${entry.firstDueDate || refazerGetDefaultFirstDueDate()}"
                                   onchange="refazerOnPaymentFirstDueDateChange(${entry.id})">
                        </div>
                    </div>
                `;
                }).join('');
            }

            function refazerResetForm() {
                refazerPaymentEntries = [];
                refazerPaymentEntryCounter = 0;
                const initId = ++refazerPaymentEntryCounter;
                refazerPaymentEntries.push({
                    id: initId,
                    method: '',
                    value: REFAZER_PAGAMENTO_TOTAL > 0 ? parseFloat(REFAZER_PAGAMENTO_TOTAL.toFixed(2)) : '',
                    installments: 1,
                    firstDueDate: refazerGetDefaultFirstDueDate(),
                    userModified: REFAZER_PAGAMENTO_TOTAL > 0,
                });
                refazerRenderPaymentEntries();
                refazerUpdatePaymentSummary();
                const obs = document.getElementById('refazer_observacoes');
                if (obs) obs.value = '';
            }

            function refazerGetModalEl() {
                return document.getElementById('refazerPagamentoModal');
            }

            function refazerEnsureModalInstance() {
                if (!refazerPagamentoModalInstance) {
                    var modalEl = refazerGetModalEl();
                    if (modalEl && window.bootstrap && window.bootstrap.Modal) {
                        refazerPagamentoModalInstance = new window.bootstrap.Modal(modalEl, {
                            backdrop: 'static',
                            keyboard: false,
                        });
                    }
                }
                return refazerPagamentoModalInstance;
            }

            function refazerHideModal() {
                var modalInstance = refazerEnsureModalInstance();
                if (modalInstance) {
                    modalInstance.hide();
                } else if (window.jQuery && window.jQuery.fn && window.jQuery.fn.modal) {
                    window.jQuery('#refazerPagamentoModal').modal('hide');
                } else {
                    var fallback = refazerGetModalEl();
                    if (fallback) {
                        fallback.classList.remove('show');
                        fallback.style.display = 'none';
                    }
                }
            }

            window.openRefazerPagamentoModal = function() {
                if (REFAZER_PAGAMENTO_TOTAL <= 0) {
                    showError('Não há parcelas em aberto para refazer pagamento.');
                    return;
                }
                refazerResetForm();

                var modalInstance = refazerEnsureModalInstance();
                if (modalInstance) {
                    modalInstance.show();
                } else if (window.jQuery && window.jQuery.fn && window.jQuery.fn.modal) {
                    window.jQuery('#refazerPagamentoModal').modal({
                        backdrop: 'static',
                        keyboard: false,
                    });
                    window.jQuery('#refazerPagamentoModal').modal('show');
                } else {
                    var fallback = refazerGetModalEl();
                    if (fallback) {
                        fallback.classList.add('show');
                        fallback.style.display = 'block';
                    }
                }
            };

            function submitRefazerPagamento(event) {
                event.preventDefault();

                if (!refazerIsPaymentValid()) {
                    const target = REFAZER_PAGAMENTO_TOTAL;
                    const allocated = refazerGetTotalAllocated();
                    if (refazerPaymentEntries.length === 0) {
                        showError('Adicione pelo menos uma forma de pagamento.');
                    } else if (refazerPaymentEntries.some(e => !e.method)) {
                        showError('Selecione a forma de pagamento em todas as entradas.');
                    } else if (refazerPaymentEntries.some(e => !(parseFloat(e.value) > 0))) {
                        showError('Informe o valor em todas as formas de pagamento.');
                    } else if (Math.abs(allocated - target) > 0.02) {
                        showError('A soma dos pagamentos (R$ ' + refazerFormatCurrency(allocated) +
                            ') deve ser igual ao valor em aberto (R$ ' + refazerFormatCurrency(target) + ').');
                    } else if (refazerPaymentEntries.some(e => e.method === 'crediario' && !e.firstDueDate)) {
                        showError('Informe o primeiro vencimento para pagamentos no crediário.');
                    } else {
                        showError('Verifique os dados das formas de pagamento.');
                    }
                    return;
                }

                const btnConfirm = document.getElementById('btnConfirmRefazerPagamento');
                if (btnConfirm) {
                    btnConfirm.disabled = true;
                    btnConfirm.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i>Processando...';
                }

                const payload = {
                    pagamentos: refazerPaymentEntries.map(e => ({
                        forma_pagamento: e.method,
                        valor: parseFloat(e.value) || 0,
                        parcelas: parseInt(e.installments) || 1,
                        primeiro_vencimento: e.method === 'crediario' ? (e.firstDueDate || null) : null,
                    })),
                    observacoes: (document.getElementById('refazer_observacoes').value || '').trim() || null,
                };

                fetch(REFAZER_PAGAMENTO_URL, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': CSRF_TOKEN,
                        },
                        body: JSON.stringify(payload),
                    })
                    .then(function(res) {
                        if (res.status === 422) {
                            return res.json().then(function(errPayload) {
                                var erros = errPayload.errors || {};
                                var msgs = [];
                                if (errPayload.message) msgs.push(errPayload.message);
                                Object.keys(erros).forEach(function(k) {
                                    var arr = Array.isArray(erros[k]) ? erros[k] : [erros[k]];
                                    arr.forEach(function(m) {
                                        msgs.push(m);
                                    });
                                });
                                throw new Error(msgs.length ? msgs.join('\n') :
                                    'Verifique os campos informados.');
                            });
                        }
                        if (!res.ok) {
                            var status = res.status;
                            // Lê o body uma única vez como texto (evita "body stream already read")
                            return res.text().then(function(txt) {
                                var payload = null;
                                try {
                                    if (txt) payload = JSON.parse(txt);
                                } catch (_) {
                                    payload = null;
                                }
                                var msg = (payload && payload.message) ? payload.message : null;
                                if (!msg && payload && payload.errors) {
                                    var parts = [];
                                    Object.keys(payload.errors).forEach(function(k) {
                                        var arr = Array.isArray(payload.errors[k]) ? payload.errors[
                                            k] : [payload.errors[k]];
                                        arr.forEach(function(m) {
                                            parts.push(m);
                                        });
                                    });
                                    if (parts.length) msg = parts.join('\n');
                                }
                                if (!msg) {
                                    var short = '';
                                    if (txt && txt.length > 0) {
                                        short = txt.length < 400 ? txt : (txt.slice(0, 400) + '...');
                                    }
                                    msg = short ? ('Erro ao processar (' + status + '): ' + short) : (
                                        'Erro ao processar (' + status + ').');
                                }
                                if ((!msg || msg.indexOf('Erro ao refazer') !== 0) && payload && payload
                                    .debug) {
                                    var d = payload.debug;
                                    var extra = [];
                                    if (d.file) extra.push('Arquivo: ' + d.file);
                                    if (d.line) extra.push('Linha: ' + d.line);
                                    if (extra.length) msg += ' (' + extra.join(' / ') + ')';
                                }
                                throw new Error(msg);
                            });
                        }
                        return res.json();
                    })
                    .then(function(data) {
                        if (data && data.success) {
                            showSuccess(data.message || 'Forma de pagamento refeita com sucesso.');
                            refazerHideModal();
                            setTimeout(function() {
                                window.location.reload();
                            }, 350);
                        } else {
                            showError((data && data.message) || 'Não foi possível refazer a forma de pagamento.');
                            if (btnConfirm) {
                                btnConfirm.disabled = false;
                                btnConfirm.innerHTML = '<i class="mdi mdi-check-circle me-1"></i>Confirmar';
                            }
                        }
                    })
                    .catch(function(err) {
                        console.error('[refazer-pagamento] erro:', err);
                        showError(err.message || 'Erro ao refazer forma de pagamento.');
                        if (btnConfirm) {
                            btnConfirm.disabled = false;
                            btnConfirm.innerHTML = '<i class="mdi mdi-check-circle me-1"></i>Confirmar';
                        }
                    });
            }

            window.refazerAddPaymentEntry = refazerAddPaymentEntry;
            window.refazerRemovePaymentEntry = refazerRemovePaymentEntry;
            window.refazerOnPaymentMethodChange = refazerOnPaymentMethodChange;
            window.refazerOnPaymentValueChange = refazerOnPaymentValueChange;
            window.refazerOnPaymentInstallmentsChange = refazerOnPaymentInstallmentsChange;
            window.refazerOnPaymentFirstDueDateChange = refazerOnPaymentFirstDueDateChange;
            window.refazerResetForm = refazerResetForm;
            window.submitRefazerPagamento = submitRefazerPagamento;

            @php
                $waParcelas = [];
                $rawParcelas = $sale['parcelas_detalhes'] ?? [];
                $totalParcelas = count($rawParcelas);
                $ordensUsadas = [];
                foreach ($rawParcelas as $idx => $parcela) {
                    $statusRaw = strtolower((string) ($parcela['status'] ?? ''));
                    $isPagamentoParcial = $statusRaw === 'pagamento_parcial';
                    $isSaldoRemanescente = $statusRaw === 'saldo_remanescente';
                    $isCancelada = in_array($statusRaw, ['cancelada', 'cancelado'], true);
                    $isPaga = !$isCancelada && (!empty($parcela['pago_em']) || in_array($statusRaw, ['pago', 'paga'], true));
                    $isVencida = $statusRaw === 'vencida' || ($parcela['dias_atraso'] ?? 0) > 0;

                    $numeroParcela = isset($parcela['numero_parcela']) ? (int) $parcela['numero_parcela'] : $idx + 1;
                    $totalParcelaRaw = isset($parcela['total_parcelas']) ? (int) $parcela['total_parcelas'] : $totalParcelas;

                    $labelBadge = (string) ($parcela['parcela'] ?? '');
                    $isEntrada = false;
                    if (stripos($labelBadge, 'entrada') !== false) {
                        $isEntrada = true;
                    }

                    $ordinalLabel = '';
                    if ($isEntrada || $isSaldoRemanescente) {
                        if ($isSaldoRemanescente) {
                            $ordinalLabel = 'Saldo Remanescente';
                        } else {
                            $ordinalLabel = 'Entrada';
                        }
                    } else {
                        $num = $numeroParcela > 0 ? $numeroParcela : $idx + 1;
                        $ordinalLabel = $num . 'ª Parcela';
                    }

                    $vencBr = '';
                    if (!empty($parcela['vencimento'])) {
                        try {
                            $vencBr = \Carbon\Carbon::parse($parcela['vencimento'])->format('d/m/y');
                        } catch (\Throwable $e) {
                            $vencBr = '';
                        }
                    }

                    $valorBr = 'R$ ' . number_format((float) ($parcela['valor_parcela'] ?? ($parcela['valor_atualizado'] ?? 0)), 2, ',', '.');

                    $statusLinha = '';
                    if ($isCancelada) {
                        $statusLinha = 'cancelada';
                    } elseif ($isPaga) {
                        $pagoEmBr = '';
                        if (!empty($parcela['pago_em'])) {
                            try {
                                $dt = $parcela['pago_em'] instanceof \Carbon\Carbon ? $parcela['pago_em'] : \Carbon\Carbon::parse($parcela['pago_em']);
                                $pagoEmBr = 'Pago em ' . $dt->format('d/m/y');
                            } catch (\Throwable $e) {
                                $pagoEmBr = '';
                            }
                        }
                        $forma = trim((string) ($parcela['forma_pagamento'] ?? ''));
                        $statusLinha = $pagoEmBr;
                        if ($forma) {
                            $statusLinha .= ($statusLinha ? ' ' : '') . $forma;
                        }
                        $statusLinha .= ($statusLinha ? ' ' : '') . '🆗✅ obrigado 🙏';
                    } elseif ($isPagamentoParcial) {
                        $recebido = (float) ($parcela['valor_recebido'] ?? 0);
                        if ($recebido > 0) {
                            $pParcial = 'Recebido R$ ' . number_format($recebido, 2, ',', '.');
                            $pagoEmBr = '';
                            if (!empty($parcela['pago_em'])) {
                                try {
                                    $dt = $parcela['pago_em'] instanceof \Carbon\Carbon ? $parcela['pago_em'] : \Carbon\Carbon::parse($parcela['pago_em']);
                                    $pagoEmBr = ' em ' . $dt->format('d/m/y');
                                } catch (\Throwable $e) {
                                    $pagoEmBr = '';
                                }
                            }
                            $statusLinha = $pParcial . $pagoEmBr . ' (pagamento parcial)';
                        } else {
                            $statusLinha = 'Pagamento Parcial';
                        }
                    } elseif ($isVencida) {
                        $d = (int) ($parcela['dias_atraso'] ?? 0);
                        $statusLinha = 'atrasado' . ($d > 0 ? ' ' . $d . ' dias' : '');
                    } else {
                        // em dia / vence hoje / semana
                        $statusLinha = 'pendente';
                    }

                    $waParcelas[] = [
                        'ordinal' => $ordinalLabel,
                        'vencimento' => $vencBr,
                        'valor' => $valorBr,
                        'status' => $statusLinha,
                    ];
                }

                $waVenda = [
                    'numero' => (string) ($sale['numero'] ?? ($sale['id'] ?? '')),
                    'data' => (string) ($sale['data_formatada'] ?? ''),
                    'cliente_nome' => (string) ($sale['cliente']['nome'] ?? ''),
                    'total' => 'R$ ' . number_format((float) ($sale['total'] ?? 0), 2, ',', '.'),
                    'telefone' => (string) ($sale['cliente']['telefone'] ?? ''),
                    'parcelas' => $waParcelas,
                    'pix_chave' => (string) ($sale['pix_footer']['chave'] ?? ''),
                    'pix_nome_titular' => (string) ($sale['pix_footer']['nome_titular'] ?? ''),
                    'pix_banco' => (string) ($sale['pix_footer']['banco'] ?? ''),
                    'pix_tipo_chave' => (string) ($sale['pix_footer']['tipo_chave'] ?? ''),
                    'empresa_nome' => (string) ($sale['nome_empresa'] ?? ''),
                ];
            @endphp
            const VENDA_WHATSAPP_DATA = @json($waVenda);

            function normalizeWhatsappPhone(raw) {
                const digits = (raw || '').toString().replace(/\D+/g, '');
                if (!digits) return null;
                if (digits.startsWith('55')) return digits;
                if (digits.length === 10 || digits.length === 11) return '55' + digits;
                return digits;
            }

            function buildWhatsappMessage() {
                const d = VENDA_WHATSAPP_DATA || {};
                const linhas = [];

                linhas.push('*Histórico da compra*');
                if (d.numero || d.data) {
                    const parte = [];
                    if (d.numero) parte.push('Compra nº ' + d.numero);
                    if (d.data) parte.push('em ' + d.data);
                    linhas.push(parte.join(' '));
                }
                if (d.cliente_nome) linhas.push('Cliente: ' + d.cliente_nome);
                if (d.total) linhas.push('*Valor Total:* ' + d.total);
                linhas.push('');
                linhas.push('*-- Parcelas --*');

                const parcelas = Array.isArray(d.parcelas) ? d.parcelas : [];
                parcelas.forEach(function(p, i) {
                    if (i > 0) linhas.push('');
                    const ordinal = p.ordinal || ('Parcela ' + (i + 1));
                    const ehEntrada = /entrada/i.test(String(ordinal));
                    if (ehEntrada) {
                        let linha = ordinal + ' ' + (p.vencimento || '') + ' ' + (p.valor || '');
                        if (p.status) linha += ' ' + p.status;
                        linhas.push(linha.trim());
                    } else {
                        let linha = ordinal + '  venc. ' + (p.vencimento || '') + ' ' + (p.valor || '');
                        if (p.status) linha += ' ' + p.status;
                        linhas.push(linha.trim());
                    }
                });

                linhas.push('');
                linhas.push('Sempre que transferir favor enviar o comprovante para darmos baixa.✅');
                linhas.push('');

                const pixChave = (d.pix_chave || '').trim();
                const pixTitular = (d.pix_nome_titular || '').trim();
                const pixBanco = (d.pix_banco || '').trim();
                const empresaNome = (d.empresa_nome || '').trim();

                if (pixChave) {
                    linhas.push('Pix. ' + pixChave);
                }
                if (pixTitular) {
                    linhas.push(pixTitular);
                }
                if (pixBanco) {
                    linhas.push(pixBanco);
                }

                linhas.push('');
                if (empresaNome) {
                    linhas.push(empresaNome + ' agradece 🤝');
                }

                return linhas.join('\n');
            }

            window.sendVendaWhatsapp = function() {
                const d = VENDA_WHATSAPP_DATA || {};
                const waPhone = normalizeWhatsappPhone(d.telefone);
                if (!waPhone) {
                    showError('Cliente sem telefone cadastrado para envio via WhatsApp.');
                    return false;
                }

                const mensagem = buildWhatsappMessage();
                const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator
                    .userAgent);
                const waUrl = isMobile ?
                    ('https://wa.me/' + waPhone + '?text=' + encodeURIComponent(mensagem)) :
                    ('https://web.whatsapp.com/send?phone=' + waPhone + '&text=' + encodeURIComponent(mensagem));

                window.open(waUrl, 'whatsapp_venda');
                return true;
            };

            function getEditModalEl() {
                return document.getElementById('editParcelaModal');
            }

            function ensureModalInstance() {
                if (!editParcelaModalInstance) {
                    var modalEl = getEditModalEl();
                    if (modalEl && window.bootstrap && window.bootstrap.Modal) {
                        editParcelaModalInstance = new window.bootstrap.Modal(modalEl, {
                            backdrop: 'static',
                            keyboard: false
                        });
                    }
                }
                return editParcelaModalInstance;
            }

            function showError(message) {
                if (window.showAppModalMessage && typeof window.showAppModalMessage === 'function') {
                    window.showAppModalMessage('Atenção', message || 'Erro inesperado.', {
                        type: 'danger'
                    });
                } else {
                    alert(message || 'Erro inesperado.');
                }
            }

            function showSuccess(message) {
                if (window.showAppModalMessage && typeof window.showAppModalMessage === 'function') {
                    window.showAppModalMessage('Sucesso', message || 'Operação concluída com sucesso.', {
                        type: 'success',
                        autoClose: 2000
                    });
                } else {
                    alert(message || 'Operação concluída com sucesso.');
                }
            }

            function getReopenModalEl() {
                return document.getElementById('reopenParcelaModal');
            }

            function ensureReopenModalInstance() {
                if (!reopenParcelaModalInstance) {
                    var modalEl = getReopenModalEl();
                    if (modalEl && window.bootstrap && window.bootstrap.Modal) {
                        reopenParcelaModalInstance = new window.bootstrap.Modal(modalEl, {
                            backdrop: 'static',
                            keyboard: false
                        });
                    }
                }
                return reopenParcelaModalInstance;
            }

            function resetReopenForm() {
                const form = document.getElementById('reopenParcelaForm');
                if (!form) return;
                form.reset();
                document.getElementById('reopen_parcela_id').value = '';
                document.getElementById('reopenParcelaInfo').value = '';
            }

            function hideReopenModal() {
                var modalInstance = ensureReopenModalInstance();
                if (modalInstance) {
                    modalInstance.hide();
                } else if (window.jQuery && window.jQuery.fn && window.jQuery.fn.modal) {
                    window.jQuery('#reopenParcelaModal').modal('hide');
                } else {
                    var fallback = getReopenModalEl();
                    if (fallback) {
                        fallback.classList.remove('show');
                        fallback.style.display = 'none';
                    }
                }
            }

            function resetEditForm() {
                const form = document.getElementById('editParcelaForm');
                if (!form) return;
                form.reset();
                document.getElementById('edit_parcela_id').value = '';
                document.getElementById('editParcelaLabel').textContent = '';
            }

            window.openEditParcelaModal = function(parcelaId) {
                if (!parcelaId) {
                    showError('Parcela inválida.');
                    return;
                }

                resetEditForm();
                document.getElementById('edit_parcela_id').value = String(parcelaId);
                document.getElementById('editParcelaLabel').textContent = '#' + parcelaId;

                const url = DETAILS_URL_TEMPLATE.replace('__ID__', encodeURIComponent(parcelaId));

                fetch(url, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(function(res) {
                        if (!res.ok) {
                            return res.text().then(function(txt) {
                                throw new Error('Erro ao carregar parcela (' + res.status + ').');
                            });
                        }
                        return res.json();
                    })
                    .then(function(payload) {
                        if (!payload || !payload.success || !payload.data || !payload.data.parcela) {
                            showError((payload && payload.message) ||
                                'Não foi possível carregar os dados da parcela.');
                            return;
                        }

                        const parcela = payload.data.parcela;

                        const vencimentoInput = document.getElementById('edit_vencimento');
                        if (parcela.vencimento_em && /^\d{4}-\d{2}-\d{2}$/.test(String(parcela
                                .vencimento_em))) {
                            vencimentoInput.value = parcela.vencimento_em;
                        } else if (parcela.vencimento_em) {
                            try {
                                const d = new Date(parcela.vencimento_em);
                                if (!isNaN(d.getTime())) {
                                    vencimentoInput.value = d.toISOString().slice(0, 10);
                                }
                            } catch (e) {}
                        }

                        const valorInput = document.getElementById('edit_valor');
                        const valor = parseFloat(parcela.valor);
                        valorInput.value = isNaN(valor) ? '0.00' : valor.toFixed(2);

                        const formaInput = document.getElementById('edit_forma_pagamento');
                        formaInput.value = parcela.forma_pagamento ? String(parcela.forma_pagamento) : '';

                        const obsInput = document.getElementById('edit_observacoes');
                        obsInput.value = parcela.observacoes ? String(parcela.observacoes) : '';

                        if (payload.data.parcela && (payload.data.parcela.numero_parcela || payload.data
                                .pedido)) {
                            var textoParcela = '';
                            if (payload.data.parcela.total_parcelas) {
                                textoParcela = ' (Parcela ' + payload.data.parcela.numero_parcela + '/' +
                                    payload.data.parcela.total_parcelas + ')';
                            } else if (payload.data.parcela.numero_parcela) {
                                textoParcela = ' (Parcela ' + payload.data.parcela.numero_parcela + ')';
                            }
                            document.getElementById('editParcelaLabel').textContent = '#' + parcelaId +
                                textoParcela;
                        }

                        var modalInstance = ensureModalInstance();
                        if (modalInstance) {
                            modalInstance.show();
                        } else if (window.jQuery && window.jQuery.fn && window.jQuery.fn.modal) {
                            window.jQuery('#editParcelaModal').modal({
                                backdrop: 'static',
                                keyboard: false
                            });
                            window.jQuery('#editParcelaModal').modal('show');
                        } else {
                            var fallback = getEditModalEl();
                            if (fallback) {
                                fallback.classList.add('show');
                                fallback.style.display = 'block';
                            }
                        }
                    })
                    .catch(function(err) {
                        console.error('[editar-parcela] erro ao carregar:', err);
                        showError(err.message || 'Erro ao carregar dados da parcela.');
                    });
            };

            window.openReopenParcelaModal = function(parcelaId, parcelaLabel) {
                if (!parcelaId) {
                    showError('Parcela inválida.');
                    return;
                }

                resetReopenForm();
                document.getElementById('reopen_parcela_id').value = String(parcelaId);
                document.getElementById('reopenParcelaInfo').value =
                    '#' + parcelaId + (parcelaLabel ? ' - ' + String(parcelaLabel) : '');

                var modalInstance = ensureReopenModalInstance();
                if (modalInstance) {
                    modalInstance.show();
                } else if (window.jQuery && window.jQuery.fn && window.jQuery.fn.modal) {
                    window.jQuery('#reopenParcelaModal').modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                    window.jQuery('#reopenParcelaModal').modal('show');
                } else {
                    var fallback = getReopenModalEl();
                    if (fallback) {
                        fallback.classList.add('show');
                        fallback.style.display = 'block';
                    }
                }
            };

            function submitReopenParcela(event) {
                event.preventDefault();

                const parcelaId = document.getElementById('reopen_parcela_id').value;
                if (!parcelaId) {
                    showError('Parcela não identificada.');
                    return;
                }

                const btnConfirm = document.getElementById('btnConfirmReopen');
                if (btnConfirm) {
                    btnConfirm.disabled = true;
                    btnConfirm.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i>Processando...';
                }

                const motivo = (document.getElementById('reopen_motivo').value || '').trim();
                const payload = {
                    motivo: motivo || null,
                    return_url: window.location.href
                };

                const url = REOPEN_URL_TEMPLATE.replace('__ID__', encodeURIComponent(parcelaId));

                fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': CSRF_TOKEN
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(function(res) {
                        if (res.status === 422) {
                            return res.json().then(function(errPayload) {
                                var erros = errPayload.errors || {};
                                var msgs = [];
                                Object.keys(erros).forEach(function(k) {
                                    var arr = Array.isArray(erros[k]) ? erros[k] : [erros[k]];
                                    arr.forEach(function(m) {
                                        msgs.push(m);
                                    });
                                });
                                throw new Error(msgs.length ? msgs.join('\n') :
                                    'Verifique os campos informados.');
                            });
                        }
                        if (!res.ok) {
                            return res.text().then(function(txt) {
                                throw new Error('Erro ao reabrir parcela (' + res.status + ').');
                            });
                        }
                        return res.json();
                    })
                    .then(function(data) {
                        if (data && data.success) {
                            showSuccess(data.message || 'Parcela reaberta com sucesso.');
                            setTimeout(function() {
                                if (data.return_url) {
                                    window.location.href = data.return_url;
                                } else {
                                    window.location.reload();
                                }
                            }, 800);
                        } else {
                            showError((data && data.message) || 'Não foi possível reabrir a parcela.');
                            if (btnConfirm) {
                                btnConfirm.disabled = false;
                                btnConfirm.innerHTML =
                                    '<i class="mdi mdi-lock-open-outline me-1"></i>Confirmar Reabertura';
                            }
                        }
                    })
                    .catch(function(err) {
                        console.error('[reabrir-parcela] erro:', err);
                        showError(err.message || 'Erro ao reabrir parcela.');
                        if (btnConfirm) {
                            btnConfirm.disabled = false;
                            btnConfirm.innerHTML =
                                '<i class="mdi mdi-lock-open-outline me-1"></i>Confirmar Reabertura';
                        }
                    });
            }

            function hideEditModal() {
                var modalInstance = ensureModalInstance();
                if (modalInstance) {
                    modalInstance.hide();
                } else if (window.jQuery && window.jQuery.fn && window.jQuery.fn.modal) {
                    window.jQuery('#editParcelaModal').modal('hide');
                } else {
                    var fallback = getEditModalEl();
                    if (fallback) {
                        fallback.classList.remove('show');
                        fallback.style.display = 'none';
                    }
                }
            }

            function saveEditParcela(event) {
                event.preventDefault();

                const parcelaId = document.getElementById('edit_parcela_id').value;
                if (!parcelaId) {
                    showError('Parcela não identificada.');
                    return;
                }

                const vencimento = document.getElementById('edit_vencimento').value;
                const valor = document.getElementById('edit_valor').value;

                if (!vencimento) {
                    showError('Informe o novo vencimento.');
                    document.getElementById('edit_vencimento').focus();
                    return;
                }

                if (valor === '' || isNaN(parseFloat(valor))) {
                    showError('Informe o novo valor da parcela.');
                    document.getElementById('edit_valor').focus();
                    return;
                }

                const btnSave = document.getElementById('btnSaveEditParcela');
                if (btnSave) {
                    btnSave.disabled = true;
                    btnSave.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i>Salvando...';
                }

                const payload = {
                    vencimento_em: vencimento,
                    valor: parseFloat(valor),
                    forma_pagamento: (document.getElementById('edit_forma_pagamento').value || '').trim() || null,
                    observacoes: (document.getElementById('edit_observacoes').value || '').trim() || null,
                    return_url: window.location.href
                };

                const url = UPDATE_URL_TEMPLATE.replace('__ID__', encodeURIComponent(parcelaId));

                fetch(url, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': CSRF_TOKEN
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(function(res) {
                        if (res.status === 422) {
                            return res.json().then(function(errPayload) {
                                var erros = errPayload.errors || {};
                                var msgs = [];
                                Object.keys(erros).forEach(function(k) {
                                    var arr = Array.isArray(erros[k]) ? erros[k] : [erros[k]];
                                    arr.forEach(function(m) {
                                        msgs.push(m);
                                    });
                                });
                                throw new Error(msgs.length ? msgs.join('\n') :
                                    'Verifique os campos informados.');
                            });
                        }
                        if (!res.ok) {
                            return res.text().then(function(txt) {
                                throw new Error('Erro ao salvar parcela (' + res.status + ').');
                            });
                        }
                        return res.json();
                    })
                    .then(function(data) {
                        if (data && data.success) {
                            showSuccess(data.message || 'Parcela atualizada com sucesso.');
                            setTimeout(function() {
                                if (data.return_url) {
                                    window.location.href = data.return_url;
                                } else {
                                    window.location.reload();
                                }
                            }, 800);
                        } else {
                            showError((data && data.message) || 'Não foi possível salvar a parcela.');
                            if (btnSave) {
                                btnSave.disabled = false;
                                btnSave.innerHTML = '<i class="mdi mdi-content-save me-1"></i>Salvar Alterações';
                            }
                        }
                    })
                    .catch(function(err) {
                        console.error('[editar-parcela] erro ao salvar:', err);
                        showError(err.message || 'Erro ao salvar alterações.');
                        if (btnSave) {
                            btnSave.disabled = false;
                            btnSave.innerHTML = '<i class="mdi mdi-content-save me-1"></i>Salvar Alterações';
                        }
                    });
            }

            document.addEventListener('DOMContentLoaded', function() {
                ensureModalInstance();
                ensureReopenModalInstance();
                refazerEnsureModalInstance();

                const form = document.getElementById('editParcelaForm');
                if (form) {
                    form.addEventListener('submit', saveEditParcela);
                }

                const reopenForm = document.getElementById('reopenParcelaForm');
                if (reopenForm) {
                    reopenForm.addEventListener('submit', submitReopenParcela);
                }

                const refazerForm = document.getElementById('refazerPagamentoForm');
                if (refazerForm) {
                    refazerForm.addEventListener('submit', submitRefazerPagamento);
                }
            });
        })();
    </script>
@endpush
