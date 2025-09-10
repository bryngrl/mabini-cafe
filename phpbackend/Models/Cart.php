<?php

class Cart{

  private $conn;
  private $table = "carts";
  public $id;
  public $user_id;
  public $menu_item_id;
  public $quantity;
  public $subtotal;


 

    // GET ALL carts in table
    public function getAll(){
        $stmt = $this->conn->prepare("SELECT
          a.id,
          b.username AS customer_name,
          c.name AS menu_item_name,
          d.name AS category_name,
          c.description AS menu_item_description,
          c.price AS menu_item_price,
          a.quantity,
          a.subttotal
          FROM ".$this->table." a 
          JOIN users b on a.user_id = b.id
          JOIN menu_items c on a.menu_item_id = c.id
          JOIN menu_categories d on c.category_id = d.id

        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



       // GET cart by id
      public function getById($id)
  {
      $stmt = $this->conn->prepare("SELECT
          a.id,
          b.username AS customer_name,
          c.name AS menu_item_name,
          d.name AS category_name,
          c.description AS menu_item_description,
          c.price AS menu_item_price,
          a.quantity,
          a.subttotal
          FROM ".$this->table." a 
          JOIN users b on a.user_id = b.id
          JOIN menu_items c on a.menu_item_id = c.id
          JOIN menu_categories d on c.category_id = d.id
           WHERE a.id = :id;
        ");
    $stmt->bindParam(':id',$id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);

  }
   
    //Get by Customer id
     public function getByCustomerId($customerId)
     {
         $stmt = $this->conn->prepare("SELECT
          a.id,
          b.username AS customer_name,
          c.name AS menu_item_name,
          d.name AS category_name,
          c.description AS menu_item_description,
          c.price AS menu_item_price,
          a.quantity,
          a.subttotal
          FROM ".$this->table." a 
          JOIN users b on a.user_id = b.id
          JOIN menu_items c on a.menu_item_id = c.id
          JOIN menu_categories d on c.category_id = d.id
           WHERE a.user_id = :id;
        ");

          $stmt->bindParam(':id',$id);
          $stmt->execute();
          return $stmt->fetchAll(PDO::FETCH_ASSOC);

     }

        //add to cart
       public function create(){
            $stmt = $this->conn->prepare(
            "INSERT INTO ".$this->table."(user_id,menu_item_id,quantity,subtotal)
             VALUES(:user_id,:menu_item_id,:quantity,:subtotal);
            "
          );
          
          $stmt->bindParam(':user_id',$this->user_id);
          $stmt->bindParam(':menu_item_id',$this->menu_item_id);
          $stmt->bindParam(':quantity',$this->quantity);
          $stmt->bindParam('sudtotal',$this->subtotal);
          $stmt->execute();
       }



}