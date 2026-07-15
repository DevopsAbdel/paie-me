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
            <div class="form-group">
                <label>Sexe</label>
                <select name="sexe" class="form-control">
                    <option value="">— Sélectionner —</option>
                    <option value="M" <?= ($salarie['sexe'] ?? '') === 'M' ? 'selected' : '' ?>>Masculin</option>
                    <option value="F" <?= ($salarie['sexe'] ?? '') === 'F' ? 'selected' : '' ?>>Féminin</option>
                </select>
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
                <label>Lieu de naissance</label>
                <input type="text" name="lieu_naissance" class="form-control" value="<?= $salarie['lieu_naissance'] ?? '' ?>">
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
                <label>Matricule</label>
                <?php if ($salarie): ?>
                <input type="text" name="matricule" class="form-control" value="<?= $salarie['matricule'] ?? '' ?>">
                <?php else: ?>
                <input type="text" name="matricule" class="form-control" value="" placeholder="Généré automatiquement" style="cursor:not-allowed; opacity:0.65;" readonly>
                <?php endif; ?>
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
                    <option value="anapec" <?= ($salarie['type_contrat'] ?? '') === 'anapec' ? 'selected' : '' ?>>ANAPEC</option>
                    <option value="tahfiz" <?= ($salarie['type_contrat'] ?? '') === 'tahfiz' ? 'selected' : '' ?>>TAHFIZ</option>
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
        <div class="table-wrapper">
            <table class="edit-paie-table" id="indemnites-table">
                <thead>
                    <tr>
                        <th style="width:10%; text-align:center;">CODE</th>
                        <th style="width:26%; text-align:center;">LIBELLÉ</th>
                        <th style="width:15%; text-align:center;">MONTANT (DH)</th>
                        <th style="width:17%; text-align:center;">PLAFOND DGI (DH)</th>
                        <th style="width:17%; text-align:center;">PLAFOND CNSS (DH)</th>
                        <th style="width:15%; text-align:center;"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="indemnite-fixed-row">
                        <td class="code">330</td>
                        <td>Indemnité de transport</td>
                        <td><input type="number" step="0.01" min="0" name="indemnite_transport" class="form-control-inline" value="<?= $salarie['indemnite_transport'] ?? 500 ?>"></td>
                        <td style="text-align:center;font-size:0.7rem;color:var(--text-muted);">500,00 DH</td>
                        <td style="text-align:center;font-size:0.7rem;color:var(--text-muted);">500,00 DH</td>
                        <td>
                            <div class="table-actions">
                                <button type="button" class="btn-icon btn-delete" title="Supprimer l'indemnité" onclick="deleteIndemniteRow(this, 'indemnite_transport')">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr class="indemnite-fixed-row">
                        <td class="code">346</td>
                        <td>Indemnité de panier</td>
                        <td><input type="number" step="0.01" min="0" name="indemnite_panier" class="form-control-inline" value="<?= $salarie['indemnite_panier'] ?? 780 ?>"></td>
                        <td style="text-align:center;font-size:0.7rem;color:var(--text-muted);">780,00 DH</td>
                        <td style="text-align:center;font-size:0.7rem;color:var(--text-muted);">780,00 DH</td>
                        <td>
                            <div class="table-actions">
                                <button type="button" class="btn-icon btn-delete" title="Supprimer l'indemnité" onclick="deleteIndemniteRow(this, 'indemnite_panier')">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr class="indemnite-fixed-row">
                        <td class="code">331</td>
                        <td>Indemnité de représentation</td>
                        <td><input type="number" step="0.01" min="0" name="indemnite_representation" class="form-control-inline" value="<?= $salarie['indemnite_representation'] ?? 0 ?>"></td>
                        <td style="text-align:center;font-size:0.7rem;color:var(--text-muted);">10% S.B.</td>
                        <td style="text-align:center;font-size:0.7rem;color:var(--text-muted);">Imposable</td>
                        <td>
                            <div class="table-actions">
                                <button type="button" class="btn-icon btn-delete" title="Supprimer l'indemnité" onclick="deleteIndemniteRow(this, 'indemnite_representation')">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr class="indemnite-fixed-row">
                        <td class="code">340</td>
                        <td>Avantage logement</td>
                        <td><input type="number" step="0.01" min="0" name="avantage_logement" class="form-control-inline" value="<?= $salarie['avantage_logement'] ?? 0 ?>"></td>
                        <td style="text-align:center;font-size:0.7rem;color:var(--text-muted);">Imposable</td>
                        <td style="text-align:center;font-size:0.7rem;color:var(--text-muted);">Imposable</td>
                        <td>
                            <div class="table-actions">
                                <button type="button" class="btn-icon btn-delete" title="Supprimer l'indemnité" onclick="deleteIndemniteRow(this, 'avantage_logement')">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php foreach ($indemnitesCustom as $ic): ?>
                    <tr class="custom-indemnite-row">
                        <td class="code">CUST</td>
                        <td>
                            <input type="hidden" name="indemnite_custom_id[]" value="<?= $ic['id'] ?>">
                            <input type="text" name="indemnite_custom_libelle[]" class="form-control-inline" style="width:100%;text-align:left;" value="<?= htmlspecialchars($ic['libelle']) ?>">
                        </td>
                        <td><input type="number" step="0.01" min="0" name="indemnite_custom_montant[]" class="form-control-inline" value="<?= $ic['montant'] ?>"></td>
                        <td><input type="number" step="0.01" min="0" name="indemnite_custom_plafond_dgi[]" class="form-control-inline" value="<?= $ic['plafond_dgi'] ?? '' ?>" placeholder="—"></td>
                        <td><input type="number" step="0.01" min="0" name="indemnite_custom_plafond_cnss[]" class="form-control-inline" value="<?= $ic['plafond_cnss'] ?? '' ?>" placeholder="—"></td>
                        <td>
                            <div class="table-actions">
                                <button type="button" class="btn-icon btn-edit" title="Modifier l'indemnité" onclick="editIndemniteRow(this)">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </button>
                                <button type="button" class="btn-icon btn-delete" title="Supprimer l'indemnité" onclick="this.closest('tr').remove()">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <tr id="indemnites-custom-container"></tr>
                </tbody>
            </table>
        </div>
        <div style="padding:0.5rem 0;">
            <button type="button" class="btn btn-sm btn-secondary" onclick="document.getElementById('indemniteModal').style.display='flex'" style="font-size:0.75rem;">+ Ajouter une indemnité</button>
        </div>

        <h4 class="form-section-title">Gains automatiques</h4>
        <hr class="form-section-sep">
        <small style="display:block; color:var(--text-muted); font-size:0.7rem; margin-bottom:0.75rem;">Gains qui seront automatiquement appliqués lors du calcul de la paie pour ce salarié.</small>
        <div class="table-wrapper">
            <table class="edit-paie-table" id="gains-table">
                <thead>
                    <tr>
                        <th style="width:12%; text-align:center;">CODE</th>
                        <th style="width:35%; text-align:center;">LIBELLÉ</th>
                        <th style="width:15%; text-align:center;">TYPE</th>
                        <th style="width:18%; text-align:center;">MONTANT (DH)</th>
                        <th style="width:20%; text-align:center;"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($gainsCustom as $gc): ?>
                    <tr class="gain-custom-row">
                        <td class="code"><?= htmlspecialchars($gc['code']) ?></td>
                        <td>
                            <input type="hidden" name="gain_custom_rubrique_id[]" value="<?= (int)$gc['rubrique_id'] ?>">
                            <?= htmlspecialchars($gc['libelle']) ?>
                        </td>
                        <td style="font-size:0.72rem;color:var(--text-muted);">Fixe</td>
                        <td><input type="number" step="0.01" min="0" name="gain_custom_montant[]" class="form-control-inline" value="<?= $gc['montant'] ?>"></td>
                        <td>
                            <div class="table-actions">
                                <button type="button" class="btn-icon btn-delete" title="Supprimer le gain" onclick="this.closest('tr').remove()">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <tr id="gains-custom-container"></tr>
                </tbody>
            </table>
        </div>
        <div style="padding:0.5rem 0;">
            <button type="button" class="btn btn-sm btn-secondary" onclick="document.getElementById('gainModal').style.display='flex'" style="font-size:0.75rem;">+ Ajouter un gain</button>
        </div>

        <!-- Modale Gain -->
        <div class="custom-modal-overlay" id="gainModal" style="display:none;">
            <div class="custom-modal" style="max-width:700px;">
                <div class="custom-modal-header">
                    <h4 style="margin:0;">Ajouter un gain</h4>
                    <button type="button" class="btn btn-sm btn-secondary" onclick="document.getElementById('gainModal').style.display='none'" style="padding:0.2rem 0.5rem;">✕</button>
                </div>
                <div class="custom-modal-body">
                    <div style="margin-bottom:0.75rem;">
                        <input type="text" id="gain_search" class="form-control" placeholder="Rechercher par code ou libellé..." onkeyup="filterGains()" style="width:100%;">
                    </div>
                    <div style="max-height:280px;overflow-y:auto;border:1px solid var(--border);border-radius:6px;">
                        <table class="edit-paie-table" style="border:none;margin:0;">
                            <thead>
                                <tr>
                                    <th style="width:12%; text-align:left;">Code</th>
                                    <th style="width:45%; text-align:left;">Libellé</th>
                                    <th style="width:18%; text-align:left;">Type</th>
                                    <th style="width:25%;"></th>
                                </tr>
                            </thead>
                            <tbody id="gain_table_body">
                                <?php foreach ($rubriquesGains as $rg): ?>
                                <tr class="gain-row" data-id="<?= $rg['id'] ?>" data-code="<?= htmlspecialchars($rg['code']) ?>" data-libelle="<?= htmlspecialchars($rg['libelle']) ?>" data-type="<?= htmlspecialchars($rg['type_montant'] ?? 'fixe') ?>" onclick="selectGainRow(this)">
                                    <td class="code" style="text-align:left;"><?= htmlspecialchars($rg['code']) ?></td>
                                    <td style="text-align:left;"><?= htmlspecialchars($rg['libelle']) ?></td>
                                    <td style="text-align:center;font-size:0.72rem;"><?= htmlspecialchars($rg['type_montant'] ?? 'fixe') ?></td>
                                    <td style="text-align:center;"><button type="button" class="btn btn-sm btn-secondary" onclick="event.stopPropagation();selectGainRow(this.closest('tr'))" style="font-size:0.68rem;padding:0.15rem 0.5rem;">Choisir</button></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div id="gain_info" style="margin-top:0.4rem;font-size:0.72rem;color:var(--text-muted);min-height:1.2rem;"></div>
                    <div style="margin-top:0.75rem;display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
                        <div class="form-group" style="margin:0;">
                            <label style="font-size:0.78rem;font-weight:500;">Montant (DH) *</label>
                            <input type="number" step="0.01" min="0" id="gain_montant_input" class="form-control" value="0">
                        </div>
                        <div class="form-group" style="margin:0;">
                            <label style="font-size:0.78rem;font-weight:500;">Type</label>
                            <input type="text" id="gain_type_display" class="form-control" readonly value="" style="opacity:0.6;">
                        </div>
                    </div>
                </div>
                <div class="custom-modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('gainModal').style.display='none'">Annuler</button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="ajouterGainDepuisModal()">Ajouter</button>
                </div>
            </div>
        </div>

        <!-- Modale Indemnité -->
        <div class="custom-modal-overlay" id="indemniteModal" style="display:none;">
            <div class="custom-modal" style="max-width:780px;">
                <div class="custom-modal-header">
                    <h4 style="margin:0;">Ajouter une indemnité</h4>
                    <button type="button" class="btn btn-sm btn-secondary" onclick="document.getElementById('indemniteModal').style.display='none'" style="padding:0.2rem 0.5rem;">✕</button>
                </div>
                <div class="custom-modal-body">
                    <div style="margin-bottom:0.75rem;">
                        <input type="text" id="indemnite_search" class="form-control" placeholder="Rechercher par code ou libellé..." onkeyup="filterIndemnites()" style="width:100%;">
                    </div>
                    <div style="max-height:280px;overflow-y:auto;border:1px solid var(--border);border-radius:6px;">
                        <table class="edit-paie-table" style="border:none;margin:0;">
                            <thead>
                                <tr>
                                    <th style="width:10%; text-align:left;">Code</th>
                                    <th style="width:42%; text-align:left;">Libellé</th>
                                    <th style="width:24%; text-align:left;">Plafond DGI</th>
                                    <th style="width:24%; text-align:left;">Plafond CNSS</th>
                                    <th style="width:12%;"></th>
                                </tr>
                            </thead>
                            <tbody id="indemnite_table_body">
                                <?php foreach ($rubriquesIndemnites as $ri):
                                    $plafondDgiText = !empty($ri['plafond_dgi']) ? htmlspecialchars($ri['plafond_dgi']) : '—';
                                    $plafondCnssText = !empty($ri['plafond_cnss']) ? htmlspecialchars($ri['plafond_cnss']) : '—';
                                ?>
                                <tr class="indemnite-row" data-code="<?= htmlspecialchars($ri['code']) ?>" data-libelle="<?= htmlspecialchars($ri['libelle']) ?>" data-plafond-dgi="<?= htmlspecialchars($ri['plafond_dgi'] ?? '') ?>" data-plafond-cnss="<?= htmlspecialchars($ri['plafond_cnss'] ?? '') ?>" onclick="selectIndemniteRow(this)">
                                    <td class="code" style="text-align:left;"><?= htmlspecialchars($ri['code']) ?></td>
                                    <td style="text-align:left;"><?= htmlspecialchars($ri['libelle']) ?></td>
                                    <td style="text-align:left;font-size:0.72rem;color:var(--text-muted);"><?= $plafondDgiText ?></td>
                                    <td style="text-align:left;font-size:0.72rem;color:var(--text-muted);"><?= $plafondCnssText ?></td>
                                    <td style="text-align:center;"><button type="button" class="btn btn-sm btn-secondary" onclick="event.stopPropagation();selectIndemniteRow(this.closest('tr'))" style="font-size:0.68rem;padding:0.15rem 0.5rem;">Choisir</button></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div id="indemnite_info" style="margin-top:0.4rem;font-size:0.72rem;color:var(--text-muted);min-height:1.2rem;"></div>
                    <div style="margin-top:0.75rem;display:grid;grid-template-columns:1fr 1fr 1fr;gap:0.75rem;">
                        <div class="form-group" style="margin:0;">
                            <label style="font-size:0.78rem;font-weight:500;">Montant (DH) *</label>
                            <input type="number" step="0.01" min="0" id="indemnite_montant_input" class="form-control" value="0">
                        </div>
                        <div class="form-group" style="margin:0;">
                            <label style="font-size:0.78rem;font-weight:500;">Plafond DGI (DH)</label>
                            <input type="number" step="0.01" min="0" id="indemnite_plafond_dgi_input" class="form-control" placeholder="Optionnel">
                        </div>
                        <div class="form-group" style="margin:0;">
                            <label style="font-size:0.78rem;font-weight:500;">Plafond CNSS (DH)</label>
                            <input type="number" step="0.01" min="0" id="indemnite_plafond_cnss_input" class="form-control" placeholder="Optionnel">
                        </div>
                    </div>
                </div>
                <div class="custom-modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('indemniteModal').style.display='none'">Annuler</button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="ajouterIndemniteDepuisModal()">Ajouter</button>
                </div>
            </div>
        </div>

        <div style="display:flex; gap:0.75rem; margin-top:1rem;">
            <button type="submit" class="btn btn-success">Enregistrer</button>
            <a href="/paie-me/salaries" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>

