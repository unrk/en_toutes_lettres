<?php
/** @var array<int, array<string, mixed>> $evenements */
$ouvertPasse = false;
?>
<header class="entete-page">
    <h1>Agenda</h1>
    <p>Retrouvez les prochains rendez-vous et les événements passés de l'association.</p>
</header>

<?php if ($evenements === []): ?>
    <p class="etat-vide">Aucun événement en ligne pour l'instant.</p>
<?php else: ?>
    <div class="timeline">
        <?php foreach ($evenements as $evenement): ?>
            <?php
            $estAVenir = (bool) ($evenement['a_venir'] ?? false);
            $debut = new DateTimeImmutable((string) $evenement['debut']);
            $fin = !empty($evenement['fin']) ? new DateTimeImmutable((string) $evenement['fin']) : null;
            ?>

            <?php if (!$estAVenir && !$ouvertPasse): ?>
                <?php $ouvertPasse = true; ?>
                <h2 class="timeline__separateur">Événements passés</h2>
            <?php endif; ?>

            <article class="timeline__item<?= $estAVenir ? '' : ' timeline__item--passe' ?>">
                <p class="timeline__date">
                    <?= e($debut->format('d/m/Y')) ?> à <?= e($debut->format('H\\hi')) ?>
                    <?php if ($fin !== null): ?>
                        <?php if ($debut->format('Y-m-d') === $fin->format('Y-m-d')): ?>
                            · jusqu'à <?= e($fin->format('H\\hi')) ?>
                        <?php else: ?>
                            · jusqu'au <?= e($fin->format('d/m/Y à H\\hi')) ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </p>
                <h3><?= e((string) $evenement['titre']) ?></h3>
                <?php if (!empty($evenement['lieu'])): ?>
                    <p class="timeline__lieu"><?= e((string) $evenement['lieu']) ?></p>
                <?php endif; ?>
                <div class="timeline__description">
                    <?= (string) $evenement['description'] ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
