<?php

namespace Controllers;

use Core\Controller;
use Core\Model;
use Core\Session;
use PDO;

class ComptabiliteController extends Controller
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

        $this->render('comptabilite/index.php', [
            'title'    => 'Export comptable CGNC',
            'periodes' => $periodes,
        ]);
    }

    public function export(): void
    {
        $periodeId = (int) ($_POST['periode_id'] ?? $_GET['periode_id'] ?? 0);
        if (!$periodeId) {
            $ctx = Session::get('societe_context');
            Session::setFlash('error', 'Période non spécifiée.');
            if ($ctx) {
                $this->redirect('/paie-me/societes/' . $ctx['id'] . '?tab=compta');
            }
            $this->redirect('/paie-me/comptabilite');
        }
        $userId = Session::get('user_id');

        $periode = $this->db->query("
            SELECT p.*, so.raison_sociale, so.if_fiscal, so.ice
            FROM periodes p
            JOIN societes so ON p.societe_id = so.id
            WHERE p.id = $periodeId AND so.user_id = $userId
        ")->fetch();

        if (!$periode) {
            Session::setFlash('error', 'Période introuvable.');
            $this->redirect('/paie-me/comptabilite');
        }

        $paies = $this->db->query("
            SELECT pa.*, s.nom_famille, s.prenom
            FROM paies pa
            JOIN salaries s ON pa.salarie_id = s.id
            WHERE pa.periode_id = $periodeId
        ")->fetchAll();

        $totalBrut = array_sum(array_column($paies, 'salaire_brut'));
        $totalCnss = array_sum(array_column($paies, 'cnss_salariale'));
        $totalAmo = array_sum(array_column($paies, 'amo_salariale'));
        $totalIr = array_sum(array_column($paies, 'ir'));
        $totalNet = array_sum(array_column($paies, 'net_a_payer'));
        $totalCnssPat = array_sum(array_column($paies, 'cnss_patronale'));
        $totalAmoPat = array_sum(array_column($paies, 'amo_patronale'));

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="CGNC_' . $periode['if_fiscal'] . '_' . str_pad($periode['mois'], 2, '0', STR_PAD_LEFT) . $periode['annee'] . '.csv"');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        fputcsv($output, ['CGNC', 'Journal de paie', str_pad($periode['mois'], 2, '0', STR_PAD_LEFT) . '/' . $periode['annee'], $periode['raison_sociale']]);
        fputcsv($output, ['Compte', 'Libellé', 'Débit', 'Crédit']);

        fputcsv($output, ['6171', 'Rémunérations du personnel', number_format($totalBrut, 2, ',', ''), '']);
        fputcsv($output, ['6174', 'Cotisations CNSS part patronale', number_format($totalCnssPat, 2, ',', ''), '']);
        fputcsv($output, ['6175', 'Cotisations AMO part patronale', number_format($totalAmoPat, 2, ',', ''), '']);
        fputcsv($output, ['4431', 'Rémunérations dues au personnel', '', number_format($totalNet, 2, ',', '')]);
        fputcsv($output, ['4441', 'CNSS retenue salariale', '', number_format($totalCnss, 2, ',', '')]);
        fputcsv($output, ['4442', 'AMO retenue salariale', '', number_format($totalAmo, 2, ',', '')]);
        fputcsv($output, ['4455', 'IR retenu à la source', '', number_format($totalIr, 2, ',', '')]);

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
