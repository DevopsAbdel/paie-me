<?php
$sousTab = $_GET['tab'] ?? 'banque';
$baseUrl = '/paie-me/societes/' . $societe['id'] . '/parametres';
?>
<div style="display:flex; gap:2rem; margin-bottom:1.5rem; align-items:center;">
    <div>
        <h2 style="color:var(--accent); margin:0;">Paramètres</h2>
        <p style="color:var(--text-muted); font-size:0.875rem; margin:0.25rem 0 0 0;">
            <?= htmlspecialchars($societe['raison_sociale']) ?>
        </p>
    </div>
</div>

<div style="display:flex; gap:0; border-bottom:2px solid var(--border); margin-bottom:1.5rem;">
    <a href="<?= $baseUrl ?>?tab=banque" style="padding:0.75rem 1.25rem; color:<?= $sousTab === 'banque' ? 'var(--accent)' : 'var(--text-muted)' ?>; border-bottom:2px solid <?= $sousTab === 'banque' ? 'var(--accent)' : 'transparent' ?>; margin-bottom:-2px; font-weight:<?= $sousTab === 'banque' ? '600' : '400' ?>; text-decoration:none; transition:all 0.2s;">
        Banque
    </a>
    <a href="<?= $baseUrl ?>?tab=teleservices" style="padding:0.75rem 1.25rem; color:<?= $sousTab === 'teleservices' ? 'var(--accent)' : 'var(--text-muted)' ?>; border-bottom:2px solid <?= $sousTab === 'teleservices' ? 'var(--accent)' : 'transparent' ?>; margin-bottom:-2px; font-weight:<?= $sousTab === 'teleservices' ? '600' : '400' ?>; text-decoration:none; transition:all 0.2s;">
        Téléservices
    </a>
    <a href="<?= $baseUrl ?>?tab=bareme" style="padding:0.75rem 1.25rem; color:<?= $sousTab === 'bareme' ? 'var(--accent)' : 'var(--text-muted)' ?>; border-bottom:2px solid <?= $sousTab === 'bareme' ? 'var(--accent)' : 'transparent' ?>; margin-bottom:-2px; font-weight:<?= $sousTab === 'bareme' ? '600' : '400' ?>; text-decoration:none; transition:all 0.2s;">
        Barème IR
    </a>
    <a href="<?= $baseUrl ?>?tab=codification" style="padding:0.75rem 1.25rem; color:<?= $sousTab === 'codification' ? 'var(--accent)' : 'var(--text-muted)' ?>; border-bottom:2px solid <?= $sousTab === 'codification' ? 'var(--accent)' : 'transparent' ?>; margin-bottom:-2px; font-weight:<?= $sousTab === 'codification' ? '600' : '400' ?>; text-decoration:none; transition:all 0.2s;">
        Codification
    </a>
    <a href="<?= $baseUrl ?>?tab=general" style="padding:0.75rem 1.25rem; color:<?= $sousTab === 'general' ? 'var(--accent)' : 'var(--text-muted)' ?>; border-bottom:2px solid <?= $sousTab === 'general' ? 'var(--accent)' : 'transparent' ?>; margin-bottom:-2px; font-weight:<?= $sousTab === 'general' ? '600' : '400' ?>; text-decoration:none; transition:all 0.2s;">
        Général
    </a>
</div>

<?php if ($sousTab === 'general'): ?>
<div class="card">
    <div class="card-header"><h3>Informations générales</h3></div>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
        <div><strong>Raison sociale :</strong> <?= htmlspecialchars($societe['raison_sociale']) ?></div>
        <div><strong>Forme juridique :</strong> <?= $societe['forme_juridique'] ?></div>
        <div><strong>ICE :</strong> <?= htmlspecialchars($societe['ice']) ?></div>
        <div><strong>IF :</strong> <?= htmlspecialchars($societe['if_fiscal']) ?></div>
        <div><strong>RC :</strong> <?= htmlspecialchars($societe['rc']) ?></div>
        <div><strong>TP :</strong> <?= htmlspecialchars($societe['tp']) ?></div>
        <div><strong>CNSS :</strong> <?= htmlspecialchars($societe['cnss']) ?></div>
        <div><strong>Ville :</strong> <?= htmlspecialchars($societe['ville']) ?></div>
        <div style="grid-column:1/-1;"><strong>Adresse :</strong> <?= htmlspecialchars($societe['adresse'] ?? '') ?></div>
        <div><strong>Téléphone :</strong> <?= htmlspecialchars($societe['telephone'] ?? '') ?></div>
        <div><strong>Email :</strong> <?= htmlspecialchars($societe['email'] ?? '') ?></div>
    </div>
    <div style="margin-top:1rem;">
        <a href="/paie-me/societes/<?= $societe['id'] ?>/edit" class="btn btn-secondary btn-sm">Modifier les infos</a>
    </div>
