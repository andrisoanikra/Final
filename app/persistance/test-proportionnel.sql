-- Test de distribution proportionnelle
-- Scénario : Don de 5 unités de Riz pour 3 besoins demandant 1, 3, 5

-- Nettoyer les anciennes données de test
DELETE FROM dispatch_dons;
DELETE FROM achats;
UPDATE dons SET statut = 'disponible', quantite_restante = quantite, montant_restant = montant_argent;
UPDATE besoin_articles SET quantite_satisfaite = 0;
UPDATE besoins SET statut = 'en_cours';

-- Insérer 3 besoins de Riz avec quantités différentes
INSERT INTO besoins (id_ville, description_besoin, date_saisie, urgence, statut) VALUES
(1, 'Besoin urgent petit village', NOW(), 'urgente', 'en_cours'),
(1, 'Besoin moyen quartier', NOW(), 'urgente', 'en_cours'),
(1, 'Besoin important centre-ville', NOW(), 'urgente', 'en_cours');

-- Supposons que id_article = 1 est Riz (vérifier avec: SELECT * FROM articles WHERE nom_article LIKE '%riz%')
-- Ajouter les articles aux besoins : 1 kg, 3 kg, 5 kg
INSERT INTO besoin_articles (id_besoin, id_article, quantite, prix_unitaire, quantite_satisfaite) VALUES
(LAST_INSERT_ID() - 2, 1, 1, 5000, 0),   -- Besoin 1 : 1 kg
(LAST_INSERT_ID() - 1, 1, 3, 5000, 0),   -- Besoin 2 : 3 kg
(LAST_INSERT_ID(), 1, 5, 5000, 0);       -- Besoin 3 : 5 kg

-- Créer un don de 5 kg de Riz
INSERT INTO dons (id_type_don, id_article, description_don, quantite, quantite_restante, montant_argent, donateur_nom, donateur_contact, statut, date_don) VALUES
(1, 1, 'Don test distribution proportionnelle', 5, 5, 0, 'Testeur Proportionnel', '0340000000', 'disponible', NOW());

-- Afficher le résultat
SELECT 'Don créé avec succès !' as message;
SELECT CONCAT('ID du don : ', LAST_INSERT_ID()) as info;
SELECT CONCAT('Allez sur http://localhost:8000/dons et cliquez sur Valider pour ce don') as instruction;
