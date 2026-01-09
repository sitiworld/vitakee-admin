<?php

require_once __DIR__ . '/../models/SpecialistAvailabilityModel.php';
require_once __DIR__ . '/../models/SecondOpinionRequestsModel.php';
require_once __DIR__ . '/../models/SpecialistPricingModel.php';
require_once __DIR__ . '/../services/AvailabilitySlotGenerator.php';
class SpecialistAvailabilityController
{
    private $model;

    public function __construct()
    {
        $this->model = new SpecialistAvailabilityModel();
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
                    $this->jsonResponse(false, 'Availability not found');
                }
            } else {
                $this->jsonResponse(false, 'Invalid ID');
            }
        } catch (mysqli_sql_exception $e) {
            $this->errorResponse(400, "Error retrieving availability: " . $e->getMessage());
        }
    }

    public function getAll()
    {
        try {
            $records = $this->model->getAll();
            $this->jsonResponse(true, '', $records);
        } catch (mysqli_sql_exception $e) {
            $this->errorResponse(400, "Error retrieving availability records: " . $e->getMessage());
        }
    }

    public function create()
    {
        if (session_status() === PHP_SESSION_NONE)
            session_start();

        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            return $this->errorResponse(405, 'Method not allowed');
        }

        // specialist_id desde la sesión (o user_id como fallback si así lo manejas)
        $specialistId = $_SESSION['specialist_id'] ?? ($_SESSION['user_id'] ?? null);
        if (!$specialistId) {
            return $this->errorResponse(401, 'Missing specialist in session');
        }

        $weekday = trim($_POST['weekday'] ?? '');
        $start_time = trim($_POST['start_time'] ?? '');
        $end_time = trim($_POST['end_time'] ?? '');
        $timezone = trim($_POST['timezone'] ?? '');
        $buffer_time = trim($_POST['buffer_time_minutes'] ?? '0'); // opcional
        if (!is_numeric($buffer_time) || (int) $buffer_time < 0) {
            $buffer_time = 0; // valor por defecto si es inválido
        }

        // Validaciones
        $missing = [];
        if ($weekday === '')
            $missing[] = 'weekday';
        if ($start_time === '')
            $missing[] = 'start_time';
        if ($end_time === '')
            $missing[] = 'end_time';
        if ($timezone === '')
            $missing[] = 'timezone';
        if ($missing) {
            return $this->errorResponse(422, 'Missing fields: ' . implode(', ', $missing));
        }

        // Formato HH:MM (opcionalmente convierte a HH:MM:SS)
        $isValidTime = fn($t) => preg_match('/^\d{2}:\d{2}$/', $t);
        if (!$isValidTime($start_time) || !$isValidTime($end_time)) {
            return $this->errorResponse(422, 'Invalid time format (expected HH:MM)');
        }
        if ($start_time >= $end_time) {
            return $this->errorResponse(422, 'start_time must be before end_time');
        }

        $data = [
            'specialist_id' => $specialistId,
            'weekday' => $weekday,           // e.g. 'MON', 'TUE', ...
            'start_time' => $start_time . ':00',// normaliza a HH:MM:SS si tu DB es TIME
            'end_time' => $end_time . ':00',
            'timezone' => $timezone,
            'buffer_time_minutes' => (int) $buffer_time
        ];

        try {
            // (Opcional) Validación de traslape a nivel de modelo/DB aquí…
            $this->model->create($data);
            return $this->jsonResponse(true, 'Availability created');
        } catch (mysqli_sql_exception $e) {
            return $this->errorResponse(400, 'Error creating availability: ' . $e->getMessage());
        }
    }

    public function syncTimezoneFromSpecialist()
    {
        // Solo permitimos POST o PUT
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if ($method === 'POST' && isset($_POST['_method']) && strtoupper($_POST['_method']) === 'PUT') {
            $method = 'PUT';
        }
        if (!in_array($method, ['POST', 'PUT'])) {
            return $this->errorResponse(405, 'Method not allowed. Use POST or PUT.');
        }

        // Tomamos el specialist_id desde la sesión (que en tu caso es user_id)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $specialistId = $_SESSION['user_id'] ?? null;

        if (!$specialistId) {
            return $this->errorResponse(401, 'Specialist (user_id) not found in session');
        }

        try {
            $result = $this->model->syncTimezoneFromSpecialist($specialistId);

            if (is_int($result)) {
                return $this->jsonResponse(true, 'Timezone synchronized from specialists', [
                    'specialist_id' => $specialistId,
                    'updated_count' => $result
                ]);
            }

            return $this->jsonResponse(true, 'Timezone synchronized from specialists', [
                'specialist_id' => $specialistId
            ]);
        } catch (Exception $e) {
            return $this->errorResponse(400, 'Error syncing timezone: ' . $e->getMessage());
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
            if (!$id || !isset($_POST['weekday'], $_POST['start_time'], $_POST['end_time'], $_POST['timezone'])) {
                return $this->jsonResponse(false, 'Missing required fields');
            }

            $data = [
                'weekday' => $_POST['weekday'],
                'start_time' => $_POST['start_time'],
                'end_time' => $_POST['end_time'],
                'timezone' => $_POST['timezone'],
                'buffer_time_minutes' => isset($_POST['buffer_time_minutes']) ? (int) $_POST['buffer_time_minutes'] : 0
            ];

            try {
                $this->model->update($id, $data);
                $this->jsonResponse(true, 'Availability updated successfully');
            } catch (mysqli_sql_exception $e) {
                $this->errorResponse(400, "Error updating availability: " . $e->getMessage());
            }
        } else {
            $this->errorResponse(405, 'Method not allowed. PUT rsequired.');
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
                $this->jsonResponse(true, 'Availability deleted successfully');
            } catch (mysqli_sql_exception $e) {
                $this->errorResponse(400, "Error deleting availability: " . $e->getMessage());
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

    public function getAvailableSlots()
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'GET') {
            return $this->errorResponse(405, 'Method not allowed. GET required.');
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // --- 1. Validar Entradas (ahora con pricing_id) ---
        $specialistId = $_GET['specialist_id'] ?? null;
        $pricingId = $_GET['pricing_id'] ?? null; // NUEVO y REQUERIDO
        $rangeStart = $_GET['start'] ?? null;
        $rangeEnd = $_GET['end'] ?? null;
        $userTimezone = $_SESSION['timezone'];

        // Validar que los parámetros requeridos existan
        if (!$specialistId || !$pricingId || !$rangeStart || !$rangeEnd) {
            return $this->errorResponse(422, 'Missing required parameters: specialist_id, pricing_id, start, end');
        }

        try {
            // --- 2. Instanciar y Usar el Servicio (con el nuevo modelo) ---

            $availabilityModel = $this->model;
            $secondOpinionModel = new SecondOpinionRequestsModel();
            $pricingModel = new SpecialistPricingModel(); // NUEVO

            // Pasar las tres dependencias al generador
            $slotGenerator = new AvailabilitySlotGenerator($availabilityModel, $secondOpinionModel, $pricingModel);

            // Generar los slots de disponibilidad
            $availableSlots = $slotGenerator->generateSlots(
                $specialistId,
                $pricingId, // Se pasa el pricing_id
                $userTimezone,
                $rangeStart,
                $rangeEnd
            );

            // --- 3. Devolver la Respuesta ---
            return $this->jsonResponse(true, 'Available slots retrieved', $availableSlots);




        } catch (Exception $e) {
            return $this->errorResponse(500, 'Error generating availability slots: ' . $e->getMessage());
        }
    }



    private function errorResponse($http_code, $message)
    {
        http_response_code($http_code);
        $this->jsonResponse(false, $message);
    }
}
