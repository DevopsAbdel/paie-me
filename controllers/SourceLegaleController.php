<?php

namespace Controllers;

use Core\Controller;
use Core\Model;
use Core\Session;
use PDO;

class SourceLegaleController extends Controller
{
    private PDO $db;

    public function __construct()
    {
        if (!Session::has('user_id')) {
            $this->redirect('/paie-me/login');
        }
        $this->db = Model::db();
    }

    public function index(int $id): void
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
            $this->checkCsrf();
            // Sauvegarder le mapping article pour une rubrique
            if (!empty($_POST['rubrique_id']) && !empty($_POST['source_id']) && !empty($_POST['article'])) {
                $rubriqueId = (int)$_POST['rubrique_id'];
                $sourceId = (int)$_POST['source_id'];
                $article = trim($_POST['article']);

                $stmt = $this->db->prepare("
                    INSERT INTO rubrique_sources_articles (rubrique_id, source_id, article)
                    VALUES (?, ?, ?)
                    ON DUPLICATE KEY UPDATE article = VALUES(article)
                ");
                $stmt->execute([$rubriqueId, $sourceId, $article]);
                Session::setFlash('success', 'Article enregistré.');
            }

            $this->redirect('/paie-me/societes/' . $id . '/sources-legales');
        }

        // Suppression par GET (même pattern que SocieteController::parametres)
        if (isset($_GET['delete_rsa'])) {
            $this->db->exec("DELETE FROM rubrique_sources_articles WHERE id = " . (int)$_GET['delete_rsa']);
            Session::setFlash('success', 'Lien supprimé.');
            $this->redirect('/paie-me/societes/' . $id . '/sources-legales');
        }

        $sources = $this->db->query("SELECT * FROM sources_legales ORDER BY ISNULL(date_effet), date_effet DESC, code")->fetchAll();

        $rubriques = $this->db->query("
            SELECT * FROM rubriques_gains
            WHERE societe_id IS NULL OR societe_id = $id
            ORDER BY is_global DESC, code
        ")->fetchAll();

        // Articles par rubrique (pivot + source)
        $articles = $this->db->query("
            SELECT rsa.*, s.code as source_code, s.libelle as source_libelle, s.type as source_type,
                   rg.code as rubrique_code, rg.libelle as rubrique_libelle
            FROM rubrique_sources_articles rsa
            JOIN sources_legales s ON rsa.source_id = s.id
            JOIN rubriques_gains rg ON rsa.rubrique_id = rg.id
            ORDER BY rg.code, s.ordre
        ")->fetchAll();

        $baseUrl = '/paie-me/societes/' . $id . '/sources-legales';

        $this->render('societes/sources_legales.php', [
            'title'     => 'Sources légales — ' . $societe['raison_sociale'],
            'societe'   => $societe,
            'sources'   => $sources,
            'rubriques' => $rubriques,
            'articles'  => $articles,
            'baseUrl'   => $baseUrl,
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
