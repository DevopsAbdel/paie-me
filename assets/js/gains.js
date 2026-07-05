const modalEl = document.getElementById('gainModal');
const modal = new bootstrap.Modal(modalEl);
const form = document.getElementById('gainForm');

function showToast(message, type) {
    const c = document.getElementById('toastContainer');
    const t = document.createElement('div');
    t.className = 'custom-toast toast-' + type;
    t.innerHTML = `<span>${message}</span><button class="toast-close" onclick="this.parentElement.remove()">&times;</button>`;
    c.appendChild(t);
    setTimeout(() => { if (t.parentElement) t.remove(); }, 4000);
}

function toggleCompteInput() {
    const sel = document.getElementById('f_compte');
    const cust = document.getElementById('f_compte_custom');
    cust.style.display = sel.value === 'autre' ? 'block' : 'none';
}

function openModal(gainId, readOnly) {
    document.getElementById('gainModalLabel').textContent = readOnly ? 'Consulter la rubrique' : (gainId ? 'Modifier la rubrique' : 'Nouvelle rubrique de gain');
    document.getElementById('gain_id').value = gainId || '';

    const inputs = form.querySelectorAll('input, select, textarea, button[type="submit"]');
    if (readOnly) {
        inputs.forEach(function(el) { el.disabled = true; });
        document.getElementById('gainSubmitBtn').style.display = 'none';
        document.getElementById('gainCloseBtn').style.display = 'none';
        document.getElementById('gainCloseReadonlyBtn').style.display = 'inline-block';
    } else {
        inputs.forEach(function(el) { el.disabled = false; });
        document.getElementById('gainSubmitBtn').style.display = 'inline-block';
        document.getElementById('gainCloseBtn').style.display = 'inline-block';
        document.getElementById('gainCloseReadonlyBtn').style.display = 'none';
    }

    if (gainId) {
        const g = gainsData.find(r => String(r.id) === String(gainId));
        if (g) fillForm(g);
    } else {
        form.reset();
        document.getElementById('f_compte_custom').style.display = 'none';
        document.getElementById('f_imposable_ir').checked = true;
        document.getElementById('f_imposable_cnss').checked = true;
        document.getElementById('f_actif').checked = true;
        document.getElementById('f_imposable_ir').value = '1';
        document.getElementById('f_imposable_cnss').value = '1';
        document.getElementById('f_actif').value = '1';
    }

    modal.show();
}

function viewGain(gainId) {
    openModal(gainId, true);
}

function fillForm(g) {
    setVal('f_code', g.code || '');
    setVal('f_libelle', g.libelle || '');
    setVal('f_type_montant', g.type_montant || 'fixe');
    setVal('f_valeur_defaut', g.valeur_defaut || '');
    setVal('f_source', g.source || '');
    setVal('f_source_maj', g.source_maj || '');
    setVal('f_justificatifs', g.justificatifs || '');
    setVal('f_nature_edi', g.nature_edi || '');
    setVal('f_categorie', g.categorie || '');
    setCheck('f_base_anciennete', g.base_anciennete);
    setCheck('f_au_prorata', g.au_prorata);
    setCheck('f_imposable_ir', g.imposable_ir);
    setCheck('f_imposable_cnss', g.imposable_cnss);
    setCheck('f_plafond_dgi_actif', g.plafond_dgi_actif);
    setVal('f_plafond_dgi_valeur', g.plafond_dgi_valeur || '');
    setVal('f_plafond_dgi_type', g.plafond_dgi_type || 'mensuel');
    setCheck('f_plafond_cnss_actif', g.plafond_cnss_actif);
    setVal('f_plafond_cnss_valeur', g.plafond_cnss_valeur || '');
    setVal('f_plafond_cnss_type', g.plafond_cnss_type || 'mensuel');
    setVal('f_plafond_dgi_desc', g.plafond_dgi_desc || '');
    setVal('f_plafond_cnss_desc', g.plafond_cnss_desc || '');
    setCompte(g.compte || '');
    setCheck('f_actif', g.actif);
    setCheck('f_is_global', g.is_global);
}

function setVal(id, val) {
    const el = document.getElementById(id);
    if (el) el.value = val;
}

function setCheck(id, val) {
    const el = document.getElementById(id);
    if (el) {
        el.checked = val == 1 || val === '1' || val === true;
        el.value = el.checked ? '1' : '0';
    }
}

function setCompte(val) {
    const sel = document.getElementById('f_compte');
    const cust = document.getElementById('f_compte_custom');
    const opt = Array.from(sel.options).find(o => o.value === val);
    if (opt) {
        sel.value = val;
        cust.style.display = 'none';
    } else if (val) {
        sel.value = 'autre';
        cust.value = val;
        cust.style.display = 'block';
    } else {
        sel.value = '';
        cust.style.display = 'none';
    }
}

form.addEventListener('submit', function(e) {
    e.preventDefault();

    const fd = new FormData(form);
    fd.set('format', 'json');

    const checks = ['base_anciennete', 'au_prorata', 'imposable_ir', 'imposable_cnss', 'plafond_dgi_actif', 'plafond_cnss_actif', 'actif', 'is_global'];
    checks.forEach(function(name) {
        const el = document.getElementById('f_' + name);
        if (el) fd.set(name, el.checked ? '1' : '0');
    });

    // Compte comptable: select + custom input
    const sel = document.getElementById('f_compte');
    if (sel.value === 'autre') {
        const cust = document.getElementById('f_compte_custom');
        fd.set('compte', cust.value);
    } else {
        fd.set('compte', sel.value);
    }

    fetch(form.action, {
        method: 'POST',
        body: new URLSearchParams(fd),
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            showToast(data.message || 'Rubrique enregistrée.', 'success');
            modal.hide();
            setTimeout(function() { location.reload(); }, 600);
        } else {
            showToast(data.message || 'Erreur lors de l\'enregistrement.', 'error');
        }
    })
    .catch(function() {
        showToast('Erreur réseau. Veuillez réessayer.', 'error');
    });
});
