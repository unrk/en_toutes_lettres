<?php
/** @var array<int, array<string, mixed>> $activites */
/** @var array<int, array<string, mixed>> $actualites */
/** @var array<int, array<string, mixed>> $evenements */
?>
<section class="hero card border-0 overflow-hidden mb-4">
    <div class="row g-0 align-items-stretch">
        <div class="col-lg-7">
            <div class="hero__contenu p-4 p-md-5">
                <p class="hero__surtitre">Association à Noisy-le-Sec</p>
                <h1>Apprendre, lire, écrire et se rencontrer.</h1>
                <p class="hero__texte">
                    Ateliers de français, actions culturelles et un lieu de rencontre à
                    Noisy-le-Sec.
                </p>
                <div class="hero__actions d-flex flex-wrap gap-2">
                    <a href="/activites" class="btn btn-warning fw-semibold">Découvrir nos activités</a>
                    <a href="/adhesion-et-dons" class="btn btn-outline-light fw-semibold">Adhérer ou faire un don</a>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <img src="<?= e(image_substitution('accueil-lecture', 900, 700)) ?>"
                 alt="Illustration de lecture et d'entraide"
                 class="hero__image">
        </div>
    </div>
</section>

<section class="section mb-4">
    <div class="section__entete d-flex justify-content-between align-items-end flex-wrap gap-2">
        <h2>Nos activités</h2>
        <a href="/activites" class="section__lien">Voir toutes les activités</a>
    </div>

    <?php if ($activites === []): ?>
        <p class="etat-vide">Les activités seront bientôt affichées ici.</p>
    <?php else: ?>
        <div class="row g-3 mt-1">
            <?php foreach ($activites as $activite): ?>
                <article class="col-md-6 col-xl-4">
                    <div class="carte card h-100 border-0 shadow-sm">
                        <img src="<?= !empty($activite['image_chemin']) ? '/' . e((string) $activite['image_chemin']) : e(image_substitution((string) $activite['adresse'], 600, 400)) ?>"
                             alt="<?= e((string) ($activite['image_alt'] ?? ('Illustration de l\'activité ' . $activite['titre']))) ?>"
                             class="carte__image card-img-top">
                        <div class="carte__contenu card-body">
                            <h3 class="h5">
                                <a href="/activites/<?= e((string) $activite['adresse']) ?>" class="stretched-link text-decoration-none"><?= e((string) $activite['titre']) ?></a>
                            </h3>
                            <?php if (!empty($activite['resume'])): ?>
                                <p class="mb-0"><?= e((string) $activite['resume']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<section class="section section--teintee p-3 p-md-4 mb-4">
    <div class="section__entete d-flex justify-content-between align-items-end flex-wrap gap-2">
        <h2>Actualités et annonces</h2>
        <a href="/actualites" class="section__lien">Consulter toutes les actualités</a>
    </div>

    <?php if ($actualites === []): ?>
        <p class="etat-vide">Les actualités seront bientôt affichées ici.</p>
    <?php else: ?>
        <div class="liste-actus mt-2">
            <?php foreach ($actualites as $actualite): ?>
                <?php $date = $actualite['date_affichage'] ?? null; ?>
                <article class="actu-ligne d-flex gap-3 align-items-start">
                    <img src="<?= !empty($actualite['image_chemin']) ? '/' . e((string) $actualite['image_chemin']) : e(image_substitution((string) $actualite['adresse'], 320, 240)) ?>"
                         alt="<?= e((string) ($actualite['image_alt'] ?? ('Illustration de l\'actualité ' . $actualite['titre']))) ?>"
                         class="actu-ligne__image">
                    <div>
                    <h3 class="h5 mb-1">
                        <a href="/actualites/<?= e((string) $actualite['adresse']) ?>"><?= e((string) $actualite['titre']) ?></a>
                    </h3>
                    <p class="actu-ligne__meta mb-1">
                        <?= e((string) ($actualite['type'] === 'annonce' ? 'Annonce' : 'Actualité')) ?>
                        <?php if ($date instanceof DateTimeImmutable): ?>
                            · <?= e($date->format('d/m/Y')) ?>
                        <?php endif; ?>
                    </p>
                    <?php if (!empty($actualite['extrait'])): ?>
                        <p class="mb-0"><?= e((string) $actualite['extrait']) ?></p>
                    <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<section class="section mb-4">
    <div class="section__entete d-flex justify-content-between align-items-end flex-wrap gap-2">
        <h2>À venir dans l'agenda</h2>
        <a href="/agenda" class="section__lien">Voir l'agenda complet</a>
    </div>

    <?php if ($evenements === []): ?>
        <p class="etat-vide">Aucun événement à venir pour l'instant.</p>
    <?php else: ?>
        <ul class="liste-agenda mt-2">
            <?php foreach ($evenements as $evenement): ?>
                <?php $debut = new DateTimeImmutable((string) $evenement['debut']); ?>
                <li class="agenda-item card border-0 shadow-sm">
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

<section class="section section--appel p-3 p-md-4">
    <h2>Soutenir l'association</h2>
    <p>Chaque contribution aide à garder nos activités accessibles à tous.</p>
    <div class="d-flex flex-wrap align-items-center gap-3">
        <a href="/adhesion-et-dons" class="btn btn-warning fw-semibold">Voir les options de soutien</a>
        <img src="<?= e(image_substitution('soutien-asso', 520, 360)) ?>"
             alt="Illustration de soutien"
             class="section__image-inline">
    </div>
</section>
