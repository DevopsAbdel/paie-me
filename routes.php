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
use Controllers\ComptabiliteController;
use Controllers\SourceLegaleController;
use Controllers\CongeController;
use Controllers\ModeleBulletinController;

Router::get('/',                       [AuthController::class, 'login']);
Router::get('/login',                  [AuthController::class, 'login']);
Router::post('/login',                 [AuthController::class, 'login']);
Router::get('/logout',                [AuthController::class, 'logout']);

Router::get('/dashboard',              [DashboardController::class, 'index']);

Router::get('/societes',               [SocieteController::class, 'index']);
Router::get('/societes/clear-context', [SocieteController::class, 'clearContext']);
Router::get('/societes/create',        [SocieteController::class, 'create']);
Router::post('/societes/create',       [SocieteController::class, 'create']);
Router::get('/societes/{id}',           [SocieteController::class, 'show']);
Router::get('/societes/{id}/salaries',  [SocieteController::class, 'salaries']);
Router::get('/societes/{id}/paies',     [SocieteController::class, 'paies']);
Router::get('/societes/{id}/bulletins', [SocieteController::class, 'bulletins']);
Router::get('/societes/{id}/cnss',      [SocieteController::class, 'cnss']);
Router::get('/societes/{id}/ir',        [SocieteController::class, 'ir']);
Router::get('/societes/{id}/edit',     [SocieteController::class, 'edit']);
Router::post('/societes/{id}/edit',    [SocieteController::class, 'edit']);
Router::post('/societes/{id}/delete',   [SocieteController::class, 'delete']);
Router::get('/societes/{id}/parametres', [SocieteController::class, 'parametres']);
Router::get('/societes/{id}/parametres/{sous_tab}', [SocieteController::class, 'parametres']);
Router::post('/societes/{id}/parametres', [SocieteController::class, 'parametres']);
Router::post('/societes/{id}/parametres/{sous_tab}', [SocieteController::class, 'parametres']);

Router::get('/societes/{id}/baremes', [SocieteController::class, 'baremes']);
Router::get('/societes/{id}/baremes/{sous_tab}', [SocieteController::class, 'baremes']);
Router::post('/societes/{id}/baremes', [SocieteController::class, 'baremes']);
Router::post('/societes/{id}/baremes/{sous_tab}', [SocieteController::class, 'baremes']);

Router::get('/societes/{id}/reglages', [SocieteController::class, 'reglages']);
Router::get('/societes/{id}/reglages/{sous_tab}', [SocieteController::class, 'reglages']);
Router::post('/societes/{id}/reglages', [SocieteController::class, 'reglages']);
Router::post('/societes/{id}/reglages/{sous_tab}', [SocieteController::class, 'reglages']);

Router::get('/salaries',               [SalarieController::class, 'index']);
Router::get('/salaries/create',        [SalarieController::class, 'create']);
Router::post('/salaries/create',       [SalarieController::class, 'create']);
Router::get('/salaries/{id}/edit',     [SalarieController::class, 'edit']);
Router::post('/salaries/{id}/edit',    [SalarieController::class, 'edit']);
Router::post('/salaries/{id}/delete',   [SalarieController::class, 'delete']);
Router::get('/salaries/{id}/stc',      [SalarieController::class, 'stc']);
Router::get('/salaries/{id}/stc/pdf',  [SalarieController::class, 'stcPdf']);

Router::get('/paies',                  [PaieController::class, 'index']);
Router::get('/paies/create',           [PaieController::class, 'create']);
Router::post('/paies/create',          [PaieController::class, 'create']);
Router::get('/paies/{id}/calculate',   [PaieController::class, 'calculate']);
Router::get('/paies/{id}/cloturer',    [PaieController::class, 'cloturer']);
Router::get('/paies/{id}/rouvrir',     [PaieController::class, 'rouvrir']);
Router::get('/paies/{id}/supprimer',   [PaieController::class, 'supprimerPeriode']);
Router::get('/paies/{id}/lignes',      [PaieController::class, 'lignes']);
Router::post('/paies/{id}/ajouter-salaries', [PaieController::class, 'ajouterSalaries']);
Router::get('/paies/{id}/journal',     [PaieController::class, 'journal']);
Router::get('/paies/paie/{id}/edit',  [PaieController::class, 'editPaie']);
Router::post('/paies/paie/{id}/edit', [PaieController::class, 'editPaie']);
Router::get('/paies/paie/{id}/supprimer', [PaieController::class, 'supprimerPaie']);

Router::get('/bulletins',              [BulletinController::class, 'index']);
Router::get('/bulletins/{id}',         [BulletinController::class, 'show']);
Router::get('/bulletins/{id}/pdf',     [BulletinController::class, 'pdf']);

Router::get('/damancom',               [DamancomController::class, 'index']);
Router::post('/damancom/generate',     [DamancomController::class, 'generate']);
Router::get('/damancom/generate',      [DamancomController::class, 'generate']);

Router::get('/ir',                     [IrController::class, 'index']);
Router::post('/ir/export',             [IrController::class, 'export']);
Router::get('/ir/export',              [IrController::class, 'export']);

Router::get('/comptabilite',           [ComptabiliteController::class, 'index']);
Router::post('/comptabilite/export',   [ComptabiliteController::class, 'export']);

Router::get('/societes/{id}/sources-legales', [SourceLegaleController::class, 'index']);
Router::post('/societes/{id}/sources-legales', [SourceLegaleController::class, 'index']);

Router::get('/societes/{id}/conges',                  [CongeController::class, 'index']);
Router::get('/societes/{id}/conges/nouveau',            [CongeController::class, 'nouveau']);
Router::post('/societes/{id}/conges/nouveau',           [CongeController::class, 'nouveau']);
Router::get('/societes/{id}/conges/modifier/{id2}',     [CongeController::class, 'modifier']);
Router::post('/societes/{id}/conges/modifier/{id2}',    [CongeController::class, 'modifier']);
Router::get('/societes/{id}/conges/supprimer/{id2}',    [CongeController::class, 'supprimer']);
Router::get('/societes/{id}/conges/solde-initial',      [CongeController::class, 'soldeInitial']);
Router::post('/societes/{id}/conges/solde-initial',     [CongeController::class, 'soldeInitial']);
Router::get('/societes/{id}/conges/attestation',        [CongeController::class, 'attestation']);
Router::get('/societes/{id}/conges/attestation/pdf/{id2}', [CongeController::class, 'pdf']);

Router::get('/societes/{id}/modeles-bulletins', [ModeleBulletinController::class, 'index']);
Router::post('/societes/{id}/modeles-bulletins', [ModeleBulletinController::class, 'store']);
Router::get('/societes/{id}/modeles-bulletins/{id2}/delete', [ModeleBulletinController::class, 'delete']);
Router::get('/societes/{id}/modeles-bulletins/{id2}/assign', [ModeleBulletinController::class, 'assign']);
Router::post('/societes/{id}/modeles-bulletins/{id2}/update', [ModeleBulletinController::class, 'update']);
