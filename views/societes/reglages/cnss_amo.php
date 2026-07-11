<form method="post" action="<?= $baseUrl ?>/cnss_amo">
<?= \Core\Session::csrfField() ?>
<input type="hidden" name="sous_tab" value="cnss_amo">

<div class="card">
    <div class="card-header"><h3>CNSS</h3></div>

    <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:0.5rem;">
        <div class="form-group">
            <label>Plafond CNSS (MAD)</label>
            <input type="number" name="plafond_cnss" value="<?= htmlspecialchars($cnssParams['plafond_cnss'] ?? '6000') ?>" class="form-control" step="0.01">
        </div>
        <div class="form-group">
            <label>Taux part salariale (%)</label>
            <input type="number" name="taux_cnss_salarial" value="<?= htmlspecialchars($cnssParams['taux_cnss_salarial'] ?? '4.48') ?>" class="form-control" step="0.01">
        </div>
        <div class="form-group">
            <label>Taux part patronale (%)</label>
            <input type="number" name="taux_cnss_patronal" value="<?= htmlspecialchars($cnssParams['taux_cnss_patronal'] ?? '8.98') ?>" class="form-control" step="0.01">
        </div>
    </div>
    <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:0.5rem;">
        <div class="form-group">
            <label>Taux prestations sociales (%)</label>
            <input type="number" name="taux_prestations_sociales" value="<?= htmlspecialchars($cnssParams['taux_prestations_sociales'] ?? '13.46') ?>" class="form-control" step="0.01">
        </div>
        <div class="form-group">
            <label>Allocations familiales (%)</label>
            <input type="number" name="taux_allocations_familiales" value="<?= htmlspecialchars($cnssParams['taux_allocations_familiales'] ?? '6.40') ?>" class="form-control" step="0.01">
        </div>
        <div class="form-group">
            <label>Taxe formation pro (%)</label>
            <input type="number" name="taxe_formation" value="<?= htmlspecialchars($cnssParams['taxe_formation'] ?? '1.60') ?>" class="form-control" step="0.01">
        </div>
    </div>

    <div style="margin-top:0.75rem; padding-top:0.75rem; border-top:1px solid var(--border);">
        <div style="font-size:0.75rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.5rem;">Pénalités de retard</div>
        <p style="font-size:0.75rem; color:var(--text-muted); margin:0 0 0.5rem 0;">Majoration <strong>3 %</strong> 1<sup>er</sup> mois, puis <strong>0,5 %</strong> / mois — Astreinte <strong>50 DH/mois</strong> par salarié</p>
        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:0.5rem;">
            <div class="form-group">
                <label>Taux 1<sup>er</sup> mois (%)</label>
                <input type="number" name="penalite_cnss_premier_mois" value="<?= htmlspecialchars($cnssParams['penalite_cnss_premier_mois'] ?? '3.00') ?>" class="form-control" step="0.01">
            </div>
            <div class="form-group">
                <label>Taux mois suivants (%)</label>
                <input type="number" name="penalite_cnss_mois_suivants" value="<?= htmlspecialchars($cnssParams['penalite_cnss_mois_suivants'] ?? '0.50') ?>" class="form-control" step="0.01">
            </div>
            <div class="form-group">
                <label>Astreinte / salarié (DH/mois)</label>
                <input type="number" name="astreinte_cnss_par_salarie" value="<?= htmlspecialchars($cnssParams['astreinte_cnss_par_salarie'] ?? '50.00') ?>" class="form-control" step="0.01">
            </div>
        </div>
    </div>
</div>

<div class="card" style="margin-top:0.75rem;">
    <div class="card-header"><h3>AMO</h3></div>

    <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:0.5rem;">
        <div class="form-group">
            <label>Taux part salariale (%)</label>
            <input type="number" name="taux_amo_salarial" value="<?= htmlspecialchars($cnssParams['taux_amo_salarial'] ?? '2.26') ?>" class="form-control" step="0.01">
        </div>
        <div class="form-group">
            <label>Taux part patronale (%)</label>
            <input type="number" name="taux_amo_patronal" value="<?= htmlspecialchars($cnssParams['taux_amo_patronal'] ?? '4.11') ?>" class="form-control" step="0.01">
        </div>
        <div class="form-group">
            <label>Taux total cotisation (%)</label>
            <input type="number" name="taux_amo_total" value="<?= htmlspecialchars($cnssParams['taux_amo_total'] ?? '6.37') ?>" class="form-control" step="0.01">
        </div>
    </div>
    <div style="display:grid; grid-template-columns:1fr; gap:0.5rem; max-width:300px;">
        <div class="form-group">
            <label>Participation patronale AMO (%)</label>
            <input type="number" name="participation_amo" value="<?= htmlspecialchars($cnssParams['participation_amo'] ?? '1.85') ?>" class="form-control" step="0.01">
        </div>
    </div>

    <div style="margin-top:0.75rem; padding-top:0.75rem; border-top:1px solid var(--border);">
        <div style="font-size:0.75rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.5rem;">Pénalités de retard</div>
        <p style="font-size:0.75rem; color:var(--text-muted); margin:0 0 0.5rem 0;">Majoration <strong>1 %</strong> / mois — Astreinte <strong>100 DH/mois</strong> par salarié — TFP pénalité <strong>0 %</strong></p>
        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:0.5rem;">
            <div class="form-group">
                <label>Taux pénalité AMO (%)</label>
                <input type="number" name="penalite_amo_taux" value="<?= htmlspecialchars($cnssParams['penalite_amo_taux'] ?? '1.00') ?>" class="form-control" step="0.01">
            </div>
            <div class="form-group">
                <label>Taux pénalité TFP (%)</label>
                <input type="number" name="taux_penalites_tfp" value="<?= htmlspecialchars($cnssParams['taux_penalites_tfp'] ?? '0') ?>" class="form-control" step="0.01">
            </div>
            <div class="form-group">
                <label>Astreinte / salarié (DH/mois)</label>
                <input type="number" name="astreinte_amo_par_salarie" value="<?= htmlspecialchars($cnssParams['astreinte_amo_par_salarie'] ?? '100.00') ?>" class="form-control" step="0.01">
            </div>
        </div>
    </div>
</div>

<div style="margin-top:0.75rem; display:flex; justify-content:flex-end;">
    <button type="submit" class="btn btn-primary">Enregistrer les taux</button>
</div>
</form>
