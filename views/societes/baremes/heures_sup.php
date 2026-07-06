<form method="post" action="<?= $baseUrl ?>/heures_sup">
<?= \Core\Session::csrfField() ?>
<input type="hidden" name="sous_tab" value="heures_sup">

<div class="card">
    <div class="card-header"><h3>Barème heures supplémentaires</h3></div>
    <p style="font-size:0.8125rem; color:var(--text-muted); margin:0 0 0.5rem 0;">
        Taux de majoration applicables aux heures supplémentaires selon le Code du Travail marocain (Art. 185-191).
    </p>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.75rem; max-width:500px;">
        <div class="form-group">
            <label>Taux normal (%)</label>
            <input type="number" name="taux_normal" value="<?= htmlspecialchars($heuresSup['taux_normal'] ?? '25') ?>" class="form-control" step="0.01" min="0">
            <small style="color:var(--text-muted); font-size:0.7rem;">1<sup>res</sup> heures (légal : 25%)</small>
        </div>
        <div class="form-group">
            <label>Seuil (heures)</label>
            <input type="number" name="seuil_heures" value="<?= htmlspecialchars($heuresSup['seuil_heures'] ?? '8') ?>" class="form-control" min="1">
            <small style="color:var(--text-muted); font-size:0.7rem;">Heures avant taux majoré (légal : 8h)</small>
        </div>
        <div class="form-group">
            <label>Taux majoré (%)</label>
            <input type="number" name="taux_majore" value="<?= htmlspecialchars($heuresSup['taux_majore'] ?? '50') ?>" class="form-control" step="0.01" min="0">
            <small style="color:var(--text-muted); font-size:0.7rem;">Au-delà du seuil / week-end (légal : 50%)</small>
        </div>
        <div class="form-group">
            <label>Taux jour férié (%)</label>
            <input type="number" name="taux_jour_ferie" value="<?= htmlspecialchars($heuresSup['taux_jour_ferie'] ?? '100') ?>" class="form-control" step="0.01" min="0">
            <small style="color:var(--text-muted); font-size:0.7rem;">Jours fériés (légal : 100%)</small>
        </div>
    </div>
</div>

<div style="margin-top:1rem; display:flex; justify-content:flex-end;">
    <button type="submit" class="btn btn-primary">Enregistrer</button>
</div>
</form>
