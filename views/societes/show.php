<?php
$tab = $_GET['tab'] ?? 'infos';
$baseUrl = '/paie-me/societes/' . $societe['id'];
?>

<div style="display:flex; gap:2rem; margin-bottom:1.5rem; align-items:center;">
    <div>
        <h2 style="color:var(--accent); margin:0;"><?= htmlspecialchars($societe['raison_sociale']) ?></h2>
        <p style="color:var(--text-muted); font-size:0.875rem; margin:0.25rem 0 0 0;">
            <?= $societe['forme_juridique'] ?> — ICE: <?= htmlspecialchars($societe['ice']) ?>
        </p>
    </div>
    <div style="margin-left:auto; display:flex; gap:0.5rem;">
        <a href="/paie-me/societes/<?= $societe['id'] ?>/edit" class="btn btn-secondary btn-sm">Modifier</a>
        <a href="/paie-me/societes/<?= $societe['id'] ?>/delete" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cette société ?')">Supprimer</a>
    </div>
</div>

<div style="display:flex; gap:0; border-bottom:2px solid var(--border); margin-bottom:1.5rem;">
    <a href="<?= $baseUrl ?>?tab=infos" style="padding:0.75rem 1.25rem; color:<?= $tab === 'infos' ? 'var(--accent)' : 'var(--text-muted)' ?>; border-bottom:2px solid <?= $tab === 'infos' ? 'var(--accent)' : 'transparent' ?>; margin-bottom:-2px; font-weight:<?= $tab === 'infos' ? '600' : '400' ?>; text-decoration:none; transition:all 0.2s;">
        Infos société
    </a>
    <a href="<?= $baseUrl ?>?tab=salaries" style="padding:0.75rem 1.25rem; color:<?= $tab === 'salaries' ? 'var(--accent)' : 'var(--text-muted)' ?>; border-bottom:2px solid <?= $tab === 'salaries' ? 'var(--accent)' : 'transparent' ?>; margin-bottom:-2px; font-weight:<?= $tab === 'salaries' ? '600' : '400' ?>; text-decoration:none; transition:all 0.2s;">
        Salariés (<?= count($salaries) ?>)
    </a>
    <a href="<?= $baseUrl ?>?tab=paies" style="padding:0.75rem 1.25rem; color:<?= $tab === 'paies' ? 'var(--accent)' : 'var(--text-muted)' ?>; border-bottom:2px solid <?= $tab === 'paies' ? 'var(--accent)' : 'transparent' ?>; margin-bottom:-2px; font-weight:<?= $tab === 'paies' ? '600' : '400' ?>; text-decoration:none; transition:all 0.2s;">
        Paies
    </a>
    <a href="<?= $baseUrl ?>?tab=parametres" style="padding:0.75rem 1.25rem; color:<?= $tab === 'parametres' ? 'var(--accent)' : 'var(--text-muted)' ?>; border-bottom:2px solid <?= $tab === 'parametres' ? 'var(--accent)' : 'transparent' ?>; margin-bottom:-2px; font-weight:<?= $tab === 'parametres' ? '600' : '400' ?>; text-decoration:none; transition:all 0.2s;">
        Paramètres
    </a>
</div>

<?php if ($tab === 'infos'): ?>
<div class="card">
    <div class="card-header"><h3>Coordonnées</h3></div>
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
        <div><strong>Banque :</strong> <?= htmlspecialchars($societe['banque'] ?? '') ?></div>
        <div><strong>RIB :</strong> <?= htmlspecialchars($societe['rib'] ?? '') ?></div>
        <div><strong>Damancom :</strong> <?= htmlspecialchars($societe['compte_damancom'] ?? '') ?></div>
        <div><strong>SIMPL :</strong> <?= htmlspecialchars($societe['compte_simpl'] ?? '') ?></div>
    </div>
</div>

<?php elseif ($tab === 'salaries'): ?>
<div class="card">
    <div class="card-header">
        <h3>Salariés — <?= htmlspecialchars($societe['raison_sociale']) ?></h3>
        <a href="/paie-me/salaries/create?from_societe=<?= $societe['id'] ?>" class="btn btn-primary btn-sm">+ Nouveau</a>
    </div>
    <?php if (empty($salaries)): ?>
        <div class="empty-state"><p>Aucun salarié dans cette société.</p></div>
    <?php else: ?>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Matricule</th><th>Nom</th><th>Prénom</th><th>Poste</th>
                    <th>Salaire</th><th>CNSS</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($salaries as $s): ?>
                <tr>
                    <td><?= htmlspecialchars($s['matricule']) ?></td>
                    <td><?= htmlspecialchars($s['nom_famille']) ?></td>
                    <td><?= htmlspecialchars($s['prenom']) ?></td>
                    <td><?= htmlspecialchars($s['poste']) ?></td>
                    <td><?= number_format($s['salaire_base'], 2, ',', ' ') ?></td>
                    <td><?= htmlspecialchars($s['cnss']) ?></td>
                    <td class="table-actions">
                        <a href="/paie-me/salaries/<?= $s['id'] ?>/edit?from_societe=<?= $societe['id'] ?>" class="btn btn-secondary btn-sm">Modifier</a>
                        <a href="/paie-me/salaries/<?= $s['id'] ?>/delete" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ?')">Supprimer</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php elseif ($tab === 'paies'): ?>
