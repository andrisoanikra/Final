-- Table pour la configuration
CREATE TABLE IF NOT EXISTS configuration (
    id_config INT PRIMARY KEY AUTO_INCREMENT,
    cle VARCHAR(100) UNIQUE NOT NULL,
    valeur TEXT,
    description VARCHAR(255),
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insérer la configuration des frais par défaut
INSERT INTO configuration (cle, valeur, description) VALUES 
('frais_achat_pourcentage', '10', 'Pourcentage de frais appliqué lors des achats (ex: 10 pour 10%)')
ON DUPLICATE KEY UPDATE cle = cle;

-- Table pour les achats
CREATE TABLE IF NOT EXISTS achats (
    id_achat INT PRIMARY KEY AUTO_INCREMENT,
    id_don_argent INT NOT NULL,
    id_besoin INT NOT NULL,
    id_article INT NULL, -- NULL pour les achats automatiques sans article spécifique
    quantite DECIMAL(15,2) NOT NULL,
    prix_unitaire DECIMAL(15,2) NOT NULL,
    montant_article DECIMAL(15,2) NOT NULL,
    frais_pourcentage DECIMAL(5,2) NOT NULL,
    montant_frais DECIMAL(15,2) NOT NULL,
    montant_total DECIMAL(15,2) NOT NULL,
    statut VARCHAR(20) DEFAULT 'simule', -- 'simule', 'valide', 'annule'
    id_don_cree INT NULL, -- ID du don créé après validation
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_validation TIMESTAMP NULL,
    FOREIGN KEY (id_don_argent) REFERENCES dons(id_don),
    FOREIGN KEY (id_besoin) REFERENCES besoins(id_besoin),
    FOREIGN KEY (id_article) REFERENCES articles(id_article) ON DELETE SET NULL,
    FOREIGN KEY (id_don_cree) REFERENCES dons(id_don) ON DELETE SET NULL
);
