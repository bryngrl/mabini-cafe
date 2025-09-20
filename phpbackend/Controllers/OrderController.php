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

    public function index() {
        echo json_encode($this->model->getAll());
    }

    public function show($id) {
        $order = $this->model->getById($id);
        echo json_encode($order ?: ["Nothing" => "no Order found"]);
    }

    public function showByCustomerId($customerId) {
        $order = $this->model->getByCustomerId($customerId);
        echo json_encode($order ?: ["Nothing" => "no Customer order found"]);
    }

    public function store() {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!empty($data['user_id']) && !empty($data['total_amount'])) {
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

    public function updateToPreparing($id) {
        $order = $this->model->setPreparingOrder($id);
        echo json_encode($order ? ["message" => "Order updated to preparing"]
                                : ["error" => "Failed to update order"]);
    }

    public function updateToCompleted($id) {
        $order = $this->model->setCompletedOrder($id);
        echo json_encode($order ? ["message" => "Order updated to completed"]
                                : ["error" => "Failed to update order"]);
    }

    public function updateToDelivering($id) {
        $order = $this->model->setDeliveringOrder($id);
        echo json_encode($order ? ["message" => "Order updated to delivering"]
                                : ["error" => "Failed to update order"]);
    }

    public function updateToCancelled($id) {
        $order = $this->model->setCancelingOrder($id);
        echo json_encode($order ? ["message" => "Order cancelled successfully"]
                                : ["error" => "Failed to update order"]);
    }

    public function updatePaymentStatus($id) {
        $order = $this->model->setPaidStatus($id);
        echo json_encode($order ? ["message" => "Payment status updated"]
                                : ["error" => "Failed to update order"]);
    }

    public function destroy($id) {
        $order = $this->model->delete($id);
        echo json_encode($order ? ["message" => "Order deleted successfully"]
                                : ["error" => "Failed to delete order"]);
    }


    public function showTotalOrders(){
        echo json_encode($this->model->getTotalOrders());
    }

    public function showTotalDelivered(){
        echo json_encode($this->model->getTotalDelivered());
    }

    public function showTotalCancelled(){
        echo json_encode($this->model->getTotalCancelled());
    }
}
