/**
 * Custom Select — dropdown dark mode vanilla JS
 * Remplace les <select> natifs par un composant 100% contrôlé.
 * Usage: document.addEventListener('DOMContentLoaded', () => initCustomSelects());
 */
function initCustomSelects() {
    document.querySelectorAll('select.form-control:not(.no-custom), select.form-select:not(.no-custom):not(.form-select-inline)').forEach(initCustomSelect);
}

function initCustomSelect(nativeSelect) {
    if (nativeSelect.dataset.customInit) return;
    nativeSelect.dataset.customInit = '1';

    const wrapper = document.createElement('div');
    wrapper.className = 'cs-wrapper';
    wrapper.tabIndex = 0;

    const display = document.createElement('div');
    display.className = 'cs-display';

    const chevron = document.createElement('span');
    chevron.className = 'cs-chevron';
    chevron.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>';

    const list = document.createElement('div');
    list.className = 'cs-list';

    const searchWrap = document.createElement('div');
    searchWrap.className = 'cs-search-wrap';
    const searchInput = document.createElement('input');
    searchInput.type = 'text';
    searchInput.className = 'cs-search';
    searchInput.placeholder = 'Rechercher...';
    searchWrap.appendChild(searchInput);
    list.appendChild(searchWrap);

    const optionsWrap = document.createElement('div');
    optionsWrap.className = 'cs-options';
    list.appendChild(optionsWrap);

    function buildOptions(filter) {
        optionsWrap.innerHTML = '';
        const q = (filter || '').toLowerCase();
        const groups = [];
        let currentGroup = { label: '', options: [] };

        Array.from(nativeSelect.options).forEach(opt => {
            if (opt.tagName === 'OPTGROUP') {
                if (currentGroup.options.length) groups.push(currentGroup);
                currentGroup = { label: opt.label, options: [] };
            } else {
                const text = opt.textContent.trim();
                const match = !q || text.toLowerCase().includes(q);
                if (match) {
                    currentGroup.options.push({ value: opt.value, text, disabled: opt.disabled, selected: opt.selected });
                }
            }
        });
        if (currentGroup.options.length) groups.push(currentGroup);
        if (q && groups.length === 0) {
            const empty = document.createElement('div');
            empty.className = 'cs-empty';
            empty.textContent = 'Aucun résultat';
            optionsWrap.appendChild(empty);
            return;
        }

        groups.forEach(grp => {
            if (grp.label) {
                const lbl = document.createElement('div');
                lbl.className = 'cs-group-label';
                lbl.textContent = grp.label;
                optionsWrap.appendChild(lbl);
            }
            grp.options.forEach(opt => {
                const item = document.createElement('div');
                item.className = 'cs-option' + (opt.selected ? ' selected' : '') + (opt.disabled ? ' disabled' : '');
                item.textContent = opt.text;
                item.dataset.value = opt.value;
                if (!opt.disabled) {
                    item.addEventListener('mousedown', e => {
                        e.preventDefault();
                        selectOption(opt.value, opt.text);
                    });
                }
                optionsWrap.appendChild(item);
            });
        });
    }

    function selectOption(value, text) {
        nativeSelect.value = value;
        display.textContent = text || '— Sélectionner —';
        if (!text) display.classList.add('cs-placeholder');
        else display.classList.remove('cs-placeholder');
        nativeSelect.dispatchEvent(new Event('change', { bubbles: true }));
        close();
    }

    function updateDisplay() {
        const selected = nativeSelect.options[nativeSelect.selectedIndex];
        if (selected && selected.value) {
            display.textContent = selected.textContent.trim();
            display.classList.remove('cs-placeholder');
        } else {
            display.textContent = nativeSelect.options[0]?.textContent?.trim() || '— Sélectionner —';
            display.classList.add('cs-placeholder');
        }
    }

    function open() {
        list.classList.add('open');
        wrapper.classList.add('cs-open');
        buildOptions('');
        searchInput.value = '';
        setTimeout(() => searchInput.focus(), 10);
    }

    function close() {
        list.classList.remove('open');
        wrapper.classList.remove('cs-open');
        searchInput.value = '';
    }

    searchInput.addEventListener('input', () => buildOptions(searchInput.value));

    searchInput.addEventListener('keydown', e => {
        if (e.key === 'Escape') { close(); wrapper.focus(); }
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            const first = optionsWrap.querySelector('.cs-option:not(.disabled)');
            if (first) first.focus();
        }
    });

    optionsWrap.addEventListener('keydown', e => {
        const items = [...optionsWrap.querySelectorAll('.cs-option:not(.disabled)')];
        const idx = items.indexOf(document.activeElement);
        if (e.key === 'ArrowDown' && idx < items.length - 1) { e.preventDefault(); items[idx + 1].focus(); }
        if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (idx <= 0) { searchInput.focus(); } else { items[idx - 1].focus(); }
        }
        if (e.key === 'Enter' && document.activeElement.classList.contains('cs-option')) {
            e.preventDefault();
            document.activeElement.dispatchEvent(new Event('mousedown'));
        }
        if (e.key === 'Escape') { close(); wrapper.focus(); }
    });

    display.addEventListener('click', e => {
        e.stopPropagation();
        list.classList.contains('open') ? close() : open();
    });

    wrapper.addEventListener('focus', () => {});

    wrapper.addEventListener('keydown', e => {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); list.classList.contains('open') ? close() : open(); }
        if (e.key === 'Escape') close();
    });

    document.addEventListener('mousedown', e => {
        if (!wrapper.contains(e.target)) close();
    });

    nativeSelect.addEventListener('change', updateDisplay);

    // Insert wrapper
    nativeSelect.style.display = 'none';
    nativeSelect.parentNode.insertBefore(wrapper, nativeSelect);
    wrapper.appendChild(display);
    wrapper.appendChild(chevron);
    wrapper.appendChild(list);
    wrapper.appendChild(nativeSelect);

    updateDisplay();
}
