-- ========================================
-- DONNÉES INITIALES - À exécuter UNE SEULE FOIS au démarrage
-- ========================================
-- Ce fichier crée toutes les données de base pour tester l'application

USE bngrc_dons;

-- ========================================
-- RÉGIONS ET VILLES
-- ========================================
INSERT INTO regions (id_region, nom_region, description) VALUES
(1, 'Analamanga', 'Région de la capitale'),
(2, 'Vakinankaratra', 'Région des hauts plateaux'),
(3, 'Atsinanana', 'Région de la côte est')
ON DUPLICATE KEY UPDATE nom_region = nom_region;

INSERT INTO villes (id_ville, nom_ville, id_region, description, population) VALUES
(1, 'Antananarivo', 1, 'Capitale de Madagascar', 1500000),
(2, 'Antsirabe', 2, 'Ville thermale', 250000),
(3, 'Toamasina', 3, 'Premier port de Madagascar', 300000),
(4, 'Ambatolampy', 2, 'Ville de montagne', 50000),
(5, 'Moramanga', 3, 'Carrefour routier', 45000)
ON DUPLICATE KEY UPDATE nom_ville = nom_ville;

-- ========================================
-- TYPES DE BESOIN ET TYPES DE DON
-- ========================================
INSERT INTO type_besoin (id_type_besoin, libelle_type, description) VALUES
(1, 'Nature', 'Produits alimentaires et de première nécessité'),
(2, 'Matériel', 'Matériaux de construction et équipements')
ON DUPLICATE KEY UPDATE libelle_type = libelle_type;

INSERT INTO type_don (id_type_don, libelle_type, description) VALUES
(1, 'Nature', 'Dons en nature (produits alimentaires)'),
(2, 'Matériel', 'Dons matériels (matériaux de construction)'),
(3, 'Argent', 'Dons en espèces')
ON DUPLICATE KEY UPDATE libelle_type = libelle_type;

-- ========================================
-- ARTICLES PAR TYPE
-- ========================================
-- Articles de type NATURE
INSERT INTO articles (id_article, nom_article, id_type_besoin, description, prix_unitaire) VALUES
(1, 'Riz', 1, 'Riz blanc de qualité (sac de 50kg)', 120000),
(2, 'Huile', 1, 'Huile alimentaire (bidon de 5L)', 45000),
(3, 'Sucre', 1, 'Sucre blanc (sac de 25kg)', 35000),
(4, 'Haricots', 1, 'Haricots secs (sac de 25kg)', 50000),
(5, 'Farine', 1, 'Farine de blé (sac de 25kg)', 40000)
ON DUPLICATE KEY UPDATE nom_article = nom_article;

-- Articles de type MATERIAU
INSERT INTO articles (id_article, nom_article, id_type_besoin, description, prix_unitaire) VALUES
(6, 'Tôles', 2, 'Tôles ondulées 3m', 25000),
(7, 'Clous', 2, 'Clous pour toiture (kg)', 8000),
(8, 'Briques', 2, 'Briques rouges (unité)', 500),
(9, 'Ciment', 2, 'Sac de ciment 50kg', 35000),
(10, 'Bois de construction', 2, 'Planche 4m (unité)', 15000)
ON DUPLICATE KEY UPDATE nom_article = nom_article;

-- ========================================
-- BESOINS (variés pour tests)
-- ========================================

-- Besoin 1: CRITIQUE - Ville sinistrée (Moramanga)
INSERT INTO besoins (id_besoin, id_ville, description, urgence, statut) VALUES
(1, 5, 'Ville touchée par cyclone - besoins urgents en matériaux et vivres', 'critique', 'en_cours')
ON DUPLICATE KEY UPDATE description = description;

INSERT INTO besoin_articles (id_besoin, id_article, quantite, quantite_satisfaite, prix_unitaire) VALUES
(1, 1, 200, 0, 120000),     -- 200 sacs riz = 24 000 000 Ar
(1, 6, 500, 0, 25000),      -- 500 tôles = 12 500 000 Ar
(1, NULL, 1, 0, 5000000)    -- 5 000 000 Ar en argent
ON DUPLICATE KEY UPDATE quantite = quantite;

-- Besoin 2: URGENT - Reconstruction (Ambatolampy)
INSERT INTO besoins (id_besoin, id_ville, description, urgence, statut) VALUES
(2, 4, 'Reconstruction après inondation', 'urgente', 'en_cours')
ON DUPLICATE KEY UPDATE description = description;

INSERT INTO besoin_articles (id_besoin, id_article, quantite, quantite_satisfaite, prix_unitaire) VALUES
(2, 8, 5000, 0, 500),       -- 5000 briques = 2 500 000 Ar
(2, 9, 50, 0, 35000)        -- 50 sacs ciment = 1 750 000 Ar
ON DUPLICATE KEY UPDATE quantite = quantite;

-- Besoin 3: NORMAL - Aide alimentaire (Toamasina)
INSERT INTO besoins (id_besoin, id_ville, description, urgence, statut) VALUES
(3, 3, 'Distribution alimentaire trimestrielle', 'normale', 'en_cours')
ON DUPLICATE KEY UPDATE description = description;

INSERT INTO besoin_articles (id_besoin, id_article, quantite, quantite_satisfaite, prix_unitaire) VALUES
(3, 1, 100, 0, 120000),     -- 100 sacs riz = 12 000 000 Ar
(3, 2, 50, 0, 45000)        -- 50 bidons huile = 2 250 000 Ar
ON DUPLICATE KEY UPDATE quantite = quantite;

