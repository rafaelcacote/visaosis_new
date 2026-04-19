@extends('layouts.app')

@section('title', 'Nova Ordem de Serviço - Connect Plus')

@section('content')
    <div class="d-xl-flex justify-content-between align-items-start mb-4">
        <div>
            <h2 class="text-dark font-weight-bold mb-2">
                <i class="mdi mdi-cog-plus me-2"></i>
                Nova Ordem de Serviço
            </h2>
            <p class="text-muted mb-0">Criar uma nova ordem de serviço para produção</p>
        </div>
        <a href="{{ route('ordens-servico.index') }}" class="btn btn-outline-secondary">
            <i class="mdi mdi-arrow-left me-2"></i>
            Voltar
        </a>
    </div>

    <div class="row">
        <!-- Seleção de Cliente e Vendas -->
        <div class="col-lg-6">
            <!-- Seleção de Cliente -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="mdi mdi-account text-primary me-2"></i>
                        Selecionar Cliente
                    </h5>
                    <div class="form-group">
                        <label for="client_search" class="form-label">Buscar Cliente ou Venda</label>
                        <div id="client_search_container" class="position-relative">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="mdi mdi-magnify"></i>
                                </span>
                                <input type="text" class="form-control" id="client_search"
                                    placeholder="Digite nome, CPF, e-mail ou ID da venda..." autocomplete="off">
                            </div>
                            <div id="client_suggestions" class="list-group position-absolute w-100 shadow-sm"
                                style="display: none; z-index: 1000;">
                            </div>
                        </div>
                        <small class="text-muted">Digite pelo menos 2 caracteres para pesquisar.</small>
                    </div>

                    <div id="selected_client" class="mt-3" style="display: none;">
                        <div class="alert alert-info d-flex align-items-center">
                            <i class="mdi mdi-account-check me-2"></i>
                            <div>
                                <strong id="client_name"></strong><br>
                                <small id="client_document" class="d-block"></small>
                                <small id="client_contact" class="d-block"></small>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary ms-auto" onclick="clearClient()">
                                <i class="mdi mdi-close"></i>
                            </button>
                        </div>

                        <!-- Vendas do Cliente Integradas -->
                        <div class="mt-3">
                            <h6 class="text-success mb-3">
                                <i class="mdi mdi-receipt me-2"></i>
                                Vendas do Cliente
                            </h6>
                            <div id="vendas_list" class="row">
                                <!-- Vendas serão listadas aqui -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Busca de Prescrições -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="mdi mdi-glasses text-info me-2"></i>
                        Buscar Prescrição (opcional)
                    </h5>
                    <div class="form-group">
                        <label for="prescricao_search" class="form-label">Buscar Prescrição</label>
                        <div id="prescricao_search_container" class="position-relative">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="mdi mdi-magnify"></i>
                                </span>
                                <input type="text" class="form-control" id="prescricao_search"
                                    placeholder="Digite ID da prescrição ou nome do paciente..." autocomplete="off">
                            </div>
                            <div id="prescricao_suggestions" class="list-group position-absolute w-100 shadow-sm"
                                style="display: none; z-index: 1000;">
                            </div>
                        </div>
                        <small class="text-muted">Digite pelo menos 2 caracteres para pesquisar.</small>
                    </div>

                    <div id="selected_prescricao" class="mt-3" style="display: none;">
                        <div class="alert alert-info d-flex align-items-start">
                            <i class="mdi mdi-glasses me-2"></i>
                            <div class="flex-grow-1">
                                <strong>Prescrição #<span id="prescricao_id_display"></span></strong><br>
                                <small class="d-block text-muted">Paciente: <span
                                        id="prescricao_paciente_nome"></span></small>
                                <small class="d-block text-muted">Data: <span id="prescricao_data"></span></small>
                                <small class="d-block text-muted">CPF: <span id="prescricao_paciente_cpf"></span></small>
                                <div class="mt-2" id="prescricao_graduacao_info">
                                    <!-- Informações da graduação serão exibidas aqui -->
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary ms-auto"
                                onclick="clearPrescricao()">
                                <i class="mdi mdi-close"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulário da Ordem -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="mdi mdi-cog text-primary me-2"></i>
                        Dados da Ordem de Serviço
                    </h5>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('ordens-servico.store') }}" method="POST" id="ordemForm">
                        @csrf
                        <input type="hidden" name="pedido_id" id="pedido_id">
                        <input type="hidden" name="prescricao_id" id="prescricao_id" value="">

                        <!-- Venda Selecionada -->
                        <div id="venda_selecionada" style="display: none;" class="mb-4">
                            <label class="form-label">Venda Selecionada</label>
                            <div class="alert alert-light border" id="info_venda_selecionada">
                                <!-- Info da venda será exibida aqui -->
                            </div>

                            <!-- Exibir erros de validação para itens -->
                            @error('itens_selecionados')
                                <div class="alert alert-danger">
                                    <i class="mdi mdi-alert-circle me-2"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Prescrição Selecionada -->
                        <div id="prescricao_selecionada_ordem" style="display: none;" class="mb-4">
                            <label class="form-label">Prescrição Selecionada</label>
                            <div class="alert alert-info border d-flex align-items-start">
                                <i class="mdi mdi-glasses me-2 mt-1"></i>
                                <div class="flex-grow-1" id="info_prescricao_selecionada">
                                    <!-- Info da prescrição será exibida aqui -->
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary ms-2"
                                    onclick="clearPrescricaoFromOrdem()">
                                    <i class="mdi mdi-close"></i>
                                </button>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Fornecedor -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="fornecedor_id" class="form-label">
                                        Fornecedor/Laboratório <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select form-select-sm @error('fornecedor_id') is-invalid @enderror"
                                        id="fornecedor_id" name="fornecedor_id" required>
                                        <option value="">Selecione um fornecedor</option>
                                        @foreach ($fornecedores as $fornecedor)
                                            <option value="{{ $fornecedor->id }}"
                                                {{ old('fornecedor_id') == $fornecedor->id ? 'selected' : '' }}>
                                                {{ $fornecedor->razao_social }}
                                                @if ($fornecedor->nome_fantasia)
                                                    ({{ $fornecedor->nome_fantasia }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('fornecedor_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <!-- Quantidade -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="quantidade" class="form-label">
                                        Quantidade <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control @error('quantidade') is-invalid @enderror"
                                        id="quantidade" name="quantidade" value="{{ old('quantidade', 1) }}"
                                        min="1" required>
                                    @error('quantidade')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <!-- Prioridade -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="prioridade" class="form-label">
                                        Prioridade <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select form-select-sm @error('prioridade') is-invalid @enderror"
                                        id="prioridade" name="prioridade" required>
                                        <option value="normal" {{ old('prioridade') == 'normal' ? 'selected' : '' }}>
                                            Normal
                                        </option>
                                        <option value="urgente" {{ old('prioridade') == 'urgente' ? 'selected' : '' }}>
                                            Urgente
                                        </option>
                                        <option value="expressa" {{ old('prioridade') == 'expressa' ? 'selected' : '' }}>
                                            Expressa</option>
                                    </select>
                                    @error('prioridade')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">

                            <!-- Preço Unitário -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="preco_unit" class="form-label">
                                        Preço Unitário <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="number"
                                            class="form-control @error('preco_unit') is-invalid @enderror" id="preco_unit"
                                            name="preco_unit" value="{{ old('preco_unit') }}" step="0.01"
                                            min="0" required>
                                    </div>
                                    @error('preco_unit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Desconto -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="desconto" class="form-label">Desconto</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="number" class="form-control @error('desconto') is-invalid @enderror"
                                            id="desconto" name="desconto" value="{{ old('desconto', 0) }}"
                                            step="0.01" min="0">
                                    </div>
                                    @error('desconto')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <!-- Total -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="total_linha" class="form-label">Total</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="text" class="form-control" id="total_linha" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="row mt-3">
                            <!-- Observações -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="entrega_em" class="form-label">Data de Entrega</label>
                                    <input type="datetime-local"
                                        class="form-control @error('entrega_em') is-invalid @enderror" id="entrega_em"
                                        name="entrega_em" value="{{ old('entrega_em') }}"
                                        min="{{ date('Y-m-d\TH:i') }}">
                                    @error('entrega_em')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <!-- Observações -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="observacoes" class="form-label">Observações</label>
                                    <textarea class="form-control @error('observacoes') is-invalid @enderror" id="observacoes" name="observacoes"
                                        rows="3" placeholder="Observações adicionais sobre a ordem de serviço...">{{ old('observacoes') }}</textarea>
                                    @error('observacoes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="mdi mdi-information-outline me-2"></i>
                                    <strong>Informação:</strong>
                                    Selecione primeiro um cliente e uma venda para criar a ordem de serviço.
                                    Os campos marcados com <span class="text-danger">*</span> são obrigatórios.
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('ordens-servico.index') }}" class="btn btn-secondary">
                                        <i class="mdi mdi-close-circle me-2"></i>
                                        Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary" id="btnSalvar" disabled>
                                        <i class="mdi mdi-check-circle me-2"></i>
                                        Criar Ordem de Serviço
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de aviso (validação) -->
    <div class="modal fade" id="ordemValidacaoModal" tabindex="-1" aria-labelledby="ordemValidacaoModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title text-warning" id="ordemValidacaoModalLabel">
                        <i class="mdi mdi-alert-circle-outline me-2"></i>
                        Atenção
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body pt-2">
                    <p class="text-muted mb-0" id="ordemValidacaoModalMessage"></p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                        <i class="mdi mdi-check me-1"></i>
                        Entendi
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmação de criação -->
    <div class="modal fade" id="confirmCriarOrdemModal" tabindex="-1" aria-labelledby="confirmCriarOrdemModalLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="confirmCriarOrdemModalLabel">
                        <i class="mdi mdi-check-circle me-2"></i>
                        Confirmar ordem de serviço
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-4 small">Revise os dados antes de criar a ordem de serviço.</p>

                    <!-- Cliente e Venda -->
                    <div class="mb-4">
                        <h6 class="text-dark mb-3 fw-bold">Cliente e Venda</h6>
                        <div class="row g-3">
                            <div class="col-6">
                                <small class="text-muted d-block mb-1">Cliente</small>
                                <strong id="confirm_os_cliente">—</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block mb-1">Venda</small>
                                <strong id="confirm_os_venda">—</strong>
                            </div>
                        </div>
                    </div>

                    <!-- ID da Prescrição -->
                    <div class="mb-4" id="confirm_os_prescricao_container" style="display: none;">
                        <h6 class="text-dark mb-3 fw-bold">Prescrição</h6>
                        <div class="row">
                            <div class="col-12">
                                <small class="text-muted d-block mb-1">ID da Prescrição</small>
                                <strong id="confirm_os_prescricao">—</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Informações da Ordem de Serviço -->
                    <div class="mb-4">
                        <h6 class="text-dark mb-3 fw-bold">Ordem de Serviço</h6>
                        <div class="row g-3">
                            <div class="col-4">
                                <small class="text-muted d-block mb-1">Fornecedor</small>
                                <strong id="confirm_os_fornecedor">—</strong>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block mb-1">Itens</small>
                                <strong id="confirm_os_itens">—</strong>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block mb-1">Quantidade</small>
                                <strong id="confirm_os_quantidade">—</strong>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block mb-1">Prioridade</small>
                                <strong class="text-capitalize" id="confirm_os_prioridade">—</strong>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block mb-1">Data de Entrega</small>
                                <strong id="confirm_os_entrega">—</strong>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block mb-1">Total</small>
                                <strong class="text-success" id="confirm_os_total">—</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Itens Detalhados -->
                    <div class="mb-4" id="confirm_os_itens_detalhes_container" style="display: none;">
                        <h6 class="text-dark mb-3 fw-bold">Produtos Selecionados</h6>
                        <div class="bg-light p-3 rounded" style="max-height: 120px; overflow-y: auto;">
                            <div id="confirm_os_itens_detalhes"></div>
                        </div>
                    </div>

                    <!-- Observações -->
                    <div class="mb-3" id="confirm_os_observacoes_container" style="display: none;">
                        <h6 class="text-dark mb-3 fw-bold">Observações</h6>
                        <div class="bg-light p-3 rounded">
                            <small id="confirm_os_observacoes"></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="mdi mdi-close-circle me-1"></i>
                        Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" id="btnConfirmarCriarOrdem">
                        <i class="mdi mdi-check-circle me-1"></i>
                        Criar ordem de serviço
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .venda-card {
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
        }

        .venda-card:hover {
            border-color: #1f4e79;
            box-shadow: 0 4px 8px rgba(31, 78, 121, 0.15);
            transform: translateY(-2px);
        }

        .venda-card.selected {
            border-color: #1f4e79;
            background-color: #f0f7ff;
            box-shadow: 0 4px 12px rgba(31, 78, 121, 0.2);
        }

        .client-suggestions {
            max-height: 250px;
            overflow-y: auto;
            z-index: 1000;
            border-radius: 4px;
        }

        .client-suggestions .list-group-item {
            border-left: none;
            border-right: none;
        }

        .client-suggestions .list-group-item:first-child {
            border-top-left-radius: 4px;
            border-top-right-radius: 4px;
        }

        .client-suggestions .list-group-item:last-child {
            border-bottom-left-radius: 4px;
            border-bottom-right-radius: 4px;
        }

        .form-check:hover {
            background-color: #f8f9fa;
        }

        .form-check-input:checked+.form-check-label {
            background-color: #f0f7ff;
        }

        .item-checkbox {
            cursor: pointer;
            transition: all 0.2s;
            border-radius: 6px;
        }

        .item-checkbox:hover {
            border-color: #1f4e79;
            background-color: #f8f9fa;
        }

        .prescricao-suggestions {
            max-height: 300px;
            overflow-y: auto;
            z-index: 1000;
            border-radius: 4px;
        }

        .prescricao-card {
            cursor: pointer;
            transition: all 0.2s;
        }

        .prescricao-card:hover {
            background-color: #f8f9fa;
        }

        .card-title {
            font-weight: 600;
            color: #2c3e50;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .alert {
            border-radius: 6px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        let selectedClient = null;
        let selectedVenda = null;
        let selectedPrescricao = null;
        let clientSearchTimeout = null;
        let vendaSearchTimeout = null;
        let prescricaoSearchTimeout = null;

        const CLIENT_SEARCH_URL = "{{ route('ordens-servico.buscar-clientes') }}";
        const VENDAS_SEARCH_URL = "{{ route('ordens-servico.buscar-vendas-cliente') }}";
        const PRESCRICOES_SEARCH_URL = "{{ route('ordens-servico.buscar-prescricoes') }}";

        let ordemFormSubmitSkipConfirm = false;
        let validacaoModalInstance = null;
        let confirmOrdemModalInstance = null;

        function getValidacaoModal() {
            if (!validacaoModalInstance) {
                validacaoModalInstance = new bootstrap.Modal(document.getElementById('ordemValidacaoModal'));
            }
            return validacaoModalInstance;
        }

        function getConfirmOrdemModal() {
            if (!confirmOrdemModalInstance) {
                confirmOrdemModalInstance = new bootstrap.Modal(document.getElementById('confirmCriarOrdemModal'));
            }
            return confirmOrdemModalInstance;
        }

        function showOrdemValidacao(message) {
            document.getElementById('ordemValidacaoModalMessage').textContent = message;
            getValidacaoModal().show();
        }

        function prioridadeLabel(value) {
            const map = {
                normal: 'Normal',
                urgente: 'Urgente',
                expressa: 'Expressa'
            };
            return map[value] || value || '—';
        }

        // Busca de clientes
        document.getElementById('client_search').addEventListener('input', function() {
            const term = this.value.trim();
            clearTimeout(clientSearchTimeout);

            if (term.length < 2) {
                hideClientSuggestions();
                return;
            }

            clientSearchTimeout = setTimeout(() => fetchClients(term), 300);
        });

        async function fetchClients(query) {
            try {
                const response = await fetch(`${CLIENT_SEARCH_URL}?q=${encodeURIComponent(query)}`);
                if (!response.ok) throw new Error('Erro ao buscar clientes');

                const clients = await response.json();
                renderClientSuggestions(clients);
            } catch (error) {
                console.error(error);
                renderClientSuggestions([]);
            }
        }

        function renderClientSuggestions(results) {
            const suggestions = document.getElementById('client_suggestions');

            if (!results.length) {
                suggestions.innerHTML = '<div class="list-group-item text-muted">Nenhum cliente ou venda encontrado.</div>';
            } else {
                suggestions.innerHTML = results.map(result => {
                    if (result.type === 'venda') {
                        return `
                            <button type="button" class="list-group-item list-group-item-action" onclick="selectClient(${JSON.stringify(result).replace(/"/g, '&quot;')})">
                                <div class="fw-semibold">
                                    <i class="mdi mdi-receipt text-success me-2"></i>
                                    Venda #${result.venda_numero} - ${result.nome}
                                </div>
                                <small class="text-muted">
                                    ${result.cpf || 'CPF não informado'} • ${result.data_pedido} • ${result.valor_total}
                                    ${result.telefone ? ` • ${result.telefone}` : ''}
                                </small>
                                ${result.email ? `<small class="text-muted d-block">${result.email}</small>` : ''}
                            </button>
                        `;
                    } else {
                        return `
                            <button type="button" class="list-group-item list-group-item-action" onclick="selectClient(${JSON.stringify(result).replace(/"/g, '&quot;')})">
                                <div class="fw-semibold">
                                    <i class="mdi mdi-account text-primary me-2"></i>
                                    ${result.nome}
                                </div>
                                <small class="text-muted">
                                    ${result.cpf || 'CPF não informado'}
                                    ${result.telefone ? ` • ${result.telefone}` : ''}
                                </small>
                                ${result.email ? `<small class="text-muted d-block">${result.email}</small>` : ''}
                            </button>
                        `;
                    }
                }).join('');
            }

            suggestions.style.display = 'block';
        }

        function hideClientSuggestions() {
            document.getElementById('client_suggestions').style.display = 'none';
        }

        function selectClient(client) {
            selectedClient = client;
            selectedVenda = null; // Reset venda selecionada

            // Determinar o nome para exibição baseado no tipo
            const displayName = client.type === 'venda' ?
                `${client.nome} (via Venda #${client.venda_numero})` :
                client.nome;

            document.getElementById('client_search').value = displayName;
            document.getElementById('client_name').textContent = client.nome;
            document.getElementById('client_document').textContent = client.cpf ? `CPF: ${client.cpf}` : '';
            document.getElementById('client_contact').textContent = client.telefone || '';

            document.getElementById('selected_client').style.display = 'block';
            document.getElementById('venda_selecionada').style.display = 'none';
            document.getElementById('pedido_id').value = '';
            document.getElementById('btnSalvar').disabled = true;

            hideClientSuggestions();

            // Se foi selecionado via venda específica, mostrar apenas essa venda
            if (client.type === 'venda') {
                loadVendaEspecifica(client.venda_id);
            } else {
                // Se foi selecionado cliente diretamente, carregar todas as vendas
                loadVendasCliente(client.id);
            }
        }

        // Nova função para carregar uma venda específica
        async function loadVendaEspecifica(vendaId) {
            try {
                // Buscar apenas a venda específica
                const response = await fetch(`${VENDAS_SEARCH_URL}?cliente_id=${selectedClient.id}&termo=${vendaId}`);
                if (!response.ok) throw new Error('Erro ao buscar venda');

                const vendas = await response.json();
                const vendaEspecifica = vendas.find(v => v.id == vendaId);

                if (vendaEspecifica) {
                    renderVendas([vendaEspecifica]); // Renderizar apenas esta venda
                } else {
                    document.getElementById('vendas_list').innerHTML =
                        '<div class="col-12"><div class="alert alert-warning">Venda não encontrada.</div></div>';
                }
            } catch (error) {
                console.error(error);
                document.getElementById('vendas_list').innerHTML =
                    '<div class="col-12"><div class="alert alert-warning">Erro ao carregar venda específica.</div></div>';
            }
        }

        function clearClient() {
            selectedClient = null;
            selectedVenda = null;
            document.getElementById('client_search').value = '';
            document.getElementById('selected_client').style.display = 'none';
            document.getElementById('venda_selecionada').style.display = 'none';
            document.getElementById('pedido_id').value = '';
            document.getElementById('btnSalvar').disabled = true;
        }

        // Busca de prescrições
        document.getElementById('prescricao_search').addEventListener('input', function() {
            const term = this.value.trim();
            clearTimeout(prescricaoSearchTimeout);

            if (term.length < 2) {
                hidePrescricaoSuggestions();
                return;
            }

            prescricaoSearchTimeout = setTimeout(() => fetchPrescricoes(term), 300);
        });

        async function fetchPrescricoes(query) {
            try {
                const response = await fetch(`${PRESCRICOES_SEARCH_URL}?q=${encodeURIComponent(query)}`);
                if (!response.ok) throw new Error('Erro ao buscar prescrições');

                const prescricoes = await response.json();
                renderPrescricaoSuggestions(prescricoes);
            } catch (error) {
                console.error(error);
                renderPrescricaoSuggestions([]);
            }
        }

        function renderPrescricaoSuggestions(prescricoes) {
            const suggestions = document.getElementById('prescricao_suggestions');

            if (!prescricoes.length) {
                suggestions.innerHTML = '<div class="list-group-item text-muted">Nenhuma prescrição encontrada.</div>';
            } else {
                suggestions.innerHTML = prescricoes.map(prescricao => `
                    <button type="button" class="list-group-item list-group-item-action" onclick="selectPrescricao(${JSON.stringify(prescricao).replace(/"/g, '&quot;')})">
                        <div class="fw-semibold">Prescrição #${prescricao.id} - ${prescricao.paciente_nome}</div>
                        <small class="text-muted">
                            Data: ${prescricao.data_criacao} • CPF: ${prescricao.paciente_cpf || 'Não informado'}
                        </small>
                        <div class="mt-1">
                            <small class="text-info">
                                OD: ${prescricao.graduacao.od.esfera || '0.00'}/${prescricao.graduacao.od.cilindro || '0.00'}/${prescricao.graduacao.od.eixo || '0'} •
                                OE: ${prescricao.graduacao.oe.esfera || '0.00'}/${prescricao.graduacao.oe.cilindro || '0.00'}/${prescricao.graduacao.oe.eixo || '0'}
                            </small>
                        </div>
                        ${prescricao.diagnostico ? `<small class="text-muted d-block">Diagnóstico: ${prescricao.diagnostico}</small>` : ''}
                    </button>
                `).join('');
            }

            suggestions.style.display = 'block';
        }

        function hidePrescricaoSuggestions() {
            document.getElementById('prescricao_suggestions').style.display = 'none';
        }

        function selectPrescricao(prescricao) {
            selectedPrescricao = prescricao;

            document.getElementById('prescricao_search').value = `#${prescricao.id} - ${prescricao.paciente_nome}`;
            document.getElementById('prescricao_id_display').textContent = prescricao.id;
            document.getElementById('prescricao_paciente_nome').textContent = prescricao.paciente_nome;
            document.getElementById('prescricao_data').textContent = prescricao.data_criacao;
            document.getElementById('prescricao_paciente_cpf').textContent = prescricao.paciente_cpf || 'Não informado';
            document.getElementById('prescricao_id').value = prescricao.id;

            // Exibir informações da graduação
            const graduacaoInfo = `
                <div class="row">
                    <div class="col-6">
                        <strong>Olho Direito (OD):</strong><br>
                        <small>Esfera: ${prescricao.graduacao.od.esfera || '0.00'}</small><br>
                        <small>Cilindro: ${prescricao.graduacao.od.cilindro || '0.00'}</small><br>
                        <small>Eixo: ${prescricao.graduacao.od.eixo || '0'}°</small>
                    </div>
                    <div class="col-6">
                        <strong>Olho Esquerdo (OE):</strong><br>
                        <small>Esfera: ${prescricao.graduacao.oe.esfera || '0.00'}</small><br>
                        <small>Cilindro: ${prescricao.graduacao.oe.cilindro || '0.00'}</small><br>
                        <small>Eixo: ${prescricao.graduacao.oe.eixo || '0'}°</small>
                    </div>
                </div>
                ${prescricao.diagnostico ? `<div class="mt-2"><strong>Diagnóstico:</strong><br><small>${prescricao.diagnostico}</small></div>` : ''}
                ${prescricao.observacoes ? `<div class="mt-2"><strong>Observações:</strong><br><small>${prescricao.observacoes}</small></div>` : ''}
            `;

            document.getElementById('prescricao_graduacao_info').innerHTML = graduacaoInfo;
            document.getElementById('selected_prescricao').style.display = 'block';

            // Atualizar também o campo no card "Dados da Ordem de Serviço"
            const prescricaoInfoOrdem = `
                <strong>Prescrição #${prescricao.id} - ${prescricao.paciente_nome}</strong><br>
                <small class="text-muted">Data: ${prescricao.data_criacao} • CPF: ${prescricao.paciente_cpf || 'Não informado'}</small><br>
                <small class="text-info">
                    OD: ${prescricao.graduacao.od.esfera || '0.00'}/${prescricao.graduacao.od.cilindro || '0.00'}/${prescricao.graduacao.od.eixo || '0'}° •
                    OE: ${prescricao.graduacao.oe.esfera || '0.00'}/${prescricao.graduacao.oe.cilindro || '0.00'}/${prescricao.graduacao.oe.eixo || '0'}°
                </small>
            `;

            document.getElementById('info_prescricao_selecionada').innerHTML = prescricaoInfoOrdem;
            document.getElementById('prescricao_selecionada_ordem').style.display = 'block';

            hidePrescricaoSuggestions();
        }

        function clearPrescricao() {
            selectedPrescricao = null;
            document.getElementById('prescricao_search').value = '';
            document.getElementById('selected_prescricao').style.display = 'none';
            document.getElementById('prescricao_id').value = '';

            // Limpar também o campo no card "Dados da Ordem de Serviço"
            document.getElementById('prescricao_selecionada_ordem').style.display = 'none';
            document.getElementById('info_prescricao_selecionada').innerHTML = '';
        }

        function clearPrescricaoFromOrdem() {
            clearPrescricao();
        }

        // Busca vendas do cliente
        async function loadVendasCliente(clienteId, termo = '') {
            try {
                const response = await fetch(
                    `${VENDAS_SEARCH_URL}?cliente_id=${clienteId}&termo=${encodeURIComponent(termo)}`);
                if (!response.ok) throw new Error('Erro ao buscar vendas');

                const vendas = await response.json();
                renderVendas(vendas);
            } catch (error) {
                console.error(error);
                document.getElementById('vendas_list').innerHTML =
                    '<div class="col-12"><div class="alert alert-warning">Erro ao carregar vendas.</div></div>';
            }
        }

        function renderVendas(vendas) {
            const vendasList = document.getElementById('vendas_list');

            if (!vendas.length) {
                vendasList.innerHTML =
                    '<div class="col-12"><div class="alert alert-info">Nenhuma venda faturada encontrada para este cliente.</div></div>';
                return;
            }

            vendasList.innerHTML = vendas.map(venda => `
                <div class="col-md-6 mb-3">
                    <div class="card venda-card h-100" onclick="selectVenda(${JSON.stringify(venda).replace(/"/g, '&quot;')})">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="card-title mb-0">Venda #${venda.numero}</h6>
                                <span class="badge bg-success">${venda.status}</span>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Data:</small> ${venda.data_pedido}<br>
                                <small class="text-muted">Valor:</small> <strong>${venda.valor_total}</strong><br>
                                <small class="text-muted">Pagamento:</small> ${venda.forma_pagamento}
                            </div>
                            <div class="border-top pt-2">
                                <small class="text-muted d-block mb-1">
                                    <i class="mdi mdi-package me-1"></i>
                                    ${venda.quantidade_itens} tipo(s) de produto • ${venda.quantidade_produtos} unidade(s)
                                </small>
                                <div class="itens-preview" style="max-height: 60px; overflow-y: auto; font-size: 0.8em;">
                                    ${venda.itens.slice(0, 3).map(item => `
                                                                                                                                                                    <div class="text-muted">• ${item.produto_nome} (${item.quantidade}x)</div>
                                                                                                                                                                `).join('')}
                                    ${venda.itens.length > 3 ? `<div class="text-muted">... e mais ${venda.itens.length - 3} produto(s)</div>` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function selectVenda(venda) {
            selectedVenda = venda;

            // Marcar como selecionada
            document.querySelectorAll('.venda-card').forEach(card => card.classList.remove('selected'));
            event.target.closest('.venda-card').classList.add('selected');

            // Preencher dados
            document.getElementById('pedido_id').value = venda.id;

            // Criar lista de itens para exibição
            const itensHtml = venda.itens.map(item => `
                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                    <div>
                        <strong>${item.produto_nome}</strong><br>
                        <small class="text-muted">Qtd: ${item.quantidade} • Preço Unit.: ${item.preco_unit}</small>
                    </div>
                    <div class="text-end">
                        <strong>${item.total_linha}</strong>
                        ${item.desconto_raw > 0 ? `<br><small class="text-success">Desc: ${item.desconto}</small>` : ''}
                    </div>
                </div>
            `).join('');

            // Mostrar info da venda selecionada com itens
            document.getElementById('info_venda_selecionada').innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <strong>Venda #${venda.numero}</strong><br>
                        <small>Data: ${venda.data_pedido} • ${venda.forma_pagamento}</small><br>
                        <small>Status: <span class="badge bg-success">${venda.status}</span></small>
                    </div>
                    <div class="col-md-6 text-end">
                        <strong class="fs-5">${venda.valor_total}</strong><br>
                        <small class="text-muted">${venda.quantidade_itens} produto(s) • ${venda.quantidade_produtos} unidade(s)</small>
                    </div>
                </div>
                <div class="mt-3">
                    <h6 class="border-bottom pb-2 mb-2">
                        Selecionar Itens para Ordem de Serviço:
                        <small class="text-muted">(marque os itens que serão incluídos)</small>
                    </h6>
                    <div style="max-height: 60vh; overflow-y: auto; overflow-x: hidden; width: 100%;">
                        ${venda.itens.map(item => `
                                                                                                                                                    <div class="border rounded p-2 mb-2 item-checkbox d-flex align-items-start" style="cursor: pointer;">
                                                                                                                                                        <input class="form-check-input me-3" type="checkbox" name="itens_selecionados[]"
                                                                                                                                                               value="${item.id}" id="item_${item.id}" onchange="updateSelectedItems()" style="margin-top: 2px;">
                                                                                                                                                        <div class="flex-grow-1">
                                                                                                                                                            <label class="form-check-label w-100" for="item_${item.id}" style="cursor: pointer;">
                                                                                                                                                                <strong style="display: block; font-size: 0.9rem; line-height: 1.3; margin-bottom: 2px;">${item.produto_nome}</strong>
                                                                                                                                                                <small class="text-muted" style="font-size: 0.75rem; line-height: 1.2;">
                                                                                                                                                                    Qtd: ${item.quantidade} • Preço Unit.: ${item.preco_unit}
                                                                                                                                                                    ${item.desconto_raw > 0 ? ` • Desc: ${item.desconto}` : ''}
                                                                                                                                                                </small>
                                                                                                                                                            </label>
                                                                                                                                                        </div>
                                                                                                                                                    </div>
                                                                                                                                                `).join('')}
                    </div>
                    <div class="mt-3 text-center">
                        <button type="button" class="btn btn-sm btn-outline-primary me-2" onclick="selectAllItems()">
                            <i class="mdi mdi-check-all me-1"></i>Selecionar Todos
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearAllItems()">
                            <i class="mdi mdi-close-box me-1"></i>Limpar Seleção
                        </button>
                    </div>
                </div>
                ${venda.observacoes ? `
                                                                                                                                            <div class="mt-3">
                                                                                                                                                <h6 class="mb-1">Observações da Venda:</h6>
                                                                                                                                                <p class="mb-0 text-muted small">${venda.observacoes}</p>
                                                                                                                                            </div>
                                                                                                                                        ` : ''}
            `;
            document.getElementById('venda_selecionada').style.display = 'block';
            document.getElementById('btnSalvar').disabled = false;
        }

        // Cálculo do total
        function calcularTotal() {
            const quantidade = parseFloat(document.getElementById('quantidade').value) || 0;
            const precoUnit = parseFloat(document.getElementById('preco_unit').value) || 0;
            const desconto = parseFloat(document.getElementById('desconto').value) || 0;

            const total = (quantidade * precoUnit) - desconto;
            document.getElementById('total_linha').value = total.toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        // Event listeners para cálculo
        ['quantidade', 'preco_unit', 'desconto'].forEach(id => {
            document.getElementById(id).addEventListener('input', calcularTotal);
        });

        document.getElementById('btnConfirmarCriarOrdem').addEventListener('click', function() {
            const form = document.getElementById('ordemForm');
            getConfirmOrdemModal().hide();
            ordemFormSubmitSkipConfirm = true;
            form.requestSubmit();
        });

        // Validação do formulário
        document.getElementById('ordemForm').addEventListener('submit', function(e) {
            if (ordemFormSubmitSkipConfirm) {
                ordemFormSubmitSkipConfirm = false;
                return true;
            }

            e.preventDefault();

            if (!selectedVenda) {
                showOrdemValidacao('Por favor, selecione uma venda antes de continuar.');
                return false;
            }

            const itensSelecionados = document.querySelectorAll('input[name="itens_selecionados[]"]:checked');
            if (itensSelecionados.length === 0) {
                showOrdemValidacao(
                    'Selecione pelo menos um item da venda para incluir na ordem de serviço.'
                );
                return false;
            }

            const fornecedorSelect = document.getElementById('fornecedor_id');
            const fornecedorText = fornecedorSelect.options[fornecedorSelect.selectedIndex]?.text?.trim() || '—';

            // Cliente
            document.getElementById('confirm_os_cliente').textContent =
                document.getElementById('client_name')?.textContent?.trim() || '—';

            // Venda
            document.getElementById('confirm_os_venda').textContent = selectedVenda ?
                `Venda #${selectedVenda.numero}` : '—';

            // Prescrição
            document.getElementById('confirm_os_prescricao').textContent = selectedPrescricao ?
                `#${selectedPrescricao.id}` : '—';

            // Fornecedor
            document.getElementById('confirm_os_fornecedor').textContent = fornecedorText;

            // Itens
            document.getElementById('confirm_os_itens').textContent =
                `${itensSelecionados.length} item(ns)`;

            // Quantidade
            document.getElementById('confirm_os_quantidade').textContent =
                document.getElementById('quantidade').value || '—';

            // Prioridade
            document.getElementById('confirm_os_prioridade').textContent = prioridadeLabel(
                document.getElementById('prioridade').value
            );

            // Data de Entrega
            const dataEntrega = document.getElementById('entrega_em').value;
            document.getElementById('confirm_os_entrega').textContent = dataEntrega ?
                new Date(dataEntrega).toLocaleDateString('pt-BR') : '—';

            // Total
            document.getElementById('confirm_os_total').textContent =
                document.getElementById('total_linha').value ?
                `R$ ${document.getElementById('total_linha').value}` : '—';

            // Observações
            const observacoes = document.getElementById('observacoes').value.trim();
            if (observacoes) {
                document.getElementById('confirm_os_observacoes').textContent = observacoes;
                document.getElementById('confirm_os_observacoes_container').style.display = 'block';
            } else {
                document.getElementById('confirm_os_observacoes_container').style.display = 'none';
            }

            // Prescrição (mostrar/ocultar)
            if (selectedPrescricao) {
                document.getElementById('confirm_os_prescricao_container').style.display = 'block';
            } else {
                document.getElementById('confirm_os_prescricao_container').style.display = 'none';
            }

            // Itens Detalhados
            if (itensSelecionados.length > 0) {
                let itensHtml = '';
                itensSelecionados.forEach(checkbox => {
                    const container = checkbox.closest('.item-checkbox');
                    if (container) {
                        const label = container.querySelector('label');
                        if (label) {
                            const produto = label.querySelector('strong')?.textContent?.trim() ||
                                'Produto não identificado';
                            const detalhes = label.querySelector('small')?.textContent?.trim() || '';
                            itensHtml +=
                                `<small class="d-block"><strong>${produto}</strong><br><span class="text-muted">${detalhes}</span></small>`;
                        }
                    }
                });
                document.getElementById('confirm_os_itens_detalhes').innerHTML = itensHtml;
                document.getElementById('confirm_os_itens_detalhes_container').style.display = 'block';
            } else {
                document.getElementById('confirm_os_itens_detalhes_container').style.display = 'none';
            }

            getConfirmOrdemModal().show();
            return false;
        });

        // Função para selecionar todos os itens
        function selectAllItems() {
            const checkboxes = document.querySelectorAll('input[name="itens_selecionados[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
            updateSelectedItems();
        }

        // Função para limpar toda a seleção
        function clearAllItems() {
            const checkboxes = document.querySelectorAll('input[name="itens_selecionados[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            updateSelectedItems();
        }

        // Função para atualizar contadores de itens selecionados
        function updateSelectedItems() {
            const itensSelecionados = document.querySelectorAll('input[name="itens_selecionados[]"]:checked');
            const btnSalvar = document.getElementById('btnSalvar');

            if (itensSelecionados.length > 0) {
                btnSalvar.disabled = false;
                btnSalvar.innerHTML = `
                    <i class="mdi mdi-check-circle me-1"></i>
                    Criar Ordem de Serviço (${itensSelecionados.length} item${itensSelecionados.length > 1 ? 's' : ''})
                `;
            } else {
                btnSalvar.disabled = true;
                btnSalvar.innerHTML = `
                    <i class="mdi mdi-check-circle me-1"></i>
                    Criar Ordem de Serviço
                `;
            }
        }

        // Ocultar sugestões ao clicar fora
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#client_search_container')) {
                hideClientSuggestions();
            }
            if (!e.target.closest('#prescricao_search_container')) {
                hidePrescricaoSuggestions();
            }
        });
    </script>
@endpush
