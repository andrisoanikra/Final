<?php
// app/controllers/ArticlesController.php - nampian'i Francia

namespace app\controllers;

use app\models\ArticlesModel;

class ArticlesController
{
    private $model;
    private $db;
    
    public function __construct($db)
    {
        $this->db = $db;
        $this->model = new ArticlesModel($db);
    }
    
    // Liste des articles
    public function index()
    {
        $articles = $this->model->getAllArticles();
        
        // Charger la vue
        require_once __DIR__ . '/../views/articles/liste.php';
    }
    
    // Formulaire d'ajout
    public function ajouter()
    {
        $types = $this->model->getAllTypesBesoin();
        require_once __DIR__ . '/../views/articles/form.php';
    }
    
    // Traitement ajout
    public function save()
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = $_POST['nom_article'] ?? '';
            $type = $_POST['id_type_besoin'] ?? '';
            $description = $_POST['description'] ?? '';
            $prix_unitaire = $_POST['prix_unitaire'] ?? 0;
            
            if(!empty($nom) && !empty($type) && $prix_unitaire > 0) {
                $this->model->createArticle($nom, $type, $description, $prix_unitaire);
                header('Location: /articles?success=1');
                exit;
            } else {
                header('Location: /articles/ajouter?error=1');
                exit;
            }
        }
    }
    
    // Formulaire modification
    public function modifier($id)
    {
        $article = $this->model->getArticleById($id);
        $types = $this->model->getAllTypesBesoin();
        
        if(!$article) {
            header('Location: /articles?notfound=1');
            exit;
        }
        
        require_once __DIR__ . '/../views/articles/form.php';
    }
    
    // Traitement modification
    public function update($id)
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = $_POST['nom_article'] ?? '';
            $type = $_POST['id_type_besoin'] ?? '';
            $description = $_POST['description'] ?? '';
            $prix_unitaire = $_POST['prix_unitaire'] ?? 0;
            
            if(!empty($nom) && !empty($type) && $prix_unitaire > 0) {
                $this->model->updateArticle($id, $nom, $type, $description, $prix_unitaire);
                header('Location: /articles?updated=1');
                exit;
            } else {
                header("Location: /articles/modifier/$id?error=1");
                exit;
            }
        }
    }
    
    // Suppression
    public function supprimer($id)
    {
        $result = $this->model->deleteArticle($id);
        
        if($result) {
            header('Location: /articles?deleted=1');
        } else {
            header('Location: /articles?error_delete=1');
        }
        exit;
    }
}