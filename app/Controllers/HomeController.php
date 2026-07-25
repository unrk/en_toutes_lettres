<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;

final class HomeController
{
    public function accueil(Request $requete): void
    {
        Response::html('home', [
            'titre' => 'Accueil',
        ]);
    }
}
