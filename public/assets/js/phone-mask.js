/**
 * Máscara de telefone reutilizável
 * Suporta formatos: (00) 00000-0000 (celular) e (00) 0000-0000 (fixo)
 * 
 * Uso:
 * - Adicione a classe 'phone-mask' ao input
 * - Ou chame applyPhoneMask(element) diretamente
 */

(function() {
    'use strict';

    /**
     * Aplica máscara de telefone no elemento
     * @param {HTMLElement} input - Elemento input
     */
    function applyPhoneMask(input) {
        // Remove tudo que não é número
        let value = input.value.replace(/\D/g, '');
        
        // Limita a 11 dígitos (máximo para telefone brasileiro)
        if (value.length > 11) {
            value = value.substring(0, 11);
        }
        
        // Aplica a máscara baseada no tamanho
        let maskedValue = '';
        
        if (value.length <= 2) {
            maskedValue = value.length > 0 ? '(' + value : value;
        } else if (value.length <= 6) {
            maskedValue = '(' + value.substring(0, 2) + ') ' + value.substring(2);
        } else if (value.length <= 10) {
            // Telefone fixo: (00) 0000-0000
            maskedValue = '(' + value.substring(0, 2) + ') ' + value.substring(2, 6) + '-' + value.substring(6);
        } else {
            // Celular: (00) 00000-0000
            maskedValue = '(' + value.substring(0, 2) + ') ' + value.substring(2, 7) + '-' + value.substring(7, 11);
        }
        
        input.value = maskedValue;
    }

    /**
     * Inicializa máscaras de telefone em todos os elementos com a classe 'phone-mask'
     */
    function initPhoneMasks() {
        const phoneInputs = document.querySelectorAll('.phone-mask');
        
        phoneInputs.forEach(function(input) {
            // Aplica máscara ao carregar (caso já tenha valor)
            if (input.value) {
                applyPhoneMask(input);
            }
            
            // Aplica máscara ao digitar
            input.addEventListener('input', function() {
                applyPhoneMask(this);
            });
            
            // Aplica máscara ao colar
            input.addEventListener('paste', function(e) {
                setTimeout(function() {
                    applyPhoneMask(e.target);
                }, 10);
            });
        });
    }

    // Inicializa quando o DOM estiver pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPhoneMasks);
    } else {
        initPhoneMasks();
    }

    // Disponibiliza a função globalmente para uso manual
    window.applyPhoneMask = applyPhoneMask;
})();
