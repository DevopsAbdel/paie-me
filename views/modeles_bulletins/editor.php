<?php
$cfg = $config;
$baseUrl = $baseUrl;
$societeId = $societe['id'];
$modeleId = $modele['id'];
$couleur = $cfg['couleur_primaire'] ?? '#3b82f6';
$allCodes = [
    '100' => 'Salaire de base', '204' => "Prime d'ancienneté", '330' => 'Indemnité de transport',
    '346' => 'Indemnité de panier', '331' => 'Indemnité de représentation', '340' => 'Avantage logement',
    '201' => 'Heures sup. 25%', '202' => 'Heures sup. 50%', '203' => 'Heures sup. 100%',
    'SB' => 'Salaire brut', '400' => 'CNSS salariale', '410' => 'AMO salariale',
    '420' => 'Mutuelle', '501' => 'Frais professionnels', '502' => 'SNI',
    '600' => 'IR', '601' => 'Déductions charges famille',
    '400P' => 'CNSS patronale', '410P' => 'AMO patronale',
    'AF' => 'Allocation familiale', 'PS' => 'Prestation sociale', 'TF' => 'Taxe de formation',
];
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
    <div>
        <h2 style="margin:0; font-size:1.25rem;">
            <a href="<?= $baseUrl ?>" style="color:var(--text-muted); text-decoration:none;">Modèles Bulletins</a>
            <span style="color:var(--text-muted); margin:0 0.5rem;">/</span>
            <span style="color:var(--accent);">Éditeur</span>
        </h2>
        <p style="color:var(--text-muted); font-size:0.8rem; margin:0.25rem 0 0 0;">
            <?= htmlspecialchars($societe['raison_sociale']) ?> — <?= htmlspecialchars($modele['nom']) ?>
        </p>
    </div>
    <div style="display:flex; gap:0.5rem;">
        <a href="<?= $baseUrl ?>" class="btn btn-secondary btn-sm">Retour</a>
        <button type="button" class="btn btn-success btn-sm" onclick="saveConfig()" id="btnSave">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:0.25rem; vertical-align:middle;"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Enregistrer
        </button>
    </div>
</div>

<!-- Infos générales -->
<div class="card" style="margin-bottom:1rem;">
    <div class="card-header">
        <h3 style="margin:0; font-size:1rem;">Informations générales</h3>
    </div>
    <div style="padding:1rem;">
        <div style="display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:0.75rem;">
            <div class="form-group">
                <label class="form-label" style="font-size:0.7rem; text-transform:uppercase; color:var(--text-muted);">Nom du modèle</label>
                <input type="text" id="cfgNom" class="form-control" value="<?= htmlspecialchars($cfg['nom'] ?? $modele['nom']) ?>">
            </div>
            <div class="form-group">
                <label class="form-label" style="font-size:0.7rem; text-transform:uppercase; color:var(--text-muted);">Description</label>
                <input type="text" id="cfgDesc" class="form-control" value="<?= htmlspecialchars($cfg['description'] ?? $modele['description'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label" style="font-size:0.7rem; text-transform:uppercase; color:var(--text-muted);">Couleur principale</label>
                <div style="display:flex; gap:0.5rem; align-items:center;">
                    <input type="color" id="cfgCouleur" value="<?= htmlspecialchars($couleur) ?>" style="width:40px; height:36px; border:none; cursor:pointer; background:transparent;">
                    <input type="text" id="cfgCouleurHex" class="form-control" value="<?= htmlspecialchars($couleur) ?>" style="flex:1;" oninput="document.getElementById('cfgCouleur').value=this.value;">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" style="font-size:0.7rem; text-transform:uppercase; color:var(--text-muted);">Libellé net à payer</label>
                <input type="text" id="cfgNetLabel" class="form-control" value="<?= htmlspecialchars($cfg['net_label'] ?? 'Net à payer') ?>">
            </div>
        </div>
        <div style="display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:0.75rem; margin-top:0.75rem;">
            <div class="form-group">
                <label class="form-label" style="font-size:0.7rem; text-transform:uppercase; color:var(--text-muted);">Couleur net à payer</label>
                <div style="display:flex; gap:0.5rem; align-items:center;">
                    <input type="color" id="cfgNetColor" value="<?= htmlspecialchars($cfg['net_color'] ?? $couleur) ?>" style="width:40px; height:36px; border:none; cursor:pointer; background:transparent;">
                    <input type="text" id="cfgNetColorHex" class="form-control" value="<?= htmlspecialchars($cfg['net_color'] ?? $couleur) ?>" style="flex:1;" oninput="document.getElementById('cfgNetColor').value=this.value;">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" style="font-size:0.7rem; text-transform:uppercase; color:var(--text-muted);">Afficher le pied de bulletin</label>
                <select id="cfgShowFooter" class="form-control">
                    <option value="1" <?= ($cfg['show_footer'] ?? true) ? 'selected' : '' ?>>Oui</option>
                    <option value="0" <?= empty($cfg['show_footer']) ? 'selected' : '' ?>>Non</option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Sections -->
