<?php
// Siguraduhing tama ang casing ng folder names (Auth vs auth)
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
 *         description="List of all menu items"
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
 *         description="Menu item details"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Menu item not found"
 *     )
 * )
 */


    // Return specific menu item by ID
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
 * @OA\Get(
 *     path="/mabini-cafe/phpbackend/routes/menu?category_id={category_id}",
 *     tags={"Menu/Products"},
 *     summary="Get menu items by category ID",
 *     description="Filter menu items by passing a category_id as query parameter",
 *     @OA\Parameter(
 *         name="category_id",
 *         in="query",
 *         required=false,
 *         description="The category ID to filter menu items",
 *         @OA\Schema(
 *             type="integer",
 *             example=3
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of menu items by category"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Category not found"
 *     )
 * )
 */


    // Return menu items by category ID
    public function showItemByCategory($id)
    {
        $menu = $this->model->getByCategoryId($id);

        if ($menu) {
            echo json_encode($menu);
        } else {
            echo json_encode(["Nothing" => "No category found"]);
        }
    }

/**
 * @OA\Get(
 *     path="/mabini-cafe/phpbackend/routes/menu?description={description}",
 *     tags={"Menu/Products"},
 *     summary="Get menu items by subCategory",
 * 
 *     @OA\Parameter(
 *         name="description",
 *         in="query",
 *         required=false,
 *         description="The description to filter menu items",
 *         @OA\Schema(
 *             type="String",
 *             example="Example_Description4updated"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of menu items by category"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Category not found"
 *     )
 * )
 */

    // Return menu items by description
    public function showItemByDescription($description)
    {
        $menu = $this->model->getByDescription($description);

        if ($menu) {
            echo json_encode($menu);
        } else {
            echo json_encode(["Nothing" => "No description found"]);
        }
    }

/**
 * @OA\Post(
 *     path="/mabini-cafe/phpbackend/routes/menu",
 *     tags={"Menu/Products"},
 *     summary="Create a new menu item",
 *     description="Creates a menu item. Requires `name` and `price`. Supports image upload via multipart/form-data.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"name","price"},
 *                 @OA\Property(
 *                     property="name",
 *                     type="string",
 *                     example="Cappuccino",
 *                     description="Name of the menu item"
 *                 ),
 *                 @OA\Property(
 *                     property="description",
 *                     type="string",
 *                     example="Rich espresso with steamed milk foam",
 *                     description="Optional description of the menu item"
 *                 ),
 *                 @OA\Property(
 *                     property="price",
 *                     type="number",
 *                     format="float",
 *                     example=120.50,
 *                     description="Price of the menu item"
 *                 ),
 *                 @OA\Property(
 *                     property="category_id",
 *                     type="integer",
 *                     example=2,
 *                     description="Category ID of the menu item"
 *                 ),
 *                 @OA\Property(
 *                     property="image",
 *                     type="string",
 *                     format="binary",
 *                     description="Optional image file for the menu item"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Menu created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Menu created successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation error: Name and price are required",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Name and price are required")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed to create menu item",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Failed to create menu")
 *         )
 *     )
 * )
 */


    public function store()
    {
       $this->model->name=$_POST['name'] ?? null;
       $this->model->description = $_POST['description'] ?? null;
       $this->model->price = $_POST['price'] ?? null;
       $this->model->category_id = $_POST['category_id'] ?? null;

       if(empty($this->model->name) || empty($this->model->price)){
          http_response_code(400);
          echo json_encode(["error" => "Name and price are required"]);
         return;
       }
     if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $image = $_FILES['image'];
        $imageName = time() . "_" . $image['name'];
        $targetDir = "../uploads/menu/"; // siguraduhing may write permission
        if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
        $targetFile = $targetDir . $imageName;

        if (move_uploaded_file($image['tmp_name'], $targetFile)) {
            // Save relative path sa DB para magamit sa frontend
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

    // Update an existing menu item

 /**
 * @OA\Put(
 *     path="/mabini-cafe/phpbackend/routes/menu/{id}",
 *     tags={"Menu/Products"},
 *     summary="Update an existing menu item",
 *     description="Updates a menu item by ID. Requires `name` and `price`. Supports image upload via multipart/form-data.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="The ID of the menu item to update",
 *         @OA\Schema(type="integer", example=5)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"name","price"},
 *                 @OA\Property(
 *                     property="name",
 *                     type="string",
 *                     example="Iced Latte",
 *                     description="Name of the menu item"
 *                 ),
 *                 @OA\Property(
 *                     property="description",
 *                     type="string",
 *                     example="Chilled espresso with milk and ice",
 *                     description="Optional description of the menu item"
 *                 ),
 *                 @OA\Property(
 *                     property="price",
 *                     type="number",
 *                     format="float",
 *                     example=135.00,
 *                     description="Price of the menu item"
 *                 ),
 *                 @OA\Property(
 *                     property="category_id",
 *                     type="integer",
 *                     example=3,
 *                     description="Category ID of the menu item"
 *                 ),
 *                 @OA\Property(
 *                     property="image",
 *                     type="string",
 *                     format="binary",
 *                     description="Optional image file to replace the current menu item image"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Menu updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Menu updated successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation error: Name and price are required",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Name and price are required")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed to update menu item",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Failed to update menu")
 *         )
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

    if (empty($this->model->name) || empty($this->model->price)) {
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
 *         description="The ID of the menu item to delete",
 *         @OA\Schema(type="integer", example=7)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Menu deleted successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Menu deleted successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Menu item not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Menu item not found")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed to delete menu",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Failed to delete menu")
 *         )
 *     )
 * )
 */


    // Delete a menu item by ID
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
