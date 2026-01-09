<?php

require_once __DIR__ . '/../models/SpecialtyModel.php';

class SpecialtyController
{
    private $model;

    public function __construct()
    {
        $this->model = new SpecialtyModel();
    }

    private function getJsonInput(): array
    {
        return json_decode(file_get_contents("php://input"), true) ?? [];
    }

    private function jsonResponse(bool $value, string $message = '', $data = null)
    {
        header('Content-Type: application/json');
        echo json_encode([
            'value' => $value,
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }

    public function showAll()
    {
        try {
            $items = $this->model->getAll();
            $this->jsonResponse(true, '', $items);
        } catch (Exception $e) {
            $this->jsonResponse(false, $e->getMessage());
        }
    }

    public function showById($params)
    {
        try {
            $item = $this->model->getById($params['id'] ?? 0);
            $this->jsonResponse((bool) $item, $item ? '' : 'Not found', $item);
        } catch (Exception $e) {
            $this->jsonResponse(false, $e->getMessage());
        }
    }

    public function create()
    {
        try {
            $data = $this->getJsonInput();
            $result = $this->model->create($data);
            $this->jsonResponse($result, $result ? 'Created successfully' : 'Error creating');
        } catch (Exception $e) {
            $this->jsonResponse(false, $e->getMessage());
        }
    }

    public function update($params)
    {
        try {
            $data = $this->getJsonInput();
            $result = $this->model->update($params['id'] ?? 0, $data);
            $this->jsonResponse($result, $result ? 'Updated successfully' : 'Error updating');
        } catch (Exception $e) {
            $this->jsonResponse(false, $e->getMessage());
        }
    }

    public function delete($params)
    {
        try {
            $result = $this->model->delete($params['id'] ?? 0);
            $this->jsonResponse($result, $result ? 'Deleted successfully' : 'Error deleting');
        } catch (Exception $e) {
            $this->jsonResponse(false, $e->getMessage());
        }
    }
     public function export($userId)
    {
        try {
            $this->model->exportAllSpecialtiesToCSV();
        } catch (Exception $e) {
            echo json_encode(['value' => false, 'message' => $e->getMessage()]);
        }
    }
}
