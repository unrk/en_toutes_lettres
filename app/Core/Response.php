<?php

declare(strict_types=1);

namespace App\Core;

final class Response
{
    public static function html(string $vue, array $donnees = [], int $codeStatut = 200, string $gabarit = 'layout'): void
    {
        http_response_code($codeStatut);
        extract($donnees, EXTR_SKIP);

        ob_start();
        require __DIR__ . '/../../templates/' . $vue . '.php';
        $contenu = ob_get_clean();

        require __DIR__ . '/../../templates/' . $gabarit . '.php';
    }

    public static function rediriger(string $url, int $codeStatut = 302): never
    {
        http_response_code($codeStatut);
        header('Location: ' . $url);
        exit;
    }
}
