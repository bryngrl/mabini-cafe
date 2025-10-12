<?php
require_once(__DIR__ . '/../models/Contacts.php');
require_once(__DIR__ . '/../Auth/Auth.php');
require_once __DIR__ . '/../auth/jwtMiddleware.php';
require_once __DIR__ . '/../Services/Mailer/Mail.php';
require_once 'Controller.php';
class ContactsController extends Controller{
   private $model;
   private $mail;
   
    public function __construct($db) {
        $this->model = new Contacts($db);
        $this->mail = new Mail();
        header('Content-Type: application/json');
    }
 
     /**
 * @OA\Get(
 *     path="/mabini-cafe/phpbackend/routes/contacts",
 *     tags={"Contacts"},
 *     summary="Get all contacts",
 *     @OA\Response(
 *         response=200,
 *         description="List of all contacts"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal Server Error"
 *     )
 * )
 */
    public function index(){
        echo json_encode($this->model->getAll() ?? []);
    }

  /**
 * @OA\Post(
 *   path="/mabini-cafe/phpbackend/routes/contacts",
 *     summary="Store a new contact message",
 *     description="Upload a contact message along with optional image",
 *     tags={"Contacts"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"name","email","contact_reason","topic"},
 *                 @OA\Property(
 *                     property="user_id",
 *                     type="integer",
 *                     description="ID of the user sending the message",
 *                      example=1
 *                 ),
 *                 @OA\Property(
 *                     property="name",
 *                     type="string",
 *                     description="Name of the sender",
 *                     example="Kenneth Domdom"
 *                 ),
 *                 @OA\Property(
 *                     property="email",
 *                     type="string",
 *                     format="email",
 *                     description="Email of the sender",
 *                     example="domdomkenneth23@gmail.com"
 *                 ),
 *                 @OA\Property(
 *                     property="contact_reason",
 *                     type="string",
 *                     description="Reason for contacting",
 *                      enum={"Issue with my product","Shipping questions","Product questions","Collaboration","Other / general inquiry"}
 *                 ),
 *                 @OA\Property(
 *                     property="order_id",
 *                     type="integer",
 *                     description="Related order ID (optional)"
 *                 ),
 *                 @OA\Property(
 *                     property="topic",
 *                     type="string",
 *                     description="Topic of the message",
 *                     example="event invitation"
 *                 ),
 *                 @OA\Property(
 *                     property="message",
 *                     type="string",
 *                     description="The actual message content",
 *                     example="i want you to invite lerum ipsum etc etc"
 *                 ),
 *                 @OA\Property(
 *                     property="image",
 *                     type="string",
 *                     format="binary",
 *                     description="Optional image to upload"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Message sent successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="message sent successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad request - required fields missing",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Name and price are required")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error - failed to send message",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Failed to sent message")
 *         )
 *     )
 * )
 */
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
        $targetDir = "../uploads/contacts/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
        $targetFile = $targetDir . $imageName;

        if (move_uploaded_file($image['tmp_name'], $targetFile)) {
            $this->model->image_path = "uploads/contacts/" . $imageName;
        }
    }
     
      $isSendNotif=$this->mail->sendAutomate($_POST['email'],$_POST['name']);
    if ($this->model->create() && $isSendNotif ) {
       
        http_response_code(200);
        echo json_encode(["message" => "message sent successfully"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Failed to sent message"]);
    }
}

}