<?php
class CustomizeWebsite {
    private $conn;
    private $table = "custom_image_website"; // typo fixed

    public $id;
    public $image_custom_name;
    public $image_path;

    public function __construct($db) {
        $this->conn = $db;
    }

    // CREATE
    public function create() {
        $query = "INSERT INTO {$this->table} (image_custom_name, image_path)
                  VALUES (:image_custom_name, :image_path)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':image_custom_name', $this->image_custom_name);
        $stmt->bindParam(':image_path', $this->image_path);
        return $stmt->execute();
    }

    // READ ALL
    public function getAll() {
        $query = "SELECT * FROM {$this->table} ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // READ BY ID
    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // UPDATE NAME ONLY
    public function updateName() {
        $query = "UPDATE {$this->table}
                  SET image_custom_name = :image_custom_name
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':image_custom_name', $this->image_custom_name);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    // UPDATE IMAGE ONLY
    public function updateImage() {
        $query = "UPDATE {$this->table}
                  SET image_path = :image_path
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':image_path', $this->image_path);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    // DELETE
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
