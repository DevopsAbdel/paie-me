<div class="card">
    <div class="card-header">
        <h3>Génération fichier DS — Damancom</h3>
    </div>

    <?php if (empty($periodes)): ?>
        <div class="empty-state">
            <p>Aucune période clôturée disponible. Veuillez d'abord créer et clôturer une période de paie.</p>
            <a href="/paie-me/paies/create" class="btn btn-primary">Créer une paie</a>
        </div>
    <?php else: ?>
        <form method="POST" action="/paie-me/damancom/generate">
            <?= \Core\Session::csrfField() ?>
            <div class="form-group">
                <label>Période clôturée *</label>
                <select name="periode_id" class="form-control" required>
                    <option value="">— Sélectionner —</option>
                    <?php foreach ($periodes as $p): ?>
                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['raison_sociale']) ?> — <?= str_pad($p['mois'], 2, '0', STR_PAD_LEFT) ?>/<?= $p['annee'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Générer le fichier DS</button>
        </form>
    <?php endif; ?>
</div>
