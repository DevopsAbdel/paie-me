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
                Émis le: <?= $b['date_emission'] ?>
            </p>
        </div>
    </div>

    <table>
        <tr>
            <td style="padding:0.5rem 1rem;">
                <strong>Salarié:</strong> <?= htmlspecialchars($b['nom_famille'] . ' ' . $b['prenom']) ?><br>
                <strong>Matricule:</strong> <?= htmlspecialchars($b['matricule']) ?><br>
                <strong>CIN:</strong> <?= htmlspecialchars($b['cin']) ?><br>
                <strong>CNSS:</strong> <?= htmlspecialchars($b['cnss_num']) ?><br>
                <strong>Poste:</strong> <?= htmlspecialchars($b['fonction_nom'] ?? $b['poste']) ?><br>
                <strong>Date d'embauche:</strong> <?= $b['date_embauche'] ?>
            </td>
            <td style="padding:0.5rem 1rem; text-align:right;">
                <strong>Durée de travail:</strong> <?= $joursTrav ?> jour(s) / <?= $heuresMensuelles ?> heures<br>
                <?php if ((float)($b['jours_conge'] ?? 0) > 0): ?>
                <strong>Jours congé:</strong> <?= number_format((float)$b['jours_conge'], 1, ',', ' ') ?> jour(s)<br>
                <?php endif; ?>
                <?php if ((float)($b['jours_feries'] ?? 0) > 0): ?>
                <strong>Jours fériés:</strong> <?= number_format((float)$b['jours_feries'], 1, ',', ' ') ?> jour(s)<br>
                <?php endif; ?>
                <strong>Situation:</strong> <?= htmlspecialchars($b['situation_familiale'] ?? 'Célibataire') ?>
                | <?= (int)($b['nb_enfants'] ?? 0) ?> enfant(s)
            </td>
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

    <div style="margin-top:2rem; padding-top:1rem; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:flex-start;">
        <div style="width:45%;">
            <p style="margin:0 0 2px 0; color:var(--text-muted);">Fait à <?= htmlspecialchars($b['ville'] ?? '_______') ?>, le <?= date('d/m/Y') ?></p>
            <div style="margin-top:20px; text-align:center;">
                <div style="border-bottom:1px solid var(--text-muted); width:140px; margin:0 auto 4px;"></div>
                <strong style="font-size:0.8125rem;">Cachet et signature<br>du responsable RH</strong>
            </div>
        </div>
        <div style="width:45%; text-align:right;">
            <div style="margin-top:20px; text-align:center; float:right;">
                <div style="border-bottom:1px solid var(--text-muted); width:140px; margin:0 auto 4px;"></div>
                <strong style="font-size:0.8125rem;">Émargement<br>du salarié</strong>
            </div>
        </div>
    </div>

</div>
