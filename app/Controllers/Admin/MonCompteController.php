<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Core\Validateur;
use App\Repositories\UtilisateurRepository;

/**
 * « Mon mot de passe » : accessible à tous les rôles, pour son propre compte
 * uniquement. Le mot de passe actuel est redemandé avant tout changement, afin
 * qu'un poste laissé ouvert ne permette pas de verrouiller le compte de
 * quelqu'un d'autre.
 */
final class MonCompteController extends ControleurAdmin
{
    private const LONGUEUR_MINIMALE = 10;

    public function formulaire(Request $requete): void
    {
        $this->afficher([]);
    }

    public function enregistrer(Request $requete): void
    {
        $v = new Validateur();

        if (!Csrf::valide($requete->post('jeton_csrf'))) {
            $v->ajouterErreur('general', "Votre session a expiré. Réessayez.");
        }

        $actuel = (string) $requete->post('mot_de_passe_actuel', '');
        $nouveau = (string) $requete->post('mot_de_passe', '');
        $confirmation = (string) $requete->post('mot_de_passe_confirmation', '');

        $utilisateur = UtilisateurRepository::trouve((int) Auth::utilisateur()['id']);

        if ($utilisateur === null || !password_verify($actuel, $utilisateur['mot_de_passe_hash'])) {
            $v->ajouterErreur('mot_de_passe_actuel', "Votre mot de passe actuel n'est pas correct.");
        }

        if (mb_strlen($nouveau) < self::LONGUEUR_MINIMALE) {
            $v->ajouterErreur(
                'mot_de_passe',
                "Le nouveau mot de passe doit contenir au moins " . self::LONGUEUR_MINIMALE
                . " caractères. Une phrase facile à retenir fait un très bon mot de passe, "
                . "par exemple « la cabane ouvre à 14 heures »."
            );
        } elseif ($nouveau !== $confirmation) {
            $v->ajouterErreur('mot_de_passe_confirmation', "Les deux mots de passe saisis ne sont pas identiques.");
        }

        if ($v->aDesErreurs()) {
            $this->afficher($v->erreurs(), 422);
            return;
        }

        UtilisateurRepository::changerMotDePasse((int) $utilisateur['id'], $nouveau);

        $this->afficher([], 200, true);
    }

    private function afficher(array $erreurs, int $codeStatut = 200, bool $succes = false): void
    {
        Response::html(
            'admin/mon_compte',
            ['titre' => 'Mon mot de passe', 'erreurs' => $erreurs, 'succes' => $succes],
            $codeStatut,
            'admin/gabarit'
        );
    }
}
