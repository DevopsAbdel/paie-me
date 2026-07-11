<div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; gap:1rem;">
        <h3 style="margin:0;">Retenues</h3>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#ajoutRetenue">+ Ajouter</button>
    </div>
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr><th>Code</th><th>Libellé</th><th>Type</th><th>Valeur défaut</th><th>Actif</th><th>Source</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php if (empty($retenues)): ?>
                <tr><td colspan="7" style="text-align:center; color:var(--text-muted);">Aucune retenue</td></tr>
                <?php else: ?>
                <?php foreach ($retenues as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['code']) ?></td>
                    <td><?= htmlspecialchars($r['libelle']) ?></td>
                    <td><?= htmlspecialchars($r['type_montant']) ?></td>
                    <td><?= htmlspecialchars($r['valeur_defaut'] ?? '') ?></td>
                    <td><span class="badge badge-<?= $r['actif'] ? 'success' : 'secondary' ?>"><?= $r['actif'] ? 'Oui' : 'Non' ?></span></td>
                    <td><span class="badge badge-info"><?= !empty($r['is_global']) ? 'Globale' : 'Société' ?></span></td>
                    <td>
                        <?php if (empty($r['is_global'])): ?>
                        <a href="<?= $baseUrl ?>/retenues?delete_retenue=<?= $r['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cette retenue ?')">Supprimer</a>
                        <?php else: ?>
                        <span style="color:var(--text-muted); font-size:0.85rem;">Par défaut</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Ajouter retenue -->
<div class="modal fade" id="ajoutRetenue" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background:var(--bg-surface); color:var(--text); border:1px solid var(--border); border-radius:12px;">
            <form method="post" action="<?= $baseUrl ?>/retenues">
                <?= \Core\Session::csrfField() ?>
                <input type="hidden" name="sous_tab" value="retenues">
                <div class="modal-header" style="border-bottom:1px solid var(--border);">
                    <h5 class="modal-title">Nouvelle retenue</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                        <div class="form-group">
                            <label class="form-label" style="font-size:0.75rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Code</label>
                            <input type="text" name="code" class="form-control" placeholder="RET_AVANCE" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="font-size:0.75rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Libellé</label>
                            <input type="text" name="libelle" class="form-control" placeholder="Avance sur salaire" required>
                        </div>
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                        <div class="form-group">
                            <label class="form-label" style="font-size:0.75rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Type de montant</label>
                            <select name="type_montant" class="form-control">
                                <option value="fixe">Fixe</option>
                                <option value="proportionnel">Proportionnel</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="font-size:0.75rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Valeur défaut</label>
                            <input type="number" name="valeur_defaut" class="form-control" step="0.01" placeholder="0.00">
                        </div>
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
