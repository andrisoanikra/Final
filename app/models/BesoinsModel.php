<?php
namespace app\models;

class BesoinsModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }


    /**
     * Récupère tous les besoins avec détails
     */
    public function getAllBesoins()
    {
        $sql = "SELECT * FROM v_besoins_par_ville ORDER BY date_saisie DESC";
        return $this->db->runQuery($sql)->fetchAll();
    }

    /**
     * Récupère les besoins par ville
     */
    public function getBesoinsByVille($villeId)
    {
        $sql = "SELECT * FROM v_besoins_par_ville WHERE id_ville = ? ORDER BY date_saisie DESC";
        return $this->db->runQuery($sql, [$villeId])->fetchAll();
    }

    /**
     * Récupère les besoins par statut
     */
    public function getBesoinsByStatut($statut)
    {
        $sql = "SELECT * FROM v_besoins_par_ville WHERE statut = ? ORDER BY date_saisie DESC";
        return $this->db->runQuery($sql, [$statut])->fetchAll();
    }

    public function getBesoins()
    {
        $tmt = $this->db->runQuery("SELECT b.*, v.nom_ville, GROUP_CONCAT(a.nom_article SEPARATOR ', ') as articles, 
            GROUP_CONCAT(tb.libelle_type SEPARATOR ', ') as types
        FROM besoins b
        LEFT JOIN villes v ON b.id_ville = v.id_ville
        LEFT JOIN besoin_articles ba ON b.id_besoin = ba.id_besoin
        LEFT JOIN articles a ON ba.id_article = a.id_article
        LEFT JOIN type_besoin tb ON a.id_type_besoin = tb.id_type_besoin
        GROUP BY b.id_besoin
        ORDER BY b.date_saisie DESC;");
        return $tmt->fetchAll();
    }

    public function getAllBesoinsWithDetails()
    {
        return $this->getBesoins();
    }

    public function getBesoinById($id)
    {
        $tmt = $this->db->runQuery("SELECT b.*, v.nom_ville, v.id_region
        FROM besoins b
        LEFT JOIN villes v ON b.id_ville = v.id_ville
        WHERE b.id_besoin = ?;", [$id]);
        $besoin = $tmt->fetch();
        
        if ($besoin) {
            // Récupérer les articles du besoin
            $besoin['articles'] = $this->getArticlesDuBesoin($id);
        }
        
        return $besoin;
    }

    public function getArticlesDuBesoin($id_besoin)
    {
        $tmt = $this->db->runQuery("SELECT ba.*, a.nom_article, tb.libelle_type
        FROM besoin_articles ba
        JOIN articles a ON ba.id_article = a.id_article
        JOIN type_besoin tb ON a.id_type_besoin = tb.id_type_besoin
        WHERE ba.id_besoin = ?;", [$id_besoin]);
        return $tmt->fetchAll();
    }

    // public function getBesoinsByVille($id_ville)
    // {
    //     $tmt = $this->db->runQuery("SELECT b.*, v.nom_ville, GROUP_CONCAT(a.nom_article SEPARATOR ', ') as articles,
    //         GROUP_CONCAT(tb.libelle_type SEPARATOR ', ') as types
    //     FROM besoins b
    //     LEFT JOIN villes v ON b.id_ville = v.id_ville
    //     LEFT JOIN besoin_articles ba ON b.id_besoin = ba.id_besoin
    //     LEFT JOIN articles a ON ba.id_article = a.id_article
    //     LEFT JOIN type_besoin tb ON a.id_type_besoin = tb.id_type_besoin
    //     WHERE b.id_ville = ?
    //     GROUP BY b.id_besoin
    //     ORDER BY b.date_saisie DESC;", [$id_ville]);
    //     return $tmt->fetchAll();
    // }

    public function getBesoinsByArticle($id_article)
    {
        $tmt = $this->db->runQuery("SELECT b.*, v.nom_ville, a.nom_article, tb.libelle_type
        FROM besoins b
        LEFT JOIN villes v ON b.id_ville = v.id_ville
        LEFT JOIN besoin_articles ba ON b.id_besoin = ba.id_besoin
        LEFT JOIN articles a ON ba.id_article = a.id_article
        LEFT JOIN type_besoin tb ON a.id_type_besoin = tb.id_type_besoin
        WHERE ba.id_article = ?
        GROUP BY b.id_besoin
        ORDER BY b.date_saisie DESC;", [$id_article]);
        return $tmt->fetchAll();
    }

    /**
     * Ajoute un nouveau besoin
     * @param int $id_ville
     * @param string $description
     * @param string $urgence
     * @return int ID du besoin créé
     */
    public function addBesoin($id_ville, $description = null, $urgence = 'normale')
    {
        $tmt = $this->db->runQuery("INSERT INTO besoins (id_ville, description, urgence) 
        VALUES (?, ?, ?);", [$id_ville, $description, $urgence]);
        
        // Retourner l'ID inséré
        return $this->db->lastInsertId();
    }

    /**
     * Ajoute un article à un besoin
     * @param int $id_besoin
     * @param int $id_article
     * @param decimal $quantite
     * @param decimal $prix_unitaire
     * @return int Nombre de lignes affectées
     */
    public function addArticleToBesoin($id_besoin, $id_article, $quantite, $prix_unitaire)
    {
        $tmt = $this->db->runQuery("INSERT INTO besoin_articles (id_besoin, id_article, quantite, prix_unitaire) 
        VALUES (?, ?, ?, ?);", [$id_besoin, $id_article, $quantite, $prix_unitaire]);
        return $tmt->rowCount();
    }

    public function updateBesoin($id, $id_ville, $description, $urgence)
    {
        $tmt = $this->db->runQuery("UPDATE besoins SET id_ville = ?, description = ?, urgence = ? 
        WHERE id_besoin = ?;", [$id_ville, $description, $urgence, $id]);
        return $tmt->rowCount();
    }

    public function updateBesoinStatut($id, $statut)
    {
        $tmt = $this->db->runQuery("UPDATE besoins SET statut = ? WHERE id_besoin = ?;", [$statut, $id]);
        return $tmt->rowCount();
    }

    public function deleteBesoin($id)
    {
        // La suppression en cascade va supprimer les articles associés
        $tmt = $this->db->runQuery("DELETE FROM besoins WHERE id_besoin = ?;", [$id]);
        return $tmt->rowCount();
    }

    public function getBesoinsNonSatisfaits()
    {
        $tmt = $this->db->runQuery("SELECT b.*, v.nom_ville, GROUP_CONCAT(a.nom_article SEPARATOR ', ') as articles,
            GROUP_CONCAT(tb.libelle_type SEPARATOR ', ') as types
        FROM besoins b
        LEFT JOIN villes v ON b.id_ville = v.id_ville
        LEFT JOIN besoin_articles ba ON b.id_besoin = ba.id_besoin
        LEFT JOIN articles a ON ba.id_article = a.id_article
        LEFT JOIN type_besoin tb ON a.id_type_besoin = tb.id_type_besoin
        WHERE b.statut != 'satisfait'
        GROUP BY b.id_besoin
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
        $tmt = $this->db->runQuery("SELECT SUM(ba.quantite * ba.prix_unitaire) AS total FROM besoin_articles ba;");
        return $tmt->fetch();
    }



}

