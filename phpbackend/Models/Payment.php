<?php

class Payment {
    private $conn;
    private $table = "payments";

    public $id;
    public $user_id;
    public $order_id;
    public $amount;
    public $currency;
    public $payment_method;
    public $status;
    public $checkout_url;
    public $reference_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO {$this->table} 
                  (user_id, order_id, amount, currency, payment_method, status, checkout_url)
                  VALUES (:user_id, :order_id, :amount, :currency, :payment_method, :status, :checkout_url)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':order_id', $this->order_id);
        $stmt->bindParam(':amount', $this->amount);
        $stmt->bindParam(':currency', $this->currency);
        $stmt->bindParam(':payment_method', $this->payment_method);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':checkout_url', $this->checkout_url);

        return $stmt->execute();
    }

    public function updateStatus($order_id, $new_status) {
        $query = "UPDATE {$this->table} SET status = :status WHERE order_id = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $new_status);
        $stmt->bindParam(':order_id', $order_id);
        return $stmt->execute();
    }

    public function getByOrderId($order_id) {
        $query = "SELECT * FROM {$this->table} WHERE order_id = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
