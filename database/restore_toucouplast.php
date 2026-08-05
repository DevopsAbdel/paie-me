<?php
/**
 * Restauration TOUCOUPLAST — met à jour les paies juillet 2026
 * avec les valeurs EXACTES de la feuille 7 du classeur (alignées sur les PDF).
 *
 * Différence avec import_toucouplast.php :
 *   - ne crée ni société, ni salariés, ni période (ils existent déjà) ;
 *   - cnss_patronale = CB + CC (CNSS PP plafonné + non plafonné = total PDF).
 *
 * Usage CLI : php database/restore_toucouplast.php
 */
require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$FICHIER = 'C:/Users/Dev/Desktop/2026_Paie_TOUCOUPLAST/2026_Paie_TOUCOUPLAST.xlsm';

// ── Lecture du classeur ──
$reader = IOFactory::createReaderForFile($FICHIER);
$reader->setReadDataOnly(true);
$spreadsheet = $reader->load($FICHIER);

function rcv($ws, $col, $row) { $v = $ws->getCell($col . $row)->getValue(); return $v; }

$wsSte = $spreadsheet->getSheetByName('ste infos');
$societeInfo = [
    'nom'   => (string)rcv($wsSte, 'B', 3),
    'ice'   => (string)rcv($wsSte, 'B', 7),
];

$ws7 = $spreadsheet->getSheetByName('7');
$rows = [];
$maxRow = $ws7->getHighestDataRow();
for ($r = 2; $r <= $maxRow; $r++) {
    $nom = rcv($ws7, 'D', $r);
    if ($nom === null || trim((string)$nom) === '') continue;
    $bh = rcv($ws7, 'BH', $r);
    $repr = 0.0;
    if ($bh !== null && $bh !== '') {
        $S = (float)rcv($ws7, 'S', $r);
        $repr = is_numeric($bh) ? (float)$bh : round($S * 10 / 100, 2);
    }
    $rows[] = [
        'matricule' => rcv($ws7, 'B', $r),
        'nom'       => trim((string)$nom),
        'P'  => (int)rcv($ws7, 'P', $r),
        'Q'  => (float)(rcv($ws7, 'Q', $r) ?? 0),
        'R'  => (float)(rcv($ws7, 'R', $r) ?? 0),
        'S'  => (float)rcv($ws7, 'S', $r),
        'AD' => (float)rcv($ws7, 'AD', $r),
        'AH' => (float)rcv($ws7, 'AH', $r),
        'AI' => (float)rcv($ws7, 'AI', $r),
        'AJ' => (float)rcv($ws7, 'AJ', $r),
        'AX' => (float)rcv($ws7, 'AX', $r),
        'AZ' => (float)rcv($ws7, 'AZ', $r),
        'BG' => (float)rcv($ws7, 'BG', $r),
        'BI' => (float)(rcv($ws7, 'BI', $r) ?? 0),
        'BJ' => (float)(rcv($ws7, 'BJ', $r) ?? 0),
        'BW' => (float)rcv($ws7, 'BW', $r),
        'BZ' => (float)rcv($ws7, 'BZ', $r),
        'CA' => (float)rcv($ws7, 'CA', $r),
        'CB' => (float)rcv($ws7, 'CB', $r),
        'CC' => (float)rcv($ws7, 'CC', $r),
        'CD' => (float)rcv($ws7, 'CD', $r),
        'repr' => $repr,
    ];
}

