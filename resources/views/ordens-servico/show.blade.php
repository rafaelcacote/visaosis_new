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
            <a href="{{ route('ordens-servico.pdf', $ordemServico) }}" class="btn btn-success" target="_blank">
                <i class="mdi mdi-file-pdf me-2"></i>
                Gerar PDF
            </a>
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
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 44px;"></th>
                                            <th style="width: 90px;"></th>
                                            <th class="text-center">Esférico</th>
                                            <th class="text-center">Cilíndrico</th>
                                            <th class="text-center">Eixo</th>
                                            <th class="text-center">AV</th>
                                            <th class="text-center">DNP</th>
                                            <th class="text-center">Altura</th>
                                            <th class="text-center">Adição</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td rowspan="2" class="text-center fw-bold"
                                                style="writing-mode: vertical-rl; transform: rotate(180deg); letter-spacing: 0.12em; color: #0d6efd; border: 1px solid rgba(13, 110, 253, 0.45);">
                                                LONGE
                                            </td>
                                            <td class="text-center fw-semibold" style="white-space: nowrap;">
                                                <i class="mdi mdi-eye-outline me-1"></i>OD
                                            </td>
                                            <td class="text-center">{{ $ordemServico->prescricao->esfera_od ?? '-' }}</td>
                                            <td class="text-center">{{ $ordemServico->prescricao->cilindro_od ?? '-' }}
                                            </td>
                                            <td class="text-center">
                                                @if (!is_null($ordemServico->prescricao->eixo_od))
                                                    {{ $ordemServico->prescricao->eixo_od }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $ordemServico->prescricao->acuidade_od ?? '-' }}
                                            </td>
                                            <td class="text-center">{{ $ordemServico->prescricao->dnp_od ?? '-' }}</td>
                                            <td class="text-center">{{ $ordemServico->prescricao->altura_od ?? '-' }}</td>
                                            <td class="text-center">{{ $ordemServico->prescricao->adicao_od ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center fw-semibold" style="white-space: nowrap;">
                                                <i class="mdi mdi-eye-outline me-1"></i>OE
                                            </td>
                                            <td class="text-center">{{ $ordemServico->prescricao->esfera_oe ?? '-' }}</td>
                                            <td class="text-center">{{ $ordemServico->prescricao->cilindro_oe ?? '-' }}
                                            </td>
                                            <td class="text-center">
                                                @if (!is_null($ordemServico->prescricao->eixo_oe))
                                                    {{ $ordemServico->prescricao->eixo_oe }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $ordemServico->prescricao->acuidade_oe ?? '-' }}
                                            </td>
                                            <td class="text-center">{{ $ordemServico->prescricao->dnp_oe ?? '-' }}</td>
                                            <td class="text-center">{{ $ordemServico->prescricao->altura_oe ?? '-' }}</td>
                                            <td class="text-center">{{ $ordemServico->prescricao->adicao_oe ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td rowspan="2" class="text-center fw-bold"
                                                style="writing-mode: vertical-rl; transform: rotate(180deg); letter-spacing: 0.12em; color: #dc3545; border: 1px solid rgba(220, 53, 69, 0.45);">
                                                PERTO
                                            </td>
                                            <td class="text-center fw-semibold" style="white-space: nowrap;">
                                                <i class="mdi mdi-eye-outline me-1"></i>OD
                                            </td>
                                            <td class="text-center">
                                                {{ $ordemServico->prescricao->esfera_od_perto ?? '-' }}</td>
                                            <td class="text-center">
                                                {{ $ordemServico->prescricao->cilindro_od_perto ?? '-' }}</td>
                                            <td class="text-center">
                                                @if (!is_null($ordemServico->prescricao->eixo_od_perto))
                                                    {{ $ordemServico->prescricao->eixo_od_perto }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                {{ $ordemServico->prescricao->acuidade_od_perto ?? '-' }}</td>
                                            <td class="text-center">{{ $ordemServico->prescricao->dnp_od_perto ?? '-' }}
                                            </td>
                                            <td class="text-center">
                                                {{ $ordemServico->prescricao->altura_od_perto ?? '-' }}</td>
                                            <td class="text-center">
                                                {{ $ordemServico->prescricao->adicao_od_perto ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center fw-semibold" style="white-space: nowrap;">
                                                <i class="mdi mdi-eye-outline me-1"></i>OE
                                            </td>
                                            <td class="text-center">
                                                {{ $ordemServico->prescricao->esfera_oe_perto ?? '-' }}</td>
                                            <td class="text-center">
                                                {{ $ordemServico->prescricao->cilindro_oe_perto ?? '-' }}</td>
                                            <td class="text-center">
                                                @if (!is_null($ordemServico->prescricao->eixo_oe_perto))
                                                    {{ $ordemServico->prescricao->eixo_oe_perto }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                {{ $ordemServico->prescricao->acuidade_oe_perto ?? '-' }}</td>
                                            <td class="text-center">{{ $ordemServico->prescricao->dnp_oe_perto ?? '-' }}
                                            </td>
                                            <td class="text-center">
                                                {{ $ordemServico->prescricao->altura_oe_perto ?? '-' }}</td>
                                            <td class="text-center">
                                                {{ $ordemServico->prescricao->adicao_oe_perto ?? '-' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
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
                                @switch($ordemServico->prioridade)
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
                                        <span class="text-muted">{{ $ordemServico->prioridade_label }}</span>
                                @endswitch
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">Status</label>
                            <div>
                                @switch($ordemServico->status)
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
                                        <span class="text-muted">{{ $ordemServico->status_label }}</span>
                                @endswitch
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
