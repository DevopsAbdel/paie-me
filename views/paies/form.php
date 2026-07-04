<div class="card">
    <div class="card-header">
        <h3>Nouvelle période de paie</h3>
    </div>
    <form method="POST">
        <?= \Core\Session::csrfField() ?>
        <div class="form-group">
            <label>Société *</label>
            <select name="societe_id" class="form-control" required>
                <option value="">— Sélectionner —</option>
                <?php foreach ($societes as $so): ?>
                <option value="<?= $so['id'] ?>" <?= ($fromSociete ?? '') == $so['id'] ? 'selected' : '' ?>><?= htmlspecialchars($so['raison_sociale']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Mois *</label>
                <select name="mois" class="form-control" required>
                    <option value="">—</option>
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?= $m ?>" <?= $m === (int)date('n') ? 'selected' : '' ?>><?= str_pad($m, 2, '0', STR_PAD_LEFT) ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Année *</label>
                <select name="annee" class="form-control" required>
                    <?php for ($y = date('Y') - 2; $y <= date('Y') + 1; $y++): ?>
                    <option value="<?= $y ?>" <?= $y === (int)date('Y') ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Date début</label>
                <input type="date" name="date_debut" class="form-control">
            </div>
            <div class="form-group">
                <label>Date fin</label>
                <input type="date" name="date_fin" class="form-control">
            </div>
        </div>
        <div style="display:flex; gap:0.75rem; margin-top:1rem;">
            <button type="submit" class="btn btn-primary">Créer et calculer</button>
            <a href="/paie-me/paies" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>
