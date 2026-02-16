-- --------------------------------------------------------
-- Base de données: bngrc_dons
-- --------------------------------------------------------

CREATE DATABASE IF NOT EXISTS bngrc_dons;
USE bngrc_dons;

-- --------------------------------------------------------
-- Table: regions
-- --------------------------------------------------------
CREATE TABLE regions (
    id_region INT PRIMARY KEY AUTO_INCREMENT,
    nom_region VARCHAR(100) NOT NULL,
    description TEXT
);

-- --------------------------------------------------------
-- Table: villes
-- --------------------------------------------------------
CREATE TABLE villes (
    id_ville INT PRIMARY KEY AUTO_INCREMENT,
    nom_ville VARCHAR(100) NOT NULL,
    id_region INT,
    description TEXT,
    population INT,
    FOREIGN KEY (id_region) REFERENCES regions(id_region)
);

-- --------------------------------------------------------
-- Table: type_besoin (nature, materiau, argent)
-- --------------------------------------------------------
CREATE TABLE type_besoin (
    id_type_besoin INT PRIMARY KEY AUTO_INCREMENT,
    libelle_type VARCHAR(50) NOT NULL UNIQUE -- 'nature', 'materiau', 'argent'
);

-- --------------------------------------------------------
-- Table: articles (riz, huile, tole, clou, etc.)
-- --------------------------------------------------------
CREATE TABLE articles (
    id_article INT PRIMARY KEY AUTO_INCREMENT,
    nom_article VARCHAR(100) NOT NULL,
    id_type_besoin INT,
    description TEXT,
    FOREIGN KEY (id_type_besoin) REFERENCES type_besoin(id_type_besoin)
);

-- --------------------------------------------------------
-- Table: besoins
-- --------------------------------------------------------
CREATE TABLE besoins (
    id_besoin INT PRIMARY KEY AUTO_INCREMENT,
    id_ville INT,
    id_article INT,
    quantite DECIMAL(15,2) NOT NULL,
    prix_unitaire DECIMAL(15,2) NOT NULL,
    description TEXT,
    urgence VARCHAR(20) DEFAULT 'normale', -- 'normale', 'urgente', 'critique'
    date_saisie DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut VARCHAR(20) DEFAULT 'en_cours', -- 'en_cours', 'satisfait', 'partiel'
    FOREIGN KEY (id_ville) REFERENCES villes(id_ville),
    FOREIGN KEY (id_article) REFERENCES articles(id_article)
);

-- --------------------------------------------------------
-- Table: type_don (nature, materiau, argent)
-- --------------------------------------------------------
CREATE TABLE type_don (
    id_type_don INT PRIMARY KEY AUTO_INCREMENT,
    libelle_type VARCHAR(50) NOT NULL UNIQUE -- 'nature', 'materiau', 'argent'
);

-- --------------------------------------------------------
-- Table: dons
-- --------------------------------------------------------
CREATE TABLE dons (
    id_don INT PRIMARY KEY AUTO_INCREMENT,
    id_type_don INT,
    id_article INT NULL, -- NULL si don en argent
    description_don VARCHAR(255),
    quantite DECIMAL(15,2) NULL, -- NULL si don en argent
    montant_argent DECIMAL(15,2) NULL, -- NULL si don en nature/materiau
    date_don DATETIME DEFAULT CURRENT_TIMESTAMP,
    donateur_nom VARCHAR(100),
    donateur_contact VARCHAR(50),
    statut VARCHAR(20) DEFAULT 'disponible', -- 'disponible', 'affecte', 'partiel'
    FOREIGN KEY (id_type_don) REFERENCES type_don(id_type_don),
    FOREIGN KEY (id_article) REFERENCES articles(id_article)
);

-- --------------------------------------------------------
-- Table: dispatch_dons (historique des affectations)
-- --------------------------------------------------------
CREATE TABLE dispatch_dons (
    id_dispatch INT PRIMARY KEY AUTO_INCREMENT,
    id_don INT,
    id_besoin INT,
    quantite_affectee DECIMAL(15,2) NOT NULL,
    montant_affecte DECIMAL(15,2) NULL, -- pour les dons en argent
    date_dispatch DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_don) REFERENCES dons(id_don),
    FOREIGN KEY (id_besoin) REFERENCES besoins(id_besoin)
);

