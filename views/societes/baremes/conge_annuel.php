<form method="post" action="<?= $baseUrl ?>/conge_annuel">
<?= \Core\Session::csrfField() ?>
<input type="hidden" name="sous_tab" value="conge_annuel">

<div class="card">
    <div class="card-header"><h3>Configuration congé annuel</h3></div>
    <p style="font-size:0.8125rem; color:var(--text-muted); margin:0 0 0.5rem 0;">
        Selon le Code du Travail marocain (Art. 231) : 1,5 jour ouvrable par mois de travail effectif (18 jours/an).
        Certaines conventions collectives prévoient 2 jours/mois (24 jours/an).
    </p>
    <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:0.75rem; max-width:600px;">
        <div class="form-group">
            <label>Jours par mois</label>
            <input type="number" name="jours_par_mois" value="<?= htmlspecialchars($conge['jours_par_mois'] ?? '1.50') ?>" class="form-control" step="0.01" min="0">
            <small style="color:var(--text-muted); font-size:0.7rem;">Légal : 1.50</small>
        </div>
        <div class="form-group">
            <label>Report autorisé</label>
            <select name="report_autorise" class="form-control">
                <option value="1" <?= ($conge['report_autorise'] ?? 1) ? 'selected' : '' ?>>Oui</option>
                <option value="0" <?= !($conge['report_autorise'] ?? 1) ? 'selected' : '' ?>>Non</option>
            </select>
        </div>
        <div class="form-group">
            <label>Report max (jours)</label>
            <input type="number" name="report_max" value="<?= htmlspecialchars($conge['report_max'] ?? '15') ?>" class="form-control" min="0">
        </div>
    </div>
</div>

<div style="margin-top:1rem; display:flex; justify-content:flex-end;">
    <button type="submit" class="btn btn-primary">Enregistrer</button>
</div>
</form>
