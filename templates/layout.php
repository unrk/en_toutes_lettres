<?php
/** @var string $contenu */
$titreVue = $titre ?? '';
$titrePage = $titreVue !== ''
    ? $titreVue . ' — ' . config('nom_application', 'En Toutes Lettres')
    : config('nom_application', 'En Toutes Lettres');

$cheminActuel = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$navigation = [
    ['url' => '/', 'libelle' => 'Accueil'],
    ['url' => '/activites', 'libelle' => 'Activités'],
    ['url' => '/actualites', 'libelle' => 'Actualités'],
    ['url' => '/agenda', 'libelle' => 'Agenda'],
    ['url' => '/partenaires', 'libelle' => 'Partenaires'],
    ['url' => '/a-propos-de', 'libelle' => 'À propos'],
    ['url' => '/contact', 'libelle' => 'Contact'],
];

$liensReseaux = config('liens_reseaux_sociaux', []);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titrePage, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="site-corps">
    <a href="#contenu-principal" class="site-lien-evitement">Aller au contenu principal</a>

    <header class="site-entete">
        <div class="site-entete__barre">
            <a href="/" class="site-entete__logo">En Toutes Lettres</a>
            <a href="/adhesion-et-dons" class="site-entete__action">Adhérer ou soutenir</a>
        </div>

        <details class="site-menu">
            <summary class="site-menu__bouton">Menu</summary>
            <nav class="site-menu__contenu" aria-label="Navigation principale">
                <ul>
                    <?php foreach ($navigation as $lien): ?>
                        <li>
                            <a href="<?= e($lien['url']) ?>"
                               <?= $cheminActuel === $lien['url'] ? 'aria-current="page"' : '' ?>>
                                <?= e($lien['libelle']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        </details>
    </header>

    <main id="contenu-principal" class="site-contenu">
        <?= $contenu ?>
    </main>

    <footer class="site-pied">
        <div class="site-pied__grille">
            <div>
                <h2>En Toutes Lettres</h2>
                <p>Association de quartier à Noisy-le-Sec : ateliers de français, actions culturelles et lieu convivial avec La Cabane.</p>
            </div>
            <div>
                <h2>Informations pratiques</h2>
                <ul>
                    <li><a href="/contact">Contact et accès</a></li>
                    <li><a href="/mentions-legales">Mentions légales</a></li>
                    <li><a href="/politique-de-confidentialite">Politique de confidentialité</a></li>
                </ul>
            </div>
            <?php if (($liensReseaux['facebook'] ?? '') !== '' || ($liensReseaux['instagram'] ?? '') !== ''): ?>
                <div>
                    <h2>Suivre l'association</h2>
                    <ul>
                        <?php if (($liensReseaux['facebook'] ?? '') !== ''): ?>
                            <li><a href="<?= e($liensReseaux['facebook']) ?>" target="_blank" rel="noopener noreferrer">Facebook</a></li>
                        <?php endif; ?>
                        <?php if (($liensReseaux['instagram'] ?? '') !== ''): ?>
                            <li><a href="<?= e($liensReseaux['instagram']) ?>" target="_blank" rel="noopener noreferrer">Instagram</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
        <p class="site-pied__copyright">&copy; <?= date('Y') ?> En Toutes Lettres — Noisy-le-Sec</p>
    </footer>
</body>
</html>
