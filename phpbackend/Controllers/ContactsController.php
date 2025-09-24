<?php
require_once(__DIR__ . '/../models/Contacts.php');
require_once(__DIR__ . '/../Auth/Auth.php');
require_once __DIR__ . '/../auth/jwtMiddleware.php';

class ContactsController{
   private $model;

    public function __construct($db) {
        $this->model = new Contacts($db);
        header('Content-Type: application/json');
    }
 

    public function index(){
        echo json_encode($this->model->getAll());
    }

    public function store(){
        $this->model->user_id = $_POST['user_id'];
        $this->model->name = $_POST['name'];
        $this->model->email = $_POST['email'];
        $this->model->contact_reason = $_POST['contact_reason'];
        $this->model->order_id = $_POST['order_id'];
        $this->model->topic = $_POST['topic'];
        $this->model->message = $_POST['message'];

        if(empty($this->model->email) || empty($this->model->name)||empty($this->model->contact_reason)||empty($this->model->topic)){
             http_response_code(400);
        echo json_encode(["error" => "Name and price are required"]);
        return;

        }
    // File upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $image = $_FILES['image'];
        $imageName = time() . "_" . $image['name'];
        $targetDir = "../uploads/menu/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
        $targetFile = $targetDir . $imageName;

        if (move_uploaded_file($image['tmp_name'], $targetFile)) {
            $this->model->image_path = "uploads/contacts/" . $imageName;
        }
    }
    
    if ($this->model->update()) {
        http_response_code(200);
        echo json_encode(["message" => "message sent successfully"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Failed to sent message"]);
    }
    }
}