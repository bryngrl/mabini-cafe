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