<div id="sectionsContainer"></div>

<!-- Bouton ajouter section -->
<div style="margin:1rem 0; text-align:center;">
    <button type="button" class="btn btn-primary btn-sm" onclick="addSection()">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:0.25rem; vertical-align:middle;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Ajouter une section
    </button>
</div>

<!-- Modal Ajouter ligne -->
<div class="modal fade" id="ligneModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background:var(--bg-surface); color:var(--text); border:1px solid var(--border); border-radius:12px;">
            <div class="modal-header" style="border-bottom:1px solid var(--border);">
                <h5 class="modal-title" id="ligneModalTitle">Ajouter une ligne</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="ligneSectionIdx">
                <input type="hidden" id="ligneEditIdx">
                <div class="form-group" style="margin-bottom:0.75rem;">
                    <label class="form-label" style="font-size:0.7rem; text-transform:uppercase; color:var(--text-muted);">Code</label>
                    <select id="ligneCode" class="form-control" onchange="onCodeChange()">
                        <option value="">— Choisir un code —</option>
                        <?php foreach ($allCodes as $code => $label): ?>
                        <option value="<?= htmlspecialchars($code) ?>"><?= htmlspecialchars($code) ?> — <?= htmlspecialchars($label) ?></option>
                        <?php endforeach; ?>
                        <option value="__custom">Code personnalisé...</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0.75rem;" id="customCodeGroup" style="display:none;">
                    <label class="form-label" style="font-size:0.7rem; text-transform:uppercase; color:var(--text-muted);">Code personnalisé</label>
                    <input type="text" id="ligneCodeCustom" class="form-control" placeholder="ex: 700">
                </div>
                <div class="form-group" style="margin-bottom:0.75rem;">
                    <label class="form-label" style="font-size:0.7rem; text-transform:uppercase; color:var(--text-muted);">Libellé</label>
                    <input type="text" id="ligneLabel" class="form-control" placeholder="Nom de la ligne">
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.75rem;">
                    <div class="form-group">
                        <label class="form-label" style="font-size:0.7rem; text-transform:uppercase; color:var(--text-muted);">Afficher Base</label>
                        <select id="ligneShowBase" class="form-control">
                            <option value="1">Oui</option>
                            <option value="0" selected>Non</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="font-size:0.7rem; text-transform:uppercase; color:var(--text-muted);">Afficher Taux</label>
                        <select id="ligneShowTaux" class="form-control">
                            <option value="1">Oui</option>
                            <option value="0" selected>Non</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer; font-size:0.85rem;">
                        <input type="checkbox" id="ligneConditionnel"> Ligne conditionnelle (masquée si montant = 0)
                    </label>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid var(--border);">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-success btn-sm" onclick="saveLigne()">Enregistrer</button>
            </div>
        </div>
    </div>
</div>

