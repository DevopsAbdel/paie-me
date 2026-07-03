# Paie Me

Application web de gestion de paie marocaine — PHP 8+, MySQL, Dark UI.

Automatise le traitement salarial, la déclaration sociale (CNSS/Damancom, IR/SIMPL) et la gestion administrative des employés selon les normes marocaines.

## Stack

| Layer    | Technologie                  |
| -------- | ---------------------------- |
| Backend  | PHP 8+, PDO, MVC modulaire   |
| Database | MySQL / MariaDB (XAMPP)      |
| Frontend | HTML5, CSS3, JavaScript      |
| Server   | Apache (XAMPP)               |
| UI       | Dark mode strict             |

## Modules

1. **Authentification** — Login/logout, sessions sécurisées
2. **Sociétés** — CRUD + infos fiscales (ICE, IF, RC, TP, CNSS)
3. **Salariés** — CRUD + contrat, indemnités (transport 500 DH, panier 780 DH)
4. **Paie** — Création période + calcul automatique
5. **Bulletins de paie** — HTML + export PDF
6. **CNSS/Damancom** — Génération fichier DS (format fixed-width)
7. **IR/SIMPL** — Calcul IR (barème 2025) + export CSV
8. **Comptabilité** — Écritures comptables

## Calcul paie (Maroc)

```
CNSS = min(salaire, 6000) × 4.48%
AMO  = salaire × 2.26%
SNI  = salaire - (CNSS + AMO)
IR   = (SNI × taux) - déduction (barème progressif 2025)
Net  = salaire - (CNSS + AMO + IR)
```

## Installation

1. Cloner le dépôt dans `htdocs/` de XAMPP
2. Importer `database/schema.sql` dans phpMyAdmin
3. Configurer `config/database.php` (identifiants MySQL)
4. Accéder à `http://localhost/paie-me/`
5. Compte admin : `admin@paie-me.ma` / `admin123`

## Architecture

```
index.php              → point d'entrée
routes.php             → définition des routes
config/                → database.php, app.php
Core/                  → Router, Controller, Model, Session, Helper
controllers/           → logique métier
models/                → modèles (réservé)
views/                 → templates PHP
assets/css/style.css   → design system dark
assets/js/             → scripts JS
uploads/               → fichiers uploadés
database/schema.sql    → structure BDD
```

## opencode

Ce projet utilise opencode avec 4 agents spécialisés :

- **chrome-devtools** — Debug frontend, inspection, Lighthouse
- **sql** — Requêtes, optimisation, schema DB
- **awesome-design** — UI dark mode, CSS, responsive
- **git-cli** — Git, PowerShell, automation

## Conventions

- PDO prepared statements pour toutes les requêtes SQL
- Sessions PHP sécurisées
- Validation des entrées côté serveur
- Code en français (variables, commentaires, vues)
- Noms de tables en minuscules, pluriel
- Clés étrangères avec `ON DELETE CASCADE`
- Dark mode strict uniquement
