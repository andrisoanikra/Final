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
}