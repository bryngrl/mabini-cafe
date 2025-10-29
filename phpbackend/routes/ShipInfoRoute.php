<?php
require_once (__DIR__.'/../Controllers/ShipInfoController.php');
$controller = new ShipInfoController($db);


switch ($method) {
    case 'GET':
        
        if(isset($_GET['user_id']))
        {
        $user_id = $_GET['user_id'];
        $controller->showByUserId($user_id);
        }else{
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
        echo json_encode(["error" => "Method not allowed"]);
        break;
}