<?php

class Order
{
    private $conn;
    private $table = "orders";

    public $id;
    public $user_id;
    public $total_amount;
    public $status;
    public $payment_status;
    public $payment_method;
    public $shipping_fee_id;

    public function __construct($db)
    {
        $this->conn = $db;
    }

  // Get all orders
public function getAll()
{
    $stmt = $this->conn->prepare("
        SELECT 
            a.id,
            b.username AS customer_name,
            a.total_amount,
            a.status,
            a.payment_status,
            a.payment_method,
            a.shipping_fee_id,
            c.ship_name AS shipping_name,
            c.fee AS shipping_fee,
            a.created_at AS order_time
        FROM {$this->table} a
        JOIN users b ON a.user_id = b.id
        JOIN shipping_fee c ON a.shipping_fee_id = c.id
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get single order by ID
public function getById($id)
{
    $stmt = $this->conn->prepare("
        SELECT 
            a.id,
            b.username AS customer_name,
            a.total_amount,
            a.status,
            a.payment_status,
            a.payment_method,
            a.shipping_fee_id,
            c.ship_name AS shipping_name,
            c.fee AS shipping_fee,
            a.created_at AS order_time
        FROM {$this->table} a
        JOIN users b ON a.user_id = b.id
        JOIN shipping_fee c ON a.shipping_fee_id = c.id
        WHERE a.id = :id
    ");
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


// Get all orders by a specific customer
public function getByCustomerId($customerId)
{
    $stmt = $this->conn->prepare("
        SELECT 
            a.id,
            b.username AS customer_name,
            a.total_amount,
            a.status,
            a.payment_status,
            a.payment_method,
            a.shipping_fee_id,
            c.ship_name AS shipping_name,
            c.fee AS shipping_fee,
            a.created_at AS order_time
        FROM {$this->table} a
        JOIN users b ON a.user_id = b.id
        JOIN shipping_fee c ON a.shipping_fee_id = c.id
        WHERE a.user_id = :customerId
    ");
    $stmt->bindParam(":customerId", $customerId);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    //  Totals
    public function getTotalDelivered()
    {
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) AS total_delivered 
            FROM {$this->table} 
            WHERE status = 'Completed'
        ");
        $stmt->execute();
        $total = $stmt->fetch(PDO::FETCH_ASSOC);
        return $total['total_delivered'];
    }

    public function getTotalCancelled()
    {
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) AS total_cancelled 
            FROM {$this->table} 
            WHERE status = 'Canceled'
        ");
        $stmt->execute();
        $total = $stmt->fetch(PDO::FETCH_ASSOC);
        return $total['total_cancelled'];
    }

    public function getTotalOrders()
    {
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) AS total_orders 
            FROM {$this->table}
        ");
        $stmt->execute();
        $total = $stmt->fetch(PDO::FETCH_ASSOC);
        return $total['total_orders'];
    }

    //  Create new order (with shipping_fee_id)
    public function create()
    {
        $stmt = $this->conn->prepare("
            INSERT INTO {$this->table} (user_id, total_amount, shipping_fee_id) 
            VALUES (:user_id, :total_amount, :shipping_fee_id)
        ");
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":total_amount", $this->total_amount);
        $stmt->bindParam(":shipping_fee_id", $this->shipping_fee_id);
        return $stmt->execute();
    }

    //  Status updates
    public function setPreparingOrder($id)
    {
        $stmt = $this->conn->prepare("
            UPDATE {$this->table} 
            SET status = 'Preparing' 
            WHERE id = :id
        ");
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public function setCompletedOrder($id)
    {
        $stmt = $this->conn->prepare("
            UPDATE {$this->table} 
            SET status = 'Completed' 
            WHERE id = :id
        ");
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public function setDeliveringOrder($id)
    {
        $stmt = $this->conn->prepare("
            UPDATE {$this->table} 
            SET status = 'Delivering' 
            WHERE id = :id
        ");
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public function setCancelingOrder($id)
    {
        $stmt = $this->conn->prepare("
            UPDATE {$this->table} 
            SET status = 'Canceled' 
            WHERE id = :id
        ");
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    //  Payment updates
    public function setPaidStatus($id)
    {
        $stmt = $this->conn->prepare("
            UPDATE {$this->table} 
            SET payment_status = 'Paid' 
            WHERE id = :id
        ");
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public function setToOnline($id)
    {
        $stmt = $this->conn->prepare("
            UPDATE {$this->table} 
            SET payment_method = 'Online' 
            WHERE id = :id
        ");
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public function setToCash($id)
    {
        $stmt = $this->conn->prepare("
            UPDATE {$this->table} 
            SET payment_method = 'Cash' 
            WHERE id = :id
        ");
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
