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
 * Adresse d'une image d'illustration « de substitution », affichée quand aucune
 * photo n'a été mise en ligne pour un contenu. Les photos proviennent du service
 * gratuit Lorem Picsum (picsum.photos).
 *
 * Le « germe » sert à choisir la photo : un même germe redonne toujours la même
 * image. Ainsi une activité garde la même illustration d'une page à l'autre au
 * lieu d'en changer à chaque visite, et deux contenus différents (germes
 * différents) reçoivent deux photos différentes. Passez donc un texte propre à
 * chaque contenu (son adresse ou son titre).
 */
function image_substitution(string $germe, int $largeur = 800, int $hauteur = 600): string
{
    $germe = $germe !== '' ? $germe : 'en-toutes-lettres';

    return 'https://picsum.photos/seed/' . rawurlencode($germe) . '/' . $largeur . '/' . $hauteur;
}

/**
 * Affiche un champ de formulaire du back-office depuis templates/admin/champs/.
 * Regrouper les champs ici évite de répéter la même structure (libellé, aide,
 * message d'erreur) dans chaque formulaire : une correction d'ergonomie faite
 * ici profite à toutes les rubriques d'un coup.
 */
function champ(string $typeDeChamp, array $reglagesDuChamp = []): void
{
    $reglagesDuChamp += [
        'nom' => '',
        'libelle' => '',
        'valeur' => '',
        'erreurs' => [],
        'aide' => '',
        'obligatoire' => false,
    ];

    // Les variables locales portent des noms volontairement inhabituels : en
    // mode EXTR_SKIP, extract() refuse d'écraser une variable existante, donc
    // un réglage qui porterait le même nom qu'une variable d'ici serait
    // silencieusement ignoré. C'est exactement ce qui est arrivé au réglage
    // « options » du champ de type « choix », quand ce paramètre s'appelait
    // encore $options : le partiel recevait le tableau de réglages complet à la
    // place de la liste des choix.
    $cheminPartielDuChamp = __DIR__ . '/../templates/admin/champs/' . $typeDeChamp . '.php';
    extract($reglagesDuChamp, EXTR_SKIP);

    require $cheminPartielDuChamp;
}

/**
 * Affiche un morceau de gabarit réutilisable depuis templates/admin/partiels/.
 * Les variables passées ne vivent que le temps de l'appel : impossible qu'une
 * valeur d'un tour de boucle déborde sur le suivant.
 */
function partiel(string $nomDuPartiel, array $reglagesDuPartiel = []): void
{
    // Même précaution que dans champ() ci-dessus sur le nom des variables locales.
    $cheminDuPartiel = __DIR__ . '/../templates/admin/partiels/' . $nomDuPartiel . '.php';
    extract($reglagesDuPartiel, EXTR_SKIP);

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
