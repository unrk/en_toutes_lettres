<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;

final class TableauDeBordController extends ControleurAdmin
{
    public function index(Request $requete): void
    {
        $tuiles = [
            [
                'url' => '/admin/actualites',
                'icone' => '📰',
                'libelle' => 'Actualités et annonces',
                'description' => 'Raconter ce que fait l\'association, prévenir d\'un changement.',
            ],
            [
                'url' => '/admin/activites',
                'icone' => '📚',
                'libelle' => 'Activités',
                'description' => 'Les ateliers, les actions culturelles, La Cabane.',
            ],
            [
                'url' => '/admin/agenda',
                'icone' => '📅',
                'libelle' => 'Agenda',
                'description' => 'Les dates à venir : rencontres, fêtes, sorties.',
            ],
            [
                'url' => '/admin/galeries',
                'icone' => '🖼️',
                'libelle' => 'Galeries photos',
                'description' => 'Montrer en images ce qui se passe à l\'association.',
            ],
            [
                'url' => '/admin/partenaires',
                'icone' => '🤝',
                'libelle' => 'Partenaires',
                'description' => 'Les logos et les liens de ceux qui nous soutiennent.',
            ],
            [
                'url' => '/admin/pages',
                'icone' => '📄',
                'libelle' => 'Pages du site',
                'description' => '« À propos », « Contact », mentions légales.',
            ],
        ];

        if (Auth::estAdministrateur()) {
            $tuiles[] = [
                'url' => '/admin/comptes',
                'icone' => '👥',
                'libelle' => 'Comptes',
                'description' => 'Qui peut se connecter pour modifier le site.',
            ];
        }

        // Le prénom suffit pour dire bonjour, et un « Bonjour Marie » est plus
        // accueillant qu'un « Bonjour Marie Dupont ».
        $nomComplet = trim((string) Auth::utilisateur()['nom']);
        $prenom = explode(' ', $nomComplet)[0];

        Response::html(
            'admin/tableau_de_bord',
            ['titre' => 'Accueil', 'tuiles' => $tuiles, 'prenom' => $prenom],
            200,
            'admin/gabarit'
        );
    }
}
