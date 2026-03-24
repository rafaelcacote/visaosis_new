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

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="mdi mdi-flag text-primary me-2"></i>
                    Status e prioridade
                </h5>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="badge bg-{{ $ordemServico->status_class }} fs-6">
                        {{ $ordemServico->status_label }}
                    </span>
                    <span class="badge bg-{{ $ordemServico->prioridade_class }} fs-6">
                        {{ $ordemServico->prioridade_label }}
                    </span>
                </div>
                <div class="form-group mb-0">
                    <label class="text-muted small">Entrega prevista</label>
                    <p class="font-weight-medium mb-0">
                        @if ($ordemServico->entrega_em)
                            {{ $ordemServico->entrega_em->format('d/m/Y H:i') }}
                        @else
                            <span class="text-muted">Não definida</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="mdi mdi-account text-primary me-2"></i>
                    Cliente e venda
                </h5>
                <div class="form-group">
                    <label class="text-muted small">Cliente</label>
                    <p class="font-weight-medium mb-2">{{ $ordemServico->pedido->cliente->nome ?? '—' }}</p>
                </div>
                <div class="form-group mb-0">
                    <label class="text-muted small">Venda</label>
                    <p class="font-weight-medium mb-0">
                        @if ($ordemServico->pedido)
                            #{{ $ordemServico->pedido->numero ?? $ordemServico->pedido->id }}
                            <span class="text-muted">(ID {{ $ordemServico->pedido->id }})</span>
                        @else
                            —
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="mdi mdi-flask text-primary me-2"></i>
                    Fornecedor
                </h5>
                @if ($ordemServico->fornecedor)
                    <p class="font-weight-medium mb-1">{{ $ordemServico->fornecedor->razao_social }}</p>
                    @if ($ordemServico->fornecedor->nome_fantasia)
                        <p class="text-muted small mb-0">{{ $ordemServico->fornecedor->nome_fantasia }}</p>
                    @endif
                @else
                    <p class="text-muted mb-0">—</p>
                @endif
            </div>
        </div>
    </div>
</div>

@if ($ordemServico->prescricao)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="mdi mdi-glasses text-info me-2"></i>
                        Prescrição #{{ $ordemServico->prescricao->id }}
                    </h5>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="text-muted small">Paciente</label>
                            <p class="font-weight-medium mb-0">
                                {{ $ordemServico->prescricao->paciente->nome ?? '—' }}
                            </p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">OD</label>
                            <p class="mb-0">
                                Esf. {{ $ordemServico->prescricao->esfera_od ?? '—' }}
                                · Cil. {{ $ordemServico->prescricao->cilindro_od ?? '—' }}
                                · Eixo {{ $ordemServico->prescricao->eixo_od ?? '—' }}
                            </p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">OE</label>
                            <p class="mb-0">
                                Esf. {{ $ordemServico->prescricao->esfera_oe ?? '—' }}
                                · Cil. {{ $ordemServico->prescricao->cilindro_oe ?? '—' }}
                                · Eixo {{ $ordemServico->prescricao->eixo_oe ?? '—' }}
                            </p>
                        </div>
                    </div>
                    @if ($ordemServico->prescricao->diagnostico)
                        <div class="mt-3">
                            <label class="text-muted small">Diagnóstico</label>
                            <p class="mb-0">{{ $ordemServico->prescricao->diagnostico }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif

<div class="row mt-4">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="mdi mdi-currency-usd text-success me-2"></i>
                    Valores
                </h5>
                <div class="row">
                    <div class="col-6">
                        <label class="text-muted small">Quantidade</label>
                        <p class="font-weight-medium">{{ $ordemServico->quantidade }}</p>
                    </div>
                    <div class="col-6">
                        <label class="text-muted small">Preço unitário</label>
                        <p class="font-weight-medium">{{ $ordemServico->preco_unit_formatado }}</p>
                    </div>
                    <div class="col-6">
                        <label class="text-muted small">Desconto</label>
                        <p class="font-weight-medium">{{ $ordemServico->desconto_formatado }}</p>
                    </div>
                    <div class="col-6">
                        <label class="text-muted small">Total</label>
                        <p class="font-weight-bold text-success mb-0">{{ $ordemServico->total_formatado }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="mdi mdi-text-box-outline me-2"></i>
                    Observações
                </h5>
                @if ($ordemServico->observacoes)
                    <p class="mb-0">{{ $ordemServico->observacoes }}</p>
                @else
                    <p class="text-muted mb-0">Nenhuma observação.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="mdi mdi-format-list-bulleted text-primary me-2"></i>
                    Itens vinculados à ordem
                </h5>
                <span class="badge bg-secondary">{{ $ordemServico->itensOrdem->count() }} itens</span>
            </div>
            <div class="card-body p-0">
                @if ($ordemServico->itensOrdem->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Produto</th>
                                    <th>Categoria</th>
                                    <th class="text-end">Qtd. venda</th>
                                    <th class="text-end">Preço unit.</th>
                                    <th class="text-end">Total linha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ordemServico->itensOrdem as $linha)
                                    @php $ip = $linha->item; @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $ip?->produto->nome ?? '—' }}</strong>
                                        </td>
                                        <td>{{ $ip?->produto?->categoria?->nome ?? '—' }}</td>
                                        <td class="text-end">{{ $ip?->quantidade ?? '—' }}</td>
                                        <td class="text-end">
                                            @if ($ip)
                                                R$ {{ number_format($ip->preco_unit, 2, ',', '.') }}
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if ($ip)
                                                R$ {{ number_format($ip->total_linha, 2, ',', '.') }}
                                            @else
                                                —
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-4 text-center text-muted">
                        Nenhum item vinculado.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
