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
    <div class="card-header"><h3>Comptes téléservices</h3></div>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
        <div class="form-group">
            <label>Damancom (CNSS)</label>
            <input type="text" name="compte_damancom" value="<?= htmlspecialchars($societe['compte_damancom'] ?? '') ?>" class="form-control" placeholder="Compte Damancom">
            <small style="color:var(--text-muted); font-size:0.75rem;">Utilisé pour la déclaration CNSS mensuelle.</small>
        </div>
        <div class="form-group">
            <label>SIMPL (Impôts)</label>
            <input type="text" name="compte_simpl" value="<?= htmlspecialchars($societe['compte_simpl'] ?? '') ?>" class="form-control" placeholder="Compte SIMPL">
            <small style="color:var(--text-muted); font-size:0.75rem;">Utilisé pour la déclaration IR mensuelle.</small>
        </div>
        <div class="form-group">
            <label>CIMR (Retraite)</label>
            <input type="text" name="compte_cimr" value="<?= htmlspecialchars($societe['compte_cimr'] ?? '') ?>" class="form-control" placeholder="Compte CIMR">
            <small style="color:var(--text-muted); font-size:0.75rem;">Caisse Interprofessionnelle Marocaine de Retraite.</small>
        </div>
    </div>
    <div style="margin-top:1rem; padding-top:1rem; border-top:1px solid var(--border);">
        <button type="submit" class="btn btn-primary">Enregistrer</button>
    </div>
</div>
</form>

<?php elseif ($sousTab === 'bareme'): ?>
<div class="card">
    <div class="card-header"><h3>Barème IR 2025</h3></div>
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr><th>Tranche (MAD)</th><th>Taux</th><th>Déduction</th></tr>
            </thead>
            <tbody>
                <?php foreach ($bareme as $b): ?>
                <tr>
                    <td><?= number_format($b['min'], 0, ',', ' ') . ' — ' . number_format($b['max'], 0, ',', ' ') ?></td>
                    <td><?= ($b['taux'] * 100) ?>%</td>
                    <td><?= number_format($b['deduction'], 2, ',', ' ') ?> MAD</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <p style="font-size:0.8125rem; color:var(--text-muted); margin-top:1rem;">
        Barème progressif 2025 appliqué automatiquement au calcul de chaque paie.
        Mettre à jour en janvier 2026 avec les nouvelles tranches.
    </p>
</div>

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
