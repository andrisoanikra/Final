<?php

namespace app\controllers;

use app\models\ArticlesModel;
use flight\Engine;

class ArticleController {

	protected Engine $app;

	public function __construct($app) {
		$this->app = $app;
	}

    public function getAllArticles() {
        $articleModel = new ArticlesModel($this->app);
        $articles = $articleModel->getAllArticles();
        return $articles;
    }

}
