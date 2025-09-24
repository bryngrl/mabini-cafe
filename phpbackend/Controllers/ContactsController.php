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
        
    }
}