<?php
$th = $paie['salaire_base'] > 0 ? $paie['salaire_base'] / 191 : 0;
$t25 = (float) ($baremeHS['taux_normal'] ?? 25);
$t50 = (float) ($baremeHS['taux_majore'] ?? 50);
$t100 = (float) ($baremeHS['taux_jour_ferie'] ?? 100);
$hs25 = (float) ($paie['heures_sup_25'] ?? 0);
$hs50 = (float) ($paie['heures_sup_50'] ?? 0);
$hs100 = (float) ($paie['heures_sup_100'] ?? 0);
$mHS25 = round($hs25 * $th * $t25 / 100, 2);
$mHS50 = round($hs50 * $th * $t50 / 100, 2);
$mHS100 = round($hs100 * $th * $t100 / 100, 2);
$jt = (int) ($paie['jours_travailles'] ?? 30);
$prorata = $jt / 26;
$baseProrata = round($paie['salaire_base'] * $prorata, 2);

function getPlafondDgi(string $code, array $plafonds, float $salaireBase): ?float
{
    if (isset($plafonds[$code]) && $plafonds[$code]['plafond_dgi_actif']) {
        return (float) $plafonds[$code]['plafond_dgi_valeur'];
    }
    if ($code === '331') return round($salaireBase * 0.10, 2);
    if ($code === '346') return 780.0;
    return null;
}

