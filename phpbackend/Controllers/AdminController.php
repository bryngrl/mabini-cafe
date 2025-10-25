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

     /**
 * @OA\Get(
 *     path="/mabini-cafe/phpbackend/routes/admins",
 *     tags={"Admin"},
 *  security={{"bearerAuth":{}}},
 *     summary="Get all Admin accounts",
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

 // GET all Admins
    public function index() {
        // $decoded = validateJWT();
        //  if(!isAdminAuthorized($decoded))
        //   {
        //     http_response_code(403);
        //     echo json_encode(["error" => "Forbidden access"]);
        //    return;
        //   }


          echo json_encode($this->model->getAll() ?? []);

    }


     /**
 * @OA\Get(
 *     path="/mabini-cafe/phpbackend/routes/admins/{id}",
 *     tags={"Admin"},
 *  security={{"bearerAuth":{}}},
 *     summary="Get admin account by id",
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

    //get single admin
    public function show($id)
    {
    //     $decoded = validateJWT();

    //    if(!isAdminAuthorized($decoded))
    //       {
    //         http_response_code(403);
    //         echo json_encode(["error" => "Forbidden access"]);
    //        return;
    //       }
    $admin = $this->model->getById($id);

    if($admin){
     echo json_encode($admin ?? []);
    }else{
       http_response_code(404);
        echo json_encode(["error"=>"Admin not found"]);
    }
   } 


   /**
 * @OA\Post(
 *     path="/mabini-cafe/phpbackend/routes/admins",
 *     summary="Create a new admin user",
 *     description="This endpoint creates a new admin account.",
 *     tags={"Admin"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"username","email","password"},
 *             @OA\Property(property="username", type="string", example="adminUser"),
 *             @OA\Property(property="email", type="string", format="email", example="admin@example.com"),
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

   //post create Admin
   public function store(){
       $data = json_decode(file_get_contents("php://input"), true);
        $notUniqueEmail=$this->model->findbyEmail($data['email']);
            
             if($notUniqueEmail)
             {
             http_response_code(400);
            echo json_encode(["error"=>"Email already Exist"]);
            return;
             }
       if(!empty($data['username']) && !empty($data['email']) && !empty($data['password'])){
          

            $this->model->username = $data['username'];
            $this->model->email = $data['email'];
            $this->model->password = password_hash($data['password'],PASSWORD_DEFAULT);
            $this->model->role ='admin';
            if($this->model->create()){
                http_response_code(201);
                echo json_encode(["message"=>"Admin created successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["error"=>"Failed to create Admin"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error"=>"Invalid input"]);
        }
   }



   /**
 * @OA\Put(
 *    path="/mabini-cafe/phpbackend/routes/admins/{id}",
 *     summary="Update an existing user",
 *     description="Updates the username and email of a user. Requires a valid JWT.",
 *     operationId="updateUser",
 *     tags={"Admin"},
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
 *             @OA\Property(property="email", type="string", format="email", example="newemail@example.com")
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


       // PUT update Admin
    public function update($id) {
        // $decoded = validateJWT();
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
        
        // $decoded = validateJWT();
        //  if(!isAdminAuthorized($decoded))
        //   {
        //     http_response_code(403);
        //     echo json_encode(["error" => "Forbidden access"]);
        //    return;
        //   }
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
 *    path="/mabini-cafe/phpbackend/routes/admins/login",
 *     summary="Admin login",
 *     description="Authenticate user and set HttpOnly token cookie.",
 *     tags={"Admin"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email","password"},
 *             @OA\Property(property="email", type="string", format="email", example="admin@example.com"),
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

            echo json_encode([
                "message" => "Login successful",
                "token" => $token,
                "info" => $this->model->getById($user['id'])                   
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
 *      path="/mabini-cafe/phpbackend/routes/admins/logout",
 *     summary="Logout admin",
 *      security={{"bearerAuth":{}}},
 *     tags={"Admin"},
 *     @OA\Response(
 *         response=200,
 *         description="Logged out successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Logged out successfully")
 *         )
 *     )
 * )
 */

public function logout() {
    $decoded = validateJWT();
    setcookie("token", "", [
        "expires" => time() - 3600, // expire immediately
        "path" => "/",
        "domain" => "localhost",
        "httponly" => true,
        "samesite" => "Strict"
    ]);

    echo json_encode(["message" => "Logged out successfully"]);
}


}