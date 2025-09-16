<?php

require_once(__DIR__ . '/../Auth/Auth.php');
require_once(__DIR__ . '/../auth/jwtMiddleware.php');
require_once(__DIR__ . '/../Models/Order.php');

class OrderController{
   
    
    private $model;

    public function __construct($db) {
        $this->model = new order($db);
        header('Content-Type: application/json');
    }

   // return all Order in server
   public function index(){
     echo json_encode($this->model->getAll());
   } 



    // return specific order to server
   public function show($id){
    $order = $this->model->getById($id);
    
    if($order)
    {
        echo json_encode($order);
    }else{
        echo json_encode(["Nothing"=>"no Order found"]);
    }
   }
  
   // show order based in Customer id
   public function showByCustomerId($customerId){
    
     $order = $this->model->getByCustomerId();
    
     if($order){
        echo json_encode($order);
     }else{
        echo json_encode(["Nothing"=>"no Customer order found"]);
     }

   }

    // new order
   public function store()
   {
    $data = json_decode(file_get_contents("php://input"), true);
      
     if(!empty($data['user_id']) && !empty($data['total_amount'])){
       
        if($this->model->create()){
               http_response_code(201);
                echo json_encode(["message"=>"order created successfully"]);
        }else{
              http_response_code(500);
              echo json_encode(["error"=>"Failed to create order"]);
        }
      
     }else{
            http_response_code(400);
            echo json_encode(["error" => " name and price required"]);
     }

   }
   // set to preparing
   public function updateToPreparing($id){
      $odrer=$this->model->setPreparingOrder($id);

      if($order){
            http_response_code(201);
            echo json_encode(["message"=>"Order created successfully"]);
      }else{
          http_response_code(500);
          echo json_encode(["error"=>"Failed to update order"]);
      }

   }

   //set to completed
  public function updateToCompleted($id)
  {
   $odrer=$this->model->setCompletedOrder($id);

      if($order){
            http_response_code(201);
            echo json_encode(["message"=>"Order created successfully"]);
      }else{
          http_response_code(500);
          echo json_encode(["error"=>"Failed to update order"]);
      }

  }
//set to delivering
 public function updateToDelivering($id){
     $order=$this->model->setDeliveringOrder($id);

      if($order){
            http_response_code(201);
            echo json_encode(["message"=>"Order created successfully"]);
      }else{
          http_response_code(500);
          echo json_encode(["error"=>"Failed to update order"]);
      }

 }

// set to canelled
 public function updateToCancelled($id){
     $odrer=$this->model->setCancelingOrder($id);

      if($order){
            http_response_code(201);
            echo json_encode(["message"=>"Order created successfully"]);
      }else{
          http_response_code(500);
          echo json_encode(["error"=>"Failed to update order"]);
      }

 }

 //set opaid payment status
 public function updatePaymentStatus($id)
 {
  $odrer=$this->model->setPaidStatus($id);

      if($order){
            http_response_code(201);
            echo json_encode(["message"=>"Order created successfully"]);
      }else{
          http_response_code(500);
          echo json_encode(["error"=>"Failed to update order"]);
      }



 }

 //delete
public function destroy($id)
{
  $order = $this->model->delete($id);
  
      if($order){
            http_response_code(201);
            echo json_encode(["message"=>"Order deleted successfully"]);
      }else{
          http_response_code(500);
          echo json_encode(["error"=>"Failed to delete order"]);
      }
}

}