<style>
.edit-paie-table { width:100%; border-collapse:collapse; }
.edit-paie-table th { padding:0.4rem 0.5rem; text-align:center; font-size:0.7rem; text-transform:uppercase; letter-spacing:0.04em; color:var(--text-muted); border-bottom:1px solid var(--border); }
.edit-paie-table td { padding:0.3rem 0.5rem; font-size:0.8rem; border-bottom:1px solid var(--border-subtle); text-align:center; }
.edit-paie-table td.code { text-align:center; font-size:0.7rem; color:var(--text-muted); font-family:monospace; }
.form-control-inline { width:100px; padding:0.3rem 0.4rem; font-size:0.8rem; background:var(--bg-surface); border:1px solid var(--border); border-radius:4px; color:var(--text); text-align:right; }
.form-control-inline:focus { border-color:var(--accent); outline:none; }
.form-control-inline:-webkit-autofill { -webkit-box-shadow:0 0 0 30px var(--bg-surface) inset !important; -webkit-text-fill-color:var(--text) !important; }
</style>
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

let indemniteSelected = null;

function filterIndemnites() {
    const q = document.getElementById('indemnite_search').value.toLowerCase();
    document.querySelectorAll('#indemnite_table_body tr').forEach(tr => {
        const code = tr.dataset.code.toLowerCase();
        const libelle = tr.dataset.libelle.toLowerCase();
        tr.style.display = (code.includes(q) || libelle.includes(q)) ? '' : 'none';
    });
}

