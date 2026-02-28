{{-- Script para formatação e validação de CPF --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Função para formatar CPF
        function formatCPF(value) {
            // Remove todos os caracteres não numéricos
            value = value.replace(/\D/g, '');

            // Aplica a formatação
            if (value.length <= 3) {
                return value;
            } else if (value.length <= 6) {
                return value.replace(/(\d{3})(\d{1,3})/, '$1.$2');
            } else if (value.length <= 9) {
                return value.replace(/(\d{3})(\d{3})(\d{1,3})/, '$1.$2.$3');
            } else {
                return value.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/, '$1.$2.$3-$4');
            }
        }

        // Função para validar CPF (algoritmo oficial)
        function validarCPF(cpf) {
            cpf = cpf.replace(/[^\d]+/g, '');

            if (cpf.length !== 11 || /^(\d)\1{10}$/.test(cpf)) {
                return false;
            }

            let soma = 0;
            let resto;

            for (let i = 1; i <= 9; i++) {
                soma += parseInt(cpf.substring(i - 1, i)) * (11 - i);
            }

            resto = (soma * 10) % 11;

            if (resto === 10 || resto === 11) {
                resto = 0;
            }

            if (resto !== parseInt(cpf.substring(9, 10))) {
                return false;
            }

            soma = 0;

            for (let i = 1; i <= 10; i++) {
                soma += parseInt(cpf.substring(i - 1, i)) * (12 - i);
            }

            resto = (soma * 10) % 11;

            if (resto === 10 || resto === 11) {
                resto = 0;
            }

            return resto === parseInt(cpf.substring(10, 11));
        }

        // Aplicar formatação e validação em todos os campos de CPF
        const cpfSelectors = [
            'input[name="cpf"]',
            'input[id*="cpf"]',
            'input[name*="cpf"]',
            'input[class*="cpf"]'
        ];

        const cpfInputs = document.querySelectorAll(cpfSelectors.join(', '));

        cpfInputs.forEach(function(input) {
            // Excluir campos de chave PIX da formatação de CPF
            if (input.name === 'chave_pix' || input.id === 'chave_pix') {
                return;
            }

            // Excluir campos de busca da formatação de CPF
            if (input.id === 'searchPatient' || input.name === 'search' || input.classList.contains('search-input')) {
                return;
            }

            // Evitar duplicação se o campo já foi processado
            if (input.hasAttribute('data-cpf-processed')) {
                return;
            }
            input.setAttribute('data-cpf-processed', 'true');

            // Criar elemento para mostrar status da validação
            const validationDiv = document.createElement('div');
            validationDiv.className = 'cpf-validation mt-1';
            validationDiv.style.display = 'none';
            input.parentNode.appendChild(validationDiv);

            // Armazenar valor inicial do CPF para detectar mudanças
            let initialCpfValue = input.value.replace(/\D/g, '');

            input.addEventListener('input', function(e) {
                const formatted = formatCPF(e.target.value);
                e.target.value = formatted;

                // Limpar feedback durante a digitação
                const cleanCpf = e.target.value.replace(/\D/g, '');

                // Remover todas as classes de validação e ocultar feedback durante a digitação
                e.target.classList.remove('is-valid', 'is-invalid');
                validationDiv.style.display = 'none';

                // Só limpar mensagens de erro do servidor se o CPF foi realmente alterado
                if (cleanCpf !== initialCpfValue) {
                    // Limpar mensagens de erro do servidor (como "CPF já cadastrado")
                    const serverErrorDiv = e.target.parentNode.querySelector('.invalid-feedback');
                    if (serverErrorDiv) {
                        serverErrorDiv.style.display = 'none';
                    }

                    // Ocultar alertas de validação do servidor
                    const validationAlerts = document.querySelectorAll('.alert-danger');
                    validationAlerts.forEach(function(alert) {
                        if (alert.textContent.includes('CPF') || alert.textContent.includes('cpf')) {
                            alert.style.display = 'none';
                        }
                    });
                }

                // Só validar e mostrar feedback quando CPF estiver completo (11 dígitos)
                if (cleanCpf.length === 11) {
                    if (validarCPF(cleanCpf)) {
                        // CPF válido
                        e.target.classList.add('is-valid');
                        validationDiv.className = 'cpf-validation mt-1 text-success small';
                        validationDiv.innerHTML = '<i class="mdi mdi-check-circle me-1"></i>CPF válido';
                        validationDiv.style.display = 'block';
                    } else {
                        // CPF inválido
                        e.target.classList.add('is-invalid');
                        validationDiv.className = 'cpf-validation mt-1 text-danger small';
                        validationDiv.innerHTML = '<i class="mdi mdi-close-circle me-1"></i>CPF inválido';
                        validationDiv.style.display = 'block';
                    }
                }
            });

            // Formatar e validar valor inicial se existir
            if (input.value) {
                input.value = formatCPF(input.value);
                input.dispatchEvent(new Event('input'));
            }
        });

        // Validação antes do submit e limpeza da formatação
        const forms = document.querySelectorAll('form');
        forms.forEach(function(form) {
            form.addEventListener('submit', function(e) {
                let hasInvalidCpf = false;

                cpfInputs.forEach(function(input) {
                    // Excluir chave PIX da validação e limpeza de CPF
                    if (input.name === 'chave_pix' || input.id === 'chave_pix') {
                        return;
                    }

                    if (input.value) {
                        const cleanCpf = input.value.replace(/\D/g, '');

                        // Verificar se o CPF é obrigatório e se está válido
                        if (input.hasAttribute('required') || input.closest('.form-group, .mb-3')?.querySelector('label')?.textContent?.includes('*')) {
                            if (!validarCPF(cleanCpf)) {
                                hasInvalidCpf = true;
                                input.classList.add('is-invalid');
                                input.focus();
                                return;
                            }
                        }

                        // Remove formatação antes do envio
                        input.value = cleanCpf;
                    }
                });

                if (hasInvalidCpf) {
                    e.preventDefault();

                    // Adicionar campo hidden com a mensagem de erro para o servidor processar
                    const form = e.target;
                    let hiddenInput = form.querySelector('input[name="cpf_validation_error"]');

                    if (!hiddenInput) {
                        hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'cpf_validation_error';
                        form.appendChild(hiddenInput);
                    }

                    hiddenInput.value = 'Por favor, verifique os CPFs informados. Há campos com CPFs inválidos.';

                    // Submeter o formulário para que o servidor exiba o alerta colorido
                    form.submit();
                    return false;
                }
            });
        });

        // Função global para aplicar validação a novos campos criados dinamicamente
        window.aplicarValidacaoCPF = function(input) {
            if (input.hasAttribute('data-cpf-processed')) {
                return;
            }
            input.setAttribute('data-cpf-processed', 'true');

            const validationDiv = document.createElement('div');
            validationDiv.className = 'cpf-validation mt-1';
            validationDiv.style.display = 'none';
            input.parentNode.appendChild(validationDiv);

            // Armazenar valor inicial do CPF para detectar mudanças
            let initialCpfValue = input.value.replace(/\D/g, '');

            input.addEventListener('input', function(e) {
                const formatted = formatCPF(e.target.value);
                e.target.value = formatted;

                // Limpar feedback durante a digitação
                const cleanCpf = e.target.value.replace(/\D/g, '');

                // Remover todas as classes de validação e ocultar feedback durante a digitação
                e.target.classList.remove('is-valid', 'is-invalid');
                validationDiv.style.display = 'none';

                // Só limpar mensagens de erro do servidor se o CPF foi realmente alterado
                if (cleanCpf !== initialCpfValue) {
                    // Limpar mensagens de erro do servidor (como "CPF já cadastrado")
                    const serverErrorDiv = e.target.parentNode.querySelector('.invalid-feedback');
                    if (serverErrorDiv) {
                        serverErrorDiv.style.display = 'none';
                    }

                    // Ocultar alertas de validação do servidor
                    const validationAlerts = document.querySelectorAll('.alert-danger');
                    validationAlerts.forEach(function(alert) {
                        if (alert.textContent.includes('CPF') || alert.textContent.includes('cpf')) {
                            alert.style.display = 'none';
                        }
                    });
                }

                // Só validar e mostrar feedback quando CPF estiver completo (11 dígitos)
                if (cleanCpf.length === 11) {
                    if (validarCPF(cleanCpf)) {
                        // CPF válido
                        e.target.classList.add('is-valid');
                        validationDiv.className = 'cpf-validation mt-1 text-success small';
                        validationDiv.innerHTML = '<i class="mdi mdi-check-circle me-1"></i>CPF válido';
                        validationDiv.style.display = 'block';
                    } else {
                        // CPF inválido
                        e.target.classList.add('is-invalid');
                        validationDiv.className = 'cpf-validation mt-1 text-danger small';
                        validationDiv.innerHTML = '<i class="mdi mdi-close-circle me-1"></i>CPF inválido';
                        validationDiv.style.display = 'block';
                    }
                }
            });

            if (input.value) {
                input.value = formatCPF(input.value);
                input.dispatchEvent(new Event('input'));
            }
        };
    });
</script>
