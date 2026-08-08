<?php if (!$isAdmin): ?>
<div class="card">
    <div class="card-header"><h3>Barème de référence</h3></div>
    <p style="font-size:0.8125rem; color:var(--text-muted); margin:0;">
        Cette page est réservée à l'administrateur. Le barème de référence sert de base légale commune à toutes les sociétés.
    </p>
</div>
<?php else: ?>

<div class="card" style="background:linear-gradient(135deg, rgba(139,92,246,0.14) 0%, rgba(217,70,239,0.06) 100%); border:1px solid rgba(139,92,246,0.35);">
    <div class="card-header" style="display:flex; align-items:center; gap:0.75rem;">
        <div style="width:40px; height:40px; background:var(--accent); border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v18"/><path d="M5 8h14"/><path d="M5 16h14"/><path d="M8 3v5"/><path d="M16 3v5"/><path d="M8 16v5"/><path d="M16 16v5"/></svg>
        </div>
        <div>
            <h3 style="margin:0;">Barème de référence (base admin)</h3>
            <small style="color:var(--text-muted); font-size:0.75rem;">
                Valeurs légales de référence. Après modification, cliquez sur « Appliquer à toutes les sociétés » pour propager (remplit les barèmes manquants et met à jour les existants).
            </small>
        </div>
    </div>
</div>

<form method="post" action="<?= $baseUrl ?>/reference" id="formRefSmig">
    <?= \Core\Session::csrfField() ?>
    <input type="hidden" name="sous_tab" value="reference">
    <input type="hidden" name="ref_action" value="save">
    <input type="hidden" name="ref_type" value="smig">
    <div class="card">
        <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap;">
            <h3 style="margin:0;">SMIG & SMAG — référence</h3>
            <button type="button" class="btn btn-primary btn-sm" onclick="refSmigAjouter()" style="font-size:0.75rem;">+ Ajouter une ligne</button>
        </div>
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="text-align:center;">Année</th>
                        <th style="text-align:center;">Type</th>
                        <th style="text-align:center;">Taux horaire / journalier (MAD)</th>
                        <th style="text-align:center;">Taux mensuel (MAD/mois)</th>
                        <th style="text-align:center;">Date d'effet</th>
                        <th style="width:60px; text-align:center;">&nbsp;</th>
                    </tr>
                </thead>
                <tbody id="ref-smig-tbody">
                    <?php if (empty($refSmigSmag)): ?>
                    <tr class="ref-smig-empty">
                        <td colspan="6" style="text-align:center; color:var(--text-muted); padding:1.5rem;">Aucune valeur de référence. Ajoutez-en une.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($refSmigSmag as $b): ?>
                    <tr>
                        <td style="text-align:center;"><input type="number" name="ref_smig_annee[]" class="form-control-inline" style="width:80px; text-align:center;" min="2020" max="2035" value="<?= (int) $b['annee'] ?>"></td>
                        <td style="text-align:center;">
                            <select name="ref_smig_type[]" class="form-control-inline no-custom" style="width:90px;">
                                <option value="SMIG" <?= $b['type'] === 'SMIG' ? 'selected' : '' ?>>SMIG</option>
                                <option value="SMAG" <?= $b['type'] === 'SMAG' ? 'selected' : '' ?>>SMAG</option>
                            </select>
                        </td>
                        <td style="text-align:center;"><input type="number" step="0.01" name="ref_smig_horaire[]" class="form-control-inline" style="width:100px; text-align:right;" value="<?= htmlspecialchars($b['horaire']) ?>"></td>
                        <td style="text-align:center;"><input type="number" step="0.01" name="ref_smig_mensuel[]" class="form-control-inline" style="width:100px; text-align:right;" value="<?= htmlspecialchars($b['mensuel']) ?>"></td>
                        <td style="text-align:center;"><input type="date" name="ref_smig_date_effet[]" class="form-control-inline" style="width:140px;" value="<?= htmlspecialchars($b['date_effet'] ?? '') ?>"></td>
                        <td style="text-align:center;">
                            <div class="table-actions">
                                <button type="button" class="btn-icon btn-delete" title="Supprimer" onclick="this.closest('tr').remove()">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div style="padding:0.5rem 0; display:flex; align-items:center; gap:0.5rem;">
            <button type="submit" class="btn btn-sm btn-success" style="font-size:0.75rem;">Enregistrer le barème SMIG/SMAG</button>
        </div>
    </div>
