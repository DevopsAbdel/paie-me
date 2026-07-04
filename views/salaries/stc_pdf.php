<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Solde de Tout Compte</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11pt; margin: 30px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { font-size: 18pt; margin-bottom: 5px; }
        .infos { display: flex; justify-content: space-between; margin-bottom: 25px; }
        .infos > div { width: 48%; }
        .infos p { margin: 2px 0; font-size: 10pt; }
        .table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .table th, .table td { border: 1px solid #999; padding: 6px 8px; text-align: left; font-size: 10pt; }
        .table th { background: #eaeaea; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .totaux-row { background: #f5f5f5; }
        .footer { margin-top: 40px; font-size: 10pt; }
        .signatures { display: flex; justify-content: space-between; margin-top: 50px; }
        .signature-box { width: 40%; text-align: center; border-top: 1px solid #999; padding-top: 8px; font-size: 9pt; }
        .header-entreprise { font-size: 14pt; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <p class="header-entreprise"><?= htmlspecialchars($s['raison_sociale']) ?></p>
        <h1>SOLDE DE TOUT COMPTE</h1>
        <p>Établi le <?= date('d/m/Y') ?></p>
    </div>

    <div class="infos">
        <div>
            <p><strong>Employeur :</strong> <?= htmlspecialchars($s['raison_sociale']) ?></p>
            <p><?= htmlspecialchars($s['adresse']) ?> - <?= htmlspecialchars($s['ville']) ?></p>
            <p>ICE : <?= htmlspecialchars($s['ice']) ?> / RC : <?= htmlspecialchars($s['rc']) ?></p>
        </div>
        <div>
            <p><strong>Salarié :</strong> <?= htmlspecialchars($s['nom_famille'] . ' ' . $s['prenom']) ?></p>
            <p>Matricule : <?= htmlspecialchars($s['matricule']) ?> / CIN : <?= htmlspecialchars($s['cin']) ?></p>
            <p>CNSS : <?= htmlspecialchars($s['cnss']) ?></p>
            <p>Embauche : <?= $s['date_embauche'] ?> / Sortie : <?= $s['date_sortie'] ?? '—' ?></p>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Rubrique</th>
                <th class="text-right">Montant (DH)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total = 0;
            if ($dernierePaie):
                $rows = [
                    ['Salaire de base', (float) $dernierePaie['salaire_brut']],
                    ['Indemnité de transport', (float) $dernierePaie['indemnite_transport']],
                    ['Indemnité de panier', (float) $dernierePaie['indemnite_panier']],
                    ['Indemnité de représentation', (float) $dernierePaie['indemnite_representation']],
                    ['Avantage logement', (float) $dernierePaie['avantage_logement']],
                    ['Prime d\'ancienneté', (float) $dernierePaie['prime_anciennete']],
                    ['Heures supplémentaires', (float) $dernierePaie['montant_heures_sup']],
                    ['Autres gains', (float) $dernierePaie['total_gains']],
                ];
                foreach ($rows as $r):
                    $total += $r[1]; ?>
            <tr>
                <td><?= $r[0] ?></td>
                <td class="text-right"><?= number_format($r[1], 2) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr class="totaux-row">
                <td class="bold">TOTAL BRUT</td>
                <td class="text-right bold"><?= number_format($total, 2) ?> DH</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Arrêté le présent solde de tout compte à la somme de <strong><?= number_format($total, 2) ?> DH</strong>.</p>
        <p>Le salarié déclare avoir reçu la totalité des sommes lui revenant et être rempli de tous ses droits.</p>
    </div>

    <div class="signatures">
        <div class="signature-box">Cachet et signature de l'employeur</div>
        <div class="signature-box">Signature du salarié<br>(Mention "Reçu et solde de tout compte")</div>
    </div>
</body>
</html>
