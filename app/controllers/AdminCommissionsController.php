<?php

require_once __DIR__ . '/../models/AdminCommissionsModel.php';

class AdminCommissionsController
{
    private $model;

    public function __construct()
    {
        $this->model = new AdminCommissionsModel();
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
                    $this->jsonResponse(false, 'Commission record not found');
                }
            } else {
                $this->jsonResponse(false, 'Invalid ID');
            }
        } catch (mysqli_sql_exception $e) {
            $this->errorResponse(400, "Error retrieving commission: " . $e->getMessage());
        }
    }

    public function getAll()
    {
        try {
            $records = $this->model->getAll();
            $this->jsonResponse(true, '', $records);
        } catch (mysqli_sql_exception $e) {
            $this->errorResponse(400, "Error retrieving commission records: " . $e->getMessage());
        }
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['transaction_id'], $_POST['commission_amount'], $_POST['transaction_type'])) {
            $data = [
                'transaction_id'    => $_POST['transaction_id'],
                'commission_amount' => $_POST['commission_amount'],
                'transaction_type'  => $_POST['transaction_type']
            ];

            try {
                $this->model->create($data);
                $this->jsonResponse(true, 'Commission created successfully');
            } catch (mysqli_sql_exception $e) {
                $this->errorResponse(400, "Error creating commission: " . $e->getMessage());
            }
        } else {
            $this->errorResponse(400, 'Missing required fields: transaction_id, commission_amount, transaction_type');
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
                $this->jsonResponse(true, 'Commission deleted successfully');
            } catch (mysqli_sql_exception $e) {
                $this->errorResponse(400, "Error deleting commission: " . $e->getMessage());
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
