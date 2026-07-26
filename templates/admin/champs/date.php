<?php
/**
 * Date + heure.
 * Options : nom, libelle, valeur (format MySQL ou vide), erreurs, aide, obligatoire.
 */
$id = 'champ_' . $nom;
$erreur = $erreurs[$nom] ?? null;
$idAide = $aide !== '' ? $id . '_aide' : null;
$idErreur = $erreur !== null ? $id . '_erreur' : null;
$decritPar = trim(($idAide ?? '') . ' ' . ($idErreur ?? ''));

// Le navigateur attend « 2026-07-25T14:30 », MySQL renvoie « 2026-07-25 14:30:00 ».
$valeurAffichee = (string) $valeur;
if ($valeurAffichee !== '' && !str_contains($valeurAffichee, 'T')) {
    $valeurAffichee = str_replace(' ', 'T', substr($valeurAffichee, 0, 16));
}
?>
<div class="admin-champ">
    <label for="<?= e($id) ?>">
        <?= e($libelle) ?><?php if ($obligatoire): ?> <span class="admin-champ__requis">(obligatoire)</span><?php endif; ?>
    </label>

    <?php if ($aide !== ''): ?>
        <p class="admin-champ__aide" id="<?= e($idAide) ?>"><?= e($aide) ?></p>
    <?php endif; ?>

    <input type="datetime-local"
           id="<?= e($id) ?>"
           name="<?= e($nom) ?>"
           value="<?= e($valeurAffichee) ?>"
           <?= $obligatoire ? 'required' : '' ?>
           <?= $erreur !== null ? 'aria-invalid="true"' : '' ?>
           <?= $decritPar !== '' ? 'aria-describedby="' . e($decritPar) . '"' : '' ?>>

    <?php if ($erreur !== null): ?>
        <p class="admin-message admin-message--erreur" id="<?= e($idErreur) ?>"><?= e($erreur) ?></p>
    <?php endif; ?>
</div>
