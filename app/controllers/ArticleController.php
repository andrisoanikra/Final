<?php

namespace app\controllers;

use Flight;
use app\models\ArticlesModel;

class ArticleController
{
    protected $db;
    protected $articlesModel;

    public function __construct()
    {
        $this->db = Flight::db();
        $this->articlesModel = new ArticlesModel($this->db);
    }

    public function getAllArticles()
    {
        $articles = $this->articlesModel->getAllArticles();
        return $articles;
    }
}
