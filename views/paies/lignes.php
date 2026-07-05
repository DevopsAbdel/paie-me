<div class="card">
    <div class="card-header">
        <h3>Paies — <?= htmlspecialchars($periode['raison_sociale']) ?> — <?= str_pad($periode['mois'], 2, '0', STR_PAD_LEFT) . '/' . $periode['annee'] ?></h3>
        <div style="display:flex; gap:0.5rem;">
            <a href="/paie-me/paies/<?= $periode['id'] ?>/calculate" class="btn btn-secondary btn-sm" onclick="return confirm('Recalculer toutes les paies ?')">Recalculer</a>
            <a href="/paie-me/paies" class="btn btn-secondary btn-sm">Retour</a>
        </div>
    </div>

    <?php if (empty($paies)): ?>
        <div class="empty-state">
            <p>Aucune paie pour cette période.</p>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Matricule</th>
                        <th>Salarié</th>
                        <th>Salaire brut</th>
                        <th>SBI</th>
                        <th>Ancienneté</th>
                        <th>HS (h)</th>
                        <th>M. HS</th>
                        <th>Frais pro</th>
                        <th>SNI</th>
                        <th>CNSS</th>
                        <th>AMO</th>
                        <th>IR</th>
                        <th>Net</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($paies as $pa): ?>
                    <tr>
                        <td><?= htmlspecialchars($pa['matricule']) ?></td>
                        <td><?= htmlspecialchars($pa['nom_famille'] . ' ' . $pa['prenom']) ?></td>
                        <td><?= number_format($pa['salaire_brut'], 2, ',', ' ') ?></td>
                        <td><?= number_format($pa['sbi'], 2, ',', ' ') ?></td>
                        <td><?= number_format($pa['prime_anciennete'], 2, ',', ' ') ?></td>
                        <td><?= (float)$pa['heures_supplementaires'] ?: '-' ?></td>
                        <td><?= number_format($pa['montant_heures_sup'], 2, ',', ' ') ?></td>
                        <td><?= number_format($pa['frais_professionnels'], 2, ',', ' ') ?></td>
                        <td><?= number_format($pa['sni'], 2, ',', ' ') ?></td>
                        <td><?= number_format($pa['cnss_salariale'], 2, ',', ' ') ?></td>
                        <td><?= number_format($pa['amo_salariale'], 2, ',', ' ') ?></td>
                        <td><?= number_format($pa['ir'], 2, ',', ' ') ?></td>
                        <td><strong><?= number_format($pa['net_a_payer'], 2, ',', ' ') ?></strong></td>
                        <td>
                            <div class="table-actions">
                                <a href="/paie-me/paies/paie/<?= $pa['id'] ?>/edit" class="btn btn-secondary btn-sm">HS</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
