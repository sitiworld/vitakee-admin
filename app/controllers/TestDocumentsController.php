<?php

require_once __DIR__ . '/../models/TestDocumentsModel.php';

class TestDocumentsController
{
    private $TestDocumentsModel;

    public function __construct()
    {
        $this->TestDocumentsModel = new TestDocumentsModel();
    }

    public function showImage($parametros)
    {
        $image_id = $parametros['id'] ?? null;
        try {
            if ($image_id != 0) {
                $image = $this->TestDocumentsModel->getImageById($image_id);
                if ($image) {
                    $this->jsonResponse(true, '', $image);
                } else {
                    $this->jsonResponse(false, 'Image not found');
                }
            } else {
                $this->jsonResponse(false, 'Invalid image ID');
            }
        } catch (mysqli_sql_exception $e) {
            $this->errorResponse(400, "Error retrieving image: " . $e->getMessage());
        }
    }

    public function getImagesByPanelAndTest($parametros)
    {
        $testPanelId = $parametros['test_panel_id'] ?? 0;
        $testId = $parametros['test_id'] ?? 0;

        try {
            if ($testPanelId > 0 && $testId > 0) {
                $images = $this->TestDocumentsModel->getByPanelAndTest($testPanelId, $testId);
                $this->jsonResponse($images['value'], $images['message'], $images['data']);
            } else {
                $this->jsonResponse(false, 'Invalid test panel or test ID');
            }
        } catch (mysqli_sql_exception $e) {
            $this->errorResponse(400, "Error retrieving images: " . $e->getMessage());
        }
    }

    public function create()
    {
        // var_dump($_POST);
        // var_dump($_FILES['name_image']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['name_image'])) {
            $data = $_POST;
            $file = $_FILES['name_image'];

            if (
                !empty($data['id_test_panel']) &&
                !empty($data['id_test']) &&
                !empty($data['description']) &&
                $file
            ) {
                try {
                    $result = $this->TestDocumentsModel->create($data, $file);
                    $this->jsonResponse($result['value'], $result['message']);
                } catch (mysqli_sql_exception $e) {
                    $this->errorResponse(400, "Error creating image: " . $e->getMessage());
                }
            } else {
                $this->errorResponse(400, "Missing required fields");
            }
        } else {
            $this->errorResponse(405, "Method not allowed. POST required / No Image found");
        }
    }

    public function update($parametros)
    {
        // Soporte para POST con _method = PUT
        $actualMethod = $_SERVER['REQUEST_METHOD'];
        if ($actualMethod === 'POST' && isset($_POST['_method']) && strtoupper($_POST['_method']) === 'PUT') {
            $actualMethod = 'PUT';
        }

        if ($actualMethod === 'PUT') {
            $image_id = $parametros['id'] ?? null;
            $data = $_POST;

            if (!$image_id || empty($data['description'])) {
                return $this->jsonResponse(false, 'Missing required fields');
            }

            $file = isset($_FILES['name_image']) ? $_FILES['name_image'] : null;

            try {
                $result = $this->TestDocumentsModel->update($data, $file);
                $this->jsonResponse($result['value'], $result['message']);
            } catch (mysqli_sql_exception $e) {
                $this->errorResponse(400, "Error updating image: " . $e->getMessage());
            }
        } else {
            $this->errorResponse(405, "Method not allowed. PUT required.");
        }
    }




    public function delete($parametros)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            $image_id = $parametros['id'] ?? null;
            if (!$image_id) {
                return $this->jsonResponse(false, 'Image ID is required for deletion');
            }

            try {
                $result = $this->TestDocumentsModel->delete($image_id);
                $this->jsonResponse($result['value'], $result['message'] ? "Image deleted successfully" : "Error deleting image");
            } catch (mysqli_sql_exception $e) {
                $this->errorResponse(400, "Error deleting image: " . $e->getMessage());
            }
        } else {
            $this->errorResponse(405, "Method not allowed. DELETE required.");
        }
    }

    private function getJsonInput(): array
    {
        $input = file_get_contents("php://input");
        return json_decode($input, true) ?? [];
    }

    private function jsonResponse(bool $value, string $message = '', $data = null)
    {
        header('Content-Type: application/json');

        $response = [
            'value' => $value,
            'message' => $message,
            'data' => is_array($data) ? $data : ($data !== null ? [$data] : [])
        ];

        echo json_encode($response);
        exit;
    }

    private function errorResponse($http_code, $message)
    {
        http_response_code($http_code);
        $this->jsonResponse(false, $message);
    }
}
