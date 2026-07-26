<?php
/**
 * Boutons « Monter » / « Descendre » pour classer une fiche.
 *
 * Options : base (URL de la rubrique, ex. /admin/activites), id, premier, dernier.
 *
 * Deux boutons plutôt qu'un glisser-déposer : c'est utilisable au doigt, au
 * clavier, et sans JavaScript. Le bouton est désactivé aux extrémités plutôt
 * que masqué, pour que la position des commandes ne bouge pas d'une ligne à
 * l'autre.
 */
?>
<div class="admin-classement">
    <form method="post" action="<?= e($base) ?>/<?= (int) $id ?>/monter">
        <?= \App\Core\Csrf::champ() ?>
        <button type="submit"
                class="admin-bouton admin-bouton--discret"
                title="Monter d'un cran"
                <?= $premier ? 'disabled' : '' ?>>
            <span aria-hidden="true">▲</span><span class="admin-invisible"> Monter</span>
        </button>
    </form>
    <form method="post" action="<?= e($base) ?>/<?= (int) $id ?>/descendre">
        <?= \App\Core\Csrf::champ() ?>
        <button type="submit"
                class="admin-bouton admin-bouton--discret"
                title="Descendre d'un cran"
                <?= $dernier ? 'disabled' : '' ?>>
            <span aria-hidden="true">▼</span><span class="admin-invisible"> Descendre</span>
        </button>
    </form>
</div>
