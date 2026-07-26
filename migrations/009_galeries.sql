-- Galeries photos : la rubrique « Galerie » du site actuel.
--
-- Deux tables plutôt qu'une : une galerie regroupe un nombre libre de photos,
-- chacune avec sa propre description et sa propre place dans le classement.
CREATE TABLE IF NOT EXISTS galeries (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(200) NOT NULL,
    adresse VARCHAR(200) NULL,
    description MEDIUMTEXT NULL,
    ordre INT NOT NULL DEFAULT 0,
    statut ENUM('brouillon','publie') NOT NULL DEFAULT 'brouillon',
    cree_le DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    modifie_le DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE UNIQUE INDEX idx_galeries_adresse ON galeries (adresse);
CREATE INDEX idx_galeries_statut_ordre ON galeries (statut, ordre);

-- ON DELETE CASCADE : supprimer une galerie supprime ses photos en base.
-- Les fichiers correspondants sont effacés du disque par le contrôleur, qui
-- relève les chemins AVANT de lancer la suppression.
CREATE TABLE IF NOT EXISTS galerie_photos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    galerie_id INT UNSIGNED NOT NULL,
    chemin VARCHAR(255) NOT NULL,
    -- Vide juste après un envoi groupé : on ne peut pas demander une
    -- description pendant que l'on sélectionne vingt fichiers d'un coup. Elles
    -- se saisissent ensuite, et la galerie ne peut pas être mise en ligne tant
    -- qu'il en manque une.
    alt VARCHAR(255) NOT NULL DEFAULT '',
    ordre INT NOT NULL DEFAULT 0,
    cree_le DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_galerie_photos_galerie
        FOREIGN KEY (galerie_id) REFERENCES galeries(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX idx_galerie_photos_galerie_ordre ON galerie_photos (galerie_id, ordre);
