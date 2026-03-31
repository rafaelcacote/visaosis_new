<tr>
    <td>
        <div class="d-flex align-items-center">
            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3"
                style="width: 40px; height: 40px; font-size: 16px; font-weight: 600;">
                {{ strtoupper(mb_substr($pessoa->nome, 0, 1, 'UTF-8')) }}
            </div>
            <div>
                <div class="font-weight-medium">{{ $pessoa->nome }}</div>
                @if ($pessoa->nascimento_em)
                    <small class="text-muted d-block mt-1">
                        <i class="mdi mdi-cake-variant-outline me-1" style="font-size: 14px;"></i>
                        {{ $pessoa->idade }} anos
                    </small>
                @endif
            </div>
        </div>
    </td>
    <td>
        <span class="text-muted">{{ $pessoa->cpf_formatado ?: 'Não informado' }}</span>
    </td>
    <td>
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
    <td>
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
    <td>
        <div class="actions">
            <a href="{{ route('pessoas.show', $pessoa->id) }}" class="btn-action btn-action-view" title="Visualizar">
                <i class="mdi mdi-eye"></i>
            </a>
            <a href="{{ route('pessoas.edit', $pessoa->id) }}" class="btn-action btn-action-edit" title="Editar">
                <i class="mdi mdi-pencil"></i>
            </a>
            <a href="{{ route('pessoas.receitas', $pessoa->id) }}" class="btn-action btn-action-view" title="Receitas">
                <i class="mdi mdi-file-document-outline"></i>
            </a>
            <button type="button"
                class="btn-action {{ $pessoa->ativo ? 'btn-action-status-desativar' : 'btn-action-status-ativar' }}"
                title="{{ $pessoa->ativo ? 'Desativar' : 'Ativar' }}" data-toggle-status
                data-pessoa-id="{{ $pessoa->id }}" data-novo-status="{{ $pessoa->ativo ? 'false' : 'true' }}"
                data-pessoa-nome="{{ $pessoa->nome }}">
                <i class="mdi mdi-{{ $pessoa->ativo ? 'close-circle' : 'check-circle' }}"></i>
            </button>
            <button type="button" class="btn-action btn-action-delete" title="Excluir" data-bs-toggle="modal"
                data-bs-target="#deletePacienteModal" data-pessoa-id="{{ $pessoa->id }}"
                data-pessoa-nome="{{ $pessoa->nome }}">
                <i class="mdi mdi-delete"></i>
            </button>
        </div>
    </td>
</tr>
