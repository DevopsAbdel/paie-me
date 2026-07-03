<div class="card" style="position:relative;">
    <div class="card-header">
        <h3>Bulletin de paie — <?= htmlspecialchars($b['nom_famille'] . ' ' . $b['prenom']) ?></h3>
        <div>
            <a href="/paie-me/bulletins/<?= $b['id'] ?>/pdf" class="btn btn-primary btn-sm">Télécharger PDF</a>
            <a href="/paie-me/bulletins" class="btn btn-secondary btn-sm">Retour</a>
        </div>
    </div>

    <div style="display:flex; align-items:center; gap:1rem; padding:1rem; border-bottom:2px solid var(--accent); margin-bottom:1rem;">
        <div style="width:60px; height:60px; background:var(--accent); border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:1.5rem; font-weight:700; color:#fff; flex-shrink:0;">
            <?= strtoupper(mb_substr($b['raison_sociale'], 0, 2)) ?>
        </div>
        <div style="flex:1;">
            <h4 style="color:var(--accent); margin:0;"><?= htmlspecialchars($b['raison_sociale']) ?></h4>
            <p style="color:var(--text-muted); font-size:0.8125rem; margin:0.25rem 0 0 0;">
                ICE: <?= htmlspecialchars($b['ice']) ?> | IF: <?= htmlspecialchars($b['if_fiscal']) ?> | CNSS: <?= htmlspecialchars($b['cnss_societe']) ?>
                <?php if ($b['rc']): ?> | RC: <?= htmlspecialchars($b['rc']) ?><?php endif; ?>
            </p>
            <p style="color:var(--text-muted); font-size:0.8125rem; margin:0;">
                <?= htmlspecialchars($b['adresse'] ?? '') ?>
                <?php if ($b['telephone']): ?> | Tél: <?= htmlspecialchars($b['telephone']) ?><?php endif; ?>
                <?php if ($b['email']): ?> | <?= htmlspecialchars($b['email']) ?><?php endif; ?>
            </p>
        </div>
        <div style="text-align:right;">
            <h4 style="color:var(--accent); margin:0;">Bulletin de paie</h4>
            <p style="color:var(--text-muted); font-size:0.875rem; margin:0.25rem 0 0 0;">
                N° <?= htmlspecialchars($b['numero']) ?><br>
                Période: <?= str_pad($b['mois'], 2, '0', STR_PAD_LEFT) . '/' . $b['annee'] ?><br>
                Émis le: <?= $b['date_emission'] ?>
            </p>
        </div>
    </div>

    <table>
        <tr>
            <td style="padding:0.5rem 1rem;">
                <strong>Salarié:</strong> <?= htmlspecialchars($b['nom_famille'] . ' ' . $b['prenom']) ?><br>
                <strong>Matricule:</strong> <?= htmlspecialchars($b['matricule']) ?><br>
                <strong>CIN:</strong> <?= htmlspecialchars($b['cin']) ?><br>
                <strong>CNSS:</strong> <?= htmlspecialchars($b['cnss_num']) ?><br>
                <strong>Poste:</strong> <?= htmlspecialchars($b['fonction_nom'] ?? $b['poste']) ?><br>
                <strong>Date d'embauche:</strong> <?= $b['date_embauche'] ?>
            </td>
        </tr>
    </table>

    <hr style="border-color:var(--border); margin:0.5rem 0;">

    <h4 style="margin:1rem 0 0.5rem 1rem;">Détail des éléments</h4>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Libellé</th>
                    <th style="text-align:right;">Montant</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Salaire de base</td>
                    <td style="text-align:right;"><?= number_format($b['salaire_base'], 2, ',', ' ') ?></td>
                </tr>
                <tr>
                    <td>Indemnité de transport</td>
                    <td style="text-align:right;"><?= number_format($b['indemnite_transport'], 2, ',', ' ') ?></td>
                </tr>
                <tr>
                    <td>Indemnité de panier</td>
                    <td style="text-align:right;"><?= number_format($b['indemnite_panier'], 2, ',', ' ') ?></td>
                </tr>
                <?php if ((float)$b['indemnite_representation'] > 0): ?>
                <tr>
                    <td>Indemnité de représentation</td>
                    <td style="text-align:right;"><?= number_format($b['indemnite_representation'], 2, ',', ' ') ?></td>
                </tr>
                <?php endif; ?>
                <?php if ((float)$b['avantage_logement'] > 0): ?>
                <tr>
                    <td>Avantage logement</td>
                    <td style="text-align:right;"><?= number_format($b['avantage_logement'], 2, ',', ' ') ?></td>
                </tr>
                <?php endif; ?>
                <tr style="font-weight:bold; border-top:2px solid var(--accent);">
                    <td>Salaire brut</td>
                    <td style="text-align:right;"><?= number_format($b['salaire_brut'], 2, ',', ' ') ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <h4 style="margin:1rem 0 0.5rem 1rem;">Cotisations et retenues</h4>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Libellé</th>
                    <th style="text-align:right;">Montant</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>CNSS (part salariale)</td>
                    <td style="text-align:right;">- <?= number_format($b['cnss_salariale'], 2, ',', ' ') ?></td>
                </tr>
                <tr>
                    <td>AMO (part salariale)</td>
                    <td style="text-align:right;">- <?= number_format($b['amo_salariale'], 2, ',', ' ') ?></td>
                </tr>
                <tr>
                    <td>Salaire net imposable (SNI)</td>
                    <td style="text-align:right;"><?= number_format($b['sni'], 2, ',', ' ') ?></td>
                </tr>
                <tr>
                    <td>Impôt sur le revenu (IR)</td>
                    <td style="text-align:right;">- <?= number_format($b['ir'], 2, ',', ' ') ?></td>
                </tr>
                <?php if ((float)$b['deductions_familiales'] > 0): ?>
                <tr>
                    <td>Déductions familiales (<?= (int)$b['nb_enfants'] ?> enfants)</td>
                    <td style="text-align:right;">- <?= number_format($b['deductions_familiales'], 2, ',', ' ') ?></td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div style="background:var(--bg-primary); border-radius:8px; padding:1rem; margin-top:1rem; display:flex; justify-content:space-between; align-items:center;">
        <strong style="font-size:1.125rem;">Net à payer</strong>
        <strong style="font-size:1.25rem; color:var(--accent);"><?= number_format($b['net_a_payer'], 2, ',', ' ') ?> MAD</strong>
    </div>

    <div style="margin-top:2rem; padding-top:1rem; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
        <div style="display:flex; align-items:center; gap:0.5rem;">
            <div style="width:32px; height:32px; background:var(--accent); border-radius:6px; display:flex; align-items:center; justify-content:center; font-size:0.75rem; font-weight:700; color:#fff;">
                <?= strtoupper(mb_substr($b['raison_sociale'], 0, 2)) ?>
            </div>
            <span style="font-size:0.8125rem; color:var(--text-muted);"><?= htmlspecialchars($b['raison_sociale']) ?></span>
        </div>
        <div style="text-align:right; font-size:0.75rem; color:var(--text-muted);">
            <?= htmlspecialchars($b['adresse'] ?? '') ?>
            <?php if ($b['telephone']): ?> | Tél: <?= htmlspecialchars($b['telephone']) ?><?php endif; ?>
            <?php if ($b['email']): ?> | <?= htmlspecialchars($b['email']) ?><?php endif; ?>
            <br>
            ICE: <?= htmlspecialchars($b['ice']) ?> | IF: <?= htmlspecialchars($b['if_fiscal']) ?> | CNSS: <?= htmlspecialchars($b['cnss_societe']) ?>
            <?php if ($b['banque']): ?> | <?= htmlspecialchars($b['banque']) ?> <?php endif; ?>
            <?php if ($b['rib']): ?>| RIB: <?= htmlspecialchars($b['rib']) ?><?php endif; ?>
        </div>
    </div>
</div>
