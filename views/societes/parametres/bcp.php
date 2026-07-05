<?php
$plafond_cnss = $cnssParams['plafond_cnss'] ?? 6000.00;
$taux_af = $cnssParams['taux_allocations_familiales'] ?? 6.40;
$taux_ps = $cnssParams['taux_prestations_sociales'] ?? 13.46;
$taux_tfp = $cnssParams['taxe_formation'] ?? 1.60;
$taux_part_amo = $cnssParams['participation_amo'] ?? 1.85;
$taux_amo_total = $cnssParams['taux_amo_total'] ?? 6.37;
$taux_cot_amo = round($taux_amo_total - $taux_part_amo, 2);

$masse_totale = 0;
$masse_plafonnee = 0;
$lignes_salaries = [];
foreach ($salaries as $s) {
    $sb = (float)$s['salaire_base'];
    $masse_totale += $sb;
    $plafonne = min($sb, $plafond_cnss);
    $masse_plafonnee += $plafonne;
    $lignes_salaries[] = [
        'nom'       => $s['prenom'] . ' ' . $s['nom_famille'],
        'brut'      => $sb,
        'plafonne'  => $plafonne,
    ];
}

$mt_af = round($masse_totale * $taux_af / 100, 2);
$mt_ps = round($masse_plafonnee * $taux_ps / 100, 2);
$mt_tfp = round($masse_totale * $taux_tfp / 100, 2);
$mt_part_amo = round($masse_totale * $taux_part_amo / 100, 2);
$mt_cot_amo = round($masse_totale * $taux_cot_amo / 100, 2);
$total_cnss = $mt_af + $mt_ps;
$total_premier = $total_cnss + $mt_tfp;
$total_amo = $mt_part_amo + $mt_cot_amo;
$total_cotisations = $total_cnss + $total_amo;
$total_general = $total_premier + $total_amo;
function fmt($n) { return number_format($n, 2, ',', ' '); }
?>

