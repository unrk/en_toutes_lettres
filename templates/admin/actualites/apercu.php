<?php
$libellesStatut = [
    'brouillon' => 'Brouillon',
    'publie' => 'Publié',
    'programme' => 'Programmé',
];
?>
<div class="admin-message admin-message--info">
    Ceci est un aperçu réservé à l'équipe, la mise en page définitive du site
    public pourra légèrement différer. Statut actuel :
    <strong><?= htmlspecialchars($libellesStatut[$actualite['statut']] ?? $actualite['statut'], ENT_QUOTES, 'UTF-8') ?></strong>.
</div>

<article class="apercu-actualite">
    <?php if (!empty($actualite['image_chemin'])): ?>
        <img src="/<?= htmlspecialchars($actualite['image_chemin'], ENT_QUOTES, 'UTF-8') ?>"
             alt="<?= htmlspecialchars($actualite['image_alt'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
             class="apercu-actualite__image">
    <?php endif; ?>

    <h1><?= htmlspecialchars($actualite['titre'], ENT_QUOTES, 'UTF-8') ?></h1>

    <div class="apercu-actualite__contenu">
        <?= $actualite['contenu'] ?>
    </div>
</article>

<p><a href="/admin/actualites/<?= (int) $actualite['id'] ?>/modifier">Retour à la modification</a></p>
