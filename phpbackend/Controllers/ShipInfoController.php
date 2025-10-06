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


     /**
 * @OA\Get(
 *     path="/mabini-cafe/phpbackend/routes/shipinfo",
 *     tags={"Shipping Info"},
 *     summary="Get all Ship info",
 *     @OA\Response(
 *         response=200,
 *         description="List of all Shipping Info"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal Server Error"
 *     )
 * )
 */

    public function index() {
        
        $shipinfo = $this->model->getAll();
        echo json_encode($shipinfo);
    }

 /**
 * @OA\Get(
 *     path="/mabini-cafe/phpbackend/routes/shipinfo/{id}",
 *     tags={"Shipping Info"},
 *     summary="Get ship infos by id",
 * @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Ship info by primary key id",
 *         @OA\Schema(type="integer", example=3)
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal Server Error"
 *     )
 * )
 */
    public function show($id){
        $shipinfo = $this->model->getById($id);

        if($shipinfo){
            echo json_encode($shipinfo);
        }
        else{
              http_response_code(404);
            echo json_encode(["empty" => "info not found"]);
        }
    }
  
     /**
 * @OA\Get(
 *     path="/mabini-cafe/phpbackend/routes/shipinfo?user_id={user_id}",
 *     tags={"Shipping Info"},
 *     summary="Get all ship info by user id",
 * @OA\Parameter(
 *         name="user_id",
 *         in="query",
 *         required=true,
 *         description="Ship info by user_id",
 *         @OA\Schema(type="integer", example=3)
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal Server Error"
 *     )
 * )
 */
     public function showByUserId($user_id){
         $shipInfo = $this->model->getByUserId($user_id);
         if($shipInfo){
            echo json_encode($shipInfo);
         }else{
              http_response_code(404);
            echo json_encode(["empty" => "user_id not found"]);
         }
     }
          /**
     * @OA\Post(
     *     path="/mabini-cafe/phpbackend/routes/shipinfo",
     *     tags={"Shipping Info"},
     *     summary="Create a new Shipping Info record",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "first_name", "last_name", "address", "postal_code", "city", "region", "phone"},
     *             @OA\Property(property="user_id", type="integer", example=3),
     *             @OA\Property(property="email", type="string", example="kenneth@example.com"),
     *             @OA\Property(property="first_name", type="string", example="kenneth"),
     *             @OA\Property(property="last_name", type="string", example="Domdom"),
     *             @OA\Property(property="address", type="string", example="123 Main Street"),
     *             @OA\Property(property="apartment_suite_etc", type="string", example="Apt 4B"),
     *             @OA\Property(property="postal_code", type="string", example="12345"),
     *             @OA\Property(property="city", type="string", example="Manila"),
     *             @OA\Property(property="region", type="string", example="NCR"),
     *             @OA\Property(property="phone", type="string", example="09123456789")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Shipping info created successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Missing required fields or invalid input"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to create shipping info"
     *     )
     * )
     */
     public function store(){
        $data = json_decode(file_get_contents("php://input"), true);
        $user_id = $data['user_id'] ?? null;
        $email = $data['email'];
        $first_name = $data['first_name'];
        $last_name = $data['last_name'];
        $address = $data['address'];
        $apartment_suite_etc = $data['apartment_suite_etc'] ?? ' ';
        $postal_code = $data['postal_code'];
        $city = $data['city'];
        $region = $data['region'];
        $phone = $data['phone'];

        if(!empty($email)&& !empty($first_name) && !empty($last_name)
            && !empty($address) && !empty($postal_code) && !empty($city)
            && !empty($region) && !empty($phone)){
               $this->model->user_id = $user_id;
               $this->model->email = $email;
               $this->model->first_name = $first_name;
               $this->model->last_name = $last_name;
               $this->model->address = $address;
               $this->model->apartment_suite_etc = $apartment_suite_etc;
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


     /**
     * @OA\Put(
     *     path="/mabini-cafe/phpbackend/routes/shipinfo?user_id={user_id}",
     *     tags={"Shipping Info"},
     *     summary="update Shipping Info record",
     *   @OA\Parameter(
 *         name="user_id",
 *         in="query",
 *         required=true,
 *         description="Ship info by user id",
 *         @OA\Schema(type="integer", example=3)
 *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "first_name", "last_name", "address", "postal_code", "city", "region", "phone"},
     *             @OA\Property(property="user_id", type="integer", example=3),
     *             @OA\Property(property="email", type="string", example="kenneth@example.com"),
     *             @OA\Property(property="first_name", type="string", example="kenneth"),
     *             @OA\Property(property="last_name", type="string", example="Domdom"),
     *             @OA\Property(property="address", type="string", example="123 Main Street"),
     *             @OA\Property(property="apartment_suite_etc", type="string", example="Apt 4B"),
     *             @OA\Property(property="postal_code", type="string", example="12345"),
     *             @OA\Property(property="city", type="string", example="Manila"),
     *             @OA\Property(property="region", type="string", example="NCR"),
     *             @OA\Property(property="phone", type="string", example="09123456789")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Shipping info created successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Missing required fields or invalid input"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to create shipping info"
     *     )
     * )
     */

    public function update($user_id){
             $data = json_decode(file_get_contents("php://input"), true);
             $user_id = $data['user_id'];
             $email = $data['email'];
            $first_name = $data['first_name'];
            $last_name = $data['last_name'];
            $address = $data['address'];
            $apartment_suite_etc = $data['apartment_suite_etc'] ?? ' ';
            $postal_code = $data['postal_code'];
            $city = $data['city'];
            $region = $data['region'];
            $phone = $data['phone'];
    if(!empty($user_id) && !empty($email) && !empty($first_name) && !empty($last_name)
    && !empty($address) && !empty($postal_code) && !empty($city)
    && !empty($region) && !empty($phone)) {
               $this->model->user_id = $user_id;
               $this->model->email = $email;
               $this->model->first_name = $first_name;
               $this->model->last_name = $last_name;
               $this->model->address = $address;
               $this->model->apartment_suite_etc = $apartment_suite_etc;
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


/**
 * @OA\Delete(
 *     path="/mabini-cafe/phpbackend/routes/shipinfo/{id}",
 *     summary="Delete shipping info by ID",
 *     description="Deletes a specific shipping information record using its unique ID.",
 *     tags={"Shipping Info"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="The ID of the shipping info record to delete",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Info deleted successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="info deleted successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed to delete info",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="string", example="Failed to delete info")
 *         )
 *     )
 * )
 */
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

