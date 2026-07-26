<?php
/** @var array $actualite */
$libellesStatut = [
    'brouillon' => 'Brouillon',
    'publie' => 'En ligne',
    'programme' => 'Programmé',
];
$libellesType = [
    'actualite' => 'Actualité',
    'annonce' => 'Annonce',
];
?>
<div class="admin-message admin-message--info">
    Aperçu réservé à l'équipe : la mise en page définitive du site public pourra
    légèrement différer.
    <br>
    <?= e($libellesType[$actualite['type']] ?? $actualite['type']) ?> —
    <strong><?= e($libellesStatut[$actualite['statut']] ?? $actualite['statut']) ?></strong>
    <?php if ($actualite['statut'] === 'programme' && $actualite['publie_le'] !== null): ?>
        pour le <?= e((new DateTime($actualite['publie_le']))->format('d/m/Y à H\hi')) ?>
    <?php endif; ?>
</div>

<article class="apercu-actualite">
    <?php if (!empty($actualite['image_chemin'])): ?>
        <img src="/<?= e($actualite['image_chemin']) ?>"
             alt="<?= e($actualite['image_alt'] ?? '') ?>"
             class="apercu-actualite__image">
    <?php endif; ?>

    <h1><?= e($actualite['titre']) ?></h1>

    <div class="apercu-actualite__contenu">
        <?php /* Contenu déjà passé par AssainisseurHtml à l'enregistrement. */ ?>
        <?= $actualite['contenu'] ?>
    </div>
</article>

<p class="admin-formulaire__actions">
    <a href="/admin/actualites/<?= (int) $actualite['id'] ?>/modifier" class="admin-bouton">Modifier</a>
    <a href="/admin/actualites" class="admin-bouton">Retour à la liste</a>
</p>
