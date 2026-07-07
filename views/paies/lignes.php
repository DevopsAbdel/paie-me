<div class="card">
    <div class="card-header">
        <h3>Paies — <?= htmlspecialchars($periode['raison_sociale']) ?> — <?= str_pad($periode['mois'], 2, '0', STR_PAD_LEFT) . '/' . $periode['annee'] ?></h3>
        <div style="display:flex; gap:0.5rem;">
            <?php if (!$periode['cloturee'] && !empty($disponibles)): ?>
            <button type="button" class="btn btn-primary btn-sm" id="btn-importer">Importer</button>
            <?php endif; ?>
            <a href="/paie-me/paies/<?= $periode['id'] ?>/calculate" class="btn btn-secondary btn-sm" onclick="return confirm('Recalculer toutes les paies ?')">Recalculer</a>
            <a href="/paie-me/paies" class="btn btn-secondary btn-sm">Retour</a>
        </div>
    </div>

    <?php if (empty($paies)): ?>
        <div class="empty-state">
            <p>Aucune paie pour cette période.</p>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Matricule</th>
                        <th>N° CNSS</th>
                        <th>Salarié</th>
                        <th>Salaire brut</th>
                        <th>SBI</th>
                        <th>Ancienneté</th>
                        <th>HS (h)</th>
                        <th>M. HS</th>
                        <th>Frais pro</th>
                        <th>SNI</th>
                        <th>CNSS</th>
                        <th>AMO</th>
                        <th>IR</th>
                        <th>Net</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($paies as $pa): ?>
                    <tr>
                        <td><?= htmlspecialchars($pa['matricule']) ?></td>
                        <td><?= htmlspecialchars($pa['cnss'] ?? '') ?></td>
                        <td><?= htmlspecialchars($pa['nom_famille'] . ' ' . $pa['prenom']) ?></td>
                        <td><?= number_format($pa['salaire_brut'], 2, ',', ' ') ?></td>
                        <td><?= number_format($pa['sbi'], 2, ',', ' ') ?></td>
                        <td><?= number_format($pa['prime_anciennete'], 2, ',', ' ') ?></td>
                        <td><?= (float)$pa['heures_supplementaires'] ?: '-' ?></td>
                        <td><?= number_format($pa['montant_heures_sup'], 2, ',', ' ') ?></td>
                        <td><?= number_format($pa['frais_professionnels'], 2, ',', ' ') ?></td>
                        <td><?= number_format($pa['sni'], 2, ',', ' ') ?></td>
                        <td><?= number_format($pa['cnss_salariale'], 2, ',', ' ') ?></td>
                        <td><?= number_format($pa['amo_salariale'], 2, ',', ' ') ?></td>
                        <td><?= number_format($pa['ir'], 2, ',', ' ') ?></td>
                        <td><strong><?= number_format($pa['net_a_payer'], 2, ',', ' ') ?></strong></td>
                        <td>
                            <div class="table-actions">
                                <?php if (!$periode['cloturee']): ?>
                                <a href="/paie-me/paies/paie/<?= $pa['id'] ?>/edit" class="btn btn-secondary btn-sm">Modifier</a>
                                <a href="/paie-me/paies/paie/<?= $pa['id'] ?>/supprimer" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer la paie de <?= htmlspecialchars($pa['nom_famille'] . ' ' . $pa['prenom']) ?> ?')">Supprimer</a>
                                <?php else: ?>
                                <span class="text-muted" style="font-size:0.75rem;">—</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php if (!$periode['cloturee'] && !empty($disponibles)): ?>
