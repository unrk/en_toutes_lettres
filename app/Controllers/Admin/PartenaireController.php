<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\ChampImage;
use App\Core\Csrf;
use App\Core\NotFoundException;
use App\Core\Ordonnancement;
use App\Core\Request;
use App\Core\Response;
use App\Core\TeleversementImage;
use App\Core\Validateur;
use App\Repositories\PartenaireRepository;

final class PartenaireController extends ControleurAdmin
{
    private const STATUTS = ['brouillon', 'publie'];

    public function liste(Request $requete): void
    {
        Response::html(
            'admin/partenaires/liste',
            ['titre' => 'Partenaires', 'partenaires' => PartenaireRepository::tous()],
            200,
            'admin/gabarit'
        );
    }

    public function creer(Request $requete): void
    {
        $this->afficherFormulaire('Ajouter un partenaire', null, $this->valeursVides(), []);
    }

    public function enregistrerNouveau(Request $requete): void
    {
        $resultat = $this->valider($requete, null);

        if ($resultat['erreurs'] !== []) {
            $this->afficherFormulaire('Ajouter un partenaire', null, $resultat['valeurs'], $resultat['erreurs'], 422);
            return;
        }

        PartenaireRepository::creer($resultat['donnees']);

        Response::rediriger('/admin/partenaires');
    }

    public function modifier(Request $requete, string $id): void
    {
        $partenaire = $this->trouverOuEchouer($id);

        $this->afficherFormulaire(
            'Modifier le partenaire',
            $partenaire,
            [
                'nom' => $partenaire['nom'],
                'lien_url' => $partenaire['lien_url'] ?? '',
                'statut' => $partenaire['statut'],
                'logo_alt' => $partenaire['logo_alt'] ?? '',
            ],
            []
        );
    }

    public function enregistrerModification(Request $requete, string $id): void
    {
        $partenaire = $this->trouverOuEchouer($id);
        $resultat = $this->valider($requete, $partenaire);

        if ($resultat['erreurs'] !== []) {
            $this->afficherFormulaire(
                'Modifier le partenaire',
                $partenaire,
                $resultat['valeurs'],
                $resultat['erreurs'],
                422
            );
            return;
        }

        PartenaireRepository::modifier((int) $id, $resultat['donnees']);
        TeleversementImage::supprimer($resultat['ancienne_image']);

        Response::rediriger('/admin/partenaires');
    }

    public function supprimer(Request $requete, string $id): void
    {
        if (!Csrf::valide($requete->post('jeton_csrf'))) {
            Response::rediriger('/admin/partenaires');
            return;
        }

        $partenaire = PartenaireRepository::trouve((int) $id);

        if ($partenaire !== null) {
            PartenaireRepository::supprimer((int) $id);
            TeleversementImage::supprimer($partenaire['logo_chemin']);
        }

        Response::rediriger('/admin/partenaires');
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
            Ordonnancement::deplacer('partenaires', $id, $direction);
        }

        Response::rediriger('/admin/partenaires');
    }

    private function trouverOuEchouer(string $id): array
    {
        $partenaire = PartenaireRepository::trouve((int) $id);

        if ($partenaire === null) {
            throw new NotFoundException("Partenaire {$id} introuvable.");
        }

        return $partenaire;
    }

    /**
     * @return array<string, string>
     */
    private function valeursVides(): array
    {
        return [
            'nom' => '',
            'lien_url' => '',
            'statut' => 'publie',
            'logo_alt' => '',
        ];
    }

    private function afficherFormulaire(
        string $titrePage,
        ?array $partenaire,
        array $valeurs,
        array $erreurs,
        int $codeStatut = 200,
    ): void {
        Response::html(
            'admin/partenaires/formulaire',
            [
                'titre' => $titrePage,
                'partenaire' => $partenaire,
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
            'nom' => trim((string) $requete->post('nom', '')),
            'lien_url' => trim((string) $requete->post('lien_url', '')),
            'statut' => (string) $requete->post('statut', 'publie'),
            'logo_alt' => trim((string) $requete->post('logo_alt', '')),
        ];

        if (!Csrf::valide($requete->post('jeton_csrf'))) {
            return [
                'erreurs' => ['general' => "Votre session a expiré. Vérifiez vos informations puis réessayez."],
                'valeurs' => $valeurs,
            ];
        }

        $v = new Validateur();

        $nom = $v->texte('nom', $valeurs['nom'], 'Le nom du partenaire', min: 2, max: 200);
        $lien = $v->url('lien_url', $valeurs['lien_url'], 'Le lien vers leur site');
        $statut = $v->choix('statut', $valeurs['statut'], 'L\'affichage', self::STATUTS, 'publie');

        // Un partenaire sans logo n'a pas grand sens sur une page de logos.
        $logo = ChampImage::traiter(
            'logo',
            $existant['logo_chemin'] ?? null,
            $existant['logo_alt'] ?? null,
            'partenaires',
            $v,
            $requete,
            obligatoire: true,
            libelle: 'Le logo'
        );

        if ($v->aDesErreurs()) {
            return ['erreurs' => $v->erreurs(), 'valeurs' => $valeurs];
        }

        return [
            'erreurs' => [],
            'valeurs' => $valeurs,
            'donnees' => [
                'nom' => $nom,
                'lien_url' => $lien,
                'logo_chemin' => $logo['chemin'],
                'logo_alt' => $logo['alt'],
                'statut' => $statut,
            ],
            'ancienne_image' => $logo['ancienne_a_supprimer'],
        ];
    }
}
