<?php
/** @var array|null $actualite */
/** @var array<string, string> $erreurs */
/** @var array<string, string> $valeurs */
?>
<h1><?= htmlspecialchars($titre, ENT_QUOTES, 'UTF-8') ?></h1>

<?php if (!empty($erreurs['general'])): ?>
    <p class="admin-message admin-message--erreur"><?= htmlspecialchars($erreurs['general'], ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<form method="post"
      action="<?= $actualite === null ? '/admin/actualites/creer' : '/admin/actualites/' . (int) $actualite['id'] . '/modifier' ?>"
      enctype="multipart/form-data"
      class="admin-formulaire admin-formulaire--actualite">
    <?= \App\Core\Csrf::champ() ?>

    <label for="champ_titre">Titre</label>
    <input type="text" id="champ_titre" name="titre" value="<?= htmlspecialchars($valeurs['titre'], ENT_QUOTES, 'UTF-8') ?>" required>
    <?php if (!empty($erreurs['titre'])): ?>
        <p class="admin-message admin-message--erreur"><?= htmlspecialchars($erreurs['titre'], ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <label for="champ_contenu_visible">Contenu</label>
    <div class="admin-editeur__barre" data-barre-pour="champ_contenu_visible">
        <button type="button" data-commande="bold">Gras</button>
        <button type="button" data-commande="italic">Italique</button>
        <button type="button" data-commande="formatBlock" data-valeur="H2">Titre</button>
        <button type="button" data-commande="formatBlock" data-valeur="H3">Sous-titre</button>
        <button type="button" data-commande="insertUnorderedList">Liste à puces</button>
        <button type="button" data-commande="insertOrderedList">Liste numérotée</button>
        <button type="button" data-commande="createLink">Lien</button>
    </div>
    <div id="champ_contenu_visible"
         class="admin-editeur"
         contenteditable="true"
         data-editeur-enrichi
         data-cible="champ_contenu_cache"><?= $valeurs['contenu'] ?></div>
    <textarea id="champ_contenu_cache" name="contenu" class="admin-champ-cache"><?= $valeurs['contenu'] ?></textarea>
    <?php if (!empty($erreurs['contenu'])): ?>
        <p class="admin-message admin-message--erreur"><?= htmlspecialchars($erreurs['contenu'], ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <label for="champ_image">Image de couverture (JPEG, PNG ou WebP, 5 Mo maximum)</label>
    <?php if ($actualite !== null && !empty($actualite['image_chemin'])): ?>
        <p>
            <img src="/<?= htmlspecialchars($actualite['image_chemin'], ENT_QUOTES, 'UTF-8') ?>" alt="" class="admin-image-actuelle">
            <label class="admin-case">
                <input type="checkbox" name="supprimer_image" value="1"> Retirer cette image
            </label>
        </p>
    <?php endif; ?>
    <input type="file" id="champ_image" name="image" accept="image/jpeg,image/png,image/webp">
    <?php if (!empty($erreurs['image'])): ?>
        <p class="admin-message admin-message--erreur"><?= htmlspecialchars($erreurs['image'], ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <label for="champ_image_alt">Texte alternatif de l'image (décrit l'image pour les personnes malvoyantes ; obligatoire si une image est présente)</label>
    <input type="text" id="champ_image_alt" name="image_alt" value="<?= htmlspecialchars($valeurs['image_alt'], ENT_QUOTES, 'UTF-8') ?>">
    <?php if (!empty($erreurs['image_alt'])): ?>
        <p class="admin-message admin-message--erreur"><?= htmlspecialchars($erreurs['image_alt'], ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <fieldset class="admin-statuts">
        <legend>Statut</legend>
        <label class="admin-case">
            <input type="radio" name="statut" value="brouillon" <?= $valeurs['statut'] === 'brouillon' ? 'checked' : '' ?>>
            Brouillon (non visible du public)
        </label>
        <label class="admin-case">
            <input type="radio" name="statut" value="publie" <?= $valeurs['statut'] === 'publie' ? 'checked' : '' ?>>
            Publié immédiatement
        </label>
        <label class="admin-case">
            <input type="radio" name="statut" value="programme" <?= $valeurs['statut'] === 'programme' ? 'checked' : '' ?>>
            Programmé à une date précise
        </label>
        <?php if (!empty($erreurs['statut'])): ?>
            <p class="admin-message admin-message--erreur"><?= htmlspecialchars($erreurs['statut'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </fieldset>

    <label for="champ_publie_le">Date de publication programmée (uniquement si « Programmé » est coché ci-dessus)</label>
    <input type="datetime-local" id="champ_publie_le" name="publie_le" value="<?= htmlspecialchars($valeurs['publie_le'], ENT_QUOTES, 'UTF-8') ?>">
    <?php if (!empty($erreurs['publie_le'])): ?>
        <p class="admin-message admin-message--erreur"><?= htmlspecialchars($erreurs['publie_le'], ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <div class="admin-formulaire__actions">
        <button type="submit" class="admin-bouton admin-bouton--principal">Enregistrer</button>
        <a href="/admin/actualites" class="admin-bouton">Annuler</a>
        <?php if ($actualite !== null): ?>
            <a href="/admin/actualites/<?= (int) $actualite['id'] ?>/apercu" class="admin-bouton">Voir l'aperçu</a>
        <?php endif; ?>
    </div>
</form>

<script src="/assets/js/editeur-enrichi.js" defer></script>
