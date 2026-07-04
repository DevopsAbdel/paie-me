<div class="card">
    <div class="card-header">
        <h3>Solde de Tout Compte</h3>
        <div>
            <span class="badge badge-info"><?= htmlspecialchars($s['nom_famille'] . ' ' . $s['prenom']) ?></span>
            <a href="/paie-me/salaries/<?= $s['id'] ?>/edit" class="btn btn-secondary btn-sm">← Fiche salarié</a>
            <a href="/paie-me/salaries/<?= $s['id'] ?>/stc/pdf" class="btn btn-primary btn-sm" target="_blank">PDF</a>
        </div>
    </div>

    <div class="card-body">
        <div class="stc-header">
            <div class="stc-entreprise">
                <p><strong><?= htmlspecialchars($s['raison_sociale']) ?></strong></p>
                <p><?= htmlspecialchars($s['adresse']) ?></p>
                <p><?= htmlspecialchars($s['ville']) ?></p>
                <p>ICE : <?= htmlspecialchars($s['ice']) ?></p>
            </div>
            <div class="stc-salarie">
                <p><strong>Salarié :</strong> <?= htmlspecialchars($s['nom_famille'] . ' ' . $s['prenom']) ?></p>
                <p><strong>Matricule :</strong> <?= htmlspecialchars($s['matricule']) ?></p>
                <p><strong>CIN :</strong> <?= htmlspecialchars($s['cin']) ?></p>
                <p><strong>CNSS :</strong> <?= htmlspecialchars($s['cnss']) ?></p>
                <p><strong>Date d'embauche :</strong> <?= $s['date_embauche'] ?></p>
                <p><strong>Date de sortie :</strong> <?= $s['date_sortie'] ?? '—' ?></p>
                <p><strong>Poste :</strong> <?= htmlspecialchars($s['poste']) ?></p>
            </div>
        </div>

        <h4>Détail des sommes dues</h4>
        <table>
            <thead>
                <tr>
                    <th>Rubrique</th>
                    <th>Montant</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total = 0;
                $lignes = [];
                if ($dernierePaie) {
                    $lignes[] = ['Salaire de base', (float) $dernierePaie['salaire_brut']];
                    $lignes[] = ['Indemnité transport', (float) $dernierePaie['indemnite_transport']];
                    $lignes[] = ['Indemnité panier', (float) $dernierePaie['indemnite_panier']];
                    $total = (float) $dernierePaie['salaire_brut'] +
                             (float) $dernierePaie['indemnite_transport'] +
                             (float) $dernierePaie['indemnite_panier'];
                } else {
                    $lignes[] = ['Aucune paie trouvée', 0];
                }
                foreach ($lignes as $l): ?>
                <tr>
                    <td><?= htmlspecialchars($l[0]) ?></td>
                    <td class="text-right"><?= number_format($l[1], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="totaux-row">
                    <td><strong>TOTAL BRUT</strong></td>
                    <td class="text-right"><strong><?= number_format($total, 2) ?></strong></td>
                </tr>
            </tfoot>
        </table>

        <div class="stc-footer">
            <p>Fait le <?= date('d/m/Y') ?> à <?= htmlspecialchars($s['ville']) ?></p>
            <div class="stc-signatures">
                <div class="signature-box">
                    <p>Cachet et signature de l'employeur</p>
                </div>
                <div class="signature-box">
                    <p>Signature du salarié</p>
                </div>
            </div>
        </div>
    </div>
</div>
