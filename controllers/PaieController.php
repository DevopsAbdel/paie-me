<?php

namespace Controllers;

use Core\Controller;
use Core\Model;
use Core\Session;
use PDO;

class PaieController extends Controller
{
    private PDO $db;

    public function __construct()
    {
        if (!Session::has('user_id')) {
            $this->redirect('/paie-me/login');
        }
        $this->db = Model::db();
    }

    public function index(): void
    {
        $userId = Session::get('user_id');
        $periodes = $this->db->query("
            SELECT p.*, so.raison_sociale,
                (SELECT COUNT(*) FROM paies WHERE periode_id = p.id) as nb_paies
            FROM periodes p
            JOIN societes so ON p.societe_id = so.id
            WHERE so.user_id = $userId
            ORDER BY p.annee DESC, p.mois DESC
        ")->fetchAll();

        $this->render('paies/index.php', [
            'title'    => 'Paies',
            'periodes' => $periodes,
        ]);
    }

    private function calcAnciennete(string $dateEmbauche, string $dateFin): float
    {
        $embauche = new \DateTime($dateEmbauche);
        $fin = new \DateTime($dateFin);
        $annees = (int) $embauche->diff($fin)->format('%y');

        if ($annees >= 25) return 0.25;
        if ($annees >= 20) return 0.20;
        if ($annees >= 12) return 0.15;
        if ($annees >= 5)  return 0.10;
        if ($annees >= 2)  return 0.05;
        return 0.00;
    }

    private function calculerPaie(array $s, array $cnssParams, string $dateFin, float $heuresSup = 0, array $gains = [], array $retenues = [], string $dateDebut = ''): array
    {
        $salaireBase = (float) $s['salaire_base'];

        $joursTravailles = 26;
        if ($dateDebut && !empty($s['date_embauche'])) {
            $embauche = new \DateTime($s['date_embauche']);
            $debut = new \DateTime($dateDebut);
            $fin = new \DateTime($dateFin);
            $dateSortie = !empty($s['date_sortie']) ? new \DateTime($s['date_sortie']) : null;

            $debutPeriode = $embauche > $debut ? $embauche : $debut;
            $finPeriode = ($dateSortie && $dateSortie < $fin) ? $dateSortie : $fin;
            $diff = (int) $debutPeriode->diff($finPeriode)->format('%a') + 1;
            $joursTravailles = max(min($diff, 26), 0);
        }
        $prorata = $joursTravailles / 26;

        $primeAnciennete = 0;
        if (!empty($s['date_embauche'])) {
            $primeAnciennete = round($salaireBase * $this->calcAnciennete($s['date_embauche'], $dateFin), 2) * $prorata;
        }

        $montantHeuresSup = 0;
        if ($heuresSup > 0 && $salaireBase > 0) {
            $montantHeuresSup = round($heuresSup * ($salaireBase / 191) * 1.25, 2);
        }

        $salaireBaseProrata = round($salaireBase * $prorata, 2);

        $transport     = round((float) ($s['indemnite_transport'] ?? 0) * $prorata, 2);
        $panier        = round((float) ($s['indemnite_panier'] ?? 0) * $prorata, 2);
        $representation = round((float) ($s['indemnite_representation'] ?? 0) * $prorata, 2);
        $logement      = round((float) ($s['avantage_logement'] ?? 0) * $prorata, 2);

        $totalGains = 0;
        $gainsImposables = 0;
        foreach ($gains as $g) {
            $baseGain = $g['type_montant'] === 'proportionnel'
                ? $salaireBase * (float) $g['valeur_defaut'] / 100
                : (float) $g['valeur_defaut'];
            $montant = round($baseGain * $prorata, 2);
            $totalGains += $montant;
            if ($g['imposable']) $gainsImposables += $montant;
        }

        $totalRetenuesCustom = 0;
        foreach ($retenues as $r) {
            $baseRet = $r['type_montant'] === 'proportionnel'
                ? $salaireBase * (float) $r['valeur_defaut'] / 100
                : (float) $r['valeur_defaut'];
            $totalRetenuesCustom += round($baseRet * $prorata, 2);
        }

        $sb = $salaireBaseProrata + $primeAnciennete + $montantHeuresSup + $transport + $panier + $representation + $logement + $totalGains;

        $plafondTransport = round(500 * $prorata, 2);
        $plafondPanier = round(780 * $prorata, 2);
        $transportExonere = min($transport, $plafondTransport);
        $panierExonere = min($panier, $plafondPanier);
        $sbi = $sb - $transportExonere - $panierExonere;

        $plafonne = min($sb, (float) ($cnssParams['plafond_cnss'] ?? 6000));
        $cnss = round($plafonne * (float) ($cnssParams['taux_cnss_salarial'] ?? 4.48) / 100, 2);
        $amo  = round($sb * (float) ($cnssParams['taux_amo_salarial'] ?? 2.26) / 100, 2);

        $tauxFraisPro = ($sbi * 12 <= 78000) ? 0.35 : 0.25;
        $fraisPro = round(min($sbi * $tauxFraisPro, 2500), 2);

        $sni = round($sbi - ($cnss + $amo) - $fraisPro, 2);

        $ir = $this->calculateIr(max($sni, 0));

        $nbEnfants = (int) ($s['nb_enfants'] ?? 0);
        $nbCharges = $nbEnfants + (($s['situation_familiale'] ?? 'celibataire') === 'marie' ? 1 : 0);
        $deductionsFamiliales = round(min($nbCharges, 6) * 50, 2);

        $irNet = round(max($ir - $deductionsFamiliales, 0), 2);

        $avances = (float) ($s['avances_salaire'] ?? 0);
        $mutuelle = (float) ($s['mutuelle'] ?? 0);
        $autresRetenues = $avances + $mutuelle + $totalRetenuesCustom;

        $netAvant = round($sb - ($cnss + $amo) - $ir, 2);
        $net = round(max($netAvant + $deductionsFamiliales - $autresRetenues, 0), 2);

        $cnssPatronale = round($plafonne * (float) ($cnssParams['taux_cnss_patronal'] ?? 8.98) / 100, 2);
        $amoPatronale  = round($sb * (float) ($cnssParams['taux_amo_patronal'] ?? 4.11) / 100, 2);

        return compact(
            'joursTravailles', 'primeAnciennete', 'heuresSup', 'montantHeuresSup', 'transport', 'panier',
            'representation', 'logement', 'totalGains', 'sb', 'sbi', 'plafonne', 'cnss', 'amo',
            'fraisPro', 'sni', 'deductionsFamiliales', 'avances',
            'mutuelle', 'autresRetenues', 'netAvant', 'net', 'cnssPatronale', 'amoPatronale'
        ) + ['ir' => $ir, 'irNet' => $irNet];
    }

    public function create(): void
    {
        $userId = Session::get('user_id');
        $societes = $this->db->query("SELECT id, raison_sociale FROM societes WHERE user_id = $userId ORDER BY raison_sociale")->fetchAll();

        $fromSociete = isset($_GET['from_societe']) ? (int) $_GET['from_societe'] : null;

        if ($this->isPost()) {
            $this->checkCsrf();
            $societeId  = (int) ($_POST['societe_id'] ?? 0);
            $mois       = (int) ($_POST['mois'] ?? 0);
            $annee      = (int) ($_POST['annee'] ?? 0);
            $dateDebut  = $_POST['date_debut'] ?? null;
            $dateFin    = $_POST['date_fin'] ?? null;

            if (!$societeId || !$mois || !$annee) {
                Session::setFlash('error', 'Veuillez remplir tous les champs.');
                $this->redirect('/paie-me/paies/create');
            }

            $existing = $this->db->prepare("SELECT id FROM periodes WHERE societe_id = ? AND mois = ? AND annee = ?");
            $existing->execute([$societeId, $mois, $annee]);
            if ($existing->fetch()) {
                Session::setFlash('error', 'Cette période existe déjà pour cette société.');
                $this->redirect('/paie-me/paies/create');
            }

            $stmt = $this->db->prepare("
                INSERT INTO periodes (societe_id, mois, annee, date_debut, date_fin)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$societeId, $mois, $annee, $dateDebut, $dateFin]);
            $periodeId = $this->db->lastInsertId();

            $cnssParams = $this->db->query("SELECT * FROM parametres_cnss_amo WHERE societe_id = $societeId")->fetch() ?: [];
            $gains = $this->db->query("SELECT * FROM rubriques_gains WHERE societe_id = $societeId AND actif = 1")->fetchAll();
            $retenues = $this->db->query("SELECT * FROM rubriques_retenues WHERE societe_id = $societeId AND actif = 1")->fetchAll();

            $salaries = $this->db->query("SELECT id, salaire_base, date_embauche, date_sortie, situation_familiale, indemnite_transport, indemnite_panier, indemnite_representation, avantage_logement, nb_enfants, avances_salaire, mutuelle FROM salaries WHERE societe_id = $societeId AND actif = 1")->fetchAll();

            foreach ($salaries as $s) {
                $c = $this->calculerPaie($s, $cnssParams, $dateFin, 0, $gains, $retenues, $dateDebut);

                $stmtPaie = $this->db->prepare("
                    INSERT INTO paies (periode_id, salarie_id, societe_id, jours_travailles, salaire_brut, sbi, prime_anciennete, salaire_plafonne_cnss, indemnite_transport, indemnite_panier, indemnite_representation, avantage_logement, total_gains, heures_supplementaires, montant_heures_sup, cnss_salariale, amo_salariale, mutuelle, sni, ir, deductions_familiales, autres_retenues, net_avant_retenues, net_a_payer, cnss_patronale, amo_patronale, frais_professionnels)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmtPaie->execute([
                    $periodeId, $s['id'], $societeId,
                    $c['joursTravailles'],
                    $c['sb'], $c['sbi'], $c['primeAnciennete'], $c['plafonne'],
                    $c['transport'], $c['panier'], $c['representation'], $c['logement'],
                    $c['totalGains'],
                    $c['heuresSup'], $c['montantHeuresSup'],
                    $c['cnss'], $c['amo'], $c['mutuelle'], $c['sni'], $c['ir'], $c['deductionsFamiliales'],
                    $c['autresRetenues'], $c['netAvant'], $c['net'],
                    $c['cnssPatronale'], $c['amoPatronale'], $c['fraisPro'],
                ]);
            }

            $nbBulletins = BulletinController::genererPourPeriode((int) $periodeId, $this->db);

            Session::setFlash('success', 'Période créée et paies calculées pour ' . count($salaries) . ' salariés. ' . $nbBulletins . ' bulletins générés.');
            $this->redirect('/paie-me/paies');
        }

        $this->render('paies/form.php', [
            'title'       => 'Nouvelle période de paie',
            'societes'    => $societes,
            'fromSociete' => $fromSociete,
        ]);
    }

    public function calculate(int $id): void
    {
        $userId = Session::get('user_id');
        $periode = $this->db->query("
            SELECT p.* FROM periodes p
            JOIN societes so ON p.societe_id = so.id
            WHERE p.id = $id AND so.user_id = $userId
        ")->fetch();

        if (!$periode) {
            Session::setFlash('error', 'Période introuvable.');
            $this->redirect('/paie-me/paies');
        }

        if (!empty($periode['cloturee'])) {
            Session::setFlash('error', 'Cette période est clôturée. Vous ne pouvez plus la modifier.');
            $this->redirect('/paie-me/paies');
        }

        $existingPaies = $this->db->query("SELECT salarie_id, heures_supplementaires FROM paies WHERE periode_id = $id")->fetchAll();
        $heuresSupMap = [];
        foreach ($existingPaies as $ep) {
            $heuresSupMap[(int) $ep['salarie_id']] = (float) $ep['heures_supplementaires'];
        }

        if (count($existingPaies) > 0) {
            $this->db->exec("DELETE FROM paies WHERE periode_id = $id");
        }

        $societeId = $periode['societe_id'];
        $dateDebut = $periode['date_debut'];
        $dateFin = $periode['date_fin'];
        $cnssParams = $this->db->query("SELECT * FROM parametres_cnss_amo WHERE societe_id = $societeId")->fetch() ?: [];
        $gains = $this->db->query("SELECT * FROM rubriques_gains WHERE societe_id = $societeId AND actif = 1")->fetchAll();
        $retenues = $this->db->query("SELECT * FROM rubriques_retenues WHERE societe_id = $societeId AND actif = 1")->fetchAll();

        $salaries = $this->db->query("SELECT id, salaire_base, date_embauche, date_sortie, situation_familiale, indemnite_transport, indemnite_panier, indemnite_representation, avantage_logement, nb_enfants, avances_salaire, mutuelle FROM salaries WHERE societe_id = $societeId AND actif = 1")->fetchAll();

        foreach ($salaries as $s) {
            $heuresSup = $heuresSupMap[(int) $s['id']] ?? 0;
            $c = $this->calculerPaie($s, $cnssParams, $dateFin, $heuresSup, $gains, $retenues, $dateDebut);

            $stmtPaie = $this->db->prepare("
                INSERT INTO paies (periode_id, salarie_id, societe_id, jours_travailles, salaire_brut, sbi, prime_anciennete, salaire_plafonne_cnss, indemnite_transport, indemnite_panier, indemnite_representation, avantage_logement, total_gains, heures_supplementaires, montant_heures_sup, cnss_salariale, amo_salariale, mutuelle, sni, ir, deductions_familiales, autres_retenues, net_avant_retenues, net_a_payer, cnss_patronale, amo_patronale, frais_professionnels)
                VALUES (?, ?, ?, 30, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmtPaie->execute([
                $id, $s['id'], $societeId,
                $c['sb'], $c['sbi'], $c['primeAnciennete'], $c['plafonne'],
                $c['transport'], $c['panier'], $c['representation'], $c['logement'],
                $c['totalGains'],
                $c['heuresSup'], $c['montantHeuresSup'],
                $c['cnss'], $c['amo'], $c['mutuelle'], $c['sni'], $c['ir'], $c['deductionsFamiliales'],
                $c['autresRetenues'], $c['netAvant'], $c['net'],
                $c['cnssPatronale'], $c['amoPatronale'], $c['fraisPro'],
            ]);
        }

        $nbBulletins = BulletinController::genererPourPeriode($id, $this->db);

        Session::setFlash('success', 'Paies recalculées pour ' . count($salaries) . ' salariés. ' . $nbBulletins . ' bulletins générés.');
        $this->redirect('/paie-me/paies');
    }

    public function cloturer(int $id): void
    {
        $userId = Session::get('user_id');
        $periode = $this->db->query("
            SELECT p.* FROM periodes p
            JOIN societes so ON p.societe_id = so.id
            WHERE p.id = $id AND so.user_id = $userId
        ")->fetch();

        if (!$periode) {
            Session::setFlash('error', 'Période introuvable.');
            $this->redirect('/paie-me/paies');
        }

        if (!empty($periode['cloturee'])) {
            Session::setFlash('error', 'Cette période est déjà clôturée.');
            $this->redirect('/paie-me/paies');
        }

        $paiesCount = $this->db->query("SELECT COUNT(*) FROM paies WHERE periode_id = $id")->fetchColumn();
        if ((int) $paiesCount === 0) {
            Session::setFlash('error', 'Impossible de clôturer une période sans paies.');
            $this->redirect('/paie-me/paies');
        }

        $this->db->exec("UPDATE periodes SET cloturee = 1 WHERE id = $id");
        Session::setFlash('success', 'Période clôturée avec succès.');
        $this->redirect('/paie-me/paies');
    }

    public function lignes(int $id): void
    {
        $userId = Session::get('user_id');
        $periode = $this->db->query("
            SELECT p.*, so.raison_sociale FROM periodes p
            JOIN societes so ON p.societe_id = so.id
            WHERE p.id = $id AND so.user_id = $userId
        ")->fetch();

        if (!$periode) {
            Session::setFlash('error', 'Période introuvable.');
            $this->redirect('/paie-me/paies');
        }

        $paies = $this->db->query("
            SELECT pa.*, s.nom_famille, s.prenom, s.matricule
            FROM paies pa
            JOIN salaries s ON pa.salarie_id = s.id
            WHERE pa.periode_id = $id
            ORDER BY s.nom_famille, s.prenom
        ")->fetchAll();

        $this->render('paies/lignes.php', [
            'title'   => 'Paies — ' . $periode['raison_sociale'] . ' ' . str_pad($periode['mois'], 2, '0', STR_PAD_LEFT) . '/' . $periode['annee'],
            'periode' => $periode,
            'paies'   => $paies,
        ]);
    }

    public function editPaie(int $id): void
    {
        $userId = Session::get('user_id');
        $paie = $this->db->query("
            SELECT pa.*, s.nom_famille, s.prenom, s.salaire_base, so.raison_sociale
            FROM paies pa
            JOIN salaries s ON pa.salarie_id = s.id
            JOIN societes so ON pa.societe_id = so.id
            WHERE pa.id = $id AND so.user_id = $userId
        ")->fetch();

        if (!$paie) {
            Session::setFlash('error', 'Paie introuvable.');
            $this->redirect('/paie-me/paies');
        }

        $periode = $this->db->query("SELECT cloturee FROM periodes WHERE id = {$paie['periode_id']}")->fetch();
        if ($periode && !empty($periode['cloturee'])) {
            Session::setFlash('error', 'Cette période est clôturée. Vous ne pouvez plus la modifier.');
            $this->redirect('/paie-me/paies');
        }

        if ($this->isPost()) {
            $this->checkCsrf();
            $heuresSup = (float) ($_POST['heures_supplementaires'] ?? 0);
            $stmt = $this->db->prepare("UPDATE paies SET heures_supplementaires = ? WHERE id = ?");
            $stmt->execute([$heuresSup, $id]);
            Session::setFlash('success', 'Heures supplémentaires enregistrées. Recalculez la période pour appliquer.');
            $this->redirect('/paie-me/paies/paie/' . $id . '/edit');
        }

        $this->render('paies/edit.php', [
            'title' => 'Modifier la paie — ' . $paie['nom_famille'] . ' ' . $paie['prenom'],
            'paie'  => $paie,
        ]);
    }

    public function journal(int $id): void
    {
        $userId = Session::get('user_id');
        $periode = $this->db->query("
            SELECT p.*, so.raison_sociale FROM periodes p
            JOIN societes so ON p.societe_id = so.id
            WHERE p.id = $id AND so.user_id = $userId
        ")->fetch();

        if (!$periode) {
            Session::setFlash('error', 'Période introuvable.');
            $this->redirect('/paie-me/paies');
        }

        $paies = $this->db->query("
            SELECT pa.*, s.nom_famille, s.prenom, s.matricule
            FROM paies pa
            JOIN salaries s ON pa.salarie_id = s.id
            WHERE pa.periode_id = $id
            ORDER BY s.nom_famille, s.prenom
        ")->fetchAll();

        $totaux = [
            'salaire_brut' => 0, 'sbi' => 0, 'prime_anciennete' => 0,
            'total_gains' => 0, 'montant_heures_sup' => 0,
            'indemnite_transport' => 0, 'indemnite_panier' => 0,
            'indemnite_representation' => 0, 'avantage_logement' => 0,
            'cnss_salariale' => 0, 'amo_salariale' => 0,
            'frais_professionnels' => 0, 'mutuelle' => 0,
            'ir' => 0, 'autres_retenues' => 0,
            'net_a_payer' => 0,
            'cnss_patronale' => 0, 'amo_patronale' => 0,
        ];

        foreach ($paies as $pa) {
            foreach ($totaux as $k => &$v) {
                $v += (float) ($pa[$k] ?? 0);
            }
        }
        unset($v);

        $this->render('paies/journal.php', [
            'title'  => 'Journal de paie — ' . $periode['raison_sociale'] . ' ' . str_pad($periode['mois'], 2, '0', STR_PAD_LEFT) . '/' . $periode['annee'],
            'periode' => $periode,
            'paies'  => $paies,
            'totaux' => $totaux,
        ]);
    }

    private function calculateIr(float $sni): float
    {
        $brackets = $this->db->query("SELECT * FROM bareme_ir ORDER BY min")->fetchAll();
        foreach ($brackets as $b) {
            if ($sni >= (float) $b['min'] && $sni <= (float) $b['max']) {
                $ir = $sni * (float) $b['taux'] / 100 - (float) $b['deduction'];
                return round(max($ir, 0), 2);
            }
        }
        return 0;
    }

    protected function render(string $view, array $data = []): void
    {
        extract($data);
        ob_start();
        require __DIR__ . '/../views/' . $view;
        $content = ob_get_clean();
        require __DIR__ . '/../views/layout.php';
    }
}
