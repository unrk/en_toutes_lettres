<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Repositories\UtilisateurRepository;

final class AuthController
{
    public function formulaire(Request $requete): void
    {
        if (Auth::estConnecte()) {
            Response::rediriger('/admin');
        }

        Response::html('admin/connexion', ['titre' => 'Connexion'], 200, 'admin/gabarit_connexion');
    }

    public function traiter(Request $requete): void
    {
        $email = trim((string) $requete->post('email', ''));

        if (!Csrf::valide($requete->post('jeton_csrf'))) {
            Response::html(
                'admin/connexion',
                [
                    'titre' => 'Connexion',
                    'erreur' => "Votre session a expiré, merci de réessayer.",
                    'email' => $email,
                ],
                400,
                'admin/gabarit_connexion'
            );
            return;
        }

        $motDePasse = (string) $requete->post('mot_de_passe', '');
        $utilisateur = $email !== '' ? UtilisateurRepository::parEmail($email) : null;

        $identifiantsValides = $utilisateur !== null
            && (bool) $utilisateur['actif']
            && password_verify($motDePasse, $utilisateur['mot_de_passe_hash']);

        if (!$identifiantsValides) {
            Response::html(
                'admin/connexion',
                [
                    'titre' => 'Connexion',
                    'erreur' => "Adresse e-mail ou mot de passe incorrect.",
                    'email' => $email,
                ],
                401,
                'admin/gabarit_connexion'
            );
            return;
        }

        Auth::connecte($utilisateur);
        Response::rediriger('/admin');
    }

    public function deconnexion(Request $requete): void
    {
        if (Csrf::valide($requete->post('jeton_csrf'))) {
            Auth::deconnecte();
        }

        Response::rediriger('/admin/connexion');
    }
}
