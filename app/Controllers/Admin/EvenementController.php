<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Adresse;
use App\Core\ChampImage;
use App\Core\Csrf;
use App\Core\NotFoundException;
use App\Core\Request;
use App\Core\Response;
use App\Core\TeleversementImage;
use App\Core\Validateur;
use App\Repositories\EvenementRepository;
use DateTime;

final class EvenementController extends ControleurAdmin
{
    private const STATUTS = ['brouillon', 'publie'];

    public function liste(Request $requete): void
    {
        Response::html(
            'admin/evenements/liste',
            ['titre' => 'Agenda', 'evenements' => EvenementRepository::tous()],
            200,
            'admin/gabarit'
        );
    }

    public function creer(Request $requete): void
    {
        $this->afficherFormulaire('Ajouter un événement', null, $this->valeursVides(), []);
    }

    public function enregistrerNouveau(Request $requete): void
    {
        $resultat = $this->valider($requete, null);

        if ($resultat['erreurs'] !== []) {
            $this->afficherFormulaire('Ajouter un événement', null, $resultat['valeurs'], $resultat['erreurs'], 422);
            return;
        }

        EvenementRepository::creer([
            ...$resultat['donnees'],
            'adresse' => Adresse::unique($resultat['donnees']['titre'], 'evenements'),
        ]);

        Response::rediriger('/admin/agenda');
    }

    public function modifier(Request $requete, string $id): void
    {
        $evenement = $this->trouverOuEchouer($id);

        $this->afficherFormulaire(
            'Modifier l\'événement',
            $evenement,
            [
                'titre' => $evenement['titre'],
                'description' => $evenement['description'],
                'debut' => $evenement['debut'],
                'fin' => $evenement['fin'] ?? '',
                'lieu' => $evenement['lieu'] ?? '',
                'statut' => $evenement['statut'],
                'image_alt' => $evenement['image_alt'] ?? '',
            ],
            []
        );
    }

    public function enregistrerModification(Request $requete, string $id): void
    {
        $evenement = $this->trouverOuEchouer($id);
        $resultat = $this->valider($requete, $evenement);

        if ($resultat['erreurs'] !== []) {
            $this->afficherFormulaire(
                'Modifier l\'événement',
                $evenement,
                $resultat['valeurs'],
                $resultat['erreurs'],
                422
            );
            return;
        }

        EvenementRepository::modifier((int) $id, $resultat['donnees']);
        TeleversementImage::supprimer($resultat['ancienne_image']);

        Response::rediriger('/admin/agenda');
    }

    public function supprimer(Request $requete, string $id): void
    {
        if (!Csrf::valide($requete->post('jeton_csrf'))) {
            Response::rediriger('/admin/agenda');
            return;
        }

        $evenement = EvenementRepository::trouve((int) $id);

        if ($evenement !== null) {
            EvenementRepository::supprimer((int) $id);
            TeleversementImage::supprimer($evenement['image_chemin']);
        }

        Response::rediriger('/admin/agenda');
    }

    public function apercu(Request $requete, string $id): void
    {
        $evenement = $this->trouverOuEchouer($id);

        Response::html(
            'admin/evenements/apercu',
            ['titre' => 'Aperçu — ' . $evenement['titre'], 'evenement' => $evenement],
            200,
            'admin/gabarit'
        );
    }

    private function trouverOuEchouer(string $id): array
    {
        $evenement = EvenementRepository::trouve((int) $id);

        if ($evenement === null) {
            throw new NotFoundException("Événement {$id} introuvable.");
        }

        return $evenement;
    }

    /**
     * @return array<string, string>
     */
    private function valeursVides(): array
    {
        return [
            'titre' => '',
            'description' => '',
            'debut' => '',
            'fin' => '',
            'lieu' => '',
            'statut' => 'brouillon',
            'image_alt' => '',
        ];
    }

    private function afficherFormulaire(
        string $titrePage,
        ?array $evenement,
        array $valeurs,
        array $erreurs,
        int $codeStatut = 200,
    ): void {
        Response::html(
            'admin/evenements/formulaire',
            [
                'titre' => $titrePage,
                'evenement' => $evenement,
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
    private function valider(Request $requete, ?array $existant): array
    {
        $valeurs = [
            'titre' => trim((string) $requete->post('titre', '')),
            'description' => (string) $requete->post('description', ''),
            'debut' => trim((string) $requete->post('debut', '')),
            'fin' => trim((string) $requete->post('fin', '')),
            'lieu' => trim((string) $requete->post('lieu', '')),
            'statut' => (string) $requete->post('statut', 'brouillon'),
            'image_alt' => trim((string) $requete->post('image_alt', '')),
        ];

        if (!Csrf::valide($requete->post('jeton_csrf'))) {
            return [
                'erreurs' => ['general' => "Votre session a expiré. Vérifiez vos informations puis réessayez."],
                'valeurs' => $valeurs,
            ];
        }

        $v = new Validateur();

        // La date de début n'a pas à être dans le futur : on doit pouvoir
        // saisir après coup un événement qui a déjà eu lieu, ou corriger une
        // erreur sur un événement passé.
        $donnees = [
            'titre' => $v->texte('titre', $valeurs['titre'], 'Le nom de l\'événement', min: 3, max: 200),
            'description' => $v->html('description', $valeurs['description'], 'La description'),
            'debut' => $v->dateHeure('debut', $valeurs['debut'], 'la date de début', obligatoire: true),
            'fin' => $v->dateHeure('fin', $valeurs['fin'], 'la date de fin'),
            'lieu' => $v->texte('lieu', $valeurs['lieu'], 'Le lieu', obligatoire: false, max: 255),
            'statut' => $v->choix('statut', $valeurs['statut'], 'La mise en ligne', self::STATUTS, 'brouillon'),
        ];

        if (
            $donnees['debut'] !== null
            && $donnees['fin'] !== null
            && new DateTime($donnees['fin']) < new DateTime($donnees['debut'])
        ) {
            $v->ajouterErreur('fin', "La fin de l'événement ne peut pas précéder son début.");
        }

        $image = ChampImage::traiter(
            'image',
            $existant['image_chemin'] ?? null,
            $existant['image_alt'] ?? null,
            'evenements',
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
                ...$donnees,
                'image_chemin' => $image['chemin'],
                'image_alt' => $image['alt'],
            ],
            'ancienne_image' => $image['ancienne_a_supprimer'],
        ];
    }
}
