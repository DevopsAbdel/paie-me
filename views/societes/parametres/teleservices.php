<form method="post" action="<?= $baseUrl ?>/teleservices">
<?= \Core\Session::csrfField() ?>
<input type="hidden" name="sous_tab" value="teleservices">
<div class="card">
    <div class="card-header"><h3>Damancom (CNSS)</h3></div>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
        <div class="form-group">
            <label>Login</label>
            <input type="text" name="damancom_login" value="<?= htmlspecialchars($societe['damancom_login'] ?? '') ?>" class="form-control" placeholder="Identifiant Damancom">
        </div>
        <div class="form-group">
            <label>Mot de passe</label>
            <input type="password" name="damancom_password" value="<?= htmlspecialchars($societe['damancom_password'] ?? '') ?>" class="form-control" placeholder="Mot de passe">
        </div>
    </div>
    <small style="color:var(--text-muted); font-size:0.75rem;">Déclaration CNSS mensuelle via Damancom.</small>
</div>

<div class="card" style="margin-top:1rem;">
    <div class="card-header"><h3>SIMPL (Impôts)</h3></div>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
        <div class="form-group">
            <label>Login</label>
            <input type="text" name="simpl_login" value="<?= htmlspecialchars($societe['simpl_login'] ?? '') ?>" class="form-control" placeholder="Identifiant SIMPL">
        </div>
        <div class="form-group">
            <label>Mot de passe</label>
            <input type="password" name="simpl_password" value="<?= htmlspecialchars($societe['simpl_password'] ?? '') ?>" class="form-control" placeholder="Mot de passe">
        </div>
    </div>
    <small style="color:var(--text-muted); font-size:0.75rem;">Déclaration IR mensuelle via SIMPL.</small>
</div>

<div class="card" style="margin-top:1rem;">
    <div class="card-header"><h3>CIMR (Retraite)</h3></div>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
        <div class="form-group">
            <label>Login</label>
            <input type="text" name="cimr_login" value="<?= htmlspecialchars($societe['cimr_login'] ?? '') ?>" class="form-control" placeholder="Identifiant CIMR">
        </div>
        <div class="form-group">
            <label>Mot de passe</label>
            <input type="password" name="cimr_password" value="<?= htmlspecialchars($societe['cimr_password'] ?? '') ?>" class="form-control" placeholder="Mot de passe">
        </div>
    </div>
    <small style="color:var(--text-muted); font-size:0.75rem;">Caisse Interprofessionnelle Marocaine de Retraite.</small>
</div>
<div style="margin-top:1rem; padding-top:1rem; border-top:1px solid var(--border);">
    <button type="submit" class="btn btn-primary">Enregistrer les accès</button>
</div>
</form>
