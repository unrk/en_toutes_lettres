<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Adresse;
use App\Core\ChampImage;
use App\Core\Csrf;
use App\Core\NotFoundException;
use App\Core\Ordonnancement;
use App\Core\Request;
use App\Core\Response;
use App\Core\TeleversementImage;
use App\Core\Validateur;
use App\Repositories\ActiviteRepository;

final class ActiviteController extends ControleurAdmin
{
    private const STATUTS = ['brouillon', 'publie'];

    public function liste(Request $requete): void
    {
        Response::html(
            'admin/activites/liste',
            ['titre' => 'Activités', 'activites' => ActiviteRepository::tous()],
            200,
            'admin/gabarit'
        );
    }

    public function creer(Request $requete): void
    {
        $this->afficherFormulaire('Ajouter une activité', null, $this->valeursVides(), []);
    }

    public function enregistrerNouvelle(Request $requete): void
    {
        $resultat = $this->valider($requete, null);

        if ($resultat['erreurs'] !== []) {
            $this->afficherFormulaire('Ajouter une activité', null, $resultat['valeurs'], $resultat['erreurs'], 422);
            return;
        }

        ActiviteRepository::creer([
            ...$resultat['donnees'],
            'adresse' => Adresse::unique($resultat['donnees']['titre'], 'activites'),
        ]);

        Response::rediriger('/admin/activites');
    }

    public function modifier(Request $requete, string $id): void
    {
        $activite = $this->trouverOuEchouer($id);

        $this->afficherFormulaire(
            'Modifier l\'activité',
            $activite,
            [
                'titre' => $activite['titre'],
                'resume' => $activite['resume'] ?? '',
                'description' => $activite['description'],
                'creneaux' => $activite['creneaux'] ?? '',
                'lieu' => $activite['lieu'] ?? '',
                'public_vise' => $activite['public_vise'] ?? '',
                'tarif' => $activite['tarif'] ?? '',
                'inscriptions' => $activite['inscriptions'] ?? '',
                'statut' => $activite['statut'],
                'image_alt' => $activite['image_alt'] ?? '',
            ],
            []
        );
    }

    public function enregistrerModification(Request $requete, string $id): void
    {
        $activite = $this->trouverOuEchouer($id);
        $resultat = $this->valider($requete, $activite);

        if ($resultat['erreurs'] !== []) {
            $this->afficherFormulaire(
                'Modifier l\'activité',
                $activite,
                $resultat['valeurs'],
                $resultat['erreurs'],
                422
            );
            return;
        }

        ActiviteRepository::modifier((int) $id, $resultat['donnees']);
        TeleversementImage::supprimer($resultat['ancienne_image']);

        Response::rediriger('/admin/activites');
    }

    public function supprimer(Request $requete, string $id): void
    {
        if (!Csrf::valide($requete->post('jeton_csrf'))) {
            Response::rediriger('/admin/activites');
            return;
        }

        $activite = ActiviteRepository::trouve((int) $id);

        if ($activite !== null) {
            ActiviteRepository::supprimer((int) $id);
            TeleversementImage::supprimer($activite['image_chemin']);
        }

        Response::rediriger('/admin/activites');
    }

    public function monter(Request $requete, string $id): void
    {
        $this->deplacer($requete, (int) $id, 'monter');
    }

    public function descendre(Request $requete, string $id): void
    {
        $this->deplacer($requete, (int) $id, 'descendre');
    }

    private function deplacer(Request $requete, int $id, string $direction): void
    {
        if (Csrf::valide($requete->post('jeton_csrf'))) {
            Ordonnancement::deplacer('activites', $id, $direction);
        }

        Response::rediriger('/admin/activites');
    }

    public function apercu(Request $requete, string $id): void
    {
        $activite = $this->trouverOuEchouer($id);

        Response::html(
            'admin/activites/apercu',
            ['titre' => 'Aperçu — ' . $activite['titre'], 'activite' => $activite],
            200,
            'admin/gabarit'
        );
    }

    private function trouverOuEchouer(string $id): array
    {
        $activite = ActiviteRepository::trouve((int) $id);

        if ($activite === null) {
            throw new NotFoundException("Activité {$id} introuvable.");
        }

        return $activite;
    }

    /**
     * @return array<string, string>
     */
    private function valeursVides(): array
    {
        return [
            'titre' => '',
            'resume' => '',
            'description' => '',
            'creneaux' => '',
            'lieu' => '',
            'public_vise' => '',
            'tarif' => '',
            'inscriptions' => '',
            'statut' => 'brouillon',
            'image_alt' => '',
        ];
    }

    private function afficherFormulaire(
        string $titrePage,
        ?array $activite,
        array $valeurs,
        array $erreurs,
        int $codeStatut = 200,
    ): void {
        Response::html(
            'admin/activites/formulaire',
            [
                'titre' => $titrePage,
                'activite' => $activite,
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
            'resume' => trim((string) $requete->post('resume', '')),
            'description' => (string) $requete->post('description', ''),
            'creneaux' => trim((string) $requete->post('creneaux', '')),
            'lieu' => trim((string) $requete->post('lieu', '')),
            'public_vise' => trim((string) $requete->post('public_vise', '')),
            'tarif' => trim((string) $requete->post('tarif', '')),
            'inscriptions' => trim((string) $requete->post('inscriptions', '')),
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

        $donnees = [
            'titre' => $v->texte('titre', $valeurs['titre'], 'Le nom de l\'activité', min: 3, max: 200),
            'resume' => $v->texte('resume', $valeurs['resume'], 'Le résumé', obligatoire: false, max: 500),
            'description' => $v->html('description', $valeurs['description'], 'La description'),
            'creneaux' => $v->texte('creneaux', $valeurs['creneaux'], 'Les créneaux', obligatoire: false, max: 1000),
            'lieu' => $v->texte('lieu', $valeurs['lieu'], 'Le lieu', obligatoire: false, max: 255),
            'public_vise' => $v->texte('public_vise', $valeurs['public_vise'], 'Le public concerné', obligatoire: false, max: 255),
            'tarif' => $v->texte('tarif', $valeurs['tarif'], 'Le tarif', obligatoire: false, max: 100),
            'inscriptions' => $v->texte('inscriptions', $valeurs['inscriptions'], 'Les inscriptions', obligatoire: false, max: 1000),
            'statut' => $v->choix('statut', $valeurs['statut'], 'La mise en ligne', self::STATUTS, 'brouillon'),
        ];

        $image = ChampImage::traiter(
            'image',
            $existante['image_chemin'] ?? null,
            $existante['image_alt'] ?? null,
            'activites',
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
