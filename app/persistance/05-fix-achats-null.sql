-- Modifier la table achats pour permettre id_article NULL
USE bngrc_dons;

ALTER TABLE achats MODIFY COLUMN id_article INT NULL;

SELECT 'Table achats modifiée avec succès - id_article peut maintenant être NULL' as Message;
