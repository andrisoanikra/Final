<?php

namespace app\models;

class VillesModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Récupère toutes les villes avec leur région
     */
    public function getAll()
    {
        return $this->getAllVilles();
    }

    /**
     * Récupère toutes les villes avec leur région
     */
    public function getAllVilles()
    {
        $stmt = $this->db->runQuery("
            SELECT v.*, r.nom_region 
            FROM villes v
            JOIN regions r ON v.id_region = r.id_region
            ORDER BY r.nom_region, v.nom_ville
        ");
        return $stmt->fetchAll();
    }

    /**
     * Récupère une ville par son ID
     */
    public function getById($id)
    {
        return $this->getVilleById($id);
    }

    /**
     * Récupère une ville par son ID
     */
    public function getVilleById($id)
    {
        $stmt = $this->db->runQuery("
            SELECT v.*, r.nom_region 
            FROM villes v
            JOIN regions r ON v.id_region = r.id_region
            WHERE v.id_ville = ?
        ", [$id]);
        return $stmt->fetch();
    }

    /**
     * Crée une nouvelle ville
     */
    public function create($id_region, $nom_ville, $description = null)
    {
        $stmt = $this->db->runQuery("
            INSERT INTO villes (id_region, nom_ville, description)
            VALUES (?, ?, ?)
        ", [$id_region, $nom_ville, $description]);
        return $stmt->rowCount();
    }

    /**
     * Met à jour une ville
     */
    public function update($id, $id_region, $nom_ville, $description = null)
    {
        $stmt = $this->db->runQuery("
            UPDATE villes
            SET id_region = ?, nom_ville = ?, description = ?
            WHERE id_ville = ?
        ", [$id_region, $nom_ville, $description, $id]);
        return $stmt->rowCount();
    }

    /**
     * Supprime une ville
     */
    public function delete($id)
    {
        $stmt = $this->db->runQuery("
            DELETE FROM villes
            WHERE id_ville = ?
        ", [$id]);
        return $stmt->rowCount();
    }

    /**
     * Compte le nombre de villes
     */
    public function count()
    {
        $stmt = $this->db->runQuery("
            SELECT COUNT(*) as total FROM villes
        ");
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    /**
     * Récupère les villes avec leurs statistiques de besoins
     */
    public function getVillesWithStats()
    {
        $stmt = $this->db->runQuery("
            SELECT 
                v.id_ville,
                v.nom_ville,
                v.population,
                r.nom_region,
                COUNT(DISTINCT b.id_besoin) AS total_besoins,
                COUNT(DISTINCT CASE WHEN b.statut != 'satisfait' THEN b.id_besoin END) AS besoins_non_satisfaits,
                COUNT(DISTINCT CASE WHEN b.urgence = 'critique' AND b.statut != 'satisfait' THEN b.id_besoin END) AS besoins_critiques,
                COALESCE(SUM(b.quantite * b.prix_unitaire), 0) AS montant_total_besoins,
                COALESCE(SUM(dd.montant_affecte), 0) AS montant_total_dons_recus
            FROM villes v
            JOIN regions r ON v.id_region = r.id_region
            LEFT JOIN besoins b ON v.id_ville = b.id_ville
            LEFT JOIN dispatch_dons dd ON b.id_besoin = dd.id_besoin
            GROUP BY v.id_ville, v.nom_ville, v.population, r.nom_region
            ORDER BY besoins_critiques DESC, besoins_non_satisfaits DESC
        ");
        return $stmt->fetchAll();
    }
}