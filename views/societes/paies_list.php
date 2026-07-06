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
                            <a href="/paie-me/paies/<?= $p['id'] ?>/lignes" class="btn btn-secondary btn-sm">Détail</a>
                            <a href="/paie-me/paies/<?= $p['id'] ?>/journal" class="btn btn-secondary btn-sm">Journal</a>
                            <?php if (!$p['cloturee']): ?>
                                <a href="/paie-me/paies/<?= $p['id'] ?>/calculate" class="btn btn-secondary btn-sm" onclick="return confirm('Recalculer les paies pour cette période ?')">Recalculer</a>
                                <a href="/paie-me/paies/<?= $p['id'] ?>/cloturer" class="btn btn-danger btn-sm" onclick="return confirm('Clôturer cette période ? Cette action est irréversible.')">Clôturer</a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
