<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10pt;
            color: #1a1a1a;
            line-height: 1.5;
            background: #fff;
            padding: 15mm 20mm;
        }

        .a4-header {
            text-align: center;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #1e293b;
        }
        .a4-company-logo {
            display: inline-block;
            width: 48px;
            height: 48px;
            background: #1e293b;
            color: #fff;
            font-weight: 800;
            font-size: 1.1rem;
            border-radius: 10px;
            line-height: 48px;
            margin-bottom: 0.3rem;
        }
        .a4-company-name {
            font-size: 14pt;
            font-weight: 700;
            color: #1e293b;
        }
        .a4-company-sub {
            font-size: 8pt;
            color: #666;
        }
.a4-title {
    text-align: center;
    font-size: 16pt;
    font-weight: 800;
    color: #1e293b;
    letter-spacing: 0.04em;
    margin: 2rem 0 1.5rem;
    text-transform: uppercase;
}
        .a4-info-grid {
            margin-bottom: 0.75rem;
        }
        .a4-info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .a4-info-table td {
            padding: 0.2rem 0.5rem;
            border-bottom: 1px solid #e2e8f0;
            font-size: 9.5pt;
            vertical-align: top;
        }
        .a4-label {
            font-weight: 700;
            color: #475569;
            width: 30%;
            white-space: nowrap;
        }
        .a4-observation {
            background: #f8fafc;
            border-left: 3px solid #3b82f6;
            padding: 0.3rem 0.5rem;
            margin-bottom: 0.75rem;
            font-size: 9pt;
        }
        .a4-body {
            margin-bottom: 1.5rem;
            text-align: justify;
            font-size: 10pt;
            line-height: 1.7;
        }
        .a4-footer {
            display: flex;
            justify-content: space-between;
            font-size: 9pt;
            color: #666;
            margin-bottom: 1.5rem;
        }
        .a4-signature {
            text-align: right;
            margin-top: 1rem;
            width: 140px;
            float: right;
        }
        .a4-sig-line {
            border-bottom: 1px solid #1a1a1a;
            margin-bottom: 0.2rem;
            height: 50px;
        }
        .a4-sig-label {
            font-size: 8pt;
            color: #666;
        }
    </style>
</head>
<body>
    <?php
    $types = ['paye' => 'Congé payé', 'sans_solde' => 'Sans solde', 'maladie' => 'Maladie', 'maternite' => 'Maternité', 'exceptionnel' => 'Exceptionnel', 'autre' => 'Autre'];
    $statuts = ['en_attente' => 'En attente', 'valide' => 'Validé', 'refuse' => 'Refusé', 'annule' => 'Annulé'];
    $typeLabel = $types[$conge['type_conge']] ?? $conge['type_conge'];
    $statutLabel = $statuts[$conge['statut']] ?? $conge['statut'];
    ?>

    <div class="a4-header">
        <div class="a4-company-logo">TE</div>
        <div class="a4-company-name"><?= htmlspecialchars($societe['raison_sociale']) ?></div>
        <div class="a4-company-sub">ICE : <?= htmlspecialchars($societe['ice'] ?? '') ?></div>
    </div>

    <h2 class="a4-title">ATTESTATION DE CONGÉ</h2>

    <div class="a4-info-grid">
        <table class="a4-info-table">
            <tr><td class="a4-label">Matricule</td><td><?= htmlspecialchars($conge['matricule']) ?></td></tr>
            <tr><td class="a4-label">Nom complet</td><td><?= htmlspecialchars($conge['nom_complet']) ?></td></tr>
            <tr><td class="a4-label">Poste</td><td><?= htmlspecialchars($conge['poste'] ?? '—') ?></td></tr>
            <tr><td class="a4-label">Type de congé</td><td><?= $typeLabel ?></td></tr>
            <tr><td class="a4-label">Date début</td><td><?= date('d/m/Y', strtotime($conge['date_debut'])) ?></td></tr>
            <tr><td class="a4-label">Date fin</td><td><?= date('d/m/Y', strtotime($conge['date_fin'])) ?></td></tr>
            <tr><td class="a4-label">Nombre de jours</td><td><?= number_format($conge['nb_jours'], 1, ',', '') ?> jour(s)</td></tr>
            <tr><td class="a4-label">Statut</td><td><?= $statutLabel ?></td></tr>
        </table>
    </div>

    <?php if ($conge['observation']): ?>
    <div class="a4-observation">
        <strong>Observation :</strong> <?= nl2br(htmlspecialchars($conge['observation'])) ?>
    </div>
    <?php endif; ?>

    <div class="a4-body">
        La société <strong><?= htmlspecialchars($societe['raison_sociale']) ?></strong> atteste que le/la salarié(e)
        <strong><?= htmlspecialchars($conge['nom_complet']) ?></strong> (matricule <?= htmlspecialchars($conge['matricule']) ?>)
        bénéficie d'un congé du <strong><?= date('d/m/Y', strtotime($conge['date_debut'])) ?></strong>
        au <strong><?= date('d/m/Y', strtotime($conge['date_fin'])) ?></strong>,
        soit <strong><?= number_format($conge['nb_jours'], 1, ',', '') ?> jour(s)</strong> de <?= strtolower($typeLabel) ?>.
    </div>

    <div class="a4-footer">
        <div>Fait le <?= date('d/m/Y') ?></div>
        <div>Le responsable</div>
    </div>

    <div class="a4-signature">
        <div class="a4-sig-line"></div>
        <div class="a4-sig-label">Signature &amp; cachet</div>
    </div>

    <div style="clear:both;"></div>
</body>
</html>
