# Paie Me — Instructions générales

## Projet
Application web de gestion de paie marocaine (PHP 8+ / MySQL / Dark UI).

## Stack
- **Backend** : PHP 8+, PDO, MVC modulaire
- **Base de données** : MySQL (MariaDB via XAMPP)
- **Frontend** : HTML5, CSS3, JavaScript vanilla
- **Serveur** : Apache (XAMPP)
- **UI** : Dark mode uniquement — thème **Scrimba Design — Dark Neutre + Violet** (bg: #0b0d12, surface: #14171f, hover: #1e222d, accent: #8b5cf6, text: #e8e9ee, muted: #a3a8b4, border: #232834, icônes actions: view=violet #8b5cf6 / edit=ambre #eab308 / delete=rouge / info=magenta #d946ef). Source : `DESIGN-scrimba-com.md`. Voir la skill globale `scrimba-design` pour le design system complet.

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
  | `btn-info` | `#22d3ee` (cyan) | PDF / CSV / Journal / Export | Fichier (`file`) |
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
- **Règle `<small>` (texte d'aide) dans un grid** : si UN seul champ du grid a un `<small>`, les autres champs doivent aussi avoir un `<small>` (même vide avec `&nbsp;`) pour que la hauteur reste uniforme. Sinon le champ avec `<small>` sera plus grand que les autres.
  ```html
  <div class="form-group">
      <label>Champ avec aide</label>
      <input type="number" name="champ1" class="form-control">
      <small style="color:var(--text-muted); font-size:0.7rem; margin-top:0.25rem; display:block;">Texte d'aide ici</small>
  </div>
  <div class="form-group">
      <label>Champ sans aide</label>
      <input type="text" name="champ2" class="form-control">
      <small style="font-size:0.7rem;">&nbsp;</small>
  </div>
  ```
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

## Règles Import / Export Excel (méthode Odoo)
- **Méthode en 2 phases** : d'abord un **Test** (`importPreview`) qui valide ligne × colonne SANS écrire en base et affiche un rapport d'erreurs ; ensuite un **Commit** (`importCommit`) transactionnel — si une ligne échoue, `ROLLBACK`, rien n'est écrit.
- **`Core/SpreadsheetService.php`** est le service générique réutilisable (namespace `Core`) :
  - `parseFile()` → tableau de lignes (XLSX/XLS via PhpSpreadsheet, CSV avec BOM/Windows-1252/séparateur auto), garde-fou 5000 lignes max.
  - `streamExport()` / `streamTemplate()` → export XLSX ou CSV (BOM), modèle 2 feuilles (Données + Instructions).
  - Normalisations : `normalizeKey()` (casse/accents), `normalizeDate()` (ISO, JJ/MM/AAAA, JJ-MM-AAAA, JJ.MM.AAAA, sérial Excel), `normalizeNumber()` (virgule/point/espaces milliers), `normalizeEnum()`, `matchByName()`.
- **Template** : en-têtes = mêmes libellés que l'export ; les colonnes requises sont marquées ` *` ; une ligne d'exemple est pré-remplie ; feuille « Instructions » = mode d'emploi en 3 étapes + règles + **tableau structuré** des colonnes (Colonne | Requis | Type | Exemple | Valeurs acceptées) avec bordure, ligne requise en rouge, valeurs acceptées issues des listes déroulantes.
- **Listes déroulantes dans le modèle** : les colonnes `enum` (Sexe, Situation, Type de contrat, Type de salaire, Fréquence, Mode de paiement) et les `m2o` (Service, Fonction, Société) ont une **data validation Excel** (`DataValidation::TYPE_LIST`) qui **interdit toute valeur non conforme à la base**. Les valeurs sont écrites dans une feuille cachée « Listes », référencée par `setDataValidation('X2:X2000', ...)`. Sources : enums = **valeurs canoniques `allowed` uniquement** (PAS les variantes accentuées du `labelMap`, sinon doublons visibles dans la liste) ; services/fonctions du référentiel (société active si contexte, sinon toutes, avec `SELECT DISTINCT`) ; sociétés de l'utilisateur (active seule si contexte, avec `DISTINCT`). Piège OOXML : `showDropDown` est **inversé** → appeler `setShowDropDown(true)` pour AFFICHER la flèche (false = masquée) ; utiliser `new DataValidation()` (pas `getDataValidation()`) pour éviter les doublons dans le XML. La ligne d'exemple et la feuille « Instructions » doivent utiliser les formes canoniques (ex: `marie`, pas `marié`).
- **Mapping** par en-tête normalisé (`normalizeKey`) — insensible à la casse et aux accents ; les colonnes inconnues sont **ignorées** avec un avertissement (jamais d'erreur bloquante) ; les colonnes requises manquantes → erreur.
- **Champs enum** : valeurs acceptées en clair (ex: `M`, `F`, `marie`) avec `labelMap` pour tolérer les libellés longs ; `normalizeEnum` gère casse/accents.
- **Champs m2o** (service, fonction, société) : résolution par nom via `buildSocieteLookups()` (index par `normalizeKey`), la société cible = colonne Société ou contexte courant.
- **Matricule vide** : auto-généré (`SAL0001`, `TMS0007`…) à partir du préfixe du matricule max existant — l'aperçu montre déjà le matricule qui sera attribué.
- **Doublons** : matricule dupliqué dans le fichier OU déjà présent en base → erreur.
- **Valeur vide pour un champ chiffré** (cin/rib) → stockée `NULL` (pas de chiffrement d'une chaîne vide).
- **Chiffrement** : `cin` et `rib` passent par `Crypto::encrypt()` à l'INSERT ; `Crypto::tryDecrypt()` (best-effort) à l'export pour tolérer les valeurs en clair (seed démo). Le colonne `salaries.cin` est `VARCHAR(255)` (chiffré = 56 caractères ; rib = 80).
- **Piège PHP 8.2+** : `DateTime::getLastErrors()` retourne `false` quand il n'y a aucune erreur → traiter `false` comme « 0 erreur », sinon toute date est rejetée.
- **Routes** : les routes `/salaries/export`, `/salaries/import/modele`, `/salaries/import/preview`, `/salaries/import` doivent être déclarées AVANT `/salaries/{id}/...`.
- **Vues** : modale d'upload (`_import_ui.php`, CSRF + enctype multipart), rapport Odoo-style (`import_result.php` : stats Lignes/Valides/Erreurs + tableau erreurs ou aperçu des lignes + bouton « Importer N salarié(s) »).
- **Rapport d'erreurs** : un seul message par champ — si le format est invalide, ne pas aussi reporter « requis manquant » (liste `$invalidFields` par ligne).

## Règles — Salariés sortants
- **Sortie ≠ suppression** : un salarié qui quitte la société est marqué `actif = 0` (UPDATE, jamais DELETE) — son historique (paies, congés, STC) reste intact.
- **Colonnes** : `salaries.date_sortie` (DATE nullable) et `salaries.motif_sortie` (VARCHAR(100) nullable, ex: Démission, Licenciement, Fin de CDD, Retraite…).
- **Action « Sortir »** : bouton icône dans les listes de salariés actifs (`views/salaries/index.php` et `views/societes/salaries_list.php`) → modale partagée `views/salaries/_sortie_modal.php` (date obligatoire + motif optionnel). POST sur `/salaries/{id}/sortir` → `SalarieController::sortir()` : UPDATE `date_sortie`, `motif_sortie`, `actif = 0` + Audit + flash.
- **Section « Salariés sortants »** : page dédiée `views/salaries/sortants.php` (GET `/salaries/sortants`, méthode `SalarieController::sortants()`) — affiche **toutes les infos** (matricule, CIN, CNSS, poste, société, salaire, dates, motif sortie en badge rouge) avec actions STC / Modifier / **Réintégrer** (POST `/salaries/{id}/reintegrer` → `actif = 1` + efface `date_sortie`/`motif_sortie`) / Supprimer définitivement.
- **Filtre actifs** : `index()` et `SocieteController::salaries()` ne listent QUE `actif = 1` ; la liste index affiche un bouton « Sortants (N) » quand N > 0.
- **Sidebar** : sous-menu « Salariés » → « Salariés sortants » (visible quand l'URI contient `/salaries`), affiché dans le menu latéral en contexte société.
- **Calcul paie** : `PaieCalculator` applique déjà le prorata du mois de sortie via `date_sortie` (Core/PaieCalculator.php:54) ; les sortants ne sont pas inclus dans les nouvelles paies (`actif = 1`).
- **Routes** : les routes `/salaries/sortants`, `/salaries/{id}/sortir`, `/salaries/{id}/reintegrer` doivent être déclarées AVANT `/salaries/{id}/...`.

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
- **Changement TOTAL de palette vers Dark Neutre + Violet** — palette finale dans `style.css` `:root` : bg `#0b0d12`, surface `#14171f`, hover `#1e222d`, élevé `#252a38`, **accent violet `#8b5cf6`**, hover `#a78bfa`, text `#e8e9ee`, muted `#a3a8b4`, border `#232834`, border-strong `#3a4150`
- **Bouton primaire violet plein** `#8b5cf6` + texte blanc, hover `#a78bfa`, radius 6px ; secondaire = surface + bordure ardoise + texte clair
- **Toutes les teintes bleu/cyan/navy supprimées** : `#1E90FF`, `#3b82f6`, `#3abff8`, `#22d3ee`, `#06b6d4`, `#1e293b`, `#334155`, `rgba(30,144,255,…)`, `rgba(58,191,248,…)` → violet/magenta dans l'UI écran
- **Icônes d'actions recolorées** : view=violet `#8b5cf6`, edit=ambre `#eab308`, delete=rouge `#ef4444`, info=magenta `#d946ef` (style.css `.btn-icon`)
- **Badges** : `badge-info` → magenta `#d946ef` ; badge MODE DÉMO (layout.php) → violet ; `sources_legales.php` borders loi→violet, arrêté→magenta
- **Tints vues** : `edit.php`, `lignes.php`, `bcp.php` → `rgba(139,92,246,…)` ; tooltip info (`edit.php`) → `var(--bg-surface)`/`var(--text)`
- **Typographie Scrimba** : headings weight **800** (topbar h1 2rem/800, card-header h3 800, stat-values 800), body 400, system-ui ; main-content padding 2.5rem ; radius boutons 6px / cards 12px
- **Nav active** : bordure gauche 3px **violet `#8b5cf6`** sur fond `--bg-hover`
- **Skill `scrimba-design` mise à jour** (`~/.claude/skills/scrimba-design/SKILL.md`) : palette graphite+violet, icônes recolorées, interdiction du bleu/cyan
- **`DESIGN-scrimba-com.md`** : note « APPLIQUÉ (dark neutre) » mise à jour (thème light source, transposé dark graphite+violet)
- **AGENTS.md** ligne UI mise à jour (palette finale + icônes actions + source `DESIGN-scrimba-com.md`)
- **Fichiers PDF/print inchangés** (encre sombre papier) : `bulletins/pdf.php`, `modeles_bulletins/preview.php`, `conges/attestation*.php`, `modeles_bulletins/editor.php` (`$couleur` par société)
- **Vérifié navigateur** : body `rgb(11,13,18)`, accent `#8b5cf6`, bouton primaire violet/texte blanc, icônes view violet + edit ambre, stat-values violettes
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
- **Bouton « Mode démo »** sur la page de login : entre en mode démo en créant/initialisant une base séparée `paie_me_demo` (schéma + seed) si elle n'existe pas, connecte l'admin, pose le flag session `demo_mode`
- **Bascule de base par session** : `Model::db()` utilise `paie_me_demo` quand `demo_mode` est actif ; `Model::resetDb()` réinitialise le singleton PDO
- **seed_demo.php refactorisé** : logique extraite dans `seed_demo_database(PDO $pdo): array` (retourne des stats, sans echo/exit) ; usage CLI : `php database/seed_demo.php [dbname]`
- **database/create_demo.php** : `create_demo_database(): PDO` crée la base démo si absente (CREATE DATABASE + import schema.sql avec `paie_me` → `paie_me_demo` + seed) ; idempotent (skip si sociétés existantes, n'importe le schéma que si table `users` absente)
- **Badge « MODE DÉMO »** ajouté dans la topbar (layout.php) quand `demo_mode` est actif
- **Barème de référence (base admin)** ajouté dans la page Barèmes :
  - Sous-page `reference` : SMIG/SMAG (édition ligne par ligne), ancienneté, heures sup + note « barème IR déjà global »
  - `societe_id` rendu **nullable** sur `bareme_smig_smag`, `bareme_anciennete`, `bareme_heures_sup` → lignes `societe_id IS NULL` = barème de référence
  - Seed de référence (12 lignes SMIG/SMAG 2021-2026, 7 tranches ancienneté, 1 ligne heures sup) dans migrate.php
  - Bouton « Appliquer à toutes les sociétés » (admin) : propage référence → toutes les sociétés (SMIG/SMAG = ON DUPLICATE KEY UPDATE, ancienneté = delete+insert, heures sup = upsert)
  - Enregistrement par section (save) : delete+insert des lignes de référence
  - Réservé au rôle `admin` (`Session::get('user_role')`), lien visible dans le sous-menu Barèmes uniquement pour l'admin
- **7e demande** : flèche de collapse de la sidebar (bouton + persistance localStorage `paieSidebarCollapsed`) — `assets/js/sidebar.js` + classe `body.sidebar-collapsed` (72px)
- **Tri sur toutes les tables** : `assets/js/table-tools.js` — tri clic avec indicateur ▲/▼, détection numérique/date (DD/MM/YYYY, MM/YYYY, ISO), colonne Actions et headers vides exclus
- **Filtres sur les tables** : barre de filtre au-dessus de chaque table (`.table-toolbar`), insensible aux accents/casse, ligne « Aucun résultat », appliqué après tri (l'ordre trié est conservé)
- **Protection des grilles éditables** : table-tools **désactive le tri** (garde le filtre) sur les tables contenant des champs visibles (`input:not([type="hidden"]), select, textarea` dans le tbody) pour ne pas casser l'appariement des `name="xxx[]"` au POST (baremes/reference, smig_smag…) ; les `input hidden` (CSRF des formulaires d'action) ne bloquent pas le tri
- **Page d'administration** (`/admin`, réservée `admin`) :
  - Carte **Bases de données** : statut des 2 bases (existence, nb tables, nb sociétés, taille, base « En cours »), boutons « + Créer / réinitialiser la démo » (drop+recreate), « Vider et re-seeder la démo », « Appliquer les migrations » (sur les 2 bases)
  - Carte **Utilisateurs** : liste (nom, email, rôle, statut, créé le), modale d'ajout (nom/email/mdp ≥6/rôle), toggle actif/désactivé, suppression — avec CSRF, interdiction de se désactiver/se supprimer soi-même, refus de supprimer le dernier admin actif
- **`database/create_demo.php`** : après le seed, applique aussi `migrate.php` (capturé via `ob_start`) pour aligner le schéma démo sur la base principale (ex: table `droit_conge` présente dans migrate.php mais absente de schema.sql)
- **Vérifié navigateur** : collapse (72px + persistance après reload), admin (2 bases à 30 tables, « En cours »), CRUD utilisateur complet (création/toggle/suppression + flash), migrations + réinitialisation démo via l'UI, tri numérique « Net à payer » (asc/desc corrects), barème de référence inchangé après migration, accès non-admin à `/admin` redirigé vers `/dashboard` + lien sidebar masqué pour un gestionnaire
- **Fix crash « utilisateur supprimé mais encore connecté »** :
  - Cause : suppression d'un utilisateur via la page admin alors que sa session était active → le logout (ou toute action) échouait sur la FK `audit_log.user_id` / `societes.user_id` (fatal PDOException)
  - Fix 1 (racine) : `index.php` vérifie après `Session::start()` que l'utilisateur de session existe toujours et est `actif = 1` (via `Core\Model::db()`, qui respecte `demo_mode`) ; sinon `Session::destroy()` + redirect `/login` — une session orpheline ne survit jamais à une suppression/désactivation
  - Fix 2 (défense en profondeur) : `Core\Audit::log()` capture les `PDOException` (journalisation best-effort, ne bloque jamais l'application)
- **Fix doublons « Articles par source » (Sources légales)** :
  - Cause : `schema.sql` insérait l'ancien jeu de codes (`PRIME_*` + 330–377 sans `source`), puis `migrate.php` ré-insérait le jeu canonique (501–505 + 330–377 avec `source`) via `INSERT IGNORE` — sans contrainte UNIQUE sur `code`, l'IGNORE ne dédupliquait pas → 43 codes ×2 + 5 `PRIME_*`
  - `migrate.php` : bloc de **fusion des doublons** (après le seed des articles) — garde **une seule ligne par code** (la plus petite `id`, indépendamment de `source`), remappe les références enfants (`rubrique_sources_articles`, `salarie_gains`, `paie_gains`) via DELETE-sans-doublon + UPDATE puis DELETE des lignes excédentaires ; `PRIME_*` → 501–505 via `$primeMap`
  - Complément des articles manquants pour les rubriques canoniques en `INSERT IGNORE` (idempotent) → 90 articles au total (80 remappés + 10 pour 501–505)
  - `schema.sql` : seed `rubriques_gains` réécrit avec le jeu canonique (18 colonnes : `compte` 6 chiffres, `source`, `source_maj`, `nature_edi`, `base_anciennete`, `au_prorata`) — plus aucun `PRIME_*`
  - Vérifié sur `paie_me` + `paie_me_demo` : 48 rubriques globales, 0 doublon par code, 0 `PRIME_*`, 90 articles, aucun FK orphelin
- **Gestion des salariés sortants** (sortie ≠ suppression) :
  - Colonne `salaries.motif_sortie VARCHAR(100) DEFAULT NULL` (après `date_sortie`) ajoutée dans `schema.sql` + `migrate.php` (migrations relancées sur `paie_me` + `paie_me_demo`)
  - `SalarieController` : `index()` filtre `actif = 1` + compte `nbSortants` ; méthodes `sortants()` (liste actif=0 avec JOIN société/fonction/service + décrypt CIN/RIB), `sortir()` (POST, date obligatoire + motif optionnel, UPDATE + Audit + flash, redirect referer), `reintegrer()` (POST, efface date/motif, `actif = 1`, Audit)
  - Routes : `GET /salaries/sortants`, `POST /salaries/{id}/sortir`, `POST /salaries/{id}/reintegrer` (avant `/salaries/{id}/...`)
  - Vues : `views/salaries/sortants.php` (nouvelle, toutes les infos + badge rouge motif + actions STC/Modifier/Réintégrer/Supprimer) ; `views/salaries/_sortie_modal.php` (nouvelle, modale partagée date+motif) ; `index.php` + `views/societes/salaries_list.php` (bouton « Sortants (N) » + action Sortir + include modale) ; `layout.php` sous-menu sidebar « Salariés sortants » (visible si URI contient `/salaries`)
  - Vérifié navigateur : sortie depuis liste globale + contexte société (redirect referer, flash, disparition liste active, badge), page sortants (motif « Décès » affiché), réintégration (retour effectifs actifs + liste vide), sous-menu sidebar présent

### Pending
- (none)

### Key changes
| File | Change |
|------|--------|
| `Core/Model.php` | `db()` bascule sur `paie_me_demo` si `demo_mode` ; + `demoDbName()` + `resetDb()` |
| `database/seed_demo.php` | logique extraite dans `seed_demo_database(PDO): array` (sans echo/exit) |
| `database/create_demo.php` | nouveau : crée/initialise la base démo (schéma + seed) |
| `controllers/AuthController.php` | + `demo()` : crée la base démo, connecte admin, pose `demo_mode` |
| `routes.php` | + route `GET /demo` |
| `views/login.php` | + lien « Entrer en mode démo » |
| `views/layout.php` | + badge « MODE DÉMO » dans la topbar |
| `database/migrate.php` | `societe_id` nullable sur 3 tables baremes + seed du barème de référence (SMIG/SMAG, ancienneté, heures sup) |
| `database/schema.sql` | 3 CREATE TABLE baremes : `societe_id INT UNSIGNED DEFAULT NULL` |
| `controllers/SocieteController.php` | sous-tab `reference` : `handleReferencePost()` (save sections + apply propagation admin) ; GET charge `refSmigSmag`/`refAnciennete`/`refHeuresSup`/`isAdmin` |
| `views/societes/baremes/reference.php` | nouveau : édition du barème de référence + bouton « Appliquer à toutes les sociétés » |
| `views/layout.php` | + lien « Barème de référence » dans le sous-menu Barèmes (admin uniquement) |
| `database/migrate.php` | refactor : boucle sur `paie_me` + `paie_me_demo` (crée la base + importe `schema.sql` si `users` absente) ; helpers `colExists`/`addCol` hors boucle |
| `assets/js/sidebar.js` | nouveau : collapse sidebar + persistance localStorage `paieSidebarCollapsed` |
| `assets/js/table-tools.js` | nouveau : tri + filtre auto sur toutes les tables (opt-out `data-table-tools="off"`), tri désactivé sur les grilles éditables |
| `assets/css/style.css` | nouveaux blocs : `.sidebar-collapse-btn`, `body.sidebar-collapsed`, `.table-toolbar`, `.table-filter-input`, `.sort-ind`, `th.sortable` |
| `controllers/AdminController.php` | nouveau : page admin (bases de données + utilisateurs) avec CSRF et garde-fous |
| `views/admin/index.php` | nouveau : 2 cartes (Bases + Utilisateurs) + modale d'ajout utilisateur |
| `views/layout.php` | + bouton collapse, + lien Administration (admin), includes `sidebar.js` + `table-tools.js` |
| `routes.php` | + `use Controllers\AdminController` + `GET`/`POST /admin` |
| `index.php` | + contrôle d'intégrité de session (utilisateur supprimé/désactivé → session détruite + redirect login) |
| `Core/Audit.php` | `log()` best-effort : les `PDOException` sont capturées (jamais de fatal sur la journalisation) |
| `database/migrate.php` | + fusion des doublons de `rubriques_gains` (remap enfants → ligne canonique + DELETE) ; seed articles passé en `INSERT IGNORE` idempotent ; complément articles 501–505 |
| `database/schema.sql` | seed `rubriques_gains` = jeu canonique (501–505 + 330–377 avec `source`/`compte` 6 chiffres/`nature_edi`) au lieu de `PRIME_*` + codes sans source |
| `database/schema.sql` + `database/migrate.php` | + colonne `salaries.motif_sortie VARCHAR(100) DEFAULT NULL` (après `date_sortie`) |
| `controllers/SalarieController.php` | `index()` filtre `actif = 1` + `nbSortants` ; + `sortants()`, `sortir()`, `reintegrer()` |
| `routes.php` | + `GET /salaries/sortants`, `POST /salaries/{id}/sortir`, `POST /salaries/{id}/reintegrer` |
| `views/salaries/sortants.php` | nouveau : section « Salariés sortants » (toutes infos + badge motif rouge + STC/Modifier/Réintégrer/Supprimer) |
| `views/salaries/_sortie_modal.php` | nouveau : modale partagée « Sortir de la société » (date + motif, CSRF) |
| `views/salaries/index.php` + `views/societes/salaries_list.php` | bouton « Sortants (N) » + action « Sortir de la société » (icône) + include modale |
| `views/layout.php` | sous-menu sidebar « Salariés sortants » sous « Salariés » |
