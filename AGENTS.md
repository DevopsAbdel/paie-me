# Paie Me — Instructions générales

## Projet
Application web de gestion de paie marocaine (PHP 8+ / MySQL / Dark UI).

## Stack
- **Backend** : PHP 8+, PDO, MVC modulaire
- **Base de données** : MySQL (MariaDB via XAMPP)
- **Frontend** : HTML5, CSS3, JavaScript vanilla
- **Serveur** : Apache (XAMPP)
- **UI** : Dark mode uniquement (bg: #0f172a, surface: #1e293b, accent: #3b82f6, text: #e2e8f0)

## Conventions
- Utiliser PDO prepared statements pour toutes les requêtes SQL
- Sessions PHP sécurisées pour l'authentification
- Validation des entrées côté serveur
- Code en français (noms de variables, commentaires, vues)
- Noms de tables en minuscules, pluriel (societes, salaries, paies)
- Clés étrangères avec ON DELETE CASCADE
- Pas de framework JS — vanilla JS uniquement
- Pas de light mode — dark mode strict

## Architecture
```
/index.php            → point d'entrée, autoload manuel
/routes.php           → définition des routes
/config/              → database.php, app.php
/controllers/         → logique métier
/models/              → modèles (réservé)
/views/               → templates PHP
/Core/                → Router, Controller, Model, Session, Helper
/assets/css/          → style.css (design system dark)
/assets/js/           → scripts JS
/uploads/             → fichiers uploadés
/database/            → schema.sql
```

## Règles pour les sous-pages (paramètres et autres)
- **PAS de tabs/conditions** dans une vue PHP. Chaque sous-page = fichier dédié.
- **PAS de barre d'onglets**. Chaque sous-page s'affiche seule, sans navigation horizontale.
- Les sous-pages vont dans un dossier nommé comme la vue principale : `views/societes/parametres/banque.php`, `views/societes/parametres/services.php`, etc.
- Le controller rend la sous-page **directement** (`render('societes/parametres/' . $sous_tab . '.php')`) — pas de fichier `parametres.php` intermédiaire avec des `if/elseif`.
- Le controller passe `$baseUrl` dans les données de la vue pour les liens et formulaires.
- Le titre de la page est défini dynamiquement dans le controller (ex: `"Coordonnées bancaires — " . $societe['raison_sociale']`).
- La navigation entre sous-pages se fait **uniquement** par le menu latéral (sidebar).
- **Cette règle s'applique à toutes les sous-pages existantes et futures.**
- Pour ajouter une nouvelle sous-page :
  1. Créer le fichier `views/societes/parametres/nouvelle.php` avec son contenu complet (pas de tabs)
  2. Ajouter le titre dans le tableau `$titles` dans `SocieteController::parametres()`
  3. Ajouter la route dans `routes.php`
  4. Ajouter le traitement POST dans `SocieteController::parametres()` (si formulaire)
  5. Ajouter le lien dans le sous-menu latéral dans `views/layout.php`

## Modules (ordre de priorité)
1. Authentification (login/logout/sessions)
2. Sociétés (CRUD + infos fiscales)
3. Salariés (CRUD + contrat/indemnités)
4. Paie (création période + calcul automatique)
5. Bulletins de paie (HTML + PDF)
6. CNSS/Damancom (génération fichier DS)
7. IR/SIMPL (export CSV)
8. Comptabilité (écritures comptables)

## Calcul paie (Maroc)
```
CNSS = min(salaire, 6000) × 4.48%
AMO  = salaire × 2.26%
SNI  = salaire - (CNSS + AMO)
IR   = (SNI × taux) - déduction (barème progressif 2025)
Net  = salaire - (CNSS + AMO + IR)
```

## Règles CSS — Tableaux
- **Ne jamais mettre `display: flex` directement sur un `<td>`.** Cela casse le comportement natif `table-cell` et désaligne les bordures de ligne. Toujours utiliser un `<div>` interne :
  ```html
  <td>
      <div class="table-actions">
          <a href="..." class="btn btn-sm">Action</a>
      </div>
  </td>
  ```
- La classe `.table-actions` applique `display: flex; align-items: center; gap: 0.35rem; white-space: nowrap;`.
- Les boutons dans `.table-actions` utilisent `.btn-sm` avec `padding: 0.25rem 0.5rem; font-size: 0.75rem;`.

## Règles CSS — Listes déroulantes (select)
- Toute balise `<select>` **doit** porter la classe `form-control` pour activer la flèche custom (SVG chevron via `background-image` définie dans `select.form-control` à style.css:416).
- La flèche native du navigateur est masquée (`appearance: none`) et remplacée par un chevron SVG gris (`#94a3b8`) positionné à droite.
- **Ne jamais créer un `<select>` sans `class="form-control"`** — sinon la flèche est absente et le select est difficilement distinguishable d'un champ texte en dark mode.
- Exemple correct : `<select name="type" class="form-control" required>...</select>`

## Règles CSS — Date/Time Pickers (dark mode)
- Tout `<input type="date">`, `<input type="time">` et `<input type="datetime-local">` **doit** avoir `color-scheme: dark` pour que le sélecteur natif du navigateur s'affiche en mode sombre.
- Cette règle est déjà appliquée dans `style.css:428` pour toutes les classes (`form-control`, `form-control-inline`, ou sans classe).
- **Ne jamais créer un champ date/time sans la classe `form-control`** (ou `form-control-inline` pour les tableaux) — sinon le picker reste en mode clair.
- **Pattern correct :**
  ```html
  <!-- Champ date dans un formulaire -->
  <input type="date" name="date_effet" class="form-control" value="2026-01-01">

  <!-- Champ date dans un tableau éditable -->
  <input type="date" name="date_effet[]" class="form-control-inline" value="2026-01-01" style="width:140px;">
  ```
- **Pour les modales** : le `color-scheme: dark` est déjà couvert par la règle globale. Aucun style supplémentaire n'est nécessaire dans la modale.
- **Ne pas utiliser** de bibliothèque JS de date picker (flatpickr, datepicker, etc.) — le `<input type="date">` natif suffit en dark mode avec `color-scheme: dark`.

## Règles CSS — Dark Mode Global (controls natifs)
- **Tous les champs de formulaire** (`<input>`, `<select>`, `<textarea>`) doivent être en dark mode natif.
- **Pas de CSS supplémentaire** nécessaire sur chaque champ individuellement — le dark mode est couvert par :
  - `<meta name="color-scheme" content="dark">` dans `<head>` (layout.php)
  - `color-scheme: dark !important` sur `*` dans `style.css`
- **Cela couvre** : dropdowns natifs des `<select>`, date pickers, scrollbars, autocomplétion, focus rings.
- **Règle CSS** : tout `<select>` doit porter `class="form-control"` pour la flèche custom SVG (déjà dans style.css).
- **Custom Select JS** : tout `<select class="form-control">` est automatiquement remplacé par un composant custom dark (`assets/js/custom-select.js` + CSS dans `style.css`). Le composant gère : recherche, navigation clavier, groupes (optgroup), synchronisation avec le `<select>` natif caché pour le form submit.
- **Opt-out** : ajouter `class="no-custom"` pour garder le `<select>` natif (ex: selects inline dans les tableaux).
- **Pattern correct pour les modales** :
  ```html
  <select name="xxx" class="form-control" required>
      <option value="1">Option 1</option>
  </select>
  <input type="date" name="xxx" class="form-control">
  <textarea name="xxx" class="form-control" rows="3"></textarea>
  ```
- **Ne jamais créer** un `<select>` sans `class="form-control"`.
- **Ne jamais créer** un `<input type="date">` sans `class="form-control"`.

## Règles CSS — Grilles de formulaires (hauteur uniforme)
- **Tout `<div class="form-group">` est un flex container** (`display: flex; flex-direction: column` dans `style.css`). Les enfants directs (label + input/select/textarea) s'étirent automatiquement à la hauteur du grid row.
- **Pattern correct pour un grid de formulaire** (2 ou 3 colonnes) :
  ```html
  <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:0.75rem;">
      <div class="form-group">
          <label>Champ 1</label>
          <input type="text" name="champ1" class="form-control" required>
      </div>
      <div class="form-group">
          <label>Champ 2</label>
          <select name="champ2" class="form-control" required>...</select>
      </div>
      <div class="form-group">
          <label>Champ 3</label>
          <input type="date" name="champ3" class="form-control" required>
      </div>
  </div>
  ```
- **Ne jamais définir `height` ou `min-height` manuellement** sur les `.form-group` ou les `.form-control` dans un grid — la hauteur uniforme est assurée par le flexbox + grid auto-row.
- **Le custom select** (`.cs-wrapper`) utilise `display: flex; flex: 1` pour matcher la hauteur des `<input>` natifs. Ne pas modifier ce comportement.
- **Pour un formulaire entier** : retirer `max-width` du `<form>` pour que le grid s'étende sur toute la largeur de la card.

## Règles Modales — Boutons « Ajouter »
- **Tout bouton « Ajouter » doit ouvrir une modale Bootstrap**, jamais un formulaire inline dans le `card-header`.
- Le formulaire d'ajout se trouve dans un `<div class="modal fade" id="...Modal">` avec `modal-dialog-centered`.
- Le bouton déclencheur utilise `data-bs-toggle="modal"` et `data-bs-target="#...Modal"`, ou un `onclick="new bootstrap.Modal(document.getElementById('...Modal')).show()"`.
- **Pattern HTML standard :**
  ```html
  <!-- Bouton -->
  <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#ajoutXxx">
      + Ajouter
  </button>

  <!-- Modal -->
  <div class="modal fade" id="ajoutXxx" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content" style="background:var(--bg-surface); color:var(--text); border:1px solid var(--border); border-radius:12px;">
              <form method="post" action="<?= $baseUrl ?>/xxx">
                  <?= \Core\Session::csrfField() ?>
                  <input type="hidden" name="sous_tab" value="xxx">
                  <div class="modal-header" style="border-bottom:1px solid var(--border);">
                      <h5 class="modal-title">Nouveau(x) xxx</h5>
                      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                      <!-- champs du formulaire -->
                  </div>
                  <div class="modal-footer" style="border-top:1px solid var(--border);">
                      <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                      <button type="submit" class="btn btn-primary btn-sm">Enregistrer</button>
                  </div>
              </form>
          </div>
      </div>
  </div>
  ```
- **Ne jamais laisser un formulaire d'ajout en inline** dans le `card-header` — toujours dans une modale.
- **Exception** : les formulaires de configuration pure (Enregistrer la config, ex: congé annuel, heures sup) restent en inline.
- **Cette règle s'applique à toutes les pages existantes et futures.**
- Pages concernées (checklist) :
  - `baremes/smig_smag.php` — Ajouter bareme SMIG/SMAG → modale
  - `baremes/jours_feries.php` — Ajouter jour férié → modale
  - `parametres/services.php` — Ajouter service + Ajouter fonction → 2 modales
  - `parametres/retenues.php` — Ajouter retenue → modale
  - `parametres/attestations.php` — Ajouter attestation → modale
  - `parametres/gains.php` — déjà modale ✅

## Règles CSS — Icônes d'actions (tableaux)
- **Toute colonne « Actions »** dans un tableau utilise des icônes outlined (stroke-only, pas de fill) via SVG inline.
- Les icônes sont dans un `<div class="table-actions">` pour l'alignement flex.
- **Pas de texte** — uniquement des icônes SVG avec `title="..."` pour le tooltip natif au hover.
- Chaque type d'action a une couleur dédiée via `.btn-icon.btn-{type}` :
  | Classe | Couleur | Usage | Icône SVG |
  |--------|---------|-------|-----------|
  | `btn-view` | `#3b82f6` (bleu) | Voir / détails | Oeil (`eye`) |
  | `btn-edit` | `#eab308` (jaune) | Modifier | Crayon (`edit`) |
  | `btn-delete` | `#ef4444` (rouge) | Supprimer | Corbeille (`trash-2`) |
- Au hover, chaque icône affiche un fond teinté correspondant (ex: `rgba(239,68,68,0.12)` pour delete).
- **Pattern HTML standard :**
  ```html
  <td>
      <div class="table-actions">
          <button type="button" class="btn-icon btn-view" title="Voir les détails" onclick="voirXxx(<?= (int)$x['id'] ?>)">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          </button>
          <button type="button" class="btn-icon btn-edit" title="Modifier" onclick="openModal(<?= (int)$x['id'] ?>)">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
          </button>
          <a href="<?= $baseUrl ?>/xxx?delete_xxx=<?= $x['id'] ?>" class="btn-icon btn-delete" title="Supprimer" onclick="return confirm('Supprimer ?')">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
          </a>
      </div>
  </td>
  ```
- **Ne jamais utiliser** du texte brut (ex: « Supprimer », « Modifier ») dans les colonnes d'actions — toujours des icônes outlined.
- **Ne jamais utiliser** d'icônes avec `fill` (solid) — uniquement `stroke` pour un rendu outlined léger.
- Le `title` est obligatoire pour l'accessibilité et l'info-bulle au survol.

## Encodage UTF-8 — RÈGLE CRITIQUE
- **Tous les fichiers PHP, SQL, CSS, JS** doivent être **sauvés en UTF-8 sans BOM**.
- **Toute donnée contenant des accents français** (`é`, `è`, `ê`, `ë`, `à`, `â`, `ù`, `û`, `ô`, `î`, `ç`, `É`, `È`, etc.) doit être **validée** avant insertion.
- **Import SQL** : utiliser impérativement `--default-character-set=utf8mb4` :
  ```
  mysql -u root --default-character-set=utf8mb4 paie_me < schema.sql
  ```
  Ne JAMAIS utiliser `Set-Content` de PowerShell pour écrire du SQL contenant des accents → utiliser `Out-File -Encoding utf8NoBOM` ou l'éditeur de code.
- **PDO** : le DSN doit toujours contenir `charset=utf8mb4` (déjà fait dans `config/database.php`).
- **HTML** : `<meta charset="UTF-8">` + header PHP `Content-Type: text/html; charset=utf-8` (déjà fait).
- **Corruption connue** : `0xC3 0x9A` au lieu de `0xC3 0xA9` (caractère `é`). Vérifier avec `SELECT id, HEX(nom) FROM services WHERE HEX(nom) LIKE '%C39A%'`.
- **Fix** si données corrompues : `UPDATE table SET col = REPLACE(col, _utf8mb4 0xC39A, _utf8mb4 0xC3A9) WHERE HEX(col) LIKE '%C39A%';`
- Les fichiers PHP et SQL ne doivent **jamais** être ouverts/sauvés avec un éditeur qui utilise l'encodage système Windows (cp850/Windows-1252) par défaut.

## Progress (current session)

### Done
- **Règle CSS select** ajoutée dans AGENTS.md : tout `<select>` doit porter `class="form-control"` pour la flèche custom SVG
- **Fix CSS modal** : `background` → `background-color` dans `.modal-body .form-control` pour préserver la flèche SVG des selects en modal (style.css:728)
- **Barème SMIG & SMAG déplacé** des Paramètres vers les Barèmes (sous-page `smig_smag`)
- **Barèmes 2025 + 2026** insérés dans `bareme_smig_smag` pour les 3 sociétés
- **Modal calcul salaire SMIG/SMAG** ajoutée dans `smig_smag.php` (sélection type + jours travaillés → calcul en temps réel)
- Bouton "Calculer SMIG/SMAG" vert, aligné avec "Enregistrer" dans le pied de carte
- **Indemnités et gains modifiables dans la page d'édition** : 
  - `edit.php` affiche 4 champs indemnités (transport, panier, représentation, logement) éditables + tableau des rubriques de gains avec checkbox/montant
  - `editPaie()` POST sauvegarde indemnités dans `paies` + gains dans nouvelle table `paie_gains`
  - Récupération des overrides dans `calculate()` pour préserver indemnités + gains pendant recalcul
- Créé table `paie_gains` (paie_id, rubrique_id, montant) dans schema.sql + migrate.php
- `calculate()` mémorise et restore les overrides de 4 indemnités + paie_gains + heures_sup pendant DELETE/INSERT
- **Règle Modales** ajoutée dans AGENTS.md : tout bouton "Ajouter" doit ouvrir une modale Bootstrap, jamais de formulaire inline dans le card-header
- **Règle Date/Time Pickers** ajoutée dans AGENTS.md : tous les `<input type="date">` doivent avoir `color-scheme: dark` via la classe `form-control`
- **Fix CSS** : `color-scheme: dark` étendu à tous les date/time inputs dans `style.css:428` (y compris `form-control-inline` et sans classe)
- **Règle Icônes d'actions** ajoutée dans AGENTS.md : colonnes Actions = icônes outlined SVG (stroke-only) avec `title` tooltip au hover, 3 couleurs dédiées (view=bleu, edit=jaune, delete=rouge)
- **5 vues converties** Ajouter → modale : smig_smag, jours_feries, retenues, services (x2), attestations

### Pending
- (none)

### Key changes
| File | Change |
|------|--------|
| `database/migrate.php` | + table paie_gains |
| `database/schema.sql` | + table paie_gains |
| `views/paies/edit.php` | indemnités éditables + gains checkbox/input |
| `controllers/PaieController.php` | editPaie() sauvegarde indemnités+gains ; calculate() préserve 4 indemnités + gains overrides |
