<?php

require_once __DIR__ . '/../models/VideoCallsModel.php';

class VideoCallsController
{
    private $model;

    public function __construct()
    {
        $this->model = new VideoCallsModel();
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
                    $this->jsonResponse(false, 'Video call not found');
                }
            } else {
                $this->jsonResponse(false, 'Invalid ID');
            }
        } catch (mysqli_sql_exception $e) {
            $this->errorResponse(400, "Error retrieving video call: " . $e->getMessage());
        }
    }

    public function getAll()
    {
        try {
            $records = $this->model->getAll();
            $this->jsonResponse(true, '', $records);
        } catch (mysqli_sql_exception $e) {
            $this->errorResponse(400, "Error retrieving video calls: " . $e->getMessage());
        }
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'], $_POST['scheduled_at'])) {
            $data = [
                'request_id'       => $_POST['request_id'],
                'scheduled_at'     => $_POST['scheduled_at'],
                'duration_minutes' => $_POST['duration_minutes'] ?? 30,
                'meeting_url'      => $_POST['meeting_url'] ?? '',
                'meeting_token'    => $_POST['meeting_token'] ?? ''
            ];

            try {
                $this->model->create($data);
                $this->jsonResponse(true, 'Video call created successfully');
            } catch (mysqli_sql_exception $e) {
                $this->errorResponse(400, "Error creating video call: " . $e->getMessage());
            }
        } else {
            $this->errorResponse(400, 'Missing required fields: request_id and scheduled_at');
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
            if (!$id || !isset($_POST['scheduled_at'])) {
                return $this->jsonResponse(false, 'Missing required fields');
            }

            $data = [
                'scheduled_at'     => $_POST['scheduled_at'],
                'duration_minutes' => $_POST['duration_minutes'] ?? 30,
                'meeting_url'      => $_POST['meeting_url'] ?? '',
                'meeting_token'    => $_POST['meeting_token'] ?? ''
            ];

            try {
                $this->model->update($id, $data);
                $this->jsonResponse(true, 'Video call updated successfully');
            } catch (mysqli_sql_exception $e) {
                $this->errorResponse(400, "Error updating video call: " . $e->getMessage());
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
                $this->jsonResponse(true, 'Video call deleted successfully');
            } catch (mysqli_sql_exception $e) {
                $this->errorResponse(400, "Error deleting video call: " . $e->getMessage());
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
