<?php

namespace Controllers;

use Core\Controller;
use Core\Model;
use Core\Session;
use PDO;

class AdminController extends Controller
{
    private PDO $db;

    public function __construct()
    {
        if (!Session::has('user_id')) {
            $this->redirect('/paie-me/login');
        }
        $this->requireRole('admin');
        $this->db = Model::db();
    }

    public function index(): void
    {
        if ($this->isPost()) {
            $this->checkCsrf();
            $action = $_POST['action'] ?? '';

            switch ($action) {
                case 'create_demo':
                case 'reset_demo':
                    try {
                        if ($action === 'reset_demo') {
                            $this->dropDatabase(Model::demoDbName());
                        }
                        require_once __DIR__ . '/../database/create_demo.php';
                        create_demo_database();
                        Session::setFlash('success', 'Base démo créée / réinitialisée (schéma + seed).');
                    } catch (\Throwable $e) {
                        Session::setFlash('error', 'Erreur base démo : ' . $e->getMessage());
                    }
                    break;

                case 'reseed':
                    try {
                        $this->dropDatabase(Model::demoDbName());
                        require_once __DIR__ . '/../database/create_demo.php';
                        create_demo_database();
                        Session::setFlash('success', 'Base démo vidée puis re-seedée.');
                    } catch (\Throwable $e) {
                        Session::setFlash('error', 'Erreur re-seed : ' . $e->getMessage());
                    }
                    break;

                case 'migrate':
                    try {
                        ob_start();
                        require_once __DIR__ . '/../database/migrate.php';
                        $out = ob_get_clean();
                        Session::setFlash('success', 'Migrations appliquées sur paie_me et paie_me_demo.');
                    } catch (\Throwable $e) {
                        if (ob_get_level()) ob_end_clean();
                        Session::setFlash('error', 'Erreur migration : ' . $e->getMessage());
                    }
                    break;

                case 'sync_schema':
                    try {
                        require_once __DIR__ . '/../database/sync_schema.php';
                        $r = sync_schema_database();
                        $nb = count($r['tables_created']) + count($r['columns_added']) + count($r['indexes_added']);
                        $msg = 'Schéma synchronisé : '
                            . count($r['tables_created']) . ' table(s) créée(s), '
                            . count($r['columns_added']) . ' colonne(s) ajoutée(s), '
                            . count($r['indexes_added']) . ' index ajouté(s).';
                        if ($nb === 0) {
                            Session::setFlash('success', 'Schéma à jour — aucune différence entre paie_me et paie_me_demo.');
                        } elseif ($r['errors']) {
                            Session::setFlash('warning', $msg . ' Erreurs : ' . implode(' | ', array_slice($r['errors'], 0, 3)));
                        } else {
                            Session::setFlash('success', $msg);
                        }
                    } catch (\Throwable $e) {
                        Session::setFlash('error', 'Erreur synchronisation : ' . $e->getMessage());
                    }
                    break;

                case 'create_user':
                    $this->createUser();
                    break;

                case 'toggle_user':
                    $this->toggleUser();
                    break;

                case 'delete_user':
                    $this->deleteUser();
                    break;

                default:
                    Session::setFlash('warning', 'Action inconnue.');
            }

            $this->redirect('/paie-me/admin');
        }

        $this->render('admin/index.php', [
            'title'     => 'Administration',
            'databases' => $this->dbStatus(),
            'users'     => $this->users(),
        ]);
    }

    // ── Gestion des bases ──

