<?php
/** @var string $contenu */
$titrePage = ($titre ?? '') !== '' ? $titre . ' — Espace bénévoles' : 'Espace bénévoles';
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
<body class="admin-corps admin-corps--connexion">
    <main class="admin-connexion">
        <?= $contenu ?>
    </main>
</body>
</html>
