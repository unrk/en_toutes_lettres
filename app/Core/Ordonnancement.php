<?php

declare(strict_types=1);

namespace App\Core;

use InvalidArgumentException;
use Throwable;

/**
 * Réordonne les fiches d'une rubrique (activités, partenaires, photos…).
 *
 * Le classement se fait par boutons « Monter » / « Descendre » plutôt qu'en
 * glisser-déposer : c'est utilisable au doigt sur un téléphone, ça fonctionne
 * au clavier, et ça ne dépend pas de JavaScript.
 *
 * La méthode renumérote toute la liste à chaque déplacement (1, 2, 3…). C'est
 * un peu plus de travail pour la base, mais ça répare au passage les valeurs
 * en double ou à zéro — par exemple juste après l'ajout de la colonne.
 */
final class Ordonnancement
{
    /** Un nom de table ne peut pas être un paramètre de requête préparée. */
    private const TABLES_AUTORISEES = ['activites', 'partenaires', 'galeries', 'galerie_photos'];

    public static function deplacer(string $table, int $id, string $direction, ?string $filtreColonne = null, mixed $filtreValeur = null): void
    {
        if (!in_array($table, self::TABLES_AUTORISEES, true)) {
            throw new InvalidArgumentException("Table non autorisée pour un classement : {$table}");
        }

        if (!in_array($direction, ['monter', 'descendre'], true)) {
            throw new InvalidArgumentException("Direction inconnue : {$direction}");
        }

        if ($filtreColonne !== null && !in_array($filtreColonne, ['galerie_id'], true)) {
            throw new InvalidArgumentException("Filtre non autorisé : {$filtreColonne}");
        }

        $connexion = Database::connexion();

        $sql = "SELECT id FROM {$table}";
        $parametres = [];

        if ($filtreColonne !== null) {
            $sql .= " WHERE {$filtreColonne} = :filtre";
            $parametres['filtre'] = $filtreValeur;
        }

        $sql .= ' ORDER BY ordre ASC, id ASC';

        $requete = $connexion->prepare($sql);
        $requete->execute($parametres);
        $ids = array_map('intval', $requete->fetchAll(\PDO::FETCH_COLUMN));

        $position = array_search($id, $ids, true);
        if ($position === false) {
            return;
        }

        $voisin = $direction === 'monter' ? $position - 1 : $position + 1;
        if ($voisin < 0 || $voisin >= count($ids)) {
            // Déjà tout en haut ou tout en bas : rien à faire.
            return;
        }

        [$ids[$position], $ids[$voisin]] = [$ids[$voisin], $ids[$position]];

        $connexion->beginTransaction();

        try {
            $miseAJour = $connexion->prepare("UPDATE {$table} SET ordre = :ordre WHERE id = :id");

            foreach ($ids as $rang => $idFiche) {
                $miseAJour->execute(['ordre' => $rang + 1, 'id' => $idFiche]);
            }

            $connexion->commit();
        } catch (Throwable $exception) {
            $connexion->rollBack();
            throw $exception;
        }
    }
}