-- --------------------------------------------------------
-- Table: suivi_ville (pour le tableau de bord)
-- --------------------------------------------------------
CREATE TABLE suivi_ville (
    id_suivi INT PRIMARY KEY AUTO_INCREMENT,
    id_ville INT,
    total_besoins DECIMAL(15,2) DEFAULT 0,
    total_dons_recus DECIMAL(15,2) DEFAULT 0,
    date_mise_a_jour DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_ville) REFERENCES villes(id_ville)
);

-- --------------------------------------------------------
-- Insertion des données de base
-- --------------------------------------------------------

-- Types de besoins
INSERT INTO type_besoin (libelle_type) VALUES 
('nature'),
('materiau'),
('argent');

-- Types de dons
INSERT INTO type_don (libelle_type) VALUES 
('nature'),
('materiau'),
('argent');



-- --------------------------------------------------------
-- Vues utiles pour le tableau de bord
-- --------------------------------------------------------

-- Vue: besoins_par_ville
CREATE VIEW v_besoins_par_ville AS
SELECT 
    v.id_ville,
    v.nom_ville,
    r.nom_region,
    a.nom_article,
    tb.libelle_type,
    b.quantite,
    b.prix_unitaire,
    (b.quantite * b.prix_unitaire) AS montant_total,
    b.date_saisie,
    b.statut,
    COALESCE(SUM(dd.quantite_affectee), 0) AS quantite_deja_affectee,
    (b.quantite - COALESCE(SUM(dd.quantite_affectee), 0)) AS quantite_restante
FROM besoins b
JOIN villes v ON b.id_ville = v.id_ville
JOIN regions r ON v.id_region = r.id_region
JOIN articles a ON b.id_article = a.id_article
JOIN type_besoin tb ON a.id_type_besoin = tb.id_type_besoin
LEFT JOIN dispatch_dons dd ON b.id_besoin = dd.id_besoin
GROUP BY b.id_besoin;

-- Vue: dons_disponibles
CREATE VIEW v_dons_disponibles AS
SELECT 
    d.id_don,
    td.libelle_type AS type_don,
    a.nom_article,
    d.quantite,
    d.montant_argent,
    d.date_don,
    d.donateur_nom,
    d.statut,
    COALESCE(SUM(dd.quantite_affectee), 0) AS quantite_deja_affectee,
    CASE 
        WHEN td.libelle_type = 'argent' THEN 
            d.montant_argent - COALESCE(SUM(dd.montant_affecte), 0)
        ELSE 
            d.quantite - COALESCE(SUM(dd.quantite_affectee), 0)
    END AS quantite_restante
FROM dons d
JOIN type_don td ON d.id_type_don = td.id_type_don
LEFT JOIN articles a ON d.id_article = a.id_article
LEFT JOIN dispatch_dons dd ON d.id_don = dd.id_don
GROUP BY d.id_don
HAVING quantite_restante > 0;

-- Vue: tableau_de_bord_ville
CREATE VIEW v_tableau_bord_ville AS
SELECT 
    v.id_ville,
    v.nom_ville,
    r.nom_region,
    COUNT(DISTINCT b.id_besoin) AS nb_besoins,
    COUNT(DISTINCT CASE WHEN b.statut = 'en_cours' THEN b.id_besoin END) AS besoins_en_cours,
    COUNT(DISTINCT CASE WHEN b.statut = 'satisfait' THEN b.id_besoin END) AS besoins_satisfaits,
    COALESCE(SUM(b.quantite * b.prix_unitaire), 0) AS total_besoins_valeur,
    COALESCE(SUM(dd.montant_affecte), 0) AS total_dons_recus_valeur
FROM villes v
LEFT JOIN regions r ON v.id_region = r.id_region
LEFT JOIN besoins b ON v.id_ville = b.id_ville
LEFT JOIN dispatch_dons dd ON b.id_besoin = dd.id_besoin
GROUP BY v.id_ville;

