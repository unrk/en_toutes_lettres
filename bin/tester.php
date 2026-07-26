#!/usr/bin/env php
<?php

declare(strict_types=1);

// Vérifie que les fonctions délicates du site marchent toujours.
//
//   php bin/tester.php
//
// À lancer après toute modification du code, avant de mettre en ligne.
// Si tout s'affiche en « OK », rien n'est cassé. Au moindre « ECHEC », ne
// mettez pas en ligne : le détail indique ce qui était attendu et ce qui a été
// obtenu à la place.
//
// Aucune installation n'est nécessaire : ni Composer, ni PHPUnit, ni Internet.
// Ce script ne touche jamais à la base de données.

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Ce script ne peut être exécuté que depuis la ligne de commande.');
}

require __DIR__ . '/../app/bootstrap.php';

use App\Core\AssainisseurHtml;
use App\Core\Adresse;
use App\Core\Validateur;

$echecs = 0;
$reussites = 0;

function verifie(string $intitule, mixed $obtenu, mixed $attendu): void
{
    global $echecs, $reussites;

    if ($obtenu === $attendu) {
        $reussites++;
        echo "  OK     {$intitule}\n";
        return;
    }

    $echecs++;
    echo "  ECHEC  {$intitule}\n";
    echo "         attendu : " . var_export($attendu, true) . "\n";
    echo "         obtenu  : " . var_export($obtenu, true) . "\n";
}

function titre(string $texte): void
{
    echo "\n{$texte}\n";
}

// ---------------------------------------------------------------------------
titre('Adresses web des fiches (Adresse)');

verifie('titre simple', Adresse::depuis('La Cabane'), 'la-cabane');
verifie('accents', Adresse::depuis('Été à Noisy — déjà prêt !'), 'ete-a-noisy-deja-pret');
verifie('ligature œ', Adresse::depuis('Cœur & âme'), 'coeur-ame');
verifie('apostrophes', Adresse::depuis("L'accès à l'écrit"), 'l-acces-a-l-ecrit');
verifie('espaces en trop', Adresse::depuis('  Trop   d espaces  '), 'trop-d-espaces');
verifie('chiffres conservés', Adresse::depuis('Bilan 2025 / 2026'), 'bilan-2025-2026');
verifie('titre non latin', Adresse::depuis('日本語'), 'fiche');
verifie('titre vide', Adresse::depuis('   '), 'fiche');
verifie('longueur bornée', mb_strlen(Adresse::depuis(str_repeat('a', 300))), 180);

// ---------------------------------------------------------------------------
titre('Nettoyage du texte enrichi (AssainisseurHtml)');

verifie(
    'script supprimé',
    AssainisseurHtml::nettoyer('<p>Bonjour <script>alert(1)</script></p>'),
    '<p>Bonjour </p>'
);
verifie(
    'style supprimé',
    AssainisseurHtml::nettoyer('<p>a<style>body{display:none}</style>b</p>'),
    '<p>ab</p>'
);
verifie(
    'balise non autorisée dépliée, contenu gardé',
    AssainisseurHtml::nettoyer('<div><p>Texte</p></div>'),
    '<p>Texte</p>'
);
verifie(
    'attribut onclick retiré',
    AssainisseurHtml::nettoyer('<p onclick="voler()">Texte</p>'),
    '<p>Texte</p>'
);
verifie(
    'lien javascript: neutralisé',
    AssainisseurHtml::nettoyer('<a href="javascript:alert(1)">clic</a>'),
    '<a>clic</a>'
);
verifie(
    'lien https conservé',
    AssainisseurHtml::nettoyer('<a href="https://exemple.fr">clic</a>'),
    '<a href="https://exemple.fr" rel="noopener">clic</a>'
);
verifie(
    'lien interne conservé',
    AssainisseurHtml::nettoyer('<a href="/contact">nous écrire</a>'),
    '<a href="/contact" rel="noopener">nous écrire</a>'
);
verifie(
    'mise en forme autorisée conservée',
    AssainisseurHtml::nettoyer('<h2>Titre</h2><ul><li><strong>gras</strong></li></ul>'),
    '<h2>Titre</h2><ul><li><strong>gras</strong></li></ul>'
);
verifie(
    'accents préservés',
    AssainisseurHtml::nettoyer('<p>Été à Noisy</p>'),
    '<p>Été à Noisy</p>'
);
verifie('texte vide', AssainisseurHtml::nettoyer('   '), '');

// ---------------------------------------------------------------------------
titre('Validation des formulaires (Validateur)');

$v = new Validateur();
$v->texte('t', '', 'Le titre');
verifie('champ obligatoire vide', $v->erreurs()['t'] ?? null, 'Le titre est obligatoire.');

$v = new Validateur();
$v->texte('t', 'ab', 'Le titre', min: 3, max: 200);
verifie('texte trop court', $v->erreurs()['t'] ?? null, 'Le titre doit contenir au moins 3 caractères.');

$v = new Validateur();
verifie('espaces rognés', $v->texte('t', '  Bonjour  ', 'Le titre', min: 3), 'Bonjour');
verifie('  aucune erreur', $v->aDesErreurs(), false);

$v = new Validateur();
$v->html('c', '<p>   </p>', 'Le contenu');
verifie('contenu enrichi vide détecté', $v->erreurs()['c'] ?? null, 'Le contenu ne peut pas être vide.');

$v = new Validateur();
verifie(
    'choix invalide retombe sur le défaut',
    $v->choix('s', 'valeur-forgee', 'Le statut', ['brouillon', 'publie'], 'brouillon'),
    'brouillon'
);

$v = new Validateur();
verifie('date passée refusée', $v->dateHeure('d', '2020-01-01T10:00', 'La date', doitEtreFuture: true), null);

$v = new Validateur();
verifie(
    'date future acceptée',
    $v->dateHeure('d', '2030-06-01T09:30', 'La date', doitEtreFuture: true),
    '2030-06-01 09:30:00'
);

$v = new Validateur();
verifie('adresse web complétée', $v->url('u', 'www.exemple.fr', 'Le lien'), 'https://www.exemple.fr');

$v = new Validateur();
$v->url('u', 'pas une adresse', 'Le lien');
verifie('adresse web invalide détectée', isset($v->erreurs()['u']), true);

$v = new Validateur();
$v->email('e', 'pasunemail', "L'adresse e-mail");
verifie('e-mail invalide détecté', isset($v->erreurs()['e']), true);

// ---------------------------------------------------------------------------
echo "\n" . str_repeat('-', 60) . "\n";

if ($echecs === 0) {
    echo "Tout va bien : {$reussites} vérifications passées.\n";
    exit(0);
}

echo "{$echecs} vérification(s) en échec sur " . ($reussites + $echecs) . ".\n";
echo "Ne mettez pas en ligne tant que ce n'est pas corrigé.\n";
exit(1);
