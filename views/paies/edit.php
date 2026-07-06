<div class="card">
    <div class="card-header">
        <h3>Modifier la paie</h3>
        <div class="table-actions">
            <a href="/paie-me/paies/<?= $paie['periode_id'] ?>/lignes" class="btn btn-secondary btn-sm">Retour aux paies</a>
        </div>
    </div>

    <div style="padding:1rem; border-bottom:1px solid var(--border); display:grid; grid-template-columns:1fr 1fr; gap:0.5rem;">
        <div><strong>Salarié :</strong> <?= htmlspecialchars($paie['nom_famille'] . ' ' . $paie['prenom']) ?></div>
        <div><strong>Société :</strong> <?= htmlspecialchars($paie['raison_sociale']) ?></div>
        <div><strong>Période :</strong> <?= str_pad($paie['mois'] ?? '', 2, '0', STR_PAD_LEFT) ?>/<?= $paie['annee'] ?? '' ?></div>
        <div><strong>Salaire de base :</strong> <?= number_format($paie['salaire_base'], 2, ',', ' ') ?> MAD</div>
    </div>

    <form method="POST">
        <?= \Core\Session::csrfField() ?>

        <div class="card" style="margin:1rem;">
            <div class="card-header"><h4>Heures supplémentaires</h4></div>
            <div style="padding:1rem;">
                <div class="form-group">
                    <label>Nombre d'heures supplémentaires</label>
                    <input type="number" step="0.5" min="0" name="heures_supplementaires" class="form-control"
                           value="<?= $paie['heures_supplementaires'] ?>" placeholder="0">
                    <small style="color:var(--text-muted);">Le montant est calculé selon le barème Heures sup configuré (taux normal / majoré / seuil). Recalculez la période pour appliquer.</small>
                </div>
                <div style="margin-top:0.75rem;">
                    <strong>Montant calculé :</strong> <?= number_format($paie['montant_heures_sup'], 2, ',', ' ') ?> MAD
                </div>
                <div style="margin-top:0.75rem;">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </div>
        </div>
    </form>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; padding:0 1rem 1rem;">
        <div class="card">
            <div class="card-header"><h4>Indemnités et avantages</h4></div>
            <div style="padding:0.75rem;">
                <table style="width:100%; font-size:0.875rem;">
                    <tr><td>Transport</td><td style="text-align:right;"><?= number_format($paie['indemnite_transport'], 2, ',', ' ') ?></td></tr>
                    <tr><td>Panier</td><td style="text-align:right;"><?= number_format($paie['indemnite_panier'], 2, ',', ' ') ?></td></tr>
                    <tr><td>Représentation</td><td style="text-align:right;"><?= number_format($paie['indemnite_representation'], 2, ',', ' ') ?></td></tr>
                    <tr><td>Logement</td><td style="text-align:right;"><?= number_format($paie['avantage_logement'], 2, ',', ' ') ?></td></tr>
                    <tr><td>Prime ancienneté</td><td style="text-align:right;"><?= number_format($paie['prime_anciennete'], 2, ',', ' ') ?></td></tr>
                    <tr><td>Total gains</td><td style="text-align:right;"><?= number_format($paie['total_gains'], 2, ',', ' ') ?></td></tr>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h4>Cotisations</h4></div>
            <div style="padding:0.75rem;">
                <table style="width:100%; font-size:0.875rem;">
                    <tr><td>CNSS salariale</td><td style="text-align:right;"><?= number_format($paie['cnss_salariale'], 2, ',', ' ') ?></td></tr>
                    <tr><td>AMO salariale</td><td style="text-align:right;"><?= number_format($paie['amo_salariale'], 2, ',', ' ') ?></td></tr>
                    <tr><td>Mutuelle</td><td style="text-align:right;"><?= number_format($paie['mutuelle'], 2, ',', ' ') ?></td></tr>
                    <tr><td>CNSS patronale</td><td style="text-align:right;"><?= number_format($paie['cnss_patronale'], 2, ',', ' ') ?></td></tr>
                    <tr><td>AMO patronale</td><td style="text-align:right;"><?= number_format($paie['amo_patronale'], 2, ',', ' ') ?></td></tr>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h4>IR et frais professionnels</h4></div>
            <div style="padding:0.75rem;">
                <table style="width:100%; font-size:0.875rem;">
                    <tr><td>SBI</td><td style="text-align:right;"><?= number_format($paie['sbi'], 2, ',', ' ') ?></td></tr>
                    <tr><td>Frais professionnels</td><td style="text-align:right;"><?= number_format($paie['frais_professionnels'], 2, ',', ' ') ?></td></tr>
                    <tr><td>SNI</td><td style="text-align:right;"><?= number_format($paie['sni'], 2, ',', ' ') ?></td></tr>
                    <tr><td>IR brut</td><td style="text-align:right;"><?= number_format($paie['ir'], 2, ',', ' ') ?></td></tr>
                    <tr><td>Déductions familiales</td><td style="text-align:right;"><?= number_format($paie['deductions_familiales'], 2, ',', ' ') ?></td></tr>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h4>Récapitulatif</h4></div>
            <div style="padding:0.75rem;">
                <table style="width:100%; font-size:0.875rem;">
                    <tr><td>Salaire brut</td><td style="text-align:right;"><?= number_format($paie['salaire_brut'], 2, ',', ' ') ?></td></tr>
                    <tr><td>Autres retenues</td><td style="text-align:right;"><?= number_format($paie['autres_retenues'], 2, ',', ' ') ?></td></tr>
                    <tr><td style="padding-top:0.5rem;"><strong>Net avant retenues</strong></td><td style="padding-top:0.5rem; text-align:right;"><?= number_format($paie['net_avant_retenues'], 2, ',', ' ') ?></td></tr>
                    <tr><td style="border-top:2px solid var(--accent);"><strong>Net à payer</strong></td><td style="border-top:2px solid var(--accent); text-align:right;"><strong style="color:var(--accent);"><?= number_format($paie['net_a_payer'], 2, ',', ' ') ?> MAD</strong></td></tr>
                </table>
            </div>
        </div>
    </div>

    <div style="padding:0.75rem 1rem 1rem; display:flex; gap:0.5rem; border-top:1px solid var(--border);">
        <a href="/paie-me/paies/<?= $paie['periode_id'] ?>/calculate" class="btn btn-primary btn-sm" onclick="return confirm('Recalculer toutes les paies de cette période ?')">Recalculer la période</a>
        <a href="/paie-me/bulletins?periode_id=<?= $paie['periode_id'] ?>" class="btn btn-secondary btn-sm">Voir bulletins</a>
        <a href="/paie-me/paies/<?= $paie['periode_id'] ?>/lignes" class="btn btn-secondary btn-sm">Retour</a>
    </div>
</div>
