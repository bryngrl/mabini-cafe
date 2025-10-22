<?php

class ShipInfo {
    private $conn;
    private $table = "ship_infos";

    public $id;
    public $user_id;
    public $email;
    public $first_name;
    public $last_name;
    public $address;
    public $apartment_suite_etc;
    public $postal_code;
    public $city;
    public $region;
    public $phone;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $stmt = $this->conn->prepare("SELECT * FROM " . $this->table);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM " . $this->table . " WHERE id = :id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByUserId($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM " . $this->table . " WHERE user_id = :user_id");
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create() {
        $stmt = $this->conn->prepare("
            INSERT INTO " . $this->table . " 
            (user_id, email, first_name, last_name, address, apartment_suite_etc, postal_code, city, region, phone)
            VALUES (:user_id, :email, :first_name, :last_name, :address, :apartment_suite_etc, :postal_code, :city, :region, :phone)
        ");

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':first_name', $this->first_name);
        $stmt->bindParam(':last_name', $this->last_name);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':apartment_suite_etc', $this->apartment_suite_etc);
        $stmt->bindParam(':postal_code', $this->postal_code);
        $stmt->bindParam(':city', $this->city);
        $stmt->bindParam(':region', $this->region);
        $stmt->bindParam(':phone', $this->phone);

        return $stmt->execute();
    }

    public function update() {
        $stmt = $this->conn->prepare("
            UPDATE " . $this->table . " 
            SET 
                email = :email, 
                first_name = :first_name,
                last_name = :last_name, 
                address = :address, 
                apartment_suite_etc = :apartment_suite_etc, 
                postal_code = :postal_code,
                city = :city, 
                region = :region, 
                phone = :phone 
            WHERE user_id = :user_id
        ");

        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':first_name', $this->first_name);
        $stmt->bindParam(':last_name', $this->last_name);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':apartment_suite_etc', $this->apartment_suite_etc);
        $stmt->bindParam(':postal_code', $this->postal_code);
        $stmt->bindParam(':city', $this->city);
        $stmt->bindParam(':region', $this->region);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':user_id', $this->user_id);

        return $stmt->execute();
    }

    public function delete() {
        $stmt = $this->conn->prepare("DELETE FROM " . $this->table . " WHERE id = :id");
        $stmt->bindParam(":id", $this->id);
        return $stmt->execute();
    }
}

