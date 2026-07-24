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
