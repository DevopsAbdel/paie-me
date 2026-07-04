# Guide Complet du Calcul du Salaire Net au Maroc (Mise à jour 2025/2026)

Ce guide est conçu pour servir de spécification technique et fonctionnelle pour le développement d'un module de paie marocaine au sein d'une application web. Il intègre les derniers barèmes de l'Impôt sur le Revenu (IR), les taux de la CNSS/AMO, ainsi que les nouvelles règles concernant la prime d'ancienneté.

---

## 1. Structure Générale du Calcul du Salaire

Le passage du salaire brut au salaire net à payer suit une séquence d'étapes strictes, régies par le Code du Travail et le Code Général des Impôts (CGI) marocain :

```
[Salaire de Base]
       +
[Primes & Indemnités (Imposables/Exonérées)]
       =
[Salaire Brut Global (SBG)]
       -
[Éléments Exonérés (Frais professionnels, allocations...)]
       =
[Salaire Brut Imposable (SBI)]
       -
[Cotisations Sociales (CNSS, AMO, Retraite)]
       -
[Frais Professionnels (Forfait d'abattement)]
       =
[Salaire Net Imposable (SNI)]
       -
[Impôt sur le Revenu (IR Brut)]
       +
[Déductions pour Charges de Famille]
       =
[Impôt sur le Revenu Net (IR Net)]
       -
[Cotisations Salariales (CNSS, AMO, Retraite)]
       -
[Autres Retenues (Prêts, Avances, Saisies)]
       =
[Salaire Net à Payer]
```

---

## 2. Éléments du Salaire Brut Global (SBG)

### A. Salaire de Base
Calculé à partir du taux horaire (base légale de 191 heures/mois dans le secteur non agricole) ou d'un forfait mensuel.
* **Heures supplémentaires :** Majoration de **25%** (si effectuées entre 6h et 21h les jours ouvrables) ou **50%** (entre 21h et 6h). Majoration de **50%** ou **100%** si effectuées le jour du repos hebdomadaire ou les jours fériés.

### B. La Prime d'Ancienneté (Obligatoire légalement)
*Base de calcul :* Salaire de base + Heures supplémentaires + Primes contractuelles (hors indemnités représentatives de frais, gratifications ou participation aux bénéfices).

**Barème légal actualisé :**
* Après 2 ans de service : **5%**
* Après 5 ans de service : **10%**
* Après 12 ans de service : **15%**
* Après 20 ans de service : **20%**
* Après 25 ans de service : **25%**

### C. Primes et Indemnités Usuelles
Pour le développement de la web app, ces éléments doivent être catégorisés selon leur traitement fiscal (Imposable/Exonéré) et social (Soumis/Non soumis à la CNSS).