</form>

<form method="post" action="<?= $baseUrl ?>/reference" id="formRefAnc">
    <?= \Core\Session::csrfField() ?>
    <input type="hidden" name="sous_tab" value="reference">
    <input type="hidden" name="ref_action" value="save">
    <input type="hidden" name="ref_type" value="anciennete">
    <div class="card">
        <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap;">
            <h3 style="margin:0;">Ancienneté — référence</h3>
            <button type="button" class="btn btn-primary btn-sm" onclick="refAncAjouter()" style="font-size:0.75rem;">+ Ajouter une tranche</button>
        </div>
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="text-align:center;">Années min</th>
                        <th style="text-align:center;">Années max</th>
                        <th style="text-align:center;">Taux (%)</th>
                        <th style="width:60px; text-align:center;">&nbsp;</th>
                    </tr>
                </thead>
                <tbody id="ref-anc-tbody">
                    <?php if (empty($refAnciennete)): ?>
                    <tr class="ref-anc-empty">
                        <td colspan="4" style="text-align:center; color:var(--text-muted); padding:1.5rem;">Aucune tranche de référence.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($refAnciennete as $a): ?>
                    <tr>
                        <td style="text-align:center;"><input type="number" name="ref_anc_min[]" class="form-control-inline" style="width:70px; text-align:center;" min="0" max="99" value="<?= (int) $a['annees_min'] ?>"></td>
                        <td style="text-align:center;"><input type="number" name="ref_anc_max[]" class="form-control-inline" style="width:70px; text-align:center;" min="0" max="99" value="<?= (int) $a['annees_max'] ?>"></td>
                        <td style="text-align:center;"><input type="number" step="0.01" name="ref_anc_taux[]" class="form-control-inline" style="width:70px; text-align:center;" min="0" value="<?= htmlspecialchars($a['taux']) ?>"></td>
                        <td style="text-align:center;">
                            <div class="table-actions">
                                <button type="button" class="btn-icon btn-delete" title="Supprimer" onclick="this.closest('tr').remove()">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div style="padding:0.5rem 0; display:flex; align-items:center; gap:0.5rem;">
            <button type="submit" class="btn btn-sm btn-success" style="font-size:0.75rem;">Enregistrer le barème d'ancienneté</button>
        </div>
    </div>
</form>

<form method="post" action="<?= $baseUrl ?>/reference" id="formRefHs">
    <?= \Core\Session::csrfField() ?>
    <input type="hidden" name="sous_tab" value="reference">
    <input type="hidden" name="ref_action" value="save">
    <input type="hidden" name="ref_type" value="heures_sup">
    <div class="card">
        <div class="card-header"><h3 style="margin:0;">Heures supplémentaires — référence</h3></div>
        <div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:0.75rem; padding:0.25rem 0;">
            <div class="form-group">
                <label class="form-label" style="font-size:0.75rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Taux normal (%)</label>
                <input type="number" step="0.01" name="ref_hs_taux_normal" class="form-control" value="<?= htmlspecialchars($refHeuresSup['taux_normal'] ?? 25) ?>">
            </div>
            <div class="form-group">
                <label class="form-label" style="font-size:0.75rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Taux majoré (%)</label>
                <input type="number" step="0.01" name="ref_hs_taux_majore" class="form-control" value="<?= htmlspecialchars($refHeuresSup['taux_majore'] ?? 50) ?>">
            </div>
            <div class="form-group">
                <label class="form-label" style="font-size:0.75rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Jour férié (%)</label>
                <input type="number" step="0.01" name="ref_hs_taux_jour_ferie" class="form-control" value="<?= htmlspecialchars($refHeuresSup['taux_jour_ferie'] ?? 100) ?>">
            </div>
            <div class="form-group">
                <label class="form-label" style="font-size:0.75rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Seuil heures / jour</label>
                <input type="number" name="ref_hs_seuil_heures" class="form-control" value="<?= htmlspecialchars($refHeuresSup['seuil_heures'] ?? 8) ?>">
            </div>
        </div>
        <div style="padding:0.25rem 0 0 0; display:flex; align-items:center; gap:0.5rem;">
            <button type="submit" class="btn btn-sm btn-success" style="font-size:0.75rem;">Enregistrer le barème heures sup</button>
        </div>
    </div>
</form>

