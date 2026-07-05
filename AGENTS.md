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
