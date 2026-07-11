<div class="card">
    <div class="card-header">
        <h3><?= $salarie ? 'Modifier' : 'Nouveau' ?> salarié</h3>
    </div>
    <form method="POST">
        <?= \Core\Session::csrfField() ?>

        <h4 class="form-section-title">État civil</h4>
        <hr class="form-section-sep">
        <div class="form-row">
            <div class="form-group">
                <label>Nom de famille *</label>
                <input type="text" name="nom_famille" class="form-control" value="<?= $salarie['nom_famille'] ?? '' ?>" required>
            </div>
            <div class="form-group">
                <label>Prénom *</label>
                <input type="text" name="prenom" class="form-control" value="<?= $salarie['prenom'] ?? '' ?>" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Situation familiale</label>
                <select name="situation_familiale" class="form-control">
                    <option value="celibataire" <?= ($salarie['situation_familiale'] ?? '') === 'celibataire' ? 'selected' : '' ?>>Célibataire</option>
                    <option value="marie" <?= ($salarie['situation_familiale'] ?? '') === 'marie' ? 'selected' : '' ?>>Marié(e)</option>
                    <option value="divorce" <?= ($salarie['situation_familiale'] ?? '') === 'divorce' ? 'selected' : '' ?>>Divorcé(e)</option>
                    <option value="veuf" <?= ($salarie['situation_familiale'] ?? '') === 'veuf' ? 'selected' : '' ?>>Veuf/Veuve</option>
                </select>
            </div>
            <div class="form-group">
                <label>Nb enfants (total)</label>
                <input type="number" name="nb_enfants" class="form-control" value="<?= $salarie['nb_enfants'] ?? 0 ?>" min="0">
            </div>
            <div class="form-group">
                <label>Enfants à charge</label>
                <input type="number" name="enfants_a_charge" class="form-control" id="enfants_a_charge" value="<?= $salarie['enfants_a_charge'] ?? 0 ?>" min="0">
            </div>
            <div class="form-group">
                <label>Personnes à charge</label>
                <input type="number" name="personnes_a_charge" class="form-control" id="personnes_a_charge" readonly value="<?= $salarie['personnes_a_charge'] ?? 0 ?>" min="0" style="cursor:not-allowed; opacity:0.65;">
            </div>
        </div>
        <small style="display:block; color:var(--text-muted); font-size:0.7rem; margin-top:-0.5rem; margin-bottom:0.75rem;">&lt;18 ans / &le;21 ans étudiant / &le;25 ans études sup. — Personnes à charge = enfants à charge + conjoint si marié(e)</small>
        <script>
        (function() {
            var sitEl = document.querySelector('select[name="situation_familiale"]');
            var enfEl = document.querySelector('input[name="enfants_a_charge"]');
            var nbEl = document.querySelector('input[name="nb_enfants"]');
            var pacEl = document.getElementById('personnes_a_charge');

            function calc() {
                var nb = parseInt(nbEl.value) || 0;
                var enf = parseInt(enfEl.value) || 0;
                if (enf > nb) { enf = nb; enfEl.value = nb; }
                var conj = sitEl.value === 'marie' ? 1 : 0;
                pacEl.value = enf + conj;
            }

            sitEl.addEventListener('change', calc);
            enfEl.addEventListener('input', calc);
            nbEl.addEventListener('input', calc);
            calc();
        })();
        </script>

        <h4 class="form-section-title">Immatriculation</h4>
        <hr class="form-section-sep">
        <div class="form-row">
            <div class="form-group">
                <label>Date de naissance</label>
                <input type="date" name="date_naissance" class="form-control" value="<?= $salarie['date_naissance'] ?? '' ?>">
            </div>
            <div class="form-group">
                <label>CIN</label>
                <input type="text" name="cin" class="form-control" value="<?= $salarie['cin'] ?? '' ?>">
            </div>
            <div class="form-group">
                <label>CNSS</label>
                <input type="text" name="cnss" class="form-control" value="<?= $salarie['cnss'] ?? '' ?>">
            </div>
        </div>

        <h4 class="form-section-title">Coordonnées</h4>
        <hr class="form-section-sep">
        <div class="form-group">
            <label>Adresse</label>
            <textarea name="adresse" class="form-control" rows="2"><?= $salarie['adresse'] ?? '' ?></textarea>
        </div>

        <h4 class="form-section-title">Affectation</h4>
        <hr class="form-section-sep">
        <div class="form-row">
            <div class="form-group">
                <label>Société *</label>
                <?php if ($societeContext ?? null): ?>
                <input type="hidden" name="societe_id" value="<?= (int)$societeContext['id'] ?>">
                <p class="form-text" style="color:var(--text-muted); padding:0.625rem 0.75rem; background:var(--bg-surface); border:1px solid var(--border); border-radius:6px;"><?= htmlspecialchars($societeContext['raison_sociale']) ?></p>
                <?php else: ?>
                <select name="societe_id" class="form-control" required onchange="var s=document.getElementById('service_group'),f=document.getElementById('fonction_group');s.style.display=this.value?'block':'none';f.style.display=this.value?'block':'none'">
                    <option value="">— Sélectionner —</option>
                    <?php foreach ($societes as $so): ?>
                    <option value="<?= $so['id'] ?>" <?= ($fromSociete ?? $salarie['societe_id'] ?? '') == $so['id'] ? 'selected' : '' ?>><?= htmlspecialchars($so['raison_sociale']) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php endif; ?>
            </div>
            <div class="form-group" id="service_group" style="display:<?= ($salarie['societe_id'] ?? $fromSociete) ? 'block' : 'none' ?>">
                <label>Service</label>
                <select name="service_id" class="form-control" onchange="filterFonctions(this.value)">
                    <option value="">— Aucun —</option>
                    <?php foreach ($services as $sv): ?>
                    <option value="<?= $sv['id'] ?>" <?= ($salarie['service_id'] ?? '') == $sv['id'] ? 'selected' : '' ?>><?= htmlspecialchars($sv['nom']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" id="fonction_group" style="display:<?= ($salarie['societe_id'] ?? $fromSociete) ? 'block' : 'none' ?>">
                <label>Fonction</label>
                <select name="fonction_id" class="form-control">
                    <option value="">— Aucune —</option>
                    <?php foreach ($fonctions as $fn): ?>
                    <option value="<?= $fn['id'] ?>" data-service-id="<?= $fn['service_id'] ?: '' ?>" <?= ($salarie['fonction_id'] ?? '') == $fn['id'] ? 'selected' : '' ?>><?= htmlspecialchars($fn['nom']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Matricule *</label>
                <input type="text" name="matricule" class="form-control" value="<?= $salarie['matricule'] ?? '' ?>" required>
            </div>
            <div class="form-group">
                <label>Date d'embauche</label>
                <input type="date" name="date_embauche" class="form-control" value="<?= $salarie['date_embauche'] ?? '' ?>">
            </div>
            <div class="form-group">
                <label>Poste / Fonction</label>
                <input type="text" name="poste" class="form-control" value="<?= $salarie['poste'] ?? '' ?>">
            </div>
        </div>

        <h4 class="form-section-title">Contrat</h4>
        <hr class="form-section-sep">
        <div class="form-row">
            <div class="form-group">
                <label>Type de contrat</label>
                <select name="type_contrat" class="form-control">
                    <option value="CDI" <?= ($salarie['type_contrat'] ?? '') === 'CDI' ? 'selected' : '' ?>>CDI</option>
                    <option value="CDD" <?= ($salarie['type_contrat'] ?? '') === 'CDD' ? 'selected' : '' ?>>CDD</option>
                    <option value="stage" <?= ($salarie['type_contrat'] ?? '') === 'stage' ? 'selected' : '' ?>>Stage</option>
                    <option value="interim" <?= ($salarie['type_contrat'] ?? '') === 'interim' ? 'selected' : '' ?>>Intérim</option>
                </select>
            </div>
            <div class="form-group">
                <label>Salaire de base *</label>
                <input type="number" step="0.01" name="salaire_base" class="form-control" value="<?= $salarie['salaire_base'] ?? 0 ?>" required>
            </div>
            <div class="form-group">
                <label>Type de salaire</label>
                <select name="type_salaire" class="form-control">
                    <option value="mensuel" <?= ($salarie['type_salaire'] ?? '') === 'mensuel' ? 'selected' : '' ?>>Mensuel</option>
                    <option value="horaire" <?= ($salarie['type_salaire'] ?? '') === 'horaire' ? 'selected' : '' ?>>Horaire</option>
                    <option value="journalier" <?= ($salarie['type_salaire'] ?? '') === 'journalier' ? 'selected' : '' ?>>Journalier</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Fréquence de paiement</label>
                <select name="frequence_paiement" class="form-control">
                    <option value="mensuel" <?= ($salarie['frequence_paiement'] ?? '') === 'mensuel' ? 'selected' : '' ?>>Mensuel</option>
                    <option value="quinzaine" <?= ($salarie['frequence_paiement'] ?? '') === 'quinzaine' ? 'selected' : '' ?>>Quinzaine</option>
                    <option value="hebdomadaire" <?= ($salarie['frequence_paiement'] ?? '') === 'hebdomadaire' ? 'selected' : '' ?>>Hebdomadaire</option>
                </select>
            </div>
            <div class="form-group">
                <label>Mode de paiement</label>
                <select name="mode_paiement" class="form-control">
                    <option value="virement" <?= ($salarie['mode_paiement'] ?? '') === 'virement' ? 'selected' : '' ?>>Virement</option>
                    <option value="cheque" <?= ($salarie['mode_paiement'] ?? '') === 'cheque' ? 'selected' : '' ?>>Chèque</option>
                    <option value="especes" <?= ($salarie['mode_paiement'] ?? '') === 'especes' ? 'selected' : '' ?>>Espèces</option>
                </select>
            </div>
            <div class="form-group">
                <label>RIB</label>
                <input type="text" name="rib" class="form-control" value="<?= $salarie['rib'] ?? '' ?>">
            </div>
        </div>

        <h4 class="form-section-title">Indemnités et avantages</h4>
        <hr class="form-section-sep">
        <div class="form-row">
            <div class="form-group">
                <label>Indemnité de transport</label>
                <input type="number" step="0.01" name="indemnite_transport" class="form-control" value="<?= $salarie['indemnite_transport'] ?? 500 ?>">
            </div>
            <div class="form-group">
                <label>Indemnité de panier</label>
                <input type="number" step="0.01" name="indemnite_panier" class="form-control" value="<?= $salarie['indemnite_panier'] ?? 780 ?>">
            </div>
            <div class="form-group">
                <label>Indemnité de représentation</label>
                <input type="number" step="0.01" name="indemnite_representation" class="form-control" value="<?= $salarie['indemnite_representation'] ?? 0 ?>">
            </div>
            <div class="form-group">
                <label>Avantage logement</label>
                <input type="number" step="0.01" name="avantage_logement" class="form-control" value="<?= $salarie['avantage_logement'] ?? 0 ?>">
            </div>
        </div>

        <div style="display:flex; gap:0.75rem; margin-top:1rem;">
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="/paie-me/salaries" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>
<script>
function filterFonctions(serviceId) {
    const selects = document.querySelectorAll('select[name="fonction_id"] option');
    let selected = document.querySelector('select[name="fonction_id"]').value;
    let hasVisible = false;
    selects.forEach(function(opt) {
        if (opt.value === '') return;
        var sid = opt.getAttribute('data-service-id');
        if (!serviceId || sid === serviceId || !sid) {
            opt.style.display = '';
            hasVisible = true;
        } else {
            opt.style.display = 'none';
        }
    });
    var sel = document.querySelector('select[name="fonction_id"]');
    if (sel.value && sel.querySelector('option[value="' + sel.value + '"]')?.style.display === 'none') {
        sel.value = '';
    }
}
document.addEventListener('DOMContentLoaded', function() {
    filterFonctions(document.querySelector('select[name="service_id"]')?.value || '');
});
</script>