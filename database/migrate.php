<?php
/**
 * Migration script — exécuter après chaque pull pour mettre à jour la base
 * Usage : php database/migrate.php
 *
 * Vérifie les colonnes manquantes et applique les ALTER nécessaires.
 */

$p = new PDO("mysql:host=127.0.0.1;dbname=paie_me;charset=utf8mb4", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$migrations = [
    // is_global + societe_id nullable sur rubriques_gains
    "rubriques_gains_is_global" => "
        ALTER TABLE rubriques_gains
        ADD COLUMN is_global TINYINT(1) NOT NULL DEFAULT 0
        AFTER societe_id
    ",
    "rubriques_gains_societe_id_nullable" => "
        ALTER TABLE rubriques_gains
        MODIFY COLUMN societe_id INT UNSIGNED DEFAULT NULL
    ",
    // is_global + societe_id nullable sur rubriques_retenues
    "rubriques_retenues_is_global" => "
        ALTER TABLE rubriques_retenues
        ADD COLUMN is_global TINYINT(1) NOT NULL DEFAULT 0
        AFTER societe_id
    ",
    "rubriques_retenues_societe_id_nullable" => "
        ALTER TABLE rubriques_retenues
        MODIFY COLUMN societe_id INT UNSIGNED DEFAULT NULL
    ",
    // Barème IR — on remplace les anciennes valeurs
    "bareme_ir_2025" => "
        REPLACE INTO bareme_ir (min, max, taux, deduction, type) VALUES
            (0.00, 3333.33, 0, 0, 'mensuel'),
            (3333.34, 5000.00, 10, 333.33, 'mensuel'),
            (5000.01, 6666.67, 20, 833.33, 'mensuel'),
            (6666.68, 8333.33, 30, 1500.00, 'mensuel'),
            (8333.34, 15000.00, 34, 1833.33, 'mensuel'),
            (15000.01, 999999.99, 37, 2283.33, 'mensuel')
    ",
    // Rubriques gains globales
    "rubriques_gains_globales" => "
        INSERT IGNORE INTO rubriques_gains (societe_id, is_global, code, libelle, type_montant, valeur_defaut, imposable) VALUES
            (NULL, 1, 'PRIME_REND', 'Prime de rendement', 'proportionnel', 10.00, 1),
            (NULL, 1, 'PRIME_OBJECTIF', 'Prime d''objectifs', 'proportionnel', 5.00, 1),
            (NULL, 1, 'PRIME_ASSIDUITE', 'Prime d''assiduité', 'fixe', 300.00, 1),
            (NULL, 1, 'PRIME_NUIT', 'Prime de nuit', 'fixe', 250.00, 1),
            (NULL, 1, 'PRIME_13EME', '13ème mois (prorata)', 'proportionnel', 8.33, 1),
            (NULL, 1, '330', 'Indemnité de transport urbain', 'fixe', 500, 0),
            (NULL, 1, '331', 'Indemnité de représentation', 'proportionnel', 10, 0),
            (NULL, 1, '334', 'Indemnité kilométrique', 'fixe', 0, 0),
            (NULL, 1, '337', 'Indemnité de tournée', 'fixe', 1500, 0),
            (NULL, 1, '339', 'Indemnité de déplacement justifiée', 'fixe', 0, 0),
            (NULL, 1, '340', 'Indemnité de déplacement forfaitaire ponctuelle', 'fixe', 0, 0),
            (NULL, 1, '341', 'Indemnité de déplacement forfaitaire régulière', 'fixe', 5000, 0),
            (NULL, 1, '342', 'Indemnité de transport hors urbain', 'fixe', 750, 0),
            (NULL, 1, '343', 'Prime d''outillage', 'fixe', 100, 0),
            (NULL, 1, '344', 'Prime de salissure', 'fixe', 210, 0),
            (NULL, 1, '345', 'Prime d''usure de vêtements / Tenue', 'fixe', 0, 0),
            (NULL, 1, '346', 'Indemnité de panier / Panier de nuit', 'fixe', 0, 0),
            (NULL, 1, '347', 'Indemnité de pénibilité', 'fixe', 0, 0),
            (NULL, 1, '348', 'Indemnité de risque / Danger', 'fixe', 0, 0),
            (NULL, 1, '349', 'Indemnité d''astreinte', 'fixe', 0, 0),
            (NULL, 1, '350', 'Indemnité de garde', 'fixe', 0, 0),
            (NULL, 1, '351', 'Voiture de fonction ou de service', 'fixe', 0, 0),
            (NULL, 1, '352', 'Indemnité de voyage à l''étranger', 'fixe', 0, 0),
            (NULL, 1, '353', 'Indemnité de déménagement / mutation', 'fixe', 0, 0),
            (NULL, 1, '354', 'Allocations familiales additionnelles', 'fixe', 0, 0),
            (NULL, 1, '355', 'Allocation de naissance', 'fixe', 0, 0),
            (NULL, 1, '356', 'Allocation de mariage', 'fixe', 0, 0),
            (NULL, 1, '357', 'Allocation de décès / Obsèques', 'fixe', 0, 0),
            (NULL, 1, '358', 'Prime de scolarité / Rentrée scolaire', 'fixe', 400, 0),
            (NULL, 1, '359', 'Bons d''achat / Cadeaux de fin d''année', 'fixe', 0, 0),
            (NULL, 1, '360', 'Indemnité de caisse (responsabilité pécuniaire)', 'fixe', 190, 0),
            (NULL, 1, '361', 'Subvention de cantine / Titres repas', 'fixe', 0, 0),
            (NULL, 1, '362', 'Prise en charge des frais médicaux', 'fixe', 0, 0),
            (NULL, 1, '363', 'Aide aux vacances / Estivage', 'fixe', 0, 0),
            (NULL, 1, '364', 'Secours exceptionnel / Social', 'fixe', 0, 0),
            (NULL, 1, '365', 'Bourses d''études pour les enfants', 'fixe', 0, 0),
            (NULL, 1, '366', 'Indemnité légale de licenciement', 'fixe', 0, 0),
            (NULL, 1, '367', 'Indemnité de licenciement abusive', 'fixe', 0, 0),
            (NULL, 1, '368', 'Indemnité de départ volontaire / Retraite', 'fixe', 0, 0),
            (NULL, 1, '369', 'Indemnité de préavis (dispensé)', 'fixe', 0, 0),
            (NULL, 1, '370', 'Prime de fin de carrière', 'fixe', 0, 0),
            (NULL, 1, '371', 'Indemnité compensatrice de logement', 'fixe', 0, 0),
            (NULL, 1, '372', 'Indemnité de non-concurrence', 'fixe', 0, 0),
            (NULL, 1, '373', 'Indemnité de clientèle (VRP)', 'fixe', 0, 0),
            (NULL, 1, '374', 'Indemnité de reconversion professionnelle', 'fixe', 0, 0),
            (NULL, 1, '375', 'Indemnité de chômage technique / Partiel', 'fixe', 0, 0),
            (NULL, 1, '376', 'Indemnité transactionnelle globale', 'fixe', 0, 0),
            (NULL, 1, '377', 'Prime de tutorat / Fin de projet', 'fixe', 0, 0)
    ",
    // Rubriques retenues globales
    "rubriques_retenues_globales" => "
        INSERT IGNORE INTO rubriques_retenues (societe_id, is_global, code, libelle, type_montant, valeur_defaut) VALUES
            (NULL, 1, 'AVANCE', 'Avance sur salaire', 'fixe', 0),
            (NULL, 1, 'PRET', 'Prêt personnel', 'fixe', 0),
            (NULL, 1, 'PRET_LOGEMENT', 'Prêt logement', 'fixe', 0),
            (NULL, 1, 'COTIS_SYNDICALE', 'Cotisation syndicale', 'fixe', 0),
            (NULL, 1, 'PENSION_ALIMENT', 'Pension alimentaire', 'fixe', 0),
            (NULL, 1, 'SAISIE_ARRET', 'Saisie-arrêt', 'fixe', 0)
    ",
];

$count = 0;
foreach ($migrations as $name => $sql) {
    try {
        // Vérifier si la colonne existe déjà
        if (str_contains($name, 'is_global')) {
            $table = str_starts_with($name, 'rubriques_gains') ? 'rubriques_gains' : 'rubriques_retenues';
            $check = $p->query("SHOW COLUMNS FROM $table LIKE 'is_global'")->fetch();
            if ($check) {
                echo "   déjà fait : $name\n";
                continue;
            }
        }
        if (str_contains($name, 'societe_id_nullable')) {
            $table = str_starts_with($name, 'rubriques_gains') ? 'rubriques_gains' : 'rubriques_retenues';
            $check = $p->query("SHOW COLUMNS FROM $table LIKE 'societe_id'")->fetch();
            if ($check && $check['Null'] === 'YES') {
                echo "   déjà fait : $name\n";
                continue;
            }
        }
        // Pour les INSERT IGNORE, vérifier si une rubrique existe déjà
        if (str_contains($name, 'globales')) {
            $table = str_contains($name, 'gains') ? 'rubriques_gains' : 'rubriques_retenues';
            $existing = $p->query("SELECT COUNT(*) FROM $table WHERE is_global = 1")->fetchColumn();
            if ($existing > 0) {
                echo "   déjà fait : $name ($existing rubriques)\n";
                continue;
            }
        }
        if (str_contains($name, 'bareme')) {
            $check = $p->query("SELECT COUNT(*) FROM bareme_ir WHERE deduction = 333.33")->fetchColumn();
            if ($check > 0) {
                echo "   déjà fait : $name\n";
                continue;
            }
        }

        $p->exec($sql);
        echo "   OK : $name\n";
        $count++;
    } catch (\PDOException $e) {
        echo "   ERREUR : $name — " . $e->getMessage() . "\n";
    }
}

echo "\n$count migrations appliquées.\n";
