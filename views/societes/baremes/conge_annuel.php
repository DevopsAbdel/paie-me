<form method="post" action="<?= $baseUrl ?>/conge_annuel" id="congeAnnuelForm">
<?= \Core\Session::csrfField() ?>
<input type="hidden" name="sous_tab" value="conge_annuel">

<div class="card">
    <div class="card-header">
        <h3>Configuration congé annuel</h3>
        <span class="badge badge-primary">Base légale : 1,5 j/mois = 18 j/an</span>
    </div>
    <div style="display:flex; gap:0.85rem; align-items:flex-start; background:rgba(139,92,246,0.08); border:1px solid rgba(139,92,246,0.25); border-radius:10px; padding:0.95rem 1.1rem;">
        <div style="flex-shrink:0; width:36px; height:36px; background:var(--accent); border-radius:9px; display:flex; align-items:center; justify-content:center;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
        </div>
        <div>
            <div style="font-weight:700; font-size:0.85rem; color:var(--text);">Art. 231 — Code du Travail marocain</div>
            <div style="font-size:0.8125rem; color:var(--text-muted); line-height:1.5; margin-top:0.15rem;">
                Le salarié acquiert <strong style="color:var(--text);">1,5 jour ouvrable par mois</strong> de travail effectif
                (soit <strong style="color:var(--text);">18 jours/an</strong>). Des jours supplémentaires sont accordés selon
                l'ancienneté : chaque tranche donne droit au total affiché dans la dernière colonne du tableau.
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Paramètres généraux</h3>
    </div>
    <input type="hidden" name="jours_par_mois" value="<?= htmlspecialchars($conge['jours_par_mois'] ?? '1.50') ?>">
    <div style="display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:0.9rem;">
        <div class="form-group">
            <label style="font-size:0.72rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Délai d'ancienneté</label>
            <input type="number" name="delai_anciennete" value="<?= htmlspecialchars($conge['delai_anciennete'] ?? '6') ?>" class="form-control" min="6">
            <small style="color:var(--text-muted); font-size:0.7rem; margin-top:0.25rem; display:block;">Ancienneté minimale avant ouverture du droit (en mois)</small>
        </div>
        <div class="form-group">
            <label style="font-size:0.72rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Report autorisé</label>
            <select name="report_autorise" class="form-control">
                <option value="1" <?= ($conge['report_autorise'] ?? 1) ? 'selected' : '' ?>>Oui</option>
                <option value="0" <?= !($conge['report_autorise'] ?? 1) ? 'selected' : '' ?>>Non</option>
            </select>
            <small style="color:var(--text-muted); font-size:0.7rem; margin-top:0.25rem; display:block;">Reporter les congés non pris sur l'année suivante</small>
        </div>
        <div class="form-group">
            <label style="font-size:0.72rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Report max (jours)</label>
            <input type="number" name="report_max" value="<?= htmlspecialchars($conge['report_max'] ?? '15') ?>" class="form-control" min="0" max="15">
            <small style="color:var(--text-muted); font-size:0.7rem; margin-top:0.25rem; display:block;">Plafond de jours reportables sur une année</small>
        </div>
        <div class="form-group">
            <label style="font-size:0.72rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Report max (années consécutives)</label>
            <input type="number" name="report_max_annees" value="<?= htmlspecialchars($conge['report_max_annees'] ?? '2') ?>" class="form-control" min="0" max="2">
            <small style="color:var(--text-muted); font-size:0.7rem; margin-top:0.25rem; display:block;">Nombre d'années successives de report autorisé</small>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Droit au congé annuel payé par ancienneté</h3>
    </div>

    <div class="table-wrapper">
        <table class="data-table" id="droit-conge-table">
            <thead>
                <tr>
                    <th style="text-align:center;">Tranche d'ancienneté</th>
                    <th style="text-align:center;">Jours / Mois</th>
                    <th style="text-align:center;">Jours supplémentaires / an</th>
                    <th style="text-align:center;">Total / An</th>
                    <th id="dc-actions-header" style="width:60px; text-align:center; display:none;"></th>
                </tr>
            </thead>
            <tbody id="dc-tbody">
                <?php
                $defaultTranches = [
                    ['annees_min' => 0,  'annees_max' => 5,  'jours_par_mois' => 1.50, 'jours_supplementaires' => 0.00],
                    ['annees_min' => 5,  'annees_max' => 10, 'jours_par_mois' => 1.50, 'jours_supplementaires' => 1.50],
                    ['annees_min' => 10, 'annees_max' => 15, 'jours_par_mois' => 1.50, 'jours_supplementaires' => 3.00],
                    ['annees_min' => 15, 'annees_max' => 20, 'jours_par_mois' => 1.50, 'jours_supplementaires' => 4.50],
                    ['annees_min' => 20, 'annees_max' => 25, 'jours_par_mois' => 1.50, 'jours_supplementaires' => 6.00],
                    ['annees_min' => 25, 'annees_max' => 30, 'jours_par_mois' => 1.50, 'jours_supplementaires' => 7.50],
                    ['annees_min' => 30, 'annees_max' => 35, 'jours_par_mois' => 1.50, 'jours_supplementaires' => 9.00],
                    ['annees_min' => 35, 'annees_max' => 40, 'jours_par_mois' => 1.50, 'jours_supplementaires' => 10.50],
                    ['annees_min' => 40, 'annees_max' => 99, 'jours_par_mois' => 1.50, 'jours_supplementaires' => 12.00],
                ];
                $tranches = !empty($droitConge) ? $droitConge : $defaultTranches;
                $maxTotal = 0;
                $maxLabel = '';
                foreach ($tranches as $dc):
                    $min = (int)($dc['annees_min'] ?? 0);
                    $max = (int)($dc['annees_max'] ?? 99);
                    $jpm = (float)($dc['jours_par_mois'] ?? 1.50);
                    $jsup = (float)($dc['jours_supplementaires'] ?? 0);
                    $total = $jpm * 12 + $jsup;
                    if ($total > $maxTotal) { $maxTotal = $total; $maxLabel = ($max >= 99 ? ($min . ' ans et +') : ($min . ' à ' . $max . ' ans')); }
                ?>
                <tr data-min="<?= $min ?>" data-max="<?= $max ?>" data-jpm="<?= $jpm ?>" data-jsup="<?= $jsup ?>">
                    <td style="text-align:center;">
                        <span class="badge badge-dark"><?= $max >= 99 ? ($min . ' ans et +') : ($min . ' à ' . $max . ' ans') ?></span>
                    </td>
                    <td style="text-align:center;"><?= number_format($jpm, 2, ',', ' ') ?></td>
                    <td style="text-align:center;">
                        <?php if ($jsup > 0): ?>
                            <span class="badge badge-success">+ <?= number_format($jsup, 2, ',', ' ') ?></span>
                        <?php else: ?>
                            <span style="color:var(--text-muted);">—</span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align:center; font-weight:700; color:var(--accent); font-size:0.875rem;"><?= number_format($total, 2, ',', ' ') ?></td>
                    <td class="dc-edit-action" style="width:60px; text-align:center; display:none;">
                        <div class="table-actions">
                            <button type="button" class="btn-icon btn-delete" title="Supprimer" onclick="this.closest('tr').remove()">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($maxLabel): ?>
    <div style="display:flex; gap:0.5rem; flex-wrap:wrap; margin-top:0.5rem;">
        <span class="badge badge-info">Droit maximal : <?= number_format($maxTotal, 2, ',', ' ') ?> j/an</span>
        <span class="badge badge-dark">Atteint après <?= $maxLabel ?></span>
    </div>
    <?php endif; ?>

    <div style="padding:0.5rem 0; display:flex; align-items:center; justify-content:flex-end; gap:0.5rem;">
        <button type="button" id="dc-btn-edit" class="btn btn-warning btn-sm" onclick="dcToggleEdit(true)" style="font-size:0.75rem;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle; margin-right:0.25rem;"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Modifier
        </button>
        <button type="button" id="dc-btn-add" class="btn btn-primary btn-sm" onclick="dcAjouter()" style="font-size:0.75rem; display:none;">+ Ajouter une tranche</button>
        <button type="button" id="dc-btn-save" class="btn btn-sm btn-success" onclick="document.getElementById('congeAnnuelForm').submit()" style="font-size:0.75rem; display:none;">Enregistrer les modifications</button>
        <button type="button" id="dc-btn-cancel" class="btn btn-secondary btn-sm" onclick="dcToggleEdit(false)" style="font-size:0.75rem; display:none;">Annuler</button>
    </div>
