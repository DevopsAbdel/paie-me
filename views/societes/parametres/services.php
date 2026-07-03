<div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; gap:1rem;">
        <h3 style="margin:0;">Services</h3>
        <form method="post" action="<?= $baseUrl ?>/services" style="display:flex; gap:0.5rem; align-items:center;">
            <input type="hidden" name="sous_tab" value="services">
            <input type="text" name="service_nom" class="form-control" placeholder="Nom du service" style="width:180px;" required>
            <input type="text" name="service_description" class="form-control" placeholder="Description" style="width:240px;">
            <button type="submit" class="btn btn-primary btn-sm">Ajouter</button>
        </form>
    </div>
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr><th>Nom</th><th>Description</th><th>Actif</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php if (empty($services)): ?>
                <tr><td colspan="4" style="text-align:center; color:var(--text-muted);">Aucun service</td></tr>
                <?php else: ?>
                <?php foreach ($services as $s): ?>
                <tr>
                    <td><?= htmlspecialchars($s['nom']) ?></td>
                    <td><?= htmlspecialchars($s['description'] ?? '') ?></td>
                    <td><span class="badge badge-<?= $s['actif'] ? 'success' : 'secondary' ?>"><?= $s['actif'] ? 'Oui' : 'Non' ?></span></td>
                    <td>
                        <a href="<?= $baseUrl ?>/services?delete_service=<?= $s['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce service ?')">Supprimer</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
