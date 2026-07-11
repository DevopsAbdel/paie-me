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
                    if ($val == 0 && !isset($values[$code])) continue;
                    $isConditionnel = $ligne['conditionnel'] ?? false;
                    if ($isConditionnel && $val == 0) continue;
                ?>
                <tr>
                    <td><?= htmlspecialchars($ligne['label']) ?></td>
                    <?php for ($i = 1; $i < count($section['colonnes']); $i++): ?>
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
                    <td><?= htmlspecialchars($section['total']['label']) ?></td>
                    <?php for ($i = 1; $i < count($section['colonnes']); $i++): ?>
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

    <?php if ($cfg['show_footer'] ?? true): ?>
    <div style="margin-top:2rem; padding-top:1rem; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
        <div style="display:flex; align-items:center; gap:0.5rem;">
            <div style="width:32px; height:32px; background:<?= $couleur ?>; border-radius:6px; display:flex; align-items:center; justify-content:center; font-size:0.75rem; font-weight:700; color:#fff;">
                <?= strtoupper(mb_substr($b['raison_sociale'], 0, 2)) ?>
            </div>
            <span style="font-size:0.8125rem; color:var(--text-muted);"><?= htmlspecialchars($b['raison_sociale']) ?></span>
        </div>
        <div style="text-align:right; font-size:0.75rem; color:var(--text-muted);">
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
</div>
