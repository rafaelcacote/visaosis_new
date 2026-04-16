@extends('layouts.app')

@section('title', 'Ordens de Serviço - Connect Plus')

@section('content')
    <div class="d-xl-flex justify-content-between align-items-start mb-4">
        <div>
            <h2 class="text-dark font-weight-bold mb-2">
                <i class="mdi mdi-cog me-2"></i>
                Ordens de Serviço
            </h2>
            <p class="text-muted mb-0">Gestão de ordens de serviço</p>
        </div>
        <a href="{{ route('ordens-servico.create') }}" class="btn btn-primary btn-icon-text">
            <i class="mdi mdi-plus me-2"></i>
            Nova Ordem de Serviço
        </a>
    </div>

    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form class="row g-3 align-items-end form-aligned-sm js-list-filter-form" method="GET">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Buscar</label>
                            <input type="text" class="form-control form-control-sm" id="search" name="search"
                                placeholder="Cliente, fornecedor ou observações..." value="{{ $search ?? '' }}">
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select form-select-sm" id="status" name="status">
                                <option value="todos" {{ ($status ?? 'todos') === 'todos' ? 'selected' : '' }}>Todos
                                </option>
                                <option value="pendente" {{ ($status ?? '') === 'pendente' ? 'selected' : '' }}>Pendente
                                </option>
                                <option value="enviado" {{ ($status ?? '') === 'enviado' ? 'selected' : '' }}>Enviado
                                </option>
                                <option value="em_producao" {{ ($status ?? '') === 'em_producao' ? 'selected' : '' }}>Em
                                    Produção</option>
                                <option value="pronto" {{ ($status ?? '') === 'pronto' ? 'selected' : '' }}>Pronto</option>
                                <option value="entregue" {{ ($status ?? '') === 'entregue' ? 'selected' : '' }}>Entregue
                                </option>
                                <option value="cancelado" {{ ($status ?? '') === 'cancelado' ? 'selected' : '' }}>Cancelado
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="prioridade" class="form-label">Prioridade</label>
                            <select class="form-select form-select-sm" id="prioridade" name="prioridade">
                                <option value="todas" {{ ($prioridade ?? 'todas') === 'todas' ? 'selected' : '' }}>Todas
                                </option>
                                <option value="normal" {{ ($prioridade ?? '') === 'normal' ? 'selected' : '' }}>Normal
                                </option>
                                <option value="urgente" {{ ($prioridade ?? '') === 'urgente' ? 'selected' : '' }}>Urgente
                                </option>
                                <option value="expressa" {{ ($prioridade ?? '') === 'expressa' ? 'selected' : '' }}>
                                    Expressa</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                    <i class="mdi mdi-magnify me-1"></i>
                                    Buscar
                                </button>
                                <a href="{{ route('ordens-servico.index') }}"
                                    class="btn btn-sm btn-outline-secondary js-list-filter-clear d-none">
                                    <i class="mdi mdi-refresh me-1"></i>
                                    Limpar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Ordens -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-format-list-bulleted text-primary me-2"></i>
                        Lista de Ordens de Serviço
                    </h5>
                    <div class="d-flex gap-2 align-items-center">
                        <span class="tag"
                            style="background-color: #e0f0ff; color: #1d7dd6;">{{ $ordensServico->total() }} ordens</span>
                    </div>
                </div>

                <div class="card-body p-0">
                    @if ($ordensServico->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Ordem</th>
                                        <th>Cliente</th>
                                        <th>Fornecedor</th>
                                        <th>Valor</th>
                                        <th>Entrega</th>
                                        <th>Status</th>
                                        <th>Prioridade</th>
                                        <th width="150">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ordensServico as $ordem)
                                        <tr data-id="{{ $ordem->id }}">
                                            <td>
                                                <div>
                                                    <h6 class="mb-0">#{{ str_pad($ordem->id, 6, '0', STR_PAD_LEFT) }}
                                                    </h6>
                                                    <small
                                                        class="text-muted">{{ $ordem->created_at->format('d/m/Y H:i') }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <h6 class="mb-0">{{ $ordem->pedido->cliente->nome ?? 'N/A' }}</h6>
                                                    <small class="text-muted">Venda:
                                                        #{{ $ordem->pedido->numero ?? 'N/A' }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <h6 class="mb-0">{{ $ordem->fornecedor->razao_social ?? 'N/A' }}</h6>
                                                    @if ($ordem->fornecedor && $ordem->fornecedor->nome_fantasia)
                                                        <small
                                                            class="text-muted">{{ $ordem->fornecedor->nome_fantasia }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $ordem->total_formatado }}</strong><br>
                                                    <small class="text-muted">Qtd: {{ $ordem->quantidade }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                @if ($ordem->entrega_em)
                                                    <div>
                                                        {{ $ordem->entrega_em->format('d/m/Y') }}<br>
                                                        <small
                                                            class="text-muted">{{ $ordem->entrega_em->format('H:i') }}</small>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Não definida</span>
                                                @endif
                                            </td>
                                            <td>
                                                @switch($ordem->status)
                                                    @case('pendente')
                                                        <span class="tag" style="background-color: #fff8e6; color: #d97706;">
                                                            <i class="mdi mdi-clock"></i>
                                                            Pendente
                                                        </span>
                                                    @break

                                                    @case('enviado')
                                                        <span class="tag" style="background-color: #e0f0ff; color: #1d7dd6;">
                                                            <i class="mdi mdi-send"></i>
                                                            Enviado
                                                        </span>
                                                    @break

                                                    @case('em_producao')
                                                        <span class="tag" style="background-color: #f3e8ff; color: #9333ea;">
                                                            <i class="mdi mdi-cog"></i>
                                                            Em Produção
                                                        </span>
                                                    @break

                                                    @case('pronto')
                                                        <span class="tag tag-status tag-status-ativo">
                                                            <i class="mdi mdi-check-circle"></i>
                                                            Pronto
                                                        </span>
                                                    @break

                                                    @case('entregue')
                                                        <span class="tag" style="background-color: #dcfce7; color: #16a34a;">
                                                            <i class="mdi mdi-package-check"></i>
                                                            Entregue
                                                        </span>
                                                    @break

                                                    @case('cancelado')
                                                        <span class="tag tag-status tag-status-inativo">
                                                            <i class="mdi mdi-close-circle"></i>
                                                            Cancelado
                                                        </span>
                                                    @break

                                                    @default
                                                        <span class="text-muted">{{ $ordem->status_label }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                @switch($ordem->prioridade)
                                                    @case('normal')
                                                        <span class="tag" style="background-color: #f3f4f6; color: #6b7280;">
                                                            <i class="mdi mdi-minus"></i>
                                                            Normal
                                                        </span>
                                                    @break

                                                    @case('urgente')
                                                        <span class="tag" style="background-color: #fff8e6; color: #d97706;">
                                                            <i class="mdi mdi-star"></i>
                                                            Urgente
                                                        </span>
                                                    @break

                                                    @case('expressa')
                                                        <span class="tag" style="background-color: #fee2e2; color: #dc2626;">
                                                            <i class="mdi mdi-alert"></i>
                                                            Expressa
                                                        </span>
                                                    @break

                                                    @default
                                                        <span class="text-muted">{{ $ordem->prioridade_label }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                <div class="actions">
                                                    <a href="{{ route('ordens-servico.show', $ordem) }}"
                                                        class="btn-action"
                                                        style="background-color: #e0f0ff; color: #1d7dd6;"
                                                        title="Visualizar">
                                                        <i class="mdi mdi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('ordens-servico.pdf', $ordem) }}"
                                                        class="btn-action"
                                                        style="background-color: #f0fdf4; color: #16a34a;"
                                                        title="Gerar PDF" target="_blank">
                                                        <i class="mdi mdi-printer"></i>
                                                    </a>
                                                    <a href="{{ route('ordens-servico.edit', $ordem) }}"
                                                        class="btn-action"
                                                        style="background-color: #fff7ed; color: #ea580c;" title="Editar">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </a>
                                                    <button type="button" class="btn-action"
                                                        style="background-color: #f3e8ff; color: #9333ea;"
                                                        onclick="changeStatus({{ $ordem->id }}, '#{{ str_pad($ordem->id, 6, '0', STR_PAD_LEFT) }}', '{{ $ordem->status }}', '{{ $ordem->pedido->cliente->nome ?? 'N/A' }}', '{{ $ordem->fornecedor->razao_social ?? 'N/A' }}', '{{ $ordem->total_formatado }}', '{{ $ordem->prioridade }}')"
                                                        title="Alterar Status">
                                                        <i class="mdi mdi-swap-horizontal"></i>
                                                    </button>
                                                    <button type="button" class="btn-action"
                                                        style="background-color: #fee2e2; color: #dc2626;"
                                                        onclick="confirmDelete({{ $ordem->id }}, '#{{ str_pad($ordem->id, 6, '0', STR_PAD_LEFT) }}')"
                                                        title="Excluir">
                                                        <i class="mdi mdi-delete"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="mdi mdi-inbox text-muted" style="font-size: 3rem;"></i>
                            <h5 class="mt-3 text-muted">Nenhuma ordem de serviço encontrada</h5>
                            @if (($search ?? '') || ($status ?? 'todos') !== 'todos' || ($prioridade ?? 'todas') !== 'todas')
                                <p class="text-muted mb-3">
                                    Tente ajustar os filtros de busca
                                </p>
                                <a href="{{ route('ordens-servico.index') }}" class="btn btn-outline-primary">
                                    <i class="mdi mdi-arrow-left me-2"></i>
                                    Voltar à lista completa
                                </a>
                            @else
                                <p class="text-muted mb-3">
                                    Comece criando sua primeira ordem de serviço
                                </p>
                                <a href="{{ route('ordens-servico.create') }}" class="btn btn-primary">
                                    <i class="mdi mdi-plus-circle me-2"></i>
                                    Nova Ordem de Serviço
                                </a>
                            @endif
                        </div>
                    @endif
                </div>

                @if ($ordensServico->count() > 0)
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                Mostrando {{ $ordensServico->firstItem() ?? 0 }} a {{ $ordensServico->lastItem() ?? 0 }}
                                de {{ $ordensServico->total() }} ordens de serviço
                            </small>
                            <div>
                                {{ $ordensServico->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal de Alteração de Status -->
    <div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="statusModalLabel">
                        <i class="mdi mdi-swap-horizontal me-2"></i>
                        Alterar Status da Ordem
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <h6 class="text-dark mb-3 fw-bold">Informações da Ordem</h6>
                        <div class="row g-3">
                            <div class="col-6">
                                <small class="text-muted d-block mb-1">Número da Ordem</small>
                                <strong id="statusOrdemNumber">—</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block mb-1">Cliente</small>
                                <strong id="statusOrdemCliente">—</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block mb-1">Fornecedor</small>
                                <strong id="statusOrdemFornecedor">—</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block mb-1">Valor Total</small>
                                <strong class="text-success" id="statusOrdemValor">—</strong>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-dark mb-3 fw-bold">Alterar Status</h6>
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted d-block mb-1">Status Atual</small>
                                <span id="statusAtual" class="tag">—</span>
                            </div>
                            <div class="col-6">
                                <label for="novoStatus" class="form-label">Novo Status <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="novoStatus" name="status" required>
                                    <option value="">Selecione o novo status</option>
                                    <option value="pendente">Pendente</option>
                                    <option value="enviado">Enviado</option>
                                    <option value="em_producao">Em Produção</option>
                                    <option value="pronto">Pronto</option>
                                    <option value="entregue">Entregue</option>
                                    <option value="cancelado">Cancelado</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="mdi mdi-close-circle me-1"></i>
                        Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" id="btnConfirmarStatus">
                        <i class="mdi mdi-check-circle me-1"></i>
                        Alterar Status
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger">
                        <i class="mdi mdi-alert-circle me-2"></i>
                        Confirmar Exclusão
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="mdi mdi-delete text-danger" style="font-size: 3rem;"></i>
                    </div>
                    <p class="text-center">
                        Tem certeza que deseja excluir a ordem de serviço <br>
                        <strong id="deleteOrdemNumber" class="text-danger"></strong>?
                    </p>
                    <div class="alert alert-warning">
                        <i class="mdi mdi-alert-circle me-2"></i>
                        <strong>Atenção:</strong> Esta ação não pode ser desfeita. Todos os dados relacionados a esta
                        ordem de serviço serão removidos permanentemente.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="mdi mdi-close-circle me-1"></i>
                        Cancelar
                    </button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="mdi mdi-delete me-1"></i>
                            Excluir Ordem de Serviço
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let currentOrdemId = null;

        function confirmDelete(ordemId, ordemNumber) {
            document.getElementById('deleteOrdemNumber').textContent = ordemNumber;
            const form = document.getElementById('deleteForm');
            form.action = `/ordens-servico/${ordemId}`;
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }

        function changeStatus(ordemId, ordemNumber, currentStatus, cliente, fornecedor, valor, prioridade) {
            currentOrdemId = ordemId;

            // Preencher informações da ordem
            document.getElementById('statusOrdemNumber').textContent = ordemNumber;
            document.getElementById('statusOrdemCliente').textContent = cliente;
            document.getElementById('statusOrdemFornecedor').textContent = fornecedor;
            document.getElementById('statusOrdemValor').textContent = valor;

            // Mostrar status atual com formatação
            const statusAtualElement = document.getElementById('statusAtual');
            const statusLabels = {
                'pendente': {
                    text: 'Pendente',
                    style: 'background-color: #fff8e6; color: #d97706;',
                    icon: 'mdi-clock'
                },
                'enviado': {
                    text: 'Enviado',
                    style: 'background-color: #e0f0ff; color: #1d7dd6;',
                    icon: 'mdi-send'
                },
                'em_producao': {
                    text: 'Em Produção',
                    style: 'background-color: #f3e8ff; color: #9333ea;',
                    icon: 'mdi-cog'
                },
                'pronto': {
                    text: 'Pronto',
                    style: 'background-color: #dcfce7; color: #16a34a;',
                    icon: 'mdi-check-circle'
                },
                'entregue': {
                    text: 'Entregue',
                    style: 'background-color: #dcfce7; color: #16a34a;',
                    icon: 'mdi-package-check'
                },
                'cancelado': {
                    text: 'Cancelado',
                    style: 'background-color: #fee2e2; color: #dc2626;',
                    icon: 'mdi-close-circle'
                }
            };

            const statusInfo = statusLabels[currentStatus] || {
                text: currentStatus,
                style: '',
                icon: 'mdi-help'
            };
            statusAtualElement.innerHTML = `<i class="mdi ${statusInfo.icon}"></i> ${statusInfo.text}`;
            statusAtualElement.setAttribute('style', statusInfo.style);

            // Limpar seleção anterior
            document.getElementById('novoStatus').value = '';

            // Mostrar modal
            const modal = new bootstrap.Modal(document.getElementById('statusModal'));
            modal.show();
        }

        // Event listener para confirmar alteração de status
        document.getElementById('btnConfirmarStatus').addEventListener('click', function() {
            const novoStatus = document.getElementById('novoStatus').value;

            if (!novoStatus) {
                alert('Por favor, selecione um novo status.');
                return;
            }

            if (!currentOrdemId) {
                alert('Erro: ID da ordem não encontrado.');
                return;
            }

            // Criar formulário para submissão
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/ordens-servico/${currentOrdemId}/status`;
            form.style.display = 'none';

            // Token CSRF
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);

            // Method override para PATCH
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PATCH';
            form.appendChild(methodInput);

            // Status
            const statusInput = document.createElement('input');
            statusInput.type = 'hidden';
            statusInput.name = 'status';
            statusInput.value = novoStatus;
            form.appendChild(statusInput);

            // Adicionar ao DOM e submeter
            document.body.appendChild(form);
            form.submit();
        });
    </script>
@endpush

@push('styles')
    <style>
        .actions {
            display: flex;
            gap: 5px;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            border: none;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn-action:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .tag {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            line-height: 1;
        }

        .tag i {
            font-size: 12px;
        }

        .tag-status-ativo {
            background-color: #dcfce7 !important;
            color: #16a34a !important;
        }

        .tag-status-inativo {
            background-color: #fee2e2 !important;
            color: #dc2626 !important;
        }
    </style>
@endpush
