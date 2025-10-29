<?php
require_once (__DIR__.'/../Controllers/CartController.php');
$controller = new CartController($db);

switch ($method) {
    case 'GET':
       $customer_id = $_GET['customer_id'] ?? null;
       
            if ($customer_id) {
                $controller->showCartByCustomerId($customer_id);
            } else {
                $id ? $controller->show($id) : $controller->index();
            }
        break;
    case 'POST':
        $controller->store();
        break;
    case 'PUT':
        $controller->update($id);
        break;
    case 'DELETE':
            $controller->destroy($id);
        break;
    default:
        http_response_code(405);
        echo json_encode(["error"=>"Method not allowed"]);
}
