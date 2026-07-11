<form method="post" action="<?= $baseUrl ?>/anciennete" id="ancienneteForm">
<?= \Core\Session::csrfField() ?>
<input type="hidden" name="sous_tab" value="anciennete">

<div class="card">
    <div class="card-header"><h3>Barème légal d'ancienneté</h3></div>
    <p style="font-size:0.8125rem; color:var(--text-muted); margin:0 0 0.5rem 0;">
        Grille légale selon le Code du Travail marocain. La prime d'ancienneté se calcule sur le salaire de base.
    </p>
    <div style="overflow-x:auto;">
        <table class="data-table" id="anciennete-table">
            <thead>
                <tr>
                    <th style="text-align:center;">Années min</th>
                    <th style="text-align:center;">Années max</th>
                    <th style="text-align:center;">Taux (%)</th>
                    <th style="text-align:center;">Légal</th>
                    <th id="anc-actions-header" style="width:60px; text-align:center; display:none;"></th>
                </tr>
            </thead>
            <tbody id="anc-tbody">
                <?php
                $legal = [
                    [0, 2, 0, 'Moins de 2 ans'],
                    [2, 5, 5, '2 à 5 ans'],
                    [5, 10, 10, '5 à 10 ans'],
                    [10, 15, 15, '10 à 15 ans'],
                    [15, 20, 20, '15 à 20 ans'],
                    [20, 25, 25, '20 à 25 ans'],
                    [25, 99, 30, '25 ans et +'],
                ];
                $rows = !empty($anciennete) ? $anciennete : $legal;
                foreach ($rows as $i => $a):
                    $min = is_array($a) ? ($a['annees_min'] ?? $a[0] ?? 0) : 0;
                    $max = is_array($a) ? ($a['annees_max'] ?? $a[1] ?? 5) : 5;
                    $taux = is_array($a) ? ($a['taux'] ?? $a[2] ?? 0) : 0;
                    $label = is_array($a) ? ($a['label'] ?? $a[3] ?? '—') : '—';
                ?>
                <tr data-min="<?= (int)$min ?>" data-max="<?= (int)$max ?>" data-taux="<?= $taux ?>">
                    <td style="text-align:center;"><?= (int)$min ?></td>
                    <td style="text-align:center;"><?= (int)$max ?></td>
                    <td style="text-align:center;"><?= number_format((float)$taux, 2, ',', ' ') ?></td>
                    <td style="color:var(--text-muted); font-size:0.8rem; text-align:center;"><?= htmlspecialchars($label) ?></td>
                    <td class="anc-edit-action" style="width:60px; text-align:center; display:none;">
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
        <button type="button" id="anc-btn-edit" class="btn btn-sm btn-secondary" onclick="ancToggleEdit(true)" style="font-size:0.75rem;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle; margin-right:0.25rem;"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Modifier
        </button>
        <button type="button" id="anc-btn-add" class="btn btn-sm btn-secondary" onclick="ancAjouter()" style="font-size:0.75rem; display:none;">+ Ajouter une tranche</button>
        <button type="button" id="anc-btn-save" class="btn btn-sm btn-success" onclick="document.getElementById('ancienneteForm').submit()" style="font-size:0.75rem; display:none;">Enregistrer les modifications</button>
        <button type="button" id="anc-btn-cancel" class="btn btn-sm btn-danger" onclick="ancToggleEdit(false)" style="font-size:0.75rem; display:none;">Annuler</button>
    </div>
</div>
</form>

<script>
function ancToggleEdit(edit) {
    document.getElementById('anc-btn-edit').style.display = edit ? 'none' : '';
    document.getElementById('anc-btn-add').style.display = edit ? '' : 'none';
    document.getElementById('anc-btn-save').style.display = edit ? '' : 'none';
    document.getElementById('anc-btn-cancel').style.display = edit ? '' : 'none';
    document.getElementById('anc-actions-header').style.display = edit ? '' : 'none';

    var rows = document.querySelectorAll('#anc-tbody tr');
    rows.forEach(function(row) {
        if (edit) {
            var min = row.dataset.min;
            var max = row.dataset.max;
            var taux = row.dataset.taux;
            var tds = row.querySelectorAll('td');
            tds[0].innerHTML = '<input type="number" name="annees_min[]" class="form-control-inline" style="width:70px; text-align:center;" min="0" max="99" value="' + min + '">';
            tds[1].innerHTML = '<input type="number" name="annees_max[]" class="form-control-inline" style="width:70px; text-align:center;" min="0" max="99" value="' + max + '">';
            tds[2].innerHTML = '<input type="number" name="taux[]" class="form-control-inline" style="width:70px; text-align:center;" step="0.01" min="0" value="' + taux + '">';
        } else {
            location.reload();
        }
    });
}

function ancAjouter() {
    var tbody = document.getElementById('anc-tbody');
    var tr = document.createElement('tr');
    tr.dataset.min = '0';
    tr.dataset.max = '5';
    tr.dataset.taux = '0';
    tr.innerHTML =
        '<td style="text-align:center;"><input type="number" name="annees_min[]" class="form-control-inline" style="width:70px; text-align:center;" min="0" max="99" value="0"></td>' +
        '<td style="text-align:center;"><input type="number" name="annees_max[]" class="form-control-inline" style="width:70px; text-align:center;" min="0" max="99" value="5"></td>' +
        '<td style="text-align:center;"><input type="number" name="taux[]" class="form-control-inline" style="width:70px; text-align:center;" step="0.01" min="0" value="0"></td>' +
        '<td style="color:var(--text-muted); font-size:0.8rem; text-align:center;">—</td>' +
        '<td class="anc-edit-action" style="width:60px; text-align:center;"><div class="table-actions">' +
        '<button type="button" class="btn-icon btn-delete" title="Supprimer" onclick="this.closest(\'tr\').remove()"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg></button>' +
        '</div></td>';
    tbody.appendChild(tr);
}
</script>
