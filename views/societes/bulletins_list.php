<div class="card">
    <div class="card-header">
        <h3>Bulletins de paie</h3>
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
                            <a href="/paie-me/bulletins/<?= $b['id'] ?>" class="btn-icon btn-view" title="Voir">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
                            <a href="/paie-me/bulletins/<?= $b['id'] ?>/pdf" class="btn-icon btn-info" title="PDF" target="_blank">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
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
