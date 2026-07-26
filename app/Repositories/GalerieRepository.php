<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class GalerieRepository
{
    public static function toutes(): array
    {
        return Database::connexion()->query(
            'SELECT g.id, g.titre, g.ordre, g.statut,
                    COUNT(p.id) AS nombre_photos,
                    MIN(p.chemin) AS premiere_photo
             FROM galeries g
             LEFT JOIN galerie_photos p ON p.galerie_id = g.id
             GROUP BY g.id, g.titre, g.ordre, g.statut
             ORDER BY g.ordre ASC, g.id ASC'
        )->fetchAll();
    }

    public static function trouve(int $id): ?array
    {
        $requete = Database::connexion()->prepare(
            'SELECT id, titre, adresse, description, ordre, statut
             FROM galeries
             WHERE id = :id
             LIMIT 1'
        );
        $requete->execute(['id' => $id]);

        $galerie = $requete->fetch();

        return $galerie === false ? null : $galerie;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function photos(int $galerieId): array
    {
        $requete = Database::connexion()->prepare(
            'SELECT id, galerie_id, chemin, alt, ordre
             FROM galerie_photos
             WHERE galerie_id = :galerie_id
             ORDER BY ordre ASC, id ASC'
        );
        $requete->execute(['galerie_id' => $galerieId]);

        return $requete->fetchAll();
    }

    public static function photo(int $photoId): ?array
    {
        $requete = Database::connexion()->prepare(
            'SELECT id, galerie_id, chemin, alt, ordre FROM galerie_photos WHERE id = :id LIMIT 1'
        );
        $requete->execute(['id' => $photoId]);

        $photo = $requete->fetch();

        return $photo === false ? null : $photo;
    }

    /**
     * @param array<string, mixed> $donnees
     */
    public static function creer(array $donnees): int
    {
        $connexion = Database::connexion();

        $rangSuivant = (int) $connexion->query('SELECT COALESCE(MAX(ordre), 0) + 1 FROM galeries')->fetchColumn();

        $requete = $connexion->prepare(
            'INSERT INTO galeries (titre, adresse, description, ordre, statut)
             VALUES (:titre, :adresse, :description, :ordre, :statut)'
        );
        $requete->execute([
            'titre' => $donnees['titre'],
            'adresse' => $donnees['adresse'],
            'description' => $donnees['description'],
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
            'UPDATE galeries
             SET titre = :titre, description = :description, statut = :statut
             WHERE id = :id'
        );
        $requete->execute([
            'titre' => $donnees['titre'],
            'description' => $donnees['description'],
            'statut' => $donnees['statut'],
            'id' => $id,
        ]);
    }

    public static function supprimer(int $id): void
    {
        $requete = Database::connexion()->prepare('DELETE FROM galeries WHERE id = :id');
        $requete->execute(['id' => $id]);
    }

    public static function ajouterPhoto(int $galerieId, string $chemin, string $alt): int
    {
        $connexion = Database::connexion();

        $rang = $connexion->prepare(
            'SELECT COALESCE(MAX(ordre), 0) + 1 FROM galerie_photos WHERE galerie_id = :galerie_id'
        );
        $rang->execute(['galerie_id' => $galerieId]);
        $rangSuivant = (int) $rang->fetchColumn();

        $requete = $connexion->prepare(
            'INSERT INTO galerie_photos (galerie_id, chemin, alt, ordre)
             VALUES (:galerie_id, :chemin, :alt, :ordre)'
        );
        $requete->execute([
            'galerie_id' => $galerieId,
            'chemin' => $chemin,
            'alt' => $alt,
            'ordre' => $rangSuivant,
        ]);

        return (int) $connexion->lastInsertId();
    }

    public static function modifierTexteAlternatif(int $photoId, string $alt): void
    {
        $requete = Database::connexion()->prepare('UPDATE galerie_photos SET alt = :alt WHERE id = :id');
        $requete->execute(['alt' => $alt, 'id' => $photoId]);
    }

    public static function supprimerPhoto(int $photoId): void
    {
        $requete = Database::connexion()->prepare('DELETE FROM galerie_photos WHERE id = :id');
        $requete->execute(['id' => $photoId]);
    }
}
