<div class="card">
    <div class="card-header">
        <h3>Journal de paie</h3>
        <div>
            <span class="badge badge-info"><?= htmlspecialchars($periode['raison_sociale']) ?></span>
            <span class="badge badge-info"><?= str_pad($periode['mois'], 2, '0', STR_PAD_LEFT) ?>/<?= $periode['annee'] ?></span>
            <a href="/paie-me/paies" class="btn btn-secondary btn-sm">← Retour</a>
        </div>
    </div>

    <?php if (empty($paies)): ?>
        <div class="empty-state">
            <p>Aucune paie dans cette période.</p>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table class="table-journal">
                <thead>
                    <tr>
                        <th rowspan="2">Matricule</th>
                        <th rowspan="2">Salarié</th>
                        <th rowspan="2">SB</th>
                        <th rowspan="2">Anc.</th>
                        <th colspan="3">Indemnités</th>
                        <th rowspan="2">HS</th>
                        <th rowspan="2">Autres gains</th>
                        <th rowspan="2">SBI</th>
                        <th rowspan="2">FP</th>
                        <th rowspan="2">CNSS</th>
                        <th rowspan="2">AMO</th>
                        <th rowspan="2">Mut.</th>
                        <th rowspan="2">IR</th>
                        <th rowspan="2">Autres ret.</th>
                        <th rowspan="2">Net</th>
                    </tr>
                    <tr>
                        <th>Transp.</th>
                        <th>Panier</th>
                        <th>Représ.</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($paies as $pa): ?>
                    <tr>
                        <td><?= htmlspecialchars($pa['matricule']) ?></td>
                        <td><?= htmlspecialchars($pa['nom_famille'] . ' ' . $pa['prenom']) ?></td>
                        <td class="text-right"><?= number_format((float) $pa['salaire_brut'], 2) ?></td>
                        <td class="text-right"><?= number_format((float) $pa['prime_anciennete'], 2) ?></td>
                        <td class="text-right"><?= number_format((float) $pa['indemnite_transport'], 2) ?></td>
                        <td class="text-right"><?= number_format((float) $pa['indemnite_panier'], 2) ?></td>
                        <td class="text-right"><?= number_format((float) $pa['indemnite_representation'], 2) ?></td>
                        <td class="text-right"><?= number_format((float) $pa['montant_heures_sup'], 2) ?></td>
                        <td class="text-right"><?= number_format((float) $pa['total_gains'], 2) ?></td>
                        <td class="text-right"><?= number_format((float) $pa['sbi'], 2) ?></td>
                        <td class="text-right"><?= number_format((float) $pa['frais_professionnels'], 2) ?></td>
                        <td class="text-right"><?= number_format((float) $pa['cnss_salariale'], 2) ?></td>
                        <td class="text-right"><?= number_format((float) $pa['amo_salariale'], 2) ?></td>
                        <td class="text-right"><?= number_format((float) $pa['mutuelle'], 2) ?></td>
                        <td class="text-right"><?= number_format((float) $pa['ir'], 2) ?></td>
                        <td class="text-right"><?= number_format((float) $pa['autres_retenues'], 2) ?></td>
                        <td class="text-right font-bold"><?= number_format((float) $pa['net_a_payer'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="totaux-row">
                        <td colspan="2"><strong>TOTAUX</strong></td>
                        <td class="text-right"><?= number_format($totaux['salaire_brut'], 2) ?></td>
                        <td class="text-right"><?= number_format($totaux['prime_anciennete'], 2) ?></td>
                        <td class="text-right"><?= number_format($totaux['indemnite_transport'], 2) ?></td>
                        <td class="text-right"><?= number_format($totaux['indemnite_panier'], 2) ?></td>
                        <td class="text-right"><?= number_format($totaux['indemnite_representation'], 2) ?></td>
                        <td class="text-right"><?= number_format($totaux['montant_heures_sup'], 2) ?></td>
                        <td class="text-right"><?= number_format($totaux['total_gains'], 2) ?></td>
                        <td class="text-right"><?= number_format($totaux['sbi'], 2) ?></td>
                        <td class="text-right"><?= number_format($totaux['frais_professionnels'], 2) ?></td>
                        <td class="text-right"><?= number_format($totaux['cnss_salariale'], 2) ?></td>
                        <td class="text-right"><?= number_format($totaux['amo_salariale'], 2) ?></td>
                        <td class="text-right"><?= number_format($totaux['mutuelle'], 2) ?></td>
                        <td class="text-right"><?= number_format($totaux['ir'], 2) ?></td>
                        <td class="text-right"><?= number_format($totaux['autres_retenues'], 2) ?></td>
                        <td class="text-right font-bold"><?= number_format($totaux['net_a_payer'], 2) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    <?php endif; ?>
</div>
