<?php

declare(strict_types=1);

$configParDefaut = [
    'debug' => false,
    'fuseau_horaire' => 'Europe/Paris',
    'nom_application' => 'En Toutes Lettres',
    'url_base' => '',
    'liens_helloasso' => [
        'adhesion' => '',
        'don' => '',
        'billetterie' => '',
    ],
    'liens_reseaux_sociaux' => [
        'facebook' => '',
        'instagram' => '',
    ],
    'bdd' => [
        'hote' => '127.0.0.1',
        'port' => 3306,
        'nom' => '',
        'utilisateur' => '',
        'mot_de_passe' => '',
    ],
    'taches_planifiees' => [
        'jeton' => '',
    ],
];

$cheminConfigLocale = __DIR__ . '/config.local.php';

if (!is_file($cheminConfigLocale)) {
    http_response_code(500);
    die(
        "Configuration manquante : copiez config.local.php.dist vers config.local.php " .
        "et renseignez-y vos identifiants de base de données avant de continuer."
    );
}

$configLocale = require $cheminConfigLocale;

return array_replace_recursive($configParDefaut, $configLocale);
