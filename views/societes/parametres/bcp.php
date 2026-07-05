<?php
$masse = 5874.88;
$taux_af = $cnssParams['taux_allocations_familiales'] ?? 6.40;
$taux_ps = $cnssParams['taux_prestations_sociales'] ?? 13.46;
$taux_tfp = $cnssParams['taxe_formation'] ?? 1.60;
$taux_part_amo = $cnssParams['participation_amo'] ?? 1.85;
$taux_amo_total = $cnssParams['taux_amo_total'] ?? 6.37;
$taux_cot_amo = round($taux_amo_total - $taux_part_amo, 2);
$mt_af = round($masse * $taux_af / 100, 2);
$mt_ps = round($masse * $taux_ps / 100, 2);
$mt_tfp = round($masse * $taux_tfp / 100, 2);
$mt_part_amo = round($masse * $taux_part_amo / 100, 2);
$mt_cot_amo = round($masse * $taux_cot_amo / 100, 2);
$total_cnss = $mt_af + $mt_ps;
$total_premier = $total_cnss + $mt_tfp;
$total_amo = $mt_part_amo + $mt_cot_amo;
$total_cotisations = $total_cnss + $total_amo;
$total_general = $total_premier + $total_amo;
?>

<div class="card">
    <div class="card-header"><h3>BCP — Bordereau 1 : Cotisations CNSS + Taxe de Formation Professionnelle</h3></div>
    <p style="font-size:0.8125rem; color:var(--text-muted); margin:0 0 0.75rem 0;">Basé sur une masse salariale de <?= number_format($masse, 2, ',', ' ') ?> MAD et les taux configurés dans <a href="<?= $baseUrl ?>/cnss_amo" style="color:var(--accent);">Taux CNSS & AMO</a></p>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th style="width:50px;">C</th><th>Nature des prestations</th><th style="width:140px; text-align:right; white-space:nowrap;">Masse salariale</th><th style="width:80px; text-align:right;">Taux</th><th style="width:120px; text-align:right;">Montant</th></tr>
            </thead>
            <tbody>
                <tr><td>1</td><td>Allocations Familiales</td><td style="text-align:right; white-space:nowrap;"><?= number_format($masse, 2, ',', ' ') ?></td><td style="text-align:right; white-space:nowrap;"><?= number_format($taux_af, 2, ',', ' ') ?> %</td><td style="text-align:right; font-weight:600; white-space:nowrap;"><?= number_format($mt_af, 2, ',', ' ') ?></td></tr>
                <tr><td>2</td><td>Prestations sociales à court terme</td><td style="text-align:right; white-space:nowrap;"><?= number_format($masse, 2, ',', ' ') ?></td><td style="text-align:right; white-space:nowrap;"><?= number_format($taux_ps, 2, ',', ' ') ?> %</td><td style="text-align:right; font-weight:600; white-space:nowrap;"><?= number_format($mt_ps, 2, ',', ' ') ?></td></tr>
                <tr style="background:rgba(59,130,246,0.15); font-weight:700;"><td>3</td><td>Total des cotisations versées</td><td style="text-align:right;">—</td><td style="text-align:right;">—</td><td style="text-align:right; color:var(--accent); white-space:nowrap;"><?= number_format($total_cnss, 2, ',', ' ') ?></td></tr>
                <tr><td>4</td><td>Pénalités sur cotisations</td><td style="text-align:right;">—</td><td style="text-align:right;">—</td><td style="text-align:right;">0,00</td></tr>
                <tr><td>5</td><td>Montant des AF reversées</td><td style="text-align:right;">—</td><td style="text-align:right;">—</td><td style="text-align:right;">—</td></tr>
                <tr><td>6</td><td>Astreintes</td><td style="text-align:right;">—</td><td style="text-align:right;">—</td><td style="text-align:right;">0,00</td></tr>
                <tr><td>8</td><td>Taxe de la formation professionnelle</td><td style="text-align:right; white-space:nowrap;"><?= number_format($masse, 2, ',', ' ') ?></td><td style="text-align:right; white-space:nowrap;"><?= number_format($taux_tfp, 2, ',', ' ') ?> %</td><td style="text-align:right; font-weight:600; white-space:nowrap;"><?= number_format($mt_tfp, 2, ',', ' ') ?></td></tr>
                <tr><td>9</td><td>Pénalités TFP</td><td style="text-align:right;">—</td><td style="text-align:right;">—</td><td style="text-align:right;">0,00</td></tr>
                <tr style="background:rgba(59,130,246,0.25); font-weight:700;"><td>10</td><td>Montant global du versement</td><td style="text-align:right;">—</td><td style="text-align:right;">—</td><td style="text-align:right; color:var(--accent); font-size:1rem; white-space:nowrap;"><?= number_format($total_premier, 2, ',', ' ') ?></td></tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card" style="margin-top:0.75rem;">
    <div class="card-header"><h3>BCP — Bordereau 2 : Cotisations AMO</h3></div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th style="width:50px;">C</th><th>Nature des prestations</th><th style="width:140px; text-align:right; white-space:nowrap;">Masse salariale</th><th style="width:80px; text-align:right;">Taux</th><th style="width:120px; text-align:right;">Montant</th></tr>
            </thead>
            <tbody>
                <tr><td>1</td><td>Participation AMO</td><td style="text-align:right; white-space:nowrap;"><?= number_format($masse, 2, ',', ' ') ?></td><td style="text-align:right; white-space:nowrap;"><?= number_format($taux_part_amo, 2, ',', ' ') ?> %</td><td style="text-align:right; font-weight:600; white-space:nowrap;"><?= number_format($mt_part_amo, 2, ',', ' ') ?></td></tr>
                <tr><td>2</td><td>Cotisation AMO</td><td style="text-align:right; white-space:nowrap;"><?= number_format($masse, 2, ',', ' ') ?></td><td style="text-align:right; white-space:nowrap;"><?= number_format($taux_cot_amo, 2, ',', ' ') ?> %</td><td style="text-align:right; font-weight:600; white-space:nowrap;"><?= number_format($mt_cot_amo, 2, ',', ' ') ?></td></tr>
                <tr style="background:rgba(59,130,246,0.15); font-weight:700;"><td>3</td><td>Total des cotisations versées AMO</td><td style="text-align:right;">—</td><td style="text-align:right;">—</td><td style="text-align:right; color:var(--accent); white-space:nowrap;"><?= number_format($total_amo, 2, ',', ' ') ?></td></tr>
                <tr><td>4</td><td>Pénalités sur cotisations AMO et astreintes</td><td style="text-align:right;">—</td><td style="text-align:right;">—</td><td style="text-align:right;">0,00</td></tr>
                <tr style="background:rgba(59,130,246,0.25); font-weight:700;"><td>10</td><td>Montant global du versement</td><td style="text-align:right;">—</td><td style="text-align:right;">—</td><td style="text-align:right; color:var(--accent); font-size:1rem; white-space:nowrap;"><?= number_format($total_amo, 2, ',', ' ') ?></td></tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card" style="margin-top:0.75rem;">
    <div class="card-header"><h3>BCP — Tableau récapitulatif des cotisations</h3></div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th style="width:80px;">Bordereau</th><th style="width:50px;">C</th><th>Nature des prestations</th><th style="width:140px; text-align:right; white-space:nowrap;">Masse salariale</th><th style="width:80px; text-align:right;">Taux</th><th style="width:120px; text-align:right;">Montant</th></tr>
            </thead>
            <tbody>
                <tr><td>CNSS</td><td>1</td><td>Allocations Familiales</td><td style="text-align:right; white-space:nowrap;"><?= number_format($masse, 2, ',', ' ') ?></td><td style="text-align:right; white-space:nowrap;"><?= number_format($taux_af, 2, ',', ' ') ?> %</td><td style="text-align:right; font-weight:600; white-space:nowrap;"><?= number_format($mt_af, 2, ',', ' ') ?></td></tr>
                <tr><td>CNSS</td><td>2</td><td>Prestations sociales à court terme</td><td style="text-align:right; white-space:nowrap;"><?= number_format($masse, 2, ',', ' ') ?></td><td style="text-align:right; white-space:nowrap;"><?= number_format($taux_ps, 2, ',', ' ') ?> %</td><td style="text-align:right; font-weight:600; white-space:nowrap;"><?= number_format($mt_ps, 2, ',', ' ') ?></td></tr>
                <tr><td>AMO</td><td>1</td><td>Participation AMO</td><td style="text-align:right; white-space:nowrap;"><?= number_format($masse, 2, ',', ' ') ?></td><td style="text-align:right; white-space:nowrap;"><?= number_format($taux_part_amo, 2, ',', ' ') ?> %</td><td style="text-align:right; font-weight:600; white-space:nowrap;"><?= number_format($mt_part_amo, 2, ',', ' ') ?></td></tr>
                <tr><td>AMO</td><td>2</td><td>Cotisation AMO</td><td style="text-align:right; white-space:nowrap;"><?= number_format($masse, 2, ',', ' ') ?></td><td style="text-align:right; white-space:nowrap;"><?= number_format($taux_cot_amo, 2, ',', ' ') ?> %</td><td style="text-align:right; font-weight:600; white-space:nowrap;"><?= number_format($mt_cot_amo, 2, ',', ' ') ?></td></tr>
                <tr style="background:rgba(59,130,246,0.15); font-weight:700;"><td colspan="5" style="text-align:right;">Total des cotisations (CNSS + AMO)</td><td style="text-align:right; color:var(--accent); white-space:nowrap;"><?= number_format($total_cotisations, 2, ',', ' ') ?></td></tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card" style="margin-top:0.75rem; background:linear-gradient(135deg, rgba(59,130,246,0.15), rgba(59,130,246,0.05)); border-color:var(--accent);">
    <div class="card-header"><h3 style="color:var(--accent);">BCP — Montant global des versements (incluant TFP)</h3></div>
    <div style="text-align:center; padding:0.75rem;">
        <span style="font-size:1.5rem; font-weight:700; color:var(--accent);"><?= number_format($total_general, 2, ',', ' ') ?> MAD</span>
    </div>
</div>
