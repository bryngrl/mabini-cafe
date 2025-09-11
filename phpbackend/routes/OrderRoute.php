<?php
require_once "../controllers/OrderController.php";

$controller = new MenuController($db);

switch ($method) {
     case 'GET':
        $customer_id = $_GET['customerId'] ?? null;
        
        if($customer_id)
        {
             $controller->showByCustomerId($customer_id);
        }else{
            $id ? $controller->show($id) : $controller->index();
        }
    
        break;
     case 'POST':
    
        break;
     case 'PUT':
    
        break;
     case 'DELETE':
     
        break;
    default:
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
        break;
}