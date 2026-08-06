<?php
/** @var array<string, mixed> $activite */
/** @var array<string, string> $informations */
?>
<article class="detail">
    <p><a href="/activites" class="lien-retour">← Retour aux activités</a></p>

    <header class="detail__entete">
        <h1><?= e((string) $activite['titre']) ?></h1>
        <?php if (!empty($activite['resume'])): ?>
            <p class="detail__chapo"><?= e((string) $activite['resume']) ?></p>
        <?php endif; ?>
    </header>

    <?php if (!empty($activite['image_chemin'])): ?>
        <img src="/<?= e((string) $activite['image_chemin']) ?>"
             alt="<?= e((string) ($activite['image_alt'] ?? '')) ?>"
             class="detail__image">
    <?php endif; ?>

    <div class="detail__contenu">
        <?= (string) $activite['description'] ?>
    </div>

    <?php if ($informations !== []): ?>
        <dl class="fiche-infos">
            <?php foreach ($informations as $cle => $valeur): ?>
                <dt><?= e($cle) ?></dt>
                <dd><?= nl2br(e($valeur)) ?></dd>
            <?php endforeach; ?>
        </dl>
    <?php endif; ?>
</article>
