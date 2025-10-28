<?php
require_once __DIR__ . '/../config/Database.php'; 
require_once __DIR__ . '/../Models/Order.php';

class PaymongoService {
    private $conn;
    private $model;
    private $secretKey = 'sk_test_VevdLzLHEydRC4EqsY4fiUPs'; // your PayMongo test key

    public function __construct($db) {
        $this->conn = $db;
        $this->model = new Order($db);
    }

    public function createCheckout($order_id, $payment_method = 'gcash') {
        // ===== 1️⃣ Get order details =====
        $orderQuery = $this->model->getById($order_id);
        
        if (!$orderQuery) {
            return ['error' => 'Order not found'];
        }

        $shipping_fee = $orderQuery['shipping_fee'];
        $order_total = $orderQuery['total_amount']; // don't add shipping here

        // ===== 2️⃣ Get order items =====
        $itemsQuery = "
            SELECT 
                mi.name AS item_name,
                oi.quantity,
                oi.price
            FROM order_items oi
            JOIN menu_items mi ON oi.menu_item_id = mi.id
            WHERE oi.order_id = :order_id
        ";
        $stmt = $this->conn->prepare($itemsQuery);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->execute();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$items) {
            return ['error' => 'No items found for this order'];
        }

        // ===== 3️⃣ Build line_items array dynamically =====
        $lineItems = [];
        foreach ($items as $item) {
            $lineItems[] = [
                "name" => $item['item_name'],
                "amount" => intval($item['price'] * 100), // convert to centavos
                "currency" => "PHP",
                "quantity" => intval($item['quantity'])
            ];
        }

        // Add shipping fee as a single line item
        if (!empty($shipping_fee) && $shipping_fee > 0) {
            $lineItems[] = [
                "name" => "Shipping Fee",
                "amount" => intval($shipping_fee * 100),
                "currency" => "PHP",
                "quantity" => 1
            ];
        }

        // ===== 4️⃣ Redirect URLs =====
        $successUrl = "http://localhost:5173/payment-success";
        $cancelUrl  = "http://localhost:5173/payment-cancelled";

        // ===== 5️⃣ Build payload =====
        $payload = [
            "data" => [
                "attributes" => [
                    "line_items" => $lineItems,
                    "payment_method_types" => [$payment_method],
                    "checkout_option" => "payment",
                    "success_url" => $successUrl,
                    "cancel_url" => $cancelUrl,
                    "metadata" => [
                        "order_id" => $order_id
                    ]
                ]
            ]
        ];

        // ===== 6️⃣ Send request to PayMongo =====
        $ch = curl_init("https://api.paymongo.com/v1/checkout_sessions");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "User-Agent: MabiniCafeApp/1.0"
        ]);
        curl_setopt($ch, CURLOPT_USERPWD, $this->secretKey . ":");

        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // ===== 7️⃣ Handle response =====
        if ($httpcode >= 200 && $httpcode < 300) {
            $json = json_decode($response, true);
            $checkoutUrl = $json['data']['attributes']['checkout_url'] ?? null;
            $checkoutId = $json['data']['id'] ?? null; // checkout session id

            return [
                'checkout_url' => $checkoutUrl,
                'checkout_session_id' => $checkoutId,
                'total_amount' => $order_total + $shipping_fee // now just add once
            ];
        } else {
            return ['error' => "PayMongo API error", 'response' => $response];
        }
    }
}
