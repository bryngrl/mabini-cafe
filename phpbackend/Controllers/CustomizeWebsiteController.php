<?php
require_once __DIR__ . '/../Auth/Auth.php';
require_once __DIR__ . '/../Auth/jwtMiddleware.php';
require_once __DIR__ . '/../Models/CustomizeWebsite.php';

class CustomizeWebsiteController {
    private $model;

    public function __construct($db)
    {
        $this->model = new CustomizeWebsite($db);
        header('Content-Type: application/json');
    }

    // READ ALL
    public function index() {
        echo json_encode($this->model->getAll());
    }

    // READ ONE
    public function show($id) {
        $custom = $this->model->getById($id);
        if ($custom) {
            echo json_encode($custom);
        } else {
            echo json_encode(["message" => "No record found"]);
        }
    }

    // CREATE
    public function store() {
        $this->model->image_custom_name = $_POST['image_custom_name'] ?? null;

        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $image = $_FILES['image'];
            $imageName = time() . "_" . $image['name'];
            $targetDir = "../uploads/customize/";
            if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
            $targetFile = $targetDir . $imageName;

            if (move_uploaded_file($image['tmp_name'], $targetFile)) {
                $this->model->image_path = "uploads/customize/" . $imageName;
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
            echo json_encode(["message" => "Custom image created successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Failed to create custom image"]);
        }
    }

    // UPDATE NAME ONLY
    public function updateName($id) {
        $data = json_decode(file_get_contents("php://input"), true);
        $this->model->id = $id;
        $this->model->image_custom_name = $data['image_custom_name'] ?? null;

        if ($this->model->updateName()) {
            echo json_encode(["message" => "Name updated successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Failed to update name"]);
        }
    }

    // UPDATE IMAGE ONLY
    public function updateImage($id) {
        $this->model->id = $id;

        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $image = $_FILES['image'];
            $imageName = time() . "_" . $image['name'];
            $targetDir = "../uploads/customize/";
            if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
            $targetFile = $targetDir . $imageName;

            if (move_uploaded_file($image['tmp_name'], $targetFile)) {
                $this->model->image_path = "uploads/customize/" . $imageName;
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Failed to upload new image"]);
                return;
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "No image uploaded"]);
            return;
        }

        if ($this->model->updateImage()) {
            echo json_encode(["message" => "Image updated successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Failed to update image"]);
        }
    }

    // DELETE
    public function delete($id) {
        $this->model->id = $id;
        if ($this->model->delete()) {
            echo json_encode(["message" => "Record deleted successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Failed to delete record"]);
        }
    }
}



/**
 * List all customization records
 *
 * @OA\Get(
 *   path="/mabini-cafe/phpbackend/routes/customize",
 *   tags={"CustomizeWebsite"},
 *   summary="Get all customization records",
 *   description="Returns list of customization records (hero images)",
 *   @OA\Response(
 *     response=200,
 *     description="Successful operation",
 *     @OA\JsonContent(
 *       type="array",
 *       @OA\Items(
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="image_custom_name", type="string"),
 *         @OA\Property(property="image_path", type="string"),
 *         @OA\Property(property="created_at", type="string", format="date-time")
 *       )
 *     )
 *   ),
 *   @OA\Response(response=500, description="Server error")
 * )
 */

/**
 * Get a single customization record by ID
 *
 * @OA\Get(
 *   path="/mabini-cafe/phpbackend/routes/customize/{id}",
 *   tags={"CustomizeWebsite"},
 *   summary="Get customization record by ID",
 *   @OA\Parameter(
 *     name="id",
 *     in="path",
 *     required=true,
 *     description="Record ID",
 *     @OA\Schema(type="integer")
 *   ),
 *   @OA\Response(
 *     response=200,
 *     description="Successful operation",
 *     @OA\JsonContent(
 *       @OA\Property(property="id", type="integer"),
 *       @OA\Property(property="image_custom_name", type="string"),
 *       @OA\Property(property="image_path", type="string"),
 *       @OA\Property(property="created_at", type="string", format="date-time")
 *     )
 *   ),
 *   @OA\Response(response=404, description="Not found")
 * )
 */

