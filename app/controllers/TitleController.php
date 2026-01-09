<?php

require_once __DIR__ . '/../models/TitleModel.php';

class TitleController
{
    private $model;

    public function __construct()
    {
        $this->model = new TitleModel();
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
        $items = $this->model->getAll();
        $this->jsonResponse(true, '', $items);
    }

    public function showById($params)
    {
        $item = $this->model->getById($params['id'] ?? 0);
        $this->jsonResponse((bool) $item, $item ? '' : 'Not found', $item);
    }

    public function create()
    {
        $data = $this->getJsonInput();
        $result = $this->model->create($data);
        $this->jsonResponse($result, $result ? 'Created successfully' : 'Error creating');
    }

    public function update($params)
    {
        $data = $this->getJsonInput();
        $result = $this->model->update($params['id'] ?? 0, $data);
        $this->jsonResponse($result, $result ? 'Updated successfully' : 'Error updating');
    }



public function replaceAllIds()
{
    $result = $this->model->replaceAllIdsWithUUID();

    $success = $result['status'] ?? false;
    $message = $result['message'] ?? 'Unexpected error';
    $data = $result['details'] ?? [];

    $this->jsonResponse($success, $message, $data);
}

public function delete($params)
{
    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $id = $params['id'] ?? null;
        try {
            $result = $this->model->delete($id);
            return $this->jsonResponse(true, "Título eliminado correctamente.");
        } catch (mysqli_sql_exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        } catch (Exception $e) {
            return $this->jsonResponse(false, "Error desconocido: " . $e->getMessage());
        }
    }
}


  public function export($userId)
    {
        try {
            $this->model->exportAllTitlesToCSV();
        } catch (Exception $e) {
            echo json_encode(['value' => false, 'message' => $e->getMessage()]);
        }
    }

}
