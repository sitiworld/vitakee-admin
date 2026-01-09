<?php

require_once __DIR__ . '/../models/SpecialistLocationsModel.php';

class SpecialistLocationsController
{
    private $model;

    public function __construct()
    {
        $this->model = new SpecialistLocationsModel();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /** =======================
     *  HELPERS PRIVADOS
     *  ======================= */
    private function requireSpecialistIdFromSession(): ?string
    {
        // specialist_id preferido; fallback a user_id (por si tu sesión lo usa así)
        $sid = $_SESSION['specialist_id'] ?? ($_SESSION['user_id'] ?? null);
        if (!$sid) {
            $this->errorResponse(401, 'Missing specialist in session (expected specialist_id or user_id)');
            return null; // unreachable por exit en errorResponse, pero mantiene tipado
        }
        return $sid;
    }

    private function isValidId($id): bool
    {
        // Acepta enteros positivos o UUID v4 (char(36))
        if (is_numeric($id) && (int)$id > 0) return true;
        return is_string($id) && (bool)preg_match('/^[0-9a-fA-F-]{36}$/', $id);
    }

    private function toBool01($val): int
    {
        if (is_bool($val)) return $val ? 1 : 0;
        $s = strtolower(trim((string)$val));
        return in_array($s, ['1','true','on','yes','si','sí'], true) ? 1 : 0;
    }

    private function cleanStr($val, int $max = 100): string
    {
        return mb_substr(trim((string)$val), 0, $max);
    }

    private function ensureOwnership(array $record, string $sessionSpecialistId): void
    {
        // Si el modelo retorna specialist_id en el registro, verifícalo
        if (isset($record['specialist_id']) && (string)$record['specialist_id'] !== (string)$sessionSpecialistId) {
            $this->errorResponse(403, 'You cannot modify a location that does not belong to you');
        }
    }

    private function unsetOtherPrimaryIfNeeded(string $specialistId, ?string $excludeId = null): void
    {
        // Intenta llamar al método del modelo si existe (nombre flexible)
        if (method_exists($this->model, 'unsetPrimaryForSpecialist')) {
            $this->model->unsetPrimaryForSpecialist($specialistId, $excludeId);
        } elseif (method_exists($this->model, 'unsetPrimary')) {
            $this->model->unsetPrimary($specialistId, $excludeId);
        }
        // Si no existe, se omite silenciosamente (lo ideal es implementarlo en el modelo)
    }

    /** =======================
     *  ENDPOINTS
     *  ======================= */

    public function getById($parametros)
    {
        $id = $parametros['id'] ?? null;

        try {
            if (!$this->isValidId($id)) {
                return $this->jsonResponse(false, 'Invalid ID');
            }

            $record = $this->model->getById($id);
            if (!$record) {
                return $this->jsonResponse(false, 'Record not found');
            }

            // Si hay sesión, valida ownership (no bloquea lecturas si el modelo no retorna specialist_id)
            $sid = $_SESSION['specialist_id'] ?? ($_SESSION['user_id'] ?? null);
            if ($sid && is_array($record)) {
                $this->ensureOwnership((array)$record, (string)$sid);
            }

            $this->jsonResponse(true, '', $record);
        } catch (mysqli_sql_exception $e) {
            $this->errorResponse(400, "Error retrieving record: " . $e->getMessage());
        }
    }

    public function getAll()
    {
        try {
            // Si el modelo soporta listar por especialista, úsalo; si no, fallback a getAll()
            $sid = $_SESSION['specialist_id'] ?? ($_SESSION['user_id'] ?? null);
            if ($sid && method_exists($this->model, 'getAllBySpecialist')) {
                $records = $this->model->getAllBySpecialist($sid);
            } elseif ($sid && method_exists($this->model, 'getBySpecialist')) {
                $records = $this->model->getBySpecialist($sid);
            } else {
                $records = $this->model->getAll();
            }

            $this->jsonResponse(true, '', $records);
        } catch (mysqli_sql_exception $e) {
            $this->errorResponse(400, "Error retrieving records: " . $e->getMessage());
        }
    }

    public function create()
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            return $this->errorResponse(405, 'Method not allowed');
        }

        $specialistId = $this->requireSpecialistIdFromSession();

        // Inputs
        $city_id      = $this->cleanStr($_POST['city_id'] ?? '', 100);
        $state_id     = $this->cleanStr($_POST['state_id'] ?? '', 100);
        $country_id   = $this->cleanStr($_POST['country_id'] ?? '', 100);
        $isPrimary = $this->toBool01($_POST['is_primary'] ?? 0);

