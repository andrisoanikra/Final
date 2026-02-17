<?php
namespace app\models;

class DonsModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Récupère tous les dons avec leurs détails
     */
    public function getAllDons()
    {
        $tmt = $this->db->runQuery("
            SELECT d.*, td.libelle_type, a.nom_article,
                CASE 
                    WHEN d.id_article IS NULL THEN 'Don en argent'
                    ELSE a.nom_article
                END as article_nom
            FROM dons d
            JOIN type_don td ON d.id_type_don = td.id_type_don
            LEFT JOIN articles a ON d.id_article = a.id_article
            ORDER BY d.date_don DESC
        ");
        return $tmt->fetchAll();
    }

    /**
     * Récupère un don par son ID
     */
    public function getDonById($id)
    {
        $tmt = $this->db->runQuery("
            SELECT d.*, td.libelle_type, a.nom_article
            FROM dons d
            JOIN type_don td ON d.id_type_don = td.id_type_don
            LEFT JOIN articles a ON d.id_article = a.id_article
            WHERE d.id_don = ?
        ", [$id]);
        return $tmt->fetch();
    }

    /**
     * Ajoute un nouveau don
     */
    public function addDon($id_type_don, $id_article, $description, $quantite, $montant_argent, $donateur_nom, $donateur_contact)
    {
        $tmt = $this->db->runQuery("
            INSERT INTO dons (id_type_don, id_article, description_don, quantite, montant_argent, donateur_nom, donateur_contact, statut) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'disponible')
        ", [$id_type_don, $id_article, $description, $quantite, $montant_argent, $donateur_nom, $donateur_contact]);
        
        return $this->db->lastInsertId();
    }

    /**
     * Met à jour un don
     */
    public function updateDon($id, $id_type_don, $id_article, $description, $quantite, $montant_argent, $donateur_nom, $donateur_contact)
    {
        $tmt = $this->db->runQuery("
            UPDATE dons 
            SET id_type_don = ?, id_article = ?, description_don = ?, quantite = ?, 
                montant_argent = ?, donateur_nom = ?, donateur_contact = ?
            WHERE id_don = ?
        ", [$id_type_don, $id_article, $description, $quantite, $montant_argent, $donateur_nom, $donateur_contact, $id]);
        return $tmt->rowCount();
    }

    /**
     * Met à jour le statut d'un don
     */
    public function updateStatut($id, $statut)
    {
        $tmt = $this->db->runQuery("UPDATE dons SET statut = ? WHERE id_don = ?", [$statut, $id]);
        return $tmt->rowCount();
    }

    /**
     * Supprime un don
     */
    public function deleteDon($id)
    {
        $tmt = $this->db->runQuery("DELETE FROM dons WHERE id_don = ?", [$id]);
        return $tmt->rowCount();
    }

    /**
     * Récupère les dons disponibles
     */
    public function getDonsDisponibles()
    {
        $tmt = $this->db->runQuery("
            SELECT d.*, td.libelle_type, a.nom_article
            FROM dons d
            JOIN type_don td ON d.id_type_don = td.id_type_don
            LEFT JOIN articles a ON d.id_article = a.id_article
            WHERE d.statut = 'disponible'
            ORDER BY d.date_don DESC
        ");
        return $tmt->fetchAll();
    }

    /**
     * Récupère le montant total des dons en argent
     */
    public function getMontantTotalArgent()
    {
        $tmt = $this->db->runQuery("SELECT SUM(montant_argent) AS total FROM dons WHERE montant_argent IS NOT NULL");
        $result = $tmt->fetch();
        return $result['total'] ?? 0;
    }

    /**
     * Compte le nombre de dons
     */
    public function getNombreDons()
    {
        $tmt = $this->db->runQuery("SELECT COUNT(*) AS total FROM dons");
        $result = $tmt->fetch();
        return $result['total'] ?? 0;
    }

    /**
     * Dispatche un don vers les besoins correspondants par ordre de date
     */
    public function dispatcherDon($id_don)
    {
        // Récupérer les informations du don
        $don = $this->getDonById($id_don);
        
        if (!$don || $don['statut'] != 'disponible') {
            return ['success' => false, 'message' => 'Don non disponible'];
        }

        $id_article = $don['id_article'];
        $quantite_disponible = $don['quantite'] ?? 0;
        $montant_disponible = $don['montant_argent'] ?? 0;

        // Si c'est un don en argent
        if ($id_article === null && $montant_disponible > 0) {
            return $this->dispatcherDonArgent($id_don, $montant_disponible);
        }

        // Si c'est un don en nature/matériel
        if ($id_article && $quantite_disponible > 0) {
            return $this->dispatcherDonMateriel($id_don, $id_article, $quantite_disponible);
        }

        return ['success' => false, 'message' => 'Don invalide'];
    }

    /**
     * Dispatche un don en argent vers les besoins
     */
    private function dispatcherDonArgent($id_don, $montant_disponible)
    {
        // Récupérer TOUS les besoins non satisfaits, en excluant les villes dont tous les besoins sont couverts
        $tmt = $this->db->runQuery("
            SELECT ba.*, b.id_besoin, b.id_ville, b.date_saisie, b.urgence, a.nom_article, tb.libelle_type, v.nom_ville
            FROM besoin_articles ba
            JOIN besoins b ON ba.id_besoin = b.id_besoin
            JOIN articles a ON ba.id_article = a.id_article
            JOIN type_besoin tb ON a.id_type_besoin = tb.id_type_besoin
            JOIN villes v ON b.id_ville = v.id_ville
            WHERE b.statut IN ('en_cours', 'partiel')
            AND b.id_ville NOT IN (
                -- Exclure les villes dont TOUS les besoins sont satisfaits
                SELECT id_ville 
                FROM besoins 
                GROUP BY id_ville 
                HAVING COUNT(*) = SUM(CASE WHEN statut = 'satisfait' THEN 1 ELSE 0 END)
            )
            ORDER BY 
                CASE b.urgence
                    WHEN 'critique' THEN 1
                    WHEN 'urgente' THEN 2
                    WHEN 'normale' THEN 3
                END,
                b.date_saisie ASC
        ");
        $besoins = $tmt->fetchAll();

        $montant_restant = $montant_disponible;
        $dispatches = [];
        $villes_satisfaites = [];

        foreach ($besoins as $besoin) {
            if ($montant_restant <= 0) break;

            // Calculer le montant total du besoin (quantité × prix unitaire)
            $montant_besoin = $besoin['quantite'] * $besoin['prix_unitaire'];
            
            // Vérifier combien a déjà été dispatché pour ce besoin
            $tmt_deja_affecte = $this->db->runQuery("
                SELECT COALESCE(SUM(montant_affecte), 0) as total_affecte
                FROM dispatch_dons
                WHERE id_besoin = ?
            ", [$besoin['id_besoin']]);
            $deja_affecte = $tmt_deja_affecte->fetch()['total_affecte'];
            
            $montant_restant_besoin = $montant_besoin - $deja_affecte;
            
            if ($montant_restant_besoin <= 0) continue;
            
            $montant_a_affecter = min($montant_restant, $montant_restant_besoin);

            // Enregistrer le dispatch
            $this->db->runQuery("
                INSERT INTO dispatch_dons (id_don, id_besoin, quantite_affectee, montant_affecte, date_dispatch)
                VALUES (?, ?, 0, ?, NOW())
            ", [$id_don, $besoin['id_besoin'], $montant_a_affecter]);

            $dispatches[] = [
                'id_besoin' => $besoin['id_besoin'],
                'article' => $besoin['nom_article'],
                'montant' => $montant_a_affecter,
                'ville' => $besoin['nom_ville']
            ];

            $montant_restant -= $montant_a_affecter;

            // Vérifier si TOUS les articles de ce besoin sont maintenant couverts
            $tmt_verif_besoin = $this->db->runQuery("
                SELECT 
                    SUM(ba.quantite * ba.prix_unitaire) as montant_total_besoin,
                    COALESCE((
                        SELECT SUM(dd.montant_affecte) + SUM(
                            CASE 
                                WHEN d.id_article IS NOT NULL 
                                THEN dd.quantite_affectee * ba_prix.prix_unitaire 
                                ELSE 0 
                            END
                        )
                        FROM dispatch_dons dd
                        JOIN dons d ON dd.id_don = d.id_don
                        LEFT JOIN besoin_articles ba_prix ON dd.id_besoin = ba_prix.id_besoin AND d.id_article = ba_prix.id_article
                        WHERE dd.id_besoin = ?
                    ), 0) as montant_total_recu
                FROM besoin_articles ba
                WHERE ba.id_besoin = ?
            ", [$besoin['id_besoin'], $besoin['id_besoin']]);
            $verif_besoin = $tmt_verif_besoin->fetch();
            
            // Mettre à jour le statut du besoin
            if ($verif_besoin['montant_total_recu'] >= $verif_besoin['montant_total_besoin']) {
                $this->db->runQuery("UPDATE besoins SET statut = 'satisfait' WHERE id_besoin = ?", [$besoin['id_besoin']]);
                
                // Vérifier si tous les besoins de cette ville sont maintenant satisfaits
                $tmt_verif_ville = $this->db->runQuery("
                    SELECT COUNT(*) as total, SUM(CASE WHEN statut = 'satisfait' THEN 1 ELSE 0 END) as satisfaits
                    FROM besoins WHERE id_ville = ?
                ", [$besoin['id_ville']]);
                $verif_ville = $tmt_verif_ville->fetch();
                
                if ($verif_ville['total'] == $verif_ville['satisfaits'] && !in_array($besoin['nom_ville'], $villes_satisfaites)) {
                    $villes_satisfaites[] = $besoin['nom_ville'];
                }
            } else {
                $this->db->runQuery("UPDATE besoins SET statut = 'partiel' WHERE id_besoin = ?", [$besoin['id_besoin']]);
            }
        }

        // Mettre à jour le statut du don
        if ($montant_restant <= 0) {
            $this->updateStatut($id_don, 'utilise');
        } else {
            $this->updateStatut($id_don, 'affecte');
        }

        return [
            'success' => true,
            'dispatches' => $dispatches,
            'montant_affecte' => $montant_disponible - $montant_restant,
            'montant_restant' => $montant_restant,
            'villes_satisfaites' => $villes_satisfaites
        ];
    }

    /**
     * Dispatche un don en matériel vers les besoins
     */
    private function dispatcherDonMateriel($id_don, $id_article, $quantite_disponible)
    {
        // Récupérer tous les besoins de cet article non satisfaits, en excluant les villes entièrement couvertes
        $tmt = $this->db->runQuery("
            SELECT ba.*, b.id_besoin, b.id_ville, b.date_saisie, b.urgence, a.nom_article, v.nom_ville
            FROM besoin_articles ba
            JOIN besoins b ON ba.id_besoin = b.id_besoin
            JOIN articles a ON ba.id_article = a.id_article
            JOIN villes v ON b.id_ville = v.id_ville
            WHERE ba.id_article = ? 
            AND b.statut IN ('en_cours', 'partiel')
            AND b.id_ville NOT IN (
                -- Exclure les villes dont TOUS les besoins sont satisfaits
                SELECT id_ville 
                FROM besoins 
                GROUP BY id_ville 
                HAVING COUNT(*) = SUM(CASE WHEN statut = 'satisfait' THEN 1 ELSE 0 END)
            )
            ORDER BY 
                CASE b.urgence
                    WHEN 'critique' THEN 1
                    WHEN 'urgente' THEN 2
                    WHEN 'normale' THEN 3
                END,
                b.date_saisie ASC
        ", [$id_article]);
        $besoins = $tmt->fetchAll();

        $quantite_restante = $quantite_disponible;
        $dispatches = [];
        $villes_satisfaites = [];

        foreach ($besoins as $besoin) {
            if ($quantite_restante <= 0) break;

            $quantite_besoin = $besoin['quantite'];
            
            // Vérifier combien a déjà été dispatché pour cet article de ce besoin
            $tmt_deja_affecte = $this->db->runQuery("
                SELECT COALESCE(SUM(dd.quantite_affectee), 0) as total_affecte
                FROM dispatch_dons dd
                JOIN dons d ON dd.id_don = d.id_don
                WHERE dd.id_besoin = ? AND d.id_article = ?
            ", [$besoin['id_besoin'], $id_article]);
            $deja_affecte = $tmt_deja_affecte->fetch()['total_affecte'];
            
            $quantite_restant_besoin = $quantite_besoin - $deja_affecte;
            
            if ($quantite_restant_besoin <= 0) continue;
            
            $quantite_a_affecter = min($quantite_restante, $quantite_restant_besoin);

            // Enregistrer le dispatch
            $this->db->runQuery("
                INSERT INTO dispatch_dons (id_don, id_besoin, quantite_affectee, montant_affecte, date_dispatch)
                VALUES (?, ?, ?, 0, NOW())
            ", [$id_don, $besoin['id_besoin'], $quantite_a_affecter]);

            $dispatches[] = [
                'id_besoin' => $besoin['id_besoin'],
                'article' => $besoin['nom_article'],
                'quantite' => $quantite_a_affecter,
                'ville' => $besoin['nom_ville']
            ];

            $quantite_restante -= $quantite_a_affecter;

            // Vérifier si TOUS les articles du besoin sont satisfaits
            $tmt_verif = $this->db->runQuery("
                SELECT 
                    SUM(ba.quantite * ba.prix_unitaire) as montant_total_besoin,
                    COALESCE((
                        SELECT SUM(dd.montant_affecte) + SUM(
                            CASE 
                                WHEN d.id_article IS NOT NULL 
                                THEN dd.quantite_affectee * ba_prix.prix_unitaire 
                                ELSE 0 
                            END
                        )
                        FROM dispatch_dons dd
                        JOIN dons d ON dd.id_don = d.id_don
                        LEFT JOIN besoin_articles ba_prix ON dd.id_besoin = ba_prix.id_besoin AND d.id_article = ba_prix.id_article
                        WHERE dd.id_besoin = ?
                    ), 0) as montant_total_recu
                FROM besoin_articles ba
                WHERE ba.id_besoin = ?
            ", [$besoin['id_besoin'], $besoin['id_besoin']]);
            $verif = $tmt_verif->fetch();
            
            if ($verif['montant_total_recu'] >= $verif['montant_total_besoin']) {
                $this->db->runQuery("UPDATE besoins SET statut = 'satisfait' WHERE id_besoin = ?", [$besoin['id_besoin']]);
                
                // Vérifier si tous les besoins de cette ville sont maintenant satisfaits
                $tmt_verif_ville = $this->db->runQuery("
                    SELECT COUNT(*) as total, SUM(CASE WHEN statut = 'satisfait' THEN 1 ELSE 0 END) as satisfaits
                    FROM besoins WHERE id_ville = ?
                ", [$besoin['id_ville']]);
                $verif_ville = $tmt_verif_ville->fetch();
                
                if ($verif_ville['total'] == $verif_ville['satisfaits'] && !in_array($besoin['nom_ville'], $villes_satisfaites)) {
                    $villes_satisfaites[] = $besoin['nom_ville'];
                }
            } else {
                $this->db->runQuery("UPDATE besoins SET statut = 'partiel' WHERE id_besoin = ?", [$besoin['id_besoin']]);
            }
        }

        // Mettre à jour le statut du don
        if ($quantite_restante <= 0) {
            $this->updateStatut($id_don, 'utilise');
        } else {
            $this->updateStatut($id_don, 'affecte');
        }

        return [
            'success' => true,
            'dispatches' => $dispatches,
            'quantite_affectee' => $quantite_disponible - $quantite_restante,
            'quantite_restante' => $quantite_restante,
            'villes_satisfaites' => $villes_satisfaites
        ];
    }

    /**
     * Distribue un article à un besoin (pour distribution plus petit montant)
     */
    public function distribuerArticle($id_don, $id_besoin, $quantite)
    {
        // Récupérer les informations du besoin et de l'article
        $tmt = $this->db->runQuery("
            SELECT ba.prix_unitaire, ba.quantite_satisfaite, ba.quantite
            FROM besoin_articles ba
            JOIN dons d ON d.id_article = ba.id_article
            WHERE ba.id_besoin = ? AND d.id_don = ?
        ", [$id_besoin, $id_don]);
        $article = $tmt->fetch();
        
        if (!$article) {
            return false;
        }
        
        $montant = $quantite * floatval($article['prix_unitaire']);
        
        // Insérer dans dispatch_dons
        $this->db->runQuery("
            INSERT INTO dispatch_dons (id_don, id_besoin, quantite_affectee, montant_affecte, date_dispatch)
            VALUES (?, ?, ?, ?, NOW())
        ", [$id_don, $id_besoin, $quantite, $montant]);
        
        // Mettre à jour quantite_restante du don
        $this->db->runQuery("
            UPDATE dons 
            SET quantite_restante = GREATEST(0, quantite_restante - ?)
            WHERE id_don = ?
        ", [$quantite, $id_don]);
        
        // Mettre à jour quantite_satisfaite du besoin
        $this->db->runQuery("
            UPDATE besoin_articles 
            SET quantite_satisfaite = quantite_satisfaite + ?
            WHERE id_besoin = ? AND id_article = (SELECT id_article FROM dons WHERE id_don = ?)
        ", [$quantite, $id_besoin, $id_don]);
        
        // Vérifier et mettre à jour le statut du besoin
        $this->updateBesoinStatut($id_besoin);
        
        return true;
    }

    /**
     * Distribue de l'argent à un besoin (pour distribution plus petit montant)
     */
    public function distribuerArgent($id_don, $id_besoin, $montant)
    {
        // Insérer dans dispatch_dons
        $this->db->runQuery("
            INSERT INTO dispatch_dons (id_don, id_besoin, quantite_affectee, montant_affecte, date_dispatch)
            VALUES (?, ?, 0, ?, NOW())
        ", [$id_don, $id_besoin, $montant]);
        
        // Mettre à jour montant_restant du don
        $this->db->runQuery("
            UPDATE dons 
            SET montant_restant = GREATEST(0, montant_restant - ?)
            WHERE id_don = ?
        ", [$montant, $id_don]);
        
        // Mettre à jour le statut du besoin
        $this->updateBesoinStatut($id_besoin);
        
        return true;
    }

    /**
     * Met à jour le statut d'un besoin en fonction de sa satisfaction
     */
    private function updateBesoinStatut($id_besoin)
    {
        // Calculer montant total nécessaire et reçu
        $tmt = $this->db->runQuery("
            SELECT 
                SUM(ba.quantite * ba.prix_unitaire) as montant_total,
                (
                    -- Somme des dispatch_dons
                    COALESCE((SELECT SUM(montant_affecte) FROM dispatch_dons WHERE id_besoin = ?), 0)
                    +
                    -- Somme des achats
                    COALESCE((SELECT SUM(montant_article) FROM achats WHERE id_besoin = ? AND statut IN ('simule', 'valide')), 0)
                ) as montant_recu
            FROM besoin_articles ba
            WHERE ba.id_besoin = ?
        ", [$id_besoin, $id_besoin, $id_besoin]);
        
        $result = $tmt->fetch();
        $montant_total = floatval($result['montant_total']);
        $montant_recu = floatval($result['montant_recu']);
        
        if ($montant_recu >= $montant_total) {
            $this->db->runQuery("UPDATE besoins SET statut = 'satisfait' WHERE id_besoin = ?", [$id_besoin]);
        } elseif ($montant_recu > 0) {
            $this->db->runQuery("UPDATE besoins SET statut = 'partiel' WHERE id_besoin = ?", [$id_besoin]);
        }
    }
}
