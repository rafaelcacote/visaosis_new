<tr>
    <td>
        <div class="d-flex align-items-center">
            <div class="bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-size: 16px;">
                <i class="mdi mdi-flask"></i>
            </div>
            <div>
                <div class="font-weight-medium">{{ $laboratorio->razao_social }}</div>
                @if($laboratorio->nome_fantasia)
                    <small class="text-muted">{{ $laboratorio->nome_fantasia }}</small>
                @endif
            </div>
        </div>
    </td>
    <td>
        {{ $laboratorio->cnpj_formatado ?: '—' }}
    </td>
    <td>
        @if($laboratorio->telefone || $laboratorio->email)
            @if($laboratorio->telefone)
                <div><i class="mdi mdi-phone me-1"></i>{{ $laboratorio->telefone }}</div>
            @endif
            @if($laboratorio->email)
                <div><i class="mdi mdi-email me-1"></i>{{ Str::limit($laboratorio->email, 25) }}</div>
            @endif
        @else
            <span class="text-muted">—</span>
        @endif
    </td>
    <td>
        @if($laboratorio->ativo)
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
            <a href="{{ route('laboratorios.show', $laboratorio->id) }}"
               class="btn-action btn-action-view"
               title="Visualizar">
                <i class="mdi mdi-eye"></i>
            </a>
            <a href="{{ route('laboratorios.edit', $laboratorio->id) }}"
               class="btn-action btn-action-edit"
               title="Editar">
                <i class="mdi mdi-pencil"></i>
            </a>
            <button type="button"
                    class="btn-action {{ $laboratorio->ativo ? 'btn-action-status-desativar' : 'btn-action-status-ativar' }}"
                    title="{{ $laboratorio->ativo ? 'Desativar' : 'Ativar' }}"
                    data-toggle-status
                    data-laboratorio-id="{{ $laboratorio->id }}"
                    data-novo-status="{{ $laboratorio->ativo ? 'false' : 'true' }}"
                    data-laboratorio-nome="{{ $laboratorio->nome }}">
                <i class="mdi mdi-{{ $laboratorio->ativo ? 'close-circle' : 'check-circle' }}"></i>
            </button>
            <button type="button"
                    class="btn-action btn-action-delete"
                    title="Excluir"
                    data-bs-toggle="modal"
                    data-bs-target="#deleteLaboratorioModal"
                    data-laboratorio-id="{{ $laboratorio->id }}"
                    data-laboratorio-nome="{{ $laboratorio->nome }}">
                <i class="mdi mdi-delete"></i>
            </button>
        </div>
    </td>
</tr>
