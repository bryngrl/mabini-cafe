<?php
require_once (__DIR__.'/../Controllers/OrderItemsController.php');
$controller = new OrderItemsController($db);

switch($method){
case 'GET':
    $order_id =$_GET['order_id']??null;

    if($order_id){
        $controller->showByOrderId($order_id);
    }else{
          $id ? $controller->show($id) : $controller->index();
    }
break;
case 'POST':
       $controller->store();
break;
case 'PUT': 
break;
case 'DELETE':
break;
default:
break;

}