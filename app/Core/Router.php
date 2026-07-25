<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    /** @var array<int, array{methode: string, motif: string, action: array{0: class-string, 1: string}}> */
    private array $routes = [];

    public function get(string $motif, array $action): void
    {
        $this->ajouter('GET', $motif, $action);
    }

    public function post(string $motif, array $action): void
    {
        $this->ajouter('POST', $motif, $action);
    }

    private function ajouter(string $methode, string $motif, array $action): void
    {
        $this->routes[] = [
            'methode' => $methode,
            'motif' => $motif,
            'action' => $action,
        ];
    }

    public function distribuer(Request $requete): void
    {
        foreach ($this->routes as $route) {
            if ($route['methode'] !== $requete->methode) {
                continue;
            }

            $parametres = $this->correspond($route['motif'], $requete->chemin);
            if ($parametres === null) {
                continue;
            }

            [$classe, $methode] = $route['action'];
            $controleur = new $classe();
            $controleur->$methode($requete, ...array_values($parametres));
            return;
        }

        throw new NotFoundException("Aucune route ne correspond à {$requete->chemin}");
    }

    /**
     * @return array<string, string>|null
     */
    private function correspond(string $motif, string $chemin): ?array
    {
        $regex = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $motif);
        $regex = '#^' . $regex . '$#';

        if (!preg_match($regex, $chemin, $correspondances)) {
            return null;
        }

        $parametres = [];
        foreach ($correspondances as $cle => $valeur) {
            if (is_string($cle)) {
                $parametres[$cle] = $valeur;
            }
        }

        return $parametres;
    }
}
