<?php
/** @var array|null $partenaire */
/** @var array<string, string> $erreurs */
/** @var array<string, string> $valeurs */
$action = $partenaire === null
    ? '/admin/partenaires/creer'
    : '/admin/partenaires/' . (int) $partenaire['id'] . '/modifier';
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
        'nom' => 'nom',
        'libelle' => 'Nom du partenaire',
        'valeur' => $valeurs['nom'],
        'erreurs' => $erreurs,
        'obligatoire' => true,
        'autofocus' => true,
        'aide' => 'Exemple : « Ville de Noisy-le-Sec ».',
    ]);

    champ('texte', [
        'nom' => 'lien_url',
        'libelle' => 'Lien vers leur site',
        'valeur' => $valeurs['lien_url'],
        'erreurs' => $erreurs,
        'type' => 'url',
        'aide' => 'Facultatif. Le logo deviendra cliquable et mènera vers ce site. '
            . 'Vous pouvez écrire simplement « www.exemple.fr ».',
    ]);

    champ('image', [
        'nom' => 'logo',
        'libelle' => 'Logo',
        'erreurs' => $erreurs,
        'aide' => 'Formats acceptés : JPEG, PNG ou WebP, 5 Mo maximum. '
            . 'Un logo sur fond transparent (PNG) rend généralement mieux.',
        'image_actuelle' => $partenaire['logo_chemin'] ?? null,
        'nom_alt' => 'logo_alt',
        'valeur_alt' => $valeurs['logo_alt'],
        'libelle_alt' => 'Description du logo',
    ]);

    champ('choix', [
        'nom' => 'statut',
        'libelle' => 'Affichage sur le site',
        'valeur' => $valeurs['statut'],
        'erreurs' => $erreurs,
        'options' => [
            'publie' => 'Afficher ce partenaire',
            'brouillon' => 'Le masquer pour l\'instant',
        ],
        'descriptions' => [
            'publie' => 'Son logo apparaît sur la page des partenaires.',
            'brouillon' => 'Il reste enregistré ici, mais n\'apparaît pas sur le site.',
        ],
    ]);
    ?>

    <div class="admin-formulaire__actions">
        <button type="submit" class="admin-bouton admin-bouton--principal">Enregistrer</button>
        <a href="/admin/partenaires" class="admin-bouton">Annuler</a>
    </div>
</form>