function selectIndemniteRow(tr) {
    document.querySelectorAll('#indemnite_table_body tr').forEach(r => r.classList.remove('selected'));
    tr.classList.add('selected');
    indemniteSelected = {
        code: tr.dataset.code,
        libelle: tr.dataset.libelle,
    };
    document.getElementById('indemnite_info').textContent = 'Sélection : ' + tr.dataset.code + ' — ' + tr.dataset.libelle;
}

function ajouterIndemniteDepuisModal() {
    if (!indemniteSelected) { alert('Veuillez sélectionner une indemnité dans la liste.'); return; }
    var montant = parseFloat(document.getElementById('indemnite_montant_input').value) || 0;
    if (montant <= 0) { alert('Le montant doit être supérieur à 0.'); return; }
    var plafondDgi = document.getElementById('indemnite_plafond_dgi_input').value;
    var plafondCnss = document.getElementById('indemnite_plafond_cnss_input').value;
    var tr = document.createElement('tr');
    tr.className = 'custom-indemnite-row';
    tr.innerHTML =
        '<td class="code">' + indemniteSelected.code + '</td>' +
        '<td><input type="hidden" name="indemnite_custom_id[]" value="0"><input type="hidden" name="indemnite_custom_libelle[]" value="' + indemniteSelected.libelle.replace(/"/g, '&quot;') + '">' + indemniteSelected.libelle + '</td>' +
        '<td><input type="number" step="0.01" min="0" name="indemnite_custom_montant[]" class="form-control-inline" value="' + montant.toFixed(2) + '"></td>' +
        '<td><input type="number" step="0.01" min="0" name="indemnite_custom_plafond_dgi[]" class="form-control-inline" value="' + (plafondDgi || '') + '" placeholder="—"></td>' +
        '<td><input type="number" step="0.01" min="0" name="indemnite_custom_plafond_cnss[]" class="form-control-inline" value="' + (plafondCnss || '') + '" placeholder="—"></td>' +
        '<td><div class="table-actions">' +
        '<button type="button" class="btn-icon btn-edit" title="Modifier l\'indemnité" onclick="editIndemniteRow(this)"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>' +
        '<button type="button" class="btn-icon btn-delete" title="Supprimer l\'indemnité" onclick="this.closest(\'tr\').remove()"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg></button>' +
        '</div></td>';
    document.getElementById('indemnites-custom-container').before(tr);
    document.getElementById('indemniteModal').style.display = 'none';
    document.querySelectorAll('#indemnite_table_body tr').forEach(r => r.classList.remove('selected'));
    indemniteSelected = null;
    document.getElementById('indemnite_info').textContent = '';
    document.getElementById('indemnite_montant_input').value = 0;
    document.getElementById('indemnite_plafond_dgi_input').value = '';
    document.getElementById('indemnite_plafond_cnss_input').value = '';
}

function deleteIndemniteRow(btn, fieldName) {
    var tr = btn.closest('tr');
    tr.style.opacity = '0.35';
    var input = tr.querySelector('input[name="' + fieldName + '"]');
    if (input) input.value = 0;
    btn.remove();
}

function editIndemniteRow(btn) {
    var tr = btn.closest('tr');
    var inputs = tr.querySelectorAll('.form-control-inline');
    inputs.forEach(function(inp) {
        inp.readOnly = !inp.readOnly;
        inp.style.borderColor = inp.readOnly ? '' : 'var(--accent)';
    });
}

document.addEventListener('DOMContentLoaded', function() {
    var sbInput = document.querySelector('input[name="salaire_base"]');
    var reprInput = document.querySelector('input[name="indemnite_representation"]');
    if (sbInput && reprInput) {
        sbInput.addEventListener('input', function() {
            var sb = parseFloat(sbInput.value) || 0;
            reprInput.value = (sb * 0.10).toFixed(2);
        });
        var sb = parseFloat(sbInput.value) || 0;
        if (sb > 0) reprInput.value = (sb * 0.10).toFixed(2);
    }
});

let gainSelected = null;

function filterGains() {
    const q = document.getElementById('gain_search').value.toLowerCase();
    document.querySelectorAll('#gain_table_body .gain-row').forEach(r => {
        const code = r.dataset.code.toLowerCase();
        const libelle = r.dataset.libelle.toLowerCase();
        r.style.display = (code.includes(q) || libelle.includes(q)) ? '' : 'none';
    });
}

function selectGainRow(tr) {
    document.querySelectorAll('#gain_table_body .gain-row').forEach(r => r.classList.remove('selected'));
    tr.classList.add('selected');
    gainSelected = {
        id: tr.dataset.id,
        code: tr.dataset.code,
        libelle: tr.dataset.libelle,
        type: tr.dataset.type,
    };
    document.getElementById('gain_info').textContent = 'Sélection : ' + gainSelected.code + ' — ' + gainSelected.libelle;
    document.getElementById('gain_type_display').value = gainSelected.type || 'Fixe';
}

function ajouterGainDepuisModal() {
    if (!gainSelected) { alert('Veuillez sélectionner un gain dans la liste.'); return; }
    var montant = parseFloat(document.getElementById('gain_montant_input').value) || 0;
    if (montant <= 0) { alert('Le montant doit être supérieur à 0.'); return; }
    var tr = document.createElement('tr');
    tr.className = 'gain-custom-row';
    tr.innerHTML =
        '<td class="code">' + gainSelected.code + '</td>' +
        '<td><input type="hidden" name="gain_custom_rubrique_id[]" value="' + gainSelected.id + '">' + gainSelected.libelle + '</td>' +
        '<td style="font-size:0.72rem;color:var(--text-muted);">' + (gainSelected.type || 'Fixe') + '</td>' +
        '<td><input type="number" step="0.01" min="0" name="gain_custom_montant[]" class="form-control-inline" value="' + montant.toFixed(2) + '"></td>' +
        '<td><div class="table-actions">' +
        '<button type="button" class="btn-icon btn-delete" title="Supprimer le gain" onclick="this.closest(\'tr\').remove()"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg></button>' +
        '</div></td>';
    document.getElementById('gains-custom-container').before(tr);
    document.getElementById('gainModal').style.display = 'none';
    document.querySelectorAll('#gain_table_body .gain-row').forEach(r => r.classList.remove('selected'));
    gainSelected = null;
    document.getElementById('gain_info').textContent = '';
    document.getElementById('gain_montant_input').value = 0;
    document.getElementById('gain_type_display').value = '';
}
</script>