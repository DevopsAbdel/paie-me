<div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap;">
        <h3 style="margin:0;">Barème SMIG & SMAG</h3>
        <button type="button" class="btn btn-sm btn-secondary" onclick="document.getElementById('bs-add-row').style.display = document.getElementById('bs-add-row').style.display === 'none' ? 'flex' : 'none';">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle; margin-right:0.25rem;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Ajouter
        </button>
    </div>
    <div style="overflow-x:auto;">
        <form method="post" action="<?= $baseUrl ?>/bareme_salaire" id="bsForm">
            <?= \Core\Session::csrfField() ?>
            <input type="hidden" name="sous_tab" value="bareme_salaire">
            <div id="bs-add-row" style="display:none; padding:0.75rem 1rem; border-bottom:1px solid var(--border); background:var(--bg-surface); gap:0.5rem; align-items:flex-end;">
                <div class="form-group" style="margin:0;">
                    <label style="font-size:0.7rem; color:var(--text-muted);">Type</label>
                    <select name="nouveau_type" class="form-control" style="width:80px;" required>
                        <option value="">—</option>
                        <option value="SMIG">SMIG</option>
                        <option value="SMAG">SMAG</option>
                    </select>
                </div>
                <div class="form-group" style="margin:0;">
                    <label style="font-size:0.7rem; color:var(--text-muted);">Année</label>
                    <input type="number" name="nouvelle_annee" class="form-control" placeholder="2026" style="width:90px;" min="2020" max="2035" required>
                </div>
                <div class="form-group" style="margin:0;">
                    <label style="font-size:0.7rem; color:var(--text-muted);">Horaire</label>
                    <input type="number" step="0.01" name="nouveau_horaire" class="form-control" placeholder="17.92" style="width:100px;" required>
                </div>
                <div class="form-group" style="margin:0;">
                    <label style="font-size:0.7rem; color:var(--text-muted);">Mensuel</label>
                    <input type="number" step="0.01" name="nouveau_mensuel" class="form-control" placeholder="3422.72" style="width:100px;" required>
                </div>
                <button type="submit" class="btn btn-success btn-sm">Ajouter</button>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="text-align:center;">Année</th>
                        <th style="text-align:center;">Type</th>
                        <th style="text-align:center;">Taux horaire (MAD/h)</th>
                        <th style="text-align:center;">Taux mensuel (MAD/mois)</th>
                        <th style="text-align:center;">Date d'effet</th>
                        <th id="bs-actions-header" style="width:60px; text-align:center; display:none;"></th>
                    </tr>
                </thead>
                <tbody id="bs-tbody">
                    <?php if (empty($baremeSmigSmag)): ?>
                    <tr>
                        <td colspan="6" style="text-align:center; color:var(--text-muted); padding:2rem;">
                            Aucun barème SMIG/SMAG défini.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($baremeSmigSmag as $b): ?>
                    <tr data-id="<?= $b['id'] ?>" data-horaire="<?= $b['horaire'] ?>" data-mensuel="<?= $b['mensuel'] ?>" data-date="<?= htmlspecialchars($b['date_effet'] ?? '') ?>">
                        <td style="text-align:center; font-weight:600;"><?= (int) $b['annee'] ?></td>
                        <td style="text-align:center;"><span class="badge badge-<?= $b['type'] === 'SMIG' ? 'primary' : 'info' ?>"><?= htmlspecialchars($b['type']) ?></span></td>
                        <td style="text-align:right;"><?= number_format((float)$b['horaire'], 2, ',', ' ') ?></td>
                        <td style="text-align:right;"><?= number_format((float)$b['mensuel'], 2, ',', ' ') ?></td>
                        <td style="text-align:center;"><?= htmlspecialchars($b['date_effet'] ?? '—') ?></td>
                        <td class="bs-edit-action" style="width:60px; text-align:center; display:none;">
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
            <div style="padding:0.5rem 0; display:flex; align-items:center; gap:0.5rem;">
                <button type="button" id="bs-btn-edit" class="btn btn-sm btn-secondary" onclick="bsToggleEdit(true)" style="font-size:0.75rem;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle; margin-right:0.25rem;"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Modifier
                </button>
                <button type="button" id="bs-btn-save" class="btn btn-sm btn-success" onclick="document.getElementById('bsForm').submit()" style="font-size:0.75rem; display:none;">Enregistrer les modifications</button>
                <button type="button" id="bs-btn-cancel" class="btn btn-sm btn-danger" onclick="location.reload()" style="font-size:0.75rem; display:none;">Annuler</button>
            </div>
        </form>
    </div>
</div>

<script>
function bsToggleEdit(edit) {
    document.getElementById('bs-btn-edit').style.display = edit ? 'none' : '';
    document.getElementById('bs-btn-save').style.display = edit ? '' : 'none';
    document.getElementById('bs-btn-cancel').style.display = edit ? '' : 'none';
    document.getElementById('bs-actions-header').style.display = edit ? '' : 'none';

    document.querySelectorAll('#bs-tbody tr').forEach(function(row) {
        if (edit && row.dataset.id) {
            var id = row.dataset.id;
            var horaire = row.dataset.horaire;
            var mensuel = row.dataset.mensuel;
            var dateEffet = row.dataset.date;
            var tds = row.querySelectorAll('td');
            tds[2].innerHTML = '<input type="hidden" name="bareme_id[]" value="' + id + '"><input type="number" step="0.01" name="horaire[]" class="form-control-inline" value="' + horaire + '" style="width:100px; text-align:right;">';
            tds[3].innerHTML = '<input type="number" step="0.01" name="mensuel[]" class="form-control-inline" value="' + mensuel + '" style="width:100px; text-align:right;">';
            tds[4].innerHTML = '<input type="date" name="date_effet[]" class="form-control-inline" value="' + dateEffet + '" style="width:140px;">';
        }
    });
}
</script>
