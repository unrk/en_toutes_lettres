<?php

declare(strict_types=1);

namespace App\Core;

final class Auth
{
    public static function connecte(array $utilisateur): void
    {
        session_regenerate_id(true);

        $_SESSION['utilisateur'] = [
            'id' => $utilisateur['id'],
            'nom' => $utilisateur['nom'],
            'role' => $utilisateur['role'],
        ];
    }

    public static function deconnecte(): void
    {
        $_SESSION = [];
        session_regenerate_id(true);
    }

    public static function estConnecte(): bool
    {
        return isset($_SESSION['utilisateur']);
    }

    public static function utilisateur(): ?array
    {
        return $_SESSION['utilisateur'] ?? null;
    }

    public static function role(): ?string
    {
        return self::utilisateur()['role'] ?? null;
    }

    public static function estAdministrateur(): bool
    {
        return self::role() === 'administrateur';
    }
}
