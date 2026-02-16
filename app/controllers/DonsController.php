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
     * Enregistre un nouveau don (ou plusieurs dons)
     */
    public function storeDon()
    {
        $donateur_nom = $_POST['donateur_nom'] ?? null;
        $donateur_contact = $_POST['donateur_contact'] ?? null;
        $description = $_POST['description'] ?? null;
        $dons = $_POST['dons'] ?? [];

        // Validation
        $errors = [];
        
        if (empty($donateur_nom)) {
            $errors[] = 'Veuillez entrer le nom du donateur';
        }

        if (empty($donateur_contact)) {
            $errors[] = 'Veuillez entrer le contact du donateur';
        }

        if (empty($dons) || !is_array($dons)) {
            $errors[] = 'Veuillez ajouter au moins un don';
        }

        // Valider chaque don
        $donsValides = [];
        foreach ($dons as $index => $don) {
            $id_type_don = $don['type_don'] ?? null;
            $article = $don['article'] ?? null;
            $quantite = $don['quantite'] ?? null;
            $montant = $don['montant'] ?? null;

            if (empty($id_type_don)) {
                $errors[] = "Don #" . ($index + 1) . " : Veuillez sélectionner un type";
                continue;
            }

            if (empty($article)) {
                $errors[] = "Don #" . ($index + 1) . " : Veuillez sélectionner un article";
                continue;
            }

            // Si c'est un don en argent
            if ($article === 'argent') {
                $id_article = null;
                $quantite_finale = null;
                
                if (empty($montant) || $montant <= 0) {
                    $errors[] = "Don #" . ($index + 1) . " : Le montant doit être supérieur à 0";
                    continue;
                }
                $montant_final = $montant;
            } else {
                // Don en nature ou matériel
                $id_article = $article;
                $montant_final = null;
                
                if (empty($quantite) || $quantite <= 0) {
                    $errors[] = "Don #" . ($index + 1) . " : La quantité doit être supérieure à 0";
                    continue;
                }
                $quantite_finale = $quantite;
            }

            $donsValides[] = [
                'id_type_don' => $id_type_don,
                'id_article' => $id_article,
                'quantite' => $quantite_finale,
                'montant' => $montant_final
            ];
        }

        if (!empty($errors)) {
            Flight::render('formulaire-don', [
                'typeDons' => $this->typeDonModel->getAllTypes(),
                'articles' => $this->articlesModel->getAllArticles(),
                'errors' => $errors,
                'old' => [
                    'description' => $description,
                    'donateur_nom' => $donateur_nom,
                    'donateur_contact' => $donateur_contact
                ]
            ]);
            return;
        }

        // Créer tous les dons
        $nbDonsAjoutes = 0;
        foreach ($donsValides as $don) {
            $donId = $this->donsModel->addDon(
                $don['id_type_don'],
                $don['id_article'],
                $description,
                $don['quantite'],
                $don['montant'],
                $donateur_nom,
                $donateur_contact
            );

            if ($donId) {
                $nbDonsAjoutes++;
            }
        }

        if ($nbDonsAjoutes > 0) {
            $message = $nbDonsAjoutes > 1 
                ? "$nbDonsAjoutes dons ajoutés avec succès !" 
                : "Don ajouté avec succès !";
            Flight::redirect('/dons?success=don_ajoute&message=' . urlencode($message));
        } else {
            $errors[] = 'Erreur lors de l\'ajout des dons';
            Flight::render('formulaire-don', [
                'typeDons' => $this->typeDonModel->getAllTypes(),
                'articles' => $this->articlesModel->getAllArticles(),
                'errors' => $errors,
                'old' => [
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
            
            // Ajouter un message de félicitation pour les villes dont tous les besoins sont couverts
            if (!empty($result['villes_satisfaites'])) {
                $message .= ' 🎉 FÉLICITATIONS ! Tous les besoins de ' . 
                           (count($result['villes_satisfaites']) > 1 ? 'ces villes sont' : 'cette ville est') . 
                           ' maintenant couverts : ' . 
                           implode(', ', $result['villes_satisfaites']) . ' !';
            }
            
            Flight::redirect('/dons?success=don_valide&message=' . urlencode($message));
        } else {
            Flight::redirect('/dons?error=' . urlencode($result['message']));
        }
    }
}
