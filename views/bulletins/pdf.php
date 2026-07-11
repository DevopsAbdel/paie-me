<?php
$cfg = $template['config'] ?? [];
$couleur = $cfg['couleur_primaire'] ?? '#3b82f6';
$sections = $cfg['sections'] ?? [];
$netLabel = $cfg['net_label'] ?? 'Net à payer';
$netColor = $cfg['net_color'] ?? $couleur;

$values = [
    '100' => (float)($b['salaire_base'] ?? 0),
    '204' => (float)($b['prime_anciennete'] ?? 0),
    '330' => (float)($b['indemnite_transport'] ?? 0),
    '346' => (float)($b['indemnite_panier'] ?? 0),
    '331' => (float)($b['indemnite_representation'] ?? 0),
    '340' => (float)($b['avantage_logement'] ?? 0),
    '201' => 0,
    '202' => 0,
    '203' => (float)($b['montant_heures_sup'] ?? 0),
    'SB'  => (float)($b['salaire_brut'] ?? 0),
    '400' => (float)($b['cnss_salariale'] ?? 0),
    '410' => (float)($b['amo_salariale'] ?? 0),
    '420' => 0,
    '501' => (float)($b['frais_professionnels'] ?? 0),
    '502' => (float)($b['sni'] ?? 0),
    '600' => (float)($b['ir'] ?? 0),
    '601' => (float)($b['deductions_familiales'] ?? 0),
    '400P' => (float)($b['cnss_patronale'] ?? 0),
    '410P' => (float)($b['amo_patronale'] ?? 0),
];

$bases = [
    '400' => min($values['SB'] ?? $values['100'] ?? 0, 6000),
    '400P' => min($values['SB'] ?? $values['100'] ?? 0, 6000),
    '410' => $values['SB'] ?? $values['100'] ?? 0,
    '410P' => $values['SB'] ?? $values['100'] ?? 0,
    '501' => $values['502'] ?? 0,
    '600' => $values['502'] ?? 0,
];

$taux = [
    '400'  => '4,48%',
    '400P' => '8,98%',
    '410'  => '2,26%',
    '410P' => '4,52%',
    '501'  => '20%',
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; color: #1e293b; line-height: 1.3; }
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
    <div class="header">
        <div class="header-logo"><?= strtoupper(mb_substr($b['raison_sociale'], 0, 2)) ?></div>
        <div class="header-info">
            <h1><?= htmlspecialchars($b['raison_sociale']) ?></h1>
            <p>ICE: <?= htmlspecialchars($b['ice']) ?> | IF: <?= htmlspecialchars($b['if_fiscal']) ?> | CNSS: <?= htmlspecialchars($b['cnss_societe']) ?></p>
            <p><?= htmlspecialchars($b['adresse'] ?? '') ?><?php if ($b['telephone']): ?> | Tél: <?= htmlspecialchars($b['telephone']) ?><?php endif; ?><?php if ($b['email']): ?> | <?= htmlspecialchars($b['email']) ?><?php endif; ?></p>
        </div>
        <div class="header-right">
            <h2>Bulletin de paie</h2>
            <p>N° <?= htmlspecialchars($b['numero']) ?></p>
            <p>Période: <?= str_pad($b['mois'], 2, '0', STR_PAD_LEFT) . '/' . $b['annee'] ?></p>
            <p>Émis le: <?= $b['date_emission'] ?></p>
        </div>
    </div>

    <div class="infos">
        <div class="infos-left">
            <p><strong>Salarié:</strong> <?= htmlspecialchars($b['nom_famille'] . ' ' . $b['prenom']) ?></p>
            <p><strong>Matricule:</strong> <?= htmlspecialchars($b['matricule']) ?></p>
            <p><strong>CIN:</strong> <?= htmlspecialchars($b['cin']) ?> | <strong>CNSS:</strong> <?= htmlspecialchars($b['cnss_num']) ?></p>
            <p><strong>Poste:</strong> <?= htmlspecialchars($b['fonction_nom'] ?? $b['poste']) ?></p>
        </div>
        <div class="infos-right">
            <p><strong>Date embauche:</strong> <?= $b['date_embauche'] ?></p>
            <p><strong>Situation:</strong> <?= htmlspecialchars($b['situation_familiale'] ?? '') ?> | <?= (int)($b['nb_enfants'] ?? 0) ?> enfant(s)</p>
        </div>
    </div>

    <?php foreach ($sections as $section): ?>
    <p class="section-title"><?= htmlspecialchars($section['titre']) ?></p>
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

    <?php if ($cfg['show_footer'] ?? true): ?>
    <div class="footer">
        <div style="display:flex; align-items:center; gap:4px;">
            <div class="footer-logo"><?= strtoupper(mb_substr($b['raison_sociale'], 0, 2)) ?></div>
            <span><?= htmlspecialchars($b['raison_sociale']) ?></span>
        </div>
        <div style="text-align:right;">
            <?= htmlspecialchars($b['adresse'] ?? '') ?><?php if ($b['telephone']): ?> | Tél: <?= htmlspecialchars($b['telephone']) ?><?php endif; ?>
            <?php if ($b['email']): ?> | <?= htmlspecialchars($b['email']) ?><?php endif; ?>
            | ICE: <?= htmlspecialchars($b['ice']) ?> | IF: <?= htmlspecialchars($b['if_fiscal']) ?>
            <?php if ($b['banque']): ?> | <?= htmlspecialchars($b['banque']) ?><?php endif; ?>
            <?php if ($b['rib']): ?> | RIB: <?= htmlspecialchars($b['rib']) ?><?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</body>
</html>
