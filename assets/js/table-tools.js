(function () {
    'use strict';

    // ── Normalisation (accents + casse) ──
    function normalize(s) {
        return String(s == null ? '' : s)
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/\s+/g, ' ')
            .trim()
            .toLowerCase();
    }

    // ── Valeur de tri : numérique, date, ou texte ──
    function parseSortableValue(text) {
        var t = String(text).trim();
        var numRaw = t.replace(/[^\d.,\-]/g, '');
        if (numRaw !== '' && /^-?\d+([.,]\d+)?$/.test(numRaw)) {
            var n = parseFloat(numRaw.replace(/,/g, '.'));
            if (!isNaN(n)) return { type: 'num', val: n };
        }
        var my = t.match(/^(\d{1,2})\s*\/\s*(\d{4})$/);
        if (my) return { type: 'num', val: parseInt(my[2], 10) * 100 + parseInt(my[1], 10) };
        var d1 = t.match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/);
        var d2 = t.match(/^(\d{4})-(\d{1,2})-(\d{1,2})$/);
        if (d1) return { type: 'num', val: +d1[3] * 10000 + +d1[2] * 100 + +d1[1] };
        if (d2) return { type: 'num', val: +d2[1] * 10000 + +d2[2] * 100 + +d2[3] };
        return { type: 'str', val: normalize(t) };
    }

    function getCellValue(row, index) {
        var td = row.cells && row.cells[index];
        if (!td) return { type: 'str', val: '' };
        if (td.dataset && td.dataset.sort !== undefined) return parseSortableValue(td.dataset.sort);
        return parseSortableValue(td.innerText);
    }

    function isSortableTh(th) {
        if (th.classList.contains('no-sort')) return false;
        var txt = (th.innerText || '').trim();
        if (txt === '') return false;
        if (/^actions?$/i.test(txt)) return false;
        return true;
    }

    // ── Enhancement d'une table ──
    function enhanceTable(table) {
        if (table.__tableTools) return;

        // Opt-out explicite ou zones éditeur
        if (table.getAttribute('data-table-tools') === 'off') return;
        if (table.closest('[data-table-tools="off"], #editorPreview, .table-editor, .no-sort-table')) return;

        var thead = table.querySelector('thead');
        var tbody = table.querySelector('tbody');
        if (!thead || !tbody) return;

        var ths = Array.prototype.slice.call(thead.querySelectorAll('th'));
        if (!ths.length) return;

        var rows = Array.prototype.slice.call(tbody.querySelectorAll('tr'))
            .filter(function (r) { return r.querySelectorAll('td').length > 0; });
        if (!rows.length) return;

        // Tableau éditable (inputs visibles/select/textarea dans les lignes) :
        // le tri réordonnerait les champs et casserait l'appariement des
        // tableaux name="xxx[]" au POST — le filtre reste autorisé (sans danger).
        var editable = rows.some(function (r) {
            return r.querySelector('input:not([type="hidden"]), select, textarea');
        });

        table.__tableTools = { rows: rows, col: -1, dir: 1 };

        // ── Barre de filtre ──
        var toolbar = document.createElement('div');
        toolbar.className = 'table-toolbar';
        toolbar.innerHTML =
            '<input type="text" class="table-filter-input" placeholder="Filtrer le tableau…" aria-label="Filtrer le tableau">' +
            '<button type="button" class="table-filter-clear" title="Effacer le filtre" aria-label="Effacer le filtre">&times;</button>';
        table.parentNode.insertBefore(toolbar, table);

        var input = toolbar.querySelector('.table-filter-input');
        var clearBtn = toolbar.querySelector('.table-filter-clear');

        // ── Indicateurs de tri ──
        ths.forEach(function (th, i) {
            if (editable || !isSortableTh(th)) return;
            th.classList.add('sortable');
            th.title = 'Trier';
            var ind = document.createElement('span');
            ind.className = 'sort-ind';
            ind.textContent = '↕';
            th.appendChild(ind);
            th.addEventListener('click', function () { sortBy(i); });
        });

        function clearIndicators() {
            ths.forEach(function (th) {
                var ind = th.querySelector('.sort-ind');
                if (ind) {
                    ind.textContent = '↕';
                    ind.removeAttribute('data-state');
                }
            });
        }

        function renderRows() {
            var nr = tbody.querySelector('tr.no-result');
            if (nr) nr.remove();
            while (tbody.firstChild) tbody.removeChild(tbody.firstChild);
            table.__tableTools.rows.forEach(function (r) { tbody.appendChild(r); });
        }

        function sortBy(col) {
            var state = table.__tableTools;
            if (state.col === col) {
                state.dir *= -1;
            } else {
                state.col = col;
                state.dir = 1;
            }
            var dir = state.dir;
            var sorted = state.rows.slice().sort(function (a, b) {
                var va = getCellValue(a, col);
                var vb = getCellValue(b, col);
                var cmp;
                if (va.type === 'num' && vb.type === 'num') {
                    cmp = va.val - vb.val;
                } else {
                    cmp = String(va.val).localeCompare(String(vb.val), 'fr', { numeric: true });
                }
                return cmp * dir;
            });
            state.rows = sorted;
            renderRows();
            clearIndicators();
            var ind = ths[col].querySelector('.sort-ind');
            if (ind) {
                ind.textContent = dir === 1 ? '▲' : '▼';
                ind.setAttribute('data-state', dir === 1 ? 'asc' : 'desc');
            }
            applyFilter();
        }

        function applyFilter() {
            var q = normalize(input.value);
            var anyVisible = false;
            table.__tableTools.rows.forEach(function (r) {
                var show = q === '' || normalize(r.innerText).indexOf(q) !== -1;
                r.style.display = show ? '' : 'none';
                if (show) anyVisible = true;
            });
            var nr = tbody.querySelector('tr.no-result');
            if (!anyVisible) {
                if (!nr) {
                    nr = document.createElement('tr');
                    nr.className = 'no-result';
                    var td = document.createElement('td');
                    td.colSpan = ths.length;
                    td.textContent = 'Aucun résultat pour « ' + input.value + ' ».';
                    td.style.cssText = 'text-align:center; padding:1.5rem; color:var(--text-muted);';
                    nr.appendChild(td);
                }
                tbody.appendChild(nr);
            } else if (nr) {
                nr.remove();
            }
        }

        input.addEventListener('input', applyFilter);
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') e.preventDefault();
        });
        clearBtn.addEventListener('click', function () {
            input.value = '';
            applyFilter();
            input.focus();
        });
    }

    function init() {
        document.querySelectorAll('table').forEach(function (t) {
            try { enhanceTable(t); } catch (e) { /* jamais bloquer la page */ }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
