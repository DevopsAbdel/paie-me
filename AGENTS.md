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
