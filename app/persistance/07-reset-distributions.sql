-- ========================================
-- RESET.SQL - Réinitialisation des distributions
-- ========================================
-- Ce fichier supprime uniquement les distributions/achats/suivis
-- Les besoins, dons, articles, villes restent intacts

USE bngrc_dons;

-- Nettoyer UNIQUEMENT les distributions et achats
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE achats;
TRUNCATE TABLE dispatch_dons;
TRUNCATE TABLE suivi_ville;
SET FOREIGN_KEY_CHECKS = 1;

-- Réinitialiser les quantités restantes des dons à leur valeur initiale
UPDATE dons SET 
    quantite_restante = quantite,
    montant_restant = montant_argent,
    statut = 'disponible'
WHERE id_don IS NOT NULL;

-- Réinitialiser les quantités satisfaites des besoins à 0
UPDATE besoin_articles SET quantite_satisfaite = 0;

-- Mettre à jour le statut des besoins
UPDATE besoins SET statut = 'en_cours' WHERE statut != 'satisfait';

-- Message de confirmation
SELECT '✅ Distributions réinitialisées avec succès! Besoins et dons conservés.' as Message;
