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
                <form class="row g-3 form-aligned-sm js-list-filter-form" method="GET">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Buscar</label>
                        <input type="text" class="form-control form-control-sm" id="search" name="search"
                            placeholder="Cliente, fornecedor ou observações..." value="{{ $search ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select form-select-sm" id="status" name="status">
                            <option value="todos" {{ ($status ?? 'todos') === 'todos' ? 'selected' : '' }}>Todos</option>
                            <option value="pendente" {{ ($status ?? '') === 'pendente' ? 'selected' : '' }}>Pendente</option>
                            <option value="enviado" {{ ($status ?? '') === 'enviado' ? 'selected' : '' }}>Enviado</option>
                            <option value="em_producao" {{ ($status ?? '') === 'em_producao' ? 'selected' : '' }}>Em Produção</option>
                            <option value="pronto" {{ ($status ?? '') === 'pronto' ? 'selected' : '' }}>Pronto</option>
                            <option value="entregue" {{ ($status ?? '') === 'entregue' ? 'selected' : '' }}>Entregue</option>
                            <option value="cancelado" {{ ($status ?? '') === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="prioridade" class="form-label">Prioridade</label>
                        <select class="form-select form-select-sm" id="prioridade" name="prioridade">
                            <option value="todas" {{ ($prioridade ?? 'todas') === 'todas' ? 'selected' : '' }}>Todas</option>
                            <option value="normal" {{ ($prioridade ?? '') === 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="urgente" {{ ($prioridade ?? '') === 'urgente' ? 'selected' : '' }}>Urgente</option>
                            <option value="expressa" {{ ($prioridade ?? '') === 'expressa' ? 'selected' : '' }}>Expressa</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex flex-column">
                        <label class="form-label invisible user-select-none" aria-hidden="true">.</label>
                        <div class="d-flex align-items-end flex-grow-1">
                            <div class="d-flex gap-2 align-items-end flex-wrap">
                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                    <i class="mdi mdi-magnify me-1"></i>
                                    Buscar
                                </button>
                                <a href="{{ route('ordens-servico.index') }}" class="btn btn-sm btn-outline-secondary js-list-filter-clear d-none">
                                    <i class="mdi mdi-refresh me-1"></i>
                                    Limpar
                                </a>
                            </div>
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
                    <i class="mdi mdi-cog text-primary me-2"></i>
                    Lista de Ordens de Serviço
                </h5>
                <span class="badge bg-primary">{{ $ordensServico->total() }} ordens</span>
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
                                    <th width="120">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ordensServico as $ordem)
                                    <tr data-id="{{ $ordem->id }}">
                                        <td>
                                            <div>
                                                <h6 class="mb-0">#{{ str_pad($ordem->id, 6, '0', STR_PAD_LEFT) }}</h6>
                                                <small class="text-muted">{{ $ordem->created_at->format('d/m/Y H:i') }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <h6 class="mb-0">{{ $ordem->pedido->cliente->nome ?? 'N/A' }}</h6>
                                                <small class="text-muted">Venda: #{{ $ordem->pedido->numero ?? 'N/A' }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <h6 class="mb-0">{{ $ordem->fornecedor->razao_social ?? 'N/A' }}</h6>
                                                @if ($ordem->fornecedor && $ordem->fornecedor->nome_fantasia)
                                                    <small class="text-muted">{{ $ordem->fornecedor->nome_fantasia }}</small>
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
                                                    <small class="text-muted">{{ $ordem->entrega_em->format('H:i') }}</small>
                                                </div>
                                            @else
                                                <span class="text-muted">Não definida</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $ordem->status_class }}">
                                                {{ $ordem->status_label }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $ordem->prioridade_class }}">
                                                {{ $ordem->prioridade_label }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('ordens-servico.show', $ordem) }}"
                                                    class="btn btn-sm btn-outline-primary" title="Visualizar">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
                                                <a href="{{ route('ordens-servico.edit', $ordem) }}"
                                                    class="btn btn-sm btn-outline-secondary" title="Editar">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger"
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
                        <i class="mdi mdi-cog text-muted" style="font-size: 3rem;"></i>
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
        function confirmDelete(ordemId, ordemNumber) {
            document.getElementById('deleteOrdemNumber').textContent = ordemNumber;
            const form = document.getElementById('deleteForm');
            form.action = `/ordens-servico/${ordemId}`;
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }
    </script>
@endpush
