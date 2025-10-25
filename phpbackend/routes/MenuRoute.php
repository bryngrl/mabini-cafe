<?php
require_once "../controllers/MenuController.php";

$controller = new MenuController($db);

switch ($method) {
    case 'GET':
        $category_id = $_GET['category_id'] ?? null;
        $description = $_GET['description'] ?? null;

        if ($category_id) {
            $controller->showItemByCategory($category_id);
        } elseif ($description) {
            $controller->showItemByDescription($description);
        } else {
            $id ? $controller->show($id) : $controller->index();
        }
        break;

    case 'POST':

        $menu_id = $_GET['menu_id'] ?? null;
             $menu_id = $_GET['menu_id'] ?? $_POST['menu_id'] ?? null;
     if ($menu_id) 
        $controller->updateImage($menu_id);
         else
        $controller->store();
        break;

    case 'PUT':
     
        if ($id) {
            $controller->updateInfo($id);
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
