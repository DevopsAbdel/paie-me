<div class="card">
    <div class="card-header">
        <h3>Liste des sociétés</h3>
        <a href="/paie-me/societes/create" class="btn btn-primary btn-sm">+ Nouvelle</a>
    </div>

    <?php if (empty($societes)): ?>
        <div class="empty-state">
            <p>Aucune société enregistrée.</p>
            <a href="/paie-me/societes/create" class="btn btn-primary">Créer une société</a>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Raison sociale</th>
                        <th>Forme</th>
                        <th>ICE</th>
                        <th>IF</th>
                        <th>RC</th>
                        <th>CNSS</th>
                        <th>Ville</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($societes as $s): ?>
                    <tr>
                        <td><a href="/paie-me/societes/<?= $s['id'] ?>" style="font-weight:600;"><?= htmlspecialchars($s['raison_sociale']) ?></a></td>
                        <td><?= $s['forme_juridique'] ?></td>
                        <td><?= htmlspecialchars($s['ice']) ?></td>
                        <td><?= htmlspecialchars($s['if_fiscal']) ?></td>
                        <td><?= htmlspecialchars($s['rc']) ?></td>
                        <td><?= htmlspecialchars($s['cnss']) ?></td>
                        <td><?= htmlspecialchars($s['ville']) ?></td>
                        <td class="table-actions">
                            <a href="/paie-me/societes/<?= $s['id'] ?>" class="btn btn-primary btn-sm">Ouvrir</a>
                            <a href="/paie-me/societes/<?= $s['id'] ?>/edit" class="btn btn-secondary btn-sm">Modifier</a>
                            <a href="/paie-me/societes/<?= $s['id'] ?>/delete" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cette société ?')">Supprimer</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