<script>
var configData = <?= json_encode($cfg, JSON_UNESCAPED_UNICODE) ?>;
if (!configData.sections) configData.sections = [];
var allCodes = <?= json_encode($allCodes, JSON_UNESCAPED_UNICODE) ?>;
var draggedEl = null;

function renderSections() {
    var container = document.getElementById('sectionsContainer');
    var html = '';
    configData.sections.forEach(function(section, si) {
        html += '<div class="card" style="margin-bottom:1rem;" data-section-idx="' + si + '">';
        html += '<div class="card-header" style="display:flex; justify-content:space-between; align-items:center; cursor:move;" draggable="true" ondragstart="dragSection(event,' + si + ')" ondragover="event.preventDefault()" ondrop="dropSection(event,' + si + ')">';
        html += '<div style="display:flex; align-items:center; gap:0.5rem;">';
        html += '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--text-muted)" stroke-width="2" style="cursor:grab;"><circle cx="9" cy="5" r="1"/><circle cx="9" cy="12" r="1"/><circle cx="9" cy="19" r="1"/><circle cx="15" cy="5" r="1"/><circle cx="15" cy="12" r="1"/><circle cx="15" cy="19" r="1"/></svg>';
        html += '<input type="text" value="' + escHtml(section.titre) + '" class="form-control" style="width:300px; font-weight:600;" onchange="configData.sections[' + si + '].titre=this.value;">';
        html += '</div>';
        html += '<div style="display:flex; gap:0.35rem;">';
        html += '<button type="button" class="btn-icon btn-view" title="Basculer aperçu" onclick="toggleSectionPreview(' + si + ')"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button>';
        html += '<button type="button" class="btn-icon btn-delete" title="Supprimer section" onclick="removeSection(' + si + ')"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>';
        html += '</div>';
        html += '</div>';
        html += '<div style="padding:1rem;">';
        html += renderSectionBody(section, si);
        html += '</div></div>';
    });
    container.innerHTML = html;
}

