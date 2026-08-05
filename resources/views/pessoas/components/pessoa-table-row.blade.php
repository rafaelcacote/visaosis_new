<tr onclick="window.location='{{ route('pessoas.show', $pessoa->id) }}'" style="cursor: pointer;">
    <td class="list-actions-col-descricao">
        <div class="d-flex align-items-center">
            <div class="bg-primary text-white rounded-circle d-none d-md-inline-flex align-items-center justify-content-center me-3 flex-shrink-0"
                style="width: 40px; height: 40px; font-size: 16px; font-weight: 600;">
                {{ strtoupper(mb_substr($pessoa->nome, 0, 1, 'UTF-8')) }}
            </div>
            <div class="list-actions-desc-text">
                <div class="font-weight-medium">{{ $pessoa->nome }}</div>
                @if ($pessoa->nascimento_em)
                    <small class="text-muted d-block mt-1">
                        <i class="mdi mdi-cake-variant-outline me-1" style="font-size: 14px;"></i>
                        {{ $pessoa->idade }} anos
                    </small>
                @endif
                <small class="text-muted d-md-none d-block mt-1">
                    @if ($pessoa->telefone_formatado)
                        {{ $pessoa->telefone_formatado }}
                    @elseif ($pessoa->email)
                        {{ $pessoa->email }}
                    @else
                        Sem contato
                    @endif
                </small>
            </div>
        </div>
    </td>
    <td class="d-none d-md-table-cell">
        <span class="text-muted">{{ $pessoa->cpf_formatado ?: 'Não informado' }}</span>
    </td>
    <td class="d-none d-md-table-cell">
        <div>
            @if ($pessoa->telefone_formatado)
                <small class="text-muted">Tel:</small>
                {{ $pessoa->telefone_formatado }}<br>
            @endif
            @if ($pessoa->email)
                <small class="text-muted">Email:</small>
                {{ $pessoa->email }}
            @endif
            @if (!$pessoa->telefone_formatado && !$pessoa->email)
                <span class="text-muted small">Sem contato</span>
            @endif
        </div>
    </td>
    <td class="d-none d-md-table-cell">
        @if ($pessoa->ativo)
            <span class="tag tag-status tag-status-ativo">
                <i class="mdi mdi-check-circle"></i>
                Ativo
            </span>
        @else
            <span class="tag tag-status tag-status-inativo">
                <i class="mdi mdi-close-circle"></i>
                Inativo
            </span>
        @endif
    </td>
    <td class="list-actions-col-acoes">
        <div class="actions list-actions-buttons">
            <a href="{{ route('pessoas.show', $pessoa->id) }}" class="btn-action btn-action-view" title="Visualizar"
                onclick="event.stopPropagation();">
                <i class="mdi mdi-eye"></i>
            </a>
        </div>
    </td>
</tr>
