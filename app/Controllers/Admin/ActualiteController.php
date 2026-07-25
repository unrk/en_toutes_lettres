<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\AssainisseurHtml;
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\NotFoundException;
use App\Core\Request;
use App\Core\Response;
use App\Core\TeleversementImage;
use App\Repositories\ActualiteRepository;
use DateTime;
use InvalidArgumentException;

final class ActualiteController extends ControleurAdmin
{
    private const STATUTS_VALIDES = ['brouillon', 'publie', 'programme'];

    public function liste(Request $requete): void
    {
        Response::html(
            'admin/actualites/liste',
            ['titre' => 'Actualités', 'actualites' => ActualiteRepository::tous()],
            200,
            'admin/gabarit'
        );
    }

    public function creer(Request $requete): void
    {
        Response::html(
            'admin/actualites/formulaire',
            [
                'titre' => 'Nouvelle actualité',
                'actualite' => null,
                'erreurs' => [],
                'valeurs' => [
                    'titre' => '',
                    'contenu' => '',
                    'statut' => 'brouillon',
                    'publie_le' => '',
                    'image_alt' => '',
                ],
            ],
            200,
            'admin/gabarit'
        );
    }

    public function enregistrerNouvelle(Request $requete): void
    {
        $resultat = $this->validerFormulaire($requete, null);

        if ($resultat['erreurs'] !== []) {
            Response::html(
                'admin/actualites/formulaire',
                [
                    'titre' => 'Nouvelle actualité',
                    'actualite' => null,
                    'erreurs' => $resultat['erreurs'],
                    'valeurs' => $resultat['valeurs'],
                ],
                422,
                'admin/gabarit'
            );
            return;
        }

        ActualiteRepository::creer([
            ...$resultat['donnees'],
            'auteur_id' => Auth::utilisateur()['id'],
        ]);

        Response::rediriger('/admin/actualites');
    }

    public function modifier(Request $requete, string $id): void
    {
        $actualite = ActualiteRepository::trouve((int) $id);

        if ($actualite === null) {
            throw new NotFoundException("Actualité {$id} introuvable.");
        }

        Response::html(
            'admin/actualites/formulaire',
            [
                'titre' => 'Modifier l\'actualité',
                'actualite' => $actualite,
                'erreurs' => [],
                'valeurs' => [
                    'titre' => $actualite['titre'],
                    'contenu' => $actualite['contenu'],
                    'statut' => $actualite['statut'],
                    'publie_le' => $actualite['publie_le'] !== null
                        ? str_replace(' ', 'T', substr($actualite['publie_le'], 0, 16))
                        : '',
                    'image_alt' => $actualite['image_alt'] ?? '',
                ],
            ],
            200,
            'admin/gabarit'
        );
    }

    public function enregistrerModification(Request $requete, string $id): void
    {
        $actualite = ActualiteRepository::trouve((int) $id);

        if ($actualite === null) {
            throw new NotFoundException("Actualité {$id} introuvable.");
        }

        $resultat = $this->validerFormulaire($requete, $actualite);

        if ($resultat['erreurs'] !== []) {
            Response::html(
                'admin/actualites/formulaire',
                [
                    'titre' => 'Modifier l\'actualité',
                    'actualite' => $actualite,
                    'erreurs' => $resultat['erreurs'],
                    'valeurs' => $resultat['valeurs'],
                ],
                422,
                'admin/gabarit'
            );
            return;
        }

        ActualiteRepository::modifier((int) $id, $resultat['donnees']);

        if ($resultat['ancienneImageASupprimer'] !== null) {
            TeleversementImage::supprimer($resultat['ancienneImageASupprimer']);
        }

        Response::rediriger('/admin/actualites');
    }

    public function supprimer(Request $requete, string $id): void
    {
        if (!Csrf::valide($requete->post('jeton_csrf'))) {
            Response::rediriger('/admin/actualites');
            return;
        }

        $actualite = ActualiteRepository::trouve((int) $id);

        if ($actualite !== null) {
            ActualiteRepository::supprimer((int) $id);
            TeleversementImage::supprimer($actualite['image_chemin']);
        }

        Response::rediriger('/admin/actualites');
    }

    public function apercu(Request $requete, string $id): void
    {
        $actualite = ActualiteRepository::trouve((int) $id);

        if ($actualite === null) {
            throw new NotFoundException("Actualité {$id} introuvable.");
        }

        Response::html(
            'admin/actualites/apercu',
            ['titre' => 'Aperçu — ' . $actualite['titre'], 'actualite' => $actualite],
            200,
            'admin/gabarit'
        );
    }

