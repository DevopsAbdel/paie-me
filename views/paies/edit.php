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
$jt = (int) ($paie['jours_travailles'] ?? 30);
$jc = (float) ($paie['jours_conge'] ?? 0);
$jf = (float) ($paie['jours_feries'] ?? 0);
$prorata = $jt / 26;
$baseProrata = round($paie['salaire_base'] * $prorata, 2);
$totalPaidDays = min($jt + $jc + $jf, 26);
$prorataElargi = $totalPaidDays / 26;
$primeBase = round($paie['salaire_base'] * $prorataElargi, 2);
$tauxJournalier = $paie['salaire_base'] > 0 ? round($paie['salaire_base'] / 26, 2) : 0;
$tauxHoraire = $paie['salaire_base'] > 0 ? round($paie['salaire_base'] / 26 / 8, 2) : 0;
$primeBaseElargie = $baseProrata + ($jc * $tauxJournalier) + ($jf * $tauxJournalier) + $mHS25 + $mHS50 + $mHS100;
$ancienPct = $primeBaseElargie > 0 ? round((float)($paie['prime_anciennete'] ?? 0) / $primeBaseElargie * 100, 0) : 0;

function getPlafondDgi(string $code, array $plafonds, float $salaireBase): ?float
{
    if (isset($plafonds[$code]) && $plafonds[$code]['plafond_dgi_actif']) {
        return (float) $plafonds[$code]['plafond_dgi_valeur'];
    }
    if ($code === '331') return round($salaireBase * 0.10, 2);
    if ($code === '346') return 780.0;
    return null;
}

function getPlafondCnss(string $code, array $plafonds): ?float
{
    if (isset($plafonds[$code]) && $plafonds[$code]['plafond_cnss_actif']) {
        return (float) $plafonds[$code]['plafond_cnss_valeur'];
    }
    if ($code === '330') return 500.0;
    if ($code === '346') return 780.0;
    return null;
}

function overLimit(?float $valeur, ?float $plafond): bool
{
    return $plafond !== null && $valeur !== null && $valeur > $plafond;
}

$alerts = [];

$indemnFields = [
    'indemnite_transport' => ['330', 'Indemnité transport', 'Exonérée IR/CNSS — Plafond : 500 MAD/mois'],
    'indemnite_panier'    => ['346', 'Indemnité panier', 'Exonérée IR/CNSS jusqu\'à 780 MAD/mois'],
    'indemnite_representation' => ['331', 'Indemnité représentation', '10% du salaire de base', '100 × 10%'],
    'avantage_logement'   => ['340', 'Avantage logement', 'Avantage en nature imposable'],
];

if (!empty($smigMensuel) && (float) $paie['salaire_base'] < $smigMensuel) {
    $alerts[] = [
        'type'  => 'danger',
        'titre' => 'Salaire non conforme au SMIG',
        'texte' => 'Le salaire de base (' . number_format($paie['salaire_base'], 2, ',', ' ') . ' MAD) est inférieur au SMIG mensuel ' . ($paie['annee'] ?? '') . ' (' . number_format($smigMensuel, 2, ',', ' ') . ' MAD).',
    ];
}

foreach ($indemnFields as $field => $meta) {
    $code = $meta[0];
    $val = (float) ($paie[$field] ?? 0);
    $ptDgi = getPlafondDgi($code, $plafonds, (float) $paie['salaire_base']);
    $ptCnss = getPlafondCnss($code, $plafonds);
    if (overLimit($val, $ptDgi)) {
        $alerts[] = [
            'type'  => 'warning',
            'titre' => $meta[1] . ' — plafond DGI dépassé',
            'texte' => 'Le montant de ' . number_format($val, 2, ',', ' ') . ' MAD dépasse le plafond DGI (' . number_format($ptDgi, 2, ',', ' ') . ' MAD).',
        ];
    }
    if (overLimit($val, $ptCnss)) {
        $alerts[] = [
            'type'  => 'warning',
            'titre' => $meta[1] . ' — plafond CNSS dépassé',
            'texte' => 'Le montant de ' . number_format($val, 2, ',', ' ') . ' MAD dépasse le plafond CNSS (' . number_format($ptCnss, 2, ',', ' ') . ' MAD).',
        ];
    }
}

