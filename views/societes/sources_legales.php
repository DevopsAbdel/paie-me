<div style="display:flex; gap:2rem; margin-bottom:1.5rem; align-items:center;">
    <div>
        <h2 style="color:var(--accent); margin:0;">Sources légales</h2>
        <p style="color:var(--text-muted); font-size:0.875rem; margin:0.25rem 0 0 0;">
            Références juridiques des rubriques de paie (CNSS, DGI, Code du Travail)
        </p>
    </div>
</div>

<!-- Sources legend -->
<div class="card mb-4">
    <div class="card-header"><h3>Références légales</h3></div>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.75rem; padding:1rem;">
        <?php
        $typeColors = [
            'loi'        => ['badge' => 'badge-primary',   'border' => '#3b82f6'],
            'decret'     => ['badge' => 'badge-warning',   'border' => '#f59e0b'],
            'arrete'     => ['badge' => 'badge-info',      'border' => '#06b6d4'],
            'note'       => ['badge' => 'badge-dark',      'border' => '#8b5cf6'],
            'convention' => ['badge' => 'badge-success',   'border' => '#10b981'],
        ];
        $modifies = ['DAHIR_CNSS' => ['D266', 'A1314']];
        ?>
        <?php foreach ($sources as $s):
            $tc = $typeColors[$s['type']] ?? ['badge' => 'badge-secondary', 'border' => '#64748b'];
        ?>
        <div style="background:var(--bg-primary); border:1px solid var(--border); border-radius:8px; padding:0.75rem 1rem; border-left:3px solid <?= $tc['border'] ?>;">
            <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.25rem;">
                <span class="badge <?= $tc['badge'] ?>" style="font-size:0.7rem;"><?= htmlspecialchars($s['code']) ?></span>
                <strong style="font-size:0.8125rem; color:<?= $tc['border'] ?>;"><?= htmlspecialchars(mb_substr($s['libelle'], 0, 45)) ?></strong>
            </div>
            <div style="font-size:0.75rem; color:var(--text-muted);">
                <span class="badge <?= $tc['badge'] ?>" style="font-size:0.65rem;"><?= htmlspecialchars($s['type']) ?></span>
                <?= htmlspecialchars($s['organisme']) ?>
                <?php if (!empty($s['date_effet']) && $s['date_effet'] !== '—'): ?>
                · <?= htmlspecialchars($s['date_effet']) ?>
                <?php endif; ?>
            </div>
            <?php if (!empty($s['reference_bo']) && $s['reference_bo'] !== '—'): ?>
            <div style="font-size:0.7rem; color:var(--text-muted); margin-top:0.15rem;"><?= htmlspecialchars($s['reference_bo']) ?></div>
            <?php endif; ?>
            <?php if (!empty($s['description'])): ?>
            <div style="font-size:0.7rem; color:var(--text-muted); margin-top:0.25rem; line-height:1.3;">
                <?= htmlspecialchars(mb_substr($s['description'], 0, 100)) ?><?= mb_strlen($s['description']) > 100 ? '…' : '' ?>
            </div>
            <?php endif; ?>
            <?php if (isset($modifies[$s['code']])): ?>
            <div style="font-size:0.7rem; color:var(--accent); margin-top:0.3rem;">
                ⇢ modifié par <?= implode(', ', array_map(fn($c) => '<strong>' . htmlspecialchars($c) . '</strong>', $modifies[$s['code']])) ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Rubriques × Articles table -->