<div class="card">
    <div class="card-header">
        <h3>Paies — <?= htmlspecialchars($societe['raison_sociale']) ?></h3>
        <a href="/paie-me/paies/create?from_societe=<?= $societe['id'] ?>" class="btn btn-primary btn-sm">+ Nouvelle période</a>
    </div>
    <?php if (empty($periodes)): ?>
        <div class="empty-state"><p>Aucune période de paie pour cette société.</p></div>
    <?php else: ?>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Période</th><th>Du</th><th>Au</th><th>Salariés</th><th>Statut</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($periodes as $p): ?>
                <tr>
                    <td><?= str_pad($p['mois'], 2, '0', STR_PAD_LEFT) ?>/<?= $p['annee'] ?></td>
                    <td><?= $p['date_debut'] ?></td><td><?= $p['date_fin'] ?></td>
                    <td><?= (int) $p['nb_paies'] ?></td>
                    <td><span class="badge badge-<?= $p['cloturee'] ? 'success' : 'warning' ?>"><?= $p['cloturee'] ? 'Clôturée' : 'En cours' ?></span></td>
                    <td class="table-actions">
                        <a href="/paie-me/paies/<?= $p['id'] ?>/calculate" class="btn btn-secondary btn-sm" onclick="return confirm('Recalculer ?')">Recalculer</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php elseif ($tab === 'parametres'): ?>
<div class="card">
    <div class="card-header"><h3>Paramètres — <?= htmlspecialchars($societe['raison_sociale']) ?></h3></div>
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(250px,1fr)); gap:1rem;">

        <div class="card" style="margin:0;">
            <h4 style="color:var(--accent); margin-bottom:0.75rem;">Barèmes</h4>
            <p style="color:var(--text-muted); font-size:0.875rem;">IR, CNSS, AMO</p>
            <p style="font-size:0.8125rem; color:var(--text-muted);">Barème IR 2025 chargé automatiquement. Mise à jour annuelle requise.</p>
        </div>

        <div class="card" style="margin:0;">
            <h4 style="color:var(--accent); margin-bottom:0.75rem;">Services</h4>
            <p style="color:var(--text-muted); font-size:0.875rem;">Organisation interne</p>
            <p style="font-size:0.8125rem; color:var(--text-muted);">Définir les services (RH, Compta, Production…) et affecter les salariés.</p>
        </div>

        <div class="card" style="margin:0;">
            <h4 style="color:var(--accent); margin-bottom:0.75rem;">Gains</h4>
            <p style="color:var(--text-muted); font-size:0.875rem;">Éléments de rémunération</p>
            <p style="font-size:0.8125rem; color:var(--text-muted);">Salaire de base, indemnités transport/panier, primes.</p>
        </div>

        <div class="card" style="margin:0;">
            <h4 style="color:var(--accent); margin-bottom:0.75rem;">Retenues</h4>
            <p style="color:var(--text-muted); font-size:0.875rem;">Déductions salariales</p>
            <p style="font-size:0.8125rem; color:var(--text-muted);">CNSS, AMO, IR, avances sur salaire, mutuelle.</p>
        </div>

        <div class="card" style="margin:0;">
            <h4 style="color:var(--accent); margin-bottom:0.75rem;">Comptes téléservices</h4>
            <p style="color:var(--text-muted); font-size:0.875rem;">Damancom, SIMPL, CIMR</p>
            <p style="font-size:0.8125rem; color:var(--text-muted);">
                Damancom: <?= htmlspecialchars($societe['compte_damancom'] ?: 'Non configuré') ?><br>
                SIMPL: <?= htmlspecialchars($societe['compte_simpl'] ?: 'Non configuré') ?><br>
                CIMR: <?= htmlspecialchars($societe['compte_cimr'] ?: 'Non configuré') ?>
            </p>
        </div>

        <div class="card" style="margin:0;">
            <h4 style="color:var(--accent); margin-bottom:0.75rem;">Codification</h4>
            <p style="color:var(--text-muted); font-size:0.875rem;">Numérotation automatique</p>
            <p style="font-size:0.8125rem; color:var(--text-muted);">Codes rubriques paie, références internes salariés et documents.</p>
        </div>

    </div>
</div>
<?php endif; ?>
