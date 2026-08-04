<?php
// Rapport de validation d'import (méthode Odoo : Test avant Import).
$baseUrl = '/paie-me';
$ctxName = $ctx['raison_sociale'] ?? null;

// Liste plate des erreurs (ligne × colonne).
$errorsFlat = [];
foreach ($report['rows'] as $row) {
    foreach ($row['_errors'] as $err) {
        $errorsFlat[] = ['line' => $row['_line'], 'label' => $err['label'], 'value' => $err['value'], 'message' => $err['message']];
    }
}
$nbImportables = $report['valid'];
?>
<div class="card">
    <div class="card-header">
        <h3>Vérification de l'import</h3>
        <div style="display:flex; align-items:center; gap:0.5rem;">
            <a href="<?= $baseUrl ?>/salaries/import/modele" class="btn btn-secondary btn-sm">Télécharger le modèle</a>
            <a href="<?= $baseUrl ?>/salaries" class="btn btn-secondary btn-sm">Retour</a>
        </div>
    </div>

    <p style="font-size:0.8125rem; color:var(--text-muted); margin-bottom:1rem;">
        Fichier : <strong><?= htmlspecialchars($file) ?></strong>
        <?php if ($ctxName): ?> — Import dans la société <strong><?= htmlspecialchars($ctxName) ?></strong><?php endif; ?>
    </p>

    <div class="stats-grid" style="margin-bottom:1.25rem;">
        <div class="stat-card">
            <div class="stat-label">Lignes détectées</div>
            <div class="stat-value"><?= (int) $report['total'] ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label" style="color:var(--success);">Lignes valides</div>
            <div class="stat-value" style="color:var(--success);"><?= $report['valid'] ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label" style="color:var(--danger);">Erreurs</div>
            <div class="stat-value" style="color:var(--danger);"><?= $report['errors'] ?></div>
        </div>
    </div>

    <?php if (!empty($report['missing'])): ?>
        <div class="alert alert-danger">
            <strong>Colonnes requises absentes du fichier :</strong> <?= implode(', ', $report['missing']) ?>.
            Téléchargez le modèle d'import pour retrouver les en-têtes exacts.
        </div>
    <?php endif; ?>

    <?php if (!empty($report['unknown'])): ?>
        <div class="alert alert-warning">
            <strong>Colonnes ignorées (inconnues) :</strong> <?= implode(', ', array_map('htmlspecialchars', $report['unknown'])) ?>.
        </div>
    <?php endif; ?>

    <?php if ($report['total'] === 0): ?>
        <div class="alert alert-warning">Le fichier ne contient aucune ligne de données à importer.</div>
    <?php endif; ?>

    <?php if (!empty($errorsFlat)): ?>
        <div class="alert alert-danger">
            <strong><?= count($errorsFlat) ?> erreur(s)</strong> : aucune donnée ne sera importée tant que le fichier n'est pas corrigé.
            Modifiez le fichier puis importez-le à nouveau.
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr><th>Ligne</th><th>Champ</th><th>Valeur</th><th>Erreur</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($errorsFlat as $e): ?>
                    <tr>
                        <td><?= (int) $e['line'] ?></td>
                        <td><strong><?= htmlspecialchars($e['label']) ?></strong></td>
                        <td style="color:var(--text-muted);"><?= $e['value'] === '' ? '—' : htmlspecialchars((string) $e['value']) ?></td>
                        <td><?= htmlspecialchars($e['message']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-success">
            <strong>Fichier valide.</strong> <?= $nbImportables ?> salarié(s) prêt(s) à être importé(s).
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr><th>Ligne</th><th>Matricule</th><th>Nom</th><th>Prénom</th><th>Société</th><th>Service</th><th>Fonction</th><th>Salaire de base</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($report['rows'] as $row): ?>
                    <tr>
                        <td><?= (int) $row['_line'] ?></td>
                        <td><?= htmlspecialchars((string) $row['matricule']) ?></td>
                        <td><?= htmlspecialchars((string) $row['nom_famille']) ?></td>
                        <td><?= htmlspecialchars((string) $row['prenom']) ?></td>
                        <td><?= htmlspecialchars($row['societe_id'] ? ($report['societesById'][$row['societe_id']] ?? '') : '') ?></td>
                        <td><?= htmlspecialchars($row['service_nom'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['fonction_nom'] ?? '') ?></td>
                        <td><?= number_format((float) ($row['salaire_base'] ?? 0), 2, ',', ' ') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <form method="post" action="<?= $baseUrl ?>/salaries/import" style="margin-top:1rem; display:flex; gap:0.5rem; align-items:center;">
            <?= \Core\Session::csrfField() ?>
            <button type="submit" class="btn btn-primary btn-sm">Importer <?= $nbImportables ?> salarié(s)</button>
            <a href="<?= $baseUrl ?>/salaries" class="btn btn-secondary btn-sm">Annuler</a>
        </form>
    <?php endif; ?>
</div>
