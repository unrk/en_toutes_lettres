<?php

declare(strict_types=1);

namespace App\Core;

use finfo;
use InvalidArgumentException;

final class TeleversementImage
{
    private const TAILLE_MAX_OCTETS = 5 * 1024 * 1024; // 5 Mo
    private const LARGEUR_MAX = 1600;

    private const TYPES_AUTORISES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    /**
     * @param array{name?: string, type?: string, tmp_name?: string, error?: int, size?: int} $fichier
     * @return array{chemin: string}
     */
    public static function traiter(array $fichier, string $sousDossier): array
    {
        if (!extension_loaded('gd')) {
            throw new InvalidArgumentException(
                "Le traitement des images n'est pas disponible sur ce serveur (extension gd manquante)."
            );
        }

        $erreur = $fichier['error'] ?? UPLOAD_ERR_NO_FILE;

        if ($erreur === UPLOAD_ERR_NO_FILE) {
            throw new InvalidArgumentException('Aucun fichier reçu.');
        }

        if ($erreur !== UPLOAD_ERR_OK) {
            throw new InvalidArgumentException("L'envoi du fichier a échoué, réessayez.");
        }

        if (!is_uploaded_file($fichier['tmp_name'])) {
            throw new InvalidArgumentException('Fichier invalide.');
        }

        if ($fichier['size'] > self::TAILLE_MAX_OCTETS) {
            throw new InvalidArgumentException('Image trop lourde : 5 Mo maximum.');
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $typeMime = $finfo->file($fichier['tmp_name']);

        if (!isset(self::TYPES_AUTORISES[$typeMime])) {
            throw new InvalidArgumentException(
                'Le fichier doit être une image au format JPEG, PNG ou WebP.'
            );
        }

        $extension = self::TYPES_AUTORISES[$typeMime];

        $image = match ($typeMime) {
            'image/jpeg' => imagecreatefromjpeg($fichier['tmp_name']),
            'image/png' => imagecreatefrompng($fichier['tmp_name']),
            'image/webp' => imagecreatefromwebp($fichier['tmp_name']),
        };

        if ($image === false) {
            throw new InvalidArgumentException("Le fichier envoyé n'est pas une image valide.");
        }

        $largeur = imagesx($image);
        $hauteur = imagesy($image);

        if ($largeur > self::LARGEUR_MAX) {
            $nouvelleHauteur = (int) round($hauteur * (self::LARGEUR_MAX / $largeur));
            $imageRedimensionnee = imagecreatetruecolor(self::LARGEUR_MAX, $nouvelleHauteur);

            if ($typeMime === 'image/png') {
                imagealphablending($imageRedimensionnee, false);
                imagesavealpha($imageRedimensionnee, true);
            }

            imagecopyresampled(
                $imageRedimensionnee,
                $image,
                0,
                0,
                0,
                0,
                self::LARGEUR_MAX,
                $nouvelleHauteur,
                $largeur,
                $hauteur
            );

            imagedestroy($image);
            $image = $imageRedimensionnee;
        }

        $dossierRelatif = 'uploads/' . trim($sousDossier, '/') . '/' . date('Y') . '/' . date('m');
        $dossierAbsolu = __DIR__ . '/../../' . $dossierRelatif;

        if (!is_dir($dossierAbsolu) && !mkdir($dossierAbsolu, 0755, true) && !is_dir($dossierAbsolu)) {
            throw new InvalidArgumentException("Impossible d'enregistrer l'image sur le serveur.");
        }

        $nomFichier = bin2hex(random_bytes(16)) . '.' . $extension;
        $cheminAbsolu = $dossierAbsolu . '/' . $nomFichier;

        $reussite = match ($typeMime) {
            'image/jpeg' => imagejpeg($image, $cheminAbsolu, 82),
            'image/png' => imagepng($image, $cheminAbsolu, 6),
            'image/webp' => imagewebp($image, $cheminAbsolu, 82),
        };

        imagedestroy($image);

        if (!$reussite) {
            throw new InvalidArgumentException("Impossible d'enregistrer l'image sur le serveur.");
        }

        return [
            'chemin' => $dossierRelatif . '/' . $nomFichier,
        ];
    }

    public static function supprimer(?string $cheminRelatif): void
    {
        if ($cheminRelatif === null || $cheminRelatif === '') {
            return;
        }

        $cheminAbsolu = __DIR__ . '/../../' . $cheminRelatif;
        if (is_file($cheminAbsolu)) {
            unlink($cheminAbsolu);
        }
    }
}
