<div class="card">
    <div class="card-header">
        <h3>Périodes de paie</h3>
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
                            <a href="/paie-me/paies/<?= $p['id'] ?>/lignes" class="btn-icon btn-view" title="Détail">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
                            <a href="/paie-me/paies/<?= $p['id'] ?>/journal" class="btn-icon btn-info" title="Journal">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                            </a>
                            <?php if (!$p['cloturee']): ?>
                                <a href="/paie-me/paies/<?= $p['id'] ?>/calculate" class="btn-icon btn-edit" title="Recalculer" onclick="return confirm('Recalculer les paies pour cette période ?')">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
                                </a>
                                <a href="/paie-me/paies/<?= $p['id'] ?>/cloturer" class="btn-icon btn-info" title="Clôturer" onclick="return confirm('Clôturer cette période ? Cette action est irréversible.')">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                </a>
                            <?php endif; ?>
                            <a href="/paie-me/paies/<?= $p['id'] ?>/supprimer" class="btn-icon btn-delete" title="Supprimer" onclick="return confirm('Supprimer définitivement cette période et toutes les paies associées ? Cette action est irréversible.')">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
