<?php

require_once __DIR__ . '/../models/SpecialistPricingModel.php';

class SpecialistPricingController
{
    private $model;

    public function __construct()
    {
        $this->model = new SpecialistPricingModel();
    }

    public function getById($parametros)
    {
        $id = $parametros['id'] ?? null;
        try {
            if ($id > 0) {
                $record = $this->model->getById($id);
                if ($record) {
                    $this->jsonResponse(true, '', $record);
                } else {
                    $this->jsonResponse(false, 'Pricing record not found');
                }
            } else {
                $this->jsonResponse(false, 'Invalid ID');
            }
        } catch (mysqli_sql_exception $e) {
            $this->errorResponse(400, "Error retrieving pricing: " . $e->getMessage());
        }
    }

    public function getAll()
    {
        try {
            $records = $this->model->getAll();
            $this->jsonResponse(true, '', $records);
        } catch (mysqli_sql_exception $e) {
            $this->errorResponse(400, "Error retrieving pricing records: " . $e->getMessage());
        }
    }
    public function create()
    {
        // Asegura sesión
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Solo POST
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            return $this->errorResponse(405, 'Method not allowed');
        }

        // specialist_id desde sesión (fallback a user_id)
        $specialistId = $_SESSION['specialist_id'] ?? ($_SESSION['user_id'] ?? null);
        if (!$specialistId) {
            return $this->errorResponse(401, 'Missing specialist in session (expected specialist_id or user_id)');
        }

        // Lee inputs
        $serviceType = trim($_POST['service_type'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $priceUsdRaw = trim($_POST['price_usd'] ?? '');
        $durationServices = trim($_POST['duration_services'] ?? NULL); // Nuevo campo opcional

        // on/1/true -> 1; vacío/0/false -> 0
        $isActive = !empty($_POST['is_active']) ? 1 : 0;



        // Valida requeridos y forma de datos
        $missing = [];
        if ($serviceType === '')
            $missing[] = 'service_type';
        if ($priceUsdRaw === '')
            $missing[] = 'price_usd';

        if (!empty($missing)) {
            return $this->errorResponse(422, 'Missing fields: ' . implode(', ', $missing));
        }

        // Normaliza precio: acepta coma o punto, y exige número >= 0
        $priceNormalized = str_replace(',', '.', $priceUsdRaw);
        if (!is_numeric($priceNormalized)) {
            return $this->errorResponse(422, 'Invalid price_usd: must be numeric');
        }
        $priceUsd = (float) $priceNormalized;
        if ($priceUsd < 0) {
            return $this->errorResponse(422, 'Invalid price_usd: must be >= 0');
        }
        // Opcional: fija 2 decimales para guardar como string
        $priceUsd = number_format($priceUsd, 2, '.', '');

        $data = [
            'specialist_id' => $specialistId,
            'service_type' => $serviceType,
            'description' => $description,
            'price_usd' => $priceUsd,
            'is_active' => $isActive,
            'duration_services' => $durationServices
        ];

        try {
            $this->model->create($data);
            return $this->jsonResponse(true, 'Pricing created successfully');
        } catch (mysqli_sql_exception $e) {
            return $this->errorResponse(400, 'Error creating pricing: ' . $e->getMessage());
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
            if (!$id || !isset($_POST['service_type'], $_POST['price_usd'])) {
                return $this->jsonResponse(false, 'Missing required fields');
            }

            $data = [
                'service_type' => $_POST['service_type'],
                'description' => $_POST['description'] ?? '',
                'price_usd' => $_POST['price_usd'],
                'is_active' => $_POST['is_active'] ?? 1,
                'duration_services' => $_POST['duration_services'] ?? NULL
            ];

            try {
                $this->model->update($id, $data);
                $this->jsonResponse(true, 'Pricing updated successfully');
            } catch (mysqli_sql_exception $e) {
                $this->errorResponse(400, "Error updating pricing: " . $e->getMessage());
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
                return $this->jsonResponse(false, 'ID is required for deletion');
            }

            try {
                $this->model->delete($id);
                $this->jsonResponse(true, 'Pricing deleted successfully');
            } catch (mysqli_sql_exception $e) {
                $this->errorResponse(400, "Error deleting pricing: " . $e->getMessage());
            }
        } else {
            $this->errorResponse(405, 'Method not allowed. DELETE required.');
        }
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
