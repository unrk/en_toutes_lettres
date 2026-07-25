<?php

declare(strict_types=1);

namespace App\Core;

final class Csrf
{
    public static function jeton(): string
    {
        if (empty($_SESSION['jeton_csrf'])) {
            $_SESSION['jeton_csrf'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['jeton_csrf'];
    }

    public static function champ(): string
    {
        return '<input type="hidden" name="jeton_csrf" value="'
            . htmlspecialchars(self::jeton(), ENT_QUOTES, 'UTF-8')
            . '">';
    }

    public static function valide(?string $jeton): bool
    {
        return is_string($jeton)
            && isset($_SESSION['jeton_csrf'])
            && hash_equals($_SESSION['jeton_csrf'], $jeton);
    }
}