function renderSectionBody(section, si) {
    var html = '';
    html += '<div style="margin-bottom:0.75rem;">';
    html += '<label style="font-size:0.7rem; text-transform:uppercase; color:var(--text-muted); font-weight:600;">Colonnes affichées</label>';
    html += '<div style="display:flex; gap:1rem; margin-top:0.35rem;">';
    var allCols = ['Code', 'Libellé', 'Base', 'Taux', 'Montant'];
    allCols.forEach(function(col) {
        var checked = (section.colonnes || []).indexOf(col) >= 0 ? 'checked' : '';
        html += '<label style="display:flex; align-items:center; gap:0.35rem; cursor:pointer; font-size:0.8rem;"><input type="checkbox" ' + checked + ' onchange="toggleColumn(' + si + ',\'' + col + '\',this.checked)">' + col + '</label>';
    });
    html += '</div></div>';
    html += '<div style="overflow-x:auto;">';
    html += '<table class="data-table" style="font-size:0.8rem;"><thead><tr>';
    html += '<th style="width:30px;">#</th><th>Code</th><th>Libellé</th><th style="text-align:center;">Base</th><th style="text-align:center;">Taux</th><th style="text-align:center;">Conditionnel</th><th style="width:100px; text-align:center;">Actions</th>';
    html += '</tr></thead><tbody>';
    (section.lignes || []).forEach(function(ligne, li) {
        html += '<tr draggable="true" ondragstart="dragLigne(event,' + si + ',' + li + ')" ondragover="event.preventDefault()" ondrop="dropLigne(event,' + si + ',' + li + ')">';
        html += '<td style="color:var(--text-muted); cursor:grab;">⋮⋮</td>';
        html += '<td><code style="font-size:0.75rem; background:var(--bg-primary); padding:0.15rem 0.4rem; border-radius:4px;">' + escHtml(ligne.code) + '</code></td>';
        html += '<td>' + escHtml(ligne.label) + '</td>';
        html += '<td style="text-align:center;">' + (ligne.show_base ? '<span style="color:#22c55e;">✓</span>' : '<span style="color:var(--text-muted);">—</span>') + '</td>';
        html += '<td style="text-align:center;">' + (ligne.show_taux ? '<span style="color:#22c55e;">✓</span>' : '<span style="color:var(--text-muted);">—</span>') + '</td>';
        html += '<td style="text-align:center;">' + (ligne.conditionnel ? '<span style="color:#eab308;">●</span>' : '<span style="color:var(--text-muted);">—</span>') + '</td>';
        html += '<td><div class="table-actions" style="justify-content:center;">';
        html += '<button type="button" class="btn-icon btn-edit" title="Modifier" onclick="editLigne(' + si + ',' + li + ')"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>';
        html += '<button type="button" class="btn-icon btn-delete" title="Supprimer" onclick="removeLigne(' + si + ',' + li + ')"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg></button>';
        html += '</div></td></tr>';
    });
    html += '</tbody></table>';
    html += '</div>';
    html += '<div style="margin-top:0.75rem; display:flex; gap:0.5rem; align-items:center;">';
    html += '<button type="button" class="btn btn-primary btn-sm" onclick="openAddLigne(' + si + ')">+ Ajouter une ligne</button>';
    html += '</div>';
    html += '<div style="margin-top:0.75rem; display:flex; gap:0.75rem; align-items:center; padding-top:0.75rem; border-top:1px solid var(--border);">';
    html += '<label style="font-size:0.7rem; text-transform:uppercase; color:var(--text-muted); font-weight:600;">Total de section :</label>';
    if (section.total) {
        html += '<code style="font-size:0.75rem; background:var(--bg-primary); padding:0.15rem 0.4rem; border-radius:4px;">' + escHtml(section.total.code) + '</code>';
        html += '<input type="text" value="' + escHtml(section.total.label) + '" class="form-control" style="width:250px; font-size:0.8rem;" onchange="configData.sections[' + si + '].total.label=this.value;">';
        html += '<button type="button" class="btn-icon btn-delete" title="Supprimer total" onclick="removeTotal(' + si + ')" style="flex-shrink:0;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>';
    } else {
        html += '<button type="button" class="btn btn-secondary btn-sm" onclick="addTotal(' + si + ')">+ Ajouter un total</button>';
    }
    html += '</div>';
    return html;
}

function addSection() {
    configData.sections.push({
        titre: 'Nouvelle section',
        colonnes: ['Libellé', 'Montant'],
        lignes: [],
        total: null
    });
    renderSections();
}

function removeSection(si) {
    if (!confirm('Supprimer cette section ?')) return;
    configData.sections.splice(si, 1);
    renderSections();
}

function toggleColumn(si, col, checked) {
    var cols = configData.sections[si].colonnes;
    var idx = cols.indexOf(col);
    if (checked && idx < 0) {
        if (col === 'Code') cols.splice(0, 0, col);
        else if (col === 'Libellé') cols.splice(cols.indexOf('Code') >= 0 ? 1 : 0, 0, col);
        else cols.push(col);
    } else if (!checked && idx >= 0) {
        cols.splice(idx, 1);
    }
    renderSections();
}

function openAddLigne(si) {
    document.getElementById('ligneSectionIdx').value = si;
    document.getElementById('ligneEditIdx').value = '-1';
    document.getElementById('ligneModalTitle').textContent = 'Ajouter une ligne';
    document.getElementById('ligneCode').value = '';
    document.getElementById('ligneCodeCustom').value = '';
    document.getElementById('ligneLabel').value = '';
    document.getElementById('ligneShowBase').value = '0';
    document.getElementById('ligneShowTaux').value = '0';
    document.getElementById('ligneConditionnel').checked = false;
    document.getElementById('customCodeGroup').style.display = 'none';
    new bootstrap.Modal(document.getElementById('ligneModal')).show();
}

