# Blueprint Technique et Fonctionnel : Moteur de Paie Marocain (Enterprise-Grade)
## Spécifications d'Architecture et de Logique Métier pour Application Web SaaS

Ce document constitue le cahier des charges technique étendu pour le développement d'un moteur de paie (Payroll Engine) 100 % conforme aux exigences légales, fiscales, sociales et comptables au Maroc.

---

## 1. Architecture des Données : Modèle Relationnel Multi-Tenant

Pour garantir l'isolement des données en mode SaaS et permettre la rétroactivité des calculs sans altérer l'historique, l'architecture de la base de données doit suivre une logique stricte de découplage et d'historisation.

### Schéma Conceptuel des Tables Étoiles

```
[Entreprise] 1 --- * [Établissement] 1 --- * [Département]
    |
    +--- * [Profil_Législatif_Annee] (Variables fiscales/sociales de l'année)
    |
    +--- * [Salarié] 1 --- * [Contrat] 1 --- * [Bulletin_Mensuel]
                               |                  |
                         [Rubriques_Fixes]   [Lignes_Bulletin]
```

### Dictionnaire des Tables Stratégiques

#### A. Table `Profil_Législatif_Annee`
Centralise les constantes dictées par la Loi de Finances et les décrets d'application.
*   `annee` (Int, Clé primaire)
*   `plafond_cnss_mensuel` (Decimal, ex: 6000.00)
*   `taux_cnss_salarial` (Decimal)
*   `taux_cnss_patronal` (Decimal)
*   `taux_amo_salarial` (Decimal)
*   `taux_amo_patronal` (Decimal)
*   `taux_frais_professionnels_standard` (Decimal)
*   `plafond_frais_professionnels_mensuel` (Decimal)

#### B. Table `Rubriques_Paie`
Dictionnaire dynamique des éléments de gains et de retenues.
*   `code_rubrique` (Varchar, ex: `R102` pour Prime de Panier)
*   `libelle` (Varchar)
*   `est_imposable` (Boolean)
*   `est_cotisable` (Boolean)
*   `est_exonere_plafonne` (Boolean)
*   `plafond_reglementaire` (Decimal)

#### C. Table `Contrat`
*   `id_contrat` (UUID, Clé primaire)
*   `type_contrat` (Enum: `CDI`, `CDD`, `ANAPEC`, `Stagiaire`)
*   `date_entree` (Date)
*   `date_sortie` (Date, Nullable)
*   `salaire_base` (Decimal)
*   `categorie_professionnelle` (Enum: `Standard`, `VRP`, `Journaliste`, `Personnel_Navigant`)

---

## 2. Le Pipeline Algorithmique du Moteur de Paie

Le calcul d'un bulletin de paie s'exécute selon une séquence d'étapes ordonnées. L'ordre est immuable car chaque étape génère l'assiette de calcul de la suivante.

```
[Étape 1: Salaire Brut de Base] -> [Étape 2: Salaire Brut Global] -> [Étape 3: Assiette CNSS/AMO] 
                                                                            |
[Étape 8: Salaire Net à Payer] <- [Étape 7: Calcul IR] <- [Étape 6: Calcul du SNI] <-+
```

### Description Détaillée du Pipeline (Pseudo-Code / Logique)

#### Étape 1 : Salaire Brut de Base
Calculé à partir du salaire contractuel ajusté des absences (règle du 1/30ème) et majoré des Heures Supplémentaires (HS).
*   *Calcul des HS :* `Heures_Sup * Taux_Horaire_Base * (1 + Pourcentage_Majoration)`
*   *Majorations légales (Art. 201 Code du Travail) :* +25% ou +50% en journée, +50% ou +100% de nuit ou jour de repos hebdomadaire.

#### Étape 2 : Salaire Brut Global (SBG)
`SBG = Salaire Brut de Base + Primes + Indemnités + Avantages (Nature/Cash) + Prime d'Ancienneté`
*   *Calcul Automatique de l'Ancienneté (Art. 350) :* Se base sur la `date_entree` du contrat.
    *   2 ans d'ancienneté : 5% du salaire de base et des éléments constants.
    *   5 ans : 10% | 12 ans : 15% | 20 ans : 20% | 25 ans : 25%.

#### Étape 3 : Salaire Brut Cotisable (SBC)
Définition de l'assiette pour les charges sociales.
*   `SBC = SBG - Indemnités Exonérées de CNSS` (Ex: Prime de transport ou de panier dans la limite des plafonds définis par la note conjointe DGI-CNSS).

#### Étape 4 : Cotisations Sociales Salariales
Calcul des retenues obligatoires.
*   `Retenue_CNSS = Min(SBC, Profil_Législatif.plafond_cnss_mensuel) * Profil_Législatif.taux_cnss_salarial`
*   `Retenue_AMO = SBC * Profil_Législatif.taux_amo_salarial` (Non plafonné).
*   `Retenue_CIMR = Assiette_CIMR * Taux_Option_Entreprise` (Retraite complémentaire).

#### Étape 5 : Salaire Brut Imposable (SBI)
`SBI = SBG - Indemnités Exonérées d'IR` (Note conjointe DGI-CNSS).

#### Étape 6 : Salaire Net Imposable (SNI)
Détermination de l'assiette de l'Impôt sur le Revenu.
*   `Frais_Professionnels = (SBI - Avantages en nature) * Taux_Frais_Pro_Categorie`
*   `Frais_Professionnels_Ajustes = Min(Frais_Professionnels, Plafond_Frais_Pro_Mensuel)`
*   `SNI = SBI - Total_Cotisations_Sociales_Salariales - Frais_Professionnels_Ajustes`

