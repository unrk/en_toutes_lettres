<?php
/** @var array $page */
$libellesStatut = ['brouillon' => 'Brouillon', 'publie' => 'En ligne'];
?>
<div class="admin-message admin-message--info">
    Aperçu réservé à l'équipe : la mise en page définitive du site public pourra
    légèrement différer.
    État actuel : <strong><?= e($libellesStatut[$page['statut']] ?? $page['statut']) ?></strong>.
</div>

<article class="apercu-actualite">
    <h1><?= e($page['titre']) ?></h1>

    <div class="apercu-actualite__contenu">
        <?php /* Contenu déjà passé par AssainisseurHtml à l'enregistrement. */ ?>
        <?= $page['contenu'] ?>
    </div>
</article>

<p class="admin-formulaire__actions">
    <a href="/admin/pages/<?= (int) $page['id'] ?>/modifier" class="admin-bouton">Modifier</a>
    <a href="/admin/pages" class="admin-bouton">Retour à la liste</a>
</p>
