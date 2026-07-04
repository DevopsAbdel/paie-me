<div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; gap:1rem;">
        <h3 style="margin:0;">Attestations</h3>
        <form method="post" action="<?= $baseUrl ?>/attestations" style="display:flex; gap:0.5rem; align-items:center;">
            <?= \Core\Session::csrfField() ?>
            <input type="hidden" name="sous_tab" value="attestations">
            <input type="text" name="titre" class="form-control" placeholder="Titre" style="width:180px;" required>
            <textarea name="contenu" class="form-control" placeholder="Contenu du modèle" style="width:240px; height:32px; resize:vertical;" required></textarea>
            <button type="submit" class="btn btn-primary btn-sm">Ajouter</button>
        </form>
    </div>
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr><th>Titre</th><th>Actif</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php if (empty($attestations)): ?>
                <tr><td colspan="3" style="text-align:center; color:var(--text-muted);">Aucune attestation</td></tr>
                <?php else: ?>
                <?php foreach ($attestations as $a): ?>
                <tr>
                    <td><?= htmlspecialchars($a['titre']) ?></td>
                    <td><span class="badge badge-<?= $a['actif'] ? 'success' : 'secondary' ?>"><?= $a['actif'] ? 'Oui' : 'Non' ?></span></td>
                    <td>
                        <a href="<?= $baseUrl ?>/attestations?delete_attestation=<?= $a['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cette attestation ?')">Supprimer</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
