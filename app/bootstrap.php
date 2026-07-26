<?php

declare(strict_types=1);

spl_autoload_register(function (string $classe): void {
    $prefixe = 'App\\';
    if (!str_starts_with($classe, $prefixe)) {
        return;
    }

    $cheminRelatif = substr($classe, strlen($prefixe));
    $chemin = __DIR__ . '/' . str_replace('\\', '/', $cheminRelatif) . '.php';

    if (is_file($chemin)) {
        require $chemin;
    }
});

use App\Core\Config;

$config = require __DIR__ . '/../config.php';
Config::charger($config);

date_default_timezone_set(Config::get('fuseau_horaire', 'Europe/Paris'));

ini_set('display_errors', Config::get('debug', false) ? '1' : '0');
error_reporting(E_ALL);

function config(string $cle, mixed $defaut = null): mixed
{
    return Config::get($cle, $defaut);
}

/**
 * Échappement pour l'affichage. À utiliser sur TOUTE valeur insérée dans un
 * gabarit, sans exception — la seule exception volontaire est le contenu déjà
 * passé par AssainisseurHtml, qui doit rester du HTML.
 */
function e(?string $valeur): string
{
    return htmlspecialchars($valeur ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Affiche un champ de formulaire du back-office depuis templates/admin/champs/.
 * Regrouper les champs ici évite de répéter la même structure (libellé, aide,
 * message d'erreur) dans chaque formulaire : une correction d'ergonomie faite
 * ici profite à toutes les rubriques d'un coup.
 */
function champ(string $typeDeChamp, array $options = []): void
{
    $options += [
        'nom' => '',
        'libelle' => '',
        'valeur' => '',
        'erreurs' => [],
        'aide' => '',
        'obligatoire' => false,
    ];

    // Noms volontairement peu banals : extract() ci-dessous ne doit pas pouvoir
    // les écraser avec une option portant le même nom.
    $cheminPartielDuChamp = __DIR__ . '/../templates/admin/champs/' . $typeDeChamp . '.php';
    extract($options, EXTR_SKIP);

    require $cheminPartielDuChamp;
}

/**
 * Affiche un morceau de gabarit réutilisable depuis templates/admin/partiels/.
 * Les variables passées ne vivent que le temps de l'appel : impossible qu'une
 * valeur d'un tour de boucle déborde sur le suivant.
 */
function partiel(string $nomDuPartiel, array $options = []): void
{
    $cheminDuPartiel = __DIR__ . '/../templates/admin/partiels/' . $nomDuPartiel . '.php';
    extract($options, EXTR_SKIP);

    require $cheminDuPartiel;
}

if (PHP_SAPI !== 'cli') {
    $estHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $estHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}
