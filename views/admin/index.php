<div class="card">
    <div class="card-header">
        <h3>Bases de données</h3>
    </div>
    <div class="card-body">
        <div style="display:flex; flex-wrap:wrap; gap:0.75rem; padding:0 1rem 1rem;">
            <form method="post" action="/paie-me/admin" class="inline-form" onsubmit="return confirm('Créer / réinitialiser la base démo (paie_me_demo) ?')">
                <?= \Core\Session::csrfField() ?>
                <input type="hidden" name="action" value="reset_demo">
                <button type="submit" class="btn btn-primary btn-sm">+ Créer / réinitialiser la démo</button>
            </form>
            <form method="post" action="/paie-me/admin" class="inline-form" onsubmit="return confirm('Vider et re-seeder la base démo ? Cette action efface toutes les données démo.')">
                <?= \Core\Session::csrfField() ?>
                <input type="hidden" name="action" value="reseed">
                <button type="submit" class="btn btn-secondary btn-sm">Vider et re-seeder la démo</button>
            </form>
            <form method="post" action="/paie-me/admin" class="inline-form" onsubmit="return confirm('Appliquer les migrations sur paie_me et paie_me_demo ?')">
                <?= \Core\Session::csrfField() ?>
                <input type="hidden" name="action" value="migrate">
                <button type="submit" class="btn btn-secondary btn-sm">Appliquer les migrations</button>
            </form>
            <form method="post" action="/paie-me/admin" class="inline-form" onsubmit="return confirm('Copier les nouvelles tables, colonnes et index de paie_me vers paie_me_demo ?')">
                <?= \Core\Session::csrfField() ?>
                <input type="hidden" name="action" value="sync_schema">
                <button type="submit" class="btn btn-secondary btn-sm">Copier les nouvelles colonnes/tables vers la démo</button>
            </form>
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Base</th>
                        <th>Statut</th>
                        <th>Tables</th>
                        <th>Sociétés</th>
                        <th>Taille</th>
                        <th>Active</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($databases as $db): ?>
                    <tr>
                        <td style="font-weight:600; font-family:Consolas,monospace;"><?= htmlspecialchars($db['name']) ?></td>
                        <td>
                            <?php if ($db['exists']): ?>
                                <span class="badge badge-success">Existe</span>
                            <?php else: ?>
                                <span class="badge badge-warning">Absente</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $db['nb_tables'] ?></td>
                        <td><?= $db['nb_societes'] ?></td>
                        <td><?= $db['size'] ?></td>
                        <td>
                            <?php if ($db['current']): ?>
                                <span class="badge badge-info" style="background:rgba(139,92,246,0.15); color:#a78bfa; border:1px solid rgba(139,92,246,0.4);">En cours</span>
                            <?php else: ?>
                                <span style="color:var(--text-muted); font-size:0.8125rem;">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p style="padding:0 1rem 1rem; color:var(--text-muted); font-size:0.75rem;">
            <code>paie_me</code> = base principale (production locale), <code>paie_me_demo</code> = sandbox de démonstration.
            Le mode démo (page de connexion) bascule l'application sur <code>paie_me_demo</code>.
        </p>
    </div>
</div>

<div class="card" style="margin-top:1.25rem;">
    <div class="card-header">
        <h3>Utilisateurs</h3>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#ajoutUtilisateur">+ Ajouter</button>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Statut</th>
                    <th>Créé le</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td style="font-weight:600;">
                        <?= htmlspecialchars($u['nom']) ?>
                        <?php if ((int)$u['id'] === (int)\Core\Session::get('user_id')): ?>
                            <span style="color:var(--text-muted); font-size:0.7rem;">(vous)</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td>
                        <?php if ($u['role'] === 'admin'): ?>
                            <span class="badge badge-info" style="background:rgba(139,92,246,0.15); color:#a78bfa; border:1px solid rgba(139,92,246,0.4);">Admin</span>
                        <?php else: ?>
                            <span class="badge" style="background:var(--bg-elevated); color:var(--text); border:1px solid var(--border-strong);">Gestionnaire</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($u['actif']): ?>
                            <span class="badge badge-success">Actif</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Désactivé</span>
                        <?php endif; ?>
                    </td>
                    <td><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                    <td>
                        <div class="table-actions">
                            <form method="post" action="/paie-me/admin" class="inline-form">
                                <?= \Core\Session::csrfField() ?>
                                <input type="hidden" name="action" value="toggle_user">
                                <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                                <button type="submit" class="btn-icon btn-edit" title="<?= $u['actif'] ? 'Désactiver' : 'Activer' ?>">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
                                </button>
                            </form>
                            <form method="post" action="/paie-me/admin" class="inline-form" onsubmit="return confirm('Supprimer cet utilisateur ?')">
                                <?= \Core\Session::csrfField() ?>
                                <input type="hidden" name="action" value="delete_user">
                                <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                                <button type="submit" class="btn-icon btn-delete" title="Supprimer">
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
</div>

<!-- Modal : ajouter un utilisateur -->
<div class="modal fade" id="ajoutUtilisateur" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background:var(--bg-surface); color:var(--text); border:1px solid var(--border); border-radius:12px;">
            <form method="post" action="/paie-me/admin">
                <?= \Core\Session::csrfField() ?>
                <input type="hidden" name="action" value="create_user">
                <div class="modal-header" style="border-bottom:1px solid var(--border);">
                    <h5 class="modal-title">Nouvel utilisateur</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nom complet</label>
                        <input type="text" name="nom" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Mot de passe</label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                        <small style="color:var(--text-muted); font-size:0.7rem; margin-top:0.25rem; display:block;">6 caractères minimum.</small>
                    </div>
                    <div class="form-group">
                        <label>Rôle</label>
                        <select name="role" class="form-control" required>
                            <option value="gestionnaire">Gestionnaire</option>
                            <option value="admin">Administrateur</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--border);">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary btn-sm">Créer</button>
                </div>
            </form>
        </div>
    </div>
</div>
