<?php
/**
 * Envoi d'une image avec son texte alternatif (obligatoire dès qu'une image
 * est présente : une image sans description est inaccessible aux personnes
 * qui utilisent un lecteur d'écran).
 *
 * Options : nom (nom du champ fichier), libelle, erreurs, aide,
 *           image_actuelle (chemin ou null), nom_alt, valeur_alt, libelle_alt.
 */
$nomAlt = $nom_alt ?? ($nom . '_alt');
$valeurAlt = $valeur_alt ?? '';
$libelleAlt = $libelle_alt ?? "Description de l'image";
$imageActuelle = $image_actuelle ?? null;

$id = 'champ_' . $nom;
$erreur = $erreurs[$nom] ?? null;
$erreurAlt = $erreurs[$nomAlt] ?? null;
$idAide = $aide !== '' ? $id . '_aide' : null;
?>
<div class="admin-champ">
    <label for="<?= e($id) ?>"><?= e($libelle) ?></label>

    <?php if ($aide !== ''): ?>
        <p class="admin-champ__aide" id="<?= e($idAide) ?>"><?= e($aide) ?></p>
    <?php endif; ?>

    <?php if ($imageActuelle !== null && $imageActuelle !== ''): ?>
        <div class="admin-champ__image-actuelle">
            <img src="/<?= e($imageActuelle) ?>" alt="<?= e($valeurAlt) ?>">
            <label class="admin-case">
                <input type="checkbox" name="supprimer_<?= e($nom) ?>" value="1">
                Retirer cette image
            </label>
        </div>
    <?php endif; ?>

    <input type="file"
           id="<?= e($id) ?>"
           name="<?= e($nom) ?>"
           accept="image/jpeg,image/png,image/webp"
           <?= $idAide !== null ? 'aria-describedby="' . e($idAide) . '"' : '' ?>>

    <?php if ($erreur !== null): ?>
        <p class="admin-message admin-message--erreur"><?= e($erreur) ?></p>
    <?php endif; ?>
</div>

<?php
champ('texte', [
    'nom' => $nomAlt,
    'libelle' => $libelleAlt,
    'valeur' => $valeurAlt,
    'erreurs' => $erreurs,
    'aide' => "Décrivez en une phrase ce que montre l'image, pour les personnes "
        . "qui ne peuvent pas la voir. Exemple : « Trois personnes autour d'une table de lecture ».",
]);
