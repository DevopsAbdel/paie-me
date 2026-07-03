<div class="card">
    <div class="card-header">
        <h3><?= $societe ? 'Modifier' : 'Nouvelle' ?> société</h3>
    </div>
    <form method="POST">
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
                <input type="text" name="banque" class="form-control" value="<?= $societe['banque'] ?? '' ?>">
            </div>
            <div class="form-group">
                <label>Agence</label>
                <input type="text" name="agence" class="form-control" value="<?= $societe['agence'] ?? '' ?>">
            </div>
            <div class="form-group">
                <label>RIB</label>
                <input type="text" name="rib" class="form-control" value="<?= $societe['rib'] ?? '' ?>">
            </div>
        </div>
        <hr style="border-color: var(--border); margin: 1rem 0;">
        <h4 style="margin-bottom:1rem;">Comptes téléservices</h4>
        <div class="form-row">
            <div class="form-group">
                <label>Compte Damancom (CNSS)</label>
                <input type="text" name="compte_damancom" class="form-control" value="<?= $societe['compte_damancom'] ?? '' ?>">
            </div>
            <div class="form-group">
                <label>Login Damancom</label>
                <input type="text" name="damancom_login" class="form-control" value="<?= $societe['damancom_login'] ?? '' ?>">
            </div>
            <div class="form-group">
                <label>Mot de passe Damancom</label>
                <input type="password" name="damancom_password" class="form-control" value="<?= $societe['damancom_password'] ?? '' ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Compte SIMPL (DGI)</label>
                <input type="text" name="compte_simpl" class="form-control" value="<?= $societe['compte_simpl'] ?? '' ?>">
            </div>
            <div class="form-group">
                <label>Login SIMPL</label>
                <input type="text" name="simpl_login" class="form-control" value="<?= $societe['simpl_login'] ?? '' ?>">
            </div>
            <div class="form-group">
                <label>Mot de passe SIMPL</label>
                <input type="password" name="simpl_password" class="form-control" value="<?= $societe['simpl_password'] ?? '' ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Compte CIMR</label>
                <input type="text" name="compte_cimr" class="form-control" value="<?= $societe['compte_cimr'] ?? '' ?>">
            </div>
            <div class="form-group">
                <label>Login CIMR</label>
                <input type="text" name="cimr_login" class="form-control" value="<?= $societe['cimr_login'] ?? '' ?>">
            </div>
            <div class="form-group">
                <label>Mot de passe CIMR</label>
                <input type="password" name="cimr_password" class="form-control" value="<?= $societe['cimr_password'] ?? '' ?>">
            </div>
        </div>
        <div style="display:flex; gap:0.75rem; margin-top:1rem;">
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="/paie-me/societes" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>
