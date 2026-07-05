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
    <a href="<?= $baseUrl ?>?tab=bulletins" style="padding:0.75rem 1.25rem; color:<?= $tab === 'bulletins' ? 'var(--accent)' : 'var(--text-muted)' ?>; border-bottom:2px solid <?= $tab === 'bulletins' ? 'var(--accent)' : 'transparent' ?>; margin-bottom:-2px; font-weight:<?= $tab === 'bulletins' ? '600' : '400' ?>; text-decoration:none; transition:all 0.2s;">
        Bulletins (<?= count($bulletins) ?>)
    </a>
    <a href="<?= $baseUrl ?>?tab=cnss" style="padding:0.75rem 1.25rem; color:<?= $tab === 'cnss' ? 'var(--accent)' : 'var(--text-muted)' ?>; border-bottom:2px solid <?= $tab === 'cnss' ? 'var(--accent)' : 'transparent' ?>; margin-bottom:-2px; font-weight:<?= $tab === 'cnss' ? '600' : '400' ?>; text-decoration:none; transition:all 0.2s;">
        CNSS
    </a>
    <a href="<?= $baseUrl ?>?tab=ir" style="padding:0.75rem 1.25rem; color:<?= $tab === 'ir' ? 'var(--accent)' : 'var(--text-muted)' ?>; border-bottom:2px solid <?= $tab === 'ir' ? 'var(--accent)' : 'transparent' ?>; margin-bottom:-2px; font-weight:<?= $tab === 'ir' ? '600' : '400' ?>; text-decoration:none; transition:all 0.2s;">
        IR
    </a>
</div>

<?php if ($tab === 'infos'): ?>
<div class="card">
    <div class="card-header"><h3>Informations société</h3></div>

    <h4 class="form-section-title">Identité</h4>
    <hr class="form-section-sep">
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
        <div><strong>Raison sociale :</strong> <?= htmlspecialchars($societe['raison_sociale']) ?></div>
        <div><strong>Forme juridique :</strong> <?= $societe['forme_juridique'] ?></div>
    </div>

    <h4 class="form-section-title">Immatriculations</h4>
    <hr class="form-section-sep">
    <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:1rem;">
        <div><strong>ICE :</strong> <?= htmlspecialchars($societe['ice']) ?></div>
        <div><strong>IF :</strong> <?= htmlspecialchars($societe['if_fiscal']) ?></div>
        <div><strong>RC :</strong> <?= htmlspecialchars($societe['rc']) ?></div>
        <div><strong>TP :</strong> <?= htmlspecialchars($societe['tp']) ?></div>
        <div><strong>CNSS :</strong> <?= htmlspecialchars($societe['cnss']) ?></div>
    </div>

    <h4 class="form-section-title">Coordonnées</h4>
    <hr class="form-section-sep">
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
        <div><strong>Adresse :</strong> <?= htmlspecialchars($societe['adresse'] ?? '') ?></div>
        <div><strong>Ville :</strong> <?= htmlspecialchars($societe['ville']) ?></div>
        <div><strong>Téléphone :</strong> <?= htmlspecialchars($societe['telephone'] ?? '') ?></div>
        <div><strong>Email :</strong> <?= htmlspecialchars($societe['email'] ?? '') ?></div>
        <div><strong>Site web :</strong> <?= htmlspecialchars($societe['site_web'] ?? '') ?></div>
    </div>

    <h4 class="form-section-title">Coordonnées bancaires</h4>
    <hr class="form-section-sep">
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
        <div><strong>Banque :</strong> <?= htmlspecialchars($societe['banque'] ?? '') ?></div>
        <div><strong>RIB :</strong> <?= htmlspecialchars($societe['rib'] ?? '') ?></div>
    </div>

    <h4 class="form-section-title">Téléservices</h4>
    <hr class="form-section-sep">
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
        <div><strong>Damancom :</strong> <?= htmlspecialchars($societe['damancom_login'] ?? 'Non configuré') ?></div>
        <div><strong>SIMPL :</strong> <?= htmlspecialchars($societe['simpl_login'] ?? 'Non configuré') ?></div>
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
                    <td><?= htmlspecialchars($s['fonction_nom'] ?? $s['poste']) ?></td>
                    <td><?= number_format($s['salaire_base'], 2, ',', ' ') ?></td>
                    <td><?= htmlspecialchars($s['cnss']) ?></td>
                    <td>
                        <div class="table-actions">
                            <a href="/paie-me/salaries/<?= $s['id'] ?>/edit?from_societe=<?= $societe['id'] ?>" class="btn btn-secondary btn-sm">Modifier</a>
                            <a href="/paie-me/salaries/<?= $s['id'] ?>/delete" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ?')">Supprimer</a>
                        </div>
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
                    <td>
                        <div class="table-actions">
                            <a href="/paie-me/paies/<?= $p['id'] ?>/calculate" class="btn btn-secondary btn-sm" onclick="return confirm('Recalculer ?')">Recalculer</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php elseif ($tab === 'bulletins'): ?>
