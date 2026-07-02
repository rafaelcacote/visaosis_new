<tr>
    <td>
        <div class="d-flex align-items-center">
            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-size: 16px; font-weight: 600;">
                {{ strtoupper(substr($especialidade->descricao, 0, 1)) }}
            </div>
            <div>
                <div class="font-weight-medium">{{ $especialidade->descricao }}</div>
            </div>
        </div>
    </td>
    <td>
        <div class="actions">
            <a href="{{ route('especialidades.show', $especialidade->id) }}"
               class="btn-action btn-action-view"
               title="Visualizar">
                <i class="mdi mdi-eye"></i>
            </a>
            <a href="{{ route('especialidades.edit', $especialidade->id) }}"
               class="btn-action btn-action-edit"
               title="Editar">
                <i class="mdi mdi-pencil"></i>
            </a>
            <button type="button"
                    class="btn-action btn-action-delete"
                    title="Excluir"
                    data-bs-toggle="modal"
                    data-bs-target="#deleteModal"
                    data-especialidade-id="{{ $especialidade->id }}"
                    data-especialidade-descricao="{{ $especialidade->descricao }}">
                <i class="mdi mdi-delete"></i>
            </button>
        </div>
    </td>
</tr>
