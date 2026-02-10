@php
    $cpf_cnpj = null;
    if($user->cpf_cnpj) {
        $cpf_cnpj = preg_replace('/[^0-9]/', '', $user->cpf_cnpj);
        if(strlen($cpf_cnpj) == 11) {
            // CPF: 000.000.000-00
            $cpf_cnpj = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf_cnpj);
        } elseif(strlen($cpf_cnpj) == 14) {
            // CNPJ: 00.000.000/0000-00
            $cpf_cnpj = preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $cpf_cnpj);
        } else {
            $cpf_cnpj = $user->cpf_cnpj;
        }
    }
    
    $locations = $userLocations[$user->id] ?? collect();
@endphp

<tr>
    <td>
        #{{ $user->id }}
    </td>
    <td>
        <div class="font-weight-medium">{{ $user->name }}</div>
        <small class="text-muted d-block mt-1">
            <i class="mdi mdi-identifier me-1" style="font-size: 14px;"></i>
            {{ $cpf_cnpj ?? 'N/A' }}
        </small>
    </td>
    <td>
        <span class="text-muted">{{ $user->email }}</span>
    </td>
    <td>
        @if($locations->isEmpty())
            <span class="text-muted">
                <i class="mdi mdi-map-marker-off me-1"></i>
                Nenhuma localização
            </span>
        @else
            <div class="locations-list">
                @foreach($locations as $location)
                    <span class="tag tag-location">
                        <i class="mdi mdi-store"></i>
                        {{ $location->short_name ?? $location->name }}
                    </span>
                @endforeach
            </div>
        @endif
    </td>
    <td>
        @if($user->status == 1)
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
        <span class="text-muted timestamp-cell">
            {{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : 'N/A' }}
        </span>
    </td>
    <td>
        <div class="actions">
            <a href="{{ route('users.show', $user->id) }}" 
               class="btn-action btn-action-view" 
               title="Visualizar">
                <i class="mdi mdi-eye"></i>
            </a>
            <a href="{{ route('users.edit', $user->id) }}" 
               class="btn-action btn-action-edit" 
               title="Editar">
                <i class="mdi mdi-pencil"></i>
            </a>
            <button type="button" 
                    class="btn-action btn-action-password" 
                    title="Alterar Senha"
                    data-bs-toggle="modal"
                    data-bs-target="#changePasswordModal"
                    data-user-id="{{ $user->id }}"
                    data-user-name="{{ $user->name }}">
                <i class="mdi mdi-key"></i>
            </button>
            <form method="POST" action="{{ route('users.destroy', $user->id) }}" class="d-inline delete-user-form" id="delete-form-{{ $user->id }}">
                @csrf
                @method('DELETE')
                <button type="button" 
                        class="btn-action btn-action-delete" 
                        title="Excluir"
                        data-bs-toggle="modal"
                        data-bs-target="#deleteUserModal"
                        data-user-id="{{ $user->id }}"
                        data-user-name="{{ $user->name }}"
                        data-form-id="delete-form-{{ $user->id }}">
                    <i class="mdi mdi-delete"></i>
                </button>
            </form>
        </div>
    </td>
</tr>