    /**
     * @return array{
     *     erreurs: array<string, string>,
     *     valeurs: array<string, string>,
     *     donnees?: array{titre: string, contenu: string, image_chemin: ?string, image_alt: ?string, statut: string, publie_le: ?string},
     *     ancienneImageASupprimer?: ?string
     * }
     */
    private function validerFormulaire(Request $requete, ?array $actualiteExistante): array
    {
        $valeurs = [
            'titre' => trim((string) $requete->post('titre', '')),
            'contenu' => (string) $requete->post('contenu', ''),
            'statut' => (string) $requete->post('statut', 'brouillon'),
            'publie_le' => trim((string) $requete->post('publie_le', '')),
            'image_alt' => trim((string) $requete->post('image_alt', '')),
        ];

        $erreurs = [];

        if (!Csrf::valide($requete->post('jeton_csrf'))) {
            $erreurs['general'] = "Votre session a expiré, merci de réessayer.";
            return ['erreurs' => $erreurs, 'valeurs' => $valeurs];
        }

        if (mb_strlen($valeurs['titre']) < 3 || mb_strlen($valeurs['titre']) > 200) {
            $erreurs['titre'] = 'Le titre doit contenir entre 3 et 200 caractères.';
        }

        $contenuNettoye = AssainisseurHtml::nettoyer($valeurs['contenu']);
        if (trim(strip_tags($contenuNettoye)) === '') {
            $erreurs['contenu'] = 'Le contenu ne peut pas être vide.';
        }

        if (!in_array($valeurs['statut'], self::STATUTS_VALIDES, true)) {
            $erreurs['statut'] = 'Statut invalide.';
        }

        $publieLe = null;
        if ($valeurs['statut'] === 'programme') {
            if ($valeurs['publie_le'] === '') {
                $erreurs['publie_le'] = 'Choisissez une date de publication.';
            } else {
                $date = DateTime::createFromFormat('Y-m-d\TH:i', $valeurs['publie_le'])
                    ?: DateTime::createFromFormat('Y-m-d\TH:i:s', $valeurs['publie_le']);

                if ($date === false) {
                    $erreurs['publie_le'] = 'Date de publication invalide.';
                } elseif ($date <= new DateTime()) {
                    $erreurs['publie_le'] = 'La date de programmation doit être dans le futur.';
                } else {
                    $publieLe = $date->format('Y-m-d H:i:s');
                }
            }
        } elseif ($valeurs['statut'] === 'publie') {
            $publieLe = (new DateTime())->format('Y-m-d H:i:s');
        }

        if ($erreurs !== []) {
            return ['erreurs' => $erreurs, 'valeurs' => $valeurs];
        }

        // Gestion de l'image : on ne touche au système de fichiers qu'une fois
        // les autres champs validés, pour ne jamais enregistrer une image
        // orpheline si le formulaire est de toute façon invalide.
        $imageChemin = $actualiteExistante['image_chemin'] ?? null;
        $ancienneImageASupprimer = null;
        $fichierImage = $_FILES['image'] ?? null;
        $nouvelleImageEnvoyee = $fichierImage !== null
            && ($fichierImage['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
        $suppressionDemandee = $requete->post('supprimer_image') === '1';

        if ($nouvelleImageEnvoyee) {
            try {
                $resultatImage = TeleversementImage::traiter($fichierImage, 'actualites');
            } catch (InvalidArgumentException $exception) {
                $erreurs['image'] = $exception->getMessage();
                return ['erreurs' => $erreurs, 'valeurs' => $valeurs];
            }

            if ($imageChemin !== null) {
                $ancienneImageASupprimer = $imageChemin;
            }
            $imageChemin = $resultatImage['chemin'];
        } elseif ($suppressionDemandee) {
            $ancienneImageASupprimer = $imageChemin;
            $imageChemin = null;
        }

        if ($imageChemin !== null && $valeurs['image_alt'] === '') {
            $erreurs['image_alt'] = "Le texte alternatif de l'image est obligatoire.";
            return ['erreurs' => $erreurs, 'valeurs' => $valeurs];
        }

        return [
            'erreurs' => [],
            'valeurs' => $valeurs,
            'donnees' => [
                'titre' => $valeurs['titre'],
                'contenu' => $contenuNettoye,
                'image_chemin' => $imageChemin,
                'image_alt' => $imageChemin !== null ? $valeurs['image_alt'] : null,
                'statut' => $valeurs['statut'],
                'publie_le' => $publieLe,
            ],
            'ancienneImageASupprimer' => $ancienneImageASupprimer,
        ];
    }
}