function overLimit(?float $valeur, ?float $plafond): bool
{
    return $plafond !== null && $valeur !== null && $valeur > $plafond;
}
?>
<div class="card">
    <div class="card-header">
        <h3>Modifier la paie</h3>
        <div class="table-actions">
            <a href="/paie-me/paies/<?= $paie['periode_id'] ?>/lignes" class="btn btn-secondary btn-sm">Retour aux paies</a>
        </div>
    </div>

    <div style="padding:1rem; border-bottom:1px solid var(--border); display:grid; grid-template-columns:1fr 1fr; gap:0.5rem;">
        <div><strong>Salarié :</strong> <?= htmlspecialchars($paie['nom_famille'] . ' ' . $paie['prenom']) ?></div>
        <div><strong>Société :</strong> <?= htmlspecialchars($paie['raison_sociale']) ?></div>
        <div><strong>Période :</strong> <?= str_pad($paie['mois'] ?? '', 2, '0', STR_PAD_LEFT) ?>/<?= $paie['annee'] ?? '' ?></div>
        <div><strong>Salaire de base :</strong> <?= number_format($paie['salaire_base'], 2, ',', ' ') ?> MAD</div>
    </div>

    <form method="POST">
        <?= \Core\Session::csrfField() ?>

        <div class="table-wrapper">
            <table class="edit-paie-table">
                <thead>
                    <tr>
                        <th style="width:5%;">Code</th>
                        <th style="width:22%;">Libellé</th>
                        <th style="width:10%;">Base</th>
                        <th style="width:7%;">Unité</th>
                        <th style="width:7%;">Taux</th>
                        <th style="width:11%;">Gains</th>
                        <th style="width:11%;">Retenus</th>
                        <th style="width:11%;">Salariale</th>
                        <th style="width:11%;">Patronale</th>
                        <th style="width:5%;"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="section-header"><td colspan="10">Salaire et indemnités</td></tr>

                    <tr>
                        <td class="code">100</td>
                        <td>Salaire de base</td>
                        <td class="montant"><?= number_format($paie['salaire_base'], 2, ',', ' ') ?></td>
                        <td class="unite">DH</td>
                        <td class="taux">—</td>
                        <td class="montant"><?= number_format($paie['salaire_base'], 2, ',', ' ') ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td class="code">101</td>
                        <td>Durée de travail</td>
                        <td class="montant">
                            <input type="number" step="1" min="0" max="31" name="jours_travailles" class="form-control-inline" value="<?= $jt ?>" style="width:55px;">
                        </td>
                        <td class="unite">Jours</td>
                        <td class="taux">—</td>
                        <td class="montant"><?= number_format($baseProrata, 2, ',', ' ') ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="code">102</td>
                        <td>Jours de congé</td>
                        <td class="montant">
                            <input type="number" step="0.5" min="0" max="31" name="jours_conge" class="form-control-inline" value="<?= (float)($paie['jours_conge'] ?? 0) ?>" style="width:55px;">
                        </td>
                        <td class="unite">Jours</td>
                        <td class="taux">—</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="code">103</td>
                        <td>Jours fériés</td>
                        <td class="montant">
                            <input type="number" step="0.5" min="0" max="31" name="jours_feries" class="form-control-inline" value="<?= (float)($paie['jours_feries'] ?? 0) ?>" style="width:55px;">
                        </td>
                        <td class="unite">Jours</td>
                        <td class="taux">—</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td class="code">201</td>
                        <td>
                            <span class="info" title="Taux horaire <?= number_format($th, 2, ',', ' ') ?> MAD/h">ⓘ</span>
                            HS <?= $t25 ?>%
                        </td>
                        <td class="montant">
                            <input type="number" step="0.5" min="0" name="heures_sup_25" class="form-control-inline" value="<?= $hs25 ?>" style="width:55px;">
                        </td>
                        <td class="unite">Heure</td>
                        <td class="taux"><?= $t25 ?>%</td>
                        <td class="montant"><?= number_format($mHS25, 2, ',', ' ') ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td class="code">202</td>
                        <td>
                            <span class="info" title="Taux horaire <?= number_format($th, 2, ',', ' ') ?> MAD/h">ⓘ</span>
                            HS <?= $t50 ?>%
                        </td>
                        <td class="montant">
                            <input type="number" step="0.5" min="0" name="heures_sup_50" class="form-control-inline" value="<?= $hs50 ?>" style="width:55px;">
                        </td>
                        <td class="unite">Heure</td>
                        <td class="taux"><?= $t50 ?>%</td>
                        <td class="montant"><?= number_format($mHS50, 2, ',', ' ') ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td class="code">203</td>
                        <td>
                            <span class="info" title="Taux horaire <?= number_format($th, 2, ',', ' ') ?> MAD/h">ⓘ</span>
                            HS <?= $t100 ?>%
                        </td>
                        <td class="montant">
                            <input type="number" step="0.5" min="0" name="heures_sup_100" class="form-control-inline" value="<?= $hs100 ?>" style="width:55px;">
                        </td>
                        <td class="unite">Heure</td>
                        <td class="taux"><?= $t100 ?>%</td>
                        <td class="montant"><?= number_format($mHS100, 2, ',', ' ') ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <?php
                    $indemnFields = [
                        'indemnite_transport' => ['330', 'Indemnité transport', 'Exonérée IR/CNSS — Plafond : 500 MAD/mois'],
                        'indemnite_panier'    => ['346', 'Indemnité panier', 'Exonérée IR/CNSS jusqu\'à 780 MAD/mois'],
                        'indemnite_representation' => ['331', 'Indemnité représentation', '10% du salaire de base'],
                        'avantage_logement'   => ['340', 'Avantage logement', 'Avantage en nature imposable'],
                    ];
                    foreach ($indemnFields as $field => $meta):
                        $code = $meta[0];
                        $val = (float) ($paie[$field] ?? 0);
                        $pt = getPlafondDgi($code, $plafonds, (float) $paie['salaire_base']);
                        $ov = overLimit($val, $pt);
                    ?>
                    <tr<?= $ov ? ' class="row-over-limit"' : '' ?>>
                        <td class="code"><?= $code ?></td>
                        <td><span class="info" title="<?= $meta[2] ?>">ⓘ</span> <?= $meta[1] ?></td>
                        <td></td>
                        <td class="unite">DH</td>
                        <td class="taux">—</td>
                        <td class="montant">
                            <input type="number" step="0.01" min="0" name="<?= $field ?>" class="form-control-inline<?= $ov ? ' over-limit' : '' ?>" value="<?= $val ?>">
                            <?php if ($pt !== null): ?><span class="plafond-label">max <?= number_format($pt, 2, ',', ' ') ?></span><?php endif; ?>
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><button type="button" class="btn-icon" title="Supprimer" onclick="this.closest('tr').querySelector('input').value='0'">✖</button></td>
                    </tr>
                    <?php endforeach; ?>

                    <?php foreach ($paieGains as $g):
                        $ptG = getPlafondDgi($g['code'], $plafonds, (float) $paie['salaire_base']);
                        $ovG = overLimit((float) $g['montant'], $ptG);
                    ?>
                    <tr<?= $ovG ? ' class="row-over-limit"' : '' ?>>
                        <td class="code"><?= htmlspecialchars($g['code']) ?></td>
                        <td><?= htmlspecialchars($g['libelle']) ?></td>
                        <td></td>
                        <td class="unite">DH</td>
                        <td class="taux">—</td>
                        <td class="montant<?= $ovG ? ' over-limit' : '' ?>"><?= number_format($g['montant'], 2, ',', ' ') ?>
                            <?php if ($ptG !== null): ?><span class="plafond-label">max <?= number_format($ptG, 2, ',', ' ') ?></span><?php endif; ?>
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <?php endforeach; ?>

                    <tr id="gains-container"></tr>

                    <tr>
                        <td colspan="10" style="padding:0.25rem 0.75rem;">
                            <button type="button" class="btn btn-sm btn-secondary" onclick="document.getElementById('gainModal').style.display='flex'" style="font-size:0.75rem;">+ Ajouter un gain</button>
                        </td>
                    </tr>

                    <tr class="total-row">
                        <td></td>
                        <td><strong>Salaire brut</strong></td>
                        <td></td>
                        <td class="unite">DH</td>
                        <td></td>
                        <td class="montant"><strong><?= number_format($paie['salaire_brut'], 2, ',', ' ') ?></strong></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr class="section-header"><td colspan="10">Cotisations</td></tr>

                    <tr>
                        <td class="code">400</td>
                        <td><span class="info" title="min(Salaire brut, 6 000) × 4.48%">ⓘ</span> CNSS</td>
                        <td class="montant"><?= number_format(min($paie['salaire_brut'], 6000), 2, ',', ' ') ?></td>
                        <td class="unite">DH</td>
                        <td class="taux">4,48%</td>
                        <td></td>
                        <td></td>
                        <td class="montant"><?= number_format($paie['cnss_salariale'], 2, ',', ' ') ?></td>
                        <td class="montant"><?= number_format($paie['cnss_patronale'], 2, ',', ' ') ?></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td class="code">410</td>
                        <td><span class="info" title="Salaire brut × 2.26%">ⓘ</span> AMO</td>
                        <td class="montant"><?= number_format($paie['salaire_brut'], 2, ',', ' ') ?></td>
                        <td class="unite">DH</td>
                        <td class="taux">2,26%</td>
                        <td></td>
                        <td></td>
                        <td class="montant"><?= number_format($paie['amo_salariale'], 2, ',', ' ') ?></td>
                        <td class="montant"><?= number_format($paie['amo_patronale'], 2, ',', ' ') ?></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td class="code">420</td>
                        <td><span class="info" title="Montant fiche salarié">ⓘ</span> Mutuelle</td>
                        <td></td>
                        <td class="unite">DH</td>
                        <td class="taux">—</td>
                        <td></td>
                        <td></td>
                        <td class="montant"><?= number_format($paie['mutuelle'], 2, ',', ' ') ?></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr class="section-header"><td colspan="10">IR et frais professionnels</td></tr>

                    <?php $sbiAnnuel = $paie['sbi'] * 12; $fpTaux = $sbiAnnuel <= 78000 ? 35 : 25; ?>
                    <tr>
                        <td class="code">500</td>
                        <td><span class="info" title="Salaire brut – Gains exonérés">ⓘ</span> SBI</td>
                        <td class="montant"><?= number_format($paie['sbi'], 2, ',', ' ') ?></td>
                        <td class="unite">DH</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td class="code">501</td>
                        <td><span class="info" title="<?= $sbiAnnuel <= 78000 ? 'SBI annuel ≤ 78 000 → 35%' : 'SBI annuel > 78 000 → 25% (max 2 916,70 MAD)' ?>">ⓘ</span> Frais professionnels</td>
                        <td class="montant"><?= number_format($paie['sbi'], 2, ',', ' ') ?></td>
                        <td class="unite">DH</td>
                        <td class="taux"><?= $fpTaux ?>%</td>
                        <td></td>
                        <td class="montant"><?= number_format($paie['frais_professionnels'], 2, ',', ' ') ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td class="code">502</td>
                        <td><span class="info" title="SBI – Frais pro – CNSS – AMO – Mutuelle">ⓘ</span> SNI</td>
                        <td class="montant"><?= number_format($paie['sni'], 2, ',', ' ') ?></td>
                        <td class="unite">DH</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td class="code">600</td>
                        <td><span class="info" title="Barème progressif IR sur SNI">ⓘ</span> IR brut</td>
                        <td class="montant"><?= number_format($paie['sni'], 2, ',', ' ') ?></td>
                        <td class="unite">DH</td>
                        <td class="taux">Barème</td>
                        <td></td>
                        <td class="montant"><?= number_format($paie['ir'], 2, ',', ' ') ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td class="code">601</td>
                        <td><span class="info" title="30 MAD × Nb enfants à charge (max 6)">ⓘ</span> Déductions familiales</td>
                        <td></td>
                        <td class="unite">DH</td>
                        <td class="taux">—</td>
                        <td></td>
                        <td></td>
                        <td class="montant" style="color:var(--success);"><?= number_format($paie['deductions_familiales'], 2, ',', ' ') ?></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr class="section-header"><td colspan="10">Retenues personnalisées</td></tr>

                    <?php foreach ($paieRetenues as $r): ?>
                    <tr class="retenue-row" data-id="<?= $r['id'] ?>">
                        <td class="code">900</td>
                        <td>
                            <select name="retenue_type_existing[<?= $r['id'] ?>]" class="form-select-inline" style="width:75px;">
                                <option value="avance"<?= $r['type'] === 'avance' ? ' selected' : '' ?>>Avance</option>
                                <option value="pret"<?= $r['type'] === 'pret' ? ' selected' : '' ?>>Prêt</option>
                                <option value="sanction"<?= $r['type'] === 'sanction' ? ' selected' : '' ?>>Sanction</option>
                                <option value="autre"<?= $r['type'] === 'autre' ? ' selected' : '' ?>>Autre</option>
                            </select>
                            <input type="text" name="retenue_libelle_existing[<?= $r['id'] ?>]" class="form-control-inline" style="width:calc(100% - 80px);text-align:left;" value="<?= htmlspecialchars($r['libelle']) ?>">
                        </td>
                        <td></td>
                        <td class="unite">DH</td>
                        <td class="taux">—</td>
                        <td></td>
                        <td class="montant">
                            <input type="number" step="0.01" min="0" name="retenue_montant_existing[<?= $r['id'] ?>]" class="form-control-inline" style="width:80px;" value="<?= $r['montant'] ?>">
                        </td>
                        <td></td>
                        <td></td>
                        <td style="text-align:center;">
                            <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()" style="padding:0.1rem 0.3rem;font-size:0.65rem;">✕</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <tr id="retenues-container"></tr>

                    <tr>
                        <td colspan="10" style="padding:0.25rem 0.75rem;">
                            <button type="button" class="btn btn-sm btn-secondary" onclick="document.getElementById('retenueModal').style.display='flex'" style="font-size:0.75rem;">+ Ajouter une retenue</button>
                        </td>
                    </tr>

                    <tr class="section-header recap-section"><td colspan="10">Récapitulatif</td></tr>

                    <tr>
                        <td></td>
                        <td><span class="info" title="Avances + Prêts + Retenues personnalisées">ⓘ</span> Autres retenues</td>
                        <td></td>
                        <td class="unite">DH</td>
                        <td></td>
                        <td></td>
                        <td class="montant"><?= number_format($paie['autres_retenues'], 2, ',', ' ') ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td></td>
                        <td><span class="info" title="Salaire brut – Cotisations – IR – Mutuelle">ⓘ</span> Net avant retenues</td>
                        <td></td>
                        <td class="unite">DH</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="montant"><strong><?= number_format($paie['net_avant_retenues'], 2, ',', ' ') ?></strong></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr class="net-row">
                        <td></td>
                        <td><span class="info" title="Net avant retenues – Autres retenues">ⓘ</span> <strong style="color:var(--accent);">Net à payer</strong></td>
                        <td></td>
                        <td class="unite">DH</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="montant"><strong style="color:var(--accent);font-size:1rem;"><?= number_format($paie['net_a_payer'], 2, ',', ' ') ?> MAD</strong></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div style="padding:0.75rem 1rem 1rem; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:0.5rem; border-top:1px solid var(--border);">
            <label style="display:flex;align-items:center;gap:0.4rem;cursor:pointer;font-size:0.8rem;color:var(--text-muted);user-select:none;">
                <input type="checkbox" name="fermer_apres" value="1" style="accent-color:var(--accent);cursor:pointer;">
                <span>Fermer la fenêtre après l'enregistrement</span>
            </label>
            <div style="display:flex;gap:0.5rem;align-items:center;">
                <button type="submit" class="btn btn-primary">Enregistrer</button>
                <button type="submit" name="recalculer" value="1" class="btn btn-secondary btn-sm" onclick="return confirm('Recalculer cette paie ? Les modifications seront sauvegardées avant le calcul.')">Recalculer la paie</button>
                <a href="/paie-me/paies/<?= $paie['periode_id'] ?>/lignes" class="btn btn-secondary btn-sm">Retour</a>
            </div>
        </div>
    </form>

    <!-- Modale Gain -->
    <div class="custom-modal-overlay" id="gainModal" style="display:none;">
        <div class="custom-modal" style="max-width:780px;">
            <div class="custom-modal-header">
                <h4 style="margin:0;">Ajouter un gain</h4>
                <button type="button" class="btn btn-sm btn-secondary" onclick="document.getElementById('gainModal').style.display='none'" style="padding:0.2rem 0.5rem;">✕</button>
            </div>
            <div class="custom-modal-body">
                <div style="margin-bottom:0.75rem;">
                    <input type="text" id="gain_search" class="form-control" placeholder="Rechercher par code ou libellé..." onkeyup="filterGains()" style="width:100%;">
                </div>
                <div style="max-height:280px;overflow-y:auto;border:1px solid var(--border);border-radius:6px;">
                    <table class="edit-paie-table" style="border:none;margin:0;">
                        <thead>
                            <tr>
                                <th style="width:8%;">Code</th>
                                <th style="width:40%;">Libellé</th>
                                <th style="width:10%;">Type</th>
                                <th style="width:20%;">Plafond DGI</th>
                                <th style="width:22%;"></th>
                            </tr>
                        </thead>
                        <tbody id="gain_table_body">
                            <?php foreach ($rubriquesGains as $rg):
                                $plafondText = (!empty($rg['plafond_dgi_valeur']) ? number_format((float)$rg['plafond_dgi_valeur'], 2, ',', ' ') . ' ' . ($rg['plafond_dgi_type'] ?? '') : '—');
                            ?>
                            <tr class="gain-row" data-id="<?= $rg['id'] ?>" data-code="<?= htmlspecialchars($rg['code']) ?>" data-libelle="<?= htmlspecialchars($rg['libelle']) ?>" data-plafond="<?= htmlspecialchars($rg['plafond_dgi_valeur'] ?? '') ?>" data-plafond-type="<?= htmlspecialchars($rg['plafond_dgi_type'] ?? '') ?>" onclick="selectGainRow(this)">
                                <td class="code"><?= htmlspecialchars($rg['code']) ?></td>
                                <td><?= htmlspecialchars($rg['libelle']) ?></td>
                                <td style="text-align:center;font-size:0.72rem;"><?= htmlspecialchars($rg['type_montant'] ?? 'fixe') ?></td>
                                <td style="text-align:right;font-size:0.72rem;color:var(--text-muted);"><?= $plafondText ?></td>
                                <td style="text-align:center;"><button type="button" class="btn btn-sm btn-secondary" onclick="event.stopPropagation();selectGainRow(this.closest('tr'))" style="font-size:0.68rem;padding:0.15rem 0.5rem;">Choisir</button></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div id="gain_info" style="margin-top:0.4rem;font-size:0.72rem;color:var(--text-muted);min-height:1.2rem;"></div>
                <div style="margin-top:0.75rem;display:flex;align-items:center;gap:0.75rem;">
                    <label style="font-size:0.8rem;white-space:nowrap;">Montant (DH)</label>
                    <input type="number" step="0.01" min="0" id="gain_montant_input" class="form-control" value="0" style="width:150px;">
                </div>
            </div>
            <div class="custom-modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('gainModal').style.display='none'">Annuler</button>
                <button type="button" class="btn btn-primary btn-sm" onclick="ajouterGainDepuisModal()">Ajouter</button>
            </div>
        </div>
    </div>

    <!-- Modale Retenue -->
    <div class="custom-modal-overlay" id="retenueModal" style="display:none;">
        <div class="custom-modal" style="max-width:520px;">
            <div class="custom-modal-header">
                <h4 style="margin:0;">Ajouter une retenue</h4>
                <button type="button" class="btn btn-sm btn-secondary" onclick="document.getElementById('retenueModal').style.display='none'" style="padding:0.2rem 0.5rem;">✕</button>
            </div>
            <div class="custom-modal-body">
                <div class="form-group">
                    <label>Rubrique</label>
                    <select id="retenue_rubrique_select" class="form-select" style="width:100%;">
                        <option value="">— Choisir une rubrique —</option>
                        <?php foreach ($rubriquesRetenues as $rr): ?>
                        <option value="<?= $rr['id'] ?>" data-code="<?= htmlspecialchars($rr['code']) ?>" data-libelle="<?= htmlspecialchars($rr['libelle']) ?>">
                            [<?= htmlspecialchars($rr['code']) ?>] <?= htmlspecialchars(mb_substr($rr['libelle'], 0, 60)) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" style="margin-top:0.5rem;">
                    <label>Montant (DH)</label>
                    <input type="number" step="0.01" min="0" id="retenue_montant_input" class="form-control" value="0" style="width:150px;">
                </div>
            </div>
            <div class="custom-modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('retenueModal').style.display='none'">Annuler</button>
                <button type="button" class="btn btn-primary btn-sm" onclick="ajouterRetenueDepuisModal()">Ajouter</button>
            </div>
        </div>
    </div>
