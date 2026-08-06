<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class EvenementRepository
{
    public static function publies(): array
    {
        return Database::connexion()->query(
            'SELECT id, titre, adresse, description, debut, fin, lieu, image_chemin, image_alt,
                    (debut >= NOW()) AS a_venir
             FROM evenements
             WHERE statut = "publie"
             ORDER BY (debut >= NOW()) DESC,
                      CASE WHEN debut >= NOW() THEN debut END ASC,
                      CASE WHEN debut <  NOW() THEN debut END DESC'
        )->fetchAll();
    }

    public static function aVenir(int $limite): array
    {
        $requete = Database::connexion()->prepare(
            'SELECT id, titre, adresse, debut, fin, lieu
             FROM evenements
             WHERE statut = "publie"
               AND debut >= NOW()
             ORDER BY debut ASC
             LIMIT :limite'
        );
        $requete->bindValue('limite', $limite, \PDO::PARAM_INT);
        $requete->execute();

        return $requete->fetchAll();
    }

    /**
     * Les événements à venir d'abord (du plus proche au plus lointain), puis
     * les passés du plus récent au plus ancien : c'est l'ordre dans lequel une
     * bénévole les cherche.
     */
    public static function tous(): array
    {
        return Database::connexion()->query(
            'SELECT id, titre, debut, fin, lieu, statut, image_chemin,
                    (debut >= NOW()) AS a_venir
             FROM evenements
             ORDER BY (debut >= NOW()) DESC,
                      CASE WHEN debut >= NOW() THEN debut END ASC,
                      CASE WHEN debut <  NOW() THEN debut END DESC'
        )->fetchAll();
    }

    public static function trouve(int $id): ?array
    {
        $requete = Database::connexion()->prepare(
            'SELECT id, titre, adresse, description, debut, fin, lieu,
                    image_chemin, image_alt, statut
             FROM evenements
             WHERE id = :id
             LIMIT 1'
        );
        $requete->execute(['id' => $id]);

        $evenement = $requete->fetch();

        return $evenement === false ? null : $evenement;
    }

    /**
     * @param array<string, mixed> $donnees
     */
    public static function creer(array $donnees): int
    {
        $requete = Database::connexion()->prepare(
            'INSERT INTO evenements
                (titre, adresse, description, debut, fin, lieu, image_chemin, image_alt, statut)
             VALUES
                (:titre, :adresse, :description, :debut, :fin, :lieu, :image_chemin, :image_alt, :statut)'
        );
        $requete->execute([
            'titre' => $donnees['titre'],
            'adresse' => $donnees['adresse'],
            'description' => $donnees['description'],
            'debut' => $donnees['debut'],
            'fin' => $donnees['fin'],
            'lieu' => $donnees['lieu'],
            'image_chemin' => $donnees['image_chemin'],
            'image_alt' => $donnees['image_alt'],
            'statut' => $donnees['statut'],
        ]);

        return (int) Database::connexion()->lastInsertId();
    }

    /**
     * @param array<string, mixed> $donnees
     */
    public static function modifier(int $id, array $donnees): void
    {
        $requete = Database::connexion()->prepare(
            'UPDATE evenements
             SET titre = :titre,
                 description = :description,
                 debut = :debut,
                 fin = :fin,
                 lieu = :lieu,
                 image_chemin = :image_chemin,
                 image_alt = :image_alt,
                 statut = :statut
             WHERE id = :id'
        );
        $requete->execute([
            'titre' => $donnees['titre'],
            'description' => $donnees['description'],
            'debut' => $donnees['debut'],
            'fin' => $donnees['fin'],
            'lieu' => $donnees['lieu'],
            'image_chemin' => $donnees['image_chemin'],
            'image_alt' => $donnees['image_alt'],
            'statut' => $donnees['statut'],
            'id' => $id,
        ]);
    }

    public static function supprimer(int $id): void
    {
        $requete = Database::connexion()->prepare('DELETE FROM evenements WHERE id = :id');
        $requete->execute(['id' => $id]);
    }
}
