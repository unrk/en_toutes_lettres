<?php
/** @var array $galerie */
/** @var array $photos */
/** @var array<string, string> $erreurs */
/** @var array<string, string> $valeurs */
/** @var int $photos_ajoutees */
/** @var array<int, string> $erreurs_photos */
$idGalerie = (int) $galerie['id'];
$dernierIndice = count($photos) - 1;
$sansDescription = array_filter($photos, static fn (array $p): bool => trim($p['alt']) === '');

// Les boutons « monter / descendre / retirer » vivent visuellement dans la
// carte de chaque photo, mais un formulaire ne peut pas en contenir un autre.
// L'attribut « form » de HTML5 permet à un bouton de déclencher un formulaire
// déclaré ailleurs dans la page : les formulaires correspondants sont donc
// regroupés tout en bas. Aucun JavaScript n'est nécessaire.
?>
<h1><?= e($galerie['titre']) ?></h1>

<?php if ($photos_ajoutees > 0): ?>
    <p class="admin-message admin-message--succes" role="status">
        <?= $photos_ajoutees === 1 ? '1 photo ajoutée' : $photos_ajoutees . ' photos ajoutées' ?>.
        Décrivez-les ci-dessous avant de mettre la galerie en ligne.
    </p>
<?php endif; ?>

<?php if ($erreurs_photos !== []): ?>
    <div class="admin-message admin-message--erreur" role="alert">
        <p>Certaines photos n'ont pas pu être ajoutées :</p>
        <ul>
            <?php foreach ($erreurs_photos as $messageErreur): ?>
                <li><?= e($messageErreur) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<section class="admin-section">
    <h2>Ajouter des photos</h2>

    <form method="post"
          action="/admin/galeries/<?= $idGalerie ?>/photos"
          enctype="multipart/form-data"
          class="admin-formulaire">
        <?= \App\Core\Csrf::champ() ?>

        <div class="admin-champ">
            <label for="champ_photos">Choisir des photos</label>
            <p class="admin-champ__aide" id="champ_photos_aide">
                Vous pouvez en sélectionner plusieurs à la fois. Formats acceptés :
                JPEG, PNG ou WebP, 5 Mo maximum par photo. Elles sont
                automatiquement redimensionnées.
            </p>
            <input type="file"
                   id="champ_photos"
                   name="photos[]"
                   multiple
                   accept="image/jpeg,image/png,image/webp"
                   aria-describedby="champ_photos_aide">
        </div>

        <div class="admin-formulaire__actions">
            <button type="submit" class="admin-bouton admin-bouton--principal">Envoyer ces photos</button>
        </div>
    </form>
</section>

<?php if ($sansDescription !== []): ?>
    <p class="admin-message admin-message--attention">
        <?php $nombreManquant = count($sansDescription); ?>
        <?= $nombreManquant === 1
            ? 'Une photo attend encore sa description.'
            : $nombreManquant . ' photos attendent encore leur description.' ?>
        Cette description est lue à voix haute aux personnes qui ne peuvent pas
        voir l'image : la galerie ne pourra pas être mise en ligne tant qu'il en
        manque une.
    </p>
<?php endif; ?>