    private function serverPdo(): PDO
    {
        $config = require __DIR__ . '/../config/database.php';
        return new PDO(
            "mysql:host={$config['host']};port={$config['port']};charset={$config['charset']}",
            $config['username'],
            $config['password'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }

    private function dropDatabase(string $dbname): void
    {
        $server = $this->serverPdo();
        $server->exec("DROP DATABASE IF EXISTS `$dbname`");
    }

    private function dbStatus(): array
    {
        $config = require __DIR__ . '/../config/database.php';
        $server = $this->serverPdo();
        $demo = Model::demoDbName();
        $currentDemo = Session::get('demo_mode') ? true : false;

        $out = [];
        foreach ([$config['dbname'], $demo] as $dbname) {
            $exists = (int) $server->query(
                "SELECT COUNT(*) FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = " . $server->quote($dbname)
            )->fetchColumn() > 0;

            $nbTables = '-';
            $nbSocietes = '-';
            $size = '-';
            if ($exists) {
                $nbTables = (int) $server->query(
                    "SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = " . $server->quote($dbname)
                )->fetchColumn();

                $hasSocietes = (int) $server->query(
                    "SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = " . $server->quote($dbname) . " AND TABLE_NAME = 'societes'"
                )->fetchColumn();
                if ($hasSocietes) {
                    $nbSocietes = (int) $server->query("SELECT COUNT(*) FROM `$dbname`.societes")->fetchColumn();
                }
                $size = $server->query(
                    "SELECT CONCAT(ROUND(SUM(data_length + index_length) / 1024 / 1024, 2), ' MB') FROM information_schema.TABLES WHERE TABLE_SCHEMA = " . $server->quote($dbname)
                )->fetchColumn() ?? '-';
                if ($size === null || $size === '') $size = '0.00 MB';
            }

            $out[] = [
                'name'        => $dbname,
                'exists'      => $exists,
                'nb_tables'   => $nbTables,
                'nb_societes' => $nbSocietes,
                'size'        => $size,
                'current'     => $exists && (($dbname === $demo) === $currentDemo),
            ];
        }
        return $out;
    }

    // ── Gestion des utilisateurs ──

    private function users(): array
    {
        return $this->db->query(
            "SELECT id, nom, email, role, actif, created_at FROM users ORDER BY id"
        )->fetchAll();
    }

    private function createUser(): void
    {
        $nom = trim($_POST['nom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
        $role = (($_POST['role'] ?? 'gestionnaire') === 'admin') ? 'admin' : 'gestionnaire';

        if ($nom === '' || $email === '') {
            Session::setFlash('error', 'Nom et email obligatoires.');
            return;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::setFlash('error', 'Adresse email invalide.');
            return;
        }
        if (strlen($password) < 6) {
            Session::setFlash('error', 'Mot de passe : 6 caractères minimum.');
            return;
        }

        try {
            $stmt = $this->db->prepare("INSERT INTO users (nom, email, password, role, actif) VALUES (?, ?, ?, ?, 1)");
            $stmt->execute([$nom, $email, password_hash($password, PASSWORD_DEFAULT), $role]);
            Session::setFlash('success', "Utilisateur « $nom » créé.");
        } catch (\PDOException $e) {
            Session::setFlash('error', 'Email déjà utilisé ou erreur d\'insertion.');
        }
    }

    private function toggleUser(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id === (int) Session::get('user_id')) {
            Session::setFlash('warning', 'Vous ne pouvez pas désactiver votre propre compte.');
            return;
        }
        $user = $this->db->query("SELECT id, actif FROM users WHERE id = $id")->fetch();
        if (!$user) {
            Session::setFlash('error', 'Utilisateur introuvable.');
            return;
        }
        $new = $user['actif'] ? 0 : 1;
        $this->db->exec("UPDATE users SET actif = $new WHERE id = $id");
        Session::setFlash('success', 'Statut de l\'utilisateur mis à jour.');
    }

    private function deleteUser(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id === (int) Session::get('user_id')) {
            Session::setFlash('warning', 'Vous ne pouvez pas supprimer votre propre compte.');
            return;
        }
        $target = $this->db->query("SELECT role, actif FROM users WHERE id = $id")->fetch();
        if (!$target) {
            Session::setFlash('error', 'Utilisateur introuvable.');
            return;
        }
        if ($target['role'] === 'admin' && $target['actif'] == 1) {
            $adminCount = (int) $this->db->query("SELECT COUNT(*) FROM users WHERE role = 'admin' AND actif = 1")->fetchColumn();
            if ($adminCount <= 1) {
                Session::setFlash('error', 'Impossible de supprimer le dernier administrateur actif.');
                return;
            }
        }
        $this->db->exec("DELETE FROM users WHERE id = $id");
        Session::setFlash('success', 'Utilisateur supprimé.');
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
