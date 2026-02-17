-- ========================================
-- DONNÉES INITIALES - À exécuter UNE SEULE FOIS au démarrage
-- ========================================
-- Ce fichier crée toutes les données de base pour tester l'application

USE bngrc_dons;

-- ========================================
-- SUPPRESSION DES ANCIENNES DONNÉES
-- (ordre inversé pour respecter les clés étrangères)
-- ========================================
DELETE FROM dispatch_dons;
DELETE FROM achats;
DELETE FROM besoin_articles;
DELETE FROM dons;
DELETE FROM besoins;
DELETE FROM articles;
DELETE FROM villes;
DELETE FROM regions;
DELETE FROM type_don;
DELETE FROM type_besoin;

-- ========================================
-- RÉGIONS ET VILLES
-- ========================================
INSERT INTO regions (id_region, nom_region, description) VALUES
(1, 'Analamanga', 'Région de la capitale'),
(2, 'Vakinankaratra', 'Région des hauts plateaux'),
(3, 'Atsinanana', 'Région de la côte est')
ON DUPLICATE KEY UPDATE nom_region = nom_region;

INSERT INTO villes (id_ville, nom_ville, id_region, description, population) VALUES
(1, 'Toamasina', 1, 'Premier port de Madagascar', 300000),
(2, 'Mananjary', 1, 'Ville côtière du sud-est', 50000),
(3, 'Farafangana', 1, 'Ville du sud-est', 30000),
(4, 'Nosy Be', 1, 'Île touristique', 75000),
(5, 'Morondava', 1, 'Ville de la côte ouest', 60000)
ON DUPLICATE KEY UPDATE nom_ville = nom_ville;

-- ========================================
-- TYPES DE BESOIN ET TYPES DE DON
-- ========================================
INSERT INTO type_besoin (id_type_besoin, libelle_type) VALUES
(1, 'Nature'),
(2, 'Matériel'),
(3, 'Argent')
ON DUPLICATE KEY UPDATE libelle_type = libelle_type;

INSERT INTO type_don (id_type_don, libelle_type) VALUES
(1, 'Nature'),
(2, 'Matériel'),
(3, 'Argent')
ON DUPLICATE KEY UPDATE libelle_type = libelle_type;

-- ========================================
-- ARTICLES PAR TYPE
-- ========================================
-- Articles de type NATURE
INSERT INTO articles (id_article, nom_article, id_type_besoin, description, prix_unitaire) VALUES
(1, 'Riz (kg)', 1, 'Riz en kilogrammes', 3000),
(2, 'Eau (L)', 1, 'Eau potable en litres', 1000),
(3, 'Huile (L)', 1, 'Huile alimentaire en litres', 6000),
(4, 'Haricots', 1, 'Haricots secs', 4000)
ON DUPLICATE KEY UPDATE nom_article = nom_article;

-- Articles de type MATERIEL
INSERT INTO articles (id_article, nom_article, id_type_besoin, description, prix_unitaire) VALUES
(5, 'Tôle', 2, 'Tôle ondulée', 25000),
(6, 'Bâche', 2, 'Bâche de protection', 15000),
(7, 'Clous (kg)', 2, 'Clous en kilogrammes', 8000),
(8, 'Bois', 2, 'Bois de construction', 10000),
(9, 'Groupe', 2, 'Groupe électrogène', 6750000)
ON DUPLICATE KEY UPDATE nom_article = nom_article;

-- ========================================
-- BESOINS PAR VILLE
-- ========================================

-- Besoin 1: Toamasina (id_ville=1)
INSERT INTO besoins (id_besoin, id_ville, description, urgence, statut) VALUES
(1, 1, 'Besoins urgents Toamasina', 'critique', 'en_cours')
ON DUPLICATE KEY UPDATE description = description;

INSERT INTO besoin_articles (id_besoin, id_article, quantite, quantite_satisfaite, prix_unitaire) VALUES
(1, 1, 800, 0, 3000),       -- Riz (kg)
(1, 2, 1500, 0, 1000),      -- Eau (L)
(1, 5, 120, 0, 25000),      -- Tôle
(1, 6, 200, 0, 15000),      -- Bâche
(1, NULL, 1, 0, 12000000),  -- Argent
(1, 9, 3, 0, 6750000)       -- Groupe
ON DUPLICATE KEY UPDATE quantite = quantite;

-- Besoin 2: Mananjary (id_ville=2)
INSERT INTO besoins (id_besoin, id_ville, description, urgence, statut) VALUES
(2, 2, 'Besoins urgents Mananjary', 'urgente', 'en_cours')
ON DUPLICATE KEY UPDATE description = description;

INSERT INTO besoin_articles (id_besoin, id_article, quantite, quantite_satisfaite, prix_unitaire) VALUES
(2, 1, 500, 0, 3000),       -- Riz (kg)
(2, 3, 120, 0, 6000),       -- Huile (L)
(2, 5, 80, 0, 25000),       -- Tôle
(2, 7, 60, 0, 8000),        -- Clous (kg)
(2, NULL, 1, 0, 6000000)    -- Argent
ON DUPLICATE KEY UPDATE quantite = quantite;

