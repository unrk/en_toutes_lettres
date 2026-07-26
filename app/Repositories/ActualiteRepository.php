<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class ActualiteRepository
{
    public static function tous(): array
    {
        $requete = Database::connexion()->query(
            'SELECT a.id, a.titre, a.type, a.statut, a.publie_le, a.image_chemin, a.cree_le,
                    u.nom AS auteur_nom
             FROM actualites a
             INNER JOIN utilisateurs u ON u.id = a.auteur_id
             ORDER BY a.cree_le DESC'
        );

        return $requete->fetchAll();
    }

    public static function trouve(int $id): ?array
    {
        $requete = Database::connexion()->prepare(
            'SELECT id, titre, type, adresse, contenu, image_chemin, image_alt,
                    statut, publie_le, auteur_id
             FROM actualites
             WHERE id = :id
             LIMIT 1'
        );
        $requete->execute(['id' => $id]);

        $actualite = $requete->fetch();

        return $actualite === false ? null : $actualite;
    }

    /**
     * @param array<string, mixed> $donnees
     */
    public static function creer(array $donnees): int
    {
        $requete = Database::connexion()->prepare(
            'INSERT INTO actualites
                (titre, type, adresse, contenu, image_chemin, image_alt, statut, publie_le, auteur_id)
             VALUES
                (:titre, :type, :adresse, :contenu, :image_chemin, :image_alt, :statut, :publie_le, :auteur_id)'
        );
        $requete->execute([
            'titre' => $donnees['titre'],
            'type' => $donnees['type'],
            'adresse' => $donnees['adresse'],
            'contenu' => $donnees['contenu'],
            'image_chemin' => $donnees['image_chemin'],
            'image_alt' => $donnees['image_alt'],
            'statut' => $donnees['statut'],
            'publie_le' => $donnees['publie_le'],
            'auteur_id' => $donnees['auteur_id'],
        ]);

        return (int) Database::connexion()->lastInsertId();
    }

    /**
     * L'adresse n'est jamais modifiée : elle est figée à la création pour ne
     * pas casser les liens déjà partagés.
     *
     * @param array<string, mixed> $donnees
     */
    public static function modifier(int $id, array $donnees): void
    {
        $requete = Database::connexion()->prepare(
            'UPDATE actualites
             SET titre = :titre,
                 type = :type,
                 contenu = :contenu,
                 image_chemin = :image_chemin,
                 image_alt = :image_alt,
                 statut = :statut,
                 publie_le = :publie_le
             WHERE id = :id'
        );
        $requete->execute([
            'titre' => $donnees['titre'],
            'type' => $donnees['type'],
            'contenu' => $donnees['contenu'],
            'image_chemin' => $donnees['image_chemin'],
            'image_alt' => $donnees['image_alt'],
            'statut' => $donnees['statut'],
            'publie_le' => $donnees['publie_le'],
            'id' => $id,
        ]);
    }

    public static function supprimer(int $id): void
    {
        $requete = Database::connexion()->prepare('DELETE FROM actualites WHERE id = :id');
        $requete->execute(['id' => $id]);
    }
}
