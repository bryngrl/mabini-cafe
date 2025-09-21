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

    // Create a new menu item
    public function store()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!empty($data['name']) && !empty($data['price'])) {
            $this->model->name = $data['name'];
            $this->model->description = $data['description'] ?? null;
            $this->model->price = $data['price'];
            $this->model->category_id = $data['category_id'] ?? null;
            $this->model->image_path = $data['image_path'] ?? null;

            if ($this->model->create()) {
                http_response_code(201);
                echo json_encode(["message" => "Menu created successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Failed to create menu"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Name and price are required"]);
        }
    }

    // Update an existing menu item
    public function update($id)
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!empty($data['name']) && !empty($data['price'])) {
            $this->model->id = $id;
            $this->model->name = $data['name'];
            $this->model->description = $data['description'] ?? null;
            $this->model->price = $data['price'];
            $this->model->category_id = $data['category_id'] ?? null;
            $this->model->image_path = $data['image_path'] ?? null;

            if ($this->model->update()) {
                http_response_code(200);
                echo json_encode(["message" => "Menu updated successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Failed to update menu"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Name and price are required"]);
        }
    }

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
