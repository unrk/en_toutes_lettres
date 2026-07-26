<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Adresse;
use App\Core\Csrf;
use App\Core\NotFoundException;
use App\Core\Request;
use App\Core\Response;
use App\Core\Validateur;
use App\Repositories\PageRepository;

final class PageController extends ControleurAdmin
{
    private const STATUTS = ['brouillon', 'publie'];

    public function liste(Request $requete): void
    {
        Response::html(
            'admin/pages/liste',
            ['titre' => 'Pages du site', 'pages' => PageRepository::toutes()],
            200,
            'admin/gabarit'
        );
    }

    public function creer(Request $requete): void
    {
        $this->afficherFormulaire('Ajouter une page', null, $this->valeursVides(), []);
    }

    public function enregistrerNouvelle(Request $requete): void
    {
        $resultat = $this->valider($requete);

        if ($resultat['erreurs'] !== []) {
            $this->afficherFormulaire('Ajouter une page', null, $resultat['valeurs'], $resultat['erreurs'], 422);
            return;
        }

        PageRepository::creer([
            ...$resultat['donnees'],
            'adresse' => Adresse::unique($resultat['donnees']['titre'], 'pages'),
        ]);

        Response::rediriger('/admin/pages');
    }

    public function modifier(Request $requete, string $id): void
    {
        $page = $this->trouverOuEchouer($id);

        $this->afficherFormulaire(
            'Modifier la page',
            $page,
            [
                'titre' => $page['titre'],
                'contenu' => $page['contenu'],
                'statut' => $page['statut'],
            ],
            []
        );
    }

    public function enregistrerModification(Request $requete, string $id): void
    {
        $page = $this->trouverOuEchouer($id);
        $resultat = $this->valider($requete);

        if ($resultat['erreurs'] !== []) {
            $this->afficherFormulaire('Modifier la page', $page, $resultat['valeurs'], $resultat['erreurs'], 422);
            return;
        }

        PageRepository::modifier((int) $id, $resultat['donnees']);

        Response::rediriger('/admin/pages');
    }

    public function supprimer(Request $requete, string $id): void
    {
        if (!Csrf::valide($requete->post('jeton_csrf'))) {
            Response::rediriger('/admin/pages');
            return;
        }

        $page = PageRepository::trouve((int) $id);

        // Double garde volontaire : ici pour afficher une explication, et dans
        // la requête SQL de suppression pour que même une requête forgée à la
        // main ne puisse pas passer.
        if ($page !== null && (bool) $page['verrouillee']) {
            Response::html(
                'admin/pages/verrouillee',
                ['titre' => 'Page protégée', 'page' => $page],
                403,
                'admin/gabarit'
            );
            return;
        }

        PageRepository::supprimer((int) $id);

        Response::rediriger('/admin/pages');
    }

    public function apercu(Request $requete, string $id): void
    {
        $page = $this->trouverOuEchouer($id);

        Response::html(
            'admin/pages/apercu',
            ['titre' => 'Aperçu — ' . $page['titre'], 'page' => $page],
            200,
            'admin/gabarit'
        );
    }

    private function trouverOuEchouer(string $id): array
    {
        $page = PageRepository::trouve((int) $id);

        if ($page === null) {
            throw new NotFoundException("Page {$id} introuvable.");
        }

        return $page;
    }

    /**
     * @return array<string, string>
     */
    private function valeursVides(): array
    {
        return ['titre' => '', 'contenu' => '', 'statut' => 'brouillon'];
    }

    private function afficherFormulaire(
        string $titrePage,
        ?array $page,
        array $valeurs,
        array $erreurs,
        int $codeStatut = 200,
    ): void {
        Response::html(
            'admin/pages/formulaire',
            [
                'titre' => $titrePage,
                'page' => $page,
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
     *     donnees?: array<string, mixed>
     * }
     */
    private function valider(Request $requete): array
    {
        $valeurs = [
            'titre' => trim((string) $requete->post('titre', '')),
            'contenu' => (string) $requete->post('contenu', ''),
            'statut' => (string) $requete->post('statut', 'brouillon'),
        ];

        if (!Csrf::valide($requete->post('jeton_csrf'))) {
            return [
                'erreurs' => ['general' => "Votre session a expiré. Vérifiez vos informations puis réessayez."],
                'valeurs' => $valeurs,
            ];
        }

        $v = new Validateur();

        $donnees = [
            'titre' => $v->texte('titre', $valeurs['titre'], 'Le titre de la page', min: 3, max: 200),
            'contenu' => $v->html('contenu', $valeurs['contenu'], 'Le contenu'),
            'statut' => $v->choix('statut', $valeurs['statut'], 'La mise en ligne', self::STATUTS, 'brouillon'),
        ];

        if ($v->aDesErreurs()) {
            return ['erreurs' => $v->erreurs(), 'valeurs' => $valeurs];
        }

        return ['erreurs' => [], 'valeurs' => $valeurs, 'donnees' => $donnees];
    }
}
