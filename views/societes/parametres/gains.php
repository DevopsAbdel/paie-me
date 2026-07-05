<div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; gap:1rem;">
        <h3 style="margin:0;">Rubriques de gains</h3>
        <button type="button" class="btn btn-primary btn-sm" onclick="openModal(null)">
            + Ajouter
        </button>
    </div>

    <div style="overflow-x:auto;">
        <table class="table-gains" style="min-width:1200px;">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Libellé</th>
                    <th>Type</th>
                    <th>Valeur</th>
                    <th>Impos. IR</th>
                    <th>Impos. CNSS</th>
                    <th>Actif</th>
                    <th>Base Anc.</th>
                    <th>Prorata</th>
                    <th>Plafond DGI</th>
                    <th>Plafond CNSS</th>
                    <th>Desc. DGI</th>
                    <th>Desc. CNSS</th>
                    <th>Compte</th>
                    <th>Justificatifs</th>
                    <th>Source</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($gains)): ?>
                <tr><td colspan="17" style="text-align:center; color:var(--text-muted);">Aucune rubrique de gain</td></tr>
                <?php else: ?>
                <?php foreach ($gains as $g): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($g['code']) ?></strong></td>
                    <td><?= htmlspecialchars($g['libelle']) ?></td>
                    <td><span class="badge badge-<?= $g['type_montant'] === 'calcule' ? 'warning' : ($g['type_montant'] === 'proportionnel' ? 'info' : 'secondary') ?>"><?= htmlspecialchars($g['type_montant']) ?></span></td>
                    <td class="text-right"><?= htmlspecialchars($g['valeur_defaut'] ?? '') ?></td>
                    <td><span class="badge badge-<?= $g['imposable_ir'] ? 'warning' : 'secondary' ?>"><?= $g['imposable_ir'] ? 'Oui' : 'Non' ?></span></td>
                    <td><span class="badge badge-<?= $g['imposable_cnss'] ? 'warning' : 'secondary' ?>"><?= $g['imposable_cnss'] ? 'Oui' : 'Non' ?></span></td>
                    <td><span class="badge badge-<?= $g['actif'] ? 'success' : 'secondary' ?>"><?= $g['actif'] ? 'Oui' : 'Non' ?></span></td>
                    <td><span class="badge badge-<?= $g['base_anciennete'] ? 'success' : 'secondary' ?>"><?= $g['base_anciennete'] ? 'Oui' : 'Non' ?></span></td>
                    <td><span class="badge badge-<?= $g['au_prorata'] ? 'success' : 'secondary' ?>"><?= $g['au_prorata'] ? 'Oui' : 'Non' ?></span></td>
                    <td><small><?php if ($g['plafond_dgi_actif']): ?><?= htmlspecialchars($g['plafond_dgi_valeur'] ?? '') ?><?php endif; ?></small></td>
                    <td><small><?php if ($g['plafond_cnss_actif']): ?><?= htmlspecialchars($g['plafond_cnss_valeur'] ?? '') ?><?php endif; ?></small></td>
                    <td><small title="<?= htmlspecialchars($g['plafond_dgi_desc'] ?? '') ?>"><?= htmlspecialchars(mb_substr($g['plafond_dgi_desc'] ?? '', 0, 25)) ?><?= isset($g['plafond_dgi_desc']) && mb_strlen($g['plafond_dgi_desc']) > 25 ? '…' : '' ?></small></td>
                    <td><small title="<?= htmlspecialchars($g['plafond_cnss_desc'] ?? '') ?>"><?= htmlspecialchars(mb_substr($g['plafond_cnss_desc'] ?? '', 0, 25)) ?><?= isset($g['plafond_cnss_desc']) && mb_strlen($g['plafond_cnss_desc']) > 25 ? '…' : '' ?></small></td>
                    <td><code><?= htmlspecialchars($g['compte'] ?? '') ?></code></td>
                    <td><small title="<?= htmlspecialchars($g['justificatifs'] ?? '') ?>"><?= htmlspecialchars(mb_substr($g['justificatifs'] ?? '', 0, 30)) ?><?= isset($g['justificatifs']) && mb_strlen($g['justificatifs']) > 30 ? '…' : '' ?></small></td>
                    <td><small><?= htmlspecialchars($g['source'] ?? '') ?></small></td>
                    <td>
                        <div class="table-actions">
                            <button type="button" class="btn-icon btn-view" title="Voir" onclick="viewGain(<?= (int)$g['id'] ?>)">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                            <button type="button" class="btn-icon btn-edit" title="Modifier" onclick="openModal(<?= (int)$g['id'] ?>)">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </button>
                            <a href="<?= $baseUrl ?>/gains?delete_gain=<?= $g['id'] ?>" class="btn-icon btn-delete" title="Supprimer" onclick="return confirm('Supprimer cette rubrique ?')">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Bootstrap -->
