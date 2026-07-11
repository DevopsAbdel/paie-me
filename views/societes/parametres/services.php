<div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; gap:1rem;">
        <h3 style="margin:0;">Services</h3>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#ajoutService">+ Ajouter</button>
    </div>
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr><th>Nom</th><th>Description</th><th>Actif</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php if (empty($services)): ?>
                <tr><td colspan="4" style="text-align:center; color:var(--text-muted);">Aucun service</td></tr>
                <?php else: ?>
                <?php foreach ($services as $s): ?>
                <tr>
                    <td><?= htmlspecialchars($s['nom']) ?></td>
                    <td><?= htmlspecialchars($s['description'] ?? '') ?></td>
                    <td><span class="badge badge-<?= $s['actif'] ? 'success' : 'secondary' ?>"><?= $s['actif'] ? 'Oui' : 'Non' ?></span></td>
                    <td>
                        <a href="<?= $baseUrl ?>/services?delete_service=<?= $s['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce service ?')">Supprimer</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Ajouter service -->
<div class="modal fade" id="ajoutService" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background:var(--bg-surface); color:var(--text); border:1px solid var(--border); border-radius:12px;">
            <form method="post" action="<?= $baseUrl ?>/services">
                <?= \Core\Session::csrfField() ?>
                <input type="hidden" name="sous_tab" value="services">
                <div class="modal-header" style="border-bottom:1px solid var(--border);">
                    <h5 class="modal-title">Nouveau service</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group" style="margin-bottom:1rem;">
                        <label class="form-label" style="font-size:0.75rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Nom du service</label>
                        <input type="text" name="service_nom" class="form-control" placeholder="Ressources humaines" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="font-size:0.75rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Description</label>
                        <input type="text" name="service_description" class="form-control" placeholder="Gestion du personnel">
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--border);">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary btn-sm">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="card" style="margin-top:1.5rem;">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; gap:1rem;">
        <h3 style="margin:0;">Fonctions (postes)</h3>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#ajoutFonction">+ Ajouter</button>
    </div>
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr><th>Fonction</th><th>Service</th><th>Description</th><th>Actif</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php if (empty($fonctions)): ?>
                <tr><td colspan="5" style="text-align:center; color:var(--text-muted);">Aucune fonction</td></tr>
                <?php else: ?>
                <?php foreach ($fonctions as $f): ?>
                <tr>
                    <td><?= htmlspecialchars($f['nom']) ?></td>
                    <td><?= htmlspecialchars($f['service_nom'] ?? '— Tous services —') ?></td>
                    <td><?= htmlspecialchars($f['description'] ?? '') ?></td>
                    <td><span class="badge badge-<?= $f['actif'] ? 'success' : 'secondary' ?>"><?= $f['actif'] ? 'Oui' : 'Non' ?></span></td>
                    <td>
                        <a href="<?= $baseUrl ?>/services?delete_fonction=<?= $f['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cette fonction ?')">Supprimer</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Ajouter fonction -->
<div class="modal fade" id="ajoutFonction" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background:var(--bg-surface); color:var(--text); border:1px solid var(--border); border-radius:12px;">
            <form method="post" action="<?= $baseUrl ?>/services">
                <?= \Core\Session::csrfField() ?>
                <input type="hidden" name="sous_tab" value="services">
                <div class="modal-header" style="border-bottom:1px solid var(--border);">
                    <h5 class="modal-title">Nouvelle fonction</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group" style="margin-bottom:1rem;">
                        <label class="form-label" style="font-size:0.75rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Service</label>
                        <select name="fonction_service_id" class="form-control">
                            <option value="">— Tous services —</option>
                            <?php foreach ($services as $sv): ?>
                            <option value="<?= $sv['id'] ?>"><?= htmlspecialchars($sv['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:1rem;">
                        <label class="form-label" style="font-size:0.75rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Nom de la fonction</label>
                        <input type="text" name="fonction_nom" class="form-control" placeholder="Développeur PHP" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="font-size:0.75rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Description</label>
                        <input type="text" name="fonction_description" class="form-control" placeholder="Développement et maintenance">
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--border);">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary btn-sm">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
