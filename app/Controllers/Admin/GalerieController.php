<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Adresse;
use App\Core\Csrf;
use App\Core\NotFoundException;
use App\Core\Ordonnancement;
use App\Core\Request;
use App\Core\Response;
use App\Core\TeleversementImage;
use App\Core\Validateur;
use App\Repositories\GalerieRepository;
use InvalidArgumentException;

/**
 * Galeries photos.
 *
 * Déroulé volontairement en deux temps : on crée d'abord la galerie (son nom),
 * puis on y ajoute des photos depuis la page de modification. Demander les
 * deux en même temps rendrait le premier écran intimidant, et surtout il est
 * impossible de saisir une description par photo pendant qu'on en sélectionne
 * vingt d'un coup.
 *
 * Les descriptions se saisissent donc après l'envoi, et la galerie ne peut pas
 * être mise en ligne tant qu'il en manque une : l'exigence d'accessibilité est
 * tenue sans rendre l'ajout de photos pénible.
 */
final class GalerieController extends ControleurAdmin
{
    private const STATUTS = ['brouillon', 'publie'];

    public function liste(Request $requete): void
    {
        Response::html(
            'admin/galeries/liste',
            ['titre' => 'Galeries photos', 'galeries' => GalerieRepository::toutes()],
            200,
            'admin/gabarit'
        );
    }

    public function creer(Request $requete): void
    {
        Response::html(
            'admin/galeries/creer',
            [
                'titre' => 'Créer une galerie',
                'valeurs' => ['titre' => '', 'description' => '', 'statut' => 'brouillon'],
                'erreurs' => [],
            ],
            200,
            'admin/gabarit'
        );
    }

    public function enregistrerNouvelle(Request $requete): void
    {
        $valeurs = [
            'titre' => trim((string) $requete->post('titre', '')),
            'description' => (string) $requete->post('description', ''),
            'statut' => 'brouillon',
        ];

        $v = new Validateur();

        if (!Csrf::valide($requete->post('jeton_csrf'))) {
            $v->ajouterErreur('general', "Votre session a expiré. Vérifiez vos informations puis réessayez.");
        }

        $titre = $v->texte('titre', $valeurs['titre'], 'Le nom de la galerie', min: 3, max: 200);
        $description = $v->html('description', $valeurs['description'], 'La description', obligatoire: false);

        if ($v->aDesErreurs()) {
            Response::html(
                'admin/galeries/creer',
                ['titre' => 'Créer une galerie', 'valeurs' => $valeurs, 'erreurs' => $v->erreurs()],
                422,
                'admin/gabarit'
            );
            return;
        }

        $id = GalerieRepository::creer([
            'titre' => $titre,
            'adresse' => Adresse::unique($titre, 'galeries'),
            'description' => $description,
            'statut' => 'brouillon',
        ]);

        // On enchaîne directement sur la page de modification : c'est là que
        // se trouvent les photos, qui sont la raison d'être d'une galerie.
        Response::rediriger('/admin/galeries/' . $id . '/modifier');
    }

    public function modifier(Request $requete, string $id): void
    {
        $galerie = $this->trouverOuEchouer($id);

        $this->afficherFormulaire(
            $galerie,
            [
                'titre' => $galerie['titre'],
                'description' => $galerie['description'] ?? '',
                'statut' => $galerie['statut'],
            ],
            []
        );
    }

    public function enregistrerModification(Request $requete, string $id): void
    {
        $galerie = $this->trouverOuEchouer($id);

        $valeurs = [
            'titre' => trim((string) $requete->post('titre', '')),
            'description' => (string) $requete->post('description', ''),
            'statut' => (string) $requete->post('statut', 'brouillon'),
        ];

        $v = new Validateur();

        if (!Csrf::valide($requete->post('jeton_csrf'))) {
            $v->ajouterErreur('general', "Votre session a expiré. Vérifiez vos informations puis réessayez.");
        }

        $titre = $v->texte('titre', $valeurs['titre'], 'Le nom de la galerie', min: 3, max: 200);
        $description = $v->html('description', $valeurs['description'], 'La description', obligatoire: false);
        $statut = $v->choix('statut', $valeurs['statut'], 'La mise en ligne', self::STATUTS, 'brouillon');

        // Les descriptions de photos se saisissent dans le même formulaire :
        // on les enregistre avant de vérifier s'il en manque.
        $photos = GalerieRepository::photos((int) $id);
        $descriptions = (array) ($requete->post('alt', []) ?? []);

        foreach ($photos as $photo) {
            $nouvelAlt = trim((string) ($descriptions[$photo['id']] ?? $photo['alt']));

            if ($nouvelAlt !== $photo['alt']) {
                GalerieRepository::modifierTexteAlternatif((int) $photo['id'], mb_substr($nouvelAlt, 0, 255));
            }
        }

        $photos = GalerieRepository::photos((int) $id);
        $sansDescription = array_filter($photos, static fn (array $p): bool => trim($p['alt']) === '');

        if ($statut === 'publie' && $sansDescription !== []) {
            $nombre = count($sansDescription);
            $v->ajouterErreur(
                'statut',
                $nombre === 1
                    ? "Une photo n'a pas encore de description. Complétez-la ci-dessous "
                        . "avant de mettre la galerie en ligne."
                    : "{$nombre} photos n'ont pas encore de description. Complétez-les "
                        . "ci-dessous avant de mettre la galerie en ligne."
            );
        }

        if ($v->aDesErreurs()) {
            $this->afficherFormulaire($galerie, $valeurs, $v->erreurs(), 422);
            return;
        }

        GalerieRepository::modifier((int) $id, [
            'titre' => $titre,
            'description' => $description,
            'statut' => $statut,
        ]);

        Response::rediriger('/admin/galeries');
    }

