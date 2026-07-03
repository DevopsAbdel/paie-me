<form method="post" action="<?= $baseUrl ?>/banque">
<input type="hidden" name="sous_tab" value="banque">
<div class="card">
    <div class="card-header"><h3>Coordonnées bancaires</h3></div>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
        <div class="form-group">
            <label>Banque</label>
            <input type="text" name="banque" value="<?= htmlspecialchars($societe['banque'] ?? '') ?>" class="form-control" placeholder="Nom de la banque">
        </div>
        <div class="form-group">
            <label>Agence</label>
            <input type="text" name="agence" value="<?= htmlspecialchars($societe['agence'] ?? '') ?>" class="form-control" placeholder="Agence bancaire">
        </div>
        <div class="form-group" style="grid-column:1/-1;">
            <label>RIB</label>
            <input type="text" name="rib" value="<?= htmlspecialchars($societe['rib'] ?? '') ?>" class="form-control" placeholder="RIB complet">
            <small style="color:var(--text-muted); font-size:0.75rem;">Le RIB apparaîtra sur les bulletins de paie.</small>
        </div>
    </div>
    <div style="margin-top:1rem; padding-top:1rem; border-top:1px solid var(--border);">
        <button type="submit" class="btn btn-primary">Enregistrer</button>
    </div>
</div>
</form>
