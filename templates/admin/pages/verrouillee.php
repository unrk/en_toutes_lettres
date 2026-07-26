<?php /** @var array $page */ ?>
<h1>Cette page ne peut pas être supprimée</h1>

<p>
    La page « <?= e($page['titre']) ?> » fait partie des pages obligatoires du
    site : la loi impose qu'elle reste accessible aux visiteurs. Elle ne peut
    donc pas être supprimée.
</p>

<p>
    Vous pouvez en revanche en modifier librement le contenu, ou la repasser en
    brouillon si vous avez besoin de la retravailler.
</p>

<p class="admin-formulaire__actions">
    <a href="/admin/pages/<?= (int) $page['id'] ?>/modifier" class="admin-bouton admin-bouton--principal">
        Modifier cette page
    </a>
    <a href="/admin/pages" class="admin-bouton">Retour à la liste</a>
</p>
