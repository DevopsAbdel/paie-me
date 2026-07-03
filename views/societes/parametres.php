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
    <a href="<?= $baseUrl ?>?tab=cnss_amo" style="padding:0.75rem 1.25rem; color:<?= $sousTab === 'cnss_amo' ? 'var(--accent)' : 'var(--text-muted)' ?>; border-bottom:2px solid <?= $sousTab === 'cnss_amo' ? 'var(--accent)' : 'transparent' ?>; margin-bottom:-2px; font-weight:<?= $sousTab === 'cnss_amo' ? '600' : '400' ?>; text-decoration:none; transition:all 0.2s;">
        CNSS/AMO
    </a>
    <a href="<?= $baseUrl ?>?tab=services" style="padding:0.75rem 1.25rem; color:<?= $sousTab === 'services' ? 'var(--accent)' : 'var(--text-muted)' ?>; border-bottom:2px solid <?= $sousTab === 'services' ? 'var(--accent)' : 'transparent' ?>; margin-bottom:-2px; font-weight:<?= $sousTab === 'services' ? '600' : '400' ?>; text-decoration:none; transition:all 0.2s;">
        Services
    </a>
    <a href="<?= $baseUrl ?>?tab=gains" style="padding:0.75rem 1.25rem; color:<?= $sousTab === 'gains' ? 'var(--accent)' : 'var(--text-muted)' ?>; border-bottom:2px solid <?= $sousTab === 'gains' ? 'var(--accent)' : 'transparent' ?>; margin-bottom:-2px; font-weight:<?= $sousTab === 'gains' ? '600' : '400' ?>; text-decoration:none; transition:all 0.2s;">
        Gains
    </a>
    <a href="<?= $baseUrl ?>?tab=retenues" style="padding:0.75rem 1.25rem; color:<?= $sousTab === 'retenues' ? 'var(--accent)' : 'var(--text-muted)' ?>; border-bottom:2px solid <?= $sousTab === 'retenues' ? 'var(--accent)' : 'transparent' ?>; margin-bottom:-2px; font-weight:<?= $sousTab === 'retenues' ? '600' : '400' ?>; text-decoration:none; transition:all 0.2s;">
        Retenues
    </a>
    <a href="<?= $baseUrl ?>?tab=organismes" style="padding:0.75rem 1.25rem; color:<?= $sousTab === 'organismes' ? 'var(--accent)' : 'var(--text-muted)' ?>; border-bottom:2px solid <?= $sousTab === 'organismes' ? 'var(--accent)' : 'transparent' ?>; margin-bottom:-2px; font-weight:<?= $sousTab === 'organismes' ? '600' : '400' ?>; text-decoration:none; transition:all 0.2s;">
        Organismes
    </a>
    <a href="<?= $baseUrl ?>?tab=attestations" style="padding:0.75rem 1.25rem; color:<?= $sousTab === 'attestations' ? 'var(--accent)' : 'var(--text-muted)' ?>; border-bottom:2px solid <?= $sousTab === 'attestations' ? 'var(--accent)' : 'transparent' ?>; margin-bottom:-2px; font-weight:<?= $sousTab === 'attestations' ? '600' : '400' ?>; text-decoration:none; transition:all 0.2s;">
        Attestations
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

<?php elseif ($sousTab === 'cnss_amo'): ?>
<form method="post" action="<?= $baseUrl ?>">
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

