<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\NotFoundException;
use App\Core\Request;
use App\Core\Response;
use App\Repositories\ActiviteRepository;
use App\Repositories\ActualiteRepository;
use App\Repositories\EvenementRepository;
use App\Repositories\PageRepository;
use App\Repositories\PartenaireRepository;
use DateTimeImmutable;

final class SiteController
{
    public function accueil(Request $requete): void
    {
        $activites = array_slice(ActiviteRepository::publiees(), 0, 3);
        $actualites = ActualiteRepository::recentes(3);
        $evenements = EvenementRepository::aVenir(3);

        Response::html('public/accueil', [
            'titre' => 'Accueil',
            'activites' => $activites,
            'actualites' => $this->avecExtraits($actualites, 'contenu', 170),
            'evenements' => $evenements,
        ]);
    }

    public function activites(Request $requete): void
    {
        Response::html('public/activites_liste', [
            'titre' => 'Activites',
            'activites' => ActiviteRepository::publiees(),
        ]);
    }

    public function activite(Request $requete, string $adresse): void
    {
        $activite = ActiviteRepository::trouveParAdressePubliee($adresse);

        if ($activite === null) {
            throw new NotFoundException('Activite introuvable');
        }

        $informations = array_filter([
            'Jours et horaires' => (string) ($activite['creneaux'] ?? ''),
            'Lieu' => (string) ($activite['lieu'] ?? ''),
            'Pour qui ?' => (string) ($activite['public_vise'] ?? ''),
            'Tarif' => (string) ($activite['tarif'] ?? ''),
            'Comment participer' => (string) ($activite['inscriptions'] ?? ''),
        ], static fn (string $valeur): bool => trim($valeur) !== '');

        Response::html('public/activite_detail', [
            'titre' => (string) $activite['titre'],
            'activite' => $activite,
            'informations' => $informations,
        ]);
    }

    public function actualites(Request $requete): void
    {
        $actualites = $this->avecExtraits(ActualiteRepository::publiees(), 'contenu', 240);

        Response::html('public/actualites_liste', [
            'titre' => 'Actualites',
            'actualites' => $actualites,
        ]);
    }

    public function actualite(Request $requete, string $adresse): void
    {
        $actualite = ActualiteRepository::trouveParAdressePublique($adresse);

        if ($actualite === null) {
            throw new NotFoundException('Actualite introuvable');
        }

        Response::html('public/actualite_detail', [
            'titre' => (string) $actualite['titre'],
            'actualite' => $actualite,
        ]);
    }

    public function agenda(Request $requete): void
    {
        Response::html('public/agenda', [
            'titre' => 'Agenda',
            'evenements' => EvenementRepository::publies(),
        ]);
    }

    public function partenaires(Request $requete): void
    {
        Response::html('public/partenaires', [
            'titre' => 'Partenaires',
            'partenaires' => PartenaireRepository::publies(),
        ]);
    }

    public function soutenir(Request $requete): void
    {
        $liensHelloAsso = config('liens_helloasso', []);

        Response::html('public/soutenir', [
            'titre' => 'Adhesion et dons',
            'liensHelloAsso' => [
                'adhesion' => $liensHelloAsso['adhesion'] ?? '',
                'don' => $liensHelloAsso['don'] ?? '',
                'billetterie' => $liensHelloAsso['billetterie'] ?? '',
            ],
        ]);
    }

    public function page(Request $requete, string $adresse): void
    {
        $page = PageRepository::trouveParAdressePubliee($adresse);

        if ($page === null) {
            throw new NotFoundException('Page introuvable');
        }

        Response::html('public/page', [
            'titre' => (string) $page['titre'],
            'page' => $page,
        ]);
    }

    /**
     * @param array<int, array<string, mixed>> $lignes
     * @return array<int, array<string, mixed>>
     */
    private function avecExtraits(array $lignes, string $champHtml, int $limite): array
    {
        foreach ($lignes as &$ligne) {
            $ligne['extrait'] = $this->extrait((string) ($ligne[$champHtml] ?? ''), $limite);
            $ligne['date_affichage'] = $this->dateAffichage($ligne);
        }

        return $lignes;
    }

    /**
     * @param array<string, mixed> $ligne
     */
    private function dateAffichage(array $ligne): ?DateTimeImmutable
    {
        $date = $ligne['publie_le'] ?? $ligne['cree_le'] ?? null;
        if (!is_string($date) || trim($date) === '') {
            return null;
        }

        return new DateTimeImmutable($date);
    }

    private function extrait(string $html, int $limite): string
    {
        $texte = trim((string) preg_replace('/\s+/', ' ', strip_tags($html)));

        if ($texte === '') {
            return '';
        }

        if (function_exists('mb_strlen') && function_exists('mb_substr')) {
            if (mb_strlen($texte) <= $limite) {
                return $texte;
            }

            return rtrim(mb_substr($texte, 0, $limite - 1)) . '…';
        }

        if (strlen($texte) <= $limite) {
            return $texte;
        }

        return rtrim(substr($texte, 0, $limite - 1)) . '...';
    }
}
