<?php

namespace app\controllers;

use Flight;
use app\models\TypeDonModel;

class TypeDonController
{
    protected $db;
    protected $typeDonModel;

    public function __construct()
    {
        $this->db = Flight::db();
        $this->typeDonModel = new TypeDonModel($this->db);
    }

    public function getAllTypes()
    {
        $typeDons = $this->typeDonModel->getAllTypes();
        return $typeDons;
    }
}