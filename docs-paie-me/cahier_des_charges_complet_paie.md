# Cahier des Charges Fonctionnel & Technique : Application Web de Paie Marocaine (SaaS)

Ce document rassemble les spécifications fonctionnelles, réglementaires et techniques requises pour la conception, le développement et l'homologation d'une application web de gestion des Ressources Humaines et de la paie, en totale conformité avec la législation marocaine (Code du Travail, Code Général des Impôts, portails CNSS et DGI).

---

## 1. Contexte, Vision et Objectifs du Projet

### A. Contexte
La gestion de la paie au Maroc repose sur des processus rigoureux soumis à des évolutions législatives fréquentes (Lois de Finances). Les entreprises, fiduciaires et centres d'affaires ont besoin d'une solution moderne, hébergée sur le Cloud, capable d'automatiser le traitement du salaire du profil de l'employé jusqu'aux télé-déclarations.

### B. Objectifs de la Web App
* **Automatisation complète :** Réduire les interventions manuelles et éliminer les risques d'erreurs de calcul.
* **Conformité réglementaire :** Intégrer nativement les barèmes fiscaux (IR, frais professionnels) et sociaux (CNSS, AMO) mis à jour.
* **Interopérabilité :** Générer les fichiers d'échanges natifs aux normes de l'administration marocaine (BDS pour la CNSS, XML EDI pour la DGI).
* **Intégration comptable :** Exporter les écritures de paie directement vers les logiciels tiers (Sage, Odoo, etc.).

---

## 2. Couverture Fonctionnelle (Spécifications Métier)

### Module A : Gestion Administrative du Personnel (Core RH)
* **Fiche Collaborateur :** Stockage des données d'identification (Nom, Prénom, CIN, N° CNSS), situation familiale (Marié, nombre d'enfants pour les charges de famille), et coordonnées bancaires (RIB valide sur 24 chiffres).
* **Gestion des Contrats :** Prise en charge des contrats CDI, CDD, Intérim et contrats d'insertion ANAPEC (avec gestion des règles d'exonérations spécifiques).
* **Cycle de vie :** Historisation des changements de postes, de départements et d'évolutions salariales.

### Module B : Gestion des Temps, Absences et Variables de Paie
* **Moteur de Temps de Travail :** Base standard paramétrable à **191 heures/mois** (secteur non agricole) ou **208 heures/mois** (secteur agricole).
* **Calculateur d'Heures Supplémentaires :** Application automatique des taux de majoration légaux :
    * **25% :** Effectuées le jour (6h à 21h) en jours ouvrables.
    * **50% :** Effectuées la nuit (21h à 6h) en jours ouvrables OU le jour durant le repos hebdomadaire / jours fériés.
    * **100% :** Effectuées la nuit durant le repos hebdomadaire OU les jours fériés chômés.
* **Suivi des Absences :** Gestion des congés payés (calcul des droits), arrêts maladie (avec prise en compte de la carence de la CNSS) et absences injustifiées (déduction automatique sur salaire de base).

### Module C : Moteur de Calcul de la Paie (Séquence Légale)
Le moteur doit exécuter séquentiellement le pipeline de calcul suivant sans interruption :
1.  **Salaire Brut Global (SBG) :** `Salaire de Base + Heures Sup + Primes & Gratifications + Indemnités`.
2.  **Prime d'Ancienneté Automatique :** Calculé sur la base de la date d'embauche selon le barème légal mis à jour (**5% après 2 ans, 10% après 5 ans, 15% après 12 ans, 20% après 20 ans, 25% après 25 ans**).
3.  **Salaire Brut Imposable (SBI) :** `SBG - Éléments Exonérés`. Contrôle automatique des plafonds d'exonération :
    * *Indemnité de transport :* Max 500 DH/mois en zone urbaine (750 DH/mois hors périmètre).
    * *Prime de panier :* Max 30 DH/jour travaillé (dans la limite de 26 jours par mois).
4.  **Calcul des Cotisations Sociales Salariales :**
    * *CNSS (Prestations sociales) :* **4,48%** appliqué sur le salaire brut, plafonné à **6 000 DH/mois**.
    * *AMO :* **2,26%** appliqué sur le Salaire Brut Global, **sans aucun plafond**.
