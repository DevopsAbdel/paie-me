<div class="card" style="margin-bottom:1rem;">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; gap:1rem;">
        <h3 style="margin:0;">Jours fériés</h3>
        <form method="post" action="<?= $baseUrl ?>/jours_feries" style="display:flex; gap:0.5rem; align-items:center; flex-wrap:wrap;">
            <?= \Core\Session::csrfField() ?>
            <input type="hidden" name="sous_tab" value="jours_feries">
            <input type="text" name="nom" class="form-control" placeholder="Nom du jour férié" style="width:180px;" required>
            <input type="number" name="jour" class="form-control" placeholder="Jour" min="1" max="31" style="width:60px;" required>
            <input type="number" name="mois" class="form-control" placeholder="Mois" min="1" max="12" style="width:60px;" required>
            <select name="type" class="form-control" style="width:110px;">
                <option value="fixe">Fixe</option>
                <option value="variable">Variable</option>
            </select>
            <button type="submit" class="btn btn-primary btn-sm">Ajouter</button>
        </form>
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
