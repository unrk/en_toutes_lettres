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

        <img src="<?= !empty($actualite['image_chemin']) ? '/' . e((string) $actualite['image_chemin']) : e(image_substitution((string) $actualite['adresse'], 1200, 700)) ?>"
            alt="<?= e((string) ($actualite['image_alt'] ?? ('Illustration de l\'actualité ' . $actualite['titre']))) ?>"
            class="detail__image mb-3">

    <div class="detail__contenu">
        <?= (string) $actualite['contenu'] ?>
    </div>
</article>
