<?php
require_once "../controllers/CartController.php";

$controller = new CartController($db);

switch ($method) {
    case 'GET':
       $customer_id = $_GET['customer_id'] ?? null;


         if($customer_id){
           
            $controller->showCartByCustomerId($customer_id);
        
         }else if(!$customer_id){
            $id ? $controller->show($id) : $controller->index();
              
         }
         else{
             http_response_code(404);
             echo json_encode(["error"=>"Endpoint not found"]);
         }

        $id ? $controller->show($id) : $controller->index();
        break;
    case 'POST':
        $controller->store();
        break;
    case 'PUT':
        $controller->update($id);
        break;
    case 'DELETE':
        if($id){
            $controller->destroy($id);
        } else {
            http_response_code(400);
            echo json_encode(["error"=>"ID is required for deletion"]);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(["error"=>"Method not allowed"]);
}