function editLigne(si, li) {
    var ligne = configData.sections[si].lignes[li];
    document.getElementById('ligneSectionIdx').value = si;
    document.getElementById('ligneEditIdx').value = li;
    document.getElementById('ligneModalTitle').textContent = 'Modifier la ligne';
    var isCustom = !allCodes[ligne.code];
    document.getElementById('ligneCode').value = isCustom ? '__custom' : ligne.code;
    document.getElementById('ligneCodeCustom').value = isCustom ? ligne.code : '';
    document.getElementById('customCodeGroup').style.display = isCustom ? 'block' : 'none';
    document.getElementById('ligneLabel').value = ligne.label;
    document.getElementById('ligneShowBase').value = ligne.show_base ? '1' : '0';
    document.getElementById('ligneShowTaux').value = ligne.show_taux ? '1' : '0';
    document.getElementById('ligneConditionnel').checked = !!ligne.conditionnel;
    new bootstrap.Modal(document.getElementById('ligneModal')).show();
}

function onCodeChange() {
    var sel = document.getElementById('ligneCode').value;
    document.getElementById('customCodeGroup').style.display = sel === '__custom' ? 'block' : 'none';
    if (sel !== '__custom' && sel) {
        var label = allCodes[sel] || '';
        if (!document.getElementById('ligneLabel').value) {
            document.getElementById('ligneLabel').value = label;
        }
    }
}

function saveLigne() {
    var si = parseInt(document.getElementById('ligneSectionIdx').value);
    var li = parseInt(document.getElementById('ligneEditIdx').value);
    var codeSel = document.getElementById('ligneCode').value;
    var code = codeSel === '__custom' ? document.getElementById('ligneCodeCustom').value.trim() : codeSel;
    var label = document.getElementById('ligneLabel').value.trim();
    if (!code || !label) { alert('Code et libellé requis.'); return; }
    var ligne = {
        code: code,
        label: label,
        show_base: document.getElementById('ligneShowBase').value === '1',
        show_taux: document.getElementById('ligneShowTaux').value === '1',
        conditionnel: document.getElementById('ligneConditionnel').checked
    };
    if (li >= 0) {
        configData.sections[si].lignes[li] = ligne;
    } else {
        configData.sections[si].lignes.push(ligne);
    }
    bootstrap.Modal.getInstance(document.getElementById('ligneModal')).hide();
    renderSections();
}

function removeLigne(si, li) {
    configData.sections[si].lignes.splice(li, 1);
    renderSections();
}

function addTotal(si) {
    configData.sections[si].total = { code: 'TOTAL_' + si, label: 'Total' };
    renderSections();
}

function removeTotal(si) {
    configData.sections[si].total = null;
    renderSections();
}

function toggleSectionPreview(si) {
    var card = document.querySelector('[data-section-idx="' + si + '"]');
    if (!card) return;
    var body = card.querySelector('.card-body, div[style*="padding:1rem"]');
    if (!body) return;
    var section = configData.sections[si];
    if (body.dataset.preview === '1') {
        body.innerHTML = renderSectionBody(section, si);
        body.dataset.preview = '0';
    } else {
        var previewHtml = '<div style="font-size:0.8rem; color:var(--text-muted);">';
        previewHtml += '<p style="margin:0 0 0.5rem 0;"><strong>Colonnes :</strong> ' + (section.colonnes || []).join(', ') + '</p>';
        previewHtml += '<table class="data-table" style="font-size:0.75rem;"><thead><tr>';
        (section.colonnes || []).forEach(function(c) { previewHtml += '<th>' + escHtml(c) + '</th>'; });
        previewHtml += '</tr></thead><tbody>';
        (section.lignes || []).forEach(function(l) {
            previewHtml += '<tr><td>' + escHtml(l.code) + '</td><td>' + escHtml(l.label) + '</td>';
            for (var i = 2; i < (section.colonnes || []).length; i++) {
                var cn = section.colonnes[i];
                if (cn === 'Base') previewHtml += '<td style="text-align:right;">' + (l.show_base ? '1 000,00' : '—') + '</td>';
                else if (cn === 'Taux') previewHtml += '<td style="text-align:right;">' + (l.show_taux ? '4,48 %' : '—') + '</td>';
                else previewHtml += '<td style="text-align:right;">448,00</td>';
            }
            previewHtml += '</tr>';
        });
        if (section.total) {
            previewHtml += '<tr style="font-weight:bold; border-top:2px solid var(--accent);"><td colspan="' + (section.colonnes || []).length + '">' + escHtml(section.total.label) + '</td></tr>';
        }
        previewHtml += '</tbody></table></div>';
        body.innerHTML = previewHtml;
        body.dataset.preview = '1';
    }
}

