<?php

declare(strict_types=1);

namespace App\Core;

final class Request
{
    public readonly string $methode;
    public readonly string $chemin;

    public function __construct()
    {
        $this->methode = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

        $cheminBrut = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
        $chemin = rawurldecode($cheminBrut);
        $chemin = rtrim($chemin, '/');

        $this->chemin = $chemin === '' ? '/' : $chemin;
    }

    public function post(string $cle, mixed $defaut = null): mixed
    {
        return $_POST[$cle] ?? $defaut;
    }

    public function get(string $cle, mixed $defaut = null): mixed
    {
        return $_GET[$cle] ?? $defaut;
    }
}
