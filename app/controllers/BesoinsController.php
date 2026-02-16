<?php

namespace app\controllers;

use Flight;
use app\models\BesoinsModel;
use app\models\VillesModel;
use app\models\ArticlesModel;

class BesoinsController
{
    protected $db;
    protected $besoinsModel;
    protected $villesModel;
    protected $articlesModel;
    private $model;

    public function __construct()
    {
        $this->db = Flight::db();
        $this->besoinsModel = new BesoinsModel($this->db);
        $this->villesModel = new VillesModel($this->db);
        $this->articlesModel = new ArticlesModel($this->db);
        $this->model = new BesoinsModel($this->db);
    }

    /**
     * Affiche le formulaire d'ajout de besoin
     */
    public function newBesoinForm()
    {
        $villes = $this->villesModel->getAllVilles();
        $articles = $this->articlesModel->getAllArticles();
        
        Flight::render('besoins/create', [
            'villes' => $villes,
            'articles' => $articles
        ]);
    }

    /**
     * Enregistre un nouveau besoin avec ses articles
     */
    public function storeBesoin()
    {
        $id_ville = $_POST['id_ville'] ?? null;
        $description = $_POST['description'] ?? null;
        $urgence = $_POST['urgence'] ?? 'normale';
        $id_articles = $_POST['id_article'] ?? [];
        $quantites = $_POST['quantite'] ?? [];
        $prix_unitaires = $_POST['prix_unitaire'] ?? [];

        // Validation
        $errors = [];
        if (empty($id_ville)) {
            $errors[] = 'Veuillez sélectionner une ville';
        }
        
        // Valider les articles
        if (empty($id_articles) || !is_array($id_articles)) {
            $errors[] = 'Veuillez ajouter au moins un article';
        } else {
            foreach ($id_articles as $index => $id_article) {
                if (empty($id_article)) {
                    $errors[] = 'Veuillez sélectionner un article pour chaque ligne';
                    break;
                }
                
                $quantite = $quantites[$index] ?? null;
                $prix = $prix_unitaires[$index] ?? null;
                
                if (empty($quantite) || $quantite <= 0) {
                    $errors[] = 'La quantité de l\'article ' . ($index + 1) . ' doit être supérieure à 0';
                }
                if (empty($prix) || $prix <= 0) {
                    $errors[] = 'Le prix unitaire de l\'article ' . ($index + 1) . ' doit être supérieur à 0';
                }
            }
        }

        if (!empty($errors)) {
            Flight::render('besoins/create', [
                'villes' => $this->villesModel->getAllVilles(),
                'articles' => $this->articlesModel->getAllArticles(),
                'errors' => $errors,
                'old' => [
                    'id_ville' => $id_ville,
                    'description' => $description,
                    'urgence' => $urgence
                ]
            ]);
            return;
        }

        // Créer le besoin
        $besoinId = $this->besoinsModel->addBesoin(
            $id_ville,
            $description,
            $urgence
        );
        
        if ($besoinId) {
            // Ajouter les articles au besoin
            foreach ($id_articles as $index => $id_article) {
                $this->besoinsModel->addArticleToBesoin(
                    $besoinId,
                    $id_article,
                    $quantites[$index],
                    $prix_unitaires[$index]
                );
            }
            
            Flight::redirect('/besoins?success=besoin_ajoute');
        } else {
            $errors[] = 'Erreur lors de l\'ajout du besoin';
            
            Flight::render('besoins/create', [
                'villes' => $this->villesModel->getAllVilles(),
                'articles' => $this->articlesModel->getAllArticles(),
                'errors' => $errors,
                'old' => [
                    'id_ville' => $id_ville,
                    'description' => $description,
                    'urgence' => $urgence
                ]
            ]);
        }
    }

    /**
     * Affiche la liste des besoins
     */
    public function getBesoins()
    {
        $besoins = $this->besoinsModel->getAllBesoinsWithDetails();
        
        Flight::render('besoins/index', [
            'besoins' => $besoins
        ]);
    }

    /**
     * Affiche les détails d'un besoin
     */
    public function getBesoinById($id)
    {
        $besoin = $this->besoinsModel->getBesoinById($id);
        
        if (!$besoin) {
            Flight::halt(404, 'Besoin introuvable');
        }
        
        Flight::render('besoins/show', [
            'besoin' => $besoin
        ]);
    }

    /**
     * Supprime un besoin
     */
    public function deleteBesoin($id)
    {
        $besoin = $this->besoinsModel->getBesoinById($id);
        $villeId = $besoin['id_ville'] ?? null;
        
        $count = $this->besoinsModel->deleteBesoin($id);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Flight::redirect('/besoins?success=besoin_supprime');
            return;
        }
        
        Flight::json(['deleted' => $count]);
    }

    /**
     * Page de confirmation de suppression
     */
    public function confirmDeleteBesoin($id)
    {
        $besoin = $this->besoinsModel->getBesoinById($id);
        
        if (!$besoin) {
            Flight::halt(404, 'Besoin introuvable');
        }
        
        Flight::render('confirm_delete', [
            'entity' => 'besoin',
            'id' => $id,
            'label' => 'Besoin: ' . $besoin['nom_article'] . ' - ' . $besoin['nom_ville'],
            'back' => '/besoins',
            'details' => $besoin
        ]);
    }

    /**
     * Met à jour le statut d'un besoin
     */
    public function updateStatut($id, $statut = null)
    {
        if ($statut === null) {
            $statut = $_POST['statut'] ?? null;
        }
        
        if ($statut === null) {
            Flight::json(['error' => 'statut manquant']);
            return;
        }
        
        $count = $this->besoinsModel->updateBesoinStatut($id, $statut);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Flight::redirect('/besoins');
            return;
        }
        
        Flight::json(['updated' => $count]);
    }

    /**
     * Met à jour le statut d'un besoin (alias pour la route)
     */
    public function updateBesoinStatut($id)
    {
        return $this->updateStatut($id);
    }

    /**
     * Récupère les besoins non satisfaits
     */
    public function getBesoinsNonSatisfaits()
    {
        $besoins = $this->besoinsModel->getBesoinsNonSatisfaits();
        
        Flight::render('besoins/non-satisfaits', [
            'besoins' => $besoins
        ]);
    }

    /**
     * Récupère le montant total
     */
    public function getMontantTotal()
    {
        $total = $this->besoinsModel->getMontantTotal();
        
        Flight::json([
            'montant_total' => $total
        ]);
    }

    /**
     * Récupère le montant d'un besoin
     */
    public function getBesoinMontant($id)
    {
        $besoin = $this->besoinsModel->getBesoinById($id);
        
        if (!$besoin) {
            Flight::halt(404, 'Besoin introuvable');
        }
        
        $montant = $besoin['quantite'] * $besoin['prix_unitaire'];
        
        Flight::json([
            'id_besoin' => $id,
            'quantite' => $besoin['quantite'],
            'prix_unitaire' => $besoin['prix_unitaire'],
            'montant_total' => $montant
        ]);
    }

      /**
     * Affiche la liste des besoins via v_besoins_par_ville
     */
  public function listeBesoins()
{
    // Maka données avy amin'ny modèle
    $besoins = $this->besoinsModel->getAllBesoins();

    // Charger la vue (mampita données)
    require_once __DIR__ . '/../views/besoins/liste.php';
}
}
