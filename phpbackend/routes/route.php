    <?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    // Allow specific origin when using credentials
    $allowed_origins = [
        'http://localhost:5173',
        'http://127.0.0.1:5173',
        'https://mabini-cafe.bscs3a.com'
    ];

    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

    if (in_array($origin, $allowed_origins)) {
        header("Access-Control-Allow-Origin: $origin");
        header("Access-Control-Allow-Credentials: true");
    }

    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Content-Type: application/json");

    require_once (__DIR__.'/../config/database.php');
  require_once __DIR__ . '/../vendor/autoload.php';
    // Handle preflight OPTIONS request
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204); // Changed from 200 to 204 (No Content)
        exit();
    }

    $db = (new Database())->getConnection();

    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri = explode("/", trim($uri, "/")); 
    $id = is_numeric(end($uri)) ? array_pop($uri) : null;
    $resource = $uri[2] ?? null;
    $subresource = $uri[3] ?? null;

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
            require_once "OrderItemsRoute.php";
            break;
        case 'contacts':
            require_once "ContactsRoute.php";
            break;
        case 'shipinfo':
            require_once "ShipInfoRoute.php";
            break;
        case 'payments':
            require_once "PaymentRoute.php";
            break;
        case 'customize':
            require_once "CustomizeRoute.php";
            break;
        default:
            http_response_code(404);
            echo json_encode(["error"=>"Endpoint not found"]);
            exit;
    }