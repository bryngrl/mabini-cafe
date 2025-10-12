<?php
require_once(__DIR__ . '/../Auth/Auth.php');
require_once(__DIR__ . '/../auth/jwtMiddleware.php');
require_once(__DIR__ . '/../Models/Order.php');

class OrderController {
    private $model;

    public function __construct($db) {
        $this->model = new Order($db);
        header('Content-Type: application/json');
    }
  /**
 * @OA\Get(
 *     path="/mabini-cafe/phpbackend/routes/orders",
 *     tags={"Orders"},
 *     summary="Get all Orders",
 *     @OA\Response(
 *         response=200,
 *         description="List of all Orders"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal Server Error"
 *     )
 * )
 */
    public function index() {
        echo json_encode($this->model->getAll() ?? []);
    }


        
 /**
 * @OA\Get(
 *     path="/mabini-cafe/phpbackend/routes/orders/{id}",
 *     tags={"Orders"},
 *     summary="Get a single order by their db id",
 *     description="Returns the orders corresponding to the provided ID in database",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of order",
 *         @OA\Schema(type="integer", example=3)
 *     ),
 *     
 *     @OA\Response(
 *         response=404,
 *         description="order not found"
 *     )
 * )
 */

    public function show($id) {
        $order = $this->model->getById($id);
        echo json_encode($order ?: ["Nothing" => "no Order found"]);
    }

     /**
 * @OA\Get(
 *     path="/mabini-cafe/phpbackend/routes/orders?customerId={customerId}",
 *     tags={"Orders"},
 *     summary="Get a single order by their customer_id",
 *     description="Returns the orders corresponding to the customer id",
 *     @OA\Parameter(
 *         name="customerId",
 *         in="query",
 *         required=true,
 *         description="ID of customer_id",
 *         @OA\Schema(type="integer", example=3)
 *     ),
 *     
 *     @OA\Response(
 *         response=404,
 *         description="Order not found"
 *     )
 * )
 */
    public function showByCustomerId($customerId) {
        $order = $this->model->getByCustomerId($customerId);
        echo json_encode($order ?: ["Nothing" => "no Customer order found"]);
    }

/**
 * @OA\Post(
 *     path="/mabini-cafe/phpbackend/routes/orders",
 *     tags={"Orders"},
 *     summary="Create a new order",
 *     description="Creates a new order with user_id and total_amount",
 *     @OA\RequestBody(
 *         required=true,
 *         description="Order data in JSON format",
 *         @OA\JsonContent(
 *             type="object",
 *             required={"user_id","total_amount"},
 *             @OA\Property(property="user_id", type="integer", example=1),
 *             @OA\Property(property="total_amount", type="number", format="float", example=250.75)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Order created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="order created successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="user_id and total_amount required",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="user_id and total_amount required")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed to create order",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Failed to create order")
 *         )
 *     )
 * )
 */

    public function store() {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!empty($data['user_id']) && !empty($data['total_amount'])) {
              $this->model->user_id = $data['user_id'];
              $this->model->total_amount = $data['total_amount'];
            if ($this->model->create($data)) {
                http_response_code(201);
                echo json_encode(["message" => "order created successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Failed to create order"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "user_id and total_amount required"]);
        }
    }

  /**
 * @OA\Put(
 *     path="/mabini-cafe/phpbackend/routes/orders/preparing/{id}",
 *     tags={"Orders"},
 *     summary="Set Order to Preparing",
 *        @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="unique id of order.",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *   
 *     @OA\Response(
 *         response=200,
 *         description="Order Being updated to preparing"
 *     )
 * )
 */
    public function updateToPreparing($id) {
        $order = $this->model->setPreparingOrder($id);
        echo json_encode($order ? ["message" => "Order updated to preparing"]
                                : ["error" => "Failed to update order"]);
    }

