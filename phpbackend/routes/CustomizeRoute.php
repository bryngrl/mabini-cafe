<?php
require_once "../Controllers/CustomizeWebsiteController.php"; //Nicapital ko lang yung letter C :D

$controller = new CustomizeWebsiteController($db);


switch ($method) {
    case 'GET':
       $id ? $controller->show($id) : $controller->index();
            
        break;
    case 'POST':
       $custom_id = $_GET["custom_id"] ?? $_POST["custom_id"] ?? null;
        if($custom_id)
        $controller->updateImage($custom_id);
        else
        $controller->store();
        break;
    case 'PUT':
        $controller->updateName($id);
        break;
    case 'DELETE':
            $controller->destroy($id);
        break;
    default:
        http_response_code(405);
        echo json_encode(["error"=>"Method not allowed"]);
}