-- Besoin 4: NORMAL - Mixte articles + argent (Antsirabe)
INSERT INTO besoins (id_besoin, id_ville, description, urgence, statut) VALUES
(4, 2, 'Programme de soutien aux familles', 'normale', 'en_cours')
ON DUPLICATE KEY UPDATE description = description;

INSERT INTO besoin_articles (id_besoin, id_article, quantite, quantite_satisfaite, prix_unitaire) VALUES
(4, 1, 50, 0, 120000),      -- 50 sacs riz = 6 000 000 Ar
(4, 4, 30, 0, 50000),       -- 30 sacs haricots = 1 500 000 Ar
(4, NULL, 1, 0, 3000000)    -- 3 000 000 Ar en argent
ON DUPLICATE KEY UPDATE quantite = quantite;

-- Besoin 5: CRITIQUE - Argent seulement (Antananarivo)
INSERT INTO besoins (id_besoin, id_ville, description, urgence, statut) VALUES
(5, 1, 'Fonds d\'urgence pour achat de médicaments', 'critique', 'en_cours')
ON DUPLICATE KEY UPDATE description = description;

INSERT INTO besoin_articles (id_besoin, id_article, quantite, quantite_satisfaite, prix_unitaire) VALUES
(5, NULL, 1, 0, 10000000)   -- 10 000 000 Ar en argent
ON DUPLICATE KEY UPDATE quantite = quantite;

-- ========================================
-- DONS (variés pour tests)
-- ========================================

-- Don 1: Riz - DISPONIBLE
INSERT INTO dons (id_don, id_type_don, id_article, description_don, quantite, quantite_restante, donateur_nom, donateur_contact, statut, date_don) VALUES
(1, 1, 1, 'Don de riz pour les sinistrés', 150, 150, 'Association AIDER', '034 12 345 67', 'disponible', NOW())
ON DUPLICATE KEY UPDATE donateur_nom = donateur_nom;

-- Don 2: Riz - DISPONIBLE
INSERT INTO dons (id_don, id_type_don, id_article, description_don, quantite, quantite_restante, donateur_nom, donateur_contact, statut, date_don) VALUES
(2, 1, 1, 'Stock de riz d\'urgence', 200, 200, 'ONG SecoursMada', '033 98 765 43', 'disponible', NOW())
ON DUPLICATE KEY UPDATE donateur_nom = donateur_nom;

-- Don 3: Tôles - DISPONIBLE
INSERT INTO dons (id_don, id_type_don, id_article, description_don, quantite, quantite_restante, donateur_nom, donateur_contact, statut, date_don) VALUES
(3, 2, 6, 'Tôles pour reconstruction', 300, 300, 'Entreprise BTP Plus', '034 55 666 77', 'disponible', NOW())
ON DUPLICATE KEY UPDATE donateur_nom = donateur_nom;

-- Don 4: Briques - DISPONIBLE
INSERT INTO dons (id_don, id_type_don, id_article, description_don, quantite, quantite_restante, donateur_nom, donateur_contact, statut, date_don) VALUES
(4, 2, 8, 'Briques de construction', 5000, 5000, 'Briqueterie Antsirabe', '033 11 222 33', 'disponible', NOW())
ON DUPLICATE KEY UPDATE donateur_nom = donateur_nom;

-- Don 5: Ciment - DISPONIBLE
INSERT INTO dons (id_don, id_type_don, id_article, description_don, quantite, quantite_restante, donateur_nom, donateur_contact, statut, date_don) VALUES
(5, 2, 9, 'Sacs de ciment', 50, 50, 'Holcim Madagascar', '034 88 999 00', 'disponible', NOW())
ON DUPLICATE KEY UPDATE donateur_nom = donateur_nom;

-- Don 6: Huile - DISPONIBLE
INSERT INTO dons (id_don, id_type_don, id_article, description_don, quantite, quantite_restante, donateur_nom, donateur_contact, statut, date_don) VALUES
(6, 1, 2, 'Huile alimentaire', 50, 50, 'Société STAR', '033 44 555 66', 'disponible', NOW())
ON DUPLICATE KEY UPDATE donateur_nom = donateur_nom;

-- Don 7: ARGENT - DISPONIBLE
INSERT INTO dons (id_don, id_type_don, id_article, description_don, montant_argent, montant_restant, donateur_nom, donateur_contact, statut, date_don) VALUES
(7, 3, NULL, 'Don en espèces pour urgences', 15000000, 15000000, 'Donateur Anonyme', NULL, 'disponible', NOW())
ON DUPLICATE KEY UPDATE donateur_nom = donateur_nom;

-- Don 8: ARGENT - DISPONIBLE
INSERT INTO dons (id_don, id_type_don, id_article, description_don, montant_argent, montant_restant, donateur_nom, donateur_contact, statut, date_don) VALUES
(8, 3, NULL, 'Collecte de fonds entreprises', 20000000, 20000000, 'Groupe CNaPS', '020 22 123 45', 'disponible', NOW())
ON DUPLICATE KEY UPDATE donateur_nom = donateur_nom;

-- Don 9: Haricots - DISPONIBLE
INSERT INTO dons (id_don, id_type_don, id_article, description_don, quantite, quantite_restante, donateur_nom, donateur_contact, statut, date_don) VALUES
(9, 1, 4, 'Haricots secs', 40, 40, 'Coopérative Agricole', '034 77 888 99', 'disponible', NOW())
ON DUPLICATE KEY UPDATE donateur_nom = donateur_nom;

-- Don 10: Farine - DISPONIBLE
INSERT INTO dons (id_don, id_type_don, id_article, description_don, quantite, quantite_restante, donateur_nom, donateur_contact, statut, date_don) VALUES
(10, 1, 5, 'Farine de blé', 30, 30, 'Minoterie Ravinala', '033 22 333 44', 'disponible', NOW())
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
