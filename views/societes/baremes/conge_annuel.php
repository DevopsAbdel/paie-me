<form method="post" action="<?= $baseUrl ?>/conge_annuel">
<?= \Core\Session::csrfField() ?>
<input type="hidden" name="sous_tab" value="conge_annuel">

<div class="card">
    <div class="card-header"><h3>Configuration congé annuel</h3></div>
    <p style="font-size:0.8125rem; color:var(--text-muted); margin:0 0 0.75rem 0;">
        Selon le Code du Travail marocain (Art. 231) : 1,5 jour ouvrable par mois de travail effectif (18 jours/an).
        Des jours supplémentaires sont accordés selon l'ancienneté du salarié.
    </p>

    <div style="display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:0.75rem; max-width:800px; margin-bottom:1rem;">
        <div class="form-group">
            <label>Délai d'ancienneté (mois)</label>
            <input type="number" name="delai_anciennete" value="<?= htmlspecialchars($conge['delai_anciennete'] ?? '6') ?>" class="form-control" min="0">
            <small style="color:var(--text-muted); font-size:0.7rem;">Mois avant éligibilité au congé payé (défaut : 6)</small>
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
            <label>Report max (années)</label>
            <input type="number" name="report_max_annees" value="<?= htmlspecialchars($conge['report_max_annees'] ?? '2') ?>" class="form-control" min="0">
            <small style="color:var(--text-muted); font-size:0.7rem;">Années consécutives max (défaut : 2)</small>
        </div>
    </div>

    <h4 style="font-size:0.85rem; font-weight:600; margin-bottom:0.5rem;">Droit au congé annuel payé par ancienneté</h4>

    <div class="table-wrapper">
        <table class="edit-paie-table" id="droit-conge-table">
            <thead>
                <tr>
                    <th style="width:15%; text-align:center;">Début (ans)</th>
                    <th style="width:15%; text-align:center;">Fin (ans)</th>
                    <th style="width:18%; text-align:center;">Jours / Mois</th>
                    <th style="width:22%; text-align:center;">Jours supplémentaires</th>
                    <th style="width:18%; text-align:center;">Total / An</th>
                    <th style="width:12%; text-align:center;"></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($droitConge)): ?>
                <?php foreach ($droitConge as $dc): ?>
                <tr>
                    <td><input type="number" name="dc_annees_min[]" class="form-control-inline" style="width:60px; text-align:center;" min="0" max="99" value="<?= (int)$dc['annees_min'] ?>"></td>
                    <td><input type="number" name="dc_annees_max[]" class="form-control-inline" style="width:60px; text-align:center;" min="0" max="99" value="<?= (int)$dc['annees_max'] ?>"></td>
                    <td><input type="number" name="dc_jours_par_mois[]" class="form-control-inline" style="width:70px; text-align:center;" step="0.01" min="0" value="<?= $dc['jours_par_mois'] ?>"></td>
                    <td><input type="number" name="dc_jours_sup[]" class="form-control-inline" style="width:70px; text-align:center;" step="0.01" min="0" value="<?= $dc['jours_supplementaires'] ?>"></td>
                    <td class="montant" style="font-weight:500;"><?= number_format((float)$dc['jours_par_mois'] * 12 + (float)$dc['jours_supplementaires'], 2, ',', ' ') ?></td>
                    <td>
                        <div class="table-actions">
                            <button type="button" class="btn-icon btn-delete" title="Supprimer la tranche" onclick="this.closest('tr').remove()">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td><input type="number" name="dc_annees_min[]" class="form-control-inline" style="width:60px; text-align:center;" min="0" max="99" value="0"></td>
                    <td><input type="number" name="dc_annees_max[]" class="form-control-inline" style="width:60px; text-align:center;" min="0" max="99" value="5"></td>
                    <td><input type="number" name="dc_jours_par_mois[]" class="form-control-inline" style="width:70px; text-align:center;" step="0.01" min="0" value="1.50"></td>
                    <td><input type="number" name="dc_jours_sup[]" class="form-control-inline" style="width:70px; text-align:center;" step="0.01" min="0" value="0"></td>
                    <td class="montant" style="font-weight:500;">18,00</td>
                    <td>
                        <div class="table-actions">
                            <button type="button" class="btn-icon btn-delete" title="Supprimer la tranche" onclick="this.closest('tr').remove()">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
                <tr id="droit-conge-container"></tr>
            </tbody>
        </table>
    </div>
    <div style="padding:0.5rem 0;">
        <button type="button" class="btn btn-sm btn-secondary" onclick="ajouterTranche()" style="font-size:0.75rem;">+ Ajouter une tranche</button>
    </div>
</div>

<div style="margin-top:1rem; display:flex; justify-content:flex-end;">
    <button type="submit" class="btn btn-primary">Enregistrer</button>
</div>
</form>

<script>
function ajouterTranche() {
    var tbody = document.querySelector('#droit-conge-table tbody');
    var container = document.getElementById('droit-conge-container');
    var tr = document.createElement('tr');
    tr.innerHTML =
        '<td><input type="number" name="dc_annees_min[]" class="form-control-inline" style="width:60px; text-align:center;" min="0" max="99" value="0"></td>' +
        '<td><input type="number" name="dc_annees_max[]" class="form-control-inline" style="width:60px; text-align:center;" min="0" max="99" value="5"></td>' +
        '<td><input type="number" name="dc_jours_par_mois[]" class="form-control-inline" style="width:70px; text-align:center;" step="0.01" min="0" value="1.50"></td>' +
        '<td><input type="number" name="dc_jours_sup[]" class="form-control-inline" style="width:70px; text-align:center;" step="0.01" min="0" value="0"></td>' +
        '<td class="montant" style="font-weight:500;">18,00</td>' +
        '<td><div class="table-actions">' +
        '<button type="button" class="btn-icon btn-delete" title="Supprimer la tranche" onclick="this.closest(\'tr\').remove()"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg></button>' +
        '</div></td>';
    container.before(tr);
    recalcAllTotals();
}

document.addEventListener('input', function(e) {
    if (e.target.name && (e.target.name.startsWith('dc_jours_par_mois') || e.target.name.startsWith('dc_jours_sup'))) {
        recalcAllTotals();
    }
});

function recalcAllTotals() {
    var rows = document.querySelectorAll('#droit-conge-table tbody tr');
    rows.forEach(function(row) {
        var jpmInput = row.querySelector('input[name="dc_jours_par_mois[]"]');
        var jsupInput = row.querySelector('input[name="dc_jours_sup[]"]');
        var totalCell = row.querySelector('.montant');
        if (!jpmInput || !jsupInput || !totalCell) return;
        var jpm = parseFloat(jpmInput.value) || 0;
        var jsup = parseFloat(jsupInput.value) || 0;
        var total = jpm * 12 + jsup;
        totalCell.textContent = total.toFixed(2).replace('.', ',');
    });
}
</script>
