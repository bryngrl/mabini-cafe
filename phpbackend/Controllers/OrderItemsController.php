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

     /**
 * @OA\Get(
 *     path="/mabini-cafe/phpbackend/routes/orderitems?orderId={orderId}",
 *     tags={"Order Items"},
 *     summary="Get all Orderitems by id",
 * @OA\Parameter(
 *         name="orderId",
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
  
   public function showByOrderId($orderId){
    $orderItem = $this->model->getByOrderId($orderId);

    if($orderItem)
    {
        echo json_encode($orderItem);
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