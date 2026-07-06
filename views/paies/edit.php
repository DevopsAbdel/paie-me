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

        <div class="table-wrapper">
            <table class="edit-paie-table">
                <thead>
                    <tr>
                        <th style="width:60%;">Rubrique</th>
                        <th style="width:30%;">Montant (MAD)</th>
                        <th style="width:10%;"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="section-header"><td colspan="3">Salaire et indemnités</td></tr>

                    <tr>
                        <td>Salaire de base</td>
                        <td class="montant"><?= number_format($paie['salaire_base'], 2, ',', ' ') ?></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td>Heures supplémentaires</td>
                        <td class="montant">
                            <input type="number" step="0.5" min="0" name="heures_supplementaires" class="form-control-inline"
                                   value="<?= $paie['heures_supplementaires'] ?>" style="width:80px;">
                            <small style="color:var(--text-muted);">× <?= number_format($paie['salaire_base'] / (30 * 8 * 52/12), 2, ',', ' ') ?> = <?= number_format($paie['montant_heures_sup'], 2, ',', ' ') ?></small>
                        </td>
                        <td></td>
                    </tr>

                    <tr>
                        <td>Indemnité de transport</td>
                        <td class="montant">
                            <input type="number" step="0.01" min="0" name="indemnite_transport" class="form-control-inline"
                                   value="<?= $paie['indemnite_transport'] ?>">
                        </td>
                        <td></td>
                    </tr>

                    <tr>
                        <td>Indemnité de panier</td>
                        <td class="montant">
                            <input type="number" step="0.01" min="0" name="indemnite_panier" class="form-control-inline"
                                   value="<?= $paie['indemnite_panier'] ?>">
                        </td>
                        <td></td>
                    </tr>

                    <tr>
                        <td>Indemnité de représentation</td>
                        <td class="montant">
                            <input type="number" step="0.01" min="0" name="indemnite_representation" class="form-control-inline"
                                   value="<?= $paie['indemnite_representation'] ?>">
                        </td>
                        <td></td>
                    </tr>

                    <tr>
                        <td>Avantage logement</td>
                        <td class="montant">
                            <input type="number" step="0.01" min="0" name="avantage_logement" class="form-control-inline"
                                   value="<?= $paie['avantage_logement'] ?>">
                        </td>
                        <td></td>
                    </tr>

                    <tr id="gains-container"></tr>

                    <tr>
                        <td colspan="3" style="padding:0.25rem 0.75rem;">
                            <button type="button" class="btn btn-sm btn-secondary" onclick="ajouterGain()" style="font-size:0.75rem;">+ Ajouter un gain</button>
                        </td>
                    </tr>

                    <tr class="section-header"><td colspan="3">Cotisations</td></tr>

                    <tr><td>CNSS salariale</td><td class="montant"><?= number_format($paie['cnss_salariale'], 2, ',', ' ') ?></td><td></td></tr>
                    <tr><td>AMO salariale</td><td class="montant"><?= number_format($paie['amo_salariale'], 2, ',', ' ') ?></td><td></td></tr>
                    <tr><td>Mutuelle</td><td class="montant"><?= number_format($paie['mutuelle'], 2, ',', ' ') ?></td><td></td></tr>
                    <tr><td>CNSS patronale</td><td class="montant"><?= number_format($paie['cnss_patronale'], 2, ',', ' ') ?></td><td></td></tr>
                    <tr><td>AMO patronale</td><td class="montant"><?= number_format($paie['amo_patronale'], 2, ',', ' ') ?></td><td></td></tr>

                    <tr class="section-header"><td colspan="3">IR et frais professionnels</td></tr>

                    <tr><td>SBI</td><td class="montant"><?= number_format($paie['sbi'], 2, ',', ' ') ?></td><td></td></tr>
                    <tr><td>Frais professionnels</td><td class="montant"><?= number_format($paie['frais_professionnels'], 2, ',', ' ') ?></td><td></td></tr>
                    <tr><td>SNI</td><td class="montant"><?= number_format($paie['sni'], 2, ',', ' ') ?></td><td></td></tr>
                    <tr><td>IR brut</td><td class="montant"><?= number_format($paie['ir'], 2, ',', ' ') ?></td><td></td></tr>
                    <tr><td>Déductions familiales</td><td class="montant"><?= number_format($paie['deductions_familiales'], 2, ',', ' ') ?></td><td></td></tr>

                    <tr class="section-header"><td colspan="3">Retenues personnalisées</td></tr>

                    <?php foreach ($paieRetenues as $r): ?>
                    <tr>
                        <td style="padding-left:1.5rem;"><?= htmlspecialchars($r['libelle']) ?></td>
                        <td class="montant"><?= number_format($r['montant'], 2, ',', ' ') ?></td>
                        <td></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr id="retenues-container"></tr>

                    <tr>
                        <td colspan="3" style="padding:0.25rem 0.75rem;">
                            <button type="button" class="btn btn-sm btn-secondary" onclick="ajouterRetenue()" style="font-size:0.75rem;">+ Ajouter une retenue</button>
                        </td>
                    </tr>

                    <tr class="section-header"><td colspan="3">Récapitulatif</td></tr>

                    <tr><td>Salaire brut</td><td class="montant"><?= number_format($paie['salaire_brut'], 2, ',', ' ') ?></td><td></td></tr>
                    <tr><td>Autres retenues</td><td class="montant"><?= number_format($paie['autres_retenues'], 2, ',', ' ') ?></td><td></td></tr>
                    <tr><td><strong>Net avant retenues</strong></td><td class="montant"><strong><?= number_format($paie['net_avant_retenues'], 2, ',', ' ') ?></strong></td><td></td></tr>
                    <tr style="border-top:2px solid var(--accent);"><td><strong style="color:var(--accent);">Net à payer</strong></td><td class="montant"><strong style="color:var(--accent);"><?= number_format($paie['net_a_payer'], 2, ',', ' ') ?></strong></td><td></td></tr>
                </tbody>
            </table>
        </div>

        <div style="padding:0.75rem 1rem 1rem; display:flex; gap:0.5rem; border-top:1px solid var(--border);">
            <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
            <a href="/paie-me/paies/<?= $paie['periode_id'] ?>/calculate" class="btn btn-secondary btn-sm" onclick="return confirm('Recalculer toutes les paies de cette période ? Les modifications manuelles seront perdues.')">Recalculer la période</a>
            <a href="/paie-me/paies/<?= $paie['periode_id'] ?>/lignes" class="btn btn-secondary btn-sm">Retour</a>
        </div>
    </form>
