<?php
/** @var array<int, array<string, mixed>> $actualites */
$anneeCourante = null;
?>
<header class="entete-page">
    <h1>Actualités et annonces</h1>
    <p>Suivez la vie de l'association, les événements passés et les informations pratiques.</p>
</header>

<?php if ($actualites === []): ?>
    <p class="etat-vide">Aucune actualité en ligne pour l'instant.</p>
<?php else: ?>
    <div class="archives-actus">
        <?php foreach ($actualites as $actualite): ?>
            <?php
            $date = $actualite['date_affichage'] ?? null;
            $annee = $date instanceof DateTimeImmutable ? $date->format('Y') : 'Archives';
            ?>
            <?php if ($anneeCourante !== $annee): ?>
                <?php $anneeCourante = $annee; ?>
                <h2 class="archives-actus__annee"><?= e((string) $anneeCourante) ?></h2>
            <?php endif; ?>

            <article class="actu-ligne d-flex gap-3 align-items-start">
                <img src="<?= !empty($actualite['image_chemin']) ? '/' . e((string) $actualite['image_chemin']) : '/assets/img/placeholders/actualite.svg' ?>"
                     alt="<?= e((string) ($actualite['image_alt'] ?? ('Illustration de l\'actualité ' . $actualite['titre']))) ?>"
                     class="actu-ligne__image">
                <div>
                    <h3 class="h5 mb-1">
                        <a href="/actualites/<?= e((string) $actualite['adresse']) ?>"><?= e((string) $actualite['titre']) ?></a>
                    </h3>
                    <p class="actu-ligne__meta mb-1">
                        <?= e((string) ($actualite['type'] === 'annonce' ? 'Annonce' : 'Actualité')) ?>
                        <?php if ($date instanceof DateTimeImmutable): ?>
                            · <?= e($date->format('d/m/Y')) ?>
                        <?php endif; ?>
                    </p>
                    <?php if (!empty($actualite['extrait'])): ?>
                        <p class="mb-0"><?= e((string) $actualite['extrait']) ?></p>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
