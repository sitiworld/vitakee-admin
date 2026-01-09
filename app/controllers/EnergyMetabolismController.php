<?php

require_once __DIR__ . '/../models/EnergyMetabolismModel.php';

class EnergyMetabolismController
{
    private $energyMetabolismModel;

    public function __construct()
    {
        $this->energyMetabolismModel = new EnergyMetabolismModel();
    }

    public function showAll()
    {
        try {
            $user_id = $_SESSION['user_id'];
            $records = $this->energyMetabolismModel->getByUserId($user_id);
            $this->jsonResponse(true, 'Records retrieved successfully', $records);
        } catch (mysqli_sql_exception $e) {
            $this->errorResponse(400, "Error retrieving records: " . $e->getMessage());
        }
    }

    public function showById($parametros)
    {
        $id = $parametros['id'] ?? null;
        if ($id) {
            try {
                // OJO: getById() en el modelo imprime y hace exit vía jsonResponse.
                // Este jsonResponse se ejecutará solo si el modelo NO salió ya.
                $record = $this->energyMetabolismModel->getById($id);
                $this->jsonResponse((bool)$record, $record ? 'Record found' : 'Record not found', $record);
            } catch (mysqli_sql_exception $e) {
                $this->errorResponse(400, "Error fetching record: " . $e->getMessage());
            }
        } else {
            $this->errorResponse(400, "Invalid record ID");
        }
    }

    public function showByIdUser($parametros)
    {
        $user_id = $parametros['user_id'] ?? null;
        if ($user_id) {
            try {
                $record = $this->energyMetabolismModel->getByUserId($user_id);
                $this->jsonResponse((bool)$record, $record ? 'Record found' : 'Record not found', $record);
            } catch (mysqli_sql_exception $e) {
                $this->errorResponse(400, "Error fetching record: " . $e->getMessage());
            }
        } else {
            $this->errorResponse(400, "Invalid user ID");
        }
    }

    public function getHistoryByRecordId($parametros)
    {
        $recId = $parametros['recId'] ?? null;
        $type = $parametros['type'] ?? null;

        if ($recId && $type) {
            // ✅ Ahora permitimos HbA1c además de glucose y ketone
            if (!in_array($type, ['glucose', 'ketone', 'hba1c'])) {
                return $this->errorResponse(400, "Invalid biomarker type.");
            }

            try {
                $history = $this->energyMetabolismModel->getUserBiomarkerHistoryByRecordId($recId, $type);
                if ($history['status'] === 'success') {
                    $this->jsonResponse(true, 'History fetched successfully', $history['data']);
                } else {
                    $this->jsonResponse(false, $history['message']);
                }
            } catch (Exception $e) {
                $this->errorResponse(400, "Error fetching history: " . $e->getMessage());
            }
        } else {
            $this->errorResponse(400, "Invalid parameters. 'recId' and 'type' are required.");
        }
    }

    public function exportCSVByUserId()
    {
        $userId = $_SESSION['user_id'];

        try {
            $this->energyMetabolismModel->exportUserRecordsToCSV($userId);
        } catch (Exception $e) {
            $this->errorResponse(500, "Error exporting data: " . $e->getMessage());
        }
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->errorResponse(405, "Method not allowed. POST is required.");
        }

        $data = $this->getJsonInput();
        $data['user_id'] = $_SESSION['user_id'];

        try {
            $result = $this->energyMetabolismModel->create($data);
            $this->jsonResponse(
                $result['status'] === 'success',
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
        $data = $this->getJsonInput();
        $data['id'] = $id;

        try {
            $result = $this->energyMetabolismModel->update($data);
            $this->jsonResponse(
                $result['status'] === 'success',
                $result['message'] ?? '',
                $result
            );
        } catch (mysqli_sql_exception $e) {
            $this->jsonResponse(false, 'Error while updating biomarker: ' . $e->getMessage());
        }
    }

    // --- Endpoints de muteo (siguen igual) ---

    public function updateNoAlertUser()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $recordId = $_POST['record_id'] ?? null;

            if (!$recordId) {
                return $this->errorResponse(400, "Missing record_id.");
            }

            try {
                $result = $this->energyMetabolismModel->updateNoAlertUser($recordId);
                $this->jsonResponse($result['status'] !== 'error', $result['message']);
            } catch (mysqli_sql_exception $e) {
                $this->errorResponse(400, "Error updating user alert flag: " . $e->getMessage());
            }
        } else {
            $this->errorResponse(405, "Method not allowed. POST required.");
        }
    }

    public function updateNoAlertAdmin()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $recordId = $_POST['record_id'] ?? null;

            if (!$recordId) {
                return $this->errorResponse(400, "Missing record_id.");
            }

            try {
                $result = $this->energyMetabolismModel->updateNoAlertAdmin($recordId);
                $this->jsonResponse($result['status'] !== 'error', $result['message']);
            } catch (mysqli_sql_exception $e) {
                $this->errorResponse(400, "Error updating admin alert flag: " . $e->getMessage());
            }
        } else {
            $this->errorResponse(405, "Method not allowed. POST required.");
        }
    }

    public function updateNoAlertUserByUser()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_POST['user_id'] ?? null;

            if (!$userId) {
                return $this->errorResponse(400, "Missing user_id.");
            }

            try {
                $result = $this->energyMetabolismModel->updateNoAlertUserByUserId($userId);
                $this->jsonResponse($result['status'] !== 'error', $result['message']);
            } catch (mysqli_sql_exception $e) {
                $this->errorResponse(400, "Error updating user alert flags: " . $e->getMessage());
            }
        } else {
            $this->errorResponse(405, "Method not allowed. POST required.");
        }
    }

    public function updateNoAlertAdminByUser()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $result = $this->energyMetabolismModel->updateNoAlertAdminByUserId();
                $this->jsonResponse($result['status'] !== 'error', $result['message']);
            } catch (mysqli_sql_exception $e) {
                $this->errorResponse(400, "Error updating admin alert flags: " . $e->getMessage());
            }
        } else {
            $this->errorResponse(405, "Method not allowed. POST required.");
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
            $result = $this->energyMetabolismModel->delete($id);
            $this->jsonResponse($result['value'], $result['message'] ?? "Record deleted");
        } catch (mysqli_sql_exception $e) {
            $this->errorResponse(400, "Error deleting record: " . $e->getMessage());
        }
    }

    /* ===================== HELPERS ===================== */

    private function getJsonInput(): array
    {
        $input = file_get_contents("php://input");
        return json_decode($input, true) ?? [];
    }

    private function jsonResponse(bool $value, string $message = '', $data = null)
    {
        header('Content-Type: application/json');
        echo json_encode([
            'value' => $value,
            'message' => $message,
            'data' => is_array($data) ? $data : ($data !== null ? [$data] : [])
        ]);
        exit;
    }

    private function errorResponse(int $http_code, string $message)
    {
        http_response_code($http_code);
        $this->jsonResponse(false, $message);
    }

    protected function view($view, $data = [])
    {
        $viewPath = __DIR__ . '/../Views/' . $view . '.php';
        if (file_exists($viewPath)) {
            extract($data);
            include $viewPath;
        } else {
            $this->errorResponse(500, "Internal server error: View not found.");
        }
    }
}
