<?php
require_once "../config/database.php";

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

$db = (new Database())->getConnection();

// Kunin ang URI
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode("/", trim($uri, "/")); 
$id = is_numeric(end($uri)) ? array_pop($uri) : null;
$resource = $uri[3] ?? null;
$subresource = $uri[4] ?? null;


switch($resource){
    case 'users':
        require_once "../controllers/UserController.php";
        $controller = new UserController($db);
        if($subresource === 'login') {
            $controller->login();
            exit;
        }
        break;
    case 'admins':
        require_once "../controllers/AdminController.php";
        $controller = new AdminController($db);
        if($subresource === 'login') {
            $controller->login();
            exit;
        }
        break;
    default:
        http_response_code(404);
        echo json_encode(["error"=>"Endpoint not found"]);
        exit;
}

// Dispatch sa tamang HTTP method
$method = $_SERVER['REQUEST_METHOD'];

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
