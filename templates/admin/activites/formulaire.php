<?php
/** @var array|null $activite */
/** @var array<string, string> $erreurs */
/** @var array<string, string> $valeurs */
$action = $activite === null
    ? '/admin/activites/creer'
    : '/admin/activites/' . (int) $activite['id'] . '/modifier';
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
        'libelle' => "Nom de l'activité",
        'valeur' => $valeurs['titre'],
        'erreurs' => $erreurs,
        'obligatoire' => true,
        'autofocus' => true,
        'aide' => 'Exemple : « Les ateliers sociaux linguistiques ».',
    ]);

    champ('texte_long', [
        'nom' => 'resume',
        'libelle' => 'Résumé',
        'valeur' => $valeurs['resume'],
        'erreurs' => $erreurs,
        'lignes' => 2,
        'aide' => 'Une ou deux phrases qui donnent envie d\'en savoir plus. '
            . 'C\'est ce texte qui s\'affichera dans la liste des activités.',
    ]);

    champ('editeur', [
        'nom' => 'description',
        'libelle' => 'Description complète',
        'valeur' => $valeurs['description'],
        'erreurs' => $erreurs,
        'obligatoire' => true,
        'aide' => 'Présentez l\'activité : à quoi elle sert, comment elle se déroule, avec qui.',
    ]);

    champ('texte_long', [
        'nom' => 'creneaux',
        'libelle' => 'Jours et horaires',
        'valeur' => $valeurs['creneaux'],
        'erreurs' => $erreurs,
        'lignes' => 3,
        'aide' => 'Exemple : « Le matin, deux heures deux fois par semaine. Un cours en soirée est également proposé. »',
    ]);

    champ('texte', [
        'nom' => 'lieu',
        'libelle' => 'Lieu',
        'valeur' => $valeurs['lieu'],
        'erreurs' => $erreurs,
        'aide' => 'Exemple : « 1 place du Maréchal Foch, 93130 Noisy-le-Sec ».',
    ]);

    champ('texte', [
        'nom' => 'public_vise',
        'libelle' => 'Pour qui ?',
        'valeur' => $valeurs['public_vise'],
        'erreurs' => $erreurs,
        'aide' => 'Exemple : « Toute personne souhaitant apprendre ou améliorer son français ».',
    ]);

    champ('texte', [
        'nom' => 'tarif',
        'libelle' => 'Tarif',
        'valeur' => $valeurs['tarif'],
        'erreurs' => $erreurs,
        'aide' => 'Exemple : « 21 € par an », ou « Gratuit ».',
    ]);

    champ('texte_long', [
        'nom' => 'inscriptions',
        'libelle' => 'Comment s\'inscrire',
        'valeur' => $valeurs['inscriptions'],
        'erreurs' => $erreurs,
        'lignes' => 3,
        'aide' => 'Exemple : « Pré-inscriptions en juin, inscriptions en septembre ».',
    ]);

    champ('image', [
        'nom' => 'image',
        'libelle' => 'Photo de l\'activité',
        'erreurs' => $erreurs,
        'aide' => 'Formats acceptés : JPEG, PNG ou WebP, 5 Mo maximum. '
            . 'L\'image est automatiquement redimensionnée.',
        'image_actuelle' => $activite['image_chemin'] ?? null,
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
            'brouillon' => 'Personne d\'autre que l\'équipe ne la verra.',
            'publie' => 'Visible par tout le monde sur le site.',
        ],
    ]);
    ?>

    <div class="admin-formulaire__actions">
        <button type="submit" class="admin-bouton admin-bouton--principal">Enregistrer</button>
        <a href="/admin/activites" class="admin-bouton">Annuler</a>
        <?php if ($activite !== null): ?>
            <a href="/admin/activites/<?= (int) $activite['id'] ?>/apercu" class="admin-bouton">Voir l'aperçu</a>
        <?php endif; ?>
    </div>
</form>

<script src="/assets/js/editeur-enrichi.js" defer></script>