</div>

<style>
.edit-paie-table { width:100%; border-collapse:collapse; }
.edit-paie-table th { padding:0.4rem 0.5rem; text-align:left; font-size:0.7rem; text-transform:uppercase; letter-spacing:0.04em; color:var(--text-muted); border-bottom:1px solid var(--border); }
.edit-paie-table td { padding:0.3rem 0.5rem; font-size:0.8rem; border-bottom:1px solid var(--border-subtle); }
.edit-paie-table .montant { text-align:right; white-space:nowrap; }
.edit-paie-table .taux { text-align:center; font-size:0.75rem; color:var(--text-muted); }
.edit-paie-table .unite { text-align:center; font-size:0.7rem; color:var(--text-muted); width:55px; }
.edit-paie-table .code { text-align:center; font-size:0.7rem; color:var(--text-muted); font-family:monospace; }
.edit-paie-table .section-header td { padding:0.4rem 0.5rem 0.2rem; font-size:0.65rem; font-weight:600; text-transform:uppercase; letter-spacing:0.06em; color:var(--accent); border-bottom:none; background:rgba(59,130,246,0.04); }
.edit-paie-table .total-row td { padding:0.4rem 0.5rem; border-top:1px solid var(--border); font-weight:600; }
.edit-paie-table .recap-section td { border-top:1px solid var(--border); }
.edit-paie-table .net-row td { border-top:2px solid var(--accent); }
.form-control-inline { width:60px; padding:0.2rem 0.3rem; font-size:0.75rem; background:var(--bg-surface); border:1px solid var(--border); border-radius:3px; color:var(--text); text-align:right; }
.form-control-inline:focus { border-color:var(--accent); outline:none; }
.form-control-inline.over-limit { border-color:#ef4444; background:rgba(239,68,68,0.12); color:#fca5a5; }
.row-over-limit td { background:rgba(239,68,68,0.06); }
.montant.over-limit { color:#fca5a5; font-weight:600; }
.plafond-label { display:block; font-size:0.6rem; color:var(--text-muted); white-space:nowrap; margin-top:0.1rem; }
.info { cursor:help; font-size:0.7rem; color:var(--text-muted); margin-right:0.15rem; }
.info:hover { color:var(--accent); }
.form-select-inline { padding:0.2rem 0.3rem; font-size:0.72rem; background:var(--bg-surface); border:1px solid var(--border); border-radius:3px; color:var(--text); }
.form-select-inline:focus { border-color:var(--accent); outline:none; }

.custom-modal-overlay { position:fixed; inset:0; background:rgba(0,0,0,0.7); z-index:1000; display:flex; align-items:center; justify-content:center; }
.custom-modal { background:var(--bg-surface); border:1px solid var(--border); border-radius:8px; width:90%; max-height:80vh; overflow-y:auto; box-shadow:0 8px 32px rgba(0,0,0,0.5); }
.custom-modal-header { display:flex; justify-content:space-between; align-items:center; padding:0.75rem 1rem; border-bottom:1px solid var(--border); }
.custom-modal-body { padding:1rem; }
.custom-modal-body .form-control,
.custom-modal-body .form-select { background:var(--bg-primary); border:1px solid var(--border); color:var(--text); color-scheme:dark; }
.custom-modal-body .form-control:focus,
.custom-modal-body .form-select:focus { border-color:var(--accent); outline:none; }
.custom-modal-body select.form-select option { background:#1e293b; color:var(--text); }
.custom-modal-body .form-control[type="number"] { -moz-appearance:textfield; appearance:textfield; }
.gain-row { cursor:pointer; }
.gain-row:hover { background:var(--bg-hover); }
.gain-row.selected { background:rgba(59,130,246,0.15); }
.gain-row.selected td { color:var(--accent); font-weight:500; }
.custom-modal-footer { display:flex; justify-content:flex-end; gap:0.5rem; padding:0.75rem 1rem; border-top:1px solid var(--border); }
</style>

<script>
let gainIdx = <?= count($paieGains) ?>;
let gainSelected = null;

function filterGains() {
    const q = document.getElementById('gain_search').value.toLowerCase();
    document.querySelectorAll('.gain-row').forEach(r => {
        const code = r.dataset.code.toLowerCase();
        const libelle = r.dataset.libelle.toLowerCase();
        r.style.display = (code.includes(q) || libelle.includes(q)) ? '' : 'none';
    });
}

function selectGainRow(row) {
    document.querySelectorAll('.gain-row').forEach(r => r.classList.remove('selected'));
    row.classList.add('selected');
    gainSelected = {
        id: row.dataset.id,
        code: row.dataset.code,
        libelle: row.dataset.libelle,
        plafond: row.dataset.plafond,
        plafondType: row.dataset.plafondType,
    };
    const info = document.getElementById('gain_info');
    if (gainSelected.plafond) {
        info.textContent = 'Plafond DGI : ' + gainSelected.plafond + (gainSelected.plafondType ? ' / ' + gainSelected.plafondType : '');
    } else {
        info.textContent = 'Aucun plafond configuré';
    }
}

function ajouterGainDepuisModal() {
    if (!gainSelected) { alert('Veuillez sélectionner une rubrique.'); return; }
    const montant = parseFloat(document.getElementById('gain_montant_input').value) || 0;
    gainIdx++;
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td class="code">${gainSelected.code}</td>
        <td>${gainSelected.libelle}</td>
        <td></td>
        <td class="unite">DH</td>
        <td class="taux">—</td>
        <td class="montant">
            <input type="hidden" name="gain_new_rubrique_id[${gainIdx}]" value="${gainSelected.id}">
            <input type="number" step="0.01" min="0" name="gain_new_montant[${gainIdx}]" class="form-control-inline" style="width:80px;" value="${montant.toFixed(2)}">
        </td>
        <td></td>
        <td></td>
        <td></td>
        <td style="text-align:center;">
            <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()" style="padding:0.1rem 0.3rem;font-size:0.65rem;">✕</button>
        </td>
    `;
    document.getElementById('gains-container').before(tr);
    document.getElementById('gainModal').style.display = 'none';
    gainSelected = null;
    document.querySelectorAll('.gain-row').forEach(r => r.classList.remove('selected'));
    document.getElementById('gain_search').value = '';
    document.getElementById('gain_montant_input').value = 0;
    document.getElementById('gain_info').textContent = '';
    document.querySelectorAll('.gain-row').forEach(r => r.style.display = '');
}

let retenueIdx = <?= count($paieRetenues) ?>;

function ajouterRetenueDepuisModal() {
    const sel = document.getElementById('retenue_rubrique_select');
    const opt = sel.options[sel.selectedIndex];
    if (!opt || !opt.value) { alert('Veuillez sélectionner une rubrique.'); return; }
    const montant = parseFloat(document.getElementById('retenue_montant_input').value) || 0;
    retenueIdx++;
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td class="code">900</td>
        <td>${opt.dataset.libelle}</td>
        <td></td>
        <td class="unite">DH</td>
        <td class="taux">—</td>
        <td></td>
        <td class="montant">
            <input type="hidden" name="retenue_new_rubrique_id[${retenueIdx}]" value="${opt.value}">
            <input type="number" step="0.01" min="0" name="retenue_new_montant[${retenueIdx}]" class="form-control-inline" style="width:80px;" value="${montant.toFixed(2)}">
        </td>
        <td></td>
        <td></td>
        <td style="text-align:center;">
            <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()" style="padding:0.1rem 0.3rem;font-size:0.65rem;">✕</button>
        </td>
    `;
    document.getElementById('retenues-container').before(tr);
    document.getElementById('retenueModal').style.display = 'none';
    sel.value = '';
    document.getElementById('retenue_montant_input').value = 0;
}
</script>