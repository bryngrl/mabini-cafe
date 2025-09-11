<?php

class Order
{

private $conn;
private $table = "orders";
public $id;
public $user_id;
public $total_amount;
public $payment_status;

 public function __construct($db)
 {
 $this->conn = $db;
 }


 public function getAll()
 {
  $stmt = $this->conn->prepare("SELECT * FROM ".$this->table);
  $stmt->execute();
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
 }


 public function getById()
 {

 }


 public function create()
 {

 }

 public function update()
 {

 }

 public function delete()
 {}







}