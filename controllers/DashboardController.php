<?php

namespace Controllers;

use Core\Controller;
use Core\Model;
use Core\Session;
use PDO;

class DashboardController extends Controller
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
        $ctx = Session::get('societe_context');

        $societeFilter = $ctx ? " AND s.societe_id = " . (int)$ctx['id'] : "";
        $societeFilterP = $ctx ? " AND p.societe_id = " . (int)$ctx['id'] : "";
        $societeFilterPa = $ctx ? " AND pa.societe_id = " . (int)$ctx['id'] : "";

        if ($ctx) {
            $nbSocietes = 1;
        } else {
            $nbSocietes  = $this->db->query("SELECT COUNT(*) FROM societes WHERE user_id = $userId")->fetchColumn();
        }
        $nbSalaries  = $this->db->query("SELECT COUNT(*) FROM salaries s JOIN societes so ON s.societe_id = so.id WHERE so.user_id = $userId$societeFilter")->fetchColumn();
        $nbPeriodes  = $this->db->query("SELECT COUNT(*) FROM periodes p JOIN societes so ON p.societe_id = so.id WHERE so.user_id = $userId$societeFilterP")->fetchColumn();
        $totalNet    = $this->db->query("SELECT COALESCE(SUM(pa.net_a_payer), 0) FROM paies pa JOIN periodes p ON pa.periode_id = p.id JOIN societes so ON p.societe_id = so.id WHERE so.user_id = $userId$societeFilterP")->fetchColumn();

        $totalBrut   = $this->db->query("SELECT COALESCE(SUM(pa.salaire_brut), 0) FROM paies pa JOIN periodes p ON pa.periode_id = p.id JOIN societes so ON p.societe_id = so.id WHERE so.user_id = $userId$societeFilterP")->fetchColumn();
        $totalCnss   = $this->db->query("SELECT COALESCE(SUM(pa.cnss_salariale), 0) FROM paies pa JOIN periodes p ON pa.periode_id = p.id JOIN societes so ON p.societe_id = so.id WHERE so.user_id = $userId$societeFilterP")->fetchColumn();
        $totalIr     = $this->db->query("SELECT COALESCE(SUM(pa.ir), 0) FROM paies pa JOIN periodes p ON pa.periode_id = p.id JOIN societes so ON p.societe_id = so.id WHERE so.user_id = $userId$societeFilterP")->fetchColumn();

        $latestPaies = $this->db->query("
            SELECT pa.*, s.nom_famille, s.prenom, p.mois, p.annee
            FROM paies pa
            JOIN salaries s ON pa.salarie_id = s.id
            JOIN periodes p ON pa.periode_id = p.id
            JOIN societes so ON pa.societe_id = so.id
            WHERE so.user_id = $userId
            $societeFilterPa
            ORDER BY pa.created_at DESC
            LIMIT 10
        ")->fetchAll();

        $monthlyNet = $this->db->query("
            SELECT p.annee, p.mois, COALESCE(SUM(pa.net_a_payer), 0) AS total_net, COUNT(DISTINCT pa.salarie_id) AS nb_salaries
            FROM periodes p
            JOIN paies pa ON pa.periode_id = p.id
            JOIN societes so ON p.societe_id = so.id
            WHERE so.user_id = $userId
            $societeFilterP
            GROUP BY p.annee, p.mois
            ORDER BY p.annee DESC, p.mois DESC
            LIMIT 6
        ")->fetchAll();
        $monthlyNet = array_reverse($monthlyNet);

        $latestPeriode = null;
        if ($ctx) {
            $latestPeriode = $this->db->query("
                SELECT p.*, (SELECT COUNT(*) FROM paies pa WHERE pa.periode_id = p.id) AS nb_paies
                FROM periodes p
                WHERE p.societe_id = " . (int)$ctx['id'] . "
                ORDER BY p.annee DESC, p.mois DESC
                LIMIT 1
            ")->fetch();
        }

        $this->render('dashboard.php', [
            'title'          => 'Dashboard',
            'actions'        => '<a href="' . ($ctx ? '/paie-me/societes/' . (int)$ctx['id'] . '/paies' : '/paie-me/paies') . '" class="btn btn-primary btn-sm">Voir les paies</a>',
            'nbSocietes'     => $nbSocietes,
            'nbSalaries'     => $nbSalaries,
            'nbPeriodes'     => $nbPeriodes,
            'totalNet'       => $totalNet,
            'totalBrut'      => $totalBrut,
            'totalCnss'      => $totalCnss,
            'totalIr'        => $totalIr,
            'latestPaies'    => $latestPaies,
            'monthlyNet'     => $monthlyNet,
            'latestPeriode'  => $latestPeriode,
            'ctx'            => $ctx,
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
