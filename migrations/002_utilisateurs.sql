-- Comptes du back-office (bénévoles). Deux rôles : administrateur et
-- rédacteur. La création d'un compte se fait pour l'instant via
-- bin/creer_utilisateur.php (pas encore d'interface de gestion des comptes).
CREATE TABLE IF NOT EXISTS utilisateurs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    mot_de_passe_hash VARCHAR(255) NOT NULL,
    role ENUM('administrateur','redacteur') NOT NULL DEFAULT 'redacteur',
    actif TINYINT(1) NOT NULL DEFAULT 1,
    cree_le DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
