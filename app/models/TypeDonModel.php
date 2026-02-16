<?php
namespace app\models;

use flight\Engine;

class TypeDonModel {

    protected Engine $app;

    public function __construct(Engine $app) {
        $this->app = $app;
    }

    public function getAllTypes() {

        $db = $this->app->get('db');

        $result = $db->query("SELECT * FROM type_don");

        $types = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $types[] = $row;
            }
        }

        return $types;
    }
}