</div>

<style>
.edit-paie-table { width:100%; border-collapse:collapse; }
.edit-paie-table th { padding:0.5rem 0.75rem; text-align:left; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--text-muted); border-bottom:1px solid var(--border); }
.edit-paie-table td { padding:0.35rem 0.75rem; font-size:0.875rem; border-bottom:1px solid var(--border-subtle); }
.edit-paie-table .montant { text-align:right; white-space:nowrap; }
.edit-paie-table .section-header td { padding:0.5rem 0.75rem 0.25rem; font-size:0.7rem; font-weight:600; text-transform:uppercase; letter-spacing:0.06em; color:var(--accent); border-bottom:none; }
.form-control-inline { width:100px; padding:0.25rem 0.4rem; font-size:0.8rem; background:var(--surface); border:1px solid var(--border); border-radius:4px; color:var(--text); text-align:right; }
.form-control-inline:focus { border-color:var(--accent); outline:none; }
</style>

<script>
let gainIdx = 0;
function ajouterGain() {
    gainIdx++;
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td style="padding-left:1.5rem;">
            <input type="hidden" name="gain_custom_actif[${gainIdx}]" value="0">
            <input type="checkbox" name="gain_custom_actif[${gainIdx}]" value="1" checked style="vertical-align:middle;margin-right:0.35rem;">
            <input type="text" name="gain_custom_libelle[${gainIdx}]" class="form-control-inline" style="width:200px;text-align:left;" placeholder="Libellé du gain">
        </td>
        <td class="montant">
            <input type="number" step="0.01" min="0" name="gain_custom_montant[${gainIdx}]" class="form-control-inline" value="0">
        </td>
        <td style="text-align:center;">
            <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()" style="padding:0.15rem 0.4rem;font-size:0.7rem;">✕</button>
        </td>
    `;
    document.getElementById('gains-container').before(tr);
}

let retenueIdx = 0;
function ajouterRetenue() {
    retenueIdx++;
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td style="padding-left:1.5rem;">
            <input type="text" name="retenue_libelle[${retenueIdx}]" class="form-control-inline" style="width:200px;text-align:left;" placeholder="Libellé de la retenue">
        </td>
        <td class="montant">
            <input type="number" step="0.01" min="0" name="retenue_montant[${retenueIdx}]" class="form-control-inline" value="0">
        </td>
        <td style="text-align:center;">
            <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()" style="padding:0.15rem 0.4rem;font-size:0.7rem;">✕</button>
        </td>
    `;
    document.getElementById('retenues-container').before(tr);
}
</script>
