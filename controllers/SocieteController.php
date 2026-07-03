<?php

namespace Controllers;

use Core\Controller;
use Core\Model;
use Core\Session;
use PDO;

class SocieteController extends Controller
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
        $societes = $this->db->query("SELECT * FROM societes WHERE user_id = $userId ORDER BY raison_sociale")->fetchAll();

        $this->render('societes/index.php', [
            'title'    => 'Sociétés',
            'societes' => $societes,
        ]);
    }

    public function create(): void
    {
        if ($this->isPost()) {
            $userId = Session::get('user_id');
            $data = $this->getPostData();

            $stmt = $this->db->prepare("
                INSERT INTO societes (user_id, raison_sociale, forme_juridique, ice, if_fiscal, rc, tp, cnss, adresse, ville, telephone, email, site_web, banque, agence, rib, compte_damancom, compte_simpl, compte_cimr)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $userId, $data['raison_sociale'], $data['forme_juridique'], $data['ice'], $data['if_fiscal'],
                $data['rc'], $data['tp'], $data['cnss'], $data['adresse'], $data['ville'], $data['telephone'],
                $data['email'], $data['site_web'], $data['banque'], $data['agence'], $data['rib'],
                $data['compte_damancom'], $data['compte_simpl'], $data['compte_cimr'],
            ]);

            Session::setFlash('success', 'Société créée avec succès.');
            $this->redirect('/paie-me/societes');
        }

        $this->render('societes/form.php', [
            'title'   => 'Nouvelle société',
            'societe' => null,
        ]);
    }

    public function show(int $id): void
    {
        $userId = Session::get('user_id');
        $societe = $this->db->query("SELECT * FROM societes WHERE id = $id AND user_id = $userId")->fetch();

        if (!$societe) {
            Session::setFlash('error', 'Société introuvable.');
            $this->redirect('/paie-me/societes');
        }

        Session::set('societe_context', [
            'id'             => $societe['id'],
            'raison_sociale' => $societe['raison_sociale'],
            'ice'            => $societe['ice'],
            'cnss'           => $societe['cnss'],
        ]);

        $salaries = $this->db->query("SELECT * FROM salaries WHERE societe_id = $id AND actif = 1 ORDER BY nom_famille, prenom")->fetchAll();
        $periodes = $this->db->query("SELECT p.*, (SELECT COUNT(*) FROM paies WHERE periode_id = p.id) as nb_paies FROM periodes p WHERE p.societe_id = $id ORDER BY p.annee DESC, p.mois DESC")->fetchAll();
        $bulletins = $this->db->query("
            SELECT b.*, pa.salaire_brut, pa.net_a_payer, s.nom_famille, s.prenom, p.mois, p.annee
            FROM bulletins b
            JOIN paies pa ON b.paie_id = pa.id
            JOIN salaries s ON pa.salarie_id = s.id
            JOIN periodes p ON pa.periode_id = p.id
            WHERE pa.societe_id = $id
            ORDER BY p.annee DESC, p.mois DESC, s.nom_famille
        ")->fetchAll();

        $this->render('societes/show.php', [
            'title'     => $societe['raison_sociale'],
            'societe'   => $societe,
            'salaries'  => $salaries,
            'periodes'  => $periodes,
            'bulletins' => $bulletins,
            'societeId' => $id,
        ]);
    }

    public function edit(int $id): void
    {
        $userId = Session::get('user_id');
        $societe = $this->db->query("SELECT * FROM societes WHERE id = $id AND user_id = $userId")->fetch();

        if (!$societe) {
            Session::setFlash('error', 'Société introuvable.');
            $this->redirect('/paie-me/societes');
        }

        if ($this->isPost()) {
            $data = $this->getPostData();

            $stmt = $this->db->prepare("
                UPDATE societes SET raison_sociale=?, forme_juridique=?, ice=?, if_fiscal=?, rc=?, tp=?, cnss=?, adresse=?, ville=?, telephone=?, email=?, site_web=?, banque=?, agence=?, rib=?, compte_damancom=?, compte_simpl=?, compte_cimr=?
                WHERE id = ?
            ");
            $stmt->execute([
                $data['raison_sociale'], $data['forme_juridique'], $data['ice'], $data['if_fiscal'],
                $data['rc'], $data['tp'], $data['cnss'], $data['adresse'], $data['ville'], $data['telephone'],
                $data['email'], $data['site_web'], $data['banque'], $data['agence'], $data['rib'],
                $data['compte_damancom'], $data['compte_simpl'], $data['compte_cimr'], $id,
            ]);

            Session::setFlash('success', 'Société mise à jour.');
            $this->redirect('/paie-me/societes');
        }

        $this->render('societes/form.php', [
            'title'   => 'Modifier société',
            'societe' => $societe,
        ]);
    }

    public function clearContext(): void
    {
        Session::remove('societe_context');
        $this->redirect('/paie-me/societes');
    }

    public function switchContext(int $id): void
    {
        $this->redirect('/paie-me/societes/' . $id);
    }

    public function delete(int $id): void
    {
        $userId = Session::get('user_id');
        $this->db->exec("DELETE FROM societes WHERE id = $id AND user_id = $userId");
        Session::setFlash('success', 'Société supprimée.');
        $this->redirect('/paie-me/societes');
    }

    public function parametres(int $id): void
    {
        $userId = Session::get('user_id');
        $societe = $this->db->query("SELECT * FROM societes WHERE id = $id AND user_id = $userId")->fetch();

        if (!$societe) {
            Session::setFlash('error', 'Société introuvable.');
            $this->redirect('/paie-me/societes');
        }

        Session::set('societe_context', [
            'id'             => $societe['id'],
            'raison_sociale' => $societe['raison_sociale'],
            'ice'            => $societe['ice'],
            'cnss'           => $societe['cnss'],
        ]);

        if ($this->isPost()) {
            $stmt = $this->db->prepare("
                UPDATE societes SET banque=?, agence=?, rib=?, compte_damancom=?, compte_simpl=?, compte_cimr=?
                WHERE id = ?
            ");
            $stmt->execute([
                $_POST['banque'] ?? '',
                $_POST['agence'] ?? '',
                $_POST['rib'] ?? '',
                $_POST['compte_damancom'] ?? '',
                $_POST['compte_simpl'] ?? '',
                $_POST['compte_cimr'] ?? '',
                $id,
            ]);

            Session::setFlash('success', 'Paramètres mis à jour.');
            $this->redirect('/paie-me/societes/' . $id . '/parametres?tab=' . ($_POST['sous_tab'] ?? 'banque'));
        }

        $bareme = $this->db->query("SELECT * FROM bareme_ir ORDER BY `min`")->fetchAll();

        $this->render('societes/parametres.php', [
            'title'   => 'Paramètres — ' . $societe['raison_sociale'],
            'societe' => $societe,
            'bareme'  => $bareme,
        ]);
    }

    private function getPostData(): array
    {
        return [
            'raison_sociale'  => $_POST['raison_sociale'] ?? '',
            'forme_juridique' => $_POST['forme_juridique'] ?? 'SARL',
            'ice'             => $_POST['ice'] ?? '',
            'if_fiscal'       => $_POST['if_fiscal'] ?? '',
            'rc'              => $_POST['rc'] ?? '',
            'tp'              => $_POST['tp'] ?? '',
            'cnss'            => $_POST['cnss'] ?? '',
            'adresse'         => $_POST['adresse'] ?? '',
            'ville'           => $_POST['ville'] ?? '',
            'telephone'       => $_POST['telephone'] ?? '',
            'email'           => $_POST['email'] ?? '',
            'site_web'        => $_POST['site_web'] ?? '',
            'banque'          => $_POST['banque'] ?? '',
            'agence'          => $_POST['agence'] ?? '',
            'rib'             => $_POST['rib'] ?? '',
            'compte_damancom' => $_POST['compte_damancom'] ?? '',
            'compte_simpl'    => $_POST['compte_simpl'] ?? '',
            'compte_cimr'     => $_POST['compte_cimr'] ?? '',
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
