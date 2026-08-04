<?php
/**
 * Seed de données démo — exécuté automatiquement par run.ps1
 * si la base est vide (aucune société).
 * Usage CLI : php database/seed_demo.php [dbname]
 * La logique est exposée via seed_demo_database(PDO $pdo) pour être
 * réutilisée (ex: création de la base démo depuis l'application).
 */

require_once __DIR__ . '/../vendor/autoload.php';

spl_autoload_register(function (string $class) {
    $prefixes = [
        'Core\\'        => __DIR__ . '/../Core/',
        'Controllers\\' => __DIR__ . '/../controllers/',
    ];
    foreach ($prefixes as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) === 0) {
            $relative = substr($class, $len);
            $file = $baseDir . str_replace('\\', '/', $relative) . '.php';
            if (file_exists($file)) {
                require $file;
                return;
            }
        }
    }
});

use Core\PaieCalculator;
use Controllers\BulletinController;

/**
 * Insère les données démo dans une connexion PDO déjà ouverte.
 * Retourne un tableau de statistiques. Ne fait aucun echo, aucune sortie.
 */
function seed_demo_database(PDO $pdo): array
{
    $nbSocietes = (int) $pdo->query("SELECT COUNT(*) FROM societes")->fetchColumn();
    if ($nbSocietes > 0) {
        return ['skip' => true, 'nb_societes' => $nbSocietes];
    }

    // ── Société ──
    $stmt = $pdo->prepare("INSERT INTO societes (user_id, raison_sociale, forme_juridique, ice, if_fiscal, rc, tp, cnss, adresse, ville, telephone, email, site_web, banque, agence, rib) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        1, 'TechMaroc Solutions', 'SARL', 'ICE001234567', 'IF123456', 'RC78901', 'TP34567', 'CNSS1001',
        '12 Rue des Innovateurs, Quartier des Affaires', 'Casablanca', '0522123456', 'contact@techmaroc.ma',
        'www.techmaroc.ma', 'Attijariwafa Bank', 'Agence Anfa', '0078100001234000000001234',
    ]);
    $societeId = (int) $pdo->lastInsertId();

    // ── Services ──
    $services = [];
    foreach (['Direction Générale', 'Informatique', 'Ressources Humaines', 'Comptabilité', 'Commercial'] as $nomService) {
        $pdo->prepare("INSERT INTO services (societe_id, nom, description) VALUES (?, ?, ?)")
            ->execute([$societeId, $nomService, null]);
        $services[$nomService] = (int) $pdo->lastInsertId();
    }

    // ── Fonctions ──
    $fonctions = [];
    $fonctionData = [
        'Direction Générale' => ['Directeur Général', 'Directeur Administratif'],
        'Informatique'       => ['Développeur Full Stack', 'Administrateur Systèmes'],
        'Ressources Humaines'=> ['Responsable RH', 'Gestionnaire Paie'],
        'Comptabilité'       => ['Comptable', 'Contrôleur de Gestion'],
        'Commercial'         => ['Commercial Senior', 'Assistant Commercial'],
    ];
    foreach ($fonctionData as $service => $noms) {
        foreach ($noms as $nomFonction) {
            $pdo->prepare("INSERT INTO fonctions (societe_id, service_id, nom) VALUES (?, ?, ?)")
                ->execute([$societeId, $services[$service], $nomFonction]);
            $fonctions[$nomFonction] = (int) $pdo->lastInsertId();
        }
    }

    // ── Salariés ──
    $salariesData = [
        // 0 matricule, 1 nom, 2 prenom, 3 sexe, 4 poste, 5 service, 6 date_naissance,
        // 7 date_embauche, 8 type_contrat, 9 salaire_base, 10 situation, 11 nb_enfants,
        // 12 transp, 13 panier, 14 repr, 15 logement, 16 avances, 17 mutuelle
        ['TMS001', 'Benali',   'Karim',   'M', 'Développeur Full Stack',  'Informatique',       '1990-03-15', '2022-01-10', 'CDI',   15000.00, 'marie',     2, 500.00, 780.00, 1500.00, 0.00, 0.00, 0.00],
        ['TMS002', 'Alaoui',   'Fatima',  'F', 'Directeur Administratif', 'Direction Générale', '1985-07-22', '2020-06-01', 'CDI',   22000.00, 'marie',     3, 500.00, 780.00, 2200.00, 0.00, 0.00, 0.00],
        ['TMS003', 'Idrissi',  'Youssef', 'M', 'Développeur Full Stack',  'Informatique',       '1995-11-08', '2023-03-15', 'CDI',   12000.00, 'celibataire', 0, 500.00, 780.00, 0.00,    0.00, 0.00, 0.00],
        ['TMS004', 'Bennani',  'Sara',    'F', 'Gestionnaire Paie',       'Ressources Humaines','1992-09-30', '2021-09-01', 'CDI',   13000.00, 'celibataire', 0, 500.00, 780.00, 1000.00, 0.00, 0.00, 0.00],
        ['TMS005', 'Ouazzani', 'Hicham',  'M', 'Commercial Senior',       'Commercial',         '1988-05-12', '2019-11-20', 'CDI',   28000.00, 'marie',     4, 500.00, 780.00, 2800.00, 0.00, 0.00, 0.00],
        ['TMS006', 'El Amrani','Nadia',   'F', 'Comptable',               'Comptabilité',       '1997-01-25', '2024-02-01', 'CDD',    9500.00, 'celibataire', 0, 500.00, 780.00, 0.00,    0.00, 0.00, 0.00],
    ];
    $stmtSalarie = $pdo->prepare("INSERT INTO salaries (societe_id, service_id, fonction_id, matricule, nom_famille, prenom, sexe, adresse, date_naissance, lieu_naissance, date_embauche, cin, cnss, situation_familiale, nb_enfants, enfants_a_charge, personnes_a_charge, poste, type_contrat, salaire_base, type_salaire, frequence_paiement, mode_paiement, rib, indemnite_transport, indemnite_panier, indemnite_representation, avantage_logement, avances_salaire, mutuelle) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $salarieIds = [];
    $adresses = [
        '15 Rue Atlas, Casablanca', '22 Avenue Hassan II, Casablanca', '8 Rue des Oliviers, Casablanca',
        '5 Boulevard Mohammed VI, Casablanca', '12 Rue de la Gare, Casablanca', '30 Rue Oued Zem, Casablanca',
    ];
    $cins = ['AB123456', 'CD234567', 'EF345678', 'GH456789', 'IJ567890', 'KL678901'];
    foreach ($salariesData as $i => $d) {
        $stmtSalarie->execute([
            $societeId, $services[$d[5]], $fonctions[$d[4]],
            $d[0], $d[1], $d[2], $d[3], $adresses[$i], $d[6], 'Casablanca', $d[7], $cins[$i], 'CNSS200' . ($i + 1),
            $d[10], $d[11], $d[11], $d[11], $d[4], $d[8], $d[9], 'mensuel', 'mensuel', 'virement', 'RIB00' . ($i + 1),
            $d[12], $d[13], $d[14], $d[15], $d[16], $d[17],
        ]);
        $salarieIds[] = (int) $pdo->lastInsertId();
    }

    // ── Paramètres CNSS / AMO ──
    $pdo->prepare("INSERT INTO parametres_cnss_amo (societe_id) VALUES (?)")->execute([$societeId]);

    // ── Barème d'ancienneté ──
    foreach ([[0, 1, 0], [2, 4, 5], [5, 11, 10], [12, 19, 15], [20, 24, 20], [25, 99, 25]] as $tr) {
        $pdo->prepare("INSERT INTO bareme_anciennete (societe_id, annees_min, annees_max, taux) VALUES (?, ?, ?, ?)")
            ->execute([$societeId, $tr[0], $tr[1], $tr[2]]);
    }

    // ── Congé annuel ──
    $pdo->prepare("INSERT INTO conge_annuel (societe_id, jours_par_mois, report_autorise, report_max) VALUES (?, 1.5, 1, 15)")
        ->execute([$societeId]);

    // ── Barème heures supplémentaires ──
    $pdo->prepare("INSERT INTO bareme_heures_sup (societe_id, taux_normal, taux_majore, taux_jour_ferie, seuil_heures) VALUES (?, 25, 50, 100, 8)")
        ->execute([$societeId]);

    // ── Barème SMIG / SMAG ──
    $annee = (int) date('Y');
    foreach (['SMIG' => [15.00, 3000.00], 'SMAG' => [67.00, 1447.20]] as $type => $vals) {
        $pdo->prepare("INSERT INTO bareme_smig_smag (societe_id, annee, type, horaire, mensuel) VALUES (?, ?, ?, ?, ?)")
            ->execute([$societeId, $annee, $type, $vals[0], $vals[1]]);
    }

    // ── Jours fériés fixes ──
    foreach ([['Jour de l\'an', 1, 1], ['Manifeste de l\'Indépendance', 11, 1], ['Fête du Trône', 30, 7], ['Fête des Oueds', 14, 8], ['Anniversaire de la Révolution', 20, 8], ['Marche Verte', 6, 11]] as $jf) {
        $pdo->prepare("INSERT INTO jours_feries (societe_id, nom, jour, mois, type, actif) VALUES (?, ?, ?, ?, 'fixe', 1)")
            ->execute([$societeId, $jf[0], $jf[1], $jf[2]]);
    }

    // ── Gains custom (2 salariés) ──
    $gainsAuto = $pdo->query("SELECT * FROM rubriques_gains WHERE (societe_id IS NULL OR societe_id = $societeId) AND actif = 1 AND categorie = 'Gain standard' ORDER BY code")->fetchAll();
    foreach (array_slice($salarieIds, 0, 2) as $sid) {
        foreach (array_slice($gainsAuto, 0, 2) as $g) {
            $pdo->prepare("INSERT INTO salarie_gains (salarie_id, rubrique_id, montant, actif) VALUES (?, ?, ?, 1)")
                ->execute([$sid, $g['id'], 0]);
        }
    }

    // ── Période en cours + calcul des paies ──
    $moisCourant = (int) date('n');
    $anneeCourante = (int) date('Y');
    $dateDebut = sprintf('%04d-%02d-01', $anneeCourante, $moisCourant);
    $dateFin = date('Y-m-t', strtotime($dateDebut));

    $pdo->prepare("INSERT INTO periodes (societe_id, mois, annee, date_debut, date_fin) VALUES (?, ?, ?, ?, ?)")
        ->execute([$societeId, $moisCourant, $anneeCourante, $dateDebut, $dateFin]);
    $periodeId = (int) $pdo->lastInsertId();

    $calculator = new PaieCalculator($pdo);
    $cnssParams = $pdo->query("SELECT * FROM parametres_cnss_amo WHERE societe_id = $societeId")->fetch();
    $baremeHS = $pdo->query("SELECT * FROM bareme_heures_sup WHERE societe_id = $societeId")->fetch();
    $retenues = $pdo->query("SELECT * FROM rubriques_retenues WHERE (societe_id IS NULL OR societe_id = $societeId) AND actif = 1 ORDER BY is_global, code")->fetchAll();

    $salaries = $pdo->query("SELECT * FROM salaries WHERE societe_id = $societeId AND actif = 1 ORDER BY id")->fetchAll();

    $stmtPaie = $pdo->prepare("INSERT INTO paies (periode_id, salarie_id, societe_id, jours_travailles, salaire_brut, sbi, prime_anciennete, salaire_plafonne_cnss, indemnite_transport, indemnite_panier, indemnite_representation, avantage_logement, total_gains, heures_supplementaires, montant_heures_sup, montant_hs_25, montant_hs_50, montant_hs_100, heures_sup_25, heures_sup_50, heures_sup_100, cnss_salariale, amo_salariale, mutuelle, sni, ir, deductions_familiales, autres_retenues, net_avant_retenues, net_a_payer, cnss_patronale, amo_patronale, frais_professionnels) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    foreach ($salaries as $s) {
        $gainsSalarie = $pdo->query("SELECT sg.salarie_id, rg.id as rubrique_id, rg.code, rg.libelle, rg.type_montant, rg.valeur_defaut, rg.imposable FROM salarie_gains sg JOIN rubriques_gains rg ON sg.rubrique_id = rg.id WHERE sg.salarie_id = {$s['id']} AND sg.actif = 1 AND rg.actif = 1 ORDER BY rg.code")->fetchAll();
        $mergedGains = [];
        foreach ($gainsAuto as $g) {
            $mergedGains[$g['code']] = $g;
        }
        foreach ($gainsSalarie as $sg) {
            $mergedGains[$sg['code']] = $sg;
        }
        $mergedGains = array_values($mergedGains);

        $c = $calculator->calculerPaie($s, $cnssParams, $dateFin, 0, 0, 0, $mergedGains, $retenues, $dateDebut, $baremeHS, null, 0, 0, []);

        $stmtPaie->execute([
            $periodeId, $s['id'], $societeId, $c['joursTravailles'],
            $c['sb'], $c['sbi'], $c['primeAnciennete'], $c['plafonne'],
            $c['transport'], $c['panier'], $c['representation'], $c['logement'],
            $c['totalGains'],
            $c['heuresSup'], $c['montantHeuresSup'],
            $c['montantHS25'], $c['montantHS50'], $c['montantHS100'],
            $c['heuresSup25'], $c['heuresSup50'], $c['heuresSup100'],
            $c['cnss'], $c['amo'], $c['mutuelle'], $c['sni'], $c['ir'], $c['deductionsFamiliales'],
            $c['autresRetenues'], $c['netAvant'], $c['net'],
            $c['cnssPatronale'], $c['amoPatronale'], $c['fraisPro'],
        ]);
    }

    // ── Bulletins ──
    $nbBulletins = BulletinController::genererPourPeriode($periodeId, $pdo);

    return [
        'skip' => false,
        'societe_id' => $societeId,
        'nb_salaries' => count($salaries),
        'periode_id' => $periodeId,
        'mois' => $moisCourant,
        'annee' => $anneeCourante,
        'nb_bulletins' => $nbBulletins,
    ];
}

if (PHP_SAPI === 'cli' && !defined('SEED_INCLUDED')) {
    $dbname = $argv[1] ?? 'paie_me';
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=$dbname;charset=utf8mb4", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $res = seed_demo_database($pdo);
    if (!empty($res['skip'])) {
        echo "  + données déjà présentes ({$res['nb_societes']} société(s)) — seed ignoré\n";
    } else {
        echo "  + société + services + fonctions + {$res['nb_salaries']} salariés créés\n";
        echo '  + période ' . str_pad((string)$res['mois'], 2, '0', STR_PAD_LEFT) . "/{$res['annee']} avec {$res['nb_salaries']} paies calculées\n";
        echo "  + {$res['nb_bulletins']} bulletins générés\n";
        echo "Seed terminé.\n";
    }
    exit(0);
}
