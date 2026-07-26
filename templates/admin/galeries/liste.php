<?php
/** @var array $galeries */
$libellesStatut = ['brouillon' => 'Brouillon', 'publie' => 'En ligne'];
$dernierIndice = count($galeries) - 1;
?>
<div class="admin-entete-page">
    <h1>Galeries photos</h1>
    <a href="/admin/galeries/creer" class="admin-bouton admin-bouton--principal">Créer une galerie</a>
</div>

<?php if ($galeries === []): ?>
    <p class="admin-vide">
        Aucune galerie pour l'instant. Créez-en une, puis vous pourrez y ajouter
        des photos.
    </p>
<?php else: ?>
    <table class="admin-tableau">
        <thead>
            <tr>
                <th>Ordre</th>
                <th>Aperçu</th>
                <th>Galerie</th>
                <th>État</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($galeries as $indice => $galerie): ?>
                <tr>
                    <td data-intitule="Ordre">
                        <?php partiel('boutons_classement', [
                            'base' => '/admin/galeries',
                            'id' => $galerie['id'],
                            'premier' => $indice === 0,
                            'dernier' => $indice === $dernierIndice,
                        ]); ?>
                    </td>
                    <td data-intitule="Aperçu">
                        <?php if (!empty($galerie['premiere_photo'])): ?>
                            <img src="/<?= e($galerie['premiere_photo']) ?>" alt="" class="admin-vignette">
                        <?php endif; ?>
                    </td>
                    <td data-intitule="Galerie">
                        <strong><?= e($galerie['titre']) ?></strong>
                        <small class="admin-tableau__precision">
                            <?php $nombre = (int) $galerie['nombre_photos']; ?>
                            <?= $nombre === 0 ? 'Aucune photo' : ($nombre === 1 ? '1 photo' : $nombre . ' photos') ?>
                        </small>
                    </td>
                    <td data-intitule="État">
                        <span class="admin-badge admin-badge--<?= e($galerie['statut']) ?>">
                            <?= e($libellesStatut[$galerie['statut']] ?? $galerie['statut']) ?>
                        </span>
                    </td>
                    <td data-intitule="Actions" class="admin-tableau__actions">
                        <a href="/admin/galeries/<?= (int) $galerie['id'] ?>/modifier" class="admin-bouton">
                            Gérer les photos
                        </a>
                        <?php partiel('bouton_supprimer', [
                            'action' => '/admin/galeries/' . (int) $galerie['id'] . '/supprimer',
                            'nom' => $galerie['titre'],
                            'precision' => 'Les photos qu\'elle contient seront supprimées avec elle.',
                        ]); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
