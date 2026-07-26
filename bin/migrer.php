#!/usr/bin/env php
<?php

declare(strict_types=1);

// À exécuter à la main sur le serveur (ex. via SSH), jamais automatiquement
// par le déploiement : php bin/migrer.php

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Ce script ne peut être exécuté que depuis la ligne de commande.');
}

require __DIR__ . '/../app/bootstrap.php';

use App\Core\Adresse;
use App\Core\Database;

$dossierMigrations = __DIR__ . '/../migrations';
$connexion = Database::connexion();

$connexion->exec(
    'CREATE TABLE IF NOT EXISTS schema_migrations (
        fichier VARCHAR(255) NOT NULL PRIMARY KEY,
        applique_le DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
);

$dejaAppliquees = $connexion->query('SELECT fichier FROM schema_migrations')
    ->fetchAll(PDO::FETCH_COLUMN);

$fichiers = glob($dossierMigrations . '/*.sql');
sort($fichiers);

$nombreAppliquees = 0;

foreach ($fichiers as $chemin) {
    $nom = basename($chemin);

    if (in_array($nom, $dejaAppliquees, true)) {
        continue;
    }

    echo "Application de {$nom}...\n";

    $sql = file_get_contents($chemin);
    $connexion->exec($sql);

    $insertion = $connexion->prepare(
        'INSERT INTO schema_migrations (fichier) VALUES (:fichier)'
    );
    $insertion->execute(['fichier' => $nom]);

    $nombreAppliquees++;
}

if ($nombreAppliquees === 0) {
    echo "Rien à faire : toutes les migrations sont déjà appliquées.\n";
} else {
    echo "{$nombreAppliquees} migration(s) appliquée(s) avec succès.\n";
}

// Complète les adresses web manquantes.
//
// Une adresse se calcule à partir d'un titre accentué : impossible à faire
// correctement en SQL pur, donc c'est fait ici. C'est volontairement intégré à
// ce script plutôt que confié à une seconde commande : oublier de la lancer
// laisserait des fiches sans adresse, donc inaccessibles sur le site public.
// Sans effet si tout est déjà en ordre, et relançable sans risque.
$tablesAvecAdresse = ['actualites', 'activites', 'evenements', 'pages', 'galeries'];
$adressesCompletees = 0;

foreach ($tablesAvecAdresse as $table) {
    $tableExiste = $connexion->query("SHOW TABLES LIKE " . $connexion->quote($table))->fetchColumn();
    if ($tableExiste === false) {
        continue;
    }

    $sansAdresse = $connexion
        ->query("SELECT id, titre FROM {$table} WHERE adresse IS NULL OR adresse = ''")
        ->fetchAll();

    foreach ($sansAdresse as $ligne) {
        $adresse = Adresse::unique($ligne['titre'], $table, (int) $ligne['id']);

        $miseAJour = $connexion->prepare("UPDATE {$table} SET adresse = :adresse WHERE id = :id");
        $miseAJour->execute(['adresse' => $adresse, 'id' => $ligne['id']]);

        echo "  Adresse « {$adresse} » attribuée à {$table} #{$ligne['id']}.\n";
        $adressesCompletees++;
    }
}

if ($adressesCompletees > 0) {
    echo "{$adressesCompletees} adresse(s) web complétée(s).\n";
}
