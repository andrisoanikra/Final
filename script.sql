CREATE DATABASE IF NOT EXISTS livraison_db;
USE livraison_db;

CREATE TABLE lv_chauffeur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    salaire_journalier DECIMAL(10,2) NOT NULL
);

CREATE TABLE lv_vehicule (
    id INT AUTO_INCREMENT PRIMARY KEY,
    immatriculation VARCHAR(50) NOT NULL,
    cout_journalier DECIMAL(10,2) NOT NULL
);

CREATE TABLE lv_colis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    poids_kg DECIMAL(6,2) NOT NULL,
    prix_par_kg DECIMAL(10,2) NOT NULL
);

CREATE TABLE lv_livraison (
    id INT AUTO_INCREMENT PRIMARY KEY,
    colis_id INT NOT NULL,
    chauffeur_id INT NOT NULL,
    vehicule_id INT NOT NULL,
    adresse_depart VARCHAR(255) NOT NULL,
    adresse_destination VARCHAR(255) NOT NULL,
    statut ENUM('en attente', 'livré', 'annulé') NOT NULL,
    prix_facture DECIMAL(10,2) NOT NULL,
    date_livraison DATE NOT NULL,

    FOREIGN KEY (colis_id) REFERENCES lv_colis(id),
    FOREIGN KEY (chauffeur_id) REFERENCES lv_chauffeur(id),
    FOREIGN KEY (vehicule_id) REFERENCES lv_vehicule(id)
);

INSERT INTO lv_chauffeur (nom, salaire_journalier) VALUES
('Jean Dupont', 50),
('Marie Martin', 60);

INSERT INTO lv_vehicule (immatriculation, cout_journalier) VALUES
('AA-123-BB', 30),
('CC-456-DD', 40);

INSERT INTO lv_colis (poids_kg, prix_par_kg) VALUES
(10, 2.5),
(5, 3.0);

INSERT INTO lv_livraison (
    colis_id, chauffeur_id, vehicule_id,
    adresse_depart, adresse_destination,
    statut, prix_facture, date_livraison
) VALUES
(1, 1, 1, 'Entrepôt Central', 'Antananarivo', 'livré', 150, '2025-12-10'),
(2, 2, 2, 'Entrepôt Central', 'Toamasina', 'livré', 120, '2025-12-10'),
(1, 1, 1, 'Entrepôt Central', 'Fianarantsoa', 'en attente', 180, '2025-12-11');

-- Table to allow multiple colis per livraison
CREATE TABLE IF NOT EXISTS lv_livraison_colis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    livraison_id INT NOT NULL,
    colis_id INT NOT NULL,
    FOREIGN KEY (livraison_id) REFERENCES lv_livraison(id) ON DELETE CASCADE,
    FOREIGN KEY (colis_id) REFERENCES lv_colis(id) ON DELETE CASCADE
);

-- Migrate existing single-colis associations to the join table
INSERT INTO lv_livraison_colis (livraison_id, colis_id)
SELECT id, colis_id FROM lv_livraison WHERE colis_id IS NOT NULL;


INSERT INTO lv_vehicule (immatriculation, cout_journalier) VALUES
('GG-100-HH', 30),
('II-101-JJ', 30),
('KK-102-LL', 30),
('MM-103-NN', 30),
('OO-104-PP', 30),
('QQ-105-RR', 30),
('SS-106-TT', 30);

INSERT INTO lv_chauffeur (nom, salaire_journalier) VALUES
('Jean Martin', 15000),
('Pierre Dubois', 15000),
('Paul Bernard', 15000),
('Jacques Thomas', 15000),
('Michel Robert', 15000),
('André Richard', 18000),
('Philippe Petit', 18000),
('Alain Durand', 18000),
('Patrick Leroy', 20000),
('Nicolas Moreau', 20000),
('Christophe Simon', 20000),
('Daniel Laurent', 20000);

CREATE TABLE lv_zone(
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    pourcentage DECIMAL(10,2) NOT NULL
);

INSERT INTO lv_zone (nom, pourcentage) VALUES
('Zone A', 12.5),
('Zone B', 12.5),
('Zone C', 12.5),
('Zone D', 0),
('Zone E', 0);