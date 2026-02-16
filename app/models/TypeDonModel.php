<?php
namespace app\models;

class TypeDonModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAllTypes()
    {
        $tmt = $this->db->runQuery("SELECT * FROM type_don ORDER BY libelle_type;");
        return $tmt->fetchAll();
    }

    public function getTypeById($id)
    {
        $tmt = $this->db->runQuery("SELECT * FROM type_don WHERE id_type_don = ?;", [$id]);
        return $tmt->fetch();
    }

    public function addType($libelle_type)
    {
        $tmt = $this->db->runQuery("INSERT INTO type_don (libelle_type) VALUES (?);", [$libelle_type]);
        return $this->db->lastInsertId();
    }

    public function updateType($id, $libelle_type)
    {
        $tmt = $this->db->runQuery("UPDATE type_don SET libelle_type = ? WHERE id_type_don = ?;", [$libelle_type, $id]);
        return $tmt->rowCount();
    }

    public function deleteType($id)
    {
        $tmt = $this->db->runQuery("DELETE FROM type_don WHERE id_type_don = ?;", [$id]);
        return $tmt->rowCount();
    }
}
