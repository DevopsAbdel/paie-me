<form method="post" action="<?= $baseUrl ?>/conge_annuel" id="congeAnnuelForm">
<?= \Core\Session::csrfField() ?>
<input type="hidden" name="sous_tab" value="conge_annuel">

<div class="card">
    <div class="card-header"><h3>Configuration congé annuel</h3></div>
    <p style="font-size:0.8125rem; color:var(--text-muted); margin:0 0 0.75rem 0;">
        Selon le Code du Travail marocain (Art. 231) : 1,5 jour ouvrable par mois de travail effectif (18 jours/an).
        Des jours supplémentaires sont accordés selon l'ancienneté du salarié.
    </p>

    <input type="hidden" name="jours_par_mois" value="1.50">
    <h4 class="form-section-title">Paramètres généraux</h4>
    <hr class="form-section-sep">
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.75rem;">
        <div class="form-group">
            <label>Délai d'ancienneté (mois)</label>
            <input type="number" name="delai_anciennete" value="<?= htmlspecialchars($conge['delai_anciennete'] ?? '6') ?>" class="form-control" min="0">
            <small style="color:var(--text-muted); font-size:0.7rem;">Mois avant éligibilité au congé payé</small>
        </div>
        <div class="form-group">
            <label>Report autorisé</label>
            <select name="report_autorise" class="form-control">
                <option value="1" <?= ($conge['report_autorise'] ?? 1) ? 'selected' : '' ?>>Oui</option>
                <option value="0" <?= !($conge['report_autorise'] ?? 1) ? 'selected' : '' ?>>Non</option>
            </select>
        </div>
        <div class="form-group">
            <label>Report max (jours)</label>
            <input type="number" name="report_max" value="<?= htmlspecialchars($conge['report_max'] ?? '15') ?>" class="form-control" min="0">
        </div>
        <div class="form-group">
            <label>Report max (années consécutives)</label>
            <input type="number" name="report_max_annees" value="<?= htmlspecialchars($conge['report_max_annees'] ?? '2') ?>" class="form-control" min="0">
            <small style="color:var(--text-muted); font-size:0.7rem;">Années consécutives max (défaut : 2)</small>
        </div>
    </div>

    <h4 class="form-section-title">Droit au congé annuel payé par ancienneté</h4>
    <hr class="form-section-sep">

    <div class="table-wrapper">
        <table class="data-table" id="droit-conge-table">
            <thead>
                <tr>
                    <th style="text-align:center;">Début (ans)</th>
                    <th style="text-align:center;">Fin (ans)</th>
                    <th style="text-align:center;">Jours / Mois</th>
                    <th style="text-align:center;">Jours supplémentaires</th>
                    <th style="text-align:center;">Total / An</th>
                    <th id="dc-actions-header" style="width:60px; text-align:center; display:none;"></th>
                </tr>
            </thead>
            <tbody id="dc-tbody">
                <?php
                $defaultTranches = [
                    [0, 5, 1.50, 0.00],
                    [5, 10, 1.50, 1.50],
                    [10, 15, 1.50, 3.00],
                    [15, 20, 1.50, 4.50],
                    [20, 25, 1.50, 6.00],
                    [25, 30, 1.50, 7.50],
                    [30, 35, 1.50, 9.00],
                    [35, 40, 1.50, 10.50],
                    [40, 99, 1.50, 12.00],
                ];
                $tranches = !empty($droitConge) ? $droitConge : $defaultTranches;
                foreach ($tranches as $dc):
                    $min = is_array($dc) ? ($dc['annees_min'] ?? 0) : $dc['annees_min'];
                    $max = is_array($dc) ? ($dc['annees_max'] ?? 5) : $dc['annees_max'];
                    $jpm = is_array($dc) ? ($dc['jours_par_mois'] ?? 1.50) : $dc['jours_par_mois'];
                    $jsup = is_array($dc) ? ($dc['jours_supplementaires'] ?? 0) : $dc['jours_supplementaires'];
                    $total = (float)$jpm * 12 + (float)$jsup;
                ?>
                <tr data-min="<?= (int)$min ?>" data-max="<?= (int)$max ?>" data-jpm="<?= $jpm ?>" data-jsup="<?= $jsup ?>">
                    <td style="text-align:center;"><?= (int)$min ?></td>
                    <td style="text-align:center;"><?= (int)$max ?></td>
                    <td style="text-align:center;"><?= number_format((float)$jpm, 2, ',', ' ') ?></td>
                    <td style="text-align:center;"><?= number_format((float)$jsup, 2, ',', ' ') ?></td>
                    <td style="text-align:center; font-weight:500;"><?= number_format($total, 2, ',', ' ') ?></td>
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

    <div style="padding:0.5rem 0; display:flex; align-items:center; gap:0.5rem;">
        <button type="button" id="dc-btn-edit" class="btn btn-sm btn-secondary" onclick="dcToggleEdit(true)" style="font-size:0.75rem;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle; margin-right:0.25rem;"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Modifier
        </button>
        <button type="button" id="dc-btn-add" class="btn btn-sm btn-secondary" onclick="dcAjouter()" style="font-size:0.75rem; display:none;">+ Ajouter une tranche</button>
        <button type="button" id="dc-btn-cancel" class="btn btn-sm btn-secondary" onclick="dcToggleEdit(false)" style="font-size:0.75rem; display:none;">Annuler</button>
    </div>
