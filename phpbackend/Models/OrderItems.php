<?php

class OrderItems{

private $conn;
private $table = "order_items";
public $id;
public $menu_item_id;
public $order_id;
public $quantity;
public $price;
public $subtotal;

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

public function getById($id){
  $stmt = $this->conn->prepare("SELECT 
    a.id,
    a.order_id,
    b.name as order_name,
    b.description as description,
    c.name as category_name,
    a.quantity,
    a.price,
    a.subtotal 
    FROM ".$this->table." a JOIN menu_items b on a.menu_item_id = b.id 
    JOIN menu_categories c on b.category_id = c.id 
    WHERE a.id = :id
  ");

   $stmt->bindParam(':id',$id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);

}
 
public function getByOrderId($orderId)
{

     $stmt = $this->conn->prepare("SELECT 
    a.id,
    a.order_id,
    b.name as order_name,
    b.description as description,
    c.name as category_name,
    a.quantity,
    a.price,
    a.subtotal 
    FROM ".$this->table." a JOIN menu_items b on a.menu_item_id = b.id 
    JOIN menu_categories c on b.category_id = c.id 
    WHERE a.order_id = :orderId
  ");

   $stmt->bindParam(':orderId',$orderId);
   $stmt->execute();
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
 
public function create(){
  
}

 public function delete($id)
 {
   $stmt = $this->conn->prepare("DELETE FROM ".$this->table." WHERE id = :id");
         $stmt->bindParam(':id',$id);
         return $stmt->execute();
 }

}