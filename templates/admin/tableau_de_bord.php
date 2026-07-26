<?php
/** @var array $tuiles */
/** @var string $prenom */
?>
<h1>Bonjour <?= e($prenom) ?></h1>

<p class="admin-champ__aide">
    Choisissez ce que vous souhaitez modifier sur le site.
</p>

<ul class="admin-tuiles">
    <?php foreach ($tuiles as $tuile): ?>
        <li>
            <a href="<?= e($tuile['url']) ?>" class="admin-tuile">
                <span class="admin-tuile__icone" aria-hidden="true"><?= e($tuile['icone']) ?></span>
                <span class="admin-tuile__titre"><?= e($tuile['libelle']) ?></span>
                <span class="admin-tuile__description"><?= e($tuile['description']) ?></span>
            </a>
        </li>
    <?php endforeach; ?>
</ul>
