<?php
/** @var array $comptes */
/** @var int $moi */
$libellesRole = ['administrateur' => 'Administrateur', 'redacteur' => 'Rédacteur'];
?>
<div class="admin-entete-page">
    <h1>Comptes</h1>
    <a href="/admin/comptes/creer" class="admin-bouton admin-bouton--principal">Ajouter un compte</a>
</div>

<p class="admin-champ__aide">
    Un <strong>rédacteur</strong> peut créer et modifier tout le contenu du site.
    Un <strong>administrateur</strong> peut en plus gérer les comptes de cette page.
    Un compte désactivé ne peut plus se connecter, mais les contenus qu'il a
    écrits restent en place.
</p>

<table class="admin-tableau">
    <thead>
        <tr>
            <th>Personne</th>
            <th>Rôle</th>
            <th>État</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($comptes as $compte): ?>
            <?php $estMoi = (int) $compte['id'] === (int) $moi; ?>
            <tr<?= (bool) $compte['actif'] ? '' : ' class="admin-tableau__ligne--inactive"' ?>>
                <td data-intitule="Personne">
                    <strong><?= e($compte['nom']) ?></strong>
                    <?php if ($estMoi): ?>
                        <span class="admin-badge admin-badge--publie">vous</span>
                    <?php endif; ?>
                    <small class="admin-tableau__precision"><?= e($compte['email']) ?></small>
                </td>
                <td data-intitule="Rôle"><?= e($libellesRole[$compte['role']] ?? $compte['role']) ?></td>
                <td data-intitule="État">
                    <?php if ((bool) $compte['actif']): ?>
                        <span class="admin-badge admin-badge--publie">Actif</span>
                    <?php else: ?>
                        <span class="admin-badge admin-badge--brouillon">Désactivé</span>
                    <?php endif; ?>
                </td>
                <td data-intitule="Actions" class="admin-tableau__actions">
                    <a href="/admin/comptes/<?= (int) $compte['id'] ?>/modifier" class="admin-bouton">Modifier</a>

                    <?php if ((bool) $compte['actif']): ?>
                        <?php if (!$estMoi): ?>
                            <?php partiel('bouton_supprimer', [
                                'action' => '/admin/comptes/' . (int) $compte['id'] . '/desactiver',
                                'nom' => $compte['nom'],
                                'libelle' => 'Désactiver',
                                'verbe' => 'Désactiver le compte de',
                                'precision' => 'Cette personne ne pourra plus se connecter. '
                                    . 'Ses contenus resteront en ligne et vous pourrez la réactiver plus tard.',
                            ]); ?>
                        <?php endif; ?>
                    <?php else: ?>
                        <form method="post" action="/admin/comptes/<?= (int) $compte['id'] ?>/reactiver">
                            <?= \App\Core\Csrf::champ() ?>
                            <button type="submit" class="admin-bouton">Réactiver</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