    public function ajouterPhotos(Request $requete, string $id): void
    {
        $galerie = $this->trouverOuEchouer($id);

        if (!Csrf::valide($requete->post('jeton_csrf'))) {
            Response::rediriger('/admin/galeries/' . (int) $id . '/modifier');
            return;
        }

        $envoyes = $_FILES['photos'] ?? null;
        $erreurs = [];
        $ajoutees = 0;

        if ($envoyes !== null && is_array($envoyes['name'])) {
            foreach (array_keys($envoyes['name']) as $indice) {
                if (($envoyes['error'][$indice] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                    continue;
                }

                $fichier = [
                    'name' => $envoyes['name'][$indice],
                    'type' => $envoyes['type'][$indice],
                    'tmp_name' => $envoyes['tmp_name'][$indice],
                    'error' => $envoyes['error'][$indice],
                    'size' => $envoyes['size'][$indice],
                ];

                try {
                    $resultat = TeleversementImage::traiter($fichier, 'galeries');
                } catch (InvalidArgumentException $exception) {
                    // Une photo refusée n'annule pas les autres : on signale
                    // laquelle a posé problème et on continue.
                    $erreurs[] = '« ' . $fichier['name'] . ' » : ' . $exception->getMessage();
                    continue;
                }

                GalerieRepository::ajouterPhoto((int) $id, $resultat['chemin'], '');
                $ajoutees++;
            }
        }

        $this->afficherFormulaire(
            $galerie,
            [
                'titre' => $galerie['titre'],
                'description' => $galerie['description'] ?? '',
                'statut' => $galerie['statut'],
            ],
            [],
            200,
            $ajoutees,
            $erreurs
        );
    }

    public function supprimerPhoto(Request $requete, string $id, string $photoId): void
    {
        if (!Csrf::valide($requete->post('jeton_csrf'))) {
            Response::rediriger('/admin/galeries/' . (int) $id . '/modifier');
            return;
        }

        $photo = GalerieRepository::photo((int) $photoId);

        if ($photo !== null && (int) $photo['galerie_id'] === (int) $id) {
            GalerieRepository::supprimerPhoto((int) $photoId);
            TeleversementImage::supprimer($photo['chemin']);
        }

        Response::rediriger('/admin/galeries/' . (int) $id . '/modifier');
    }

    public function monterPhoto(Request $requete, string $id, string $photoId): void
    {
        $this->deplacerPhoto($requete, (int) $id, (int) $photoId, 'monter');
    }

    public function descendrePhoto(Request $requete, string $id, string $photoId): void
    {
        $this->deplacerPhoto($requete, (int) $id, (int) $photoId, 'descendre');
    }

    public function supprimer(Request $requete, string $id): void
    {
        if (!Csrf::valide($requete->post('jeton_csrf'))) {
            Response::rediriger('/admin/galeries');
            return;
        }

        // Les chemins sont relevés AVANT la suppression : la contrainte
        // ON DELETE CASCADE effacera les lignes en base, mais elle ne sait
        // rien des fichiers posés sur le disque.
        $photos = GalerieRepository::photos((int) $id);

        GalerieRepository::supprimer((int) $id);

        foreach ($photos as $photo) {
            TeleversementImage::supprimer($photo['chemin']);
        }

        Response::rediriger('/admin/galeries');
    }

    public function monter(Request $requete, string $id): void
    {
        if (Csrf::valide($requete->post('jeton_csrf'))) {
            Ordonnancement::deplacer('galeries', (int) $id, 'monter');
        }

        Response::rediriger('/admin/galeries');
    }

    public function descendre(Request $requete, string $id): void
    {
        if (Csrf::valide($requete->post('jeton_csrf'))) {
            Ordonnancement::deplacer('galeries', (int) $id, 'descendre');
        }

        Response::rediriger('/admin/galeries');
    }

    private function deplacerPhoto(Request $requete, int $id, int $photoId, string $direction): void
    {
        $photo = GalerieRepository::photo($photoId);

        if (Csrf::valide($requete->post('jeton_csrf')) && $photo !== null && (int) $photo['galerie_id'] === $id) {
            Ordonnancement::deplacer('galerie_photos', $photoId, $direction, 'galerie_id', $id);
        }

        Response::rediriger('/admin/galeries/' . $id . '/modifier');
    }

    private function trouverOuEchouer(string $id): array
    {
        $galerie = GalerieRepository::trouve((int) $id);

        if ($galerie === null) {
            throw new NotFoundException("Galerie {$id} introuvable.");
        }

        return $galerie;
    }

    /**
     * @param array<string, string> $valeurs
     * @param array<string, string> $erreurs
     * @param array<int, string> $erreursPhotos
     */
    private function afficherFormulaire(
        array $galerie,
        array $valeurs,
        array $erreurs,
        int $codeStatut = 200,
        int $photosAjoutees = 0,
        array $erreursPhotos = [],
    ): void {
        Response::html(
            'admin/galeries/formulaire',
            [
                'titre' => 'Galerie — ' . $galerie['titre'],
                'galerie' => $galerie,
                'photos' => GalerieRepository::photos((int) $galerie['id']),
                'valeurs' => $valeurs,
                'erreurs' => $erreurs,
                'photos_ajoutees' => $photosAjoutees,
                'erreurs_photos' => $erreursPhotos,
            ],
            $codeStatut,
            'admin/gabarit'
        );
    }
}
