<tr>
    <td class="list-actions-col-descricao">
        <div class="d-flex align-items-center">
            <div class="bg-primary text-white rounded-circle d-none d-md-inline-flex align-items-center justify-content-center me-3 flex-shrink-0"
                style="width: 40px; height: 40px; font-size: 16px; font-weight: 600;">
                {{ strtoupper(substr($categoria->descricao, 0, 1)) }}
            </div>
            <div class="list-actions-desc-text">
                <div class="font-weight-medium">{{ $categoria->descricao }}</div>
            </div>
        </div>
    </td>
    <td class="d-none d-md-table-cell">
        @if ($categoria->ativo)
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
            <a href="{{ route('categorias.show', $categoria->id) }}" class="btn-action btn-action-view"
                title="Visualizar">
                <i class="mdi mdi-eye"></i>
            </a>
            <a href="{{ route('categorias.edit', $categoria->id) }}" class="btn-action btn-action-edit" title="Editar">
                <i class="mdi mdi-pencil"></i>
            </a>
            <button type="button"
                class="btn-action d-none d-md-inline-flex {{ $categoria->ativo ? 'btn-action-status-desativar' : 'btn-action-status-ativar' }}"
                title="{{ $categoria->ativo ? 'Desativar' : 'Ativar' }}" data-toggle-status
                data-categoria-id="{{ $categoria->id }}" data-novo-status="{{ $categoria->ativo ? 'false' : 'true' }}"
                data-categoria-descricao="{{ $categoria->descricao }}">
                <i class="mdi mdi-{{ $categoria->ativo ? 'close-circle' : 'check-circle' }}"></i>
            </button>
            <button type="button" class="btn-action btn-action-delete" title="Excluir" data-bs-toggle="modal"
                data-bs-target="#deleteModal" data-categoria-id="{{ $categoria->id }}"
                data-categoria-descricao="{{ $categoria->descricao }}">
                <i class="mdi mdi-delete"></i>
            </button>
        </div>
    </td>
</tr>
