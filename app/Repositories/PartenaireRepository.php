<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class PartenaireRepository
{
    public static function publies(): array
    {
        return Database::connexion()->query(
            'SELECT id, nom, lien_url, logo_chemin, logo_alt, ordre
             FROM partenaires
             WHERE statut = "publie"
             ORDER BY ordre ASC, id ASC'
        )->fetchAll();
    }

    public static function tous(): array
    {
        return Database::connexion()->query(
            'SELECT id, nom, lien_url, logo_chemin, logo_alt, ordre, statut
             FROM partenaires
             ORDER BY ordre ASC, id ASC'
        )->fetchAll();
    }

    public static function trouve(int $id): ?array
    {
        $requete = Database::connexion()->prepare(
            'SELECT id, nom, lien_url, logo_chemin, logo_alt, ordre, statut
             FROM partenaires
             WHERE id = :id
             LIMIT 1'
        );
        $requete->execute(['id' => $id]);

        $partenaire = $requete->fetch();

        return $partenaire === false ? null : $partenaire;
    }

    /**
     * @param array<string, mixed> $donnees
     */
    public static function creer(array $donnees): int
    {
        $connexion = Database::connexion();

        $rangSuivant = (int) $connexion->query('SELECT COALESCE(MAX(ordre), 0) + 1 FROM partenaires')->fetchColumn();

        $requete = $connexion->prepare(
            'INSERT INTO partenaires (nom, lien_url, logo_chemin, logo_alt, ordre, statut)
             VALUES (:nom, :lien_url, :logo_chemin, :logo_alt, :ordre, :statut)'
        );
        $requete->execute([
            'nom' => $donnees['nom'],
            'lien_url' => $donnees['lien_url'],
            'logo_chemin' => $donnees['logo_chemin'],
            'logo_alt' => $donnees['logo_alt'],
            'ordre' => $rangSuivant,
            'statut' => $donnees['statut'],
        ]);

        return (int) $connexion->lastInsertId();
    }

    /**
     * @param array<string, mixed> $donnees
     */
    public static function modifier(int $id, array $donnees): void
    {
        $requete = Database::connexion()->prepare(
            'UPDATE partenaires
             SET nom = :nom,
                 lien_url = :lien_url,
                 logo_chemin = :logo_chemin,
                 logo_alt = :logo_alt,
                 statut = :statut
             WHERE id = :id'
        );
        $requete->execute([
            'nom' => $donnees['nom'],
            'lien_url' => $donnees['lien_url'],
            'logo_chemin' => $donnees['logo_chemin'],
            'logo_alt' => $donnees['logo_alt'],
            'statut' => $donnees['statut'],
            'id' => $id,
        ]);
    }

    public static function supprimer(int $id): void
    {
        $requete = Database::connexion()->prepare('DELETE FROM partenaires WHERE id = :id');
        $requete->execute(['id' => $id]);
    }
}