| Désignation | Catégorie | Traitement CNSS | Traitement IR (Fisc) | Plafond / Conditions (Règles Maroc) |
| :--- | :--- | :--- | :--- | :--- |
| **Prime de Rendement / Assiduité** | Prime | Soumis | Imposable | Entièrement soumis et imposable. |
| **Allocations Familiales** | Prestation | Non Soumis | Exonéré | Versées directement par la CNSS (hors SBG pour l'IR). |
| **Indemnité de Transport** | Indemnité | Non Soumis | Exonéré | Max **500 DH/mois** en périmètre urbain (**750 DH/mois** hors périmètre). |
| **Prime de Panier / Repas** | Indemnité | Non Soumis | Exonéré | Limite de **30 DH par jour** de travail (dans la limite de 26 jours). |
| **Indemnité de Kilométrage** | Indemnité | Non Soumis | Exonéré | Barème fiscal selon la puissance fiscale du véhicule (justificatifs requis). |
| **Indemnité de Caisse (Manque)** | Indemnité | Non Soumis | Exonéré | Max **150 à 200 DH/mois** selon l'usage habituel admis par le fisc. |
| **Indemnité de Licenciement** | Indemnité | Non Soumis | Exonéré | Dans la limite des plafonds légaux ou conventionnels. |

---

## 3. Le Salaire Brut Imposable (SBI) et Déterminations des Cotisations

Le **Salaire Brut Imposable (SBI)** est égal au : `SBG - Éléments Exonérés (Frais professionnels réels ou forfaitaires justifiés)`.
*Note informatique :* La plupart des indemnités de transport ou panier non justifiées au-delà des plafonds basculent automatiquement dans le SBI.

### Cotisations Sociales Salariales (Taux applicables)

1.  **CNSS (Prestations Sociales) :** **4,48%** applicable sur le salaire brut, plafonné obligatoirement à **6 000 DH** par mois. (Cotisation max : `6000 × 4,48% = 268,80 DH`).
2.  **AMO (Assurance Maladie Obligatoire) :** **2,26%** applicable sur le salaire brut global **sans aucun plafond**.
3.  **CIMR (Retraite Complémentaire - Optionnelle) :** Taux variable choisi par l'entreprise (ex: 3% à 10%) calculé sur une assiette définie par le contrat, généralement le salaire brut.

---

## 4. Calcul du Salaire Net Imposable (SNI)

Le SNI est la base sur laquelle est appliqué le barème de l'IR.

```
SNI = SBI - (Cotisations CNSS + Cotisations AMO + Cotisations Retraite) - Frais Professionnels
```

### Les Frais Professionnels (Abattement Forfaitaire)
* **Taux standard :** **35%** pour les salaires dont le revenu brut imposable ne dépasse pas 78 000 DH par an (soit <= 6 500 DH/mois).
* **Nouveau Taux / Plafond :** **25%** pour les salaires supérieurs à 78 000 DH par an.
* **Plafond mensuel global :** Plafonné rigoureusement à **2 500 DH/mois** (soit 30 000 DH/an).
* *Déduction Logement Économique / Principal :* Sont également déductibles (sous conditions) les intérêts d'emprunt ou le coût d'acquisition pour logement social/principal dans la limite de 10% du SBI.

---

## 5. Nouveau Barème de l'Impôt sur le Revenu (IR)

Voici le barème progressif mensuel mis à jour (issu des récentes réformes fiscales) :

| Tranche de Salaire Mensuel (SNI) | Taux de l'IR | Somme à Déduire |
| :--- | :---: | :--- |
| De 0 à 3 333,33 DH | **0%** | 0,00 DH |
| De 3 333,34 à 4 166,67 DH | **10%** | 333,33 DH |
| De 4 166,68 à 5 000,00 DH | **20%** | 750,00 DH |
| De 5 000,01 à 6 666,67 DH | **30%** | 1 250,00 DH |
| De 6 666,68 à 15 000,00 DH | **34%** | 1 516,67 DH |
| Au-delà de 15 000,00 DH | **38%** | 2 116,67 DH |

**Formule de l'IR Brut :** `(SNI × Taux) - Somme à Déduire`

### Déductions pour charges de famille
Il faut retrancher de l'IR Brut **50 DH par mois** par personne à charge (conjoint, enfants légitimes), dans la limite absolue de 6 personnes (soit un maximum de **300 DH/mois**).
```
IR Net = IR Brut - (Nombre de charges × 50)
```

---

## 6. Exemple de Calcul Pratique (Pas à Pas)

### Profil de l'employé :
* **Statut :** Cadre comptable, 6 ans d'ancienneté dans l'entreprise.
* **Situation familiale :** Marié, 2 enfants à charge (3 charges de famille au total).
* **Salaire de base :** 10 000 DH
* **Indemnités reçues :** 600 DH (Indemnité de transport urbain), 800 DH (Prime de panier pour 26 jours travaillés).

---

### Étape 1 : Calcul de la Prime d'Ancienneté
L'employé a 6 ans d'ancienneté, le taux applicable est donc de **10%**.
* *Assiette d'ancienneté :* Salaire de base = 10 000 DH
* *Montant de la prime d'ancienneté :* `10 000 × 10% = 1 000 DH`

### Étape 2 : Calcul du Salaire Brut Global (SBG)
* `SBG = Salaire de base (10 000) + Prime d'ancienneté (1 000) + Indemnité de transport (600) + Prime de panier (800)`
* **SBG = 12 400,00 DH**

### Étape 3 : Détermination du Salaire Brut Imposable (SBI)
Il faut analyser les exonérations :
* **Transport :** Reçu 600 DH. Plafond exonéré = 500 DH. La différence est imposable : `600 - 500 = 100 DH`.
* **Panier :** Reçu 800 DH. Plafond exonéré = `30 DH × 26 jours = 780 DH`. La différence est imposable : `800 - 780 = 20 DH`.

* `SBI = SBG - Éléments Exonérés (500 + 780)`
* `SBI = 12 400 - 1 280`
* **SBI = 11 120,00 DH**

### Étape 4 : Calcul des Cotisations Sociales
Les cotisations se calculent sur le **SBG** (hors indemnités non soumises selon la doctrine CNSS si justifiées, mais par prudence standard sur le SBI ou SBG réajusté. Ici nous appliquons sur l'assiette brute soumise standard) :
* **CNSS Salariale :** Plafonné à 6 000 DH. `6 000 × 4,48% = 268,80 DH`
* **AMO Salariale :** Pas de plafond. `12 400 × 2,26% = 280,24 DH`
* *Total Cotisations :* `268,80 + 280,24 = 549,04 DH`

### Étape 5 : Calcul des Frais Professionnels
Le salaire annuel dépasse 78 000 DH, on applique le taux de **25%** sur le (SBI - Avantages en nature). Ici pas d'avantages en nature.
* *Calcul théorique :* `11 120,00 × 25% = 2 780,00 DH`
* *Application du plafond mensuel :* 2 780,00 DH dépasse le plafond de 2 500 DH.
* **Frais professionnels retenus = 2 500,00 DH**

### Étape 6 : Calcul du Salaire Net Imposable (SNI)
* `SNI = SBI (11 120,00) - Cotisations Sociales (549,04) - Frais Professionnels (2 500,00)`
* **SNI = 8 070,96 DH**

### Étape 7 : Calcul de l'IR (Brut et Net)
Le SNI de 8 070,96 DH se situe dans la dernière tranche (> 15 000) ? Non, dans la tranche **de 6 666,68 à 15 000,00 DH**.
* *Taux :* 34%
* *Somme à déduire :* 1 516,67 DH
* `IR Brut = (8 070,96 × 34%) - 1 516,67 = 2 744,13 - 1 516,67 = 1 227,46 DH`

*Charges de famille :* Marié + 2 enfants = 3 charges × 50 DH = 150 DH de réduction.
* `IR Net = 1 227,46 - 150,00 = 1 077,46 DH`

### Étape 8 : Calcul du Salaire Net à Payer
```
Salaire Net = SBG - Cotisations Sociales - IR Net
Salaire Net = 12 400,00 - 549,04 - 1 077,46
```
* **Salaire Net à Payer = 10 773,50 DH**

---

## 7. Directives pour les Algorithmes de la Web App

1.  **Gestion des arrondis :** Les intermédiaires de calcul (cotisations, SNI) doivent idéalement conserver 2 décimales. L'IR Net et le Salaire Net final sont souvent arrondis au dirham supérieur ou le plus proche selon les préférences de l'entreprise (ou 2 décimales strictes).
2.  **Règle de proratisation :** Si un employé entre ou sort en cours de mois, le salaire de base, la prime d'ancienneté et les plafonds d'exonérations (panier, transport) doivent être proratisés au prorata des jours travaillés (ex: `Plafond Transport × (Jours travaillés / 26)`).
3.  **Flexibilité de l'assiette CNSS :** Prévoir dans le modèle de données un booléen `is_cnss_subsidized` et `is_ir_taxable` pour chaque élément de prime créé dynamiquement par l'utilisateur.