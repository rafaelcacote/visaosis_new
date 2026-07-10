<tr>
    <td>
        <div class="d-flex align-items-center">
            @if($produto->imagem_principal_url)
                <div class="produto-list-thumb me-3" title="{{ $produto->nome }}">
                    <img src="{{ $produto->imagem_principal_url }}"
                         alt="{{ $produto->nome }}"
                         class="produto-list-thumb__img"
                         loading="lazy">
                </div>
            @else
                <div class="produto-list-thumb produto-list-thumb--placeholder me-3" aria-hidden="true">
                    <i class="mdi mdi-package-variant"></i>
                </div>
            @endif
            <div class="min-w-0">
                <div class="font-weight-medium text-truncate">{{ $produto->nome }}</div>
                @if($produto->marca)
                    <small class="text-muted text-truncate d-block">{{ $produto->marca }}</small>
                @endif
            </div>
        </div>
    </td>
    <td>
        {{ $produto->categoria ? $produto->categoria->descricao : '—' }}
    </td>
    <td>
        <div>
            @if($produto->preco_custo !== null)
                <small class="text-muted">Custo:</small> {{ $produto->preco_custo_formatado }}<br>
            @endif
            <small class="text-muted">Venda:</small> {{ $produto->preco_venda_formatado }}
        </div>
    </td>
    <td>
        @if($produto->ativo)
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
            <a href="{{ route('produtos.show', $produto->id) }}"
               class="btn-action btn-action-view"
               title="Visualizar">
                <i class="mdi mdi-eye"></i>
            </a>
            <a href="{{ route('produtos.edit', $produto->id) }}"
               class="btn-action btn-action-edit"
               title="Editar">
                <i class="mdi mdi-pencil"></i>
            </a>
            <button type="button"
                    class="btn-action {{ $produto->ativo ? 'btn-action-status-desativar' : 'btn-action-status-ativar' }}"
                    title="{{ $produto->ativo ? 'Desativar' : 'Ativar' }}"
                    data-toggle-status
                    data-produto-id="{{ $produto->id }}"
                    data-novo-status="{{ $produto->ativo ? 'false' : 'true' }}"
                    data-produto-nome="{{ $produto->nome }}">
                <i class="mdi mdi-{{ $produto->ativo ? 'close-circle' : 'check-circle' }}"></i>
            </button>
            <button type="button"
                    class="btn-action btn-action-delete"
                    title="Excluir"
                    data-bs-toggle="modal"
                    data-bs-target="#deleteProdutoModal"
                    data-produto-id="{{ $produto->id }}"
                    data-produto-nome="{{ $produto->nome }}">
                <i class="mdi mdi-delete"></i>
            </button>
        </div>
    </td>
</tr>
