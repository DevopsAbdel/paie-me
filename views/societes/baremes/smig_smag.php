<div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap;">
        <h3 style="margin:0;">Barème SMIG & SMAG</h3>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#ajoutBaremeSmigSmag">+ Ajouter</button>
    </div>
    <div style="overflow-x:auto;">
        <form method="post" action="<?= $baseUrl ?>/smig_smag" id="smigSmagForm">
            <?= \Core\Session::csrfField() ?>
            <input type="hidden" name="sous_tab" value="smig_smag">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="text-align:center;">Année</th>
                        <th style="text-align:center;">Type</th>
                        <th style="text-align:center;">Taux horaire (MAD/h)</th>
                        <th style="text-align:center;">Taux mensuel (MAD/mois)</th>
                        <th style="text-align:center;">Date d'effet</th>
                        <th id="sm-actions-header" style="width:60px; text-align:center; display:none;"></th>
                    </tr>
                </thead>
                <tbody id="sm-tbody">
                    <?php if (empty($baremeSmigSmag)): ?>
                    <tr>
                        <td colspan="6" style="text-align:center; color:var(--text-muted); padding:2rem;">
                            Aucun barème SMIG/SMAG défini. Ajoutez-en un avec le bouton ci-dessus.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($baremeSmigSmag as $b): ?>
                    <tr data-id="<?= $b['id'] ?>" data-horaire="<?= $b['horaire'] ?>" data-mensuel="<?= $b['mensuel'] ?>" data-date="<?= htmlspecialchars($b['date_effet'] ?? '') ?>">
                        <td style="text-align:center; font-weight:600;"><?= (int) $b['annee'] ?></td>
                        <td style="text-align:center;"><span class="badge badge-<?= $b['type'] === 'SMIG' ? 'primary' : 'info' ?>"><?= htmlspecialchars($b['type']) ?></span></td>
                        <td style="text-align:right;"><?= number_format((float)$b['horaire'], 2, ',', ' ') ?></td>
                        <td style="text-align:right;"><?= number_format((float)$b['mensuel'], 2, ',', ' ') ?></td>
                        <td style="text-align:center;"><?= htmlspecialchars($b['date_effet'] ?? '—') ?></td>
                        <td class="sm-edit-action" style="width:60px; text-align:center; display:none;">
                            <div class="table-actions">
                                <button type="button" class="btn-icon btn-delete" title="Supprimer" onclick="this.closest('tr').remove()">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <div style="padding:0.5rem 0; display:flex; align-items:center; gap:0.5rem;">
                <button type="button" id="sm-btn-edit" class="btn btn-sm btn-secondary" onclick="smToggleEdit(true)" style="font-size:0.75rem;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle; margin-right:0.25rem;"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Modifier
                </button>
                <button type="button" id="sm-btn-save" class="btn btn-sm btn-success" onclick="document.getElementById('smigSmagForm').submit()" style="font-size:0.75rem; display:none;">Enregistrer les modifications</button>
                <button type="button" id="sm-btn-cancel" class="btn btn-sm btn-danger" onclick="location.reload()" style="font-size:0.75rem; display:none;">Annuler</button>
                <span style="margin-left:auto;">
                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalCalculSalaire" style="font-size:0.75rem;">Calculer SMIG/SMAG</button>
                </span>
            </div>
        </form>
    </div>
</div>

<!-- Modal Ajouter bareme SMIG/SMAG -->
<div class="modal fade" id="ajoutBaremeSmigSmag" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background:var(--bg-surface); color:var(--text); border:1px solid var(--border); border-radius:12px;">
            <form method="post" action="<?= $baseUrl ?>/smig_smag">
                <?= \Core\Session::csrfField() ?>
                <input type="hidden" name="sous_tab" value="smig_smag">
                <div class="modal-header" style="border-bottom:1px solid var(--border);">
                    <h5 class="modal-title">Nouveau barème SMIG / SMAG</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                        <div class="form-group">
                            <label class="form-label" style="font-size:0.75rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Type</label>
                            <select name="nouveau_type" class="form-control" required>
                                <option value="">— Choisir —</option>
                                <option value="SMIG">SMIG</option>
                                <option value="SMAG">SMAG</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="font-size:0.75rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Année</label>
                            <input type="number" name="nouvelle_annee" class="form-control" min="2020" max="2035" placeholder="2026" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="font-size:0.75rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Taux horaire (MAD/h)</label>
                            <input type="number" step="0.01" name="nouveau_horaire" class="form-control" placeholder="17.92" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="font-size:0.75rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Taux mensuel (MAD/mois)</label>
                            <input type="number" step="0.01" name="nouveau_mensuel" class="form-control" placeholder="3422.72" required>
                        </div>
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

