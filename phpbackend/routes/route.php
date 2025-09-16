<?php
require_once "../config/database.php";

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

$db = (new Database())->getConnection();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode("/", trim($uri, "/")); 
$id = is_numeric(end($uri)) ? array_pop($uri) : null;
$resource = $uri[3] ?? null;
$subresource = $uri[4] ?? null;

$method = $_SERVER['REQUEST_METHOD'];

switch($resource){
    case 'users':
         include "UserRoute.php";
        break;
    case 'admins':
         require_once "AdminRoute.php";
        break;
    case 'menu':
         require_once "MenuRoute.php";
         break;
    case 'cart':
         require_once "CartRoute.php";
         break;
    case 'orders':
         require_once "OrderRoute.php";
    default:
        http_response_code(404);
        echo json_encode(["error"=>"Endpoint not found"]);
        exit;
}

