<?php
/** @var array<string, mixed> $page */
?>
<article class="detail">
    <header class="detail__entete">
        <h1><?= e((string) $page['titre']) ?></h1>
        <img src="<?= e(image_substitution((string) ($page['adresse'] ?? $page['titre']), 1200, 500)) ?>"
             alt="Illustration de lecture"
             class="detail__banniere">
    </header>

    <div class="detail__contenu">
        <?= (string) $page['contenu'] ?>
    </div>
</article>
