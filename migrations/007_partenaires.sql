-- Partenaires institutionnels et associatifs : logo, nom, lien vers leur site.
--
-- Pas de colonne « adresse » : un partenaire n'a pas de page dédiée sur le
-- site, il apparaît uniquement dans la liste des partenaires.
--
-- Le logo est obligatoire (c'est tout l'intérêt de la page) et son texte
-- alternatif aussi : un logo sans description est illisible pour une personne
-- utilisant un lecteur d'écran.
CREATE TABLE IF NOT EXISTS partenaires (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(200) NOT NULL,
    lien_url VARCHAR(500) NULL,
    logo_chemin VARCHAR(255) NULL,
    logo_alt VARCHAR(255) NULL,
    ordre INT NOT NULL DEFAULT 0,
    statut ENUM('brouillon','publie') NOT NULL DEFAULT 'publie',
    cree_le DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    modifie_le DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX idx_partenaires_statut_ordre ON partenaires (statut, ordre);
