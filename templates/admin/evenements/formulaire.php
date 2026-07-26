<?php
/** @var array|null $evenement */
/** @var array<string, string> $erreurs */
/** @var array<string, string> $valeurs */
$action = $evenement === null
    ? '/admin/agenda/creer'
    : '/admin/agenda/' . (int) $evenement['id'] . '/modifier';
?>
<h1><?= e($titre) ?></h1>

<?php if (!empty($erreurs['general'])): ?>
    <p class="admin-message admin-message--erreur" role="alert"><?= e($erreurs['general']) ?></p>
<?php elseif ($erreurs !== []): ?>
    <p class="admin-message admin-message--erreur" role="alert">
        Le formulaire n'a pas pu être enregistré. Corrigez les points signalés
        ci-dessous : rien de ce que vous avez écrit n'est perdu.
    </p>
<?php endif; ?>

<form method="post" action="<?= e($action) ?>" enctype="multipart/form-data" class="admin-formulaire">
    <?= \App\Core\Csrf::champ() ?>

    <?php
    champ('texte', [
        'nom' => 'titre',
        'libelle' => "Nom de l'événement",
        'valeur' => $valeurs['titre'],
        'erreurs' => $erreurs,
        'obligatoire' => true,
        'autofocus' => true,
        'aide' => 'Exemple : « Fête de fin d\'année », « Lecture publique à la médiathèque ».',
    ]);

    champ('date', [
        'nom' => 'debut',
        'libelle' => 'Début',
        'valeur' => $valeurs['debut'],
        'erreurs' => $erreurs,
        'obligatoire' => true,
        'aide' => 'Jour et heure auxquels l\'événement commence.',
    ]);

    champ('date', [
        'nom' => 'fin',
        'libelle' => 'Fin',
        'valeur' => $valeurs['fin'],
        'erreurs' => $erreurs,
        'aide' => 'Facultatif. À remplir si vous connaissez l\'heure de fin.',
    ]);

    champ('texte', [
        'nom' => 'lieu',
        'libelle' => 'Lieu',
        'valeur' => $valeurs['lieu'],
        'erreurs' => $erreurs,
        'aide' => 'Où cela se passe-t-il ? Exemple : « La Cabane, 1 place du Maréchal Foch ».',
    ]);

    champ('editeur', [
        'nom' => 'description',
        'libelle' => 'Description',
        'valeur' => $valeurs['description'],
        'erreurs' => $erreurs,
        'obligatoire' => true,
        'aide' => 'De quoi s\'agit-il ? Faut-il s\'inscrire ? Est-ce ouvert à tous ?',
    ]);

    champ('image', [
        'nom' => 'image',
        'libelle' => 'Affiche ou photo',
        'erreurs' => $erreurs,
        'aide' => 'Formats acceptés : JPEG, PNG ou WebP, 5 Mo maximum.',
        'image_actuelle' => $evenement['image_chemin'] ?? null,
        'valeur_alt' => $valeurs['image_alt'],
    ]);

    champ('choix', [
        'nom' => 'statut',
        'libelle' => 'Mise en ligne',
        'valeur' => $valeurs['statut'],
        'erreurs' => $erreurs,
        'options' => [
            'brouillon' => 'Garder en brouillon',
            'publie' => 'Mettre en ligne',
        ],
        'descriptions' => [
            'brouillon' => 'Personne d\'autre que l\'équipe ne le verra.',
            'publie' => 'Visible dans l\'agenda du site. Il en disparaîtra tout seul une fois la date passée.',
        ],
    ]);
    ?>

    <div class="admin-formulaire__actions">
        <button type="submit" class="admin-bouton admin-bouton--principal">Enregistrer</button>
        <a href="/admin/agenda" class="admin-bouton">Annuler</a>
        <?php if ($evenement !== null): ?>
            <a href="/admin/agenda/<?= (int) $evenement['id'] ?>/apercu" class="admin-bouton">Voir l'aperçu</a>
        <?php endif; ?>
    </div>
</form>

<script src="/assets/js/editeur-enrichi.js" defer></script>