<?php if (!empty($baremeSmigSmag)): ?>
<div class="modal fade" id="modalCalculSalaire" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background:var(--bg-surface); color:var(--text); border:1px solid var(--border); border-radius:12px; overflow:hidden;">
            <div style="background:linear-gradient(135deg, #059669 0%, #10b981 100%); padding:1.25rem 1.5rem; display:flex; align-items:center; gap:0.75rem;">
                <div style="width:40px; height:40px; background:rgba(255,255,255,0.2); border-radius:10px; display:flex; align-items:center; justify-content:center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2"/><line x1="8" y1="6" x2="16" y2="6"/><line x1="8" y1="10" x2="16" y2="10"/><line x1="8" y1="14" x2="12" y2="14"/></svg>
                </div>
                <div>
                    <h5 class="modal-title" style="margin:0; font-size:1rem; font-weight:700; color:#fff;">Calculer salaire SMIG / SMAG</h5>
                    <small style="color:rgba(255,255,255,0.75); font-size:0.75rem;">Simulation rapide basée sur les derniers taux</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" style="margin-left:auto;"></button>
            </div>
            <div class="modal-body" style="padding:1.25rem 1.5rem;">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1.25rem;">
                    <div>
                        <label class="form-label" style="font-size:0.75rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.35rem;">Type de barème</label>
                        <select id="calcType" class="form-control" style="font-weight:600;">
                            <?php
                            $latestSmig = null;
                            $latestSmag = null;
                            foreach ($baremeSmigSmag as $b) {
                                if ($b['type'] === 'SMIG' && !$latestSmig) $latestSmig = $b;
                                if ($b['type'] === 'SMAG' && !$latestSmag) $latestSmag = $b;
                            }
                            if ($latestSmig): ?>
                                <option value="SMIG" data-horaire="<?= $latestSmig['horaire'] ?>" data-mensuel="<?= $latestSmig['mensuel'] ?>" data-annee="<?= $latestSmig['annee'] ?>">SMIG <?= $latestSmig['annee'] ?></option>
                            <?php endif;
                            if ($latestSmag): ?>
                                <option value="SMAG" data-horaire="<?= $latestSmag['horaire'] ?>" data-mensuel="<?= $latestSmag['mensuel'] ?>" data-annee="<?= $latestSmag['annee'] ?>">SMAG <?= $latestSmag['annee'] ?></option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div>
                        <label class="form-label" style="font-size:0.75rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.35rem;">Jours travaillés</label>
                        <input type="number" id="calcJours" class="form-control" min="0" max="31" step="0.5" value="26" placeholder="26" style="font-weight:600;">
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.75rem; margin-bottom:1.25rem;">
                    <div style="background:var(--bg); border:1px solid var(--border); border-radius:8px; padding:0.75rem;">
                        <div style="font-size:0.7rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">Taux horaire</div>
                        <div id="calcTauxH" style="font-size:1.05rem; font-weight:700; color:var(--accent);">—</div>
                    </div>
                    <div style="background:var(--bg); border:1px solid var(--border); border-radius:8px; padding:0.75rem;">
                        <div style="font-size:0.7rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">Taux journalier</div>
                        <div id="calcTauxJ" style="font-size:1.05rem; font-weight:700; color:#f59e0b;">—</div>
                    </div>
                    <div style="background:var(--bg); border:1px solid var(--border); border-radius:8px; padding:0.75rem;">
                        <div style="font-size:0.7rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">Taux mensuel</div>
                        <div id="calcTauxM" style="font-size:1.05rem; font-weight:700; color:#8b5cf6;">—</div>
                    </div>
                    <div style="background:var(--bg); border:1px solid var(--border); border-radius:8px; padding:0.75rem;">
                        <div style="font-size:0.7rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">Base mensuelle</div>
                        <div id="calcBase" style="font-size:0.9rem; font-weight:600; color:var(--text);">—</div>
                    </div>
                </div>

                <div id="calcResultatBox" style="background:linear-gradient(135deg, #059669 0%, #10b981 100%); color:#fff; border-radius:10px; padding:1.25rem; text-align:center; box-shadow:0 4px 15px rgba(16,185,129,0.3);">
                    <div style="font-size:0.7rem; text-transform:uppercase; letter-spacing:0.1em; opacity:0.8; margin-bottom:0.35rem;">Salaire brut estimé</div>
                    <div id="calcResultat" style="font-size:2rem; font-weight:800; line-height:1.1;">0,00 MAD</div>
                    <div id="calcDetail" style="font-size:0.72rem; opacity:0.7; margin-top:0.4rem;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function smToggleEdit(edit) {
    document.getElementById('sm-btn-edit').style.display = edit ? 'none' : '';
    document.getElementById('sm-btn-save').style.display = edit ? '' : 'none';
    document.getElementById('sm-btn-cancel').style.display = edit ? '' : 'none';
    document.getElementById('sm-actions-header').style.display = edit ? '' : 'none';

    document.querySelectorAll('#sm-tbody tr').forEach(function(row) {
        if (edit && row.dataset.id) {
            var id = row.dataset.id;
            var horaire = row.dataset.horaire;
            var mensuel = row.dataset.mensuel;
            var dateEffet = row.dataset.date;
            var tds = row.querySelectorAll('td');
            tds[2].innerHTML = '<input type="hidden" name="bareme_id[]" value="' + id + '"><input type="number" step="0.01" name="horaire[]" class="form-control-inline" value="' + horaire + '" style="width:100px; text-align:right;">';
            tds[3].innerHTML = '<input type="number" step="0.01" name="mensuel[]" class="form-control-inline" value="' + mensuel + '" style="width:100px; text-align:right;">';
            tds[4].innerHTML = '<input type="date" name="date_effet[]" class="form-control-inline" value="' + dateEffet + '" style="width:140px;">';
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    var sel = document.getElementById('calcType');
    var joursInput = document.getElementById('calcJours');
    var tauxH = document.getElementById('calcTauxH');
    var tauxM = document.getElementById('calcTauxM');
    var baseEl = document.getElementById('calcBase');
    var tauxJ = document.getElementById('calcTauxJ');
    var resultat = document.getElementById('calcResultat');
    var detail = document.getElementById('calcDetail');
    var resultatBox = document.getElementById('calcResultatBox');

    var themes = {
        SMIG: { gradient: 'linear-gradient(135deg, #059669 0%, #10b981 100%)', shadow: 'rgba(16,185,129,0.3)' },
        SMAG: { gradient: 'linear-gradient(135deg, #0284c7 0%, #38bdf8 100%)', shadow: 'rgba(56,189,248,0.3)' }
    };

    function fmt(n) {
        return n.toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    function calculer() {
        var opt = sel.options[sel.selectedIndex];
        var type = sel.value;
        var horaire = parseFloat(opt.dataset.horaire) || 0;
        var mensuel = parseFloat(opt.dataset.mensuel) || 0;
        var jours = parseFloat(joursInput.value) || 0;
        var annee = opt.dataset.annee || '';

        var t = themes[type] || themes.SMIG;
        resultatBox.style.background = t.gradient;
        resultatBox.style.boxShadow = '0 4px 15px ' + t.shadow;

        var baseLabel = '', tauxJVal = 0, salaire = 0;
        baseLabel = '26 jours/mois';
        tauxJVal = mensuel / 26;
        salaire = mensuel / 26 * jours;

        tauxH.textContent = horaire > 0 ? fmt(horaire) + ' MAD/h' : '—';
        tauxM.textContent = mensuel > 0 ? fmt(mensuel) + ' MAD' : '—';
        baseEl.textContent = baseLabel;
        tauxJ.textContent = fmt(tauxJVal) + ' MAD/j';
        resultat.textContent = fmt(salaire) + ' MAD';
        detail.textContent = fmt(mensuel) + ' ÷ 26 × ' + jours + ' jour' + (jours > 1 ? 's' : '') + (annee ? ' — ' + annee : '');
    }

    sel.addEventListener('change', calculer);
    joursInput.addEventListener('input', calculer);
    calculer();
});
</script>
<?php endif; ?>
