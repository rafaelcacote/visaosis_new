@extends('layouts.app')

@section('title', 'Gestão de Boletos - Connect Plus')

@section('content')
<div class="d-xl-flex justify-content-between align-items-start mb-4">
    <div>
        <h2 class="text-dark font-weight-bold mb-2">
            <i class="mdi mdi-file-document-outline me-2"></i>
            Gestão de Boletos
        </h2>
        <p class="text-muted mb-0">Geração, envio e controle de boletos bancários</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary" onclick="generateBulkBoletos()">
            <i class="mdi mdi-file-multiple me-2"></i>Gerar em Lote
        </button>
        <button class="btn btn-primary" onclick="generateNewBoleto()">
            <i class="mdi mdi-plus me-2"></i>Novo Boleto
        </button>
    </div>
</div>

<!-- Estatísticas -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card card-statistics">
            <div class="card-body">
                <div class="clearfix">
                    <div class="float-start">
                        <i class="mdi mdi-file-document text-primary icon-lg"></i>
                    </div>
                    <div class="float-end">
                        <p class="mb-0 text-right text-dark">Boletos Gerados</p>
                        <div class="fluid-container">
                            <h3 class="font-weight-medium text-right mb-0 text-dark">{{ count($boletos) }}</h3>
                        </div>
                        <small class="text-muted">Este mês</small>
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
                        <i class="mdi mdi-check-circle text-success icon-lg"></i>
                    </div>
                    <div class="float-end">
                        <p class="mb-0 text-right text-dark">Pagos</p>
                        <div class="fluid-container">
                            <h3 class="font-weight-medium text-right mb-0 text-dark">1</h3>
                        </div>
                        <small class="text-success">R$ 0,00</small>
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
                        <i class="mdi mdi-clock-outline text-warning icon-lg"></i>
                    </div>
                    <div class="float-end">
                        <p class="mb-0 text-right text-dark">Pendentes</p>
                        <div class="fluid-container">
                            <h3 class="font-weight-medium text-right mb-0 text-dark">1</h3>
                        </div>
                        <small class="text-warning">R$ 300,00</small>
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
                        <i class="mdi mdi-alert-circle text-danger icon-lg"></i>
                    </div>
                    <div class="float-end">
                        <p class="mb-0 text-right text-dark">Vencidos</p>
                        <div class="fluid-container">
                            <h3 class="font-weight-medium text-right mb-0 text-dark">1</h3>
                        </div>
                        <small class="text-danger">R$ 149,62</small>
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
                            <option value="pendente">Pendente</option>
                            <option value="pago">Pago</option>
                            <option value="vencido">Vencido</option>
                            <option value="cancelado">Cancelado</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" id="startDate" placeholder="Data Inicial">
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" id="endDate" placeholder="Data Final">
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control" placeholder="Buscar por cliente, CPF, boleto..." id="searchInput">
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" id="orderBy">
                            <option value="vencimento">Vencimento</option>
                            <option value="valor">Valor</option>
                            <option value="cliente">Cliente</option>
                            <option value="gerado">Data Geração</option>
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

