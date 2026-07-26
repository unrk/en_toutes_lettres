<?php
/**
 * Groupe de boutons radio.
 * Options : nom, libelle, valeur, erreurs, aide, options (valeur => libellé),
 *           descriptions (valeur => texte explicatif, facultatif).
 */
$descriptions ??= [];
$erreur = $erreurs[$nom] ?? null;
?>
<fieldset class="admin-champ admin-champ--choix">
    <legend><?= e($libelle) ?></legend>

    <?php if ($aide !== ''): ?>
        <p class="admin-champ__aide"><?= e($aide) ?></p>
    <?php endif; ?>

    <?php foreach ($options as $valeurOption => $libelleOption): ?>
        <label class="admin-case">
            <input type="radio"
                   name="<?= e($nom) ?>"
                   value="<?= e((string) $valeurOption) ?>"
                   <?= (string) $valeur === (string) $valeurOption ? 'checked' : '' ?>>
            <span>
                <?= e($libelleOption) ?>
                <?php if (isset($descriptions[$valeurOption])): ?>
                    <small class="admin-case__precision"><?= e($descriptions[$valeurOption]) ?></small>
                <?php endif; ?>
            </span>
        </label>
    <?php endforeach; ?>

    <?php if ($erreur !== null): ?>
        <p class="admin-message admin-message--erreur"><?= e($erreur) ?></p>
    <?php endif; ?>
</fieldset>
