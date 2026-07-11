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
                        <a href="<?= $baseUrl ?>/jours_feries?delete_jf=<?= $jf['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce jour férié ?')">Supprimer</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Ajouter jour férié -->
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
