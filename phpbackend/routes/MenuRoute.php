<?php
require_once "../controllers/MenuController.php";

$controller = new MenuController($db);
switch($method){
    case 'GET':
         $category_id = $_GET['category_id'] ?? null;
         $description  = $_GET['description'] ?? null;
         
         //check if hindi nagrerequest ng category or ng description si frontend
        if(!$category_id && !$description)
        {
            $id ? $controller->show($id) : $controller->index();
        }
        else if($category_id)
         {
            $controller->showItemByCategory($category_id);
         }
        else if($description)
         {
            $controller->showItemByDescription($description);
         }
         else
         {
             http_response_code(404);
             echo json_encode(["error"=>"Endpoint not found"]);
         }
        

       
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

