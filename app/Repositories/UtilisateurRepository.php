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

    public static function trouve(int $id): ?array
    {
        $requete = Database::connexion()->prepare(
            'SELECT id, nom, email, mot_de_passe_hash, role, actif, cree_le
             FROM utilisateurs
             WHERE id = :id
             LIMIT 1'
        );
        $requete->execute(['id' => $id]);

        $utilisateur = $requete->fetch();

        return $utilisateur === false ? null : $utilisateur;
    }

    public static function tous(): array
    {
        return Database::connexion()->query(
            'SELECT id, nom, email, role, actif, cree_le
             FROM utilisateurs
             ORDER BY actif DESC, nom ASC'
        )->fetchAll();
    }

    public static function emailDejaPris(string $email, ?int $idAExclure = null): bool
    {
        $sql = 'SELECT 1 FROM utilisateurs WHERE email = :email';
        $parametres = ['email' => $email];

        if ($idAExclure !== null) {
            $sql .= ' AND id <> :id';
            $parametres['id'] = $idAExclure;
        }

        $requete = Database::connexion()->prepare($sql . ' LIMIT 1');
        $requete->execute($parametres);

        return $requete->fetchColumn() !== false;
    }

    public static function creer(string $nom, string $email, string $motDePasse, string $role): int
    {
        $requete = Database::connexion()->prepare(
            'INSERT INTO utilisateurs (nom, email, mot_de_passe_hash, role, actif)
             VALUES (:nom, :email, :mot_de_passe_hash, :role, 1)'
        );
        $requete->execute([
            'nom' => $nom,
            'email' => $email,
            'mot_de_passe_hash' => password_hash($motDePasse, PASSWORD_DEFAULT),
            'role' => $role,
        ]);

        return (int) Database::connexion()->lastInsertId();
    }

    public static function modifier(int $id, string $nom, string $email, string $role): void
    {
        $requete = Database::connexion()->prepare(
            'UPDATE utilisateurs SET nom = :nom, email = :email, role = :role WHERE id = :id'
        );
        $requete->execute(['nom' => $nom, 'email' => $email, 'role' => $role, 'id' => $id]);
    }

    public static function changerMotDePasse(int $id, string $motDePasse): void
    {
        $requete = Database::connexion()->prepare(
            'UPDATE utilisateurs SET mot_de_passe_hash = :hash WHERE id = :id'
        );
        $requete->execute([
            'hash' => password_hash($motDePasse, PASSWORD_DEFAULT),
            'id' => $id,
        ]);
    }

    /**
     * On désactive au lieu de supprimer : un compte a signé des actualités, et
     * effacer la ligne casserait le lien vers son auteur. Un compte désactivé
     * ne peut plus se connecter, ce qui est le seul effet recherché.
     */
    public static function changerActivation(int $id, bool $actif): void
    {
        $requete = Database::connexion()->prepare('UPDATE utilisateurs SET actif = :actif WHERE id = :id');
        $requete->execute(['actif' => $actif ? 1 : 0, 'id' => $id]);
    }

    /**
     * Sert à empêcher la suppression du dernier accès administrateur au site.
     */
    public static function nombreAdministrateursActifs(): int
    {
        return (int) Database::connexion()->query(
            "SELECT COUNT(*) FROM utilisateurs WHERE role = 'administrateur' AND actif = 1"
        )->fetchColumn();
    }
}