<div class="card">
    <div class="card-header">
        <h3>Bulletins de paie — <?= htmlspecialchars($societe['raison_sociale']) ?></h3>
    </div>
    <?php if (empty($bulletins)): ?>
        <div class="empty-state"><p>Aucun bulletin généré pour cette société.</p></div>
    <?php else: ?>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>N° Bulletin</th><th>Période</th><th>Salarié</th>
                    <th>Salaire brut</th><th>Net à payer</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bulletins as $b): ?>
                <tr>
                    <td><?= htmlspecialchars($b['numero']) ?></td>
                    <td><?= str_pad($b['mois'], 2, '0', STR_PAD_LEFT) ?>/<?= $b['annee'] ?></td>
                    <td><?= htmlspecialchars($b['nom_famille']) ?> <?= htmlspecialchars($b['prenom']) ?></td>
                    <td><?= number_format($b['salaire_brut'], 2, ',', ' ') ?></td>
                    <td><strong style="color:var(--accent);"><?= number_format($b['net_a_payer'], 2, ',', ' ') ?></strong></td>
                    <td>
                        <div class="table-actions">
                            <a href="/paie-me/bulletins/<?= $b['id'] ?>" class="btn btn-secondary btn-sm">Voir</a>
                            <a href="/paie-me/bulletins/<?= $b['id'] ?>/pdf" class="btn btn-primary btn-sm" target="_blank">PDF</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php elseif ($tab === 'cnss'): ?>
<div class="card">
    <div class="card-header">
        <h3>CNSS / Damancom — <?= htmlspecialchars($societe['raison_sociale']) ?></h3>
        <a href="/paie-me/damancom" class="btn btn-primary btn-sm">Générer fichier DS</a>
    </div>
    <?php if (empty($periodes)): ?>
        <div class="empty-state"><p>Aucune période de paie. Créez une paie avant de générer la déclaration CNSS.</p></div>
    <?php else: ?>
    <div style="padding:1rem;">
        <h4 style="color:var(--accent); margin-bottom:0.75rem;">Déclarations par période</h4>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr><th>Période</th><th>Salariés</th><th>Statut</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($periodes as $p): ?>
                    <tr>
                        <td><?= str_pad($p['mois'], 2, '0', STR_PAD_LEFT) ?>/<?= $p['annee'] ?></td>
                        <td><?= (int)$p['nb_paies'] ?></td>
                        <td><span class="badge badge-<?= $p['cloturee'] ? 'success' : 'warning' ?>"><?= $p['cloturee'] ? 'Clôturée' : 'En cours' ?></span></td>
                        <td>
                            <div class="table-actions">
                                <a href="/paie-me/damancom/generate?periode_id=<?= $p['id'] ?>&from_societe=<?= $societe['id'] ?>" class="btn btn-secondary btn-sm">DS</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div style="margin-top:1.5rem; background:var(--bg-secondary); border-radius:8px; padding:1rem;">
            <h4 style="color:var(--accent); margin-bottom:0.5rem;">Damancom (CNSS)</h4>
            <p style="font-size:0.875rem; color:var(--text-muted);">
                Login : <strong><?= htmlspecialchars($societe['damancom_login'] ?: 'Non configuré') ?></strong><br>
                CNSS : <strong><?= htmlspecialchars($societe['cnss']) ?></strong>
            </p>
            <p style="font-size:0.8125rem; color:var(--text-muted);">
                Le fichier DS est généré au format XML conforme à la spécification Damancom de la CNSS.
            </p>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php elseif ($tab === 'ir'): ?>
<div class="card">
    <div class="card-header">
        <h3>IR / SIMPL — <?= htmlspecialchars($societe['raison_sociale']) ?></h3>
        <a href="/paie-me/ir" class="btn btn-primary btn-sm">Export CSV IR</a>
    </div>
    <?php if (empty($periodes)): ?>
        <div class="empty-state"><p>Aucune période de paie. Créez une paie avant d'exporter l'IR.</p></div>
    <?php else: ?>
    <div style="padding:1rem;">
        <h4 style="color:var(--accent); margin-bottom:0.75rem;">Récapitulatif IR par période</h4>
        <?php
        $irByPeriode = [];
        foreach ($bulletins as $b) {
            $key = sprintf("%02d/%d", $b['mois'], $b['annee']);
            if (!isset($irByPeriode[$key])) {
                $irByPeriode[$key] = ['mois' => $b['mois'], 'annee' => $b['annee'], 'salaries' => 0, 'total_ir' => 0];
            }
            $irByPeriode[$key]['salaries']++;
            $irByPeriode[$key]['total_ir'] += $b['ir'];
        }
        ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr><th>Période</th><th>Salariés</th><th>Total IR</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($periodes as $p): ?>
                    <?php $key = sprintf("%02d/%d", $p['mois'], $p['annee']); ?>
                    <tr>
                        <td><?= $key ?></td>
                        <td><?= (int)$p['nb_paies'] ?></td>
                        <td><strong style="color:var(--accent);"><?= number_format($irByPeriode[$key]['total_ir'] ?? 0, 2, ',', ' ') ?> MAD</strong></td>
                        <td>
                            <div class="table-actions">
                                <a href="/paie-me/ir/export?periode_id=<?= $p['id'] ?>" class="btn btn-secondary btn-sm">CSV</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div style="margin-top:1.5rem; background:var(--bg-secondary); border-radius:8px; padding:1rem;">
            <h4 style="color:var(--accent); margin-bottom:0.5rem;">SIMPL (Impôts)</h4>
            <p style="font-size:0.875rem; color:var(--text-muted);">
                Login : <strong><?= htmlspecialchars($societe['simpl_login'] ?: 'Non configuré') ?></strong><br>
                IF : <strong><?= htmlspecialchars($societe['if_fiscal']) ?></strong>
            </p>
            <p style="font-size:0.8125rem; color:var(--text-muted);">
                L'export CSV est conforme au format d'import SIMPL de la Direction Générale des Impôts.
            </p>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>
