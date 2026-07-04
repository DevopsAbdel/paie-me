<?php

namespace Controllers;

use Core\Controller;
use Core\Model;
use Core\PaieCalculator;
use Core\Session;
use Core\Validator;
use Core\Audit;
use PDO;

class PaieController extends Controller
{
    private PDO $db;
    private PaieCalculator $calculator;

    public function __construct()
    {
        if (!Session::has('user_id')) {
            $this->redirect('/paie-me/login');
        }
        $this->db = Model::db();
        $this->calculator = new PaieCalculator($this->db);
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

    public function create(): void
    {
        $userId = Session::get('user_id');
        $societes = $this->db->query("SELECT id, raison_sociale FROM societes WHERE user_id = $userId ORDER BY raison_sociale")->fetchAll();

        $fromSociete = isset($_GET['from_societe']) ? (int) $_GET['from_societe'] : null;

        if ($this->isPost()) {
            $this->checkCsrf();
            $v = new Validator($_POST);
            $v->required('societe_id', 'Société')
              ->required('mois', 'Mois')
              ->required('annee', 'Année')
              ->numeric('mois', 'Mois')
              ->numeric('annee', 'Année')
              ->date('date_debut', 'Date début')
              ->date('date_fin', 'Date fin');

            if (!$v->passes()) {
                Session::setFlash('error', $v->firstError());
                $this->redirect('/paie-me/paies/create');
            }

            $societeId  = (int) $_POST['societe_id'];
            $mois       = (int) $_POST['mois'];
            $annee      = (int) $_POST['annee'];
            $dateDebut  = $_POST['date_debut'];
            $dateFin    = $_POST['date_fin'];

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
                $c = $this->calculator->calculerPaie($s, $cnssParams, $dateFin, 0, $gains, $retenues, $dateDebut);

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

            Audit::log($this->db, 'create', 'periode', (int) $periodeId, 'Création période: ' . $mois . '/' . $annee);

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
            $c = $this->calculator->calculerPaie($s, $cnssParams, $dateFin, $heuresSup, $gains, $retenues, $dateDebut);

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

        Audit::log($this->db, 'calculate', 'periode', $id, 'Recalcul paies période');

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
        Audit::log($this->db, 'cloture', 'periode', $id, 'Clôture période');
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

    protected function render(string $view, array $data = []): void
    {
        extract($data);
        ob_start();
        require __DIR__ . '/../views/' . $view;
        $content = ob_get_clean();
        require __DIR__ . '/../views/layout.php';
    }
}
