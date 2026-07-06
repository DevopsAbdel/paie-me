# Guide Complet et Pratique du Traitement de la Paie au Maroc (Actualisé Réglementation 2026)
*Inspiré de l'analyse d'un cas réel de bulletin de paie et mis à jour selon la Loi de Finances*

---

## Introduction

Le traitement de la paie est une composante essentielle de la gestion des ressources humaines et de la comptabilité de toute entreprise au Maroc. Il ne s'agit pas d'un simple calcul de rémunération, mais d'un processus réglementé combinant le Code du travail, la législation fiscale (Code Général des Impôts) et les règles de la protection sociale (CNSS).

Ce guide pratique, structuré sous forme de manuel de référence, a pour objectif de détailler chaque étape du calcul de la paie au Maroc, en intégrant les réformes majeures de l'Impôt sur le Revenu (IR) et des charges de famille, tout en s'appuyant sur l'analyse technique du cas de M. NAJI.

---

## 1. Structure Globale d'un Bulletin de Paie : Les Grandes Étapes

Le calcul de la paie suit une logique séquentielle stricte. Chaque indicateur découle du précédent selon une cascade de formules bien définie :

```
[ Salaire de Base ]
       │
       ▼ (+ Primes, + Indemnités)
[ Salaire Brut Global (SBG) ]
       │
       ▼ (- Indemnités exonérées)
[ Salaire Brut Imposable (SBI) ]
       │
       ▼ (- Cotisations Sociales, - Frais Professionnels)
[ Salaire Net Imposable (SNI) ]
       │
       ▼ (Application du barème de l'IR actualisé - Déductions revalorisées)
[ Impôt sur le Revenu (IR Net) ]
       │
       ▼ (SBG - Cotisations - IR Net - Retenues/Avances)
[ Salaire Net à Payer ]
```

---

## 2. Analyse Étape par Étape & Formules de Calcul

### Étape 1 : Le Salaire de Base et l'Ancienneté
Le salaire de base est la rémunération fixée d'un commun accord entre l'employeur et le salarié, soit au taux horaire, soit au forfait mensuel.

* **Cas Pratique (M. NAJI) :**
  * Date d'embauche : 01/01/2008.
  * Statut : Marié, 4 enfants à charge.
  * Base mensuelle : 26 jours de travail.
  * Salaire de Base Mensuel : **13 000,00 DH**.

