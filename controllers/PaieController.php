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

    public function create(): void
    {
        $userId = Session::get('user_id');
        $societes = $this->db->query("SELECT id, raison_sociale FROM societes WHERE user_id = $userId ORDER BY raison_sociale")->fetchAll();

        $fromSociete = isset($_GET['from_societe']) ? (int) $_GET['from_societe'] : null;

        if ($this->isPost()) {
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

            $salaries = $this->db->query("SELECT id, salaire_base, indemnite_transport, indemnite_panier, indemnite_representation, avantage_logement, nb_enfants FROM salaries WHERE societe_id = $societeId AND actif = 1")->fetchAll();

            foreach ($salaries as $s) {
                $salaireBrut   = (float) $s['salaire_base'];
                $transport     = (float) $s['indemnite_transport'];
                $panier        = (float) $s['indemnite_panier'];
                $representation = (float) $s['indemnite_representation'];
                $logement      = (float) $s['avantage_logement'];

                $sb = $salaireBrut + $transport + $panier + $representation + $logement;
                $plafonne = min($sb, 6000);
                $cnss = round($plafonne * 4.48 / 100, 2);
                $amo  = round($sb * 2.26 / 100, 2);
                $sni  = round($sb - ($cnss + $amo), 2);

                $ir = $this->calculateIr($sni);

                $deductionsFamiliales = $s['nb_enfants'] * 30;
                $netAvant = $sni - $ir - $deductionsFamiliales;
                $net = round(max($netAvant, 0), 2);

                $cnssPatronale = round($sb * 8.98 / 100 + $sb * 8 / 100, 2);
                $amoPatronale  = round($sb * 4.11 / 100, 2);

                $stmtPaie = $this->db->prepare("
                    INSERT INTO paies (periode_id, salarie_id, societe_id, jours_travailles, salaire_brut, salaire_plafonne_cnss, indemnite_transport, indemnite_panier, indemnite_representation, avantage_logement, cnss_salariale, amo_salariale, sni, ir, deductions_familiales, net_avant_retenues, net_a_payer, cnss_patronale, amo_patronale)
                    VALUES (?, ?, ?, 30, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmtPaie->execute([
                    $periodeId, $s['id'], $societeId, $sb, $plafonne,
                    $transport, $panier, $representation, $logement,
                    $cnss, $amo, $sni, $ir, $deductionsFamiliales, $net, $net,
                    $cnssPatronale, $amoPatronale,
                ]);
            }

            Session::setFlash('success', 'Période créée et paies calculées pour ' . count($salaries) . ' salariés.');
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

        $existing = $this->db->query("SELECT COUNT(*) FROM paies WHERE periode_id = $id")->fetchColumn();
        if ($existing > 0) {
            $this->db->exec("DELETE FROM paies WHERE periode_id = $id");
        }

        $societeId = $periode['societe_id'];
        $cnssParams = $this->db->query("SELECT * FROM parametres_cnss_amo WHERE societe_id = $societeId")->fetch();
        if (!$cnssParams) {
            $cnssParams = [
                'plafond_cnss' => 6000,
                'taux_cnss_salarial' => 4.48,
                'taux_cnss_patronal' => 8.98,
                'taux_amo_salarial' => 2.26,
                'taux_amo_patronal' => 4.11,
            ];
        }

        $salaries = $this->db->query("SELECT id, salaire_base, indemnite_transport, indemnite_panier, indemnite_representation, avantage_logement, nb_enfants, avances_salaire, mutuelle FROM salaries WHERE societe_id = $societeId AND actif = 1")->fetchAll();

        foreach ($salaries as $s) {
            $salaireBrut   = (float) $s['salaire_base'];
            $transport     = (float) $s['indemnite_transport'];
            $panier        = (float) $s['indemnite_panier'];
            $representation = (float) $s['indemnite_representation'];
            $logement      = (float) $s['avantage_logement'];

            $sb = $salaireBrut + $transport + $panier + $representation + $logement;
            $plafonne = min($sb, (float) $cnssParams['plafond_cnss']);
            $cnss = round($plafonne * (float) $cnssParams['taux_cnss_salarial'] / 100, 2);
            $amo  = round($sb * (float) $cnssParams['taux_amo_salarial'] / 100, 2);
            $sni  = round($sb - ($cnss + $amo), 2);

            $ir = $this->calculateIr($sni);

            $deductionsFamiliales = $s['nb_enfants'] * 30;
            $avances = (float) $s['avances_salaire'];
            $mutuelle = (float) $s['mutuelle'];
            $netAvant = $sni - $ir - $deductionsFamiliales - $avances - $mutuelle;
            $net = round(max($netAvant, 0), 2);

            $cnssPatronale = round($sb * (float) $cnssParams['taux_cnss_patronal'] / 100, 2);
            $amoPatronale  = round($sb * (float) $cnssParams['taux_amo_patronal'] / 100, 2);

            $stmtPaie = $this->db->prepare("
                INSERT INTO paies (periode_id, salarie_id, societe_id, jours_travailles, salaire_brut, salaire_plafonne_cnss, indemnite_transport, indemnite_panier, indemnite_representation, avantage_logement, cnss_salariale, amo_salariale, mutuelle, sni, ir, deductions_familiales, autres_retenues, net_avant_retenues, net_a_payer, cnss_patronale, amo_patronale)
                VALUES (?, ?, ?, 30, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmtPaie->execute([
                $id, $s['id'], $societeId, $sb, $plafonne,
                $transport, $panier, $representation, $logement,
                $cnss, $amo, $mutuelle, $sni, $ir, $deductionsFamiliales, $avances, $net, $net,
                $cnssPatronale, $amoPatronale,
            ]);
        }

        Session::setFlash('success', 'Paies recalculées pour ' . count($salaries) . ' salariés.');
        $this->redirect('/paie-me/paies');
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
