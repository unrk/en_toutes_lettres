<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\NotFoundException;
use App\Core\Request;
use App\Core\Response;
use App\Core\Validateur;
use App\Repositories\UtilisateurRepository;

/**
 * Gestion des comptes du back-office. Réservée aux administrateurs.
 *
 * Trois garde-fous, tous vérifiés côté serveur : on ne se désactive pas
 * soi-même, on ne se retire pas soi-même le rôle d'administrateur, et on ne
 * désactive pas le dernier administrateur actif. Sans eux, une seule fausse
 * manipulation pourrait fermer définitivement l'accès au site à toute
 * l'association — et personne ne serait là pour rouvrir la porte.
 */
final class CompteController extends ControleurAdmin
{
    private const ROLES = ['administrateur', 'redacteur'];
    private const LONGUEUR_MINIMALE_MOT_DE_PASSE = 10;

    public function __construct()
    {
        parent::__construct();
        $this->exigerAdministrateur();
    }

    public function liste(Request $requete): void
    {
        Response::html(
            'admin/comptes/liste',
            [
                'titre' => 'Comptes',
                'comptes' => UtilisateurRepository::tous(),
                'moi' => Auth::utilisateur()['id'],
            ],
            200,
            'admin/gabarit'
        );
    }

    public function creer(Request $requete): void
    {
        $this->afficherFormulaire('Ajouter un compte', null, ['nom' => '', 'email' => '', 'role' => 'redacteur'], []);
    }

    public function enregistrerNouveau(Request $requete): void
    {
        $valeurs = [
            'nom' => trim((string) $requete->post('nom', '')),
            'email' => trim((string) $requete->post('email', '')),
            'role' => (string) $requete->post('role', 'redacteur'),
        ];

        $v = new Validateur();

        if (!Csrf::valide($requete->post('jeton_csrf'))) {
            $v->ajouterErreur('general', "Votre session a expiré. Vérifiez vos informations puis réessayez.");
        }

        $nom = $v->texte('nom', $valeurs['nom'], 'Le nom', min: 2, max: 100);
        $email = $v->email('email', $valeurs['email'], "L'adresse e-mail");
        $role = $v->choix('role', $valeurs['role'], 'Le rôle', self::ROLES, 'redacteur');
        $motDePasse = $this->validerMotDePasse($v, $requete);

        if ($email !== '' && UtilisateurRepository::emailDejaPris($email)) {
            $v->ajouterErreur('email', "Un compte utilise déjà cette adresse e-mail.");
        }

        if ($v->aDesErreurs()) {
            $this->afficherFormulaire('Ajouter un compte', null, $valeurs, $v->erreurs(), 422);
            return;
        }

        UtilisateurRepository::creer($nom, $email, $motDePasse, $role);

        Response::rediriger('/admin/comptes');
    }

    public function modifier(Request $requete, string $id): void
    {
        $compte = $this->trouverOuEchouer($id);

        $this->afficherFormulaire(
            'Modifier le compte',
            $compte,
            ['nom' => $compte['nom'], 'email' => $compte['email'], 'role' => $compte['role']],
            []
        );
    }

    public function enregistrerModification(Request $requete, string $id): void
    {
        $compte = $this->trouverOuEchouer($id);

        $valeurs = [
            'nom' => trim((string) $requete->post('nom', '')),
            'email' => trim((string) $requete->post('email', '')),
            'role' => (string) $requete->post('role', 'redacteur'),
        ];

        $v = new Validateur();

        if (!Csrf::valide($requete->post('jeton_csrf'))) {
            $v->ajouterErreur('general', "Votre session a expiré. Vérifiez vos informations puis réessayez.");
        }

        $nom = $v->texte('nom', $valeurs['nom'], 'Le nom', min: 2, max: 100);
        $email = $v->email('email', $valeurs['email'], "L'adresse e-mail");
        $role = $v->choix('role', $valeurs['role'], 'Le rôle', self::ROLES, 'redacteur');

        if ($email !== '' && UtilisateurRepository::emailDejaPris($email, (int) $id)) {
            $v->ajouterErreur('email', "Un autre compte utilise déjà cette adresse e-mail.");
        }

        $estMoi = (int) $id === (int) Auth::utilisateur()['id'];

        if ($estMoi && $role !== 'administrateur') {
            $v->ajouterErreur(
                'role',
                "Vous ne pouvez pas retirer votre propre rôle d'administrateur : "
                . "vous perdriez l'accès à cette page. Demandez à un autre administrateur de le faire."
            );
        }

        if (
            !$estMoi
            && $compte['role'] === 'administrateur'
            && $role !== 'administrateur'
            && UtilisateurRepository::nombreAdministrateursActifs() <= 1
        ) {
            $v->ajouterErreur('role', "C'est le dernier administrateur du site : son rôle ne peut pas être changé.");
        }

        // Le mot de passe n'est renseigné que si l'on souhaite le remplacer.
        $nouveauMotDePasse = (string) $requete->post('mot_de_passe', '');
        if ($nouveauMotDePasse !== '') {
            $this->validerMotDePasse($v, $requete);
        }

        if ($v->aDesErreurs()) {
            $this->afficherFormulaire('Modifier le compte', $compte, $valeurs, $v->erreurs(), 422);
            return;
        }

        UtilisateurRepository::modifier((int) $id, $nom, $email, $role);

        if ($nouveauMotDePasse !== '') {
            UtilisateurRepository::changerMotDePasse((int) $id, $nouveauMotDePasse);
        }

        Response::rediriger('/admin/comptes');
    }

