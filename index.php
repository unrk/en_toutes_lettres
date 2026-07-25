<?php

declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';

use App\Controllers\Admin\ActualiteController;
use App\Controllers\Admin\AuthController;
use App\Controllers\Admin\TableauDeBordController;
use App\Controllers\HomeController;
use App\Core\NotFoundException;
use App\Core\Request;
use App\Core\Response;
use App\Core\Router;

$routeur = new Router();
$routeur->get('/', [HomeController::class, 'accueil']);

$routeur->get('/admin/connexion', [AuthController::class, 'formulaire']);
$routeur->post('/admin/connexion', [AuthController::class, 'traiter']);
$routeur->post('/admin/deconnexion', [AuthController::class, 'deconnexion']);

$routeur->get('/admin', [TableauDeBordController::class, 'index']);

$routeur->get('/admin/actualites', [ActualiteController::class, 'liste']);
$routeur->get('/admin/actualites/creer', [ActualiteController::class, 'creer']);
$routeur->post('/admin/actualites/creer', [ActualiteController::class, 'enregistrerNouvelle']);
$routeur->get('/admin/actualites/{id}/modifier', [ActualiteController::class, 'modifier']);
$routeur->post('/admin/actualites/{id}/modifier', [ActualiteController::class, 'enregistrerModification']);
$routeur->post('/admin/actualites/{id}/supprimer', [ActualiteController::class, 'supprimer']);
$routeur->get('/admin/actualites/{id}/apercu', [ActualiteController::class, 'apercu']);

try {
    $routeur->distribuer(new Request());
} catch (NotFoundException) {
    Response::html('erreurs/404', ['titre' => 'Page introuvable'], 404);
} catch (\Throwable $exception) {
    error_log($exception->getMessage() . "\n" . $exception->getTraceAsString());

    if (config('debug', false)) {
        throw $exception;
    }

    Response::html('erreurs/500', ['titre' => 'Erreur'], 500);
}
