<div class="card">
    <div class="card-header">
        <h3>Périodes de paie</h3>
        <a href="/paie-me/paies/create" class="btn btn-primary btn-sm">+ Nouvelle période</a>
    </div>

    <?php if (empty($periodes)): ?>
        <div class="empty-state">
            <p>Aucune période de paie créée.</p>
            <a href="/paie-me/paies/create" class="btn btn-primary">Créer une période</a>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Société</th>
                        <th>Période</th>
                        <th>Du</th>
                        <th>Au</th>
                        <th>Salariés</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($periodes as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['raison_sociale']) ?></td>
                        <td><?= str_pad($p['mois'], 2, '0', STR_PAD_LEFT) . '/' . $p['annee'] ?></td>
                        <td><?= $p['date_debut'] ?></td>
                        <td><?= $p['date_fin'] ?></td>
                        <td><?= (int) $p['nb_paies'] ?></td>
                        <td>
                            <?php if ($p['cloturee']): ?>
                                <span class="badge badge-success">Clôturée</span>
                            <?php else: ?>
                                <span class="badge badge-warning">En cours</span>
                            <?php endif; ?>
                        </td>
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
