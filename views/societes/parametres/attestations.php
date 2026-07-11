<div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; gap:1rem;">
        <h3 style="margin:0;">Attestations</h3>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#ajoutAttestation">+ Ajouter</button>
    </div>
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr><th>Titre</th><th>Actif</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php if (empty($attestations)): ?>
                <tr><td colspan="3" style="text-align:center; color:var(--text-muted);">Aucune attestation</td></tr>
                <?php else: ?>
                <?php foreach ($attestations as $a): ?>
                <tr>
                    <td><?= htmlspecialchars($a['titre']) ?></td>
                    <td><span class="badge badge-<?= $a['actif'] ? 'success' : 'secondary' ?>"><?= $a['actif'] ? 'Oui' : 'Non' ?></span></td>
                    <td>
                        <a href="<?= $baseUrl ?>/attestations?delete_attestation=<?= $a['id'] ?>" class="btn-icon btn-delete" title="Supprimer" onclick="return confirm('Supprimer cette attestation ?')">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Ajouter attestation -->
<div class="modal fade" id="ajoutAttestation" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background:var(--bg-surface); color:var(--text); border:1px solid var(--border); border-radius:12px;">
            <form method="post" action="<?= $baseUrl ?>/attestations">
                <?= \Core\Session::csrfField() ?>
                <input type="hidden" name="sous_tab" value="attestations">
                <div class="modal-header" style="border-bottom:1px solid var(--border);">
                    <h5 class="modal-title">Nouvelle attestation</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group" style="margin-bottom:1rem;">
                        <label class="form-label" style="font-size:0.75rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Titre</label>
                        <input type="text" name="titre" class="form-control" placeholder="Attestation de travail" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="font-size:0.75rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Contenu du modèle</label>
                        <textarea name="contenu" class="form-control" rows="5" placeholder="Contenu HTML du modèle d'attestation..." style="resize:vertical;" required></textarea>
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
