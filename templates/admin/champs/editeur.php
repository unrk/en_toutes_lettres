<?php
/**
 * Éditeur de texte enrichi (gras, italique, titres, listes, liens).
 * Options : nom, libelle, valeur, erreurs, aide, obligatoire.
 *
 * $valeur contient du HTML DÉJÀ passé par AssainisseurHtml : il est donc inséré
 * tel quel dans la zone d'édition (c'est le seul endroit où l'on n'échappe pas,
 * sinon les bénévoles verraient les balises au lieu de la mise en forme).
 * Dans le <textarea> en revanche on échappe : le navigateur décodera de
 * lui-même les entités au moment de l'envoi.
 */
$id = 'champ_' . $nom;
$idCache = $id . '_source';
$erreur = $erreurs[$nom] ?? null;
$idAide = $aide !== '' ? $id . '_aide' : null;
$idErreur = $erreur !== null ? $id . '_erreur' : null;
$decritPar = trim(($idAide ?? '') . ' ' . ($idErreur ?? ''));
?>
<div class="admin-champ">
    <label for="<?= e($id) ?>">
        <?= e($libelle) ?><?php if ($obligatoire): ?> <span class="admin-champ__requis">(obligatoire)</span><?php endif; ?>
    </label>

    <?php if ($aide !== ''): ?>
        <p class="admin-champ__aide" id="<?= e($idAide) ?>"><?= e($aide) ?></p>
    <?php endif; ?>

    <div class="admin-editeur__barre" data-barre-pour="<?= e($id) ?>" role="toolbar" aria-label="Mise en forme du texte">
        <button type="button" data-commande="bold" title="Mettre en gras"><strong>G</strong></button>
        <button type="button" data-commande="italic" title="Mettre en italique"><em>I</em></button>
        <button type="button" data-commande="formatBlock" data-valeur="H2">Titre</button>
        <button type="button" data-commande="formatBlock" data-valeur="H3">Sous-titre</button>
        <button type="button" data-commande="insertUnorderedList">Liste à puces</button>
        <button type="button" data-commande="insertOrderedList">Liste numérotée</button>
        <button type="button" data-commande="createLink">Lien</button>
        <button type="button" data-commande="removeFormat">Enlever la mise en forme</button>
    </div>

    <div id="<?= e($id) ?>"
         class="admin-editeur"
         contenteditable="true"
         role="textbox"
         aria-multiline="true"
         aria-label="<?= e($libelle) ?>"
         data-editeur-enrichi
         data-cible="<?= e($idCache) ?>"
         <?= $erreur !== null ? 'aria-invalid="true"' : '' ?>
         <?= $decritPar !== '' ? 'aria-describedby="' . e($decritPar) . '"' : '' ?>><?= $valeur ?></div>

    <textarea id="<?= e($idCache) ?>" name="<?= e($nom) ?>" class="admin-champ-cache" aria-hidden="true" tabindex="-1"><?= e((string) $valeur) ?></textarea>

    <?php if ($erreur !== null): ?>
        <p class="admin-message admin-message--erreur" id="<?= e($idErreur) ?>"><?= e($erreur) ?></p>
    <?php endif; ?>
</div>
