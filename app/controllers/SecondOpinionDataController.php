<?php

require_once __DIR__ . '/../models/SecondOpinionDataModel.php';

class SecondOpinionDataController
{
    private SecondOpinionDataModel $model;

    public function __construct()
    {
        $this->model = new SecondOpinionDataModel();
    }

    /* ============== Helpers de respuesta ============== */

    private function jsonResponse(bool $value, string $message = '', $data = null, int $httpCode = 200): void
    {
        http_response_code($httpCode);
        header('Content-Type: application/json');
        echo json_encode([
            'value'   => $value,
            'message' => $message,
            'data'    => is_array($data) ? $data : ($data !== null ? [$data] : [])
        ]);
        exit;
    }

    private function getJson(): array
    {
        $raw = file_get_contents('php://input');
        return json_decode($raw, true) ?? [];
    }

    private function t(string $key, string $fallback): string
    {
        $lang = strtoupper($_SESSION['idioma'] ?? 'EN');
        $langPath = PROJECT_ROOT . "/lang/{$lang}.php";
        $tr = file_exists($langPath) ? include $langPath : include PROJECT_ROOT . "/lang/EN.php";
        return $tr[$key] ?? $fallback;
    }

    /* ============== Endpoints ============== */

    // GET /second_opinion_data
    public function index(): void
    {
        try {
            $rows = $this->model->getAll();
            $this->jsonResponse(true, '', $rows);
        } catch (\Throwable $e) {
            $this->jsonResponse(false, "Error fetching data: " . $e->getMessage(), null, 400);
        }
    }

    // GET /second_opinion_data/{id}
    public function show(array $params): void
    {
        $id = $params['id'] ?? null;
        if (!$id) {
            $this->jsonResponse(false, "Missing id", null, 400);
        }
        try {
            $row = $this->model->getById($id);
            if (!$row) {
                $this->jsonResponse(false, $this->t('record_not_found', 'Record not found'), null, 404);
            }
            $this->jsonResponse(true, '', $row);
        } catch (\Throwable $e) {
            $this->jsonResponse(false, "Error: " . $e->getMessage(), null, 400);
        }
    }

    // GET /second_opinion_data/by-request/{second_opinion_id}
    public function listByRequest(array $params): void
    {
        $sid = $params['second_opinion_id'] ?? null;
        if (!$sid) {
            $this->jsonResponse(false, "Missing second_opinion_id", null, 400);
        }
        try {
            $rows = $this->model->listBySecondOpinionId($sid);
            $this->jsonResponse(true, '', $rows);
        } catch (\Throwable $e) {
            $this->jsonResponse(false, "Error fetching data: " . $e->getMessage(), null, 400);
        }
    }

    // POST /second_opinion_data
    // Acepta application/json o form-data
    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(false, $this->t('method_not_allowed', 'Method not allowed'), null, 405);
        }

        $input = !empty($_POST) ? $_POST : $this->getJson();
        try {
            $id = $this->model->create($input);
            $msg = $this->t('created_successfully', 'Created successfully');
            $this->jsonResponse(true, $msg, ['second_opinion_data_id' => $id], 201);
        } catch (mysqli_sql_exception $e) {
            $this->jsonResponse(false, $e->getMessage(), null, 400);
        } catch (\Throwable $e) {
            $this->jsonResponse(false, "Unexpected error: " . $e->getMessage(), null, 500);
        }
    }

    // POST /second_opinion_data/{id}
    // (siguiendo tu patrón de usar POST con id para update)
    public function update(array $params): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(false, $this->t('method_not_allowed', 'Method not allowed'), null, 405);
        }

        $id = $params['id'] ?? null;
        if (!$id) {
            $this->jsonResponse(false, "Missing id", null, 400);
        }

        $input = !empty($_POST) ? $_POST : $this->getJson();

        try {
            $ok = $this->model->update($id, $input);
            $msg = $ok ? $this->t('updated_successfully', 'Updated successfully') : $this->t('no_changes', 'No changes');
            $this->jsonResponse(true, $msg, ['second_opinion_data_id' => $id]);
        } catch (mysqli_sql_exception $e) {
            $this->jsonResponse(false, $e->getMessage(), null, 400);
        } catch (\Throwable $e) {
            $this->jsonResponse(false, "Unexpected error: " . $e->getMessage(), null, 500);
        }
    }

    // DELETE lógico: POST /second_opinion_data/delete/{id}  (o DELETE si ya lo manejas)
    public function delete(array $params): void
    {
        $id = $params['id'] ?? null;
        if (!$id) {
            $this->jsonResponse(false, "Missing id", null, 400);
        }

        try {
            $ok = $this->model->delete($id);
            $msg = $ok ? $this->t('deleted_successfully', 'Deleted successfully') : $this->t('delete_failed', 'Delete failed');
            $this->jsonResponse($ok, $msg);
        } catch (mysqli_sql_exception $e) {
            $this->jsonResponse(false, $e->getMessage(), null, 400);
        } catch (\Throwable $e) {
            $this->jsonResponse(false, "Unexpected error: " . $e->getMessage(), null, 500);
        }
    }
}
