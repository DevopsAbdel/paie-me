<div class="card">
    <div class="card-header">
        <h3>Salariés — <?= htmlspecialchars($societe['raison_sociale']) ?></h3>
        <a href="/paie-me/salaries/create?from_societe=<?= $societe['id'] ?>" class="btn btn-primary btn-sm">+ Nouveau</a>
    </div>
    <?php if (empty($salaries)): ?>
        <div class="empty-state"><p>Aucun salarié dans cette société.</p></div>
    <?php else: ?>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Matricule</th><th>Nom</th><th>Prénom</th><th>Poste</th>
                    <th>Salaire</th><th>CNSS</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($salaries as $s): ?>
                <tr>
                    <td><?= htmlspecialchars($s['matricule']) ?></td>
                    <td><?= htmlspecialchars($s['nom_famille']) ?></td>
                    <td><?= htmlspecialchars($s['prenom']) ?></td>
                    <td><?= htmlspecialchars($s['fonction_nom'] ?? $s['poste']) ?></td>
                    <td><?= number_format($s['salaire_base'], 2, ',', ' ') ?></td>
                    <td><?= htmlspecialchars($s['cnss']) ?></td>
                    <td>
                        <div class="table-actions">
                            <a href="/paie-me/salaries/<?= $s['id'] ?>/edit?from_societe=<?= $societe['id'] ?>" class="btn btn-secondary btn-sm">Modifier</a>
                            <a href="/paie-me/salaries/<?= $s['id'] ?>/delete" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ?')">Supprimer</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
