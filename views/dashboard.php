<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Sociétés</div>
        <div class="stat-value"><?= $nbSocietes ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Salariés</div>
        <div class="stat-value"><?= $nbSalaries ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Périodes traitées</div>
        <div class="stat-value"><?= $nbPeriodes ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total net payé</div>
        <div class="stat-value"><?= number_format($totalNet, 2, ',', ' ') ?> DH</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Dernières paies</h3>
    </div>

    <?php if (empty($latestPaies)): ?>
        <div class="empty-state">
            <p>Aucune paie enregistrée pour le moment.</p>
            <a href="/paie-me/paies/create" class="btn btn-primary">Créer une paie</a>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Salarié</th>
                        <th>Période</th>
                        <th>Salaire brut</th>
                        <th>CNSS</th>
                        <th>AMO</th>
                        <th>IR</th>
                        <th>Net à payer</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($latestPaies as $paie): ?>
                    <tr>
                        <td><?= htmlspecialchars($paie['nom_famille'] . ' ' . $paie['prenom']) ?></td>
                        <td><?= str_pad($paie['mois'], 2, '0', STR_PAD_LEFT) . '/' . $paie['annee'] ?></td>
                        <td><?= number_format($paie['salaire_brut'], 2, ',', ' ') ?></td>
                        <td><?= number_format($paie['cnss_salariale'], 2, ',', ' ') ?></td>
                        <td><?= number_format($paie['amo_salariale'], 2, ',', ' ') ?></td>
                        <td><?= number_format($paie['ir'], 2, ',', ' ') ?></td>
                        <td><strong><?= number_format($paie['net_a_payer'], 2, ',', ' ') ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