foreach ($paieGains as $g) {
    $valG = (float) $g['montant'];
    $ptDG = getPlafondDgi($g['code'], $plafonds, (float) $paie['salaire_base']);
    $ptCG = getPlafondCnss($g['code'], $plafonds);
    if (overLimit($valG, $ptDG)) {
        $alerts[] = [
            'type'  => 'warning',
            'titre' => $g['libelle'] . ' — plafond DGI dépassé',
            'texte' => 'Le montant de ' . number_format($valG, 2, ',', ' ') . ' MAD dépasse le plafond DGI (' . number_format($ptDG, 2, ',', ' ') . ' MAD).',
        ];
    }
    if (overLimit($valG, $ptCG)) {
        $alerts[] = [
            'type'  => 'warning',
            'titre' => $g['libelle'] . ' — plafond CNSS dépassé',
            'texte' => 'Le montant de ' . number_format($valG, 2, ',', ' ') . ' MAD dépasse le plafond CNSS (' . number_format($ptCG, 2, ',', ' ') . ' MAD).',
        ];
    }
}
?>
<div class="card">
    <div class="card-header">
        <h3>Modifier la paie</h3>
        <div class="table-actions">
            <a href="/paie-me/paies/<?= $paie['periode_id'] ?>/lignes" class="btn btn-secondary btn-sm">Retour aux paies</a>
        </div>
    </div>

    <div class="edit-paie-summary">
        <div class="ep-summary-item">
            <span class="ep-summary-label">Salarié</span>
            <span class="ep-summary-value"><?= htmlspecialchars($paie['nom_famille'] . ' ' . $paie['prenom']) ?></span>
        </div>
        <div class="ep-summary-item">
            <span class="ep-summary-label">Société</span>
            <span class="ep-summary-value"><?= htmlspecialchars($paie['raison_sociale']) ?></span>
        </div>
        <div class="ep-summary-item">
            <span class="ep-summary-label">Période</span>
            <span class="ep-summary-value"><?= str_pad($paie['mois'] ?? '', 2, '0', STR_PAD_LEFT) ?>/<?= $paie['annee'] ?? '' ?></span>
        </div>
        <div class="ep-summary-item">
            <span class="ep-summary-label">Salaire de base</span>
            <span class="ep-summary-value"><?= number_format($paie['salaire_base'], 2, ',', ' ') ?> MAD</span>
        </div>
    </div>

    <?php if (!empty($alerts)): ?>
    <div class="ep-alerts">
        <?php foreach ($alerts as $alert): ?>
        <div class="ep-alert ep-alert-<?= $alert['type'] ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            <div class="ep-alert-body">
                <strong><?= htmlspecialchars($alert['titre']) ?></strong>
                <span><?= htmlspecialchars($alert['texte']) ?></span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <form method="POST">
        <?= \Core\Session::csrfField() ?>

        <div class="table-wrapper edit-paie-table-wrap">
            <table class="edit-paie-table">
                <thead>
                    <tr>
                        <th style="width:5%;">Code</th>
                        <th style="width:22%;">Libellé</th>
                        <th style="width:10%;">Base</th>
                        <th style="width:7%;">Unité</th>
                        <th style="width:7%;">Taux</th>
                        <th style="width:11%;">Gains</th>
                        <th style="width:11%;">Retenues Salariales</th>
                        <th style="width:11%;">Retenues Patronales</th>
                        <th style="width:5%;"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="section-header"><td colspan="9">Salaire et indemnités</td></tr>

                    <tr>
                        <td class="code">100</td>
                        <td><span class="info-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg><span class="info-tooltip">Salaire mensuel contractualisé</span></span> Salaire de base</td>
                        <td class="montant"><?= number_format($paie['salaire_base'], 2, ',', ' ') ?></td>
                        <td class="unite">DH</td>
                        <td class="taux"><?= number_format($tauxHoraire, 2, ',', ' ') ?> /h</td>
                        <td class="montant gains-cell"><?= number_format($paie['salaire_base'], 2, ',', ' ') ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    
                    <tr>
                        <td class="code">101</td>
                        <td><span class="info-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg><span class="info-tooltip">Jours ouvrés dans le mois (salaire proratisé)</span></span> Durée de travail<span class="formule-label">(100 × jours travaillés / 26)</span></td>
                        <td class="montant">
                            <input type="number" step="1" min="0" max="31" name="jours_travailles" class="form-control-inline" value="<?= $jt ?>" style="width:64px;">
                        </td>
                        <td class="unite">Jours</td>
                        <td class="taux"><?= number_format($tauxJournalier, 2, ',', ' ') ?> /j</td>
                        <td class="montant gains-cell"><?= number_format($baseProrata, 2, ',', ' ') ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="code">102</td>
                        <td><span class="info-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg><span class="info-tooltip">Jours de congé payé — rémunérés au taux journalier</span></span> Jours de congé<span class="formule-label">(100 / 26 × jours de congé)</span></td>
                        <td class="montant">
                            <input type="number" step="0.5" min="0" max="31" name="jours_conge" class="form-control-inline" value="<?= (float)($paie['jours_conge'] ?? 0) ?>" style="width:64px;">
                        </td>
                        <td class="unite">Jours</td>
                        <td class="taux"><?= number_format($tauxJournalier, 2, ',', ' ') ?> /j</td>
                        <td class="montant gains-cell"><?= number_format($jc * $tauxJournalier, 2, ',', ' ') ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="code">103</td>
                        <td><span class="info-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg><span class="info-tooltip">Jours fériés chômés — rémunérés au taux journalier</span></span> Jours fériés<span class="formule-label">(100 / 26 × jours fériés)</span></td>
                        <td class="montant">
                            <input type="number" step="0.5" min="0" max="31" name="jours_feries" class="form-control-inline" value="<?= (float)($paie['jours_feries'] ?? 0) ?>" style="width:64px;">
                        </td>
                        <td class="unite">Jours</td>
                        <td class="taux"><?= number_format($tauxJournalier, 2, ',', ' ') ?> /j</td>
                        <td class="montant gains-cell"><?= number_format($jf * $tauxJournalier, 2, ',', ' ') ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td class="code">201</td>
                        <td><span class="info-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg><span class="info-tooltip">Taux horaire <?= number_format($th, 2, ',', ' ') ?> MAD/h — majoration <?= $t25 ?>%</span></span>
                            HS <?= $t25 ?>%<span class="formule-label">(100 / 191 × <?= $t25 ?>% × nbre h)</span>
                        </td>
                        <td class="montant">
                            <input type="number" step="0.5" min="0" name="heures_sup_25" class="form-control-inline" value="<?= $hs25 ?>" style="width:64px;">
                        </td>
                        <td class="unite">Heure</td>
                        <td class="taux"><?= $t25 ?>%</td>
                        <td class="montant gains-cell"><?= number_format($mHS25, 2, ',', ' ') ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td class="code">202</td>
                        <td><span class="info-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg><span class="info-tooltip">Taux horaire <?= number_format($th, 2, ',', ' ') ?> MAD/h — majoration <?= $t50 ?>%</span></span>
                            HS <?= $t50 ?>%<span class="formule-label">(100 / 191 × <?= $t50 ?>% × nbre h)</span>
                        </td>
                        <td class="montant">
                            <input type="number" step="0.5" min="0" name="heures_sup_50" class="form-control-inline" value="<?= $hs50 ?>" style="width:64px;">
                        </td>
                        <td class="unite">Heure</td>
                        <td class="taux"><?= $t50 ?>%</td>
                        <td class="montant gains-cell"><?= number_format($mHS50, 2, ',', ' ') ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td class="code">203</td>
                        <td><span class="info-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg><span class="info-tooltip">Taux horaire <?= number_format($th, 2, ',', ' ') ?> MAD/h — majoration <?= $t100 ?>%</span></span>
                            HS <?= $t100 ?>%<span class="formule-label">(100 / 191 × <?= $t100 ?>% × nbre h)</span>
                        </td>
                        <td class="montant">
                            <input type="number" step="0.5" min="0" name="heures_sup_100" class="form-control-inline" value="<?= $hs100 ?>" style="width:64px;">
                        </td>
                        <td class="unite">Heure</td>
                        <td class="taux"><?= $t100 ?>%</td>
                        <td class="montant gains-cell"><?= number_format($mHS100, 2, ',', ' ') ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <?php if ($paie['prime_anciennete'] > 0): ?>
                    <tr>
                        <td class="code">204</td>
                        <td><span class="info-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg><span class="info-tooltip">Ancienneté : <?= $ancienPct ?>% du salaire de base + HS</span></span> Prime d'ancienneté<span class="formule-label">((100 × prorata) + 201 + 202 + 203) × <?= $ancienPct ?>%</span></td>
                        <td class="montant"><?= number_format($primeBaseElargie, 2, ',', ' ') ?></td>
                        <td class="unite">DH</td>
                        <td class="taux"><?= $ancienPct ?>%</td>
                        <td class="montant gains-cell"><?= number_format($paie['prime_anciennete'], 2, ',', ' ') ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <?php endif; ?>

                    <?php $totalApresFeries = $baseProrata + ($jc * $tauxJournalier) + ($jf * $tauxJournalier) + $mHS25 + $mHS50 + $mHS100 + (float)($paie['prime_anciennete'] ?? 0); ?>
                    <tr>
                        <td class="code">205</td>
                        <td><span class="info-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg><span class="info-tooltip">Base prorata + congés + fériés + HS + ancienneté</span></span> Salaire Recalculer<span class="formule-label">(101 + 102 + 103 + 201 + 202 + 203 + 204)</span></td>
                        <td></td>
                        <td class="unite">DH</td>
                        <td class="taux">—</td>
                        <td class="montant gains-cell"><?= number_format($totalApresFeries, 2, ',', ' ') ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <?php
                    foreach ($indemnFields as $field => $meta):
                        $code = $meta[0];
                        $formule = $meta[3] ?? null;
                        $val = (float) ($paie[$field] ?? 0);
                        $pt = getPlafondDgi($code, $plafonds, (float) $paie['salaire_base']);
                        $ov = overLimit($val, $pt);
                    ?>
                    <tr<?= $ov ? ' class="row-over-limit"' : '' ?><?= $val == 0 ? ' style="display:none"' : '' ?>>
                        <td class="code"><?= $code ?></td>
                        <td><span class="info-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg><span class="info-tooltip"><?= $meta[2] ?></span></span> <?= $meta[1] ?><?php if ($formule): ?><span class="formule-label">(<?= $formule ?>)</span><?php endif; ?></td>
                        <td></td>
                        <td class="unite">DH</td>
                        <td class="taux">—</td>
                        <td class="montant gains-cell">
                            <input type="number" step="0.01" min="0" name="<?= $field ?>" class="form-control-inline<?= $ov ? ' over-limit' : '' ?>" value="<?= $val ?>">
                            <?php if ($pt !== null): ?><span class="plafond-label">max <?= number_format($pt, 2, ',', ' ') ?></span><?php endif; ?>
                        </td>
                        <td></td>
                        <td></td>
                        <td><button type="button" class="btn-icon" title="Supprimer" onclick="var r=this.closest('tr');r.querySelector('input').value='0';r.style.display='none'">✖</button></td>
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
                        <td class="unite">DH</td>
                        <td class="taux">—</td>
                        <td class="montant gains-cell<?= $ovG ? ' over-limit' : '' ?>">
                            <input type="hidden" name="gain_existing_rubrique_id[]" value="<?= (int)$g['rubrique_id'] ?>">
                            <input type="number" step="0.01" min="0" name="gain_existing_montant[]" class="form-control-inline" style="width:90px;" value="<?= $g['montant'] ?>">
                            <?php if ($ptG !== null): ?><span class="plafond-label">max <?= number_format($ptG, 2, ',', ' ') ?></span><?php endif; ?>
                        </td>
                        <td></td>
                        <td></td>
                        <td style="text-align:center;">
                            <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()" style="padding:0.1rem 0.3rem;font-size:0.65rem;">✕</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <tr id="gains-container"></tr>

                    <tr>
                        <td colspan="9" style="padding:0.25rem 0.75rem;">
                            <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('gainModal').style.display='flex'" style="font-size:0.75rem;">+ Ajouter un gain</button>
                        </td>
                    </tr>

                    <tr class="total-row">
                        <td></td>
                        <td><strong><span class="info-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg><span class="info-tooltip">Somme de tous les gains du mois</span></span> Salaire brut</strong><span class="formule-label">(205 + 330 + 346 + 331 + 340 + rubriques gains)</span></td>
                        <td></td>
                        <td class="unite">DH</td>
                        <td></td>
                        <td class="montant gains-cell"><strong><?= number_format($paie['salaire_brut'], 2, ',', ' ') ?></strong></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td class="code">500</td>
                        <td><span class="info-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg><span class="info-tooltip">Salaire brut – Gains exonérés</span></span> <strong>Salaire brut imposable</strong><span class="formule-label">(Salaire brut − 330 exonérée − 346 exonérée − 331)</span></td>
                        <td></td>
                        <td class="unite">DH</td>
                        <td></td>
                        <td class="montant gains-cell"><strong><?= number_format($paie['sbi'], 2, ',', ' ') ?></strong></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr class="section-header"><td colspan="9">Cotisations</td></tr>

                    <tr>
                        <td class="code">400</td>
                        <td><span class="info-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg><span class="info-tooltip">min(Salaire brut, 6 000) × 4.48%</span></span> CNSS<span class="formule-label">(min(SBI, 6 000) × 4,48%)</span></td>
                        <td class="montant"><?= number_format(min($paie['salaire_brut'], 6000), 2, ',', ' ') ?></td>
                        <td class="unite">DH</td>
                        <td class="taux">4,48%</td>
                        <td></td>
                        <td class="montant retenues-cell"><?= number_format($paie['cnss_salariale'], 2, ',', ' ') ?></td>
                        <td class="montant patronales-cell"><?= number_format($paie['cnss_patronale'], 2, ',', ' ') ?></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td class="code">410</td>
                        <td><span class="info-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg><span class="info-tooltip">Salaire brut × 2.26%</span></span> AMO<span class="formule-label">(SBI × 2,26%)</span></td>
                        <td class="montant"><?= number_format($paie['salaire_brut'], 2, ',', ' ') ?></td>
                        <td class="unite">DH</td>
                        <td class="taux">2,26%</td>
                        <td></td>
                        <td class="montant retenues-cell"><?= number_format($paie['amo_salariale'], 2, ',', ' ') ?></td>
                        <td class="montant patronales-cell"><?= number_format($paie['amo_patronale'], 2, ',', ' ') ?></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td class="code">420</td>
                        <td><span class="info-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg><span class="info-tooltip">Montant fiche salarié</span></span> Mutuelle</td>
                        <td></td>
                        <td class="unite">DH</td>
                        <td class="taux">—</td>
                        <td></td>
                        <td class="montant retenues-cell"><?= number_format($paie['mutuelle'], 2, ',', ' ') ?></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr class="total-row">
                        <td></td>
                        <td><strong>Total cotisations</strong><span class="formule-label">(400 + 410 + 420)</span></td>
                        <td></td>
                        <td class="unite">DH</td>
                        <td></td>
                        <td></td>
                        <td class="montant retenues-cell"><strong><?= number_format($paie['cnss_salariale'] + $paie['amo_salariale'] + $paie['mutuelle'], 2, ',', ' ') ?></strong></td>
                        <td class="montant patronales-cell"><strong><?= number_format($paie['cnss_patronale'] + $paie['amo_patronale'], 2, ',', ' ') ?></strong></td>
                        <td></td>
                    </tr>

                    <tr class="section-header"><td colspan="9">IR et frais professionnels</td></tr>

                    <?php $sbiAnnuel = $paie['sbi'] * 12; $fpTaux = $sbiAnnuel <= 78000 ? 35 : 25; ?>
                    <tr>
                        <td class="code">501</td>
                        <td><span class="info-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg><span class="info-tooltip"><?= $sbiAnnuel <= 78000 ? 'SBI annuel ≤ 78 000 → 35%' : 'SBI annuel > 78 000 → 25% (max 2 916,70 MAD)' ?></span></span> Frais professionnels<span class="formule-label">(SBI × <?= $fpTaux ?>%)</span></td>
                        <td class="montant"><?= number_format($paie['sbi'], 2, ',', ' ') ?></td>
                        <td class="unite">DH</td>
                        <td class="taux"><?= $fpTaux ?>%</td>
                        <td></td>
                        <td class="montant retenues-cell"><?= number_format($paie['frais_professionnels'], 2, ',', ' ') ?></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td class="code">502</td>
                        <td><span class="info-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg><span class="info-tooltip">SBI – Frais pro – CNSS – AMO – Mutuelle</span></span> SNI<span class="formule-label">(SBI − 501 − 400 − 410 − 420)</span></td>
                        <td class="montant"><?= number_format($paie['sni'], 2, ',', ' ') ?></td>
                        <td class="unite">DH</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td class="code">600</td>
                        <td><span class="info-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg><span class="info-tooltip">Barème progressif IR sur SNI</span></span> IR brut<span class="formule-label">(barème progressif sur 502)</span></td>
                        <td class="montant"><?= number_format($paie['sni'], 2, ',', ' ') ?></td>
                        <td class="unite">DH</td>
                        <td class="taux">Barème</td>
                        <td></td>
                        <td class="montant retenues-cell"><?= number_format($paie['ir'], 2, ',', ' ') ?></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td class="code">601</td>
                        <td><span class="info-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg><span class="info-tooltip">30 MAD × Nb enfants à charge (max 6)</span></span> Déductions familiales<span class="formule-label">(50 MAD × nb charges, max 6)</span></td>
                        <td></td>
                        <td class="unite">DH</td>
                        <td class="taux">—</td>
                        <td></td>
                        <td></td>
                        <td class="montant" style="color:var(--success);"><?= number_format($paie['deductions_familiales'], 2, ',', ' ') ?></td>
                        <td></td>
                    </tr>

                    <tr class="section-header"><td colspan="9">Retenues personnalisées</td></tr>

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
                            <input type="text" name="retenue_libelle_existing[<?= $r['id'] ?>]" class="form-control-inline" style="width:calc(100% - 90px);text-align:left;" value="<?= htmlspecialchars($r['libelle']) ?>">
                        </td>
                        <td></td>
                        <td class="unite">DH</td>
                        <td class="taux">—</td>
                        <td></td>
                        <td class="montant retenues-cell">
                            <input type="number" step="0.01" min="0" name="retenue_montant_existing[<?= $r['id'] ?>]" class="form-control-inline" style="width:90px;" value="<?= $r['montant'] ?>">
                        </td>
                        <td></td>
                        <td style="text-align:center;">
                            <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()" style="padding:0.1rem 0.3rem;font-size:0.65rem;">✕</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <tr id="retenues-container"></tr>

                    <tr>
                        <td colspan="9" style="padding:0.25rem 0.75rem;">
                            <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('retenueModal').style.display='flex'" style="font-size:0.75rem;">+ Ajouter une retenue</button>
                        </td>
                    </tr>

                    <tr class="section-header recap-section"><td colspan="9">Récapitulatif</td></tr>

                    <tr>
                        <td></td>
                        <td><span class="info-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg><span class="info-tooltip">Avances + Prêts + Retenues personnalisées</span></span> Autres retenues<span class="formule-label">(801 + 802 + …)</span></td>
                        <td></td>
                        <td class="unite">DH</td>
                        <td></td>
                        <td></td>
                        <td class="montant retenues-cell"><?= number_format($paie['autres_retenues'], 2, ',', ' ') ?></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td></td>
                        <td><span class="info-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg><span class="info-tooltip">Salaire brut – Cotisations – IR – Mutuelle</span></span> Net avant retenues<span class="formule-label">(Salaire brut − 400 − 410 − 420 − 600)</span></td>
                        <td></td>
                        <td class="unite">DH</td>
                        <td></td>
                        <td class="montant gains-cell"><strong><?= number_format($paie['net_avant_retenues'], 2, ',', ' ') ?></strong></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr class="net-row">
                        <td></td>
                        <td><span class="info-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg><span class="info-tooltip">Net avant retenues – Autres retenues</span></span> <strong style="color:var(--accent);">Net à payer</strong><span class="formule-label">(Net avant retenues − Autres retenues)</span></td>
                        <td></td>
                        <td class="unite">DH</td>
                        <td></td>
                        <td class="montant gains-cell"><strong style="color:var(--accent);font-size:1.15rem;"><?= number_format($paie['net_a_payer'], 2, ',', ' ') ?> MAD</strong></td>
                        <td></td>
                        <td></td>
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
            <div style="display:flex;gap:0.5rem;align-items:center;flex-wrap:wrap;">
                <button type="submit" class="btn btn-success ep-footer-btn">Enregistrer</button>
                <button type="submit" name="recalculer" value="1" class="btn btn-warning ep-footer-btn" onclick="return confirm('Recalculer cette paie ? Les modifications seront sauvegardées avant le calcul.')">Recalculer la paie</button>
                <a href="/paie-me/paies/<?= $paie['periode_id'] ?>/lignes" class="btn btn-secondary ep-footer-btn">Retour</a>
            </div>
        </div>
    </form>

    <!-- Modale Gain -->
    <div class="custom-modal-overlay" id="gainModal" style="display:none;">
        <div class="custom-modal" style="max-width:780px;">
            <div class="custom-modal-header">
                <h4 style="margin:0;">Ajouter un gain</h4>
                <button type="button" class="btn btn-sm btn-secondary" onclick="document.getElementById('gainModal').style.display='none'" style="padding:0.2rem 0.5rem;">✕</button>
            </div>
            <div class="custom-modal-body">
                <div style="margin-bottom:0.75rem;">
                    <input type="text" id="gain_search" class="form-control" placeholder="Rechercher par code ou libellé..." onkeyup="filterGains()" style="width:100%;">
                </div>
                <div style="max-height:280px;overflow-y:auto;border:1px solid var(--border);border-radius:6px;">
                    <table class="edit-paie-table" style="border:none;margin:0;">
                        <thead>
                            <tr>
                                <th style="width:8%;">Code</th>
                                <th style="width:40%;">Libellé</th>
                                <th style="width:10%;">Type</th>
                                <th style="width:20%;">Plafond DGI</th>
                                <th style="width:22%;"></th>
                            </tr>
                        </thead>
                        <tbody id="gain_table_body">
                            <?php foreach ($rubriquesGains as $rg):
                                $plafondText = (!empty($rg['plafond_dgi_valeur']) ? number_format((float)$rg['plafond_dgi_valeur'], 2, ',', ' ') . ' ' . ($rg['plafond_dgi_type'] ?? '') : '—');
                            ?>
                            <tr class="gain-row" data-id="<?= $rg['id'] ?>" data-code="<?= htmlspecialchars($rg['code']) ?>" data-libelle="<?= htmlspecialchars($rg['libelle']) ?>" data-plafond="<?= htmlspecialchars($rg['plafond_dgi_valeur'] ?? '') ?>" data-plafond-type="<?= htmlspecialchars($rg['plafond_dgi_type'] ?? '') ?>" onclick="selectGainRow(this)">
                                <td class="code"><?= htmlspecialchars($rg['code']) ?></td>
                                <td><?= htmlspecialchars($rg['libelle']) ?></td>
                                <td style="text-align:center;font-size:0.72rem;"><?= htmlspecialchars($rg['type_montant'] ?? 'fixe') ?></td>
                                <td style="text-align:right;font-size:0.72rem;color:var(--text-muted);"><?= $plafondText ?></td>
                                <td style="text-align:center;"><button type="button" class="btn btn-primary btn-sm" onclick="event.stopPropagation();selectGainRow(this.closest('tr'))" style="font-size:0.68rem;padding:0.15rem 0.5rem;">Choisir</button></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div id="gain_info" style="margin-top:0.4rem;font-size:0.72rem;color:var(--text-muted);min-height:1.2rem;"></div>
                <div style="margin-top:0.75rem;display:flex;align-items:center;gap:0.75rem;">
                    <label style="font-size:0.8rem;white-space:nowrap;">Montant (DH)</label>
                    <input type="number" step="0.01" min="0" id="gain_montant_input" class="form-control" value="0" style="width:150px;">
                </div>
            </div>
            <div class="custom-modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('gainModal').style.display='none'">Annuler</button>
                <button type="button" class="btn btn-primary btn-sm" onclick="ajouterGainDepuisModal()">Ajouter</button>
            </div>
        </div>
    </div>

    <!-- Modale Retenue -->
    <div class="custom-modal-overlay" id="retenueModal" style="display:none;">
        <div class="custom-modal" style="max-width:780px;">
            <div class="custom-modal-header">
                <h4 style="margin:0;">Ajouter une retenue</h4>
                <button type="button" class="btn btn-sm btn-secondary" onclick="document.getElementById('retenueModal').style.display='none'" style="padding:0.2rem 0.5rem;">✕</button>
            </div>
            <div class="custom-modal-body">
                <div style="margin-bottom:0.75rem;">
                    <input type="text" id="retenue_search" class="form-control" placeholder="Rechercher par code ou libellé..." onkeyup="filterRetenues()" style="width:100%;">
                </div>
                <div style="max-height:280px;overflow-y:auto;border:1px solid var(--border);border-radius:6px;">
                    <table class="edit-paie-table" style="border:none;margin:0;">
                        <thead>
                            <tr>
                                <th style="width:8%;">Code</th>
                                <th style="width:40%;">Libellé</th>
                                <th style="width:10%;">Type</th>
                                <th style="width:20%;">Valeur défaut</th>
                                <th style="width:22%;"></th>
                            </tr>
                        </thead>
                        <tbody id="retenue_table_body">
                            <?php foreach ($rubriquesRetenues as $rr): ?>
                            <tr class="retenue-row" data-id="<?= $rr['id'] ?>" data-code="<?= htmlspecialchars($rr['code']) ?>" data-libelle="<?= htmlspecialchars($rr['libelle']) ?>" onclick="selectRetenueRow(this)">
                                <td class="code"><?= htmlspecialchars($rr['code']) ?></td>
                                <td><?= htmlspecialchars($rr['libelle']) ?></td>
                                <td style="text-align:center;font-size:0.72rem;"><?= htmlspecialchars($rr['type_montant'] ?? 'fixe') ?></td>
                                <td style="text-align:right;font-size:0.72rem;color:var(--text-muted);"><?= htmlspecialchars($rr['valeur_defaut'] ?? '—') ?></td>
                                <td style="text-align:center;"><button type="button" class="btn btn-primary btn-sm" onclick="event.stopPropagation();selectRetenueRow(this.closest('tr'))" style="font-size:0.68rem;padding:0.15rem 0.5rem;">Choisir</button></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div id="retenue_info" style="margin-top:0.4rem;font-size:0.72rem;color:var(--text-muted);min-height:1.2rem;"></div>
                <div style="margin-top:0.75rem;display:flex;align-items:center;gap:0.75rem;">
                    <label style="font-size:0.8rem;white-space:nowrap;">Montant (DH)</label>
                    <input type="number" step="0.01" min="0" id="retenue_montant_input" class="form-control" value="0" style="width:150px;">
                </div>
            </div>
            <div class="custom-modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('retenueModal').style.display='none'">Annuler</button>
                <button type="button" class="btn btn-primary btn-sm" onclick="ajouterRetenueDepuisModal()">Ajouter</button>
            </div>
        </div>
    </div>
