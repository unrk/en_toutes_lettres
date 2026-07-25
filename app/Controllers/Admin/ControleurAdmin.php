<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Response;

abstract class ControleurAdmin
{
    public function __construct()
    {
        if (!Auth::estConnecte()) {
            Response::rediriger('/admin/connexion');
        }
    }

    protected function exigerAdministrateur(): void
    {
        if (!Auth::estAdministrateur()) {
            Response::html('erreurs/403', ['titre' => 'Accès refusé'], 403, 'admin/gabarit');
            exit;
        }
    }
}
