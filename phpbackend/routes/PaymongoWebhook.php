<?php
// ✅ CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ✅ Database connection
require_once "../config/Database.php";
require_once __DIR__ . '/../Models/Payment.php';
require_once __DIR__ . '/../Models/Order.php';
$database = new Database();
$db = $database->getConnection();
$model = new Payment($db);
$orderModel = new Order($db);

try {
    // ✅ Read webhook payload
    $payload = file_get_contents("php://input");
    $data = json_decode($payload, true);

    // Log payload
    file_put_contents('webhook.log', date('Y-m-d H:i:s') . " " . $payload . "\n", FILE_APPEND);

    // ✅ Validate structure
    if (!isset($data['data']['attributes']['data']['id'])) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid payload structure"]);
        exit();
    }

    // ✅ Extract fields
    $eventType = $data['data']['attributes']['type'] ?? $data['data']['type'] ?? '';
    $order_id = $data['data']['attributes']['data']['attributes']['metadata']['order_id'] ?? null;
      $checkoutId = $data['data']['attributes']['data']['id'] ?? null;
if ($eventType === 'payment.paid' && $order_id) {
    if ($model->updateToPaidByOrderId($order_id)&&$orderModel->sePaidStatus($order_id)) {
    
        echo json_encode(["message" => "Payment marked as PAID for order_id {$order_id}"]);
    } else {
        echo json_encode(["warning" => "No payment record found for order_id {$order_id}"]);
    }
    
} else {
    echo json_encode(["info" => "Event not handled or missing identifiers"]);
}


} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
