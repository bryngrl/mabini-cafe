<?php

require_once(__DIR__ . '/../Auth/Auth.php');
require_once __DIR__ . '/../auth/jwtMiddleware.php';
require_once __DIR__ . '/../Models/Menu.php';

class MenuController{

    private $model;

    public function __construct($db) {
        $this->model = new Menu($db);
        header('Content-Type: application/json');
    }

    // return all menu in server
   public function index(){
     echo json_encode($this->model->getAll());
   }
  

   // return specific menu_item to server
   public function show($id){
    $menu = $this->model->getById($id);
    
    if($menu)
    {
        echo json_encode($menu);
    }else{
        echo json_encode(["Nothing"=>"no menu found"]);
    }
   }

   // return menu with specific category
   public function showItemByCategory($id){
    $menu = $this->model->getByCategoryId($id);
     
    if($menu)
    {
        echo json_encode($menu);
    }else{
        echo json_encode(["Nothing"=>"no Category found"]);
    }
    

   }

    // return specific item based on description
   public function showItemByDescription($description)
   {
     $menu = $this->model->getByDescription($description);

        if($menu)
    {
        echo json_encode($menu);
    }else{
        echo json_encode(["Nothing"=>"no Description found"]);
    }
    

   }


   
   //post create Menu from server
   public function store(){
       $data = json_decode(file_get_contents("php://input"), true);
      
     if(!empty($data['name']) && !empty(!empty($data['price'])))
       {

       $this->model->name = $data['name'];
       $this->model->description = $data['description'];
       $this->model->price = $data['price'];
       $this->model->category_id = $data['category_id'];
       $this->model->image_path = $data['image_path'];
       
        if($this->model->create()){
                http_response_code(201);
                echo json_encode(["message"=>"Menu created successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["error"=>"Failed to create Menu"]);
            }
       }else{
            http_response_code(400);
            echo json_encode(["error" => " name and price required"]);
       }
      
   }



   // update menu 
   public function update($id){
        $data = json_decode(file_get_contents("php://input"), true);

        if(!empty($data['name']) && !empty(!empty($data['price'])))
       {
        $this->model->id= $id;
       $this->model->name = $data['name'];
       $this->model->description = $data['description'];
       $this->model->price = $data['price'];
       $this->model->category_id = $data["category_id"];
       $this->model->image_path = $data['image_path'];
       
        if($this->model->update()){
                http_response_code(201);
                echo json_encode(["message"=>"Menu updated successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["error"=>"Failed to update Menu"]);
            }
       }
   }
   
   //delete Menu by id
   public function destroy($id)
   {
       $this->model->id = $id;
        if($this->model->delete()){
            echo json_encode(["message"=>"Menu deleted successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["error"=>"Failed to delete Menu"]);
        }
     
   }



}