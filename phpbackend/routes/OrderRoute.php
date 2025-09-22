<?php
require_once "../controllers/OrderController.php";

$controller = new OrderController($db);

switch ($method) {
     case 'GET':
        $customer_id = $_GET['customerId'] ?? null;
     //    $totalOrders = $_GET['total_orders'] ?? null;
     //    $totalCancelled = $_GET['total_cancelled'] ?? null;
     //    $totalDelivered = $_GET['total_delivered'] ?? null;

       if($subresource!=null){
        
          switch($subresource){
                case 'totalOrders':
             $controller->showTotalOrders();
            break;
         case 'totalCancelled':
             $controller->showTotalCancelled();
             break;
         case 'totalDelivered':
           $controller->showTotalDelivered();
            break;
         default:
             http_response_code(405);
             echo json_encode(["error" => "Method not allowed"]);
             break;
          }

       } else {

        if($customer_id)
        {
             $controller->showByCustomerId($customer_id);
        }else{
            $id ? $controller->show($id) : $controller->index();
        }
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
         case 'updatepayment':
            $controller->updatePaymentStatus($id);
            break;
          case 'setToCash':
           break;
           case 'setTOOnline':
            break;
         default:
             http_response_code(405);
             echo json_encode(["error" => "Method not allowed"]);
             break;
        }   

        break;
    default:
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
        break;
}