function saveConfig() {
    configData.nom = document.getElementById('cfgNom').value;
    configData.description = document.getElementById('cfgDesc').value;
    configData.couleur_primaire = document.getElementById('cfgCouleur').value;
    configData.net_label = document.getElementById('cfgNetLabel').value;
    configData.net_color = document.getElementById('cfgNetColor').value;
    configData.show_footer = document.getElementById('cfgShowFooter').value === '1';

    var btn = document.getElementById('btnSave');
    btn.disabled = true;
    btn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:0.25rem; vertical-align:middle; animation:spin 1s linear infinite;"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg> Enregistrement...';

    fetch('<?= $baseUrl ?>/<?= $modeleId ?>/config', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(configData)
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        btn.disabled = false;
        btn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:0.25rem; vertical-align:middle;"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg> Enregistrer';
        if (data.success) {
            showToast('Modèle enregistré avec succès.', 'success');
        } else {
            showToast('Erreur : ' + (data.error || 'Inconnue'), 'error');
        }
    })
    .catch(function(err) {
        btn.disabled = false;
        btn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:0.25rem; vertical-align:middle;"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg> Enregistrer';
        showToast('Erreur réseau.', 'error');
    });
}

function escHtml(s) {
    var d = document.createElement('div');
    d.textContent = s || '';
    return d.innerHTML;
}

function showToast(msg, type) {
    var toast = document.createElement('div');
    toast.style.cssText = 'position:fixed; top:1rem; right:1rem; z-index:9999; padding:0.75rem 1.25rem; border-radius:8px; font-size:0.85rem; font-weight:500; color:#fff; box-shadow:0 4px 12px rgba(0,0,0,0.3); transition:opacity 0.3s;';
    toast.style.background = type === 'success' ? '#22c55e' : '#ef4444';
    toast.textContent = msg;
    document.body.appendChild(toast);
    setTimeout(function() { toast.style.opacity = '0'; setTimeout(function() { toast.remove(); }, 300); }, 3000);
}

function dragSection(e, si) { draggedEl = { type: 'section', si: si }; e.dataTransfer.effectAllowed = 'move'; }
function dropSection(e, si) {
    e.preventDefault();
    if (!draggedEl || draggedEl.type !== 'section' || draggedEl.si === si) return;
    var item = configData.sections.splice(draggedEl.si, 1)[0];
    configData.sections.splice(si, 0, item);
    draggedEl = null;
    renderSections();
}
function dragLigne(e, si, li) { draggedEl = { type: 'ligne', si: si, li: li }; e.dataTransfer.effectAllowed = 'move'; e.stopPropagation(); }
function dropLigne(e, si, li) {
    e.preventDefault(); e.stopPropagation();
    if (!draggedEl || draggedEl.type !== 'ligne' || draggedEl.si !== si || draggedEl.li === li) return;
    var item = configData.sections[si].lignes.splice(draggedEl.li, 1)[0];
    configData.sections[si].lignes.splice(li, 0, item);
    draggedEl = null;
    renderSections();
}

document.getElementById('cfgCouleur').addEventListener('input', function() {
    document.getElementById('cfgCouleurHex').value = this.value;
});
document.getElementById('cfgNetColor').addEventListener('input', function() {
    document.getElementById('cfgNetColorHex').value = this.value;
});

renderSections();
</script>

<style>
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>