<div class="card mb-4">
    <div class="card-header"><h3>Rubriques de gains — Articles par source</h3></div>
    <div style="overflow-x:auto;">
        <table class="table" style="min-width:1000px;">
            <?php
            $colTypes = [
                'Code du Travail' => $typeColors['loi'],
                'CGI'             => $typeColors['loi'],
                'Arrêté 1314-25'  => $typeColors['arrete'],
            ];
            $colSource = ['Code du Travail' => 'CT', 'CGI' => 'CGI', 'Arrêté 1314-25' => 'A1314'];
            ?>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Libellé gain</th>
                    <th>Catégorie</th>
                    <?php foreach (['Code du Travail', 'CGI', 'Arrêté 1314-25'] as $col): ?>
                    <th style="border-left:3px solid <?= $colTypes[$col]['border'] ?>;"><?= $col ?></th>
                    <?php endforeach; ?>
                    <th>Ajouter</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $artMap = [];
                foreach ($articles as $a) {
                    $rid = $a['rubrique_id'];
                    if (!isset($artMap[$rid])) $artMap[$rid] = [];
                    $artMap[$rid][] = $a;
                }
                ?>
                <?php foreach ($rubriques as $r):
                    $rid = $r['id'];
                    $ra = $artMap[$rid] ?? [];
                    $ctArts = array_filter($ra, fn($a) => $a['source_code'] === 'CT');
                    $cgiArts = array_filter($ra, fn($a) => $a['source_code'] === 'CGI');
                    $a1314Arts = array_filter($ra, fn($a) => $a['source_code'] === 'A1314');
                ?>
                <tr>
                    <td><strong><?= htmlspecialchars($r['code']) ?></strong></td>
                    <td><?= htmlspecialchars($r['libelle']) ?></td>
                    <td><small><?= htmlspecialchars($r['categorie'] ?? '') ?></small></td>
                    <td>
                        <?php foreach ($ctArts as $ca): ?>
                        <span class="badge badge-primary" style="font-size:0.75rem; padding-right:0.25rem;">
                            <?= htmlspecialchars($ca['article']) ?>
                            <a href="<?= $baseUrl ?>?delete_rsa=<?= $ca['id'] ?>"
                               onclick="return confirm('Supprimer le lien «&nbsp;<?= htmlspecialchars($ca['article']) ?>&nbsp;» (CT) pour la rubrique <?= $r['code'] ?> ?')"
                               style="color:inherit; text-decoration:none; opacity:0.6; margin-left:0.2rem;"
                               title="Supprimer ce lien">×</a>
                        </span>
                        <?php endforeach; ?>
                        <?php if (!$ctArts): ?><span style="color:var(--text-muted);">—</span><?php endif; ?>
                    </td>
                    <td>
                        <?php foreach ($cgiArts as $ca): ?>
                        <span class="badge badge-primary" style="font-size:0.75rem; padding-right:0.25rem;">
                            <?= htmlspecialchars($ca['article']) ?>
                            <a href="<?= $baseUrl ?>?delete_rsa=<?= $ca['id'] ?>"
                               onclick="return confirm('Supprimer le lien «&nbsp;<?= htmlspecialchars($ca['article']) ?>&nbsp;» (CGI) pour la rubrique <?= $r['code'] ?> ?')"
                               style="color:inherit; text-decoration:none; opacity:0.6; margin-left:0.2rem;"
                               title="Supprimer ce lien">×</a>
                        </span>
                        <?php endforeach; ?>
                        <?php if (!$cgiArts): ?><span style="color:var(--text-muted);">—</span><?php endif; ?>
                    </td>
                    <td>
                        <?php foreach ($a1314Arts as $aa): ?>
                        <span class="badge badge-info" style="font-size:0.75rem; padding-right:0.25rem;">
                            <?= htmlspecialchars($aa['article']) ?>
                            <a href="<?= $baseUrl ?>?delete_rsa=<?= $aa['id'] ?>"
                               onclick="return confirm('Supprimer le lien «&nbsp;<?= htmlspecialchars($aa['article']) ?>&nbsp;» (A1314) pour la rubrique <?= $r['code'] ?> ?')"
                               style="color:inherit; text-decoration:none; opacity:0.6; margin-left:0.2rem;"
                               title="Supprimer ce lien">×</a>
                        </span>
                        <?php endforeach; ?>
                        <?php if (!$a1314Arts): ?><span style="color:var(--text-muted);">—</span><?php endif; ?>
                    </td>
                    <td>
                        <div class="table-actions">
                            <button type="button" class="btn-icon btn-edit" title="Ajouter / modifier article" onclick="openArticleModal(<?= $rid ?>, '<?= htmlspecialchars($r['code']) ?>')">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal: Ajouter un article -->
<div class="modal fade" id="articleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <form method="post" action="<?= $baseUrl ?>">
                <?= \Core\Session::csrfField() ?>
                <input type="hidden" name="rubrique_id" id="f_rubrique_id" value="">

                <div class="modal-header">
                    <h5 class="modal-title">Lier un article à la rubrique <span id="rubriqueCodeDisplay"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Source légale</label>
                        <select name="source_id" id="f_source_id" class="form-select" required>
                            <option value="">— Choisir —</option>
                            <?php foreach ($sources as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['code']) ?> — <?= htmlspecialchars($s['libelle']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Article / Référence</label>
                        <input type="text" name="article" id="f_article" class="form-control" required
                               placeholder="Ex: Art. 57-1°, Titre II, Art. 345">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary btn-sm">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openArticleModal(rubriqueId, rubriqueCode) {
    document.getElementById('f_rubrique_id').value = rubriqueId;
    document.getElementById('rubriqueCodeDisplay').textContent = rubriqueCode;
    document.getElementById('f_source_id').value = '';
    document.getElementById('f_article').value = '';
    new bootstrap.Modal(document.getElementById('articleModal')).show();
}
</script>
