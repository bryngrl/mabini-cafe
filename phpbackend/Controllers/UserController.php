<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../Auth/Auth.php';
require_once __DIR__ . '/../auth/jwtMiddleware.php';
require_once __DIR__ . '/../auth/Otp.php';
require_once __DIR__ . '/../Services/Mailer/Mail.php';
class UserController {
    private $model;
    private $auth;
    private $otp;
    private $mail;
    public function __construct($db) {
        $this->model = new User($db);
        $this->auth = new Auth();
        $this->otp = new Otp();
        $this->mail = new Mail();
          header('Content-Type: application/json');
    }

       /**
 * @OA\Get(
 *     path="/mabini-cafe/phpbackend/routes/users",
 *     tags={"User"},
 *  security={{"bearerAuth":{}}},
 *     summary="Get all user accounts",
 *     @OA\Response(
 *         response=200,
 *         description="List of all admins"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal Server Error"
 *     )
 * )
 */

    // GET all users
    public function index() {
        // $decoded = validateJWT();

        //  if(!isAdminAuthorized($decoded))
        //   {http_response_code(403);
        //     echo json_encode(["error" => "Forbidden access"]);
        //    return;
        //   }

            
        echo json_encode($this->model->getAll());
    }

     /**
 * @OA\Get(
 *     path="/mabini-cafe/phpbackend/routes/users/{id}",
 *     tags={"User"},
 *  security={{"bearerAuth":{}}},
 *     summary="Get user account by id",
 *  @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="The ID of the Admin",
 *         @OA\Schema(type="integer", example=5)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="info of admin"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal Server Error"
 *     )
 * )
 */
    
    // GET single user
    public function show($id) {
    //     $decoded = validateJWT();


        
    //    if(!isAdminAuthorized($decoded))
    //       {
    //         http_response_code(403);
    //         echo json_encode(["error" => "Forbidden access"]);
    //        return;
    //       }
        $user = $this->model->getById($id);
        if($user){
            echo json_encode($user);
        } else {
            http_response_code(404);
            echo json_encode(["error"=>"User not found"]);
        }
    }

