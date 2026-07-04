<div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; gap:1rem;">
        <h3 style="margin:0;">Gains</h3>
        <form method="post" action="<?= $baseUrl ?>/gains" style="display:flex; gap:0.5rem; align-items:center; flex-wrap:wrap;">
            <?= \Core\Session::csrfField() ?>
            <input type="hidden" name="sous_tab" value="gains">
            <input type="text" name="code" class="form-control" placeholder="Code" style="width:80px;" required>
            <input type="text" name="libelle" class="form-control" placeholder="Libellé" style="width:140px;" required>
            <select name="type_montant" class="form-control" style="width:110px;">
                <option value="fixe">Fixe</option>
                <option value="proportionnel">Proportionnel</option>
            </select>
            <input type="number" name="valeur_defaut" class="form-control" placeholder="Valeur" style="width:80px;" step="0.01">
            <select name="categorie" class="form-control" style="width:140px;">
                <option value="">Catégorie</option>
                <option>Transport & Déplacement</option>
                <option>Spécifiques à certains emplois</option>
                <option>Caractère Social & Familial</option>
                <option>Rupture & Fin de Contrat</option>
                <option>Gain standard</option>
            </select>
            <input type="text" name="affectation" class="form-control" placeholder="Compte" style="width:70px;">
            <button type="submit" class="btn btn-primary btn-sm">Ajouter</button>
        </form>
    </div>
    <div style="overflow-x:auto;">
        <table class="table-gains" style="min-width:1000px;">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Libellé</th>
                    <th>Type</th>
                    <th>Valeur</th>
                    <th>Imposable</th>
                    <th>Actif</th>
                    <th>Catégorie</th>
                    <th>Compte</th>
                    <th>Plafond DGI</th>
                    <th>Plafond CNSS</th>
                    <th>Justificatifs</th>
                    <th>Source</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($gains)): ?>
                <tr><td colspan="13" style="text-align:center; color:var(--text-muted);">Aucun gain</td></tr>
                <?php else: ?>
                <?php foreach ($gains as $g): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($g['code']) ?></strong></td>
                    <td><?= htmlspecialchars($g['libelle']) ?></td>
                    <td><?= htmlspecialchars($g['type_montant']) ?></td>
                    <td class="text-right"><?= htmlspecialchars($g['valeur_defaut'] ?? '') ?></td>
                    <td><span class="badge badge-<?= $g['imposable'] ? 'warning' : 'secondary' ?>"><?= $g['imposable'] ? 'Oui' : 'Non' ?></span></td>
                    <td><span class="badge badge-<?= $g['actif'] ? 'success' : 'secondary' ?>"><?= $g['actif'] ? 'Oui' : 'Non' ?></span></td>
                    <td><small><?= htmlspecialchars($g['categorie'] ?? '') ?></small></td>
                    <td><code><?= htmlspecialchars($g['affectation'] ?? '') ?></code></td>
                    <td><small><?= htmlspecialchars($g['plafond_dgi'] ?? '') ?></small></td>
                    <td><small><?= htmlspecialchars($g['plafond_cnss'] ?? '') ?></small></td>
                    <td><small title="<?= htmlspecialchars($g['justificatifs'] ?? '') ?>"><?= htmlspecialchars(mb_substr($g['justificatifs'] ?? '', 0, 40)) ?><?= isset($g['justificatifs']) && mb_strlen($g['justificatifs']) > 40 ? '…' : '' ?></small></td>
                    <td><span class="badge badge-info"><?= !empty($g['is_global']) ? 'Globale' : 'Société' ?></span></td>
                    <td>
                        <?php if (empty($g['is_global'])): ?>
                        <a href="<?= $baseUrl ?>/gains?delete_gain=<?= $g['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce gain ?')">Supprimer</a>
                        <?php else: ?>
                        <span style="color:var(--text-muted); font-size:0.85rem;">Par défaut</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
