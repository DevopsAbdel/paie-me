<form method="post" action="<?= $baseUrl ?>/anciennete">
<?= \Core\Session::csrfField() ?>
<input type="hidden" name="sous_tab" value="anciennete">

<div class="card">
    <div class="card-header"><h3>Barème légal d'ancienneté</h3></div>
    <p style="font-size:0.8125rem; color:var(--text-muted); margin:0 0 0.5rem 0;">
        Grille légale selon le Code du Travail marocain. La prime d'ancienneté se calcule sur le salaire de base.
    </p>
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr><th>Années min</th><th>Années max</th><th>Taux (%)</th><th>Légal</th></tr>
            </thead>
            <tbody>
                <?php $legal = [['min'=>0,'max'=>2,'taux'=>0,'label'=>'Moins de 2 ans'], ['min'=>2,'max'=>5,'taux'=>5,'label'=>'2 à 5 ans'], ['min'=>5,'max'=>10,'taux'=>10,'label'=>'5 à 10 ans'], ['min'=>10,'max'=>15,'taux'=>15,'label'=>'10 à 15 ans'], ['min'=>15,'max'=>20,'taux'=>20,'label'=>'15 à 20 ans'], ['min'=>20,'max'=>25,'taux'=>25,'label'=>'20 à 25 ans'], ['min'=>25,'max'=>99,'taux'=>30,'label'=>'25 ans et +']]; ?>
                <?php if (!empty($anciennete)): ?>
                <?php foreach ($anciennete as $i => $a): ?>
                <tr>
                    <td><input type="number" name="annees_min[<?= $i ?>]" value="<?= $a['annees_min'] ?>" class="form-control" min="0" style="width:80px;"></td>
                    <td><input type="number" name="annees_max[<?= $i ?>]" value="<?= $a['annees_max'] ?>" class="form-control" min="0" style="width:80px;"></td>
                    <td><input type="number" name="taux[<?= $i ?>]" value="<?= $a['taux'] ?>" class="form-control" step="0.01" min="0" style="width:80px;"></td>
                    <td style="color:var(--text-muted); font-size:0.8rem;"><?= $legal[array_search($a['annees_min'], array_column($legal, 'min'))]['label'] ?? '—' ?></td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <?php foreach ($legal as $i => $l): ?>
                <tr>
                    <td><input type="number" name="annees_min[<?= $i ?>]" value="<?= $l['min'] ?>" class="form-control" min="0" style="width:80px;"></td>
                    <td><input type="number" name="annees_max[<?= $i ?>]" value="<?= $l['max'] ?>" class="form-control" min="0" style="width:80px;"></td>
                    <td><input type="number" name="taux[<?= $i ?>]" value="<?= $l['taux'] ?>" class="form-control" step="0.01" min="0" style="width:80px;"></td>
                    <td style="color:var(--text-muted); font-size:0.8rem;"><?= $l['label'] ?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div style="margin-top:1rem; display:flex; justify-content:flex-end;">
    <button type="submit" class="btn btn-success">Enregistrer le barème</button>
</div>
</form>
