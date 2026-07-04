<?php

namespace Controllers;

use Core\Controller;
use Core\Model;
use Core\Session;
use Core\Audit;
use PDO;

class AuthController extends Controller
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Model::db();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function login(): void
    {
        if (Session::has('user_id')) {
            $this->redirect('/paie-me/dashboard');
        }

        if ($this->isPost()) {
            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($email === '' || $password === '') {
                $this->renderLogin('Veuillez remplir tous les champs.');
                return;
            }

            $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ? AND actif = 1 LIMIT 1');
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                Session::set('user_id', $user['id']);
                Session::set('user_nom', $user['nom']);
                Session::set('user_email', $user['email']);
                Session::set('user_role', $user['role']);
                Audit::log($this->db, 'login', 'user', $user['id'], 'Connexion utilisateur');
                $this->redirect('/paie-me/dashboard');
            }

            $this->renderLogin('Email ou mot de passe incorrect.');
            return;
        }

        $this->renderLogin();
    }

    public function logout(): void
    {
        Audit::log($this->db, 'logout', 'user', Session::get('user_id'), 'Déconnexion utilisateur');
        Session::destroy();
        $this->redirect('/paie-me/login');
    }

    private function renderLogin(string $error = null): void
    {
        $content = '';
        ob_start();
        require __DIR__ . '/../views/login.php';
        $content = ob_get_clean();
        require __DIR__ . '/../views/layout.php';
    }
}
