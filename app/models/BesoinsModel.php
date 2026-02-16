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
        $tmt = $this->db->runQuery("SELECT b.*, v.nom_ville, 
            GROUP_CONCAT(a.nom_article SEPARATOR ', ') as articles, 
            GROUP_CONCAT(tb.libelle_type SEPARATOR ', ') as types,
            (SELECT SUM(ba.quantite * ba.prix_unitaire) 
             FROM besoin_articles ba 
             WHERE ba.id_besoin = b.id_besoin) as montant_total,
            COALESCE((SELECT SUM(dd.montant_affecte) + SUM(
                CASE WHEN d.id_article IS NOT NULL 
                THEN dd.quantite_affectee * ba_prix.prix_unitaire 
                ELSE 0 END)
             FROM dispatch_dons dd
             JOIN dons d ON dd.id_don = d.id_don
             LEFT JOIN besoin_articles ba_prix ON dd.id_besoin = ba_prix.id_besoin AND d.id_article = ba_prix.id_article
             WHERE dd.id_besoin = b.id_besoin), 0) as montant_recu
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
        $tmt = $this->db->runQuery("SELECT b.*, v.nom_ville, v.id_region,
            (SELECT SUM(ba.quantite * ba.prix_unitaire) 
             FROM besoin_articles ba 
             WHERE ba.id_besoin = b.id_besoin) as montant_total,
            COALESCE((SELECT SUM(dd.montant_affecte) + SUM(
                CASE WHEN d.id_article IS NOT NULL 
                THEN dd.quantite_affectee * ba_prix.prix_unitaire 
                ELSE 0 END)
             FROM dispatch_dons dd
             JOIN dons d ON dd.id_don = d.id_don
             LEFT JOIN besoin_articles ba_prix ON dd.id_besoin = ba_prix.id_besoin AND d.id_article = ba_prix.id_article
             WHERE dd.id_besoin = b.id_besoin), 0) as montant_recu
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

    public function getBesoinsByVille($id_ville)
    {
        $tmt = $this->db->runQuery("SELECT b.*, v.nom_ville, 
            GROUP_CONCAT(a.nom_article SEPARATOR ', ') as articles,
            GROUP_CONCAT(tb.libelle_type SEPARATOR ', ') as types,
            (SELECT SUM(ba.quantite * ba.prix_unitaire) 
             FROM besoin_articles ba 
             WHERE ba.id_besoin = b.id_besoin) as montant_total,
            COALESCE((SELECT SUM(dd.montant_affecte) + SUM(
                CASE WHEN d.id_article IS NOT NULL 
                THEN dd.quantite_affectee * ba_prix.prix_unitaire 
                ELSE 0 END)
             FROM dispatch_dons dd
             JOIN dons d ON dd.id_don = d.id_don
             LEFT JOIN besoin_articles ba_prix ON dd.id_besoin = ba_prix.id_besoin AND d.id_article = ba_prix.id_article
             WHERE dd.id_besoin = b.id_besoin), 0) as montant_recu
        FROM besoins b
        LEFT JOIN villes v ON b.id_ville = v.id_ville
        LEFT JOIN besoin_articles ba ON b.id_besoin = ba.id_besoin
        LEFT JOIN articles a ON ba.id_article = a.id_article
        LEFT JOIN type_besoin tb ON a.id_type_besoin = tb.id_type_besoin
        WHERE b.id_ville = ?
        GROUP BY b.id_besoin
        ORDER BY b.date_saisie DESC;", [$id_ville]);
        return $tmt->fetchAll();
    }

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
        $tmt = $this->db->runQuery("SELECT b.*, v.nom_ville, 
            GROUP_CONCAT(a.nom_article SEPARATOR ', ') as articles,
            GROUP_CONCAT(tb.libelle_type SEPARATOR ', ') as types,
            (SELECT SUM(ba.quantite * ba.prix_unitaire) 
             FROM besoin_articles ba 
             WHERE ba.id_besoin = b.id_besoin) as montant_total,
            COALESCE((SELECT SUM(dd.montant_affecte) + SUM(
                CASE WHEN d.id_article IS NOT NULL 
                THEN dd.quantite_affectee * ba_prix.prix_unitaire 
                ELSE 0 END)
             FROM dispatch_dons dd
             JOIN dons d ON dd.id_don = d.id_don
             LEFT JOIN besoin_articles ba_prix ON dd.id_besoin = ba_prix.id_besoin AND d.id_article = ba_prix.id_article
             WHERE dd.id_besoin = b.id_besoin), 0) as montant_recu
        FROM besoins b
        LEFT JOIN villes v ON b.id_ville = v.id_ville
        LEFT JOIN besoin_articles ba ON b.id_besoin = ba.id_besoin
        LEFT JOIN articles a ON ba.id_article = a.id_article
        LEFT JOIN type_besoin tb ON a.id_type_besoin = tb.id_type_besoin
        WHERE b.statut IN ('en_cours', 'partiel')
        GROUP BY b.id_besoin
        ORDER BY 
            CASE b.urgence 
                WHEN 'critique' THEN 1
                WHEN 'urgente' THEN 2
                WHEN 'normale' THEN 3
                ELSE 4
            END,
            b.date_saisie ASC;");
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

    /**
     * Récupère les villes dont tous les besoins sont satisfaits
     */
    public function getVillesSatisfaites()
    {
        $tmt = $this->db->runQuery("
            SELECT v.id_ville, v.nom_ville, 
                COUNT(b.id_besoin) as nombre_besoins,
                SUM(
                    (SELECT SUM(ba.quantite * ba.prix_unitaire) 
                     FROM besoin_articles ba 
                     WHERE ba.id_besoin = b.id_besoin)
                ) as montant_total_besoins,
                SUM(
                    COALESCE((SELECT SUM(dd.montant_affecte) + SUM(
                        CASE WHEN d.id_article IS NOT NULL 
                        THEN dd.quantite_affectee * ba_prix.prix_unitaire 
                        ELSE 0 END)
                     FROM dispatch_dons dd
                     JOIN dons d ON dd.id_don = d.id_don
                     LEFT JOIN besoin_articles ba_prix ON dd.id_besoin = ba_prix.id_besoin AND d.id_article = ba_prix.id_article
                     WHERE dd.id_besoin = b.id_besoin), 0)
                ) as montant_total_recu
            FROM villes v
            JOIN besoins b ON v.id_ville = b.id_ville
            WHERE v.id_ville IN (
                -- Villes dont TOUS les besoins ont le statut 'satisfait'
                SELECT id_ville 
                FROM besoins 
                GROUP BY id_ville 
                HAVING COUNT(*) = SUM(CASE WHEN statut = 'satisfait' THEN 1 ELSE 0 END)
                AND COUNT(*) > 0
            )
            GROUP BY v.id_ville, v.nom_ville
            ORDER BY v.nom_ville ASC
        ");
        return $tmt->fetchAll();
    }

    /**
     * Récupère les besoins critiques de type matériel/nature uniquement
     */
    public function getBesoinsCritiquesMateriels()
    {
        $tmt = $this->db->runQuery("
            SELECT b.*, v.nom_ville, 
                GROUP_CONCAT(a.nom_article SEPARATOR ', ') as articles,
                GROUP_CONCAT(tb.libelle_type SEPARATOR ', ') as types,
                GROUP_CONCAT(DISTINCT ba.quantite SEPARATOR ', ') as quantites,
                (SELECT SUM(ba.quantite * ba.prix_unitaire) 
                 FROM besoin_articles ba 
                 WHERE ba.id_besoin = b.id_besoin) as montant_total,
                COALESCE((SELECT SUM(dd.montant_affecte) + SUM(
                    CASE WHEN d.id_article IS NOT NULL 
                    THEN dd.quantite_affectee * ba_prix.prix_unitaire 
                    ELSE 0 END)
                 FROM dispatch_dons dd
                 JOIN dons d ON dd.id_don = d.id_don
                 LEFT JOIN besoin_articles ba_prix ON dd.id_besoin = ba_prix.id_besoin AND d.id_article = ba_prix.id_article
                 WHERE dd.id_besoin = b.id_besoin), 0) as montant_recu
            FROM besoins b
            LEFT JOIN villes v ON b.id_ville = v.id_ville
            LEFT JOIN besoin_articles ba ON b.id_besoin = ba.id_besoin
            LEFT JOIN articles a ON ba.id_article = a.id_article
            LEFT JOIN type_besoin tb ON a.id_type_besoin = tb.id_type_besoin
            WHERE b.urgence = 'critique' 
            AND b.statut IN ('en_cours', 'partiel')
            AND tb.libelle_type IN ('Matériel', 'Nature')
            GROUP BY b.id_besoin
            ORDER BY b.date_saisie ASC
        ");
        return $tmt->fetchAll();
    }

}

