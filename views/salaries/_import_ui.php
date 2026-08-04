<?php
// Boutons Import/Export (méthode Odoo) + modale d'import.
// Utilisé par : views/salaries/index.php et views/societes/salaries_list.php
$importBase = '/paie-me/salaries';
?>
<div style="display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap;">
    <a href="<?= $importBase ?>/import/modele" class="btn btn-secondary btn-sm" title="Télécharger le modèle d'import (XLSX)">
        Modèle d'import
    </a>
    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#importSalariesModal">
        Importer
    </button>
    <a href="<?= $importBase ?>/export" class="btn btn-secondary btn-sm" title="Exporter les salariés en Excel (XLSX)">
        Exporter
    </a>
</div>

<!-- Modale d'import -->
<div class="modal fade" id="importSalariesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background:var(--bg-surface); color:var(--text); border:1px solid var(--border); border-radius:12px;">
            <form method="post" action="<?= $importBase ?>/import/preview" enctype="multipart/form-data">
                <?= \Core\Session::csrfField() ?>
                <div class="modal-header" style="border-bottom:1px solid var(--border);">
                    <h5 class="modal-title">Importer des salariés</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group" style="margin-bottom:0.75rem;">
                        <label>Fichier Excel ou CSV</label>
                        <input type="file" name="fichier" class="form-control" accept=".xlsx,.xls,.csv" required>
                        <small style="color:var(--text-muted); font-size:0.7rem; margin-top:0.25rem; display:block;">
                            Formats acceptés : XLSX, XLS, CSV — maximum 10 Mo.
                        </small>
                    </div>
                    <p style="font-size:0.8rem; color:var(--text-muted); margin:0;">
                        Téléchargez d'abord le <a href="<?= $importBase ?>/import/modele" style="color:var(--accent);">modèle d'import</a>,
                        remplissez-le puis importez. Les en-têtes de colonnes ne doivent pas être modifiés.
                    </p>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--border);">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary btn-sm">Vérifier le fichier</button>
                </div>
            </form>
        </div>
    </div>
</div>
