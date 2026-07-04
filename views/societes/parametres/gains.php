<div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; gap:1rem;">
        <h3 style="margin:0;">Gains</h3>
        <form method="post" action="<?= $baseUrl ?>/gains" style="display:flex; gap:0.5rem; align-items:center;">
            <?= \Core\Session::csrfField() ?>
            <input type="hidden" name="sous_tab" value="gains">
            <input type="text" name="code" class="form-control" placeholder="Code" style="width:100px;" required>
            <input type="text" name="libelle" class="form-control" placeholder="Libellé" style="width:180px;" required>
            <select name="type_montant" class="form-control" style="width:130px;">
                <option value="fixe">Fixe</option>
                <option value="proportionnel">Proportionnel</option>
            </select>
            <input type="number" name="valeur_defaut" class="form-control" placeholder="Valeur défaut" style="width:120px;" step="0.01">
            <button type="submit" class="btn btn-primary btn-sm">Ajouter</button>
        </form>
    </div>
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr><th>Code</th><th>Libellé</th><th>Type</th><th>Valeur défaut</th><th>Imposable</th><th>Actif</th><th>Source</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php if (empty($gains)): ?>
                <tr><td colspan="8" style="text-align:center; color:var(--text-muted);">Aucun gain</td></tr>
                <?php else: ?>
                <?php foreach ($gains as $g): ?>
                <tr>
                    <td><?= htmlspecialchars($g['code']) ?></td>
                    <td><?= htmlspecialchars($g['libelle']) ?></td>
                    <td><?= htmlspecialchars($g['type_montant']) ?></td>
                    <td><?= htmlspecialchars($g['valeur_defaut'] ?? '') ?></td>
                    <td><span class="badge badge-<?= $g['imposable'] ? 'warning' : 'secondary' ?>"><?= $g['imposable'] ? 'Oui' : 'Non' ?></span></td>
                    <td><span class="badge badge-<?= $g['actif'] ? 'success' : 'secondary' ?>"><?= $g['actif'] ? 'Oui' : 'Non' ?></span></td>
                    <td><span class="badge badge-info"><?= !empty($g['is_global']) ? 'Globale' : 'Société' ?></span></td>
                    <td>
                        <?php if (empty($g['is_global'])): ?>
                        <a href="<?= $baseUrl ?>/gains?delete_gain=<?= $g['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce gain ?')">Supprimer</a>
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
