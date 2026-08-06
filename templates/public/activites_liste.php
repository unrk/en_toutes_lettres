<?php
/** @var array<int, array<string, mixed>> $activites */
?>
<header class="entete-page">
    <h1>Activités</h1>
    <p>
        Ateliers de français, actions culturelles, médiation de l'écrit,
        animations autour des livres: découvrez nos actions à Noisy-le-Sec.
    </p>
</header>

<?php if ($activites === []): ?>
    <p class="etat-vide">Aucune activité n'est en ligne pour l'instant.</p>
<?php else: ?>
    <div class="row g-3">
        <?php foreach ($activites as $activite): ?>
            <article class="col-md-6 col-xl-4">
                <div class="carte card h-100 border-0 shadow-sm">
                    <img src="<?= !empty($activite['image_chemin']) ? '/' . e((string) $activite['image_chemin']) : e(image_substitution((string) $activite['adresse'], 600, 400)) ?>"
                         alt="<?= e((string) ($activite['image_alt'] ?? ('Illustration de l\'activité ' . $activite['titre']))) ?>"
                         class="carte__image card-img-top">
                    <div class="carte__contenu card-body d-flex flex-column">
                        <h2 class="h5">
                            <a href="/activites/<?= e((string) $activite['adresse']) ?>" class="text-decoration-none stretched-link"><?= e((string) $activite['titre']) ?></a>
                        </h2>
                        <?php if (!empty($activite['resume'])): ?>
                            <p class="mb-3"><?= e((string) $activite['resume']) ?></p>
                        <?php endif; ?>
                        <span class="lien-fleche mt-auto">Voir la fiche</span>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
