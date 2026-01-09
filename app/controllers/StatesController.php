<?php
require_once __DIR__ . '/../models/StatesModel.php';

class StatesController
{
    private StatesModel $model;

    public function __construct()
    {
        $this->model = new StatesModel();
    }

    private function jsonResponse(bool $value, string $message = '', $data = null, int $http = 200)
    {
        http_response_code($http);
        header('Content-Type: application/json');
        echo json_encode([
            'value'   => $value,
            'message' => $message,
            'data'    => is_array($data) ? $data : ($data !== null ? [$data] : [])
        ]);
        exit;
    }

    private function errorResponse(int $code, string $message)
    {
        $this->jsonResponse(false, $message, null, $code);
    }

    public function getAll()
    {
        try {
            // Filtros opcionales
            $countryId = $_GET['country_id'] ?? null;
            $q         = $_GET['q'] ?? null;             // busca por state_name
            $code      = $_GET['state_code'] ?? null;
            $iso       = $_GET['iso'] ?? null;           // iso3166_2
            $type      = $_GET['type'] ?? null;

            $rows = $this->model->getAll($countryId, $q, $code, $iso, $type);
            return $this->jsonResponse(true, '', $rows);
        } catch (\mysqli_sql_exception $e) {
            return $this->errorResponse(400, "Error fetching states: " . $e->getMessage());
        }
    }

    public function getById($params)
    {
        $id = $params['id'] ?? null;
        if (!$id) return $this->errorResponse(400, "Missing state_id.");

        try {
            $row = $this->model->getById($id);
            return $row ? $this->jsonResponse(true, '', $row)
                        : $this->errorResponse(404, "State not found.");
        } catch (\mysqli_sql_exception $e) {
            return $this->errorResponse(400, "Error fetching state: " . $e->getMessage());
        }
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->errorResponse(405, "Method not allowed");
        }

        $data = $_POST;
        if (empty($data)) {
            $raw = file_get_contents("php://input");
            $data = json_decode($raw, true) ?? [];
        }

        foreach (['country_id','state_name'] as $f) {
            if (empty($data[$f])) {
                return $this->errorResponse(400, "Field {$f} is required.");
            }
        }

        try {
            $ok = $this->model->create($data);
            return $this->jsonResponse(true, 'State created successfully.', $ok);
        } catch (\mysqli_sql_exception $e) {
            return $this->errorResponse(400, 'Error creating state: ' . $e->getMessage());
        }
    }

    public function update($params)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->errorResponse(405, "Method not allowed");
        }

        $id = $params['id'] ?? null;
        if (!$id) return $this->errorResponse(400, "Missing state_id.");

        $data = $_POST;
        if (empty($data)) {
            parse_str(file_get_contents("php://input"), $data);
            if (empty($data)) {
                $raw = file_get_contents("php://input");
                $data = json_decode($raw, true) ?? [];
            }
        }

        try {
            $ok = $this->model->update($id, $data);
            return $this->jsonResponse(true, 'State updated successfully.', $ok);
        } catch (\mysqli_sql_exception $e) {
            return $this->errorResponse(400, 'Error updating state: ' . $e->getMessage());
        }
    }

    public function delete($params)
    {
        $id = $params['id'] ?? null;
        if (!$id) return $this->errorResponse(400, "Missing state_id.");

        try {
            $ok = $this->model->delete($id);
            return $this->jsonResponse(true, 'State deleted successfully.', $ok);
        } catch (\mysqli_sql_exception $e) {
            return $this->errorResponse(400, 'Error deleting state: ' . $e->getMessage());
        }
    }
}
