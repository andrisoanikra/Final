<?php

namespace app\controllers;

use Flight;
use app\models\VillesModel;
use app\models\BesoinsModel;
use app\models\DonsModel;

class TableauBordController
{
    protected $db;
    protected $villesModel;
    protected $besoinsModel;
    protected $donsModel;

    public function __construct()
    {
        $this->db = Flight::db();
        $this->villesModel = new VillesModel($this->db);
        $this->besoinsModel = new BesoinsModel($this->db);
        $this->donsModel = new DonsModel($this->db);
    }

    /**
     * Affiche le tableau de bord avec les villes, besoins et dons
     */
    public function index()
    {
        // Récupérer toutes les villes avec leurs statistiques
        $villes = $this->getVillesAvecStats();
        
        Flight::render('tableau-bord/index', [
            'villes' => $villes
        ]);
    }

    /**
     * Récupère les villes avec leurs besoins et dons affectés
     */
    private function getVillesAvecStats()
    {
        $tmt = $this->db->runQuery("
            SELECT 
                v.id_ville,
                v.nom_ville,
                v.population,
                r.nom_region,
                COUNT(DISTINCT b.id_besoin) as nb_besoins,
                COUNT(DISTINCT CASE WHEN b.statut = 'en_cours' THEN b.id_besoin END) as nb_besoins_en_cours,
                COUNT(DISTINCT CASE WHEN b.statut = 'satisfait' THEN b.id_besoin END) as nb_besoins_satisfaits,
                COUNT(DISTINCT dd.id_dispatch) as nb_dons_recus
            FROM villes v
            LEFT JOIN regions r ON v.id_region = r.id_region
            LEFT JOIN besoins b ON v.id_ville = b.id_ville
            LEFT JOIN dispatch_dons dd ON b.id_besoin = dd.id_besoin
            GROUP BY v.id_ville
            ORDER BY v.nom_ville ASC
        ");
        
        $villes = $tmt->fetchAll();
        
        // Pour chaque ville, récupérer les détails des besoins et dons
        foreach ($villes as &$ville) {
            $ville['besoins'] = $this->getBesoinsParVille($ville['id_ville']);
            $ville['dons_recus'] = $this->getDonsParVille($ville['id_ville']);
        }
        
        return $villes;
    }

    /**
     * Récupère les besoins d'une ville
     */
    private function getBesoinsParVille($id_ville)
    {
        $tmt = $this->db->runQuery("
            SELECT 
                b.id_besoin,
                b.description,
                b.urgence,
                b.statut,
                b.date_saisie,
                GROUP_CONCAT(DISTINCT a.nom_article SEPARATOR ', ') as articles,
                GROUP_CONCAT(DISTINCT tb.libelle_type SEPARATOR ', ') as types,
                SUM(ba.quantite * ba.prix_unitaire) as montant_total
            FROM besoins b
            LEFT JOIN besoin_articles ba ON b.id_besoin = ba.id_besoin
            LEFT JOIN articles a ON ba.id_article = a.id_article
            LEFT JOIN type_besoin tb ON a.id_type_besoin = tb.id_type_besoin
            WHERE b.id_ville = ?
            GROUP BY b.id_besoin
            ORDER BY b.date_saisie DESC
            LIMIT 5
        ", [$id_ville]);
        
        return $tmt->fetchAll();
    }

    /**
     * Récupère les dons affectés à une ville
     */
    private function getDonsParVille($id_ville)
    {
        $tmt = $this->db->runQuery("
            SELECT 
                d.id_don,
                d.donateur_nom,
                a.nom_article,
                dd.quantite_affectee,
                dd.montant_affecte,
                dd.date_dispatch,
                td.libelle_type
            FROM dispatch_dons dd
            JOIN dons d ON dd.id_don = d.id_don
            JOIN besoins b ON dd.id_besoin = b.id_besoin
            LEFT JOIN articles a ON d.id_article = a.id_article
            LEFT JOIN type_don td ON d.id_type_don = td.id_type_don
            WHERE b.id_ville = ?
            ORDER BY dd.date_dispatch DESC
            LIMIT 10
        ", [$id_ville]);
        
        return $tmt->fetchAll();
    }

    /**
     * Affiche la page de récapitulation des besoins
     */
    public function recapitulation()
    {
        Flight::render('tableau-bord/recapitulation', []);
    }

    /**
     * API Ajax : Récupère les statistiques des besoins
     */
    public function getRecapitulatifAjax()
    {
        $stats = $this->getStatistiquesBesoins();
        
        header('Content-Type: application/json');
        echo json_encode($stats);
        exit;
    }

    /**
     * Calcule les statistiques des besoins (totaux, satisfaits, restants)
     */
    private function getStatistiquesBesoins()
    {
        // Besoins totaux en montant
        $tmt = $this->db->runQuery("
            SELECT 
                COALESCE(SUM(ba.quantite * ba.prix_unitaire), 0) as montant_total
            FROM besoins b
            JOIN besoin_articles ba ON b.id_besoin = ba.id_besoin
        ");
        $resultTotal = $tmt->fetch();
        $montantTotal = $resultTotal['montant_total'] ?? 0;

        // Besoins satisfaits en montant
        $tmt = $this->db->runQuery("
            SELECT 
                COALESCE(SUM(ba.quantite * ba.prix_unitaire), 0) as montant_satisfait
            FROM besoins b
            JOIN besoin_articles ba ON b.id_besoin = ba.id_besoin
            WHERE b.statut = 'satisfait'
        ");
        $resultSatisfait = $tmt->fetch();
        $montantSatisfait = $resultSatisfait['montant_satisfait'] ?? 0;

        // Montant des besoins partiellement satisfaits (on prend le montant total des besoins partiels)
        $tmt = $this->db->runQuery("
            SELECT 
                COALESCE(SUM(ba.quantite * ba.prix_unitaire), 0) as montant_partiel
            FROM besoins b
            JOIN besoin_articles ba ON b.id_besoin = ba.id_besoin
            WHERE b.statut = 'partiel'
        ");
        $resultPartiel = $tmt->fetch();
        $montantPartiel = $resultPartiel['montant_partiel'] ?? 0;

        // Montant restant = Total - Satisfait
        $montantRestant = $montantTotal - $montantSatisfait;

        // Nombre de besoins par statut
        $tmt = $this->db->runQuery("
            SELECT 
                COUNT(CASE WHEN statut = 'en_cours' THEN 1 END) as nb_en_cours,
                COUNT(CASE WHEN statut = 'satisfait' THEN 1 END) as nb_satisfaits,
                COUNT(CASE WHEN statut = 'partiel' THEN 1 END) as nb_partiels,
                COUNT(*) as nb_total
            FROM besoins
        ");
        $resultNombres = $tmt->fetch();

        // Statistiques sur les dons
        $tmt = $this->db->runQuery("
            SELECT 
                COUNT(*) as nb_dons_total,
                COUNT(CASE WHEN statut = 'disponible' THEN 1 END) as nb_dons_disponibles,
                COUNT(CASE WHEN statut = 'affecte' OR statut = 'dispatche' THEN 1 END) as nb_dons_dispatches,
                COALESCE(SUM(CASE WHEN id_type_don = 3 AND statut = 'disponible' THEN montant_argent ELSE 0 END), 0) as montant_argent_disponible
            FROM dons
        ");
        $resultDons = $tmt->fetch();

        return [
            'montant_total' => floatval($montantTotal),
            'montant_satisfait' => floatval($montantSatisfait),
            'montant_restant' => floatval($montantRestant),
            'montant_partiel' => floatval($montantPartiel),
            'pourcentage_satisfait' => $montantTotal > 0 ? round(($montantSatisfait / $montantTotal) * 100, 2) : 0,
            'nb_besoins_total' => intval($resultNombres['nb_total'] ?? 0),
            'nb_besoins_en_cours' => intval($resultNombres['nb_en_cours'] ?? 0),
            'nb_besoins_satisfaits' => intval($resultNombres['nb_satisfaits'] ?? 0),
            'nb_besoins_partiels' => intval($resultNombres['nb_partiels'] ?? 0),
            'nb_dons_total' => intval($resultDons['nb_dons_total'] ?? 0),
            'nb_dons_disponibles' => intval($resultDons['nb_dons_disponibles'] ?? 0),
            'nb_dons_dispatches' => intval($resultDons['nb_dons_dispatches'] ?? 0),
            'montant_argent_disponible' => floatval($resultDons['montant_argent_disponible'] ?? 0),
            'date_actualisation' => date('d/m/Y H:i:s')
        ];
    }
}
