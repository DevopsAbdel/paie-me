<div class="card" style="margin-bottom:1rem;">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; gap:1rem;">
        <h3 style="margin:0;">Jours fériés</h3>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#ajoutJourFerie">+ Ajouter</button>
    </div>
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr><th>Nom</th><th>Date</th><th>Type</th><th>Actif</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php if (empty($joursFeries)): ?>
                <tr><td colspan="5" style="text-align:center; color:var(--text-muted);">Aucun jour férié</td></tr>
                <?php else: ?>
                <?php foreach ($joursFeries as $jf): ?>
                <tr>
                    <td><?= htmlspecialchars($jf['nom']) ?></td>
                    <td><?= str_pad($jf['jour'], 2, '0', STR_PAD_LEFT) ?>/<?= str_pad($jf['mois'], 2, '0', STR_PAD_LEFT) ?></td>
                    <td><span class="badge badge-<?= $jf['type'] === 'fixe' ? 'info' : 'warning' ?>"><?= $jf['type'] === 'fixe' ? 'Fixe' : 'Variable' ?></span></td>
                    <td><span class="badge badge-<?= $jf['actif'] ? 'success' : 'secondary' ?>"><?= $jf['actif'] ? 'Oui' : 'Non' ?></span></td>
                    <td>
                        <div class="table-actions">
                            <button type="button" class="btn-icon btn-edit" title="Modifier" onclick="modifierJF(<?= (int)$jf['id'] ?>, <?= json_encode(htmlspecialchars($jf['nom'])) ?>, <?= (int)$jf['jour'] ?>, <?= (int)$jf['mois'] ?>, '<?= $jf['type'] ?>')">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </button>
                            <a href="<?= $baseUrl ?>/jours_feries?delete_jf=<?= $jf['id'] ?>" class="btn-icon btn-delete" title="Supprimer" onclick="return confirm('Supprimer ce jour férié ?')">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Modifier jour férié -->
<div class="modal fade" id="editJourFerie" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background:var(--bg-surface); color:var(--text); border:1px solid var(--border); border-radius:12px;">
            <form method="post" action="<?= $baseUrl ?>/jours_feries">
                <?= \Core\Session::csrfField() ?>
                <input type="hidden" name="sous_tab" value="jours_feries">
                <input type="hidden" name="edit_jf_id" id="edit_jf_id">
                <div class="modal-header" style="border-bottom:1px solid var(--border);">
                    <h5 class="modal-title">Modifier jour férié</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group" style="margin-bottom:1rem;">
                        <label class="form-label" style="font-size:0.75rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Nom du jour férié</label>
                        <input type="text" name="nom" id="edit_jf_nom" class="form-control" required>
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                        <div class="form-group">
                            <label class="form-label" style="font-size:0.75rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Jour</label>
                            <input type="number" name="jour" id="edit_jf_jour" class="form-control" min="1" max="31" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="font-size:0.75rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Mois</label>
                            <input type="number" name="mois" id="edit_jf_mois" class="form-control" min="1" max="12" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="font-size:0.75rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Type</label>
                        <select name="type" id="edit_jf_type" class="form-control">
                            <option value="fixe">Fixe</option>
                            <option value="variable">Variable</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--border);">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary btn-sm">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function modifierJF(id, nom, jour, mois, type) {
    document.getElementById('edit_jf_id').value = id;
    document.getElementById('edit_jf_nom').value = nom;
    document.getElementById('edit_jf_jour').value = jour;
    document.getElementById('edit_jf_mois').value = mois;
    document.getElementById('edit_jf_type').value = type;
    new bootstrap.Modal(document.getElementById('editJourFerie')).show();
}
</script>
<div class="modal fade" id="ajoutJourFerie" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background:var(--bg-surface); color:var(--text); border:1px solid var(--border); border-radius:12px;">
            <form method="post" action="<?= $baseUrl ?>/jours_feries">
                <?= \Core\Session::csrfField() ?>
                <input type="hidden" name="sous_tab" value="jours_feries">
                <div class="modal-header" style="border-bottom:1px solid var(--border);">
                    <h5 class="modal-title">Nouveau jour férié</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group" style="margin-bottom:1rem;">
                        <label class="form-label" style="font-size:0.75rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Nom du jour férié</label>
                        <input type="text" name="nom" class="form-control" placeholder="Aïd Al Fitr" required>
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                        <div class="form-group">
                            <label class="form-label" style="font-size:0.75rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Jour</label>
                            <input type="number" name="jour" class="form-control" min="1" max="31" placeholder="1" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="font-size:0.75rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Mois</label>
                            <input type="number" name="mois" class="form-control" min="1" max="12" placeholder="1" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="font-size:0.75rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Type</label>
                        <select name="type" class="form-control">
                            <option value="fixe">Fixe</option>
                            <option value="variable">Variable</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--border);">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary btn-sm">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
