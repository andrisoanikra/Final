<?php

namespace app\controllers;

use flight\Engine;
use app\models\BesoinsModel;
use app\models\VillesModel;
use app\models\ArticlesModel;
use Flight;

class BesoinsController {
    
    protected Engine $app;

    public function __construct($app) {
        $this->app = $app;
    }

    /**
     * Affiche le formulaire d'ajout de besoin
     */
    public function newBesoinForm() {
        $villesModel = new VillesModel(Flight::db());
        $articlesModel = new ArticlesModel(Flight::db());
        
        $villes = $villesModel->getAllVilles();
        $articles = $articlesModel->getAllArticles();
        
        // Récupérer les paramètres GET pour pré-remplir si nécessaire
        $selectedVille = $_GET['ville_id'] ?? null;
        $selectedArticle = $_GET['article_id'] ?? null;
        
        Flight::render('besoins/create', [
            'villes' => $villes,
            'articles' => $articles,
            'selectedVille' => $selectedVille,
            'selectedArticle' => $selectedArticle
        ]);
    }

    /**
     * Enregistre un nouveau besoin
     */
    public function storeBesoin() {
        $model = new BesoinsModel(Flight::db());
        
        $data = [
            'id_ville' => $_POST['id_ville'] ?? null,
            'id_article' => $_POST['id_article'] ?? null,
            'quantite' => $_POST['quantite'] ?? null,
            'prix_unitaire' => $_POST['prix_unitaire'] ?? null,
            'description' => $_POST['description'] ?? null,
            'urgence' => $_POST['urgence'] ?? 'normale' // normale, urgente, critique
        ];

        // Validation
        $errors = [];
        if (empty($data['id_ville'])) {
            $errors[] = 'Veuillez sélectionner une ville';
        }
        if (empty($data['id_article'])) {
            $errors[] = 'Veuillez sélectionner un article';
        }
        if (empty($data['quantite']) || $data['quantite'] <= 0) {
            $errors[] = 'La quantité doit être supérieure à 0';
        }
        if (empty($data['prix_unitaire']) || $data['prix_unitaire'] <= 0) {
            $errors[] = 'Le prix unitaire doit être supérieur à 0';
        }

        if (!empty($errors)) {
            // En cas d'erreur, réafficher le formulaire avec les erreurs
            $villesModel = new VillesModel(Flight::db());
            $articlesModel = new ArticlesModel(Flight::db());
            
            Flight::render('besoins/create', [
                'villes' => $villesModel->getAllVilles(),
                'articles' => $articlesModel->getAllArticles(),
                'errors' => $errors,
                'old' => $data
            ]);
            return;
        }

        // Appeler la méthode addBesoin avec les paramètres individuels
        $statut = $_POST['statut'] ?? 'en_cours';
        $description = $_POST['description'] ?? null;
        $urgence = $_POST['urgence'] ?? 'normale';
        
        $id = $model->addBesoin(
            $data['id_ville'],
            $data['id_article'],
            $data['quantite'],
            $data['prix_unitaire'],
            $statut,
            $description,
            $urgence
        );
        
        if ($id) {
            // Rediriger vers la page de la ville avec un message de succès
            Flight::redirect('/villes/' . $data['id_ville'] . '?success=besoin_ajoute');
        } else {
            // Erreur lors de l'insertion
            $errors[] = 'Erreur lors de l\'ajout du besoin';
            $villesModel = new VillesModel(Flight::db());
            $articlesModel = new ArticlesModel(Flight::db());
            
            Flight::render('besoins/create', [
                'villes' => $villesModel->getAllVilles(),
                'articles' => $articlesModel->getAllArticles(),
                'errors' => $errors,
                'old' => $data
            ]);
        }
    }

    /**
     * Affiche la liste des besoins (optionnel)
     */
    public function getBesoins() {
        $model = new BesoinsModel(Flight::db());
        $besoins = $model->getAllBesoinsWithDetails();
        
        Flight::render('besoins/index', [
            'besoins' => $besoins
        ]);
    }

    /**
     * Affiche les détails d'un besoin
     */
    public function getBesoinById($id) {
        $model = new BesoinsModel(Flight::db());
        $besoin = $model->getBesoinById($id);
        
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
    public function deleteBesoin($id) {
        $model = new BesoinsModel(Flight::db());
        
        // Récupérer l'ID de la ville pour la redirection
        $besoin = $model->getBesoinById($id);
        $villeId = $besoin['id_ville'] ?? null;
        
        $count = $model->deleteBesoin($id);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($villeId) {
                Flight::redirect('/villes/' . $villeId . '?success=besoin_supprime');
            } else {
                Flight::redirect('/besoins');
            }
            return;
        }
        
        Flight::json(['deleted' => $count]);
    }

    /**
     * Page de confirmation de suppression
     */
    public function confirmDeleteBesoin($id) {
        $model = new BesoinsModel(Flight::db());
        $besoin = $model->getBesoinById($id);
        
        if (!$besoin) {
            Flight::halt(404, 'Besoin introuvable');
        }
        
        Flight::render('confirm_delete', [
            'entity' => 'besoin',
            'id' => $id,
            'label' => 'Besoin: ' . $besoin['nom_article'] . ' - ' . $besoin['nom_ville'],
            'back' => '/villes/' . $besoin['id_ville'],
            'details' => $besoin
        ]);
    }

    /**
     * Met à jour le statut d'un besoin
     */
    public function updateStatut($id, $statut = null) {
        $model = new BesoinsModel(Flight::db());
        
        if ($statut === null) {
            $statut = $_POST['statut'] ?? null;
        }
        
        if ($statut === null) {
            Flight::json(['error' => 'statut manquant']);
            return;
        }
        
        $count = $model->updateBesoinStatut($id, $statut);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $besoin = $model->getBesoinById($id);
            Flight::redirect('/villes/' . $besoin['id_ville']);
            return;
        }
        
        Flight::json(['updated' => $count]);
    }

    /**
     * Met à jour le statut d'un besoin (alias pour la route)
     */
    public function updateBesoinStatut($id) {
        return $this->updateStatut($id);
    }

    /**
     * Récupère les besoins non satisfaits
     */
    public function getBesoinsNonSatisfaits() {
        $model = new BesoinsModel(Flight::db());
        $besoins = $model->getBesoinsNonSatisfaits();
        
        Flight::render('besoins/non-satisfaits', [
            'besoins' => $besoins
        ]);
    }

    /**
     * Récupère le montant total
     */
    public function getMontantTotal() {
        $model = new BesoinsModel(Flight::db());
        $total = $model->getMontantTotal();
        
        Flight::json([
            'montant_total' => $total
        ]);
    }

    /**
     * Récupère le montant d'un besoin
     */
    public function getBesoinMontant($id) {
        $model = new BesoinsModel(Flight::db());
        $besoin = $model->getBesoinById($id);
        
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
}