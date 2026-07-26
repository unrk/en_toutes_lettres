<?php

declare(strict_types=1);

namespace App\Core;

use InvalidArgumentException;

/**
 * Traite le trio « fichier envoyé / case retirer / texte alternatif » présent
 * dans presque tous les formulaires du back-office.
 *
 * Sans cette classe, la même trentaine de lignes serait recopiée dans chaque
 * contrôleur — et la règle « pas d'image sans texte alternatif » finirait par
 * être oubliée dans l'un d'eux.
 */
final class ChampImage
{
    /**
     * @return array{chemin: ?string, alt: ?string, ancienne_a_supprimer: ?string}
     */
    public static function traiter(
        string $nom,
        ?string $cheminActuel,
        ?string $altActuel,
        string $sousDossier,
        Validateur $validateur,
        Request $requete,
        bool $obligatoire = false,
        string $libelle = 'Une image',
    ): array {
        $nomAlt = $nom . '_alt';
        $alt = trim((string) $requete->post($nomAlt, ''));

        $chemin = $cheminActuel;
        $ancienneASupprimer = null;

        $fichier = $_FILES[$nom] ?? null;
        $nouvelleImage = $fichier !== null
            && ($fichier['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
        $retraitDemande = $requete->post('supprimer_' . $nom) === '1';

        if ($nouvelleImage) {
            try {
                $resultat = TeleversementImage::traiter($fichier, $sousDossier);
            } catch (InvalidArgumentException $exception) {
                $validateur->ajouterErreur($nom, $exception->getMessage());
                return [
                    'chemin' => $cheminActuel,
                    'alt' => $altActuel,
                    'ancienne_a_supprimer' => null,
                ];
            }

            $ancienneASupprimer = $cheminActuel;
            $chemin = $resultat['chemin'];
        } elseif ($retraitDemande) {
            $ancienneASupprimer = $cheminActuel;
            $chemin = null;
        }

        if ($chemin === null) {
            if ($obligatoire) {
                $validateur->ajouterErreur($nom, "{$libelle} est obligatoire : choisissez un fichier à envoyer.");
            }

            return ['chemin' => null, 'alt' => null, 'ancienne_a_supprimer' => $ancienneASupprimer];
        }

        if ($alt === '') {
            $validateur->ajouterErreur(
                $nomAlt,
                "Décrivez l'image en quelques mots : cette description est lue "
                . "à voix haute aux personnes qui ne peuvent pas la voir."
            );
        }

        return ['chemin' => $chemin, 'alt' => $alt, 'ancienne_a_supprimer' => $ancienneASupprimer];
    }
}
