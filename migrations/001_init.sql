-- Table de suivi des migrations déjà appliquées.
-- Permet à bin/migrer.php de savoir quels fichiers de ce dossier ont déjà
-- été exécutés, pour ne jamais rejouer une migration deux fois.
CREATE TABLE IF NOT EXISTS schema_migrations (
    fichier VARCHAR(255) NOT NULL PRIMARY KEY,
    applique_le DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
