<?php
/** @var string $contenu */
$titrePage = ($titre ?? '') !== ''
    ? $titre . ' — ' . config('nom_application', 'En Toutes Lettres')
    : config('nom_application', 'En Toutes Lettres');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titrePage, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <header class="entete">
        <a href="/" class="entete__logo">En Toutes Lettres</a>
    </header>

    <main class="contenu">
        <?= $contenu ?>
    </main>

    <footer class="pied">
        <p>&copy; <?= date('Y') ?> En Toutes Lettres — Noisy-le-Sec</p>
    </footer>
</body>
</html>
