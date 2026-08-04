<?php

require_once __DIR__ . '/vendor/autoload.php';

spl_autoload_register(function (string $class) {
    $prefixes = [
        'Core\\'        => __DIR__ . '/Core/',
        'Controllers\\' => __DIR__ . '/controllers/',
    ];
    foreach ($prefixes as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) === 0) {
            $relative = substr($class, $len);
            $file = $baseDir . str_replace('\\', '/', $relative) . '.php';
            if (file_exists($file)) {
                require $file;
                return;
            }
        }
    }
});

header('Content-Type: text/html; charset=utf-8');
mb_internal_encoding('UTF-8');

use Core\Session;
use Core\Router;

Session::start();

// Intégrité de session : si l'utilisateur connecté a été supprimé ou désactivé
// (ex: via la page admin), on invalide sa session pour éviter les erreurs de FK
// (audit_log, societes) et l'accès d'un compte inactif.
if (Session::has('user_id')) {
    try {
        $stmt = Core\Model::db()->prepare('SELECT actif FROM users WHERE id = ?');
        $stmt->execute([(int) Session::get('user_id')]);
        $user = $stmt->fetch();
        if (!$user || (int) $user['actif'] !== 1) {
            Session::destroy();
            header('Location: /paie-me/login');
            exit;
        }
    } catch (\Throwable $e) {
        // Base indisponible : on ne bloque pas (la page s'occupera de l'erreur).
    }
}

$app = require __DIR__ . '/config/app.php';
date_default_timezone_set($app['timezone']);

require_once __DIR__ . '/routes.php';

$method = $_SERVER['REQUEST_METHOD'];
$uri    = $_SERVER['REQUEST_URI'];

Router::dispatch($method, $uri);
