<?php
/**
 * Champ de saisie sur une ligne.
 * Options : nom, libelle, valeur, erreurs, aide, obligatoire, type, autofocus.
 */
$type ??= 'text';
$autofocus ??= false;
$id = 'champ_' . $nom;
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

    <input type="<?= e($type) ?>"
           id="<?= e($id) ?>"
           name="<?= e($nom) ?>"
           value="<?= e((string) $valeur) ?>"
           <?= $obligatoire ? 'required' : '' ?>
           <?= $autofocus ? 'autofocus' : '' ?>
           <?= $erreur !== null ? 'aria-invalid="true"' : '' ?>
           <?= $decritPar !== '' ? 'aria-describedby="' . e($decritPar) . '"' : '' ?>>

    <?php if ($erreur !== null): ?>
        <p class="admin-message admin-message--erreur" id="<?= e($idErreur) ?>"><?= e($erreur) ?></p>
    <?php endif; ?>
</div>
