<?php
require_once __DIR__ . '/../models/ContactEmailModel.php';

class ContactEmailController
{
    private ContactEmailModel $model;

    public function __construct()
    {
        $this->model = new ContactEmailModel();
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

    /* ===================== Endpoints ===================== */
    public function getByEmail($params)
    {
        $email = $params['email'] ?? null;
        $entityType = $params['entity_type'] ?? '';

        if (!$email) {
            return $this->jsonResponse(false, 'Missing email', null, 400);
        }

        try {
            $row = $this->model->getByEmail($email, $entityType);
            if ($row) {
                return $this->jsonResponse(true, '', $row);
            }
            return $this->jsonResponse(false, 'Contact email not found', null);
        } catch (\mysqli_sql_exception $e) {
            return $this->jsonResponse(false, 'Error fetching contact email: ' . $e->getMessage(), null, 400);
        }
    }

    public function showByEmail($params)
    {
        $email = $params['email_contact'] ?? ($_POST['email_contact'] ?? ($_GET['email_contact'] ?? null));
        $entityType = $params['entity_type'] ?? ($_POST['entity_type'] ?? ($_GET['entity_type'] ?? null));

        if (!$email) {
            return $this->jsonResponse(false, 'Missing email', null, 400);
        }

        try {
            $row = $this->model->getByEmail($email, $entityType);
            if ($row) {
                return $this->jsonResponse(true, '', $row);
            }
            return $this->jsonResponse(false, 'Contact email not found', null);
        } catch (\mysqli_sql_exception $e) {
            return $this->jsonResponse(false, 'Error fetching contact email: ' . $e->getMessage(), null, 400);
        }
    }


    public function getAll()
    {
        try {
            $rows = $this->model->getAll();
            return $this->jsonResponse(true, '', $rows);
        } catch (mysqli_sql_exception $e) {
            return $this->jsonResponse(false, 'Error fetching contact emails: ' . $e->getMessage(), null, 400);
        }
    }

    public function getById($params)
    {
        $id = $params['id'] ?? null;
        if (!$id)
            return $this->jsonResponse(false, 'Missing contact_email_id', null, 400);

        try {
            $row = $this->model->getById($id);
            if ($row)
                return $this->jsonResponse(true, '', $row);
            return $this->jsonResponse(false, 'Contact email not found', null, 404);
        } catch (mysqli_sql_exception $e) {
            return $this->jsonResponse(false, 'Error fetching contact email: ' . $e->getMessage(), null, 400);
        }
    }

    // GET /contact-emails/entity/{type}/{id}
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
            return $this->jsonResponse(false, 'Error fetching contact emails: ' . $e->getMessage(), null, 400);
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
            return $this->jsonResponse(true, 'Contact email created successfully', ['contact_email_id' => $id]);
        } catch (mysqli_sql_exception $e) {
            return $this->jsonResponse(false, 'Error creating contact email: ' . $e->getMessage(), null, 400);
        }
    }

    public function update($params)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(false, 'Method not allowed', null, 405);
        }

        $id = $params['id'] ?? null;
        if (!$id)
            return $this->jsonResponse(false, 'Missing contact_email_id', null, 400);

        $data = $_POST ?: $this->getJsonInput();
        try {
            $ok = $this->model->update($id, $data);
            return $this->jsonResponse(true, $ok ? 'Contact email updated successfully' : 'No changes', ['contact_email_id' => $id]);
        } catch (mysqli_sql_exception $e) {
            return $this->jsonResponse(false, 'Error updating contact email: ' . $e->getMessage(), null, 400);
        }
    }

    public function delete($params)
    {
        $id = $params['id'] ?? null;
        if (!$id)
            return $this->jsonResponse(false, 'Missing contact_email_id', null, 400);

        try {
            $ok = $this->model->delete($id);
            return $this->jsonResponse($ok, $ok ? 'Contact email deleted successfully' : 'Delete failed');
        } catch (mysqli_sql_exception $e) {
            return $this->jsonResponse(false, 'Error deleting contact email: ' . $e->getMessage(), null, 400);
        }
    }

    // POST /contact-emails/{id}/set-primary
    public function setPrimary($params)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(false, 'Method not allowed', null, 405);
        }
        $id = $params['id'] ?? null;
        if (!$id)
            return $this->jsonResponse(false, 'Missing contact_email_id', null, 400);

        try {
            $ok = $this->model->setPrimary($id);
            return $this->jsonResponse($ok, $ok ? 'Primary email set' : 'Could not set primary');
        } catch (mysqli_sql_exception $e) {
            return $this->jsonResponse(false, 'Error setting primary: ' . $e->getMessage(), null, 400);
        }
    }
}
