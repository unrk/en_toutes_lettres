<?php

declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';

use App\Controllers\HomeController;
use App\Core\NotFoundException;
use App\Core\Request;
use App\Core\Response;
use App\Core\Router;

$routeur = new Router();
$routeur->get('/', [HomeController::class, 'accueil']);

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