// ── Connexion DB ──
$cfg = require __DIR__ . '/../config/database.php';
$dsn = "mysql:host={$cfg['host']};port={$cfg['port']};dbname={$cfg['dbname']};charset={$cfg['charset']}";
$pdo = new PDO($dsn, $cfg['username'], $cfg['password'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$societe = $pdo->prepare("SELECT id FROM societes WHERE ice = ?");
$societe->execute([$societeInfo['ice']]);
$societeId = (int)$societe->fetchColumn();
if (!$societeId) {
    fwrite(STDERR, "ERREUR : société ICE {$societeInfo['ice']} introuvable.\n");
    exit(1);
}

$periode = $pdo->prepare("SELECT id FROM periodes WHERE societe_id = ? AND mois = 7 AND annee = 2026");
$periode->execute([$societeId]);
$periodeId = (int)$periode->fetchColumn();
if (!$periodeId) {
    fwrite(STDERR, "ERREUR : période 07/2026 introuvable pour la société id=$societeId.\n");
    exit(1);
}

// ── Mapping matricule → salarie_id ──
// maxMat est calculé sur les matricules NON vides de la feuille 7 (comme l'import)
// pour que l'auto-numérotation d'un matricule vide (MOUHASSINE) retombe sur 25.
$maxMat = 0;
foreach ($rows as $x) {
    if ($x['matricule'] !== null) $maxMat = max($maxMat, (int)$x['matricule']);
}
$salaries = $pdo->prepare("SELECT id, matricule, nom_famille, prenom FROM salaries WHERE societe_id = ?");
$salaries->execute([$societeId]);
$salarieIds = [];
foreach ($salaries->fetchAll() as $s) {
    $salarieIds[(string)$s['matricule']] = (int)$s['id'];
}

// ── Config CNSS/AMO : part patronale non plafonnée = 8 % (règle Excel) ──
$pdo->exec("INSERT INTO parametres_cnss_amo (societe_id, taux_cnss_patronal_non_plafonne) VALUES ($societeId, 8.00)
            ON DUPLICATE KEY UPDATE taux_cnss_patronal_non_plafonne = 8.00");

$pdo->beginTransaction();
try {
    // Nettoyage des paies de la période (paie_gains/retenues puis paies)
    $oldPaies = $pdo->query("SELECT id FROM paies WHERE periode_id = $periodeId")->fetchAll();
    if ($oldPaies) {
        $ids = implode(',', array_map(fn($p) => (int)$p['id'], $oldPaies));
        $pdo->exec("DELETE FROM bulletins WHERE paie_id IN ($ids)");
        $pdo->exec("DELETE FROM paie_gains WHERE paie_id IN ($ids)");
        $pdo->exec("DELETE FROM paie_retenues WHERE paie_id IN ($ids)");
        $pdo->exec("DELETE FROM paies WHERE periode_id = $periodeId");
    }

    $stmtP = $pdo->prepare("INSERT INTO paies (periode_id, salarie_id, societe_id, jours_travailles, jours_conge, jours_feries, salaire_brut, prime_anciennete, sbi, frais_professionnels, salaire_plafonne_cnss, indemnite_transport, indemnite_panier, indemnite_representation, avantage_logement, total_gains, heures_supplementaires, montant_heures_sup, montant_hs_25, montant_hs_50, montant_hs_100, heures_sup_25, heures_sup_50, heures_sup_100, cnss_salariale, amo_salariale, mutuelle, sni, ir, deductions_familiales, autres_retenues, net_avant_retenues, net_a_payer, cnss_patronale, amo_patronale) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, ?, ?, 0, ?, ?, 0, 0, ?, ?, ?, ?)");

    $insB = $pdo->prepare("INSERT INTO bulletins (paie_id, numero, date_emission) VALUES (?, ?, '2026-07-31')");

    $compte = 0;
    foreach ($rows as $x) {
        $mat = $x['matricule'] !== null ? (string)$x['matricule'] : (string)($maxMat + 1);
        if (!isset($salarieIds[$mat])) {
            fwrite(STDERR, "ERREUR : aucun salarié avec matricule '$mat' ({$x['nom']}).\n");
            $pdo->rollBack();
            exit(1);
        }
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
            $x['CB'] + $x['CC'], // cnss_patronale (plafonné + non plafonné)
            $x['CD'],          // amo_patronale
        ]);
        $insB->execute([(int)$pdo->lastInsertId(), 'TOU-' . str_pad((string)$pdo->lastInsertId(), 5, '0', STR_PAD_LEFT)]);
        $compte++;
    }

    $pdo->commit();
    echo "Restauration OK : $compte paies + bulletins recréés (période id=$periodeId).\n";
    $ex = null;
    foreach ($rows as $r) { if (stripos($r['nom'], 'TOUCHOUNI') !== false) { $ex = $r; break; } }
    if ($ex) {
        echo "CNSS patronale stockée = CB + CC (total PDF), ex. TOUCHOUNI : " . number_format($ex['CB'] + $ex['CC'], 2) . " DH\n";
    }
} catch (\Throwable $e) {
    $pdo->rollBack();
    fwrite(STDERR, "ERREUR : " . $e->getMessage() . "\n");
    exit(1);
}
