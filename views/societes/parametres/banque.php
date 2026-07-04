<form method="post" action="<?= $baseUrl ?>/banque">
<?= \Core\Session::csrfField() ?>
<input type="hidden" name="sous_tab" value="banque">
<div class="card">
    <div class="card-header"><h3>Coordonnées bancaires</h3></div>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
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
                    <option value="Al Mada (holding)" <?= ($societe['banque'] ?? '') === 'Al Mada (holding)' ? 'selected' : '' ?>>Al Mada (holding)</option>
                </optgroup>
            </select>
        </div>
        <div class="form-group">
            <label>Agence</label>
            <input type="text" name="agence" value="<?= htmlspecialchars($societe['agence'] ?? '') ?>" class="form-control" placeholder="Agence bancaire">
        </div>
        <div class="form-group" style="grid-column:1/-1;">
            <label>RIB</label>
            <input type="text" name="rib" value="<?= htmlspecialchars($societe['rib'] ?? '') ?>" class="form-control" placeholder="RIB complet (24 chiffres)" maxlength="24" pattern="[0-9]{24}" title="Le RIB marocain fait 24 chiffres">
            <small style="color:var(--text-muted); font-size:0.75rem;">Le RIB apparaîtra sur les bulletins de paie. Format : 24 chiffres.</small>
        </div>
    </div>
    <div style="margin-top:1rem; padding-top:1rem; border-top:1px solid var(--border);">
        <button type="submit" class="btn btn-primary">Enregistrer</button>
    </div>
</div>
</form>
