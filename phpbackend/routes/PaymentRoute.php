<?php
require_once __DIR__ . "/../Controllers/PaymentController.php";

$controller = new PaymentController($db);

switch ($method) {
    case 'POST':
        $controller->checkout();
            break;

        default:
            http_response_code(405);
            echo json_encode(["error" => "Method not allowed"]);
            break;
}