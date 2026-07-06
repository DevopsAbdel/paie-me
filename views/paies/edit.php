<?php
$th = $paie['salaire_base'] > 0 ? $paie['salaire_base'] / 191 : 0;
$t25 = (float) ($baremeHS['taux_normal'] ?? 25);
$t50 = (float) ($baremeHS['taux_majore'] ?? 50);
$t100 = (float) ($baremeHS['taux_jour_ferie'] ?? 100);
$hs25 = (float) ($paie['heures_sup_25'] ?? 0);
$hs50 = (float) ($paie['heures_sup_50'] ?? 0);
$hs100 = (float) ($paie['heures_sup_100'] ?? 0);
$mHS25 = round($hs25 * $th * $t25 / 100, 2);
$mHS50 = round($hs50 * $th * $t50 / 100, 2);
$mHS100 = round($hs100 * $th * $t100 / 100, 2);
?>
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
                        <th style="width:6%;">Code</th>
                        <th style="width:28%;">Libellé</th>
                        <th style="width:11%;">Base</th>
                        <th style="width:7%;">Taux</th>
                        <th style="width:12%;">Gains</th>
                        <th style="width:12%;">Retenus</th>
                        <th style="width:12%;">Part Salariale</th>
                        <th style="width:12%;">Part Patronale</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="section-header"><td colspan="8">Salaire et indemnités</td></tr>

                    <tr>
                        <td class="code">100</td>
                        <td><span class="info" title="Fiche salarié">ⓘ</span> Salaire de base</td>
                        <td class="montant"><?= number_format($paie['salaire_base'], 2, ',', ' ') ?></td>
                        <td class="taux">—</td>
                        <td class="montant"><?= number_format($paie['salaire_base'], 2, ',', ' ') ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <?php $baseHS25 = round($th * $hs25, 2); ?>
                    <tr>
                        <td class="code">201</td>
                        <td><span class="info" title="Taux horaire <?= number_format($th, 2, ',', ' ') ?> MAD/h × <?= $hs25 ?>h">ⓘ</span> HS <?= $t25 ?>%</td>
                        <td class="montant"><?= number_format($baseHS25, 2, ',', ' ') ?> <input type="number" step="0.5" min="0" name="heures_sup_25" class="form-control-inline" value="<?= $hs25 ?>" style="width:40px;">h</td>
                        <td class="taux"><?= $t25 ?>%</td>
                        <td class="montant"><?= number_format($mHS25, 2, ',', ' ') ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <?php $baseHS50 = round($th * $hs50, 2); ?>
                    <tr>
                        <td class="code">202</td>
                        <td><span class="info" title="Taux horaire <?= number_format($th, 2, ',', ' ') ?> MAD/h × <?= $hs50 ?>h">ⓘ</span> HS <?= $t50 ?>%</td>
                        <td class="montant"><?= number_format($baseHS50, 2, ',', ' ') ?> <input type="number" step="0.5" min="0" name="heures_sup_50" class="form-control-inline" value="<?= $hs50 ?>" style="width:40px;">h</td>
                        <td class="taux"><?= $t50 ?>%</td>
                        <td class="montant"><?= number_format($mHS50, 2, ',', ' ') ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <?php $baseHS100 = round($th * $hs100, 2); ?>
                    <tr>
                        <td class="code">203</td>
                        <td><span class="info" title="Taux horaire <?= number_format($th, 2, ',', ' ') ?> MAD/h × <?= $hs100 ?>h">ⓘ</span> HS <?= $t100 ?>%</td>
                        <td class="montant"><?= number_format($baseHS100, 2, ',', ' ') ?> <input type="number" step="0.5" min="0" name="heures_sup_100" class="form-control-inline" value="<?= $hs100 ?>" style="width:40px;">h</td>
                        <td class="taux"><?= $t100 ?>%</td>
                        <td class="montant"><?= number_format($mHS100, 2, ',', ' ') ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td class="code">330</td>
                        <td><span class="info" title="Exonérée IR/CNSS jusqu'à 500 MAD/mois (Arrêté 1314-25)">ⓘ</span> Indemnité transport</td>
                        <td></td>
                        <td class="taux">—</td>
                        <td class="montant"><input type="number" step="0.01" min="0" name="indemnite_transport" class="form-control-inline" value="<?= $paie['indemnite_transport'] ?>"></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td class="code">346</td>
                        <td><span class="info" title="Exonérée IR/CNSS jusqu'à 780 MAD/mois (Arrêté 1314-25)">ⓘ</span> Indemnité panier</td>
                        <td></td>
                        <td class="taux">—</td>
                        <td class="montant"><input type="number" step="0.01" min="0" name="indemnite_panier" class="form-control-inline" value="<?= $paie['indemnite_panier'] ?>"></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td class="code">331</td>
                        <td><span class="info" title="Arrêté 1314-25">ⓘ</span> Indemnité représentation</td>
                        <td></td>
                        <td class="taux">—</td>
                        <td class="montant"><input type="number" step="0.01" min="0" name="indemnite_representation" class="form-control-inline" value="<?= $paie['indemnite_representation'] ?>"></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td class="code">340</td>
                        <td><span class="info" title="Avantage en nature imposable">ⓘ</span> Avantage logement</td>
                        <td></td>
                        <td class="taux">—</td>
                        <td class="montant"><input type="number" step="0.01" min="0" name="avantage_logement" class="form-control-inline" value="<?= $paie['avantage_logement'] ?>"></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <?php foreach ($paieGains as $g): ?>
                    <tr>
                        <td class="code"><?= htmlspecialchars($g['code']) ?></td>
                        <td><?= htmlspecialchars($g['libelle']) ?></td>
                        <td></td>
                        <td class="taux">—</td>
                        <td class="montant"><?= number_format($g['montant'], 2, ',', ' ') ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <?php endforeach; ?>

                    <tr id="gains-container"></tr>

                    <tr>
                        <td colspan="8" style="padding:0.25rem 0.75rem;">
                            <button type="button" class="btn btn-sm btn-secondary" onclick="ajouterGain()" style="font-size:0.75rem;">+ Ajouter un gain</button>
                        </td>
                    </tr>

                    <tr class="total-row">
                        <td></td>
                        <td><strong>Salaire brut</strong></td>
                        <td></td>
                        <td></td>
                        <td class="montant"><strong><?= number_format($paie['salaire_brut'], 2, ',', ' ') ?></strong></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr class="section-header"><td colspan="8">Cotisations</td></tr>

                    <tr>
                        <td class="code">400</td>
                        <td><span class="info" title="min(Salaire brut, 6 000) × 4.48%">ⓘ</span> CNSS</td>
                        <td class="montant"><?= number_format(min($paie['salaire_brut'], 6000), 2, ',', ' ') ?></td>
                        <td class="taux">4,48%</td>
                        <td></td>
                        <td></td>
                        <td class="montant"><?= number_format($paie['cnss_salariale'], 2, ',', ' ') ?></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td class="code">401</td>
                        <td style="padding-left:1.5rem;">CNSS patronale</td>
                        <td class="montant"><?= number_format(min($paie['salaire_brut'], 6000), 2, ',', ' ') ?></td>
                        <td class="taux">8,98%</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="montant"><?= number_format($paie['cnss_patronale'], 2, ',', ' ') ?></td>
                    </tr>

                    <tr>
                        <td class="code">410</td>
                        <td><span class="info" title="Salaire brut × 2.26%">ⓘ</span> AMO</td>
                        <td class="montant"><?= number_format($paie['salaire_brut'], 2, ',', ' ') ?></td>
                        <td class="taux">2,26%</td>
                        <td></td>
                        <td></td>
                        <td class="montant"><?= number_format($paie['amo_salariale'], 2, ',', ' ') ?></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td class="code">411</td>
                        <td style="padding-left:1.5rem;">AMO patronale</td>
                        <td class="montant"><?= number_format($paie['salaire_brut'], 2, ',', ' ') ?></td>
                        <td class="taux">4,11%</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="montant"><?= number_format($paie['amo_patronale'], 2, ',', ' ') ?></td>
                    </tr>

                    <tr>
                        <td class="code">420</td>
                        <td><span class="info" title="Montant fiche salarié">ⓘ</span> Mutuelle</td>
                        <td></td>
                        <td class="taux">—</td>
                        <td></td>
                        <td></td>
                        <td class="montant"><?= number_format($paie['mutuelle'], 2, ',', ' ') ?></td>
                        <td></td>
                    </tr>

                    <tr class="section-header"><td colspan="8">IR et frais professionnels</td></tr>

                    <tr>
                        <td class="code">500</td>
                        <td><span class="info" title="Salaire brut – Gains exonérés">ⓘ</span> SBI</td>
                        <td class="montant"><?= number_format($paie['sbi'], 2, ',', ' ') ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <?php $sbiAnnuel = $paie['sbi'] * 12; $fpTaux = $sbiAnnuel <= 78000 ? 35 : 25; ?>
                    <tr>
                        <td class="code">501</td>
                        <td><span class="info" title="<?= $sbiAnnuel <= 78000 ? 'SBI annuel ≤ 78 000 → 35%' : 'SBI annuel > 78 000 → 25% (max 2 916,70 MAD)' ?>">ⓘ</span> Frais professionnels</td>
                        <td class="montant"><?= number_format($paie['sbi'], 2, ',', ' ') ?></td>
                        <td class="taux"><?= $fpTaux ?>%</td>
                        <td></td>
                        <td class="montant"><?= number_format($paie['frais_professionnels'], 2, ',', ' ') ?></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td class="code">502</td>
                        <td><span class="info" title="SBI – Frais pro – CNSS – AMO – Mutuelle">ⓘ</span> SNI</td>
                        <td class="montant"><?= number_format($paie['sni'], 2, ',', ' ') ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td class="code">600</td>
                        <td><span class="info" title="Barème progressif IR sur SNI">ⓘ</span> IR brut</td>
                        <td class="montant"><?= number_format($paie['sni'], 2, ',', ' ') ?></td>
                        <td class="taux">Barème</td>
                        <td></td>
                        <td class="montant"><?= number_format($paie['ir'], 2, ',', ' ') ?></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td class="code">601</td>
                        <td><span class="info" title="30 MAD × Nb enfants à charge (max 6)">ⓘ</span> Déductions familiales</td>
                        <td></td>
                        <td class="taux">—</td>
                        <td></td>
                        <td></td>
                        <td class="montant" style="color:var(--success);"><?= number_format($paie['deductions_familiales'], 2, ',', ' ') ?></td>
                        <td></td>
                    </tr>

                    <tr class="section-header"><td colspan="8">Retenues personnalisées</td></tr>

                    <?php foreach ($paieRetenues as $r): ?>
                    <tr>
                        <td class="code">900</td>
                        <td style="padding-left:1.5rem;"><?= htmlspecialchars($r['libelle']) ?></td>
                        <td></td>
                        <td class="taux">—</td>
                        <td></td>
                        <td class="montant"><?= number_format($r['montant'], 2, ',', ' ') ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr id="retenues-container"></tr>

                    <tr>
                        <td colspan="8" style="padding:0.25rem 0.75rem;">
                            <button type="button" class="btn btn-sm btn-secondary" onclick="ajouterRetenue()" style="font-size:0.75rem;">+ Ajouter une retenue</button>
                        </td>
                    </tr>

                    <tr class="section-header"><td colspan="8">Récapitulatif</td></tr>

                    <tr>
                        <td></td>
                        <td><span class="info" title="Avances + Prêts + Retenues personnalisées">ⓘ</span> Autres retenues</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="montant"><?= number_format($paie['autres_retenues'], 2, ',', ' ') ?></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td></td>
                        <td><span class="info" title="Salaire brut – Cotisations – IR – Mutuelle">ⓘ</span> <strong>Net avant retenues</strong></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td colspan="2" class="montant"><strong><?= number_format($paie['net_avant_retenues'], 2, ',', ' ') ?></strong></td>
                    </tr>

                    <tr style="border-top:2px solid var(--accent);">
                        <td></td>
                        <td><span class="info" title="Net avant retenues – Autres retenues">ⓘ</span> <strong style="color:var(--accent);">Net à payer</strong></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td colspan="2" class="montant"><strong style="color:var(--accent);"><?= number_format($paie['net_a_payer'], 2, ',', ' ') ?> MAD</strong></td>
                    </tr>
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
.edit-paie-table th { padding:0.4rem 0.5rem; text-align:left; font-size:0.7rem; text-transform:uppercase; letter-spacing:0.04em; color:var(--text-muted); border-bottom:1px solid var(--border); }
.edit-paie-table td { padding:0.3rem 0.5rem; font-size:0.8rem; border-bottom:1px solid var(--border-subtle); }
.edit-paie-table .montant { text-align:right; white-space:nowrap; }
.edit-paie-table .taux { text-align:center; font-size:0.75rem; color:var(--text-muted); }
.edit-paie-table .code { text-align:center; font-size:0.7rem; color:var(--text-muted); font-family:monospace; }
.edit-paie-table .section-header td { padding:0.4rem 0.5rem 0.2rem; font-size:0.65rem; font-weight:600; text-transform:uppercase; letter-spacing:0.06em; color:var(--accent); border-bottom:none; }
.edit-paie-table .total-row td { padding:0.4rem 0.5rem; border-top:1px solid var(--border); font-weight:600; }
.form-control-inline { width:60px; padding:0.2rem 0.3rem; font-size:0.75rem; background:var(--surface); border:1px solid var(--border); border-radius:3px; color:var(--text); text-align:right; }
.form-control-inline:focus { border-color:var(--accent); outline:none; }
.info { cursor:help; font-size:0.7rem; color:var(--text-muted); margin-right:0.15rem; position:relative; }
.info:hover { color:var(--accent); }
</style>