<div class="card">
    <div class="card-header"><h3 style="margin:0;">Impôt sur le revenu — barème global</h3></div>
    <p style="font-size:0.8125rem; color:var(--text-muted); margin:0;">
        Le barème IR est déjà <strong>global</strong> (commun à toutes les sociétés) : il est géré directement dans la sous-page « Impôt sur le revenu » et n'a pas besoin d'être propagé.
    </p>
</div>

<form method="post" action="<?= $baseUrl ?>/reference" onsubmit="return confirm('Appliquer le barème de référence à toutes les sociétés ? Les barèmes existants seront remplacés par les valeurs de référence (SMIG/SMAG, ancienneté, heures sup).')">
    <?= \Core\Session::csrfField() ?>
    <input type="hidden" name="sous_tab" value="reference">
    <input type="hidden" name="ref_action" value="apply">
    <div class="card" style="border-color:rgba(139,92,246,0.45);">
        <div class="card-header"><h3 style="margin:0;">Appliquer à toutes les sociétés</h3></div>
        <p style="font-size:0.8125rem; color:var(--text-muted); margin:0 0 0.75rem 0;">
            Copie le barème de référence vers toutes les sociétés : met à jour les barèmes existants et insère les lignes manquantes.
        </p>
        <button type="submit" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle; margin-right:0.35rem;"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><polyline points="17 8 17 12 13 12"/></svg>
            Appliquer à toutes les sociétés
        </button>
    </div>
</form>

<script>
function refSmigLigne(annee, type, horaire, mensuel, dateEffet) {
    var tr = document.createElement('tr');
    tr.innerHTML =
        '<td style="text-align:center;"><input type="number" name="ref_smig_annee[]" class="form-control-inline" style="width:80px; text-align:center;" min="2020" max="2035" value="' + annee + '"></td>' +
        '<td style="text-align:center;"><select name="ref_smig_type[]" class="form-control-inline no-custom" style="width:90px;">' +
        '<option value="SMIG"' + (type === 'SMIG' ? ' selected' : '') + '>SMIG</option>' +
        '<option value="SMAG"' + (type === 'SMAG' ? ' selected' : '') + '>SMAG</option></select></td>' +
        '<td style="text-align:center;"><input type="number" step="0.01" name="ref_smig_horaire[]" class="form-control-inline" style="width:100px; text-align:right;" value="' + horaire + '"></td>' +
        '<td style="text-align:center;"><input type="number" step="0.01" name="ref_smig_mensuel[]" class="form-control-inline" style="width:100px; text-align:right;" value="' + mensuel + '"></td>' +
        '<td style="text-align:center;"><input type="date" name="ref_smig_date_effet[]" class="form-control-inline" style="width:140px;" value="' + dateEffet + '"></td>' +
        '<td style="text-align:center;"><div class="table-actions">' +
        '<button type="button" class="btn-icon btn-delete" title="Supprimer" onclick="this.closest(\'tr\').remove()"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg></button></div></td>';
    return tr;
}

function refSmigAjouter() {
    var tbody = document.getElementById('ref-smig-tbody');
    var vide = tbody.querySelector('.ref-smig-empty');
    if (vide) vide.remove();
    tbody.appendChild(refSmigLigne('2026', 'SMIG', '', '', ''));
}

function refAncAjouter() {
    var tbody = document.getElementById('ref-anc-tbody');
    var vide = tbody.querySelector('.ref-anc-empty');
    if (vide) vide.remove();
    var tr = document.createElement('tr');
    tr.innerHTML =
        '<td style="text-align:center;"><input type="number" name="ref_anc_min[]" class="form-control-inline" style="width:70px; text-align:center;" min="0" max="99" value="0"></td>' +
        '<td style="text-align:center;"><input type="number" name="ref_anc_max[]" class="form-control-inline" style="width:70px; text-align:center;" min="0" max="99" value="5"></td>' +
        '<td style="text-align:center;"><input type="number" step="0.01" name="ref_anc_taux[]" class="form-control-inline" style="width:70px; text-align:center;" min="0" value="0"></td>' +
        '<td style="text-align:center;"><div class="table-actions">' +
        '<button type="button" class="btn-icon btn-delete" title="Supprimer" onclick="this.closest(\'tr\').remove()"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg></button></div></td>';
    tbody.appendChild(tr);
}
</script>
<?php endif; ?>
