<?php
require_once __DIR__ . '/../models/ContactPhoneModel.php';

class ContactPhoneController
{
    private ContactPhoneModel $model;

    public function __construct()
    {
        $this->model = new ContactPhoneModel();
    }

    private function getJsonInput(): array
    {
        $raw = file_get_contents("php://input");
        return json_decode($raw, true) ?? [];
    }

    private function jsonResponse(bool $value, string $message = '', $data = null, int $code = 200)
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode([
            'value' => $value,
            'message' => $message,
            'data' => is_array($data) ? $data : ($data !== null ? [$data] : [])
        ]);
        exit;
    }
    public function getByTelephone($params)
    {
        $telephone = $params['telephone'] ?? null;
        $entityType = $params['entity_type'] ?? null;

        if (!$telephone) {
            return $this->jsonResponse(false, 'Missing telephone', null, 400);
        }

        try {
            $row = $this->model->getByTelephone($telephone, $entityType);
            if ($row) {
                return $this->jsonResponse(true, '', $row);
            }
            return $this->jsonResponse(false, 'Contact phone not found', null, 404);
        } catch (\mysqli_sql_exception $e) {
            return $this->jsonResponse(false, 'Error fetching contact phone: ' . $e->getMessage(), null, 400);
        }
    }

    public function showByTelephone($params)
    {
        $telephone = $params['telephone_contact'] ?? $_POST['telephone_contact'] ?? $_GET['telephone_contact'] ?? null;
        // NUEVO: entity_type opcional
        $entityType = $params['entity_type'] ?? $_POST['entity_type'] ?? $_GET['entity_type'] ?? null;

        if (!$telephone) {
            return $this->jsonResponse(false, 'Missing telephone', null, 400);
        }

        try {
            $row = $this->model->getByTelephone($telephone, $entityType);
            if ($row) {
                return $this->jsonResponse(true, '', $row);
            }
            return $this->jsonResponse(false, 'Contact phone not found', null);
        } catch (\mysqli_sql_exception $e) {
            return $this->jsonResponse(false, 'Error fetching contact phone: ' . $e->getMessage(), null, 400);
        }
    }

    public function getAll()
    {
        try {
            $rows = $this->model->getAll();
            return $this->jsonResponse(true, '', $rows);
        } catch (mysqli_sql_exception $e) {
            return $this->jsonResponse(false, 'Error fetching contact phones: ' . $e->getMessage(), null, 400);
        }
    }

    public function getById($params)
    {
        $id = $params['id'] ?? null;
        if (!$id)
            return $this->jsonResponse(false, 'Missing contact_phone_id', null, 400);

        try {
            $row = $this->model->getById($id);
            if ($row)
                return $this->jsonResponse(true, '', $row);
            return $this->jsonResponse(false, 'Contact phone not found', null, 404);
        } catch (mysqli_sql_exception $e) {
            return $this->jsonResponse(false, 'Error fetching contact phone: ' . $e->getMessage(), null, 400);
        }
    }

    // GET /contact-phones/entity/{type}/{id}
    public function getByEntity($params)
    {
        $type = $params['type'] ?? null;
        $id = $params['id'] ?? null;
        if (!$type || !$id)
            return $this->jsonResponse(false, 'Missing entity_type or entity_id', null, 400);

        try {
            $rows = $this->model->getByEntity($type, $id);
            return $this->jsonResponse(true, '', $rows);
        } catch (mysqli_sql_exception $e) {
            return $this->jsonResponse(false, 'Error fetching contact phones: ' . $e->getMessage(), null, 400);
        }
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(false, 'Method not allowed', null, 405);
        }

        $data = $_POST ?: $this->getJsonInput();
        try {
            $id = $this->model->create($data);
            return $this->jsonResponse(true, 'Contact phone created successfully', ['contact_phone_id' => $id]);
        } catch (mysqli_sql_exception $e) {
            return $this->jsonResponse(false, 'Error creating contact phone: ' . $e->getMessage(), null, 400);
        }
    }

    public function update($params)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(false, 'Method not allowed', null, 405);
        }
        $id = $params['id'] ?? null;
        if (!$id)
            return $this->jsonResponse(false, 'Missing contact_phone_id', null, 400);

        $data = $_POST ?: $this->getJsonInput();
        try {
            $ok = $this->model->update($id, $data);
            return $this->jsonResponse(true, $ok ? 'Contact phone updated successfully' : 'No changes', ['contact_phone_id' => $id]);
        } catch (mysqli_sql_exception $e) {
            return $this->jsonResponse(false, 'Error updating contact phone: ' . $e->getMessage(), null, 400);
        }
    }

    public function delete($params)
    {
        $id = $params['id'] ?? null;
        if (!$id)
            return $this->jsonResponse(false, 'Missing contact_phone_id', null, 400);

        try {
            $ok = $this->model->delete($id);
            return $this->jsonResponse($ok, $ok ? 'Contact phone deleted successfully' : 'Delete failed');
        } catch (mysqli_sql_exception $e) {
            return $this->jsonResponse(false, 'Error deleting contact phone: ' . $e->getMessage(), null, 400);
        }
    }

    // POST /contact-phones/{id}/set-primary
    public function setPrimary($params)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(false, 'Method not allowed', null, 405);
        }
        $id = $params['id'] ?? null;
        if (!$id)
            return $this->jsonResponse(false, 'Missing contact_phone_id', null, 400);

        try {
            $ok = $this->model->setPrimary($id);
            return $this->jsonResponse($ok, $ok ? 'Primary phone set' : 'Could not set primary');
        } catch (mysqli_sql_exception $e) {
            return $this->jsonResponse(false, 'Error setting primary: ' . $e->getMessage(), null, 400);
        }
    }
}
