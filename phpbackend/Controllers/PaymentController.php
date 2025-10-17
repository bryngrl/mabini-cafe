<?php
require_once __DIR__ . '/../Models/Payment.php';
require_once __DIR__ . '/../Services/PaymongoService.php';

class PaymentController {
    private $conn;
    private $model;
    private $paymongoservice;

    public function __construct($db) {
        $this->conn = $db;
        $this->model  = new Payment($db);
        $this->paymongoservice = new PaymongoService($db); // or pass secret key if needed
        header('Content-Type: application/json');
    }

    public function checkout() {
        try {
            $data = json_decode(file_get_contents("php://input"), true);
            if (!$data || !isset($data['order_id'])) {
                http_response_code(400);
                echo json_encode(["error" => "Missing order_id or amount"]);
                return;
            }

            $order_id = $data['order_id'];
            $payment_method = $data['payment_method'] ?? 'gcash';
            // Call PayMongo service
            $result = $this->paymongoservice->createCheckout($order_id, $payment_method);
            $amount = $result['total_amount'];
            if (isset($result['checkout_url'])) {
                $checkout_url = $result['checkout_url'];
            } else {
                http_response_code(500);
                echo json_encode(["error" => $result['error'] ?? 'Unknown error']);
                return;
            }

            // Store in DB
            $this->model->order_id = $order_id;
            $this->model->amount = $amount;
            $this->model->currency = 'PHP';
            $this->model->payment_method = $payment_method;
            $this->model->status = 'pending';
            $this->model->checkout_url = $checkout_url;
              $this->model->checkout_session_id = $result['checkout_session_id'];
 
            $this->model->create();
            echo json_encode([
                "message" => "Checkout created",
                "checkout_url" => $checkout_url
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => $e->getMessage()]);
        }
    }
}
