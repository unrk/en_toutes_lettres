-- Les actualités accueillent aussi les annonces : mêmes champs, même flux
-- chronologique côté public, seule l'étiquette change. Une seule rubrique à
-- utiliser et à maintenir, plutôt que deux CRUD jumeaux.
ALTER TABLE actualites
    ADD COLUMN type ENUM('actualite','annonce') NOT NULL DEFAULT 'actualite';

-- Adresse web de la fiche (« la-fete-de-quartier »), calculée depuis le titre
-- à la création puis figée, pour ne jamais casser un lien déjà partagé.
-- Nullable : les lignes déjà en base sont complétées par bin/migrer.php,
-- qui remplit les adresses manquantes après avoir appliqué les migrations.
ALTER TABLE actualites
    ADD COLUMN adresse VARCHAR(200) NULL;

CREATE UNIQUE INDEX idx_actualites_adresse ON actualites (adresse);
