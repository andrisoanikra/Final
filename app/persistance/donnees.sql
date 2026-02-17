-- ========================================
-- DONNÉES DE TEST - Base BNGRC Dons
-- ========================================

USE bngrc_dons;

-- ========================================
-- 1. RÉGIONS
-- ========================================
INSERT INTO regions (nom_region, description) VALUES 
('Analamanga', 'Région du centre incluant la capitale Antananarivo'),
('Betsiboka', 'Région du nord-ouest, zone côtière');

-- ========================================
-- 2. VILLES
-- ========================================
INSERT INTO villes (nom_ville, id_region, description, population) VALUES 
('Antananarivo', 1, 'Capitale de Madagascar', 1275207),
('Mahajanga', 2, 'Port important du nord-ouest', 226339);

-- ========================================
-- 3. ARTICLES
-- ========================================

INSERT INTO articles (nom_article, id_type_besoin, description, prix_unitaire) VALUES 
-- Articles de type "nature" (1)
('Riz', 1, 'Riz blanc de bonne qualité', 2500),
('Haricots', 1, 'Haricots rouges de qualité supérieure', 4000),
('Huile de cuisine', 1, 'Huile végétale de qualité alimentaire', 15000),
('Sucre', 1, 'Sucre blanc raffiné', 8000),
('Farine', 1, 'Farine de blé de bonne qualité', 5000),
-- Articles de type "matériel" (2)
('Tôles ondulées', 2, 'Tôles ondulées en acier galvanisé', 55000),
('Ciment', 2, 'Ciment Portland standard', 45000),
('Clous', 2, 'Clous de construction standard', 500),
('Bois (m3)', 2, 'Bois brut pour construction', 35000),
('Briques', 2, 'Briques en terre cuite standard', 12000);

-- ========================================
-- 4. BESOINS
-- ========================================
INSERT INTO besoins (id_ville, description, urgence, statut) VALUES 
(1, 'Aide alimentaire urgente pour orphelinat', 'critique', 'en_cours'),
(1, 'Matériaux pour reconstruction école', 'urgente', 'en_cours'),
(2, 'Ravitaillement général communauté côtière', 'normale', 'en_cours'),
(2, 'Réparation toitures après tempête', 'critique', 'partiel');

-- ========================================
-- 5. ARTICLES DES BESOINS
-- ========================================
-- Besoin 1: Aide alimentaire orphelinat
       INSERT INTO besoin_articles (id_besoin, id_article, quantite, prix_unitaire,quantite_satisfaite) VALUES 
       (1, 1, 500, 2500, 500),      -- 500kg de riz
       (1, 2, 100, 4000, 100),      -- 100kg haricots
       (1, 3, 50, 15000, 50);      -- 50L huile

-- Besoin 2: Matériaux école
INSERT INTO besoin_articles (id_besoin, id_article, quantite, prix_unitaire,quantite_satisfaite) VALUES 
(2, 7, 200, 45000, 200),     -- 200 sacs ciment
(2, 8, 1000, 500, 1000),      -- 1000 clous
(2, 9, 100, 35000, 100);     -- 100m3 bois

-- Besoin 3: Ravitaillement côtier
INSERT INTO besoin_articles (id_besoin, id_article, quantite, prix_unitaire,quantite_satisfaite) VALUES 
(3, 1, 300, 2500, 300),      -- 300kg riz
(3, 4, 150, 8000, 150),      -- 150kg sucre
(3, 5, 80, 5000, 80);       -- 80kg farine

-- Besoin 4: Toitures
INSERT INTO besoin_articles (id_besoin, id_article, quantite, prix_unitaire,quantite_satisfaite ) VALUES 
(4, 6, 150, 55000, 150),     -- 150 tôles ondulées
(4, 10, 500, 12000, 500);    -- 500 briques

-- ========================================
-- 6. DONS
-- ========================================
INSERT INTO dons (id_type_don, id_article, description_don, quantite, montant_argent, donateur_nom, donateur_contact, statut) VALUES 
-- Dons en nature
(1, 1, 'Don de riz pour orphelinat', 300, NULL, 'Coopérative Agricole Andapa', '+261340001111', 'disponible'),
(1, 2, 'Don de haricots', 50, NULL, 'Ferme Familiale', '+261340002222', 'affecte'),
(1, 3, 'Don d\'huile de cuisine', 25, NULL, 'Commerce Rakoto', '+261340003333', 'disponible'),

-- Dons en matériau
(2, 7, 'Don de ciment', 100, NULL, 'Cimenterie Madagascar', '+261340004444', 'affecte'),
(2, 6, 'Don de tôles ondulées', 75, NULL, 'Quincaillerie Central', '+261340005555', 'disponible'),
(2, 10, 'Don de briques', 300, NULL, 'Briqueterie du Nord', '+261340006666', 'affecte'),

-- Dons en argent
(3, NULL, 'Donation pour aide d\'urgence', NULL, 1000000, 'Jean Dupont', '+261340007777', 'disponible'),
(3, NULL, 'Contribution aide alimentaire', NULL, 500000, 'ONG Internationale', '+261340008888', 'affecte'),
(3, NULL, 'Donation construction', NULL, 2000000, 'Entreprise BTP', '+261340009999', 'disponible');

-- ========================================
-- 7. DISPATCH DES DONS
-- ========================================
INSERT INTO dispatch_dons (id_don, id_besoin, quantite_affectee, montant_affecte) VALUES 
(2, 1, 50, NULL),           -- Don haricots → Besoin 1
(4, 2, 100, NULL),          -- Don ciment → Besoin 2
(6, 4, 300, NULL),          -- Don briques → Besoin 4
(8, 1, NULL, 500000),       -- Donation argent → Besoin 1
(9, 2, NULL, 2000000);      -- Donation construction → Besoin 2

-- ========================================
-- VÉRIFICATION DES DONNÉES
-- ========================================
SELECT '=== RÉGIONS ===' AS info;
SELECT * FROM regions;

SELECT '=== VILLES ===' AS info;
SELECT * FROM villes;

SELECT '=== ARTICLES ===' AS info;
SELECT a.*, tb.libelle_type FROM articles a JOIN type_besoin tb ON a.id_type_besoin = tb.id_type_besoin;

SELECT '=== BESOINS AVEC ARTICLES ===' AS info;
SELECT b.id_besoin, v.nom_ville, b.urgence, b.statut, COUNT(ba.id_besoin_article) as nb_articles
FROM besoins b 
JOIN villes v ON b.id_ville = v.id_ville
LEFT JOIN besoin_articles ba ON b.id_besoin = ba.id_besoin
GROUP BY b.id_besoin;

SELECT '=== DONS ===' AS info;
SELECT d.id_don, td.libelle_type, COALESCE(a.nom_article, 'Argent') as article, 
       COALESCE(d.quantite, d.montant_argent) as montant, d.donateur_nom, d.statut
FROM dons d
JOIN type_don td ON d.id_type_don = td.id_type_don
LEFT JOIN articles a ON d.id_article = a.id_article;

