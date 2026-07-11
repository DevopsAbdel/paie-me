<div class="card">
    <div class="card-header">
        <h3>Liste des sociétés</h3>
        <a href="/paie-me/societes/create" class="btn btn-primary btn-sm">+ Nouvelle</a>
    </div>

    <?php if (empty($societes)): ?>
        <div class="empty-state">
            <p>Aucune société enregistrée.</p>
            <a href="/paie-me/societes/create" class="btn btn-primary">Créer une société</a>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Raison sociale</th>
                        <th>Forme</th>
                        <th>ICE</th>
                        <th>IF</th>
                        <th>RC</th>
                        <th>CNSS</th>
                        <th>Ville</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($societes as $s): ?>
                    <tr>
                        <td><a href="/paie-me/societes/<?= $s['id'] ?>" style="font-weight:600;"><?= htmlspecialchars($s['raison_sociale']) ?></a></td>
                        <td><?= $s['forme_juridique'] ?></td>
                        <td><?= htmlspecialchars($s['ice']) ?></td>
                        <td><?= htmlspecialchars($s['if_fiscal']) ?></td>
                        <td><?= htmlspecialchars($s['rc']) ?></td>
                        <td><?= htmlspecialchars($s['cnss']) ?></td>
                        <td><?= htmlspecialchars($s['ville']) ?></td>
                        <td>
                            <div class="table-actions">
                                <a href="/paie-me/societes/<?= $s['id'] ?>" class="btn-icon btn-view" title="Ouvrir">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                                <a href="/paie-me/societes/<?= $s['id'] ?>/edit" class="btn-icon btn-edit" title="Modifier">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </a>
                                <form method="POST" action="/paie-me/societes/<?= $s['id'] ?>/delete" class="inline-form">
                                    <?= \Core\Session::csrfField() ?>
                                    <button type="submit" class="btn-icon btn-delete" title="Supprimer" onclick="return confirm('Supprimer cette société ?')">
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
