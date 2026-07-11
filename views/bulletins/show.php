<?php
$cfg = $template['config'] ?? [];
$couleur = $cfg['couleur_primaire'] ?? '#3b82f6';
$sections = $cfg['sections'] ?? [];
$netLabel = $cfg['net_label'] ?? 'Net à payer';
$netColor = $cfg['net_color'] ?? $couleur;

$plafond = (float)($cnssParams['plafond_cnss'] ?? 6000);
$tauxCnssS = (float)($cnssParams['taux_cnss_salarial'] ?? 4.48);
$tauxCnssP = (float)($cnssParams['taux_cnss_patronal'] ?? 8.98);
$tauxAmoS  = (float)($cnssParams['taux_amo_salarial'] ?? 2.26);
$tauxAmoP  = (float)($cnssParams['taux_amo_patronal'] ?? 4.11);

$salaireBase = (float)($b['salaire_base'] ?? 0);
$joursTrav = (int)($b['jours_travailles'] ?? 26);
$heuresMensuelles = 191;
$tauxHoraire = $joursTrav > 0 ? $salaireBase / $heuresMensuelles : 0;

$dateFin = date('Y-m-t', strtotime(sprintf('%04d-%02d-01', $b['annee'], $b['mois'])));
$tauxAncPct = 0;
if (!empty($b['date_embauche'])) {
    $annees = (int)(new DateTime($b['date_embauche']))->diff(new DateTime($dateFin))->format('%y');
    if ($annees >= 25) $tauxAncPct = 25;
    elseif ($annees >= 20) $tauxAncPct = 20;
    elseif ($annees >= 12) $tauxAncPct = 15;
    elseif ($annees >= 5) $tauxAncPct = 10;
    elseif ($annees >= 2) $tauxAncPct = 5;
}

$values = [
    '100' => (float)($b['salaire_base'] ?? 0),
    '204' => (float)($b['prime_anciennete'] ?? 0),
    '330' => (float)($b['indemnite_transport'] ?? 0),
    '346' => (float)($b['indemnite_panier'] ?? 0),
    '331' => (float)($b['indemnite_representation'] ?? 0),
    '340' => (float)($b['avantage_logement'] ?? 0),
    '201' => (float)($b['montant_hs_25'] ?? 0),
    '202' => (float)($b['montant_hs_50'] ?? 0),
    '203' => (float)($b['montant_hs_100'] ?? 0),
    'SB'  => (float)($b['salaire_brut'] ?? 0),
    '400' => (float)($b['cnss_salariale'] ?? 0),
    '410' => (float)($b['amo_salariale'] ?? 0),
    '420' => (float)($b['mutuelle'] ?? 0),
    '501' => (float)($b['frais_professionnels'] ?? 0),
    '502' => (float)($b['sni'] ?? 0),
    '600' => (float)($b['ir'] ?? 0),
    '601' => (float)($b['deductions_familiales'] ?? 0),
    '400P' => (float)($b['cnss_patronale'] ?? 0),
    '410P' => (float)($b['amo_patronale'] ?? 0),
];

$sbiAnnuel = (float)($b['sbi'] ?? 0) * 12;
$fpTaux = $sbiAnnuel <= 78000 ? '35 %' : '25 %';

$bases = [
    '100' => $salaireBase,
    '204' => $salaireBase,
    '201' => $tauxHoraire,
    '202' => $tauxHoraire,
    '203' => $tauxHoraire,
    '400' => min($values['SB'] ?? $values['100'] ?? 0, $plafond),
    '400P' => min($values['SB'] ?? $values['100'] ?? 0, $plafond),
    '410' => $values['SB'] ?? $values['100'] ?? 0,
    '410P' => $values['SB'] ?? $values['100'] ?? 0,
    '501' => (float)($b['sbi'] ?? 0),
    '600' => (float)($b['sni'] ?? 0),
];

