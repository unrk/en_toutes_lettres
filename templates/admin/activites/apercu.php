<?php
/** @var array $activite */
$libellesStatut = ['brouillon' => 'Brouillon', 'publie' => 'En ligne'];

$informations = array_filter([
    'Jours et horaires' => $activite['creneaux'] ?? '',
    'Lieu' => $activite['lieu'] ?? '',
    'Pour qui ?' => $activite['public_vise'] ?? '',
    'Tarif' => $activite['tarif'] ?? '',
    'Comment s\'inscrire' => $activite['inscriptions'] ?? '',
], static fn (string $valeur): bool => trim($valeur) !== '');
?>
<div class="admin-message admin-message--info">
    Aperçu réservé à l'équipe : la mise en page définitive du site public pourra
    légèrement différer.
    État actuel : <strong><?= e($libellesStatut[$activite['statut']] ?? $activite['statut']) ?></strong>.
</div>

<article class="apercu-actualite">
    <?php if (!empty($activite['image_chemin'])): ?>
        <img src="/<?= e($activite['image_chemin']) ?>"
             alt="<?= e($activite['image_alt'] ?? '') ?>"
             class="apercu-actualite__image">
    <?php endif; ?>

    <h1><?= e($activite['titre']) ?></h1>

    <?php if (!empty($activite['resume'])): ?>
        <p class="apercu-actualite__resume"><?= e($activite['resume']) ?></p>
    <?php endif; ?>

    <div class="apercu-actualite__contenu">
        <?php /* Description déjà passée par AssainisseurHtml à l'enregistrement. */ ?>
        <?= $activite['description'] ?>
    </div>

    <?php if ($informations !== []): ?>
        <dl class="apercu-informations">
            <?php foreach ($informations as $intitule => $valeur): ?>
                <dt><?= e($intitule) ?></dt>
                <dd><?= nl2br(e($valeur)) ?></dd>
            <?php endforeach; ?>
        </dl>
    <?php endif; ?>
</article>

<p class="admin-formulaire__actions">
    <a href="/admin/activites/<?= (int) $activite['id'] ?>/modifier" class="admin-bouton">Modifier</a>
    <a href="/admin/activites" class="admin-bouton">Retour à la liste</a>
</p>
