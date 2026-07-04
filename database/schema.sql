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
    rib                 VARCHAR(40),
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
    poste               VARCHAR(150),
    type_contrat        ENUM('CDI', 'CDD', 'stage', 'interim') NOT NULL DEFAULT 'CDI',
    salaire_base        DECIMAL(10,2)       NOT NULL DEFAULT 0.00,
    type_salaire        ENUM('mensuel', 'horaire', 'journalier') NOT NULL DEFAULT 'mensuel',
    frequence_paiement  ENUM('mensuel', 'quinzaine', 'hebdomadaire') NOT NULL DEFAULT 'mensuel',
    mode_paiement       ENUM('virement', 'cheque', 'especes') NOT NULL DEFAULT 'virement',
    rib                 VARCHAR(40),
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
    taux_ams_salarial           DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    taux_ams_patronal           DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    taux_allocations_familiales DECIMAL(5,2) NOT NULL DEFAULT 6.40,
    taux_prestations_sociales   DECIMAL(5,2) NOT NULL DEFAULT 13.46,
    taxe_formation              DECIMAL(5,2) NOT NULL DEFAULT 1.60,
    participation_amo           DECIMAL(5,2) NOT NULL DEFAULT 1.85,
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
    imposable       TINYINT(1)          NOT NULL DEFAULT 1,
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
    ADD FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL,
    ADD FOREIGN KEY (fonction_id) REFERENCES fonctions(id) ON DELETE SET NULL;

ALTER TABLE paies
    ADD COLUMN prime_anciennete  DECIMAL(10,2)   NOT NULL DEFAULT 0.00 AFTER salaire_brut,
    ADD COLUMN sbi               DECIMAL(10,2)   NOT NULL DEFAULT 0.00 AFTER salaire_brut,
    ADD COLUMN frais_professionnels DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER sbi,
    ADD COLUMN total_gains       DECIMAL(10,2)   NOT NULL DEFAULT 0.00 AFTER montant_heures_sup;

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
-- Index utilisateur par défaut (password: admin123)
-- -----------------------------------------------------------
INSERT INTO users (nom, email, password, role)
VALUES ('Administrateur', 'admin@paie-me.ma',
        '$2y$10$DRPsKOgLy.Ib4oKPT8oX/.2gRRWXSCgQz3UdUMLbbiyYvVOnX6fhq',
        'admin');

