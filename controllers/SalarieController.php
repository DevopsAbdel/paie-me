<?php

namespace Controllers;

use Core\Controller;
use Core\Model;
use Core\Session;
use Core\Audit;
use Core\Crypto;
use PDO;

class SalarieController extends Controller
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
        $salaries = $this->db->query("
            SELECT s.*, so.raison_sociale, f.nom as fonction_nom
            FROM salaries s
            JOIN societes so ON s.societe_id = so.id
            LEFT JOIN fonctions f ON s.fonction_id = f.id
            WHERE so.user_id = $userId
            ORDER BY s.nom_famille, s.prenom
        ")->fetchAll();

        $this->render('salaries/index.php', [
            'title'    => 'Salariés',
            'salaries' => $salaries,
        ]);
    }

    public function create(): void
    {
        $userId = Session::get('user_id');
        $societes = $this->db->query("SELECT id, raison_sociale FROM societes WHERE user_id = $userId ORDER BY raison_sociale")->fetchAll();
        $fromSociete = isset($_GET['from_societe']) ? (int) $_GET['from_societe'] : null;

        if ($this->isPost()) {
            $this->checkCsrf();
            $data = $this->getPostData();
            $data['rib'] = Crypto::encrypt($data['rib']);
            $data['cin'] = Crypto::encrypt($data['cin']);
            $services = $this->db->query("SELECT * FROM services WHERE societe_id = " . (int)$data['societe_id'] . " ORDER BY nom")->fetchAll();
            $stmt = $this->db->prepare("
                INSERT INTO salaries (societe_id, service_id, fonction_id, matricule, nom_famille, prenom, adresse, date_naissance, date_embauche, cin, cnss, situation_familiale, nb_enfants, enfants_a_charge, personnes_a_charge, poste, type_contrat, salaire_base, type_salaire, frequence_paiement, mode_paiement, rib, indemnite_transport, indemnite_panier, indemnite_representation, avantage_logement, avances_salaire, mutuelle)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $data['societe_id'], $data['service_id'], $data['fonction_id'], $data['matricule'], $data['nom_famille'], $data['prenom'],
                $data['adresse'], $data['date_naissance'], $data['date_embauche'], $data['cin'],
                $data['cnss'], $data['situation_familiale'], $data['nb_enfants'], $data['enfants_a_charge'], $data['personnes_a_charge'], $data['poste'],
                $data['type_contrat'], $data['salaire_base'], $data['type_salaire'],
                $data['frequence_paiement'], $data['mode_paiement'], $data['rib'],
                $data['indemnite_transport'], $data['indemnite_panier'],
                $data['indemnite_representation'], $data['avantage_logement'],
                $data['avances_salaire'], $data['mutuelle'],
            ]);

            Audit::log($this->db, 'create', 'salarie', (int) $this->db->lastInsertId(), 'Création salarié: ' . $data['nom_famille'] . ' ' . $data['prenom']);

            Session::setFlash('success', 'Salarié ajouté avec succès.');
            $this->redirect($fromSociete ? '/paie-me/societes/' . $fromSociete . '?tab=salaries' : '/paie-me/salaries');
        }

        $services = $fromSociete ? $this->db->query("SELECT * FROM services WHERE societe_id = $fromSociete ORDER BY nom")->fetchAll() : [];
        $fonctions = $fromSociete ? $this->db->query("SELECT * FROM fonctions WHERE societe_id = $fromSociete ORDER BY nom")->fetchAll() : [];
        $this->render('salaries/form.php', [
            'title'       => 'Nouveau salarié',
            'salarie'     => null,
            'societes'    => $societes,
            'services'    => $services,
            'fonctions'   => $fonctions,
            'fromSociete' => $fromSociete,
        ]);
    }

    public function edit(int $id): void
    {
        $userId = Session::get('user_id');
        $salarie = $this->db->query("
            SELECT s.* FROM salaries s
            JOIN societes so ON s.societe_id = so.id
            WHERE s.id = $id AND so.user_id = $userId
        ")->fetch();

        if (!$salarie) {
            Session::setFlash('error', 'Salarié introuvable.');
            $this->redirect('/paie-me/salaries');
        }

        $societes = $this->db->query("SELECT id, raison_sociale FROM societes WHERE user_id = $userId ORDER BY raison_sociale")->fetchAll();

        if ($this->isPost()) {
            $this->checkCsrf();
            $data = $this->getPostData();
            $data['rib'] = Crypto::encrypt($data['rib']);
            $data['cin'] = Crypto::encrypt($data['cin']);
            $stmt = $this->db->prepare("
                UPDATE salaries SET societe_id=?, service_id=?, fonction_id=?, matricule=?, nom_famille=?, prenom=?, adresse=?, date_naissance=?, date_embauche=?, cin=?, cnss=?, situation_familiale=?, nb_enfants=?, enfants_a_charge=?, personnes_a_charge=?, poste=?, type_contrat=?, salaire_base=?, type_salaire=?, frequence_paiement=?, mode_paiement=?, rib=?, indemnite_transport=?, indemnite_panier=?, indemnite_representation=?, avantage_logement=?, avances_salaire=?, mutuelle=?
                WHERE id = ?
            ");
            $stmt->execute([
                $data['societe_id'], $data['service_id'], $data['fonction_id'], $data['matricule'], $data['nom_famille'], $data['prenom'],
                $data['adresse'], $data['date_naissance'], $data['date_embauche'], $data['cin'],
                $data['cnss'], $data['situation_familiale'], $data['nb_enfants'], $data['enfants_a_charge'], $data['personnes_a_charge'], $data['poste'],
                $data['type_contrat'], $data['salaire_base'], $data['type_salaire'],
                $data['frequence_paiement'], $data['mode_paiement'], $data['rib'],
                $data['indemnite_transport'], $data['indemnite_panier'],
                $data['indemnite_representation'], $data['avantage_logement'],
                $data['avances_salaire'], $data['mutuelle'], $id,
            ]);

            Audit::log($this->db, 'update', 'salarie', $id, 'Modification salarié: ' . $salarie['nom_famille'] . ' ' . $salarie['prenom']);

            Session::setFlash('success', 'Salarié mis à jour.');
            $societeId = $data['societe_id'] ?? $salarie['societe_id'];
            $this->redirect('/paie-me/societes/' . $societeId . '?tab=salaries');
        }

        $fromSociete = isset($_GET['from_societe']) ? (int) $_GET['from_societe'] : null;
        $societeId = $salarie['societe_id'];
        $services = $this->db->query("SELECT * FROM services WHERE societe_id = $societeId ORDER BY nom")->fetchAll();
        $fonctions = $this->db->query("SELECT * FROM fonctions WHERE societe_id = $societeId ORDER BY nom")->fetchAll();

        $salarie['rib'] = Crypto::decrypt($salarie['rib']);
        $salarie['cin'] = Crypto::decrypt($salarie['cin']);

        $this->render('salaries/form.php', [
            'title'       => 'Modifier salarié',
            'salarie'     => $salarie,
            'societes'    => $societes,
            'services'    => $services,
            'fonctions'   => $fonctions,
            'fromSociete' => $fromSociete,
        ]);
    }

    public function delete(int $id): void
    {
        $this->checkCsrf();
        $this->requireRole('admin');
        $userId = Session::get('user_id');
        $salarie = $this->db->query("SELECT nom_famille, prenom FROM salaries WHERE id = $id")->fetch();
        Audit::log($this->db, 'delete', 'salarie', $id, 'Suppression salarié: ' . ($salarie['nom_famille'] ?? '') . ' ' . ($salarie['prenom'] ?? ''));
        $this->db->exec("
            DELETE s FROM salaries s
            JOIN societes so ON s.societe_id = so.id
            WHERE s.id = $id AND so.user_id = $userId
        ");
        Session::setFlash('success', 'Salarié supprimé.');
        $this->redirect('/paie-me/salaries');
    }

    public function stc(int $id): void
    {
        $userId = Session::get('user_id');
        $salarie = $this->db->query("
            SELECT s.*, so.raison_sociale, so.ice, so.if_fiscal, so.cnss as cnss_societe,
                   so.rc, so.ville, so.adresse, so.telephone, so.email, so.logo
            FROM salaries s
            JOIN societes so ON s.societe_id = so.id
            WHERE s.id = $id AND so.user_id = $userId
        ")->fetch();

        if (!$salarie) {
            Session::setFlash('error', 'Salarié introuvable.');
            $this->redirect('/paie-me/salaries');
        }

        $dernierePaie = $this->db->query("
            SELECT pa.*, p.mois, p.annee FROM paies pa
            JOIN periodes p ON pa.periode_id = p.id
            WHERE pa.salarie_id = $id
            ORDER BY p.annee DESC, p.mois DESC
            LIMIT 1
        ")->fetch();

        $salarie['rib'] = Crypto::decrypt($salarie['rib']);
        $salarie['cin'] = Crypto::decrypt($salarie['cin']);

        $this->render('salaries/stc.php', [
            'title'       => 'Solde de Tout Compte — ' . $salarie['nom_famille'] . ' ' . $salarie['prenom'],
            's'           => $salarie,
            'dernierePaie' => $dernierePaie,
        ]);
    }

    public function stcPdf(int $id): void
    {
        $userId = Session::get('user_id');
        $salarie = $this->db->query("
            SELECT s.*, so.raison_sociale, so.ice, so.if_fiscal, so.cnss as cnss_societe,
                   so.rc, so.ville, so.adresse, so.telephone, so.email, so.logo
            FROM salaries s
            JOIN societes so ON s.societe_id = so.id
            WHERE s.id = $id AND so.user_id = $userId
        ")->fetch();

        if (!$salarie) {
            Session::setFlash('error', 'Salarié introuvable.');
            $this->redirect('/paie-me/salaries');
        }

        $dernierePaie = $this->db->query("
            SELECT pa.*, p.mois, p.annee FROM paies pa
            JOIN periodes p ON pa.periode_id = p.id
            WHERE pa.salarie_id = $id
            ORDER BY p.annee DESC, p.mois DESC
            LIMIT 1
        ")->fetch();

        $salarie['rib'] = Crypto::decrypt($salarie['rib']);
        $salarie['cin'] = Crypto::decrypt($salarie['cin']);

        ob_start();
        require __DIR__ . '/../views/salaries/stc_pdf.php';
        $html = ob_get_clean();

        $dompdf = new \Dompdf\Dompdf(['defaultFont' => 'DejaVu Sans']);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dompdf->stream('stc_' . $salarie['matricule'] . '.pdf', ['Attachment' => false]);
        exit;
    }

    private function getPostData(): array
    {
        return [
            'societe_id'             => $_POST['societe_id'] ?? 0,
            'service_id'             => $_POST['service_id'] ?? null,
            'fonction_id'            => !empty($_POST['fonction_id']) ? (int)$_POST['fonction_id'] : null,
            'matricule'              => $_POST['matricule'] ?? '',
            'nom_famille'            => $_POST['nom_famille'] ?? '',
            'prenom'                 => $_POST['prenom'] ?? '',
            'adresse'                => $_POST['adresse'] ?? '',
            'date_naissance'         => $_POST['date_naissance'] ?? null,
            'date_embauche'          => $_POST['date_embauche'] ?? null,
            'cin'                    => $_POST['cin'] ?? '',
            'cnss'                   => $_POST['cnss'] ?? '',
            'situation_familiale'    => $_POST['situation_familiale'] ?? 'celibataire',
            'nb_enfants'             => $_POST['nb_enfants'] ?? 0,
            'enfants_a_charge'       => $_POST['enfants_a_charge'] ?? 0,
            'personnes_a_charge'     => $_POST['personnes_a_charge'] ?? 0,
            'poste'                  => $_POST['poste'] ?? '',
            'type_contrat'           => $_POST['type_contrat'] ?? 'CDI',
            'salaire_base'           => $_POST['salaire_base'] ?? 0,
            'type_salaire'           => $_POST['type_salaire'] ?? 'mensuel',
            'frequence_paiement'     => $_POST['frequence_paiement'] ?? 'mensuel',
            'mode_paiement'          => $_POST['mode_paiement'] ?? 'virement',
            'rib'                    => $_POST['rib'] ?? '',
            'indemnite_transport'    => $_POST['indemnite_transport'] ?? 500.00,
            'indemnite_panier'       => $_POST['indemnite_panier'] ?? 780.00,
            'indemnite_representation' => $_POST['indemnite_representation'] ?? 0,
            'avantage_logement'      => $_POST['avantage_logement'] ?? 0,
            'avances_salaire'        => $_POST['avances_salaire'] ?? 0,
            'mutuelle'               => $_POST['mutuelle'] ?? 0,
        ];
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
