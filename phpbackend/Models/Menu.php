<?php

class Menu{
  private $conn;
  private $table = "menu_items";
  public $id;
  public $name;
  public $description;
  public $price;
  public $category_id;
  public $image_path;
  
  public function __construct($db)
  {
    $this->conn =$db;
  }

// SELECT 
//     menu_items.id,
//     menu_items.item_name,
//     menu_items.price,
//     menu_category.category_name
// FROM menu_items
// JOIN menu_category 
//     ON menu_items.category_id = menu_category.id;


    // GET ALL menu_items in table
    public function getAll(){
        $stmt = $this->conn->prepare(
      "SELECT 
       a.id,
      a.name,
      a.description,
      a.price,
      a.image_path,
      b.name 
      FROM ".$this->table." a JOIN menu_categories b ON 
      a.category_id = b.id 
      "
       );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



  // get Menu by id
  public function getById($id)
  {
    $stmt = $this->conn->prepare(
      "SELECT 
       a.id,
      a.name,
      a.description,
      a.price,
      a.image_path,
      b.name 
      FROM ".$this->table." a JOIN menu_categories b ON 
      a.category_id = b.id WHERE a.id = :id
      "
    );

    $stmt->bindParam(':id',$id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);

  }


  
  // get Menu by  category_id
  public function getByCategoryId($id)
  {
    $stmt = $this->conn->prepare(
      "SELECT 
       a.id,
      a.name,
      a.description,
      a.price,
      a.image_path,
      b.name AS category_name
      FROM ".$this->table." a JOIN menu_categories b ON 
      a.category_id = b.id WHERE a.category_id = :id
      "
    );

    $stmt->bindParam(':id',$id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);

  }


  public function getByDescription($description){
     $stmt = $this->conn->prepare(
      "SELECT 
       a.id,
      a.name,
      a.description,
      a.price,
      a.image_path,
      b.name AS category_name
      FROM ".$this->table." a JOIN menu_categories b ON 
      a.category_id = b.id WHERE a.description = :description
      "
    );

    $stmt->bindParam(':description',$description);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }


  //create new Menu 
  public function create(){
    $stmt = $this->conn->prepare(
      "INSERT INTO".$this->table."(name,description,price,category_id,image_path)
       VALUES (:name,:description,:price,:category_id,image_path)
      " 
    );
    $stmt->bindParam(':name',$this->name);
    $stmt->bindParam(':description',$this->description);
    $stmt->bindParam(':price',$this->price);
    $stmt->bindParam(':category_id',$this_category_id);
    $stmt->bindParam(':image_path',$image_path);
      return $stmt->execute();
  }

//update Menu
 public function update(){
    $stmt = $this->conn->prepare(
      "UPDATE".$this->table." SET name=:name, description=:description, price=:price, category_id=:category_id, image_path=:image_path WHERE id=:id"
    );
    $stmt->bindParam(':id');
    $stmt->bindParam(':name',$this->name);
    $stmt->bindParam(':description',$this->description);
    $stmt->bindParam(':price',$this->price);
    $stmt->bindParam(':category_id',$this_category_id);
    $stmt->bindParam(':image_path',$image_path);
      return $stmt->execute();
 }
 

  // DELETE user
    public function delete() {
        $stmt = $this->conn->prepare("DELETE FROM " . $this->table . " WHERE id=:id");
        $stmt->bindParam(":id", $this->id);
        return $stmt->execute();
    }
  
}