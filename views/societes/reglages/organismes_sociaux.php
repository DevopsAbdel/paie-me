<div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; gap:1rem;">
        <h3 style="margin:0;">Organismes Sociaux</h3>
        <form method="post" action="<?= $baseUrl ?>/organismes_sociaux" style="display:flex; gap:0.5rem; align-items:center;">
            <?= \Core\Session::csrfField() ?>
            <input type="hidden" name="sous_tab" value="organismes_sociaux">
            <input type="text" name="nom" class="form-control" placeholder="Nom" style="width:150px;" required>
            <select name="type" class="form-control" style="width:110px;">
                <option value="cnss">CNSS</option>
                <option value="amo">AMO</option>
                <option value="cimr">CIMR</option>
                <option value="mutuelle">Mutuelle</option>
                <option value="autre">Autre</option>
            </select>
            <input type="text" name="login" class="form-control" placeholder="Login" style="width:120px;">
            <input type="password" name="mot_de_passe" class="form-control" placeholder="Mot de passe" style="width:120px;">
            <button type="submit" class="btn btn-primary btn-sm">Ajouter</button>
        </form>
    </div>
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr><th>Nom</th><th>Type</th><th>Login</th><th>Actif</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php if (empty($organismes)): ?>
                <tr><td colspan="5" style="text-align:center; color:var(--text-muted);">Aucun organisme</td></tr>
                <?php else: ?>
                <?php foreach ($organismes as $o): ?>
                <tr>
                    <td><?= htmlspecialchars($o['nom']) ?></td>
                    <td><span class="badge badge-info"><?= htmlspecialchars($o['type']) ?></span></td>
                    <td><?= htmlspecialchars($o['login'] ?? '') ?></td>
                    <td><span class="badge badge-<?= $o['actif'] ? 'success' : 'secondary' ?>"><?= $o['actif'] ? 'Oui' : 'Non' ?></span></td>
                    <td>
                        <a href="<?= $baseUrl ?>/organismes_sociaux?delete_organisme=<?= $o['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cet organisme ?')">Supprimer</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
(function() {
    var params = new URLSearchParams(window.location.search);
    if (params.get('delete_organisme')) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', window.location.pathname + '?delete_organisme=' + params.get('delete_organisme'), true);
        xhr.send();
    }
})();
</script>
