<?php

class Payment {
    private $conn;
    private $table = "payments";

    // ========== Properties ==========
    public $id;
    public $user_id;
    public $order_id;
    public $amount;
    public $currency;
    public $payment_method;
    public $status;
    public $checkout_url;
    public $checkout_session_id;

    // ========== Constructor ==========
    public function __construct($db) {
        $this->conn = $db;
    }

    // ========== Sanitize Helper ==========
    private function sanitize() {
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->order_id = htmlspecialchars(strip_tags($this->order_id));
        $this->amount = htmlspecialchars(strip_tags($this->amount));
        $this->currency = htmlspecialchars(strip_tags($this->currency));
        $this->payment_method = htmlspecialchars(strip_tags($this->payment_method));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->checkout_url = htmlspecialchars(strip_tags($this->checkout_url));
        $this->checkout_session_id = htmlspecialchars(strip_tags($this->checkout_session_id));
    }

    // ========== Create Payment ==========
    public function create() {
        $this->sanitize();

        $query = "INSERT INTO {$this->table} 
                  (user_id, order_id, amount, currency, payment_method, status, checkout_url, checkout_session_id)
                  VALUES (:user_id, :order_id, :amount, :currency, :payment_method, :status, :checkout_url, :checkout_session_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':order_id', $this->order_id);
        $stmt->bindParam(':amount', $this->amount);
        $stmt->bindParam(':currency', $this->currency);
        $stmt->bindParam(':payment_method', $this->payment_method);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':checkout_url', $this->checkout_url);
        $stmt->bindParam(':checkout_session_id', $this->checkout_session_id);

        return $stmt->execute();
    }


    // ========== Update row to paid (for Webhook) ==========
    public function updateToPaidByOrderId($order_id) {
        $query = "UPDATE {$this->table} 
                  SET status = 'paid' 
                  WHERE order_id = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $order_id);
        return $stmt->execute();
    }


     // ========== Update row to failed (for Webhook) ==========
  public function updateToFailedByCheckoutId($checkout_session_id) {
    $query = "UPDATE {$this->table}
              SET status = 'failed'
              WHERE checkout_session_id = :checkout_session_id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':checkout_session_id', $checkout_session_id);
    return $stmt->execute();
}


    // ========== Get Payment by Order ID ==========
    public function getByOrderId($order_id) {
        $query = "SELECT * FROM {$this->table} WHERE order_id = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


      // ========== Get Payment by Order ID ==========
    public function getAll() {
        $query = "SELECT * FROM {$this->table}";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchall(PDO::FETCH_ASSOC);
    }


}
