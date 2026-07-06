<?php

namespace Controllers;

use Core\Controller;
use Core\Model;
use Core\Session;
use PDO;

class DamancomController extends Controller
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
        $sql = "
            SELECT p.*, so.raison_sociale
            FROM periodes p
            JOIN societes so ON p.societe_id = so.id
            WHERE so.user_id = $userId AND p.cloturee = 1
        ";
        if ($ctx) {
            $sql .= " AND p.societe_id = " . (int)$ctx['id'];
        }
        $sql .= " ORDER BY p.annee DESC, p.mois DESC";
        $periodes = $this->db->query($sql)->fetchAll();

        $this->render('damancom/index.php', [
            'title'    => 'CNSS / Damancom',
            'periodes' => $periodes,
        ]);
    }

    public function generate(): void
    {
        $periodeId = (int) ($_POST['periode_id'] ?? $_GET['periode_id'] ?? 0);
        if (!$periodeId) {
            Session::setFlash('error', 'Période non spécifiée.');
            $ctx = Session::get('societe_context');
            if ($ctx) {
                $this->redirect('/paie-me/societes/' . $ctx['id'] . '/cnss');
            }
            $this->redirect('/paie-me/damancom');
        }
        $userId = Session::get('user_id');

        $periode = $this->db->query("
            SELECT p.*, so.raison_sociale, so.cnss as cnss_societe, so.ice
            FROM periodes p
            JOIN societes so ON p.societe_id = so.id
            WHERE p.id = $periodeId AND so.user_id = $userId
        ")->fetch();

        if (!$periode) {
            Session::setFlash('error', 'Période introuvable.');
            $this->redirect('/paie-me/damancom');
        }

        $paies = $this->db->query("
            SELECT pa.*, s.nom_famille, s.prenom, s.cnss as cnss_num
            FROM paies pa
            JOIN salaries s ON pa.salarie_id = s.id
            WHERE pa.periode_id = $periodeId
        ")->fetchAll();

        $lines = [];
        $lines[] = 'B00' . str_pad('', 197);
        $lines[] = 'B01' . str_pad($periode['cnss_societe'], 15) . str_pad('', 185);

        foreach ($paies as $paie) {
            $nomComplet = mb_substr(strtoupper($paie['nom_famille'] . ' ' . $paie['prenom']), 0, 30);
            $cnssNum = str_pad($paie['cnss_num'] ?? '', 15);
            $montantCnss = str_pad((int) round($paie['cnss_salariale'] * 100), 10, '0', STR_PAD_LEFT);

            $lines[] = 'B02' . $cnssNum . str_pad($nomComplet, 30) . $montantCnss . str_pad('', 155);
        }

        $totalSalaries = count($paies);
        $totalCnss = array_sum(array_column($paies, 'cnss_salariale'));
        $penalitesCnss = (float) ($periode['penalites_cnss'] ?? 0);
        $penalitesTfp = (float) ($periode['penalites_tfp'] ?? 0);
        $penalitesAmo = (float) ($periode['penalites_amo'] ?? 0);
        $totalGeneral = $totalCnss + $penalitesCnss + $penalitesTfp + $penalitesAmo;
        $lines[] = 'B05' . str_pad('', 200);
        $lines[] = 'B06' . str_pad($totalSalaries, 10, '0', STR_PAD_LEFT) . str_pad((int) round($totalGeneral * 100), 15, '0', STR_PAD_LEFT) . str_pad('', 175);

        $content = implode("\r\n", $lines);

        $filename = 'DS_' . $periode['cnss_societe'] . '_' . str_pad($periode['mois'], 2, '0', STR_PAD_LEFT) . $periode['annee'] . '.txt';

        header('Content-Type: text/plain; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $content;
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
