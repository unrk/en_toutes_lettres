<?php
/** @var array $evenement */
$libellesStatut = ['brouillon' => 'Brouillon', 'publie' => 'En ligne'];
$debut = new DateTime($evenement['debut']);
$fin = $evenement['fin'] !== null ? new DateTime($evenement['fin']) : null;
$estPasse = $debut < new DateTime();
?>
<div class="admin-message admin-message--info">
    Aperçu réservé à l'équipe.
    État actuel : <strong><?= e($libellesStatut[$evenement['statut']] ?? $evenement['statut']) ?></strong>.
    <?php if ($estPasse): ?>
        Cet événement est passé : il n'apparaît plus dans l'agenda du site.
    <?php endif; ?>
</div>

<article class="apercu-actualite">
    <?php if (!empty($evenement['image_chemin'])): ?>
        <img src="/<?= e($evenement['image_chemin']) ?>"
             alt="<?= e($evenement['image_alt'] ?? '') ?>"
             class="apercu-actualite__image">
    <?php endif; ?>

    <h1><?= e($evenement['titre']) ?></h1>

    <p class="apercu-actualite__resume">
        Le <?= e($debut->format('d/m/Y')) ?> à <?= e($debut->format('H\hi')) ?>
        <?php if ($fin !== null): ?>
            <?php if ($fin->format('Y-m-d') === $debut->format('Y-m-d')): ?>
                , jusqu'à <?= e($fin->format('H\hi')) ?>
            <?php else: ?>
                , jusqu'au <?= e($fin->format('d/m/Y')) ?> à <?= e($fin->format('H\hi')) ?>
            <?php endif; ?>
        <?php endif; ?>
        <?php if (!empty($evenement['lieu'])): ?>
            — <?= e($evenement['lieu']) ?>
        <?php endif; ?>
    </p>

    <div class="apercu-actualite__contenu">
        <?php /* Description déjà passée par AssainisseurHtml à l'enregistrement. */ ?>
        <?= $evenement['description'] ?>
    </div>
</article>

<p class="admin-formulaire__actions">
    <a href="/admin/agenda/<?= (int) $evenement['id'] ?>/modifier" class="admin-bouton">Modifier</a>
    <a href="/admin/agenda" class="admin-bouton">Retour à l'agenda</a>
</p>