<?php elseif ($sousTab === 'services'): ?>
<div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; gap:1rem;">
        <h3 style="margin:0;">Services</h3>
        <form method="post" action="<?= $baseUrl ?>" style="display:flex; gap:0.5rem; align-items:center;">
            <input type="hidden" name="sous_tab" value="services">
            <input type="text" name="service_nom" class="form-control" placeholder="Nom du service" style="width:180px;" required>
            <input type="text" name="service_description" class="form-control" placeholder="Description" style="width:240px;">
            <button type="submit" class="btn btn-primary btn-sm">Ajouter</button>
        </form>
    </div>
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr><th>Nom</th><th>Description</th><th>Actif</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php if (empty($services)): ?>
                <tr><td colspan="4" style="text-align:center; color:var(--text-muted);">Aucun service</td></tr>
                <?php else: ?>
                <?php foreach ($services as $s): ?>
                <tr>
                    <td><?= htmlspecialchars($s['nom']) ?></td>
                    <td><?= htmlspecialchars($s['description'] ?? '') ?></td>
                    <td><span class="badge badge-<?= $s['actif'] ? 'success' : 'secondary' ?>"><?= $s['actif'] ? 'Oui' : 'Non' ?></span></td>
                    <td>
                        <a href="<?= $baseUrl ?>?tab=services&delete_service=<?= $s['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce service ?')">Supprimer</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php elseif ($sousTab === 'gains'): ?>
<div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; gap:1rem;">
        <h3 style="margin:0;">Gains</h3>
        <form method="post" action="<?= $baseUrl ?>" style="display:flex; gap:0.5rem; align-items:center;">
            <input type="hidden" name="sous_tab" value="gains">
            <input type="text" name="code" class="form-control" placeholder="Code" style="width:100px;" required>
            <input type="text" name="libelle" class="form-control" placeholder="Libellé" style="width:180px;" required>
            <select name="type_montant" class="form-control" style="width:130px;">
                <option value="fixe">Fixe</option>
                <option value="proportionnel">Proportionnel</option>
            </select>
            <input type="number" name="valeur_defaut" class="form-control" placeholder="Valeur défaut" style="width:120px;" step="0.01">
            <button type="submit" class="btn btn-primary btn-sm">Ajouter</button>
        </form>
    </div>
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr><th>Code</th><th>Libellé</th><th>Type</th><th>Valeur défaut</th><th>Imposable</th><th>Actif</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php if (empty($gains)): ?>
                <tr><td colspan="7" style="text-align:center; color:var(--text-muted);">Aucun gain</td></tr>
                <?php else: ?>
                <?php foreach ($gains as $g): ?>
                <tr>
                    <td><?= htmlspecialchars($g['code']) ?></td>
                    <td><?= htmlspecialchars($g['libelle']) ?></td>
                    <td><?= htmlspecialchars($g['type_montant']) ?></td>
                    <td><?= htmlspecialchars($g['valeur_defaut'] ?? '') ?></td>
                    <td><span class="badge badge-<?= $g['imposable'] ? 'warning' : 'secondary' ?>"><?= $g['imposable'] ? 'Oui' : 'Non' ?></span></td>
                    <td><span class="badge badge-<?= $g['actif'] ? 'success' : 'secondary' ?>"><?= $g['actif'] ? 'Oui' : 'Non' ?></span></td>
                    <td>
                        <a href="<?= $baseUrl ?>?tab=gains&delete_gain=<?= $g['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce gain ?')">Supprimer</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php elseif ($sousTab === 'retenues'): ?>
<div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; gap:1rem;">
        <h3 style="margin:0;">Retenues</h3>
        <form method="post" action="<?= $baseUrl ?>" style="display:flex; gap:0.5rem; align-items:center;">
            <input type="hidden" name="sous_tab" value="retenues">
            <input type="text" name="code" class="form-control" placeholder="Code" style="width:100px;" required>
            <input type="text" name="libelle" class="form-control" placeholder="Libellé" style="width:180px;" required>
            <select name="type_montant" class="form-control" style="width:130px;">
                <option value="fixe">Fixe</option>
                <option value="proportionnel">Proportionnel</option>
            </select>
            <input type="number" name="valeur_defaut" class="form-control" placeholder="Valeur défaut" style="width:120px;" step="0.01">
            <button type="submit" class="btn btn-primary btn-sm">Ajouter</button>
        </form>
    </div>
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr><th>Code</th><th>Libellé</th><th>Type</th><th>Valeur défaut</th><th>Actif</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php if (empty($retenues)): ?>
                <tr><td colspan="6" style="text-align:center; color:var(--text-muted);">Aucune retenue</td></tr>
                <?php else: ?>
                <?php foreach ($retenues as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['code']) ?></td>
                    <td><?= htmlspecialchars($r['libelle']) ?></td>
                    <td><?= htmlspecialchars($r['type_montant']) ?></td>
                    <td><?= htmlspecialchars($r['valeur_defaut'] ?? '') ?></td>
                    <td><span class="badge badge-<?= $r['actif'] ? 'success' : 'secondary' ?>"><?= $r['actif'] ? 'Oui' : 'Non' ?></span></td>
                    <td>
                        <a href="<?= $baseUrl ?>?tab=retenues&delete_retenue=<?= $r['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cette retenue ?')">Supprimer</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php elseif ($sousTab === 'organismes'): ?>
