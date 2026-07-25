#!/usr/bin/env php
<?php

declare(strict_types=1);

// À exécuter à la main sur le serveur (ex. via SSH), jamais par le
// déploiement automatique. Sert à créer un compte du back-office (il n'y a
// pas encore d'interface pour le faire depuis le navigateur) :
//
//   php bin/creer_utilisateur.php "Nom Prénom" email@exemple.fr motdepasse administrateur
//
// Le rôle doit être "administrateur" ou "redacteur".

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Ce script ne peut être exécuté que depuis la ligne de commande.');
}

require __DIR__ . '/../app/bootstrap.php';

use App\Core\Database;

$arguments = array_slice($argv, 1);

if (count($arguments) !== 4) {
    fwrite(STDERR, "Utilisation : php bin/creer_utilisateur.php \"Nom\" email mot_de_passe role\n");
    fwrite(STDERR, "role = administrateur ou redacteur\n");
    exit(1);
}

[$nom, $email, $motDePasse, $role] = $arguments;

if (!in_array($role, ['administrateur', 'redacteur'], true)) {
    fwrite(STDERR, "Le rôle doit être \"administrateur\" ou \"redacteur\".\n");
    exit(1);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, "Adresse e-mail invalide : {$email}\n");
    exit(1);
}

if (strlen($motDePasse) < 8) {
    fwrite(STDERR, "Le mot de passe doit contenir au moins 8 caractères.\n");
    exit(1);
}

$connexion = Database::connexion();

$existe = $connexion->prepare('SELECT id FROM utilisateurs WHERE email = :email');
$existe->execute(['email' => $email]);

if ($existe->fetch() !== false) {
    fwrite(STDERR, "Un compte existe déjà avec cet e-mail : {$email}\n");
    exit(1);
}

$insertion = $connexion->prepare(
    'INSERT INTO utilisateurs (nom, email, mot_de_passe_hash, role, actif)
     VALUES (:nom, :email, :mot_de_passe_hash, :role, 1)'
);
$insertion->execute([
    'nom' => $nom,
    'email' => $email,
    'mot_de_passe_hash' => password_hash($motDePasse, PASSWORD_DEFAULT),
    'role' => $role,
]);

echo "Compte créé pour {$nom} ({$email}), rôle : {$role}.\n";
