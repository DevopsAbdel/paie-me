<div class="card">
    <div class="card-header">
        <h3>Liste des salariés</h3>
        <a href="/paie-me/salaries/create" class="btn btn-primary btn-sm">+ Nouveau</a>
    </div>

    <?php if (empty($salaries)): ?>
        <div class="empty-state">
            <p>Aucun salarié enregistré.</p>
            <a href="/paie-me/salaries/create" class="btn btn-primary">Ajouter un salarié</a>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Matricule</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>CIN</th>
                        <th>CNSS</th>
                        <th>Poste</th>
                        <th>Société</th>
                        <th>Salaire base</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($salaries as $s): ?>
                    <tr>
                        <td><?= htmlspecialchars($s['matricule']) ?></td>
                        <td><?= htmlspecialchars($s['nom_famille']) ?></td>
                        <td><?= htmlspecialchars($s['prenom']) ?></td>
                        <td><?= htmlspecialchars($s['cin']) ?></td>
                        <td><?= htmlspecialchars($s['cnss']) ?></td>
                        <td><?= htmlspecialchars($s['poste']) ?></td>
                        <td><?= htmlspecialchars($s['raison_sociale']) ?></td>
                        <td><?= number_format($s['salaire_base'], 2, ',', ' ') ?></td>
                        <td class="table-actions">
                            <a href="/paie-me/salaries/<?= $s['id'] ?>/edit" class="btn btn-secondary btn-sm">Modifier</a>
                            <a href="/paie-me/salaries/<?= $s['id'] ?>/delete" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce salarié ?')">Supprimer</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
