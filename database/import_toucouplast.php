<?php
/**
 * Import TOUCOUPLAST — lecture du classeur 2026_Paie_TOUCOUPLAST.xlsm
 * et import dans paie_me : société + 19 salariés + période juillet 2026
 * + paies avec les valeurs EXACTES de la feuille 7 (méthode annuelle IR).
 *
 * Usage CLI : php database/import_toucouplast.php
 */
require __DIR__ . '/../vendor/autoload.php';

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

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Core\Crypto;

$FICHIER = 'C:/Users/Dev/Desktop/2026_Paie_TOUCOUPLAST/2026_Paie_TOUCOUPLAST.xlsm';

// ── Lecture du classeur ──
$reader = IOFactory::createReaderForFile($FICHIER);
$reader->setReadDataOnly(true);
$spreadsheet = $reader->load($FICHIER);

function cv($ws, $col, $row) { $v = $ws->getCell($col . $row)->getValue(); return $v; }
function convDate($ws, $col, $row) {
    $v = cv($ws, $col, $row);
    if ($v === null || $v === '') return null;
    if (is_numeric($v)) {
        try { return Date::excelToDateTimeObject((float)$v)->format('Y-m-d'); }
        catch (\Throwable $e) { return null; }
    }
    $s = trim((string)$v);
    if ($s === '') return null;
    $d = \DateTime::createFromFormat('d/m/Y', $s);
    if ($d) return $d->format('Y-m-d');
    $d = \DateTime::createFromFormat('Y-m-d', $s);
    if ($d) return $d->format('Y-m-d');
    return $s;
}

$ws = $spreadsheet->getSheetByName('ste infos');
$societeInfo = [
    'nom'   => (string)cv($ws,'B',3),
    'if'    => (string)cv($ws,'B',5),
    'ville' => (string)cv($ws,'E',6),
    'ice'   => (string)cv($ws,'B',7),
    'rc'    => (string)cv($ws,'B',9),
    'forme' => (string)cv($ws,'E',9),
    'cnss'  => (string)cv($ws,'B',13),
];

$ws7 = $spreadsheet->getSheetByName('7');
$rows = [];
$maxRow = $ws7->getHighestDataRow();
for ($r = 2; $r <= $maxRow; $r++) {
    $nom = cv($ws7,'D',$r);
    if ($nom === null || trim((string)$nom) === '') continue;
    $bh = cv($ws7,'BH',$r);
    // BH = formule "=...*10%" → 10% du salaire de base pour 26 jours
    $repr = 0.0;
    if ($bh !== null && $bh !== '') {
        $S = (float)cv($ws7,'S',$r);
        $repr = is_numeric($bh) ? (float)$bh : round($S * 10 / 100, 2);
    }
    $rows[] = [
        'matricule' => cv($ws7,'B',$r),
        'type'      => cv($ws7,'C',$r),
        'nom'       => trim((string)$nom),
        'prenom'    => trim((string)(cv($ws7,'E',$r) ?? '')),
        'adresse'   => trim((string)(cv($ws7,'F',$r) ?? '')),
        'naissance' => convDate($ws7,'G',$r),
        'embauche'  => convDate($ws7,'H',$r),
        'cin'       => trim((string)(cv($ws7,'J',$r) ?? '')),
        'cnss'      => trim((string)(cv($ws7,'K',$r) ?? '')),
        'fonction'  => trim((string)(cv($ws7,'M',$r) ?? '')),
        'S'  => (float)cv($ws7,'S',$r),
        'P'  => (int)cv($ws7,'P',$r),
        'Q'  => (float)(cv($ws7,'Q',$r) ?? 0),
        'R'  => (float)(cv($ws7,'R',$r) ?? 0),
        'AD' => (float)cv($ws7,'AD',$r),
        'AH' => (float)cv($ws7,'AH',$r),
        'AI' => (float)cv($ws7,'AI',$r),
        'AJ' => (float)cv($ws7,'AJ',$r),
        'AX' => (float)cv($ws7,'AX',$r),
        'AZ' => (float)cv($ws7,'AZ',$r),
        'BG' => (float)cv($ws7,'BG',$r),
        'BI' => (float)(cv($ws7,'BI',$r) ?? 0),
        'BJ' => (float)(cv($ws7,'BJ',$r) ?? 0),
        'BV' => (float)cv($ws7,'BV',$r),
        'BW' => (float)cv($ws7,'BW',$r),
        'BZ' => (float)cv($ws7,'BZ',$r),
        'CA' => (float)cv($ws7,'CA',$r),
        'CB' => (float)cv($ws7,'CB',$r),
        'CD' => (float)cv($ws7,'CD',$r),
        'repr' => $repr,
    ];
}