        // Validación detallada
        $missing = [];
        if ($country_id === '') $missing[] = 'country_id';
        if ($state_id   === '') $missing[] = 'state_id';
        if ($city_id    === '') $missing[] = 'city_id';
        if (!empty($missing)) {
            return $this->errorResponse(422, 'Missing fields: ' . implode(', ', $missing));
        }

        $data = [
            'specialist_id' => $specialistId,
            'city_id'          => $city_id,
            'state_id'         => $state_id,
            'country_id'       => $country_id,
            'is_primary'    => $isPrimary,
        ];

        try {
            if ($isPrimary === 1) {
                // Asegura que sea el único principal
                $this->unsetOtherPrimaryIfNeeded($specialistId);
            }

            $this->model->create($data);
            return $this->jsonResponse(true, 'Location created successfully');
        } catch (mysqli_sql_exception $e) {
            return $this->errorResponse(400, 'Error creating location: ' . $e->getMessage());
        }
    }

    public function update($parametros)
    {
        // Soporte para override: POST + _method=PUT
        $actualMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if ($actualMethod === 'POST' && isset($_POST['_method']) && strtoupper($_POST['_method']) === 'PUT') {
            $actualMethod = 'PUT';
        }

        if ($actualMethod !== 'PUT') {
            return $this->errorResponse(405, 'Method not allowed. PUT required.');
        }

        $id = $parametros['id'] ?? null;
        if (!$this->isValidId($id)) {
            return $this->jsonResponse(false, 'Invalid ID');
        }

        $specialistId = $this->requireSpecialistIdFromSession();

        // Obtener registro para validar ownership
        try {
            $current = $this->model->getById($id);
            if (!$current) {
                return $this->jsonResponse(false, 'Record not found');
            }
            if (is_array($current)) {
                $this->ensureOwnership((array)$current, $specialistId);
            }
        } catch (mysqli_sql_exception $e) {
            return $this->errorResponse(400, "Error loading record: " . $e->getMessage());
        }

        // Lee y valida inputs
        $city_id      = $this->cleanStr($_POST['city_id'] ?? '', 100);
        $state_id     = $this->cleanStr($_POST['state_id'] ?? '', 100);
        $country_id   = $this->cleanStr($_POST['country_id'] ?? '', 100);
        $isPrimary = $this->toBool01($_POST['is_primary'] ?? 0);

        $missing = [];
        if ($country_id === '') $missing[] = 'country_id';
        if ($state_id   === '') $missing[] = 'state_id';
        if ($city_id    === '') $missing[] = 'city_id';
        if (!empty($missing)) {
            return $this->errorResponse(422, 'Missing fields: ' . implode(', ', $missing));
        }

        $data = [
            'city_id'       => $city_id,
            'state_id'      => $state_id,
            'country_id'    => $country_id,
            'is_primary' => $isPrimary,
        ];

        try {
            if ($isPrimary === 1) {
                // Dejar esta como la única primaria del especialista
                $this->unsetOtherPrimaryIfNeeded($specialistId, (string)$id);
            }

            $this->model->update($id, $data);
            $this->jsonResponse(true, 'Location updated successfully');
        } catch (mysqli_sql_exception $e) {
            $this->errorResponse(400, "Error updating location: " . $e->getMessage());
        }
    }

    public function delete($parametros)
    {
        // Soporte para override: POST + _method=DELETE
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if ($method === 'POST' && isset($_POST['_method']) && strtoupper($_POST['_method']) === 'DELETE') {
            $method = 'DELETE';
        }

        if ($method !== 'DELETE') {
            return $this->errorResponse(405, 'Method not allowed. DELETE required.');
        }

        $id = $parametros['id'] ?? null;
        if (!$this->isValidId($id)) {
            return $this->jsonResponse(false, 'Invalid ID');
        }

        $specialistId = $this->requireSpecialistIdFromSession();

        try {
            $record = $this->model->getById($id);
            if (!$record) {
                return $this->jsonResponse(false, 'Record not found');
            }

            if (is_array($record)) {
                $this->ensureOwnership((array)$record, $specialistId);
            }

            $this->model->delete($id);
            $this->jsonResponse(true, 'Location deleted successfully');
        } catch (mysqli_sql_exception $e) {
            $this->errorResponse(400, "Error deleting location: " . $e->getMessage());
        }
    }

    /** =======================
     *  RESPUESTAS
     *  ======================= */
    private function jsonResponse(bool $value, string $message = '', $data = null)
    {
        header('Content-Type: application/json');

        $response = [
            'value'   => $value,
            'message' => $message,
            'data'    => is_array($data) ? $data : ($data !== null ? [$data] : [])
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
