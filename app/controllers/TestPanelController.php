<?php

require_once __DIR__ . '/../models/TestPanelModel.php';


class TestPanelController
{
    private $testPanelModel;

    public function __construct()
    {
        $this->testPanelModel = new TestPanelModel();
    }

    private function getJsonInput(): array
    {
        $input = file_get_contents("php://input");
        return json_decode($input, true) ?? [];
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

    public function getAll()
    {
        try {
            $testPanels = $this->testPanelModel->getAll();
            return $this->jsonResponse(true, '', $testPanels);
        } catch (mysqli_sql_exception $e) {
            return $this->jsonResponse(false, "Error al listar los paneles de prueba: " . $e->getMessage());
        }
    }

    public function getAllUserPanelsBiomarkerRecords()
    {

        try {
            $panels = $this->testPanelModel->getAll();
            // OBTENER BIOMARCADORES Y REGISTROS PARA CADA PANEL POR USUARIO
            $records = [];
            $user_id = $_SESSION['user_id'] ?? 0;
            foreach ($panels as $panel) {
                $panel_id = $panel['panel_id'];
                $biomarkers = $this->testPanelModel->getBiomarkersByPanelId($panel_id);
                $userRecords = $this->testPanelModel->getUserRecordsByPanelId($user_id, $panel_id);
                $records[] = [
                    'panel' => $panel,
                    'biomarkers' => $biomarkers,
                    'user_records' => $userRecords
                ];
            }


            return $this->jsonResponse(true, '', $records);
        } catch (mysqli_sql_exception $e) {
            return $this->jsonResponse(false, "Error al listar los registros de biomarcadores de paneles de usuario: " . $e->getMessage());
        }

    }

    public function getAllUserPanelsBiomarkerRecords2()
    {

        try {
            $panels = $this->testPanelModel->getAll();
            // OBTENER BIOMARCADORES Y REGISTROS PARA CADA PANEL POR USUARIO
            $records = [];
            $user_id = $_SESSION['user_id'] ?? 0;
            foreach ($panels as $panel) {
                $panel_id = $panel['panel_id'];
                $biomarkers = $this->testPanelModel->getBiomarkersByPanelId2($panel_id);

            }


            return $this->jsonResponse(true, '', $biomarkers);
        } catch (mysqli_sql_exception $e) {
            return $this->jsonResponse(false, "Error al listar los registros de biomarcadores de paneles de usuario: " . $e->getMessage());
        }

    }
    public function getUserRecordCounts($params)
    {
        try {
            $user_id = $params['user_id'];

            $user_id = $user_id; // Asegurar que es un entero
            $counts = $this->testPanelModel->countUserRecordsByPanelName($user_id);
            return $this->jsonResponse(true, '', $counts);
        } catch (mysqli_sql_exception $e) {
            return $this->jsonResponse(false, "Error al contar los registros por panel para el usuario: " . $e->getMessage());
        }
    }

    public function getUserPanelRecords($params)
    {
        try {
            $user_id = $params['user_id'] ?? 0;
            $panel_id = $params['panel_id'] ?? 0;

            $records = $this->testPanelModel->getUserRecordsByPanelId($user_id, $panel_id);
            return $this->jsonResponse(true, '', $records);
        } catch (Exception $e) {
            return $this->jsonResponse(false, "Error al obtener registros: " . $e->getMessage());
        }
    }
    public function getPanelRecords($params)
    {
        try {
            $panel_id = $params['panel_id'] ?? null;
            if (!$panel_id || !is_string($panel_id)) {
                throw new Exception("panel_id inválido.");
            }

            $records = $this->testPanelModel->getAllRecordsByPanelId($panel_id);



            return $this->jsonResponse(true, '', $records);
        } catch (Exception $e) {
            return $this->jsonResponse(false, "Error al obtener registros: " . $e->getMessage());
        }
    }
    public function getPanelBiomarkers($params)
    {
        try {
            $panel_id = $params['panel_id'] ?? null;
            if (!$panel_id || !is_string($panel_id)) {
                throw new Exception("panel_id inválido.");
            }

            $biomarkers = $this->testPanelModel->getBiomarkersByPanelId($panel_id);
            return $this->jsonResponse(true, '', $biomarkers);
        } catch (Exception $e) {
            return $this->jsonResponse(false, "Error al obtener biomarcadores: " . $e->getMessage());
        }
    }



    public function getById($params)
    {
        $id = $params['id'] ?? null;
        try {
            $testPanel = $this->testPanelModel->getById($id);
            if ($testPanel) {
                return $this->jsonResponse(true, '', $testPanel);
            } else {
                return $this->jsonResponse(false, "Panel de prueba no encontrado");
            }
        } catch (mysqli_sql_exception $e) {
            return $this->jsonResponse(false, "Error al obtener el panel de prueba: " . $e->getMessage());
        }
    }

    public function exportCSVPanels()
    {

        try {
            // Llamar al modelo para generar el CSV directamente
            $this->testPanelModel->exportAllPanelsToCSV();
        } catch (Exception $e) {
            $this->errorResponse(500, "Error exporting data: " . $e->getMessage());
        }
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->getJsonInput();
            try {
                $result = $this->testPanelModel->create($data);
                return $this->jsonResponse(
                    (bool) $result,
                    $result ? "Panel de prueba guardado correctamente" : "Error al guardar el panel de prueba"
                );
            } catch (mysqli_sql_exception $e) {
                return $this->jsonResponse(false, "Error al guardar el panel de prueba: " . $e->getMessage());
            }
        } else {
            return $this->jsonResponse(false, "Método no permitido. Se requiere POST.");
        }
    }

    public function update($params)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            $id = $params['id'] ?? null;
            $data = $this->getJsonInput();
            try {
                $result = $this->testPanelModel->update($id, $data);
                return $this->jsonResponse(
                    (bool) $result,
                    $result ? "Panel de prueba actualizado correctamente" : "Error al actualizar el panel de prueba"
                );
            } catch (mysqli_sql_exception $e) {
                return $this->jsonResponse(false, "Error al actualizar el panel de prueba: " . $e->getMessage());
            }
        } else {
            return $this->jsonResponse(false, "Método no permitido. Se requiere PUT.");
        }
    }

    public function delete($params)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            $id = $params['id'] ?? null;
            try {
                $result = $this->testPanelModel->delete($id);
                return $this->jsonResponse(
                    (bool) $result,
                    $result ? "Panel de prueba eliminado correctamente" : "Error al eliminar el panel de prueba"
                );
            } catch (mysqli_sql_exception $e) {
                return $this->jsonResponse(false, "Error al eliminar el panel de prueba: " . $e->getMessage());
            }
        } else {
            return $this->jsonResponse(false, "Método no permitido. Se requiere DELETE.");
        }
    }

    protected function view($vista, $data = [])
    {
        $rutaVista = __DIR__ . '/../Views/' . $vista . '.php';
        if (file_exists($rutaVista)) {
            extract($data);
            include $rutaVista;
        } else {
            $this->errorResponse(500, "Error interno del servidor: Vista no encontrada.");
        }
    }

    private function errorResponse($http_code, $message)
    {
        http_response_code($http_code);
        echo json_encode([
            'value' => false,
            'message' => $message
        ]);
        exit;
    }

}



