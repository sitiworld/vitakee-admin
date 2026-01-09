<?php

require_once __DIR__ . '/../models/SpecialistReviewsModel.php';

class SpecialistReviewsController
{
    private $model;

    public function __construct()
    {
        $this->model = new SpecialistReviewsModel();
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
                    $this->jsonResponse(false, 'Review not found');
                }
            } else {
                $this->jsonResponse(false, 'Invalid ID');
            }
        } catch (mysqli_sql_exception $e) {
            $this->errorResponse(400, "Error retrieving review: " . $e->getMessage());
        }
    }

    public function getAll()
    {
        try {
            $records = $this->model->getAll();
            $this->jsonResponse(true, '', $records);
        } catch (mysqli_sql_exception $e) {
            $this->errorResponse(400, "Error retrieving reviews: " . $e->getMessage());
        }
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['specialist_id'], $_POST['rating'])) {
            $userId = isset($_POST['user_id']) ? $_POST['user_id'] : $_SESSION['user_id']; // Assuming user_id is passed in the POST data
            $data = [
                'specialist_id' => $_POST['specialist_id'],
                'user_id' => $userId,
                'rating' => $_POST['rating'],
                'comment' => $_POST['comment'] ?? '',
                'second_opinion_id' => $_POST['second_opinion_id'] ?? null
            ];

            try {
                $this->model->create($data);
                $this->jsonResponse(true, 'Review created successfully');
            } catch (mysqli_sql_exception $e) {
                $this->errorResponse(400, "Error creating review: " . $e->getMessage());
            }
        } else {
            $this->errorResponse(400, 'Missing required fields: specialist_id, user_id, rating');
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
            if (!$id || !isset($_POST['rating'])) {
                return $this->jsonResponse(false, 'Missing required fields');
            }

            $data = [
                'rating' => $_POST['rating'],
                'comment' => $_POST['comment'] ?? ''
            ];

            try {
                $this->model->update($id, $data);
                $this->jsonResponse(true, 'Review updated successfully');
            } catch (mysqli_sql_exception $e) {
                $this->errorResponse(400, "Error updating review: " . $e->getMessage());
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
                $this->jsonResponse(true, 'Review deleted successfully');
            } catch (mysqli_sql_exception $e) {
                $this->errorResponse(400, "Error deleting review: " . $e->getMessage());
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
