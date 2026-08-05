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
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <h5 class="card-title mb-0">
                            <i class="mdi mdi-view-list text-primary me-2"></i>
                            Catálogo de Produtos
                        </h5>
                        <span class="text-muted small" id="products_count_label"></span>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="input-group input-group-sm flex-grow-1" id="product_search_container">
                            <span class="input-group-text bg-white">
                                <i class="mdi mdi-magnify"></i>
                            </span>
                            <input type="text" class="form-control" id="product_search" autocomplete="off"
                                placeholder="Buscar por nome ou marca...">
                            <button type="button" class="btn btn-outline-secondary d-none" id="product_search_clear"
                                title="Limpar busca">
                                <i class="mdi mdi-close"></i>
                            </button>
                        </div>
                        <select class="form-select form-select-sm" id="category_filter" style="max-width: 190px;">
                            <option value="">Todas as categorias</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->descricao }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="products_list" class="products-list"></div>

                    <div class="text-center text-muted py-5" id="no_products_message" style="display: none;">
                        <i class="mdi mdi-package-variant-closed" style="font-size: 2.5rem;"></i>
                        <p class="mt-2 mb-0" id="no_products_text">Nenhum produto encontrado.</p>
                    </div>

                    <div class="text-center py-3 border-top" id="load_more_wrap" style="display: none;">
                        <button type="button" class="btn btn-outline-primary btn-sm" id="load_more_btn"
                            onclick="loadMoreProducts()">
                            <i class="mdi mdi-chevron-down me-1"></i>
                            <span id="load_more_text">Carregar mais produtos</span>
                        </button>
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
                        <div class="mb-2">
                            <span class="d-block small text-muted mb-1">Desconto</span>
                            <select class="form-select form-select-sm mb-2" id="discount_type">
                                <option value="percent" selected>Porcentagem (%)</option>
                                <option value="value">Valor (R$)</option>
                            </select>
                            <div id="discount_percent_wrap">
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control" id="discount_percent" inputmode="decimal"
                                        placeholder="0,00" autocomplete="off">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <div id="discount_value_wrap" class="d-none">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">R$</span>
                                    <input type="text" class="form-control" id="discount_value" inputmode="numeric"
                                        placeholder="0,00" autocomplete="off">
                                </div>
                            </div>
                            <small id="discount_auth_status" class="text-muted d-none mt-1"></small>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong>
                            <strong class="text-success" id="total">R$ 0,00</strong>
                        </div>

                        <!-- Formas de Pagamento (múltiplas) -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0 fw-semibold">
                                    <i class="mdi mdi-credit-card-multiple-outline me-1"></i>
                                    Formas de Pagamento
                                </label>
                            </div>

                            <div id="payment_entries"></div>

                            <button type="button" class="btn btn-outline-secondary btn-sm w-100 mt-1"
                                id="add_payment_btn" onclick="addPaymentEntry()">
                                <i class="mdi mdi-plus me-1"></i>
                                Adicionar outra forma de pagamento
                            </button>

                            <div class="mt-2 p-2 rounded d-none" id="payment_summary_bar"
                                style="background:#f8f9fb; border:1px solid #e5e7eb;">
                                <div class="d-flex justify-content-between align-items-center small">
                                    <span class="text-muted">Alocado:</span>
                                    <span id="payment_allocated_display" class="fw-semibold">R$ 0,00</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center small d-none"
                                    id="payment_remaining_row">
                                    <span class="text-warning fw-semibold">Restante:</span>
                                    <span id="payment_remaining_display" class="text-warning fw-semibold">R$ 0,00</span>
                                </div>
                            </div>
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

    <!-- Modal de Atributos do Produto -->
    <div class="modal fade" id="productAttributesModal" tabindex="-1" aria-labelledby="productAttributesModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title" id="productAttributesModalLabel">
                        <i class="mdi mdi-tag-multiple-outline me-2"></i>
                        Atributos do produto
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3" id="product_attributes_name"></p>
                    <div id="product_attributes_list" class="product-attributes-list"></div>
                    <div id="product_attributes_empty" class="text-muted text-center py-3 d-none">
                        Este produto não possui atributos cadastrados.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Autorização de Desconto -->
    <div class="modal fade" id="discountAuthModal" tabindex="-1" aria-labelledby="discountAuthModalLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title" id="discountAuthModalLabel">
                        <i class="mdi mdi-shield-key-outline me-2"></i>
                        Autorizar desconto
                    </h5>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        Para aplicar desconto, informe as credenciais de um supervisor (Administrador ou Gerente).
                    </p>
                    <div class="alert alert-light border mb-3 py-2">
                        <small class="text-muted d-block">Desconto solicitado</small>
                        <strong id="discount_auth_amount">R$ 0,00</strong>
                        <small class="text-muted ms-2" id="discount_auth_percent">(0%)</small>
                    </div>
                    <div class="mb-3">
                        <label for="supervisor_email" class="form-label">E-mail do supervisor</label>
                        <input type="email" class="form-control" id="supervisor_email" autocomplete="username"
                            placeholder="supervisor@empresa.com">
                    </div>
                    <div class="mb-2">
                        <label for="supervisor_password" class="form-label">Senha do supervisor</label>
                        <input type="password" class="form-control" id="supervisor_password"
                            autocomplete="current-password" placeholder="Senha">
                    </div>
                    <div id="discount_auth_error" class="alert alert-danger d-none mt-3 mb-0" role="alert"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" id="discount_auth_cancel_btn">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="discount_auth_confirm_btn">
                        <i class="mdi mdi-check me-1"></i>
                        Autorizar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação de Venda -->
    <div class="modal fade" id="confirmSaleModal" tabindex="-1" aria-labelledby="confirmSaleModalLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered confirm-sale-dialog">
            <div class="modal-content confirm-sale-content border-0 shadow">
                <div class="modal-header confirm-sale-header">
                    <h6 class="modal-title mb-0" id="confirmSaleModalLabel">
                        <i class="mdi mdi-receipt-text-outline me-1"></i>
                        Confirmar venda
                    </h6>
                </div>
                <div class="modal-body confirm-sale-body">
                    <div class="confirm-sale-meta">
                        <div class="confirm-sale-meta-item">
                            <i class="mdi mdi-account-outline"></i>
                            <div class="confirm-sale-meta-text">
                                <span class="confirm-sale-meta-value" id="confirm_client_name">-</span>
                                <small class="text-muted" id="confirm_client_document">-</small>
                            </div>
                        </div>
                        <div class="confirm-sale-meta-item">
                            <i class="mdi mdi-credit-card-outline"></i>
                            <div class="confirm-sale-meta-text">
                                <span class="confirm-sale-meta-value" id="confirm_payment_method">-</span>
                                <small class="text-muted" id="confirm_installments"></small>
                            </div>
                        </div>
                    </div>

                    <div class="confirm-sale-items" id="confirm_items_list"></div>

                    <div id="confirm_observations_section" class="confirm-sale-obs" style="display: none;">
                        <small class="text-muted d-block mb-1">Observações</small>
                        <small class="text-muted" id="confirm_observations">-</small>
                    </div>
                </div>

                <div class="confirm-sale-totals">
                    <div class="confirm-sale-total-row" id="confirm_subtotal_row">
                        <span>Subtotal</span>
                        <span id="confirm_subtotal">R$ 0,00</span>
                    </div>
                    <div class="confirm-sale-total-row" id="confirm_discount_row" style="display: none;">
                        <span>Desconto</span>
                        <span class="text-danger" id="confirm_discount">- R$ 0,00</span>
                    </div>
                    <div class="confirm-sale-total-row confirm-sale-total-final">
                        <span>Total</span>
                        <span id="confirm_total">R$ 0,00</span>
                    </div>
                </div>

                <div class="modal-footer confirm-sale-footer">
                    <button type="button" class="btn btn-sm btn-light" id="cancel_sale_btn" onclick="cancelSale()">
                        Cancelar
                    </button>
                    <button type="button" class="btn btn-sm btn-success" id="confirm_sale_btn" onclick="processSale()">
                        <i class="mdi mdi-check me-1"></i>
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Sucesso -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-body text-center py-5">
                    <div class="mb-4">
                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center"
                            style="width: 80px; height: 80px;">
                            <i class="mdi mdi-check-circle text-success" style="font-size: 3rem;"></i>
                        </div>
                    </div>
                    <h4 class="modal-title mb-3" id="successModalLabel">Venda Realizada com Sucesso!</h4>
                    <p class="text-muted mb-4" id="success_message">A venda foi registrada com sucesso no sistema.</p>
                    <button type="button" class="btn btn-success px-4" id="success_ok_btn"
                        onclick="window.location.href='{{ route('sales.index') }}'">
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
                        <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center"
                            style="width: 80px; height: 80px;">
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
    <div class="modal fade" id="validationModal" tabindex="-1" aria-labelledby="validationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-body text-center py-5">
                    <div class="mb-4">
                        <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center"
                            style="width: 80px; height: 80px;">
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

    <!-- Modal de Confirmação para Sair da Tela -->
    <div class="modal fade" id="confirmLeavePageModal" tabindex="-1" aria-labelledby="confirmLeavePageModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-body text-center py-5">
                    <div class="mb-4">
                        <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center"
                            style="width: 80px; height: 80px;">
                            <i class="mdi mdi-exit-to-app text-warning" style="font-size: 3rem;"></i>
                        </div>
                    </div>
                    <h4 class="modal-title mb-3" id="confirmLeavePageModalLabel">Sair da venda?</h4>
                    <p class="text-muted mb-4">Você tem produtos no carrinho e/ou um cliente selecionado. Se sair agora,
                        essas informações serão perdidas.</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                            <i class="mdi mdi-arrow-left me-2"></i>
                            Continuar na venda
                        </button>
                        <button type="button" class="btn btn-warning px-4" id="confirm_leave_page_btn"
                            onclick="confirmLeavePage()">
                            <i class="mdi mdi-exit-to-app me-2"></i>
                            Sair mesmo assim
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação para Limpar Carrinho -->
    <div class="modal fade" id="confirmClearCartModal" tabindex="-1" aria-labelledby="confirmClearCartModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-body text-center py-5">
                    <div class="mb-4">
                        <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center"
                            style="width: 80px; height: 80px;">
                            <i class="mdi mdi-delete text-warning" style="font-size: 3rem;"></i>
                        </div>
                    </div>
                    <h4 class="modal-title mb-3" id="confirmClearCartModalLabel">Limpar Carrinho</h4>
                    <p class="text-muted mb-4">Tem certeza que deseja limpar o carrinho? Todos os itens serão removidos.
                    </p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                            <i class="mdi mdi-close-circle me-2"></i>
                            Cancelar
                        </button>
                        <button type="button" class="btn btn-warning px-4" id="confirm_clear_cart_btn"
                            onclick="confirmClearCart()">
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
        /* Catálogo de produtos — lista compacta */
        .products-list {
            max-height: 560px;
            overflow-y: auto;
        }

        .product-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            border-bottom: 1px solid #f1f2f4;
            transition: background-color 0.15s ease;
        }

        .product-row:hover {
            background-color: #f8f9fb;
        }

        .product-row-disabled {
            opacity: 0.65;
        }

        .product-thumb {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            object-fit: cover;
            flex-shrink: 0;
            background: #f1f2f4;
        }

        .product-thumb-placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9ca3af;
            font-size: 1.25rem;
        }

        .product-row-info {
            flex: 1 1 auto;
            min-width: 0;
        }

        .product-row-name {
            font-size: 0.9rem;
            font-weight: 600;
            color: #1f2937;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .product-row-meta {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 2px;
            flex-wrap: wrap;
        }

        .product-row-meta .badge {
            font-size: 0.68rem;
            font-weight: 500;
        }

        .cart-qty-badge {
            background-color: #dcfce7;
            color: #15803d;
            font-size: 0.68rem;
            font-weight: 600;
        }

        .product-row-price {
            flex-shrink: 0;
            min-width: 92px;
            line-height: 1.3;
        }

        .product-row-price small {
            display: block;
        }

        .product-row-actions {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
        }

        .product-attributes-btn-wrap {
            display: inline-flex;
            cursor: not-allowed;
        }

        .add-to-cart-btn,
        .product-attributes-btn {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .product-attributes-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .product-attribute-item {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            padding: 10px 12px;
            background: #f8f9fb;
            border: 1px solid #eef0f3;
            border-radius: 8px;
        }

        .product-attribute-item .attr-key {
            font-size: 0.8rem;
            font-weight: 600;
            color: #6b7280;
            text-transform: capitalize;
        }

        .product-attribute-item .attr-value {
            font-size: 0.875rem;
            font-weight: 500;
            color: #1f2937;
            text-align: right;
            word-break: break-word;
        }

        /* Skeleton de carregamento */
        .product-row-skeleton {
            pointer-events: none;
        }

        .skeleton-block,
        .skeleton-line {
            background: linear-gradient(90deg, #eef0f3 25%, #f6f7f9 37%, #eef0f3 63%);
            background-size: 400% 100%;
            animation: skeleton-shimmer 1.4s ease infinite;
            border-radius: 6px;
        }

        .skeleton-line {
            height: 12px;
            margin-bottom: 6px;
        }

        @keyframes skeleton-shimmer {
            0% { background-position: 100% 50%; }
            100% { background-position: 0 50%; }
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

        .cart-item-price .input-group-text {
            font-size: 0.8rem;
        }

        .cart-item-price .form-control {
            font-size: 0.85rem;
        }

        .cart-item-price .form-control.is-invalid {
            border-color: #dc3545;
        }

        .cart-item-subtotal {
            font-size: 0.95rem;
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

        /* Modal de confirmação de venda — compacto */
        .confirm-sale-dialog {
            max-width: 420px;
        }

        .confirm-sale-content {
            border-radius: 10px;
            overflow: hidden;
        }

        .confirm-sale-header {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #eef0f3;
            background: #fafbfc;
        }

        .confirm-sale-header .modal-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: #374151;
        }

        .confirm-sale-body {
            padding: 0.875rem 1rem;
            max-height: 50vh;
            overflow-y: auto;
        }

        .confirm-sale-meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
        }

        .confirm-sale-meta-item {
            display: flex;
            align-items: flex-start;
            gap: 0.4rem;
            padding: 0.5rem 0.6rem;
            background: #f8f9fb;
            border-radius: 6px;
            font-size: 0.8rem;
            min-width: 0;
        }

        .confirm-sale-meta-item>i {
            font-size: 1rem;
            color: #9ca3af;
            margin-top: 1px;
            flex-shrink: 0;
        }

        .confirm-sale-meta-text {
            min-width: 0;
            line-height: 1.3;
        }

        .confirm-sale-meta-value {
            display: block;
            font-weight: 600;
            color: #1f2937;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .confirm-sale-meta-text small {
            font-size: 0.72rem;
        }

        .confirm-sale-items {
            border-top: 1px dashed #e5e7eb;
            padding-top: 0.5rem;
        }

        .confirm-sale-item {
            padding: 0.35rem 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .confirm-sale-item:last-child {
            border-bottom: none;
        }

        .confirm-sale-item-name {
            font-size: 0.8rem;
            font-weight: 500;
            color: #374151;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .confirm-sale-item-detail {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.75rem;
            margin-top: 1px;
        }

        .confirm-sale-obs {
            margin-top: 0.5rem;
            padding: 0.45rem 0.6rem;
            background: #fffbeb;
            border-radius: 6px;
            font-size: 0.75rem;
        }

        .confirm-sale-totals {
            padding: 0.6rem 1rem;
            background: #f8f9fb;
            border-top: 1px solid #eef0f3;
        }

        .confirm-sale-total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.78rem;
            color: #6b7280;
            padding: 0.1rem 0;
        }

        .confirm-sale-total-final {
            margin-top: 0.25rem;
            padding-top: 0.35rem;
            border-top: 1px solid #e5e7eb;
            font-size: 0.9rem;
            font-weight: 700;
            color: #111827;
        }

        .confirm-sale-total-final span:last-child {
            color: #16a34a;
            font-size: 1rem;
        }

        .confirm-sale-footer {
            padding: 0.6rem 1rem;
            border-top: none;
            gap: 0.5rem;
        }

        .confirm-sale-footer .btn {
            font-size: 0.8rem;
            padding: 0.35rem 0.85rem;
        }

        /* Entradas de pagamento */
        .payment-entry {
            background: #f9fafb;
            border-color: #e5e7eb !important;
            transition: border-color 0.15s;
        }

        .payment-entry:focus-within {
            border-color: #6366f1 !important;
        }

        /* Estilos gerais para modais */
        .modal-content {
            border-radius: 12px;
            overflow: hidden;
        }

        .modal-body .bg-success,
        .modal-body .bg-danger,
        .modal-body .bg-warning {
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
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
        let allowNavigation = false;
        let pendingNavigationUrl = null;
        let pendingNavigationAction = null;
        let discountAuthToken = null;
        let discountAuthorizedFor = null;
        let pendingDiscountAuth = null;

        // --- Múltiplas formas de pagamento ---
        let paymentEntries = [];
        let paymentEntryCounter = 0;

        const PAYMENT_METHODS_MAP = {
            'dinheiro': 'Dinheiro',
            'cartao_debito': 'Cartão de Débito',
            'cartao_credito': 'Cartão de Crédito',
            'crediario': 'Crediário',
            'pix': 'PIX'
        };

        const canApplyDiscountWithoutAuth = @json($canApplyDiscountWithoutAuth ?? false);
        const preselectedClient = @json($preselectedClient);

        const clientSearchInput = document.getElementById('client_search');
        const clientSuggestions = document.getElementById('client_suggestions');
        const clientSearchContainer = document.getElementById('client_search_container');
        const CLIENT_SEARCH_URL = "{{ route('pessoas.search') }}";

        // --- Catálogo de produtos (busca/paginação server-side) ---
        const PRODUCTS_SEARCH_URL = "{{ route('sales.products.search') }}";
        const productSearchInput = document.getElementById('product_search');
        const productSearchClearBtn = document.getElementById('product_search_clear');
        const categoryFilter = document.getElementById('category_filter');
        const productsListEl = document.getElementById('products_list');
        const noProductsMessage = document.getElementById('no_products_message');
        const noProductsText = document.getElementById('no_products_text');
        const loadMoreWrap = document.getElementById('load_more_wrap');
        const loadMoreBtn = document.getElementById('load_more_btn');
        const loadMoreText = document.getElementById('load_more_text');
        const productsCountLabel = document.getElementById('products_count_label');

        const catalogState = {
            query: '',
            categoryId: '',
            page: 1,
            hasMore: @json($productsHasMore ?? false),
            total: @json($productsTotal ?? 0),
            loading: false,
        };

        // Mapa id -> dados do produto, alimentado conforme as linhas são renderizadas.
        // addToCart() e updateAddToCartButtons() consultam esse mapa (nunca dados inline no HTML).
        const catalogProductsById = {};
        let productSearchTimeout = null;

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function getCartQuantity(productId) {
            const item = cart.find(cartItem => cartItem.id === productId);
            return item ? item.quantity : 0;
        }

        function initProductAttributeTooltips() {
            if (typeof bootstrap === 'undefined' || !bootstrap.Tooltip) {
                return;
            }

            productsListEl.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
                const existing = bootstrap.Tooltip.getInstance(el);
                if (existing) {
                    existing.dispose();
                }
                new bootstrap.Tooltip(el, { placement: 'top', trigger: 'hover focus' });
            });
        }

        function productHasAttributes(product) {
            const atributos = product && product.atributos;
            return atributos && typeof atributos === 'object' && !Array.isArray(atributos)
                && Object.keys(atributos).length > 0;
        }

        function showProductAttributes(productId) {
            const product = catalogProductsById[productId];
            if (!product || !productHasAttributes(product)) {
                return;
            }

            const nameEl = document.getElementById('product_attributes_name');
            const listEl = document.getElementById('product_attributes_list');
            const emptyEl = document.getElementById('product_attributes_empty');
            const modalEl = document.getElementById('productAttributesModal');

            nameEl.textContent = product.nome || '';
            listEl.innerHTML = Object.entries(product.atributos).map(([key, value]) => `
                <div class="product-attribute-item">
                    <span class="attr-key">${escapeHtml(key)}</span>
                    <span class="attr-value">${escapeHtml(value ?? '')}</span>
                </div>
            `).join('');
            emptyEl.classList.add('d-none');
            listEl.classList.remove('d-none');

            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }

        function productRowHtml(product) {
            catalogProductsById[product.id] = product;

            const outOfStock = product.stock === 0;
            const inCartQty = getCartQuantity(product.id);
            const hasAttributes = productHasAttributes(product);

            const priceHtml = product.preco === null || product.preco === undefined
                ? '<span class="text-muted small">Preço a definir</span>'
                : `<span class="fw-semibold text-success">R$ ${formatCurrency(product.preco)}</span>`;

            let stockHtml = '<small class="text-muted">Disponível</small>';
            if (product.stock !== null && product.stock !== undefined) {
                stockHtml = outOfStock
                    ? '<small class="text-danger fw-semibold">Sem estoque</small>'
                    : `<small class="text-muted">Estoque: ${product.stock}</small>`;
            }

            const imageHtml = product.image_url
                ? `<img src="${escapeHtml(product.image_url)}" alt="${escapeHtml(product.nome)}" class="product-thumb" loading="lazy">`
                : `<div class="product-thumb product-thumb-placeholder"><i class="mdi mdi-glasses"></i></div>`;

            const cartBadgeVisible = inCartQty > 0 ? '' : 'd-none';
            const cartBadgeText = inCartQty > 0 ? `<i class="mdi mdi-cart-check me-1"></i>${inCartQty} no carrinho` : '';

            const attributesBtnHtml = hasAttributes
                ? `<button type="button" class="btn btn-outline-secondary btn-sm product-attributes-btn"
                        onclick="showProductAttributes(${product.id})"
                        title="Ver atributos">
                        <i class="mdi mdi-tag-multiple-outline"></i>
                   </button>`
                : `<span class="product-attributes-btn-wrap" title="Sem atributos" data-bs-toggle="tooltip" data-bs-title="Sem atributos">
                        <button type="button" class="btn btn-outline-secondary btn-sm product-attributes-btn" disabled
                            aria-label="Sem atributos">
                            <i class="mdi mdi-tag-multiple-outline"></i>
                        </button>
                   </span>`;

            return `
                <div class="product-row ${outOfStock ? 'product-row-disabled' : ''}" data-product-row="${product.id}">
                    ${imageHtml}
                    <div class="product-row-info">
                        <div class="product-row-name" title="${escapeHtml(product.nome)}">${escapeHtml(product.nome)}</div>
                        <div class="product-row-meta">
                            <span class="badge bg-light text-secondary border">${escapeHtml(product.categoria)}</span>
                            ${product.marca ? `<span class="text-muted small">${escapeHtml(product.marca)}</span>` : ''}
                            <span class="badge cart-qty-badge ${cartBadgeVisible}" data-cart-badge="${product.id}">${cartBadgeText}</span>
                        </div>
                    </div>
                    <div class="product-row-price text-end">
                        ${priceHtml}
                        ${stockHtml}
                    </div>
                    <div class="product-row-actions">
                        ${attributesBtnHtml}
                        <button type="button" class="btn btn-primary btn-sm add-to-cart-btn"
                            data-product-id="${product.id}" onclick="addToCart(${product.id})"
                            ${outOfStock ? 'disabled' : ''} title="Adicionar ao carrinho">
                            <i class="mdi mdi-cart-plus"></i>
                        </button>
                    </div>
                </div>
            `;
        }

        function renderSkeletonRows(count = 5) {
            productsListEl.innerHTML = Array.from({ length: count }, () => `
                <div class="product-row product-row-skeleton">
                    <div class="product-thumb skeleton-block"></div>
                    <div class="product-row-info">
                        <div class="skeleton-line" style="width: 70%;"></div>
                        <div class="skeleton-line" style="width: 40%; height: 10px;"></div>
                    </div>
                    <div class="skeleton-line" style="width: 60px;"></div>
                </div>
            `).join('');
            noProductsMessage.style.display = 'none';
            loadMoreWrap.style.display = 'none';
        }

        function updateProductsCountLabel() {
            if (!catalogState.total) {
                productsCountLabel.textContent = '';
                return;
            }
            const shown = productsListEl.querySelectorAll('[data-product-row]').length;
            productsCountLabel.textContent =
                `Exibindo ${shown} de ${catalogState.total} produto${catalogState.total === 1 ? '' : 's'}`;
        }

        async function fetchProducts(options = {}) {
            const reset = options.reset !== false;

            if (catalogState.loading) {
                return;
            }
            catalogState.loading = true;

            if (reset) {
                catalogState.page = 1;
                renderSkeletonRows();
            } else {
                loadMoreBtn.disabled = true;
                loadMoreText.textContent = 'Carregando...';
            }

            try {
                const params = new URLSearchParams({ page: catalogState.page });
                if (catalogState.query) {
                    params.set('q', catalogState.query);
                }
                if (catalogState.categoryId) {
                    params.set('category_id', catalogState.categoryId);
                }

                const response = await fetch(`${PRODUCTS_SEARCH_URL}?${params.toString()}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                });

                if (!response.ok) {
                    throw new Error('Não foi possível buscar os produtos.');
                }

                const data = await response.json();
                catalogState.total = data.meta.total;
                catalogState.hasMore = data.meta.has_more;

                if (reset) {
                    productsListEl.innerHTML = '';
                }

                if (data.data.length === 0 && reset) {
                    noProductsText.textContent = (catalogState.query || catalogState.categoryId)
                        ? 'Nenhum produto encontrado para os filtros selecionados.'
                        : 'Nenhum produto disponível no momento.';
                    noProductsMessage.style.display = 'block';
                } else {
                    noProductsMessage.style.display = 'none';
                    productsListEl.insertAdjacentHTML('beforeend', data.data.map(productRowHtml).join(''));
                    initProductAttributeTooltips();
                }

                loadMoreWrap.style.display = catalogState.hasMore ? 'block' : 'none';
                loadMoreBtn.disabled = false;
                loadMoreText.textContent = 'Carregar mais produtos';

                updateProductsCountLabel();
                updateAddToCartButtons();

                // Se a página atual ficou vazia (itens sem estoque filtrados) mas ainda há mais,
                // busca a próxima página automaticamente para não parecer que travou.
                if (data.data.length === 0 && catalogState.hasMore) {
                    catalogState.page += 1;
                    catalogState.loading = false;
                    await fetchProducts({ reset: false });
                    return;
                }
            } catch (error) {
                console.error(error);
                if (reset) {
                    productsListEl.innerHTML = '';
                    noProductsText.textContent = 'Erro ao carregar produtos. Tente novamente.';
                    noProductsMessage.style.display = 'block';
                }
            } finally {
                catalogState.loading = false;
            }
        }

        function loadMoreProducts() {
            if (!catalogState.hasMore || catalogState.loading) {
                return;
            }
            catalogState.page += 1;
            fetchProducts({ reset: false });
        }

        function handleProductSearchInput(value) {
            catalogState.query = value.trim();
            productSearchClearBtn.classList.toggle('d-none', !value);
            clearTimeout(productSearchTimeout);
            productSearchTimeout = setTimeout(() => fetchProducts({ reset: true }), 350);
        }

        function clearProductSearch() {
            productSearchInput.value = '';
            handleProductSearchInput('');
            productSearchInput.focus();
        }

        function handleCategoryFilterChange() {
            catalogState.categoryId = categoryFilter.value;
            fetchProducts({ reset: true });
        }

        function syncCatalogCartBadges() {
            document.querySelectorAll('[data-cart-badge]').forEach(badge => {
                const id = Number(badge.dataset.cartBadge);
                const qty = getCartQuantity(id);
                if (qty > 0) {
                    badge.classList.remove('d-none');
                    badge.innerHTML = `<i class="mdi mdi-cart-check me-1"></i>${qty} no carrinho`;
                } else {
                    badge.classList.add('d-none');
                    badge.innerHTML = '';
                }
            });
        }

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

        function formatCurrency(value) {
            const num = Number(value);
            if (!Number.isFinite(num)) {
                return '0,00';
            }

            return num.toFixed(2).replace('.', ',');
        }

        function parsePrice(value) {
            if (value === null || value === undefined || value === '') {
                return null;
            }

            const normalized = String(value).trim().replace(',', '.');
            const num = parseFloat(normalized);

            return Number.isFinite(num) ? num : null;
        }

        function isValidCartItemPrice(price) {
            return price !== null && Number.isFinite(price) && price > 0;
        }

        function recalculateItemSubtotal(item) {
            item.subtotal = isValidCartItemPrice(item.price) ? item.quantity * item.price : 0;
        }

        function cartHasValidPrices() {
            return cart.length > 0 && cart.every(item => isValidCartItemPrice(item.price));
        }

        function addToCart(id) {
            // Verificar se há cliente selecionado
            if (!selectedClient) {
                showValidationModal('Selecione um cliente antes de adicionar produtos ao carrinho!');
                document.getElementById('client_search').focus();
                return;
            }

            const product = catalogProductsById[id];
            if (!product) {
                return;
            }

            const name = product.nome;
            const stock = product.stock;
            const price = product.preco;

            const stockLimit = Number.isFinite(stock) ? stock : null;
            const catalogPrice = price !== null && price !== undefined ? parsePrice(price) : null;
            const existingItem = cart.find(item => item.id === id);

            if (existingItem) {
                if (stockLimit === null || existingItem.quantity < stockLimit) {
                    existingItem.quantity++;
                    recalculateItemSubtotal(existingItem);
                } else {
                    showValidationModal('Quantidade máxima em estoque atingida!');
                    return;
                }
            } else {
                const initialPrice = catalogPrice;
                cart.push({
                    id: id,
                    name: name,
                    catalogPrice: catalogPrice,
                    price: initialPrice,
                    quantity: 1,
                    subtotal: isValidCartItemPrice(initialPrice) ? initialPrice : 0,
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
                    recalculateItemSubtotal(item);
                    updateCartDisplay();
                    updateTotal();
                    updateFinalizeButton();
                } else if (newQuantity <= 0) {
                    removeFromCart(id);
                } else {
                    showValidationModal('Quantidade máxima em estoque atingida!');
                }
            }
        }

        function updateItemPrice(id, value) {
            const item = cart.find(cartItem => cartItem.id === id);
            if (!item) {
                return;
            }

            item.price = parsePrice(value);
            recalculateItemSubtotal(item);

            const subtotalEl = document.getElementById(`cart-subtotal-${id}`);
            if (subtotalEl) {
                subtotalEl.textContent = `R$ ${formatCurrency(item.subtotal)}`;
            }

            const priceInput = document.getElementById(`cart-price-${id}`);
            if (priceInput) {
                priceInput.classList.toggle('is-invalid', !isValidCartItemPrice(item.price));
            }

            updateTotal();
            updateFinalizeButton();
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
                    const disableIncrease = item.stock !== null && item.stock !== undefined && item.quantity >= item
                        .stock;
                    const priceValue = item.price !== null && item.price !== undefined ?
                        Number(item.price).toFixed(2) :
                        '';
                    const priceInvalidClass = isValidCartItemPrice(item.price) ? '' : 'is-invalid';
                    const priceHint = isValidCartItemPrice(item.price) ?
                        '' :
                        '<small class="text-danger d-block mt-1">Informe o preço unitário</small>';

                    return `
            <div class="cart-item">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="mb-0">${item.name}</h6>
                    <button class="btn btn-sm btn-outline-danger" onclick="removeFromCart(${item.id})">
                        <i class="mdi mdi-close"></i>
                    </button>
                </div>
                <div class="cart-item-price mb-2">
                    <label class="form-label small text-muted mb-1" for="cart-price-${item.id}">Preço unitário</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">R$</span>
                        <input type="number"
                               class="form-control ${priceInvalidClass}"
                               id="cart-price-${item.id}"
                               value="${priceValue}"
                               step="0.01"
                               min="0.01"
                               placeholder="0,00"
                               onchange="updateItemPrice(${item.id}, this.value)"
                               onblur="updateItemPrice(${item.id}, this.value)">
                    </div>
                    ${priceHint}
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="quantity-controls">
                        <button onclick="updateQuantity(${item.id}, ${item.quantity - 1})">-</button>
                        <input type="number" value="${item.quantity}" min="1" ${maxAttr}
                               onchange="updateQuantity(${item.id}, parseInt(this.value))">
                        <button onclick="updateQuantity(${item.id}, ${item.quantity + 1})" ${disableIncrease ? 'disabled' : ''}>+</button>
                    </div>
                    <div class="text-end">
                        <div class="text-success fw-bold cart-item-subtotal" id="cart-subtotal-${item.id}">
                            R$ ${formatCurrency(item.subtotal)}
                        </div>
                    </div>
                </div>
            </div>
        `;
                }).join('');
                cartSummary.style.display = 'block';
            }

            syncCatalogCartBadges();
        }

        function getCartSubtotal() {
            return cart.reduce((sum, item) => sum + item.subtotal, 0);
        }

        function getDiscountType() {
            return document.getElementById('discount_type')?.value === 'value' ? 'value' : 'percent';
        }

        function formatMoneyMask(rawValue) {
            const digits = String(rawValue ?? '').replace(/\D/g, '');
            if (!digits) {
                return '';
            }

            const number = parseFloat(digits) / 100;
            return number.toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            });
        }

        function parseMoneyMasked(value) {
            const digits = String(value ?? '').replace(/\D/g, '');
            if (!digits) {
                return 0;
            }

            return parseFloat(digits) / 100;
        }

        function parsePercentInput(value) {
            const cleaned = String(value ?? '')
                .trim()
                .replace(/[^\d,.]/g, '')
                .replace(',', '.');

            if (!cleaned) {
                return 0;
            }

            const num = parseFloat(cleaned);
            return Number.isFinite(num) ? num : 0;
        }

        function getDiscountPercentValue() {
            const subtotal = getCartSubtotal();

            if (getDiscountType() === 'percent') {
                const percent = Math.min(Math.max(parsePercentInput(document.getElementById('discount_percent').value), 0),
                    100);
                return Math.round(percent * 100) / 100;
            }

            const amount = getDiscountAmountValue();
            if (subtotal <= 0 || amount <= 0) {
                return 0;
            }

            return Math.min(Math.round((amount / subtotal) * 10000) / 100, 100);
        }

        function getDiscountAmountValue() {
            const subtotal = getCartSubtotal();

            if (getDiscountType() === 'value') {
                const amount = parseMoneyMasked(document.getElementById('discount_value').value);
                return Math.max(0, Math.min(amount, subtotal));
            }

            const percent = getDiscountPercentValue();
            const amount = subtotal > 0 ? (subtotal * (percent / 100)) : 0;
            return Math.round(amount * 100) / 100;
        }

        function clearDiscountInputs() {
            document.getElementById('discount_percent').value = '';
            document.getElementById('discount_value').value = '';
        }

        function updateDiscountTypeUI() {
            const type = getDiscountType();
            const percentWrap = document.getElementById('discount_percent_wrap');
            const valueWrap = document.getElementById('discount_value_wrap');

            percentWrap.classList.toggle('d-none', type !== 'percent');
            valueWrap.classList.toggle('d-none', type !== 'value');
        }

        function handleDiscountTypeChange() {
            clearDiscountInputs();
            resetDiscountAuthorization();
            updateDiscountTypeUI();
            updateTotal();
            updateFinalizeButton();
        }

        function applyDiscountPercentMask(event) {
            const input = event.target;
            let value = input.value.replace(/[^\d,.]/g, '');

            // Normaliza ponto para vírgula e mantém só o primeiro separador decimal
            value = value.replace(/\./g, ',');
            const parts = value.split(',');
            let integerPart = (parts[0] || '').replace(/\D/g, '');
            let decimalPart = parts.length > 1 ? parts[1].replace(/\D/g, '').slice(0, 2) : null;

            // Limita a 100
            const preview = parseFloat(
                integerPart + (decimalPart !== null ? '.' + decimalPart : '')
            );
            if (Number.isFinite(preview) && preview > 100) {
                integerPart = '100';
                decimalPart = parts.length > 1 ? '' : null;
            }

            if (decimalPart !== null) {
                value = integerPart + ',' + decimalPart;
            } else {
                value = integerPart;
            }

            input.value = value;
            handleDiscountChange();
        }

        function applyDiscountValueMask(event) {
            const input = event.target;
            input.value = formatMoneyMask(input.value);
            handleDiscountChange();
        }

        function resetDiscountAuthorization() {
            discountAuthToken = null;
            discountAuthorizedFor = null;
            updateDiscountAuthStatus();
        }

        function updateDiscountAuthStatus() {
            const statusEl = document.getElementById('discount_auth_status');
            if (!statusEl) {
                return;
            }

            if (canApplyDiscountWithoutAuth) {
                statusEl.classList.add('d-none');
                statusEl.textContent = '';
                return;
            }

            const discountAmount = getDiscountAmountValue();
            if (discountAmount <= 0) {
                statusEl.classList.add('d-none');
                statusEl.textContent = '';
                return;
            }

            statusEl.classList.remove('d-none');

            if (discountAuthToken && discountAuthorizedFor &&
                Math.abs(discountAuthorizedFor.valor - discountAmount) < 0.01) {
                statusEl.className = 'text-success d-block mt-1';
                statusEl.innerHTML =
                    `<i class="mdi mdi-check-circle-outline me-1"></i>Autorizado por ${discountAuthorizedFor.name}`;
            } else {
                statusEl.className = 'text-warning d-block mt-1';
                statusEl.innerHTML =
                '<i class="mdi mdi-alert-circle-outline me-1"></i>Autorização de supervisor necessária';
            }
        }

        function isDiscountAuthorized() {
            const discountAmount = getDiscountAmountValue();
            if (discountAmount <= 0) {
                return true;
            }

            if (canApplyDiscountWithoutAuth) {
                return true;
            }

            return Boolean(
                discountAuthToken &&
                discountAuthorizedFor &&
                Math.abs(discountAuthorizedFor.valor - discountAmount) < 0.01
            );
        }

        function handleDiscountChange() {
            const discountAmount = getDiscountAmountValue();

            if (discountAmount <= 0) {
                resetDiscountAuthorization();
            } else if (
                discountAuthorizedFor &&
                Math.abs(discountAuthorizedFor.valor - discountAmount) >= 0.01
            ) {
                resetDiscountAuthorization();
            }

            updateTotal();
            updateDiscountAuthStatus();
            updateFinalizeButton();

            if (!canApplyDiscountWithoutAuth && discountAmount > 0 && !isDiscountAuthorized()) {
                openDiscountAuthModal();
            }
        }

        function openDiscountAuthModal() {
            const discountAmount = getDiscountAmountValue();
            const discountPercent = getDiscountPercentValue();

            pendingDiscountAuth = {
                valor: discountAmount,
                percentual: discountPercent,
            };

            document.getElementById('discount_auth_amount').textContent = `R$ ${formatCurrency(discountAmount)}`;
            document.getElementById('discount_auth_percent').textContent =
                `(${discountPercent.toFixed(2).replace('.', ',')}%)`;
            document.getElementById('discount_auth_error').classList.add('d-none');
            document.getElementById('discount_auth_error').textContent = '';
            document.getElementById('supervisor_password').value = '';

            const modal = new bootstrap.Modal(document.getElementById('discountAuthModal'));
            modal.show();
        }

        async function confirmDiscountAuthorization() {
            const supervisorEmail = document.getElementById('supervisor_email').value.trim();
            const supervisorPassword = document.getElementById('supervisor_password').value;
            const errorEl = document.getElementById('discount_auth_error');
            const confirmBtn = document.getElementById('discount_auth_confirm_btn');

            if (!supervisorEmail || !supervisorPassword) {
                errorEl.textContent = 'Informe o e-mail e a senha do supervisor.';
                errorEl.classList.remove('d-none');
                return;
            }

            const discountAmount = pendingDiscountAuth?.valor ?? getDiscountAmountValue();
            const discountPercent = pendingDiscountAuth?.percentual ?? getDiscountPercentValue();

            if (discountAmount <= 0) {
                errorEl.textContent = 'Informe um desconto válido antes de solicitar autorização.';
                errorEl.classList.remove('d-none');
                return;
            }

            const originalHtml = confirmBtn.innerHTML;
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = '<i class="mdi mdi-loading me-1"></i>Validando...';
            errorEl.classList.add('d-none');

            try {
                const response = await fetch('{{ route('sales.authorize-discount') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        supervisor_email: supervisorEmail,
                        supervisor_password: supervisorPassword,
                        desconto_valor: discountAmount,
                        desconto_percentual: discountPercent,
                    }),
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Não foi possível autorizar o desconto.');
                }

                discountAuthToken = data.token || null;
                discountAuthorizedFor = {
                    valor: discountAmount,
                    percentual: discountPercent,
                    name: data.authorized_by_name || 'Supervisor',
                };

                const modal = bootstrap.Modal.getInstance(document.getElementById('discountAuthModal'));
                if (modal) {
                    modal.hide();
                }

                updateDiscountAuthStatus();
                updateFinalizeButton();
            } catch (error) {
                errorEl.textContent = error.message || 'Erro ao autorizar desconto.';
                errorEl.classList.remove('d-none');
            } finally {
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = originalHtml;
            }
        }

        function cancelDiscountAuthorization() {
            const modal = bootstrap.Modal.getInstance(document.getElementById('discountAuthModal'));
            if (modal) {
                modal.hide();
            }

            if (!isDiscountAuthorized()) {
                clearDiscountInputs();
                resetDiscountAuthorization();
                updateTotal();
                updateFinalizeButton();
            }
        }

        function getCartTotal() {
            const subtotal = getCartSubtotal();
            const discountAmount = getDiscountAmountValue();
            return Math.max(0, subtotal - discountAmount);
        }

        function updateTotal() {
            const subtotal = getCartSubtotal();

            if (getDiscountType() === 'value') {
                const rawAmount = parseMoneyMasked(document.getElementById('discount_value').value);
                if (rawAmount > subtotal && subtotal >= 0) {
                    document.getElementById('discount_value').value = formatMoneyMask(
                        Math.round(subtotal * 100).toString()
                    );
                    if (
                        discountAuthorizedFor &&
                        Math.abs(discountAuthorizedFor.valor - subtotal) >= 0.01
                    ) {
                        resetDiscountAuthorization();
                    }
                }
            }

            const discountAmount = getDiscountAmountValue();
            const total = Math.max(0, subtotal - discountAmount);

            document.getElementById('subtotal').textContent = `R$ ${formatCurrency(subtotal)}`;
            document.getElementById('total').textContent = `R$ ${formatCurrency(total)}`;

            updateDiscountAuthStatus();

            // Auto-preenche a entrada única quando o usuário ainda não digitou valor
            if (paymentEntries.length === 1 && !paymentEntries[0].userModified && total > 0) {
                paymentEntries[0].value = parseFloat(total.toFixed(2));
                renderPaymentEntries();
            }

            updatePaymentSummary();
            updateFinalizeButton();
        }

        // ---- Funções de múltiplas formas de pagamento ----

        function getDefaultFirstDueDate() {
            const today = new Date();
            const dueDate = new Date(today.getFullYear(), today.getMonth() + 1, today.getDate());
            return dueDate.toLocaleDateString('en-CA');
        }

        function addPaymentEntry() {
            const id = ++paymentEntryCounter;
            const cartTotal = getCartTotal();
            const allocated = getTotalAllocated();
            const remaining = cartTotal > 0 ? Math.max(0, cartTotal - allocated) : '';
            paymentEntries.push({
                id,
                method: '',
                value: remaining > 0.001 ? parseFloat(remaining.toFixed(2)) : '',
                installments: 1,
                firstDueDate: getDefaultFirstDueDate(),
                userModified: remaining > 0.001
            });
            renderPaymentEntries();
            updatePaymentSummary();
            updateFinalizeButton();
        }

        function removePaymentEntry(id) {
            if (paymentEntries.length <= 1) return;
            paymentEntries = paymentEntries.filter(e => e.id !== id);
            renderPaymentEntries();
            updatePaymentSummary();
            updateFinalizeButton();
        }

        function onPaymentMethodChange(id) {
            const entry = paymentEntries.find(e => e.id === id);
            if (!entry) return;
            entry.method = document.getElementById(`payment-method-${id}`).value;

            const installmentsDiv = document.getElementById(`payment-installments-${id}`);
            const isInstallable = entry.method === 'cartao_credito' || entry.method === 'crediario';
            const isCrediario = entry.method === 'crediario';
            if (installmentsDiv) {
                installmentsDiv.style.display = isInstallable ? 'block' : 'none';
            }
            if (!isInstallable) {
                entry.installments = 1;
            }
            if (!entry.firstDueDate) {
                entry.firstDueDate = getDefaultFirstDueDate();
            }
            const firstDueDateDiv = document.getElementById(`payment-first-due-date-${id}`);
            if (firstDueDateDiv) {
                firstDueDateDiv.style.display = isCrediario ? 'block' : 'none';
            }

            updateInstallmentHint(id);
            updatePaymentSummary();
            updateFinalizeButton();
        }

        function onPaymentValueChange(id) {
            const entry = paymentEntries.find(e => e.id === id);
            if (!entry) return;
            entry.value = parseFloat(document.getElementById(`payment-value-${id}`).value) || 0;
            entry.userModified = true;
            updateInstallmentHint(id);
            updatePaymentSummary();
            updateFinalizeButton();
        }

        function onPaymentInstallmentsChange(id) {
            const entry = paymentEntries.find(e => e.id === id);
            if (!entry) return;
            entry.installments = parseInt(document.getElementById(`payment-installments-select-${id}`).value) || 1;
            updateInstallmentHint(id);
        }

        function onPaymentFirstDueDateChange(id) {
            const entry = paymentEntries.find(e => e.id === id);
            if (!entry) return;
            entry.firstDueDate = document.getElementById(`payment-first-due-date-input-${id}`).value ||
                getDefaultFirstDueDate();
        }

        function updateInstallmentHint(id) {
            const entry = paymentEntries.find(e => e.id === id);
            if (!entry) return;
            const hintEl = document.getElementById(`payment-installment-value-${id}`);
            if (!hintEl) return;
            if (entry.value > 0 && entry.installments > 1) {
                hintEl.textContent = `${entry.installments}x de R$ ${formatCurrency(entry.value / entry.installments)}`;
            } else {
                hintEl.textContent = '';
            }
        }

        function getTotalAllocated() {
            return paymentEntries.reduce((sum, e) => sum + (parseFloat(e.value) || 0), 0);
        }

        function isPaymentValid() {
            if (paymentEntries.length === 0) return false;
            const cartTotal = getCartTotal();
            if (cartTotal <= 0) return false;

            for (const entry of paymentEntries) {
                if (!entry.method || !(parseFloat(entry.value) > 0)) return false;
                if (entry.method === 'crediario' && !entry.firstDueDate) return false;
            }

            return Math.abs(getTotalAllocated() - cartTotal) <= 0.02;
        }

        function updatePaymentSummary() {
            const cartTotal = getCartTotal();
            const allocated = getTotalAllocated();
            const remaining = cartTotal - allocated;

            const summaryBar = document.getElementById('payment_summary_bar');
            const allocatedDisplay = document.getElementById('payment_allocated_display');
            const remainingRow = document.getElementById('payment_remaining_row');
            const remainingDisplay = document.getElementById('payment_remaining_display');

            if (paymentEntries.length > 0 && cartTotal > 0) {
                summaryBar.classList.remove('d-none');
            } else {
                summaryBar.classList.add('d-none');
                return;
            }

            allocatedDisplay.textContent = `R$ ${formatCurrency(allocated)}`;

            const addBtn = document.getElementById('add_payment_btn');

            if (remaining > 0.02) {
                remainingRow.classList.remove('d-none');
                remainingDisplay.textContent = `R$ ${formatCurrency(remaining)}`;
                allocatedDisplay.className = 'fw-semibold text-warning';
                if (addBtn) {
                    addBtn.disabled = false;
                    addBtn.title = '';
                }
            } else if (allocated > cartTotal + 0.02) {
                remainingRow.classList.add('d-none');
                allocatedDisplay.className = 'fw-semibold text-danger';
                if (addBtn) {
                    addBtn.disabled = false;
                    addBtn.title = '';
                }
            } else {
                remainingRow.classList.add('d-none');
                allocatedDisplay.className = 'fw-semibold text-success';
                if (addBtn) {
                    addBtn.disabled = true;
                    addBtn.title = 'Total já totalmente alocado';
                }
            }
        }

        function renderPaymentEntries() {
            const container = document.getElementById('payment_entries');
            const showRemove = paymentEntries.length > 1;

            const optionsHtml = (selectedMethod) => ['dinheiro', 'cartao_debito', 'cartao_credito', 'crediario', 'pix']
                .map(v =>
                    `<option value="${v}" ${selectedMethod === v ? 'selected' : ''}>${PAYMENT_METHODS_MAP[v]}</option>`)
                .join('');

            const installmentsOptions = (selected) => Array.from({
                    length: 12
                }, (_, i) => i + 1)
                .map(n => `<option value="${n}" ${selected === n ? 'selected' : ''}>${n}x sem juros</option>`)
                .join('');

            container.innerHTML = paymentEntries.map(entry => {
                const isInstallable = entry.method === 'cartao_credito' || entry.method === 'crediario';
                const isCrediario = entry.method === 'crediario';
                const hintText = (entry.value > 0 && entry.installments > 1) ?
                    `${entry.installments}x de R$ ${formatCurrency(entry.value / entry.installments)}` :
                    '';

                return `
                    <div class="payment-entry border rounded p-2 mb-2" id="payment-entry-${entry.id}">
                        <div class="d-flex gap-2 mb-2">
                            <select class="form-select form-select-sm" id="payment-method-${entry.id}"
                                    onchange="onPaymentMethodChange(${entry.id})">
                                <option value="">Selecione...</option>
                                ${optionsHtml(entry.method)}
                            </select>
                            ${showRemove ? `<button type="button" class="btn btn-sm btn-outline-danger flex-shrink-0"
                                        onclick="removePaymentEntry(${entry.id})">
                                        <i class="mdi mdi-close"></i>
                                    </button>` : ''}
                        </div>
                        <div class="input-group input-group-sm mb-1">
                            <span class="input-group-text">R$</span>
                            <input type="number" class="form-control" id="payment-value-${entry.id}"
                                   value="${entry.value !== '' ? entry.value : ''}"
                                   step="0.01" min="0.01" placeholder="0,00"
                                   oninput="onPaymentValueChange(${entry.id})">
                        </div>
                        <div id="payment-installments-${entry.id}" style="display:${isInstallable ? 'block' : 'none'};">
                            <select class="form-select form-select-sm mb-1"
                                    id="payment-installments-select-${entry.id}"
                                    onchange="onPaymentInstallmentsChange(${entry.id})">
                                ${installmentsOptions(entry.installments)}
                            </select>
                            <small class="text-muted" id="payment-installment-value-${entry.id}">${hintText}</small>
                        </div>
                        <div id="payment-first-due-date-${entry.id}" class="mt-2" style="display:${isCrediario ? 'block' : 'none'};">
                            <label class="form-label form-label-sm mb-1">Primeiro Vencimento</label>
                            <input type="date" class="form-control form-control-sm"
                                   id="payment-first-due-date-input-${entry.id}"
                                   value="${entry.firstDueDate || getDefaultFirstDueDate()}"
                                   onchange="onPaymentFirstDueDateChange(${entry.id})">
                        </div>
                    </div>
                `;
            }).join('');
        }

        function updateAddToCartButtons() {
            const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
            const hasClient = selectedClient !== null;

            addToCartButtons.forEach(btn => {
                const product = catalogProductsById[Number(btn.dataset.productId)];
                const isOutOfStock = product && product.stock === 0;

                // Desabilitar se não há cliente ou se está sem estoque
                if (!hasClient || isOutOfStock) {
                    btn.disabled = true;
                    btn.title = !hasClient ? 'Selecione um cliente primeiro' : 'Sem estoque';
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
            const hasValidPrices = cartHasValidPrices();
            const discountOk = isDiscountAuthorized();
            const hasPayment = isPaymentValid();

            finalizeBtn.disabled = !(hasClient && hasProducts && hasPayment && hasValidPrices && discountOk);
        }

        function hasSaleInProgress() {
            return cart.length > 0 || selectedClient !== null;
        }

        function shouldConfirmLeave() {
            return hasSaleInProgress() && !allowNavigation;
        }

        function showConfirmLeaveModal() {
            const modal = new bootstrap.Modal(document.getElementById('confirmLeavePageModal'));
            modal.show();
        }

        function confirmLeavePage() {
            allowNavigation = true;

            const modal = bootstrap.Modal.getInstance(document.getElementById('confirmLeavePageModal'));
            if (modal) {
                modal.hide();
            }

            if (pendingNavigationUrl) {
                window.location.href = pendingNavigationUrl;
            } else if (pendingNavigationAction === 'back') {
                history.back();
            }

            pendingNavigationUrl = null;
            pendingNavigationAction = null;
        }

        function clearCart() {
            const modal = new bootstrap.Modal(document.getElementById('confirmClearCartModal'));
            modal.show();
        }

        function confirmClearCart() {
            cart = [];
            clearDiscountInputs();
            resetDiscountAuthorization();
            updateCartDisplay();
            updateTotal();

            // Resetar formas de pagamento para uma entrada vazia (sem valor pré-preenchido)
            paymentEntries = [];
            paymentEntryCounter = 0;
            const initId = ++paymentEntryCounter;
            paymentEntries.push({
                id: initId,
                method: '',
                value: '',
                installments: 1,
                userModified: false
            });
            renderPaymentEntries();

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
            document.getElementById('confirm_client_document').textContent = selectedClient.cpf ?
                `CPF: ${selectedClient.cpf}` : '';

            // Preencher formas de pagamento (múltiplas)
            const paymentSummaryLines = paymentEntries.map(e => {
                const name = PAYMENT_METHODS_MAP[e.method] || e.method;
                const valueStr = `R$ ${formatCurrency(parseFloat(e.value) || 0)}`;
                const isInstallable = e.method === 'cartao_credito' || e.method === 'crediario';
                if (isInstallable && e.installments > 1) {
                    return `${name} ${e.installments}x ${valueStr}`;
                }
                return `${name} ${valueStr}`;
            });
            document.getElementById('confirm_payment_method').textContent = paymentSummaryLines.join(' + ');
            document.getElementById('confirm_installments').textContent = '';

            // Preencher itens
            const itemsList = document.getElementById('confirm_items_list');
            itemsList.innerHTML = cart.map(item => `
                <div class="confirm-sale-item">
                    <div class="confirm-sale-item-name" title="${item.name}">${item.name}</div>
                    <div class="confirm-sale-item-detail">
                        <span class="text-muted">${item.quantity}x R$ ${formatCurrency(item.price)}</span>
                        <span class="fw-semibold">R$ ${formatCurrency(item.subtotal)}</span>
                    </div>
                </div>
            `).join('');

            // Preencher totais
            const subtotal = getCartSubtotal();
            const discountAmount = getDiscountAmountValue();
            const discountPercent = getDiscountPercentValue();
            const total = Math.max(0, subtotal - discountAmount);

            document.getElementById('confirm_subtotal').textContent = `R$ ${formatCurrency(subtotal)}`;
            document.getElementById('confirm_discount').textContent = `- R$ ${formatCurrency(discountAmount)}`;
            document.getElementById('confirm_total').textContent = `R$ ${formatCurrency(total)}`;
            document.getElementById('confirm_discount_row').style.display = discountAmount > 0 ? 'flex' : 'none';

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

            if (!cartHasValidPrices()) {
                showValidationModal(
                'Informe o preço unitário de todos os produtos no carrinho antes de finalizar a venda!');
                return;
            }

            if (!isDiscountAuthorized()) {
                showValidationModal('É necessária autorização de supervisor para aplicar este desconto.');
                openDiscountAuthModal();
                return;
            }

            if (paymentEntries.length === 0 || paymentEntries.some(e => !e.method)) {
                showValidationModal('Selecione uma forma de pagamento para cada entrada!');
                return;
            }

            if (paymentEntries.some(e => e.method === 'crediario' && !e.firstDueDate)) {
                showValidationModal('Informe o primeiro vencimento para todas as entradas com crediário.');
                return;
            }

            if (!isPaymentValid()) {
                const cartTotal = getCartTotal();
                const allocated = getTotalAllocated();
                showValidationModal(
                    `O valor alocado (R$ ${formatCurrency(allocated)}) deve ser igual ao total da venda (R$ ${formatCurrency(cartTotal)}).`
                );
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
            const subtotal = getCartSubtotal();
            const discountPercent = getDiscountPercentValue();
            const discountAmount = getDiscountAmountValue();
            const total = Math.max(0, subtotal - discountAmount);

            const saleData = {
                cliente_id: selectedClient.id,
                produtos: cart.map(item => ({
                    produto_id: item.id,
                    quantidade: item.quantity,
                    preco_unitario: item.price,
                    subtotal: item.subtotal
                })),
                pagamentos: paymentEntries.map(e => ({
                    forma_pagamento: e.method,
                    valor: parseFloat(e.value) || 0,
                    parcelas: e.installments || 1,
                    primeiro_vencimento: e.method === 'crediario' ? (e.firstDueDate || null) : null
                })),
                desconto_percentual: discountPercent,
                desconto_valor: discountAmount,
                desconto_autorizacao_token: discountAuthToken,
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
                const response = await fetch('{{ route('sales.store') }}', {
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
                allowNavigation = true;
                showSuccessModal('A venda foi registrada com sucesso no sistema.');

                // Redirecionar após 2 segundos
                setTimeout(() => {
                    window.location.href = '{{ route('sales.index') }}';
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

        // Confirmação ao sair da tela com venda em andamento
        window.addEventListener('beforeunload', event => {
            if (!shouldConfirmLeave()) {
                return;
            }

            event.preventDefault();
            event.returnValue = '';
        });

        history.pushState({
            saleCreate: true
        }, '');

        window.addEventListener('popstate', () => {
            if (!shouldConfirmLeave()) {
                return;
            }

            history.pushState({
                saleCreate: true
            }, '');
            pendingNavigationUrl = null;
            pendingNavigationAction = 'back';
            showConfirmLeaveModal();
        });

        document.addEventListener('click', event => {
            const link = event.target.closest('a[href]');
            if (!link || !shouldConfirmLeave()) {
                return;
            }

            const href = link.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript:')) {
                return;
            }

            if (link.target === '_blank' || link.hasAttribute('download')) {
                return;
            }

            let url;
            try {
                url = new URL(link.href, window.location.origin);
            } catch {
                return;
            }

            if (url.pathname === window.location.pathname && url.search === window.location.search) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();
            pendingNavigationUrl = link.href;
            pendingNavigationAction = null;
            showConfirmLeaveModal();
        }, true);

        document.getElementById('confirmLeavePageModal').addEventListener('hidden.bs.modal', () => {
            if (!allowNavigation) {
                pendingNavigationUrl = null;
                pendingNavigationAction = null;
            }
        });

        // Inicializar primeira entrada de pagamento (sem valor pré-preenchido, pois carrinho está vazio)
        const initPaymentId = ++paymentEntryCounter;
        paymentEntries.push({
            id: initPaymentId,
            method: '',
            value: '',
            installments: 1,
            firstDueDate: getDefaultFirstDueDate(),
            userModified: false
        });
        renderPaymentEntries();

        // Event listeners
        document.getElementById('discount_type').addEventListener('change', handleDiscountTypeChange);
        document.getElementById('discount_percent').addEventListener('input', applyDiscountPercentMask);
        document.getElementById('discount_value').addEventListener('input', applyDiscountValueMask);
        document.getElementById('discount_auth_confirm_btn').addEventListener('click', confirmDiscountAuthorization);
        document.getElementById('discount_auth_cancel_btn').addEventListener('click', cancelDiscountAuthorization);
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

        productSearchInput.addEventListener('input', event => handleProductSearchInput(event.target.value));
        productSearchClearBtn.addEventListener('click', clearProductSearch);
        categoryFilter.addEventListener('change', handleCategoryFilterChange);

        // Hidrata a lista com a primeira página já renderizada pelo servidor (sem flash de loading).
        const initialProducts = @json($initialProducts ?? []);
        if (initialProducts.length > 0) {
            productsListEl.innerHTML = initialProducts.map(productRowHtml).join('');
            loadMoreWrap.style.display = catalogState.hasMore ? 'block' : 'none';
            updateProductsCountLabel();
            initProductAttributeTooltips();
        } else {
            noProductsText.textContent = 'Nenhum produto disponível no momento.';
            noProductsMessage.style.display = 'block';
        }

        // Inicializar estado dos botões ao carregar a página
        updateAddToCartButtons();

        if (preselectedClient) {
            selectClientFromSearch(preselectedClient);
        }

        // Mostrar alerta inicial se não há cliente selecionado
        if (!selectedClient) {
            document.getElementById('client_required_alert').style.display = 'block';
        }
    </script>
@endpush
