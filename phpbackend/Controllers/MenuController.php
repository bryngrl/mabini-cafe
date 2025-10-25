<?php
require_once __DIR__ . '/../Auth/Auth.php';
require_once __DIR__ . '/../Auth/jwtMiddleware.php';
require_once __DIR__ . '/../Models/Menu.php';
require_once 'Controller.php';

class MenuController
{
    private $model;
    public function __construct($db)
    {
        $this->model = new Menu($db); 
        header('Content-Type: application/json');
    }

  /**
 * @OA\Get(
 *     path="/mabini-cafe/phpbackend/routes/menu",
 *     tags={"Menu/Products"},
 *     summary="Get all menu items",
 *     @OA\Response(
 *         response=200,
 *         description="List of all menu items",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="name", type="string"),
 *                 @OA\Property(property="description", type="string"),
 *                 @OA\Property(property="price", type="number", format="float"),
 *                 @OA\Property(property="category_name", type="string"),
 *                 @OA\Property(property="image_path", type="string"),
 *                 @OA\Property(property="isAvailable", type="boolean")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal Server Error"
 *     )
 * )
 */
    public function index()
    {
        echo json_encode($this->model->getAll());
    }

  /**
 * @OA\Get(
 *     path="/mabini-cafe/phpbackend/routes/menu/{id}",
 *     tags={"Menu/Products"},
 *     summary="Get a single menu item by ID",
 *     description="Returns the menu item corresponding to the provided ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID ng menu item",
 *         @OA\Schema(type="integer", example=3)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Menu item details",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer"),
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="description", type="string"),
 *             @OA\Property(property="price", type="number", format="float"),
 *             @OA\Property(property="image_path", type="string"),
 *             @OA\Property(property="isAvailable", type="boolean"),
 *             @OA\Property(property="category_name", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Menu item not found"
 *     )
 * )
 */
    public function show($id)
    {
        $menu = $this->model->getById($id);
        if ($menu) {
            echo json_encode($menu);
        } else {
            echo json_encode(["Nothing" => "No menu found"]);
        }
    }

  /**
 * @OA\Post(
 *     path="/mabini-cafe/phpbackend/routes/menu",
 *     tags={"Menu/Products"},
 *     summary="Create a new menu item",
 *     description="Creates a menu item. Requires `name` and `price`. Supports image upload and availability status.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"name","price"},
 *                 @OA\Property(property="name", type="string", example="Cappuccino"),
 *                 @OA\Property(property="description", type="string", example="Rich espresso with milk foam"),
 *                 @OA\Property(property="price", type="number", format="float", example=120.50),
 *                 @OA\Property(property="category_id", type="integer", example=2),
 *                 @OA\Property(property="isAvailable", type="boolean", example=true, description="Whether the item is available or not"),
 *                 @OA\Property(property="image", type="string", format="binary")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Menu created successfully"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation error: Name and price are required"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed to create menu item"
 *     )
 * )
 */
    public function store()
    {
       $this->model->name = $_POST['name'] ?? null;
       $this->model->description = $_POST['description'] ?? null;
       $this->model->price = $_POST['price'] ?? null;
       $this->model->category_id = $_POST['category_id'] ?? null;
       $this->model->isAvailable = isset($_POST['isAvailable']) ? filter_var($_POST['isAvailable'], FILTER_VALIDATE_BOOLEAN) : true;

       if(empty($this->model->name) || empty($this->model->price)){
          http_response_code(400);
          echo json_encode(["error" => "Name and price are required"]);
          return;
       }

       if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $image = $_FILES['image'];
            $imageName = time() . "_" . $image['name'];
            $targetDir = "../uploads/menu/";
            if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
            $targetFile = $targetDir . $imageName;

            if (move_uploaded_file($image['tmp_name'], $targetFile)) {
                $this->model->image_path = "uploads/menu/" . $imageName;
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Failed to upload image"]);
                return;
            }
        } else {
            $this->model->image_path = null;
        }

        if ($this->model->create()) {
            http_response_code(201);
            echo json_encode(["message" => "Menu created successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Failed to create menu"]);
        }
    }

  /**
 * @OA\Put(
 *     path="/mabini-cafe/phpbackend/routes/menu/{id}",
 *     tags={"Menu/Products"},
 *     summary="Update an existing menu item",
 *     description="Updates a menu item by ID. Supports updating availability.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer", example=5)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"name","price"},
 *                 @OA\Property(property="name", type="string", example="Iced Latte"),
 *                 @OA\Property(property="description", type="string", example="Chilled espresso with milk and ice"),
 *                 @OA\Property(property="price", type="number", format="float", example=135.00),
 *                 @OA\Property(property="category_id", type="integer", example=3),
 *                 @OA\Property(property="isAvailable", type="boolean", example=false, description="Mark if the menu item is available"),
 *                 @OA\Property(property="image", type="string", format="binary")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Menu updated successfully"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation error: Name and price are required"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed to update menu item"
 *     )
 * )
 */
    public function update($id)
    {
        $this->model->id = $id;
        $this->model->name = $_POST['name'] ?? null;
        $this->model->description = $_POST['description'] ?? null;
        $this->model->price = $_POST['price'] ?? null;
        $this->model->category_id = $_POST['category_id'] ?? null;
        $this->model->isAvailable = isset($_POST['isAvailable']) ? filter_var($_POST['isAvailable'], FILTER_VALIDATE_BOOLEAN) : true;

        if (empty($this->model->name) || empty($this->model->price)) {
            http_response_code(400);
            echo json_encode(["error" => "Name and price are required"]);
            return;
        }

        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $image = $_FILES['image'];
            $imageName = time() . "_" . $image['name'];
            $targetDir = "../uploads/menu/";
            if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
            $targetFile = $targetDir . $imageName;

            if (move_uploaded_file($image['tmp_name'], $targetFile)) {
                $this->model->image_path = "uploads/menu/" . $imageName;
            }
        }

        if ($this->model->update()) {
            http_response_code(200);
            echo json_encode(["message" => "Menu updated successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Failed to update menu"]);
        }
    }

  /**
 * @OA\Delete(
 *     path="/mabini-cafe/phpbackend/routes/menu/{id}",
 *     tags={"Menu/Products"},
 *     summary="Delete a menu item by ID",
 *     description="Deletes the menu item corresponding to the provided ID.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer", example=7)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Menu deleted successfully"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Menu item not found"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed to delete menu"
 *     )
 * )
 */
    public function destroy($id)
    {
        $this->model->id = $id;
        if ($this->model->delete()) {
            echo json_encode(["message" => "Menu deleted successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Failed to delete menu"]);
        }
    }
}
