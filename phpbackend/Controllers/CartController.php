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

    // Return all carts
    public function index() {
        $carts = $this->model->getAll();
        echo json_encode($carts);
    }
     //post create Cart
    public function store()
    {
          $data = json_decode(file_get_conents("php://input"),true);
          $this->model->user_id = $data['user_id'];
          $this->model->menu_item_id = $data['menu_item_id'];
          $this->model->quantity = $data['quantity'];
          $this->model->subtotal = $data['subtotal'];
        

          if($this->model->update())
          {
             http_response_code(201);
            echo json_encode(["message"=>"Cart created successfully"]);

          }else
          {
              http_response_code(400);
            echo json_encode(["error" => "failed to create cart"]);
          }
    }

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
