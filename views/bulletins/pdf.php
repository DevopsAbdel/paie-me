<?php
$cfg = $template['config'] ?? [];
$couleur = $cfg['couleur_primaire'] ?? '#3b82f6';
$sections = $cfg['sections'] ?? [];
$netLabel = $cfg['net_label'] ?? 'Net à payer';
$netColor = $cfg['net_color'] ?? $couleur;

$values = [
    'salaire_base'          => (float)($b['salaire_base'] ?? 0),
    'prime_anciennete'      => (float)($b['prime_anciennete'] ?? 0),
    'indemnite_transport'   => (float)($b['indemnite_transport'] ?? 0),
    'indemnite_panier'      => (float)($b['indemnite_panier'] ?? 0),
    'indemnite_representation' => (float)($b['indemnite_representation'] ?? 0),
    'avantage_logement'     => (float)($b['avantage_logement'] ?? 0),
    'heures_sup'            => (float)($b['montant_heures_sup'] ?? 0),
    'salaire_brut'          => (float)($b['salaire_brut'] ?? 0),
    'sbi'                   => (float)($b['sbi'] ?? 0),
    'cnss_salariale'        => (float)($b['cnss_salariale'] ?? 0),
    'amo_salariale'         => (float)($b['amo_salariale'] ?? 0),
    'frais_professionnels'  => (float)($b['frais_professionnels'] ?? 0),
    'sni'                   => (float)($b['sni'] ?? 0),
    'ir'                    => (float)($b['ir'] ?? 0),
    'deductions_familiales' => (float)($b['deductions_familiales'] ?? 0),
    'cnss_patronale'        => (float)($b['cnss_patronale'] ?? 0),
    'amo_patronale'         => (float)($b['amo_patronale'] ?? 0),
    'allocation_familiale'  => (float)($b['allocation_familiale'] ?? 0),
    'prestation_sociale'    => (float)($b['prestation_sociale'] ?? 0),
    'taxe_formation'        => (float)($b['taxe_formation'] ?? 0),
    'net_a_payer'           => (float)($b['net_a_payer'] ?? 0),
];

$bases = [
    'cnss_salariale'       => min($values['salaire_brut'], 6000),
    'cnss_patronale'       => min($values['salaire_brut'], 6000),
    'amo_salariale'        => $values['salaire_brut'],
    'amo_patronale'        => $values['salaire_brut'],
    'frais_professionnels' => $values['sni'],
    'ir'                   => $values['sni'],
    'allocation_familiale' => $values['salaire_brut'],
    'prestation_sociale'   => $values['salaire_brut'],
    'taxe_formation'       => $values['salaire_brut'],
];

$taux = [
    'cnss_salariale'       => '4,48 %',
    'cnss_patronale'       => '8,98 %',
    'amo_salariale'        => '2,26 %',
    'amo_patronale'        => '4,52 %',
    'frais_professionnels' => '20 %',
    'allocation_familiale' => '6,40 %',
    'prestation_sociale'   => '0,99 %',
    'taxe_formation'       => '1,60 %',
];

