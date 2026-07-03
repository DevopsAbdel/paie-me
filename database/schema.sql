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
    (0.00,    3333.00,    0.00,   0.00,    'mensuel'),
    (3334.00, 5000.00,   10.00, 250.00,   'mensuel'),
    (5001.00, 6666.67,   20.00, 666.67,   'mensuel'),
    (6667.00, 8333.00,   30.00, 1166.67,  'mensuel'),
    (8334.00, 15000.00,  34.00, 1433.33,  'mensuel'),
    (15000.01, 999999.99, 37.00, 2033.33, 'mensuel'),
    (0.00,    40000.00,   0.00,   0.00,    'annuel'),
    (40001.00, 60000.00,  10.00, 4000.00,  'annuel'),
    (60001.00, 80000.00,  20.00, 10000.00, 'annuel'),
    (80001.00, 100000.00, 30.00, 18000.00, 'annuel'),
    (100001.00, 180000.00, 34.00, 22000.00, 'annuel'),
    (180000.01, 9999999.99, 37.00, 27400.00, 'annuel');

-- -----------------------------------------------------------
-- Index utilisateur par défaut (password: admin123)
-- -----------------------------------------------------------
INSERT INTO users (nom, email, password, role)
VALUES ('Administrateur', 'admin@paie-me.ma',
        '$2y$10$DRPsKOgLy.Ib4oKPT8oX/.2gRRWXSCgQz3UdUMLbbiyYvVOnX6fhq',
        'admin');
