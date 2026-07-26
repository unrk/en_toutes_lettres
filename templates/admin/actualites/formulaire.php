<?php
/** @var array|null $actualite */
/** @var array<string, string> $erreurs */
/** @var array<string, string> $valeurs */
$action = $actualite === null
    ? '/admin/actualites/creer'
    : '/admin/actualites/' . (int) $actualite['id'] . '/modifier';
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
        'libelle' => 'Titre',
        'valeur' => $valeurs['titre'],
        'erreurs' => $erreurs,
        'obligatoire' => true,
        'autofocus' => true,
    ]);

    champ('choix', [
        'nom' => 'type',
        'libelle' => 'De quoi s\'agit-il ?',
        'valeur' => $valeurs['type'],
        'erreurs' => $erreurs,
        'options' => [
            'actualite' => 'Une actualité',
            'annonce' => 'Une annonce',
        ],
        'descriptions' => [
            'actualite' => 'Un article : ce que fait l\'association, un compte rendu, un témoignage.',
            'annonce' => 'Une information courte et pratique : fermeture, changement d\'horaire, appel aux bénévoles.',
        ],
    ]);

    champ('editeur', [
        'nom' => 'contenu',
        'libelle' => 'Contenu',
        'valeur' => $valeurs['contenu'],
        'erreurs' => $erreurs,
        'obligatoire' => true,
        'aide' => 'Utilisez les boutons ci-dessous pour mettre en forme votre texte.',
    ]);

    champ('image', [
        'nom' => 'image',
        'libelle' => 'Image de couverture',
        'erreurs' => $erreurs,
        'aide' => 'Formats acceptés : JPEG, PNG ou WebP, 5 Mo maximum. '
            . 'L\'image est automatiquement redimensionnée, vous n\'avez rien à faire.',
        'image_actuelle' => $actualite['image_chemin'] ?? null,
        'valeur_alt' => $valeurs['image_alt'],
    ]);

    champ('choix', [
        'nom' => 'statut',
        'libelle' => 'Mise en ligne',
        'valeur' => $valeurs['statut'],
        'erreurs' => $erreurs,
        'options' => [
            'brouillon' => 'Garder en brouillon',
            'publie' => 'Mettre en ligne maintenant',
            'programme' => 'Mettre en ligne à une date choisie',
        ],
        'descriptions' => [
            'brouillon' => 'Personne d\'autre que l\'équipe ne le verra.',
            'publie' => 'Visible immédiatement par tout le monde sur le site.',
            'programme' => 'Apparaîtra tout seul sur le site à la date indiquée ci-dessous.',
        ],
    ]);

    champ('date', [
        'nom' => 'publie_le',
        'libelle' => 'Date de mise en ligne',
        'valeur' => $valeurs['publie_le'],
        'erreurs' => $erreurs,
        'aide' => 'À remplir uniquement si vous avez choisi « Mettre en ligne à une date choisie ».',
    ]);
    ?>

    <div class="admin-formulaire__actions">
        <button type="submit" class="admin-bouton admin-bouton--principal">Enregistrer</button>
        <a href="/admin/actualites" class="admin-bouton">Annuler</a>
        <?php if ($actualite !== null): ?>
            <a href="/admin/actualites/<?= (int) $actualite['id'] ?>/apercu" class="admin-bouton">Voir l'aperçu</a>
        <?php endif; ?>
    </div>
</form>

<script src="/assets/js/editeur-enrichi.js" defer></script>
