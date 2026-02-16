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
}
