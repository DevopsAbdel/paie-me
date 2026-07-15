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
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Aperçu A4 — <?= htmlspecialchars($b['raison_sociale']) ?></title>
    <style>
        @media print {
            body { margin: 0; }
            .no-print { display: none !important; }
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', 'Segoe UI', sans-serif; font-size: 9px; color: #1e293b; line-height: 1.3; background: #f1f5f9; display: flex; justify-content: center; padding: 1rem; }
        .a4 { width: 210mm; min-height: 297mm; background: #fff; padding: 12mm 18mm; box-shadow: 0 2px 16px rgba(0,0,0,0.15); margin: 0 auto; }
        .toolbar { position: fixed; top: 1rem; right: 1rem; z-index: 100; display: flex; gap: 0.5rem; }
        .toolbar button { padding: 0.5rem 1rem; border: none; border-radius: 8px; font-size: 0.85rem; font-weight: 600; cursor: pointer; }
        .btn-print { background: #3b82f6; color: #fff; }
        .btn-close { background: #334155; color: #fff; }
        table.section { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        table.section th { background: #e5e7eb; padding: 3px 5px; text-align: left; font-size: 8px; text-transform: uppercase; letter-spacing: 0.03em; }
        table.section td { padding: 2px 5px; border-bottom: 0.5px solid #e5e7eb; font-size: 8.5px; }
        table.section .right { text-align: right; }
        table.section .code { text-align: left; color: #666; font-size: 7.5px; font-family: monospace; }
        table.section .total-row td { border-top: 1.5px solid <?= $couleur ?>; font-weight: bold; padding-top: 3px; }
        .section-title { font-size: 9px; font-weight: 700; color: <?= $couleur ?>; margin: 5px 0 2px 0; text-transform: uppercase; letter-spacing: 0.04em; }
        .net { background: #f3f4f6; padding: 6px 10px; text-align: right; font-size: 12px; font-weight: bold; border-radius: 3px; margin-top: 4px; }
        .badge-preview { position: absolute; top: 8mm; right: 18mm; background: #eab308; color: #000; font-size: 8px; font-weight: 700; padding: 2px 8px; border-radius: 4px; text-transform: uppercase; letter-spacing: 0.05em; }
    </style>
</head>
<body>
    <div class="toolbar no-print">
        <button class="btn-print" onclick="window.print()">Imprimer / PDF</button>
        <button class="btn-close" onclick="window.close()">Fermer</button>
    </div>

    <div class="a4" style="position:relative;">
        <div class="badge-preview">Aperçu — données fictives</div>

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
        <table class="section">
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

        <div class="net" style="border-left:4px solid <?= $netColor ?>;"><?= htmlspecialchars($netLabel) ?> : <?= number_format($b['net_a_payer'], 2, ',', ' ') ?> MAD</div>

        <?php if (!empty($cfg['show_footer'])): ?>
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
        <?php endif; ?>
    </div>
</body>
</html>
