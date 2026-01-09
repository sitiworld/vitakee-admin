<?php

require_once __DIR__ . '/../models/BackupModel.php';

class BackupController
{
    private $backupModel;

    public function __construct()
    {
        $this->backupModel = new BackupModel();
    }

    public function show()
    {
        try {
            $backups = $this->backupModel->obtenerTodos();
            $this->jsonResponse(true, 'Lista de respaldos obtenida', $backups);
        } catch (mysqli_sql_exception $e) {
            $this->errorResponse(400, "Error al listar respaldos: " . $e->getMessage());
        }
    }

    public function showId($parametros)
    {
        $id = $parametros['id'] ?? null;
        try {
            $backup = $this->backupModel->obtenerPorId($id);
            if ($backup) {
                $this->jsonResponse(true, 'Respaldo encontrado', $backup);
            } else {
                $this->errorResponse(404, "Respaldo no encontrado");
            }
        } catch (mysqli_sql_exception $e) {
            $this->errorResponse(400, "Error al obtener respaldo: " . $e->getMessage());
        }
    }

 public function create()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $result = $this->backupModel->crearBackup();

            if ($result['status'] === 'success') {
                return $this->jsonResponse(true, $result['message']);
            } else {
                return $this->errorResponse(500, $result['message']);
            }
        } catch (mysqli_sql_exception $e) {
            return $this->errorResponse(400, "Error al crear respaldo: " . $e->getMessage());
        }
    } else {
        return $this->errorResponse(405, "Método no permitido. Se requiere POST.");
    }
}


    public function update($parametros)
    {
        $id = $parametros['id'] ?? null;
        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            parse_str(file_get_contents("php://input"), $datosPUT);
            try {
                $result = $this->backupModel->update($id, $datosPUT);
                if ($result) {
                    $this->jsonResponse(true, "Respaldo actualizado correctamente");
                } else {
                    $this->errorResponse(500, "Error al actualizar respaldo");
                }
            } catch (mysqli_sql_exception $e) {
                $this->errorResponse(400, "Error al actualizar respaldo: " . $e->getMessage());
            }
        } else {
            $this->errorResponse(405, "Método no permitido. Se requiere PUT.");
        }
    }

    public function delete($parametros)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            $id = $parametros['id'] ?? null;
            try {
                $result = $this->backupModel->eliminar($id);
                if ($result) {
                    $this->jsonResponse(true, "Respaldo eliminado correctamente");
                } else {
                    $this->errorResponse(500, "Error al eliminar respaldo");
                }
            } catch (mysqli_sql_exception $e) {
                $this->errorResponse(400, "Error al eliminar respaldo: " . $e->getMessage());
            }
        } else {
            $this->errorResponse(405, "Método no permitido. Se requiere DELETE.");
        }
    }

    public function restore($parametros)
    {
        $id = $parametros['id'] ?? null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $result = $this->backupModel->restaurarBackup($id);
                if ($result['status'] === 'success') {
                    $this->jsonResponse(true, $result['message']);
                } else {
                    $this->errorResponse(500, $result['message']);
                }
            } catch (mysqli_sql_exception $e) {
                $this->errorResponse(400, "Error al restaurar respaldo: " . $e->getMessage());
            }
        } else {
            $this->errorResponse(405, "Método no permitido. Se requiere POST.");
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

    private function getJsonInput(): array
    {
        $input = file_get_contents("php://input");
        return json_decode($input, true) ?? [];
    }

    private function jsonResponse($value, $message = '', $data = null)
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
}
