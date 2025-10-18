<?php

class Shippingfee{
  private $conn;
  private $table = "shipping_fee";
  public $id;
  
  
    public function __construct($db) {
        $this->conn = $db;
    }


    public function getShippingFeeById($id)
    {
            $stmt = $this->conn->prepare("SELECT fee FROM " . $this->table . " WHERE id = :id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}