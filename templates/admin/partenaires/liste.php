<?php
/** @var array $partenaires */
$libellesStatut = ['brouillon' => 'Masqué', 'publie' => 'Affiché'];
$dernierIndice = count($partenaires) - 1;
?>
<div class="admin-entete-page">
    <h1>Partenaires</h1>
    <a href="/admin/partenaires/creer" class="admin-bouton admin-bouton--principal">Ajouter</a>
</div>

<?php if ($partenaires === []): ?>
    <p class="admin-vide">
        Aucun partenaire pour l'instant. Utilisez le bouton « Ajouter » pour
        créer le premier.
    </p>
<?php else: ?>
    <p class="admin-champ__aide">
        L'ordre ci-dessous est celui dans lequel les logos apparaîtront sur le
        site. Utilisez les flèches pour le modifier.
    </p>

    <table class="admin-tableau">
        <thead>
            <tr>
                <th>Ordre</th>
                <th>Logo</th>
                <th>Partenaire</th>
                <th>État</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($partenaires as $indice => $partenaire): ?>
                <tr>
                    <td data-intitule="Ordre">
                        <?php partiel('boutons_classement', [
                            'base' => '/admin/partenaires',
                            'id' => $partenaire['id'],
                            'premier' => $indice === 0,
                            'dernier' => $indice === $dernierIndice,
                        ]); ?>
                    </td>
                    <td data-intitule="Logo">
                        <?php if (!empty($partenaire['logo_chemin'])): ?>
                            <img src="/<?= e($partenaire['logo_chemin']) ?>"
                                 alt="<?= e($partenaire['logo_alt'] ?? '') ?>"
                                 class="admin-vignette">
                        <?php endif; ?>
                    </td>
                    <td data-intitule="Partenaire">
                        <strong><?= e($partenaire['nom']) ?></strong>
                        <?php if (!empty($partenaire['lien_url'])): ?>
                            <small class="admin-tableau__precision"><?= e($partenaire['lien_url']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td data-intitule="État">
                        <span class="admin-badge admin-badge--<?= e($partenaire['statut']) ?>">
                            <?= e($libellesStatut[$partenaire['statut']] ?? $partenaire['statut']) ?>
                        </span>
                    </td>
                    <td data-intitule="Actions" class="admin-tableau__actions">
                        <a href="/admin/partenaires/<?= (int) $partenaire['id'] ?>/modifier" class="admin-bouton">Modifier</a>
                        <?php partiel('bouton_supprimer', [
                            'action' => '/admin/partenaires/' . (int) $partenaire['id'] . '/supprimer',
                            'nom' => $partenaire['nom'],
                        ]); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
