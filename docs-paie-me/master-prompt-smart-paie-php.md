# 🧠 MASTER PROMPT – Agent AI pour créer une application PHP Smart Paie

## 🎯 Objectif

Créer une application web complète de gestion de paie en **PHP + MySQL**, compatible avec **XAMPP (Windows et macOS)**.

L’application doit être :
- professionnelle
- modulaire
- sécurisée
- orientée SaaS
- avec **mode sombre uniquement (dark mode obligatoire)**

---

# 🧱 Contraintes techniques

- Backend : PHP 8+
- Base de données : MySQL (MariaDB via XAMPP)
- Frontend : HTML5, CSS3, JavaScript
- Serveur : Apache (XAMPP)
- Architecture : MVC simplifiée ou modulaire

### 🔐 Sécurité obligatoire
- PDO (prepared statements)
- Protection SQL injection
- Sessions sécurisées
- Validation des entrées

---

# 🌑 UI / UX (Très important)

Mode sombre uniquement (aucun light mode autorisé).

### 🎨 Couleurs recommandées
- Background : #0f172a
- Surface : #1e293b
- Accent : #3b82f6
- Texte : #e2e8f0

### 🧭 Interface
- Sidebar navigation fixe
- Dashboard moderne
- Responsive (desktop + mobile)

---

# 📦 Modules à développer

## 1. Authentification
- Login / Logout
- Sessions
- Gestion utilisateurs

## 2. Sociétés
- CRUD société
- Infos fiscales
- Comptes téléservices

## 3. Salariés
- CRUD salarié
- Formulaire complet
- Validation des champs

## 4. Paie
- Création période
- Calcul paie automatique

## 5. Bulletin de paie
- Génération HTML
- Export PDF

## 6. Journal de paie
- Liste complète
- Export PDF

## 7. CNSS / Damancom
- Génération fichier DS (TXT fixed width)

## 8. IR (SIMPL)
- Calcul IR
- Export données

## 9. Comptabilité
- Génération écritures comptables

---

# 🗂️ Structure projet recommandée

```text
/smart-paie
├── /config
├── /controllers
├── /models
├── /views
├── /assets
│   ├── css
│   ├── js
│   ├── img
├── /uploads
├── index.php
└── .htaccess
```

---

# 💾 Base de données

Tables :
- users
- societes
- salaries
- periodes
- paies
- bulletins

---

# 🧮 Logique de calcul (Maroc)

```text
CNSS = min(Salaire, 6000) × 4.48%
AMO = Salaire × 2.26%
SNI = Salaire - (CNSS + AMO)
IR = (SNI × taux) - deduction
Net = Salaire - (CNSS + AMO + IR)
```

---

# 🔁 Workflow utilisateur

1. Login
2. Créer société
3. Ajouter salariés
4. Créer période
5. Calcul paie
6. Générer bulletins
7. Générer DS CNSS
8. Déclarer IR

---

# ⚙️ Ce que doit générer l’agent AI

- Structure complète du projet
- Script SQL
- Code PHP (models, controllers, views)
- UI dark mode
- Pages clés :
  - login
  - dashboard
  - salariés
  - paie

---

# 🚀 BONUS attendus

- AJAX
- API JSON
- Notifications
- Logs

---

# 🎯 Instructions finales pour l’agent

- Générer une application complète
- Code propre et modulaire
- Respect strict du mode sombre
- Compatible XAMPP Windows et Mac

Commencer par :
1. Structure projet
2. Base SQL
3. Page login

Puis continuer module par module.

---

Document master prompt pour génération automatique d’application Smart Paie PHP.
