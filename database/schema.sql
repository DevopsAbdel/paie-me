SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

CREATE DATABASE IF NOT EXISTS paie_me
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE paie_me;

-- -----------------------------------------------------------
-- Utilisateurs
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom         VARCHAR(100)    NOT NULL,
    email       VARCHAR(180)    NOT NULL UNIQUE,
    password    VARCHAR(255)    NOT NULL,
    role        ENUM('admin', 'gestionnaire') NOT NULL DEFAULT 'gestionnaire',
    actif       TINYINT(1)      NOT NULL DEFAULT 1,
    created_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- Sociétés
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS societes (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id             INT UNSIGNED        NOT NULL,
    raison_sociale      VARCHAR(200)        NOT NULL,
    forme_juridique     VARCHAR(20)         NOT NULL,
    ice                 VARCHAR(20)         NOT NULL,
    if_fiscal           VARCHAR(20)         NOT NULL,
    rc                  VARCHAR(20)         NOT NULL,
    tp                  VARCHAR(20)         NOT NULL,
    cnss                VARCHAR(20)         NOT NULL,
    adresse             TEXT,
    ville               VARCHAR(100),
    telephone           VARCHAR(20),
    email               VARCHAR(180),
    site_web            VARCHAR(255),
    banque              VARCHAR(100),
    agence              VARCHAR(100),
    rib                 VARCHAR(255),
    logo                VARCHAR(255),
    damancom_login      VARCHAR(100),
    damancom_password   VARCHAR(255),
    simpl_login         VARCHAR(100),
    simpl_password      VARCHAR(255),
    cimr_login          VARCHAR(100),
    cimr_password       VARCHAR(255),
    actif               TINYINT(1)          NOT NULL DEFAULT 1,
    created_at          DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- Salariés
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS salaries (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    societe_id          INT UNSIGNED        NOT NULL,
    matricule           VARCHAR(20)         NOT NULL,
    nom_famille         VARCHAR(100)        NOT NULL,
    prenom              VARCHAR(100)        NOT NULL,
    adresse             TEXT,
    date_naissance      DATE,
    date_embauche       DATE,
    cin                 VARCHAR(20),
    cnss                VARCHAR(20),
    situation_familiale ENUM('celibataire', 'marie', 'divorce', 'veuf') NOT NULL DEFAULT 'celibataire',
    nb_enfants          TINYINT UNSIGNED    NOT NULL DEFAULT 0,
    enfants_a_charge    TINYINT UNSIGNED    NOT NULL DEFAULT 0,
    personnes_a_charge  TINYINT UNSIGNED    NOT NULL DEFAULT 0,
    poste               VARCHAR(150),
    type_contrat        ENUM('CDI', 'CDD', 'stage', 'interim') NOT NULL DEFAULT 'CDI',
    salaire_base        DECIMAL(10,2)       NOT NULL DEFAULT 0.00,
    type_salaire        ENUM('mensuel', 'horaire', 'journalier') NOT NULL DEFAULT 'mensuel',
    frequence_paiement  ENUM('mensuel', 'quinzaine', 'hebdomadaire') NOT NULL DEFAULT 'mensuel',
    mode_paiement       ENUM('virement', 'cheque', 'especes') NOT NULL DEFAULT 'virement',
    rib                 VARCHAR(255),
    indemnite_transport DECIMAL(10,2)       NOT NULL DEFAULT 500.00,
    indemnite_panier    DECIMAL(10,2)       NOT NULL DEFAULT 780.00,
    indemnite_representation DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
    avantage_logement   DECIMAL(10,2)       NOT NULL DEFAULT 0.00,
    actif               TINYINT(1)          NOT NULL DEFAULT 1,
    created_at          DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (societe_id) REFERENCES societes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- Périodes de paie
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS periodes (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    societe_id      INT UNSIGNED        NOT NULL,
    mois            TINYINT UNSIGNED    NOT NULL,
    annee           SMALLINT UNSIGNED   NOT NULL,
    date_debut      DATE                NOT NULL,
    date_fin        DATE                NOT NULL,
    cloturee        TINYINT(1)          NOT NULL DEFAULT 0,
    penalites_cnss  DECIMAL(10,2)       NOT NULL DEFAULT 0.00,
    penalites_tfp   DECIMAL(10,2)       NOT NULL DEFAULT 0.00,
    penalites_amo   DECIMAL(10,2)       NOT NULL DEFAULT 0.00,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_periode (societe_id, mois, annee),
    FOREIGN KEY (societe_id) REFERENCES societes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- Paies (lignes de calcul par salarié / période)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS paies (
    id                          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    periode_id                  INT UNSIGNED        NOT NULL,
    salarie_id                  INT UNSIGNED        NOT NULL,
    societe_id                  INT UNSIGNED        NOT NULL,
    jours_travailles            TINYINT UNSIGNED    NOT NULL DEFAULT 30,
    salaire_brut                DECIMAL(10,2)       NOT NULL DEFAULT 0.00,
    salaire_plafonne_cnss       DECIMAL(10,2)       NOT NULL DEFAULT 0.00,
    indemnite_transport         DECIMAL(10,2)       NOT NULL DEFAULT 0.00,
    indemnite_panier            DECIMAL(10,2)       NOT NULL DEFAULT 0.00,
    indemnite_representation    DECIMAL(10,2)       NOT NULL DEFAULT 0.00,
    avantage_logement           DECIMAL(10,2)       NOT NULL DEFAULT 0.00,
    heures_supplementaires      DECIMAL(10,2)       NOT NULL DEFAULT 0.00,
    montant_heures_sup          DECIMAL(10,2)       NOT NULL DEFAULT 0.00,
    heures_sup_25               DECIMAL(10,2)       NOT NULL DEFAULT 0.00,
    heures_sup_50               DECIMAL(10,2)       NOT NULL DEFAULT 0.00,
    heures_sup_100              DECIMAL(10,2)       NOT NULL DEFAULT 0.00,
    cnss_salariale              DECIMAL(10,2)       NOT NULL DEFAULT 0.00,
    amo_salariale               DECIMAL(10,2)       NOT NULL DEFAULT 0.00,
    mutuelle                    DECIMAL(10,2)       NOT NULL DEFAULT 0.00,
    sni                         DECIMAL(10,2)       NOT NULL DEFAULT 0.00,
    ir                          DECIMAL(10,2)       NOT NULL DEFAULT 0.00,
    deductions_familiales       DECIMAL(10,2)       NOT NULL DEFAULT 0.00,
    net_avant_retenues          DECIMAL(10,2)       NOT NULL DEFAULT 0.00,
    autres_retenues             DECIMAL(10,2)       NOT NULL DEFAULT 0.00,
    net_a_payer                 DECIMAL(10,2)       NOT NULL DEFAULT 0.00,
    cnss_patronale              DECIMAL(10,2)       NOT NULL DEFAULT 0.00,
    amo_patronale               DECIMAL(10,2)       NOT NULL DEFAULT 0.00,
    created_at                  DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at                  DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_paie (periode_id, salarie_id),
    FOREIGN KEY (periode_id) REFERENCES periodes(id) ON DELETE CASCADE,
    FOREIGN KEY (salarie_id) REFERENCES salaries(id) ON DELETE CASCADE,
    FOREIGN KEY (societe_id) REFERENCES societes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- Bulletins de paie
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS bulletins (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    paie_id         INT UNSIGNED        NOT NULL UNIQUE,
    numero          VARCHAR(20)         NOT NULL,
    date_emission   DATE                NOT NULL,
    pdf_path        VARCHAR(255),
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (paie_id) REFERENCES paies(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- Barème IR 2025 (mensuel)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS bareme_ir (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    min         DECIMAL(10,2) NOT NULL,
    max         DECIMAL(10,2) NOT NULL,
    taux        DECIMAL(5,2)  NOT NULL,
    deduction   DECIMAL(10,2) NOT NULL,
    type        ENUM('mensuel','annuel') NOT NULL DEFAULT 'mensuel'
) ENGINE=InnoDB;

INSERT INTO bareme_ir (min, max, taux, deduction, type) VALUES
    (0.00,    3333.33,   0.00,   0.00,    'mensuel'),
    (3333.34, 5000.00,  10.00, 333.33,   'mensuel'),
    (5000.01, 6666.67,  20.00, 833.33,   'mensuel'),
    (6666.68, 8333.33,  30.00, 1500.00,  'mensuel'),
    (8333.34, 15000.00, 34.00, 1833.33,  'mensuel'),
    (15000.01, 999999.99, 37.00, 2283.33, 'mensuel'),
    (0.00,    40000.00,   0.00,   0.00,    'annuel'),
    (40001.00, 60000.00,  10.00, 4000.00,  'annuel'),
    (60001.00, 80000.00,  20.00, 10000.00, 'annuel'),
    (80001.00, 100000.00, 30.00, 18000.00, 'annuel'),
    (100001.00, 180000.00, 34.00, 22000.00, 'annuel'),
    (180000.01, 9999999.99, 37.00, 27400.00, 'annuel');

-- -----------------------------------------------------------
-- Services (départements)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS services (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    societe_id      INT UNSIGNED        NOT NULL,
    nom             VARCHAR(100)        NOT NULL,
    description     TEXT,
    actif           TINYINT(1)          NOT NULL DEFAULT 1,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (societe_id) REFERENCES societes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- Paramètres CNSS / AMO (par société)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS parametres_cnss_amo (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    societe_id          INT UNSIGNED    NOT NULL UNIQUE,
    plafond_cnss        DECIMAL(10,2)   NOT NULL DEFAULT 6000.00,
    taux_cnss_salarial  DECIMAL(5,2)    NOT NULL DEFAULT 4.48,
    taux_cnss_patronal  DECIMAL(5,2)    NOT NULL DEFAULT 8.98,
    taux_amo_salarial   DECIMAL(5,2)    NOT NULL DEFAULT 2.26,
    taux_amo_patronal   DECIMAL(5,2)    NOT NULL DEFAULT 4.11,
    taux_amo_total      DECIMAL(5,2)    NOT NULL DEFAULT 6.37,
    taux_ams_salarial           DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    taux_ams_patronal           DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    taux_allocations_familiales DECIMAL(5,2) NOT NULL DEFAULT 6.40,
    taux_prestations_sociales   DECIMAL(5,2) NOT NULL DEFAULT 13.46,
    taxe_formation              DECIMAL(5,2) NOT NULL DEFAULT 1.60,
    participation_amo           DECIMAL(5,2) NOT NULL DEFAULT 1.85,
    taux_penalites_cnss                 DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    taux_penalites_tfp                  DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    taux_penalites_amo                  DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    penalite_cnss_premier_mois          DECIMAL(5,2) NOT NULL DEFAULT 3.00,
    penalite_cnss_mois_suivants         DECIMAL(5,2) NOT NULL DEFAULT 0.50,
    penalite_amo_taux                   DECIMAL(5,2) NOT NULL DEFAULT 1.00,
    astreinte_cnss_par_salarie          DECIMAL(10,2) NOT NULL DEFAULT 50.00,
    astreinte_amo_par_salarie           DECIMAL(10,2) NOT NULL DEFAULT 100.00,
    created_at                  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (societe_id) REFERENCES societes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- Rubriques de gains
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS rubriques_gains (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    societe_id      INT UNSIGNED        DEFAULT NULL,
    is_global       TINYINT(1)          NOT NULL DEFAULT 0,
    code            VARCHAR(20)         NOT NULL,
    libelle         VARCHAR(100)        NOT NULL,
    type_montant    ENUM('fixe','proportionnel') NOT NULL DEFAULT 'fixe',
    valeur_defaut   DECIMAL(10,2)       NOT NULL DEFAULT 0.00,
    categorie       VARCHAR(50)         DEFAULT NULL,
    imposable       TINYINT(1)          NOT NULL DEFAULT 1,
    affectation     VARCHAR(20)         DEFAULT NULL,
    plafond_dgi     VARCHAR(200)        DEFAULT NULL,
    plafond_cnss    VARCHAR(200)        DEFAULT NULL,
    justificatifs   VARCHAR(500)        DEFAULT NULL,
    compte          VARCHAR(20)         DEFAULT NULL,
    source          VARCHAR(100)        DEFAULT NULL,
    source_maj      DATE                DEFAULT NULL,
    nature_edi      VARCHAR(20)         DEFAULT NULL,
    base_anciennete TINYINT(1)          NOT NULL DEFAULT 0,
    au_prorata      TINYINT(1)          NOT NULL DEFAULT 0,
    actif           TINYINT(1)          NOT NULL DEFAULT 1,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (societe_id) REFERENCES societes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- Rubriques de retenues
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS rubriques_retenues (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    societe_id      INT UNSIGNED        DEFAULT NULL,
    is_global       TINYINT(1)          NOT NULL DEFAULT 0,
    code            VARCHAR(20)         NOT NULL,
    libelle         VARCHAR(100)        NOT NULL,
    type_montant    ENUM('fixe','proportionnel') NOT NULL DEFAULT 'fixe',
    valeur_defaut   DECIMAL(10,2)       NOT NULL DEFAULT 0.00,
    actif           TINYINT(1)          NOT NULL DEFAULT 1,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (societe_id) REFERENCES societes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- Organismes sociaux
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS organismes (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    societe_id      INT UNSIGNED        NOT NULL,
    nom             VARCHAR(100)        NOT NULL,
    type            ENUM('cnss','amo','cimr','mutuelle','autre') NOT NULL DEFAULT 'autre',
    login           VARCHAR(100),
    mot_de_passe    VARCHAR(255),
    actif           TINYINT(1)          NOT NULL DEFAULT 1,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (societe_id) REFERENCES societes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- Modèles d'attestation
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS modeles_attestation (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    societe_id      INT UNSIGNED        NOT NULL,
    titre           VARCHAR(200)        NOT NULL,
    contenu         TEXT                NOT NULL,
    actif           TINYINT(1)          NOT NULL DEFAULT 1,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (societe_id) REFERENCES societes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- Fonctions (postes par service)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS fonctions (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    societe_id      INT UNSIGNED        NOT NULL,
    service_id      INT UNSIGNED        DEFAULT NULL,
    nom             VARCHAR(100)        NOT NULL,
    description     TEXT,
    actif           TINYINT(1)          NOT NULL DEFAULT 1,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (societe_id) REFERENCES societes(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- Colonnes ajoutées
-- -----------------------------------------------------------
ALTER TABLE salaries
    ADD COLUMN service_id        INT UNSIGNED    DEFAULT NULL AFTER societe_id,
    ADD COLUMN fonction_id       INT UNSIGNED    DEFAULT NULL AFTER service_id,
    ADD COLUMN avances_salaire   DECIMAL(10,2)   NOT NULL DEFAULT 0.00 AFTER avantage_logement,
    ADD COLUMN mutuelle          DECIMAL(10,2)   NOT NULL DEFAULT 0.00 AFTER avances_salaire,
    ADD COLUMN date_sortie       DATE            DEFAULT NULL AFTER date_embauche,
    ADD COLUMN enfants_a_charge TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER nb_enfants,
    ADD COLUMN personnes_a_charge TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER enfants_a_charge,
    ADD FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL,
    ADD FOREIGN KEY (fonction_id) REFERENCES fonctions(id) ON DELETE SET NULL;

ALTER TABLE paies
    ADD COLUMN prime_anciennete  DECIMAL(10,2)   NOT NULL DEFAULT 0.00 AFTER salaire_brut,
    ADD COLUMN sbi               DECIMAL(10,2)   NOT NULL DEFAULT 0.00 AFTER salaire_brut,
    ADD COLUMN frais_professionnels DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER sbi,
    ADD COLUMN total_gains       DECIMAL(10,2)   NOT NULL DEFAULT 0.00 AFTER montant_heures_sup;

ALTER TABLE rubriques_gains
    ADD COLUMN categorie       VARCHAR(50)  DEFAULT NULL AFTER valeur_defaut,
    ADD COLUMN affectation     VARCHAR(20)  DEFAULT NULL AFTER imposable,
    ADD COLUMN plafond_dgi     VARCHAR(200) DEFAULT NULL AFTER affectation,
    ADD COLUMN plafond_cnss    VARCHAR(200) DEFAULT NULL AFTER plafond_dgi,
    ADD COLUMN justificatifs   VARCHAR(500) DEFAULT NULL AFTER plafond_cnss;

ALTER TABLE rubriques_gains
    ADD COLUMN compte          VARCHAR(20)  DEFAULT NULL AFTER justificatifs,
    ADD COLUMN source          VARCHAR(100) DEFAULT NULL AFTER compte,
    ADD COLUMN source_maj      DATE         DEFAULT NULL AFTER source,
    ADD COLUMN nature_edi      VARCHAR(20)  DEFAULT NULL AFTER source_maj,
    ADD COLUMN base_anciennete TINYINT(1)   NOT NULL DEFAULT 0 AFTER nature_edi,
    ADD COLUMN au_prorata      TINYINT(1)   NOT NULL DEFAULT 0 AFTER base_anciennete;

-- -----------------------------------------------------------
-- Audit log
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS audit_log (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED        NOT NULL,
    action      VARCHAR(50)         NOT NULL,
    entity_type VARCHAR(50)         NOT NULL,
    entity_id   INT UNSIGNED        DEFAULT NULL,
    description VARCHAR(500)        DEFAULT NULL,
    ip_address  VARCHAR(45)         DEFAULT NULL,
    created_at  DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_entity (entity_type, entity_id),
    INDEX idx_user (user_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- Paie gains (overrides par salarié/période)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS paie_gains (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    paie_id        INT UNSIGNED NOT NULL,
    rubrique_id    INT UNSIGNED NOT NULL,
    montant        DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    FOREIGN KEY (paie_id) REFERENCES paies(id) ON DELETE CASCADE,
    FOREIGN KEY (rubrique_id) REFERENCES rubriques_gains(id) ON DELETE CASCADE,
    UNIQUE KEY unique_paie_rubrique (paie_id, rubrique_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS paie_retenues (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    paie_id        INT UNSIGNED NOT NULL,
    type           ENUM('avance','pret','sanction','autre') NOT NULL DEFAULT 'autre',
    libelle        VARCHAR(200) NOT NULL,
    montant        DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    FOREIGN KEY (paie_id) REFERENCES paies(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- Index utilisateur par défaut (password: admin123)
-- -----------------------------------------------------------
INSERT INTO users (nom, email, password, role)
VALUES ('Administrateur', 'admin@paie-me.ma',
        '$2y$10$DRPsKOgLy.Ib4oKPT8oX/.2gRRWXSCgQz3UdUMLbbiyYvVOnX6fhq',
        'admin');

-- -----------------------------------------------------------
-- Rubriques gains globales (applicables à toutes les sociétés)
-- -----------------------------------------------------------
INSERT INTO rubriques_gains (societe_id, is_global, code, libelle, type_montant, valeur_defaut, categorie, imposable, affectation, plafond_dgi, plafond_cnss, justificatifs) VALUES
    -- Gains standard (imposables)
    (NULL,1,'PRIME_REND','Prime de rendement','proportionnel',10.00,'Gain standard',1,NULL,NULL,NULL,NULL),
    (NULL,1,'PRIME_OBJECTIF','Prime d''objectifs','proportionnel',5.00,'Gain standard',1,NULL,NULL,NULL,NULL),
    (NULL,1,'PRIME_ASSIDUITE','Prime d''assiduité','fixe',300.00,'Gain standard',1,NULL,NULL,NULL,NULL),
    (NULL,1,'PRIME_NUIT','Prime de nuit','fixe',250.00,'Gain standard',1,NULL,NULL,NULL,NULL),
    (NULL,1,'PRIME_13EME','13ème mois (prorata)','proportionnel',8.33,'Gain standard',1,NULL,NULL,NULL,NULL),
    -- I. Transport & Déplacement (exonérés)
    (NULL,1,'330','Indemnité de transport urbain','fixe',500,'Transport & Déplacement',0,'61713','500.00 DH / mois','500.00 DH / mois','Lieu de travail situé au milieu urbain de la ville'),
    (NULL,1,'331','Indemnité de représentation','proportionnel',10,'Spécifiques à certains emplois',0,'61713','10% du salaire de base','10% du salaire de base','Poste de direction, d''encadrement supérieur ou équivalent'),
    (NULL,1,'334','Indemnité kilométrique','fixe',0,'Transport & Déplacement',0,'61713','3 DH / KM','3 DH / KM','Carnet de bord, carte grise au nom du salarié, trajet < 50 KM'),
    (NULL,1,'337','Indemnité de tournée','fixe',1500,'Transport & Déplacement',0,'61713','1 500.00 DH / mois','1 500.00 DH / mois','Périmètre de déplacement limité à 50 KM, planning de tournée'),
    (NULL,1,'339','Indemnité de déplacement justifiée','fixe',0,'Transport & Déplacement',0,'61713','Nourriture (10x SMIG hor.), Hébergement (30x SMIG hor.)','Totalement exonéré si justifié','Pièces justificatives (factures, tickets, ordre de mission)'),
    (NULL,1,'340','Indemnité de déplacement forfaitaire ponctuelle','fixe',0,'Transport & Déplacement',0,'61713','Nourriture (10x SMIG hor.), Hébergement (30x SMIG hor.)','Repas: 171 DH/j, Hébergement: 513 DH/nuit','Ordre de mission stipulant la nature ponctuelle'),
    (NULL,1,'341','Indemnité de déplacement forfaitaire régulière','fixe',5000,'Transport & Déplacement',0,'61713','<= 5000 DH et <= Salaire de base','Exonération dans la limite de 100% du S.B. (max 5000 DH/mois)','Déplacements professionnels hors périmètre urbain (> 50 km)'),
    (NULL,1,'342','Indemnité de transport hors urbain','fixe',750,'Transport & Déplacement',0,'61713','750.00 DH / mois','750.00 DH / mois','Lieu de travail situé en dehors du milieu urbain'),
    (NULL,1,'343','Prime d''outillage','fixe',100,'Spécifiques à certains emplois',0,'61713','100 DH / mois','119.70 DH / 26 jours de travail','Le salarié doit être propriétaire de ses propres équipements'),
    (NULL,1,'344','Prime de salissure','fixe',210,'Spécifiques à certains emplois',0,'61713','210 DH / mois','239.40 DH / 26 jours de travail','Travaux salissants / insalubres (bleu de travail requis)'),
    (NULL,1,'345','Prime d''usure de vêtements / Tenue','fixe',0,'Spécifiques à certains emplois',0,'61713','Frais réels ou barème interne','Exonéré si port obligatoire pour le service','Obligation contractuelle ou règlement intérieur'),
    (NULL,1,'346','Indemnité de panier / Panier de nuit','fixe',0,'Spécifiques à certains emplois',0,'61713','2x SMIG horaire par jour','Exonération selon plafond légal en vigueur','Horaires de nuit ou travail continu sans coupure'),
    (NULL,1,'347','Indemnité de pénibilité','fixe',0,'Spécifiques à certains emplois',0,'61713','Selon convention collective','Exonéré sous réserve d''un cadre réglementé','Attestation de conditions de travail pénibles'),
    (NULL,1,'348','Indemnité de risque / Danger','fixe',0,'Spécifiques à certains emplois',0,'61713','Selon barème sectoriel','Exonéré si le risque est inhérent à la fonction','Fiche de poste, rapport d''évaluation des risques'),
    (NULL,1,'349','Indemnité d''astreinte','fixe',0,'Spécifiques à certains emplois',0,'61713','Selon convention collective','Exonéré si liée à des interventions urgentes hors horaires','Planning d''astreinte et rapports d''intervention'),
    (NULL,1,'350','Indemnité de garde','fixe',0,'Spécifiques à certains emplois',0,'61713','Barème interne conventionné','Exonéré dans le cadre médical ou de sécurité','Registre des gardes effectuées'),
    (NULL,1,'351','Voiture de fonction ou de service','fixe',0,'Transport & Déplacement',0,'61713','Charges supportées par l''entreprise','Totalement exonéré','Usage strictement professionnel ou convention d''affectation'),
    (NULL,1,'352','Indemnité de voyage à l''étranger','fixe',0,'Transport & Déplacement',0,'61713','Frais réels justifiés','Frais réels sur justificatifs ou barème officiel','Ordre de mission international, billets, factures hôtel'),
    (NULL,1,'353','Indemnité de déménagement / mutation','fixe',0,'Transport & Déplacement',0,'61713','Frais réels sur factures','Exonéré si requis par l''employeur','Décision de mutation, factures du déménageur'),
    (NULL,1,'354','Allocations familiales additionnelles','fixe',0,'Caractère Social & Familial',0,'61712','Plafond légal CNSS','Totalement exonéré','Livret de famille, attestation de non-paiement par ailleurs'),
    (NULL,1,'355','Allocation de naissance','fixe',0,'Caractère Social & Familial',0,'61712','Plafond interne raisonnable','Exonéré si ponctuel','Extrait d''acte de naissance du nouveau-né'),
    (NULL,1,'356','Allocation de mariage','fixe',0,'Caractère Social & Familial',0,'61712','Barème social de l''entreprise','Exonéré si ponctuel','Acte de mariage adoulé ou officiel'),
    (NULL,1,'357','Allocation de décès / Obsèques','fixe',0,'Caractère Social & Familial',0,'61712','Frais réels ou forfait social','Totalement exonéré','Certificat de décès du conjoint ou d''un ascendant/descendant direct'),
    (NULL,1,'358','Prime de scolarité / Rentrée scolaire','fixe',400,'Caractère Social & Familial',0,'61712','Plafond par enfant/an','Exonéré si attribué aux enfants à charge','Certificat de scolarité annuel'),
    (NULL,1,'359','Bons d''achat / Cadeaux de fin d''année','fixe',0,'Caractère Social & Familial',0,'61712','Plafond annuel (ex: 10% SMIG)','Exonéré dans la limite du plafond social','Distribution générale à l''occasion de fêtes (Aïd, Achoura, etc.)'),
    (NULL,1,'360','Indemnité de caisse (responsabilité pécuniaire)','fixe',190,'Spécifiques à certains emplois',0,'61713','190 DH / mois','239.40 DH / 26 jours de travail','Poste de caissier ou manipulation effective de fonds'),
    (NULL,1,'361','Subvention de cantine / Titres repas','fixe',0,'Caractère Social & Familial',0,'61712','Plafond par ticket / jour','Exonéré selon la quote-part patronale réglementaire','Factures du prestataire de restauration ou émetteur de titres'),
    (NULL,1,'362','Prise en charge des frais médicaux','fixe',0,'Caractère Social & Familial',0,'61712','Sur dossier médical','Exonéré si géré par le fonds social / mutuelle','Décompte AMO/Mutuelle et ordonnances restées à charge'),
    (NULL,1,'363','Aide aux vacances / Estivage','fixe',0,'Caractère Social & Familial',0,'61712','Plafond annuel fixe','Exonéré si géré via les œuvres sociales (COS)','Factures d''organismes de vacances ou convention COS'),
    (NULL,1,'364','Secours exceptionnel / Social','fixe',0,'Caractère Social & Familial',0,'61712','Forfait ponctuel motivé','Exonéré si situation de précarité avérée','Dossier d''assistante sociale ou justificatifs de force majeure'),
    (NULL,1,'365','Bourses d''études pour les enfants','fixe',0,'Caractère Social & Familial',0,'61712','Selon mérite et critères sociaux','Exonéré si versé directement à l''établissement','Facture de l''école/université, attestation de réussite'),
    (NULL,1,'366','Indemnité légale de licenciement','fixe',0,'Rupture & Fin de Contrat',0,'61715','Barème du Code du Travail','Totalement exonérée de CNSS et DGI','Lettre de licenciement, PV de l''inspecteur du travail / tribunal'),
    (NULL,1,'367','Indemnité de licenciement abusive','fixe',0,'Rupture & Fin de Contrat',0,'61715','Fixée par tribunal ou conciliation','Exonérée selon la limite légale ou judiciaire','Jugement définitif ou PV de conciliation légalisé'),
    (NULL,1,'368','Indemnité de départ volontaire / Retraite','fixe',0,'Rupture & Fin de Contrat',0,'61715','Plafonds selon barème légal','Exonérée sous conditions de l''accord DGI/CNSS','Convention de départ volontaire signée et légalisée'),
    (NULL,1,'369','Indemnité de préavis (dispensé)','fixe',0,'Rupture & Fin de Contrat',0,'61715','Montant correspondant aux salaires','Assujettie sauf cas spécifiques d''exonération globale','Lettre de dispense de préavis'),
    (NULL,1,'370','Prime de fin de carrière','fixe',0,'Rupture & Fin de Contrat',0,'61715','Selon convention collective','Exonérée si assimilée à l''indemnité de départ','Notification de mise à la retraite'),
    (NULL,1,'371','Indemnité compensatrice de logement','fixe',0,'Rupture & Fin de Contrat',0,'61715','Frais réels ou barème','Exonérée si intégrée aux dommages et intérêts','Protocole d''accord transactionnel'),
    (NULL,1,'372','Indemnité de non-concurrence','fixe',0,'Rupture & Fin de Contrat',0,'61715','Fixée par contrat','Exonérée si qualifiée de dommages et intérêts','Clause contractuelle et reçu pour solde de tout compte'),
    (NULL,1,'373','Indemnité de clientèle (VRP)','fixe',0,'Rupture & Fin de Contrat',0,'61715','Selon préjudice commercial','Exonérée selon le Code du Travail','Calcul de la perte de clientèle validé par expert/tribunal'),
    (NULL,1,'374','Indemnité de reconversion professionnelle','fixe',0,'Rupture & Fin de Contrat',0,'61715','Prise en charge de la formation','Exonérée si versée au centre de formation','Facture du centre de formation, plan de sauvegarde de l''emploi'),
    (NULL,1,'375','Indemnité de chômage technique / Partiel','fixe',0,'Rupture & Fin de Contrat',0,'61715','Selon autorisations réglementaires','Exonérée en période de crise majeure','Autorisation du gouverneur ou décision ministérielle'),
    (NULL,1,'376','Indemnité transactionnelle globale','fixe',0,'Rupture & Fin de Contrat',0,'61715','Limite des dommages légaux','Exonérée à hauteur des plafonds légaux','Protocole de transaction enregistré auprès des autorités'),
    (NULL,1,'377','Prime de tutorat / Fin de projet','fixe',0,'Rupture & Fin de Contrat',0,'61713','Forfait contractuel','Exonéré si lié à un transfert d''outils de fin de contrat','Rapport de fin de mission validé par l''entreprise');

-- -----------------------------------------------------------
-- Rubriques retenues globales (applicables à toutes les sociétés)
-- -----------------------------------------------------------
INSERT INTO rubriques_retenues (societe_id, is_global, code, libelle, type_montant, valeur_defaut) VALUES
    (NULL, 1, 'AVANCE',          'Avance sur salaire',   'fixe', 0),
    (NULL, 1, 'PRET',            'Prêt personnel',       'fixe', 0),
    (NULL, 1, 'PRET_LOGEMENT',   'Prêt logement',        'fixe', 0),
    (NULL, 1, 'COTIS_SYNDICALE', 'Cotisation syndicale', 'fixe', 0),
    (NULL, 1, 'PENSION_ALIMENT', 'Pension alimentaire',  'fixe', 0),
    (NULL, 1, 'SAISIE_ARRET',    'Saisie-arrêt',         'fixe', 0);

-- -----------------------------------------------------------
-- Barème d'ancienneté (par société)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS bareme_anciennete (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    societe_id      INT UNSIGNED        NOT NULL,
    annees_min      TINYINT UNSIGNED    NOT NULL,
    annees_max      TINYINT UNSIGNED    NOT NULL,
    taux            DECIMAL(5,2)        NOT NULL,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (societe_id) REFERENCES societes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- Configuration congé annuel (par société)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS conge_annuel (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    societe_id      INT UNSIGNED        NOT NULL UNIQUE,
    jours_par_mois  DECIMAL(4,2)        NOT NULL DEFAULT 1.50,
    report_autorise TINYINT(1)          NOT NULL DEFAULT 1,
    report_max      TINYINT UNSIGNED    NOT NULL DEFAULT 15,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (societe_id) REFERENCES societes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- Jours fériés (par société)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS jours_feries (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    societe_id      INT UNSIGNED        NOT NULL,
    nom             VARCHAR(100)        NOT NULL,
    jour            TINYINT UNSIGNED    NOT NULL,
    mois            TINYINT UNSIGNED    NOT NULL,
    type            ENUM('fixe','variable') NOT NULL DEFAULT 'fixe',
    actif           TINYINT(1)          NOT NULL DEFAULT 1,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (societe_id) REFERENCES societes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO jours_feries (societe_id, nom, jour, mois, type, actif) VALUES
    (1, 'Jour de l''an', 1, 1, 'fixe', 1),
    (1, 'Fête du Trône', 30, 7, 'fixe', 1),
    (1, 'Fête de la révolution du Roi et du peuple', 20, 8, 'fixe', 1),
    (1, 'Anniversaire du Roi Mohammed VI', 21, 8, 'fixe', 1),
    (1, 'Fête de la Marche Verte', 6, 11, 'fixe', 1),
    (1, 'Fête de l''Indépendance', 18, 11, 'fixe', 1),
    (1, 'Fête du Travail', 1, 5, 'fixe', 1),
    (1, 'Aïd el-Fitr', 1, 1, 'variable', 1),
    (1, 'Aïd el-Adha', 1, 1, 'variable', 1),
    (1, '1er Moharram (Nouvel An islamique)', 1, 1, 'variable', 1),
    (1, 'Aïd al-Mawlid (Anniversaire du Prophète)', 1, 1, 'variable', 1);

-- -----------------------------------------------------------
-- Barème heures supplémentaires (par société)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS bareme_heures_sup (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    societe_id      INT UNSIGNED        NOT NULL UNIQUE,
    taux_normal     DECIMAL(5,2)        NOT NULL DEFAULT 25.00,
    taux_majore     DECIMAL(5,2)        NOT NULL DEFAULT 50.00,
    taux_jour_ferie DECIMAL(5,2)        NOT NULL DEFAULT 100.00,
    seuil_heures    TINYINT UNSIGNED    NOT NULL DEFAULT 8,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (societe_id) REFERENCES societes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- Sources légales (lois, décrets, arrêtés, notes, etc.)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS sources_legales (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code            VARCHAR(20) NOT NULL UNIQUE,
    libelle         VARCHAR(200) NOT NULL,
    type            ENUM('loi','decret','arrete','note','circulaire','convention') NOT NULL,
    organisme       VARCHAR(50) NOT NULL,
    description     TEXT,
    reference_bo    VARCHAR(50),
    date_publication DATE,
    date_effet      DATE,
    url_officiel    VARCHAR(300),
    statut          ENUM('en_vigueur','modifie','abroge') DEFAULT 'en_vigueur',
    ordre           INT DEFAULT 0
) ENGINE=InnoDB;

INSERT INTO sources_legales (code, libelle, type, organisme, description, reference_bo, date_publication, date_effet, statut, ordre) VALUES
    ('CT', 'Code du Travail (Loi n° 65-99)', 'loi', 'Inspection du Travail', 'Code du Travail marocain promulgué par la Loi n° 65-99. Régit les relations individuelles et collectives de travail.', 'BO n° 5210', '2003-09-11', '2004-06-08', 'en_vigueur', 1),
    ('DAHIR_CNSS', 'Dahir n° 1.72.184 — Régime de sécurité sociale', 'loi', 'CNSS', 'Dahir portant institution du régime de sécurité sociale au Maroc. Base légale de la CNSS.', 'BO n° 3120', '1972-07-27', '1972-10-01', 'modifie', 2),
    ('D266', 'Décret n° 2-25-266 — Application CNSS', 'decret', 'CNSS', 'Décret fixant les modalités d''application des dispositions relatives aux exonérations de cotisations CNSS.', 'BO n° 7443', '2025-04-24', '2025-10-01', 'en_vigueur', 3),
    ('A1314', 'Arrêté n° 1314-25 — Indemnités exonérées CNSS', 'arrete', 'CNSS', 'Arrêté du ministre de l''Économie et des Finances fixant la liste des indemnités exonérées de cotisations CNSS.', 'BO n° 7443', '2025-05-19', '2025-10-01', 'en_vigueur', 4),
    ('CGI', 'Code Général des Impôts', 'loi', 'DGI', 'Code Général des Impôts marocain. Définit les règles d''assujettissement à l''IR et à l''IS.', NULL, NULL, NULL, 'en_vigueur', 5),
    ('N16_2017', 'Note n° 16/2017 — Indemnités exonérées IR', 'note', 'DGI', 'Note circulaire de la DGI précisant la liste des indemnités exonérées de l''impôt sur le revenu.', NULL, '2017-01-01', '2017-01-01', 'en_vigueur', 6),
    ('LF', 'Loi de Finances', 'loi', 'DGI', 'Loi de Finances annuelle. Modifie chaque année les dispositions fiscales (barème IR, plafonds, etc.).', NULL, NULL, NULL, 'en_vigueur', 7),
    ('CCOLL', 'Conventions collectives sectorielles', 'convention', 'Inspection du Travail', 'Conventions collectives de travail applicables par secteur d''activité. Peuvent prévoir des indemnités spécifiques.', NULL, NULL, NULL, 'en_vigueur', 8);

-- -----------------------------------------------------------
-- Liaison rubriques de gains → sources légales (articles)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS rubrique_sources_articles (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rubrique_id     INT UNSIGNED NOT NULL,
    source_id       INT UNSIGNED NOT NULL,
    article         VARCHAR(50) NOT NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_rubrique_source (rubrique_id, source_id, article),
    FOREIGN KEY (rubrique_id) REFERENCES rubriques_gains(id) ON DELETE CASCADE,
    FOREIGN KEY (source_id) REFERENCES sources_legales(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Articles par rubrique (mapping)
INSERT INTO rubrique_sources_articles (rubrique_id, source_id, article)
SELECT r.id, s.id, a.article
FROM rubriques_gains r
CROSS JOIN (SELECT 'CGI' AS c, 'Art. 57-1°' AS article, '330' AS code UNION ALL
            SELECT 'CGI', 'Art. 57-1°', '331' UNION ALL
            SELECT 'CGI', 'Art. 57-1°', '334' UNION ALL
            SELECT 'CGI', 'Art. 57-1°', '337' UNION ALL
            SELECT 'CGI', 'Art. 57-1°', '339' UNION ALL
            SELECT 'CGI', 'Art. 57-1°', '340' UNION ALL
            SELECT 'CGI', 'Art. 57-1°', '341' UNION ALL
            SELECT 'CGI', 'Art. 57-1°', '342' UNION ALL
            SELECT 'CGI', 'Art. 57-1°', '343' UNION ALL
            SELECT 'CGI', 'Art. 57-1°', '344' UNION ALL
            SELECT 'CGI', 'Art. 57-1°', '345' UNION ALL
            SELECT 'CGI', 'Art. 57-1°', '346' UNION ALL
            SELECT 'CGI', 'Art. 57-1°', '347' UNION ALL
            SELECT 'CGI', 'Art. 57-1°', '348' UNION ALL
            SELECT 'CGI', 'Art. 57-1°', '349' UNION ALL
            SELECT 'CGI', 'Art. 57-1°', '350' UNION ALL
            SELECT 'CGI', 'Art. 57-1°', '351' UNION ALL
            SELECT 'CGI', 'Art. 57-1°', '352' UNION ALL
            SELECT 'CGI', 'Art. 57-1°', '353' UNION ALL
            SELECT 'CGI', 'Art. 57-1°', '354' UNION ALL
            SELECT 'CGI', 'Art. 57-1°', '355' UNION ALL
            SELECT 'CGI', 'Art. 57-1°', '356' UNION ALL
            SELECT 'CGI', 'Art. 57-1°', '357' UNION ALL
            SELECT 'CGI', 'Art. 57-1°', '358' UNION ALL
            SELECT 'CGI', 'Art. 57-1°', '359' UNION ALL
            SELECT 'CGI', 'Art. 57-1°', '360' UNION ALL
            SELECT 'CGI', 'Art. 57-1°', '361' UNION ALL
            SELECT 'CGI', 'Art. 57-1°', '362' UNION ALL
            SELECT 'CGI', 'Art. 57-1°', '363' UNION ALL
            SELECT 'CGI', 'Art. 57-1°', '364' UNION ALL
            SELECT 'CGI', 'Art. 57-1°', '365' UNION ALL
            SELECT 'CGI', 'Art. 57-7°', '366' UNION ALL
            SELECT 'CGI', 'Art. 57-7°', '367' UNION ALL
            SELECT 'CGI', 'Art. 57-7°', '368' UNION ALL
            SELECT 'CGI', 'Art. 57 (soumis)', '501' UNION ALL
            SELECT 'CGI', 'Art. 57 (soumis)', '502' UNION ALL
            SELECT 'CGI', 'Art. 57 (soumis)', '503' UNION ALL
            SELECT 'CGI', 'Art. 57 (soumis)', '504' UNION ALL
            SELECT 'CGI', 'Art. 57 (soumis)', '505' UNION ALL
            SELECT 'CT', 'Art. 53', '366' UNION ALL
            SELECT 'CT', 'Art. 41', '367' UNION ALL
            SELECT 'CT', 'Art. 43', '369' UNION ALL
            SELECT 'CT', 'Art. 345-353', '501' UNION ALL
            SELECT 'CT', 'Art. 345-353', '502' UNION ALL
            SELECT 'CT', 'Art. 345-353', '503' UNION ALL
            SELECT 'CT', 'Art. 345-353', '504' UNION ALL
            SELECT 'CT', 'Art. 345', '505' UNION ALL
            SELECT 'A1314', 'Titre I', '330' UNION ALL
            SELECT 'A1314', 'Titre I', '334' UNION ALL
            SELECT 'A1314', 'Titre I', '337' UNION ALL
            SELECT 'A1314', 'Titre I', '339' UNION ALL
            SELECT 'A1314', 'Titre I', '340' UNION ALL
            SELECT 'A1314', 'Titre I', '341' UNION ALL
            SELECT 'A1314', 'Titre I', '342' UNION ALL
            SELECT 'A1314', 'Titre I', '351' UNION ALL
            SELECT 'A1314', 'Titre I', '352' UNION ALL
            SELECT 'A1314', 'Titre I', '353' UNION ALL
            SELECT 'A1314', 'Titre II', '331' UNION ALL
            SELECT 'A1314', 'Titre II', '343' UNION ALL
            SELECT 'A1314', 'Titre II', '344' UNION ALL
            SELECT 'A1314', 'Titre II', '345' UNION ALL
            SELECT 'A1314', 'Titre II', '346' UNION ALL
            SELECT 'A1314', 'Titre II', '347' UNION ALL
            SELECT 'A1314', 'Titre II', '348' UNION ALL
            SELECT 'A1314', 'Titre II', '349' UNION ALL
            SELECT 'A1314', 'Titre II', '350' UNION ALL
            SELECT 'A1314', 'Titre II', '360' UNION ALL
            SELECT 'A1314', 'Titre V', '354' UNION ALL
            SELECT 'A1314', 'Titre V', '355' UNION ALL
            SELECT 'A1314', 'Titre V', '356' UNION ALL
            SELECT 'A1314', 'Titre V', '357' UNION ALL
            SELECT 'A1314', 'Titre V', '358' UNION ALL
            SELECT 'A1314', 'Titre V', '359' UNION ALL
            SELECT 'A1314', 'Titre V', '361' UNION ALL
            SELECT 'A1314', 'Titre V', '362' UNION ALL
            SELECT 'A1314', 'Titre V', '363' UNION ALL
            SELECT 'A1314', 'Titre V', '364' UNION ALL
            SELECT 'A1314', 'Titre V', '365' UNION ALL
            SELECT 'A1314', 'Titre III', '366' UNION ALL
            SELECT 'A1314', 'Titre III', '367' UNION ALL
            SELECT 'A1314', 'Titre III', '368' UNION ALL
            SELECT 'A1314', 'Titre III', '369' UNION ALL
            SELECT 'A1314', 'Titre III', '370' UNION ALL
            SELECT 'A1314', 'Titre III', '371' UNION ALL
            SELECT 'A1314', 'Titre III', '372' UNION ALL
            SELECT 'A1314', 'Titre III', '373' UNION ALL
            SELECT 'A1314', 'Titre III', '374' UNION ALL
            SELECT 'A1314', 'Titre III', '375' UNION ALL
            SELECT 'A1314', 'Titre III', '376' UNION ALL
            SELECT 'A1314', 'Titre VII', '377') AS a
JOIN sources_legales s ON s.code = a.c
WHERE r.code = a.code AND r.societe_id IS NULL;

-- -----------------------------------------------------------
-- Barème SMIG / SMAG (par société, par année)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS bareme_smig_smag (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    societe_id      INT UNSIGNED        NOT NULL,
    annee           INT                 NOT NULL,
    type            ENUM('SMIG','SMAG') NOT NULL,
    horaire         DECIMAL(10,2)       NOT NULL DEFAULT 0.00,
    mensuel         DECIMAL(10,2)       NOT NULL DEFAULT 0.00,
    date_effet      DATE                DEFAULT NULL,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (societe_id) REFERENCES societes(id) ON DELETE CASCADE,
    UNIQUE KEY unique_societe_annee_type (societe_id, annee, type)
) ENGINE=InnoDB;
