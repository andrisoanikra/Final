<?php

namespace app\controllers;

use app\models\TypeDonModel;
use flight\Engine;

class TypeDonController {

	protected Engine $app;

	public function __construct($app) {
		$this->app = $app;
	}

    public function getAllTypes() {
        $typeDonModel = new TypeDonModel($this->app);
        $typeDons = $typeDonModel->getAllTypes();
        return $typeDons;
    }

}



