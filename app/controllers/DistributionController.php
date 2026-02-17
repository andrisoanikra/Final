<?php

namespace app\controllers;

use Flight;

class DistributionController
{
    protected $db;
    protected $besoinsModel;
    protected $donsModel;

    public function __construct()
    {
        $this->db = Flight::db();
        $this->besoinsModel = new \app\models\BesoinsModel();
        $this->donsModel = new \app\models\DonsModel();
    }

    /**
     * Affiche la page de distribution automatique
     */
    public function index()
    {
        Flight::render('distribution/index', []);
    }

    /**
     * Distribution automatique : donne priorité aux besoins les plus petits
     */
    public function distribuerParMontant()
    {
        try {
            // Récupérer tous les besoins non satisfaits, triés par montant total croissant
            $besoins = $this->getBesoinsNonSatisfaits();
            
            if (empty($besoins)) {
                Flight::redirect('/distribution?warning=' . urlencode('Aucun besoin à satisfaire'));
                return;
            }

            $distributionsEffectuees = 0;
            $montantTotalDistribue = 0;
            $details = [];

            // Pour chaque besoin (du plus petit au plus grand)
            foreach ($besoins as $besoin) {
                // Récupérer les articles du besoin
                $articles = $this->besoinsModel->getArticlesDuBesoin($besoin['id_besoin']);
                
                foreach ($articles as $article) {
                    $quantiteRestante = $article['quantite'] - $article['quantite_satisfaite'];
                    
                    if ($quantiteRestante <= 0) {
                        continue; // Déjà satisfait
                    }

                    // Besoin en argent
                    if ($article['id_article'] === null) {
                        $montantRestant = $article['prix_unitaire'] - $article['quantite_satisfaite'];
                        if ($montantRestant > 0) {
                            $result = $this->distribuerArgent($besoin['id_besoin'], $montantRestant);
                            if ($result['quantite'] > 0) {
                                $distributionsEffectuees++;
                                $montantTotalDistribue += $result['montant'];
                                $details[] = $result['detail'];
                            }
                        }
                    } 
                    // Besoin en articles
                    else {
                        $result = $this->distribuerArticle(
                            $besoin['id_besoin'], 
                            $article['id_article'], 
                            $quantiteRestante,
                            $article['prix_unitaire']
                        );
                        
                        if ($result['quantite'] > 0) {
                            $distributionsEffectuees++;
                            $montantTotalDistribue += $result['montant'];
                            $details[] = $result['detail'];
                        }
                    }
                }
            }

            if ($distributionsEffectuees > 0) {
                $message = "✅ Distribution automatique terminée ! " . 
                          "$distributionsEffectuees affectation(s) effectuée(s) pour un montant total de " . 
                          number_format($montantTotalDistribue, 0, ',', ' ') . " Ar";
                Flight::redirect('/distribution?success=' . urlencode($message));
            } else {
                Flight::redirect('/distribution?warning=' . urlencode('Aucun don disponible pour satisfaire les besoins'));
            }

        } catch (\Exception $e) {
            Flight::redirect('/distribution?error=' . urlencode('Erreur lors de la distribution: ' . $e->getMessage()));
        }
    }

