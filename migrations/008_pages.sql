-- Pages de contenu simples : « À propos », « Contact », mentions légales,
-- politique de confidentialité…
--
-- La colonne « verrouillee » protège les pages dont l'absence poserait un
-- problème juridique (mentions légales, politique de confidentialité) : elles
-- restent entièrement modifiables, mais ne peuvent pas être supprimées. Une
-- fausse manœuvre un dimanche soir ne doit pas mettre le site en infraction.
CREATE TABLE IF NOT EXISTS pages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(200) NOT NULL,
    adresse VARCHAR(200) NULL,
    contenu MEDIUMTEXT NOT NULL,
    statut ENUM('brouillon','publie') NOT NULL DEFAULT 'brouillon',
    verrouillee TINYINT(1) NOT NULL DEFAULT 0,
    cree_le DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    modifie_le DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE UNIQUE INDEX idx_pages_adresse ON pages (adresse);
