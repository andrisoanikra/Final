-- Ajouter des colonnes pour tracker le stock restant des dons
-- Date: 17 février 2026
-- Description: Permet de gérer les quantités disponibles après affectations partielles

-- Ajouter la colonne pour la quantité restante (articles)
ALTER TABLE dons 
ADD COLUMN quantite_restante DECIMAL(15,2) NULL AFTER quantite,
ADD COLUMN montant_restant DECIMAL(15,2) NULL AFTER montant_argent;

-- Initialiser les valeurs restantes = valeurs totales pour les dons existants
UPDATE dons 
SET quantite_restante = quantite 
WHERE quantite IS NOT NULL;

UPDATE dons 
SET montant_restant = montant_argent 
WHERE montant_argent IS NOT NULL;

-- Créer une vue pour faciliter les requêtes sur les dons disponibles
CREATE OR REPLACE VIEW v_dons_disponibles AS
SELECT 
    d.id_don,
    d.donateur_nom,
    d.donateur_contact,
    d.date_don,
    d.id_type_don,
    td.libelle_type,
    d.id_article,
    CASE 
        WHEN d.id_article IS NULL THEN '💰 Don en argent'
        ELSE a.nom_article
    END as nom_article,
    d.quantite as quantite_initiale,
    d.quantite_restante,
    d.montant_argent as montant_initial,
    d.montant_restant,
    d.statut,
    -- Calculer la quantité affectée
    COALESCE((SELECT SUM(dd.quantite_affectee) 
              FROM dispatch_dons dd 
              WHERE dd.id_don = d.id_don), 0) as quantite_affectee,
    -- Calculer le montant affecté
    COALESCE((SELECT SUM(dd.montant_affecte) 
              FROM dispatch_dons dd 
              WHERE dd.id_don = d.id_don), 0) as montant_affecte
FROM dons d
LEFT JOIN type_don td ON d.id_type_don = td.id_type_don
LEFT JOIN articles a ON d.id_article = a.id_article
WHERE d.statut IN ('disponible', 'partiel');

-- Créer un trigger pour mettre à jour automatiquement les quantités restantes
DELIMITER $$

CREATE TRIGGER after_dispatch_insert
AFTER INSERT ON dispatch_dons
FOR EACH ROW
BEGIN
    -- Mettre à jour la quantité restante si don d'article
    IF (SELECT id_article FROM dons WHERE id_don = NEW.id_don) IS NOT NULL THEN
        UPDATE dons 
        SET quantite_restante = quantite - COALESCE((
            SELECT SUM(quantite_affectee) 
            FROM dispatch_dons 
            WHERE id_don = NEW.id_don
        ), 0)
        WHERE id_don = NEW.id_don;
    END IF;
    
    -- Mettre à jour le montant restant si don en argent
    IF (SELECT montant_argent FROM dons WHERE id_don = NEW.id_don) IS NOT NULL THEN
        UPDATE dons 
        SET montant_restant = montant_argent - COALESCE((
            SELECT SUM(montant_affecte) 
            FROM dispatch_dons 
            WHERE id_don = NEW.id_don
        ), 0)
        WHERE id_don = NEW.id_don;
    END IF;
    
    -- Mettre à jour le statut
    UPDATE dons d
    SET statut = CASE
        WHEN d.id_article IS NOT NULL THEN
            CASE 
                WHEN (SELECT SUM(dd.quantite_affectee) FROM dispatch_dons dd WHERE dd.id_don = d.id_don) >= d.quantite 
                    THEN 'affecte'
                WHEN (SELECT SUM(dd.quantite_affectee) FROM dispatch_dons dd WHERE dd.id_don = d.id_don) > 0 
                    THEN 'partiel'
                ELSE 'disponible'
            END
        ELSE
            CASE 
                WHEN (SELECT SUM(dd.montant_affecte) FROM dispatch_dons dd WHERE dd.id_don = d.id_don) >= d.montant_argent 
                    THEN 'affecte'
                WHEN (SELECT SUM(dd.montant_affecte) FROM dispatch_dons dd WHERE dd.id_don = d.id_don) > 0 
                    THEN 'partiel'
                ELSE 'disponible'
            END
    END
    WHERE id_don = NEW.id_don;
END$$

DELIMITER ;
