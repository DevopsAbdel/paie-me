<div class="card">
    <div class="card-header">
        <h3>Bulletins de paie</h3>
    </div>

    <?php if (empty($bulletins)): ?>
        <div class="empty-state">
            <p>Aucun bulletin généré. Créez d'abord une période de paie.</p>
            <a href="/paie-me/paies/create" class="btn btn-primary">Créer une paie</a>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>N° Bulletin</th>
                        <th>Salarié</th>
                        <th>Société</th>
                        <th>Période</th>
                        <th>Salaire brut</th>
                        <th>Net à payer</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bulletins as $b): ?>
                    <tr>
                        <td><?= htmlspecialchars($b['numero']) ?></td>
                        <td><?= htmlspecialchars($b['nom_famille'] . ' ' . $b['prenom']) ?></td>
                        <td><?= htmlspecialchars($b['raison_sociale']) ?></td>
                        <td><?= str_pad($b['mois'], 2, '0', STR_PAD_LEFT) . '/' . $b['annee'] ?></td>
                        <td><?= number_format($b['salaire_brut'], 2, ',', ' ') ?></td>
                        <td><strong><?= number_format($b['net_a_payer'], 2, ',', ' ') ?></strong></td>
                        <td><?= $b['date_emission'] ?></td>
                        <td>
                            <div class="table-actions">
                                <a href="/paie-me/bulletins/<?= $b['id'] ?>" class="btn btn-secondary btn-sm">Voir</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