$netBg = '#f3f4f6';
$netFg = '#111827';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #222; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { font-size: 18px; margin: 0; }
        .header p { margin: 2px 0; font-size: 11px; color: #555; }
        .infos { width: 100%; margin-bottom: 15px; }
        .infos td { vertical-align: top; padding: 4px 8px; }
        .infos-left { width: 50%; }
        .infos-right { width: 50%; text-align: right; }
        table.details { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table.details th { background: #e5e7eb; padding: 6px 8px; text-align: left; font-size: 11px; }
        table.details td { padding: 4px 8px; border-bottom: 1px solid #ddd; }
        table.details .right { text-align: right; }
        table.details .bold { font-weight: bold; }
        .net { background: <?= $netBg ?>; color: <?= $netFg ?>; padding: 10px 15px; text-align: right; font-size: 16px; font-weight: bold; border-radius: 4px; margin-top: 10px; }
        .footer { text-align: center; margin-top: 30px; font-size: 10px; color: #999; border-top: 1px solid #ddd; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header" style="display:flex; align-items:center; gap:15px; text-align:left; border-bottom:3px solid <?= $couleur ?>; padding-bottom:15px;">
        <div style="width:50px; height:50px; background:<?= $couleur ?>; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:20px; font-weight:700; color:#fff;">
            <?= strtoupper(mb_substr($b['raison_sociale'], 0, 2)) ?>
        </div>
        <div style="flex:1;">
            <h1 style="font-size:16px; margin:0; color:<?= $couleur ?>;"><?= htmlspecialchars($b['raison_sociale']) ?></h1>
            <p style="margin:2px 0; font-size:10px; color:#666;">
                ICE: <?= htmlspecialchars($b['ice']) ?> | IF: <?= htmlspecialchars($b['if_fiscal']) ?> | CNSS: <?= htmlspecialchars($b['cnss_societe']) ?>
            </p>
            <p style="margin:0; font-size:10px; color:#666;">
                <?= htmlspecialchars($b['adresse'] ?? '') ?>
                <?php if ($b['telephone']): ?> | Tél: <?= htmlspecialchars($b['telephone']) ?><?php endif; ?>
                <?php if ($b['email']): ?> | <?= htmlspecialchars($b['email']) ?><?php endif; ?>
            </p>
        </div>
    </div>

    <table class="infos">
        <tr>
            <td class="infos-left">
                <strong>Salarié:</strong> <?= htmlspecialchars($b['nom_famille'] . ' ' . $b['prenom']) ?><br>
                <strong>Matricule:</strong> <?= htmlspecialchars($b['matricule']) ?><br>
                <strong>CIN:</strong> <?= htmlspecialchars($b['cin']) ?><br>
                <strong>CNSS:</strong> <?= htmlspecialchars($b['cnss_num']) ?><br>
                <strong>Poste:</strong> <?= htmlspecialchars($b['fonction_nom'] ?? $b['poste']) ?><br>
                <strong>Date d'embauche:</strong> <?= $b['date_embauche'] ?>
            </td>
            <td class="infos-right">
                <strong>Période:</strong> <?= str_pad($b['mois'], 2, '0', STR_PAD_LEFT) . '/' . $b['annee'] ?><br>
                <strong>N° Bulletin:</strong> <?= htmlspecialchars($b['numero']) ?><br>
                <strong>Date:</strong> <?= $b['date_emission'] ?>
            </td>
        </tr>
    </table>

    <?php foreach ($sections as $section): ?>
    <h3><?= htmlspecialchars($section['titre']) ?></h3>
    <table class="details">
        <tr>
            <?php foreach ($section['colonnes'] as $col): ?>
            <th<?= $col !== 'Libellé' ? ' class="right"' : '' ?>><?= htmlspecialchars($col) ?></th>
            <?php endforeach; ?>
        </tr>
        <?php foreach ($section['lignes'] as $ligne): ?>
        <?php
            $code = $ligne['code'];
            $val = $values[$code] ?? 0;
            if ($val == 0 && !isset($values[$code])) continue;
            $isConditionnel = $ligne['conditionnel'] ?? false;
            if ($isConditionnel && $val == 0) continue;
        ?>
        <tr>
            <td><?= htmlspecialchars($ligne['label']) ?></td>
            <?php for ($i = 1; $i < count($section['colonnes']); $i++): ?>
                <?php if ($section['colonnes'][$i] === 'Base'): ?>
                    <td class="right"><?= isset($bases[$code]) ? number_format($bases[$code], 2, ',', ' ') : '—' ?></td>
                <?php elseif ($section['colonnes'][$i] === 'Taux'): ?>
                    <td class="right"><?= $taux[$code] ?? '—' ?></td>
                <?php else: ?>
                    <td class="right"><?= number_format($val, 2, ',', ' ') ?></td>
                <?php endif; ?>
            <?php endfor; ?>
        </tr>
        <?php endforeach; ?>
        <?php if (!empty($section['total'])): ?>
        <tr class="bold">
            <td><?= htmlspecialchars($section['total']['label']) ?></td>
            <?php for ($i = 1; $i < count($section['colonnes']); $i++): ?>
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
    <div class="footer" style="display:flex; justify-content:space-between; align-items:center;">
        <div style="display:flex; align-items:center; gap:6px;">
            <div style="width:24px; height:24px; background:<?= $couleur ?>; border-radius:4px; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:700; color:#fff;">
                <?= strtoupper(mb_substr($b['raison_sociale'], 0, 2)) ?>
            </div>
            <span><?= htmlspecialchars($b['raison_sociale']) ?></span>
        </div>
        <div style="text-align:right; font-size:9px;">
            <?= htmlspecialchars($b['adresse'] ?? '') ?>
            <?php if ($b['telephone']): ?> | Tél: <?= htmlspecialchars($b['telephone']) ?><?php endif; ?>
            <?php if ($b['email']): ?> | <?= htmlspecialchars($b['email']) ?><?php endif; ?>
            <br>
            ICE: <?= htmlspecialchars($b['ice']) ?> | IF: <?= htmlspecialchars($b['if_fiscal']) ?> | CNSS: <?= htmlspecialchars($b['cnss_societe']) ?>
            <?php if ($b['banque']): ?> | <?= htmlspecialchars($b['banque']) ?> <?php endif; ?>
            <?php if ($b['rib']): ?>| RIB: <?= htmlspecialchars($b['rib']) ?><?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</body>
</html>
