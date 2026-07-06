<div class="card">
    <div class="card-header">
        <h3>Bulletins de paie</h3>
    </div>
    <?php if (empty($bulletins)): ?>
        <div class="empty-state"><p>Aucun bulletin généré pour cette société.</p></div>
    <?php else: ?>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>N° Bulletin</th><th>Période</th><th>Salarié</th>
                    <th>Salaire brut</th><th>Net à payer</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bulletins as $b): ?>
                <tr>
                    <td><?= htmlspecialchars($b['numero']) ?></td>
                    <td><?= str_pad($b['mois'], 2, '0', STR_PAD_LEFT) ?>/<?= $b['annee'] ?></td>
                    <td><?= htmlspecialchars($b['nom_famille']) ?> <?= htmlspecialchars($b['prenom']) ?></td>
                    <td><?= number_format($b['salaire_brut'], 2, ',', ' ') ?></td>
                    <td><strong style="color:var(--accent);"><?= number_format($b['net_a_payer'], 2, ',', ' ') ?></strong></td>
                    <td>
                        <div class="table-actions">
                            <a href="/paie-me/bulletins/<?= $b['id'] ?>" class="btn btn-secondary btn-sm">Voir</a>
                            <a href="/paie-me/bulletins/<?= $b['id'] ?>/pdf" class="btn btn-primary btn-sm" target="_blank">PDF</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
