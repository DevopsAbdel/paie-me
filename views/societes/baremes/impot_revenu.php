<form method="post" action="<?= $baseUrl ?>/impot_revenu" id="irForm">
<?= \Core\Session::csrfField() ?>
<input type="hidden" name="sous_tab" value="impot_revenu">
<?php $types = [['key'=>'mensuel','label'=>'Mensuel','data'=>$bareme], ['key'=>'annuel','label'=>'Annuel','data'=>$baremeAnnuel]]; ?>
<?php foreach ($types as $t): ?>
<div class="card" style="<?= $t['key'] === 'annuel' ? 'margin-top:1.5rem;' : '' ?>">
    <div class="card-header"><h3>Barème IR 2025 — <?= $t['label'] ?></h3></div>
    <div style="overflow-x:auto;">
        <table class="data-table ir-table" data-type="<?= $t['key'] ?>">
            <thead>
                <tr>
                    <th style="text-align:center;">Tranche min (MAD)</th>
                    <th style="text-align:center;">Tranche max (MAD)</th>
                    <th style="text-align:center;">Taux (%)</th>
                    <th style="text-align:center;">Déduction (MAD)</th>
                    <th class="ir-edit-header" style="width:60px; text-align:center; display:none;"></th>
                </tr>
            </thead>
            <tbody class="ir-tbody">
                <?php foreach ($t['data'] as $b): ?>
                <tr data-id="<?= $b['id'] ?>" data-min="<?= $b['min'] ?>" data-max="<?= $b['max'] ?>" data-taux="<?= $b['taux'] ?>" data-deduction="<?= $b['deduction'] ?>" data-type-val="<?= $b['type'] ?>">
                    <td style="text-align:right;"><?= number_format((float)$b['min'], 2, ',', ' ') ?></td>
                    <td style="text-align:right;"><?= number_format((float)$b['max'], 2, ',', ' ') ?></td>
                    <td style="text-align:right;"><?= number_format((float)$b['taux'], 2, ',', ' ') ?></td>
                    <td style="text-align:right;"><?= number_format((float)$b['deduction'], 2, ',', ' ') ?></td>
                    <td class="ir-edit-action" style="width:60px; text-align:center; display:none;">
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
        <button type="button" class="btn btn-sm btn-secondary ir-btn-edit" onclick="irToggleEdit(this, true)" style="font-size:0.75rem;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle; margin-right:0.25rem;"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Modifier
        </button>
        <button type="button" class="btn btn-sm btn-success ir-btn-save" onclick="document.getElementById('irForm').submit()" style="font-size:0.75rem; display:none;">Enregistrer les modifications</button>
        <button type="button" class="btn btn-sm btn-danger ir-btn-cancel" onclick="location.reload()" style="font-size:0.75rem; display:none;">Annuler</button>
    </div>
</div>
<?php endforeach; ?>
<div style="margin-top:1rem; padding-top:1rem; border-top:1px solid var(--border);">
    <p style="font-size:0.8125rem; color:var(--text-muted); margin:0;">Barème progressif 2025 appliqué automatiquement au calcul de chaque paie.</p>
</div>
</form>

<script>
function irToggleEdit(btn, edit) {
    var card = btn.closest('.card');
    card.querySelectorAll('.ir-btn-edit').forEach(function(b) { b.style.display = edit ? 'none' : ''; });
    card.querySelectorAll('.ir-btn-save').forEach(function(b) { b.style.display = edit ? '' : 'none'; });
    card.querySelectorAll('.ir-btn-cancel').forEach(function(b) { b.style.display = edit ? '' : 'none'; });
    card.querySelectorAll('.ir-edit-header').forEach(function(h) { h.style.display = edit ? '' : 'none'; });

    card.querySelectorAll('.ir-tbody tr').forEach(function(row) {
        if (edit) {
            var id = row.dataset.id;
            var min = row.dataset.min;
            var max = row.dataset.max;
            var taux = row.dataset.taux;
            var deduction = row.dataset.deduction;
            var typeVal = row.dataset.typeVal;
            var tds = row.querySelectorAll('td');
            tds[0].innerHTML = '<input type="number" name="min[' + id + ']" class="form-control-inline" style="width:100px; text-align:right;" step="0.01" value="' + min + '">';
            tds[1].innerHTML = '<input type="number" name="max[' + id + ']" class="form-control-inline" style="width:100px; text-align:right;" step="0.01" value="' + max + '">';
            tds[2].innerHTML = '<input type="number" name="taux[' + id + ']" class="form-control-inline" style="width:80px; text-align:right;" step="0.01" value="' + taux + '">';
            tds[3].innerHTML = '<input type="number" name="deduction[' + id + ']" class="form-control-inline" style="width:100px; text-align:right;" step="0.01" value="' + deduction + '"><input type="hidden" name="type[' + id + ']" value="' + typeVal + '">';
        }
    });
}
</script>
