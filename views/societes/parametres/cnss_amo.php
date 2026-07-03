<form method="post" action="<?= $baseUrl ?>/cnss_amo">
<input type="hidden" name="sous_tab" value="cnss_amo">
<div class="card">
    <div class="card-header"><h3>Taux CNSS & AMO</h3></div>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
        <div class="form-group">
            <label>Plafond CNSS (MAD)</label>
            <input type="number" name="plafond_cnss" value="<?= htmlspecialchars($cnssParams['plafond_cnss'] ?? '6000') ?>" class="form-control" step="0.01">
        </div>
        <div class="form-group">
            <label>Taux CNSS salarial (%)</label>
            <input type="number" name="taux_cnss_salarial" value="<?= htmlspecialchars($cnssParams['taux_cnss_salarial'] ?? '4.48') ?>" class="form-control" step="0.01">
        </div>
        <div class="form-group">
            <label>Taux CNSS patronal (%)</label>
            <input type="number" name="taux_cnss_patronal" value="<?= htmlspecialchars($cnssParams['taux_cnss_patronal'] ?? '8.98') ?>" class="form-control" step="0.01">
        </div>
        <div class="form-group">
            <label>Taux AMO salarial (%)</label>
            <input type="number" name="taux_amo_salarial" value="<?= htmlspecialchars($cnssParams['taux_amo_salarial'] ?? '2.26') ?>" class="form-control" step="0.01">
        </div>
        <div class="form-group">
            <label>Taux AMO patronal (%)</label>
            <input type="number" name="taux_amo_patronal" value="<?= htmlspecialchars($cnssParams['taux_amo_patronal'] ?? '4.11') ?>" class="form-control" step="0.01">
        </div>
    </div>
    <div style="margin-top:1rem; padding-top:1rem; border-top:1px solid var(--border);">
        <button type="submit" class="btn btn-primary">Enregistrer les taux</button>
    </div>
</div>
</form>
