<?php

declare(strict_types=1);

namespace App\Core;

final class Config
{
    private static array $donnees = [];

    public static function charger(array $donnees): void
    {
        self::$donnees = $donnees;
    }

    public static function get(string $cle, mixed $defaut = null): mixed
    {
        $segments = explode('.', $cle);
        $valeur = self::$donnees;

        foreach ($segments as $segment) {
            if (!is_array($valeur) || !array_key_exists($segment, $valeur)) {
                return $defaut;
            }
            $valeur = $valeur[$segment];
        }

        return $valeur;
    }
}
