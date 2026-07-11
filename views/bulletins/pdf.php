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
$fpTaux = $sbiAnnuel <= 78000 ? '35%' : '25%';

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
    '204'  => ($tauxAncPct > 0 ? $tauxAncPct . '%' : '—'),
    '201'  => '25%',
    '202'  => '50%',
    '203'  => '100%',
    '400'  => number_format($tauxCnssS, 2, ',', ' ') . '%',
    '400P' => number_format($tauxCnssP, 2, ',', ' ') . '%',
    '410'  => number_format($tauxAmoS, 2, ',', ' ') . '%',
    '410P' => number_format($tauxAmoP, 2, ',', ' ') . '%',
    '501'  => $fpTaux,
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; color: #1e293b; line-height: 1.3; margin: 8mm 15mm; }
        .header { display: flex; align-items: center; gap: 10px; padding-bottom: 8px; margin-bottom: 6px; border-bottom: 2px solid <?= $couleur ?>; }
        .header-logo { width: 36px; height: 36px; background: <?= $couleur ?>; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; color: #fff; flex-shrink: 0; }
        .header-info { flex: 1; }
        .header-info h1 { font-size: 12px; margin: 0; color: <?= $couleur ?>; }
        .header-info p { margin: 1px 0; font-size: 8px; color: #666; }
        .header-right { text-align: right; }
        .header-right h2 { font-size: 11px; margin: 0; color: <?= $couleur ?>; }
        .header-right p { margin: 1px 0; font-size: 8px; color: #666; }
        .infos { display: flex; gap: 10px; margin-bottom: 6px; }
        .infos-left { flex: 1; }
        .infos-right { flex: 1; text-align: right; }
        .infos p { margin: 1px 0; font-size: 8px; }
        table.details { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        table.details th { background: #e5e7eb; padding: 3px 5px; text-align: left; font-size: 8px; text-transform: uppercase; letter-spacing: 0.03em; }
        table.details td { padding: 2px 5px; border-bottom: 0.5px solid #e5e7eb; font-size: 8.5px; }
        table.details .right { text-align: right; }
        table.details .code { text-align: left; color: #666; font-size: 7.5px; font-family: monospace; }
        table.details .bold { font-weight: bold; }
        table.details .total-row td { border-top: 1.5px solid <?= $couleur ?>; font-weight: bold; padding-top: 3px; }
        .section-title { font-size: 9px; font-weight: 700; color: <?= $couleur ?>; margin: 5px 0 2px 0; text-transform: uppercase; letter-spacing: 0.04em; }
        .net { background: #f3f4f6; padding: 6px 10px; text-align: right; font-size: 12px; font-weight: bold; border-radius: 3px; margin-top: 4px; }
        .footer { display: flex; justify-content: space-between; align-items: center; margin-top: 8px; padding-top: 5px; border-top: 0.5px solid #ddd; font-size: 7px; color: #999; }
        .footer-logo { width: 18px; height: 18px; background: <?= $couleur ?>; border-radius: 3px; display: flex; align-items: center; justify-content: center; font-size: 7px; font-weight: 700; color: #fff; }
    </style>
</head>
<body>
    <table style="width:100%; border-collapse:collapse; margin-bottom:6px; border-bottom:2px solid <?= $couleur ?>;">
        <tr>
            <td style="vertical-align:middle; padding:0 8px 8px 0; width:36px;">
                <div style="width:36px; height:36px; background:<?= $couleur ?>; border-radius:6px; text-align:center; line-height:36px; font-size:14px; font-weight:700; color:#fff;"><?= strtoupper(mb_substr($b['raison_sociale'], 0, 2)) ?></div>
            </td>
            <td style="vertical-align:middle; padding-bottom:8px;">
                <h1 style="font-size:12px; margin:0; color:<?= $couleur ?>;"><?= htmlspecialchars($b['raison_sociale']) ?></h1>
                <p style="margin:1px 0; font-size:8px; color:#666;">ICE: <?= htmlspecialchars($b['ice']) ?> | IF: <?= htmlspecialchars($b['if_fiscal']) ?> | CNSS: <?= htmlspecialchars($b['cnss_societe']) ?></p>
                <p style="margin:1px 0; font-size:8px; color:#666;"><?= htmlspecialchars($b['adresse'] ?? '') ?><?php if ($b['telephone']): ?> | Tél: <?= htmlspecialchars($b['telephone']) ?><?php endif; ?><?php if ($b['email']): ?> | <?= htmlspecialchars($b['email']) ?><?php endif; ?></p>
            </td>
            <td style="vertical-align:middle; padding-bottom:8px; text-align:right; white-space:nowrap;">
                <h2 style="font-size:11px; margin:0; color:<?= $couleur ?>;"><?= htmlspecialchars($b['numero']) ?></h2>
                <p style="margin:1px 0; font-size:8px; color:#666;"><strong>Période:</strong> <?= str_pad($b['mois'], 2, '0', STR_PAD_LEFT) . '/' . $b['annee'] ?></p>
                <p style="margin:1px 0; font-size:8px; color:#666;">Émis le: <?= date('d/m/Y', strtotime($b['date_emission'])) ?></p>
            </td>
        </tr>
    </table>

    <div style="text-align:center; font-size:20px; font-weight:bold; color:<?= $couleur ?>; margin:4px 0 12px 0; padding:8px 0; border-bottom:2px solid <?= $couleur ?>; text-transform:uppercase; letter-spacing:0.1em;">BULLETIN DE PAIE</div>

    <table style="width:100%; border-collapse:collapse; margin-bottom:6px; font-size:8px;">
        <tr>
            <td style="padding:1px 0; width:25%;"><strong>Salarié:</strong> <?= htmlspecialchars($b['nom_famille'] . ' ' . $b['prenom']) ?></td>
            <td style="padding:1px 0; width:25%;"><strong>Durée de travail:</strong> <?= $joursTrav ?> j / <?= $heuresMensuelles ?> h</td>
            <td style="padding:1px 0; width:25%;"><strong>Matricule:</strong> <?= htmlspecialchars($b['matricule']) ?></td>
            <td style="padding:1px 0; width:25%;"><strong>Date d'embauche:</strong> <?= date('d/m/Y', strtotime($b['date_embauche'])) ?></td>
        </tr>
        <tr>
            <td style="padding:1px 0;"><strong>CIN:</strong> <?= htmlspecialchars($b['cin']) ?></td>
            <td style="padding:1px 0;"><strong>Situation:</strong> <?= ucfirst(htmlspecialchars($b['situation_familiale'] ?? 'Célibataire')) ?> | <?= (int)($b['nb_enfants'] ?? 0) ?> enfant(s)</td>
            <td style="padding:1px 0;"><strong>CNSS:</strong> <?= htmlspecialchars($b['cnss_num']) ?></td>
            <td style="padding:1px 0;"><strong>Poste:</strong> <?= htmlspecialchars($b['fonction_nom'] ?? $b['poste']) ?></td>
        </tr>
    </table>

    <div style="border-bottom:2px solid <?= $couleur ?>; margin-bottom:10px;"></div>

    <?php foreach ($sections as $section): ?>
    <p class="section-title" style="margin-top:10px;"><?= htmlspecialchars($section['titre']) ?></p>
    <table class="details">
        <tr>
            <?php foreach ($section['colonnes'] as $col): ?>
            <th<?= !in_array($col, ['Libellé', 'Code']) ? ' class="right"' : '' ?>><?= htmlspecialchars($col) ?></th>
            <?php endforeach; ?>
        </tr>
        <?php foreach ($section['lignes'] as $ligne): ?>
        <?php
            $code = $ligne['code'];
            $val = $values[$code] ?? 0;
            $isConditionnel = $ligne['conditionnel'] ?? false;
            if ($isConditionnel && $val == 0) continue;
        ?>
        <tr>
            <td class="code"><?= htmlspecialchars($code) ?></td>
            <td><?= htmlspecialchars($ligne['label']) ?></td>
            <?php for ($i = 2; $i < count($section['colonnes']); $i++): ?>
                <?php if ($section['colonnes'][$i] === 'Base'): ?>
                    <td class="right"><?= isset($bases[$code]) ? number_format($bases[$code], 2, ',', ' ') : '' ?></td>
                <?php elseif ($section['colonnes'][$i] === 'Taux'): ?>
                    <td class="right"><?= $taux[$code] ?? '' ?></td>
                <?php else: ?>
                    <td class="right"><?= number_format($val, 2, ',', ' ') ?></td>
                <?php endif; ?>
            <?php endfor; ?>
        </tr>
        <?php endforeach; ?>
        <?php if (!empty($section['total'])): ?>
        <tr class="total-row">
            <td></td>
            <td><?= htmlspecialchars($section['total']['label']) ?></td>
            <?php for ($i = 2; $i < count($section['colonnes']); $i++): ?>
                <?php if ($section['colonnes'][$i] === 'Base' || $section['colonnes'][$i] === 'Taux'): ?>
                    <td class="right"></td>
                <?php else: ?>
                    <td class="right"><?= number_format($values[$section['total']['code']] ?? 0, 2, ',', ' ') ?></td>
                <?php endif; ?>
            <?php endfor; ?>
        </tr>
        <?php endif; ?>
    </table>
    <?php endforeach; ?>

    <div class="net"><?= htmlspecialchars($netLabel) ?> : <?= number_format($b['net_a_payer'], 2, ',', ' ') ?> MAD</div>

    <div class="section-title" style="margin-top:12px;">Cumuls annuels</div>
    <table style="width:100%; border-collapse:collapse; font-size:7px; margin-bottom:4px;">
        <tr>
            <td style="padding:1.5px 3px; width:20%;"><strong>Brut:</strong> <?= number_format($cumuls['cumul_brut'], 2, ',', ' ') ?></td>
            <td style="padding:1.5px 3px; width:20%;"><strong>CNSS:</strong> <?= number_format($cumuls['cumul_cnss'], 2, ',', ' ') ?></td>
            <td style="padding:1.5px 3px; width:20%;"><strong>AMO:</strong> <?= number_format($cumuls['cumul_amo'], 2, ',', ' ') ?></td>
            <td style="padding:1.5px 3px; width:20%;"><strong>FP:</strong> <?= number_format($cumuls['cumul_fp'], 2, ',', ' ') ?></td>
            <td style="padding:1.5px 3px; width:20%;"><strong>IR:</strong> <?= number_format($cumuls['cumul_ir'], 2, ',', ' ') ?></td>
        </tr>
        <tr>
            <td style="padding:1.5px 3px;"><strong>Mutuelle:</strong> <?= number_format($cumuls['cumul_mutuelle'], 2, ',', ' ') ?></td>
            <?php if ($cumuls['cumul_transport'] > 0): ?>
            <td style="padding:1.5px 3px;"><strong>Transport:</strong> <?= number_format($cumuls['cumul_transport'], 2, ',', ' ') ?></td>
            <?php endif; ?>
            <?php if ($cumuls['cumul_panier'] > 0): ?>
            <td style="padding:1.5px 3px;"><strong>Panier:</strong> <?= number_format($cumuls['cumul_panier'], 2, ',', ' ') ?></td>
            <?php endif; ?>
            <?php if ($cumuls['cumul_representation'] > 0): ?>
            <td style="padding:1.5px 3px;"><strong>Repr.:</strong> <?= number_format($cumuls['cumul_representation'], 2, ',', ' ') ?></td>
            <?php endif; ?>
            <td style="padding:1.5px 3px;"><strong>SNI:</strong> <?= number_format($cumuls['cumul_sni'], 2, ',', ' ') ?></td>
        </tr>
        <tr style="border-top:1px solid #999;">
            <td colspan="2" style="padding:1.5px 3px; font-weight:bold; font-size:8px;"><?= htmlspecialchars($netLabel) ?> cumulé: <?= number_format($cumuls['cumul_net'], 2, ',', ' ') ?> MAD</td>
            <td style="padding:1.5px 3px;"><strong>Jrs déclarés:</strong> <?= number_format($cumuls['cumul_jours'], 0, ',', ' ') ?></td>
            <td style="padding:1.5px 3px;"><strong>Congés:</strong> <?= number_format($cumuls['jours_conge_consommes'], 1, ',', ' ') ?> / <?= number_format($cumuls['jours_conge_restants'], 1, ',', ' ') ?></td>
            <td>&nbsp;</td>
        </tr>
    </table>

    <div style="margin-top:16px; font-size:7px; color:#333; padding-top:6px; border-top:0.5px solid #ccc;">
        <p style="margin:0 0 2px 0;">Fait à <?= htmlspecialchars($b['ville'] ?? '_______') ?>, le <?= date('d/m/Y') ?></p>
        <table style="width:100%; margin-top:14px; border-collapse:collapse; font-size:7px;">
            <tr>
                <td style="width:50%; text-align:center; vertical-align:bottom; padding:0;">
                    <strong>Cachet et signature<br>du responsable RH</strong>
                    <div style="border-bottom:0.5px solid #999; width:130px; margin:3px auto 0;"></div>
                </td>
                <td style="width:50%; text-align:center; vertical-align:bottom; padding:0;">
                    <strong>Émargement<br>du salarié</strong>
                    <div style="border-bottom:0.5px solid #999; width:130px; margin:3px auto 0;"></div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
