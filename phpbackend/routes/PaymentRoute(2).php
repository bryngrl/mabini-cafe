<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Always send CORS headers first
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    // Database
    require_once "../config/Database.php";
    $database = new Database();
    $db = $database->getConnection();

    // Controller
    require_once "../controllers/PaymentController.php";
    $controller = new PaymentController($db);

    // Route requests
    $method = $_SERVER['REQUEST_METHOD'];
    switch ($method) {
        case 'POST':
            $controller->checkout();
            break;

        default:
            http_response_code(405);
            echo json_encode(["error" => "Method not allowed"]);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
