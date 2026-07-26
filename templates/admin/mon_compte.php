<?php
/** @var array<string, string> $erreurs */
/** @var bool $succes */
?>
<h1>Mon mot de passe</h1>

<?php if ($succes): ?>
    <p class="admin-message admin-message--succes" role="status">
        Votre mot de passe a bien été changé. Il vous servira à votre prochaine connexion.
    </p>
<?php endif; ?>

<?php if (!empty($erreurs['general'])): ?>
    <p class="admin-message admin-message--erreur" role="alert"><?= e($erreurs['general']) ?></p>
<?php endif; ?>

<form method="post" action="/admin/mon-mot-de-passe" class="admin-formulaire" autocomplete="off">
    <?= \App\Core\Csrf::champ() ?>

    <?php
    champ('texte', [
        'nom' => 'mot_de_passe_actuel',
        'libelle' => 'Mot de passe actuel',
        'valeur' => '',
        'erreurs' => $erreurs,
        'type' => 'password',
        'obligatoire' => true,
        'autofocus' => true,
        'aide' => 'Par sécurité, nous vous le redemandons avant tout changement.',
    ]);

    champ('texte', [
        'nom' => 'mot_de_passe',
        'libelle' => 'Nouveau mot de passe',
        'valeur' => '',
        'erreurs' => $erreurs,
        'type' => 'password',
        'obligatoire' => true,
        'aide' => 'Au moins 10 caractères. Une phrase facile à retenir fait un très bon '
            . 'mot de passe, par exemple « la cabane ouvre à 14 heures ».',
    ]);

    champ('texte', [
        'nom' => 'mot_de_passe_confirmation',
        'libelle' => 'Confirmer le nouveau mot de passe',
        'valeur' => '',
        'erreurs' => $erreurs,
        'type' => 'password',
        'obligatoire' => true,
        'aide' => 'Saisissez-le une seconde fois, pour éviter une faute de frappe.',
    ]);
    ?>

    <div class="admin-formulaire__actions">
        <button type="submit" class="admin-bouton admin-bouton--principal">Changer mon mot de passe</button>
        <a href="/admin" class="admin-bouton">Annuler</a>
    </div>
</form>
