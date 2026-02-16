<?php

namespace app\controllers;

use Flight;
use app\models\DonsModel;
use app\models\TypeDonModel;
use app\models\ArticlesModel;

class DonsController
{
    protected $db;
    protected $donsModel;
    protected $typeDonModel;
    protected $articlesModel;

    public function __construct()
    {
        $this->db = Flight::db();
        $this->donsModel = new DonsModel($this->db);
        $this->typeDonModel = new TypeDonModel($this->db);
        $this->articlesModel = new ArticlesModel($this->db);
    }

    /**
     * Affiche le formulaire d'ajout de don
     */
    public function newDonForm()
    {
        $typeDons = $this->typeDonModel->getAllTypes();
        $articles = $this->articlesModel->getAllArticles();
        
        Flight::render('formulaire-don', [
            'typeDons' => $typeDons,
            'articles' => $articles
        ]);
    }

    /**
     * Enregistre un nouveau don
     */
    public function storeDon()
    {
        $id_type_don = $_POST['type_don'] ?? null;
        $article = $_POST['article'] ?? null;
        $quantite = $_POST['quantite'] ?? null;
        $montant = $_POST['montant'] ?? null;
        $description = $_POST['description'] ?? null;
        $donateur_nom = $_POST['donateur_nom'] ?? null;
        $donateur_contact = $_POST['donateur_contact'] ?? null;

        // Validation
        $errors = [];
        
        if (empty($id_type_don)) {
            $errors[] = 'Veuillez sélectionner un type de don';
        }

        if (empty($article)) {
            $errors[] = 'Veuillez sélectionner un article';
        }

        // Si c'est un don en argent
        if ($article === 'argent') {
            $id_article = null;
            $quantite = null;
            
            if (empty($montant) || $montant <= 0) {
                $errors[] = 'Le montant doit être supérieur à 0';
            }
        } else {
            // Don en nature ou matériel
            $id_article = $article;
            $montant = null;
            
            if (empty($quantite) || $quantite <= 0) {
                $errors[] = 'La quantité doit être supérieure à 0';
            }
        }

        if (empty($donateur_nom)) {
            $errors[] = 'Veuillez entrer le nom du donateur';
        }

        if (empty($donateur_contact)) {
            $errors[] = 'Veuillez entrer le contact du donateur';
        }

        if (!empty($errors)) {
            Flight::render('formulaire-don', [
                'typeDons' => $this->typeDonModel->getAllTypes(),
                'articles' => $this->articlesModel->getAllArticles(),
                'errors' => $errors,
                'old' => [
                    'type_don' => $id_type_don,
                    'article' => $article,
                    'quantite' => $quantite,
                    'montant' => $montant,
                    'description' => $description,
                    'donateur_nom' => $donateur_nom,
                    'donateur_contact' => $donateur_contact
                ]
            ]);
            return;
        }

        // Créer le don
        $donId = $this->donsModel->addDon(
            $id_type_don,
            $id_article,
            $description,
            $quantite,
            $montant,
            $donateur_nom,
            $donateur_contact
        );

        if ($donId) {
            Flight::redirect('/dons?success=don_ajoute');
        } else {
            $errors[] = 'Erreur lors de l\'ajout du don';
            Flight::render('formulaire-don', [
                'typeDons' => $this->typeDonModel->getAllTypes(),
                'articles' => $this->articlesModel->getAllArticles(),
                'errors' => $errors,
                'old' => [
                    'type_don' => $id_type_don,
                    'article' => $article,
                    'quantite' => $quantite,
                    'montant' => $montant,
                    'description' => $description,
                    'donateur_nom' => $donateur_nom,
                    'donateur_contact' => $donateur_contact
                ]
            ]);
        }
    }

    /**
     * Affiche la liste des dons
     */
    public function getDons()
    {
        $dons = $this->donsModel->getAllDons();
        
        Flight::render('dons/index', [
            'dons' => $dons
        ]);
    }

    /**
     * Affiche les détails d'un don
     */
    public function getDonById($id)
    {
        $don = $this->donsModel->getDonById($id);
        
        if (!$don) {
            Flight::halt(404, 'Don introuvable');
        }
        
        Flight::render('dons/show', [
            'don' => $don
        ]);
    }

    /**
     * Supprime un don
     */
    public function deleteDon($id)
    {
        $count = $this->donsModel->deleteDon($id);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Flight::redirect('/dons?success=don_supprime');
            return;
        }
        
        Flight::json(['deleted' => $count]);
    }

    /**
     * Met à jour le statut d'un don
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
        
        $count = $this->donsModel->updateStatut($id, $statut);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Flight::redirect('/dons');
            return;
        }
        
        Flight::json(['updated' => $count]);
    }

    /**
     * Valide un don et le dispatche vers les besoins
     */
    public function validerDon($id)
    {
        $result = $this->donsModel->dispatcherDon($id);
        
        if ($result['success']) {
            $message = 'Don validé et dispatché avec succès ! ';
            
            if (isset($result['montant_affecte'])) {
                $message .= number_format($result['montant_affecte'], 0, ',', ' ') . ' Ar affectés.';
            } elseif (isset($result['quantite_affectee'])) {
                $message .= $result['quantite_affectee'] . ' unités affectées.';
            }
            
            Flight::redirect('/dons?success=don_valide&message=' . urlencode($message));
        } else {
            Flight::redirect('/dons?error=' . urlencode($result['message']));
        }
    }


    /**
     * Affiche la liste des dons disponibles via v_dons_disponibles
     */
    public function listeDonsDisponibles()
    {
        // Maka données avy amin'ny vue v_dons_disponibles
        $sql = "SELECT * FROM v_dons_disponibles ORDER BY date_don DESC";
        $dons = $this->db->runQuery($sql)->fetchAll();

        // Charger la vue
        require_once __DIR__ . '/../views/dons/liste_disponibles.php';
    }
}
