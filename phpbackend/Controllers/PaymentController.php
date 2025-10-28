<?php
require_once __DIR__ . '/Controller.php'; 
require_once __DIR__ . '/../Models/Payment.php';
require_once __DIR__ . '/../Services/PaymongoService.php';

class PaymentController 
{
    private $conn;
    private $model;
    private $paymongoService;

    public function __construct($db)
    {
        $this->conn = $db;
        $this->model = new Payment($db);
        $this->paymongoService = new PaymongoService($db); // add secret key if needed
        header('Content-Type: application/json');
    }
    /**
     * @OA\Post(
     *     path="/mabini-cafe/phpbackend/routes/payments/checkout",
     *     tags={"Payments"},
     *     summary="Create a PayMongo checkout session",
     *     description="Initializes a checkout session for an order using PayMongo API. Returns the checkout URL for payment.",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Order and payment method details",
     *         @OA\JsonContent(
     *             type="object",
     *             required={"order_id", "payment_method"},
     *              @OA\Property(property="user_id", type="integer", example=15, description="The ID of the user to be paid."),
     *             @OA\Property(property="order_id", type="integer", example=15, description="The ID of the order to be paid."),
     *             @OA\Property(property="payment_method", type="string", example="gcash", description="Payment method to use (e.g., gcash, paymaya, card).")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Checkout session created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Checkout created successfully."),
     *             @OA\Property(property="checkout_url", type="string", example="https://paymongo.link/checkout/abcd1234")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Missing required fields",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Missing required fields: order_id or payment_method")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server or PayMongo API error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Invalid PayMongo response")
     *         )
     *     )
     * )
     */

    public function checkout()
    {
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            // 🧾 Basic input validation
            if (empty($data['order_id']) || empty($data['payment_method'])) {
                http_response_code(400);
                echo json_encode(["error" => "Missing required fields: order_id or payment_method"]);
                return;
            }

            $order_id = trim($data['order_id']);
            $payment_method = strtolower(trim($data['payment_method']));
            $user_id = isset($data['user_id']) ? trim($data['user_id']) : null;
            
            // ⚡ Call PayMongo service
            $result = $this->paymongoService->createCheckout($order_id, $payment_method);

            // ✅ Validate response
            if (empty($result) || !isset($result['checkout_url'], $result['total_amount'], $result['checkout_session_id'])) {
                http_response_code(500);
                echo json_encode(["error" => $result['error'] ?? "Invalid PayMongo response"]);
                return;
            }

            // 💾 Store payment record
            $this->model->user_id = $user_id;
            $this->model->order_id = $order_id;
            $this->model->amount = $result['total_amount'];
            $this->model->currency = 'PHP';
            $this->model->payment_method = $payment_method;
            $this->model->status = 'Pending';
            $this->model->checkout_url = $result['checkout_url'];
            $this->model->checkout_session_id = $result['checkout_session_id'];

            if ($this->model->create()) {
                echo json_encode([
                    "message" => "Checkout created successfully.",
                    "checkout_url" => $result['checkout_url']
                ]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Failed to save payment record"]);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => $e->getMessage()]);
        }
    }
}
