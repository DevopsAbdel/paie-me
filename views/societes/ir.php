<div class="card">
    <div class="card-header">
        <h3>IR / SIMPL — <?= htmlspecialchars($societe['raison_sociale']) ?></h3>
        <a href="/paie-me/ir" class="btn btn-primary btn-sm">Export CSV IR</a>
    </div>
    <?php if (empty($periodes)): ?>
        <div class="empty-state"><p>Aucune période de paie. Créez une paie avant d'exporter l'IR.</p></div>
    <?php else: ?>
    <div style="padding:1rem;">
        <h4 style="color:var(--accent); margin-bottom:0.75rem;">Récapitulatif IR par période</h4>
        <?php
        $irByPeriode = [];
        foreach ($bulletins as $b) {
            $key = sprintf("%02d/%d", $b['mois'], $b['annee']);
            if (!isset($irByPeriode[$key])) {
                $irByPeriode[$key] = ['mois' => $b['mois'], 'annee' => $b['annee'], 'salaries' => 0, 'total_ir' => 0];
            }
            $irByPeriode[$key]['salaries']++;
            $irByPeriode[$key]['total_ir'] += $b['ir'];
        }
        ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr><th>Période</th><th>Salariés</th><th>Total IR</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($periodes as $p): ?>
                    <?php $key = sprintf("%02d/%d", $p['mois'], $p['annee']); ?>
                    <tr>
                        <td><?= $key ?></td>
                        <td><?= (int)$p['nb_paies'] ?></td>
                        <td><strong style="color:var(--accent);"><?= number_format($irByPeriode[$key]['total_ir'] ?? 0, 2, ',', ' ') ?> MAD</strong></td>
                        <td>
                            <div class="table-actions">
                                <a href="/paie-me/ir/export?periode_id=<?= $p['id'] ?>" class="btn btn-secondary btn-sm">CSV</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div style="margin-top:1.5rem; background:var(--bg-secondary); border-radius:8px; padding:1rem;">
            <h4 style="color:var(--accent); margin-bottom:0.5rem;">SIMPL (Impôts)</h4>
            <p style="font-size:0.875rem; color:var(--text-muted);">
                Login : <strong><?= htmlspecialchars($societe['simpl_login'] ?: 'Non configuré') ?></strong><br>
                IF : <strong><?= htmlspecialchars($societe['if_fiscal']) ?></strong>
            </p>
            <p style="font-size:0.8125rem; color:var(--text-muted);">
                L'export CSV est conforme au format d'import SIMPL de la Direction Générale des Impôts.
            </p>
        </div>
    </div>
    <?php endif; ?>
</div>
