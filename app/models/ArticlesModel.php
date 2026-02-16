<?php

namespace app\models;

class ArticlesModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Récupère tous les articles avec leur type
     */
    public function getAllArticles()
    {
        $stmt = $this->db->runQuery("
            SELECT a.*, tb.libelle_type 
            FROM articles a
            JOIN type_besoin tb ON a.id_type_besoin = tb.id_type_besoin
            ORDER BY tb.libelle_type, a.nom_article
        ");
        return $stmt->fetchAll();
    }

    /**
     * Récupère un article par son ID
     */
    public function getArticleById($id)
    {
        $stmt = $this->db->runQuery("
            SELECT a.*, tb.libelle_type 
            FROM articles a
            JOIN type_besoin tb ON a.id_type_besoin = tb.id_type_besoin
            WHERE a.id_article = ?
        ", [$id]);
        return $stmt->fetch();
    }

    /**
     * Récupère les articles par type
     */
    public function getArticlesByType($typeId)
    {
        $stmt = $this->db->runQuery("
            SELECT * FROM articles 
            WHERE id_type_besoin = ?
            ORDER BY nom_article
        ", [$typeId]);
        return $stmt->fetchAll();
    }

     /**
     * Récupère tous les types de besoin (pour formulaire)
     */
    public function getAllTypesBesoin()
    {
        $stmt = $this->db->runQuery("
            SELECT * FROM type_besoin 
            ORDER BY libelle_type
        ");
        return $stmt->fetchAll();
    }

    /**
     * Ajoute un nouvel article
     */
    public function createArticle($nom_article, $id_type_besoin, $description)
    {
        $stmt = $this->db->runQuery("
            INSERT INTO articles (nom_article, id_type_besoin, description) 
            VALUES (?, ?, ?)
        ", [$nom_article, $id_type_besoin, $description]);
        
        return $this->db->lastInsertId();
    }

    /**
     * Met à jour un article
     */
    public function updateArticle($id, $nom_article, $id_type_besoin, $description)
    {
        $stmt = $this->db->runQuery("
            UPDATE articles 
            SET nom_article = ?, id_type_besoin = ?, description = ? 
            WHERE id_article = ?
        ", [$nom_article, $id_type_besoin, $description, $id]);
        
        return $stmt->rowCount() > 0;
    }

    /**
     * Supprime un article (vérifie d'abord s'il est utilisé)
     */
    public function deleteArticle($id)
    {
        // Vérifier si l'article est utilisé dans besoins
        $checkBesoins = $this->db->runQuery("
            SELECT COUNT(*) as count FROM besoins WHERE id_article = ?
        ", [$id]);
        $besoinsCount = $checkBesoins->fetch()->count;
        
        if($besoinsCount > 0) {
            return false; // Article utilisé dans besoins
        }
        
        // Vérifier si l'article est utilisé dans dons
        $checkDons = $this->db->runQuery("
            SELECT COUNT(*) as count FROM dons WHERE id_article = ?
        ", [$id]);
        $donsCount = $checkDons->fetch()->count;
        
        if($donsCount > 0) {
            return false; // Article utilisé dans dons
        }
        
        // Supprimer si non utilisé
        $stmt = $this->db->runQuery("
            DELETE FROM articles WHERE id_article = ?
        ", [$id]);
        
        return $stmt->rowCount() > 0;
    }
}