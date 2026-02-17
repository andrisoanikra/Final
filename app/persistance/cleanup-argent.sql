-- Supprimer l'article "Argent" s'il existe
-- Car l'argent n'est pas un article mais un type de don

DELETE FROM articles WHERE LOWER(nom_article) LIKE '%argent%';

-- Vérifier qu'il n'y a pas d'articles avec le type 'argent' (id_type_besoin = 3)
DELETE FROM articles WHERE id_type_besoin = 3;