5.  **Frais Professionnels (Abattement) :** Application automatique selon les paliers réglementaires :
    * **35%** si le salaire brut imposable annuel est inférieur ou égal à 78 000 DH (soit <= 6 500 DH/mois).
    * **25%** si le salaire annuel est supérieur à 78 000 DH.
    * Plafonnement strict et impératif à **2 500 DH/mois** (30 000 DH/an).
6.  **Salaire Net Imposable (SNI) :** `SBI - Cotisations Sociales - Frais Professionnels`.
7.  **Calcul de l'IR Brut :** Application du barème progressif de l'IR par tranche (de 0% à 38%).
8.  **Calcul de l'IR Net :** Déduction pour charges de famille (**50 DH/mois par personne à charge**, dans la limite légale de 6 personnes, soit max 300 DH/mois).
9.  **Salaire Net à Payer :** `SBG - Cotisations Sociales (Salatiales) - IR Net - Retenues (avances, prêts)`.

---

## 3. Déclarations Électroniques et Interfaçages Obligatoires

### A. Portail DAMANCOM (CNSS) - Fichier BDS
* **Format d'export :** Fichier texte positionnel ou CSV selon le cahier des charges technique de la CNSS.
* **Règles métiers :** Limitation stricte à **26 jours déclarés par mois** par collaborateur. Calcul automatique du cumul de l'assiette plafonnée (6 000 DH) et non plafonnée (AMO).

### B. Portail SIMPL-IR (DGI) - État 9421 (ex-9101)
* **Format d'export :** Fichier **EDI XML** structuré, validant le schéma de contrôle `.xsd` officiel de la Direction Générale des Impôts.
* **Règles métiers :** Génération annuelle reprenant l'ensemble des éléments permanents, gains imposables, montant des indemnités exonérées, abattements appliqués et impôt retenu à la source pour chaque salarié sur l'exercice fiscal.

---

## 4. Édition des Livrables et Documents RH

* **Le Bulletin de Paie PDF :** Génération d'un document conforme aux exigences de l'inspection du travail faisant figurer l'ICE de l'entreprise, le numéro de patente, le numéro CNSS de l'employeur et le détail complet de la structure salariale (Part salariale et part patronale).
* **Le Journal de Paie :** Rapport matriciel complet du mois permettant aux gestionnaires de valider les montants globaux (Masse salariale, Total CNSS, Total IR).
* **Documents de Fin de Contrat :** Calcul automatique des indemnités de congés non consommés, des indemnités de licenciement ou de préavis, et édition du reçu pour **Solde de Tout Compte**, de l'**Attestation de Travail** et du Certificat de travail.

---

## 5. Architecture Technique et Spécifications de Développement

### A. Évolutivité et Découplage (Règle d'or)
* Les constantes légales (**taux CNSS de 4.48%, taux AMO de 2.26%, plafonds de 6000 DH et 2500 DH, barème progressif de l'IR**) ne doivent **jamais** être intégrées directement dans le code source (*no hardcoding*).
* Elles doivent être stockées dans une table `Parametres_Configuration` en base de données avec une gestion des périodes de validité (historisation), afin qu'une mise à jour de la Loi de Finances n'impacte pas rétroactivement les calculs des mois ou années précédents.

### B. Sécurité et Gestion des Accès (RBAC)
* Chiffrement des données sensibles (salaires, RIB, CIN) au repos et en transit (HTTPS).
* Gestion fine des rôles utilisateurs : *Super-Admin, Gestionnaire RH, Comptable (export des écritures), Salarié (Espace personnel en lecture seule pour télécharger les bulletins)*.
* Piste d'audit (*Audit Trail*) pour consigner l'historique des modifications sur les variables de paie validées.

### C. Module d'Intégration Comptable
* Génération automatique des écritures comptables selon le **Plan Comptable Marocain**.
* **Débit :** Comptes de charges de personnel (`6171` Salaires, `6174` Charges sociales patronales).
* **Crédit :** Comptes de tiers (`4432` Personnel - Rémunérations dues, `4441` CNSS, `44525` IR retenu à la source).
* Export paramétrable au format CSV compatible avec les logiciels comptables du marché.