<div id="modal-import" class="modal-overlay" style="display:none;">
    <div class="custom-modal" style="width:700px; max-width:95vw;">
        <div class="modal-header">
            <h3>Salariés</h3>
            <button type="button" class="btn-close" data-fermer-modal>&times;</button>
        </div>
        <div class="modal-body" style="padding:1rem;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
                <label style="font-size:0.85rem; color:var(--text-muted);">
                    <input type="checkbox" id="select-all" onchange="basculerTout()"> Sélectionner tous
                </label>
                <input type="text" id="recherche-salarie" class="form-control" placeholder="Rechercher..." style="width:220px; font-size:0.8rem;" oninput="filtrerSalaries()">
            </div>
            <form method="POST" action="/paie-me/paies/<?= $periode['id'] ?>/ajouter-salaries" id="form-import">
                <div style="max-height:360px; overflow-y:auto; border:1px solid var(--border); border-radius:4px;">
                    <table style="width:100%; border-collapse:collapse;">
                        <thead>
                            <tr>
                                <th style="width:36px; padding:0.4rem 0.5rem; font-size:0.7rem; text-align:center;">&nbsp;</th>
                                <th style="padding:0.4rem 0.5rem; font-size:0.7rem; text-align:left;">Matricule</th>
                                <th style="padding:0.4rem 0.5rem; font-size:0.7rem; text-align:left;">N° CNSS</th>
                                <th style="padding:0.4rem 0.5rem; font-size:0.7rem; text-align:left;">Nom</th>
                                <th style="padding:0.4rem 0.5rem; font-size:0.7rem; text-align:left;">Prénom</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($disponibles as $s): ?>
                            <tr class="ligne-salarie" data-nom="<?= htmlspecialchars(strtolower($s['nom_famille'])) ?>" data-prenom="<?= htmlspecialchars(strtolower($s['prenom'])) ?>" data-matricule="<?= htmlspecialchars(strtolower($s['matricule'])) ?>">
                                <td style="text-align:center; padding:0.3rem 0.5rem;">
                                    <input type="checkbox" name="salarie_ids[]" value="<?= $s['id'] ?>" class="cb-salarie">
                                </td>
                                <td style="padding:0.3rem 0.5rem; font-size:0.8rem; font-family:monospace;"><?= htmlspecialchars($s['matricule']) ?></td>
                                <td style="padding:0.3rem 0.5rem; font-size:0.8rem; font-family:monospace;"><?= htmlspecialchars($s['cnss'] ?? '') ?></td>
                                <td style="padding:0.3rem 0.5rem; font-size:0.8rem;"><?= htmlspecialchars($s['nom_famille']) ?></td>
                                <td style="padding:0.3rem 0.5rem; font-size:0.8rem;"><?= htmlspecialchars($s['prenom']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div style="display:flex; gap:0.5rem; justify-content:flex-end; margin-top:1rem; padding-top:1rem; border-top:1px solid var(--border);">
                    <button type="button" class="btn btn-secondary" data-fermer-modal>Annuler</button>
                    <button type="submit" class="btn btn-primary" id="btn-importer">Sélectionner</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<style>
.modal-overlay {
    position:fixed; top:0; left:0; width:100%; height:100%;
    background:rgba(0,0,0,0.6); display:flex; align-items:center; justify-content:center;
    z-index:1000;
}
.custom-modal {
    background:var(--surface); border:1px solid var(--border); border-radius:8px;
    box-shadow:0 8px 32px rgba(0,0,0,0.4);
}
.custom-modal .modal-header {
    display:flex; justify-content:space-between; align-items:center;
    padding:0.75rem 1rem; border-bottom:1px solid var(--border);
}
.custom-modal .modal-header h3 { margin:0; font-size:1rem; }
.custom-modal .btn-close {
    background:none; border:none; color:var(--text-muted); font-size:1.2rem;
    cursor:pointer; padding:0.2rem 0.4rem; line-height:1;
}
.custom-modal .btn-close:hover { color:var(--text); }
.btn-close {
    background:none; border:none; color:var(--text-muted); font-size:1.2rem;
    cursor:pointer; padding:0.2rem 0.4rem; line-height:1;
}
.btn-close:hover { color:var(--text); }
.ligne-salarie:hover td { background:rgba(59,130,246,0.06); }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var btn = document.getElementById('btn-importer');
    var modal = document.getElementById('modal-import');
    if (btn && modal) {
        btn.addEventListener('click', function() {
            modal.style.display = 'flex';
            document.getElementById('recherche-salarie').value = '';
            document.getElementById('select-all').checked = false;
            document.querySelectorAll('.ligne-salarie').forEach(function(r) { r.style.display = ''; });
            document.querySelectorAll('.cb-salarie').forEach(function(c) { c.checked = false; });
        });
        modal.addEventListener('click', function(e) {
            if (e.target === this) this.style.display = 'none';
        });
    }
    document.querySelectorAll('[data-fermer-modal]').forEach(function(el) {
        el.addEventListener('click', function() {
            if (modal) modal.style.display = 'none';
        });
    });
});

function basculerTout() {
    var cocher = document.getElementById('select-all').checked;
    document.querySelectorAll('.ligne-salarie:not([style*="display: none"]) .cb-salarie').forEach(function(cb) {
        cb.checked = cocher;
    });
}

function filtrerSalaries() {
    var q = document.getElementById('recherche-salarie').value.toLowerCase().trim();
    document.querySelectorAll('.ligne-salarie').forEach(function(r) {
        var nom = r.getAttribute('data-nom');
        var prenom = r.getAttribute('data-prenom');
        var mat = r.getAttribute('data-matricule');
        if (!q || nom.includes(q) || prenom.includes(q) || mat.includes(q)) {
            r.style.display = '';
        } else {
            r.style.display = 'none';
        }
    });
}
</script>
