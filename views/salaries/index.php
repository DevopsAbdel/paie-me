<div class="card">
    <div class="card-header">
        <h3>Liste des salariés</h3>
        <div style="display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap;">
            <?php include __DIR__ . '/_import_ui.php'; ?>
            <a href="/paie-me/salaries/create" class="btn btn-primary btn-sm">+ Nouveau</a>
        </div>
    </div>

    <?php if (empty($salaries)): ?>
        <div class="empty-state">
            <p>Aucun salarié enregistré.</p>
            <a href="/paie-me/salaries/create" class="btn btn-primary">Ajouter un salarié</a>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Matricule</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>CIN</th>
                        <th>CNSS</th>
                        <th>Poste</th>
                        <?php if (empty($ctx)): ?><th>Société</th><?php endif; ?>
                        <th>Salaire base</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($salaries as $s): ?>
                    <tr>
                        <td><?= htmlspecialchars($s['matricule']) ?></td>
                        <td><?= htmlspecialchars($s['nom_famille']) ?></td>
                        <td><?= htmlspecialchars($s['prenom']) ?></td>
                        <td><?= htmlspecialchars($s['cin']) ?></td>
                        <td><?= htmlspecialchars($s['cnss']) ?></td>
                        <td><?= htmlspecialchars($s['fonction_nom'] ?? $s['poste']) ?></td>
                        <?php if (empty($ctx)): ?><td><?= htmlspecialchars($s['raison_sociale']) ?></td><?php endif; ?>
                        <td><?= number_format($s['salaire_base'], 2, ',', ' ') ?></td>
                        <td>
                            <div class="table-actions">
                                <a href="/paie-me/salaries/<?= $s['id'] ?>/edit" class="btn-icon btn-edit" title="Modifier">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </a>
                                <a href="/paie-me/salaries/<?= $s['id'] ?>/stc" class="btn-icon btn-info" title="STC">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                                </a>
                                <form method="POST" action="/paie-me/salaries/<?= $s['id'] ?>/delete" class="inline-form">
                                    <?= \Core\Session::csrfField() ?>
                                    <button type="submit" class="btn-icon btn-delete" title="Supprimer" onclick="return confirm('Supprimer ce salarié ?')">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
