<?php

class User{
    private $conn;
    private $table = "users";
    public $id;
    public $username;
    public $email;
    public $password;



    public function __construct($db)
    {
        $this->conn = $db;
    }

    // GET ALL USERS
    public function getAll(){
        $stmt = $this->conn->prepare("SELECT * FROM ".$this->table);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    //GET USERS BY ID
    public function getById($id)
    {
        $stmt = $this->conn->prepare("SELECT id,email,username FROM ".$this->table." WHERE id =:id");
        $stmt->bindParam(":id",$id);
          $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC); // fetch single row

    }

        // CREATE user
    public function create() {
        $stmt = $this->conn->prepare(
            "INSERT INTO " . $this->table . "(username, email, password) VALUES (:username, :email, :password)"
        );
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password); // hashed password
        // $stmt->bindParam(":address",$this->address);
        // $stmt->bindParam(":contact_number",$this->contact_number);
        return $stmt->execute();
    }

    // UPDATE user
    public function update()
    {
        $stmt = $this->conn->prepare(
            "UPDATE " . $this->table . " SET username=:username, email=:email WHERE id=:id"
        );

        $stmt->bindParam(":username",$this->username);
        $stmt->bindParam(":email",$this->email);
        $stmt->bindParam(":id", $this->id);
        // $stmt->bindParam(":address",$this->address);
        // $stmt->bindParam(":contact_number", $this->contact_number);
        return $stmt->execute();
    }


    // DELETE user
    public function delete() {
        $stmt = $this->conn->prepare("DELETE FROM " . $this->table . " WHERE id=:id");
        $stmt->bindParam(":id", $this->id);
        return $stmt->execute();
    }

    // Find user by Email (for login)
    public function findByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM " . $this->table . " WHERE email=:email");
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}