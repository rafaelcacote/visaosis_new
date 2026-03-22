@extends('layouts.app')

@section('title', 'Editar Ordem #' . str_pad($ordemServico->id, 6, '0', STR_PAD_LEFT) . ' - Connect Plus')

@section('content')
<div class="d-xl-flex justify-content-between align-items-start mb-4">
    <div>
        <h2 class="text-dark font-weight-bold mb-2">
            <i class="mdi mdi-pencil me-2"></i>
            Editar Ordem de Serviço #{{ str_pad($ordemServico->id, 6, '0', STR_PAD_LEFT) }}
        </h2>
        <p class="text-muted mb-0">Altere fornecedor, valores, status e demais dados editáveis</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('ordens-servico.show', $ordemServico) }}" class="btn btn-outline-primary">
            <i class="mdi mdi-eye me-2"></i>
            Visualizar
        </a>
        <a href="{{ route('ordens-servico.index') }}" class="btn btn-outline-secondary">
            <i class="mdi mdi-arrow-left me-2"></i>
            Voltar
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-5 mb-4">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="mdi mdi-account text-primary me-2"></i>
                    Cliente e venda
                </h5>
                <p class="mb-1"><strong>{{ $ordemServico->pedido->cliente->nome ?? '—' }}</strong></p>
                <p class="text-muted small mb-0">
                    Venda:
                    @if ($ordemServico->pedido)
                        #{{ $ordemServico->pedido->numero ?? $ordemServico->pedido->id }}
                        (ID {{ $ordemServico->pedido->id }})
                    @else
                        —
                    @endif
                </p>
            </div>
        </div>

        @if ($ordemServico->prescricao)
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="mdi mdi-glasses text-info me-2"></i>
                        Prescrição
                    </h5>
                    <p class="mb-1">Prescrição #{{ $ordemServico->prescricao->id }}</p>
                    <p class="text-muted small mb-0">
                        Paciente: {{ $ordemServico->prescricao->paciente->nome ?? '—' }}
                    </p>
                </div>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="mdi mdi-format-list-bulleted me-2"></i>
                    Itens da ordem (somente leitura)
                </h5>
            </div>
            <div class="card-body p-0">
                @if ($ordemServico->itensOrdem->count() > 0)
                    <ul class="list-group list-group-flush">
                        @foreach ($ordemServico->itensOrdem as $linha)
                            @php $ip = $linha->item; @endphp
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div>
                                    <strong>{{ $ip?->produto->nome ?? 'Item #' . ($ip->id ?? $linha->item_id) }}</strong>
                                    @if ($ip?->produto?->categoria)
                                        <br><small class="text-muted">{{ $ip->produto->categoria->nome }}</small>
                                    @endif
                                </div>
                                <span class="badge bg-light text-dark">Qtd {{ $ip?->quantidade ?? '—' }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="card-body text-muted">Nenhum item vinculado.</div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-4">
                    <i class="mdi mdi-cog text-primary me-2"></i>
                    Dados editáveis
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

                <form action="{{ route('ordens-servico.update', $ordemServico) }}" method="POST" id="formEditOrdem">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="fornecedor_id" class="form-label">
                                Fornecedor / Laboratório <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('fornecedor_id') is-invalid @enderror" id="fornecedor_id"
                                name="fornecedor_id" required>
                                <option value="">Selecione</option>
                                @foreach ($fornecedores as $fornecedor)
                                    <option value="{{ $fornecedor->id }}"
                                        {{ (string) old('fornecedor_id', $ordemServico->fornecedor_id) === (string) $fornecedor->id ? 'selected' : '' }}>
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

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="quantidade" class="form-label">Quantidade <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('quantidade') is-invalid @enderror"
                                id="quantidade" name="quantidade" min="1" required
                                value="{{ old('quantidade', $ordemServico->quantidade) }}">
                            @error('quantidade')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="prioridade" class="form-label">Prioridade <span class="text-danger">*</span></label>
                            <select class="form-select @error('prioridade') is-invalid @enderror" id="prioridade"
                                name="prioridade" required>
                                @foreach (\App\Models\OrdemServico::getPrioridadeOptions() as $val => $label)
                                    <option value="{{ $val }}"
                                        {{ old('prioridade', $ordemServico->prioridade) === $val ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('prioridade')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status"
                                required>
                                @foreach (\App\Models\OrdemServico::getStatusOptions() as $val => $label)
                                    <option value="{{ $val }}"
                                        {{ old('status', $ordemServico->status) === $val ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="preco_unit" class="form-label">Preço unitário <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" step="0.01" min="0"
                                    class="form-control @error('preco_unit') is-invalid @enderror" id="preco_unit"
                                    name="preco_unit" required
                                    value="{{ old('preco_unit', $ordemServico->preco_unit) }}">
                            </div>
                            @error('preco_unit')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="desconto" class="form-label">Desconto</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" step="0.01" min="0"
                                    class="form-control @error('desconto') is-invalid @enderror" id="desconto"
                                    name="desconto" value="{{ old('desconto', $ordemServico->desconto) }}">
                            </div>
                            @error('desconto')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="total_preview" class="form-label">Total (prévia)</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="text" class="form-control" id="total_preview" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="entrega_em" class="form-label">Data de entrega</label>
                            <input type="datetime-local" class="form-control @error('entrega_em') is-invalid @enderror"
                                id="entrega_em" name="entrega_em"
                                value="{{ old('entrega_em', $ordemServico->entrega_em ? $ordemServico->entrega_em->format('Y-m-d\TH:i') : '') }}">
                            @error('entrega_em')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-4">
                            <label for="observacoes" class="form-label">Observações</label>
                            <textarea class="form-control @error('observacoes') is-invalid @enderror" id="observacoes"
                                name="observacoes" rows="3"
                                placeholder="Observações adicionais...">{{ old('observacoes', $ordemServico->observacoes) }}</textarea>
                            @error('observacoes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('ordens-servico.show', $ordemServico) }}" class="btn btn-secondary">
                            <i class="mdi mdi-close-circle me-2"></i>
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-content-save me-2"></i>
                            Salvar alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        (function () {
            const q = document.getElementById('quantidade');
            const pu = document.getElementById('preco_unit');
            const d = document.getElementById('desconto');
            const out = document.getElementById('total_preview');

            function brl(n) {
                return (Number(n) || 0).toLocaleString('pt-BR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            function refresh() {
                const total = (parseFloat(pu.value) || 0) * (parseInt(q.value, 10) || 0) - (parseFloat(d.value) || 0);
                out.value = brl(Math.max(total, 0));
            }

            [q, pu, d].forEach(function (el) {
                el.addEventListener('input', refresh);
            });
            refresh();
        })();
    </script>
@endpush
