-- Agenda : les événements ponctuels de l'association.
--
-- Le classement se fait par date de début, pas par un ordre manuel : c'est le
-- calendrier qui décide. La page publique affichera les événements à venir
-- (debut >= maintenant) par ordre chronologique, les passés basculant
-- automatiquement dans les archives sans aucune intervention.
CREATE TABLE IF NOT EXISTS evenements (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(200) NOT NULL,
    adresse VARCHAR(200) NULL,
    description MEDIUMTEXT NOT NULL,
    debut DATETIME NOT NULL,
    fin DATETIME NULL,
    lieu VARCHAR(255) NULL,
    image_chemin VARCHAR(255) NULL,
    image_alt VARCHAR(255) NULL,
    statut ENUM('brouillon','publie') NOT NULL DEFAULT 'brouillon',
    cree_le DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    modifie_le DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE UNIQUE INDEX idx_evenements_adresse ON evenements (adresse);
CREATE INDEX idx_evenements_statut_debut ON evenements (statut, debut);
