<?php
require_once(__DIR__ . '/../Models/Contacts.php');
require_once(__DIR__ . '/../Auth/Auth.php');
require_once __DIR__ . '/../Auth/jwtMiddleware.php';
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
 *   path="/phpbackend/routes/contacts",
 *   summary="Store a new contact message",
 *   description="Upload a contact message along with optional image",
 *   tags={"Contacts"},
 *   @OA\RequestBody(
 *     required=true,
 *     @OA\MediaType(
 *       mediaType="multipart/form-data",
 *       @OA\Schema(
 *         required={"name","email","contact_reason","topic"},
 *         @OA\Property(property="user_id", type="integer", description="ID of the user sending the message", example=1),
 *         @OA\Property(property="name", type="string", description="Name of the sender", example="Kenneth Domdom"),
 *         @OA\Property(property="email", type="string", format="email", description="Email of the sender", example="domdomkenneth23@gmail.com"),
 *         @OA\Property(property="contact_reason", type="string", description="Reason for contacting", enum={"Issue with my product","Shipping questions","Product questions","Collaboration","Other/general inquiry"}),
 *         @OA\Property(property="order_id", type="integer", description="Related order ID (optional)"),
 *         @OA\Property(property="topic", type="string", description="Topic of the message", example="event invitation"),
 *         @OA\Property(property="message", type="string", description="The actual message content", example="I want to invite lorem ipsum etc."),
 *         @OA\Property(property="image", type="string", format="binary", description="Optional image to upload")
 *       )
 *     )
 *   ),
 *   @OA\Response(
 *     response=200,
 *     description="Message sent successfully",
 *     @OA\JsonContent(@OA\Property(property="message", type="string", example="message sent successfully"))
 *   ),
 *   @OA\Response(
 *     response=400,
 *     description="Bad request - required fields missing",
 *     @OA\JsonContent(@OA\Property(property="error", type="string", example="Name and price are required"))
 *   ),
 *   @OA\Response(
 *     response=500,
 *     description="Internal server error - failed to send message",
 *     @OA\JsonContent(@OA\Property(property="error", type="string", example="Failed to send message"))
 *   )
 * )
 */

public function store() {
    header('Content-Type: application/json');

    try {
        // 1️⃣ Assign POST values safely
        $this->model->user_id        = $_POST['user_id'] ?? null;
        $this->model->name           = trim($_POST['name'] ?? '');
        $this->model->email          = trim($_POST['email'] ?? '');
        $this->model->contact_reason = trim($_POST['contact_reason'] ?? '');
        $this->model->order_id       = $_POST['order_id'] ?? null;
        $this->model->topic          = trim($_POST['topic'] ?? '');
        $this->model->message        = trim($_POST['message'] ?? '');

        // 2️⃣ Validate required fields
        if (
            empty($this->model->name) ||
            empty($this->model->email) ||
            empty($this->model->contact_reason) ||
            empty($this->model->topic)
        ) {
            http_response_code(400);
            echo json_encode(["error" => "Name, email, contact reason, and topic are required"]);
            return;
        }

        // 3️⃣ Optional file upload
        $attachmentPath = '';
        if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image = $_FILES['image'];
            $imageName = time() . "_" . basename($image['name']);
            $targetDir = __DIR__ . "/../uploads/contacts/";

            if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true)) {
                throw new Exception("Failed to create uploads directory.");
            }

            $targetFile = $targetDir . $imageName;
            if (!move_uploaded_file($image['tmp_name'], $targetFile)) {
                throw new Exception("Failed to upload image.");
            }

            $this->model->image_path = "uploads/contacts/" . $imageName;
            $attachmentPath = $targetFile;
        }

        // 4️⃣ Save message to database
        if (!$this->model->create()) {
            throw new Exception("Failed to save message to database.");
        }

        // 5️⃣ Send email to business inbox
        $isSentToBusiness = $this->mail->sendContactEmail(
            $this->model->name,
            $this->model->email,
            $this->model->topic,
            $this->model->contact_reason,
            $this->model->order_id,
            $this->model->message,
            $attachmentPath
        );

        // 6️⃣ Send automated email to user (continue even if this one fails)
        $isSentToUser = $this->mail->sendAutomate($this->model->email, $this->model->name);

        // 7️⃣ Unified success message
        $response = [
            "message" => "Message saved successfully",
            "email_to_business" => $isSentToBusiness ? "sent" : "failed",
            "email_to_user" => $isSentToUser ? "sent" : "failed"
        ];

        http_response_code(200);
        echo json_encode($response);

    } catch (\Throwable $e) {
        // 8️⃣ Error handling
        http_response_code(500);
        echo json_encode([
            "error" => "Server error occurred",
            "details" => $e->getMessage()
        ]);

        error_log("ContactsController::store() error: " . $e->getMessage());
    }
}

}