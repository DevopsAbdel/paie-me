# Logique complète de calcul de la paie – Maroc (CNSS / AMO / IR)

## 1. Données d'entrée

- Salaire brut (SB)
- Salaire plafonné CNSS (SP) → max : 6 000 MAD
- Situation familiale
- Nombre d'enfants

---

## 2. Calcul des cotisations salariales

### 2.1 CNSS (part salariale)

Formule :
```excel
CNSS = SP * 4,48%
```

### 2.2 AMO (part salariale)

```excel
AMO = SB * 2,26%
```

---

## 3. Salaire net imposable (SNI)

```excel
SNI = SB - (CNSS + AMO)
```

---

## 4. Calcul de l'impôt sur le revenu (IR)

### Étapes :
1. Déterminer la tranche IR (mensuelle)
2. Appliquer le taux
3. Soustraire la déduction

```excel
IR = (SNI × Taux) - Déduction
```

### Exemple :
- SNI = 6 000
- Tranche : 6 667 → Taux = 20% ; Déduction = 666,67

```excel
IR = (6000 × 20%) - 666,67
IR = 1 200 - 666,67 = 533,33
```

---

## 5. Net à payer

```excel
Net = SB - (CNSS + AMO + IR)
```

---

## ✅ Exemple complet

### 🔢 Données :
- SB = 10 000
- SP = 6 000

### Étape 1 : CNSS
```text
CNSS = 6000 × 4,48% = 268,80
```

### Étape 2 : AMO
```text
AMO = 10000 × 2,26% = 226,00
```

### Étape 3 : SNI
```text
SNI = 10000 - (268,80 + 226,00)
SNI = 9 505,20
```

### Étape 4 : IR
- Tranche : 8 334 → 15 000
- Taux = 34%
- Déduction = 1 433,33

```text
IR = (9505,20 × 34%) - 1433,33
IR = 3 231,77 - 1433,33
IR = 1 798,44
```

### Étape 5 : Net
```text
Net = 10000 - (268,80 + 226,00 + 1798,44)
Net = 7 706,76
```

---

## 6. Cotisations patronales

### CNSS (patronale)
```excel
CNSS_PP = SB × 8,98% + SB × 8%
```

### AMO (patronale)
```excel
AMO_PP = SB × 4,11%
```

---

## 7. Total charges employeur

```excel
TotalCharges = SB + CNSS_PP + AMO_PP
```

---

## ✅ Résumé logique

1. Calculer CNSS et AMO salariales
2. Déterminer SNI
3. Appliquer barème IR
4. Calculer Net à payer
5. Ajouter cotisations patronales

---

Document de référence pour moteur de paie conforme aux règles marocaines.
