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

    <div class="info-banner">
        <div><strong>Salarié :</strong> <?= htmlspecialchars($paie['nom_famille'] . ' ' . $paie['prenom']) ?></div>
        <div><strong>Société :</strong> <?= htmlspecialchars($paie['raison_sociale']) ?></div>
        <div><strong>Période :</strong> <?= str_pad($paie['mois'] ?? '', 2, '0', STR_PAD_LEFT) ?>/<?= $paie['annee'] ?? '' ?></div>
        <div><strong>Salaire de base :</strong> <?= number_format($paie['salaire_base'], 2, ',', ' ') ?> MAD</div>
    </div>

    <form method="POST">
        <?= \Core\Session::csrfField() ?>

        <div class="section-card">
            <h4 class="section-title">Salaire & Durée</h4>
            <div class="section-body">
                <div class="field-row">
                    <div class="field-group">
                        <label>Salaire de base</label>
                        <span class="field-value"><?= number_format($paie['salaire_base'], 2, ',', ' ') ?> MAD</span>
                    </div>
                    <div class="field-group hs-group">
                        <label>HS <?= $t25 ?>%</label>
                        <div class="hs-input">
                            <input type="number" step="0.5" min="0" name="heures_sup_25" class="form-control-inline" value="<?= $hs25 ?>">
                            <span class="hs-info">× <?= number_format($th, 2, ',', ' ') ?> = <?= number_format($mHS25, 2, ',', ' ') ?> MAD</span>
                        </div>
                    </div>
                    <div class="field-group hs-group">
                        <label>HS <?= $t50 ?>%</label>
                        <div class="hs-input">
                            <input type="number" step="0.5" min="0" name="heures_sup_50" class="form-control-inline" value="<?= $hs50 ?>">
                            <span class="hs-info">× <?= number_format($th, 2, ',', ' ') ?> = <?= number_format($mHS50, 2, ',', ' ') ?> MAD</span>
                        </div>
                    </div>
                    <div class="field-group hs-group">
                        <label>HS <?= $t100 ?>%</label>
                        <div class="hs-input">
                            <input type="number" step="0.5" min="0" name="heures_sup_100" class="form-control-inline" value="<?= $hs100 ?>">
                            <span class="hs-info">× <?= number_format($th, 2, ',', ' ') ?> = <?= number_format($mHS100, 2, ',', ' ') ?> MAD</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="section-card">
            <h4 class="section-title">Indemnités</h4>
            <div class="section-body">
                <div class="field-row">
                    <?php
                    $indemnites = [
                        'indemnite_transport' => ['Transport', '330', 500],
                        'indemnite_panier' => ['Panier', '346', 780],
                        'indemnite_representation' => ['Représentation', '331', '10% SM'],
                        'avantage_logement' => ['Avantage logement', '340', '—'],
                    ];
                    foreach ($indemnites as $field => $meta):
                        $val = (float) ($paie[$field] ?? 0);
                        $code = $meta[1];
                        $pt = getPlafondDgi($code, $plafonds, (float) $paie['salaire_base']);
                        $ov = overLimit($val, $pt);
                    ?>
                    <div class="field-group">
                        <label><?= $meta[0] ?></label>
                        <div>
                            <input type="number" step="0.01" min="0" name="<?= $field ?>" class="form-control-inline<?= $ov ? ' over-limit' : '' ?>" value="<?= $val ?>">
                            <?php if ($pt !== null): ?>
                            <span class="plafond-label">max <?= number_format($pt, 2, ',', ' ') ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="section-card">
            <h4 class="section-title">Gains
                <button type="button" class="btn btn-sm btn-secondary" onclick="ajouterGain()">+ Ajouter</button>
            </h4>
            <div class="section-body" style="padding:0;">
                <table class="items-table" id="gains-table">
                    <thead>
                        <tr>
                            <th style="width:60px;">Code</th>
                            <th>Libellé</th>
                            <th style="width:130px;">Montant</th>
                            <th style="width:30px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($paieGains as $g):
                            $ptG = getPlafondDgi($g['code'], $plafonds, (float) $paie['salaire_base']);
                            $ovG = overLimit((float) $g['montant'], $ptG);
                        ?>
                        <tr<?= $ovG ? ' class="row-over-limit"' : '' ?>>
                            <td class="code"><?= htmlspecialchars($g['code']) ?></td>
                            <td><?= htmlspecialchars($g['libelle']) ?></td>
                            <td class="montant<?= $ovG ? ' over-limit' : '' ?>">
                                <?= number_format($g['montant'], 2, ',', ' ') ?>
                                <?php if ($ptG !== null): ?>
                                <span class="plafond-label">max <?= number_format($ptG, 2, ',', ' ') ?></span>
                                <?php endif; ?>
                            </td>
                            <td></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr id="gains-container"></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="section-card">
            <h4 class="section-title">Retenues
                <button type="button" class="btn btn-sm btn-secondary" onclick="ajouterRetenue()">+ Ajouter</button>
            </h4>
            <div class="section-body" style="padding:0;">
                <table class="items-table" id="retenues-table">
                    <thead>
                        <tr>
                            <th style="width:90px;">Type</th>
                            <th>Libellé</th>
                            <th style="width:130px;">Montant</th>
                            <th style="width:30px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($paieRetenues as $r): ?>
                        <tr class="retenue-row" data-id="<?= $r['id'] ?>">
                            <td>
                                <select name="retenue_type_existing[<?= $r['id'] ?>]" class="form-select-inline">
                                    <option value="avance"<?= $r['type'] === 'avance' ? ' selected' : '' ?>>Avance</option>
                                    <option value="pret"<?= $r['type'] === 'pret' ? ' selected' : '' ?>>Prêt</option>
                                    <option value="sanction"<?= $r['type'] === 'sanction' ? ' selected' : '' ?>>Sanction</option>
                                    <option value="autre"<?= $r['type'] === 'autre' ? ' selected' : '' ?>>Autre</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" name="retenue_libelle_existing[<?= $r['id'] ?>]" class="form-control-inline" style="width:100%;text-align:left;" value="<?= htmlspecialchars($r['libelle']) ?>">
                            </td>
                            <td class="montant">
                                <input type="number" step="0.01" min="0" name="retenue_montant_existing[<?= $r['id'] ?>]" class="form-control-inline" style="width:80px;" value="<?= $r['montant'] ?>">
                            </td>
                            <td style="text-align:center;">
                                <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()" style="padding:0.1rem 0.3rem;font-size:0.65rem;">✕</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <tr id="retenues-container"></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="section-card">
            <h4 class="section-title">Cotisations sociales</h4>
            <div class="section-body" style="padding:0;">
                <table class="items-table">
                    <thead>
                        <tr>
                            <th style="width:60px;">Code</th>
                            <th>Libellé</th>
                            <th style="width:100px;">Base</th>
                            <th style="width:50px;">Taux</th>
                            <th style="width:110px;">Salariale</th>
                            <th style="width:110px;">Patronale</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="code">400</td>
                            <td>CNSS</td>
                            <td class="montant"><?= number_format(min($paie['salaire_brut'], 6000), 2, ',', ' ') ?></td>
                            <td class="taux">4,48%</td>
                            <td class="montant"><?= number_format($paie['cnss_salariale'], 2, ',', ' ') ?></td>
                            <td class="montant"><?= number_format($paie['cnss_patronale'], 2, ',', ' ') ?></td>
                        </tr>
                        <tr>
                            <td class="code">410</td>
                            <td>AMO</td>
                            <td class="montant"><?= number_format($paie['salaire_brut'], 2, ',', ' ') ?></td>
                            <td class="taux">2,26%</td>
                            <td class="montant"><?= number_format($paie['amo_salariale'], 2, ',', ' ') ?></td>
                            <td class="montant"><?= number_format($paie['amo_patronale'], 2, ',', ' ') ?></td>
                        </tr>
                        <tr>
                            <td class="code">420</td>
                            <td>Mutuelle</td>
                            <td></td>
                            <td class="taux">—</td>
                            <td class="montant"><?= number_format($paie['mutuelle'], 2, ',', ' ') ?></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="section-card">
            <h4 class="section-title">Impôt sur le Revenu (IR)</h4>
            <div class="section-body" style="padding:0;">
                <table class="items-table">
                    <thead>
                        <tr>
                            <th style="width:60px;">Code</th>
                            <th>Libellé</th>
                            <th style="width:100px;">Base</th>
                            <th style="width:50px;">Taux</th>
                            <th style="width:110px;">Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $sbiAnnuel = $paie['sbi'] * 12; $fpTaux = $sbiAnnuel <= 78000 ? 35 : 25; ?>
                        <tr>
                            <td class="code">500</td>
                            <td>SBI</td>
                            <td class="montant"><?= number_format($paie['sbi'], 2, ',', ' ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="code">501</td>
                            <td>Frais professionnels</td>
                            <td class="montant"><?= number_format($paie['sbi'], 2, ',', ' ') ?></td>
                            <td class="taux"><?= $fpTaux ?>%</td>
                            <td class="montant"><?= number_format($paie['frais_professionnels'], 2, ',', ' ') ?></td>
                        </tr>
                        <tr>
                            <td class="code">502</td>
                            <td>SNI</td>
                            <td class="montant"><?= number_format($paie['sni'], 2, ',', ' ') ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="code">600</td>
                            <td>IR brut</td>
                            <td class="montant"><?= number_format($paie['sni'], 2, ',', ' ') ?></td>
                            <td class="taux">Barème</td>
                            <td class="montant"><?= number_format($paie['ir'], 2, ',', ' ') ?></td>
                        </tr>
                        <tr>
                            <td class="code">601</td>
                            <td>Déductions familiales</td>
                            <td></td>
                            <td class="taux">—</td>
                            <td class="montant" style="color:var(--success);"><?= number_format($paie['deductions_familiales'], 2, ',', ' ') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="section-card recap-card">
            <h4 class="section-title">Récapitulatif</h4>
            <div class="section-body">
                <div class="recap-grid">
                    <div class="recap-line">
                        <span>Salaire brut</span>
                        <span class="recap-val"><?= number_format($paie['salaire_brut'], 2, ',', ' ') ?> MAD</span>
                    </div>
                    <div class="recap-line">
                        <span>Total cotisations</span>
                        <span class="recap-val"><?= number_format($paie['cnss_salariale'] + $paie['amo_salariale'] + $paie['mutuelle'], 2, ',', ' ') ?> MAD</span>
                    </div>
                    <div class="recap-line">
                        <span>Net avant retenues</span>
                        <span class="recap-val"><?= number_format($paie['net_avant_retenues'], 2, ',', ' ') ?> MAD</span>
                    </div>
                    <div class="recap-line">
                        <span>Autres retenues</span>
                        <span class="recap-val"><?= number_format($paie['autres_retenues'], 2, ',', ' ') ?> MAD</span>
                    </div>
                    <div class="recap-line recap-total">
                        <span>Net à payer</span>
                        <span class="recap-val-accent"><?= number_format($paie['net_a_payer'], 2, ',', ' ') ?> MAD</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-footer">
            <label class="fermer-check">
                <input type="checkbox" name="fermer_apres" value="1">
                <span>Fermer la fenêtre après l'enregistrement</span>
            </label>
            <div class="footer-actions">
                <button type="submit" class="btn btn-primary">Enregistrer</button>
                <a href="/paie-me/paies/<?= $paie['periode_id'] ?>/calculate" class="btn btn-secondary btn-sm" onclick="return confirm('Recalculer toutes les paies de cette période ? Les modifications manuelles seront perdues.')">Recalculer la période</a>
                <a href="/paie-me/paies/<?= $paie['periode_id'] ?>/lignes" class="btn btn-secondary btn-sm">Retour</a>
            </div>
        </div>
    </form>
</div>

<style>
.info-banner {
    display:grid; grid-template-columns:1fr 1fr; gap:0.5rem;
    padding:1rem; border-bottom:1px solid var(--border);
    font-size:0.85rem;
}
.section-card {
    border-bottom:1px solid var(--border);
}
.section-title {
    display:flex; align-items:center; gap:0.5rem;
    margin:0; padding:0.6rem 1rem;
    font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em;
    color:var(--accent); font-weight:600;
    background:rgba(59,130,246,0.04);
    border-bottom:1px solid var(--border-subtle);
}
.section-body { padding:0.75rem 1rem; }
.field-row {
    display:grid;
    grid-template-columns:repeat(auto-fill, minmax(200px, 1fr));
    gap:0.75rem;
}
.field-group label {
    display:block; font-size:0.7rem; color:var(--text-muted);
    margin-bottom:0.15rem; text-transform:uppercase; letter-spacing:0.03em;
}
.field-value {
    font-size:0.9rem; font-weight:600; color:var(--text);
}
.hs-group .hs-input {
    display:flex; align-items:center; gap:0.35rem; flex-wrap:wrap;
}
.hs-info {
    font-size:0.7rem; color:var(--text-muted); white-space:nowrap;
}
.form-control-inline {
    width:60px; padding:0.2rem 0.3rem; font-size:0.75rem;
    background:var(--surface); border:1px solid var(--border); border-radius:3px;
    color:var(--text); text-align:right;
}
.form-control-inline:focus { border-color:var(--accent); outline:none; }
.form-control-inline.over-limit { border-color:#ef4444; background:rgba(239,68,68,0.12); color:#fca5a5; }
.row-over-limit td { background:rgba(239,68,68,0.06); }
.plafond-label { display:block; font-size:0.6rem; color:var(--text-muted); white-space:nowrap; margin-top:0.1rem; }

.items-table { width:100%; border-collapse:collapse; }
.items-table th {
    padding:0.35rem 0.6rem; text-align:left; font-size:0.65rem;
    text-transform:uppercase; letter-spacing:0.04em;
    color:var(--text-muted); border-bottom:1px solid var(--border);
}
.items-table td { padding:0.3rem 0.6rem; font-size:0.8rem; border-bottom:1px solid var(--border-subtle); }
.items-table .montant { text-align:right; white-space:nowrap; }
.items-table .taux { text-align:center; font-size:0.75rem; color:var(--text-muted); }
.items-table .code { text-align:center; font-size:0.7rem; color:var(--text-muted); font-family:monospace; }
.montant.over-limit { color:#fca5a5; font-weight:600; }

.form-select-inline {
    padding:0.2rem 0.3rem; font-size:0.72rem;
    background:var(--surface); border:1px solid var(--border); border-radius:3px;
    color:var(--text); width:80px;
}
.form-select-inline:focus { border-color:var(--accent); outline:none; }

.recap-card { border-bottom:none; }
.recap-grid { display:flex; flex-direction:column; gap:0.3rem; }
.recap-line {
    display:flex; justify-content:space-between; align-items:center;
    padding:0.15rem 0; font-size:0.85rem;
}
.recap-val { font-weight:500; }
.recap-total {
    border-top:2px solid var(--accent); margin-top:0.3rem;
    padding-top:0.4rem; font-size:1rem;
}
.recap-val-accent { color:var(--accent); font-weight:700; font-size:1.1rem; }

.form-footer {
    display:flex; justify-content:space-between; align-items:center;
    padding:0.75rem 1rem; border-top:1px solid var(--border);
    flex-wrap:wrap; gap:0.5rem;
}
.fermer-check {
    display:flex; align-items:center; gap:0.4rem;
    cursor:pointer; font-size:0.8rem; color:var(--text-muted);
    user-select:none;
}
.fermer-check input { accent-color:var(--accent); cursor:pointer; }
.fermer-check:hover { color:var(--text); }
.footer-actions { display:flex; gap:0.5rem; align-items:center; }
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
        <td class="montant">
            <input type="number" step="0.01" min="0" name="gain_custom_montant[${gainIdx}]" class="form-control-inline" style="width:80px;" value="0">
        </td>
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
        <td>
            <select name="retenue_type[${retenueIdx}]" class="form-select-inline">
                <option value="avance">Avance</option>
                <option value="pret">Prêt</option>
                <option value="sanction">Sanction</option>
                <option value="autre">Autre</option>
            </select>
        </td>
        <td>
            <input type="text" name="retenue_libelle[${retenueIdx}]" class="form-control-inline" style="width:100%;text-align:left;" placeholder="Libellé">
        </td>
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
