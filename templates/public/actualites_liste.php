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

            <article class="actu-ligne">
                <h3>
                    <a href="/actualites/<?= e((string) $actualite['adresse']) ?>"><?= e((string) $actualite['titre']) ?></a>
                </h3>
                <p class="actu-ligne__meta">
                    <?= e((string) ($actualite['type'] === 'annonce' ? 'Annonce' : 'Actualité')) ?>
                    <?php if ($date instanceof DateTimeImmutable): ?>
                        · <?= e($date->format('d/m/Y')) ?>
                    <?php endif; ?>
                </p>
                <?php if (!empty($actualite['extrait'])): ?>
                    <p><?= e((string) $actualite['extrait']) ?></p>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
