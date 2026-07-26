<?php
/** @var array $evenements */
$libellesStatut = ['brouillon' => 'Brouillon', 'publie' => 'En ligne'];
$sectionPasseeOuverte = false;
?>
<div class="admin-entete-page">
    <h1>Agenda</h1>
    <a href="/admin/agenda/creer" class="admin-bouton admin-bouton--principal">Ajouter</a>
</div>

<?php if ($evenements === []): ?>
    <p class="admin-vide">
        Aucun événement pour l'instant. Utilisez le bouton « Ajouter » pour créer
        le premier.
    </p>
<?php else: ?>
    <table class="admin-tableau">
        <thead>
            <tr>
                <th>Quand</th>
                <th>Événement</th>
                <th>État</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($evenements as $evenement): ?>
                <?php if (!$evenement['a_venir'] && !$sectionPasseeOuverte): ?>
                    <?php $sectionPasseeOuverte = true; ?>
                    <tr class="admin-tableau__separateur">
                        <td colspan="4">Événements passés</td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td data-intitule="Quand">
                        <strong><?= e((new DateTime($evenement['debut']))->format('d/m/Y')) ?></strong>
                        <small class="admin-tableau__precision">
                            à <?= e((new DateTime($evenement['debut']))->format('H\hi')) ?>
                        </small>
                    </td>
                    <td data-intitule="Événement">
                        <strong><?= e($evenement['titre']) ?></strong>
                        <?php if (!empty($evenement['lieu'])): ?>
                            <small class="admin-tableau__precision"><?= e($evenement['lieu']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td data-intitule="État">
                        <span class="admin-badge admin-badge--<?= e($evenement['statut']) ?>">
                            <?= e($libellesStatut[$evenement['statut']] ?? $evenement['statut']) ?>
                        </span>
                    </td>
                    <td data-intitule="Actions" class="admin-tableau__actions">
                        <a href="/admin/agenda/<?= (int) $evenement['id'] ?>/modifier" class="admin-bouton">Modifier</a>
                        <a href="/admin/agenda/<?= (int) $evenement['id'] ?>/apercu" class="admin-bouton">Aperçu</a>
                        <?php partiel('bouton_supprimer', [
                            'action' => '/admin/agenda/' . (int) $evenement['id'] . '/supprimer',
                            'nom' => $evenement['titre'],
                        ]); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
