@extends('layouts.app')

@section('title', 'Detalhes da Venda - VisaoSis')

@section('content')
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
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="mdi mdi-calendar-multiple-check text-primary me-2"></i>
                                Parcelas da Venda
                            </h5>
                            <span class="badge bg-secondary">
                                {{ collect($sale['parcelas_detalhes'])->count() }} parcela(s)
                            </span>
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
                                                $isPaga = $parcela['status'] === 'paga' || !empty($parcela['pago_em']);
                                                $isPagamentoParcial = $statusRaw === 'pagamento_parcial';
                                                $isSaldoRemanescente = $statusRaw === 'saldo_remanescente';
                                                $isCancelada = in_array($statusRaw, ['cancelada', 'cancelado'], true);
                                                $podeEditar = !$isPaga && !$isCancelada;
                                            @endphp
                                            <tr
                                                class="@if ($parcela['status'] === 'vencida') table-danger @elseif($parcela['status'] === 'vence_hoje') table-warning @elseif($parcela['status'] === 'vence_semana') table-info @elseif($isPaga) table-success @elseif($isPagamentoParcial || $isSaldoRemanescente) table-light @endif">
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
    </style>
@endpush

@push('scripts')
    <script>
        (function() {
            'use strict';

            const DETAILS_URL_TEMPLATE = "{{ route('sales.parcela.details', '__ID__') }}";
            const UPDATE_URL_TEMPLATE = "{{ route('sales.parcela.update', '__ID__') }}";
            const REOPEN_URL_TEMPLATE = "{{ route('sales.parcela.reopen', '__ID__') }}";
            const CSRF_TOKEN = "{{ csrf_token() }}";

            let editParcelaModalInstance = null;
            let reopenParcelaModalInstance = null;

            @php
                $waParcelas = [];
                $rawParcelas = $sale['parcelas_detalhes'] ?? [];
                $totalParcelas = count($rawParcelas);
                $ordensUsadas = [];
                foreach ($rawParcelas as $idx => $parcela) {
                    $statusRaw = strtolower((string) ($parcela['status'] ?? ''));
                    $isPagamentoParcial = $statusRaw === 'pagamento_parcial';
                    $isSaldoRemanescente = $statusRaw === 'saldo_remanescente';
                    $isPaga = !empty($parcela['pago_em']) || in_array($statusRaw, ['pago', 'paga'], true);
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
                    if ($isPaga) {
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
                } else {
                    linhas.push('Pix. 92981650580');
                }
                if (pixTitular) {
                    linhas.push(pixTitular);
                } else {
                    linhas.push('Jaime Martins');
                }
                if (pixBanco) {
                    linhas.push(pixBanco);
                } else {
                    linhas.push('Caixa econômica');
                }
                linhas.push('');
                if (empresaNome) {
                    linhas.push(empresaNome + ' agradece 🤝');
                } else {
                    linhas.push('Ótica Asafe agradece 🤝');
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

                const form = document.getElementById('editParcelaForm');
                if (form) {
                    form.addEventListener('submit', saveEditParcela);
                }

                const reopenForm = document.getElementById('reopenParcelaForm');
                if (reopenForm) {
                    reopenForm.addEventListener('submit', submitReopenParcela);
                }
            });
        })();
    </script>
@endpush
