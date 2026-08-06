<?php
/** @var array<string, mixed> $actualite */
$date = null;
if (!empty($actualite['publie_le'])) {
    $date = new DateTimeImmutable((string) $actualite['publie_le']);
} elseif (!empty($actualite['cree_le'])) {
    $date = new DateTimeImmutable((string) $actualite['cree_le']);
}
?>
<article class="detail">
    <p><a href="/actualites" class="lien-retour">← Retour aux actualités</a></p>

    <header class="detail__entete">
        <p class="detail__meta">
            <?= e((string) ($actualite['type'] === 'annonce' ? 'Annonce' : 'Actualité')) ?>
            <?php if ($date instanceof DateTimeImmutable): ?>
                · <?= e($date->format('d/m/Y')) ?>
            <?php endif; ?>
        </p>
        <h1><?= e((string) $actualite['titre']) ?></h1>
    </header>

    <?php if (!empty($actualite['image_chemin'])): ?>
        <img src="/<?= e((string) $actualite['image_chemin']) ?>"
             alt="<?= e((string) ($actualite['image_alt'] ?? '')) ?>"
             class="detail__image">
    <?php endif; ?>

    <div class="detail__contenu">
        <?= (string) $actualite['contenu'] ?>
    </div>
</article>