</div>

<div style="margin-top:1rem; display:flex; justify-content:flex-end;">
    <button type="submit" class="btn btn-primary">Enregistrer</button>
</div>
</form>

<script>
function dcToggleEdit(edit) {
    document.getElementById('dc-btn-edit').style.display = edit ? 'none' : '';
    document.getElementById('dc-btn-add').style.display = edit ? '' : 'none';
    document.getElementById('dc-btn-cancel').style.display = edit ? '' : 'none';
    document.getElementById('dc-actions-header').style.display = edit ? '' : 'none';

    var rows = document.querySelectorAll('#dc-tbody tr');
    rows.forEach(function(row) {
        if (edit) {
            var min = row.dataset.min;
            var max = row.dataset.max;
            var jpm = row.dataset.jpm;
            var jsup = row.dataset.jsup;
            var tds = row.querySelectorAll('td');
            tds[0].innerHTML = '<input type="number" name="dc_annees_min[]" class="form-control-inline" style="width:70px; text-align:center;" min="0" max="99" value="' + min + '">';
            tds[1].innerHTML = '<input type="number" name="dc_annees_max[]" class="form-control-inline" style="width:70px; text-align:center;" min="0" max="99" value="' + max + '">';
            tds[2].innerHTML = '<input type="number" name="dc_jours_par_mois[]" class="form-control-inline" style="width:70px; text-align:center;" step="0.01" min="0" value="' + jpm + '">';
            tds[3].innerHTML = '<input type="number" name="dc_jours_sup[]" class="form-control-inline" style="width:70px; text-align:center;" step="0.01" min="0" value="' + jsup + '">';
            var total = parseFloat(jpm) * 12 + parseFloat(jsup);
            tds[4].innerHTML = total.toFixed(2).replace('.', ',');
            tds[4].style.fontWeight = '500';
        } else {
            location.reload();
        }
    });
    if (!edit) return;
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
        row.querySelectorAll('td')[4].innerHTML = (jpm * 12 + jsup).toFixed(2).replace('.', ',');
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
        '<td style="text-align:center;"><input type="number" name="dc_annees_min[]" class="form-control-inline" style="width:70px; text-align:center;" min="0" max="99" value="0"></td>' +
        '<td style="text-align:center;"><input type="number" name="dc_annees_max[]" class="form-control-inline" style="width:70px; text-align:center;" min="0" max="99" value="5"></td>' +
        '<td style="text-align:center;"><input type="number" name="dc_jours_par_mois[]" class="form-control-inline" style="width:70px; text-align:center;" step="0.01" min="0" value="1.50"></td>' +
        '<td style="text-align:center;"><input type="number" name="dc_jours_sup[]" class="form-control-inline" style="width:70px; text-align:center;" step="0.01" min="0" value="0"></td>' +
        '<td style="text-align:center; font-weight:500;">18,00</td>' +
        '<td class="dc-edit-action" style="width:60px; text-align:center;"><div class="table-actions">' +
        '<button type="button" class="btn-icon btn-delete" title="Supprimer" onclick="this.closest(\'tr\').remove()"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg></button>' +
        '</div></td>';
    tbody.appendChild(tr);
    dcRecalcAll();
}
</script>
