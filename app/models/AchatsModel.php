<?php
namespace app\models;

class AchatsModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Récupère le taux de frais d'achat configuré
     */
    public function getFraisAchat()
    {
        $tmt = $this->db->runQuery("
            SELECT valeur FROM configuration WHERE cle = 'frais_achat_pourcentage' LIMIT 1
        ");
        $result = $tmt->fetch();
        return $result ? floatval($result['valeur']) : 10; // Par défaut 10%
    }

    /**
     * Met à jour le taux de frais d'achat
     */
    public function updateFraisAchat($pourcentage)
    {
        $tmt = $this->db->runQuery("
            INSERT INTO configuration (cle, valeur) VALUES ('frais_achat_pourcentage', ?)
            ON DUPLICATE KEY UPDATE valeur = ?
        ", [$pourcentage, $pourcentage]);
        return $tmt->rowCount();
    }

    /**
     * Crée un achat simulé (non validé)
     */
    public function createAchatSimule($id_don_argent, $id_besoin, $id_article, $quantite, $prix_unitaire, $frais_pourcentage)
    {
        $montant_article = $quantite * $prix_unitaire;
        $montant_frais = $montant_article * ($frais_pourcentage / 100);
        $montant_total = $montant_article + $montant_frais;

        $tmt = $this->db->runQuery("
            INSERT INTO achats (id_don_argent, id_besoin, id_article, quantite, prix_unitaire, 
                               montant_article, frais_pourcentage, montant_frais, montant_total, statut)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'simule')
        ", [$id_don_argent, $id_besoin, $id_article, $quantite, $prix_unitaire, 
            $montant_article, $frais_pourcentage, $montant_frais, $montant_total]);
        
        return $this->db->lastInsertId();
    }

    /**
     * Valide un achat simulé et crée le don correspondant
     */
    public function validerAchat($id_achat)
    {
        // Récupérer l'achat
        $achat = $this->getAchatById($id_achat);
        if (!$achat || $achat['statut'] != 'simule') {
            return ['success' => false, 'message' => 'Achat invalide ou déjà validé'];
        }

        // Vérifier si le don en argent a assez de fonds
        $don = $this->db->runQuery("SELECT * FROM dons WHERE id_don = ?", [$achat['id_don_argent']])->fetch();
        if (!$don || $don['montant_argent'] < $achat['montant_total']) {
            return ['success' => false, 'message' => 'Fonds insuffisants dans le don'];
        }

        // Créer un nouveau don matériel/nature
        $this->db->runQuery("
            INSERT INTO dons (id_type_don, id_article, description_don, quantite, montant_argent, 
                             donateur_nom, donateur_contact, statut)
            VALUES (?, ?, ?, ?, NULL, ?, ?, 'disponible')
        ", [
            $achat['id_type_besoin'] == 1 ? 1 : 2, // 1=Nature, 2=Matériel
            $achat['id_article'],
            'Achat via don #' . $achat['id_don_argent'] . ' - ' . $achat['nom_article'],
            $achat['quantite'],
            'BNGRC - Achat automatique',
            'Système'
        ]);
        
        $id_don_cree = $this->db->lastInsertId();

        // Déduire le montant du don en argent
        $this->db->runQuery("
            UPDATE dons SET montant_argent = montant_argent - ? WHERE id_don = ?
        ", [$achat['montant_total'], $achat['id_don_argent']]);

        // Mettre à jour le statut de l'achat
        $this->db->runQuery("
            UPDATE achats SET statut = 'valide', id_don_cree = ?, date_validation = NOW() 
            WHERE id_achat = ?
        ", [$id_don_cree, $id_achat]);

        return [
            'success' => true, 
            'message' => 'Achat validé avec succès',
            'id_don_cree' => $id_don_cree
        ];
    }

    /**
     * Récupère tous les achats (simulés et validés)
     */
    public function getAllAchats($filtre_ville = null)
    {
        $sql = "
            SELECT a.*, 
                   d.montant_argent as montant_don_source,
                   d.donateur_nom as donateur_source,
                   art.nom_article,
                   art.id_type_besoin,
                   tb.libelle_type,
                   b.description as description_besoin,
                   b.urgence,
                   v.nom_ville,
                   v.id_ville
            FROM achats a
            JOIN dons d ON a.id_don_argent = d.id_don
            JOIN articles art ON a.id_article = art.id_article
            JOIN type_besoin tb ON art.id_type_besoin = tb.id_type_besoin
            JOIN besoins b ON a.id_besoin = b.id_besoin
            JOIN villes v ON b.id_ville = v.id_ville
        ";
        
        if ($filtre_ville) {
            $sql .= " WHERE v.id_ville = ?";
            return $this->db->runQuery($sql, [$filtre_ville])->fetchAll();
        }
        
        $sql .= " ORDER BY a.date_creation DESC";
        return $this->db->runQuery($sql)->fetchAll();
    }

    /**
     * Récupère un achat par ID
     */
    public function getAchatById($id)
    {
        $tmt = $this->db->runQuery("
            SELECT a.*, 
                   art.nom_article,
                   art.id_type_besoin,
                   tb.libelle_type,
                   v.nom_ville,
                   b.urgence
            FROM achats a
            JOIN articles art ON a.id_article = art.id_article
            JOIN type_besoin tb ON art.id_type_besoin = tb.id_type_besoin
            JOIN besoins b ON a.id_besoin = b.id_besoin
            JOIN villes v ON b.id_ville = v.id_ville
            WHERE a.id_achat = ?
        ", [$id]);
        return $tmt->fetch();
    }

    /**
     * Supprime un achat simulé
     */
    public function deleteAchatSimule($id)
    {
        $tmt = $this->db->runQuery("
            DELETE FROM achats WHERE id_achat = ? AND statut = 'simule'
        ", [$id]);
        return $tmt->rowCount();
    }

    /**
     * Vérifie si un article existe déjà dans les dons disponibles
     */
    public function articleExisteDansDons($id_article)
    {
        $tmt = $this->db->runQuery("
            SELECT COUNT(*) as count FROM dons 
            WHERE id_article = ? AND statut IN ('disponible', 'affecte')
        ", [$id_article]);
        $result = $tmt->fetch();
        return $result['count'] > 0;
    }

    /**
     * Récupère les dons en argent disponibles
     */
    public function getDonsArgentDisponibles()
    {
        $tmt = $this->db->runQuery("
            SELECT d.*, 
                   (d.montant_argent - COALESCE(
                       (SELECT SUM(montant_total) FROM achats WHERE id_don_argent = d.id_don AND statut = 'simule'), 0
                   )) as montant_disponible
            FROM dons d
            WHERE d.id_type_don = 3 
            AND d.statut IN ('disponible', 'affecte')
            AND d.montant_argent > 0
            HAVING montant_disponible > 0
            ORDER BY d.date_don DESC
        ");
        return $tmt->fetchAll();
    }
}
