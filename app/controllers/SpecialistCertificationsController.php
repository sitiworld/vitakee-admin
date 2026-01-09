<?php
require_once __DIR__ . '/../models/SpecialistCertificationsModel.php';

class SpecialistCertificationsController
{
    private $model;

    public function __construct()
    {
        $this->model = new SpecialistCertificationsModel();
    }

    public function getById($parametros)
    {
        $id = $parametros['id'] ?? null;
        try {
            if ($id > 0) {
                $cert = $this->model->getById($id);
                if ($cert) {
                    $this->jsonResponse(true, '', $cert);
                } else {
                    $this->jsonResponse(false, 'Certification not found');
                }
            } else {
                $this->jsonResponse(false, 'Invalid certification ID');
            }
        } catch (mysqli_sql_exception $e) {
            $this->errorResponse(400, "Error retrieving certification: " . $e->getMessage());
        }
    }

    public function getAll()
    {
        try {
            $certs = $this->model->getAll();
            $this->jsonResponse(true, '', $certs);
        } catch (mysqli_sql_exception $e) {
            $this->errorResponse(400, "Error retrieving certifications: " . $e->getMessage());
        }
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['specialist_id']) && isset($_FILES['file'])) {
            try {
                $file_url = $this->handleFileUpload($_FILES['file']);
                $data = [
                    'specialist_id' => $_POST['specialist_id'],
                    'file_url'      => $file_url,
                    'title'         => $_POST['title'] ?? null,
                    'description'   => $_POST['description'] ?? null,
                    'visibility'    => $_POST['visibility'] ?? 'PUBLIC'
                ];

                $this->model->create($data);
                $this->jsonResponse(true, 'Certification created successfully');
            } catch (Exception $e) {
                $this->errorResponse(400, "Error creating certification: " . $e->getMessage());
            }
        } else {
            $this->errorResponse(400, 'Missing required fields: specialist_id and file');
        }
    }

    public function update($parametros)
    {
        $actualMethod = $_SERVER['REQUEST_METHOD'];
        if ($actualMethod === 'POST' && isset($_POST['_method']) && strtoupper($_POST['_method']) === 'PUT') {
            $actualMethod = 'PUT';
        }

        if ($actualMethod === 'PUT') {
            $id = $parametros['id'] ?? null;
            if (!$id) return $this->jsonResponse(false, 'Missing ID');

            try {
                $file_url = null;

                if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
                    $file_url = $this->handleFileUpload($_FILES['file']);
                }

                $data = [
                    'file_url'    => $file_url, // puede ser null
                    'title'       => $_POST['title'] ?? null,
                    'description' => $_POST['description'] ?? null,
                    'visibility'  => $_POST['visibility'] ?? 'PUBLIC'
                ];

                $this->model->update($id, $data);
                $this->jsonResponse(true, 'Certification updated successfully');
            } catch (Exception $e) {
                $this->errorResponse(400, "Error updating certification: " . $e->getMessage());
            }
        } else {
            $this->errorResponse(405, 'Method not allowed. PUT required.');
        }
    }

    public function delete($parametros)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            $id = $parametros['id'] ?? null;
            if (!$id) {
                return $this->jsonResponse(false, 'Certification ID is required for deletion');
            }

            try {
                $this->model->delete($id);
                $this->jsonResponse(true, 'Certification deleted successfully');
            } catch (mysqli_sql_exception $e) {
                $this->errorResponse(400, "Error deleting certification: " . $e->getMessage());
            }
        } else {
            $this->errorResponse(405, 'Method not allowed. DELETE required.');
        }
    }

    /**
     * Maneja subida de archivo y retorna la ruta pública
     */
    private function handleFileUpload($file): string
    {
        $allowedExtensions = ['pdf', 'png', 'jpg', 'jpeg'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $allowedExtensions)) {
            throw new Exception("File type not allowed: $extension");
        }

        $uploadDir = __DIR__ . '/../../uploads/certifications/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $filename = uniqid('cert_') . '.' . $extension;
        $destination = $uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new Exception("Error moving uploaded file.");
        }

        // Retorna ruta accesible públicamente
        return "uploads/certifications/$filename";
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
