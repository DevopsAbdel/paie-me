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
                        <?php if (empty($ctx)): ?><th>Société</th><?php endif; ?>
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
                        <?php if (empty($ctx)): ?><td><?= htmlspecialchars($b['raison_sociale']) ?></td><?php endif; ?>
                        <td><?= str_pad($b['mois'], 2, '0', STR_PAD_LEFT) . '/' . $b['annee'] ?></td>
                        <td><?= number_format($b['salaire_brut'], 2, ',', ' ') ?></td>
                        <td><strong><?= number_format($b['net_a_payer'], 2, ',', ' ') ?></strong></td>
                        <td><?= $b['date_emission'] ?></td>
                        <td>
                            <div class="table-actions">
                                <a href="/paie-me/bulletins/<?= $b['id'] ?>" class="btn-icon btn-view" title="Voir">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
