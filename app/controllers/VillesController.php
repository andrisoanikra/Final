<?php

namespace app\controllers;

use Flight;
use app\models\VillesModel;
use app\models\BesoinsModel;

class VillesController
{
    protected $db;
    protected $villesModel;

    public function __construct()
    {
        $this->db = Flight::db();
        $this->villesModel = new VillesModel($this->db);
    }

    /**
     * Formulaire de création d'une ville
     */
    public function newVilleForm() {
        Flight::render('villes/create', [
            'regions' => $this->getAllRegions()
        ]);
    }

    /**
     * Stocke une nouvelle ville
     */
    public function storeVille() {
        $id_region = $_POST['id_region'] ?? null;
        $nom_ville = $_POST['nom_ville'] ?? null;
        $description = $_POST['description'] ?? null;

        $errors = [];
        if (empty($id_region)) {
            $errors[] = 'Veuillez sélectionner une région';
        }
        if (empty($nom_ville)) {
            $errors[] = 'Veuillez entrer le nom de la ville';
        }

        if (!empty($errors)) {
            Flight::render('villes/create', [
                'regions' => $this->getAllRegions(),
                'errors' => $errors,
                'old' => $_POST
            ]);
            return;
        }

        $result = $this->villesModel->create($id_region, $nom_ville, $description);

        if ($result) {
            Flight::redirect('/villes?success=ville_ajoutee');
        } else {
            $errors[] = 'Erreur lors de l\'ajout de la ville';
            Flight::render('villes/create', [
                'regions' => $this->getAllRegions(),
                'errors' => $errors,
                'old' => $_POST
            ]);
        }
    }

    /**
     * Liste toutes les villes
     */
    public function getVilles()
    {
        $villes = $this->villesModel->getAllVilles();
        Flight::render('villes/index', [
            'villes' => $villes
        ]);
    }

    /**
     * Détails d'une ville
     */
    public function getVilleById($id)
    {
        $ville = $this->villesModel->getById($id);
        
        if (!$ville) {
            Flight::halt(404, 'Ville introuvable');
        }

        // Récupérer les besoins de cette ville
        $besoinsModel = new BesoinsModel($this->db);
        $besoins = $besoinsModel->getBesoinsByVille($id);

        Flight::render('villes/show', [
            'ville' => $ville,
            'besoins' => $besoins
        ]);
    }

    /**
     * Supprime une ville
     */
    public function deleteVille($id)
    {
        $villesModel = $this->villesModel;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $villesModel->delete($id);
            
            if ($result) {
                Flight::redirect('/villes?success=ville_supprimee');
            } else {
                Flight::json(['error' => 'Erreur lors de la suppression']);
            }
            return;
        }
        
        Flight::json(['deleted' => false]);
    }

    /**
     * Page de confirmation de suppression
     */
    public function confirmDeleteVille($id)
    {
        $ville = $this->villesModel->getById($id);
        
        if (!$ville) {
            Flight::halt(404, 'Ville introuvable');
        }

        Flight::render('confirm_delete', [
            'entity' => 'ville',
            'id' => $id,
            'label' => 'Ville: ' . $ville['nom_ville'],
            'back' => '/villes',
            'details' => $ville
        ]);
    }

    /**
     * Nombre total de villes
     */
    public function getNombreVilles()
    {
        $nombre = $this->villesModel->count();
        Flight::json(['nombre' => $nombre]);
    }

    /**
     * Besoins d'une ville
     */
    public function getVilleBesoins($id)
    {
        $ville = $this->villesModel->getById($id);
        
        if (!$ville) {
            Flight::halt(404, 'Ville introuvable');
        }

        $besoinsModel = new BesoinsModel($this->db);
        $besoins = $besoinsModel->getBesoinsByVille($id);

        Flight::json([
            'ville' => $ville,
            'besoins' => $besoins
        ]);
    }

    /**
     * Récupère toutes les régions
     */
    private function getAllRegions()
    {
        $regionsModel = new \app\models\RegionsModel($this->db);
        return $regionsModel->getAll();
    }
}
