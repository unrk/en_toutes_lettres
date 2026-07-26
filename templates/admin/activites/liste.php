<?php
/** @var array $activites */
$libellesStatut = ['brouillon' => 'Brouillon', 'publie' => 'En ligne'];
$dernierIndice = count($activites) - 1;
?>
<div class="admin-entete-page">
    <h1>Activités</h1>
    <a href="/admin/activites/creer" class="admin-bouton admin-bouton--principal">Ajouter</a>
</div>

<?php if ($activites === []): ?>
    <p class="admin-vide">
        Aucune activité pour l'instant. Utilisez le bouton « Ajouter » pour créer
        la première fiche.
    </p>
<?php else: ?>
    <p class="admin-champ__aide">
        L'ordre ci-dessous est celui dans lequel les activités apparaîtront sur
        le site. Utilisez les flèches pour le modifier.
    </p>

    <table class="admin-tableau">
        <thead>
            <tr>
                <th>Ordre</th>
                <th>Activité</th>
                <th>État</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($activites as $indice => $activite): ?>
                <tr>
                    <td data-intitule="Ordre">
                        <?php partiel('boutons_classement', [
                            'base' => '/admin/activites',
                            'id' => $activite['id'],
                            'premier' => $indice === 0,
                            'dernier' => $indice === $dernierIndice,
                        ]); ?>
                    </td>
                    <td data-intitule="Activité">
                        <strong><?= e($activite['titre']) ?></strong>
                        <?php if (!empty($activite['resume'])): ?>
                            <small class="admin-tableau__precision"><?= e($activite['resume']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td data-intitule="État">
                        <span class="admin-badge admin-badge--<?= e($activite['statut']) ?>">
                            <?= e($libellesStatut[$activite['statut']] ?? $activite['statut']) ?>
                        </span>
                    </td>
                    <td data-intitule="Actions" class="admin-tableau__actions">
                        <a href="/admin/activites/<?= (int) $activite['id'] ?>/modifier" class="admin-bouton">Modifier</a>
                        <a href="/admin/activites/<?= (int) $activite['id'] ?>/apercu" class="admin-bouton">Aperçu</a>
                        <?php partiel('bouton_supprimer', [
                            'action' => '/admin/activites/' . (int) $activite['id'] . '/supprimer',
                            'nom' => $activite['titre'],
                        ]); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
