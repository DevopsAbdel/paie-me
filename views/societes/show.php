<div class="card">
    <div class="card-header"><h3>Informations société</h3></div>

    <h4 class="form-section-title">Identité</h4>
    <hr class="form-section-sep">
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
        <div><strong>Raison sociale :</strong> <?= htmlspecialchars($societe['raison_sociale']) ?></div>
        <div><strong>Forme juridique :</strong> <?= $societe['forme_juridique'] ?></div>
    </div>

    <h4 class="form-section-title">Immatriculations</h4>
    <hr class="form-section-sep">
    <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:1rem;">
        <div><strong>ICE :</strong> <?= htmlspecialchars($societe['ice']) ?></div>
        <div><strong>IF :</strong> <?= htmlspecialchars($societe['if_fiscal']) ?></div>
        <div><strong>RC :</strong> <?= htmlspecialchars($societe['rc']) ?></div>
        <div><strong>TP :</strong> <?= htmlspecialchars($societe['tp']) ?></div>
        <div><strong>CNSS :</strong> <?= htmlspecialchars($societe['cnss']) ?></div>
    </div>

    <h4 class="form-section-title">Coordonnées</h4>
    <hr class="form-section-sep">
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
        <div><strong>Adresse :</strong> <?= htmlspecialchars($societe['adresse'] ?? '') ?></div>
        <div><strong>Ville :</strong> <?= htmlspecialchars($societe['ville']) ?></div>
        <div><strong>Téléphone :</strong> <?= htmlspecialchars($societe['telephone'] ?? '') ?></div>
        <div><strong>Email :</strong> <?= htmlspecialchars($societe['email'] ?? '') ?></div>
        <div><strong>Site web :</strong> <?= htmlspecialchars($societe['site_web'] ?? '') ?></div>
    </div>

    <h4 class="form-section-title">Coordonnées bancaires</h4>
    <hr class="form-section-sep">
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
        <div><strong>Banque :</strong> <?= htmlspecialchars($societe['banque'] ?? '') ?></div>
        <div><strong>RIB :</strong> <?= htmlspecialchars($societe['rib'] ?? '') ?></div>
    </div>

    <h4 class="form-section-title">Téléservices</h4>
    <hr class="form-section-sep">
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
        <div><strong>Damancom :</strong> <?= htmlspecialchars($societe['damancom_login'] ?? 'Non configuré') ?></div>
        <div><strong>SIMPL :</strong> <?= htmlspecialchars($societe['simpl_login'] ?? 'Non configuré') ?></div>
    </div>
</div>