</div>

<div style="margin-top:1rem; display:flex; justify-content:flex-end;">
    <button type="submit" class="btn btn-success">Enregistrer la configuration</button>
</div>
</form>

<script>
function dcToggleEdit(edit) {
    document.getElementById('dc-btn-edit').style.display = edit ? 'none' : '';
    document.getElementById('dc-btn-add').style.display = edit ? '' : 'none';
    document.getElementById('dc-btn-save').style.display = edit ? '' : 'none';
    document.getElementById('dc-btn-cancel').style.display = edit ? '' : 'none';
    document.getElementById('dc-actions-header').style.display = edit ? '' : 'none';
    var bottomBtn = document.querySelector('#congeAnnuelForm button[type="submit"]');
    if (bottomBtn) bottomBtn.style.display = edit ? 'none' : '';

    var rows = document.querySelectorAll('#dc-tbody tr');
    if (!edit) { location.reload(); return; }

    rows.forEach(function(row) {
        var min = row.dataset.min;
        var max = row.dataset.max;
        var jpm = row.dataset.jpm;
        var jsup = row.dataset.jsup;
        var tds = row.querySelectorAll('td');
        tds[0].innerHTML = '<div style="display:flex; justify-content:center; align-items:center; gap:0.25rem;">' +
            '<input type="number" name="dc_annees_min[]" class="form-control-inline" style="width:56px; text-align:center;" min="0" max="99" value="' + min + '">' +
            '<span style="color:var(--text-muted);">–</span>' +
            '<input type="number" name="dc_annees_max[]" class="form-control-inline" style="width:56px; text-align:center;" min="0" max="99" value="' + max + '">' +
            '</div>';
        tds[1].innerHTML = '<input type="number" name="dc_jours_par_mois[]" class="form-control-inline" style="width:70px; text-align:center;" step="0.01" min="0" value="' + jpm + '">';
        tds[2].innerHTML = '<input type="number" name="dc_jours_sup[]" class="form-control-inline" style="width:70px; text-align:center;" step="0.01" min="0" value="' + jsup + '">';
    });
    dcRecalcAll();
}

