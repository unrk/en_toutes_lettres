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
    <p>Paiements sécurisés via HelloAsso, sans création de compte.</p>
    <img src="<?= e(image_substitution('soutien-asso', 640, 360)) ?>"
         alt="Illustration de soutien"
         class="entete-page__illustration">
</header>

<div class="row g-3">
    <?php foreach ($actions as $action): ?>
        <article class="col-md-6 col-xl-4">
            <div class="carte-action card h-100 border-0 shadow-sm">
                <img src="<?= e(image_substitution($action['titre'], 600, 400)) ?>"
                     alt="Illustration de soutien"
                     class="carte__image card-img-top">
                <div class="card-body d-flex flex-column">
                    <h2 class="h5"><?= e($action['titre']) ?></h2>
                    <p><?= e($action['description']) ?></p>
                    <?php if ($action['url'] !== ''): ?>
                        <a href="<?= e($action['url']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-warning fw-semibold mt-auto">
                            <?= e($action['libelle']) ?>
                        </a>
                    <?php else: ?>
                        <p class="etat-attente mb-0">
                            Lien à renseigner dans la configuration locale.
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </article>
    <?php endforeach; ?>
</div>

<section class="section section--teintee">
    <h2>Lettre d'information</h2>
    <p>L'inscription à la newsletter sera bientôt disponible ici. En attendant, contactez l'association pour être ajouté à la liste de diffusion.</p>
</section>
