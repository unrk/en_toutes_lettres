<?php

declare(strict_types=1);

namespace App\Core;

use InvalidArgumentException;

/**
 * Fabrique l'adresse web d'une fiche à partir de son titre
 * (« La Cabane » → « la-cabane »).
 *
 * Deux principes :
 *
 * 1. L'adresse est calculée à la création puis FIGÉE. Renommer un titre ne la
 *    change pas : les liens déjà partagés par l'association (courriel, Facebook,
 *    affiches) continuent de fonctionner.
 * 2. Elle n'apparaît jamais dans l'interface. Les bénévoles n'ont pas à savoir
 *    qu'elle existe.
 */
final class Adresse
{
    /**
     * Tables autorisées. Un nom de table ne peut pas passer par une requête
     * préparée : cette liste est donc la garantie qu'aucune valeur extérieure
     * ne se retrouve concaténée dans du SQL.
     */
    private const TABLES_AUTORISEES = [
        'actualites',
        'activites',
        'evenements',
        'pages',
        'galeries',
    ];

    /**
     * Translittération explicite plutôt que iconv('ASCII//TRANSLIT') : ce
     * dernier dépend de la locale du système et ne donne pas le même résultat
     * sur le poste de développement (Windows) et sur le serveur (Linux).
     */
    private const REMPLACEMENTS = [
        'à' => 'a', 'á' => 'a', 'â' => 'a', 'ä' => 'a', 'ã' => 'a', 'å' => 'a',
        'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
        'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
        'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'ö' => 'o', 'õ' => 'o',
        'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
        'ý' => 'y', 'ÿ' => 'y',
        'ñ' => 'n', 'ç' => 'c',
        'œ' => 'oe', 'æ' => 'ae',
        'ß' => 'ss',
    ];

    public static function depuis(string $titre): string
    {
        $adresse = mb_strtolower(trim($titre), 'UTF-8');
        $adresse = strtr($adresse, self::REMPLACEMENTS);

        // Tout ce qui n'est ni lettre ASCII ni chiffre devient un tiret.
        $adresse = preg_replace('/[^a-z0-9]+/', '-', $adresse) ?? '';
        $adresse = trim($adresse, '-');

        if (mb_strlen($adresse) > 180) {
            $adresse = rtrim(mb_substr($adresse, 0, 180), '-');
        }

        // Titre entièrement composé de caractères non translittérables
        // (par exemple un titre en alphabet non latin) : on retombe sur une
        // valeur neutre, l'unicité étant assurée par unique() juste après.
        return $adresse !== '' ? $adresse : 'fiche';
    }

    /**
     * Renvoie une adresse libre pour cette table, en ajoutant -2, -3… si besoin.
     */
    public static function unique(string $titre, string $table, ?int $idAExclure = null): string
    {
        if (!in_array($table, self::TABLES_AUTORISEES, true)) {
            throw new InvalidArgumentException("Table non autorisée pour une adresse : {$table}");
        }

        $base = self::depuis($titre);
        $candidate = $base;
        $suffixe = 1;

        while (self::existe($candidate, $table, $idAExclure)) {
            $suffixe++;
            $candidate = $base . '-' . $suffixe;
        }

        return $candidate;
    }

    private static function existe(string $adresse, string $table, ?int $idAExclure): bool
    {
        $sql = "SELECT 1 FROM {$table} WHERE adresse = :adresse";
        $parametres = ['adresse' => $adresse];

        if ($idAExclure !== null) {
            $sql .= ' AND id <> :id';
            $parametres['id'] = $idAExclure;
        }

        $requete = Database::connexion()->prepare($sql . ' LIMIT 1');
        $requete->execute($parametres);

        return $requete->fetchColumn() !== false;
    }
}
