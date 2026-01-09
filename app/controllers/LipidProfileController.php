<?php

require_once __DIR__ . '/../models/LipidProfileModel.php';


class LipidProfileController
{
    private $lipidProfileModel;

    public function __construct()
    {
        $this->lipidProfileModel = new LipidProfileModel();
    }

    public function getAllByUser()
    {
        try {
            if (!isset($_SESSION['user_id'])) {
                $this->errorResponse(401, 'Not authenticated');
            }

            $user_id = $_SESSION['user_id'];
            $lipidRecords = $this->lipidProfileModel->getByUserId($user_id);
            $this->jsonResponse(true, 'Records fetched successfully', $lipidRecords);
        } catch (mysqli_sql_exception $e) {
            $this->errorResponse(400, "Error fetching lipid records: " . $e->getMessage());
        }
    }

    public function getRecord($parametros)
    {
        $record_id = $parametros['record_id'] ?? null;

        try {
            if ($record_id > 0) {
                $user_id = $_SESSION['user_id'];
                $lipidRecord = $this->lipidProfileModel->getById($record_id);

                if ($lipidRecord) {
                    $this->jsonResponse(true, "Record found", $lipidRecord);
                } else {
                    $this->jsonResponse(false, "Record not found", []);
                }
            } else {
                $this->jsonResponse(false, "Invalid record ID");
            }
        } catch (mysqli_sql_exception $e) {
            $this->errorResponse(400, "Error fetching record: " . $e->getMessage());
        }
    }
     public function exportCSVByUserId()
    {

        $userId = $_SESSION['user_id'];

        try {
            // Llamar al modelo para generar el CSV directamente
            $this->lipidProfileModel->exportLipidProfilesByUserToCSV($userId);
        } catch (Exception $e) {
            $this->errorResponse(500, "Error exporting data: " . $e->getMessage());
        }
    }

    public function getRecordUser($parametros)
    {
        $user_id = $parametros['user_id'] ?? null;

        try {
            if ($user_id > 0) {
                $lipidRecord = $this->lipidProfileModel->getByIdUser($user_id);

                if ($lipidRecord) {
                    $this->jsonResponse(true, "Record found", $lipidRecord);
                } else {
                    $this->jsonResponse(false, "Record not found", []);
                }
            } else {
                $this->jsonResponse(false, "Invalid record ID");
            }
        } catch (mysqli_sql_exception $e) {
            $this->errorResponse(400, "Error fetching record: " . $e->getMessage());
        }
    }
    public function getHistoryByRecordId($parametros)
    {
        $recId = $parametros['recId'] ?? null;
        $type = $parametros['type'] ?? null;
        // Lista de campos válidos
        $validFields = [
            'ldl',
            'hdl',
            'total_cholesterol',
            'triglycerides',
            'non_hdl'
        ];

        // Validación de parámetros
        if ($recId && $type) {
            try {


                if (!in_array($type, $validFields)) {
                    throw new Exception('Invalid type.');
                }

                // Llamar al método del modelo
                $history = $this->lipidProfileModel->getUserLipidProfileHistoryByRecordId($recId, $type);

                // Verificar el resultado
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
public function create()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $this->errorResponse(405, "Method not allowed. POST required.");
    }

    $data = json_decode(file_get_contents('php://input'), true);

    try {
        if (!isset($_SESSION['user_id'])) {
            $this->errorResponse(401, 'Not authenticated');
        }

        $data['user_id'] = $_SESSION['user_id'];
        $result = $this->lipidProfileModel->create($data);

        return $this->jsonResponse(
            $result['value'] ?? false,
            $result['message'] ?? 'Unexpected result from model.',
            $result
        );
    } catch (mysqli_sql_exception $e) {
        $this->errorResponse(400, "Error creating record: " . $e->getMessage());
    }
}



    public function update($parametros)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            $this->errorResponse(405, "Method not allowed. POST required.");
        }

        $record_id = $parametros['id'] ?? null;
        // parse_str(file_get_contents("php://input"), $data);


        $data = $this->getJsonInput();

        $data['id'] = $record_id;

        try {
            if ($record_id > 0) {
                $result = $this->lipidProfileModel->update($data);
                if ($result) {
                    $this->jsonResponse($result['value'], $result['message']);
                    return;

                }


                $this->jsonResponse(false, "Error updating record");

            } else {
                $this->jsonResponse(false, "Invalid record ID");
            }
        } catch (mysqli_sql_exception $e) {
            $this->errorResponse(400, "Error updating record: " . $e->getMessage());
        }
    }
    public function updateNoAlertUser()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $recordId = $_POST['record_id'] ?? null;

        if (!$recordId) {
            $this->errorResponse(400, "Missing record_id.");
            return;
        }

        try {
            $result = $this->lipidProfileModel->updateNoAlertUser($recordId);
            if ($result['status'] === 'error') {
                $this->jsonResponse(false, $result['message']);
            } else {
                $this->jsonResponse(true, $result['message']);
            }
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
            $this->errorResponse(400, "Missing record_id.");
            return;
        }

        try {
            $result = $this->lipidProfileModel->updateNoAlertAdmin($recordId);
            if ($result['status'] === 'error') {
                $this->jsonResponse(false, $result['message']);
            } else {
                $this->jsonResponse(true, $result['message']);
            }
        } catch (mysqli_sql_exception $e) {
            $this->errorResponse(400, "Error updating admin alert flag: " . $e->getMessage());
        }
    } else {
        $this->errorResponse(405, "Method not allowed. POST required.");
    }
}

    public function delete($parametros)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            $this->errorResponse(405, "Method not allowed. DELETE required.");
        }

        $record_id = $parametros['id'] ?? null;

        try {
            if ($record_id > 0) {
                $result = $this->lipidProfileModel->delete($record_id);

                if ($result) {
                    $this->jsonResponse(true, "Record deleted successfully");
                } else {
                    $this->jsonResponse(false, "Error deleting record");
                }
            } else {
                $this->jsonResponse(false, "Invalid record ID");
            }
        } catch (mysqli_sql_exception $e) {
            $this->errorResponse(400, "Error deleting record: " . $e->getMessage());
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
}