* **La Prime d'Ancienneté :**
  Selon l'article 350 du Code du Travail marocain, la prime d'ancienneté est obligatoire après 2 ans de service. Le barème est le suivant :
  * 2 ans de service : 5%
  * 5 ans de service : 10%
  * 12 ans de service : 15%
  * 20 ans de service : 20%
  * 25 ans de service : 25%
  
  *Application au cas pratique :* Embauché en 2008, le salarié a plus de 12 ans d'ancienneté. Il bascule donc dans la tranche des 15%.
  $$\text{Prime d'Ancienneté} = \text{Salaire de Base} \times 15\% = 13\,000 \times 0{,}15 = 1\,950{,}00 \text{ DH}$$

### Étape 2 : Le Salaire Brut Global (SBG)
$$\text{SBG} = \text{Salaire de Base} + \text{Primes (Ancienneté, Panier)} + \text{Indemnités}$$

* **Composantes du SBG de M. NAJI :**
  * Salaire de base mensuel : 13 000,00 DH
  * Prime d'ancienneté (15%) : 1 950,00 DH
  * Prime de Panier : 780,00 DH
  * Indemnité de Représentation : 1 000,00 DH
  * Indemnité de Déplacement : 4 500,00 DH
  * Indemnité de Transport : 500,00 DH
  * **Total SBG = 21 730,00 DH**

### Étape 3 : Le Salaire Brut Imposable (SBI)
$$\text{SBI} = \text{SBG} - \text{Éléments exonérés}$$

* **Règles d'exonération :**
  * *Prime de Panier :* Exonérée dans la limite de 30 DH par jour ouvré. Ici, $30 \text{ DH} \times 26 \text{ jours} = 780 \text{ DH}$. La totalité est exonérée.
  * *Indemnités de frais (Déplacement, transport, représentation) :* Sont exonérées car elles correspondent à des remboursements de frais professionnels justifiés (6 000,00 DH).
  
  $$\text{SBI} = 21\,730{,}00 - 780{,}00 - 6\,000{,}00 = 14\,950{,}00 \text{ DH}$$

### Étape 4 : Le Salaire Net Imposable (SNI)
$$\text{SNI} = \text{SBI} - \text{Cotisations Sociales} - \text{Frais Professionnels}$$

* **Détail des Cotisations Sociales (Part Salariale) :**
  1. **CNSS :** 4,48% plafonné à 6 000 DH $\rightarrow 6\,000 \times 4{,}48\% = 268{,}80 \text{ DH}$.
  2. **AMO :** 2,26% non plafonné $\rightarrow 14\,950{,}00 \times 2{,}26\% = 337{,}87 \text{ DH}$.
  3. **CIMR :** Retraite complémentaire contractuelle de 10% $\rightarrow 14\,950{,}00 \times 10\% = 1\,495{,}00 \text{ DH}$.
  4. **Assurance Privée :** Taux de 3,83% sur le SBI $\rightarrow 14\,950{,}00 \times 3{,}83\% = 571{,}84 \text{ DH}$.

* **Déduction pour Frais Professionnels :**
  *(Note réglementaire : Le taux appliqué dans l'exercice d'origine est de 25% avec une retenue de 2 916,70 DH selon l'adaptation locale ou sectorielle de l'outil).*
  $$\text{SNI} = 14\,950{,}00 - (268{,}80 + 337{,}87 + 1\,495{,}00 + 571\,84) - 2\,916{,}70 = 9\,359{,}79 \text{ DH}$$

### Étape 5 : Le Nouvel Impôt sur le Revenu (Barème Actualisé)
C'est à cette étape que la réforme s'applique. Pour un SNI de **9 359,79 DH**, le calcul bascule sur la tranche correspondante du nouveau barème mensuel.

* **Nouveau Barème Mensuel de l'IR :**
  * 0 à 3 333,33 DH : 0% (Somme à déduire : 0 DH)
  * 3 333,34 à 5 000,00 DH : 10% (Somme à déduire : 333,33 DH)
  * 5 000,01 à 6 666,67 DH : 20% (Somme à déduire : 833,33 DH)
  * 6 666,68 à 8 333,33 DH : 30% (Somme à déduire : 1 500,00 DH)
  * **8 333,34 à 15 000,00 DH : 34% (Somme à déduire : 1 833,33 DH)**
  * Au-delà de 15 000,00 DH : 37% (Somme à déduire : 2 283,33 DH)

* **Calcul de l'IR Brut :**
  Le SNI (9 359,79 DH) se situe dans la tranche des **34%**.
  $$\text{IR Brut} = (9\,359{,}79 \times 34\%) - 1\,833{,}33 = 3\,182{,}33 - 1\,833{,}33 = 1\,349{,}00 \text{ DH}$$
  *(On constate une baisse immédiate par rapport à l'ancien IR brut qui était de 1 749,00 DH)*

* **Nouvelle Déduction pour Charges Familiales :**
  Le montant mensuel passe à **41,67 DH par personne à charge** (au lieu de 30 DH).
  * *Cas pratique :* 1 épouse + 4 enfants = 5 charges familiales.
  $$\text{Réduction} = 5 \times 41{,}67 \text{ DH} = 208{,}35 \text{ DH}$$

* **Calcul de l'IR Net à payer :**
  $$\text{IR Net} = \text{IR Brut} - \text{Charges Familiales} = 1\,349{,}00 - 250{,}00 = 1\,099{,}00 \text{ DH}$$

### Étape 6 : Le Salaire Net à Payer Actualisé
$$\text{Salaire Net} = \text{SBG} - \text{Total Cotisations Salariales} - \text{IR Net Nouvelle Formule}$$
$$\text{Salaire Net} = 21\,730{,}00 - 2\,673{,}51 - 1\,099{,}00 = 17\,957{,}49 \text{ DH}$$

* **Gain pour le salarié grâce à la réforme 2026 :** Le salaire net à payer passe de 17 457,49 DH à **17 957,49 DH**, soit un gain de **+500,00 DH / mois**.

---

## 3. Grille Comparative du Nouveau Barème de l'IR

| Tranche Mensuelle (DH) | Taux | Somme à déduire (DH) | Charge de famille unitaire | Max déduction famille |
| :--- | :---: | :---: | :---: | :---: |
| 0 à 3 333,33 | 0% | 0,00 | 50,00 DH / mois | 300,00 DH / mois |
| 3 333,34 à 5 000,00 | 10% | 333,33 | 50,00 DH / mois | 300,00 DH / mois |
| 5 000,01 à 6 666,67 | 20% | 833,33 | 50,00 DH / mois | 300,00 DH / mois |
| 6 666,68 à 8 333,33 | 30% | 1 500,00 | 50,00 DH / mois | 300,00 DH / mois |
| 8 333,34 à 15 000,00 | 34% | 1 833,33 | 50,00 DH / mois | 300,00 DH / mois |
| Au-delà de 15 000,00 | 37% | 2 283,33 | 50,00 DH / mois | 300,00 DH / mois |

---

## 4. Bonnes Pratiques pour la Transition Législative

* **Paramétrage Logiciel :** S'assurer que le taux supérieur de l'IR est plafonné à 37% au lieu de 38% et que le seuil d'exonération démarre bien à 40 000 DH annuel (3 333,33 DH mensuel).
* **Contrôle des Déductions :** Mettre à jour la constante "Charge de famille" à **50,00 DH/mois** (600 MAD/an) dans le module de calcul de l'impôt à la source.
* **Frais Professionnels :** Vérifier que la règle 35% (SBI annuel ≤ 78 000) / 25% plafonné à 2 916,70 DH (SBI annuel > 78 000) est correctement implémentée.