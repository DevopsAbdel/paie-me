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

$app = require __DIR__ . '/config/app.php';
date_default_timezone_set($app['timezone']);

require_once __DIR__ . '/routes.php';

$method = $_SERVER['REQUEST_METHOD'];
$uri    = $_SERVER['REQUEST_URI'];

Router::dispatch($method, $uri);
