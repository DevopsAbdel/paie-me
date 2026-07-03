<?php

namespace Controllers;

use Core\Controller;
use Core\Model;
use Core\Session;
use PDO;

class BulletinController extends Controller
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
        $bulletins = $this->db->query("
            SELECT b.*, pa.salaire_brut, pa.net_a_payer, pa.cnss_salariale, pa.amo_salariale, pa.ir,
                   s.nom_famille, s.prenom, so.raison_sociale, p.mois, p.annee
            FROM bulletins b
            JOIN paies pa ON b.paie_id = pa.id
            JOIN salaries s ON pa.salarie_id = s.id
            JOIN periodes p ON pa.periode_id = p.id
            JOIN societes so ON pa.societe_id = so.id
            WHERE so.user_id = $userId
            ORDER BY p.annee DESC, p.mois DESC, s.nom_famille
        ")->fetchAll();

        $this->render('bulletins/index.php', [
            'title'     => 'Bulletins de paie',
            'bulletins' => $bulletins,
        ]);
    }

    public function show(int $id): void
    {
        $bulletin = $this->getBulletin($id);
        if (!$bulletin) {
            Session::setFlash('error', 'Bulletin introuvable.');
            $this->redirect('/paie-me/bulletins');
        }

        $this->render('bulletins/show.php', [
            'title'    => 'Bulletin de paie',
            'b'        => $bulletin,
        ]);
    }

    public function pdf(int $id): void
    {
        $bulletin = $this->getBulletin($id);
        if (!$bulletin) {
            Session::setFlash('error', 'Bulletin introuvable.');
            $this->redirect('/paie-me/bulletins');
        }

        $this->render('bulletins/pdf.php', [
            'title' => 'Bulletin de paie',
            'b'     => $bulletin,
        ]);
    }

    private function getBulletin(int $id): ?array
    {
        $userId = Session::get('user_id');
        $stmt = $this->db->prepare("
            SELECT b.*, pa.*, s.nom_famille, s.prenom, s.matricule, s.cin, s.cnss as cnss_num, s.poste, s.fonction_id,
                   f.nom as fonction_nom,
                   s.date_embauche, s.situation_familiale, s.nb_enfants, s.indemnite_transport,
                   s.indemnite_panier, s.indemnite_representation, s.avantage_logement, s.salaire_base,
                   so.raison_sociale, so.ice, so.if_fiscal, so.cnss as cnss_societe, so.rc, so.ville,
                   so.adresse, so.telephone, so.email, so.logo, so.banque, so.rib,
                   p.mois, p.annee
            FROM bulletins b
            JOIN paies pa ON b.paie_id = pa.id
            JOIN salaries s ON pa.salarie_id = s.id
            LEFT JOIN fonctions f ON s.fonction_id = f.id
            JOIN periodes p ON pa.periode_id = p.id
            JOIN societes so ON pa.societe_id = so.id
            WHERE b.id = ? AND so.user_id = ?
            LIMIT 1
        ");
        $stmt->execute([$id, $userId]);
        return $stmt->fetch() ?: null;
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
