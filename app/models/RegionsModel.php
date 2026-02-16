<?php

namespace app\models;

class RegionsModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Récupère toutes les régions
     */
    public function getAll()
    {
        $stmt = $this->db->runQuery("
            SELECT * FROM regions
            ORDER BY nom_region
        ");
        return $stmt->fetchAll();
    }

    /**
     * Récupère une région par son ID
     */
    public function getById($id)
    {
        $stmt = $this->db->runQuery("
            SELECT * FROM regions
            WHERE id_region = ?
        ", [$id]);
        return $stmt->fetch();
    }

    /**
     * Crée une nouvelle région
     */
    public function create($nom_region)
    {
        $stmt = $this->db->runQuery("
            INSERT INTO regions (nom_region)
            VALUES (?)
        ", [$nom_region]);
        return $stmt->rowCount();
    }

    /**
     * Met à jour une région
     */
    public function update($id, $nom_region)
    {
        $stmt = $this->db->runQuery("
            UPDATE regions
            SET nom_region = ?
            WHERE id_region = ?
        ", [$nom_region, $id]);
        return $stmt->rowCount();
    }

    /**
     * Supprime une région
     */
    public function delete($id)
    {
        $stmt = $this->db->runQuery("
            DELETE FROM regions
            WHERE id_region = ?
        ", [$id]);
        return $stmt->rowCount();
    }

    /**
     * Compte le nombre de régions
     */
    public function count()
    {
        $stmt = $this->db->runQuery("
            SELECT COUNT(*) as total FROM regions
        ");
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    /**
     * Récupère une région avec toutes ses villes
     */
    public function getWithVilles($id)
    {
        $region = $this->getById($id);
        
        if (!$region) {
            return null;
        }

        $stmt = $this->db->runQuery("
            SELECT * FROM villes
            WHERE id_region = ?
            ORDER BY nom_ville
        ", [$id]);
        
        $region['villes'] = $stmt->fetchAll();
        return $region;
    }

    /**
     * Récupère toutes les régions avec leurs statistiques
     */
    public function getAllWithStats()
    {
        $stmt = $this->db->runQuery("
            SELECT 
                r.*,
                COUNT(DISTINCT v.id_ville) as nombre_villes,
                COUNT(DISTINCT b.id_besoin) as nombre_besoins
            FROM regions r
            LEFT JOIN villes v ON r.id_region = v.id_region
            LEFT JOIN besoins b ON v.id_ville = b.id_ville
            GROUP BY r.id_region
            ORDER BY r.nom_region
        ");
        return $stmt->fetchAll();
    }
}
