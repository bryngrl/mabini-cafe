<?php
require_once __DIR__ . '/../config/Database.php'; // adjust path as needed

class PaymongoService {
    private $conn;
    private $secretKey = 'sk_test_VevdLzLHEydRC4EqsY4fiUPs'; // your PayMongo test key

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createCheckout($order_id, $payment_method = 'gcash') {
        // ===== 1️⃣ Get order details =====
        $orderQuery = "SELECT total_amount FROM orders WHERE id = :order_id";
        $stmt = $this->conn->prepare($orderQuery);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->execute();
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            return ['error' => 'Order not found'];
        }

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

        // ===== 4️⃣ Redirect URLs =====
        $successUrl = "http://localhost/mabini-cafe/success.php";
        $cancelUrl  = "http://localhost/mabini-cafe/cancel.php";

        // ===== 5️⃣ Build payload =====
        $payload = [
            "data" => [
                "attributes" => [
                    "line_items" => $lineItems,
                    "payment_method_types" => [$payment_method],
                    "checkout_option" => "payment",
                    "success_url" => $successUrl,
                    "cancel_url" => $cancelUrl
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
            return ['checkout_url' => $checkoutUrl];
        } else {
            return ['error' => "PayMongo API error", 'response' => $response];
        }
    }
}
