<?php
namespace app\models;

class BesoinsModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getBesoins()
    {
        $tmt = $this->db->runQuery("SELECT b.*, v.nom_ville, a.nom_article, tb.libelle_type 
        FROM besoins b
        LEFT JOIN villes v ON b.id_ville = v.id_ville
        LEFT JOIN articles a ON b.id_article = a.id_article
        LEFT JOIN type_besoin tb ON a.id_type_besoin = tb.id_type_besoin
        ORDER BY b.date_saisie DESC;");
        return $tmt->fetchAll();
    }

    public function getAllBesoinsWithDetails()
    {
        return $this->getBesoins();
    }

    public function getBesoinById($id)
    {
        $tmt = $this->db->runQuery("SELECT b.*, v.nom_ville, v.id_region, a.nom_article, a.id_type_besoin, tb.libelle_type
        FROM besoins b
        LEFT JOIN villes v ON b.id_ville = v.id_ville
        LEFT JOIN articles a ON b.id_article = a.id_article
        LEFT JOIN type_besoin tb ON a.id_type_besoin = tb.id_type_besoin
        WHERE b.id_besoin = ?;", [$id]);
        return $tmt->fetch();
    }

    public function getBesoinsByVille($id_ville)
    {
        $tmt = $this->db->runQuery("SELECT b.*, v.nom_ville, a.nom_article, tb.libelle_type
        FROM besoins b
        LEFT JOIN villes v ON b.id_ville = v.id_ville
        LEFT JOIN articles a ON b.id_article = a.id_article
        LEFT JOIN type_besoin tb ON a.id_type_besoin = tb.id_type_besoin
        WHERE b.id_ville = ?
        ORDER BY b.date_saisie DESC;", [$id_ville]);
        return $tmt->fetchAll();
    }

    public function getBesoinsByArticle($id_article)
    {
        $tmt = $this->db->runQuery("SELECT b.*, v.nom_ville, a.nom_article, tb.libelle_type
        FROM besoins b
        LEFT JOIN villes v ON b.id_ville = v.id_ville
        LEFT JOIN articles a ON b.id_article = a.id_article
        LEFT JOIN type_besoin tb ON a.id_type_besoin = tb.id_type_besoin
        WHERE b.id_article = ?
        ORDER BY b.date_saisie DESC;", [$id_article]);
        return $tmt->fetchAll();
    }

    public function addBesoin($id_ville, $id_article, $quantite, $prix_unitaire, $statut = 'en_cours', $description = null, $urgence = 'normale')
    {
        $tmt = $this->db->runQuery("INSERT INTO besoins (id_ville, id_article, quantite, prix_unitaire, statut, description, urgence) 
        VALUES (?, ?, ?, ?, ?, ?, ?);", [$id_ville, $id_article, $quantite, $prix_unitaire, $statut, $description, $urgence]);
        return $tmt->rowCount();
    }

    public function updateBesoin($id, $id_ville, $id_article, $quantite, $prix_unitaire, $statut)
    {
        $tmt = $this->db->runQuery("UPDATE besoins SET id_ville = ?, id_article = ?, quantite = ?, prix_unitaire = ?, statut = ? 
        WHERE id_besoin = ?;", [$id_ville, $id_article, $quantite, $prix_unitaire, $statut, $id]);
        return $tmt->rowCount();
    }

    public function updateBesoinStatut($id, $statut)
    {
        $tmt = $this->db->runQuery("UPDATE besoins SET statut = ? WHERE id_besoin = ?;", [$statut, $id]);
        return $tmt->rowCount();
    }

    public function deleteBesoin($id)
    {
        $tmt = $this->db->runQuery("DELETE FROM besoins WHERE id_besoin = ?;", [$id]);
        return $tmt->rowCount();
    }

    public function getBesoinsNonSatisfaits()
    {
        $tmt = $this->db->runQuery("SELECT b.*, v.nom_ville, a.nom_article, tb.libelle_type
        FROM besoins b
        LEFT JOIN villes v ON b.id_ville = v.id_ville
        LEFT JOIN articles a ON b.id_article = a.id_article
        LEFT JOIN type_besoin tb ON a.id_type_besoin = tb.id_type_besoin
        WHERE b.statut != 'satisfait'
        ORDER BY b.date_saisie ASC;");
        return $tmt->fetchAll();
    }

    public function getNombreBesoins()
    {
        $tmt = $this->db->runQuery("SELECT COUNT(*) AS total FROM besoins;");
        return $tmt->fetch();
    }

    public function getMontantTotal()
    {
        $tmt = $this->db->runQuery("SELECT SUM(quantite * prix_unitaire) AS total FROM besoins;");
        return $tmt->fetch();
    }

}

