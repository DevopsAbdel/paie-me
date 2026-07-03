<form method="post" action="<?= $baseUrl ?>/cnss_amo">
<input type="hidden" name="sous_tab" value="cnss_amo">

<div class="card">
    <div class="card-header"><h3>Plafond CNSS</h3></div>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
        <div class="form-group">
            <label>Plafond CNSS (MAD)</label>
            <input type="number" name="plafond_cnss" value="<?= htmlspecialchars($cnssParams['plafond_cnss'] ?? '6000') ?>" class="form-control" step="0.01">
        </div>
    </div>
</div>

<div class="card" style="margin-top:1.5rem;">
    <div class="card-header"><h3>CNSS — Prestations sociales</h3></div>
    <p style="font-size:0.8125rem; color:var(--text-muted); margin:0 0 1rem 0;">Part salariale (4,48%) + Part patronale (8,98%) = Total 13,46%</p>
    <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:1rem;">
        <div class="form-group">
            <label>Taux part salariale (%)</label>
            <input type="number" name="taux_cnss_salarial" value="<?= htmlspecialchars($cnssParams['taux_cnss_salarial'] ?? '4.48') ?>" class="form-control" step="0.01">
        </div>
        <div class="form-group">
            <label>Taux part patronale (%)</label>
            <input type="number" name="taux_cnss_patronal" value="<?= htmlspecialchars($cnssParams['taux_cnss_patronal'] ?? '8.98') ?>" class="form-control" step="0.01">
        </div>
        <div class="form-group">
            <label>Taux total prestations sociales (%)</label>
            <input type="number" name="taux_prestations_sociales" value="<?= htmlspecialchars($cnssParams['taux_prestations_sociales'] ?? '13.46') ?>" class="form-control" step="0.01">
        </div>
    </div>
</div>

<div class="card" style="margin-top:1.5rem;">
    <div class="card-header"><h3>CNSS — Allocations familiales</h3></div>
    <p style="font-size:0.8125rem; color:var(--text-muted); margin:0 0 1rem 0;">Entièrement à la charge de l'employeur</p>
    <div style="display:grid; grid-template-columns:1fr; gap:1rem; max-width:300px;">
        <div class="form-group">
            <label>Taux allocations familiales (%)</label>
            <input type="number" name="taux_allocations_familiales" value="<?= htmlspecialchars($cnssParams['taux_allocations_familiales'] ?? '6.40') ?>" class="form-control" step="0.01">
        </div>
    </div>
</div>

<div class="card" style="margin-top:1.5rem;">
    <div class="card-header"><h3>Taxe de formation professionnelle</h3></div>
    <p style="font-size:0.8125rem; color:var(--text-muted); margin:0 0 1rem 0;">Entièrement à la charge de l'employeur</p>
    <div style="display:grid; grid-template-columns:1fr; gap:1rem; max-width:300px;">
        <div class="form-group">
            <label>Taux taxe formation (%)</label>
            <input type="number" name="taxe_formation" value="<?= htmlspecialchars($cnssParams['taxe_formation'] ?? '1.60') ?>" class="form-control" step="0.01">
        </div>
    </div>
</div>

<div class="card" style="margin-top:1.5rem;">
    <div class="card-header"><h3>AMO — Cotisation</h3></div>
    <p style="font-size:0.8125rem; color:var(--text-muted); margin:0 0 1rem 0;">Part salariale (2,26%) + Part patronale (4,11%)</p>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
        <div class="form-group">
            <label>Taux part salariale (%)</label>
            <input type="number" name="taux_amo_salarial" value="<?= htmlspecialchars($cnssParams['taux_amo_salarial'] ?? '2.26') ?>" class="form-control" step="0.01">
        </div>
        <div class="form-group">
            <label>Taux part patronale (%)</label>
            <input type="number" name="taux_amo_patronal" value="<?= htmlspecialchars($cnssParams['taux_amo_patronal'] ?? '4.11') ?>" class="form-control" step="0.01">
        </div>
    </div>
</div>

<div class="card" style="margin-top:1.5rem;">
    <div class="card-header"><h3>AMO — Participation</h3></div>
    <p style="font-size:0.8125rem; color:var(--text-muted); margin:0 0 1rem 0;">Entièrement à la charge de l'employeur</p>
    <div style="display:grid; grid-template-columns:1fr; gap:1rem; max-width:300px;">
        <div class="form-group">
            <label>Taux participation AMO (%)</label>
            <input type="number" name="participation_amo" value="<?= htmlspecialchars($cnssParams['participation_amo'] ?? '1.85') ?>" class="form-control" step="0.01">
        </div>
    </div>
</div>

<div style="margin-top:1.5rem; display:flex; justify-content:flex-end;">
    <button type="submit" class="btn btn-primary">Enregistrer les taux</button>
</div>
</form>
