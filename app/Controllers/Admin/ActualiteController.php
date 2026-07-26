<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Adresse;
use App\Core\Auth;
use App\Core\ChampImage;
use App\Core\Csrf;
use App\Core\NotFoundException;
use App\Core\Request;
use App\Core\Response;
use App\Core\TeleversementImage;
use App\Core\Validateur;
use App\Repositories\ActualiteRepository;

final class ActualiteController extends ControleurAdmin
{
    private const STATUTS = ['brouillon', 'publie', 'programme'];
    private const TYPES = ['actualite', 'annonce'];

    public function liste(Request $requete): void
    {
        Response::html(
            'admin/actualites/liste',
            ['titre' => 'Actualités et annonces', 'actualites' => ActualiteRepository::tous()],
            200,
            'admin/gabarit'
        );
    }

    public function creer(Request $requete): void
    {
        $this->afficherFormulaire('Ajouter une actualité', null, $this->valeursVides(), []);
    }

    public function enregistrerNouvelle(Request $requete): void
    {
        $resultat = $this->valider($requete, null);

        if ($resultat['erreurs'] !== []) {
            $this->afficherFormulaire(
                'Ajouter une actualité',
                null,
                $resultat['valeurs'],
                $resultat['erreurs'],
                422
            );
            return;
        }

        ActualiteRepository::creer([
            ...$resultat['donnees'],
            'adresse' => Adresse::unique($resultat['donnees']['titre'], 'actualites'),
            'auteur_id' => Auth::utilisateur()['id'],
        ]);

        Response::rediriger('/admin/actualites');
    }

    public function modifier(Request $requete, string $id): void
    {
        $actualite = $this->trouverOuEchouer($id);

        $this->afficherFormulaire(
            'Modifier l\'actualité',
            $actualite,
            [
                'titre' => $actualite['titre'],
                'type' => $actualite['type'],
                'contenu' => $actualite['contenu'],
                'statut' => $actualite['statut'],
                'publie_le' => $actualite['publie_le'] ?? '',
                'image_alt' => $actualite['image_alt'] ?? '',
            ],
            []
        );
    }

    public function enregistrerModification(Request $requete, string $id): void
    {
        $actualite = $this->trouverOuEchouer($id);
        $resultat = $this->valider($requete, $actualite);

        if ($resultat['erreurs'] !== []) {
            $this->afficherFormulaire(
                'Modifier l\'actualité',
                $actualite,
                $resultat['valeurs'],
                $resultat['erreurs'],
                422
            );
            return;
        }

        // L'adresse n'est volontairement pas recalculée : renommer une fiche ne
        // doit pas casser les liens déjà partagés vers elle.
        ActualiteRepository::modifier((int) $id, $resultat['donnees']);
        TeleversementImage::supprimer($resultat['ancienne_image']);

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
        $actualite = $this->trouverOuEchouer($id);

        Response::html(
            'admin/actualites/apercu',
            ['titre' => 'Aperçu — ' . $actualite['titre'], 'actualite' => $actualite],
            200,
            'admin/gabarit'
        );
    }

    private function trouverOuEchouer(string $id): array
    {
        $actualite = ActualiteRepository::trouve((int) $id);

        if ($actualite === null) {
            throw new NotFoundException("Actualité {$id} introuvable.");
        }

        return $actualite;
    }

    /**
     * @return array<string, string>
     */
    private function valeursVides(): array
    {
        return [
            'titre' => '',
            'type' => 'actualite',
            'contenu' => '',
            'statut' => 'brouillon',
            'publie_le' => '',
            'image_alt' => '',
        ];
    }

    private function afficherFormulaire(
        string $titrePage,
        ?array $actualite,
        array $valeurs,
        array $erreurs,
        int $codeStatut = 200,
    ): void {
        Response::html(
            'admin/actualites/formulaire',
            [
                'titre' => $titrePage,
                'actualite' => $actualite,
                'valeurs' => $valeurs,
                'erreurs' => $erreurs,
            ],
            $codeStatut,
            'admin/gabarit'
        );
    }

    /**
     * @return array{
     *     erreurs: array<string, string>,
     *     valeurs: array<string, string>,
     *     donnees?: array<string, mixed>,
     *     ancienne_image?: ?string
     * }
     */
    private function valider(Request $requete, ?array $existante): array
    {
        $valeurs = [
            'titre' => trim((string) $requete->post('titre', '')),
            'type' => (string) $requete->post('type', 'actualite'),
            'contenu' => (string) $requete->post('contenu', ''),
            'statut' => (string) $requete->post('statut', 'brouillon'),
            'publie_le' => trim((string) $requete->post('publie_le', '')),
            'image_alt' => trim((string) $requete->post('image_alt', '')),
        ];

        if (!Csrf::valide($requete->post('jeton_csrf'))) {
            return [
                'erreurs' => ['general' => "Votre session a expiré. Vérifiez vos informations puis réessayez."],
                'valeurs' => $valeurs,
            ];
        }

        $v = new Validateur();

        $titre = $v->texte('titre', $valeurs['titre'], 'Le titre', min: 3, max: 200);
        $type = $v->choix('type', $valeurs['type'], 'Le type', self::TYPES, 'actualite');
        $contenu = $v->html('contenu', $valeurs['contenu'], 'Le contenu');
        $statut = $v->choix('statut', $valeurs['statut'], 'Le statut', self::STATUTS, 'brouillon');

        $publieLe = match ($statut) {
            'programme' => $v->dateHeure(
                'publie_le',
                $valeurs['publie_le'],
                'La date de mise en ligne',
                obligatoire: true,
                doitEtreFuture: true
            ),
            'publie' => $existante['publie_le'] ?? date('Y-m-d H:i:s'),
            default => null,
        };

        $image = ChampImage::traiter(
            'image',
            $existante['image_chemin'] ?? null,
            $existante['image_alt'] ?? null,
            'actualites',
            $v,
            $requete
        );

        if ($v->aDesErreurs()) {
            return ['erreurs' => $v->erreurs(), 'valeurs' => $valeurs];
        }

        return [
            'erreurs' => [],
            'valeurs' => $valeurs,
            'donnees' => [
                'titre' => $titre,
                'type' => $type,
                'contenu' => $contenu,
                'image_chemin' => $image['chemin'],
                'image_alt' => $image['alt'],
                'statut' => $statut,
                'publie_le' => $publieLe,
            ],
            'ancienne_image' => $image['ancienne_a_supprimer'],
        ];
    }
}