<script>
let gainIdx = 0;
function ajouterGain() {
    gainIdx++;
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td class="code">CUST</td>
        <td>
            <input type="hidden" name="gain_custom_actif[${gainIdx}]" value="0">
            <input type="checkbox" name="gain_custom_actif[${gainIdx}]" value="1" checked style="vertical-align:middle;margin-right:0.3rem;">
            <input type="text" name="gain_custom_libelle[${gainIdx}]" class="form-control-inline" style="width:140px;text-align:left;" placeholder="Libellé">
        </td>
        <td></td>
        <td class="taux">—</td>
        <td class="montant">
            <input type="number" step="0.01" min="0" name="gain_custom_montant[${gainIdx}]" class="form-control-inline" style="width:70px;" value="0">
        </td>
        <td></td>
        <td></td>
        <td style="text-align:center;">
            <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()" style="padding:0.1rem 0.3rem;font-size:0.65rem;">✕</button>
        </td>
    `;
    document.getElementById('gains-container').before(tr);
}

let retenueIdx = 0;
function ajouterRetenue() {
    retenueIdx++;
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td class="code">900</td>
        <td>
            <input type="text" name="retenue_libelle[${retenueIdx}]" class="form-control-inline" style="width:140px;text-align:left;" placeholder="Libellé">
        </td>
        <td></td>
        <td class="taux">—</td>
        <td></td>
        <td class="montant">
            <input type="number" step="0.01" min="0" name="retenue_montant[${retenueIdx}]" class="form-control-inline" style="width:70px;" value="0">
        </td>
        <td></td>
        <td style="text-align:center;">
            <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()" style="padding:0.1rem 0.3rem;font-size:0.65rem;">✕</button>
        </td>
    `;
    document.getElementById('retenues-container').before(tr);
}
</script>
