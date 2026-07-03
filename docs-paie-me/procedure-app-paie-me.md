# Procédure Smart Paie (Bladi Soft) – Version complète issue du document

## 1. Définition

La paie correspond à la somme d'argent mensuelle versée à une personne en contrepartie du travail effectué.

Smart Paie est un logiciel qui facilite le traitement complet de la paie.

---

# 2. Workflow détaillé

## ✅ ETAPE 01 : Ouverture de la société
- Ouvrir logiciel
- Menu Fichier → Ouvrir
- Choisir la société
- Si inexistante → demander ajout

---

## ✅ ETAPE 02 : Gestion des salariés

### Mode EFI
- Cliquer sur "+ Nouveau"
- Remplir :
  - Etat civil
  - Coordonnées
  - Contrat

### Mode EDI
- Exporter modèle
- Joindre canvas salariés
- Importer

---

## ✅ ETAPE 03 : Période de paie
- Traitement → Période
- Créer nouvelle période
- Clôturer précédente
- Ouvrir

---

## ✅ ETAPE 04 : Ajout salariés
- Traitement & calcul
- Ajouter salariés à la période

---

## ✅ ETAPE 05 : Détails paie

### DETAIL
- Salaire de base
- Nombre de jours / heures
- Congés
- Jours fériés
- Heures supplémentaires (25%, 50%, 100%)

### GAINS
- Panier : 780 MAD
- Transport : 500 MAD
- Représentation : 10% salaire brut

### RETENUES
- Avances

Enregistrer

---

## ✅ ETAPE 06 : Journal de paie
- Export PDF
- Nom : Journal de paie Mois_X-XXXX
- Envoyer au client pour validation

---

## ✅ ETAPE 07 : Déclaration CNSS (Damancom)

### Téléchargement
- Se connecter Damancom
- Choisir période
- Télécharger fichier préétabli (EDI)

### Traitement Smart Paie
- Menu Damancom
- Joindre fichier
- Vérifier : raison sociale, CNSS
- Déclaration principale
- Générer fichier DS

### Dépôt
- Upload DS
- Vérifier indicateurs verts

### Cas entrants
- Ajouter salarié
- Supprimer virgule → ajouter 00
- Exemple : 2786,35 → 278635

### Mode EFI
- Modifier fiche adhérent
- Activer télérèglement

---

## ✅ ETAPE 08 : Télépaiement CNSS
- Accueil
- Sélection période
- Vérifier montant
- Valider paiement

---

## ✅ ETAPE 09 : Sorties

### Bulletins
- Générer PDF
- Envoyer client

### Journal comptable
- Générer écritures
- Transmettre à comptabilité
- Inclure BPC et BDS

---

# 🧾 Déclaration IR (SIMPL)

## Préparation
- Extraire IR depuis journal paie
- Utiliser tableau suivi IR

## Saisie SIMPL
- Connexion DGI
- Menu : Télépaiement retenue à la source

### Saisie montants
- Salaire arrondi à 10 :
  Exemple : 1958.60 → 1960
- IR arrondi à 1 :
  Exemple : 109.78 → 110

## Paiement
- Générer référence
- Valider paiement

### Mode multi canal
- Envoyer référence client
- Relancer pour paiement

---

# ✅ Résumé

1. Ouvrir société
2. Ajouter salariés
3. Créer période
4. Calculer paie
5. Valider journal
6. Déclarer CNSS
7. Payer CNSS
8. Générer bulletins
9. Comptabiliser
10. Déclarer IR

---

# ✅ Bonnes pratiques

- Vérifier données avant déclaration
- Faire valider client
- Contrôler montants
- Archiver fichiers

---

Document officiel structuré pour Smart Paie.
