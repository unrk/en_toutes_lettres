<?php
/** @var array|null $page */
/** @var array<string, string> $erreurs */
/** @var array<string, string> $valeurs */
$action = $page === null
    ? '/admin/pages/creer'
    : '/admin/pages/' . (int) $page['id'] . '/modifier';
?>
<h1><?= e($titre) ?></h1>

<?php if ($page !== null && (bool) $page['verrouillee']): ?>
    <p class="admin-message admin-message--info">
        Cette page est obligatoire pour le site. Vous pouvez en modifier
        librement le contenu, mais elle ne peut pas être supprimée.
    </p>
<?php endif; ?>

<?php if (!empty($erreurs['general'])): ?>
    <p class="admin-message admin-message--erreur" role="alert"><?= e($erreurs['general']) ?></p>
<?php elseif ($erreurs !== []): ?>
    <p class="admin-message admin-message--erreur" role="alert">
        Le formulaire n'a pas pu être enregistré. Corrigez les points signalés
        ci-dessous : rien de ce que vous avez écrit n'est perdu.
    </p>
<?php endif; ?>

<form method="post" action="<?= e($action) ?>" class="admin-formulaire">
    <?= \App\Core\Csrf::champ() ?>

    <?php
    champ('texte', [
        'nom' => 'titre',
        'libelle' => 'Titre de la page',
        'valeur' => $valeurs['titre'],
        'erreurs' => $erreurs,
        'obligatoire' => true,
        'autofocus' => true,
    ]);

    champ('editeur', [
        'nom' => 'contenu',
        'libelle' => 'Contenu',
        'valeur' => $valeurs['contenu'],
        'erreurs' => $erreurs,
        'obligatoire' => true,
        'aide' => 'Utilisez les boutons ci-dessous pour mettre en forme votre texte.',
    ]);

    champ('choix', [
        'nom' => 'statut',
        'libelle' => 'Mise en ligne',
        'valeur' => $valeurs['statut'],
        'erreurs' => $erreurs,
        'options' => [
            'brouillon' => 'Garder en brouillon',
            'publie' => 'Mettre en ligne',
        ],
        'descriptions' => [
            'brouillon' => 'Personne d\'autre que l\'équipe ne la verra.',
            'publie' => 'Visible par tout le monde sur le site.',
        ],
    ]);
    ?>

    <div class="admin-formulaire__actions">
        <button type="submit" class="admin-bouton admin-bouton--principal">Enregistrer</button>
        <a href="/admin/pages" class="admin-bouton">Annuler</a>
        <?php if ($page !== null): ?>
            <a href="/admin/pages/<?= (int) $page['id'] ?>/apercu" class="admin-bouton">Voir l'aperçu</a>
        <?php endif; ?>
    </div>
</form>

<script src="/assets/js/editeur-enrichi.js" defer></script>
