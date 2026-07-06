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
                        <?php if (empty($ctx)): ?><th>Société</th><?php endif; ?>
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
                        <td><?= htmlspecialchars($s['fonction_nom'] ?? $s['poste']) ?></td>
                        <?php if (empty($ctx)): ?><td><?= htmlspecialchars($s['raison_sociale']) ?></td><?php endif; ?>
                        <td><?= number_format($s['salaire_base'], 2, ',', ' ') ?></td>
                        <td>
                            <div class="table-actions">
                                <a href="/paie-me/salaries/<?= $s['id'] ?>/edit" class="btn btn-secondary btn-sm">Modifier</a>
                                <a href="/paie-me/salaries/<?= $s['id'] ?>/stc" class="btn btn-secondary btn-sm">STC</a>
                                <form method="POST" action="/paie-me/salaries/<?= $s['id'] ?>/delete" class="inline-form">
                                    <?= \Core\Session::csrfField() ?>
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce salarié ?')">Supprimer</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