</div>

<style>
.edit-paie-summary {
    padding:1.1rem 1.25rem;
    border-bottom:1px solid var(--border);
    display:flex;
    align-items:center;
    background:linear-gradient(180deg, rgba(139,92,246,0.06), transparent);
}
.ep-summary-item { display:flex; flex-direction:column; gap:0.15rem; flex:1; min-width:0; }
.ep-summary-item + .ep-summary-item { border-left:1px solid var(--border-subtle); padding-left:1.5rem; }
.ep-summary-label { font-size:0.72rem; font-weight:600; text-transform:uppercase; letter-spacing:0.06em; color:var(--text-muted); }
.ep-summary-value { font-size:1.05rem; font-weight:600; color:var(--text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }

.edit-paie-table { width:100%; border-collapse:collapse; }
.edit-paie-table th { padding:0.55rem 0.65rem; text-align:center; font-size:0.82rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--text-muted); border-bottom:1px solid var(--border); background:var(--bg-hover); }
.edit-paie-table td { padding:0.5rem 0.65rem; font-size:0.92rem; border-bottom:1px solid var(--border-subtle); }
.edit-paie-table .montant { text-align:right; white-space:nowrap; }
.edit-paie-table .taux { text-align:center; font-size:0.85rem; color:var(--text-muted); }
.edit-paie-table .unite { text-align:center; font-size:0.8rem; color:var(--text-muted); width:60px; }
.edit-paie-table .code { text-align:center; font-size:0.82rem; color:var(--text-muted); font-family:monospace; }
.edit-paie-table .section-header td { padding:0.55rem 0.75rem 0.35rem; font-size:0.78rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:var(--accent); border-bottom:none; background:rgba(139,92,246,0.12); border-left:3px solid var(--accent); }
.edit-paie-table .total-row td { padding:0.55rem 0.65rem; border-top:1px solid var(--border); font-weight:600; background:rgba(139,92,246,0.05); }
.edit-paie-table .recap-section td { border-top:1px solid var(--border); }
.edit-paie-table .net-row td { border-top:2px solid var(--accent); background:rgba(139,92,246,0.08); }
.form-control-inline { width:64px; padding:0.3rem 0.4rem; font-size:0.88rem; background:var(--bg-surface); border:1px solid var(--border); border-radius:4px; color:var(--text); text-align:right; }
.form-control-inline:focus { border-color:var(--accent); outline:none; box-shadow:0 0 0 2px rgba(139,92,246,0.25); }
.form-control-inline.over-limit { border-color:#ef4444; background:rgba(239,68,68,0.12); color:#fca5a5; }
.row-over-limit td { background:rgba(239,68,68,0.06); }
.montant.over-limit { color:#fca5a5; font-weight:600; }
.plafond-label { display:block; font-size:0.7rem; color:var(--text-muted); white-space:nowrap; margin-top:0.15rem; }
.info-icon { cursor:help; display:inline-flex; align-items:center; justify-content:center; width:18px; height:18px; border-radius:50%; background:rgba(139,92,246,0.18); color:var(--text-muted); font-size:0; vertical-align:middle; margin-left:0.3rem; position:relative; transition:background 0.2s; }
.info-icon:hover { background:var(--accent-hover); }
.info-icon svg { width:13px; height:13px; pointer-events:none; }
.edit-paie-table-wrap { overflow:visible !important; }
.formule-label { display:block; font-size:0.74rem; font-weight:400; color:var(--text-muted); margin-top:0.15rem; white-space:normal; }
.edit-paie-table .gains-cell { color:#34d399; }
.edit-paie-table .gains-cell strong { color:#34d399; }
.edit-paie-table .retenues-cell { color:#f87171; }
.edit-paie-table .retenues-cell strong { color:#f87171; }
.edit-paie-table .patronales-cell { color:#fbbf24; }
.edit-paie-table .patronales-cell strong { color:#fbbf24; }
.info-icon .info-tooltip { display:none; position:fixed; background:var(--bg-surface); color:var(--text); padding:0.5rem 0.75rem; border-radius:6px; font-size:0.82rem; font-weight:400; white-space:nowrap; box-shadow:0 4px 12px rgba(0,0,0,0.3); z-index:9999; line-height:1.4; pointer-events:none; }
.info-icon .info-tooltip::after { content:''; position:absolute; top:100%; left:50%; transform:translateX(-50%); border:5px solid transparent; border-top-color:var(--bg-surface); }
.info-icon:hover .info-tooltip { display:block; }
.form-select-inline { padding:0.3rem 0.4rem; font-size:0.85rem; background:var(--bg-surface); border:1px solid var(--border); border-radius:4px; color:var(--text); }
.form-select-inline:focus { border-color:var(--accent); outline:none; }

.gain-row { cursor:pointer; }
.gain-row:hover { background:var(--bg-hover); }
.gain-row.selected { background:rgba(139,92,246,0.18); }
.gain-row.selected td { color:var(--accent); font-weight:500; }
.retenue-row { cursor:pointer; }
.retenue-row:hover { background:var(--bg-hover); }
.retenue-row.selected { background:rgba(139,92,246,0.18); }
.retenue-row.selected td { color:var(--accent); font-weight:500; }

.ep-footer-btn { min-width:170px; justify-content:center; }

.ep-alerts { padding:0.9rem 1.25rem 0.25rem; display:flex; flex-direction:column; gap:0.5rem; }
.ep-alert { display:flex; align-items:flex-start; gap:0.6rem; padding:0.65rem 0.85rem; border-radius:8px; font-size:0.88rem; line-height:1.45; }
.ep-alert svg { flex-shrink:0; margin-top:0.1rem; }
.ep-alert-body { display:flex; flex-direction:column; gap:0.1rem; }
.ep-alert-body strong { font-weight:700; }
.ep-alert-body span { color:var(--text-muted); }
.ep-alert-danger { background:rgba(239,68,68,0.10); border:1px solid rgba(239,68,68,0.35); color:#f87171; }
.ep-alert-warning { background:rgba(245,158,11,0.10); border:1px solid rgba(245,158,11,0.35); color:#fbbf24; }
</style>

<script>
let gainIdx = <?= count($paieGains) ?>;
let gainSelected = null;

function filterGains() {
    const q = document.getElementById('gain_search').value.toLowerCase();
    document.querySelectorAll('.gain-row').forEach(r => {
        const code = r.dataset.code.toLowerCase();
        const libelle = r.dataset.libelle.toLowerCase();
        r.style.display = (code.includes(q) || libelle.includes(q)) ? '' : 'none';
    });
}

function selectGainRow(row) {
    document.querySelectorAll('.gain-row').forEach(r => r.classList.remove('selected'));
    row.classList.add('selected');
    gainSelected = {
        id: row.dataset.id,
        code: row.dataset.code,
        libelle: row.dataset.libelle,
        plafond: row.dataset.plafond,
        plafondType: row.dataset.plafondType,
    };
    const info = document.getElementById('gain_info');
    if (gainSelected.plafond) {
        info.textContent = 'Plafond DGI : ' + gainSelected.plafond + (gainSelected.plafondType ? ' / ' + gainSelected.plafondType : '');
    } else {
        info.textContent = 'Aucun plafond configuré';
    }
}

function ajouterGainDepuisModal() {
    if (!gainSelected) { alert('Veuillez sélectionner une rubrique.'); return; }
    const montant = parseFloat(document.getElementById('gain_montant_input').value) || 0;
    gainIdx++;
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td class="code">${gainSelected.code}</td>
        <td>${gainSelected.libelle}</td>
        <td></td>
        <td class="unite">DH</td>
        <td class="taux">—</td>
        <td class="montant gains-cell">
            <input type="hidden" name="gain_new_rubrique_id[${gainIdx}]" value="${gainSelected.id}">
            <input type="number" step="0.01" min="0" name="gain_new_montant[${gainIdx}]" class="form-control-inline" style="width:90px;" value="${montant.toFixed(2)}">
        </td>
        <td></td>
        <td></td>
        <td style="text-align:center;">
            <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()" style="padding:0.1rem 0.3rem;font-size:0.65rem;">✕</button>
        </td>
    `;
    document.getElementById('gains-container').before(tr);
    document.getElementById('gainModal').style.display = 'none';
    gainSelected = null;
    document.querySelectorAll('.gain-row').forEach(r => r.classList.remove('selected'));
    document.getElementById('gain_search').value = '';
    document.getElementById('gain_montant_input').value = 0;
    document.getElementById('gain_info').textContent = '';
    document.querySelectorAll('.gain-row').forEach(r => r.style.display = '');
}

let retenueIdx = <?= count($paieRetenues) ?>;
let retenueSelected = null;

function filterRetenues() {
    const q = document.getElementById('retenue_search').value.toLowerCase();
    document.querySelectorAll('.retenue-row').forEach(r => {
        const code = r.dataset.code.toLowerCase();
        const libelle = r.dataset.libelle.toLowerCase();
        r.style.display = (code.includes(q) || libelle.includes(q)) ? '' : 'none';
    });
}

function selectRetenueRow(row) {
    document.querySelectorAll('.retenue-row').forEach(r => r.classList.remove('selected'));
    row.classList.add('selected');
    retenueSelected = {
        id: row.dataset.id,
        code: row.dataset.code,
        libelle: row.dataset.libelle,
    };
    document.getElementById('retenue_info').textContent = 'Rubrique sélectionnée : ' + retenueSelected.code + ' — ' + retenueSelected.libelle;
}

function ajouterRetenueDepuisModal() {
    if (!retenueSelected) { alert('Veuillez sélectionner une rubrique.'); return; }
    const montant = parseFloat(document.getElementById('retenue_montant_input').value) || 0;
    retenueIdx++;
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td class="code">${retenueSelected.code}</td>
        <td>${retenueSelected.libelle}</td>
        <td></td>
        <td class="unite">DH</td>
        <td class="taux">—</td>
        <td></td>
        <td class="montant retenues-cell">
            <input type="hidden" name="retenue_new_rubrique_id[${retenueIdx}]" value="${retenueSelected.id}">
            <input type="number" step="0.01" min="0" name="retenue_new_montant[${retenueIdx}]" class="form-control-inline" style="width:90px;" value="${montant.toFixed(2)}">
        </td>
        <td></td>
        <td style="text-align:center;">
            <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()" style="padding:0.1rem 0.3rem;font-size:0.65rem;">✕</button>
        </td>
    `;
    document.getElementById('retenues-container').before(tr);
    document.getElementById('retenueModal').style.display = 'none';
    retenueSelected = null;
    document.querySelectorAll('.retenue-row').forEach(r => r.classList.remove('selected'));
    document.getElementById('retenue_search').value = '';
    document.getElementById('retenue_montant_input').value = 0;
    document.getElementById('retenue_info').textContent = '';
    document.querySelectorAll('.retenue-row').forEach(r => r.style.display = '');
}

document.querySelectorAll('.info-icon').forEach(function(icon) {
    var tip = icon.querySelector('.info-tooltip');
    if (!tip) return;
    icon.addEventListener('mouseenter', function() {
        var r = icon.getBoundingClientRect();
        tip.style.left = r.left + 'px';
        tip.style.top = (r.top - 6) + 'px';
        tip.style.transform = 'translateY(-100%)';
        tip.style.bottom = 'auto';
    });
});
</script>