<?php
/** @var array|null $compte */
/** @var array<string, string> $erreurs */
/** @var array<string, string> $valeurs */
$creation = $compte === null;
$action = $creation ? '/admin/comptes/creer' : '/admin/comptes/' . (int) $compte['id'] . '/modifier';
?>
<h1><?= e($titre) ?></h1>

<?php if (!empty($erreurs['general'])): ?>
    <p class="admin-message admin-message--erreur" role="alert"><?= e($erreurs['general']) ?></p>
<?php elseif ($erreurs !== []): ?>
    <p class="admin-message admin-message--erreur" role="alert">
        Le compte n'a pas pu être enregistré. Corrigez les points signalés ci-dessous.
    </p>
<?php endif; ?>

<form method="post" action="<?= e($action) ?>" class="admin-formulaire" autocomplete="off">
    <?= \App\Core\Csrf::champ() ?>

    <?php
    champ('texte', [
        'nom' => 'nom',
        'libelle' => 'Nom et prénom',
        'valeur' => $valeurs['nom'],
        'erreurs' => $erreurs,
        'obligatoire' => true,
        'autofocus' => true,
        'aide' => 'Ce nom apparaîtra à côté des contenus que cette personne écrira.',
    ]);

    champ('texte', [
        'nom' => 'email',
        'libelle' => 'Adresse e-mail',
        'valeur' => $valeurs['email'],
        'erreurs' => $erreurs,
        'type' => 'email',
        'obligatoire' => true,
        'aide' => 'C\'est avec cette adresse que la personne se connectera.',
    ]);

    champ('choix', [
        'nom' => 'role',
        'libelle' => 'Rôle',
        'valeur' => $valeurs['role'],
        'erreurs' => $erreurs,
        'options' => [
            'redacteur' => 'Rédacteur',
            'administrateur' => 'Administrateur',
        ],
        'descriptions' => [
            'redacteur' => 'Peut créer et modifier tout le contenu du site.',
            'administrateur' => 'Peut en plus ajouter, modifier et désactiver des comptes.',
        ],
    ]);

    champ('texte', [
        'nom' => 'mot_de_passe',
        'libelle' => $creation ? 'Mot de passe' : 'Nouveau mot de passe',
        'valeur' => '',
        'erreurs' => $erreurs,
        'type' => 'password',
        'obligatoire' => $creation,
        'aide' => $creation
            ? 'Au moins 10 caractères. Une phrase facile à retenir fait un très bon '
                . 'mot de passe, par exemple « la cabane ouvre à 14 heures ». '
                . 'Transmettez-le à la personne, elle pourra le changer ensuite.'
            : 'Laissez ce champ vide pour conserver le mot de passe actuel.',
    ]);

    champ('texte', [
        'nom' => 'mot_de_passe_confirmation',
        'libelle' => 'Confirmer le mot de passe',
        'valeur' => '',
        'erreurs' => $erreurs,
        'type' => 'password',
        'obligatoire' => $creation,
        'aide' => 'Saisissez à nouveau le même mot de passe, pour éviter une faute de frappe.',
    ]);
    ?>

    <div class="admin-formulaire__actions">
        <button type="submit" class="admin-bouton admin-bouton--principal">Enregistrer</button>
        <a href="/admin/comptes" class="admin-bouton">Annuler</a>
    </div>
</form>