-- -----------------------------------------------------------
-- Rubriques gains globales (applicables à toutes les sociétés)
-- -----------------------------------------------------------
INSERT INTO rubriques_gains (societe_id, is_global, code, libelle, type_montant, valeur_defaut, imposable) VALUES
    -- Gains standard (imposables)
    (NULL, 1, 'PRIME_REND',     'Prime de rendement',        'proportionnel', 10.00,  1),
    (NULL, 1, 'PRIME_OBJECTIF',  'Prime d''objectifs',        'proportionnel', 5.00,   1),
    (NULL, 1, 'PRIME_ASSIDUITE', 'Prime d''assiduité',        'fixe',          300.00, 1),
    (NULL, 1, 'PRIME_NUIT',     'Prime de nuit',              'fixe',          250.00, 1),
    (NULL, 1, 'PRIME_13EME',    '13ème mois (prorata)',       'proportionnel', 8.33,   1),
    -- I. Transport & Déplacement (exonérés)
    (NULL, 1, '330', 'Indemnité de transport urbain',                     'fixe',           500, 0),
    (NULL, 1, '331', 'Indemnité de représentation',                      'proportionnel',   10, 0),
    (NULL, 1, '334', 'Indemnité kilométrique',                           'fixe',             0, 0),
    (NULL, 1, '337', 'Indemnité de tournée',                             'fixe',          1500, 0),
    (NULL, 1, '339', 'Indemnité de déplacement justifiée',               'fixe',             0, 0),
    (NULL, 1, '340', 'Indemnité de déplacement forfaitaire ponctuelle',  'fixe',             0, 0),
    (NULL, 1, '341', 'Indemnité de déplacement forfaitaire régulière',   'fixe',          5000, 0),
    (NULL, 1, '342', 'Indemnité de transport hors urbain',               'fixe',           750, 0),
    (NULL, 1, '343', 'Prime d''outillage',                              'fixe',           100, 0),
    (NULL, 1, '344', 'Prime de salissure',                              'fixe',           210, 0),
    (NULL, 1, '345', 'Prime d''usure de vêtements / Tenue',             'fixe',             0, 0),
    (NULL, 1, '346', 'Indemnité de panier / Panier de nuit',            'fixe',             0, 0),
    (NULL, 1, '347', 'Indemnité de pénibilité',                          'fixe',             0, 0),
    (NULL, 1, '348', 'Indemnité de risque / Danger',                     'fixe',             0, 0),
    (NULL, 1, '349', 'Indemnité d''astreinte',                          'fixe',             0, 0),
    (NULL, 1, '350', 'Indemnité de garde',                               'fixe',             0, 0),
    (NULL, 1, '351', 'Voiture de fonction ou de service',                'fixe',             0, 0),
    (NULL, 1, '352', 'Indemnité de voyage à l''étranger',               'fixe',             0, 0),
    (NULL, 1, '353', 'Indemnité de déménagement / mutation',             'fixe',             0, 0),
    (NULL, 1, '354', 'Allocations familiales additionnelles',            'fixe',             0, 0),
    (NULL, 1, '355', 'Allocation de naissance',                          'fixe',             0, 0),
    (NULL, 1, '356', 'Allocation de mariage',                            'fixe',             0, 0),
    (NULL, 1, '357', 'Allocation de décès / Obsèques',                   'fixe',             0, 0),
    (NULL, 1, '358', 'Prime de scolarité / Rentrée scolaire',            'fixe',           400, 0),
    (NULL, 1, '359', 'Bons d''achat / Cadeaux de fin d''année',         'fixe',             0, 0),
    (NULL, 1, '360', 'Indemnité de caisse (responsabilité pécuniaire)', 'fixe',           190, 0),
    (NULL, 1, '361', 'Subvention de cantine / Titres repas',            'fixe',             0, 0),
    (NULL, 1, '362', 'Prise en charge des frais médicaux',              'fixe',             0, 0),
    (NULL, 1, '363', 'Aide aux vacances / Estivage',                    'fixe',             0, 0),
    (NULL, 1, '364', 'Secours exceptionnel / Social',                   'fixe',             0, 0),
    (NULL, 1, '365', 'Bourses d''études pour les enfants',              'fixe',             0, 0),
    (NULL, 1, '366', 'Indemnité légale de licenciement',                'fixe',             0, 0),
    (NULL, 1, '367', 'Indemnité de licenciement abusive',               'fixe',             0, 0),
    (NULL, 1, '368', 'Indemnité de départ volontaire / Retraite',       'fixe',             0, 0),
    (NULL, 1, '369', 'Indemnité de préavis (dispensé)',                 'fixe',             0, 0),
    (NULL, 1, '370', 'Prime de fin de carrière',                        'fixe',             0, 0),
    (NULL, 1, '371', 'Indemnité compensatrice de logement',             'fixe',             0, 0),
    (NULL, 1, '372', 'Indemnité de non-concurrence',                    'fixe',             0, 0),
    (NULL, 1, '373', 'Indemnité de clientèle (VRP)',                    'fixe',             0, 0),
    (NULL, 1, '374', 'Indemnité de reconversion professionnelle',       'fixe',             0, 0),
    (NULL, 1, '375', 'Indemnité de chômage technique / Partiel',        'fixe',             0, 0),
    (NULL, 1, '376', 'Indemnité transactionnelle globale',              'fixe',             0, 0),
    (NULL, 1, '377', 'Prime de tutorat / Fin de projet',                'fixe',             0, 0);

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
