<?php
/** @var array $actualites */
$libellesStatut = [
    'brouillon' => 'Brouillon',
    'publie' => 'Publié',
    'programme' => 'Programmé',
];
?>
<div class="admin-entete-page">
    <h1>Actualités</h1>
    <a href="/admin/actualites/creer" class="admin-bouton admin-bouton--principal">Ajouter une actualité</a>
</div>

<?php if ($actualites === []): ?>
    <p>Aucune actualité pour l'instant.</p>
<?php else: ?>
    <table class="admin-tableau">
        <thead>
            <tr>
                <th>Titre</th>
                <th>Statut</th>
                <th>Auteur</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($actualites as $actualite): ?>
                <tr>
                    <td><?= htmlspecialchars($actualite['titre'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <span class="admin-badge admin-badge--<?= htmlspecialchars($actualite['statut'], ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars($libellesStatut[$actualite['statut']] ?? $actualite['statut'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                        <?php if ($actualite['statut'] === 'programme' && $actualite['publie_le'] !== null): ?>
                            <br>
                            <small>le <?= htmlspecialchars((new DateTime($actualite['publie_le']))->format('d/m/Y à H:i'), ENT_QUOTES, 'UTF-8') ?></small>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($actualite['auteur_nom'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="admin-tableau__actions">
                        <a href="/admin/actualites/<?= (int) $actualite['id'] ?>/apercu">Aperçu</a>
                        <a href="/admin/actualites/<?= (int) $actualite['id'] ?>/modifier">Modifier</a>
                        <form method="post"
                              action="/admin/actualites/<?= (int) $actualite['id'] ?>/supprimer"
                              data-confirmation="Supprimer définitivement « <?= htmlspecialchars($actualite['titre'], ENT_QUOTES, 'UTF-8') ?> » ? Cette action est irréversible.">
                            <?= \App\Core\Csrf::champ() ?>
                            <button type="submit" class="admin-bouton admin-bouton--danger">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
