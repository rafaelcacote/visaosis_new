@extends('layouts.app')

@section('title', 'Central de Notificações WhatsApp - Connect Plus')

@section('content')
    <div class="d-xl-flex justify-content-between align-items-start mb-4">
        <div>
            <h2 class="text-dark font-weight-bold mb-2">
                <i class="mdi mdi-whatsapp me-2"></i>
                {{ !empty($templatesPage) ? 'Templates de Mensagens' : 'Central de Notificações WhatsApp' }}
            </h2>
            <p class="text-muted mb-0">
                {{ !empty($templatesPage) ? 'Cadastro e configuração de templates de mensagens' : 'Automação de lembretes, cobranças e comunicação com clientes' }}
            </p>
        </div>
        <div class="d-flex gap-2">
            @if (!empty($templatesPage))
                <a class="btn btn-outline-secondary" href="{{ route('financial.notifications') }}">
                    <i class="mdi mdi-arrow-left me-2"></i>Voltar
                </a>
            @else
                <a class="btn btn-outline-success" href="{{ route('financial.notifications.templates') }}">
                    <i class="mdi mdi-cog me-2"></i>Configurar Templates
                </a>
            @endif
        </div>
    </div>

    @if (!empty($templatesPage))
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="mdi mdi-message-text-outline me-2"></i>
                            Templates de Mensagens
                        </h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('financial.notifications.templates.save') }}">
                            @csrf
                            @if (!empty($templates))
                                @foreach ($templates as $i => $t)
                                    <div class="border rounded p-3 mb-3">
                                        <input type="hidden" name="templates[{{ $i }}][tipo]"
                                            value="{{ $t['tipo'] }}">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div class="fw-semibold">{{ $t['tipo'] }}</div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="1"
                                                    id="tpl_ativo_{{ $i }}"
                                                    name="templates[{{ $i }}][ativo]"
                                                    {{ !empty($t['ativo']) ? 'checked' : '' }}>
                                                <label class="form-check-label"
                                                    for="tpl_ativo_{{ $i }}">Ativo</label>
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Título</label>
                                            <input type="text" class="form-control"
                                                name="templates[{{ $i }}][titulo]" value="{{ $t['titulo'] }}">
                                        </div>
                                        <div>
                                            <label class="form-label">Mensagem</label>
                                            <textarea class="form-control" rows="6" name="templates[{{ $i }}][mensagem]">{{ $t['mensagem'] }}</textarea>
                                        </div>
                                    </div>
                                @endforeach
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('financial.notifications') }}"
                                        class="btn btn-outline-secondary">Cancelar</a>
                                    <button type="submit" class="btn btn-success">Salvar</button>
                                </div>
                            @else
                                <div class="text-muted">Nenhum template disponível.</div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Estatísticas de Envio -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card card-statistics">
                    <div class="card-body">
                        <div class="clearfix">
                            <div class="float-start">
                                <i class="mdi mdi-check-circle text-success icon-lg"></i>
                            </div>
                            <div class="float-end">
                                <p class="mb-0 text-right text-dark">Mensagens Enviadas</p>
                                <div class="fluid-container">
                                    <h3 class="font-weight-medium text-right mb-0 text-dark">
                                        {{ count(array_filter($notifications, fn($n) => $n['status'] == 'enviado')) }}</h3>
                                </div>
                                <small class="text-muted">Hoje</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card card-statistics">
                    <div class="card-body">
                        <div class="clearfix">
                            <div class="float-start">
                                <i class="mdi mdi-clock-outline text-info icon-lg"></i>
                            </div>
                            <div class="float-end">
                                <p class="mb-0 text-right text-dark">Programadas</p>
                                <div class="fluid-container">
                                    <h3 class="font-weight-medium text-right mb-0 text-dark">
                                        {{ count(array_filter($notifications, fn($n) => $n['status'] == 'programado')) }}
                                    </h3>
                                </div>
                                <small class="text-muted">Próximas horas</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card card-statistics">
                    <div class="card-body">
                        <div class="clearfix">
                            <div class="float-start">
                                <i class="mdi mdi-eye text-warning icon-lg"></i>
                            </div>
                            <div class="float-end">
                                <p class="mb-0 text-right text-dark">Lidas</p>
                                <div class="fluid-container">
                                    <h3 class="font-weight-medium text-right mb-0 text-dark">
                                        {{ count(array_filter($notifications, fn($n) => $n['lido'])) }}</h3>
                                </div>
                                <small class="text-muted">Taxa:
                                    {{ count($notifications) ? round((count(array_filter($notifications, fn($n) => $n['lido'])) / count($notifications)) * 100) : 0 }}%</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card card-statistics">
                    <div class="card-body">
                        <div class="clearfix">
                            <div class="float-start">
                                <i class="mdi mdi-reply text-primary icon-lg"></i>
                            </div>
                            <div class="float-end">
                                <p class="mb-0 text-right text-dark">Respondidas</p>
                                <div class="fluid-container">
                                    <h3 class="font-weight-medium text-right mb-0 text-dark">
                                        {{ count(array_filter($notifications, fn($n) => $n['respondido'])) }}</h3>
                                </div>
                                <small class="text-muted">Taxa:
                                    {{ count($notifications) ? round((count(array_filter($notifications, fn($n) => $n['respondido'])) / count($notifications)) * 100) : 0 }}%</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ações Rápidas -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="mdi mdi-lightning-bolt me-2"></i>
                            Ações Automáticas
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <button
                                    class="btn btn-outline-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3"
                                    onclick="scrollToEligibles('crediario_vencimento_hoje')">
                                    <i class="mdi mdi-calendar-clock fs-2 mb-2"></i>
                                    <span>Vencimento Hoje</span>
                                    <small class="text-muted">{{ $eligibleCounts['vencimento_hoje'] ?? 0 }}
                                        clientes</small>
                                </button>
                            </div>
                            <div class="col-md-3">
                                <button
                                    class="btn btn-outline-danger w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3"
                                    onclick="scrollToEligibles('crediario_atraso')">
                                    <i class="mdi mdi-alert-circle fs-2 mb-2"></i>
                                    <span>Cobrança Atraso</span>
                                    <small class="text-muted">{{ $eligibleCounts['atraso'] ?? 0 }} clientes</small>
                                </button>
                            </div>
                            <div class="col-md-3">
                                <button
                                    class="btn btn-outline-info w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3"
                                    onclick="scrollToEligibles('crediario_lembrete_amanha')">
                                    <i class="mdi mdi-bell fs-2 mb-2"></i>
                                    <span>Lembrete Amanhã</span>
                                    <small class="text-muted">{{ $eligibleCounts['lembrete_amanha'] ?? 0 }}
                                        clientes</small>
                                </button>
                            </div>
                            <div class="col-md-3">
                                <button
                                    class="btn btn-outline-success w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3"
                                    onclick="scrollToEligibles('receita_validade')">
                                    <i class="mdi mdi-file-document-outline fs-2 mb-2"></i>
                                    <span>Validade Receita</span>
                                    <small class="text-muted">{{ $eligibleCounts['receita_validade'] ?? 0 }}
                                        clientes</small>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4" id="eligiblesSection">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="mdi mdi-account-multiple-check me-2"></i>
                            Clientes elegíveis para envio automático
                            ({{ is_array($eligibles ?? null) ? count($eligibles) : 0 }})
                        </h6>
                        <div class="d-flex gap-2">
                            <select class="form-select form-select-sm" id="eligibleTipoFilter" style="width: 260px;">
                                <option value="">Todos</option>
                                <option value="crediario_vencimento_hoje">Vencimento Hoje (Crediário)</option>
                                <option value="crediario_atraso">Cobrança Atraso (Crediário)</option>
                                <option value="crediario_lembrete_amanha">Lembrete Amanhã (Crediário)</option>
                                <option value="receita_validade">Validade Receita</option>
                            </select>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="scheduleBatchBtn"
                                {{ empty($eligibles) ? 'disabled' : '' }}>
                                <i class="mdi mdi-calendar-multiple-check me-1"></i>Programar lote
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if (empty($eligibles))
                            <div class="text-muted">Nenhum cliente elegível encontrado para os critérios atuais.</div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>Tipo</th>
                                            <th>Cliente</th>
                                            <th>Telefone</th>
                                            <th class="text-end">Ação</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($eligibles as $e)
                                            <tr data-eligible-tipo="{{ $e['template_tipo'] ?? '' }}">
                                                <td style="white-space: nowrap;">
                                                    <span
                                                        class="badge bg-secondary">{{ $e['template_tipo'] ?? '-' }}</span>
                                                </td>
                                                <td>
                                                    <div class="fw-semibold">{{ $e['cliente'] ?? '-' }}</div>
                                                </td>
                                                <td style="white-space: nowrap;">
                                                    {{ $e['telefone'] ?? '-' }}
                                                </td>
                                                <td class="text-end">
                                                    <button type="button" class="btn btn-sm btn-success"
                                                        data-send-eligible
                                                        data-template-tipo="{{ $e['template_tipo'] ?? '' }}"
                                                        data-referencia-tipo="{{ $e['referencia_tipo'] ?? '' }}"
                                                        data-referencia-id="{{ $e['referencia_id'] ?? '' }}"
                                                        data-pessoa-id="{{ $e['pessoa_id'] ?? '' }}"
                                                        data-telefone="{{ $e['telefone'] ?? '' }}">
                                                        <i class="mdi mdi-whatsapp me-1"></i>Enviar
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="mdi mdi-filter me-2"></i>
                            Filtros e Busca
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <select class="form-select" id="statusFilter">
                                    <option value="">Todos Status</option>
                                    <option value="enviado">Enviado</option>
                                    <option value="programado">Programado</option>
                                    <option value="falhou">Falhou</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" id="tipoFilter">
                                    <option value="">Todos Tipos</option>
                                    <option value="vencimento">Vencimento</option>
                                    <option value="atraso">Atraso</option>
                                    <option value="lembrete">Lembrete</option>
                                    <option value="boleto">Boleto</option>
                                    <option value="personalizada">Personalizada</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="date" class="form-control" id="startDate" placeholder="Data Inicial">
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control" placeholder="Buscar cliente, telefone..."
                                    id="searchInput">
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" id="orderBy">
                                    <option value="recente">Mais Recente</option>
                                    <option value="cliente">Cliente</option>
                                    <option value="tipo">Tipo</option>
                                    <option value="status">Status</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <button class="btn btn-primary w-100" onclick="applyFilters()">
                                    <i class="mdi mdi-magnify"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Notificações -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="mdi mdi-message-text me-2"></i>
                            Histórico de Mensagens ({{ count($notifications) }})
                        </h6>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-danger" onclick="clearHistory()">
                                <i class="mdi mdi-delete me-2"></i>Limpar Histórico
                            </button>
                            <button class="btn btn-sm btn-outline-primary" onclick="exportHistory()">
                                <i class="mdi mdi-download me-2"></i>Exportar
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                            @foreach ($notifications as $notification)
                                <div class="list-group-item">
                                    <div class="row align-items-center">
                                        <div class="col-md-1">
                                            <div
                                                class="avatar-md @if ($notification['tipo'] == 'vencimento') bg-warning @elseif($notification['tipo'] == 'atraso') bg-danger @elseif($notification['tipo'] == 'lembrete') bg-info @else bg-success @endif text-white rounded-circle d-flex align-items-center justify-content-center">
                                                @if ($notification['tipo'] == 'vencimento')
                                                    <i class="mdi mdi-calendar-clock"></i>
                                                @elseif($notification['tipo'] == 'atraso')
                                                    <i class="mdi mdi-alert-circle"></i>
                                                @elseif($notification['tipo'] == 'lembrete')
                                                    <i class="mdi mdi-bell"></i>
                                                @else
                                                    <i class="mdi mdi-file-document"></i>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <strong>{{ $notification['cliente'] }}</strong>
                                            <br><small class="text-muted">{{ $notification['telefone'] }}</small>
                                        </div>
                                        <div class="col-md-1">
                                            <span
                                                class="badge @if ($notification['tipo'] == 'vencimento') bg-warning @elseif($notification['tipo'] == 'atraso') bg-danger @elseif($notification['tipo'] == 'lembrete') bg-info @else bg-success @endif">
                                                {{ ucfirst($notification['tipo']) }}
                                            </span>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="message-preview">
                                                <p class="mb-1 text-truncate">{{ $notification['mensagem'] }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            @if ($notification['status'] == 'enviado')
                                                <span class="badge bg-success">
                                                    <i class="mdi mdi-check-circle me-1"></i>Enviado
                                                </span>
                                                <br><small
                                                    class="text-muted">{{ $notification['enviado_em'] ? \Carbon\Carbon::parse($notification['enviado_em'])->format('d/m H:i') : '' }}</small>
                                                @if ($notification['lido'])
                                                    <br><span class="badge bg-info">
                                                        <i class="mdi mdi-eye me-1"></i>Lido
                                                    </span>
                                                @endif
                                                @if ($notification['respondido'])
                                                    <br><span class="badge bg-primary">
                                                        <i class="mdi mdi-reply me-1"></i>Respondido
                                                    </span>
                                                @endif
                                            @elseif($notification['status'] == 'programado')
                                                <span class="badge bg-warning">
                                                    <i class="mdi mdi-clock-outline me-1"></i>Programado
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    <i class="mdi mdi-close-circle me-1"></i>Falhou
                                                </span>
                                            @endif
                                        </div>
                                        <div class="col-md-2">
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-outline-primary"
                                                    title="Ver Mensagem" data-bs-toggle="modal"
                                                    data-bs-target="#viewMessageModal" data-view-message
                                                    data-id="{{ $notification['id'] }}"
                                                    data-cliente="{{ $notification['cliente'] }}"
                                                    data-telefone="{{ $notification['telefone'] }}"
                                                    data-tipo="{{ $notification['tipo'] }}"
                                                    data-status="{{ $notification['status'] }}"
                                                    data-enviado-em="{{ $notification['enviado_em'] ? \Carbon\Carbon::parse($notification['enviado_em'])->format('d/m/Y H:i') : '' }}"
                                                    data-wa-url="{{ $notification['wa_url'] ?? '' }}"
                                                    data-erro="{{ $notification['erro'] ?? '' }}"
                                                    data-mensagem="{{ $notification['mensagem'] }}">
                                                    <i class="mdi mdi-eye"></i>
                                                </button>
                                                @if ($notification['status'] == 'enviado')
                                                    <button class="btn btn-outline-success" title="Reenviar"
                                                        onclick="resendMessage({{ $notification['id'] }})">
                                                        <i class="mdi mdi-refresh"></i>
                                                    </button>
                                                @endif
                                                @if ($notification['status'] == 'programado')
                                                    <button class="btn btn-outline-warning" title="Editar"
                                                        onclick="editMessage({{ $notification['id'] }})">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger" title="Cancelar"
                                                        onclick="cancelMessage({{ $notification['id'] }})">
                                                        <i class="mdi mdi-close"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">Mostrando {{ count($notifications) }} mensagens</small>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-success" onclick="scheduleReminders()">
                                    <i class="mdi mdi-calendar-plus me-2"></i>Programar Lembretes
                                </button>
                                <button class="btn btn-sm btn-outline-primary" onclick="bulkActions()">
                                    <i class="mdi mdi-cog me-2"></i>Ações em Lote
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="viewMessageModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Ver Mensagem</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <div class="text-muted small">Cliente</div>
                                <div class="fw-semibold" id="viewMsgCliente">-</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Telefone</div>
                                <div class="fw-semibold" id="viewMsgTelefone">-</div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-muted small">Tipo</div>
                                <div class="fw-semibold" id="viewMsgTipo">-</div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-muted small">Status</div>
                                <div class="fw-semibold" id="viewMsgStatus">-</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Enviado em</div>
                                <div class="fw-semibold" id="viewMsgEnviadoEm">-</div>
                            </div>
                        </div>

                        <div class="mb-2 text-muted small">Mensagem</div>
                        <div class="border rounded p-3" id="viewMsgMensagem" style="white-space: pre-wrap;">-</div>

                        <div class="mt-3 d-none" id="viewMsgErroWrap">
                            <div class="mb-2 text-muted small">Erro</div>
                            <div class="alert alert-danger mb-0" id="viewMsgErro"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="#" target="_blank" class="btn btn-success d-none" id="viewMsgOpenWhatsApp">
                            <i class="mdi mdi-whatsapp me-1"></i>Abrir no WhatsApp
                        </a>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>

        @push('styles')
            <style>
                .avatar-md {
                    width: 50px;
                    height: 50px;
                    font-size: 18px;
                }

                .message-preview {
                    max-height: 60px;
                    overflow: hidden;
                }

                .list-group-item {
                    border-left: none;
                    border-right: none;
                }

                .list-group-item:hover {
                    background-color: rgba(0, 123, 255, 0.05);
                }
            </style>
        @endpush

        @push('scripts')
            <script>
                let currentMessageId = null;

                function scrollToEligibles(tipo) {
                    const select = document.getElementById('eligibleTipoFilter');
                    if (select) {
                        select.value = tipo || '';
                        select.dispatchEvent(new Event('change'));
                    }

                    const el = document.getElementById('eligiblesSection');
                    if (el) {
                        el.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                }

                function applyFilters() {
                    alert('Aplicando filtros...');
                }

                function clearHistory() {
                    if (confirm('Limpar todo o histórico de mensagens? Esta ação não pode ser desfeita.')) {
                        alert('Histórico limpo com sucesso!');
                        location.reload();
                    }
                }

                function exportHistory() {
                    alert('Exportando histórico de mensagens...');
                }

                const viewMessageModalEl = document.getElementById('viewMessageModal');
                if (viewMessageModalEl) {
                    viewMessageModalEl.addEventListener('show.bs.modal', function(event) {
                        const btn = event.relatedTarget;
                        if (!btn) {
                            return;
                        }

                        currentMessageId = btn.getAttribute('data-id');

                        const cliente = btn.getAttribute('data-cliente') || '-';
                        const telefone = btn.getAttribute('data-telefone') || '-';
                        const tipo = btn.getAttribute('data-tipo') || '-';
                        const status = btn.getAttribute('data-status') || '-';
                        const enviadoEm = btn.getAttribute('data-enviado-em') || '-';
                        const mensagem = btn.getAttribute('data-mensagem') || '-';
                        const waUrl = btn.getAttribute('data-wa-url') || '';
                        const erro = btn.getAttribute('data-erro') || '';

                        document.getElementById('viewMsgCliente').textContent = cliente;
                        document.getElementById('viewMsgTelefone').textContent = telefone;
                        document.getElementById('viewMsgTipo').textContent = tipo;
                        document.getElementById('viewMsgStatus').textContent = status;
                        document.getElementById('viewMsgEnviadoEm').textContent = enviadoEm;
                        document.getElementById('viewMsgMensagem').textContent = mensagem;

                        const openBtn = document.getElementById('viewMsgOpenWhatsApp');
                        if (waUrl) {
                            openBtn.classList.remove('d-none');
                            openBtn.setAttribute('href', waUrl);
                        } else {
                            openBtn.classList.add('d-none');
                            openBtn.setAttribute('href', '#');
                        }

                        const erroWrap = document.getElementById('viewMsgErroWrap');
                        const erroEl = document.getElementById('viewMsgErro');
                        if (erro) {
                            erroWrap.classList.remove('d-none');
                            erroEl.textContent = erro;
                        } else {
                            erroWrap.classList.add('d-none');
                            erroEl.textContent = '';
                        }
                    });
                }

                function resendMessage(id) {
                    if (!confirm(`Reenviar mensagem ${id}?`)) {
                        return;
                    }

                    const url = '{{ route('financial.notifications.resend', ['id' => '__ID__']) }}'.replace('__ID__', id);
                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    }).then(async (res) => {
                        const data = await res.json();
                        if (!res.ok) {
                            alert(data.message || 'Erro ao reenviar mensagem.');
                            return;
                        }

                        if (data.wa_url) {
                            window.open(data.wa_url, '_blank');
                        }
                        location.reload();
                    }).catch(() => {
                        alert('Erro ao reenviar mensagem.');
                    });
                }

                function editMessage(id) {
                    alert(`Editando mensagem programada ${id}...`);
                }

                function cancelMessage(id) {
                    if (confirm(`Cancelar envio da mensagem ${id}?`)) {
                        alert(`Mensagem ${id} cancelada com sucesso!`);
                        location.reload();
                    }
                }

                function scheduleReminders() {
                    alert('Abrindo agendamento de lembretes automáticos...');
                }

                function bulkActions() {
                    alert('Abrindo ações em lote para mensagens...');
                }

                document.getElementById('searchInput').addEventListener('input', function() {
                    console.log('Buscando mensagens:', this.value);
                });

                const eligibleTipoFilter = document.getElementById('eligibleTipoFilter');
                if (eligibleTipoFilter) {
                    eligibleTipoFilter.addEventListener('change', function() {
                        const tipo = this.value;
                        document.querySelectorAll('#eligiblesSection tr[data-eligible-tipo]').forEach(function(row) {
                            if (!tipo || row.getAttribute('data-eligible-tipo') === tipo) {
                                row.classList.remove('d-none');
                            } else {
                                row.classList.add('d-none');
                            }
                        });
                    });
                }

                document.querySelectorAll('[data-send-eligible]').forEach(function(btn) {
                    btn.addEventListener('click', async function() {
                        const payload = {
                            template_tipo: btn.getAttribute('data-template-tipo'),
                            referencia_tipo: btn.getAttribute('data-referencia-tipo') || null,
                            referencia_id: btn.getAttribute('data-referencia-id') || null,
                            pessoa_id: btn.getAttribute('data-pessoa-id') || null,
                            telefone: btn.getAttribute('data-telefone') || null
                        };

                        const res = await fetch('{{ route('financial.notifications.send') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(payload)
                        });

                        const data = await res.json();
                        if (!res.ok) {
                            alert(data.message || 'Erro ao enviar mensagem.');
                            return;
                        }

                        if (data.wa_url) {
                            window.open(data.wa_url, '_blank');
                        }
                        location.reload();
                    });
                });

                const scheduleBatchBtn = document.getElementById('scheduleBatchBtn');
                if (scheduleBatchBtn) {
                    scheduleBatchBtn.addEventListener('click', async function() {
                        const buttons = Array.from(document.querySelectorAll(
                            '#eligiblesSection tr[data-eligible-tipo]:not(.d-none) [data-send-eligible]'));
                        if (!buttons.length) {
                            alert('Nenhum cliente elegível para o filtro atual.');
                            return;
                        }

                        if (!confirm(`Programar envio em lote para ${buttons.length} cliente(s)?`)) {
                            return;
                        }

                        const items = buttons.map(function(btn) {
                            return {
                                template_tipo: btn.getAttribute('data-template-tipo'),
                                referencia_tipo: btn.getAttribute('data-referencia-tipo') || null,
                                referencia_id: btn.getAttribute('data-referencia-id') || null,
                                pessoa_id: btn.getAttribute('data-pessoa-id') || null,
                                telefone: btn.getAttribute('data-telefone') || null
                            };
                        });

                        scheduleBatchBtn.disabled = true;

                        const res = await fetch('{{ route('financial.notifications.schedule-batch') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                items
                            })
                        });

                        const data = await res.json();
                        if (!res.ok) {
                            scheduleBatchBtn.disabled = false;
                            alert(data.message || 'Erro ao programar lote.');
                            return;
                        }

                        alert(`Programado: ${data.created}. Enfileirado: ${data.queued}. Falhas: ${data.errors}.`);
                        location.reload();
                    });
                }
            </script>
        @endpush
    @endif
@endsection
