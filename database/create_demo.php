<?php
/**
 * Création / initialisation de la base démo (paie_me_demo).
 * Usage CLI : php database/create_demo.php
 * Appelable depuis l'application via create_demo_database().
 */

define('SEED_INCLUDED', true);
require_once __DIR__ . '/seed_demo.php';

use Core\Model;
/**
 * Crée la base démo si elle n'existe pas (schéma + seed), puis renvoie
 * une connexion PDO ouverte sur cette base.
 */
function create_demo_database(): PDO
{
    $config = require __DIR__ . '/../config/database.php';
    $demoDb = Model::demoDbName();

    $server = new PDO(
        "mysql:host={$config['host']};port={$config['port']};charset={$config['charset']}",
        $config['username'],
        $config['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $server->exec("CREATE DATABASE IF NOT EXISTS `$demoDb` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

    $pdo = new PDO(
        "mysql:host={$config['host']};port={$config['port']};dbname=$demoDb;charset={$config['charset']}",
        $config['username'],
        $config['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $hasUsers = (int) $pdo->query(
        "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$demoDb' AND table_name = 'users'"
    )->fetchColumn();

    if ($hasUsers === 0) {
        $schema = file_get_contents(__DIR__ . '/schema.sql');
        $schema = str_replace('paie_me', $demoDb, $schema);
        $pdo->exec($schema);
    }

    seed_demo_database($pdo);

    return $pdo;
}

if (PHP_SAPI === 'cli') {
    create_demo_database();
    echo "  + base démo prête\n";
    exit(0);
}
