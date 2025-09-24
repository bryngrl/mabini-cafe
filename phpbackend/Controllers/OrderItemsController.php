<?php
require_once(__DIR__ . '/../Auth/Auth.php');
require_once(__DIR__ . '/../auth/jwtMiddleware.php');
require_once(__DIR__ . '/../Models/OrderItems.php');


class OrderItemsController{
  
    private $model;
  

    public function __construct($db) {
        $this->model = new orderItems($db);
        header('Content-Type: application/json');
    }

    /**
 * @OA\Get(
 *     path="/mabini-cafe/phpbackend/routes/orderitems",
 *     tags={"Order Items"},
 *     summary="Get all Orderitems",
 *     @OA\Response(
 *         response=200,
 *         description="List of all Orderitems"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal Server Error"
 *     )
 * )
 */

   // return all Order in server
   public function index(){
     echo json_encode($this->model->getAll());
   } 


     /**
 * @OA\Get(
 *     path="/mabini-cafe/phpbackend/routes/orderitems/{id}",
 *     tags={"Order Items"},
 *     summary="Get all Orderitems by id",
 * @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID ng menu Order item",
 *         @OA\Schema(type="integer", example=3)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of all Orderitems"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal Server Error"
 *     )
 * )
 */

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

//     



     /**
 * @OA\Get(
 *     path="/mabini-cafe/phpbackend/routes/orderitems?orderId={orderId}",
 *     tags={"Order Items"},
 *     summary="Get all Orderitems by id",
 * @OA\Parameter(
 *         name="orderId",
 *         in="query",
 *         required=true,
 *         description="ID ng menu Order item",
 *         @OA\Schema(type="integer", example=3)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of all Orderitems based on order id"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal Server Error"
 *     )
 * )
 */

  
   public function showByOrderId($orderId){
    $orderItem = $this->model->getByOrderId($orderId);

    if($orderItem)
    {
        echo json_encode($orderItem);
    }else{
          echo json_encode(["Nothing"=>"no OrderItem found"]);
    }
   }



   
/**
 * @OA\Post(
 *     path="/mabini-cafe/phpbackend/routes/orderitems",
 *     tags={"Order Items"},
 *     summary="Create a new orderItem",
 *     description="Creates a new orderitem",
 *     @OA\RequestBody(
 *         required=true,
 *         description="Order data in JSON format",
 *         @OA\JsonContent(
 *             type="object",
 *             required={"order_id","menu_item_id","quantity","price","subtotal"},
 *             @OA\Property(property="order_id", type="integer", example=1),  
 *             @OA\Property(property="menu_item_id", type="integer", example=1),
 *             @OA\Property(property="quantity", type="integer", example=1),
 *             @OA\Property(property="price", type="number", format="float", example=1.0),
 *             @OA\Property(property="subtotal", type="number", format="float", example=250.75)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Order item created successfully"
 *     )
 * )
 */





   //create orderItem
   public function store(){

     $data = json_decode(file_get_contents("php://input"), true);
     
     if(in_array(null,$data,true) || in_array('',$data,true)){
         http_response_code(400);
         echo json_encode(["error" => "Incomplete data"]);
     }else{
          $this->model->order_id = $data['order_id'];
           $this->model->menu_item_id = $data['menu_item_id'];
           $this->model->quantity = $data['quantity'];
           $this->model->price = $data['price'];
           $this->model->subtotal = $data['subtotal'];
        if($this->model->create($data)){
            http_response_code(201);
            echo json_encode(["message"=>"OrderItems created successfully"]);
        }else{
               http_response_code(500);
              echo json_encode(["error"=>"Failed to create orderItems"]);
        }
     }
   }




}