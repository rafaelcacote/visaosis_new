{{-- Script para formatação e validação de telefone celular (11 dígitos) --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Função para formatar telefone celular brasileiro (11 dígitos)
        function formatTelefone(value) {
            // Remove todos os caracteres não numéricos
            value = value.replace(/\D/g, '');

            // Limitar a 11 dígitos
            if (value.length > 11) {
                value = value.substring(0, 11);
            }

            // Aplicar máscara para celular (11 dígitos)
            if (value.length === 11) {
                return value.replace(/^(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
            } else if (value.length > 6) {
                return value.replace(/^(\d{2})(\d{5})(\d)/, '($1) $2-$3');
            } else if (value.length > 2) {
                return value.replace(/^(\d{2})(\d)/, '($1) $2');
            } else if (value.length > 0) {
                return value.replace(/^(\d)/, '($1');
            }

            return value;
        }

        // Aplicar formatação em todos os campos de telefone
        const telefoneSelectors = [
            'input[name="telefone"]',
            'input[id*="telefone"]',
            'input[name*="telefone"]',
            'input[class*="telefone"]',
            'input[placeholder="(00) 00000-0000"]',
            'input[data-mask="(00) 00000-0000"]'
        ];

        const telefoneInputs = document.querySelectorAll(telefoneSelectors.join(', '));

        telefoneInputs.forEach(function(input) {
            // Excluir campos de chave PIX da formatação de telefone
            if (input.name === 'chave_pix' || input.id === 'chave_pix') {
                return;
            }

            // Excluir campos de busca da formatação de telefone
            if (input.id === 'searchPatient' || input.name === 'search' || input.classList.contains('search-input')) {
                return;
            }

            // Evitar duplicação se o campo já foi processado
            if (input.hasAttribute('data-telefone-processed')) {
                return;
            }
            input.setAttribute('data-telefone-processed', 'true');

            // Aplicar formatação durante a digitação
            input.addEventListener('input', function(e) {
                const cursorPosition = e.target.selectionStart;
                const oldValue = e.target.value;
                const newValue = formatTelefone(e.target.value);

                e.target.value = newValue;

                // Manter posição do cursor ajustada
                if (newValue.length < oldValue.length) {
                    // Se removeu caracteres, manter posição
                    e.target.setSelectionRange(cursorPosition, cursorPosition);
                } else if (newValue.length > oldValue.length) {
                    // Se adicionou caracteres (máscara), ajustar posição
                    const newPosition = cursorPosition + (newValue.length - oldValue.length);
                    e.target.setSelectionRange(newPosition, newPosition);
                }
            });

            // Formatar valor inicial se existir
            if (input.value) {
                input.value = formatTelefone(input.value);
            }

            // Adicionar validação visual apenas se não há erro do servidor
            input.addEventListener('blur', function(e) {
                const telefone = e.target.value.replace(/\D/g, '');

                // Verificar se já existe mensagem de erro do servidor
                const serverErrorDiv = e.target.parentNode.querySelector('.invalid-feedback:not(.telefone-error)');

                // Só aplicar validação JavaScript se não há erro do servidor
                if (!serverErrorDiv || serverErrorDiv.style.display === 'none') {
                    if (telefone.length > 0 && telefone.length !== 11) {
                        // Adicionar classe de erro se não tiver 11 dígitos
                        e.target.classList.add('is-invalid');

                        // Criar ou atualizar mensagem de erro JavaScript
                        let errorDiv = e.target.parentNode.querySelector('.telefone-error');
                        if (!errorDiv) {
                            errorDiv = document.createElement('div');
                            errorDiv.className = 'telefone-error invalid-feedback';
                            e.target.parentNode.appendChild(errorDiv);
                        }
                        errorDiv.textContent = 'O telefone deve conter exatamente 11 dígitos.';
                        errorDiv.style.display = 'block';
                    } else {
                        // Remover classe de erro JavaScript
                        e.target.classList.remove('is-invalid');
                        const errorDiv = e.target.parentNode.querySelector('.telefone-error');
                        if (errorDiv) {
                            errorDiv.style.display = 'none';
                        }
                    }
                }
            });

            // Limpar erros JavaScript quando começar a digitar
            input.addEventListener('focus', function(e) {
                // Remover apenas erros criados pelo JavaScript, não do servidor
                const jsErrorDiv = e.target.parentNode.querySelector('.telefone-error');
                const serverErrorDiv = e.target.parentNode.querySelector('.invalid-feedback:not(.telefone-error)');

                if (jsErrorDiv) {
                    jsErrorDiv.style.display = 'none';
                }

                // Só remover classe is-invalid se não há erro do servidor
                if (!serverErrorDiv || serverErrorDiv.style.display === 'none') {
                    e.target.classList.remove('is-invalid');
                }
            });

            // Limpar formatação antes do submit (manter apenas números)
            const form = input.closest('form');
            if (form && !form.hasAttribute('data-telefone-submit-processed')) {
                form.setAttribute('data-telefone-submit-processed', 'true');
                form.addEventListener('submit', function() {
                    telefoneInputs.forEach(function(tel) {
                        // Excluir chave PIX da limpeza de formatação
                        if (tel.name === 'chave_pix' || tel.id === 'chave_pix') {
                            return;
                        }

                        if (tel.value) {
                            // Manter apenas números para envio
                            tel.value = tel.value.replace(/\D/g, '');
                        }
                    });
                });
            }
        });

        // Função global para aplicar formatação a novos campos criados dinamicamente
        window.aplicarFormatacaoTelefone = function(input) {
            if (input.hasAttribute('data-telefone-processed')) {
                return;
            }

            input.setAttribute('data-telefone-processed', 'true');

            input.addEventListener('input', function(e) {
                e.target.value = formatTelefone(e.target.value);
            });

            if (input.value) {
                input.value = formatTelefone(input.value);
            }
        };
    });
</script>
