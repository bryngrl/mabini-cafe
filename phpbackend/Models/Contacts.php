<?php

class Contacts{

 private $conn;
  private $table = "contact_messages";
  public $id;
  public $user_id;
  public $name;
  public $email;
  public $contact_reason;
  public $order_id;
  public $topic;
  public $message;
  public $image_path;

 public function __construct($db) {
  $this->conn = $db;
 }

public function create(){
   $stmt = $this->conn->prepare("INSERT INTO ".$this->table." (user_id,name,email,contact_reason,order_id,topic,message,image_path)
   VALUES (:user_id,:name,:email,:contact_reason,:order_id,:topic,:message,:image_path)");

   $stmt->bindParam(':user_id',$this->user_id);
   $stmt->bindParam(':name',$this->name);
   $stmt->bindParam(':email',$this->email);
   $stmt->bindParam(':contact_reason',$this->contact_reason);
   $stmt->bindParam(':order_id',$this->order_id);
   $stmt->bindParam(':topic',$this->topic);
   $stmt->bindParam(':message',$this->message);
   $stmt->bindParam(':image_path',$this->image_path);
     return $stmt->execute();
}


public function getAll(){
   $stmt = $this->conn->prepare("SELECT * FROM ".$this->table);
     $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


}