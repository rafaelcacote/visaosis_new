<!-- Modal de Seleção de Location -->
<div class="modal fade" id="locationSelectorModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="locationSelectorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
                <h5 class="modal-title" id="locationSelectorModalLabel">
                    <i class="mdi mdi-map-marker-multiple me-2"></i>
                    Bem-vindo(a), {{ Auth::user()->name ?? 'Usuário' }}!
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="display: none;"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <div class="mb-3">
                        <i class="mdi mdi-store-multiple" style="font-size: 64px; color: #667eea;"></i>
                    </div>
                    <h4 class="mb-2">Você tem acesso a múltiplas lojas/localidades</h4>
                    <p class="text-muted">Por favor, selecione a localidade que deseja acessar nesta sessão:</p>
                </div>

                <div class="alert alert-info d-flex align-items-center" role="alert">
                    <i class="mdi mdi-information-outline me-2"></i>
                    <div>Você poderá trocar de localidade a qualquer momento através do menu no cabeçalho.</div>
                </div>

                <form id="locationSelectionForm">
                    @csrf
                    <div class="mb-4">
                        <label for="locationSelect" class="form-label fw-bold">
                            <i class="mdi mdi-map-marker me-2"></i>
                            Selecione a Localidade
                        </label>
                        <select class="form-select form-select-lg" id="locationSelect" name="location_id" required>
                            <option value="">-- Escolha uma localidade --</option>
                            @if(session('user_locations'))
                                @foreach(session('user_locations') as $userLocation)
                                    <option value="{{ $userLocation['location_id'] }}" 
                                        data-location-name="{{ $userLocation['location']['name'] ?? '' }}"
                                        data-location-short="{{ $userLocation['location']['short_name'] ?? '' }}">
                                        {{ $userLocation['location']['name'] ?? 'Location' }}
                                        @if(!empty($userLocation['location']['short_name']))
                                            ({{ $userLocation['location']['short_name'] }})
                                        @endif
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <div class="invalid-feedback">
                            Por favor, selecione uma localidade.
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg" id="confirmLocationBtn">
                            <i class="mdi mdi-check-circle me-2"></i>
                            Confirmar e Continuar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Verificar se precisa mostrar o modal - usando uma flag global
window.needsLocationSelection = {{ session('needs_location_selection') ? 'true' : 'false' }};
window.userLocations = @json(session('user_locations', []));

document.addEventListener('DOMContentLoaded', function() {
    // Verificar se precisa mostrar o modal de seleção
    const shouldShowModal = window.needsLocationSelection === true || window.needsLocationSelection === 'true';
    
    if (shouldShowModal) {
        // Aguardar Bootstrap estar totalmente carregado
        setTimeout(function() {
            const modalElement = document.getElementById('locationSelectorModal');
            
            if (modalElement) {
                try {
                    // Verificar se bootstrap está disponível
                    if (typeof bootstrap === 'undefined') {
                        setTimeout(arguments.callee, 100);
                        return;
                    }
                    
                    const locationModal = new bootstrap.Modal(modalElement, {
                        backdrop: 'static',
                        keyboard: false
                    });
                    
                    locationModal.show();
                } catch (error) {
                    console.error('Erro ao abrir modal:', error);
                }
            }
        }, 500);
    }

    // Handler do formulário
    const form = document.getElementById('locationSelectionForm');
    const selectElement = document.getElementById('locationSelect');
    const submitBtn = document.getElementById('confirmLocationBtn');

    // Só adicionar handler se o formulário existir
    if (form && selectElement && submitBtn) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            // Validação
            if (!selectElement.value) {
                selectElement.classList.add('is-invalid');
                return;
            }

            selectElement.classList.remove('is-invalid');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processando...';

            try {
                // Buscar CSRF token de forma segura
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (!csrfToken) {
                    throw new Error('CSRF token não encontrado');
                }

                const response = await fetch('{{ route("location.select") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        location_id: parseInt(selectElement.value)
                    })
                });

                // Verificar se a resposta é JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    // Se não for JSON, pode ser um redirect HTML
                    const text = await response.text();
                    console.error('Resposta não é JSON:', text.substring(0, 200));
                    throw new Error('Resposta do servidor não é JSON válido');
                }

                const data = await response.json();

                if (data.success) {
                    // Sucesso - recarregar a página
                    const selectedOption = selectElement.options[selectElement.selectedIndex];
                    const locationName = selectedOption.getAttribute('data-location-name');
                    
                    // Mostrar feedback de sucesso
                    submitBtn.innerHTML = '<i class="mdi mdi-check-circle me-2"></i>Localidade selecionada!';
                    submitBtn.classList.remove('btn-primary');
                    submitBtn.classList.add('btn-success');

                    // Aguardar um pouco antes de recarregar
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                } else {
                    throw new Error(data.message || 'Erro ao selecionar localidade');
                }
            } catch (error) {
                console.error('Erro completo:', error);
                let errorMessage = 'Erro ao selecionar localidade';
                
                if (error.message) {
                    errorMessage = error.message;
                } else if (error instanceof TypeError && error.message.includes('JSON')) {
                    errorMessage = 'Erro ao processar resposta do servidor. Tente novamente.';
                }
                
                alert(errorMessage);
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="mdi mdi-check-circle me-2"></i>Confirmar e Continuar';
            }
        });
    }
});
</script>
@endpush

@push('styles')
<style>
    #locationSelectorModal .modal-content {
        border-radius: 10px;
        overflow: hidden;
        border: none;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    }

    #locationSelectorModal .modal-header {
        border-bottom: none;
        padding: 1.5rem;
    }

    #locationSelectorModal .modal-body {
        padding: 2rem;
    }

    #locationSelectorModal .form-select {
        border: 2px solid #e2e8f0;
        transition: all 0.3s ease;
        padding: 0.75rem 1rem;
    }

    #locationSelectorModal .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.15);
    }

    #locationSelectorModal .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        transition: all 0.3s ease;
        padding: 0.75rem 1.5rem;
    }

    #locationSelectorModal .btn-primary:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
    }

    #locationSelectorModal .btn-primary:disabled {
        opacity: 0.7;
    }

    #locationSelectorModal .alert-info {
        background-color: rgba(102, 126, 234, 0.1);
        border-color: rgba(102, 126, 234, 0.2);
        color: #667eea;
    }
</style>
@endpush