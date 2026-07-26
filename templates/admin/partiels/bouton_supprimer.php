<?php
/**
 * Bouton d'action destructive, avec confirmation nommant explicitement ce qui
 * est en jeu.
 *
 * Centralisé ici pour que la règle « on cite toujours ce qui va disparaître »
 * ne puisse pas être oubliée dans une rubrique.
 *
 * Options : action (URL), nom (ce qui est visé), libelle (texte du bouton),
 *           verbe (début de la question), precision (conséquence énoncée).
 */
$libelle ??= 'Supprimer';
$verbe ??= 'Supprimer définitivement';
$precision ??= 'Cette action est définitive.';
?>
<form method="post"
      action="<?= e($action) ?>"
      class="admin-suppression"
      data-confirmation="<?= e($verbe) ?> « <?= e($nom) ?> » ? <?= e($precision) ?>">
    <?= \App\Core\Csrf::champ() ?>
    <button type="submit" class="admin-bouton admin-bouton--danger"><?= e($libelle) ?></button>
</form>
