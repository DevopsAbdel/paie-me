<div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; gap:1rem;">
        <h3 style="margin:0;">Retenues</h3>
        <form method="post" action="<?= $baseUrl ?>/retenues" style="display:flex; gap:0.5rem; align-items:center;">
            <?= \Core\Session::csrfField() ?>
            <input type="hidden" name="sous_tab" value="retenues">
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
                <tr><th>Code</th><th>Libellé</th><th>Type</th><th>Valeur défaut</th><th>Actif</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php if (empty($retenues)): ?>
                <tr><td colspan="6" style="text-align:center; color:var(--text-muted);">Aucune retenue</td></tr>
                <?php else: ?>
                <?php foreach ($retenues as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['code']) ?></td>
                    <td><?= htmlspecialchars($r['libelle']) ?></td>
                    <td><?= htmlspecialchars($r['type_montant']) ?></td>
                    <td><?= htmlspecialchars($r['valeur_defaut'] ?? '') ?></td>
                    <td><span class="badge badge-<?= $r['actif'] ? 'success' : 'secondary' ?>"><?= $r['actif'] ? 'Oui' : 'Non' ?></span></td>
                    <td>
                        <a href="<?= $baseUrl ?>/retenues?delete_retenue=<?= $r['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cette retenue ?')">Supprimer</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
