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
