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
        $sql = "
            SELECT a.*, tb.libelle_type 
            FROM articles a
            JOIN type_besoin tb ON a.id_type_besoin = tb.id_type_besoin
            ORDER BY tb.libelle_type, a.nom_article
        ";
        return $this->db->runQuery($sql)->fetchAll();
    }

    /**
     * Récupère un article par son ID
     */
    public function getArticleById($id)
    {
        $sql = "
            SELECT a.*, tb.libelle_type 
            FROM articles a
            JOIN type_besoin tb ON a.id_type_besoin = tb.id_type_besoin
            WHERE a.id_article = ?
        ";
        return $this->db->runQuery($sql, [$id])->fetch();
    }

    /**
     * Récupère les articles par type
     */
    public function getArticlesByType($typeId)
    {
        $sql = "
            SELECT * FROM articles 
            WHERE id_type_besoin = ?
            ORDER BY nom_article
        ";
        return $this->db->runQuery($sql, [$typeId])->fetchAll();
    }

    /**
     * Récupère tous les types de besoin (pour formulaire)
     */
    public function getAllTypesBesoin()
    {
        $sql = "SELECT * FROM type_besoin ORDER BY libelle_type";
        return $this->db->runQuery($sql)->fetchAll();
    }

    /**
     * Ajoute un nouvel article
     */
    public function createArticle($nom_article, $id_type_besoin, $description, $prix_unitaire = 0)
    {
        $sql = "INSERT INTO articles (nom_article, id_type_besoin, description, prix_unitaire) VALUES (?, ?, ?, ?)";
        $this->db->runQuery($sql, [$nom_article, $id_type_besoin, $description, $prix_unitaire]);
        return $this->db->lastInsertId();
    }

    /**
     * Met à jour un article
     */
    public function updateArticle($id, $nom_article, $id_type_besoin, $description, $prix_unitaire = 0)
    {
        $sql = "UPDATE articles SET nom_article = ?, id_type_besoin = ?, description = ?, prix_unitaire = ? WHERE id_article = ?";
        $stmt = $this->db->runQuery($sql, [$nom_article, $id_type_besoin, $description, $prix_unitaire, $id]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Supprime un article (vérifie d'abord s'il est utilisé)
     */
    public function deleteArticle($id)
    {
        // Vérifier si l'article est utilisé dans besoin_articles
        $checkBesoins = $this->db->runQuery("SELECT COUNT(*) as count FROM besoin_articles WHERE id_article = ?", [$id])->fetch();
        if($checkBesoins['count'] > 0) {
            return false;
        }
        
        // Vérifier si l'article est utilisé dans dons
        $checkDons = $this->db->runQuery("SELECT COUNT(*) as count FROM dons WHERE id_article = ?", [$id])->fetch();
        if($checkDons['count'] > 0) {
            return false;
        }
        
        // Supprimer si non utilisé
        $stmt = $this->db->runQuery("DELETE FROM articles WHERE id_article = ?", [$id]);
        return $stmt->rowCount() > 0;
    }
}