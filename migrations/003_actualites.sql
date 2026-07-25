-- Actualités du site. `publie_le` porte la date effective de publication
-- (immédiate ou programmée) : un article est visible publiquement si
-- statut = 'publie' OU (statut = 'programme' ET publie_le <= NOW()),
-- ce qui évite d'avoir besoin d'une tâche planifiée pour "faire passer"
-- un article programmé à publié.
CREATE TABLE IF NOT EXISTS actualites (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(200) NOT NULL,
    contenu MEDIUMTEXT NOT NULL,
    image_chemin VARCHAR(255) NULL,
    image_alt VARCHAR(255) NULL,
    statut ENUM('brouillon','publie','programme') NOT NULL DEFAULT 'brouillon',
    publie_le DATETIME NULL,
    auteur_id INT UNSIGNED NOT NULL,
    cree_le DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    modifie_le DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_actualites_auteur FOREIGN KEY (auteur_id) REFERENCES utilisateurs(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX idx_actualites_statut_publie_le ON actualites (statut, publie_le);