/**
 * Create a new customization (upload image)
 *
 * @OA\Post(
 *   path="/mabini-cafe/phpbackend/routes/customize",
 *   tags={"CustomizeWebsite"},
 *   summary="Create customization record (upload image)",
 *   description="Creates new record. Use multipart/form-data with `image_custom_name` and `image` file.",
 *   @OA\RequestBody(
 *     required=true,
 *     @OA\MediaType(
 *       mediaType="multipart/form-data",
 *       @OA\Schema(
 *         @OA\Property(property="image_custom_name", type="string", description="Label or name for the image"),
 *         @OA\Property(property="image", type="string", format="binary", description="Image file to upload"),
 *         required={"image_custom_name","image"}
 *       )
 *     )
 *   ),
 *   @OA\Response(
 *     response=201,
 *     description="Created",
 *     @OA\JsonContent(@OA\Property(property="message", type="string"))
 *   ),
 *   @OA\Response(response=400, description="Bad request"),
 *   @OA\Response(response=500, description="Server error")
 * )
 */

/**
 * Update name only
 *
 * @OA\Put(
 *   path="/mabini-cafe/phpbackend/routes/customize/{id}",
 *   tags={"CustomizeWebsite"},
 *   summary="Update image_custom_name for a record",
 *   @OA\Parameter(
 *     name="id",
 *     in="path",
 *     required=true,
 *     @OA\Schema(type="integer")
 *   ),
 *   @OA\RequestBody(
 *     required=true,
 *     @OA\JsonContent(
 *       @OA\Property(property="image_custom_name", type="string", description="New custom name")
 *     )
 *   ),
 *   @OA\Response(
 *     response=200,
 *     description="Updated",
 *     @OA\JsonContent(@OA\Property(property="message", type="string"))
 *   ),
 *   @OA\Response(response=400, description="Bad request"),
 *   @OA\Response(response=404, description="Not found"),
 *   @OA\Response(response=500, description="Server error")
 * )
 */

/**
 * Update image only (file upload)
 *
 * @OA\Post(
 *   path="/mabini-cafe/phpbackend/routes/customize?custom_id={custom_id}",
 *   tags={"CustomizeWebsite"},
 *   summary="Update image file for a record",
 *   description="Upload a new image file. Use multipart/form-data with field `image`.",
 *   @OA\Parameter(
 *     name="custom_id",
 *     in="query",
 *     required=true,
 *     @OA\Schema(type="integer")
 *   ),
 *   @OA\RequestBody(
 *     required=true,
 *     @OA\MediaType(
 *       mediaType="multipart/form-data",
 *       @OA\Schema(
 *         @OA\Property(property="image", type="string", format="binary", description="New image file")
 *       )
 *     )
 *   ),
 *   @OA\Response(
 *     response=200,
 *     description="Image updated",
 *     @OA\JsonContent(@OA\Property(property="message", type="string"))
 *   ),
 *   @OA\Response(response=400, description="Bad request"),
 *   @OA\Response(response=404, description="Not found"),
 *   @OA\Response(response=500, description="Server error")
 * )
 */

/**
 * Delete a record
 *
 * @OA\Delete(
 *   path="/mabini-cafe/phpbackend/routes/customize/{id}",
 *   tags={"CustomizeWebsite"},
 *   summary="Delete customization record",
 *   @OA\Parameter(
 *     name="id",
 *     in="path",
 *     required=true,
 *     @OA\Schema(type="integer")
 *   ),
 *   @OA\Response(
 *     response=200,
 *     description="Deleted",
 *     @OA\JsonContent(@OA\Property(property="message", type="string"))
 *   ),
 *   @OA\Response(response=404, description="Not found"),
 *   @OA\Response(response=500, description="Server error")
 * )
 */