    /**
     * Récupère les besoins non satisfaits triés par montant croissant
     */
    private function getBesoinsNonSatisfaits()
    {
        $tmt = $this->db->runQuery("
            SELECT b.*, v.nom_ville,
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
                 WHERE dd.id_besoin = b.id_besoin), 0) + 
                COALESCE((SELECT SUM(montant_article)
                 FROM achats
                 WHERE id_besoin = b.id_besoin AND statut IN ('simule', 'valide')), 0) as montant_recu
            FROM besoins b
            LEFT JOIN villes v ON b.id_ville = v.id_ville
            WHERE b.statut IN ('en_cours', 'partiel')
            ORDER BY montant_total ASC
        ");
        return $tmt->fetchAll();
    }

    /**
     * Distribue un article à un besoin
     */
    private function distribuerArticle($id_besoin, $id_article, $quantiteNecessaire, $prix_unitaire)
    {
        // Récupérer les dons disponibles pour cet article
        $dons = $this->getDonsDisponibles($id_article);
        
        $quantiteDistribuee = 0;
        $montantDistribue = 0;

        foreach ($dons as $don) {
            if ($quantiteNecessaire <= 0) break;

            $quantiteAAffecter = min($don['quantite_restante'], $quantiteNecessaire);

            // Créer l'affectation
            $this->db->runQuery("
                INSERT INTO dispatch_dons (id_don, id_besoin, quantite_affectee, date_dispatch)
                VALUES (?, ?, ?, NOW())
            ", [$don['id_don'], $id_besoin, $quantiteAAffecter]);

            // Mettre à jour les quantités
            $this->updateDonQuantite($don['id_don'], $quantiteAAffecter);
            $this->updateBesoinSatisfaction($id_besoin, $id_article, $quantiteAAffecter);

            $quantiteDistribuee += $quantiteAAffecter;
            $montantDistribue += $quantiteAAffecter * $prix_unitaire;
            $quantiteNecessaire -= $quantiteAAffecter;
        }

        return [
            'quantite' => $quantiteDistribuee,
            'montant' => $montantDistribue,
            'detail' => "Article ID $id_article : $quantiteDistribuee unités distribuées"
        ];
    }

    /**
     * Distribue de l'argent à un besoin
     */
    private function distribuerArgent($id_besoin, $montantNecessaire)
    {
        // Récupérer les dons en argent disponibles
        $dons = $this->getDonsArgentDisponibles();
        
        $montantDistribue = 0;

        foreach ($dons as $don) {
            if ($montantNecessaire <= 0) break;

            $montantAAffecter = min($don['montant_restant'], $montantNecessaire);

            // Créer l'affectation
            $this->db->runQuery("
                INSERT INTO dispatch_dons (id_don, id_besoin, quantite_affectee, montant_affecte, date_dispatch)
                VALUES (?, ?, 1, ?, NOW())
            ", [$don['id_don'], $id_besoin, $montantAAffecter]);

            // Mettre à jour les montants
            $this->updateDonMontant($don['id_don'], $montantAAffecter);
            $this->updateBesoinArgentSatisfaction($id_besoin, $montantAAffecter);

            $montantDistribue += $montantAAffecter;
            $montantNecessaire -= $montantAAffecter;
        }

        return [
            'quantite' => $montantDistribue > 0 ? 1 : 0,
            'montant' => $montantDistribue,
            'detail' => "Argent : " . number_format($montantDistribue, 0, ',', ' ') . " Ar distribués"
        ];
    }

    /**
     * Récupère les dons disponibles pour un article
     */
    private function getDonsDisponibles($id_article)
    {
        $tmt = $this->db->runQuery("
            SELECT * FROM dons 
            WHERE id_article = ? 
            AND statut IN ('disponible', 'partiel')
            AND quantite_restante > 0
            ORDER BY date_don ASC
        ", [$id_article]);
        return $tmt->fetchAll();
    }

    /**
     * Récupère les dons en argent disponibles
     */
    private function getDonsArgentDisponibles()
    {
        $tmt = $this->db->runQuery("
            SELECT * FROM dons 
            WHERE id_type_don = 3 
            AND statut IN ('disponible', 'partiel')
            AND montant_restant > 0
            ORDER BY date_don ASC
        ");
        return $tmt->fetchAll();
    }

    /**
     * Met à jour la quantité restante d'un don
     */
    private function updateDonQuantite($id_don, $quantite)
    {
        $this->db->runQuery("
            UPDATE dons 
            SET quantite_restante = quantite_restante - ?,
                statut = CASE 
                    WHEN quantite_restante - ? <= 0 THEN 'affecte'
                    ELSE 'partiel'
                END
            WHERE id_don = ?
        ", [$quantite, $quantite, $id_don]);
    }

    /**
     * Met à jour le montant restant d'un don en argent
     */
    private function updateDonMontant($id_don, $montant)
    {
        $this->db->runQuery("
            UPDATE dons 
            SET montant_restant = montant_restant - ?,
                statut = CASE 
                    WHEN montant_restant - ? <= 0 THEN 'affecte'
                    ELSE 'partiel'
                END
            WHERE id_don = ?
        ", [$montant, $montant, $id_don]);
    }

    /**
     * Met à jour la satisfaction d'un besoin en article
     */
    private function updateBesoinSatisfaction($id_besoin, $id_article, $quantite)
    {
        $this->db->runQuery("
            UPDATE besoin_articles 
            SET quantite_satisfaite = quantite_satisfaite + ?
            WHERE id_besoin = ? AND id_article = ?
        ", [$quantite, $id_besoin, $id_article]);

        // Mettre à jour le statut du besoin
        $this->updateStatutBesoin($id_besoin);
    }

    /**
     * Met à jour la satisfaction d'un besoin en argent
     */
    private function updateBesoinArgentSatisfaction($id_besoin, $montant)
    {
        $this->db->runQuery("
            UPDATE besoin_articles 
            SET quantite_satisfaite = quantite_satisfaite + ?
            WHERE id_besoin = ? AND id_article IS NULL
        ", [$montant, $id_besoin]);

        // Mettre à jour le statut du besoin
        $this->updateStatutBesoin($id_besoin);
    }

    /**
     * Met à jour le statut d'un besoin
     */
    private function updateStatutBesoin($id_besoin)
    {
        $this->db->runQuery("
            UPDATE besoins 
            SET statut = CASE 
                WHEN (SELECT COUNT(*) FROM besoin_articles 
                      WHERE id_besoin = ? AND quantite_satisfaite >= quantite) = 
                     (SELECT COUNT(*) FROM besoin_articles WHERE id_besoin = ?)
                THEN 'satisfait'
                WHEN (SELECT COUNT(*) FROM besoin_articles 
                      WHERE id_besoin = ? AND quantite_satisfaite > 0) > 0
                THEN 'partiel'
                ELSE 'en_cours'
            END
            WHERE id_besoin = ?
        ", [$id_besoin, $id_besoin, $id_besoin, $id_besoin]);
    }
}
