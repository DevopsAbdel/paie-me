<div class="card">
    <div class="card-header">
        <h3>Solde congé initial</h3>
    </div>

    <form method="post" action="<?= $baseUrl ?>/solde-initial">
        <?= \Core\Session::csrfField() ?>

        <div style="display:flex; align-items:center; gap:1rem; margin-bottom:1rem; flex-wrap:wrap;">
            <div class="form-group" style="margin:0;">
                <label>Année</label>
                <select name="annee" class="form-control" onchange="this.form.submit()">
                    <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                        <option value="<?= $y ?>" <?= $y == $annee ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-success btn-sm" style="margin-top:1.1rem;">Enregistrer les soldes</button>
        </div>

        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>Matricule</th>
                        <th>Salarié</th>
                        <th>Solde initial (jours)</th>
                        <th>Congés pris</th>
                        <th>Report</th>
                        <th>Solde restant</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($salaries)): ?>
                        <tr><td colspan="6" style="text-align:center; color:var(--text-muted); padding:2rem;">Aucun salarié</td></tr>
                    <?php else: ?>
                        <?php foreach ($salaries as $s): ?>
                            <tr>
                                <td><?= htmlspecialchars($s['matricule']) ?></td>
                                <td><?= htmlspecialchars($s['prenom'] . ' ' . $s['nom_famille']) ?></td>
                                <td>
                                    <input type="number" name="solde[<?= (int)$s['id'] ?>]" value="<?= number_format($s['solde_initial'], 1, '.', '') ?>" class="form-control-inline" step="0.5" min="0" style="width:100px;">
                                </td>
                                <td style="text-align:center;"><?= number_format($s['conges_pris'], 1, ',', '') ?></td>
                                <td style="text-align:center;"><?= number_format($s['report'], 1, ',', '') ?></td>
                                <?php $restant = $s['solde_initial'] + $s['report'] - $s['conges_pris']; ?>
                                <td style="text-align:center; font-weight:600; color:<?= $restant >= 0 ? '#22c55e' : '#ef4444' ?>;">
                                    <?= number_format($restant, 1, ',', '') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </form>
</div>
