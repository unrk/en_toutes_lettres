<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Response;
use App\Repositories\UtilisateurRepository;

abstract class ControleurAdmin
{
    public function __construct()
    {
        if (!Auth::estConnecte()) {
            Response::rediriger('/admin/connexion');
        }

        $this->verifierQueLeCompteEstToujoursValide();
    }

    /**
     * Le rôle et l'état du compte sont relus en base à chaque requête plutôt
     * que crus sur parole depuis la session.
     *
     * Sans cela, désactiver un compte n'aurait aucun effet tant que la personne
     * reste connectée : elle continuerait à travailler jusqu'à expiration de sa
     * session. Or on désactive justement un compte quand on veut que ça
     * s'arrête tout de suite. Même raisonnement pour un changement de rôle.
     */
    private function verifierQueLeCompteEstToujoursValide(): void
    {
        $enSession = Auth::utilisateur();
        $enBase = UtilisateurRepository::trouve((int) $enSession['id']);

        if ($enBase === null || !(bool) $enBase['actif']) {
            Auth::deconnecte();
            Response::rediriger('/admin/connexion?compte=desactive');
        }

        if ($enBase['role'] !== $enSession['role'] || $enBase['nom'] !== $enSession['nom']) {
            Auth::rafraichir($enBase);
        }
    }

    protected function exigerAdministrateur(): void
    {
        if (!Auth::estAdministrateur()) {
            Response::html(
                'erreurs/403',
                ['titre' => 'Accès refusé'],
                403,
                'admin/gabarit'
            );
            exit;
        }
    }
}
