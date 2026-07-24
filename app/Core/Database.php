<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

final class Database
{
    private static ?PDO $connexion = null;

    public static function connexion(): PDO
    {
        if (self::$connexion === null) {
            $hote = Config::get('bdd.hote');
            $port = Config::get('bdd.port');
            $nom = Config::get('bdd.nom');
            $utilisateur = Config::get('bdd.utilisateur');
            $motDePasse = Config::get('bdd.mot_de_passe');

            $dsn = "mysql:host={$hote};port={$port};dbname={$nom};charset=utf8mb4";

            try {
                self::$connexion = new PDO($dsn, $utilisateur, $motDePasse, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $exception) {
                error_log('Connexion base de données impossible : ' . $exception->getMessage());
                throw $exception;
            }
        }

        return self::$connexion;
    }
}
