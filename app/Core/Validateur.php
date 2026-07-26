<?php

declare(strict_types=1);

namespace App\Core;

use DateTime;

/**
 * Rassemble la validation des formulaires du back-office.
 *
 * Chaque méthode renvoie la valeur nettoyée et, en cas de problème, enregistre
 * un message en français courant destiné à être lu par un bénévole : il dit ce
 * qui ne va pas ET quoi faire, jamais « champ invalide ».
 *
 * Usage :
 *     $v = new Validateur();
 *     $titre = $v->texte('titre', $requete->post('titre'), 'Le titre', min: 3, max: 200);
 *     if ($v->aDesErreurs()) { ... $v->erreurs() ... }
 */
final class Validateur
{
    /** @var array<string, string> */
    private array $erreurs = [];

    public function texte(
        string $cle,
        mixed $valeur,
        string $libelle,
        bool $obligatoire = true,
        int $min = 0,
        int $max = 255,
    ): string {
        $valeur = trim((string) $valeur);

        if ($valeur === '') {
            if ($obligatoire) {
                $this->erreurs[$cle] = "{$libelle} est obligatoire.";
            }
            return '';
        }

        $longueur = mb_strlen($valeur);

        if ($longueur < $min) {
            $this->erreurs[$cle] = "{$libelle} doit contenir au moins {$min} caractères.";
        } elseif ($longueur > $max) {
            $this->erreurs[$cle] = "{$libelle} ne doit pas dépasser {$max} caractères "
                . "(vous en avez saisi {$longueur}).";
        }

        return $valeur;
    }

    /**
     * Texte enrichi venant de l'éditeur : systématiquement assaini avant d'être
     * rendu au contrôleur, pour qu'aucun HTML non validé ne puisse atteindre la
     * base de données.
     */
    public function html(string $cle, mixed $valeur, string $libelle, bool $obligatoire = true): string
    {
        $nettoye = AssainisseurHtml::nettoyer((string) $valeur);

        if ($obligatoire && trim(strip_tags($nettoye)) === '') {
            $this->erreurs[$cle] = "{$libelle} ne peut pas être vide.";
        }

        return $nettoye;
    }

    /**
     * @param array<int, string> $valeursAutorisees
     */
    public function choix(
        string $cle,
        mixed $valeur,
        string $libelle,
        array $valeursAutorisees,
        string $defaut,
    ): string {
        $valeur = (string) $valeur;

        if (!in_array($valeur, $valeursAutorisees, true)) {
            $this->erreurs[$cle] = "Choisissez une option valide pour {$libelle}.";
            return $defaut;
        }

        return $valeur;
    }

    /**
     * Attend le format des champs « datetime-local » du navigateur.
     * Renvoie une date au format MySQL, ou null si absente/invalide.
     */
    public function dateHeure(
        string $cle,
        mixed $valeur,
        string $libelle,
        bool $obligatoire = false,
        bool $doitEtreFuture = false,
    ): ?string {
        $valeur = trim((string) $valeur);

        if ($valeur === '') {
            if ($obligatoire) {
                $this->erreurs[$cle] = "Indiquez {$libelle}.";
            }
            return null;
        }

        $date = DateTime::createFromFormat('Y-m-d\TH:i', $valeur)
            ?: DateTime::createFromFormat('Y-m-d\TH:i:s', $valeur);

        if ($date === false) {
            $this->erreurs[$cle] = "La date saisie pour {$libelle} n'est pas valide.";
            return null;
        }

        if ($doitEtreFuture && $date <= new DateTime()) {
            $this->erreurs[$cle] = "{$libelle} doit être dans le futur.";
            return null;
        }

        return $date->format('Y-m-d H:i:s');
    }

    public function url(string $cle, mixed $valeur, string $libelle, bool $obligatoire = false): ?string
    {
        $valeur = trim((string) $valeur);

        if ($valeur === '') {
            if ($obligatoire) {
                $this->erreurs[$cle] = "{$libelle} est obligatoire.";
            }
            return null;
        }

        if (!str_starts_with($valeur, 'http://') && !str_starts_with($valeur, 'https://')) {
            $valeur = 'https://' . $valeur;
        }

        if (filter_var($valeur, FILTER_VALIDATE_URL) === false) {
            $this->erreurs[$cle] = "{$libelle} n'est pas une adresse web valide "
                . "(exemple attendu : https://www.exemple.fr).";
            return null;
        }

        return $valeur;
    }

    public function email(string $cle, mixed $valeur, string $libelle, bool $obligatoire = true): string
    {
        $valeur = trim((string) $valeur);

        if ($valeur === '') {
            if ($obligatoire) {
                $this->erreurs[$cle] = "{$libelle} est obligatoire.";
            }
            return '';
        }

        if (filter_var($valeur, FILTER_VALIDATE_EMAIL) === false) {
            $this->erreurs[$cle] = "{$libelle} n'est pas une adresse e-mail valide.";
        }

        return $valeur;
    }

    public function ajouterErreur(string $cle, string $message): void
    {
        $this->erreurs[$cle] = $message;
    }

    public function aDesErreurs(): bool
    {
        return $this->erreurs !== [];
    }

    /**
     * @return array<string, string>
     */
    public function erreurs(): array
    {
        return $this->erreurs;
    }
}
