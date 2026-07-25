<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class UtilisateurRepository
{
    public static function parEmail(string $email): ?array
    {
        $requete = Database::connexion()->prepare(
            'SELECT id, nom, email, mot_de_passe_hash, role, actif
             FROM utilisateurs
             WHERE email = :email
             LIMIT 1'
        );
        $requete->execute(['email' => $email]);

        $utilisateur = $requete->fetch();

        return $utilisateur === false ? null : $utilisateur;
    }
}
