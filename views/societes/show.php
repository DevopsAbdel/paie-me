<?php $moisFr = ['', 'Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc']; ?>

<div class="stats-grid compact">
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
        <div class="stat-label">Masse brute</div>
        <div class="stat-value stat-strong"><?= number_format($stats['masse_brute'], 0, ',', ' ') ?> DH</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Masse salariale nette</div>
        <div class="stat-value"><?= number_format($stats['masse_salariale'], 0, ',', ' ') ?> DH</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">CNSS salariale</div>
        <div class="stat-value stat-strong"><?= number_format($stats['total_cnss'], 0, ',', ' ') ?> DH</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">IR prélevé</div>
        <div class="stat-value stat-strong"><?= number_format($stats['total_ir'], 0, ',', ' ') ?> DH</div>
    </div>
    <?php if ($stats['nb_sortants'] > 0): ?>
    <div class="stat-card">
        <div class="stat-label">Salariés sortants</div>
        <div class="stat-value" style="color:var(--danger);"><?= $stats['nb_sortants'] ?></div>
    </div>
    <?php endif; ?>
</div>

<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-header">
        <h3>Accès rapide</h3>
    </div>
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(150px, 1fr)); gap:0.65rem; padding:1rem 1.25rem;">
        <a href="/paie-me/societes/<?= (int) $societe['id'] ?>/salaries" class="btn btn-secondary btn-sm" style="justify-content:center;">Salariés</a>
        <a href="/paie-me/societes/<?= (int) $societe['id'] ?>/conges" class="btn btn-secondary btn-sm" style="justify-content:center;">Congés</a>
        <a href="/paie-me/societes/<?= (int) $societe['id'] ?>/paies" class="btn btn-secondary btn-sm" style="justify-content:center;">Paies</a>
        <a href="/paie-me/societes/<?= (int) $societe['id'] ?>/bulletins" class="btn btn-secondary btn-sm" style="justify-content:center;">Bulletins</a>
        <a href="/paie-me/societes/<?= (int) $societe['id'] ?>/cnss" class="btn btn-secondary btn-sm" style="justify-content:center;">CNSS / DS</a>
        <a href="/paie-me/societes/<?= (int) $societe['id'] ?>/ir" class="btn btn-secondary btn-sm" style="justify-content:center;">IR / SIMPL</a>
        <a href="/paie-me/societes/<?= (int) $societe['id'] ?>/baremes" class="btn btn-secondary btn-sm" style="justify-content:center;">Barèmes</a>
        <a href="/paie-me/societes/<?= (int) $societe['id'] ?>/parametres" class="btn btn-secondary btn-sm" style="justify-content:center;">Paramètres</a>
    </div>
</div>

<?php if ($stats['derniere_periode']): ?>
    <?php
        $dp = $stats['derniere_periode'];
        $periodeLabel = str_pad($dp['mois'], 2, '0', STR_PAD_LEFT) . '/' . $dp['annee'];
    ?>
    <div class="card" style="margin-bottom:1.5rem;">
        <div class="card-header">
            <h3>Dernière période traitée</h3>
            <span class="badge badge-<?= $dp['cloturee'] ? 'success' : 'warning' ?>"><?= $dp['cloturee'] ? 'Clôturée' : 'En cours' ?></span>
        </div>
        <div style="padding:0.25rem 1.25rem 1.25rem; display:flex; gap:1.75rem; flex-wrap:wrap;">
            <div>
                <div style="font-size:0.72rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--text-muted); font-weight:600;">Période</div>
                <div style="font-size:1rem; font-weight:700; margin-top:0.15rem;"><?= $periodeLabel ?></div>
            </div>
            <div>
                <div style="font-size:0.72rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--text-muted); font-weight:600;">Bulletins</div>
                <div style="font-size:1rem; font-weight:700; margin-top:0.15rem;"><?= (int) $dp['nb_paies'] ?></div>
            </div>
            <div>
                <div style="font-size:0.72rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--text-muted); font-weight:600;">Net payé</div>
                <div style="font-size:1rem; font-weight:700; margin-top:0.15rem;"><?= number_format((float) $dp['total_net'], 0, ',', ' ') ?> DH</div>
            </div>
            <div style="margin-left:auto; display:flex; align-items:flex-end; gap:0.5rem;">
                <a href="/paie-me/societes/<?= (int) $societe['id'] ?>/paies" class="btn btn-primary btn-sm">Consulter les bulletins</a>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($stats['monthly_net'])): ?>
<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-header">
        <h3>Évolution du net à payer — 6 derniers mois</h3>
    </div>
    <div style="padding:1.25rem;">
        <?php
        $maxNet = 0;
        foreach ($stats['monthly_net'] as $m) { if ((float)$m['total_net'] > $maxNet) $maxNet = (float)$m['total_net']; }
        $barMax = $maxNet > 0 ? $maxNet : 1;
        ?>
        <div style="display:flex; align-items:flex-end; gap:0.75rem; height:180px;">
            <?php foreach ($stats['monthly_net'] as $m):
                $pct = round((float)$m['total_net'] / $barMax * 100, 1);
                $label = $moisFr[(int)$m['mois']] . ' ' . $m['annee'];
            ?>
            <div style="flex:1; display:flex; flex-direction:column; align-items:center; gap:0.4rem; min-width:0;">
                <div style="font-size:0.72rem; color:var(--text-muted); font-weight:600; white-space:nowrap;"><?= number_format((float)$m['total_net'], 0, ',', ' ') ?></div>
                <div style="width:100%; max-width:52px; background:rgba(139,92,246,0.12); border:1px solid rgba(139,92,246,0.25); border-radius:6px 6px 0 0; display:flex; align-items:flex-end; height:120px;">
                    <div style="width:100%; background:var(--accent); border-radius:6px 6px 0 0; height:<?= max(4, $pct) ?>%; transition:height 0.4s;" title="<?= $label ?> : <?= number_format((float)$m['total_net'], 2, ',', ' ') ?> DH"></div>
                </div>
                <div style="font-size:0.72rem; color:var(--text-muted); white-space:nowrap;"><?= $label ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header"><h3>Informations société</h3></div>

    <div style="display:flex; align-items:center; gap:1rem; margin-bottom:1.25rem;">
        <?php if (!empty($societe['logo'])): ?>
        <img src="/paie-me/<?= htmlspecialchars($societe['logo']) ?>" alt="Logo" style="width:52px; height:52px; min-width:52px; border-radius:12px; object-fit:contain; border:1px solid var(--border); background:var(--bg-surface); padding:3px;">
        <?php else: ?>
        <div style="width:52px; height:52px; min-width:52px; border-radius:12px; background:var(--accent-60); color:var(--accent); display:flex; align-items:center; justify-content:center; font-weight:800; font-size:1.4rem; border:1px solid var(--accent-70);">
            <?= htmlspecialchars(mb_strtoupper(mb_substr(trim($societe['raison_sociale']), 0, 1))) ?>
        </div>
        <?php endif; ?>
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
