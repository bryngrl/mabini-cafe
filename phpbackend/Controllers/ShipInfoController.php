<?php
require_once(__DIR__ . '/../Auth/Auth.php');
require_once(__DIR__ . '/../auth/jwtMiddleware.php');
require_once(__DIR__ . '/../Models/ShipInfo.php');

class ShipInfoController{

private $model;


    public function __construct($db) {
        $this->model = new ShipInfo($db);
        header('Content-Type: application/json'); // lahat ng response JSON
    }

    public function index() {
        
        $shipinfo = $this->model->getAll();
        echo json_encode($shipinfo);
    }


    public function show($id){
        $shipinfo = $this->model->getById($id);

        if($shipinfo){
            echo json_encode($shipinfo);
        }
        else{
              http_response_code(404);
            echo json_encode(["error" => "info not found"]);
        }
    }
  
     public function showByUserId($user_id){
         $shipinfo = $this->model->getByUserId($user_id);

         if($shipInfo){
            echo json_encode($shipInfo);
         }else{
              http_response_code(404);
            echo json_encode(["error" => "user_id not found"]);
         }
     }
  
     public function store(){
        $data = json_decode(file_get_contents("php://input"), true);
        $user_id = $data['user_id'] ?? null;
        $email = $data['email'];
        $first_name = $data['first_name'];
        $last_name = $data['last_name'];
        $address = $data['address'];
        $apparment_suite_etc = $data['appartment_suite_etc'] ?? ' ';
        $postal_code = $data['postal_code'];
        $city = $data['city'];
        $region = $data['region'];
        $phone = $data['phone'];

        if(!empty($email)&& !empty($first_name) && !empty($last_name)
            && !empty($address) && !empty($postal_codde) && !empty($city)
            && !empty($region) && !empty($phone)){
               $this->model->user_id = $user_id;
               $this->model->email = $email;
               $this->model->first_name = $first_name;
               $this->model->last_name = $last_name;
               $this->model->address = $address;
               $this->model->appartment_suite_etc = $apparment_suite_etc;
               $this->model->postal_code =  $postal_code;
               $this->model->city = $city;
               $this->model->region = $region;
               $this->model->phone = $phone;


        if ($this->model->create()) { 
            http_response_code(201);
            echo json_encode(["message" => "info created successfully"]);
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Failed to create info"]);
        }
        }else{
             http_response_code(400);
             echo json_encode(["error" => "Missing required fields"]);
        }

     }


    public function update($user_id){
             $data = json_decode(file_get_contents("php://input"), true);
             $email = $data['email'];
            $first_name = $data['first_name'];
            $last_name = $data['last_name'];
            $address = $data['address'];
            $apparment_suite_etc = $data['appartment_suite_etc'] ?? ' ';
            $postal_code = $data['postal_code'];
            $city = $data['city'];
            $region = $data['region'];
            $phone = $data['phone'];
     
            if(isset($email)&& isset($first_name) && isset($last_name)
            && isset($address) && isset($postal_codde) && isset($city)
            && isset($region) && isset($phone)){
               $this->model->user_id = $user_id;
               $this->model->email = $email;
               $this->model->first_name = $first_name;
               $this->model->last_name = $last_name;
               $this->model->address = $address;
               $this->model->appartment_suite_etc = $apparment_suite_etc;
               $this->model->postal_code =  $postal_code;
               $this->model->city = $city;
               $this->model->region = $region;
               $this->model->phone = $phone;
                
               if($this->model->update())
               {
                  http_response_code(200);
                echo json_encode(["message" => "info updated successfully"]);
               }else{
                 http_response_code(500);
                echo json_encode(["error" => "Failed to update info"]);
               }
            }else{
                       http_response_code(400);
                     echo json_encode(["error" => "Missing required fields"]);
            }

    }



    public function destroy($id){
    $this->model->id = $id;

        if($this->model->delete()) {
            http_response_code(200);
            echo json_encode(["message" => "info deleted successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Failed to delete info"]);
        }
    }
}

