<?php
/** @var string $contenu */
$titrePage = ($titre ?? '') !== '' ? $titre . ' — Espace bénévoles' : 'Espace bénévoles';
$utilisateurConnecte = \App\Core\Auth::utilisateur();
$estAdministrateur = \App\Core\Auth::estAdministrateur();

$rubriques = [
    ['url' => '/admin/actualites', 'libelle' => 'Actualités et annonces'],
    ['url' => '/admin/activites', 'libelle' => 'Activités'],
    ['url' => '/admin/agenda', 'libelle' => 'Agenda'],
    ['url' => '/admin/galeries', 'libelle' => 'Galeries photos'],
    ['url' => '/admin/partenaires', 'libelle' => 'Partenaires'],
    ['url' => '/admin/pages', 'libelle' => 'Pages du site'],
];

if ($estAdministrateur) {
    $rubriques[] = ['url' => '/admin/comptes', 'libelle' => 'Comptes'];
}

$cheminActuel = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titrePage) ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="admin-corps">
    <a href="#contenu-principal" class="admin-lien-evitement">Aller au contenu</a>

    <header class="admin-entete">
        <a href="/admin" class="admin-entete__logo">Espace bénévoles</a>

        <?php /* <details> natif : le menu se déplie sans JavaScript, et reste
                 utilisable si un script échoue à charger. */ ?>
        <details class="admin-menu">
            <summary class="admin-menu__bouton">Menu</summary>
            <nav class="admin-menu__contenu" aria-label="Rubriques du site">
                <ul>
                    <?php foreach ($rubriques as $rubrique): ?>
                        <li>
                            <a href="<?= e($rubrique['url']) ?>"
                               <?= str_starts_with($cheminActuel, $rubrique['url']) ? 'aria-current="page"' : '' ?>>
                                <?= e($rubrique['libelle']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <?php if ($utilisateurConnecte !== null): ?>
                    <hr>
                    <p class="admin-menu__identite">
                        Connecté en tant que <strong><?= e($utilisateurConnecte['nom']) ?></strong>
                    </p>
                    <ul>
                        <li><a href="/admin/mon-mot-de-passe">Mon mot de passe</a></li>
                    </ul>
                    <form method="post" action="/admin/deconnexion" class="admin-menu__deconnexion">
                        <?= \App\Core\Csrf::champ() ?>
                        <button type="submit" class="admin-bouton">Se déconnecter</button>
                    </form>
                <?php endif; ?>
            </nav>
        </details>
    </header>

    <main class="admin-contenu" id="contenu-principal">
        <?= $contenu ?>
    </main>

    <script src="/assets/js/confirmation-suppression.js" defer></script>
</body>
</html>