<div class="card">
    <div class="card-header"><h3>BCP — Bordereau 1 : Cotisations CNSS + Taxe de Formation Professionnelle</h3></div>
    <p style="font-size:0.8125rem; color:var(--text-muted); margin:0 0 0.75rem 0;">Basé sur une masse salariale brute de <strong><?= fmt($masse_totale) ?> MAD</strong> (<?= count($salaries) ?> salarié(s) actif(s)). La masse plafonnée CNSS est de <strong><?= fmt($masse_plafonnee) ?> MAD</strong> (plafond de <?= fmt($plafond_cnss) ?> MAD appliqué par salarié). Taux configurés dans <a href="<?= $baseUrl ?>/cnss_amo" style="color:var(--accent);">Taux CNSS & AMO</a>.</p>
    <?php if (count($lignes_salaries) > 0): ?>
    <details style="margin:0 0 0.75rem 0; font-size:0.8125rem; color:var(--text-muted); cursor:pointer;">
        <summary style="font-weight:600;">Détail du calcul par salarié</summary>
        <table style="margin-top:0.5rem; font-size:0.75rem;">
            <thead>
                <tr><th style="text-align:left;">Salarié</th><th style="text-align:right; width:120px;">Salaire brut</th><th style="text-align:right; width:120px;">Base plafonnée CNSS</th></tr>
            </thead>
            <tbody>
            <?php foreach ($lignes_salaries as $l): ?>
                <tr><td><?= htmlspecialchars($l['nom']) ?></td><td style="text-align:right;"><?= fmt($l['brut']) ?></td><td style="text-align:right;"><?= fmt($l['plafonne']) ?></td></tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </details>
    <?php endif; ?>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th style="width:50px;">C</th><th>Nature des prestations</th><th style="width:120px; text-align:right;">Plafond</th><th style="width:140px; text-align:right; white-space:nowrap;">Masse salariale</th><th style="width:80px; text-align:right;">Taux</th><th style="width:120px; text-align:right;">Montant</th></tr>
            </thead>
            <tbody>
                <tr><td>1</td><td>Allocations Familiales</td><td style="text-align:right;">—</td><td style="text-align:right; white-space:nowrap;"><?= fmt($masse_totale) ?></td><td style="text-align:right; white-space:nowrap;"><?= fmt($taux_af) ?> %</td><td style="text-align:right; font-weight:600; white-space:nowrap;"><?= fmt($mt_af) ?></td></tr>
                <tr><td>2</td><td>Prestations sociales à court terme</td><td style="text-align:right; white-space:nowrap;"><?= fmt($plafond_cnss) ?></td><td style="text-align:right; white-space:nowrap;"><?= fmt($masse_plafonnee) ?></td><td style="text-align:right; white-space:nowrap;"><?= fmt($taux_ps) ?> %</td><td style="text-align:right; font-weight:600; white-space:nowrap;"><?= fmt($mt_ps) ?></td></tr>
                <tr style="background:rgba(59,130,246,0.15); font-weight:700;"><td>3</td><td>Total des cotisations versées</td><td style="text-align:right;">—</td><td style="text-align:right;">—</td><td style="text-align:right;">—</td><td style="text-align:right; color:var(--accent); white-space:nowrap;"><?= fmt($total_cnss) ?></td></tr>
                <tr><td>4</td><td>Pénalités sur cotisations</td><td style="text-align:right;">—</td><td style="text-align:right;">—</td><td style="text-align:right;">—</td><td style="text-align:right;">0,00</td></tr>
                <tr><td>5</td><td>Montant des AF reversées</td><td style="text-align:right;">—</td><td style="text-align:right;">—</td><td style="text-align:right;">—</td><td style="text-align:right;">—</td></tr>
                <tr><td>6</td><td>Astreintes</td><td style="text-align:right;">—</td><td style="text-align:right;">—</td><td style="text-align:right;">—</td><td style="text-align:right;">0,00</td></tr>
                <tr><td>8</td><td>Taxe de la formation professionnelle</td><td style="text-align:right;">—</td><td style="text-align:right; white-space:nowrap;"><?= fmt($masse_totale) ?></td><td style="text-align:right; white-space:nowrap;"><?= fmt($taux_tfp) ?> %</td><td style="text-align:right; font-weight:600; white-space:nowrap;"><?= fmt($mt_tfp) ?></td></tr>
                <tr><td>9</td><td>Pénalités TFP</td><td style="text-align:right;">—</td><td style="text-align:right;">—</td><td style="text-align:right;">—</td><td style="text-align:right;">0,00</td></tr>
                <tr style="background:rgba(59,130,246,0.25); font-weight:700;"><td>10</td><td>Montant global du versement</td><td style="text-align:right;">—</td><td style="text-align:right;">—</td><td style="text-align:right;">—</td><td style="text-align:right; color:var(--accent); font-size:1rem; white-space:nowrap;"><?= fmt($total_premier) ?></td></tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card" style="margin-top:0.75rem;">
    <div class="card-header"><h3>BCP — Bordereau 2 : Cotisations AMO</h3></div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th style="width:50px;">C</th><th>Nature des prestations</th><th style="width:120px; text-align:right;">Plafond</th><th style="width:140px; text-align:right; white-space:nowrap;">Masse salariale</th><th style="width:80px; text-align:right;">Taux</th><th style="width:120px; text-align:right;">Montant</th></tr>
            </thead>
            <tbody>
                <tr><td>1</td><td>Participation AMO</td><td style="text-align:right;">—</td><td style="text-align:right; white-space:nowrap;"><?= fmt($masse_totale) ?></td><td style="text-align:right; white-space:nowrap;"><?= fmt($taux_part_amo) ?> %</td><td style="text-align:right; font-weight:600; white-space:nowrap;"><?= fmt($mt_part_amo) ?></td></tr>
                <tr><td>2</td><td>Cotisation AMO</td><td style="text-align:right;">—</td><td style="text-align:right; white-space:nowrap;"><?= fmt($masse_totale) ?></td><td style="text-align:right; white-space:nowrap;"><?= fmt($taux_cot_amo) ?> %</td><td style="text-align:right; font-weight:600; white-space:nowrap;"><?= fmt($mt_cot_amo) ?></td></tr>
                <tr style="background:rgba(59,130,246,0.15); font-weight:700;"><td>3</td><td>Total des cotisations versées AMO</td><td style="text-align:right;">—</td><td style="text-align:right;">—</td><td style="text-align:right;">—</td><td style="text-align:right; color:var(--accent); white-space:nowrap;"><?= fmt($total_amo) ?></td></tr>
                <tr><td>4</td><td>Pénalités sur cotisations AMO et astreintes</td><td style="text-align:right;">—</td><td style="text-align:right;">—</td><td style="text-align:right;">—</td><td style="text-align:right;">0,00</td></tr>
                <tr style="background:rgba(59,130,246,0.25); font-weight:700;"><td>10</td><td>Montant global du versement</td><td style="text-align:right;">—</td><td style="text-align:right;">—</td><td style="text-align:right;">—</td><td style="text-align:right; color:var(--accent); font-size:1rem; white-space:nowrap;"><?= fmt($total_amo) ?></td></tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card" style="margin-top:0.75rem;">
    <div class="card-header"><h3>BCP — Tableau récapitulatif des cotisations</h3></div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th style="width:80px;">Bordereau</th><th style="width:50px;">C</th><th>Nature des prestations</th><th style="width:120px; text-align:right;">Plafond</th><th style="width:140px; text-align:right; white-space:nowrap;">Masse salariale</th><th style="width:80px; text-align:right;">Taux</th><th style="width:120px; text-align:right;">Montant</th></tr>
            </thead>
            <tbody>
                <tr><td>CNSS</td><td>1</td><td>Allocations Familiales</td><td style="text-align:right;">—</td><td style="text-align:right; white-space:nowrap;"><?= fmt($masse_totale) ?></td><td style="text-align:right; white-space:nowrap;"><?= fmt($taux_af) ?> %</td><td style="text-align:right; font-weight:600; white-space:nowrap;"><?= fmt($mt_af) ?></td></tr>
                <tr><td>CNSS</td><td>2</td><td>Prestations sociales à court terme</td><td style="text-align:right; white-space:nowrap;"><?= fmt($plafond_cnss) ?></td><td style="text-align:right; white-space:nowrap;"><?= fmt($masse_plafonnee) ?></td><td style="text-align:right; white-space:nowrap;"><?= fmt($taux_ps) ?> %</td><td style="text-align:right; font-weight:600; white-space:nowrap;"><?= fmt($mt_ps) ?></td></tr>
                <tr style="background:rgba(34,197,94,0.08); font-weight:700;"><td></td><td></td><td colspan="3" style="text-align:left; color:#22c55e; font-size:0.85rem;">Sous-total cotisations CNSS</td><td style="text-align:right; color:#22c55e; font-weight:700; font-size:0.85rem;">—</td><td style="text-align:right; color:#22c55e; font-weight:700; font-size:0.85rem; white-space:nowrap;"><?= fmt($total_cnss) ?></td></tr>
                <tr><td>CNSS</td><td>8</td><td>Taxe de la formation professionnelle</td><td style="text-align:right;">—</td><td style="text-align:right; white-space:nowrap;"><?= fmt($masse_totale) ?></td><td style="text-align:right; white-space:nowrap;"><?= fmt($taux_tfp) ?> %</td><td style="text-align:right; font-weight:600; white-space:nowrap;"><?= fmt($mt_tfp) ?></td></tr>
                <tr style="background:rgba(34,197,94,0.12); font-weight:700;"><td></td><td></td><td colspan="3" style="text-align:left; color:#22c55e; font-size:0.9rem;">Total CNSS + Taxe Formation</td><td style="text-align:right; color:#22c55e; font-weight:700; font-size:0.9rem;">—</td><td style="text-align:right; color:#22c55e; font-weight:700; font-size:0.9rem; white-space:nowrap;"><?= fmt($total_premier) ?></td></tr>
                <tr><td>AMO</td><td>1</td><td>Participation AMO</td><td style="text-align:right;">—</td><td style="text-align:right; white-space:nowrap;"><?= fmt($masse_totale) ?></td><td style="text-align:right; white-space:nowrap;"><?= fmt($taux_part_amo) ?> %</td><td style="text-align:right; font-weight:600; white-space:nowrap;"><?= fmt($mt_part_amo) ?></td></tr>
                <tr><td>AMO</td><td>2</td><td>Cotisation AMO</td><td style="text-align:right;">—</td><td style="text-align:right; white-space:nowrap;"><?= fmt($masse_totale) ?></td><td style="text-align:right; white-space:nowrap;"><?= fmt($taux_cot_amo) ?> %</td><td style="text-align:right; font-weight:600; white-space:nowrap;"><?= fmt($mt_cot_amo) ?></td></tr>
                <tr style="background:rgba(34,197,94,0.08); font-weight:700;"><td></td><td></td><td colspan="3" style="text-align:left; color:#22c55e; font-size:0.85rem;">Sous-total cotisations AMO</td><td style="text-align:right; color:#22c55e; font-weight:700; font-size:0.85rem;">—</td><td style="text-align:right; color:#22c55e; font-weight:700; font-size:0.85rem; white-space:nowrap;"><?= fmt($total_amo) ?></td></tr>
                <tr style="background:rgba(34,197,94,0.18); font-weight:700;"><td></td><td></td><td colspan="4" style="text-align:left; color:#22c55e; font-size:0.95rem;">Total général des cotisations</td><td style="text-align:right; color:#22c55e; font-weight:700; font-size:0.95rem; white-space:nowrap;"><?= fmt($total_general) ?></td></tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card" style="margin-top:0.75rem; background:linear-gradient(135deg, rgba(59,130,246,0.15), rgba(59,130,246,0.05)); border-color:var(--accent);">
    <div class="card-header"><h3 style="color:var(--accent);">BCP — Montant global des versements (incluant TFP)</h3></div>
    <div style="text-align:center; padding:0.75rem;">
        <span style="font-size:1.5rem; font-weight:700; color:var(--accent);"><?= fmt($total_general) ?> MAD</span>
    </div>
</div>
