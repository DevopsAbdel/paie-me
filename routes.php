<?php

use Core\Router;
use Controllers\AuthController;
use Controllers\DashboardController;
use Controllers\SocieteController;
use Controllers\SalarieController;
use Controllers\PaieController;
use Controllers\BulletinController;
use Controllers\DamancomController;
use Controllers\IrController;

Router::get('/',                       [AuthController::class, 'login']);
Router::get('/login',                  [AuthController::class, 'login']);
Router::post('/login',                 [AuthController::class, 'login']);
Router::get('/logout',                [AuthController::class, 'logout']);

Router::get('/dashboard',              [DashboardController::class, 'index']);

Router::get('/societes',               [SocieteController::class, 'index']);
Router::get('/societes/clear-context', [SocieteController::class, 'clearContext']);
Router::get('/societes/create',        [SocieteController::class, 'create']);
Router::post('/societes/create',       [SocieteController::class, 'create']);
Router::get('/societes/{id}',          [SocieteController::class, 'show']);
Router::get('/societes/{id}/edit',     [SocieteController::class, 'edit']);
Router::post('/societes/{id}/edit',    [SocieteController::class, 'edit']);
Router::get('/societes/{id}/delete',   [SocieteController::class, 'delete']);
Router::get('/societes/{id}/parametres', [SocieteController::class, 'parametres']);
Router::get('/societes/{id}/parametres/{sous_tab}', [SocieteController::class, 'parametres']);
Router::post('/societes/{id}/parametres', [SocieteController::class, 'parametres']);
Router::post('/societes/{id}/parametres/{sous_tab}', [SocieteController::class, 'parametres']);

Router::get('/salaries',               [SalarieController::class, 'index']);
Router::get('/salaries/create',        [SalarieController::class, 'create']);
Router::post('/salaries/create',       [SalarieController::class, 'create']);
Router::get('/salaries/{id}/edit',     [SalarieController::class, 'edit']);
Router::post('/salaries/{id}/edit',    [SalarieController::class, 'edit']);
Router::get('/salaries/{id}/delete',   [SalarieController::class, 'delete']);

Router::get('/paies',                  [PaieController::class, 'index']);
Router::get('/paies/create',           [PaieController::class, 'create']);
Router::post('/paies/create',          [PaieController::class, 'create']);
Router::get('/paies/{id}/calculate',   [PaieController::class, 'calculate']);

Router::get('/bulletins',              [BulletinController::class, 'index']);
Router::get('/bulletins/{id}',         [BulletinController::class, 'show']);
Router::get('/bulletins/{id}/pdf',     [BulletinController::class, 'pdf']);

Router::get('/damancom',               [DamancomController::class, 'index']);
Router::post('/damancom/generate',     [DamancomController::class, 'generate']);
Router::get('/damancom/generate',      [DamancomController::class, 'generate']);

Router::get('/ir',                     [IrController::class, 'index']);
Router::post('/ir/export',             [IrController::class, 'export']);
Router::get('/ir/export',              [IrController::class, 'export']);
