<?php

require_once __DIR__ . '/../models/BodyCompositionModel.php';

class BodyCompositionController
{
    private $bodyCompositionModel;

    public function __construct()
    {
        $this->bodyCompositionModel = new BodyCompositionModel();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function getAllByUserId()
    {
        try {
            if (!isset($_SESSION['user_id'])) {
                $this->errorResponse(401, "Unauthorized: User ID not found in session.");
                return;
            }
            $user_id = $_SESSION['user_id'];
            $bodyCompositions = $this->bodyCompositionModel->getAllByUserId($user_id);
            // Verificar si la llamada al modelo devolvió un error
            if (isset($bodyCompositions['status']) && $bodyCompositions['status'] === 'error') {
                $this->errorResponse(400, "Error retrieving body compositions: " . $bodyCompositions['message']);
            } else {
                $this->jsonResponse(true, '', $bodyCompositions);
            }
        } catch (mysqli_sql_exception $e) {
            $this->errorResponse(400, "Error retrieving body compositions: " . $e->getMessage());
        }
    }
        public function showById($parametros)
    {
        $id = $parametros['id'] ?? null;
        if ($id) {
            try {
                $record = $this->bodyCompositionModel->getById($id);
                if ($record) {
                    $this->jsonResponse(true, 'Record found', $record);
                } else {
                    $this->jsonResponse(false, 'Record not found');
                }
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
                $record = $this->bodyCompositionModel->getByIdUser($user_id);
                if ($record) {
                    $this->jsonResponse(true, 'Record found', $record);
                } else {
                    $this->jsonResponse(false, 'Record not found');
                }
            } catch (mysqli_sql_exception $e) {
                $this->errorResponse(400, "Error fetching record: " . $e->getMessage());
            }
        } else {
            $this->errorResponse(400, "Invalid record ID");
        }
    }
       public function exportCSVByUserId()
    {

        $userId = $_SESSION['user_id'];

        try {
            // Llamar al modelo para generar el CSV directamente
            $this->bodyCompositionModel->exportBodyCompositionByUserToCSV($userId);
        } catch (Exception $e) {
            $this->errorResponse(500, "Error exporting data: " . $e->getMessage());
        }
    }
    
    public function getHistoryByRecordId($parametros)
    {
        $recId = $parametros['recId'] ?? null;
        $type = $parametros['type'] ?? null;
          $validFields = [
                'bmi',
                'body_fat_pct',
                'water_pct',
                'muscle_pct',
                'resting_metabolism',
                'visceral_fat',
                'body_age'
            ];


        // Validación de parámetros
        if ($recId && $type) {
            try {
              

            if (!in_array($type, $validFields)) {
                throw new Exception('Invalid type.');
            }

                // Llamar al método del modelo
                $history = $this->bodyCompositionModel->getUserBodyCompositionHistoryByRecordId($recId, $type);

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

    public function getBodyCompositionData($params)
    {
        $id = $params['id'] ?? 0;
        try {
            if ($id > 0) {
                if (!isset($_SESSION['user_id'])) {
                    $this->errorResponse(401, "Unauthorized: User ID not found in session.");
                    return;
                }
                $user_id = $_SESSION['user_id'];

                $data = $this->bodyCompositionModel->getBodyCompositionData($id, $user_id);
                // Verificar si la llamada al modelo devolvió un error
                if (isset($data['status']) && $data['status'] === 'error') {
                    $this->jsonResponse(false, $data['message']);
                } else if ($data) {
                    $this->jsonResponse(true, '', $data);
                } else {
                    $this->jsonResponse(false, "Body composition not found");
                }
            } else {
                $this->jsonResponse(false, "Invalid body composition ID");
            }
        } catch (mysqli_sql_exception $e) {
            $this->errorResponse(400, "Error retrieving body composition data: " . $e->getMessage());
        }
    }

// En tu controller
private function readJsonOrPost(): array
{
    // Si viene JSON, php://input lo trae aunque $_POST esté vacío
    $raw = file_get_contents('php://input') ?: '';
    $json = json_decode($raw, true);
    if (is_array($json) && !empty($json)) {
        return $json;
    }
    // Fallback a form-data
    return $_POST ?? [];
}

public function create()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $this->errorResponse(405, "Method not allowed. POST required.");
        return;
    }

    // Lee JSON o POST
    $data = $this->readJsonOrPost();

    if (!isset($_SESSION['user_id'])) {
        $this->errorResponse(401, "Unauthorized: User ID not found in session.");
        return;
    }

    // Siempre forzar user_id desde la sesión
    $data['user_id'] = $_SESSION['user_id'];

    try {
        $result = $this->bodyCompositionModel->create($data);

        if (($result['status'] ?? '') === 'error') {
            $this->jsonResponse(false, $result['message'] ?? 'Error');
            return;
        }

        $this->jsonResponse(true, "Body composition created successfully");

    } catch (mysqli_sql_exception $e) {
        $this->errorResponse(400, "Error creating body composition: " . $e->getMessage());
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
            $result = $this->bodyCompositionModel->updateNoAlertUser($recordId);
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
            $result = $this->bodyCompositionModel->updateNoAlertAdmin($recordId);
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



public function update($params)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
        $this->errorResponse(405, "Method not allowed. PUT required.");
        return;
    }

    $id = $params['id'] ?? null;
    if (!$id) {
        $this->jsonResponse(false, "Invalid body composition ID for update");
        return;
    }

    // Leer JSON o POST
    $data = $this->readJsonOrPost();

    // Forzar ID y user_id desde la sesión
    $data['id'] = $id;
    if (isset($_SESSION['user_id'])) {
        $data['user_id'] = $_SESSION['user_id'];
    }

    try {
        $result = $this->bodyCompositionModel->update($data);

        if (($result['status'] ?? '') === 'error') {
            $this->jsonResponse(false, $result['message']);
        } else {
            $this->jsonResponse(true, "Body composition updated successfully");
        }

    } catch (mysqli_sql_exception $e) {
        $this->errorResponse(400, "Error updating body composition: " . $e->getMessage());
    }
}

    public function delete($params)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            $id = $params['id'] ?? null;
            try {
                // No es necesario verificar la existencia aquí, el modelo lo hace
                if ($id > 0) {
                    $result = $this->bodyCompositionModel->delete($id);
                    if (isset($result['status']) && $result['status'] === 'error') {
                        $this->jsonResponse(false, $result['message']);
                    } else {
                        $this->jsonResponse(true, "Body composition deleted successfully");
                    }
                } else {
                    $this->jsonResponse(false, "Invalid body composition ID for deletion");
                }
            } catch (mysqli_sql_exception $e) {
                $this->errorResponse(400, "Error deleting body composition: " . $e->getMessage());
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