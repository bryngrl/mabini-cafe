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
       $controller->store();
        break;
     case 'PUT':
        switch ($subresource) {
         case 'preparing':
             $controller->updateToPreparing($id);
            break;
         case 'delivering':
             $controller->updateToDelivering($id);
             break;
         case 'completed':
            $controller->updateToCompleted($id);
            break;
         case 'cancelled':
            $controller->updateToCancelled($id);
            break;
         default:
             http_response_code(405);
             echo json_encode(["error" => "Method not allowed"]);
             break;
        }   

        break;
     case 'DELETE':
          $controller->delete($id);
        break;
    default:
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
        break;
}