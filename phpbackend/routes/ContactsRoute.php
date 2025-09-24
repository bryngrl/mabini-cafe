<?php
require_once "../controllers/ContactsController.php";

$controller = new ContactsController($db);

switch ($method) {
    case 'GET':
       $controller->index();
        break;
    case 'POST':
       $controller->store();
        break;
    
    default:
         http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
        break;
}