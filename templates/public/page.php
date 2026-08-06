<?php
/** @var array<string, mixed> $page */
?>
<article class="detail">
    <header class="detail__entete">
        <h1><?= e((string) $page['titre']) ?></h1>
        <img src="/assets/img/placeholders/hero-lecture.svg"
             alt="Illustration de lecture"
             class="detail__banniere">
    </header>

    <div class="detail__contenu">
        <?= (string) $page['contenu'] ?>
    </div>
</article>
