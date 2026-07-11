<?php $editing = !empty($conge); ?>

<div class="card">
    <div class="card-header">
        <h3><?= $editing ? 'Modifier le congé' : 'Nouvelle demande de congé' ?></h3>
    </div>

    <form method="post" action="<?= $editing ? $baseUrl . '/modifier/' . $conge['id'] : $baseUrl . '/nouveau' ?>">
        <?= \Core\Session::csrfField() ?>

        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:0.75rem; margin-bottom:0.75rem;">
            <div class="form-group">
                <label>Salarié</label>
                <?php if ($editing): ?>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($conge['matricule'] . ' — ' . $conge['nom_complet']) ?>" disabled>
                <?php else: ?>
                    <select name="salarie_id" class="form-control" required>
                        <option value="">— Sélectionner —</option>
                        <?php foreach ($salaries as $s): ?>
                            <option value="<?= (int)$s['id'] ?>"><?= htmlspecialchars($s['matricule'] . ' — ' . $s['prenom'] . ' ' . $s['nom_famille']) ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label>Date début</label>
                <input type="date" name="date_debut" value="<?= htmlspecialchars($editing ? $conge['date_debut'] : '') ?>" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Date fin</label>
                <input type="date" name="date_fin" value="<?= htmlspecialchars($editing ? $conge['date_fin'] : '') ?>" class="form-control" required>
            </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:0.75rem; margin-bottom:0.75rem;">
            <div class="form-group">
                <label>Nombre de jours</label>
                <input type="number" name="nb_jours" value="<?= htmlspecialchars($editing ? $conge['nb_jours'] : '') ?>" class="form-control" step="0.5" min="0.5" required>
            </div>
            <div class="form-group">
                <label>Type de congé</label>
                <select name="type_conge" class="form-control" required>
                    <?php
                    $types = ['paye' => 'Congé payé', 'sans_solde' => 'Sans solde', 'maladie' => 'Maladie', 'maternite' => 'Maternité', 'exceptionnel' => 'Exceptionnel', 'autre' => 'Autre'];
                    foreach ($types as $val => $label):
                        $sel = ($editing && $conge['type_conge'] === $val) ? 'selected' : '';
                    ?>
                        <option value="<?= $val ?>" <?= $sel ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Statut</label>
                <select name="statut" class="form-control">
                    <?php
                    $statuts = ['en_attente' => 'En attente', 'valide' => 'Validé', 'refuse' => 'Refusé', 'annule' => 'Annulé'];
                    foreach ($statuts as $val => $label):
                        $sel = ($editing && $conge['statut'] === $val) ? 'selected' : (!$editing && $val === 'en_attente' ? 'selected' : '');
                    ?>
                        <option value="<?= $val ?>" <?= $sel ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Observation</label>
            <textarea name="observation" class="form-control" rows="3"><?= htmlspecialchars($editing ? ($conge['observation'] ?? '') : '') ?></textarea>
        </div>

        <div style="margin-top:1rem; display:flex; justify-content:flex-end; gap:0.5rem;">
            <a href="<?= $baseUrl ?>" class="btn btn-secondary">Annuler</a>
            <button type="submit" class="btn btn-primary"><?= $editing ? 'Enregistrer' : 'Créer la demande' ?></button>
        </div>
    </form>
</div>