$taux = [
    '100'  => number_format($tauxHoraire, 2, ',', ' ') . ' DH/h',
    '204'  => ($tauxAncPct > 0 ? $tauxAncPct . ' %' : '—'),
    '201'  => '25 %',
    '202'  => '50 %',
    '203'  => '100 %',
    '400'  => number_format($tauxCnssS, 2, ',', ' ') . ' %',
    '400P' => number_format($tauxCnssP, 2, ',', ' ') . ' %',
    '410'  => number_format($tauxAmoS, 2, ',', ' ') . ' %',
    '410P' => number_format($tauxAmoP, 2, ',', ' ') . ' %',
    '501'  => $fpTaux,
];
?>

<div class="card" style="position:relative;">
    <div class="card-header">
        <h3>Bulletin de paie — <?= htmlspecialchars($b['nom_famille'] . ' ' . $b['prenom']) ?></h3>
        <div>
            <a href="/paie-me/bulletins/<?= $b['id'] ?>/pdf" class="btn btn-primary btn-sm">Télécharger PDF</a>
            <a href="/paie-me/bulletins" class="btn btn-secondary btn-sm">Retour</a>
        </div>
    </div>

    <div style="display:flex; align-items:center; gap:1rem; padding:1rem; border-bottom:2px solid <?= $couleur ?>; margin-bottom:1rem;">
        <div style="width:60px; height:60px; background:<?= $couleur ?>; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:1.5rem; font-weight:700; color:#fff; flex-shrink:0;">
            <?= strtoupper(mb_substr($b['raison_sociale'], 0, 2)) ?>
        </div>
        <div style="flex:1;">
            <h4 style="color:<?= $couleur ?>; margin:0;"><?= htmlspecialchars($b['raison_sociale']) ?></h4>
            <p style="color:var(--text-muted); font-size:0.8125rem; margin:0.25rem 0 0 0;">
                ICE: <?= htmlspecialchars($b['ice']) ?> | IF: <?= htmlspecialchars($b['if_fiscal']) ?> | CNSS: <?= htmlspecialchars($b['cnss_societe']) ?>
                <?php if ($b['rc']): ?> | RC: <?= htmlspecialchars($b['rc']) ?><?php endif; ?>
            </p>
            <p style="color:var(--text-muted); font-size:0.8125rem; margin:0;">
                <?= htmlspecialchars($b['adresse'] ?? '') ?>
                <?php if ($b['telephone']): ?> | Tél: <?= htmlspecialchars($b['telephone']) ?><?php endif; ?>
                <?php if ($b['email']): ?> | <?= htmlspecialchars($b['email']) ?><?php endif; ?>
            </p>
        </div>
        <div style="text-align:right;">
            <h4 style="color:<?= $couleur ?>; margin:0;">Bulletin de paie</h4>
            <p style="color:var(--text-muted); font-size:0.875rem; margin:0.25rem 0 0 0;">
                N° <?= htmlspecialchars($b['numero']) ?><br>
                Période: <?= str_pad($b['mois'], 2, '0', STR_PAD_LEFT) . '/' . $b['annee'] ?><br>
                Émis le: <?= date('d/m/Y', strtotime($b['date_emission'])) ?>
            </p>
        </div>
    </div>

    <table style="width:100%; border-collapse:collapse;">
        <tr>
            <td style="padding:0.25rem 0.5rem; width:25%;"><strong>Salarié:</strong> <?= htmlspecialchars($b['nom_famille'] . ' ' . $b['prenom']) ?></td>
            <td style="padding:0.25rem 0.5rem; width:25%;"><strong>Durée de travail:</strong> <?= $joursTrav ?> j / <?= $heuresMensuelles ?> h</td>
            <td style="padding:0.25rem 0.5rem; width:25%;"><strong>Matricule:</strong> <?= htmlspecialchars($b['matricule']) ?></td>
            <td style="padding:0.25rem 0.5rem; width:25%;"><strong>Date d'embauche:</strong> <?= date('d/m/Y', strtotime($b['date_embauche'])) ?></td>
        </tr>
        <tr>
            <td style="padding:0.25rem 0.5rem;"><strong>CIN:</strong> <?= htmlspecialchars($b['cin']) ?></td>
            <td style="padding:0.25rem 0.5rem;"><strong>Situation:</strong> <?= ucfirst(htmlspecialchars($b['situation_familiale'] ?? 'Célibataire')) ?> | <?= (int)($b['nb_enfants'] ?? 0) ?> enfant(s)</td>
            <td style="padding:0.25rem 0.5rem;"><strong>CNSS:</strong> <?= htmlspecialchars($b['cnss_num']) ?></td>
            <td style="padding:0.25rem 0.5rem;"><strong>Poste:</strong> <?= htmlspecialchars($b['fonction_nom'] ?? $b['poste']) ?></td>
        </tr>
    </table>

    <hr style="border-color:var(--border); margin:0.5rem 0;">

    <?php foreach ($sections as $section): ?>
    <h4 style="margin:1rem 0 0.5rem 1rem; color:<?= $couleur ?>;"><?= htmlspecialchars($section['titre']) ?></h4>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <?php foreach ($section['colonnes'] as $col): ?>
                    <th><?= htmlspecialchars($col) ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($section['lignes'] as $ligne): ?>
                <?php
                    $code = $ligne['code'];
                    $val = $values[$code] ?? 0;
                    $isConditionnel = $ligne['conditionnel'] ?? false;
                    if ($isConditionnel && $val == 0) continue;
                ?>
                <tr>
                    <td style="color:var(--text-muted); font-size:0.8rem;"><?= htmlspecialchars($code) ?></td>
                    <td><?= htmlspecialchars($ligne['label']) ?></td>
                    <?php for ($i = 2; $i < count($section['colonnes']); $i++): ?>
                        <?php if ($section['colonnes'][$i] === 'Base'): ?>
                            <td style="text-align:right;"><?= isset($bases[$code]) ? number_format($bases[$code], 2, ',', ' ') : '—' ?></td>
                        <?php elseif ($section['colonnes'][$i] === 'Taux'): ?>
                            <td style="text-align:right;"><?= $taux[$code] ?? '—' ?></td>
                        <?php else: ?>
                            <td style="text-align:right;"><?= number_format($val, 2, ',', ' ') ?></td>
                        <?php endif; ?>
                    <?php endfor; ?>
                </tr>
                <?php endforeach; ?>
                <?php if (!empty($section['total'])): ?>
                <tr style="font-weight:bold; border-top:2px solid <?= $couleur ?>;">
                    <td></td>
                    <td><?= htmlspecialchars($section['total']['label']) ?></td>
                    <?php for ($i = 2; $i < count($section['colonnes']); $i++): ?>
                        <?php if ($section['colonnes'][$i] === 'Base' || $section['colonnes'][$i] === 'Taux'): ?>
                            <td></td>
                        <?php else: ?>
                            <td style="text-align:right;"><?= number_format($values[$section['total']['code']] ?? 0, 2, ',', ' ') ?></td>
                        <?php endif; ?>
                    <?php endfor; ?>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php endforeach; ?>

    <div style="background:var(--bg-primary); border-radius:8px; padding:1rem; margin-top:1rem; display:flex; justify-content:space-between; align-items:center;">
        <strong style="font-size:1.125rem;"><?= htmlspecialchars($netLabel) ?></strong>
        <strong style="font-size:1.25rem; color:<?= $netColor ?>;"><?= number_format($b['net_a_payer'], 2, ',', ' ') ?> MAD</strong>
    </div>

    <h4 style="margin:1.5rem 0 0.5rem 1rem; color:<?= $couleur ?>;">Cumuls annuels</h4>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Libellé</th>
                    <th style="text-align:right;">Cumul</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Salaire brut</td>
                    <td style="text-align:right;"><?= number_format($cumuls['cumul_brut'], 2, ',', ' ') ?></td>
                </tr>
                <tr>
                    <td>CNSS (part salariale)</td>
                    <td style="text-align:right;"><?= number_format($cumuls['cumul_cnss'], 2, ',', ' ') ?></td>
                </tr>
                <tr>
                    <td>AMO (part salariale)</td>
                    <td style="text-align:right;"><?= number_format($cumuls['cumul_amo'], 2, ',', ' ') ?></td>
                </tr>
                <tr>
                    <td>Mutuelle</td>
                    <td style="text-align:right;"><?= number_format($cumuls['cumul_mutuelle'], 2, ',', ' ') ?></td>
                </tr>
                <tr>
                    <td>Frais professionnels</td>
                    <td style="text-align:right;"><?= number_format($cumuls['cumul_fp'], 2, ',', ' ') ?></td>
                </tr>
                <tr>
                    <td>Impôt sur le revenu (IR)</td>
                    <td style="text-align:right;"><?= number_format($cumuls['cumul_ir'], 2, ',', ' ') ?></td>
                </tr>
                <tr>
                    <td>Salaire net imposable (SNI)</td>
                    <td style="text-align:right;"><?= number_format($cumuls['cumul_sni'], 2, ',', ' ') ?></td>
                </tr>
                <tr style="font-weight:bold; border-top:2px solid <?= $couleur ?>;">
                    <td>Net à payer</td>
                    <td style="text-align:right;"><?= number_format($cumuls['cumul_net'], 2, ',', ' ') ?></td>
                </tr>
                <tr>
                    <td>Jours déclarés</td>
                    <td style="text-align:right;"><?= number_format($cumuls['cumul_jours'], 0, ',', ' ') ?> jour(s)</td>
                </tr>
                <?php if ($cumuls['cumul_transport'] > 0 || $cumuls['cumul_panier'] > 0 || $cumuls['cumul_representation'] > 0): ?>
                <tr style="border-top:1px solid var(--border);">
                    <td colspan="2" style="font-weight:bold; color:<?= $couleur ?>; padding-top:0.75rem;">Indemnités</td>
                </tr>
                <?php if ($cumuls['cumul_transport'] > 0): ?>
                <tr>
                    <td>Indemnité de transport</td>
                    <td style="text-align:right;"><?= number_format($cumuls['cumul_transport'], 2, ',', ' ') ?></td>
                </tr>
                <?php endif; ?>
                <?php if ($cumuls['cumul_panier'] > 0): ?>
                <tr>
                    <td>Indemnité de panier</td>
                    <td style="text-align:right;"><?= number_format($cumuls['cumul_panier'], 2, ',', ' ') ?></td>
                </tr>
                <?php endif; ?>
                <?php if ($cumuls['cumul_representation'] > 0): ?>
                <tr>
                    <td>Indemnité de représentation</td>
                    <td style="text-align:right;"><?= number_format($cumuls['cumul_representation'], 2, ',', ' ') ?></td>
                </tr>
                <?php endif; ?>
                <?php endif; ?>
                <tr style="border-top:1px solid var(--border);">
                    <td colspan="2" style="font-weight:bold; color:<?= $couleur ?>; padding-top:0.75rem;">Congés</td>
                </tr>
                <tr>
                    <td>Jours de congé consommés</td>
                    <td style="text-align:right;"><?= number_format($cumuls['jours_conge_consommes'], 1, ',', ' ') ?> jour(s)</td>
                </tr>
                <tr>
                    <td>Jours de congé restants</td>
                    <td style="text-align:right;"><?= number_format($cumuls['jours_conge_restants'], 1, ',', ' ') ?> jour(s)</td>
                </tr>
            </tbody>
        </table>
    </div>

</div>
