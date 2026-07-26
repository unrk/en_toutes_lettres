<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class PageRepository
{
    public static function toutes(): array
    {
        return Database::connexion()->query(
            'SELECT id, titre, adresse, statut, verrouillee, modifie_le
             FROM pages
             ORDER BY verrouillee ASC, titre ASC'
        )->fetchAll();
    }

    public static function trouve(int $id): ?array
    {
        $requete = Database::connexion()->prepare(
            'SELECT id, titre, adresse, contenu, statut, verrouillee
             FROM pages
             WHERE id = :id
             LIMIT 1'
        );
        $requete->execute(['id' => $id]);

        $page = $requete->fetch();

        return $page === false ? null : $page;
    }

    /**
     * @param array<string, mixed> $donnees
     */
    public static function creer(array $donnees): int
    {
        $requete = Database::connexion()->prepare(
            'INSERT INTO pages (titre, adresse, contenu, statut, verrouillee)
             VALUES (:titre, :adresse, :contenu, :statut, 0)'
        );
        $requete->execute([
            'titre' => $donnees['titre'],
            'adresse' => $donnees['adresse'],
            'contenu' => $donnees['contenu'],
            'statut' => $donnees['statut'],
        ]);

        return (int) Database::connexion()->lastInsertId();
    }

    /**
     * « verrouillee » n'est jamais modifiable depuis l'interface : une page
     * légale ne doit pas pouvoir être déverrouillée puis supprimée en deux
     * clics. Ce réglage se fait en base, volontairement.
     *
     * @param array<string, mixed> $donnees
     */
    public static function modifier(int $id, array $donnees): void
    {
        $requete = Database::connexion()->prepare(
            'UPDATE pages
             SET titre = :titre,
                 contenu = :contenu,
                 statut = :statut
             WHERE id = :id'
        );
        $requete->execute([
            'titre' => $donnees['titre'],
            'contenu' => $donnees['contenu'],
            'statut' => $donnees['statut'],
            'id' => $id,
        ]);
    }

    /**
     * La clause « verrouillee = 0 » est la vraie protection : même une requête
     * forgée à la main ne peut pas supprimer une page légale.
     */
    public static function supprimer(int $id): void
    {
        $requete = Database::connexion()->prepare('DELETE FROM pages WHERE id = :id AND verrouillee = 0');
        $requete->execute(['id' => $id]);
    }
}