#### Étape 7 : Calcul de l'Impôt sur le Revenu (IR Net)
*   `IR_Brut = (SNI * Taux_Tranche_Barème) - Somme_à_Déduire` (Selon le barème progressif de l'IR de la Loi de Finances en vigueur).
*   `IR_Net = IR_Brut - (Nombre_Personnes_Charge * 40 DH)` (Dans la limite absolue de 240 DH soit 6 personnes).

#### Étape 8 : Salaire Net à Payer
`Net_à_Payer = SBG - Total_Cotisations_Salariales - IR_Net - Avances_Sur_Salaire - Quotite_Saisissable`

---

## 3. Gestion des Cas Limites et Robustesse Métier

### A. Le Solde de Tout Compte (STC) - Indemnité de Licenciement
Lorsqu'un contrat prend fin, le moteur doit implémenter les barèmes de l'Article 53 du Code du Travail pour calculer l'Indemnité Légale de Licenciement (CDI, après 6 mois de présence) :

| Ancienneté Cumulée | Droits d'Indemnisation par Année (ou fraction d'année) |
| :--- | :--- |
| **Les 5 premières années** | 96 heures de salaire par an |
| **De la 6ème à la 10ème année** | 144 heures de salaire par an |
| **De la 11ème à la 15ème année** | 192 heures de salaire par an |
| **Au-delà de la 15ème année** | 240 heures de salaire par an |

*   *Règle de proratisation fiscale :* L'indemnité légale calculée est exonérée de cotisations CNSS et d'IR. Tout surplus négocié (indemnité supra-légale) doit être réintégré dans le SBI et soumis à l'IR, sauf protocole d'accord validé par l'inspecteur du travail ou tribunal compétent.

### B. La Quotité Saisissable (Saisie-Arrêt)
En cas de notification de saisie sur salaire (créanciers, pensions), le moteur doit segmenter le salaire Net selon l'Article 488 du Code de Procédure Civile marocain.
*   L'algorithme doit appliquer des pourcentages progressifs par tranches du salaire (1/20ème, 1/10ème, 1/5ème, 1/4, 1/3, jusqu'à la moitié) afin de sanctuariser une part insaisissable pour la subsistance du salarié.

### C. Le Régime Spécifique ANAPEC (Insertion Professionnelle)
Pour les contrats ANAPEC conformes à la Loi 16-04 :
*   Exonération totale des cotisations CNSS et de la Taxe de Formation Professionnelle (TFP).
*   Exonération d'IR dans la limite des plafonds réglementaires.
*   Maintien du calcul et du reversement de la part AMO selon le type de programme étatique souscrit.

---

## 4. Architecture de Sécurité et Conformité (Loi 09-08 / CNDP)

La gestion de la paie manipule des données hautement sensibles. L'application doit implémenter la sécurité dès sa conception (*Security by Design*).

1.  **Chiffrement des Données Sensibles (Encryption at Rest) :**
    *   Les colonnes contenant les RIB (Comptes bancaires), les Numéros de Cartes d'Identité Nationale (CIN), les Numéros d'Immatriculation CNSS et les montants des Salaires de Base doivent être chiffrés en base de données à l'aide d'un algorithme robuste (ex: AES-256 avec gestion dynamique des clés).
2.  **Journalisation Immuable des Modifications (Audit Trail) :**
    *   Toute écriture ou modification d'une variable de paie (ex: modification manuelle d'un net, modification d'un RIB, insertion d'une prime) doit générer un log système non modifiable et non supprimable en base contenant : `ID_Utilisateur`, `Horodatage_UTC`, `Valeur_Ancienne`, `Valeur_Nouvelle`, `Adresse_IP`.
3.  **Habilitations d'Accès Granulaires (RBAC) :**
    *   Mise en place de rôles stricts : `Super_RH` (Droits complets de validation et d'export), `Gestionnaire_Saisie` (Droit de modification des variables mensuelles sans droit de validation de la paie), `Auditeur_Externe` (Lecture seule sur les états et bulletins).

---

## 5. Formats et Protocoles d'Échanges Administratifs (EDI)

Une application certifiable doit communiquer de manière native avec les plateformes de l'administration marocaine.

### A. Flux XML pour la Direction Générale des Impôts (Simpl-IR / État 9421)
Le moteur doit être capable d'agréger toutes les données de l'année fiscale pour générer un fichier XML structuré selon le schéma XSD de la DGI.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<DeclarationSalaires xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <identifiantFiscal>12345678</identifiantFiscal>
    <anneeReference>2026</anneeReference>
    <periode>ANNUELLE</periode>
    <listePersonnel>
        <Personnel>
            <nom>EL IDRISSI</nom>
            <prenom>Karim</prenom>
            <numCin>BK987654</numCin>
            <numCnss>123456789</numCnss>
            <salaireBrutGlobal>144000.00</salaireBrutGlobal>
            <salaireNetImposable>112450.30</salaireNetImposable>
            <irRetenu>14200.00</irRetenu>
        </Personnel>
    </listePersonnel>
</DeclarationSalaires>
```

### B. Flux Plat/CSV pour la CNSS (Portail Damancom)
Génération mensuelle d'un fichier conforme au cahier des charges de la CNSS pour la déclaration automatisée des salaires et des jours travaillés (format à positions fixes ou délimité par des points-virgules).

### C. Intégration Comptable (CGNC)
Génération automatique d'un fichier de transfert d'écritures comptables (format compatible Sage, Odoo, SAP) structuré selon le Plan Comptable Marocain :
*   `6171` Rémunérations du personnel (Débit)
*   `6174` Charges sociales patronales (Débit)
*   `4432` Rémunérations dues au personnel (Crédit - Net à payer)
*   `4441` Caisse Nationale de Sécurité Sociale (Crédit - Part Salariale + Patronale)
*   `44525` État - IR retenu à la source (Crédit)
