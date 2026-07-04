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
        .net { background: #f3f4f6; padding: 10px 15px; text-align: right; font-size: 16px; font-weight: bold; border-radius: 4px; margin-top: 10px; }
        .footer { text-align: center; margin-top: 30px; font-size: 10px; color: #999; border-top: 1px solid #ddd; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header" style="display:flex; align-items:center; gap:15px; text-align:left; border-bottom:3px solid #3b82f6; padding-bottom:15px;">
        <div style="width:50px; height:50px; background:#3b82f6; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:20px; font-weight:700; color:#fff;">
            <?= strtoupper(mb_substr($b['raison_sociale'], 0, 2)) ?>
        </div>
        <div style="flex:1;">
            <h1 style="font-size:16px; margin:0; color:#3b82f6;"><?= htmlspecialchars($b['raison_sociale']) ?></h1>
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

    <h3>Éléments du salaire</h3>
    <table class="details">
        <tr><th>Libellé</th><th class="right">Montant (MAD)</th></tr>
        <tr><td>Salaire de base</td><td class="right"><?= number_format($b['salaire_base'], 2, ',', ' ') ?></td></tr>
        <?php if ((float)$b['prime_anciennete'] > 0): ?>
        <tr><td>Prime d'ancienneté</td><td class="right"><?= number_format($b['prime_anciennete'], 2, ',', ' ') ?></td></tr>
        <?php endif; ?>
        <?php if ((float)$b['montant_heures_sup'] > 0): ?>
        <tr><td>Heures supplémentaires (<?= (float)$b['heures_supplementaires'] ?>h)</td><td class="right"><?= number_format($b['montant_heures_sup'], 2, ',', ' ') ?></td></tr>
        <?php endif; ?>
        <tr><td>Indemnité de transport</td><td class="right"><?= number_format($b['indemnite_transport'], 2, ',', ' ') ?></td></tr>
        <tr><td>Indemnité de panier</td><td class="right"><?= number_format($b['indemnite_panier'], 2, ',', ' ') ?></td></tr>
        <?php if ((float)$b['indemnite_representation'] > 0): ?>
        <tr><td>Indemnité de représentation</td><td class="right"><?= number_format($b['indemnite_representation'], 2, ',', ' ') ?></td></tr>
        <?php endif; ?>
        <?php if ((float)$b['avantage_logement'] > 0): ?>
        <tr><td>Avantage logement</td><td class="right"><?= number_format($b['avantage_logement'], 2, ',', ' ') ?></td></tr>
        <?php endif; ?>
        <tr class="bold"><td>Salaire brut global (SBG)</td><td class="right"><?= number_format($b['salaire_brut'], 2, ',', ' ') ?></td></tr>
        <?php if ((float)$b['sbi'] > 0): ?>
        <tr><td>Salaire brut imposable (SBI)</td><td class="right"><?= number_format($b['sbi'], 2, ',', ' ') ?></td></tr>
        <?php endif; ?>
    </table>

    <h3>Cotisations et retenues</h3>
    <table class="details">
        <tr><th>Libellé</th><th class="right">Montant (MAD)</th></tr>
        <tr><td>CNSS (part salariale)</td><td class="right"><?= number_format($b['cnss_salariale'], 2, ',', ' ') ?></td></tr>
        <tr><td>AMO (part salariale)</td><td class="right"><?= number_format($b['amo_salariale'], 2, ',', ' ') ?></td></tr>
        <?php if ((float)$b['frais_professionnels'] > 0): ?>
        <tr><td>Frais professionnels</td><td class="right"><?= number_format($b['frais_professionnels'], 2, ',', ' ') ?></td></tr>
        <?php endif; ?>
        <tr class="bold"><td>Salaire net imposable (SNI)</td><td class="right"><?= number_format($b['sni'], 2, ',', ' ') ?></td></tr>
        <tr><td>Impôt sur le revenu (IR brut)</td><td class="right"><?= number_format($b['ir'], 2, ',', ' ') ?></td></tr>
        <?php if ((float)$b['deductions_familiales'] > 0): ?>
        <tr><td>Déductions pour charges de famille</td><td class="right" style="color:#22c55e;">+ <?= number_format($b['deductions_familiales'], 2, ',', ' ') ?></td></tr>
        <?php endif; ?>
    </table>

    <div class="net">Net à payer : <?= number_format($b['net_a_payer'], 2, ',', ' ') ?> MAD</div>

    <div class="footer" style="display:flex; justify-content:space-between; align-items:center;">
        <div style="display:flex; align-items:center; gap:6px;">
            <div style="width:24px; height:24px; background:#3b82f6; border-radius:4px; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:700; color:#fff;">
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
</body>
</html>
