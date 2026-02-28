@extends('layouts.app')

@section('title', 'Nova Venda - VisaoSis')
@section('page-title', 'Nova Venda')

@section('page-actions')
    <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">
        <i class="mdi mdi-arrow-left me-2"></i>
        Voltar
    </a>
@endsection

@section('content')
    <div class="row">
        <!-- Seleção de Cliente e Produtos -->
        <div class="col-lg-8">
            <!-- Seleção de Cliente -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-account text-primary me-2"></i>
                        Selecionar Cliente
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="client_search" class="form-label">Buscar Cliente</label>
                            <div id="client_search_container" class="position-relative">
                            <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="mdi mdi-magnify"></i>
                                    </span>
                                <input type="text" class="form-control" id="client_search"
                                        placeholder="Digite nome, CPF ou e-mail..." autocomplete="off"
                                        oninput="handleClientInput(this.value)">
                                </div>
                                <div id="client_suggestions"
                                    class="list-group position-absolute w-100 client-suggestions shadow-sm"
                                    style="display: none;">
                                </div>
                            </div>
                            <small class="text-muted">Digite pelo menos 2 caracteres para pesquisar.</small>
                        </div>
                    </div>

                    <div id="selected_client" class="mt-3" style="display: none;">
                        <div class="alert alert-success d-flex align-items-center">
                            <i class="mdi mdi-check-circle me-2"></i>
                            <div>
                                <strong id="client_name"></strong><br>
                                <small id="client_document" class="d-block"></small>
                                <small id="client_contact" class="d-block"></small>
                                <small id="client_address" class="d-block"></small>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary ms-auto" onclick="clearClient()">
                                <i class="mdi mdi-close"></i>
                            </button>
                        </div>
                    </div>
                    <div id="client_required_alert" class="alert alert-warning mt-3" style="display: none;">
                        <i class="mdi mdi-alert-circle me-2"></i>
                        <strong>Selecione um cliente</strong> para poder adicionar produtos ao carrinho.
                    </div>
                </div>
            </div>

            <!-- Catálogo de Produtos -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-view-grid text-primary me-2"></i>
                        Catálogo de Produtos
                    </h5>
                    <div class="d-flex gap-2">
                        <select class="form-select form-select-sm" id="category_filter">
                            <option value="">Todas as categorias</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->descricao }}</option>
                            @endforeach
                        </select>
                        <input type="text" class="form-control form-control-sm" id="product_search"
                            placeholder="Buscar produto...">
                    </div>
                </div>
                <div class="card-body">
                    <div class="row" id="products_grid">
                        @forelse ($products as $product)
                            <div class="col-md-6 col-lg-4 mb-3 product-item"
                                data-category-id="{{ $product['categoria_id'] }}"
                                data-name="{{ strtolower($product['nome']) }}">
                                <div class="card h-100 product-card">
                                    <div class="card-body text-center">
                                        <div class="product-image mb-3">
                                            @if (!empty($product['image_url']))
                                                <img src="{{ $product['image_url'] }}" alt="{{ $product['nome'] }}"
                                                    class="img-fluid rounded"
                                                    style="height: 120px; width: 100%; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                    style="height: 120px;">
                                                    <i class="mdi mdi-glasses text-muted" style="font-size: 3rem;"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <h6 class="card-title">{{ $product['nome'] }}</h6>
                                        <p class="card-text">
                                            <span class="badge bg-secondary">{{ $product['categoria'] }}</span>
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="text-success mb-0">R$
                                                {{ number_format($product['preco'], 2, ',', '.') }}</h5>
                                            <small class="text-muted">
                                                @if (is_null($product['stock']))
                                                    Disponível
                                                @elseif ($product['stock'] === 0)
                                                    Sem estoque
                                                @else
                                                    Estoque: {{ $product['stock'] }}
                                                @endif
                                            </small>
                                        </div>
                                        <button class="btn btn-primary btn-sm w-100 add-to-cart-btn"
                                            data-product-id="{{ $product['id'] }}"
                                            data-product-name="{{ $product['nome'] }}"
                                            data-product-price="{{ $product['preco'] }}"
                                            data-product-stock="{{ $product['stock'] ?? 'null' }}"
                                            onclick='addToCart(@json($product['id']), @json($product['nome']), @json($product['preco']), @json($product['stock']))'
                                            {{ $product['stock'] === 0 ? 'disabled' : '' }}
                                            disabled>
                                            <i class="mdi mdi-cart-plus me-1"></i>
                                            <span class="btn-text">{{ $product['stock'] === 0 ? 'Sem Estoque' : 'Adicionar' }}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="text-center text-muted py-4">
                                    <i class="mdi mdi-package-variant" style="font-size: 3rem;"></i>
                                    <p class="mt-2 mb-0">Nenhum produto disponível no momento.</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                    <div class="text-center text-muted py-4" id="no_products_message" style="display: none;">
                        <i class="mdi mdi-package-variant" style="font-size: 3rem;"></i>
                        <p class="mt-2 mb-0">Nenhum produto encontrado.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Carrinho de Compras -->
        <div class="col-lg-4">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-cart text-success me-2"></i>
                        Carrinho de Compras
                        <span class="badge bg-primary ms-2" id="cart_count">0</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div id="cart_items">
                        <div class="text-center text-muted py-4">
                            <i class="mdi mdi-cart-off" style="font-size: 3rem;"></i>
                            <p class="mt-2">Carrinho vazio</p>
                            <small>Adicione produtos do catálogo</small>
                        </div>
                    </div>

                    <div id="cart_summary" style="display: none;">
                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span id="subtotal">R$ 0,00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Desconto:</span>
                            <div class="input-group input-group-sm">
                                <input type="number" class="form-control" id="discount" value="0" min="0"
                                    max="100" onchange="updateTotal()">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong>
                            <strong class="text-success" id="total">R$ 0,00</strong>
                        </div>

                        <!-- Forma de Pagamento -->
                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Forma de Pagamento</label>
                            <select class="form-select" id="payment_method" onchange="updatePaymentOptions()">
                                <option value="">Selecione</option>
                                <option value="dinheiro">Dinheiro</option>
                                <option value="cartao_debito">Cartão de Débito</option>
                                <option value="cartao_credito">Cartão de Crédito</option>
                                <option value="crediario">Crediário</option>
                                <option value="pix">PIX</option>
                            </select>
                        </div>

                        <div id="installments_section" style="display: none;">
                            <label for="installments" class="form-label">Parcelas</label>
                            <select class="form-select mb-3" id="installments" onchange="updateInstallmentValue()">
                                <option value="1">1x sem juros</option>
                                <option value="2">2x sem juros</option>
                                <option value="3">3x sem juros</option>
                                <option value="4">4x sem juros</option>
                                <option value="5">5x sem juros</option>
                                <option value="6">6x sem juros</option>
                            </select>
                            <small class="text-muted" id="installment_value"></small>
                        </div>

                        <div class="mb-3">
                            <label for="observations" class="form-label">Observações</label>
                            <textarea class="form-control" id="observations" rows="2" placeholder="Observações sobre a venda..."></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button class="btn btn-success" onclick="finalizeSale()" id="finalize_btn" disabled>
                                <i class="mdi mdi-check-circle me-2"></i>
                                Finalizar Venda
                            </button>
                            <button class="btn btn-outline-secondary" onclick="clearCart()">
                                <i class="mdi mdi-delete me-2"></i>
                                Limpar Carrinho
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação de Venda -->
    <div class="modal fade" id="confirmSaleModal" tabindex="-1" aria-labelledby="confirmSaleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="confirmSaleModalLabel">
                        <i class="mdi mdi-check-circle me-2"></i>
                        Confirmar Venda
                    </h5>
                    <!-- Botão de fechar removido - modal só fecha ao confirmar ou cancelar -->
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Cliente</h6>
                            <p class="mb-0 fw-semibold" id="confirm_client_name">-</p>
                            <small class="text-muted" id="confirm_client_document">-</small>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Forma de Pagamento</h6>
                            <p class="mb-0 fw-semibold" id="confirm_payment_method">-</p>
                            <small class="text-muted" id="confirm_installments">-</small>
                        </div>
                    </div>
                    <hr>
                    <h6 class="text-muted mb-3">Itens do Pedido</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th class="text-center">Qtd</th>
                                    <th class="text-end">Preço Unit.</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="confirm_items_list">
                            </tbody>
                        </table>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-8"></div>
                        <div class="col-md-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span id="confirm_subtotal">R$ 0,00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Desconto:</span>
                                <span class="text-danger" id="confirm_discount">R$ 0,00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <strong>Total:</strong>
                                <strong class="text-success fs-5" id="confirm_total">R$ 0,00</strong>
                            </div>
                        </div>
                    </div>
                    <div id="confirm_observations_section" style="display: none;">
                        <hr>
                        <h6 class="text-muted mb-2">Observações</h6>
                        <p class="mb-0 text-muted" id="confirm_observations">-</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" id="cancel_sale_btn" onclick="cancelSale()">
                        <i class="mdi mdi-close-circle me-2"></i>
                        Cancelar
                    </button>
                    <button type="button" class="btn btn-success" id="confirm_sale_btn" onclick="processSale()">
                        <i class="mdi mdi-check-circle me-2"></i>
                        Confirmar Venda
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Sucesso -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-body text-center py-5">
                    <div class="mb-4">
                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="mdi mdi-check-circle text-success" style="font-size: 3rem;"></i>
                        </div>
                    </div>
                    <h4 class="modal-title mb-3" id="successModalLabel">Venda Realizada com Sucesso!</h4>
                    <p class="text-muted mb-4" id="success_message">A venda foi registrada com sucesso no sistema.</p>
                    <button type="button" class="btn btn-success px-4" id="success_ok_btn" onclick="window.location.href='{{ route('sales.index') }}'">
                        <i class="mdi mdi-check-circle me-2"></i>
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Erro -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-body text-center py-5">
                    <div class="mb-4">
                        <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="mdi mdi-alert text-danger" style="font-size: 3rem;"></i>
                        </div>
                    </div>
                    <h4 class="modal-title mb-3" id="errorModalLabel">Erro ao Finalizar Venda</h4>
                    <p class="text-muted mb-4" id="error_message">Ocorreu um erro ao processar a venda.</p>
                    <button type="button" class="btn btn-danger px-4" data-bs-dismiss="modal">
                        <i class="mdi mdi-close-circle me-2"></i>
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Validação -->
    <div class="modal fade" id="validationModal" tabindex="-1" aria-labelledby="validationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-body text-center py-5">
                    <div class="mb-4">
                        <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="mdi mdi-alert-circle text-warning" style="font-size: 3rem;"></i>
                        </div>
                    </div>
                    <h4 class="modal-title mb-3" id="validationModalLabel">Atenção</h4>
                    <p class="text-muted mb-4" id="validation_message">Por favor, verifique os dados informados.</p>
                    <button type="button" class="btn btn-warning px-4" data-bs-dismiss="modal">
                        <i class="mdi mdi-check-circle me-2"></i>
                        Entendi
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação para Limpar Carrinho -->
    <div class="modal fade" id="confirmClearCartModal" tabindex="-1" aria-labelledby="confirmClearCartModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-body text-center py-5">
                    <div class="mb-4">
                        <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="mdi mdi-delete text-warning" style="font-size: 3rem;"></i>
                        </div>
                    </div>
                    <h4 class="modal-title mb-3" id="confirmClearCartModalLabel">Limpar Carrinho</h4>
                    <p class="text-muted mb-4">Tem certeza que deseja limpar o carrinho? Todos os itens serão removidos.</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                            <i class="mdi mdi-close-circle me-2"></i>
                            Cancelar
                        </button>
                        <button type="button" class="btn btn-warning px-4" id="confirm_clear_cart_btn" onclick="confirmClearCart()">
                            <i class="mdi mdi-delete me-2"></i>
                            Limpar Carrinho
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .product-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border: 1px solid #e5e7eb;
        }

        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .cart-item {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 8px;
            background: #f9fafb;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .quantity-controls button {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: 1px solid #d1d5db;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .quantity-controls input {
            width: 50px;
            text-align: center;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            padding: 4px;
        }

        .sticky-top {
            position: sticky;
            top: 20px;
            z-index: 1020;
        }

        .client-suggestions {
            top: calc(100% + 0.25rem);
            left: 0;
            width: 100%;
            max-height: 260px;
            overflow-y: auto;
            z-index: 1051;
            background: #ffffff;
            border-radius: 0.5rem;
            border: 1px solid #e5e7eb;
        }

        .client-suggestions .list-group-item {
            cursor: pointer;
        }

        /* Estilos para os modais */
        .modal-content {
            border-radius: 12px;
            overflow: hidden;
        }

        .modal-header.bg-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        }

        #confirmSaleModal .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }

        #confirmSaleModal .table {
            font-size: 0.9rem;
        }

        #confirmSaleModal .table thead {
            background-color: #f8f9fa;
        }

        .modal-body .bg-success,
        .modal-body .bg-danger,
        .modal-body .bg-warning {
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        .modal.fade .modal-dialog {
            transition: transform 0.3s ease-out;
            transform: translate(0, -50px);
        }

        .modal.show .modal-dialog {
            transform: translate(0, 0);
        }

        /* Animação para ícone de loading */
        .mdi-loading {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        let cart = [];
        let selectedClient = null;
        let clientSearchTimeout = null;
        let clientSuggestionsData = [];
        let clientSuggestionIndex = -1;

        const clientSearchInput = document.getElementById('client_search');
        const clientSuggestions = document.getElementById('client_suggestions');
        const clientSearchContainer = document.getElementById('client_search_container');
        const productSearchInput = document.getElementById('product_search');
        const categoryFilter = document.getElementById('category_filter');
        const productsGrid = document.getElementById('products_grid');
        const noProductsMessage = document.getElementById('no_products_message');
        const CLIENT_SEARCH_URL = "{{ route('pessoas.search') }}";

        function handleClientInput(value) {
            const term = value.trim();

            clearTimeout(clientSearchTimeout);
            clientSuggestionIndex = -1;

            if (term.length < 2) {
                hideClientSuggestions();
                return;
            }

            clientSearchTimeout = setTimeout(() => fetchClients(term), 300);
        }

        async function fetchClients(query) {
            try {
                const response = await fetch(`${CLIENT_SEARCH_URL}?q=${encodeURIComponent(query)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error('Não foi possível buscar os clientes.');
                }

                const data = await response.json();
                renderClientSuggestions(Array.isArray(data) ? data : []);
            } catch (error) {
                console.error(error);
                renderClientSuggestions([]);
            }
        }

        function renderClientSuggestions(clients) {
            clientSuggestionsData = clients;
            clientSuggestionIndex = -1;

            if (!clients.length) {
                clientSuggestions.innerHTML = `
                <div class="list-group-item text-muted small">
                    Nenhum cliente encontrado.
                </div>`;
                clientSuggestions.style.display = 'block';
                return;
            }

            clientSuggestions.innerHTML = clients.map((client, index) => `
            <button type="button" class="list-group-item list-group-item-action" data-index="${index}">
                <div class="fw-semibold">${client.nome}</div>
                <small class="text-muted d-block">
                    ${client.cpf ? `CPF: ${client.cpf}` : 'CPF não informado'}
                    ${client.telefone ? ` • Tel: ${client.telefone}` : ''}
                </small>
                ${client.email ? `<small class="text-muted d-block">${client.email}</small>` : ''}
            </button>
        `).join('');

            clientSuggestions.style.display = 'block';
            updateSuggestionHighlight();
        }

        function hideClientSuggestions() {
            clientSuggestionsData = [];
            clientSuggestions.innerHTML = '';
            clientSuggestions.style.display = 'none';
            clientSuggestionIndex = -1;
        }

        function updateSuggestionHighlight() {
            const items = clientSuggestions.querySelectorAll('[data-index]');

            items.forEach((item, index) => {
                const isActive = index === clientSuggestionIndex;
                item.classList.toggle('active', isActive);

                if (isActive) {
                    item.scrollIntoView({
                        block: 'nearest'
                    });
                }
            });
        }

        function selectClientFromSearch(client) {
            selectedClient = {
                id: client.id,
                name: client.nome,
                cpf: client.cpf,
                telefone: client.telefone,
                email: client.email,
                endereco: client.endereco
            };

            clientSearchInput.value = client.nome;
            document.getElementById('client_name').textContent = client.nome || '';
            document.getElementById('client_document').textContent = client.cpf ? `CPF: ${client.cpf}` : '';
            document.getElementById('client_contact').textContent = buildClientContact(client);
            document.getElementById('client_address').textContent = client.endereco ? `Endereço: ${client.endereco}` : '';
            document.getElementById('selected_client').style.display = 'block';
            document.getElementById('client_required_alert').style.display = 'none';

            hideClientSuggestions();
            updateAddToCartButtons();
            updateFinalizeButton();
        }

        function buildClientContact(client) {
            const parts = [];
            if (client.telefone) {
                parts.push(`Telefone: ${client.telefone}`);
            }
            if (client.email) {
                parts.push(`E-mail: ${client.email}`);
            }
            return parts.join(' • ');
        }

        function clearClient() {
            selectedClient = null;
            clientSearchInput.value = '';
            document.getElementById('client_name').textContent = '';
            document.getElementById('client_document').textContent = '';
            document.getElementById('client_contact').textContent = '';
            document.getElementById('client_address').textContent = '';
            document.getElementById('selected_client').style.display = 'none';
            document.getElementById('client_required_alert').style.display = 'block';
            hideClientSuggestions();
            updateAddToCartButtons();
            updateFinalizeButton();
            clientSearchInput.focus();
        }

        function addToCart(id, name, price, stock) {
            // Verificar se há cliente selecionado
            if (!selectedClient) {
                showValidationModal('Selecione um cliente antes de adicionar produtos ao carrinho!');
                document.getElementById('client_search').focus();
                return;
            }

            const stockLimit = Number.isFinite(stock) ? stock : null;
            const existingItem = cart.find(item => item.id === id);

            if (existingItem) {
                if (stockLimit === null || existingItem.quantity < stockLimit) {
                    existingItem.quantity++;
                    existingItem.subtotal = existingItem.quantity * existingItem.price;
                } else {
                    showValidationModal('Quantidade máxima em estoque atingida!');
                    return;
                }
            } else {
                cart.push({
                    id: id,
                    name: name,
                    price: price,
                    quantity: 1,
                    subtotal: price,
                    stock: stockLimit
                });
            }

            updateCartDisplay();
            updateTotal();
            updateFinalizeButton();
        }

        function removeFromCart(id) {
            cart = cart.filter(item => item.id !== id);
            updateCartDisplay();
            updateTotal();
            updateFinalizeButton();
        }

        function updateQuantity(id, newQuantity) {
            const item = cart.find(item => item.id === id);
            if (item) {
                const hasLimit = item.stock !== null && item.stock !== undefined;
                if (newQuantity > 0 && (!hasLimit || newQuantity <= item.stock)) {
                    item.quantity = newQuantity;
                    item.subtotal = item.quantity * item.price;
                    updateCartDisplay();
                    updateTotal();
                } else if (newQuantity <= 0) {
                    removeFromCart(id);
                } else {
                    showValidationModal('Quantidade máxima em estoque atingida!');
                }
            }
        }

        function updateCartDisplay() {
            const cartItems = document.getElementById('cart_items');
            const cartCount = document.getElementById('cart_count');
            const cartSummary = document.getElementById('cart_summary');

            cartCount.textContent = cart.length;

            if (cart.length === 0) {
                cartItems.innerHTML = `
            <div class="text-center text-muted py-4">
                            <i class="mdi mdi-cart-off" style="font-size: 3rem;"></i>
                            <p class="mt-2">Carrinho vazio</p>
                            <small>Adicione produtos do catálogo</small>
            </div>
        `;
                cartSummary.style.display = 'none';
            } else {
                cartItems.innerHTML = cart.map(item => {
                    const maxAttr = item.stock !== null && item.stock !== undefined ? `max="${item.stock}"` : '';
                    const disableIncrease = item.stock !== null && item.stock !== undefined && item.quantity >= item.stock;
                    return `
            <div class="cart-item">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="mb-0">${item.name}</h6>
                    <button class="btn btn-sm btn-outline-danger" onclick="removeFromCart(${item.id})">
                        <i class="mdi mdi-close"></i>
                    </button>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="quantity-controls">
                        <button onclick="updateQuantity(${item.id}, ${item.quantity - 1})">-</button>
                        <input type="number" value="${item.quantity}" min="1" ${maxAttr}
                               onchange="updateQuantity(${item.id}, parseInt(this.value))">
                        <button onclick="updateQuantity(${item.id}, ${item.quantity + 1})" ${disableIncrease ? 'disabled' : ''}>+</button>
                    </div>
                    <div class="text-end">
                        <div class="text-success fw-bold">R$ ${item.subtotal.toFixed(2).replace('.', ',')}</div>
                        <small class="text-muted">R$ ${item.price.toFixed(2).replace('.', ',')} cada</small>
                    </div>
                </div>
            </div>
        `;
                }).join('');
                cartSummary.style.display = 'block';
            }
        }

        function updateTotal() {
            const subtotal = cart.reduce((sum, item) => sum + item.subtotal, 0);
            const discount = parseFloat(document.getElementById('discount').value) || 0;
            const discountAmount = subtotal * (discount / 100);
            const total = subtotal - discountAmount;

            document.getElementById('subtotal').textContent = `R$ ${subtotal.toFixed(2).replace('.', ',')}`;
            document.getElementById('total').textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;

            updateInstallmentValue();
        }

        function updatePaymentOptions() {
            const paymentMethod = document.getElementById('payment_method').value;
            const installmentsSection = document.getElementById('installments_section');

            if (paymentMethod === 'cartao_credito' || paymentMethod === 'crediario') {
                installmentsSection.style.display = 'block';
                updateInstallmentValue();
            } else {
                installmentsSection.style.display = 'none';
            }

            updateFinalizeButton();
        }

        function updateInstallmentValue() {
            const total = cart.reduce((sum, item) => sum + item.subtotal, 0);
            const discount = parseFloat(document.getElementById('discount').value) || 0;
            const finalTotal = total - (total * (discount / 100));
            const installments = parseInt(document.getElementById('installments').value) || 1;
            const installmentValue = finalTotal / installments;

            document.getElementById('installment_value').textContent =
                `${installments}x de R$ ${installmentValue.toFixed(2).replace('.', ',')}`;
        }

        function updateAddToCartButtons() {
            const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
            const hasClient = selectedClient !== null;

            addToCartButtons.forEach(btn => {
                const stock = btn.dataset.productStock;
                const isOutOfStock = stock === '0' || stock === 0;
                
                // Desabilitar se não há cliente ou se está sem estoque
                if (!hasClient || isOutOfStock) {
                    btn.disabled = true;
                    if (!hasClient) {
                        btn.title = 'Selecione um cliente primeiro';
                    }
                } else {
                    btn.disabled = false;
                    btn.title = 'Adicionar ao carrinho';
                }
            });
        }

        function updateFinalizeButton() {
            const finalizeBtn = document.getElementById('finalize_btn');
            const hasClient = selectedClient !== null;
            const hasProducts = cart.length > 0;
            const hasPayment = document.getElementById('payment_method').value !== '';

            finalizeBtn.disabled = !(hasClient && hasProducts && hasPayment);
        }

        function clearCart() {
            const modal = new bootstrap.Modal(document.getElementById('confirmClearCartModal'));
            modal.show();
        }

        function confirmClearCart() {
                cart = [];
                updateCartDisplay();
                updateTotal();
                updateFinalizeButton();
            
            const modal = bootstrap.Modal.getInstance(document.getElementById('confirmClearCartModal'));
            if (modal) {
                modal.hide();
            }
        }

        // Funções para exibir modais
        function showValidationModal(message) {
            document.getElementById('validation_message').textContent = message;
            const modal = new bootstrap.Modal(document.getElementById('validationModal'));
            modal.show();
        }

        function showErrorModal(message) {
            document.getElementById('error_message').textContent = message;
            const modal = new bootstrap.Modal(document.getElementById('errorModal'));
            modal.show();
        }

        function showSuccessModal(message) {
            if (message) {
                document.getElementById('success_message').textContent = message;
            }
            const modal = new bootstrap.Modal(document.getElementById('successModal'));
            modal.show();
        }

        function showConfirmSaleModal() {
            // Preencher informações do cliente
            document.getElementById('confirm_client_name').textContent = selectedClient.name;
            document.getElementById('confirm_client_document').textContent = selectedClient.cpf ? `CPF: ${selectedClient.cpf}` : '';

            // Preencher forma de pagamento
            const paymentMethod = document.getElementById('payment_method').value;
            const paymentMethods = {
                'dinheiro': 'Dinheiro',
                'cartao_debito': 'Cartão de Débito',
                'cartao_credito': 'Cartão de Crédito',
                'crediario': 'Crediário',
                'pix': 'PIX'
            };
            document.getElementById('confirm_payment_method').textContent = paymentMethods[paymentMethod] || paymentMethod;

            const installments = parseInt(document.getElementById('installments').value) || 1;
            if (paymentMethod === 'cartao_credito' || paymentMethod === 'crediario') {
                document.getElementById('confirm_installments').textContent = `${installments}x parcelas`;
            } else {
                document.getElementById('confirm_installments').textContent = 'À vista';
            }

            // Preencher itens
            const itemsList = document.getElementById('confirm_items_list');
            itemsList.innerHTML = cart.map(item => `
                <tr>
                    <td>${item.name}</td>
                    <td class="text-center">${item.quantity}</td>
                    <td class="text-end">R$ ${item.price.toFixed(2).replace('.', ',')}</td>
                    <td class="text-end fw-semibold">R$ ${item.subtotal.toFixed(2).replace('.', ',')}</td>
                </tr>
            `).join('');

            // Preencher totais
            const subtotal = cart.reduce((sum, item) => sum + item.subtotal, 0);
            const discount = parseFloat(document.getElementById('discount').value) || 0;
            const discountAmount = subtotal * (discount / 100);
            const total = subtotal - discountAmount;

            document.getElementById('confirm_subtotal').textContent = `R$ ${subtotal.toFixed(2).replace('.', ',')}`;
            document.getElementById('confirm_discount').textContent = `- R$ ${discountAmount.toFixed(2).replace('.', ',')}`;
            document.getElementById('confirm_total').textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;

            // Observações
            const observations = document.getElementById('observations').value;
            if (observations) {
                document.getElementById('confirm_observations').textContent = observations;
                document.getElementById('confirm_observations_section').style.display = 'block';
            } else {
                document.getElementById('confirm_observations_section').style.display = 'none';
            }

            // Mostrar modal
            const modal = new bootstrap.Modal(document.getElementById('confirmSaleModal'));
            modal.show();
        }

        function finalizeSale() {
            // Validações
            if (!selectedClient) {
                showValidationModal('Selecione um cliente antes de finalizar a venda!');
                document.getElementById('client_search').focus();
                return;
            }

            if (cart.length === 0) {
                showValidationModal('Adicione produtos ao carrinho antes de finalizar a venda!');
                return;
            }

            const paymentMethod = document.getElementById('payment_method').value;
            if (!paymentMethod) {
                showValidationModal('Selecione uma forma de pagamento antes de finalizar a venda!');
                document.getElementById('payment_method').focus();
                return;
            }

            // Mostrar modal de confirmação
            showConfirmSaleModal();
        }

        function cancelSale() {
            // Fechar o modal de confirmação
            const confirmModal = bootstrap.Modal.getInstance(document.getElementById('confirmSaleModal'));
            if (confirmModal) {
                confirmModal.hide();
            }
            // O usuário volta para a tela de adicionar produtos ao carrinho
        }

        async function processSale() {
            // Calcular totais
            const subtotal = cart.reduce((sum, item) => sum + item.subtotal, 0);
            const discount = parseFloat(document.getElementById('discount').value) || 0;
            const discountAmount = subtotal * (discount / 100);
            const total = subtotal - discountAmount;

            // Preparar dados da venda
            const saleData = {
                cliente_id: selectedClient.id,
                produtos: cart.map(item => ({
                    produto_id: item.id,
                    quantidade: item.quantity,
                    preco_unitario: item.price,
                    subtotal: item.subtotal
                })),
                forma_pagamento: document.getElementById('payment_method').value,
                parcelas: document.getElementById('payment_method').value === 'cartao_credito' || document.getElementById('payment_method').value === 'crediario' 
                    ? parseInt(document.getElementById('installments').value) || 1 
                    : 1,
                desconto_percentual: discount,
                desconto_valor: discountAmount,
                subtotal: subtotal,
                total: total,
                observacoes: document.getElementById('observations').value || null
            };

            // Fechar modal de confirmação
            const confirmModal = bootstrap.Modal.getInstance(document.getElementById('confirmSaleModal'));
            if (confirmModal) {
                confirmModal.hide();
            }

            // Desabilitar botão durante o processamento
            const finalizeBtn = document.getElementById('finalize_btn');
            const originalText = finalizeBtn.innerHTML;
            finalizeBtn.disabled = true;
            finalizeBtn.innerHTML = '<i class="mdi mdi-loading me-2"></i>Processando...';

            try {
                const response = await fetch('{{ route("sales.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(saleData)
                });

                const data = await response.json();

                if (!response.ok) {
                    // Tratar erros de validação
                    let errorMessage = data.message || 'Erro ao processar a venda';
                    
                    if (data.errors) {
                        const errorMessages = Object.values(data.errors).flat();
                        errorMessage = errorMessages.join('\n');
                    }
                    
                    throw new Error(errorMessage);
                }

                // Sucesso - mostrar modal de sucesso
                showSuccessModal('A venda foi registrada com sucesso no sistema.');
                
                // Redirecionar após 2 segundos
                setTimeout(() => {
                    window.location.href = '{{ route("sales.index") }}';
                }, 2000);

            } catch (error) {
                console.error('Erro:', error);
                
                // Exibir mensagem de erro
                let errorMessage = 'Erro ao finalizar a venda. Por favor, tente novamente.';
                if (error.message) {
                    errorMessage = error.message;
                }
                
                showErrorModal(errorMessage);
                
                finalizeBtn.disabled = false;
                finalizeBtn.innerHTML = originalText;
            }
        }

        function applyProductFilters() {
            const searchTerm = productSearchInput.value.toLowerCase().trim();
            const categoryId = categoryFilter.value;
            const products = productsGrid.querySelectorAll('.product-item');

            if (!products.length) {
                if (noProductsMessage) {
                    noProductsMessage.style.display = 'none';
                }
                return;
            }

            let visibleCount = 0;

            products.forEach(product => {
                const name = product.dataset.name;
                const productCategoryId = product.dataset.categoryId;
                const matchesSearch = !searchTerm || name.includes(searchTerm);
                const matchesCategory = !categoryId || productCategoryId === categoryId;
                const shouldShow = matchesSearch && matchesCategory;

                product.style.display = shouldShow ? 'block' : 'none';

                if (shouldShow) {
                    visibleCount++;
                }
            });

            if (noProductsMessage) {
                noProductsMessage.style.display = visibleCount === 0 ? 'block' : 'none';
            }
        }

        // Event listeners
        document.getElementById('discount').addEventListener('input', updateTotal);
        document.getElementById('payment_method').addEventListener('change', updateFinalizeButton);
        clientSuggestions.addEventListener('click', event => {
            const item = event.target.closest('[data-index]');
            if (!item) {
                return;
            }
            const index = Number(item.dataset.index);
            const client = clientSuggestionsData[index];
            if (client) {
                selectClientFromSearch(client);
            }
        });

        document.addEventListener('click', event => {
            if (!clientSearchContainer.contains(event.target)) {
                hideClientSuggestions();
            }
        });

        clientSearchInput.addEventListener('focus', () => {
            if (clientSuggestionsData.length) {
                clientSuggestions.style.display = 'block';
            }
        });

        clientSearchInput.addEventListener('keydown', event => {
            if (!clientSuggestionsData.length || clientSuggestions.style.display === 'none') {
                return;
            }

            switch (event.key) {
                case 'ArrowDown':
                    event.preventDefault();
                    clientSuggestionIndex = (clientSuggestionIndex + 1) % clientSuggestionsData.length;
                    updateSuggestionHighlight();
                    break;
                case 'ArrowUp':
                    event.preventDefault();
                    clientSuggestionIndex =
                        (clientSuggestionIndex - 1 + clientSuggestionsData.length) % clientSuggestionsData.length;
                    updateSuggestionHighlight();
                    break;
                case 'Enter':
                    if (clientSuggestionIndex >= 0 && clientSuggestionIndex < clientSuggestionsData.length) {
                        event.preventDefault();
                        selectClientFromSearch(clientSuggestionsData[clientSuggestionIndex]);
                    }
                    break;
                case 'Escape':
                    hideClientSuggestions();
                    break;
                default:
                    break;
            }
        });

        productSearchInput.addEventListener('input', applyProductFilters);
        categoryFilter.addEventListener('change', applyProductFilters);
        applyProductFilters();
        
        // Inicializar estado dos botões ao carregar a página
        updateAddToCartButtons();
        
        // Mostrar alerta inicial se não há cliente selecionado
        if (!selectedClient) {
            document.getElementById('client_required_alert').style.display = 'block';
        }
    </script>
@endpush