     /**
 * @OA\Put(
 *     path="/mabini-cafe/phpbackend/routes/orders/completed/{id}",
 *     tags={"Orders"},
 *     summary="Set Order to Completed",
 *        @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="unique id of order.",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *   
 *     @OA\Response(
 *         response=200,
 *         description="Order updated to completed"
 *     )
 * )
 */
    public function updateToCompleted($id) {
        $order = $this->model->setCompletedOrder($id);
        echo json_encode($order ? ["message" => "Order updated to completed"]
                                : ["error" => "Failed to update order"]);
    }

    
     /**
 * @OA\Put(
 *     path="/mabini-cafe/phpbackend/routes/orders/delivering/{id}",
 *     tags={"Orders"},
 *     summary="Set Order to delivering",
 *        @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="unique id of order.",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *   
 *     @OA\Response(
 *         response=200,
 *         description="Order updated to delivering"
 *     )
 * )
 */
    public function updateToDelivering($id) {
        $order = $this->model->setDeliveringOrder($id);
        echo json_encode($order ? ["message" => "Order updated to delivering"]
                                : ["error" => "Failed to update order"]);
    }

       /**
 * @OA\Put(
 *     path="/mabini-cafe/phpbackend/routes/orders/cancelled/{id}",
 *     tags={"Orders"},
 *     summary="Set Order to cancelled",
 *        @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="unique id of order.",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *   
 *     @OA\Response(
 *         response=200,
 *         description="Order updated to cancelled successfully"
 *     )
 * )
 */
    public function updateToCancelled($id) {
        $order = $this->model->setCancelingOrder($id);
        echo json_encode($order ? ["message" => "Order cancelled successfully"]
                                : ["error" => "Failed to update order"]);
    }

       /**
 * @OA\Put(
 *     path="/mabini-cafe/phpbackend/routes/orders/updatepayment/{id}",
 *     tags={"Orders"},
 *     summary="update payment status to paid ",
 *        @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="unique id of order.",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *   
 *     @OA\Response(
 *         response=200,
 *         description="Payment status updated"
 *     )
 * )
 */
    public function updatePaymentStatus($id) {
        $order = $this->model->setPaidStatus($id);
        echo json_encode($order ? ["message" => "Payment status updated"]
                                : ["error" => "Failed to update order"]);
    }


      /**
 * @OA\Get(
 *     path="/mabini-cafe/phpbackend/routes/orders/totalOrders",
 *     tags={"Orders"},
 *     summary="Get all the sum of Orders",
 *     @OA\Response(
 *         response=500,
 *         description="Internal Server Error"
 *     )
 * )
 */
    public function showTotalOrders(){
        $total = $this->model->getTotalOrders();
        echo json_encode(["total_orders"=>$total]);
    }

      /**
 * @OA\Get(
 *     path="/mabini-cafe/phpbackend/routes/orders/totalDelivered",
 *     tags={"Orders"},
 *     summary="Get all the sum of Delivered",
 *     @OA\Response(
 *         response=500,
 *         description="Internal Server Error"
 *     )
 * )
 */
    public function showTotalDelivered(){
        $total = $this->model->getTotalDelivered();
        echo json_encode(["total_delivered" => $total ]);
    }


      /**
 * @OA\Get(
 *     path="/mabini-cafe/phpbackend/routes/orders/totalCancelled",
 *     tags={"Orders"},
 *     summary="Get all the sum of cancelled order",
 *     @OA\Response(
 *         response=500,
 *         description="Internal Server Error"
 *     )
 * )
 */

    public function showTotalCancelled(){
       $total = $this->model->getTotalCancelled();
        echo json_encode(["total_cancelled" => $total ]);
    }


      /**
 * @OA\Put(
 *     path="/mabini-cafe/phpbackend/routes/orders/setToOnline",
 *     tags={"Orders"},
 *     summary="set payment to Online",
 *     @OA\Response(
 *         response=500,
 *         description="Internal Server Error"
 *     )
 * )
 */
    public function updateToOnlinePayment($id){
          $order = $this->model->setToOnline($id);
          echo json_encode($order ? ["message" => "Payment status updated to online"]
                                : ["error" => "Failed to update order"]);
    }




      /**
 * @OA\Put(
 *     path="/mabini-cafe/phpbackend/routes/orders/setToCash",
 *     tags={"Orders"},
 *     summary="set payment to Online",
 *     @OA\Response(
 *         response=500,
 *         description="Internal Server Error"
 *     )
 * )
 */
    public function updateToCashPayment($id){
        $order = $this->model->setToCash($id);
        echo json_encode($order ? ["message" => "Payment status updated to Cash"]
             :["error" => "Failed to update order"]);
    }
}
