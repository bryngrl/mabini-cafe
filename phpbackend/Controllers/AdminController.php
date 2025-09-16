<?php
require_once(__DIR__ . '/../models/Admin.php');
require_once(__DIR__ . '/../Auth/Auth.php');
require_once __DIR__ . '/../auth/jwtMiddleware.php';

class AdminController{

private $model;
private $auth;

 public function __construct($db) {
      $this->model = new Admin($db);
        $this->auth = new Auth();
        header('Content-Type: application/json');
}



 // GET all Admins
    public function index() {
        $decoded = validateJWT();
         if(!isAdminAuthorized($decoded))
          {
            http_response_code(403);
            echo json_encode(["error" => "Forbidden access"]);
           return;
          }


          echo json_encode($this->model->getAll());

    }



    //get single admin
    public function show($id)
    {
        $decoded = validateJWT();

       if(!isAdminAuthorized($decoded))
          {
            http_response_code(403);
            echo json_encode(["error" => "Forbidden access"]);
           return;
          }
    $admin = $this->model->getById($id);

    if($admin){
     echo json_encode($admin);
    }else{
       http_response_code(404);
        echo json_encode(["error"=>"Admin not found"]);
    }
   } 

   //post create Admin
   public function store(){
       $data = json_decode(file_get_contents("php://input"), true);
       if(!empty($data['username']) && !empty($data['email']) && !empty($data['password'])){
            $this->model->username = $data['username'];
            $this->model->email = $data['email'];
            $this->model->password = password_hash($data['password'],PASSWORD_DEFAULT);
            $this->model->role ='admin';
            if($this->model->create()){
                http_response_code(201);
                echo json_encode(["message"=>"User created successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["error"=>"Failed to create user"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error"=>"Invalid input"]);
        }
   }




       // PUT update Admin
    public function update($id) {
        $decoded = validateJWT();
        $data = json_decode(file_get_contents("php://input"), true);
        if(!empty($data['username']) && !empty($data['email'])){
            $this->model->id = $id;
            $this->model->username = $data['username'];
            $this->model->email = $data['email'];
            
            if($this->model->update()){
                echo json_encode(["message"=>"User updated successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["error"=>"Failed to update user"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error"=>"Invalid input"]);
        }
    }


       // DELETE Admin
    public function destroy($id) {
        
        $decoded = validateJWT();
         if(!isAdminAuthorized($decoded))
          {
            http_response_code(403);
            echo json_encode(["error" => "Forbidden access"]);
           return;
          }
        $this->model->id = $id;
        if($this->model->delete()){
            echo json_encode(["message"=>"User deleted successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["error"=>"Failed to delete user"]);
        }
    }



      // POST login
    public function login() {
        $data = json_decode(file_get_contents("php://input"), true);
        if(!empty($data['email']) && !empty($data['password'])){
            $user = $this->model->findByEmail($data['email']);
            if($user && password_verify($data['password'], $user['password'])){
                $token = $this->auth->generateToken($user);
               // 🔑 Set HttpOnly cookie
            setcookie(
                "token",
                $token,
                [
                    "expires" => time() + 3600,
                    "path" => "/",
                    "domain" => "localhost", // palitan kung deployed
                    "secure" => false,       // true kung HTTPS
                    "httponly" => true,      // 🔑 HttpOnly
                    "samesite" => "Strict"
                ]
            );

            echo json_encode(["message" => "Login successful"]);
            } else {
                http_response_code(401);
                echo json_encode(["error"=>"Invalid credentials"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error"=>"Username and password required"]);
        }
    }






}