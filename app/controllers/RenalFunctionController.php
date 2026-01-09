<?php

require_once __DIR__ . '/../models/RenalFunctionModel.php';

class RenalFunctionController
{
    private $renalFunctionModel;

    public function __construct()
    {
        $this->renalFunctionModel = new RenalFunctionModel();
    }

    public function showAll()
    {
        try {
            $user_id = $_SESSION['user_id'] ?? null;
            if (!$user_id) {
                return $this->errorResponse(401, "Unauthorized");
            }
            $records = $this->renalFunctionModel->getByUserId($user_id);
            $this->jsonResponse(true, 'Records retrieved successfully', $records);
        } catch (mysqli_sql_exception $e) {
            $this->errorResponse(400, "Error retrieving records: " . $e->getMessage());
        }
    }

    public function showById($parametros)
    {
        $id = $parametros['id'] ?? null;
        if (!$id) {
            return $this->errorResponse(400, "Invalid record ID");
        }

        try {
            // IMPORTANTE: el modelo ya responde en JSON y hace exit();
            $this->renalFunctionModel->getById($id);
            // No poner jsonResponse aquí, nunca se alcanzará.
        } catch (mysqli_sql_exception $e) {
            $this->errorResponse(400, "Error fetching record: " . $e->getMessage());
        }
    }

    public function showByIdUser($parametros)
    {
        $user_id = $parametros['user_id'] ?? null;
        if ($user_id) {
            try {
                $record = $this->renalFunctionModel->getByUserId($user_id);
                $this->jsonResponse(true, !empty($record) ? 'Record found' : 'Record not found', $record);
            } catch (mysqli_sql_exception $e) {
                $this->errorResponse(400, "Error fetching record: " . $e->getMessage());
            }
        } else {
            $this->errorResponse(400, "Invalid user ID");
        }
    }

    public function exportCSVByUserId()
    {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            return $this->errorResponse(401, "Unauthorized");
        }
        try {
            $this->renalFunctionModel->exportUserRecordsToCSV($userId);
        } catch (Exception $e) {
            $this->errorResponse(500, "Error exporting data: " . $e->getMessage());
        }
    }

    public function getHistoryByRecordId($parametros)
    {
        $recId = $parametros['recId'] ?? null;
        $type  = $parametros['type'] ?? null;

        // Biomarcadores/campos válidos (actualizados)
        $validFields = [
            'albumin',
            'creatinine',
            'serum_creatinine',
            'uric_acid_blood',
            'bun_blood',
            'egfr',
            'bun_cr_ratio'
        ];

        if ($recId && $type) {
            try {
                if (!in_array($type, $validFields)) {
                    throw new Exception('Invalid type.');
                }

                $history = $this->renalFunctionModel->getUserRenalFunctionHistoryByRecordId($recId, $type);

                if (($history['status'] ?? '') === 'success') {
                    $this->jsonResponse(true, 'History fetched successfully', $history['data']);
                } else {
                    $this->jsonResponse(false, $history['message'] ?? 'Error fetching history');
                }
            } catch (Exception $e) {
                $this->errorResponse(400, "Error fetching history: " . $e->getMessage());
            }
        } else {
            $this->errorResponse(400, "Invalid parameters. 'recId' and 'type' are required.");
        }
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->errorResponse(405, "Method not allowed. POST is required.");
        }

        $data = $this->getJsonInput();
        $data['user_id'] = $_SESSION['user_id'] ?? null;

        if (!$data['user_id']) {
            return $this->errorResponse(401, "Unauthorized");
        }

        try {
            $result = $this->renalFunctionModel->create($data);
            $this->jsonResponse(
                ($result['status'] ?? '') === 'success',
                $result['message'] ?? '',
                $result
            );
        } catch (mysqli_sql_exception $e) {
            $this->errorResponse(400, "Error creating record: " . $e->getMessage());
        }
    }

    public function update($parametros)
    {
        $id = $parametros['id'] ?? null;
        if (!$id) {
            return $this->errorResponse(400, "Invalid record ID");
        }

        $data = $this->getJsonInput();
        $data['id'] = $id;

        try {
            $result = $this->renalFunctionModel->update($data);
            $this->jsonResponse(
                ($result['status'] ?? '') === 'success',
                $result['message'] ?? '',
                $result
            );
        } catch (mysqli_sql_exception $e) {
            $this->jsonResponse(false, 'Error while updating biomarker: ' . $e->getMessage());
        }
    }

    public function delete($parametros)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            return $this->errorResponse(405, "Method not allowed. DELETE is required.");
        }

        $id = $parametros['id'] ?? null;
        if (!$id) {
            return $this->errorResponse(400, "Invalid or missing record ID");
        }

        try {
            $result = $this->renalFunctionModel->delete($id);
            $this->jsonResponse($result['value'] ?? false, $result['message'] ?? "Record deleted");
        } catch (mysqli_sql_exception $e) {
            $this->errorResponse(400, "Error deleting record: " . $e->getMessage());
        }
    }

    /* ===================== Helpers ===================== */

    private function getJsonInput(): array
    {
        $input = file_get_contents("php://input");
        return json_decode($input, true) ?? [];
    }

    private function jsonResponse(bool $value, string $message = '', $data = null)
    {
        header('Content-Type: application/json');
        echo json_encode([
            'value'   => $value,
            'message' => $message,
            'data'    => is_array($data) ? $data : ($data !== null ? [$data] : [])
        ]);
        exit;
    }

    private function errorResponse(int $http_code, string $message)
    {
        http_response_code($http_code);
        $this->jsonResponse(false, $message);
    }
}
