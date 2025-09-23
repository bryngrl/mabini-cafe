<?php
require_once(__DIR__ . '/../Auth/Auth.php');
require_once(__DIR__ . '/../auth/jwtMiddleware.php');
require_once(__DIR__ . '/../Models/Cart.php');

class CartController {
    private $model;

    public function __construct($db) {
        $this->model = new Cart($db);
        header('Content-Type: application/json'); // lahat ng response JSON
    }

 /**
 * @OA\Get(
 *     path="/mabini-cafe/phpbackend/routes/cart",
 *     tags={"Carts"},
 *     summary="Get all carts in db",
 *     @OA\Response(
 *         response=200,
 *         description="List of all cart"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal Server Error"
 *     )
 * )
 */

    // Return all carts
    public function index() {
        $carts = $this->model->getAll();
        echo json_encode($carts);
    }

/**
 * @OA\Post(
 *     path="/mabini-cafe/phpbackend/routes/cart",
 *     tags={"Carts"},
 *     summary="Add item(s) to the cart",
 *     description="Saves a new cart entry for a user with the provided item, quantity, and subtotal.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"user_id","menu_item_id","quantity","subtotal"},
 *             @OA\Property(property="user_id", type="integer", example=1, description="ID of the user"),
 *             @OA\Property(property="menu_item_id", type="integer", example=5, description="ID of the menu item"),
 *             @OA\Property(property="quantity", type="integer", example=2, description="Number of items"),
 *             @OA\Property(property="subtotal", type="number", format="float", example=250.75, description="Total price for this item")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Cart created successfully"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Failed to create cart or missing required fields"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal Server Error"
 *     )
 * )
 */
public function store()
{

    $data = json_decode(file_get_contents("php://input"), true);

    if (!empty($data['user_id']) && !empty($data['menu_item_id']) && 
        !empty($data['quantity']) && !empty($data['subtotal'])) {
        
        $this->model->user_id      = $data['user_id'];
        $this->model->menu_item_id = $data['menu_item_id'];
        $this->model->quantity     = $data['quantity'];
        $this->model->subtotal     = $data['subtotal'];

        if ($this->model->create()) { 
            http_response_code(201);
            echo json_encode(["message" => "Cart created successfully"]);
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Failed to create cart"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["error" => "Missing required fields"]);
    }
}
 /**
 * @OA\Get(
 *     path="/mabini-cafe/phpbackend/routes/cart/{id}",
 *     tags={"Carts"},
 *     summary="Get all carts in by id",
 *    @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of cart",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="cart based on id"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal Server Error"
 *     )
 * )
 */
    // Show cart by cart ID
    public function show($id) {
        $cart = $this->model->getById($id);

        if($cart) {
            echo json_encode($cart);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Cart not found"]);
        }
    }



 /**
 * @OA\Get(
 *     path="/mabini-cafe/phpbackend/routes/cart?customer_id={customer_id}",
 *     tags={"Carts"},
 *     summary="Get all carts in  by customer id",
 *    @OA\Parameter(
 *         name="id",
 *         in="query",
 *         required=true,
 *         description="customer id of cart",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="cart based on customer id"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal Server Error"
 *     )
 * )
 */
    // Show carts by customer ID
    public function showCartByCustomerId($customer_id) {
        $carts = $this->model->getByCustomerId($customer_id);

        if($carts) {
            echo json_encode($carts);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "No carts found for this customer"]);
        }
    }

    // Update cart
    public function update($id) {
        $data = json_decode(file_get_contents("php://input"), true);

        if(isset($data['quantity']) && isset($data['subtotal'])) {
            $this->model->id = $id;
            $this->model->quantity = $data['quantity'];
            $this->model->subtotal = $data['subtotal'];

            if($this->model->update()) {
                http_response_code(200);
                echo json_encode(["message" => "Cart updated successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Failed to update cart"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Quantity and subtotal required"]);
        }
    }

    // Delete cart
    public function destroy($id) {
        $this->model->id = $id;

        if($this->model->delete()) {
            http_response_code(200);
            echo json_encode(["message" => "Cart deleted successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Failed to delete cart"]);
        }
    }
}
