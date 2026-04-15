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

                    <!-- Dados do Cliente -->
                    <div class="mb-3">
                        <h6 class="mb-2">
                            <i class="mdi mdi-account-circle text-primary me-1"></i>
                            {{ $ordemServico->pedido->cliente->nome ?? '—' }}
                        </h6>

                        @if ($ordemServico->pedido->cliente)
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted d-block">
                                        CPF: {{ $ordemServico->pedido->cliente->cpf_formatado ?? 'Não informado' }}
                                    </small>
                                </div>

                                <div class="col-md-6">
                                    @if ($ordemServico->pedido->cliente->telefone_formatado)
                                        <small class="text-muted d-block">
                                            Tel: {{ $ordemServico->pedido->cliente->telefone_formatado }}
                                        </small>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    <hr class="my-3">

                    <!-- Dados da Venda -->
                    <div>
                        <h6 class="mb-2">
                            <i class="mdi mdi-receipt text-info me-1"></i>
                            Venda #{{ $ordemServico->pedido->numero ?? ($ordemServico->pedido->id ?? '—') }}
                        </h6>

                        @if ($ordemServico->pedido)
                            <div class="row">
                                <div class="col-6">
                                    <small class="text-muted d-block">Data:</small>
                                    <small
                                        class="fw-medium">{{ $ordemServico->pedido->data_pedido_formatada ?? 'N/A' }}</small>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Status:</small>
                                    <small class="fw-medium">
                                        @switch($ordemServico->pedido->status)
                                            @case('aberto')
                                                <span class="text-warning">{{ $ordemServico->pedido->status_label }}</span>
                                            @break

                                            @case('faturado')
                                                <span class="text-success">{{ $ordemServico->pedido->status_label }}</span>
                                            @break

                                            @case('cancelado')
                                                <span class="text-danger">{{ $ordemServico->pedido->status_label }}</span>
                                            @break

                                            @default
                                                {{ $ordemServico->pedido->status_label }}
                                        @endswitch
                                    </small>
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-6">
                                    <small class="text-muted d-block">Valor Total:</small>
                                    <small
                                        class="fw-medium text-success">{{ $ordemServico->pedido->valor_total_formatado ?? 'R$ 0,00' }}</small>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Itens:</small>
                                    <small class="fw-medium">{{ $ordemServico->pedido->itens->count() ?? 0 }}
                                        item(s)</small>
                                </div>
                            </div>

                            @if ($ordemServico->pedido->observacoes)
                                <div class="mt-2">
                                    <small class="text-muted d-block">Observações:</small>
                                    <small class="fw-medium">{{ $ordemServico->pedido->observacoes }}</small>
                                </div>
                            @endif
                        @else
                            <small class="text-muted">Dados da venda não disponíveis</small>
                        @endif
                    </div>
                </div>
            </div>

            @if ($ordemServico->prescricao)
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="mdi mdi-glasses text-info me-2"></i>
                            Prescrição
                        </h5>

                        <!-- Dados básicos -->
                        <div class="mb-3">
                            <h6 class="mb-2">
                                <i class="mdi mdi-file-document-outline text-info me-1"></i>
                                Prescrição #{{ $ordemServico->prescricao->id }}
                            </h6>
                            <div class="row">

                                <div class="col-md-6">
                                    <small class="text-muted d-block">
                                        Paciente: {{ $ordemServico->prescricao->paciente->nome ?? '—' }}
                                    </small>
                                    @if ($ordemServico->prescricao->paciente && $ordemServico->prescricao->paciente->cpf)
                                        <small class="text-muted d-block">
                                            CPF: {{ $ordemServico->prescricao->paciente->cpf_formatado }}
                                        </small>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">
                                        Criada em:
                                        {{ $ordemServico->prescricao->created_at ? $ordemServico->prescricao->created_at->format('d/m/Y H:i') : 'N/A' }}
                                    </small>
                                </div>
                            </div>
                        </div>

                        <hr class="my-3">

                        <!-- Graduação -->
                        <div class="mb-3">
                            <h6 class="mb-2">
                                <i class="mdi mdi-eye-settings text-primary me-1"></i>
                                Graduação
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong class="text-primary">Olho Direito (OD)</strong>
                                    <div class="mt-1">
                                        <small class="text-muted d-block">Esfera:
                                            <span
                                                class="fw-medium">{{ $ordemServico->prescricao->esfera_od ?? '0.00' }}</span>
                                        </small>
                                        <small class="text-muted d-block">Cilindro:
                                            <span
                                                class="fw-medium">{{ $ordemServico->prescricao->cilindro_od ?? '0.00' }}</span>
                                        </small>
                                        <small class="text-muted d-block">Eixo:
                                            <span class="fw-medium">{{ $ordemServico->prescricao->eixo_od ?? '0' }}°</span>
                                        </small>
                                        @if ($ordemServico->prescricao->dnp_od)
                                            <small class="text-muted d-block">DNP:
                                                <span class="fw-medium">{{ $ordemServico->prescricao->dnp_od }}</span>
                                            </small>
                                        @endif
                                        @if ($ordemServico->prescricao->altura_od)
                                            <small class="text-muted d-block">Altura:
                                                <span class="fw-medium">{{ $ordemServico->prescricao->altura_od }}</span>
                                            </small>
                                        @endif
                                        @if ($ordemServico->prescricao->adicao_od)
                                            <small class="text-muted d-block">Adição:
                                                <span class="fw-medium">{{ $ordemServico->prescricao->adicao_od }}</span>
                                            </small>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <strong class="text-success">Olho Esquerdo (OE)</strong>
                                    <div class="mt-1">
                                        <small class="text-muted d-block">Esfera:
                                            <span
                                                class="fw-medium">{{ $ordemServico->prescricao->esfera_oe ?? '0.00' }}</span>
                                        </small>
                                        <small class="text-muted d-block">Cilindro:
                                            <span
                                                class="fw-medium">{{ $ordemServico->prescricao->cilindro_oe ?? '0.00' }}</span>
                                        </small>
                                        <small class="text-muted d-block">Eixo:
                                            <span class="fw-medium">{{ $ordemServico->prescricao->eixo_oe ?? '0' }}°</span>
                                        </small>
                                        @if ($ordemServico->prescricao->dnp_oe)
                                            <small class="text-muted d-block">DNP:
                                                <span class="fw-medium">{{ $ordemServico->prescricao->dnp_oe }}</span>
                                            </small>
                                        @endif
                                        @if ($ordemServico->prescricao->altura_oe)
                                            <small class="text-muted d-block">Altura:
                                                <span class="fw-medium">{{ $ordemServico->prescricao->altura_oe }}</span>
                                            </small>
                                        @endif
                                        @if ($ordemServico->prescricao->adicao_oe)
                                            <small class="text-muted d-block">Adição:
                                                <span class="fw-medium">{{ $ordemServico->prescricao->adicao_oe }}</span>
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informações adicionais -->
                        @if (
                            $ordemServico->prescricao->tipo_lente ||
                                $ordemServico->prescricao->validade_dias ||
                                $ordemServico->prescricao->especialista_externo)
                            <hr class="my-3">
                            <div class="mb-3">
                                <h6 class="mb-2">
                                    <i class="mdi mdi-information-outline text-secondary me-1"></i>
                                    Informações Adicionais
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        @if ($ordemServico->prescricao->tipo_lente)
                                            <small class="text-muted d-block">Tipo de Lente:
                                                <span class="fw-medium">{{ $ordemServico->prescricao->tipo_lente }}</span>
                                            </small>
                                        @endif
                                    </div>

                                    <div class="col-md-6">
                                        @if ($ordemServico->prescricao->validade_dias)
                                            <small class="text-muted d-block">Validade:
                                                <span class="fw-medium">{{ $ordemServico->prescricao->validade_dias }}
                                                    dias</span>
                                            </small>
                                        @endif
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        @if ($ordemServico->prescricao->especialista_externo)
                                            <small class="text-muted d-block">Especialista:
                                                <span
                                                    class="fw-medium">{{ $ordemServico->prescricao->especialista_externo }}</span>
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Diagnóstico e observações -->
                        @if (
                            $ordemServico->prescricao->diagnostico ||
                                $ordemServico->prescricao->observacoes ||
                                $ordemServico->prescricao->recomendacoes)
                            <hr class="my-3">
                            <div>
                                <h6 class="mb-2">
                                    <i class="mdi mdi-note-text-outline text-warning me-1"></i>
                                    Observações Clínicas
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        @if ($ordemServico->prescricao->diagnostico)
                                            <div class="mb-2">
                                                <small class="text-muted d-block">Diagnóstico:</small>
                                                <small
                                                    class="fw-medium">{{ $ordemServico->prescricao->diagnostico }}</small>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-md-6">
                                        @if ($ordemServico->prescricao->recomendacoes)
                                            <div class="mb-2">
                                                <small class="text-muted d-block">Recomendações:</small>
                                                <small
                                                    class="fw-medium">{{ $ordemServico->prescricao->recomendacoes }}</small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        @if ($ordemServico->prescricao->observacoes)
                                            <div class="mb-2">
                                                <small class="text-muted d-block">Observações:</small>
                                                <small
                                                    class="fw-medium">{{ $ordemServico->prescricao->observacoes }}</small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-7">
            <!-- Itens da ordem (movido para cá) -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="mdi mdi-format-list-bulleted text-primary me-2"></i>
                        Itens da ordem
                    </h5>

                    @if ($ordemServico->itensOrdem->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Produto</th>
                                        <th>Categoria</th>
                                        <th width="100">Quantidade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ordemServico->itensOrdem as $linha)
                                        @php $ip = $linha->item; @endphp
                                        <tr>
                                            <td>
                                                <div>
                                                    <h6 class="mb-1">
                                                        {{ $ip?->produto->nome ?? 'Item #' . ($ip->id ?? $linha->item_id) }}
                                                    </h6>
                                                </div>
                                            </td>
                                            <td>
                                                @if ($ip?->produto?->categoria)
                                                    <span
                                                        class="badge bg-light text-dark">{{ $ip->produto->categoria->nome }}</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="fw-medium">{{ $ip?->quantidade ?? '—' }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="mdi mdi-package-variant-closed text-muted" style="font-size: 2rem;"></i>
                            <h6 class="mt-2 text-muted">Nenhum item vinculado</h6>
                            <small class="text-muted">Esta ordem não possui itens associados</small>
                        </div>
                    @endif
                </div>
            </div>
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

                    <form action="{{ route('ordens-servico.update', $ordemServico) }}" method="POST"
                        id="formEditOrdem">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="fornecedor_id" class="form-label">
                                    Fornecedor / Laboratório <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('fornecedor_id') is-invalid @enderror"
                                    id="fornecedor_id" name="fornecedor_id" required>
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
                                <label for="quantidade" class="form-label">Quantidade <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('quantidade') is-invalid @enderror"
                                    id="quantidade" name="quantidade" min="1" required
                                    value="{{ old('quantidade', $ordemServico->quantidade) }}">
                                @error('quantidade')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="prioridade" class="form-label">Prioridade <span
                                        class="text-danger">*</span></label>
                                <select class="form-select form-select-sm @error('prioridade') is-invalid @enderror"
                                    id="prioridade" name="prioridade" required>
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
                                <label for="status" class="form-label">Status <span
                                        class="text-danger">*</span></label>
                                <select class="form-select form-select-sm @error('status') is-invalid @enderror"
                                    id="status" name="status" required>
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
                                <label for="preco_unit" class="form-label">Preço unitário <span
                                        class="text-danger">*</span></label>
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
                                <input type="datetime-local"
                                    class="form-control @error('entrega_em') is-invalid @enderror" id="entrega_em"
                                    name="entrega_em"
                                    value="{{ old('entrega_em', $ordemServico->entrega_em ? $ordemServico->entrega_em->format('Y-m-d\TH:i') : '') }}">
                                @error('entrega_em')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-4">
                                <label for="observacoes" class="form-label">Observações</label>
                                <textarea class="form-control @error('observacoes') is-invalid @enderror" id="observacoes" name="observacoes"
                                    rows="3" placeholder="Observações adicionais...">{{ old('observacoes', $ordemServico->observacoes) }}</textarea>
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
        (function() {
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

            [q, pu, d].forEach(function(el) {
                el.addEventListener('input', refresh);
            });
            refresh();
        })();
    </script>
@endpush
