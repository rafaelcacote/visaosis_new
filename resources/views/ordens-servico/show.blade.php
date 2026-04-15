@extends('layouts.app')

@section('title', 'Ordem de Serviço #' . str_pad($ordemServico->id, 6, '0', STR_PAD_LEFT) . ' - Connect Plus')

@section('content')
    <div class="d-xl-flex justify-content-between align-items-start mb-4">
        <div>
            <h2 class="text-dark font-weight-bold mb-2">
                <i class="mdi mdi-cog me-2"></i>
                Ordem de Serviço #{{ str_pad($ordemServico->id, 6, '0', STR_PAD_LEFT) }}
            </h2>
            <p class="text-muted mb-0">
                Criada em {{ $ordemServico->created_at->format('d/m/Y H:i') }}
                @if ($ordemServico->user)
                    · por {{ $ordemServico->user->name }}
                @endif
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('ordens-servico.edit', $ordemServico) }}" class="btn btn-primary">
                <i class="mdi mdi-pencil me-2"></i>
                Editar
            </a>
            <a href="{{ route('ordens-servico.index') }}" class="btn btn-outline-secondary">
                <i class="mdi mdi-arrow-left me-2"></i>
                Voltar
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-5 mb-4">
            <!-- Cliente e venda -->
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

            <!-- Prescrição -->
            @if ($ordemServico->prescricao)
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="mdi mdi-glasses text-info me-2"></i>
                            Prescrição
                        </h5>

                        <!-- Dados básicos -->
                        <div class="mb-3">

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
            <!-- Itens da ordem -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="mdi mdi-format-list-bulleted text-primary me-2"></i>
                        Itens da ordem
                        <span class="badge bg-secondary ms-2">{{ $ordemServico->itensOrdem->count() }} itens</span>
                    </h5>

                    @if ($ordemServico->itensOrdem->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Produto</th>
                                        <th>Categoria</th>
                                        <th width="100">Quantidade</th>
                                        <th width="120" class="text-end">Preço unit.</th>
                                        <th width="120" class="text-end">Total</th>
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
                                            <td class="text-end">
                                                @if ($ip && $ip->preco_unit)
                                                    R$ {{ number_format($ip->preco_unit, 2, ',', '.') }}
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @if ($ip && $ip->total_linha)
                                                    <strong class="text-success">R$
                                                        {{ number_format($ip->total_linha, 2, ',', '.') }}</strong>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
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

            <!-- Dados da ordem -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="mdi mdi-cog text-primary me-2"></i>
                        Dados da ordem
                    </h5>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="text-muted small">Fornecedor / Laboratório</label>
                            <p class="mb-0">
                                @if ($ordemServico->fornecedor)
                                    {{ $ordemServico->fornecedor->razao_social }}
                                    @if ($ordemServico->fornecedor->nome_fantasia)
                                        ({{ $ordemServico->fornecedor->nome_fantasia }})
                                    @endif
                                @else
                                    <span class="text-muted">Não definido</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">Quantidade</label>
                            <p class="mb-0">{{ $ordemServico->quantidade ?? '—' }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">Prioridade</label>
                            <div>
                                <span class="badge bg-{{ $ordemServico->prioridade_class }} fs-6">
                                    {{ $ordemServico->prioridade_label }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">Status</label>
                            <div>
                                <span class="badge bg-{{ $ordemServico->status_class }} fs-6">
                                    {{ $ordemServico->status_label }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">Preço unitário</label>
                            <p class="mb-0">
                                @if ($ordemServico->preco_unit)
                                    R$ {{ number_format($ordemServico->preco_unit, 2, ',', '.') }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">Desconto</label>
                            <p class="mb-0">
                                @if ($ordemServico->desconto)
                                    R$ {{ number_format($ordemServico->desconto, 2, ',', '.') }}
                                @else
                                    <span class="text-muted">R$ 0,00</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">Total (prévia)</label>
                            <p class="mb-0">
                                @if ($ordemServico->total_linha)
                                    <strong class="text-success">R$
                                        {{ number_format($ordemServico->total_linha, 2, ',', '.') }}</strong>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="text-muted small">Data de entrega</label>
                            <p class="mb-0">
                                @if ($ordemServico->entrega_em)
                                    {{ $ordemServico->entrega_em->format('d/m/Y H:i') }}
                                @else
                                    <span class="text-muted">Não definida</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    @if ($ordemServico->observacoes)
                        <div class="row">
                            <div class="col-12 mb-4">
                                <label class="text-muted small">Observações</label>
                                <p class="mb-0">{{ $ordemServico->observacoes }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-0">
                                <label class="text-muted small">Criada em</label>
                                <p class="mb-0">{{ $ordemServico->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-0">
                                <label class="text-muted small">Última atualização</label>
                                <p class="mb-0">{{ $ordemServico->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
