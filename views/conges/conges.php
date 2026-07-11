<div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap;">
        <h3 style="margin:0;">Gestion des congés</h3>
        <div style="display:flex; gap:0.35rem; flex-wrap:wrap;">
            <a href="<?= $baseUrl ?>/nouveau" class="btn btn-primary btn-sm">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Nouveau
            </a>
            <a href="<?= $baseUrl ?>/solde-initial" class="btn btn-secondary btn-sm">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                Solde congé initial
            </a>
            <a href="<?= $baseUrl ?>/attestation" class="btn btn-secondary btn-sm">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                Attestation
            </a>
        </div>
    </div>

    <div style="padding:0.75rem 1rem; border-bottom:1px solid var(--border);">
        <input type="text" id="searchConge" placeholder="Rechercher un salarié, matricule..." class="form-control" style="max-width:400px;" oninput="filterConges()">
    </div>

    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>Matricule</th>
                    <th>Salarié</th>
                    <th>Type</th>
                    <th>Date début</th>
                    <th>Date fin</th>
                    <th>Nbr jours</th>
                    <th>Statut</th>
                    <th>Observation</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($conges)): ?>
                    <tr><td colspan="9" style="text-align:center; color:var(--text-muted); padding:2rem;">Aucun congé enregistré</td></tr>
                <?php else: ?>
                    <?php foreach ($conges as $c): ?>
                        <tr data-search="<?= strtolower(htmlspecialchars($c['matricule'] . ' ' . $c['nom_complet'])) ?>">
                            <td><?= htmlspecialchars($c['matricule']) ?></td>
                            <td><?= htmlspecialchars($c['nom_complet']) ?></td>
                            <td>
                                <?php
                                $types = ['paye' => 'Payé', 'sans_solde' => 'Sans solde', 'maladie' => 'Maladie', 'maternite' => 'Maternité', 'exceptionnel' => 'Exceptionnel', 'autre' => 'Autre'];
                                echo $types[$c['type_conge']] ?? $c['type_conge'];
                                ?>
                            </td>
                            <td><?= date('d/m/Y', strtotime($c['date_debut'])) ?></td>
                            <td><?= date('d/m/Y', strtotime($c['date_fin'])) ?></td>
                            <td style="text-align:center;"><?= number_format($c['nb_jours'], 1, ',', '') ?></td>
                            <td>
                                <?php
                                $statuts = ['en_attente' => '<span style="color:#eab308;">En attente</span>', 'valide' => '<span style="color:#22c55e;">Validé</span>', 'refuse' => '<span style="color:#ef4444;">Refusé</span>', 'annule' => '<span style="color:#94a3b8;">Annulé</span>'];
                                echo $statuts[$c['statut']] ?? $c['statut'];
                                ?>
                            </td>
                            <td style="max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="<?= htmlspecialchars($c['observation'] ?? '') ?>"><?= htmlspecialchars($c['observation'] ?? '') ?></td>
                            <td>
                                <div class="table-actions">
                                    <a href="<?= $baseUrl ?>/modifier/<?= (int)$c['id'] ?>" class="btn-icon btn-edit" title="Modifier">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </a>
                                    <a href="<?= $baseUrl ?>/supprimer/<?= (int)$c['id'] ?>" class="btn-icon btn-delete" title="Supprimer" onclick="return confirm('Supprimer ce congé ?')">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function filterConges() {
    const q = document.getElementById('searchConge').value.toLowerCase();
    document.querySelectorAll('tbody tr[data-search]').forEach(tr => {
        tr.style.display = tr.dataset.search.includes(q) ? '' : 'none';
    });
}
</script>
