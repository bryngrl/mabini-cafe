<?php

require_once(__DIR__ . '/../Auth/Auth.php');
require_once __DIR__ . '/../auth/jwtMiddleware.php';
require_once __DIR__ . '/../Models/Menu.php';

class MenuController{

    private $model;

    public function __construct($db) {
        $this->model = new Menu($db);
    }

   public function show($id)
   {}


   
   //post create Menu
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
                echo json_encode(["message"=>"User created successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["error"=>"Failed to create user"]);
            }
       }
      
   }




}