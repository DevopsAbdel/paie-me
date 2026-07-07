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

function getPlafondDgi(string $code, array $plafonds, float $salaireBase): ?float
{
    if (isset($plafonds[$code]) && $plafonds[$code]['plafond_dgi_actif']) {
        return (float) $plafonds[$code]['plafond_dgi_valeur'];
    }
    if ($code === '331') return round($salaireBase * 0.10, 2);
    if ($code === '346') return 780.0;
    return null;
}

function overLimit(?float $valeur, ?float $plafond): bool
{
    return $plafond !== null && $valeur !== null && $valeur > $plafond;
}
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
                        <th style="width:5%;">Code</th>
                        <th style="width:27%;">Libellé</th>
                        <th style="width:15%;">Base</th>
                        <th style="width:8%;">Taux</th>
                        <th style="width:20%;">Salariale</th>
                        <th style="width:20%;">Patronale</th>
                        <th style="width:5%;"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="section-header"><td colspan="7">Salaire et indemnités</td></tr>

                    <tr>
                        <td class="code">100</td>
                        <td>Salaire de base</td>
                        <td class="montant"><?= number_format($paie['salaire_base'], 2, ',', ' ') ?></td>
                        <td class="taux">—</td>
                        <td class="montant"><?= number_format($paie['salaire_base'], 2, ',', ' ') ?></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <?php $baseHS25 = round($th * $hs25, 2); ?>
                    <tr>
                        <td class="code">201</td>
                        <td>
                            <span class="info" title="Taux horaire <?= number_format($th, 2, ',', ' ') ?> MAD/h">ⓘ</span>
                            HS <?= $t25 ?>%
                            <input type="number" step="0.5" min="0" name="heures_sup_25" class="form-control-inline" value="<?= $hs25 ?>" style="width:40px;">h
                        </td>
                        <td class="montant"><?= number_format($baseHS25, 2, ',', ' ') ?></td>
                        <td class="taux"><?= $t25 ?>%</td>
                        <td class="montant"><?= number_format($mHS25, 2, ',', ' ') ?></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <?php $baseHS50 = round($th * $hs50, 2); ?>
                    <tr>
                        <td class="code">202</td>
                        <td>
                            <span class="info" title="Taux horaire <?= number_format($th, 2, ',', ' ') ?> MAD/h">ⓘ</span>
                            HS <?= $t50 ?>%
                            <input type="number" step="0.5" min="0" name="heures_sup_50" class="form-control-inline" value="<?= $hs50 ?>" style="width:40px;">h
                        </td>
                        <td class="montant"><?= number_format($baseHS50, 2, ',', ' ') ?></td>
                        <td class="taux"><?= $t50 ?>%</td>
                        <td class="montant"><?= number_format($mHS50, 2, ',', ' ') ?></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <?php $baseHS100 = round($th * $hs100, 2); ?>
                    <tr>
                        <td class="code">203</td>
                        <td>
                            <span class="info" title="Taux horaire <?= number_format($th, 2, ',', ' ') ?> MAD/h">ⓘ</span>
                            HS <?= $t100 ?>%
                            <input type="number" step="0.5" min="0" name="heures_sup_100" class="form-control-inline" value="<?= $hs100 ?>" style="width:40px;">h
                        </td>
                        <td class="montant"><?= number_format($baseHS100, 2, ',', ' ') ?></td>
                        <td class="taux"><?= $t100 ?>%</td>
                        <td class="montant"><?= number_format($mHS100, 2, ',', ' ') ?></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <?php
                    $indemnFields = [
                        'indemnite_transport' => ['330', 'Indemnité transport', 'Exonérée IR/CNSS — Plafond : 500 MAD/mois'],
                        'indemnite_panier'    => ['346', 'Indemnité panier', 'Exonérée IR/CNSS jusqu\'à 780 MAD/mois'],
                        'indemnite_representation' => ['331', 'Indemnité représentation', '10% du salaire de base'],
                        'avantage_logement'   => ['340', 'Avantage logement', 'Avantage en nature imposable'],
                    ];
                    foreach ($indemnFields as $field => $meta):
                        $code = $meta[0];
                        $val = (float) ($paie[$field] ?? 0);
                        $pt = getPlafondDgi($code, $plafonds, (float) $paie['salaire_base']);
                        $ov = overLimit($val, $pt);
                    ?>
                    <tr<?= $ov ? ' class="row-over-limit"' : '' ?>>
                        <td class="code"><?= $code ?></td>
                        <td><span class="info" title="<?= $meta[2] ?>">ⓘ</span> <?= $meta[1] ?></td>
                        <td></td>
                        <td class="taux">—</td>
                        <td class="montant">
                            <input type="number" step="0.01" min="0" name="<?= $field ?>" class="form-control-inline<?= $ov ? ' over-limit' : '' ?>" value="<?= $val ?>">
                            <?php if ($pt !== null): ?><span class="plafond-label">max <?= number_format($pt, 2, ',', ' ') ?></span><?php endif; ?>
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                    <?php endforeach; ?>

                    <?php foreach ($paieGains as $g):
                        $ptG = getPlafondDgi($g['code'], $plafonds, (float) $paie['salaire_base']);
                        $ovG = overLimit((float) $g['montant'], $ptG);
                    ?>
                    <tr<?= $ovG ? ' class="row-over-limit"' : '' ?>>
                        <td class="code"><?= htmlspecialchars($g['code']) ?></td>
                        <td><?= htmlspecialchars($g['libelle']) ?></td>
                        <td></td>
                        <td class="taux">—</td>
                        <td class="montant<?= $ovG ? ' over-limit' : '' ?>"><?= number_format($g['montant'], 2, ',', ' ') ?>
                            <?php if ($ptG !== null): ?><span class="plafond-label">max <?= number_format($ptG, 2, ',', ' ') ?></span><?php endif; ?>
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                    <?php endforeach; ?>

                    <tr id="gains-container"></tr>

                    <tr>
                        <td colspan="7" style="padding:0.25rem 0.75rem;">
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
                    </tr>

                    <tr class="section-header"><td colspan="7">Cotisations</td></tr>

                    <tr>
                        <td class="code">400</td>
                        <td><span class="info" title="min(Salaire brut, 6 000) × 4.48%">ⓘ</span> CNSS</td>
                        <td class="montant"><?= number_format(min($paie['salaire_brut'], 6000), 2, ',', ' ') ?></td>
                        <td class="taux">4,48%</td>
                        <td class="montant"><?= number_format($paie['cnss_salariale'], 2, ',', ' ') ?></td>
                        <td class="montant"><?= number_format($paie['cnss_patronale'], 2, ',', ' ') ?></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td class="code">410</td>
                        <td><span class="info" title="Salaire brut × 2.26%">ⓘ</span> AMO</td>
                        <td class="montant"><?= number_format($paie['salaire_brut'], 2, ',', ' ') ?></td>
                        <td class="taux">2,26%</td>
                        <td class="montant"><?= number_format($paie['amo_salariale'], 2, ',', ' ') ?></td>
                        <td class="montant"><?= number_format($paie['amo_patronale'], 2, ',', ' ') ?></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td class="code">420</td>
                        <td><span class="info" title="Montant fiche salarié">ⓘ</span> Mutuelle</td>
                        <td></td>
                        <td class="taux">—</td>
                        <td class="montant"><?= number_format($paie['mutuelle'], 2, ',', ' ') ?></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr class="section-header"><td colspan="7">IR et frais professionnels</td></tr>

                    <?php $sbiAnnuel = $paie['sbi'] * 12; $fpTaux = $sbiAnnuel <= 78000 ? 35 : 25; ?>
                    <tr>
                        <td class="code">500</td>
                        <td><span class="info" title="Salaire brut – Gains exonérés">ⓘ</span> SBI</td>
                        <td class="montant"><?= number_format($paie['sbi'], 2, ',', ' ') ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td class="code">501</td>
                        <td><span class="info" title="<?= $sbiAnnuel <= 78000 ? 'SBI annuel ≤ 78 000 → 35%' : 'SBI annuel > 78 000 → 25% (max 2 916,70 MAD)' ?>">ⓘ</span> Frais professionnels</td>
                        <td class="montant"><?= number_format($paie['sbi'], 2, ',', ' ') ?></td>
                        <td class="taux"><?= $fpTaux ?>%</td>
                        <td></td>
                        <td class="montant"><?= number_format($paie['frais_professionnels'], 2, ',', ' ') ?></td>
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
                    </tr>

                    <tr>
                        <td class="code">600</td>
                        <td><span class="info" title="Barème progressif IR sur SNI">ⓘ</span> IR brut</td>
                        <td class="montant"><?= number_format($paie['sni'], 2, ',', ' ') ?></td>
                        <td class="taux">Barème</td>
                        <td></td>
                        <td class="montant"><?= number_format($paie['ir'], 2, ',', ' ') ?></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td class="code">601</td>
                        <td><span class="info" title="30 MAD × Nb enfants à charge (max 6)">ⓘ</span> Déductions familiales</td>
                        <td></td>
                        <td class="taux">—</td>
                        <td class="montant" style="color:var(--success);"><?= number_format($paie['deductions_familiales'], 2, ',', ' ') ?></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr class="section-header"><td colspan="7">Retenues personnalisées</td></tr>

                    <?php foreach ($paieRetenues as $r): ?>
                    <tr class="retenue-row" data-id="<?= $r['id'] ?>">
                        <td class="code">900</td>
                        <td>
                            <select name="retenue_type_existing[<?= $r['id'] ?>]" class="form-select-inline" style="width:75px;">
                                <option value="avance"<?= $r['type'] === 'avance' ? ' selected' : '' ?>>Avance</option>
                                <option value="pret"<?= $r['type'] === 'pret' ? ' selected' : '' ?>>Prêt</option>
                                <option value="sanction"<?= $r['type'] === 'sanction' ? ' selected' : '' ?>>Sanction</option>
                                <option value="autre"<?= $r['type'] === 'autre' ? ' selected' : '' ?>>Autre</option>
                            </select>
                            <input type="text" name="retenue_libelle_existing[<?= $r['id'] ?>]" class="form-control-inline" style="width:calc(100% - 80px);text-align:left;" value="<?= htmlspecialchars($r['libelle']) ?>">
                        </td>
                        <td></td>
                        <td class="taux">—</td>
                        <td></td>
                        <td class="montant">
                            <input type="number" step="0.01" min="0" name="retenue_montant_existing[<?= $r['id'] ?>]" class="form-control-inline" style="width:80px;" value="<?= $r['montant'] ?>">
                        </td>
                        <td style="text-align:center;">
                            <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()" style="padding:0.1rem 0.3rem;font-size:0.65rem;">✕</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <tr id="retenues-container"></tr>

                    <tr>
                        <td colspan="7" style="padding:0.25rem 0.75rem;">
                            <button type="button" class="btn btn-sm btn-secondary" onclick="ajouterRetenue()" style="font-size:0.75rem;">+ Ajouter une retenue</button>
                        </td>
                    </tr>

                    <tr class="section-header recap-section"><td colspan="7">Récapitulatif</td></tr>

                    <tr>
                        <td></td>
                        <td><span class="info" title="Avances + Prêts + Retenues personnalisées">ⓘ</span> Autres retenues</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="montant"><?= number_format($paie['autres_retenues'], 2, ',', ' ') ?></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td></td>
                        <td><span class="info" title="Salaire brut – Cotisations – IR – Mutuelle">ⓘ</span> Net avant retenues</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="montant"><strong><?= number_format($paie['net_avant_retenues'], 2, ',', ' ') ?></strong></td>
                        <td></td>
                    </tr>

                    <tr class="net-row">
                        <td></td>
                        <td><span class="info" title="Net avant retenues – Autres retenues">ⓘ</span> <strong style="color:var(--accent);">Net à payer</strong></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="montant"><strong style="color:var(--accent);font-size:1rem;"><?= number_format($paie['net_a_payer'], 2, ',', ' ') ?> MAD</strong></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div style="padding:0.75rem 1rem 1rem; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:0.5rem; border-top:1px solid var(--border);">
            <label style="display:flex;align-items:center;gap:0.4rem;cursor:pointer;font-size:0.8rem;color:var(--text-muted);user-select:none;">
                <input type="checkbox" name="fermer_apres" value="1" style="accent-color:var(--accent);cursor:pointer;">
                <span>Fermer la fenêtre après l'enregistrement</span>
            </label>
            <div style="display:flex;gap:0.5rem;align-items:center;">
                <button type="submit" class="btn btn-primary">Enregistrer</button>
                <a href="/paie-me/paies/<?= $paie['periode_id'] ?>/calculate" class="btn btn-secondary btn-sm" onclick="return confirm('Recalculer toutes les paies de cette période ? Les modifications manuelles seront perdues.')">Recalculer la période</a>
                <a href="/paie-me/paies/<?= $paie['periode_id'] ?>/lignes" class="btn btn-secondary btn-sm">Retour</a>
            </div>
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
.edit-paie-table .section-header td { padding:0.4rem 0.5rem 0.2rem; font-size:0.65rem; font-weight:600; text-transform:uppercase; letter-spacing:0.06em; color:var(--accent); border-bottom:none; background:rgba(59,130,246,0.04); }
.edit-paie-table .total-row td { padding:0.4rem 0.5rem; border-top:1px solid var(--border); font-weight:600; }
.edit-paie-table .recap-section td { border-top:1px solid var(--border); }
.edit-paie-table .net-row td { border-top:2px solid var(--accent); }
.form-control-inline { width:60px; padding:0.2rem 0.3rem; font-size:0.75rem; background:var(--surface); border:1px solid var(--border); border-radius:3px; color:var(--text); text-align:right; }
.form-control-inline:focus { border-color:var(--accent); outline:none; }
.form-control-inline.over-limit { border-color:#ef4444; background:rgba(239,68,68,0.12); color:#fca5a5; }
.row-over-limit td { background:rgba(239,68,68,0.06); }
.montant.over-limit { color:#fca5a5; font-weight:600; }
.plafond-label { display:block; font-size:0.6rem; color:var(--text-muted); white-space:nowrap; margin-top:0.1rem; }
.info { cursor:help; font-size:0.7rem; color:var(--text-muted); margin-right:0.15rem; }
.info:hover { color:var(--accent); }
.form-select-inline { padding:0.2rem 0.3rem; font-size:0.72rem; background:var(--surface); border:1px solid var(--border); border-radius:3px; color:var(--text); }
.form-select-inline:focus { border-color:var(--accent); outline:none; }
</style>

<script>
let gainIdx = <?= count($paieGains) ?>;
function ajouterGain() {
    gainIdx++;
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td class="code">CUST</td>
        <td>
            <input type="text" name="gain_custom_libelle[${gainIdx}]" class="form-control-inline" style="width:100%;text-align:left;" placeholder="Libellé">
        </td>
        <td></td>
        <td class="taux">—</td>
        <td class="montant">
            <input type="number" step="0.01" min="0" name="gain_custom_montant[${gainIdx}]" class="form-control-inline" style="width:80px;" value="0">
        </td>
        <td></td>
        <td style="text-align:center;">
            <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()" style="padding:0.1rem 0.3rem;font-size:0.65rem;">✕</button>
        </td>
    `;
    document.getElementById('gains-container').before(tr);
}

let retenueIdx = <?= count($paieRetenues) ?>;
function ajouterRetenue() {
    retenueIdx++;
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td class="code">900</td>
        <td>
            <select name="retenue_type[${retenueIdx}]" class="form-select-inline" style="width:75px;">
                <option value="avance">Avance</option>
                <option value="pret">Prêt</option>
                <option value="sanction">Sanction</option>
                <option value="autre">Autre</option>
            </select>
            <input type="text" name="retenue_libelle[${retenueIdx}]" class="form-control-inline" style="width:calc(100% - 80px);text-align:left;" placeholder="Libellé">
        </td>
        <td></td>
        <td class="taux">—</td>
        <td></td>
        <td class="montant">
            <input type="number" step="0.01" min="0" name="retenue_montant[${retenueIdx}]" class="form-control-inline" style="width:80px;" value="0">
        </td>
        <td style="text-align:center;">
            <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()" style="padding:0.1rem 0.3rem;font-size:0.65rem;">✕</button>
        </td>
    `;
    document.getElementById('retenues-container').before(tr);
}
</script>