</div>

<?php elseif ($sousTab === 'banque'): ?>
<form method="post" action="<?= $baseUrl ?>">
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

<?php elseif ($sousTab === 'teleservices'): ?>
<form method="post" action="<?= $baseUrl ?>">
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

<?php elseif ($sousTab === 'bareme'): ?>
<form method="post" action="<?= $baseUrl ?>">
<input type="hidden" name="sous_tab" value="bareme">
<?php $types = [['key'=>'mensuel','label'=>'Mensuel','data'=>$bareme], ['key'=>'annuel','label'=>'Annuel','data'=>$baremeAnnuel]]; ?>
<?php foreach ($types as $t): ?>
<div class="card" style="<?= $t['key'] === 'annuel' ? 'margin-top:1.5rem;' : '' ?>">
    <div class="card-header"><h3>Barème IR 2025 — <?= $t['label'] ?></h3></div>
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr><th>Tranche min (MAD)</th><th>Tranche max (MAD)</th><th>Taux (%)</th><th>Déduction (MAD)</th></tr>
            </thead>
            <tbody>
                <?php foreach ($t['data'] as $b): ?>
                <tr>
                    <td><input type="number" name="min[<?= $b['id'] ?>]" value="<?= $b['min'] ?>" class="form-control" step="0.01" style="width:120px;"></td>
                    <td><input type="number" name="max[<?= $b['id'] ?>]" value="<?= $b['max'] ?>" class="form-control" step="0.01" style="width:120px;"></td>
                    <td><input type="number" name="taux[<?= $b['id'] ?>]" value="<?= $b['taux'] ?>" class="form-control" step="0.01" style="width:80px;"></td>
                    <td><input type="number" name="deduction[<?= $b['id'] ?>]" value="<?= $b['deduction'] ?>" class="form-control" step="0.01" style="width:120px;"></td>
                    <input type="hidden" name="type[<?= $b['id'] ?>]" value="<?= $b['type'] ?>">
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endforeach; ?>
<div style="margin-top:1rem; padding-top:1rem; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
    <p style="font-size:0.8125rem; color:var(--text-muted); margin:0;">Barème progressif 2025 appliqué automatiquement au calcul de chaque paie.</p>
    <button type="submit" class="btn btn-primary">Mettre à jour le barème</button>
</div>
</form>

<?php elseif ($sousTab === 'codification'): ?>
<div class="card">
    <div class="card-header"><h3>Codification & numérotation</h3></div>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
        <div class="form-group">
            <label>Préfixe matricule salarié</label>
            <input type="text" value="EMP-" class="form-control" disabled>
            <small style="color:var(--text-muted); font-size:0.75rem;">Le matricule est généré automatiquement.</small>
        </div>
        <div class="form-group">
            <label>Préfixe numéro bulletin</label>
            <input type="text" value="BUL-" class="form-control" disabled>
            <small style="color:var(--text-muted); font-size:0.75rem;">Le numéro de bulletin est auto-incrémenté.</small>
        </div>
        <div class="form-group">
            <label>Format période paie</label>
            <input type="text" value="MM/AAAA" class="form-control" disabled>
        </div>
        <div class="form-group">
            <label>Format fichier DS</label>
            <input type="text" value="DS_CNSS_MMAAAA.txt" class="form-control" disabled>
        </div>
        <div class="form-group">
            <label>Format export IR</label>
            <input type="text" value="IR_IF_MMAAAA.csv" class="form-control" disabled>
        </div>
    </div>
</div>
<?php endif; ?>
