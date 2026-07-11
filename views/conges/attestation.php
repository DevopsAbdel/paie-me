<div class="card">
    <div class="card-header">
        <h3>Attestation de congé</h3>
    </div>

    <div style="max-width:700px;">
        <div class="form-group">
            <label>Sélectionner un congé</label>
            <select id="congeSelect" class="form-control" data-attestation>
                <option value="">— Choisir un congé —</option>
                <?php foreach ($conges as $c): ?>
                    <option value="<?= (int)$c['id'] ?>" <?= ($conge && $conge['id'] == $c['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['matricule'] . ' — ' . $c['nom_complet'] . ' — ' . date('d/m/Y', strtotime($c['date_debut'])) . ' au ' . date('d/m/Y', strtotime($c['date_fin']))) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</div>

<?php if ($conge): ?>
<?php
$types = ['paye' => 'Congé payé', 'sans_solde' => 'Sans solde', 'maladie' => 'Maladie', 'maternite' => 'Maternité', 'exceptionnel' => 'Exceptionnel', 'autre' => 'Autre'];
$statuts = ['en_attente' => 'En attente', 'valide' => 'Validé', 'refuse' => 'Refusé', 'annule' => 'Annulé'];
$typeLabel = $types[$conge['type_conge']] ?? $conge['type_conge'];
$statutLabel = $statuts[$conge['statut']] ?? $conge['statut'];
?>

<div style="margin-top:0.75rem; display:flex; justify-content:flex-end; gap:0.5rem;">
    <a href="<?= $baseUrl ?>/attestation/pdf/<?= (int)$conge['id'] ?>" class="btn btn-primary btn-sm" target="_blank">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
        Télécharger PDF
    </a>
    <button type="button" class="btn btn-secondary btn-sm" onclick="window.print()">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
        Imprimer
    </button>
</div>

<div class="a4-preview" id="attestationA4">
    <div class="a4-page">

        <div class="a4-header">
            <div class="a4-company-logo">TE</div>
            <div class="a4-company-name"><?= htmlspecialchars($societe['raison_sociale']) ?></div>
            <div class="a4-company-sub">ICE : <?= htmlspecialchars($societe['ice'] ?? '') ?></div>
        </div>

        <h2 class="a4-title">ATTESTATION DE CONGÉ</h2>

        <div class="a4-info-grid">
            <table class="a4-info-table">
                <tr><td class="a4-label">Matricule</td><td><?= htmlspecialchars($conge['matricule']) ?></td></tr>
                <tr><td class="a4-label">Nom complet</td><td><?= htmlspecialchars($conge['nom_complet']) ?></td></tr>
                <tr><td class="a4-label">Poste</td><td><?= htmlspecialchars($conge['poste'] ?? '—') ?></td></tr>
                <tr><td class="a4-label">Type de congé</td><td><?= $typeLabel ?></td></tr>
                <tr><td class="a4-label">Date début</td><td><?= date('d/m/Y', strtotime($conge['date_debut'])) ?></td></tr>
                <tr><td class="a4-label">Date fin</td><td><?= date('d/m/Y', strtotime($conge['date_fin'])) ?></td></tr>
                <tr><td class="a4-label">Nombre de jours</td><td><?= number_format($conge['nb_jours'], 1, ',', '') ?> jour(s)</td></tr>
                <tr><td class="a4-label">Statut</td><td><?= $statutLabel ?></td></tr>
            </table>
        </div>

        <?php if ($conge['observation']): ?>
        <div class="a4-observation">
            <strong>Observation :</strong> <?= nl2br(htmlspecialchars($conge['observation'])) ?>
        </div>
        <?php endif; ?>

        <div class="a4-body">
            La société <strong><?= htmlspecialchars($societe['raison_sociale']) ?></strong> atteste que le/la salarié(e)
            <strong><?= htmlspecialchars($conge['nom_complet']) ?></strong> (matricule <?= htmlspecialchars($conge['matricule']) ?>)
            bénéficie d'un congé du <strong><?= date('d/m/Y', strtotime($conge['date_debut'])) ?></strong>
            au <strong><?= date('d/m/Y', strtotime($conge['date_fin'])) ?></strong>,
            soit <strong><?= number_format($conge['nb_jours'], 1, ',', '') ?> jour(s)</strong> de <?= strtolower($typeLabel) ?>.
        </div>

        <div class="a4-footer">
            <div>Fait le <?= date('d/m/Y') ?></div>
            <div>Le responsable</div>
        </div>

        <div class="a4-signature">
            <div class="a4-sig-line"></div>
            <div class="a4-sig-label">Signature & cachet</div>
        </div>
    </div>
</div>

<style>
.a4-preview {
    display: flex;
    justify-content: center;
    padding: 1.5rem 1rem;
}
.a4-page {
    width: 210mm;
    min-height: 297mm;
    max-width: 100%;
    background: #fff;
    color: #1a1a1a;
    padding: 15mm 20mm;
    box-shadow: 0 4px 24px rgba(0,0,0,0.3);
    border-radius: 2px;
    font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;
    font-size: 10pt;
    line-height: 1.5;
    position: relative;
}
.a4-header {
    text-align: center;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #1e293b;
}
.a4-company-logo {
    display: inline-block;
    width: 48px;
    height: 48px;
    background: #1e293b;
    color: #fff;
    font-weight: 800;
    font-size: 1.1rem;
    border-radius: 10px;
    line-height: 48px;
    margin-bottom: 0.3rem;
}
.a4-company-name {
    font-size: 14pt;
    font-weight: 700;
    color: #1e293b;
}
.a4-company-sub {
    font-size: 8pt;
    color: #666;
}
.a4-title {
    text-align: center;
    font-size: 16pt;
    font-weight: 800;
    color: #1e293b;
    letter-spacing: 0.04em;
    margin: 2rem 0 1.5rem;
    text-transform: uppercase;
}
.a4-info-grid {
    margin-bottom: 0.75rem;
}
.a4-info-table {
    width: 100%;
    border-collapse: collapse;
}
.a4-info-table td {
    padding: 0.2rem 0.5rem;
    border-bottom: 1px solid #e2e8f0;
    font-size: 9.5pt;
    vertical-align: top;
}
.a4-label {
    font-weight: 700;
    color: #475569;
    width: 30%;
    white-space: nowrap;
}
.a4-observation {
    background: #f8fafc;
    border-left: 3px solid #3b82f6;
    padding: 0.3rem 0.5rem;
    margin-bottom: 0.75rem;
    font-size: 9pt;
    border-radius: 0 4px 4px 0;
}
.a4-body {
    margin-bottom: 1.5rem;
    text-align: justify;
    font-size: 10pt;
    line-height: 1.7;
}
.a4-footer {
    display: flex;
    justify-content: space-between;
    font-size: 9pt;
    color: #666;
    margin-bottom: 1.5rem;
}
.a4-signature {
    position: absolute;
    bottom: 20mm;
    right: 20mm;
    text-align: center;
    width: 140px;
}
.a4-sig-line {
    border-bottom: 1px solid #1a1a1a;
    margin-bottom: 0.2rem;
    height: 50px;
}
.a4-sig-label {
    font-size: 8pt;
    color: #666;
}
@media print {
    body * { visibility: hidden; }
    .a4-preview, .a4-preview * { visibility: visible; }
    .a4-preview {
        position: absolute;
        left: 0;
        top: 0;
        padding: 0;
    }
    .a4-page {
        box-shadow: none;
        margin: 0;
        padding: 15mm 20mm;
        width: 210mm;
        min-height: auto;
    }
}
</style>
<?php endif; ?>

<script>
(function() {
    var sel = document.querySelector('[data-attestation]');
    if (!sel) return;

    function goTo() {
        var id = sel.value;
        if (id) window.location.href = '<?= $baseUrl ?>/attestation?conge_id=' + id;
    }

    sel.addEventListener('change', goTo);

    document.addEventListener('click', function(e) {
        var wrapper = sel.closest('.cs-wrapper');
        if (wrapper && wrapper.contains(e.target)) {
            setTimeout(goTo, 50);
        }
    });
})();
</script>
