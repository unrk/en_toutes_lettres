-- Fiches d'activités : ateliers sociaux linguistiques, médiation, le livre en
-- ville, le chemin du livre, La Cabane.
--
-- Les champs sont calqués sur ce que l'association décrit réellement
-- aujourd'hui (créneaux, lieu, public visé, tarif, modalités d'inscription)
-- plutôt que sur un modèle générique : chaque champ correspond à une question
-- que se pose un habitant du quartier devant la fiche.
--
-- Pas de statut « programmé » ici : une fiche d'activité n'a pas de date de
-- parution, elle est en préparation (brouillon) ou visible (en ligne).
CREATE TABLE IF NOT EXISTS activites (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(200) NOT NULL,
    adresse VARCHAR(200) NULL,
    resume VARCHAR(500) NULL,
    description MEDIUMTEXT NOT NULL,
    creneaux TEXT NULL,
    lieu VARCHAR(255) NULL,
    public_vise VARCHAR(255) NULL,
    tarif VARCHAR(100) NULL,
    inscriptions TEXT NULL,
    image_chemin VARCHAR(255) NULL,
    image_alt VARCHAR(255) NULL,
    ordre INT NOT NULL DEFAULT 0,
    statut ENUM('brouillon','publie') NOT NULL DEFAULT 'brouillon',
    cree_le DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    modifie_le DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE UNIQUE INDEX idx_activites_adresse ON activites (adresse);
CREATE INDEX idx_activites_statut_ordre ON activites (statut, ordre);
