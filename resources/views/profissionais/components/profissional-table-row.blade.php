<tr>
    <td>
        <div class="d-flex align-items-center">
            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-size: 16px; font-weight: 600;">
                {{ strtoupper(substr($profissional->nome, 0, 1)) }}
            </div>
            <div>
                <div class="font-weight-medium">{{ $profissional->nome }}</div>
                @if($profissional->registro_conselho)
                    <small class="text-muted d-block mt-1">
                        <i class="mdi mdi-certificate me-1" style="font-size: 14px;"></i>
                        {{ $profissional->registro_conselho }}
                    </small>
                @endif
            </div>
        </div>
    </td>
    <td>
        <span class="text-muted">{{ $profissional->especialidade->descricao ?? 'Não informado' }}</span>
    </td>
    <td>
        <span class="text-muted">{{ $profissional->cpf_formatado ?: 'Não informado' }}</span>
    </td>
    <td>
        <div>
            @if($profissional->telefone_formatado)
                <small class="text-muted">Tel:</small>
                {{ $profissional->telefone_formatado }}<br>
            @endif
            @if($profissional->email)
                <small class="text-muted">Email:</small>
                {{ $profissional->email }}
            @endif
        </div>
    </td>
    <td>
        @if($profissional->ativo)
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
            <a href="{{ route('profissionais.show', $profissional->id) }}" 
               class="btn-action btn-action-view" 
               title="Visualizar">
                <i class="mdi mdi-eye"></i>
            </a>
            <a href="{{ route('profissionais.edit', $profissional->id) }}" 
               class="btn-action btn-action-edit" 
               title="Editar">
                <i class="mdi mdi-pencil"></i>
            </a>
            <button type="button" 
                    class="btn-action {{ $profissional->ativo ? 'btn-action-status-desativar' : 'btn-action-status-ativar' }}" 
                    title="{{ $profissional->ativo ? 'Desativar' : 'Ativar' }}"
                    data-toggle-status
                    data-profissional-id="{{ $profissional->id }}"
                    data-novo-status="{{ $profissional->ativo ? 'false' : 'true' }}"
                    data-profissional-nome="{{ $profissional->nome }}">
                <i class="mdi mdi-{{ $profissional->ativo ? 'close-circle' : 'check-circle' }}"></i>
            </button>
            <button type="button" 
                    class="btn-action btn-action-delete" 
                    title="Excluir"
                    data-bs-toggle="modal"
                    data-bs-target="#deleteModal"
                    data-profissional-id="{{ $profissional->id }}"
                    data-profissional-nome="{{ $profissional->nome }}">
                <i class="mdi mdi-delete"></i>
            </button>
        </div>
    </td>
</tr>
