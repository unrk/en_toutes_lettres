<?php
/** @var string $contenu */
$titrePage = ($titre ?? '') !== '' ? $titre . ' — Espace bénévoles' : 'Espace bénévoles';
$utilisateurConnecte = \App\Core\Auth::utilisateur();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titrePage, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="admin-corps">
    <header class="admin-entete">
        <a href="/admin" class="admin-entete__logo">Espace bénévoles</a>
        <nav class="admin-entete__nav">
            <a href="/admin/actualites">Actualités</a>
        </nav>
        <?php if ($utilisateurConnecte !== null): ?>
            <div class="admin-entete__utilisateur">
                <span><?= htmlspecialchars($utilisateurConnecte['nom'], ENT_QUOTES, 'UTF-8') ?></span>
                <form method="post" action="/admin/deconnexion" class="admin-entete__deconnexion">
                    <?= \App\Core\Csrf::champ() ?>
                    <button type="submit">Se déconnecter</button>
                </form>
            </div>
        <?php endif; ?>
    </header>

    <main class="admin-contenu">
        <?= $contenu ?>
    </main>

    <script src="/assets/js/confirmation-suppression.js" defer></script>
</body>
</html>
