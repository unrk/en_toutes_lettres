<?php
/** @var array<string, string> $erreurs */
/** @var array<string, string> $valeurs */
?>
<h1>Créer une galerie</h1>

<p class="admin-champ__aide">
    Donnez d'abord un nom à votre galerie. Vous pourrez ensuite y ajouter vos
    photos.
</p>

<?php if (!empty($erreurs['general'])): ?>
    <p class="admin-message admin-message--erreur" role="alert"><?= e($erreurs['general']) ?></p>
<?php endif; ?>

<form method="post" action="/admin/galeries/creer" class="admin-formulaire">
    <?= \App\Core\Csrf::champ() ?>

    <?php
    champ('texte', [
        'nom' => 'titre',
        'libelle' => 'Nom de la galerie',
        'valeur' => $valeurs['titre'],
        'erreurs' => $erreurs,
        'obligatoire' => true,
        'autofocus' => true,
        'aide' => 'Exemple : « Fête de quartier 2026 ».',
    ]);

    champ('editeur', [
        'nom' => 'description',
        'libelle' => 'Description',
        'valeur' => $valeurs['description'],
        'erreurs' => $erreurs,
        'aide' => 'Facultatif. Quelques mots pour présenter ces photos.',
    ]);
    ?>

    <div class="admin-formulaire__actions">
        <button type="submit" class="admin-bouton admin-bouton--principal">
            Créer et ajouter des photos
        </button>
        <a href="/admin/galeries" class="admin-bouton">Annuler</a>
    </div>
</form>

<script src="/assets/js/editeur-enrichi.js" defer></script>
