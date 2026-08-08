<?php $moisFr = ['', 'Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc']; ?>

<?php if ($ctx): ?>
<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-header" style="display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap;">
        <h3><?= htmlspecialchars($ctx['raison_sociale']) ?> — Vue d'ensemble</h3>
        <span class="badge badge-primary">Société active</span>
    </div>
    <?php if ($latestPeriode): ?>
    <div style="padding:0.25rem 1.25rem 1.25rem; display:flex; gap:1.75rem; flex-wrap:wrap;">
        <div>
            <div style="font-size:0.72rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--text-muted); font-weight:600;">Dernière période</div>
            <div style="font-size:1rem; font-weight:700; margin-top:0.15rem;">
                <?= str_pad($latestPeriode['mois'], 2, '0', STR_PAD_LEFT) ?>/<?= $latestPeriode['annee'] ?>
                <?php if ($latestPeriode['cloturee']): ?>
                    <span class="badge badge-success" style="margin-left:0.35rem;">Clôturée</span>
                <?php else: ?>
                    <span class="badge badge-warning" style="margin-left:0.35rem;">En cours</span>
                <?php endif; ?>
            </div>
        </div>
        <div>
            <div style="font-size:0.72rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--text-muted); font-weight:600;">Bulletins de la période</div>
            <div style="font-size:1rem; font-weight:700; margin-top:0.15rem;"><?= (int)$latestPeriode['nb_paies'] ?></div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<div class="stats-grid compact">
    <div class="stat-card">
        <div class="stat-label">Salariés actifs</div>
        <div class="stat-value"><?= $nbSalaries ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Périodes traitées</div>
        <div class="stat-value"><?= $nbPeriodes ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Sociétés</div>
        <div class="stat-value"><?= $nbSocietes ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Masse salariale brute</div>
        <div class="stat-value stat-strong"><?= number_format($totalBrut, 0, ',', ' ') ?> DH</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total net payé</div>
        <div class="stat-value"><?= number_format($totalNet, 0, ',', ' ') ?> DH</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">CNSS salariale</div>
        <div class="stat-value stat-strong"><?= number_format($totalCnss, 0, ',', ' ') ?> DH</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">IR prélevé</div>
        <div class="stat-value stat-strong"><?= number_format($totalIr, 0, ',', ' ') ?> DH</div>
    </div>
    <div class="stat-card" style="justify-content:center; background:rgba(139,92,246,0.08); border-color:rgba(139,92,246,0.35);">
        <a href="<?= $ctx ? '/paie-me/societes/' . (int)$ctx['id'] . '/paies' : '/paie-me/paies' ?>" class="btn btn-primary btn-sm">Voir les paies</a>
    </div>
</div>

<?php if (!empty($monthlyNet)): ?>
<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-header">
        <h3>Évolution du net à payer — 6 derniers mois</h3>
    </div>
    <div style="padding:1.25rem;">
        <?php
        $maxNet = 0;
        foreach ($monthlyNet as $m) { if ((float)$m['total_net'] > $maxNet) $maxNet = (float)$m['total_net']; }
        $barMax = $maxNet > 0 ? $maxNet : 1;
        ?>
        <div style="display:flex; align-items:flex-end; gap:0.75rem; height:180px;">
            <?php foreach ($monthlyNet as $m):
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
    <div class="card-header">
        <h3>Dernières paies</h3>
        <?php if ($ctx): ?>
            <a href="<?= '/paie-me/societes/' . (int)$ctx['id'] . '/paies' ?>" class="btn btn-secondary btn-sm">Toutes les paies</a>
        <?php endif; ?>
    </div>

    <?php if (empty($latestPaies)): ?>
        <div class="empty-state">
            <p>Aucune paie enregistrée pour le moment.</p>
            <?php if ($ctx): ?>
                <a href="<?= '/paie-me/societes/' . (int)$ctx['id'] . '/paies' ?>" class="btn btn-primary">Créer une paie</a>
            <?php else: ?>
                <a href="/paie-me/societes" class="btn btn-primary">Choisir une société</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Salarié</th>
                        <th>Période</th>
                        <th>Salaire brut</th>
                        <th>CNSS</th>
                        <th>AMO</th>
                        <th>IR</th>
                        <th>Net à payer</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($latestPaies as $paie): ?>
                    <tr>
                        <td><?= htmlspecialchars($paie['nom_famille'] . ' ' . $paie['prenom']) ?></td>
                        <td><?= str_pad($paie['mois'], 2, '0', STR_PAD_LEFT) . '/' . $paie['annee'] ?></td>
                        <td><?= number_format($paie['salaire_brut'], 2, ',', ' ') ?></td>
                        <td><?= number_format($paie['cnss_salariale'], 2, ',', ' ') ?></td>
                        <td><?= number_format($paie['amo_salariale'], 2, ',', ' ') ?></td>
                        <td><?= number_format($paie['ir'], 2, ',', ' ') ?></td>
                        <td><strong><?= number_format($paie['net_a_payer'], 2, ',', ' ') ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
