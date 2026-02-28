@extends('layouts.app')

@section('title', 'Central de Notificações WhatsApp - Connect Plus')

@section('content')
<div class="d-xl-flex justify-content-between align-items-start mb-4">
    <div>
        <h2 class="text-dark font-weight-bold mb-2">
            <i class="mdi mdi-whatsapp me-2"></i>
            Central de Notificações WhatsApp
        </h2>
        <p class="text-muted mb-0">Automação de lembretes, cobranças e comunicação com clientes</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-success" onclick="configureTemplates()">
            <i class="mdi mdi-cog me-2"></i>Configurar Templates
        </button>
        <button class="btn btn-success" onclick="sendCustomMessage()">
            <i class="mdi mdi-plus me-2"></i>Nova Mensagem
        </button>
    </div>
</div>

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
                            <h3 class="font-weight-medium text-right mb-0 text-dark">{{ count(array_filter($notifications, fn($n) => $n['status'] == 'enviado')) }}</h3>
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
                            <h3 class="font-weight-medium text-right mb-0 text-dark">{{ count(array_filter($notifications, fn($n) => $n['status'] == 'programado')) }}</h3>
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
                            <h3 class="font-weight-medium text-right mb-0 text-dark">{{ count(array_filter($notifications, fn($n) => $n['lido'])) }}</h3>
                        </div>
                        <small class="text-muted">Taxa: {{ round((count(array_filter($notifications, fn($n) => $n['lido'])) / count($notifications)) * 100) }}%</small>
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
                            <h3 class="font-weight-medium text-right mb-0 text-dark">{{ count(array_filter($notifications, fn($n) => $n['respondido'])) }}</h3>
                        </div>
                        <small class="text-muted">Taxa: {{ round((count(array_filter($notifications, fn($n) => $n['respondido'])) / count($notifications)) * 100) }}%</small>
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
                        <button class="btn btn-outline-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3" onclick="sendVencimentoHoje()">
                            <i class="mdi mdi-calendar-clock fs-2 mb-2"></i>
                            <span>Vencimento Hoje</span>
                            <small class="text-muted">8 clientes</small>
                        </button>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-outline-danger w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3" onclick="sendCobrancaAtraso()">
                            <i class="mdi mdi-alert-circle fs-2 mb-2"></i>
                            <span>Cobrança Atraso</span>
                            <small class="text-muted">23 clientes</small>
                        </button>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-outline-info w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3" onclick="sendLembreteAmanha()">
                            <i class="mdi mdi-bell fs-2 mb-2"></i>
                            <span>Lembrete Amanhã</span>
                            <small class="text-muted">12 clientes</small>
                        </button>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-outline-success w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3" onclick="sendBoletosGerados()">
                            <i class="mdi mdi-file-document-outline fs-2 mb-2"></i>
                            <span>Boletos Gerados</span>
                            <small class="text-muted">5 novos</small>
                        </button>
                    </div>
                </div>
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
                        <input type="text" class="form-control" placeholder="Buscar cliente, telefone..." id="searchInput">
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
                    @foreach($notifications as $notification)
                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col-md-1">
                                <div class="avatar-md @if($notification['tipo'] == 'vencimento') bg-warning @elseif($notification['tipo'] == 'atraso') bg-danger @elseif($notification['tipo'] == 'lembrete') bg-info @else bg-success @endif text-white rounded-circle d-flex align-items-center justify-content-center">
                                    @if($notification['tipo'] == 'vencimento')
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
                                <span class="badge @if($notification['tipo'] == 'vencimento') bg-warning @elseif($notification['tipo'] == 'atraso') bg-danger @elseif($notification['tipo'] == 'lembrete') bg-info @else bg-success @endif">
                                    {{ ucfirst($notification['tipo']) }}
                                </span>
                            </div>
                            <div class="col-md-4">
                                <div class="message-preview">
                                    <p class="mb-1 text-truncate">{{ $notification['mensagem'] }}</p>
                                </div>
                            </div>
                            <div class="col-md-2">
                                @if($notification['status'] == 'enviado')
                                    <span class="badge bg-success">
                                        <i class="mdi mdi-check-circle me-1"></i>Enviado
                                    </span>
                                    <br><small class="text-muted">{{ $notification['enviado_em'] ? \Carbon\Carbon::parse($notification['enviado_em'])->format('d/m H:i') : '' }}</small>
                                    @if($notification['lido'])
                                        <br><span class="badge bg-info">
                                            <i class="mdi mdi-eye me-1"></i>Lido
                                        </span>
                                    @endif
                                    @if($notification['respondido'])
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
                                    <button class="btn btn-outline-primary" title="Ver Mensagem" onclick="viewMessage({{ $notification['id'] }})">
                                        <i class="mdi mdi-eye"></i>
                                    </button>
                                    @if($notification['status'] == 'enviado')
                                        <button class="btn btn-outline-success" title="Reenviar" onclick="resendMessage({{ $notification['id'] }})">
                                            <i class="mdi mdi-refresh"></i>
                                        </button>
                                    @endif
                                    @if($notification['status'] == 'programado')
                                        <button class="btn btn-outline-warning" title="Editar" onclick="editMessage({{ $notification['id'] }})">
                                            <i class="mdi mdi-pencil"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" title="Cancelar" onclick="cancelMessage({{ $notification['id'] }})">
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
    background-color: rgba(0,123,255,0.05);
}
</style>
@endpush

@push('scripts')
<script>
let currentMessageId = null;

const messageTemplates = {
    vencimento: "Olá {cliente}! Sua parcela de R$ {valor} vence hoje ({data}). Evite juros pagando até às 23:59h. Qualquer dúvida, estamos à disposição!",
    atraso: "{cliente}, sua parcela está em atraso há {dias} dias. Valor atualizado: R$ {valor}. Regularize para evitar negativação. Link do boleto: {link}",
    boleto: "Oi {cliente}! Seu boleto está disponível. Valor: R$ {valor}, Vencimento: {data}. Link: {link}. Dúvidas? Entre em contato!",
    agradecimento: "Obrigado {cliente}! Recebemos seu pagamento de R$ {valor}. Agradecemos a confiança em nossos serviços!"
};

function sendVencimentoHoje() {
    if (confirm('Enviar lembretes para 8 clientes com parcelas vencendo hoje?')) {
        alert('Enviando lembretes de vencimento via WhatsApp...');
    }
}

function sendCobrancaAtraso() {
    if (confirm('Enviar cobranças para 23 clientes em atraso?')) {
        alert('Enviando cobranças de atraso via WhatsApp...');
    }
}

function sendLembreteAmanha() {
    if (confirm('Enviar lembretes para 12 clientes com vencimento amanhã?')) {
        alert('Enviando lembretes preventivos via WhatsApp...');
    }
}

function sendBoletosGerados() {
    if (confirm('Enviar 5 boletos recém-gerados via WhatsApp?')) {
        alert('Enviando boletos via WhatsApp...');
    }
}

function configureTemplates() {
    alert('Abrindo configuração de templates de mensagem...');
}

function sendCustomMessage() {
    alert('Abrindo formulário para nova mensagem...');
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

function viewMessage(id) {
    currentMessageId = id;
    alert(`Visualizando mensagem ${id}...`);
}

function resendMessage(id) {
    if (confirm(`Reenviar mensagem ${id}?`)) {
        alert(`Mensagem ${id} reenviada com sucesso!`);
    }
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
</script>
@endpush
@endsection
