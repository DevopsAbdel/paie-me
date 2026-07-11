<div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap;">
        <h3 style="margin:0;">Barème SMIG & SMAG</h3>
        <form method="post" action="<?= $baseUrl ?>/bareme_salaire" style="display:flex; gap:0.4rem; align-items:center; flex-wrap:wrap;">
            <?= \Core\Session::csrfField() ?>
            <input type="hidden" name="sous_tab" value="bareme_salaire">
            <select name="nouveau_type" class="form-control" style="width:80px;" required>
                <option value="">Type</option>
                <option value="SMIG">SMIG</option>
                <option value="SMAG">SMAG</option>
            </select>
            <input type="number" name="nouvelle_annee" class="form-control" placeholder="Année" style="width:90px;" min="2020" max="2035" required>
            <input type="number" step="0.01" name="nouveau_horaire" class="form-control" placeholder="Horaire" style="width:100px;" required>
            <input type="number" step="0.01" name="nouveau_mensuel" class="form-control" placeholder="Mensuel" style="width:100px;" required>
            <button type="submit" class="btn btn-primary btn-sm">Ajouter</button>
        </form>
    </div>
    <div style="overflow-x:auto;">
        <form method="post" action="<?= $baseUrl ?>/bareme_salaire">
            <?= \Core\Session::csrfField() ?>
            <input type="hidden" name="sous_tab" value="bareme_salaire">
            <table>
                <thead>
                    <tr>
                        <th>Année</th>
                        <th>Type</th>
                        <th>Taux horaire (MAD/h)</th>
                        <th>Taux mensuel (MAD/mois)</th>
                        <th>Date d'effet</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($baremeSmigSmag)): ?>
                    <tr>
                        <td colspan="6" style="text-align:center; color:var(--text-muted); padding:2rem;">
                            Aucun barème SMIG/SMAG défini. Ajoutez-en un avec le formulaire ci-dessus.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($baremeSmigSmag as $b): ?>
                    <tr>
                        <td style="font-weight:600;"><?= (int) $b['annee'] ?></td>
                        <td><span class="badge badge-<?= $b['type'] === 'SMIG' ? 'primary' : 'info' ?>"><?= htmlspecialchars($b['type']) ?></span></td>
                        <td>
                            <input type="hidden" name="bareme_id[]" value="<?= $b['id'] ?>">
                            <input type="number" step="0.01" name="horaire[]" class="form-control-inline" value="<?= $b['horaire'] ?>" style="width:110px;">
                        </td>
                        <td>
                            <input type="number" step="0.01" name="mensuel[]" class="form-control-inline" value="<?= $b['mensuel'] ?>" style="width:110px;">
                        </td>
                        <td>
                            <input type="date" name="date_effet[]" class="form-control-inline" value="<?= htmlspecialchars($b['date_effet'] ?? '') ?>" style="width:140px;">
                        </td>
                        <td>
                            <a href="<?= $baseUrl ?>/bareme_salaire?delete_bareme=<?= $b['id'] ?>" class="btn-icon btn-delete" title="Supprimer" onclick="return confirm('Supprimer ce barème ?')">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <?php if (!empty($baremeSmigSmag)): ?>
            <div style="padding:0.75rem 1rem; border-top:1px solid var(--border);">
                <button type="submit" class="btn btn-success">Enregistrer les modifications</button>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<style>
.form-control-inline {
    width:80px;
    padding:0.2rem 0.3rem;
    font-size:0.75rem;
    background:var(--bg-surface);
    border:1px solid var(--border);
    border-radius:3px;
    color:var(--text);
    text-align:right;
}
.form-control-inline:focus {
    border-color:var(--accent);
    outline:none;
}
</style>
