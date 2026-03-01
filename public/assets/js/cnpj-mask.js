/**
 * Máscara de CNPJ reutilizável
 * Formato: 00.000.000/0001-00
 * 
 * Uso:
 * - Adicione a classe 'cnpj-mask' ao input
 * - Ou chame applyCnpjMask(element) diretamente
 */

(function() {
    'use strict';

    /**
     * Aplica máscara de CNPJ no elemento
     * @param {HTMLElement} input - Elemento input
     */
    function applyCnpjMask(input) {
        // Remove tudo que não é número
        let value = input.value.replace(/\D/g, '');
        
        // Limita a 14 dígitos (CNPJ tem exatamente 14 números)
        if (value.length > 14) {
            value = value.substring(0, 14);
        }
        
        // Aplica a máscara: 00.000.000/0001-00
        let maskedValue = '';
        
        if (value.length <= 2) {
            maskedValue = value;
        } else if (value.length <= 5) {
            maskedValue = value.replace(/^(\d{2})(\d)/, '$1.$2');
        } else if (value.length <= 8) {
            maskedValue = value.replace(/^(\d{2})(\d{3})(\d)/, '$1.$2.$3');
        } else if (value.length <= 12) {
            maskedValue = value.replace(/^(\d{2})(\d{3})(\d{3})(\d)/, '$1.$2.$3/$4');
        } else {
            maskedValue = value.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d)/, '$1.$2.$3/$4-$5');
        }
        
        input.value = maskedValue;
    }

    /**
     * Remove a formatação do CNPJ, deixando apenas números
     * @param {string} value - Valor formatado
     * @returns {string} - Valor apenas com números
     */
    function cleanCnpj(value) {
        return value.replace(/\D/g, '');
    }

    /**
     * Valida se o CNPJ tem exatamente 14 dígitos numéricos
     * @param {string} value - Valor do input
     * @returns {boolean} - true se válido, false caso contrário
     */
    function validateCnpjLength(value) {
        const clean = cleanCnpj(value);
        return clean.length === 14;
    }

    /**
     * Inicializa máscaras de CNPJ em todos os elementos com a classe 'cnpj-mask'
     */
    function initCnpjMasks() {
        const cnpjInputs = document.querySelectorAll('.cnpj-mask');
        
        cnpjInputs.forEach(function(input) {
            // Aplica máscara ao carregar (caso já tenha valor)
            if (input.value) {
                applyCnpjMask(input);
            }
            
            // Aplica máscara ao digitar
            input.addEventListener('input', function() {
                applyCnpjMask(this);
            });
            
            // Aplica máscara ao colar
            input.addEventListener('paste', function(e) {
                setTimeout(function() {
                    applyCnpjMask(e.target);
                }, 10);
            });

            // Validação visual ao perder o foco
            input.addEventListener('blur', function() {
                const clean = cleanCnpj(this.value);
                if (this.hasAttribute('required') && clean.length > 0 && clean.length !== 14) {
                    this.classList.add('is-invalid');
                    if (!this.nextElementSibling || !this.nextElementSibling.classList.contains('invalid-feedback')) {
                        const feedback = document.createElement('div');
                        feedback.className = 'invalid-feedback';
                        feedback.textContent = 'O CNPJ deve ter 14 dígitos.';
                        this.parentNode.appendChild(feedback);
                    }
                } else {
                    this.classList.remove('is-invalid');
                    const feedback = this.parentNode.querySelector('.invalid-feedback');
                    if (feedback && feedback.textContent === 'O CNPJ deve ter 14 dígitos.') {
                        feedback.remove();
                    }
                }
            });

            // Remove validação visual ao digitar
            input.addEventListener('input', function() {
                if (this.classList.contains('is-invalid')) {
                    const clean = cleanCnpj(this.value);
                    if (clean.length === 14 || clean.length === 0) {
                        this.classList.remove('is-invalid');
                        const feedback = this.parentNode.querySelector('.invalid-feedback');
                        if (feedback && feedback.textContent === 'O CNPJ deve ter 14 dígitos.') {
                            feedback.remove();
                        }
                    }
                }
            });
        });
    }

    /**
     * Limpa a formatação de todos os campos CNPJ antes do submit
     */
    function cleanCnpjBeforeSubmit() {
        const forms = document.querySelectorAll('form');
        forms.forEach(function(form) {
            form.addEventListener('submit', function(e) {
                const cnpjInputs = form.querySelectorAll('.cnpj-mask');
                cnpjInputs.forEach(function(input) {
                    if (input.value) {
                        const clean = cleanCnpj(input.value);
                        if (input.hasAttribute('required') && clean.length !== 14) {
                            e.preventDefault();
                            input.classList.add('is-invalid');
                            input.focus();
                            
                            if (!input.nextElementSibling || !input.nextElementSibling.classList.contains('invalid-feedback')) {
                                const feedback = document.createElement('div');
                                feedback.className = 'invalid-feedback';
                                feedback.textContent = 'O CNPJ deve ter 14 dígitos.';
                                input.parentNode.appendChild(feedback);
                            }
                            return;
                        }
                        // Remove formatação antes do envio (deixa apenas números)
                        input.value = clean;
                    }
                });
            });
        });
    }

    // Inicializa quando o DOM estiver pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initCnpjMasks();
            cleanCnpjBeforeSubmit();
        });
    } else {
        initCnpjMasks();
        cleanCnpjBeforeSubmit();
    }

    // Disponibiliza as funções globalmente para uso manual
    window.applyCnpjMask = applyCnpjMask;
    window.cleanCnpj = cleanCnpj;
    window.validateCnpjLength = validateCnpjLength;
})();