<div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; gap:1rem;">
        <h3 style="margin:0;">Organismes</h3>
        <form method="post" action="<?= $baseUrl ?>" style="display:flex; gap:0.5rem; align-items:center;">
            <input type="hidden" name="sous_tab" value="organismes">
            <input type="text" name="nom" class="form-control" placeholder="Nom" style="width:150px;" required>
            <select name="type" class="form-control" style="width:110px;">
                <option value="cnss">CNSS</option>
                <option value="amo">AMO</option>
                <option value="cimr">CIMR</option>
                <option value="mutuelle">Mutuelle</option>
                <option value="autre">Autre</option>
            </select>
            <input type="text" name="login" class="form-control" placeholder="Login" style="width:120px;">
            <input type="password" name="mot_de_passe" class="form-control" placeholder="Mot de passe" style="width:120px;">
            <button type="submit" class="btn btn-primary btn-sm">Ajouter</button>
        </form>
    </div>
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr><th>Nom</th><th>Type</th><th>Login</th><th>Actif</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php if (empty($organismes)): ?>
                <tr><td colspan="5" style="text-align:center; color:var(--text-muted);">Aucun organisme</td></tr>
                <?php else: ?>
                <?php foreach ($organismes as $o): ?>
                <tr>
                    <td><?= htmlspecialchars($o['nom']) ?></td>
                    <td><span class="badge badge-info"><?= htmlspecialchars($o['type']) ?></span></td>
                    <td><?= htmlspecialchars($o['login'] ?? '') ?></td>
                    <td><span class="badge badge-<?= $o['actif'] ? 'success' : 'secondary' ?>"><?= $o['actif'] ? 'Oui' : 'Non' ?></span></td>
                    <td>
                        <a href="<?= $baseUrl ?>?tab=organismes&delete_organisme=<?= $o['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cet organisme ?')">Supprimer</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php elseif ($sousTab === 'attestations'): ?>
<div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; gap:1rem;">
        <h3 style="margin:0;">Attestations</h3>
        <form method="post" action="<?= $baseUrl ?>" style="display:flex; gap:0.5rem; align-items:center;">
            <input type="hidden" name="sous_tab" value="attestations">
            <input type="text" name="titre" class="form-control" placeholder="Titre" style="width:180px;" required>
            <textarea name="contenu" class="form-control" placeholder="Contenu du modèle" style="width:240px; height:32px; resize:vertical;" required></textarea>
            <button type="submit" class="btn btn-primary btn-sm">Ajouter</button>
        </form>
    </div>
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr><th>Titre</th><th>Actif</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php if (empty($attestations)): ?>
                <tr><td colspan="3" style="text-align:center; color:var(--text-muted);">Aucune attestation</td></tr>
                <?php else: ?>
                <?php foreach ($attestations as $a): ?>
                <tr>
                    <td><?= htmlspecialchars($a['titre']) ?></td>
                    <td><span class="badge badge-<?= $a['actif'] ? 'success' : 'secondary' ?>"><?= $a['actif'] ? 'Oui' : 'Non' ?></span></td>
                    <td>
                        <a href="<?= $baseUrl ?>?tab=attestations&delete_attestation=<?= $a['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cette attestation ?')">Supprimer</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php endif; ?>
