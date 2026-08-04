(function () {
    'use strict';

    var STORAGE_KEY = 'paieSidebarCollapsed';
    var btn = document.getElementById('sidebarCollapseBtn');

    if (!btn) return;

    function apply(state) {
        document.body.classList.toggle('sidebar-collapsed', state);
        try {
            localStorage.setItem(STORAGE_KEY, state ? '1' : '0');
        } catch (e) { /* stockage indisponible */ }
        btn.title = state ? 'Déplier le menu' : 'Replier le menu';
    }

    var saved = false;
    try {
        saved = localStorage.getItem(STORAGE_KEY) === '1';
    } catch (e) { /* stockage indisponible */ }
    apply(saved);

    btn.addEventListener('click', function () {
        apply(!document.body.classList.contains('sidebar-collapsed'));
    });
})();
