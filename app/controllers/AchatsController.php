<?php

namespace app\controllers;

use Flight;
use app\models\AchatsModel;
use app\models\DonsModel;
use app\models\BesoinsModel;
use app\models\ArticlesModel;
use app\models\VillesModel;

class AchatsController
{
    protected $db;
    protected $achatsModel;
    protected $donsModel;
    protected $besoinsModel;
    protected $articlesModel;
    protected $villesModel;

    public function __construct()
    {
        $this->db = Flight::db();
        $this->achatsModel = new AchatsModel($this->db);
        $this->donsModel = new DonsModel($this->db);
        $this->besoinsModel = new BesoinsModel($this->db);
        $this->articlesModel = new ArticlesModel($this->db);
        $this->villesModel = new VillesModel($this->db);
    }

    /**
     * Affiche le formulaire d'achat pour un besoin critique
     */
    public function formulaireAchat($id_besoin)
    {
        $besoin = $this->besoinsModel->getBesoinById($id_besoin);
        if (!$besoin) {
            Flight::halt(404, 'Besoin introuvable');
        }

        // Seuls les dons en argent DISPONIBLES ou PARTIEL (avec montant restant)
        $donsArgent = $this->achatsModel->getDonsArgentDisponibles();
        $articles = $besoin['articles']; // Articles du besoin
        $fraisPourcentage = $this->achatsModel->getFraisAchat();

        Flight::render('achats/formulaire', [
            'besoin' => $besoin,
            'donsArgent' => $donsArgent,
            'articles' => $articles,
            'fraisPourcentage' => $fraisPourcentage
        ]);
    }

    /**
     * Crée un achat automatique avec TOUT l'argent disponible du don
     */
    public function createAchatSimule()
    {
        $id_don_argent = $_POST['id_don_argent'] ?? null;
        $id_besoin = $_POST['id_besoin'] ?? null;

        // Validation
        if (!$id_don_argent || !$id_besoin) {
            Flight::redirect('/besoins/critiques-materiels?error=' . urlencode('Don et besoin requis'));
            return;
        }

        // Récupérer le don et son montant disponible
        $don = $this->donsModel->getDonById($id_don_argent);
        if (!$don || !$don['montant_restant'] || $don['montant_restant'] <= 0) {
            Flight::redirect('/besoins/critiques-materiels?error=' . urlencode('Don invalide ou montant insuffisant'));
            return;
        }

        $montant_disponible = $don['montant_restant'];
        $frais_pourcentage = $this->achatsModel->getFraisAchat();

        // Créer l'achat automatique avec TOUT le montant disponible
        $id_achat = $this->achatsModel->createAchatAutomatique(
            $id_don_argent,
            $id_besoin,
            $montant_disponible,
            $frais_pourcentage
        );

        if ($id_achat) {
            Flight::redirect('/achats/simulation?success=' . urlencode('Achat automatique créé avec ' . number_format($montant_disponible, 0, ',', ' ') . ' Ar'));
        } else {
            Flight::redirect('/achat/formulaire/' . $id_besoin . '?error=' . urlencode('Erreur lors de la création de l\'achat'));
        }
    }

    /**
     * Affiche la page de simulation des achats
     */
    public function pageSimulation()
    {
        $filtre_ville = $_GET['ville'] ?? null;
        $achats = $this->achatsModel->getAllAchats($filtre_ville);
        $villes = $this->villesModel->getAllVilles();
        $fraisPourcentage = $this->achatsModel->getFraisAchat();

        Flight::render('achats/simulation', [
            'achats' => $achats,
            'villes' => $villes,
            'filtre_ville' => $filtre_ville,
            'fraisPourcentage' => $fraisPourcentage
        ]);
    }

    /**
     * Valide un achat simulé
     */
    public function validerAchat($id_achat)
    {
        $result = $this->achatsModel->validerAchat($id_achat);

        if ($result['success']) {
            // Dispatcher automatiquement le don créé
            $dispatchResult = $this->donsModel->dispatcherDon($result['id_don_cree']);
            
            Flight::redirect('/achats/simulation?success=' . urlencode($result['message'] . ' - Don dispatché automatiquement'));
        } else {
            Flight::redirect('/achats/simulation?error=' . urlencode($result['message']));
        }
    }

    /**
     * Supprime un achat simulé
     */
    public function supprimerAchatSimule($id_achat)
    {
        $count = $this->achatsModel->deleteAchatSimule($id_achat);
        
        if ($count > 0) {
            Flight::redirect('/achats/simulation?success=' . urlencode('Achat simulé supprimé'));
        } else {
            Flight::redirect('/achats/simulation?error=' . urlencode('Impossible de supprimer cet achat'));
        }
    }

    /**
     * Affiche/modifie la configuration des frais
     */
    public function configurerFrais()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pourcentage = $_POST['frais_pourcentage'] ?? null;
            
            if ($pourcentage !== null && $pourcentage >= 0 && $pourcentage <= 100) {
                $this->achatsModel->updateFraisAchat($pourcentage);
                Flight::redirect('/achats/simulation?success=' . urlencode('Frais d\'achat mis à jour : ' . $pourcentage . '%'));
            } else {
                Flight::redirect('/achats/simulation?error=' . urlencode('Pourcentage invalide (0-100)'));
            }
        }
    }
}