<form method="post" action="/admin/galeries/<?= $idGalerie ?>/modifier" class="admin-formulaire" id="formulaire_galerie">
    <?= \App\Core\Csrf::champ() ?>

    <section class="admin-section">
        <h2>Photos de la galerie</h2>

        <?php if ($photos === []): ?>
            <p class="admin-vide">Cette galerie ne contient encore aucune photo.</p>
        <?php else: ?>
            <ul class="admin-photos">
                <?php foreach ($photos as $indice => $photo): ?>
                    <?php
                    $idPhoto = (int) $photo['id'];
                    $manque = trim($photo['alt']) === '';
                    ?>
                    <li class="admin-photo<?= $manque ? ' admin-photo--incomplete' : '' ?>">
                        <img src="/<?= e($photo['chemin']) ?>"
                             alt="<?= e($photo['alt']) ?>"
                             class="admin-photo__image">

                        <div class="admin-photo__champs">
                            <label for="champ_alt_<?= $idPhoto ?>">
                                Description
                                <?php if ($manque): ?>
                                    <span class="admin-champ__requis">(à compléter)</span>
                                <?php endif; ?>
                            </label>
                            <input type="text"
                                   id="champ_alt_<?= $idPhoto ?>"
                                   name="alt[<?= $idPhoto ?>]"
                                   value="<?= e($photo['alt']) ?>"
                                   placeholder="Exemple : des enfants écoutent une lecture sous un arbre"
                                   <?= $manque ? 'aria-invalid="true"' : '' ?>>
                        </div>

                        <div class="admin-photo__actions">
                            <button type="submit"
                                    form="photo_monter_<?= $idPhoto ?>"
                                    class="admin-bouton admin-bouton--discret"
                                    title="Monter d'un cran"
                                    <?= $indice === 0 ? 'disabled' : '' ?>>
                                <span aria-hidden="true">▲</span><span class="admin-invisible"> Monter</span>
                            </button>
                            <button type="submit"
                                    form="photo_descendre_<?= $idPhoto ?>"
                                    class="admin-bouton admin-bouton--discret"
                                    title="Descendre d'un cran"
                                    <?= $indice === $dernierIndice ? 'disabled' : '' ?>>
                                <span aria-hidden="true">▼</span><span class="admin-invisible"> Descendre</span>
                            </button>
                            <button type="submit"
                                    form="photo_retirer_<?= $idPhoto ?>"
                                    class="admin-bouton admin-bouton--danger">
                                Retirer
                            </button>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>

    <section class="admin-section">
        <h2>Réglages de la galerie</h2>

        <?php if (!empty($erreurs['general'])): ?>
            <p class="admin-message admin-message--erreur" role="alert"><?= e($erreurs['general']) ?></p>
        <?php endif; ?>

        <?php
        champ('texte', [
            'nom' => 'titre',
            'libelle' => 'Nom de la galerie',
            'valeur' => $valeurs['titre'],
            'erreurs' => $erreurs,
            'obligatoire' => true,
        ]);

        champ('editeur', [
            'nom' => 'description',
            'libelle' => 'Description',
            'valeur' => $valeurs['description'],
            'erreurs' => $erreurs,
            'aide' => 'Facultatif. Quelques mots pour présenter ces photos.',
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
    </section>

    <div class="admin-formulaire__actions">
        <button type="submit" class="admin-bouton admin-bouton--principal">
            Enregistrer les descriptions et les réglages
        </button>
        <a href="/admin/galeries" class="admin-bouton">Retour aux galeries</a>
    </div>
</form>

<?php /* Formulaires déclenchés par les boutons des cartes ci-dessus. */ ?>
<?php foreach ($photos as $photo): ?>
    <?php $idPhoto = (int) $photo['id']; ?>
    <form method="post"
          id="photo_monter_<?= $idPhoto ?>"
          action="/admin/galeries/<?= $idGalerie ?>/photos/<?= $idPhoto ?>/monter"
          class="admin-invisible"><?= \App\Core\Csrf::champ() ?></form>
    <form method="post"
          id="photo_descendre_<?= $idPhoto ?>"
          action="/admin/galeries/<?= $idGalerie ?>/photos/<?= $idPhoto ?>/descendre"
          class="admin-invisible"><?= \App\Core\Csrf::champ() ?></form>
    <form method="post"
          id="photo_retirer_<?= $idPhoto ?>"
          action="/admin/galeries/<?= $idGalerie ?>/photos/<?= $idPhoto ?>/supprimer"
          class="admin-invisible"
          data-confirmation="Retirer définitivement « <?= e(trim($photo['alt']) !== '' ? $photo['alt'] : 'cette photo') ?> » de la galerie ? Cette action est définitive."><?= \App\Core\Csrf::champ() ?></form>
<?php endforeach; ?>

<script src="/assets/js/editeur-enrichi.js" defer></script>
