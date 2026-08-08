<?php
// Modale « Sortir » un salarié — incluse dans les listes de salariés actifs.
// La route POST cible /paie-me/salaries/{id}/sortir ; l'id est injecté par JS.
?>
<div class="modal fade" id="sortieModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background:var(--bg-surface); color:var(--text); border:1px solid var(--border); border-radius:12px;">
            <form method="POST" id="sortieForm">
                <?= \Core\Session::csrfField() ?>
                <div class="modal-header" style="border-bottom:1px solid var(--border);">
                    <h5 class="modal-title">Sortie du salarié</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p style="color:var(--text-muted); font-size:0.875rem; margin-bottom:0.75rem;">
                        Marquer <strong id="sortieNomSalarie">—</strong> comme sortant de la société.
                        Il restera consultable dans la section <em>Salariés sortants</em>.
                    </p>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.75rem;">
                        <div class="form-group">
                            <label>Date de sortie <span style="color:#ef4444;">*</span></label>
                            <input type="date" name="date_sortie" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Motif de sortie</label>
                            <select name="motif_sortie" class="form-control">
                                <option value="">—</option>
                                <option value="Démission">Démission</option>
                                <option value="Licenciement">Licenciement</option>
                                <option value="Fin de CDD">Fin de CDD</option>
                                <option value="Fin de stage">Fin de stage</option>
                                <option value="Retraite">Retraite</option>
                                <option value="Rupture conventionnelle">Rupture conventionnelle</option>
                                <option value="Décès">Décès</option>
                                <option value="Autre">Autre</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--border);">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger btn-sm">Confirmer la sortie</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
function openSortieModal(id, nom) {
    document.getElementById('sortieForm').action = '/paie-me/salaries/' + id + '/sortir';
    document.getElementById('sortieNomSalarie').textContent = nom;
    new bootstrap.Modal(document.getElementById('sortieModal')).show();
}
</script>
