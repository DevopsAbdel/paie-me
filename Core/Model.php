<?php

namespace Core;

use PDO;
use PDOException;

abstract class Model
{
    protected static PDO|null $db = null;

    public static function demoDbName(): string
    {
        return 'paie_me_demo';
    }

    public static function db(): PDO
    {
        if (self::$db === null) {
            $config = require __DIR__ . '/../config/database.php';
            if (Session::get('demo_mode')) {
                $config['dbname'] = self::demoDbName();
            }
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset={$config['charset']}";
            self::$db = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }
        return self::$db;
    }

    public static function resetDb(): void
    {
        self::$db = null;
    }
}