document.addEventListener('input', function(e) {
    if (e.target.name === 'dc_jours_par_mois[]' || e.target.name === 'dc_jours_sup[]') {
        dcRecalcAll();
    }
});

function dcRecalcAll() {
    document.querySelectorAll('#dc-tbody tr').forEach(function(row) {
        var inputs = row.querySelectorAll('input');
        if (inputs.length < 4) return;
        var jpm = parseFloat(inputs[2].value) || 0;
        var jsup = parseFloat(inputs[3].value) || 0;
        row.querySelectorAll('td')[3].innerHTML = '<span style="font-weight:700; color:var(--accent); font-size:0.875rem;">' + (jpm * 12 + jsup).toFixed(2).replace('.', ',') + '</span>';
    });
}

function dcAjouter() {
    var tbody = document.getElementById('dc-tbody');
    var tr = document.createElement('tr');
    tr.dataset.min = '0';
    tr.dataset.max = '5';
    tr.dataset.jpm = '1.5';
    tr.dataset.jsup = '0';
    tr.innerHTML =
        '<td style="text-align:center;"><div style="display:flex; justify-content:center; align-items:center; gap:0.25rem;">' +
        '<input type="number" name="dc_annees_min[]" class="form-control-inline" style="width:56px; text-align:center;" min="0" max="99" value="0">' +
        '<span style="color:var(--text-muted);">–</span>' +
        '<input type="number" name="dc_annees_max[]" class="form-control-inline" style="width:56px; text-align:center;" min="0" max="99" value="5">' +
        '</div></td>' +
        '<td style="text-align:center;"><input type="number" name="dc_jours_par_mois[]" class="form-control-inline" style="width:70px; text-align:center;" step="0.01" min="0" value="1.50"></td>' +
        '<td style="text-align:center;"><input type="number" name="dc_jours_sup[]" class="form-control-inline" style="width:70px; text-align:center;" step="0.01" min="0" value="0"></td>' +
        '<td style="text-align:center;"><span style="font-weight:700; color:var(--accent); font-size:0.875rem;">18,00</span></td>' +
        '<td class="dc-edit-action" style="width:60px; text-align:center;"><div class="table-actions">' +
        '<button type="button" class="btn-icon btn-delete" title="Supprimer" onclick="this.closest(\'tr\').remove()"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg></button>' +
        '</div></td>';
    tbody.appendChild(tr);
    dcRecalcAll();
}
</script>
