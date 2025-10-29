<?php
require_once (__DIR__.'/../Controllers/AdminController.php');

$controller = new AdminController($db);

if ($subresource === 'login') {
    $controller->login();
    exit;
}else if($subresource ==='logout'){
    $controller->logout();
    exit;
}   

switch ($method) {
    case 'GET':
        $id ? $controller->show($id) : $controller->index();
        break;
    case 'POST':
        $controller->store();
        break;
    case 'PUT':
        if ($id) {
            $controller->update($id);
        }
        break;
    case 'DELETE':
        if ($id) {
            $controller->destroy($id);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
}
