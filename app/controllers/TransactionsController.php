<?php

require_once __DIR__ . '/../models/TransactionsModel.php';

class TransactionsController
{
    private $model;

    public function __construct()
    {
        $this->model = new TransactionsModel();
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
                    $this->jsonResponse(false, 'Transaction not found');
                }
            } else {
                $this->jsonResponse(false, 'Invalid ID');
            }
        } catch (mysqli_sql_exception $e) {
            $this->errorResponse(400, "Error retrieving transaction: " . $e->getMessage());
        }
    }

    public function getAll()
    {
        try {
            $records = $this->model->getAll();
            $this->jsonResponse(true, '', $records);
        } catch (mysqli_sql_exception $e) {
            $this->errorResponse(400, "Error retrieving transactions: " . $e->getMessage());
        }
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['specialist_id'], $_POST['amount_usd'], $_POST['type'])) {
            $data = [
                'user_id'           => $_POST['user_id'],
                'specialist_id'     => $_POST['specialist_id'],
                'pricing_id'        => $_POST['pricing_id'] ?? null,
                'amount_usd'        => $_POST['amount_usd'],
                'type'              => $_POST['type'],
                'platform_fee'      => $_POST['platform_fee'] ?? 0,
                'status'            => $_POST['status'] ?? 'PENDING',
                'payment_reference' => $_POST['payment_reference'] ?? ''
            ];

            try {
                $this->model->create($data);
                $this->jsonResponse(true, 'Transaction created successfully');
            } catch (mysqli_sql_exception $e) {
                $this->errorResponse(400, "Error creating transaction: " . $e->getMessage());
            }
        } else {
            $this->errorResponse(400, 'Missing required fields: user_id, specialist_id, amount_usd, type');
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
            if (!$id || !isset($_POST['amount_usd'], $_POST['status'])) {
                return $this->jsonResponse(false, 'Missing required fields');
            }

            $data = [
                'amount_usd'        => $_POST['amount_usd'],
                'platform_fee'      => $_POST['platform_fee'] ?? 0,
                'status'            => $_POST['status'],
                'payment_reference' => $_POST['payment_reference'] ?? ''
            ];

            try {
                $this->model->update($id, $data);
                $this->jsonResponse(true, 'Transaction updated successfully');
            } catch (mysqli_sql_exception $e) {
                $this->errorResponse(400, "Error updating transaction: " . $e->getMessage());
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
                $this->jsonResponse(true, 'Transaction deleted successfully');
            } catch (mysqli_sql_exception $e) {
                $this->errorResponse(400, "Error deleting transaction: " . $e->getMessage());
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
