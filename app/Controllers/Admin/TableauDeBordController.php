<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;

final class TableauDeBordController extends ControleurAdmin
{
    public function index(Request $requete): void
    {
        Response::html('admin/tableau_de_bord', ['titre' => 'Tableau de bord'], 200, 'admin/gabarit');
    }
}
