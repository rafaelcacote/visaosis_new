@php
    $existingSignatureDataUri = isset($profissional) ? $profissional->assinatura_data_uri : null;
    $hasExistingSignature = !empty($existingSignatureDataUri);
@endphp

<div class="row mt-2">
    <div class="col-md-6">
        <div class="form-group">
            <label for="assinatura_imagem">Assinatura do Profissional</label>
            <input type="file"
                   class="form-control @error('assinatura_imagem') is-invalid @enderror"
                   id="assinatura_imagem"
                   name="assinatura_imagem"
                   accept="image/png,image/jpeg,image/webp">
            <small class="form-text text-muted d-block">
                Envie uma imagem da assinatura para uso no PDF da receita médica. Formatos aceitos: PNG, JPG, JPEG e WEBP. Tamanho máximo: 5MB.
            </small>
            @error('assinatura_imagem')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="border rounded p-3 bg-light h-100">
            <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
                <div>
                    <div class="fw-semibold mb-1">Pré-visualização da assinatura</div>
                    <small class="text-muted">A imagem será exibida acima da identificação do profissional no PDF da receita.</small>
                </div>
                @if ($hasExistingSignature)
                    <div class="form-check">
                        <input type="checkbox"
                               class="form-check-input"
                               id="remover_assinatura"
                               name="remover_assinatura"
                               value="1"
                               {{ old('remover_assinatura') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remover_assinatura">Remover atual</label>
                    </div>
                @endif
            </div>

            <div class="mt-3 text-center">
                <img id="assinaturaPreview"
                     src="{{ old('remover_assinatura') ? '' : $existingSignatureDataUri }}"
                     alt="Pré-visualização da assinatura"
                     class="img-fluid {{ old('remover_assinatura') || ! $hasExistingSignature ? 'd-none' : '' }}"
                     style="max-height: 120px; object-fit: contain;">
                <div id="assinaturaPreviewPlaceholder"
                     class="text-muted small {{ old('remover_assinatura') || ! $hasExistingSignature ? '' : 'd-none' }}">
                    Nenhuma assinatura cadastrada.
                </div>
            </div>
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const fileInput = document.getElementById('assinatura_imagem');
                const preview = document.getElementById('assinaturaPreview');
                const placeholder = document.getElementById('assinaturaPreviewPlaceholder');
                const removeCheckbox = document.getElementById('remover_assinatura');

                if (!fileInput || !preview || !placeholder) {
                    return;
                }

                const updatePreviewState = (src) => {
                    if (src) {
                        preview.src = src;
                        preview.classList.remove('d-none');
                        placeholder.classList.add('d-none');
                    } else {
                        preview.src = '';
                        preview.classList.add('d-none');
                        placeholder.classList.remove('d-none');
                    }
                };

                fileInput.addEventListener('change', function(event) {
                    const file = event.target.files && event.target.files[0];
                    if (!file) {
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(loadEvent) {
                        updatePreviewState(loadEvent.target?.result || '');
                    };
                    reader.readAsDataURL(file);

                    if (removeCheckbox) {
                        removeCheckbox.checked = false;
                    }
                });

                if (removeCheckbox) {
                    removeCheckbox.addEventListener('change', function() {
                        if (removeCheckbox.checked) {
                            fileInput.value = '';
                            updatePreviewState('');
                        }
                    });
                }
            });
        </script>
    @endpush
@endonce
