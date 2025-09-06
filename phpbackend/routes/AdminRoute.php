<?php
require_once "../controllers/AdminController.php";

$controller = new AdminController($db);
        if($subresource === 'login') {
            $controller->login();
            exit;
        }   

switch($method){
    case 'GET':
        $id ? $controller->show($id) : $controller->index();
        break;
    case 'POST':
        $controller->store();
        break;
    case 'PUT':
        if($id){
            $controller->update($id);
        } else {
            http_response_code(400);
            echo json_encode(["error"=>"ID is required for update"]);
        }
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
