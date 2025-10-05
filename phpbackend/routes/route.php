<?php


header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

require_once "../config/database.php";


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$db = (new Database())->getConnection();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
 $uri = explode("/", trim($uri, "/")); $id = is_numeric(end($uri)) ? array_pop($uri) : null;
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
         break;
    case 'orderitems':
          require_once "OrderitemsRoute.php";
          break;
    case 'contacts':
          require_once "contactsRoute.php";
         break;
    default:
        http_response_code(404);
        echo json_encode(["error"=>"Endpoint not found"]);
        exit;
}

