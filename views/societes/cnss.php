<div class="card">
    <div class="card-header">
        <h3>CNSS / Damancom — <?= htmlspecialchars($societe['raison_sociale']) ?></h3>
        <a href="/paie-me/damancom" class="btn btn-primary btn-sm">Générer fichier DS</a>
    </div>
    <?php if (empty($periodes)): ?>
        <div class="empty-state"><p>Aucune période de paie. Créez une paie avant de générer la déclaration CNSS.</p></div>
    <?php else: ?>
    <div style="padding:1rem;">
        <h4 class="form-section-title">Déclarations par période</h4>
        <hr class="form-section-sep">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Période</th><th>Salariés</th><th>Statut</th>
                        <th colspan="3" style="text-align:center; border-bottom:2px solid var(--border);">Pénalités (MAD)</th>
                        <th>Actions</th>
                    </tr>
                    <tr>
                        <th colspan="3"></th>
                        <th>Cotisations CNSS</th><th>TFP</th><th>Cotisations AMO et astreintes</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($periodes as $p): ?>
                    <tr>
                        <td><?= str_pad($p['mois'], 2, '0', STR_PAD_LEFT) ?>/<?= $p['annee'] ?></td>
                        <td><?= (int)$p['nb_paies'] ?></td>
                        <td><span class="badge badge-<?= $p['cloturee'] ? 'success' : 'warning' ?>"><?= $p['cloturee'] ? 'Clôturée' : 'En cours' ?></span></td>
                        <td>
                            <form method="POST" action="/paie-me/societes/<?= $societe['id'] ?>/parametres/cnss_amo" style="display:flex; align-items:center; gap:0.25rem;">
                                <?= \Core\Session::csrfField() ?>
                                <input type="hidden" name="sous_tab" value="penalites">
                                <input type="hidden" name="periode_id" value="<?= $p['id'] ?>">
                                <input type="number" name="penalites_cnss" value="<?= number_format((float)($p['penalites_cnss'] ?? 0), 2, '.', '') ?>" class="form-control" style="width:90px; padding:0.25rem 0.4rem; font-size:0.75rem;" step="0.01" min="0">
                                <button type="submit" class="btn btn-secondary btn-sm" style="padding:0.25rem 0.4rem; font-size:0.7rem;">OK</button>
                            </form>
                        </td>
                        <td>
                            <form method="POST" action="/paie-me/societes/<?= $societe['id'] ?>/parametres/cnss_amo" style="display:flex; align-items:center; gap:0.25rem;">
                                <?= \Core\Session::csrfField() ?>
                                <input type="hidden" name="sous_tab" value="penalites">
                                <input type="hidden" name="periode_id" value="<?= $p['id'] ?>">
                                <input type="number" name="penalites_tfp" value="<?= number_format((float)($p['penalites_tfp'] ?? 0), 2, '.', '') ?>" class="form-control" style="width:90px; padding:0.25rem 0.4rem; font-size:0.75rem;" step="0.01" min="0">
                                <button type="submit" class="btn btn-secondary btn-sm" style="padding:0.25rem 0.4rem; font-size:0.7rem;">OK</button>
                            </form>
                        </td>
                        <td>
                            <form method="POST" action="/paie-me/societes/<?= $societe['id'] ?>/parametres/cnss_amo" style="display:flex; align-items:center; gap:0.25rem;">
                                <?= \Core\Session::csrfField() ?>
                                <input type="hidden" name="sous_tab" value="penalites">
                                <input type="hidden" name="periode_id" value="<?= $p['id'] ?>">
                                <input type="number" name="penalites_amo" value="<?= number_format((float)($p['penalites_amo'] ?? 0), 2, '.', '') ?>" class="form-control" style="width:90px; padding:0.25rem 0.4rem; font-size:0.75rem;" step="0.01" min="0">
                                <button type="submit" class="btn btn-secondary btn-sm" style="padding:0.25rem 0.4rem; font-size:0.7rem;">OK</button>
                            </form>
                        </td>
                        <td>
                            <div class="table-actions">
                                <form method="POST" action="/paie-me/societes/<?= $societe['id'] ?>/parametres/cnss_amo" style="display:flex; align-items:center; gap:0.2rem;">
                                    <?= \Core\Session::csrfField() ?>
                                    <input type="hidden" name="sous_tab" value="calcul_penalites">
                                    <input type="hidden" name="periode_id" value="<?= $p['id'] ?>">
                                    <input type="number" name="mois_retard" placeholder="Mois retard" min="1" max="120" style="width:70px; padding:0.2rem 0.3rem; font-size:0.7rem; background:var(--bg-tertiary); color:var(--text); border:1px solid var(--border); border-radius:4px;">
                                    <button type="submit" class="btn-icon btn-info" title="Calculer pénalités">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2"/><line x1="8" y1="6" x2="16" y2="6"/><line x1="16" y1="14" x2="16" y2="14.01"/><line x1="12" y1="14" x2="12" y2="14.01"/><line x1="8" y1="14" x2="8" y2="14.01"/><line x1="16" y1="18" x2="16" y2="18.01"/><line x1="12" y1="18" x2="12" y2="18.01"/><line x1="8" y1="18" x2="8" y2="18.01"/><line x1="10" y1="10" x2="14" y2="10"/></svg>
                                    </button>
                                </form>
                                <a href="/paie-me/damancom/generate?periode_id=<?= $p['id'] ?>&from_societe=<?= $societe['id'] ?>" class="btn-icon btn-info" title="Générer DS">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div style="margin-top:1.5rem; background:var(--bg-secondary); border-radius:8px; padding:1rem;">
            <h4 style="color:var(--accent); margin-bottom:0.5rem;">Damancom (CNSS)</h4>
            <p style="font-size:0.875rem; color:var(--text-muted);">
                Login : <strong><?= htmlspecialchars($societe['damancom_login'] ?: 'Non configuré') ?></strong><br>
                CNSS : <strong><?= htmlspecialchars($societe['cnss']) ?></strong>
            </p>
            <p style="font-size:0.8125rem; color:var(--text-muted);">
                Le fichier DS est généré au format fixed-width conforme à la spécification Damancom de la CNSS.
            </p>
        </div>
    </div>
    <?php endif; ?>
</div>
