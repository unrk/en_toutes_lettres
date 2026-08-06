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
    <link rel="stylesheet" href="/assets/vendor/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/theme.css?v=20260806">
    <link rel="stylesheet" href="/assets/css/style.css?v=20260806">
</head>
<body class="site-corps">
    <a href="#contenu-principal" class="site-lien-evitement">Aller au contenu principal</a>

    <header class="site-entete shadow-sm">
        <nav class="navbar navbar-expand-lg site-navbar container-xl py-2" aria-label="Navigation principale">
            <a href="/" class="site-entete__logo text-decoration-none navbar-brand m-0">En Toutes Lettres</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal"
                    aria-controls="menuPrincipal" aria-expanded="false" aria-label="Ouvrir ou fermer le menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="menuPrincipal">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <?php foreach ($navigation as $lien): ?>
                        <li class="nav-item">
                            <a href="<?= e($lien['url']) ?>"
                               class="nav-link<?= $cheminActuel === $lien['url'] ? ' active' : '' ?>"
                               <?= $cheminActuel === $lien['url'] ? 'aria-current="page"' : '' ?>>
                                <?= e($lien['libelle']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <a href="/adhesion-et-dons" class="site-entete__action btn btn-warning fw-semibold">Adhérer ou soutenir</a>
            </div>
        </nav>
    </header>

    <main id="contenu-principal" class="site-contenu container-xl">
        <?= $contenu ?>
    </main>

    <footer class="site-pied">
        <div class="site-pied__grille container-xl">
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
        <p class="site-pied__copyright container-xl">&copy; <?= date('Y') ?> En Toutes Lettres — Noisy-le-Sec</p>
    </footer>

    <script src="/assets/vendor/bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>
