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

    public function __construct()
    {
        $this->db = Flight::db();
        $this->besoinsModel = new BesoinsModel($this->db);
        $this->villesModel = new VillesModel($this->db);
        $this->articlesModel = new ArticlesModel($this->db);
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
     * Enregistre un ou plusieurs nouveaux besoins avec leurs articles
     */
    public function storeBesoin()
    {
        $besoins = $_POST['besoins'] ?? [];

        // Validation
        $errors = [];
        
        if (empty($besoins) || !is_array($besoins)) {
            $errors[] = 'Veuillez ajouter au moins un besoin';
        }

        // Valider chaque besoin
        $besoinsValides = [];
        foreach ($besoins as $index => $besoin) {
            $id_ville = $besoin['id_ville'] ?? null;
            $description = $besoin['description'] ?? null;
            $urgence = $besoin['urgence'] ?? 'normale';
            $articles = $besoin['articles'] ?? [];

            if (empty($id_ville)) {
                $errors[] = "Besoin #" . ($index + 1) . " : Veuillez sélectionner une ville";
                continue;
            }

            if (empty($articles) || !is_array($articles)) {
                $errors[] = "Besoin #" . ($index + 1) . " : Veuillez ajouter au moins un article";
                continue;
            }

            // Valider les articles
            $articlesValides = [];
            foreach ($articles as $artIndex => $article) {
                $id_article = $article['id_article'] ?? null;
                $quantite = $article['quantite'] ?? null;
                $prix_unitaire = $article['prix_unitaire'] ?? null;

                if (empty($id_article)) {
                    $errors[] = "Besoin #" . ($index + 1) . ", Article #" . ($artIndex + 1) . " : Veuillez sélectionner un article";
                    continue;
                }

                if (empty($quantite) || $quantite <= 0) {
                    $errors[] = "Besoin #" . ($index + 1) . ", Article #" . ($artIndex + 1) . " : La quantité doit être supérieure à 0";
                    continue;
                }

                if (empty($prix_unitaire) || $prix_unitaire <= 0) {
                    $errors[] = "Besoin #" . ($index + 1) . ", Article #" . ($artIndex + 1) . " : Le prix unitaire doit être supérieur à 0";
                    continue;
                }

                $articlesValides[] = [
                    'id_article' => $id_article,
                    'quantite' => $quantite,
                    'prix_unitaire' => $prix_unitaire
                ];
            }

            if (!empty($articlesValides)) {
                $besoinsValides[] = [
                    'id_ville' => $id_ville,
                    'description' => $description,
                    'urgence' => $urgence,
                    'articles' => $articlesValides
                ];
            }
        }

        if (!empty($errors)) {
            Flight::render('besoins/create', [
                'villes' => $this->villesModel->getAllVilles(),
                'articles' => $this->articlesModel->getAllArticles(),
                'errors' => $errors
            ]);
            return;
        }

        // Créer tous les besoins
        $nbBesoinsAjoutes = 0;
        foreach ($besoinsValides as $besoinData) {
            // Créer le besoin
            $besoinId = $this->besoinsModel->addBesoin(
                $besoinData['id_ville'],
                $besoinData['description'],
                $besoinData['urgence']
            );
            
            if ($besoinId) {
                // Ajouter les articles au besoin
                foreach ($besoinData['articles'] as $article) {
                    $this->besoinsModel->addArticleToBesoin(
                        $besoinId,
                        $article['id_article'],
                        $article['quantite'],
                        $article['prix_unitaire']
                    );
                }
                $nbBesoinsAjoutes++;
            }
        }

        if ($nbBesoinsAjoutes > 0) {
            $message = $nbBesoinsAjoutes > 1 
                ? "$nbBesoinsAjoutes besoins ajoutés avec succès !" 
                : "Besoin ajouté avec succès !";
            Flight::redirect('/besoins?success=besoin_ajoute&message=' . urlencode($message));
        } else {
            $errors[] = 'Erreur lors de l\'ajout des besoins';
            
            Flight::render('besoins/create', [
                'villes' => $this->villesModel->getAllVilles(),
                'articles' => $this->articlesModel->getAllArticles(),
                'errors' => $errors
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
     * Affiche les villes satisfaites (tous les besoins couverts)
     */
    public function getVillesSatisfaites()
    {
        $villes = $this->besoinsModel->getVillesSatisfaites();
        
        Flight::render('besoins/villes-satisfaites', [
            'villes' => $villes
        ]);
    }

    /**
     * Affiche les besoins critiques de type matériel/nature
     */
    public function getBesoinsCritiquesMateriels()
    {
        $besoins = $this->besoinsModel->getBesoinsCritiquesMateriels();
        
        Flight::render('besoins/critiques-materiels', [
            'besoins' => $besoins
        ]);
    }
}