<div class="modal fade" id="gainModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <form id="gainForm" method="post" action="<?= $baseUrl ?>/gains">
                <?= \Core\Session::csrfField() ?>
                <input type="hidden" name="sous_tab" value="gains">
                <input type="hidden" name="gain_id" id="gain_id" value="">
                <input type="hidden" name="format" value="json">

                <div class="modal-header">
                    <h5 class="modal-title" id="gainModalLabel">Nouvelle rubrique de gain</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="form-section-title">Nature du gain</div>
                    <hr class="form-section-sep">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label">Code *</label>
                            <input type="text" name="code" id="f_code" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Libellé *</label>
                            <input type="text" name="libelle" id="f_libelle" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Type</label>
                            <select name="type_montant" id="f_type_montant" class="form-select">
                                <option value="fixe">Fixe</option>
                                <option value="proportionnel">Proportionnel</option>
                                <option value="calcule">Calculé</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Valeur défaut</label>
                            <input type="number" name="valeur_defaut" id="f_valeur_defaut" class="form-control" step="0.01">
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Source</label>
                            <input type="text" name="source" id="f_source" class="form-control" placeholder="Ex: Contrat, Loi">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nature EDI</label>
                            <input type="text" name="nature_edi" id="f_nature_edi" class="form-control" placeholder="Code nature pour export">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Catégorie</label>
                            <select name="categorie" id="f_categorie" class="form-select">
                                <option value="">—</option>
                                <option>Transport & Déplacement</option>
                                <option>Spécifiques à certains emplois</option>
                                <option>Caractère Social & Familial</option>
                                <option>Rupture & Fin de Contrat</option>
                                <option>Gain standard</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Justificatifs</label>
                            <textarea name="justificatifs" id="f_justificatifs" class="form-control" rows="2" placeholder="Pièces justificatives requises" style="resize: vertical;"></textarea>
                        </div>
                    </div>

                    <div class="form-section-title">Base de calcul</div>
                    <hr class="form-section-sep">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="base_anciennete" id="f_base_anciennete" value="1">
                                <label class="form-check-label" for="f_base_anciennete">Base ancienneté</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="au_prorata" id="f_au_prorata" value="1">
                                <label class="form-check-label" for="f_au_prorata">Au prorata</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="imposable_ir" id="f_imposable_ir" value="1" checked>
                                <label class="form-check-label" for="f_imposable_ir">Imposable IR</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="imposable_cnss" id="f_imposable_cnss" value="1" checked>
                                <label class="form-check-label" for="f_imposable_cnss">Imposable CNSS</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-section-title">Plafonds</div>
                    <hr class="form-section-sep">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="p-3" style="background:var(--bg-primary);border-radius:8px;border:1px solid var(--border);">
                                <h6 style="margin:0 0 0.5rem;font-size:0.875rem;color:var(--accent);">DGI</h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="plafond_dgi_actif" id="f_plafond_dgi_actif" value="1">
                                    <label class="form-check-label" for="f_plafond_dgi_actif">Actif</label>
                                </div>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <input type="number" name="plafond_dgi_valeur" id="f_plafond_dgi_valeur" class="form-control" placeholder="Montant" step="0.01">
                                    </div>
                                    <div class="col-6">
                                        <select name="plafond_dgi_type" id="f_plafond_dgi_type" class="form-select">
                                            <option value="mensuel">Mensuel</option>
                                            <option value="annuel">Annuel</option>
                                            <option value="ponctuel">Ponctuel</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3" style="background:var(--bg-primary);border-radius:8px;border:1px solid var(--border);">
                                <h6 style="margin:0 0 0.5rem;font-size:0.875rem;color:var(--accent);">CNSS</h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="plafond_cnss_actif" id="f_plafond_cnss_actif" value="1">
                                    <label class="form-check-label" for="f_plafond_cnss_actif">Actif</label>
                                </div>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <input type="number" name="plafond_cnss_valeur" id="f_plafond_cnss_valeur" class="form-control" placeholder="Montant" step="0.01">
                                    </div>
                                    <div class="col-6">
                                        <select name="plafond_cnss_type" id="f_plafond_cnss_type" class="form-select">
                                            <option value="mensuel">Mensuel</option>
                                            <option value="annuel">Annuel</option>
                                            <option value="ponctuel">Ponctuel</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Limites / Plafond DGI</label>
                            <textarea name="plafond_dgi_desc" id="f_plafond_dgi_desc" class="form-control" rows="2" placeholder="Ex: 500.00 DH / mois"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Limites / Plafond CNSS</label>
                            <textarea name="plafond_cnss_desc" id="f_plafond_cnss_desc" class="form-control" rows="2" placeholder="Ex: 500.00 DH / mois"></textarea>
                        </div>
                    </div>

                    <div class="form-section-title">Comptabilité</div>
                    <hr class="form-section-sep">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Compte comptable</label>
                            <select name="compte" id="f_compte" class="form-select" onchange="toggleCompteInput()">
                                <option value="">—</option>
                                <option value="61711000">61711000 — Salaires fixes</option>
                                <option value="61712000">61712000 — Allocations familiales, primes</option>
                                <option value="61713000">61713000 — Indemnités et avantages</option>
                                <option value="61714000">61714000 — Commissions et courtages</option>
                                <option value="61715000">61715000 — Indemnités de fin de contrat</option>
                                <option value="61716000">61716000 — Avantages en nature</option>
                                <option value="61717000">61717000 — Autres charges de personnel</option>
                                <option value="61740000">61740000 — Charges sociales</option>
                                <option value="autre">Autre…</option>
                            </select>
                            <input type="text" name="compte_custom" id="f_compte_custom" class="form-control" placeholder="Code personnalisé" style="display:none;margin-top:0.35rem;">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="actif" id="f_actif" value="1" checked>
                                <label class="form-check-label" for="f_actif">Rubrique active</label>
                            </div>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_global" id="f_is_global" value="1">
                                <label class="form-check-label" for="f_is_global">Globale (toutes sociétés)</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" id="gainCloseBtn" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-secondary btn-sm" id="gainCloseReadonlyBtn" data-bs-dismiss="modal" style="display:none;">Fermer</button>
                    <button type="submit" class="btn btn-primary btn-sm" id="gainSubmitBtn">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const gainsData = <?= json_encode($gains, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
</script>
<script src="/paie-me/assets/js/gains.js"></script>