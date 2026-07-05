# Cahier des Charges Fonctionnel : Application Web de Paie Marocaine

Ce document définit les spécifications fonctionnelles, réglementaires et techniques indispensables pour le développement d'une application web de paie conforme à la législation en vigueur au Maroc (Code du Travail, Code Général des Impôts, exigences CNSS et DGI).

---

## 1. Moteur de Calcul (Conformité Légale et Réglementaire)

Le cœur de l'application doit automatiser l'application des règles sociales et fiscales marocaines sans saisie manuelle corrective.

### A. Gestion du Temps de Travail et Salaire de Base
* **Base légale :** Prise en charge des **191 heures/mois** (secteur non agricole) ou **208 heures/mois** (secteur agricole).
* **Heures supplémentaires :** Calcul automatique des majorations selon le moment et le jour :
    * **25% :** Jour ouvrable (entre 6h et 21h).
    * **50% :** Jour ouvrable (de nuit entre 21h et 6h) OU Jour de repos hebdomadaire (de jour).
    * **100% :** Jour de repos hebdomadaire (de nuit) OU Jour férié chômé.

### B. Automatisation de la Prime d'Ancienneté
* **Déclenchement automatique :** Calcul basé sur la différence entre la date du traitement de la paie et la date d'embauche du salarié.
* **Application du barème légal :**
    * >= 2 ans : **5%**
    * >= 5 ans : **10%**
    * >= 12 ans : **15%**
    * >= 20 ans : **20%**
    * >= 25 ans : **25%**
* **Assiette de calcul :** `Salaire de base + Heures supplémentaires + Primes contractuelles` (hors indemnités de frais).

### C. Gestion des Plafonds et Exonérations (DGI / CNSS)
Le système doit bloquer ou réintégrer automatiquement dans l'assiette imposable/soumise tout dépassement de plafond :
* **Indemnité de Transport :** Exonération plafonnée à **500 DH/mois** en périmètre urbain (**750 DH/mois** hors périmètre).
* **Prime de Panier / Repas :** Exonération limitée à **30 DH par jour de travail** (maximum 26 jours par mois, soit 780 DH).
* **Frais Professionnels :** Abattement automatique de **35%** (si salaire annuel brut imposable <= 78 000 DH) ou **25%** (si > 78 000 DH) avec un plafonnement strict à **2 500 DH/mois**.

---

## 2. Déclarations Électroniques et Interfaçage Obligatoire

Une application de paie au Maroc doit nativement exporter les flux de données réglementaires sous forme de fichiers structurés.

### A. Portail DAMANCOM (CNSS) - Fichier BDS
* **Fréquence :** Mensuelle.
* **Spécification technique :** Génération du fichier **BDS (Bordereau de Déclaration des Salaires)** au format texte positionnel ou CSV requis par la CNSS.
* **Contraintes métiers :**
    * Nombre de jours déclarés plafonné à **26 jours par mois**.
    * Calcul du Salaire Brut Soumis plafonné rigoureusement à **6 000 DH/mois** pour la part Prestations Sociales.
    * Calcul de la part AMO sur le Salaire Brut Global, **sans aucun plafond**.

### B. Portail SIMPL-IR (Direction Générale des Impôts) - État 9421 (ex-9101)
* **Fréquence :** Annuelle (généralement avant fin février de l'année N+1).
* **Spécification technique :** Génération d'un fichier au format **EDI XML** conforme au schéma XSD fourni par la DGI.
* **Contraintes métiers :** Transmission exhaustive de l'identité des salariés, de la période de travail, du SBI, SNI, IR retenu, des avantages en nature (logement, véhicule, etc.) et du montant global des indemnités exonérées versées.

---

## 3. Édition des Livrables et Documents RH

L'application doit permettre l'exportation et l'archivage sécurisé des documents obligatoires selon le formalisme marocain.

### A. Le Bulletin de Paie En ligne
Mentions légales obligatoires à faire figurer sur le template :
* Raison sociale, Adresse, Identifiant Commun de l'Entreprise (ICE), N° Patente, N° CNSS de l'employeur.
* Nom, Prénom, N° CIN, N° CNSS, Date d'embauche, Qualification et N° de carte de travail du salarié.
* Détail exhaustif du brut au net (gains, retenues salariales, cotisations patronales).

### B. Les Journaux et Registres
* **Journal de Paie :** Synthèse mensuelle par ligne/salarié permettant la vérification globale avant validation définitive de la paie.
* **Livre de Paie / Registre du Travail :** Document d'archivage obligatoire à présenter en cas de contrôle de l'Inspection du Travail.

### C. Gestion des Fins de Contrat (Solde de Tout Compte)
* Calcul automatique de l'indemnité compensatrice de congé payé non pris.
* Calcul de l'indemnité légale de licenciement (le cas échéant) selon le barème du Code du Travail (heures de salaire par année d'ancienneté).
* Génération du reçu pour **Solde de Tout Compte**, de l'**Attestation de Travail** et du certificat de travail.

---

## 4. Écritures Comptables et Intégration ERP

Pour s'intégrer dans le système d'information des entreprises, l'application doit traduire le calcul de la paie en écritures comptables conformes au Plan Comptable Marocain.

### Schéma d'Imputation Standard (Fin de mois) :
* **Débit :**
    * `61711000` : Appointements et salaires (Salaire de base + heures sup + primes)
    * `61712000` : Primes et gratifications
    * `61713000` : Indemnités et avantages divers
    * `61741000` : Cotisations aux caisses de retraite (Part patronale)
    * `61742000` : Cotisations à la CNSS (Part patronale)
* **Crédit :**
    * `4432` : Rémunérations dues au personnel (Le Salaire Net à Payer)
    * `4441` : Caisse Nationale de Sécurité Sociale (Part salariale + patronale)
    * `4443` : Caisses de retraite (ex: CIMR - Part salariale + patronale)
    * `44525` : État, impôt sur le revenu retenu à la source (IR Net)

*L'application doit proposer un export de ces écritures sous format CSV paramétrable (Sage, Odoo, SAP, etc.).*

---

## 5. Architecture Technique et Évolutivité (Conseils Dev)

1.  **Architecture Découplée (Moteur de Règles) :** Ne jamais coder les taux (`4.48%`, `2.26%`), les plafonds (`6000 DH`, `2500 DH`) ou les tranches de l'IR directement dans le code source (*hardcoding*). Créer une table de configuration `Parametres_Paie` en base de données. Cela permettra une mise à jour simple lors du vote de chaque Loi de Finances.
2.  **Rétroactivité de la Paie :** Le système doit être capable de figer les paramètres d'un mois M (historisation des taux) pour permettre de recalculer ou de consulter une paie passée sans que les modifications de taux du mois M+1 n'impactent l'historique.
3.  **Gestion de la Proratisation :** Intégrer un algorithme robuste pour le calcul de la paie des entrées/sorties en cours de mois (calcul au prorata des jours réels travaillés basés sur la règle des 26 jours théoriques).