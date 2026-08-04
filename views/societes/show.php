<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Salariés actifs</div>
        <div class="stat-value"><?= $stats['nb_salaries'] ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Périodes traitées</div>
        <div class="stat-value"><?= $stats['nb_periodes'] ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Bulletins générés</div>
        <div class="stat-value"><?= $stats['nb_paies'] ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Masse salariale nette</div>
        <div class="stat-value"><?= number_format($stats['masse_salariale'], 2, ',', ' ') ?> DH</div>
    </div>
</div>

<?php if ($stats['derniere_periode']): ?>
    <?php
        $dp = $stats['derniere_periode'];
        $periodeLabel = str_pad($dp['mois'], 2, '0', STR_PAD_LEFT) . '/' . $dp['annee'];
    ?>
    <div class="card">
        <div class="card-header">
            <h3>Dernière période traitée</h3>
            <span class="badge badge-<?= $dp['cloturee'] ? 'success' : 'warning' ?>"><?= $dp['cloturee'] ? 'Clôturée' : 'En cours' ?></span>
        </div>
        <p style="color:var(--text-muted); margin:0;">
            Période de paie <strong style="color:var(--text);"><?= $periodeLabel ?></strong> — consultez les
            <a href="/paie-me/societes/<?= (int) $societe['id'] ?>/paies" style="color:var(--accent);">bulletins et paies</a>.
        </p>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header"><h3>Informations société</h3></div>

    <div style="display:flex; align-items:center; gap:1rem; margin-bottom:1.25rem;">
        <div style="width:52px; height:52px; min-width:52px; border-radius:12px; background:var(--accent-60); color:var(--accent); display:flex; align-items:center; justify-content:center; font-weight:800; font-size:1.4rem; border:1px solid var(--accent-70);">
            <?= htmlspecialchars(mb_strtoupper(mb_substr(trim($societe['raison_sociale']), 0, 1))) ?>
        </div>
        <div>
            <div style="font-size:1.25rem; font-weight:800;"><?= htmlspecialchars($societe['raison_sociale']) ?></div>
            <div style="color:var(--text-muted); font-size:0.85rem;"><?= htmlspecialchars($societe['forme_juridique']) ?></div>
        </div>
    </div>

    <h4 class="form-section-title">Identité</h4>
    <hr class="form-section-sep">
    <div class="info-grid">
        <div class="info-item">
            <div class="info-label">Raison sociale</div>
            <div class="info-value"><?= htmlspecialchars($societe['raison_sociale']) ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Forme juridique</div>
            <div class="info-value"><?= htmlspecialchars($societe['forme_juridique']) ?></div>
        </div>
    </div>

    <h4 class="form-section-title">Immatriculations</h4>
    <hr class="form-section-sep">
    <div class="info-grid">
        <div class="info-item">
            <div class="info-label">ICE</div>
            <div class="info-value"><?= htmlspecialchars($societe['ice']) ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">IF</div>
            <div class="info-value"><?= htmlspecialchars($societe['if_fiscal']) ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">RC</div>
            <div class="info-value"><?= htmlspecialchars($societe['rc']) ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">TP</div>
            <div class="info-value"><?= htmlspecialchars($societe['tp']) ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">CNSS</div>
            <div class="info-value"><?= htmlspecialchars($societe['cnss']) ?></div>
        </div>
    </div>

    <h4 class="form-section-title">Coordonnées</h4>
    <hr class="form-section-sep">
    <div class="info-grid">
        <div class="info-item">
            <div class="info-label">Adresse</div>
            <div class="info-value"><?= htmlspecialchars($societe['adresse'] ?? '') ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Ville</div>
            <div class="info-value"><?= htmlspecialchars($societe['ville']) ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Téléphone</div>
            <div class="info-value">
                <?php if (!empty($societe['telephone'])): ?>
                    <a href="tel:<?= htmlspecialchars(preg_replace('/[^0-9+]/', '', $societe['telephone'])) ?>"><?= htmlspecialchars($societe['telephone']) ?></a>
                <?php else: ?>
                    <span class="badge badge-dark" style="font-size:0.65rem;">Non renseigné</span>
                <?php endif; ?>
            </div>
        </div>
        <div class="info-item">
            <div class="info-label">Email</div>
            <div class="info-value">
                <?php if (!empty($societe['email'])): ?>
                    <a href="mailto:<?= htmlspecialchars($societe['email']) ?>"><?= htmlspecialchars($societe['email']) ?></a>
                <?php else: ?>
                    <span class="badge badge-dark" style="font-size:0.65rem;">Non renseigné</span>
                <?php endif; ?>
            </div>
        </div>
        <div class="info-item">
            <div class="info-label">Site web</div>
            <div class="info-value">
                <?php if (!empty($societe['site_web'])): ?>
                    <?php $site = $societe['site_web']; $href = (strpos($site, '://') === false ? 'https://' : '') . $site; ?>
                    <a href="<?= htmlspecialchars($href) ?>" target="_blank" rel="noopener"><?= htmlspecialchars($site) ?></a>
                <?php else: ?>
                    <span class="badge badge-dark" style="font-size:0.65rem;">Non renseigné</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <h4 class="form-section-title">Coordonnées bancaires</h4>
    <hr class="form-section-sep">
    <div class="info-grid">
        <div class="info-item">
            <div class="info-label">Banque</div>
            <div class="info-value"><?= htmlspecialchars($societe['banque'] ?? '') ?: '<span class="badge badge-dark" style="font-size:0.65rem;">Non renseigné</span>' ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">RIB</div>
            <div class="info-value"><?= htmlspecialchars($societe['rib'] ?? '') ?: '<span class="badge badge-dark" style="font-size:0.65rem;">Non renseigné</span>' ?></div>
        </div>
    </div>

    <h4 class="form-section-title">Téléservices</h4>
    <hr class="form-section-sep">
    <div class="info-grid">
        <div class="info-item">
            <div class="info-label">Damancom</div>
            <div class="info-value">
                <?php if (!empty($societe['damancom_login'])): ?>
                    <?= htmlspecialchars($societe['damancom_login']) ?>
                <?php else: ?>
                    <span class="badge badge-dark" style="font-size:0.65rem;">Non configuré</span>
                <?php endif; ?>
            </div>
        </div>
        <div class="info-item">
            <div class="info-label">SIMPL</div>
            <div class="info-value">
                <?php if (!empty($societe['simpl_login'])): ?>
                    <?= htmlspecialchars($societe['simpl_login']) ?>
                <?php else: ?>
                    <span class="badge badge-dark" style="font-size:0.65rem;">Non configuré</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
