<?php

class Payment
{
    private $conn;
    private $table = "payments";

    // 🧾 Properties
    public $id;
    public $user_id;
    public $order_id;
    public $amount;
    public $currency;
    public $payment_method;
    public $status;
    public $checkout_url;
    public $checkout_session_id;

    // ⚙️ Constructor
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // 🧼 Sanitize helper
    private function sanitize()
    {
        foreach ([
            'user_id',
            'order_id',
            'amount',
            'currency',
            'payment_method',
            'status',
            'checkout_url',
            'checkout_session_id'
        ] as $prop) {
            if (isset($this->$prop)) {
                $this->$prop = htmlspecialchars(strip_tags($this->$prop));
            }
        }
    }

    // ➕ Create new payment
    public function create()
    {
        $this->sanitize();

        $query = "
            INSERT INTO {$this->table} 
            (user_id, order_id, amount, currency, payment_method, status, checkout_url, checkout_session_id)
            VALUES (:user_id, :order_id, :amount, :currency, :payment_method, :status, :checkout_url, :checkout_session_id)
        ";

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

    // 💰 Mark payment as paid (for webhook or manual confirmation)
    public function updateToPaidByOrderId($order_id)
    {
        $query = "
            UPDATE {$this->table}
            SET status = 'Paid'
            WHERE order_id = :order_id
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $order_id);
        return $stmt->execute();
    }

    // 🔍 Get payment by order ID
    public function getByOrderId($order_id)
    {
        $query = "
            SELECT * 
            FROM {$this->table} 
            WHERE order_id = :order_id
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 📋 Get all payments
    public function getAll()
    {
        $query = "SELECT * FROM {$this->table}";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