// ── Connexion DB ──
$cfg = require __DIR__ . '/../config/database.php';
$dsn = "mysql:host={$cfg['host']};port={$cfg['port']};dbname={$cfg['dbname']};charset={$cfg['charset']}";
$pdo = new PDO($dsn, $cfg['username'], $cfg['password'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$check = $pdo->prepare("SELECT id FROM societes WHERE ice = ? OR raison_sociale = ?");
$check->execute([$societeInfo['ice'], $societeInfo['nom']]);
if ($check->fetch()) {
    fwrite(STDERR, "ERREUR : une société avec ICE {$societeInfo['ice']} existe déjà. Import annulé.\n");
    exit(1);
}

$pdo->beginTransaction();
try {
    // ── Société ──
    $stmt = $pdo->prepare("INSERT INTO societes (user_id, raison_sociale, forme_juridique, ice, if_fiscal, rc, tp, cnss, adresse, ville) VALUES (?, ?, ?, ?, ?, ?, '', ?, NULL, ?)");
    $stmt->execute([
        1,
        $societeInfo['nom'],
        $societeInfo['forme'] ?: 'SARL',
        $societeInfo['ice'],
        $societeInfo['if'],
        $societeInfo['rc'],
        $societeInfo['cnss'],
        'CASABLANCA',
    ]);
    $societeId = (int)$pdo->lastInsertId();
    echo "Société créée : {$societeInfo['nom']} (id=$societeId)\n";

    // ── Service + fonctions ──
    $pdo->prepare("INSERT INTO services (societe_id, nom, description) VALUES (?, 'Production', NULL)")->execute([$societeId]);
    $serviceId = (int)$pdo->lastInsertId();
    $fonctions = [];
    foreach (['RESPONSABLE','TECHNICIEN','ASSISTANTE ADMINISTRATIVE','MAGAZINIER','OUVRIER','CHEFFEUR'] as $f) {
        $pdo->prepare("INSERT INTO fonctions (societe_id, service_id, nom) VALUES (?, ?, ?)")->execute([$societeId, $serviceId, $f]);
        $fonctions[$f] = (int)$pdo->lastInsertId();
    }
    echo "Service 'Production' + " . count($fonctions) . " fonctions créés\n";

    // ── Paramètres standard ──
    $pdo->prepare("INSERT INTO parametres_cnss_amo (societe_id, taux_cnss_patronal_non_plafonne) VALUES (?, 8.00)")->execute([$societeId]);
    foreach ([[0,1,0],[2,4,5],[5,11,10],[12,19,15],[20,24,20],[25,99,25]] as $tr) {
        $pdo->prepare("INSERT INTO bareme_anciennete (societe_id, annees_min, annees_max, taux) VALUES (?, ?, ?, ?)")->execute([$societeId, $tr[0], $tr[1], $tr[2]]);
    }
    $pdo->prepare("INSERT INTO conge_annuel (societe_id, jours_par_mois, report_autorise, report_max) VALUES (?, 1.5, 1, 15)")->execute([$societeId]);
    $pdo->prepare("INSERT INTO bareme_heures_sup (societe_id, taux_normal, taux_majore, taux_jour_ferie, seuil_heures) VALUES (?, 25, 50, 100, 8)")->execute([$societeId]);
    foreach ([['SMIG',2021,14.13,2698.83,'2021-01-01'],['SMAG',2021,73.05,1899.30,'2021-01-01'],['SMIG',2022,14.81,2828.71,'2022-01-01'],['SMAG',2022,76.70,1994.20,'2022-01-01'],['SMIG',2023,15.55,2970.05,'2023-01-01'],['SMAG',2023,84.37,2193.62,'2023-01-01'],['SMIG',2024,16.29,3111.39,'2024-01-01'],['SMAG',2024,88.58,2303.08,'2024-01-01'],['SMIG',2025,17.10,3266.10,'2025-01-01'],['SMAG',2025,93.00,2418.00,'2025-04-01'],['SMIG',2026,17.92,3422.72,'2026-01-01'],['SMAG',2026,97.44,2533.44,'2026-04-01']] as $s) {
        $pdo->prepare("INSERT INTO bareme_smig_smag (societe_id, annee, type, horaire, mensuel, date_effet) VALUES (?, ?, ?, ?, ?, ?)")->execute([$societeId, $s[1], $s[0], $s[2], $s[3], $s[4]]);
    }
    foreach ([['Jour de l\'an',1,1],['Manifeste de l\'Indépendance',11,1],['Fête du Trône',30,7],['Fête des Oueds',14,8],['Anniversaire de la Révolution',20,8],['Marche Verte',6,11]] as $jf) {
        $pdo->prepare("INSERT INTO jours_feries (societe_id, nom, jour, mois, type, actif) VALUES (?, ?, ?, ?, 'fixe', 1)")->execute([$societeId, $jf[0], $jf[1], $jf[2]]);
    }
    echo "Paramètres (CNSS/AMO, ancienneté, congé, heures sup, SMIG/SMAG, jours fériés) créés\n";

    // ── Salariés ──
    $sexeGuess = function (string $prenom): string {
        $f = ['HABIBA','ASMAE','LATIFA','KHADIJA','FAOUZIA','AICHA','CHAIMAA'];
        foreach ($f as $n) if (stripos($prenom, $n) !== false) return 'F';
        return 'M';
    };
    $maxMat = 0;
    foreach ($rows as $x) { if ($x['matricule'] !== null) $maxMat = max($maxMat, (int)$x['matricule']); }

    $stmtS = $pdo->prepare("INSERT INTO salaries (societe_id, service_id, fonction_id, matricule, nom_famille, prenom, sexe, adresse, date_naissance, date_embauche, cin, cnss, situation_familiale, nb_enfants, poste, type_contrat, salaire_base, indemnite_transport, indemnite_panier, indemnite_representation) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'celibataire', 0, ?, 'CDI', ?, ?, ?, ?)");
    $salarieIds = [];
    foreach ($rows as $x) {
        $mat = $x['matricule'] !== null ? (string)$x['matricule'] : (string)($maxMat + 1);
        $poste = $x['fonction'] !== '' ? $x['fonction'] : null;
        $fonctionId = $poste !== null ? ($fonctions[$poste] ?? null) : null;
        $stmtS->execute([
            $societeId,
            $serviceId,
            $fonctionId,
            $mat,
            $x['nom'],
            $x['prenom'],
            $sexeGuess($x['prenom']),
            $x['adresse'] !== '' ? $x['adresse'] : null,
            $x['naissance'],
            $x['embauche'],
            $x['cin'] !== '' ? Crypto::encrypt($x['cin']) : null,
            $x['cnss'] !== '' ? $x['cnss'] : null,
            $poste,
            $x['S'],
            $x['BI'],
            $x['BJ'],
            $x['repr'],
        ]);
        $salarieIds[(string)$mat] = (int)$pdo->lastInsertId();
    }
    echo count($rows) . " salariés créés\n";

    // ── Période juillet 2026 ──
    $pdo->prepare("INSERT INTO periodes (societe_id, mois, annee, date_debut, date_fin) VALUES (?, 7, 2026, '2026-07-01', '2026-07-31')")->execute([$societeId]);
    $periodeId = (int)$pdo->lastInsertId();
    echo "Période 07/2026 créée (id=$periodeId)\n";

    // ── Paies (valeurs exactes feuille 7) ──
    $stmtP = $pdo->prepare("INSERT INTO paies (periode_id, salarie_id, societe_id, jours_travailles, jours_conge, jours_feries, salaire_brut, prime_anciennete, sbi, frais_professionnels, salaire_plafonne_cnss, indemnite_transport, indemnite_panier, indemnite_representation, avantage_logement, total_gains, heures_supplementaires, montant_heures_sup, montant_hs_25, montant_hs_50, montant_hs_100, heures_sup_25, heures_sup_50, heures_sup_100, cnss_salariale, amo_salariale, mutuelle, sni, ir, deductions_familiales, autres_retenues, net_avant_retenues, net_a_payer, cnss_patronale, amo_patronale) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, ?, ?, 0, ?, ?, 0, 0, ?, ?, ?, ?)");

    $tot = ['S' => 0, 'BW' => 0, 'AI' => 0, 'AJ' => 0, 'BG' => 0, 'BZ' => 0, 'CB' => 0, 'CD' => 0];
    foreach ($rows as $x) {
        $mat = $x['matricule'] !== null ? (string)$x['matricule'] : (string)($maxMat + 1);
        $salarieId = $salarieIds[$mat];
        $stmtP->execute([
            $periodeId, $salarieId, $societeId,
            $x['P'], $x['R'], $x['Q'],
            $x['BW'],          // salaire_brut = brut global
            $x['AD'],          // prime_anciennete
            $x['AH'],          // sbi = brut imposable
            $x['AX'],          // frais_professionnels
            $x['CA'],          // salaire_plafonne_cnss
            $x['BI'], $x['BJ'], $x['repr'],
            $x['AI'], $x['AJ'],
            $x['AZ'],          // sni
            $x['BG'],          // ir
            $x['BZ'],          // net_avant_retenues
            $x['BZ'],          // net_a_payer (arrondi Excel)
            $x['CB'], $x['CD'], // cnss/amo patronales
        ]);
        foreach ($tot as $k => &$v) { $v += $x[$k]; }
        unset($v);
    }
    echo count($rows) . " paies créées\n";

    // ── Bulletins ──
    $paies = $pdo->query("SELECT id FROM paies WHERE periode_id = $periodeId")->fetchAll();
    $insB = $pdo->prepare("INSERT INTO bulletins (paie_id, numero, date_emission) VALUES (?, ?, '2026-07-31')");
    foreach ($paies as $pa) {
        $numero = 'TOU-' . str_pad((string)$pa['id'], 5, '0', STR_PAD_LEFT);
        $insB->execute([$pa['id'], $numero]);
    }
    echo count($paies) . " bulletins créés\n";

    $pdo->commit();

    echo "\n=== TOTAUX JUILLET (feuille 7) ===\n";
    echo 'Salaire base: ' . number_format($tot['S'], 2) . "\n";
    echo 'Brut global:  ' . number_format($tot['BW'], 2) . "\n";
    echo 'CNSS sal.:    ' . number_format($tot['AI'], 2) . "\n";
    echo 'AMO sal.:     ' . number_format($tot['AJ'], 2) . "\n";
    echo 'IR:           ' . number_format($tot['BG'], 2) . "\n";
    echo 'Net à payer:  ' . number_format($tot['BZ'], 2) . "\n";
    echo 'CNSS patr.:   ' . number_format($tot['CB'], 2) . "\n";
    echo 'AMO patr.:    ' . number_format($tot['CD'], 2) . "\n";
    echo "\nSociété id=$societeId, période id=$periodeId\n";
} catch (\Throwable $e) {
    $pdo->rollBack();
    fwrite(STDERR, "ERREUR : " . $e->getMessage() . "\n");
    exit(1);
}
