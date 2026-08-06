<?php
/** @var array<int, array<string, mixed>> $activites */
/** @var array<int, array<string, mixed>> $actualites */
/** @var array<int, array<string, mixed>> $evenements */
?>
<section class="hero">
    <p class="hero__surtitre">Association à Noisy-le-Sec</p>
    <h1>Apprendre, lire, écrire et se rencontrer.</h1>
    <p class="hero__texte">
        En Toutes Lettres accompagne les habitants avec des ateliers de français,
        des actions culturelles et un lieu vivant: La Cabane.
    </p>
    <div class="hero__actions">
        <a href="/activites" class="bouton bouton--principal">Découvrir nos activités</a>
        <a href="/adhesion-et-dons" class="bouton bouton--secondaire">Adhérer ou faire un don</a>
    </div>
</section>

<section class="section">
    <div class="section__entete">
        <h2>Nos activités</h2>
        <a href="/activites" class="section__lien">Voir toutes les activités</a>
    </div>

    <?php if ($activites === []): ?>
        <p class="etat-vide">Les activités seront bientôt affichées ici.</p>
    <?php else: ?>
        <div class="grille-cartes">
            <?php foreach ($activites as $activite): ?>
                <article class="carte">
                    <?php if (!empty($activite['image_chemin'])): ?>
                        <img src="/<?= e((string) $activite['image_chemin']) ?>"
                             alt="<?= e((string) ($activite['image_alt'] ?? '')) ?>"
                             class="carte__image">
                    <?php endif; ?>
                    <div class="carte__contenu">
                        <h3>
                            <a href="/activites/<?= e((string) $activite['adresse']) ?>"><?= e((string) $activite['titre']) ?></a>
                        </h3>
                        <?php if (!empty($activite['resume'])): ?>
                            <p><?= e((string) $activite['resume']) ?></p>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<section class="section section--teintee">
    <div class="section__entete">
        <h2>Actualités et annonces</h2>
        <a href="/actualites" class="section__lien">Consulter toutes les actualités</a>
    </div>

    <?php if ($actualites === []): ?>
        <p class="etat-vide">Les actualités seront bientôt affichées ici.</p>
    <?php else: ?>
        <div class="liste-actus">
            <?php foreach ($actualites as $actualite): ?>
                <?php $date = $actualite['date_affichage'] ?? null; ?>
                <article class="actu-ligne">
                    <h3>
                        <a href="/actualites/<?= e((string) $actualite['adresse']) ?>"><?= e((string) $actualite['titre']) ?></a>
                    </h3>
                    <p class="actu-ligne__meta">
                        <?= e((string) ($actualite['type'] === 'annonce' ? 'Annonce' : 'Actualité')) ?>
                        <?php if ($date instanceof DateTimeImmutable): ?>
                            · <?= e($date->format('d/m/Y')) ?>
                        <?php endif; ?>
                    </p>
                    <?php if (!empty($actualite['extrait'])): ?>
                        <p><?= e((string) $actualite['extrait']) ?></p>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<section class="section">
    <div class="section__entete">
        <h2>À venir dans l'agenda</h2>
        <a href="/agenda" class="section__lien">Voir l'agenda complet</a>
    </div>

    <?php if ($evenements === []): ?>
        <p class="etat-vide">Aucun événement à venir pour l'instant.</p>
    <?php else: ?>
        <ul class="liste-agenda">
            <?php foreach ($evenements as $evenement): ?>
                <?php $debut = new DateTimeImmutable((string) $evenement['debut']); ?>
                <li class="agenda-item">
                    <p class="agenda-item__date"><?= e($debut->format('d/m/Y')) ?> à <?= e($debut->format('H\hi')) ?></p>
                    <h3><?= e((string) $evenement['titre']) ?></h3>
                    <?php if (!empty($evenement['lieu'])): ?>
                        <p><?= e((string) $evenement['lieu']) ?></p>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>

<section class="section section--appel">
    <h2>Soutenir l'association</h2>
    <p>
        Adhésion, don ponctuel, participation à un événement: toutes les
        contributions aident à maintenir des activités accessibles à tous.
    </p>
    <a href="/adhesion-et-dons" class="bouton bouton--principal">Voir les options de soutien</a>
</section>
