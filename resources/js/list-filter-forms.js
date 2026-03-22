/**
 * Filtros de listagem: exibe o botão "Limpar" só quando há critério ativo
 * (campos preenchidos ou selects diferentes da primeira opção "todos/todas").
 */
function listFilterFormIsActive(form) {
    for (const el of form.elements) {
        if (!el.name || el.disabled) continue;
        const t = el.type;
        if (t === 'submit' || t === 'button' || t === 'reset') continue;
        if (t === 'hidden') {
            if (el.name === '_token' || el.name === 'page') continue;
            if (String(el.value || '').trim() !== '') return true;
            continue;
        }
        if (t === 'checkbox' || t === 'radio') {
            if (el.checked !== el.defaultChecked) return true;
            continue;
        }
        if (el.tagName === 'SELECT') {
            const neutral = el.getAttribute('data-filter-neutral-value');
            if (neutral !== null) {
                if (String(el.value) !== String(neutral)) return true;
                continue;
            }
            if (el.options.length > 0) {
                const firstVal = el.options[0].value;
                if (String(el.value) !== String(firstVal)) return true;
            }
            continue;
        }
        if (String(el.value || '').trim() !== '') return true;
    }
    return false;
}

function initListFilterForms() {
    document.querySelectorAll('form.js-list-filter-form').forEach((form) => {
        const clearBtn = form.querySelector('.js-list-filter-clear');
        if (!clearBtn) return;

        const sync = () => {
            clearBtn.classList.toggle('d-none', !listFilterFormIsActive(form));
        };

        form.addEventListener('input', sync);
        form.addEventListener('change', sync);
        sync();
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initListFilterForms);
} else {
    initListFilterForms();
}
