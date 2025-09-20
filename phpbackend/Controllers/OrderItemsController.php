<?php
require_once(__DIR__ . '/../Auth/Auth.php');
require_once(__DIR__ . '/../auth/jwtMiddleware.php');
require_once(__DIR__ . '/../Models/OrderItems.php');


class OrderItemsController{
  
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
        echo json_encode(["Nothing"=>"no OrderItem found"]);
    }
   }


   public function showByOrderId($orderId){
    $order = $this->model->getByOrderId($orderId);

    if($order)
    {
        echo json_encode($order);
    }else{
          echo json_encode(["Nothing"=>"no OrderItem found"]);
    }
   }

   //create orderItem
   public function store(){

     $data = json_decode(file_get_contents("php://input"), true);

     if(in_array(null,$data,true) || in_array('',$data,true)){
         http_response_code(400);
         echo json_encode(["error" => "Incomplete data"]);
     }else{

        if($this->model->create()){
            http_response_code(201);
            echo json_encode(["message"=>"OrderItems created successfully"]);
        }else{
               http_response_code(500);
              echo json_encode(["error"=>"Failed to create orderItems"]);
        }
     }
   }




}