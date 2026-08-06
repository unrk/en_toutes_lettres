<?php
/** @var array<int, array<string, mixed>> $partenaires */
?>
<header class="entete-page">
    <h1>Partenaires</h1>
    <p>Ces structures soutiennent ou accompagnent les actions d'En Toutes Lettres.</p>
    <img src="<?= e(image_substitution('partenaires', 640, 360)) ?>"
         alt="Illustration partenaires"
         class="entete-page__illustration">
</header>

<?php if ($partenaires === []): ?>
    <p class="etat-vide">Aucun partenaire n'est affiché pour l'instant.</p>
<?php else: ?>
    <ul class="grille-partenaires">
        <?php foreach ($partenaires as $partenaire): ?>
            <li class="partenaire">
                <?php if (!empty($partenaire['lien_url'])): ?>
                    <a href="<?= e((string) $partenaire['lien_url']) ?>" target="_blank" rel="noopener noreferrer">
                <?php endif; ?>

                <?php if (!empty($partenaire['logo_chemin'])): ?>
                    <img src="/<?= e((string) $partenaire['logo_chemin']) ?>"
                         alt="<?= e((string) ($partenaire['logo_alt'] ?? $partenaire['nom'])) ?>"
                         class="partenaire__logo">
                <?php else: ?>
                    <img src="<?= e(image_substitution((string) $partenaire['nom'], 400, 300)) ?>"
                         alt="<?= e((string) ('Illustration du partenaire ' . $partenaire['nom'])) ?>"
                         class="partenaire__logo partenaire__logo--substitution">
                <?php endif; ?>

                <?php if (!empty($partenaire['lien_url'])): ?>
                    </a>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
