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
        FROM " . $this->table . " a 
        JOIN menu_items b on a.menu_item_id = b.id 
        JOIN menu_categories c on b.category_id = c.id 
        WHERE a.order_id = :orderId" // <--- Added a closing quote here
    ); // <--- Closed the parenthesis for prepare

    $stmt->bindParam(':orderId', $orderId);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function create(){
   
   $stmt = $this->conn->prepare("INSERT INTO ".$this->table." (order_id,menu_item_id,quantity,price,subtotal)
   VALUES(:order_id,:menu_item_id,:quantity,:price,:subtotal)");

   $stmt->bindParam(":order_id", $this->order_id);
   $stmt->bindParam(":menu_item_id", $this->menu_item_id);
   $stmt->bindParam(":quantity", $this->quantity);
   $stmt->bindParam(":price", $this->price);
   $stmt->bindParam(":subtotal", $this->subtotal);

   return $stmt->execute();

}

 public function delete($id)
 {
   $stmt = $this->conn->prepare("DELETE FROM ".$this->table." WHERE id = :id");
         $stmt->bindParam(':id',$this-id);
         return $stmt->execute();
 }

}