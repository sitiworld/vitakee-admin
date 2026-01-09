<?php

require_once __DIR__ . '/../models/SpecialistSocialLinksModel.php';

class SpecialistSocialLinksController
{
    private $model;

    public function __construct()
    {
        $this->model = new SpecialistSocialLinksModel();
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
                    $this->jsonResponse(false, 'Record not found');
                }
            } else {
                $this->jsonResponse(false, 'Invalid ID');
            }
        } catch (mysqli_sql_exception $e) {
            $this->errorResponse(400, "Error retrieving record: " . $e->getMessage());
        }
    }
    public function getBySpecialistId($parametros)
    {
        $id = $parametros['id'] ?? null;

        try {
            if ($id > 0) {
                $record = $this->model->getByIdSpecialist($id);

                if ($record) {
                    $this->jsonResponse(true, '', $record);
                } else {
                    $this->jsonResponse(false, "Record not found $id", $id);
                }
            } else {
                $this->jsonResponse(false, 'Invalid ID');
            }
        } catch (mysqli_sql_exception $e) {
            $this->errorResponse(400, "Error retrieving record: " . $e->getMessage());
        }
    }

    public function getAll()
    {
        try {
            $records = $this->model->getAll();
            $this->jsonResponse(true, '', $records);
        } catch (mysqli_sql_exception $e) {
            $this->errorResponse(400, "Error retrieving records: " . $e->getMessage());
        }
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = null;
            if ($_POST) {

                $data = [
                    'specialist_id' => $_POST['specialist_id'],
                    'platform' => $_POST['platform'],
                    'url' => $_POST['url']
                ];
            } else {
                $json = file_get_contents('php://input');
                $data = json_decode($json, true);

            }

            if (!isset($data['specialist_id'], $data['platform'], $data['url'])) {
                return $this->jsonResponse(false, 'Missing required fields');
            }

            try {
                $this->model->create($data);
                $this->jsonResponse(true, 'Record created successfully');
            } catch (mysqli_sql_exception $e) {
                $this->errorResponse(400, "Error creating record: " . $e->getMessage());
            }
        } else {
            $this->errorResponse(400, 'Missing required fields: specialist_id, platform, url');
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
            if (!$id || !isset($_POST['platform'], $_POST['url'])) {
                return $this->jsonResponse(false, 'Missing required fields');
            }

            $data = [
                'platform' => $_POST['platform'],
                'url' => $_POST['url']
            ];

            try {
                $this->model->update($id, $data);
                $this->jsonResponse(true, 'Record updated successfully');
            } catch (mysqli_sql_exception $e) {
                $this->errorResponse(400, "Error updating record: " . $e->getMessage());
            }
        } else {
            $this->errorResponse(405, 'Method not allowed. PUT required.');
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
                $this->jsonResponse(true, 'Record deleted successfully');
            } catch (mysqli_sql_exception $e) {
                $this->errorResponse(400, "Error deleting record: " . $e->getMessage());
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

    private function errorResponse($http_code, $message)
    {
        http_response_code($http_code);
        $this->jsonResponse(false, $message);
    }
}
