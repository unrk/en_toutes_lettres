<?php

declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';

use App\Controllers\Admin\ActiviteController;
use App\Controllers\Admin\ActualiteController;
use App\Controllers\Admin\AuthController;
use App\Controllers\Admin\CompteController;
use App\Controllers\Admin\EvenementController;
use App\Controllers\Admin\MonCompteController;
use App\Controllers\Admin\GalerieController;
use App\Controllers\Admin\PageController;
use App\Controllers\Admin\PartenaireController;
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

$routeur->get('/admin/activites', [ActiviteController::class, 'liste']);
$routeur->get('/admin/activites/creer', [ActiviteController::class, 'creer']);
$routeur->post('/admin/activites/creer', [ActiviteController::class, 'enregistrerNouvelle']);
$routeur->get('/admin/activites/{id}/modifier', [ActiviteController::class, 'modifier']);
$routeur->post('/admin/activites/{id}/modifier', [ActiviteController::class, 'enregistrerModification']);
$routeur->post('/admin/activites/{id}/supprimer', [ActiviteController::class, 'supprimer']);
$routeur->post('/admin/activites/{id}/monter', [ActiviteController::class, 'monter']);
$routeur->post('/admin/activites/{id}/descendre', [ActiviteController::class, 'descendre']);
$routeur->get('/admin/activites/{id}/apercu', [ActiviteController::class, 'apercu']);

$routeur->get('/admin/agenda', [EvenementController::class, 'liste']);
$routeur->get('/admin/agenda/creer', [EvenementController::class, 'creer']);
$routeur->post('/admin/agenda/creer', [EvenementController::class, 'enregistrerNouveau']);
$routeur->get('/admin/agenda/{id}/modifier', [EvenementController::class, 'modifier']);
$routeur->post('/admin/agenda/{id}/modifier', [EvenementController::class, 'enregistrerModification']);
$routeur->post('/admin/agenda/{id}/supprimer', [EvenementController::class, 'supprimer']);
$routeur->get('/admin/agenda/{id}/apercu', [EvenementController::class, 'apercu']);

$routeur->get('/admin/partenaires', [PartenaireController::class, 'liste']);
$routeur->get('/admin/partenaires/creer', [PartenaireController::class, 'creer']);
$routeur->post('/admin/partenaires/creer', [PartenaireController::class, 'enregistrerNouveau']);
$routeur->get('/admin/partenaires/{id}/modifier', [PartenaireController::class, 'modifier']);
$routeur->post('/admin/partenaires/{id}/modifier', [PartenaireController::class, 'enregistrerModification']);
$routeur->post('/admin/partenaires/{id}/supprimer', [PartenaireController::class, 'supprimer']);
$routeur->post('/admin/partenaires/{id}/monter', [PartenaireController::class, 'monter']);
$routeur->post('/admin/partenaires/{id}/descendre', [PartenaireController::class, 'descendre']);

$routeur->get('/admin/pages', [PageController::class, 'liste']);
$routeur->get('/admin/pages/creer', [PageController::class, 'creer']);
$routeur->post('/admin/pages/creer', [PageController::class, 'enregistrerNouvelle']);
$routeur->get('/admin/pages/{id}/modifier', [PageController::class, 'modifier']);
$routeur->post('/admin/pages/{id}/modifier', [PageController::class, 'enregistrerModification']);
$routeur->post('/admin/pages/{id}/supprimer', [PageController::class, 'supprimer']);
$routeur->get('/admin/pages/{id}/apercu', [PageController::class, 'apercu']);

$routeur->get('/admin/galeries', [GalerieController::class, 'liste']);
$routeur->get('/admin/galeries/creer', [GalerieController::class, 'creer']);
$routeur->post('/admin/galeries/creer', [GalerieController::class, 'enregistrerNouvelle']);
$routeur->get('/admin/galeries/{id}/modifier', [GalerieController::class, 'modifier']);
$routeur->post('/admin/galeries/{id}/modifier', [GalerieController::class, 'enregistrerModification']);
$routeur->post('/admin/galeries/{id}/supprimer', [GalerieController::class, 'supprimer']);
$routeur->post('/admin/galeries/{id}/monter', [GalerieController::class, 'monter']);
$routeur->post('/admin/galeries/{id}/descendre', [GalerieController::class, 'descendre']);
$routeur->post('/admin/galeries/{id}/photos', [GalerieController::class, 'ajouterPhotos']);
$routeur->post('/admin/galeries/{id}/photos/{photoId}/supprimer', [GalerieController::class, 'supprimerPhoto']);
$routeur->post('/admin/galeries/{id}/photos/{photoId}/monter', [GalerieController::class, 'monterPhoto']);
$routeur->post('/admin/galeries/{id}/photos/{photoId}/descendre', [GalerieController::class, 'descendrePhoto']);

$routeur->get('/admin/mon-mot-de-passe', [MonCompteController::class, 'formulaire']);
$routeur->post('/admin/mon-mot-de-passe', [MonCompteController::class, 'enregistrer']);

$routeur->get('/admin/comptes', [CompteController::class, 'liste']);
$routeur->get('/admin/comptes/creer', [CompteController::class, 'creer']);
$routeur->post('/admin/comptes/creer', [CompteController::class, 'enregistrerNouveau']);
$routeur->get('/admin/comptes/{id}/modifier', [CompteController::class, 'modifier']);
$routeur->post('/admin/comptes/{id}/modifier', [CompteController::class, 'enregistrerModification']);
$routeur->post('/admin/comptes/{id}/desactiver', [CompteController::class, 'desactiver']);
$routeur->post('/admin/comptes/{id}/reactiver', [CompteController::class, 'reactiver']);

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