    public function desactiver(Request $requete, string $id): void
    {
        if (!Csrf::valide($requete->post('jeton_csrf'))) {
            Response::rediriger('/admin/comptes');
            return;
        }

        $compte = $this->trouverOuEchouer($id);
        $motif = $this->motifDeRefusDeDesactivation($compte);

        if ($motif !== null) {
            Response::html(
                'admin/comptes/refus',
                ['titre' => 'Action impossible', 'motif' => $motif],
                403,
                'admin/gabarit'
            );
            return;
        }

        UtilisateurRepository::changerActivation((int) $id, false);

        Response::rediriger('/admin/comptes');
    }

    public function reactiver(Request $requete, string $id): void
    {
        if (Csrf::valide($requete->post('jeton_csrf'))) {
            $this->trouverOuEchouer($id);
            UtilisateurRepository::changerActivation((int) $id, true);
        }

        Response::rediriger('/admin/comptes');
    }

    private function motifDeRefusDeDesactivation(array $compte): ?string
    {
        if ((int) $compte['id'] === (int) Auth::utilisateur()['id']) {
            return "Vous ne pouvez pas désactiver votre propre compte : vous seriez "
                . "immédiatement déconnecté et ne pourriez plus revenir. Demandez à un "
                . "autre administrateur de le faire.";
        }

        if (
            $compte['role'] === 'administrateur'
            && (bool) $compte['actif']
            && UtilisateurRepository::nombreAdministrateursActifs() <= 1
        ) {
            return "C'est le dernier administrateur encore actif. Le désactiver "
                . "fermerait à tout le monde l'accès à la gestion des comptes. "
                . "Créez d'abord un autre administrateur.";
        }

        return null;
    }

    private function validerMotDePasse(Validateur $v, Request $requete): string
    {
        $motDePasse = (string) $requete->post('mot_de_passe', '');
        $confirmation = (string) $requete->post('mot_de_passe_confirmation', '');

        if (mb_strlen($motDePasse) < self::LONGUEUR_MINIMALE_MOT_DE_PASSE) {
            $v->ajouterErreur(
                'mot_de_passe',
                "Le mot de passe doit contenir au moins " . self::LONGUEUR_MINIMALE_MOT_DE_PASSE
                . " caractères. Une phrase facile à retenir fait un très bon mot de passe, "
                . "par exemple « la cabane ouvre à 14 heures »."
            );
        } elseif ($motDePasse !== $confirmation) {
            $v->ajouterErreur('mot_de_passe_confirmation', "Les deux mots de passe saisis ne sont pas identiques.");
        }

        return $motDePasse;
    }

    private function trouverOuEchouer(string $id): array
    {
        $compte = UtilisateurRepository::trouve((int) $id);

        if ($compte === null) {
            throw new NotFoundException("Compte {$id} introuvable.");
        }

        return $compte;
    }

    private function afficherFormulaire(
        string $titrePage,
        ?array $compte,
        array $valeurs,
        array $erreurs,
        int $codeStatut = 200,
    ): void {
        Response::html(
            'admin/comptes/formulaire',
            [
                'titre' => $titrePage,
                'compte' => $compte,
                'valeurs' => $valeurs,
                'erreurs' => $erreurs,
            ],
            $codeStatut,
            'admin/gabarit'
        );
    }
}
