<div class="card">
    <div class="card-header">
        <h3>Modifier la paie</h3>
        <a href="/paie-me/paies" class="btn btn-secondary btn-sm">Retour aux périodes</a>
    </div>

    <div style="padding:1rem; border-bottom:1px solid var(--border);">
        <p><strong>Salarié :</strong> <?= htmlspecialchars($paie['nom_famille'] . ' ' . $paie['prenom']) ?></p>
        <p><strong>Société :</strong> <?= htmlspecialchars($paie['raison_sociale']) ?></p>
        <p><strong>Salaire brut :</strong> <?= number_format($paie['salaire_brut'], 2, ',', ' ') ?> MAD</p>
    </div>

    <form method="POST" style="padding:1rem;">
        <?= \Core\Session::csrfField() ?>
        <div class="form-group">
            <label>Heures supplémentaires</label>
            <input type="number" step="0.5" min="0" name="heures_supplementaires" class="form-control"
                   value="<?= $paie['heures_supplementaires'] ?>" placeholder="Nombre d'heures sup">
            <small style="color:var(--text-muted);">Le montant sera calculé automatiquement lors du recalcul (taux horaire × 1,25).</small>
        </div>
        <div style="display:flex; gap:0.75rem; margin-top:1rem;">
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="/paie-me/paies" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>