-- Besoin 3: Farafangana (id_ville=3)
INSERT INTO besoins (id_besoin, id_ville, description, urgence, statut) VALUES
(3, 3, 'Besoins urgents Farafangana', 'urgente', 'en_cours')
ON DUPLICATE KEY UPDATE description = description;

INSERT INTO besoin_articles (id_besoin, id_article, quantite, quantite_satisfaite, prix_unitaire) VALUES
(3, 1, 600, 0, 3000),       -- Riz (kg)
(3, 2, 1000, 0, 1000),      -- Eau (L)
(3, 6, 150, 0, 15000),      -- Bâche
(3, 8, 100, 0, 10000),      -- Bois
(3, NULL, 1, 0, 8000000)    -- Argent
ON DUPLICATE KEY UPDATE quantite = quantite;

-- Besoin 4: Nosy Be (id_ville=4)
INSERT INTO besoins (id_besoin, id_ville, description, urgence, statut) VALUES
(4, 4, 'Besoins urgents Nosy Be', 'normale', 'en_cours')
ON DUPLICATE KEY UPDATE description = description;

INSERT INTO besoin_articles (id_besoin, id_article, quantite, quantite_satisfaite, prix_unitaire) VALUES
(4, 1, 300, 0, 3000),       -- Riz (kg)
(4, 4, 200, 0, 4000),       -- Haricots
(4, 5, 40, 0, 25000),       -- Tôle
(4, 7, 30, 0, 8000),        -- Clous (kg)
(4, NULL, 1, 0, 4000000)    -- Argent
ON DUPLICATE KEY UPDATE quantite = quantite;

-- Besoin 5: Morondava (id_ville=5)
INSERT INTO besoins (id_besoin, id_ville, description, urgence, statut) VALUES
(5, 5, 'Besoins urgents Morondava', 'critique', 'en_cours')
ON DUPLICATE KEY UPDATE description = description;

INSERT INTO besoin_articles (id_besoin, id_article, quantite, quantite_satisfaite, prix_unitaire) VALUES
(5, 1, 700, 0, 3000),       -- Riz (kg)
(5, 2, 1200, 0, 1000),      -- Eau (L)
(5, 6, 180, 0, 15000),      -- Bâche
(5, 8, 150, 0, 10000),      -- Bois
(5, NULL, 1, 0, 10000000)   -- Argent
ON DUPLICATE KEY UPDATE quantite = quantite;

-- ========================================
-- DONS
-- ========================================

-- Don 1: Argent - 2026-02-16
INSERT INTO dons (id_don, id_type_don, id_article, description_don, montant_argent, montant_restant, donateur_nom, donateur_contact, statut, date_don) VALUES
(1, 3, NULL, 'Don en argent', 5000000, 5000000, 'Donateur 1', NULL, 'disponible', '2026-02-16')
ON DUPLICATE KEY UPDATE donateur_nom = donateur_nom;

-- Don 2: Argent - 2026-02-16
INSERT INTO dons (id_don, id_type_don, id_article, description_don, montant_argent, montant_restant, donateur_nom, donateur_contact, statut, date_don) VALUES
(2, 3, NULL, 'Don en argent', 3000000, 3000000, 'Donateur 2', NULL, 'disponible', '2026-02-16')
ON DUPLICATE KEY UPDATE donateur_nom = donateur_nom;

-- Don 3: Argent - 2026-02-17
INSERT INTO dons (id_don, id_type_don, id_article, description_don, montant_argent, montant_restant, donateur_nom, donateur_contact, statut, date_don) VALUES
(3, 3, NULL, 'Don en argent', 4000000, 4000000, 'Donateur 3', NULL, 'disponible', '2026-02-17')
ON DUPLICATE KEY UPDATE donateur_nom = donateur_nom;

-- Don 4: Argent - 2026-02-17
INSERT INTO dons (id_don, id_type_don, id_article, description_don, montant_argent, montant_restant, donateur_nom, donateur_contact, statut, date_don) VALUES
(4, 3, NULL, 'Don en argent', 1500000, 1500000, 'Donateur 4', NULL, 'disponible', '2026-02-17')
ON DUPLICATE KEY UPDATE donateur_nom = donateur_nom;

-- Don 5: Argent - 2026-02-17
INSERT INTO dons (id_don, id_type_don, id_article, description_don, montant_argent, montant_restant, donateur_nom, donateur_contact, statut, date_don) VALUES
(5, 3, NULL, 'Don en argent', 6000000, 6000000, 'Donateur 5', NULL, 'disponible', '2026-02-17')
ON DUPLICATE KEY UPDATE donateur_nom = donateur_nom;

-- Don 6: Riz (kg) - 2026-02-16
INSERT INTO dons (id_don, id_type_don, id_article, description_don, quantite, quantite_restante, donateur_nom, donateur_contact, statut, date_don) VALUES
(6, 1, 1, 'Don de Riz (kg)', 400, 400, 'Donateur 6', NULL, 'disponible', '2026-02-16')
ON DUPLICATE KEY UPDATE donateur_nom = donateur_nom;

