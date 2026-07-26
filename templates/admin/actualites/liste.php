<?php
/** @var array $actualites */
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
<div class="admin-entete-page">
    <h1>Actualités et annonces</h1>
    <a href="/admin/actualites/creer" class="admin-bouton admin-bouton--principal">Ajouter</a>
</div>

<?php if ($actualites === []): ?>
    <p class="admin-vide">
        Il n'y a encore rien ici. Utilisez le bouton « Ajouter » pour écrire
        votre première actualité.
    </p>
<?php else: ?>
    <table class="admin-tableau">
        <thead>
            <tr>
                <th>Titre</th>
                <th>Type</th>
                <th>État</th>
                <th>Écrit par</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($actualites as $actualite): ?>
                <tr>
                    <td data-intitule="Titre"><strong><?= e($actualite['titre']) ?></strong></td>
                    <td data-intitule="Type"><?= e($libellesType[$actualite['type']] ?? $actualite['type']) ?></td>
                    <td data-intitule="État">
                        <span class="admin-badge admin-badge--<?= e($actualite['statut']) ?>">
                            <?= e($libellesStatut[$actualite['statut']] ?? $actualite['statut']) ?>
                        </span>
                        <?php if ($actualite['statut'] === 'programme' && $actualite['publie_le'] !== null): ?>
                            <small class="admin-tableau__precision">
                                le <?= e((new DateTime($actualite['publie_le']))->format('d/m/Y à H\hi')) ?>
                            </small>
                        <?php endif; ?>
                    </td>
                    <td data-intitule="Écrit par"><?= e($actualite['auteur_nom']) ?></td>
                    <td data-intitule="Actions" class="admin-tableau__actions">
                        <a href="/admin/actualites/<?= (int) $actualite['id'] ?>/modifier"
                           class="admin-bouton">Modifier</a>
                        <a href="/admin/actualites/<?= (int) $actualite['id'] ?>/apercu"
                           class="admin-bouton">Aperçu</a>
                        <?php partiel('bouton_supprimer', [
                            'action' => '/admin/actualites/' . (int) $actualite['id'] . '/supprimer',
                            'nom' => $actualite['titre'],
                        ]); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
