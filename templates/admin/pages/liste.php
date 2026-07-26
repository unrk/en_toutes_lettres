<?php
/** @var array $pages */
$libellesStatut = ['brouillon' => 'Brouillon', 'publie' => 'En ligne'];
?>
<div class="admin-entete-page">
    <h1>Pages du site</h1>
    <a href="/admin/pages/creer" class="admin-bouton admin-bouton--principal">Ajouter</a>
</div>

<p class="admin-champ__aide">
    Ce sont les pages de texte du site : « À propos », « Contact », mentions
    légales… Les pages marquées d'un cadenas sont obligatoires : vous pouvez
    les modifier, mais pas les supprimer.
</p>

<?php if ($pages === []): ?>
    <p class="admin-vide">Aucune page pour l'instant.</p>
<?php else: ?>
    <table class="admin-tableau">
        <thead>
            <tr>
                <th>Page</th>
                <th>État</th>
                <th>Dernière modification</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pages as $page): ?>
                <tr>
                    <td data-intitule="Page">
                        <strong><?= e($page['titre']) ?></strong>
                        <?php if ((bool) $page['verrouillee']): ?>
                            <span class="admin-cadenas" title="Page obligatoire, non supprimable">
                                <span aria-hidden="true">🔒</span>
                                <span class="admin-invisible">Page obligatoire, non supprimable</span>
                            </span>
                        <?php endif; ?>
                    </td>
                    <td data-intitule="État">
                        <span class="admin-badge admin-badge--<?= e($page['statut']) ?>">
                            <?= e($libellesStatut[$page['statut']] ?? $page['statut']) ?>
                        </span>
                    </td>
                    <td data-intitule="Dernière modification">
                        <?= e((new DateTime($page['modifie_le']))->format('d/m/Y')) ?>
                    </td>
                    <td data-intitule="Actions" class="admin-tableau__actions">
                        <a href="/admin/pages/<?= (int) $page['id'] ?>/modifier" class="admin-bouton">Modifier</a>
                        <a href="/admin/pages/<?= (int) $page['id'] ?>/apercu" class="admin-bouton">Aperçu</a>
                        <?php if (!(bool) $page['verrouillee']): ?>
                            <?php partiel('bouton_supprimer', [
                                'action' => '/admin/pages/' . (int) $page['id'] . '/supprimer',
                                'nom' => $page['titre'],
                            ]); ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