-- Don 7: Eau (L) - 2026-02-16
INSERT INTO dons (id_don, id_type_don, id_article, description_don, quantite, quantite_restante, donateur_nom, donateur_contact, statut, date_don) VALUES
(7, 1, 2, 'Don d''Eau (L)', 600, 600, 'Donateur 7', NULL, 'disponible', '2026-02-16')
ON DUPLICATE KEY UPDATE donateur_nom = donateur_nom;

-- Don 8: Tôle - 2026-02-17
INSERT INTO dons (id_don, id_type_don, id_article, description_don, quantite, quantite_restante, donateur_nom, donateur_contact, statut, date_don) VALUES
(8, 2, 5, 'Don de Tôle', 50, 50, 'Donateur 8', NULL, 'disponible', '2026-02-17')
ON DUPLICATE KEY UPDATE donateur_nom = donateur_nom;

-- Don 9: Bâche - 2026-02-17
INSERT INTO dons (id_don, id_type_don, id_article, description_don, quantite, quantite_restante, donateur_nom, donateur_contact, statut, date_don) VALUES
(9, 2, 6, 'Don de Bâche', 70, 70, 'Donateur 9', NULL, 'disponible', '2026-02-17')
ON DUPLICATE KEY UPDATE donateur_nom = donateur_nom;

-- Don 10: Haricots - 2026-02-17
INSERT INTO dons (id_don, id_type_don, id_article, description_don, quantite, quantite_restante, donateur_nom, donateur_contact, statut, date_don) VALUES
(10, 1, 4, 'Don de Haricots', 100, 100, 'Donateur 10', NULL, 'disponible', '2026-02-17')
ON DUPLICATE KEY UPDATE donateur_nom = donateur_nom;

-- Don 11: Riz (kg) - 2026-02-18
INSERT INTO dons (id_don, id_type_don, id_article, description_don, quantite, quantite_restante, donateur_nom, donateur_contact, statut, date_don) VALUES
(11, 1, 1, 'Don de Riz (kg)', 2000, 2000, 'Donateur 11', NULL, 'disponible', '2026-02-18')
ON DUPLICATE KEY UPDATE donateur_nom = donateur_nom;

-- Don 12: Tôle - 2026-02-18
INSERT INTO dons (id_don, id_type_don, id_article, description_don, quantite, quantite_restante, donateur_nom, donateur_contact, statut, date_don) VALUES
(12, 2, 5, 'Don de Tôle', 300, 300, 'Donateur 12', NULL, 'disponible', '2026-02-18')
ON DUPLICATE KEY UPDATE donateur_nom = donateur_nom;

-- Don 13: Eau (L) - 2026-02-18
INSERT INTO dons (id_don, id_type_don, id_article, description_don, quantite, quantite_restante, donateur_nom, donateur_contact, statut, date_don) VALUES
(13, 1, 2, 'Don d''Eau (L)', 5000, 5000, 'Donateur 13', NULL, 'disponible', '2026-02-18')
ON DUPLICATE KEY UPDATE donateur_nom = donateur_nom;

-- Don 14: Argent - 2026-02-19
INSERT INTO dons (id_don, id_type_don, id_article, description_don, montant_argent, montant_restant, donateur_nom, donateur_contact, statut, date_don) VALUES
(14, 3, NULL, 'Don en argent', 20000000, 20000000, 'Donateur 14', NULL, 'disponible', '2026-02-19')
ON DUPLICATE KEY UPDATE donateur_nom = donateur_nom;

-- Don 15: Bâche - 2026-02-19
INSERT INTO dons (id_don, id_type_don, id_article, description_don, quantite, quantite_restante, donateur_nom, donateur_contact, statut, date_don) VALUES
(15, 2, 6, 'Don de Bâche', 500, 500, 'Donateur 15', NULL, 'disponible', '2026-02-19')
ON DUPLICATE KEY UPDATE donateur_nom = donateur_nom;

-- Don 16: Haricots - 2026-02-17
INSERT INTO dons (id_don, id_type_don, id_article, description_don, quantite, quantite_restante, donateur_nom, donateur_contact, statut, date_don) VALUES
(16, 1, 4, 'Don de Haricots', 88, 88, 'Donateur 16', NULL, 'disponible', '2026-02-17')
ON DUPLICATE KEY UPDATE donateur_nom = donateur_nom;

-- ========================================
-- RÉSUMÉ
-- ========================================
SELECT '✅ Données initiales insérées avec succès!' as Message;
SELECT 
    (SELECT COUNT(*) FROM regions) as Regions,
    (SELECT COUNT(*) FROM villes) as Villes,
    (SELECT COUNT(*) FROM articles) as Articles,
    (SELECT COUNT(*) FROM besoins) as Besoins,
    (SELECT COUNT(*) FROM besoin_articles) as 'Lignes besoins',
    (SELECT COUNT(*) FROM dons) as Dons,
    (SELECT COUNT(*) FROM dispatch_dons) as Distributions,
    (SELECT COUNT(*) FROM achats) as Achats;