<!-- Lista de Boletos -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="mdi mdi-format-list-bulleted me-2"></i>
                    Boletos Bancários ({{ count($boletos) }})
                </h6>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-success" onclick="sendBulkWhatsApp()">
                        <i class="mdi mdi-whatsapp me-2"></i>Enviar Selecionados
                    </button>
                    <button class="btn btn-sm btn-outline-primary" onclick="downloadBulk()">
                        <i class="mdi mdi-download me-2"></i>Download Lote
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="40">
                                    <input type="checkbox" class="form-check-input" id="selectAllBoletos">
                                </th>
                                <th>Boleto</th>
                                <th>Cliente</th>
                                <th>Venda/Parcela</th>
                                <th>Vencimento</th>
                                <th>Valor</th>
                                <th>Status</th>
                                <th>WhatsApp</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($boletos as $boleto)
                            <tr class="@if($boleto['status'] == 'vencido') table-danger @elseif($boleto['status'] == 'pendente') table-warning @elseif($boleto['status'] == 'pago') table-success @endif">
                                <td>
                                    <input type="checkbox" class="form-check-input boleto-checkbox" value="{{ $boleto['id'] }}">
                                </td>
                                <td>
                                    <div>
                                        <strong class="text-primary">{{ $boleto['id'] }}</strong>
                                        <br><small class="text-muted">{{ \Carbon\Carbon::parse($boleto['gerado_em'])->format('d/m/Y') }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm @if($boleto['status'] == 'vencido') bg-danger @elseif($boleto['status'] == 'pendente') bg-warning @else bg-success @endif text-white rounded-circle me-3 d-flex align-items-center justify-content-center">
                                            {{ substr($boleto['cliente'], 0, 2) }}
                                        </div>
                                        <div>
                                            <strong>{{ $boleto['cliente'] }}</strong>
                                            <br><small class="text-muted">{{ $boleto['cpf'] }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <strong>{{ $boleto['venda_id'] }}</strong>
                                    <br><span class="badge bg-secondary">{{ $boleto['parcela'] }}</span>
                                </td>
                                <td>
                                    <span class="@if($boleto['status'] == 'vencido') text-danger fw-bold @elseif(\Carbon\Carbon::parse($boleto['vencimento'])->isToday()) text-warning fw-bold @else text-dark @endif">
                                        {{ \Carbon\Carbon::parse($boleto['vencimento'])->format('d/m/Y') }}
                                    </span>
                                    @if($boleto['status'] == 'vencido')
                                        <br><small class="text-danger">Vencido</small>
                                    @elseif(\Carbon\Carbon::parse($boleto['vencimento'])->isToday())
                                        <br><small class="text-warning">Vence hoje</small>
                                    @endif
                                </td>
                                <td>
                                    @if($boleto['juros'] > 0)
                                        <span class="text-decoration-line-through text-muted">R$ {{ number_format($boleto['valor'], 2, ',', '.') }}</span>
                                        <br><strong class="text-danger">R$ {{ number_format($boleto['valor_total'], 2, ',', '.') }}</strong>
                                        <br><small class="text-danger">+R$ {{ number_format($boleto['juros'], 2, ',', '.') }}</small>
                                    @else
                                        <strong>R$ {{ number_format($boleto['valor_total'], 2, ',', '.') }}</strong>
                                    @endif
                                </td>
                                <td>
                                    @if($boleto['status'] == 'vencido')
                                        <span class="badge bg-danger">
                                            <i class="mdi mdi-alert-circle me-1"></i>Vencido
                                        </span>
                                    @elseif($boleto['status'] == 'pendente')
                                        <span class="badge bg-warning">
                                            <i class="mdi mdi-clock-outline me-1"></i>Pendente
                                        </span>
                                    @elseif($boleto['status'] == 'pago')
                                        <span class="badge bg-success">
                                            <i class="mdi mdi-check-circle me-1"></i>Pago
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($boleto['enviado_whatsapp'])
                                        <span class="badge bg-success">
                                            <i class="mdi mdi-check-circle me-1"></i>Enviado
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="mdi mdi-close-circle me-1"></i>Não Enviado
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" title="Visualizar Boleto" onclick="viewBoleto('{{ $boleto['id'] }}')">
                                            <i class="mdi mdi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-success" title="Download PDF" onclick="downloadBoleto('{{ $boleto['id'] }}')">
                                            <i class="mdi mdi-download"></i>
                                        </button>
                                        @if(!$boleto['enviado_whatsapp'])
                                            <button class="btn btn-outline-success" title="Enviar WhatsApp" onclick="sendBoletoWhatsApp('{{ $boleto['id'] }}')">
                                                <i class="mdi mdi-whatsapp"></i>
                                            </button>
                                        @else
                                            <button class="btn btn-outline-info" title="Reenviar WhatsApp" onclick="resendBoletoWhatsApp('{{ $boleto['id'] }}')">
                                                <i class="mdi mdi-refresh"></i>
                                            </button>
                                        @endif
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                                <i class="mdi mdi-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="#" onclick="copyBarcode('{{ $boleto['codigo_barras'] }}')">
                                                    <i class="mdi mdi-content-copy me-2"></i>Copiar Código de Barras
                                                </a></li>
                                                <li><a class="dropdown-item" href="#" onclick="copyDigitableLine('{{ $boleto['linha_digitavel'] }}')">
                                                    <i class="mdi mdi-numeric me-2"></i>Copiar Linha Digitável
                                                </a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item" href="#" onclick="regenerateBoleto('{{ $boleto['id'] }}')">
                                                    <i class="mdi mdi-refresh me-2"></i>Regerar Boleto
                                                </a></li>
                                                @if($boleto['status'] != 'pago')
                                                <li><a class="dropdown-item text-danger" href="#" onclick="cancelBoleto('{{ $boleto['id'] }}')">
                                                    <i class="mdi mdi-close-circle me-2"></i>Cancelar Boleto
                                                </a></li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">Mostrando {{ count($boletos) }} boletos</small>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-primary" onclick="exportBoletos()">
                            <i class="mdi mdi-file-excel me-2"></i>Exportar Excel
                        </button>
                        <button class="btn btn-sm btn-outline-success" onclick="generateReport()">
                            <i class="mdi mdi-chart-line me-2"></i>Relatório
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.avatar-sm {
    width: 40px;
    height: 40px;
    font-size: 14px;
    font-weight: bold;
}
</style>
@endpush

@push('scripts')
<script>
let currentBoletoId = null;

function generateBulkBoletos() {
    alert('Gerando boletos em lote para parcelas vencendo...');
}

function generateNewBoleto() {
    alert('Abrindo formulário para novo boleto...');
}

function applyFilters() {
    alert('Aplicando filtros...');
}

function sendBulkWhatsApp() {
    const selected = document.querySelectorAll('.boleto-checkbox:checked');
    if (selected.length === 0) {
        alert('Selecione pelo menos um boleto.');
        return;
    }
    alert(`Enviando ${selected.length} boletos via WhatsApp...`);
}

function downloadBulk() {
    const selected = document.querySelectorAll('.boleto-checkbox:checked');
    if (selected.length === 0) {
        alert('Selecione pelo menos um boleto.');
        return;
    }
    alert(`Baixando ${selected.length} boletos em PDF...`);
}

function viewBoleto(id) {
    currentBoletoId = id;
    const url = `/financial/boleto-pdf/${id}`;
    window.open(url, '_blank', 'width=800,height=600,scrollbars=yes');
}

function downloadBoleto(id) {
    const url = `/financial/boleto-pdf/${id}?print=1`;
    window.open(url, '_blank', 'width=800,height=600,scrollbars=yes');
}

function sendBoletoWhatsApp(id) {
    if (confirm(`Enviar boleto ${id} via WhatsApp?`)) {
        alert(`Boleto ${id} enviado via WhatsApp com sucesso!`);
        location.reload();
    }
}

function resendBoletoWhatsApp(id) {
    if (confirm(`Reenviar boleto ${id} via WhatsApp?`)) {
        alert(`Boleto ${id} reenviado via WhatsApp com sucesso!`);
    }
}

function copyBarcode(codigo) {
    navigator.clipboard.writeText(codigo).then(() => {
        alert('Código de barras copiado para a área de transferência!');
    });
}

function copyDigitableLine(linha) {
    navigator.clipboard.writeText(linha).then(() => {
        alert('Linha digitável copiada para a área de transferência!');
    });
}

function regenerateBoleto(id) {
    if (confirm(`Regerar boleto ${id}? O boleto anterior será cancelado.`)) {
        alert(`Boleto ${id} regerado com sucesso!`);
        location.reload();
    }
}

function cancelBoleto(id) {
    if (confirm(`Cancelar boleto ${id}? Esta ação não pode ser desfeita.`)) {
        alert(`Boleto ${id} cancelado com sucesso!`);
        location.reload();
    }
}

function exportBoletos() {
    alert('Exportando lista de boletos para Excel...');
}

function generateReport() {
    alert('Gerando relatório de boletos...');
}

document.getElementById('selectAllBoletos').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.boleto-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

document.getElementById('searchInput').addEventListener('input', function() {
    console.log('Buscando boletos:', this.value);
});
</script>
@endpush
@endsection
