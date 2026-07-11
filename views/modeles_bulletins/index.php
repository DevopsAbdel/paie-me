<div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
        <h3 style="margin:0;">Modèles Bulletins — <?= htmlspecialchars($societe['raison_sociale']) ?></h3>
        <button type="button" class="btn btn-primary btn-sm" onclick="new bootstrap.Modal(document.getElementById('ajoutModele')).show()">
            + Ajouter
        </button>
    </div>
    <p style="font-size:0.8125rem; color:var(--text-muted); margin:0 0 0.75rem 0;">
        Créez et gérez les modèles de bulletins de paie. Affectez un modèle par défaut à cette société.
    </p>
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Description</th>
                    <th style="text-align:center;">Sections</th>
                    <th style="text-align:center;">Statut</th>
                    <th style="width:140px; text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($modeles)): ?>
                <tr>
                    <td colspan="5" style="text-align:center; color:var(--text-muted); padding:2rem;">Aucun modèle trouvé.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($modeles as $m): ?>
                <tr>
                    <td style="font-weight:500;"><?= htmlspecialchars($m['nom']) ?></td>
                    <td style="color:var(--text-muted); font-size:0.85rem;"><?= htmlspecialchars($m['description'] ?? '—') ?></td>
                    <td style="text-align:center;"><?= count($m['config']['sections'] ?? []) ?></td>
                    <td style="text-align:center;">
                        <?php if ($m['defaut']): ?>
                            <span class="badge badge-success">Par défaut</span>
                        <?php else: ?>
                            <a href="<?= $baseUrl ?>/<?= $m['id'] ?>/assign" class="btn btn-sm btn-secondary" style="font-size:0.7rem;" onclick="return confirm('Définir ce modèle par défaut ?')">Définir par défaut</a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="table-actions">
                            <button type="button" class="btn-icon btn-view" title="Voir les détails" onclick="voirModele(<?= $m['id'] ?>)">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                            <button type="button" class="btn-icon btn-edit" title="Modifier" onclick="editModele(<?= $m['id'] ?>, '<?= htmlspecialchars($m['nom'], ENT_QUOTES) ?>', '<?= htmlspecialchars($m['description'] ?? '', ENT_QUOTES) ?>')">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </button>
                            <a href="<?= $baseUrl ?>/<?= $m['id'] ?>/delete" class="btn-icon btn-delete" title="Supprimer" onclick="return confirm('Supprimer ce modèle ?')">
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

<!-- Modal Ajouter modèle -->
<div class="modal fade" id="ajoutModele" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background:var(--bg-surface); color:var(--text); border:1px solid var(--border); border-radius:12px;">
            <form method="post" action="<?= $baseUrl ?>">
                <?= \Core\Session::csrfField() ?>
                <div class="modal-header" style="border-bottom:1px solid var(--border);">
                    <h5 class="modal-title">Nouveau modèle de bulletin</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group" style="margin-bottom:1rem;">
                        <label class="form-label" style="font-size:0.75rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Nom du modèle</label>
                        <input type="text" name="nom" class="form-control" placeholder="Modèle Standard" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="font-size:0.75rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Description du modèle..."></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--border);">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success btn-sm">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Modifier modèle -->
<div class="modal fade" id="editModele" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background:var(--bg-surface); color:var(--text); border:1px solid var(--border); border-radius:12px;">
            <form method="post" id="editModeleForm" action="">
                <?= \Core\Session::csrfField() ?>
                <div class="modal-header" style="border-bottom:1px solid var(--border);">
                    <h5 class="modal-title">Modifier le modèle</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group" style="margin-bottom:1rem;">
                        <label class="form-label" style="font-size:0.75rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Nom du modèle</label>
                        <input type="text" name="nom" id="editModeleNom" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="font-size:0.75rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Description</label>
                        <textarea name="description" id="editModeleDesc" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--border);">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success btn-sm">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Détails modèle -->
<div class="modal fade" id="detailModele" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="background:var(--bg-surface); color:var(--text); border:1px solid var(--border); border-radius:12px;">
            <div class="modal-header" style="border-bottom:1px solid var(--border);">
                <h5 class="modal-title" id="detailModeleTitle">Détails du modèle</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailModeleBody">
            </div>
        </div>
    </div>
</div>

<script>
function editModele(id, nom, desc) {
    var baseUrl = <?= json_encode($baseUrl) ?>;
    document.getElementById('editModeleForm').action = baseUrl + '/' + id + '/update';
    document.getElementById('editModeleNom').value = nom;
    document.getElementById('editModeleDesc').value = desc;
    new bootstrap.Modal(document.getElementById('editModele')).show();
}

function voirModele(id) {
    var modeles = <?= json_encode(array_map(function($m) { return ['id' => $m['id'], 'nom' => $m['nom'], 'config' => $m['config']]; }, $modeles)); ?>;
    var modele = modeles.find(function(m) { return m.id === id; });
    if (!modele) return;

    document.getElementById('detailModeleTitle').textContent = modele.nom;
    var html = '';
    var config = modele.config;
    var sections = config.sections || [];

    sections.forEach(function(section) {
        html += '<h5 style="color:var(--accent); margin:1rem 0 0.5rem 0; font-size:0.9rem;">' + section.titre + '</h5>';
        html += '<table class="data-table" style="font-size:0.8rem;"><thead><tr>';
        section.colonnes.forEach(function(col) { html += '<th>' + col + '</th>'; });
        html += '</tr></thead><tbody>';
        section.lignes.forEach(function(ligne) {
            html += '<tr><td>' + ligne.label + '</td>';
            for (var i = 1; i < section.colonnes.length; i++) {
                var key = ['base','taux','montant'][i-1];
                html += '<td style="text-align:center;">' + (ligne['show_' + key] ? '✓' : '—') + '</td>';
            }
            html += '</tr>';
        });
        if (section.total) {
            html += '<tr style="font-weight:600; border-top:2px solid var(--accent);"><td>' + section.total.label + '</td>';
            for (var i = 1; i < section.colonnes.length; i++) { html += '<td></td>'; }
            html += '</tr>';
        }
        html += '</tbody></table>';
    });

    document.getElementById('detailModeleBody').innerHTML = html;
    new bootstrap.Modal(document.getElementById('detailModele')).show();
}
</script>
