<?php

namespace Controllers;

use Core\Controller;
use Core\Model;
use Core\Session;
use PDO;

class IrController extends Controller
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
            SELECT p.*, so.raison_sociale
            FROM periodes p
            JOIN societes so ON p.societe_id = so.id
            WHERE so.user_id = $userId AND p.cloturee = 1
            ORDER BY p.annee DESC, p.mois DESC
        ")->fetchAll();

        $this->render('ir/index.php', [
            'title'    => 'IR / SIMPL',
            'periodes' => $periodes,
        ]);
    }

    public function export(): void
    {
        if (!$this->isPost()) {
            $this->redirect('/paie-me/ir');
        }

        $periodeId = (int) ($_POST['periode_id'] ?? 0);
        $userId = Session::get('user_id');

        $periode = $this->db->query("
            SELECT p.*, so.raison_sociale, so.if_fiscal, so.ice
            FROM periodes p
            JOIN societes so ON p.societe_id = so.id
            WHERE p.id = $periodeId AND so.user_id = $userId
        ")->fetch();

        if (!$periode) {
            Session::setFlash('error', 'Période introuvable.');
            $this->redirect('/paie-me/ir');
        }

        $paies = $this->db->query("
            SELECT pa.*, s.nom_famille, s.prenom, s.cin
            FROM paies pa
            JOIN salaries s ON pa.salarie_id = s.id
            WHERE pa.periode_id = $periodeId
        ")->fetchAll();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="IR_' . $periode['if_fiscal'] . '_' . str_pad($periode['mois'], 2, '0', STR_PAD_LEFT) . $periode['annee'] . '.csv"');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        fputcsv($output, ['Matricule', 'Nom', 'Prénom', 'CIN', 'Salaire brut', 'CNSS', 'AMO', 'SNI', 'IR', 'Net à payer']);

        foreach ($paies as $paie) {
            fputcsv($output, [
                $paie['id'],
                $paie['nom_famille'],
                $paie['prenom'],
                $paie['cin'],
                number_format($paie['salaire_brut'], 2, ',', ''),
                number_format($paie['cnss_salariale'], 2, ',', ''),
                number_format($paie['amo_salariale'], 2, ',', ''),
                number_format($paie['sni'], 2, ',', ''),
                number_format($paie['ir'], 2, ',', ''),
                number_format($paie['net_a_payer'], 2, ',', ''),
            ]);
        }

        fclose($output);
        exit;
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
