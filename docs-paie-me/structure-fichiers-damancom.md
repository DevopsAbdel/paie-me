# Structure des fichiers Damancom – AFFEBD et DS

## 1. Présentation générale

Les fichiers :
- **AFFEBDS** : fichier téléchargé depuis le portail Damancom (lecture / consultation)
- **DS** : fichier généré pour envoi (upload) vers le portail Damancom (écriture)

Ces fichiers sont des fichiers **TXT à positions fixes (fixed width)**.
Chaque ligne commence par un code indiquant le type d'enregistrement.

---

## 2. Structure du fichier AFFEBDS

Fichier source : <File>AFFEBDS_1307492_202401.txt</File>

### Types d’enregistrements :

### 🔹 A00 – En-tête
- Identifiant entreprise
- Période (année + mois)
- Type de déclaration

### 🔹 A01 – Identification employeur
- Nom de l'entreprise
- Adresse
- Ville
- Code postal
- Dates (déclaration, échéance)

### 🔹 A02 – Identification salarié
- Numéro CNSS
- Nom
- Prénom
- Informations complémentaires

### 🔹 A03 – Données affilié
- Numéro salarié
- Identifiant CNSS

---

## 3. Structure du fichier DS (Déclaration de salaire)

Fichier source : <File>DS_1307492_202401.txt</File>

### Types d’enregistrements :

### 🔹 B00 – En-tête
- Identifiant employeur
- Période de déclaration

### 🔹 B01 – Identification employeur
- Nom entreprise
- Adresse complète
- Ville
- Code postal

### 🔹 B02 – Données salarié (principale)
Contient les données les plus importantes :

- Numéro CNSS
- Nom
- Prénom
- Nombre de jours travaillés
- Salaire brut
- Salaire plafonné
- Salaire soumis à cotisation
- Montants CNSS calculés

👉 Exemple extrait :
```text
00000000000000260000000327600000327600
```
- 26 jours
- 3 276,00 salaire brut
- 3 276,00 salaire CNSS

### 🔹 B03 – Données détaillées salarié
- Numéro matricule
- CNSS
- Détails des bases de calcul
- Redondance sécurisée des données (contrôle)

### 🔹 B05 – Ligne vide / padding
- Réservée (souvent remplie de zéros)

### 🔹 B06 – Agrégat salarié
- Récapitulatif des montants
- CNSS total
- Base globale

---

## 4. Logique de structuration

Chaque ligne suit :

```text
[Type ligne][Identifiant][Période][Données fixes formatées]
```

### Exemple :

```text
B021307492202401107070813MOUHIB...
```

- B02 → type ligne salarié
- 1307492 → identifiant employeur
- 202401 → période (janvier 2024)

---

## 5. Règles importantes

- Format **fixed-width (positions fixes)**
- Les montants sont souvent sans séparateur (multipliés par 100)
- Champs numériques remplis avec des zéros à gauche
- Champs texte alignés à gauche avec espaces
- Longueur de ligne fixe

---

## 6. Différences clés AFFEBDS vs DS

| Élément | AFFEBDS | DS |
|--------|--------|----|
| Usage | Lecture | Déclaration (upload) |
| Préfixe | Axx | Bxx |
| Contenu | Données existantes | Données calculées |
| Calculs | Absents | Présents (salaires, cotisations) |

---

## 7. Utilisation dans une application

### ✅ AFFEBDS
- Importer les données existantes
- Vérifier les affiliés

### ✅ DS
- Générer automatiquement depuis Excel / système paie
- Valider avant upload Damancom

---

## Conclusion

Ces fichiers constituent un format normé pour :
- déclarer les salaires
- transmettre les cotisations CNSS
- garantir la conformité automatique avec Damancom

---

Document technique pour intégration Damancom dans une application de paie.