      /**
 * @OA\Post(
 *     path="/mabini-cafe/phpbackend/routes/users",
 *     summary="Create a new user",
 *     description="This endpoint creates a new user account.",
 *     tags={"User"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"username","email","password"},
 *             @OA\Property(property="username", type="string", example="user Domdom"),
 *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="strongPassword123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="User created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="User created successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid input",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Invalid input")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed to create user",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Failed to create user")
 *         )
 *     )
 * )
 */


public function store() {
    $data = json_decode(file_get_contents("php://input"), true);

    $username = $data['username'] ?? $data['name'] ?? null;
    $email = $data['email'] ?? null;
    $password = $data['password'] ?? null;

    if (!empty($username) && !empty($email) && !empty($password)) {
        $this->model->username = $username;
        $this->model->email = $email;
        $this->model->password = password_hash($password, PASSWORD_DEFAULT);

        try {
            if ($this->model->create()) {
                http_response_code(201);
                echo json_encode(["message" => "User created successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Failed to create user"]);
            }
        } catch (PDOException $e) {
            // Check if the error code corresponds to a duplicate entry (MySQL code 1062)
            if ($e->getCode() == 23000 && str_contains($e->getMessage(), 'Duplicate')) {
                http_response_code(409); // Conflict
                echo json_encode(["error" => "Email already exists"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Database error: " . $e->getMessage()]);
            }
        }

    } else {
        http_response_code(400);
        echo json_encode(["error" => "Invalid input"]);
    }
}



   /**
 * @OA\Put(
 *    path="/mabini-cafe/phpbackend/routes/users/{id}",
 *     summary="Update an existing user",
 *     description="Updates the username and email of a user. Requires a valid JWT.",
 *     operationId="updateUser",
 *     tags={"User"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID of the user to update",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="username", type="string", example="newusername"),
 *             @OA\Property(property="email", type="string", format="email", example="newemail@example.com"),
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="User updated successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid input",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Invalid input")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed to update user",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Failed to update user")
 *         )
 *     )
 * )
 */

    // PUT update user
    public function update($id) {
        //   $decoded = validateJWT();
        $data = json_decode(file_get_contents("php://input"), true);
        if(!empty($data['username']) && !empty($data['email'])){
            $this->model->id = $id;
            $this->model->username = $data['username'];
            $this->model->email = $data['email'];
            // $this->model->address = $data['address'];
            // $this->model->contact_number = $data['contact_number'];
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

 
/**
 * @OA\Delete(
 *     path="/mabini-cafe/phpbackend/routes/users/{id}",
 *     summary="Delete user by ID",
 *     description="Deletes a specific user using its unique ID.",
 *     tags={"User"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="The ID of the user to delete",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="user deleted successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="user deleted successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed to delete user",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="string", example="Failed to user info")
 *         )
 *     )
 * )
 */

    // DELETE user
    public function destroy($id) {
        //   $decoded = validateJWT();
        $this->model->id = $id;
        if($this->model->delete()){
            echo json_encode(["message"=>"User deleted successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["error"=>"Failed to delete user"]);
        }
    }

/**
 * @OA\Post(
 *    path="/mabini-cafe/phpbackend/routes/users/login",
 *     summary="User login",
 *     description="Authenticate user and set HttpOnly token cookie.",
 *     tags={"User"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email","password"},
 *             @OA\Property(property="email", type="string", format="email", example="userKenneth@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="strongPassword123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login successful",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Login successful")
 *         ),
 *         @OA\Header(
 *             header="Set-Cookie",
 *             description="HttpOnly cookie with JWT token",
 *             @OA\Schema(type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Username and password required",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Username and password required")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Invalid credentials",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Invalid credentials")
 *         )
 *     )
 * )
 */
    // POST login
    public function login() {
        $data = json_decode(file_get_contents("php://input"), true);
        if(!empty($data['email']) && !empty($data['password'])){
            $user = $this->model->findByEmail($data['email']);
            if($user && password_verify($data['password'], $user['password'])){
                $token = $this->auth->generateToken($user);
                 setcookie(
                "token",
                $token,
                [
                     "expires" => time() - 3600, // expire immediately
                    "path" => "/",
                    "domain" => "mabini-cafe.bscs3a.com", 
                    "secure" => true,                
                    "httponly" => true,
                    "samesite" => "Strict"
                ]
            );

            echo json_encode([
                "message" => "Login successful",
                "token" => $token,
                "info" =>$this->model->getById($user['id'])                
          ]);
            } else {
                http_response_code(401);
                echo json_encode(["error"=>"Invalid credentials"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error"=>"Username and password required"]);
        }
    }

/**
 * @OA\Post(
 *    path="/mabini-cafe/phpbackend/routes/users/forgotpasswordotp",
 *    summary="Sends OTP for forgot password",
 *    description="Sends OTP to email",
 *    tags={"User"},
 *    @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email"},
 *             @OA\Property(property="email", type="string", format="email", example="user@example.com")
 *         )
 *     ),
 *    @OA\Response(
 *        response=200,
 *        description="OTP sent successfully",
 *        @OA\JsonContent(
 *            @OA\Property(property="message", type="string", example="OTP sent successfully")
 *        )
 *    ),
 *    @OA\Response(
 *        response=400,
 *        description="Username and password required or invalid email",
 *        @OA\JsonContent(
 *            @OA\Property(property="error", type="string", example="Username and password required")
 *        )
 *    )
 * )
 */

   public function forgotpassOTP(){
     $data = json_decode(file_get_contents("php://input"), true);
     $email=$data['email'];
     $user = $this->model->findByEmail($email);


       if($user){
         $token = $this->otp->generate_otp($email); 
          $otp = $token["otp"];
           
          $sendOTP = $this->mail->sendOTP($email,$otp);
             if($sendOTP && $otp !=null){
             echo json_encode([
                "message" => "otp sent successfully",
                "token" =>$token["token"]
             ]);
             }else{
                 http_response_code(400);
                echo json_encode(["error"=>"failed to send otp"]);
             }
          }else{
             http_response_code(400);
             echo json_encode(["error"=>"Invalid email"]);
          }

         }

/**
 * @OA\Post(
 *    path="/mabini-cafe/phpbackend/routes/users/signupotp",
 *    summary="Sends OTP for signup",
 *    description="Sends OTP to email for signup",
 *    tags={"User"},
 *    @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email"},
 *             @OA\Property(property="email", type="string", format="email", example="user@example.com")
 *         )
 *     ),
 *    @OA\Response(
 *        response=200,
 *        description="OTP sent successfully",
 *        @OA\JsonContent(
 *            @OA\Property(property="message", type="string", example="OTP sent successfully")
 *        )
 *    ),
 *    @OA\Response(
 *        response=400,
 *        description="Username and password required or invalid email",
 *        @OA\JsonContent(
 *            @OA\Property(property="error", type="string", example="Username and password required")
 *        )
 *    )
 * )
 */

public function signupOTP(){
     $data = json_decode(file_get_contents("php://input"), true);
     $email=$data['email'];

       $token = $this->otp->generate_otp($email); 
          $otp = $token["otp"];
           
          $sendOTP = $this->mail->sendOTP($email,$otp);
             if($sendOTP && $otp !=null){
             echo json_encode([
                "message" => "otp sent successfully",
                "token" =>$token["token"]
             ]);
             }else{
                 http_response_code(400);
                echo json_encode(["error"=>"failed to send otp"]);
             }
}

/**
 * @OA\Post(
 *      path="/mabini-cafe/phpbackend/routes/users/verifyotp",
 *     summary="Verify OTP token",
 *     description="Verifies the OTP sent to the user's email or phone using JWT token",
 *     tags={"User"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
 *             @OA\Property(property="otp", type="string", example="123456")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="OTP verified successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="OTP verified"),
 *             @OA\Property(property="email", type="string", example="user@example.com")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="No token or OTP found",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="string", example="No token or OTP found!")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Invalid or expired OTP",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="string", example="OTP expired")
 *         )
 *     )
 * )
 */
public function verify_otp(){
    $data = json_decode(file_get_contents("php://input"), true);
    $token = $data['token'] ?? null;
    $otp = $data['otp'] ?? null;

    if(!empty($token) && !empty($otp)){
        // Trim lang ulit domskrt, di kasi nababasa sa front kapag endi e, parang nagiinvalid ganon 
        $otp = trim($otp);
        
        $verify_otp = $this->otp->verify_otp($token, $otp);

        if($verify_otp['ok'] === true){
            // OTP verified
            echo json_encode([
                "message" => $verify_otp['message'],
                "email" => $verify_otp['email']
            ]);
        } else {
            // OTP invalid or expired
            http_response_code(401);
            echo json_encode([
                "error" => $verify_otp['error']
            ]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["error"=>"No token or OTP found!"]);
    }
}
 

/**
 * @OA\Put(
 *     path="/mabini-cafe/phpbackend/routes/users/changepassword",
 *     summary="change password",
 *     description="change new password after validating OTP",
 *     tags={"User"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email","password"},
 *             @OA\Property(property="email", type="string", example="domdom@ucc.com"),
 *             @OA\Property(property="password", type="string", example="strong2")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="User change password successfully"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="invalid password"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Server error"
 *     )
 * )
 */

      public function changePasswordController()
      {
            $data = json_decode(file_get_contents("php://input"), true);
            $email = $data['email'] ?? null;
          $password = $data['password'] ?? null;

            if(!empty($email)){
                
                $this->model->email = $email;
                     $this->model->password = password_hash($password, PASSWORD_DEFAULT);
              if($this->model->changePassword())
                  echo json_encode(["message"=>"password changed successfully"]);
               else{
                http_response_code(500);
                 echo json_encode(["error"=>"Failed to change password"]); 
               }


            }else{
                 http_response_code(400);
                echo json_encode(["error"=>"Invalid email"]);
            }
       

      }
}
