<?php
/** @var array<string, string> $liensHelloAsso */
$actions = [
    [
        'titre' => 'Adhérer à l\'association',
        'description' => 'Devenir adhérent permet de soutenir les ateliers et la vie de l\'association toute l\'année.',
        'url' => $liensHelloAsso['adhesion'] ?? '',
        'libelle' => 'Accéder à l\'adhésion',
    ],
    [
        'titre' => 'Faire un don ponctuel',
        'description' => 'Un don aide à financer le matériel, les sorties et les actions culturelles.',
        'url' => $liensHelloAsso['don'] ?? '',
        'libelle' => 'Faire un don',
    ],
    [
        'titre' => 'Billetterie / inscriptions',
        'description' => 'Pour les événements et activités ponctuelles gérés via HelloAsso.',
        'url' => $liensHelloAsso['billetterie'] ?? '',
        'libelle' => 'Voir la billetterie',
    ],
];
?>
<header class="entete-page">
    <h1>Adhésion, dons et inscriptions</h1>
    <p>
        Les paiements sont traités via HelloAsso. Vous restez sur un parcours
        simple et sécurisé, sans création de compte obligatoire.
    </p>
</header>

<div class="grille-actions">
    <?php foreach ($actions as $action): ?>
        <article class="carte-action">
            <h2><?= e($action['titre']) ?></h2>
            <p><?= e($action['description']) ?></p>
            <?php if ($action['url'] !== ''): ?>
                <a href="<?= e($action['url']) ?>" target="_blank" rel="noopener noreferrer" class="bouton bouton--principal">
                    <?= e($action['libelle']) ?>
                </a>
            <?php else: ?>
                <p class="etat-attente">
                    Lien à renseigner dans la configuration locale.
                </p>
            <?php endif; ?>
        </article>
    <?php endforeach; ?>
</div>

<section class="section section--teintee">
    <h2>Lettre d'information</h2>
    <p>
        La partie inscription newsletter (avec confirmation par e-mail) sera
        branchée ici. En attendant, contactez l'association pour être ajouté à
        la liste de diffusion.
    </p>
</section>
