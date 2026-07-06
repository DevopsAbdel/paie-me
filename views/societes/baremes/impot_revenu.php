<form method="post" action="<?= $baseUrl ?>/impot_revenu">
<?= \Core\Session::csrfField() ?>
<input type="hidden" name="sous_tab" value="impot_revenu">
<?php $types = [['key'=>'mensuel','label'=>'Mensuel','data'=>$bareme], ['key'=>'annuel','label'=>'Annuel','data'=>$baremeAnnuel]]; ?>
<?php foreach ($types as $t): ?>
<div class="card" style="<?= $t['key'] === 'annuel' ? 'margin-top:1.5rem;' : '' ?>">
    <div class="card-header"><h3>Barème IR 2025 — <?= $t['label'] ?></h3></div>
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr><th>Tranche min (MAD)</th><th>Tranche max (MAD)</th><th>Taux (%)</th><th>Déduction (MAD)</th></tr>
            </thead>
            <tbody>
                <?php foreach ($t['data'] as $b): ?>
                <tr>
                    <td><input type="number" name="min[<?= $b['id'] ?>]" value="<?= $b['min'] ?>" class="form-control" step="0.01" style="width:120px;"></td>
                    <td><input type="number" name="max[<?= $b['id'] ?>]" value="<?= $b['max'] ?>" class="form-control" step="0.01" style="width:120px;"></td>
                    <td><input type="number" name="taux[<?= $b['id'] ?>]" value="<?= $b['taux'] ?>" class="form-control" step="0.01" style="width:80px;"></td>
                    <td><input type="number" name="deduction[<?= $b['id'] ?>]" value="<?= $b['deduction'] ?>" class="form-control" step="0.01" style="width:120px;"></td>
                    <input type="hidden" name="type[<?= $b['id'] ?>]" value="<?= $b['type'] ?>">
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endforeach; ?>
<div style="margin-top:1rem; padding-top:1rem; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
    <p style="font-size:0.8125rem; color:var(--text-muted); margin:0;">Barème progressif 2025 appliqué automatiquement au calcul de chaque paie.</p>
    <button type="submit" class="btn btn-primary">Mettre à jour le barème</button>
</div>
</form>
