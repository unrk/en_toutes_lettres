<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Response;

final class HomeController
{
    public function accueil(): void
    {
        Response::html('home', [
            'titre' => 'Accueil',
        ]);
    }
}
