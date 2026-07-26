<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class ActiviteRepository
{
    public static function tous(): array
    {
        return Database::connexion()->query(
            'SELECT id, titre, resume, image_chemin, ordre, statut
             FROM activites
             ORDER BY ordre ASC, id ASC'
        )->fetchAll();
    }

    public static function trouve(int $id): ?array
    {
        $requete = Database::connexion()->prepare(
            'SELECT id, titre, adresse, resume, description, creneaux, lieu, public_vise,
                    tarif, inscriptions, image_chemin, image_alt, ordre, statut
             FROM activites
             WHERE id = :id
             LIMIT 1'
        );
        $requete->execute(['id' => $id]);

        $activite = $requete->fetch();

        return $activite === false ? null : $activite;
    }

    /**
     * @param array<string, mixed> $donnees
     */
    public static function creer(array $donnees): int
    {
        $connexion = Database::connexion();

        $rangSuivant = (int) $connexion->query('SELECT COALESCE(MAX(ordre), 0) + 1 FROM activites')->fetchColumn();

        $requete = $connexion->prepare(
            'INSERT INTO activites
                (titre, adresse, resume, description, creneaux, lieu, public_vise, tarif,
                 inscriptions, image_chemin, image_alt, ordre, statut)
             VALUES
                (:titre, :adresse, :resume, :description, :creneaux, :lieu, :public_vise, :tarif,
                 :inscriptions, :image_chemin, :image_alt, :ordre, :statut)'
        );
        $requete->execute([
            'titre' => $donnees['titre'],
            'adresse' => $donnees['adresse'],
            'resume' => $donnees['resume'],
            'description' => $donnees['description'],
            'creneaux' => $donnees['creneaux'],
            'lieu' => $donnees['lieu'],
            'public_vise' => $donnees['public_vise'],
            'tarif' => $donnees['tarif'],
            'inscriptions' => $donnees['inscriptions'],
            'image_chemin' => $donnees['image_chemin'],
            'image_alt' => $donnees['image_alt'],
            'ordre' => $rangSuivant,
            'statut' => $donnees['statut'],
        ]);

        return (int) $connexion->lastInsertId();
    }

    /**
     * L'adresse est figée à la création : la modifier casserait les liens
     * déjà partagés vers la fiche.
     *
     * @param array<string, mixed> $donnees
     */
    public static function modifier(int $id, array $donnees): void
    {
        $requete = Database::connexion()->prepare(
            'UPDATE activites
             SET titre = :titre,
                 resume = :resume,
                 description = :description,
                 creneaux = :creneaux,
                 lieu = :lieu,
                 public_vise = :public_vise,
                 tarif = :tarif,
                 inscriptions = :inscriptions,
                 image_chemin = :image_chemin,
                 image_alt = :image_alt,
                 statut = :statut
             WHERE id = :id'
        );
        $requete->execute([
            'titre' => $donnees['titre'],
            'resume' => $donnees['resume'],
            'description' => $donnees['description'],
            'creneaux' => $donnees['creneaux'],
            'lieu' => $donnees['lieu'],
            'public_vise' => $donnees['public_vise'],
            'tarif' => $donnees['tarif'],
            'inscriptions' => $donnees['inscriptions'],
            'image_chemin' => $donnees['image_chemin'],
            'image_alt' => $donnees['image_alt'],
            'statut' => $donnees['statut'],
            'id' => $id,
        ]);
    }

    public static function supprimer(int $id): void
    {
        $requete = Database::connexion()->prepare('DELETE FROM activites WHERE id = :id');
        $requete->execute(['id' => $id]);
    }
}
