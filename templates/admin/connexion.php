<div class="admin-connexion__carte">
    <h1>Connexion</h1>
    <p class="admin-connexion__aide">Espace réservé aux bénévoles d'En Toutes Lettres.</p>

    <?php if (!empty($erreur)): ?>
        <p class="admin-message admin-message--erreur"><?= htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <form method="post" action="/admin/connexion" class="admin-formulaire">
        <?= \App\Core\Csrf::champ() ?>

        <label for="champ_email">Adresse e-mail</label>
        <input type="email" id="champ_email" name="email" value="<?= htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8') ?>" required autofocus>

        <label for="champ_mot_de_passe">Mot de passe</label>
        <input type="password" id="champ_mot_de_passe" name="mot_de_passe" required>

        <button type="submit" class="admin-bouton admin-bouton--principal">Se connecter</button>
    </form>
</div>
