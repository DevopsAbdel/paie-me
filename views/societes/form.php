<div class="card">
    <div class="card-header">
        <h3><?= $societe ? 'Modifier' : 'Nouvelle' ?> société</h3>
    </div>
    <form method="POST" enctype="multipart/form-data">
        <?= \Core\Session::csrfField() ?>
        <div class="form-row">
            <div class="form-group" style="flex:0 0 auto; max-width:100%;">
                <label>Logo</label>
                <div class="logo-upload">
                    <div id="logo-preview" class="logo-preview <?= !empty($societe['logo']) ? 'has-logo' : '' ?>">
                        <?php if (!empty($societe['logo'])): ?>
                            <img src="/paie-me/<?= htmlspecialchars($societe['logo']) ?>" alt="Logo" class="logo-preview-img">
                        <?php else: ?>
                            <span class="logo-preview-empty">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                <span>Pas de logo</span>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="logo-upload-controls">
                        <input type="file" name="logo" id="logo-input" class="form-control logo-file-input" accept="image/png,image/jpeg,image/gif,image/webp,image/svg+xml">
                        <input type="hidden" name="logo_remove" id="logo-remove-field" value="0">
                        <div class="logo-actions">
                            <label for="logo-input" class="btn btn-primary logo-btn">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                Choisir un fichier
                            </label>
                            <?php if (!empty($societe['logo'])): ?>
                            <button type="button" id="logo-remove-btn" class="btn btn-danger logo-remove">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                Retirer le logo
                            </button>
                            <?php endif; ?>
                        </div>
                        <small style="color:var(--text-muted); font-size:0.7rem;">Formats acceptés : PNG, JPG, GIF, WEBP, SVG</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Raison sociale *</label>
                <input type="text" name="raison_sociale" class="form-control" value="<?= $societe['raison_sociale'] ?? '' ?>" required>
            </div>
            <div class="form-group">
                <label>Forme juridique</label>
                <select name="forme_juridique" class="form-control">
                    <?php foreach (['SA', 'SARL', 'SNC', 'SAS', 'SCOP', 'GIE', 'Auto-entrepreneur'] as $f): ?>
                    <option value="<?= $f ?>" <?= ($societe['forme_juridique'] ?? '') === $f ? 'selected' : '' ?>><?= $f ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>ICE *</label>
                <input type="text" name="ice" class="form-control" value="<?= $societe['ice'] ?? '' ?>" required>
            </div>
            <div class="form-group">
                <label>IF (Identifiant Fiscal)</label>
                <input type="text" name="if_fiscal" class="form-control" value="<?= $societe['if_fiscal'] ?? '' ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>RC</label>
                <input type="text" name="rc" class="form-control" value="<?= $societe['rc'] ?? '' ?>">
            </div>
            <div class="form-group">
                <label>TP</label>
                <input type="text" name="tp" class="form-control" value="<?= $societe['tp'] ?? '' ?>">
            </div>
            <div class="form-group">
                <label>CNSS *</label>
                <input type="text" name="cnss" class="form-control" value="<?= $societe['cnss'] ?? '' ?>" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Adresse</label>
                <textarea name="adresse" class="form-control" rows="2"><?= $societe['adresse'] ?? '' ?></textarea>
            </div>
            <div class="form-group">
                <label>Ville</label>
                <input type="text" name="ville" class="form-control" value="<?= $societe['ville'] ?? '' ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Téléphone</label>
                <input type="text" name="telephone" class="form-control" value="<?= $societe['telephone'] ?? '' ?>">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?= $societe['email'] ?? '' ?>">
            </div>
            <div class="form-group">
                <label>Site web</label>
                <input type="url" name="site_web" class="form-control" value="<?= $societe['site_web'] ?? '' ?>">
            </div>
        </div>
        <hr style="border-color: var(--border); margin: 1rem 0;">
        <h4 style="margin-bottom:1rem;">Coordonnées bancaires</h4>
        <div class="form-row">
            <div class="form-group">
                <label>Banque</label>
                <select name="banque" class="form-control">
                    <option value="">— Sélectionner une banque —</option>
                    <optgroup label="Banques universelles">
                        <option value="Attijariwafa Bank" <?= ($societe['banque'] ?? '') === 'Attijariwafa Bank' ? 'selected' : '' ?>>Attijariwafa Bank</option>
                        <option value="Banque Centrale Populaire (BCP)" <?= ($societe['banque'] ?? '') === 'Banque Centrale Populaire (BCP)' ? 'selected' : '' ?>>Banque Centrale Populaire (BCP)</option>
                        <option value="Bank of Africa (BMCE)" <?= ($societe['banque'] ?? '') === 'Bank of Africa (BMCE)' ? 'selected' : '' ?>>Bank of Africa (BMCE)</option>
                        <option value="Saham Bank (ex-SGMB)" <?= ($societe['banque'] ?? '') === 'Saham Bank (ex-SGMB)' ? 'selected' : '' ?>>Saham Bank (ex-Société Générale)</option>
                        <option value="BMCI" <?= ($societe['banque'] ?? '') === 'BMCI' ? 'selected' : '' ?>>BMCI (BNP Paribas)</option>
                        <option value="Crédit du Maroc" <?= ($societe['banque'] ?? '') === 'Crédit du Maroc' ? 'selected' : '' ?>>Crédit du Maroc</option>
                        <option value="CIH Bank" <?= ($societe['banque'] ?? '') === 'CIH Bank' ? 'selected' : '' ?>>CIH Bank</option>
                        <option value="Crédit Agricole du Maroc (CAM)" <?= ($societe['banque'] ?? '') === 'Crédit Agricole du Maroc (CAM)' ? 'selected' : '' ?>>Crédit Agricole du Maroc (CAM)</option>
                        <option value="Al Barid Bank" <?= ($societe['banque'] ?? '') === 'Al Barid Bank' ? 'selected' : '' ?>>Al Barid Bank</option>
                        <option value="CFG Bank" <?= ($societe['banque'] ?? '') === 'CFG Bank' ? 'selected' : '' ?>>CFG Bank</option>
                        <option value="Citibank Maghreb" <?= ($societe['banque'] ?? '') === 'Citibank Maghreb' ? 'selected' : '' ?>>Citibank Maghreb</option>
                        <option value="Arab Bank Maroc" <?= ($societe['banque'] ?? '') === 'Arab Bank Maroc' ? 'selected' : '' ?>>Arab Bank Maroc</option>
                        <option value="Banco Sabadell" <?= ($societe['banque'] ?? '') === 'Banco Sabadell' ? 'selected' : '' ?>>Banco Sabadell</option>
                        <option value="CaixaBank" <?= ($societe['banque'] ?? '') === 'CaixaBank' ? 'selected' : '' ?>>CaixaBank</option>
                        <option value="Union Marocaine de Banques (UMB)" <?= ($societe['banque'] ?? '') === 'Union Marocaine de Banques (UMB)' ? 'selected' : '' ?>>Union Marocaine de Banques (UMB)</option>
                    </optgroup>
                    <optgroup label="Banques participatives">
                        <option value="Bank Assafa" <?= ($societe['banque'] ?? '') === 'Bank Assafa' ? 'selected' : '' ?>>Bank Assafa</option>
                        <option value="Umnia Bank" <?= ($societe['banque'] ?? '') === 'Umnia Bank' ? 'selected' : '' ?>>Umnia Bank</option>
                        <option value="Al Akhdar Bank (AAB)" <?= ($societe['banque'] ?? '') === 'Al Akhdar Bank (AAB)' ? 'selected' : '' ?>>Al Akhdar Bank (AAB)</option>
                        <option value="Bank Al Yousr" <?= ($societe['banque'] ?? '') === 'Bank Al Yousr' ? 'selected' : '' ?>>Bank Al Yousr</option>
                        <option value="Bank Al-Tamweel wa Al-Inma" <?= ($societe['banque'] ?? '') === 'Bank Al-Tamweel wa Al-Inma' ? 'selected' : '' ?>>Bank Al-Tamweel wa Al-Inma</option>
                    </optgroup>
                    <optgroup label="Autres établissements">
                        <option value="CDG Capital" <?= ($societe['banque'] ?? '') === 'CDG Capital' ? 'selected' : '' ?>>CDG Capital</option>
                        <option value="Fonds d'Équipement Communal (FEC)" <?= ($societe['banque'] ?? '') === "Fonds d'Équipement Communal (FEC)" ? 'selected' : '' ?>>Fonds d'Équipement Communal (FEC)</option>
                        <option value="Bank Al-Maghrib" <?= ($societe['banque'] ?? '') === 'Bank Al-Maghrib' ? 'selected' : '' ?>>Bank Al-Maghrib (BAM)</option>
                        <option value="BCP Securities Services" <?= ($societe['banque'] ?? '') === 'BCP Securities Services' ? 'selected' : '' ?>>BCP Securities Services (ex-Mediafinance)</option>
                    </optgroup>
                </select>
            </div>
            <div class="form-group">
                <label>Agence</label>
                <input type="text" name="agence" class="form-control" value="<?= $societe['agence'] ?? '' ?>">
            </div>
            <div class="form-group">
                <label>RIB</label>
                <input type="text" name="rib" class="form-control" value="<?= $societe['rib'] ?? '' ?>" maxlength="24" pattern="[0-9]{24}" title="Le RIB marocain fait 24 chiffres">
            </div>
        </div>
        <hr style="border-color: var(--border); margin: 1rem 0;">
        <h4 style="margin-bottom:1rem;">Accès téléservices</h4>
        <div class="form-row">
            <div class="form-group">
                <label>Login Damancom (CNSS)</label>
                <input type="text" name="damancom_login" class="form-control" value="<?= $societe['damancom_login'] ?? '' ?>">
            </div>
            <div class="form-group">
                <label>Mot de passe Damancom</label>
                <input type="password" name="damancom_password" class="form-control" value="<?= $societe['damancom_password'] ?? '' ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Login SIMPL (Impôts)</label>
                <input type="text" name="simpl_login" class="form-control" value="<?= $societe['simpl_login'] ?? '' ?>">
            </div>
            <div class="form-group">
                <label>Mot de passe SIMPL</label>
                <input type="password" name="simpl_password" class="form-control" value="<?= $societe['simpl_password'] ?? '' ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Login CIMR (Retraite)</label>
                <input type="text" name="cimr_login" class="form-control" value="<?= $societe['cimr_login'] ?? '' ?>">
            </div>
            <div class="form-group">
                <label>Mot de passe CIMR</label>
                <input type="password" name="cimr_password" class="form-control" value="<?= $societe['cimr_password'] ?? '' ?>">
            </div>
        </div>
        <div style="display:flex; gap:0.75rem; margin-top:1rem;">
            <button type="submit" class="btn btn-success">Enregistrer</button>
            <a href="/paie-me/societes" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var input = document.getElementById('logo-input');
    var preview = document.getElementById('logo-preview');
    if (input && preview) {
        input.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    preview.classList.add('has-logo');
                    preview.innerHTML = '<img src="' + e.target.result + '" alt="Logo" class="logo-preview-img">';
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    var removeBtn = document.getElementById('logo-remove-btn');
    var removeField = document.getElementById('logo-remove-field');
    if (removeBtn && removeField) {
        removeBtn.addEventListener('click', function() {
            if (confirm('Supprimer le logo actuel ?')) {
                removeField.value = '1';
                this.closest('form').submit();
            }
        });
    }
});
</script>
