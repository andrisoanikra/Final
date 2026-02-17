-- Permettre id_article NULL pour supporter les besoins en argent
-- Date: 2026-02-16
-- Description: Modification de la colonne id_article pour accepter NULL (besoins en argent)

ALTER TABLE besoin_articles 
MODIFY COLUMN id_article INT NULL COMMENT 'NULL = besoin en argent';
