<?php

require_once __DIR__ . '/Core/Router.php';
require_once __DIR__ . '/Core/Controller.php';
require_once __DIR__ . '/Core/Model.php';
require_once __DIR__ . '/Core/Session.php';
require_once __DIR__ . '/Core/Helper.php';

use Core\Session;
use Core\Router;

Session::start();

$app = require __DIR__ . '/config/app.php';
date_default_timezone_set($app['timezone']);

require_once __DIR__ . '/routes.php';

$method = $_SERVER['REQUEST_METHOD'];
$uri    = $_SERVER['REQUEST_URI'];

Router::dispatch($method, $uri);
