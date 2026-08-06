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
    <div class="grille-cartes">
        <?php foreach ($activites as $activite): ?>
            <article class="carte">
                <?php if (!empty($activite['image_chemin'])): ?>
                    <img src="/<?= e((string) $activite['image_chemin']) ?>"
                         alt="<?= e((string) ($activite['image_alt'] ?? '')) ?>"
                         class="carte__image">
                <?php endif; ?>
                <div class="carte__contenu">
                    <h2>
                        <a href="/activites/<?= e((string) $activite['adresse']) ?>"><?= e((string) $activite['titre']) ?></a>
                    </h2>
                    <?php if (!empty($activite['resume'])): ?>
                        <p><?= e((string) $activite['resume']) ?></p>
                    <?php endif; ?>
                    <a href="/activites/<?= e((string) $activite['adresse']) ?>" class="lien-fleche">Voir la fiche</a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
