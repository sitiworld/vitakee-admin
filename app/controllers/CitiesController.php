<?php
require_once __DIR__ . '/../models/CitiesModel.php';

class CitiesController
{
    private CitiesModel $model;

    public function __construct()
    {
        $this->model = new CitiesModel();
    }

    private function jsonResponse(bool $value, string $message = '', $data = null, int $http = 200)
    {
        http_response_code($http);
        header('Content-Type: application/json');
        echo json_encode([
            'value' => $value,
            'message' => $message,
            'data' => is_array($data) ? $data : ($data !== null ? [$data] : [])
        ]);
        exit;
    }

    private function errorResponse(int $code, string $message)
    {
        $this->jsonResponse(false, $message, null, $code);
    }

    public function getAllForTable()
    {
        try {
            // Filtros opcionales
            $countryId = $_GET['country_id'] ?? null;
            $stateId = $_GET['state_id'] ?? null;

            // Parámetros de Bootstrap Table (server-side)
            $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
            $offset = isset($_GET['offset']) ? (int) $_GET['offset'] : 0;
            $search = isset($_GET['search']) ? trim((string) $_GET['search']) : '';

            // Orden seguro (whitelist en el modelo)
            $sort = $_GET['sort'] ?? 'city_name';
            $order = strtoupper($_GET['order'] ?? 'ASC');

            // Saneamos límites razonables
            if ($limit < 1)
                $limit = 10;
            if ($limit > 200)
                $limit = 200;
            if ($offset < 0)
                $offset = 0;
            if ($order !== 'ASC' && $order !== 'DESC')
                $order = 'ASC';

            // Llama al modelo con paginación
            [$total, $rows] = $this->model->getAllPaged(
                limit: $limit,
                offset: $offset,
                search: $search,
                sort: $sort,
                order: $order,
                countryId: $countryId,
                stateId: $stateId
            );

            // ⚠️ Bootstrap Table espera { total, rows } a secas
            // Opción A (recomendada): responde plano, sin wrapper jsonResponse
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['total' => $total, 'rows' => $rows], JSON_UNESCAPED_UNICODE);
            // return;

            // --- Opción B (si prefieres mantener jsonResponse) ---
            // Cambia tu responseHandler en el front a:
            // window.citiesResponseHandler = (res) => res?.data ? res.data : res;
            // y aquí podrías hacer:
            // return $this->jsonResponse(true, '', ['total' => $total, 'rows' => $rows]);

        } catch (\mysqli_sql_exception $e) {
            return $this->errorResponse(400, "Error fetching cities: " . $e->getMessage());
        }
    }

public function getAll()
{
    try {
        // Recoger parámetros de la request (GET o POST)
        $countryId = $_GET['country_id'] ?? null;
        $stateId   = $_GET['state_id']   ?? null;
        $q         = $_GET['q']          ?? null;

        $rows = $this->model->getAll($countryId, $stateId, $q);

        // Normalizar salida (lista de registros siempre)
        if ($rows === null) {
            $data = [];
        } elseif (is_array($rows)) {
            $isAssoc = array_keys($rows) !== range(0, count($rows) - 1);
            $data = $isAssoc ? [$rows] : $rows;
        } else {
            error_log('[CitiesController.getAll][warn] Formato inesperado: ' . gettype($rows));
            $data = [];
        }

        return $this->jsonResponse(true, '', $data);

    } catch (\mysqli_sql_exception $e) {
        error_log('[CitiesController.getAll][sql_error] ' . $e->getMessage());
        return $this->errorResponse(400, 'Error fetching cities: ' . $e->getMessage());
    } catch (\Throwable $e) {
        error_log('[CitiesController.getAll][error] ' . $e->getMessage());
        return $this->errorResponse(500, 'Unexpected error fetching cities.');
    }
}


    public function getById($params)
    {
        $id = $params['id'] ?? null;
        if (!$id)
            return $this->errorResponse(400, "Missing city_id.");

        try {
            $row = $this->model->getById($id);
            return $row ? $this->jsonResponse(true, '', $row)
                : $this->errorResponse(404, "City not found.");
        } catch (\mysqli_sql_exception $e) {
            return $this->errorResponse(400, "Error fetching city: " . $e->getMessage());
        }
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->errorResponse(405, "Method not allowed");
        }

        $data = $_POST;
        // permitir JSON
        if (empty($data)) {
            $raw = file_get_contents("php://input");
            $data = json_decode($raw, true) ?? [];
        }

        // Validaciones mínimas
        foreach (['country_id', 'state_id', 'city_name'] as $f) {
            if (empty($data[$f])) {
                return $this->errorResponse(400, "Field {$f} is required.");
            }
        }

        try {
            $ok = $this->model->create($data);
            return $this->jsonResponse(true, 'City created successfully.', $ok);
        } catch (\mysqli_sql_exception $e) {
            return $this->errorResponse(400, 'Error creating city: ' . $e->getMessage());
        }
    }

    public function update($params)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->errorResponse(405, "Method not allowed");
        }

        $id = $params['id'] ?? null;
        if (!$id)
            return $this->errorResponse(400, "Missing city_id.");

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
            return $this->jsonResponse(true, 'City updated successfully.', $ok);
        } catch (\mysqli_sql_exception $e) {
            return $this->errorResponse(400, 'Error updating city: ' . $e->getMessage());
        }
    }

    public function delete($params)
    {
        $id = $params['id'] ?? null;
        if (!$id)
            return $this->errorResponse(400, "Missing city_id.");

        try {
            $ok = $this->model->delete($id);
            return $this->jsonResponse(true, 'City deleted successfully.', $ok);
        } catch (\mysqli_sql_exception $e) {
            return $this->errorResponse(400, 'Error deleting city: ' . $e->getMessage());
        }
    }
}
