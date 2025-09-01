<?php
require_once(__DIR__ . '/../models/User.php');
require_once(__DIR__ . '/../Auth/Auth.php');
require_once __DIR__ . '/../auth/jwtMiddleware.php';

class UserController {
    private $model;
    private $auth;

    public function __construct($db) {
        $this->model = new User($db);
        $this->auth = new Auth();
    }

    // GET all users
    public function index() {
        $decoded = validateJWT();
        echo json_encode($this->model->getAll());
    }

    // GET single user
    public function show($id) {
        $decoded = validateJWT();
        $user = $this->model->getById($id);
        if($user){
            echo json_encode($user);
        } else {
            http_response_code(404);
            echo json_encode(["error"=>"User not found"]);
        }
    }

   


    // POST create user
    public function store() {
        $data = json_decode(file_get_contents("php://input"), true);
        if(!empty($data['username']) && !empty($data['email']) && !empty($data['password'])){
            $this->model->username = $data['username'];
            $this->model->email = $data['email'];
            $this->model->password = password_hash($data['password'], PASSWORD_DEFAULT);

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



    // PUT update user
    public function update($id) {
          $decoded = validateJWT();
        $data = json_decode(file_get_contents("php://input"), true);
        if(!empty($data['username']) && !empty($data['email'])){
            $this->model->id = $id;
            $this->model->username = $data['username'];
            $this->model->email = $data['email'];
            $this->model->address = $data['address'];
            $this->model->contact_number = $data['contact_number'];
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

 


    // DELETE user
    public function destroy($id) {
          $decoded = validateJWT();
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
                echo json_encode(["token"=>$token]);
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
