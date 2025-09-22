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

   public function __construct($db)
   {
   $this->conn = $db;
   }


   public function getAll()
   {
   $stmt = $this->conn->prepare("SELECT 
      a.id,
      b.username as customer_name,
      b.address as customer_address,
      a.total_amount,
      a.status,
      a.payment_status,
      a.payment_method,
      a.created_at as order_time
      FROM ".$this->table." a JOIN users b on a.user_id = b.id");
      $stmt->execute();
   return $stmt->fetchAll(PDO::FETCH_ASSOC);
   }


   public function getById($id)
   {
   $stmt = $this->conn->prepare("SELECT 
      a.id,
      b.username as customer_name,
      b.address as customer_address,
      a.total_amount,
      a.status,
      a.payment_status,
      a.payment_method,
      a.created_at as order_time
      FROM ".$this->table." a JOIN users b on a.user_id = b.id WHERE a.id = :id");
      $stmt->bindParam(":id",$id);

      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);

   }

   public function getByCustomerId($customerId)
   {
   
      $stmt = $this->conn->prepare("SELECT 
      a.id,
      b.username as customer_name,
      b.address as customer_address,
      a.total_amount,
      a.status,
      a.payment_status,
      a.payment_method,
      a.created_at as order_time
      FROM ".$this->table." a JOIN users b on a.user_id = b.id WHERE a.user_id= :customerId");
      
      $stmt->bindParam(":customerId",$customerId);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);

   }

   public function getTotalDelivered(){
      $stmt = $this->conn->prepare("SELECT COUNT(*) AS total_delivered FROM ".$this->table."WHERE status = 'Completed'");
      $stmt->execute();

      $total = $stmt->fetch(PDO::FETCH_ASSOC);
      return $total['total_delivered'];
   }

   public function getTotalCancelled(){
      $stmt= $this->conn->prepare("SELECT COUNT(*) AS total_cancelled FROM ".$this->table."WHERE status = 'Cancelled'");
      $stmt->execute();
      $total = $stmt->fetch(PDO::FETCH_ASSOC);
      return $total['total_cancelled'];
   }

   public function getTotalOrders(){
      $stmt = $this->conn->prepare("SELECT COUNT(*) AS total_orders FROM ".$this->table);
      $stmt->execute();
      $total = $stmt->fetch(PDO::FETCH_ASSOC);
      return $total['total_orders'];
   }

   public function create()
   {
      $stmt =$this->conn->prepare("INSERT INTO ".$this->table." (user_id,total_amount) 
      VALUES(:user_id,:total_amount)
      ");

      $stmt->bindParam(":user_id",$this->user_id);
      $stmt->bindParam(":total_amount",$this->total_amount);
       return $stmt->execute();
   }

   public function setPreparingOrder($id){
      $stmt= $this->conn->prepare("UPDATE ".$this->table." SET status = 'Preparing' WHERE id = :id");
      $stmt->bindParam(":id",$id);
         return $stmt->execute();
   }

   public function setCompletedOrder($id){
      $stmt= $this->conn->prepare("UPDATE ".$this->table." SET status = 'Completed' WHERE id = :id");
      $stmt->bindParam(":id",$id);
         return $stmt->execute();
   }
   
   public function setDeliveringOrder($id){
      $stmt= $this->conn->prepare("UPDATE ".$this->table." SET status = 'Delivering' WHERE id = :id");
      $stmt->bindParam(":id",$id);
     return $stmt->execute();
   }
   

   
   public function setCancelingOrder($id){
      $stmt= $this->conn->prepare("UPDATE ".$this->table." SET status = 'Canceled' WHERE id = :id");
      $stmt->bindParam(":id",$id);
        return $stmt->execute();
   }

   

   

   public function setPaidStatus($id)
   {
      $stmt= $this->conn->prepare("UPDATE ".$this->table." SET payment_status = 'Paid' WHERE id = :id");
      $stmt->bindParam(":id",$id);
        return $stmt->execute();
   }

  public function setToOnline($id){
      $stmt = $this->conn->prepare("UPDATE ".$this->table." SET payment_method = 'Online' WHERE id =");
  }



   }