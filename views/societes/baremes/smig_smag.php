<div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap;">
        <h3 style="margin:0;">Barème SMIG & SMAG</h3>
        <div style="display:flex; gap:0.4rem; align-items:center; flex-wrap:wrap;">
            <form method="post" action="<?= $baseUrl ?>/smig_smag" style="display:flex; gap:0.4rem; align-items:center; flex-wrap:wrap;">
                <?= \Core\Session::csrfField() ?>
                <input type="hidden" name="sous_tab" value="smig_smag">
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
    </div>
    <div style="overflow-x:auto;">
        <form method="post" action="<?= $baseUrl ?>/smig_smag">
            <?= \Core\Session::csrfField() ?>
            <input type="hidden" name="sous_tab" value="smig_smag">
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
                            <a href="<?= $baseUrl ?>/smig_smag?delete_bareme=<?= $b['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce barème ?')" style="padding:0.2rem 0.4rem; font-size:0.7rem;">✕</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <?php if (!empty($baremeSmigSmag)): ?>
            <div style="padding:0.75rem 1rem; border-top:1px solid var(--border); display:flex; align-items:center; justify-content:space-between;">
                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCalculSalaire" style="font-size:0.8rem;">Calculer SMIG/SMAG</button>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php if (!empty($baremeSmigSmag)): ?>
<div class="modal fade" id="modalCalculSalaire" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background:var(--bg-surface); color:var(--text); border:1px solid var(--border);">
            <div class="modal-header" style="border-bottom:1px solid var(--border);">
                <h5 class="modal-title">Calculer salaire SMIG / SMAG</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
                    <div>
                        <label class="form-label" style="font-size:0.8rem; font-weight:600;">Type</label>
                        <select id="calcType" class="form-control">
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
                        <label class="form-label" style="font-size:0.8rem; font-weight:600;">Jours travaillés</label>
                        <input type="number" id="calcJours" class="form-control" min="0" max="31" step="0.5" value="26" placeholder="26">
                    </div>
                </div>
                <div style="background:var(--bg); border:1px solid var(--border); border-radius:6px; padding:1rem; margin-bottom:0.5rem;">
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.75rem; font-size:0.85rem;">
                        <div>
                            <span style="color:var(--text-muted);">Taux horaire</span><br>
                            <strong id="calcTauxH" style="font-size:1.1rem;">—</strong>
                        </div>
                        <div>
                            <span style="color:var(--text-muted);">Taux mensuel</span><br>
                            <strong id="calcTauxM" style="font-size:1.1rem;">—</strong>
                        </div>
                        <div>
                            <span style="color:var(--text-muted);">Base mensuelle</span><br>
                            <strong id="calcBase">—</strong>
                        </div>
                        <div>
                            <span style="color:var(--text-muted);">Taux journalier</span><br>
                            <strong id="calcTauxJ">—</strong>
                        </div>
                    </div>
                </div>
                <div style="background:var(--accent); color:#fff; border-radius:6px; padding:1rem; text-align:center;">
                    <div style="font-size:0.8rem; opacity:0.85; margin-bottom:0.25rem;">Salaire brut calculé</div>
                    <div id="calcResultat" style="font-size:1.8rem; font-weight:700;">0,00 MAD</div>
                    <div id="calcDetail" style="font-size:0.75rem; opacity:0.75; margin-top:0.25rem;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sel = document.getElementById('calcType');
    const joursInput = document.getElementById('calcJours');
    const tauxH = document.getElementById('calcTauxH');
    const tauxM = document.getElementById('calcTauxM');
    const baseEl = document.getElementById('calcBase');
    const tauxJ = document.getElementById('calcTauxJ');
    const resultat = document.getElementById('calcResultat');
    const detail = document.getElementById('calcDetail');

    function fmt(n) {
        return n.toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    function calculer() {
        const opt = sel.options[sel.selectedIndex];
        const type = sel.value;
        const horaire = parseFloat(opt.dataset.horaire) || 0;
        const mensuel = parseFloat(opt.dataset.mensuel) || 0;
        const jours = parseFloat(joursInput.value) || 0;
        const annee = opt.dataset.annee || '';

        let baseLabel = '', tauxJVal = 0, salaire = 0;

        if (type === 'SMIG') {
            baseLabel = '191 h/mois (art. 184)';
            tauxJVal = mensuel / 26;
            salaire = mensuel / 26 * jours;
        } else {
            baseLabel = '26 jours/mois';
            tauxJVal = mensuel / 26;
            salaire = mensuel / 26 * jours;
        }

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
