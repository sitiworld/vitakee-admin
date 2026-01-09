<?php

require_once __DIR__ . '/../models/SecurityQuestionsModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/SpecialistModel.php';
require_once __DIR__ . '/../models/AdministratorModel.php';


class SecurityQuestionController
{
    private $securityQuestionsModel;
    private $userModel;

    public function __construct()
    {
        $this->securityQuestionsModel = new SecurityQuestionsModel();
        $this->userModel = new UserModel();
    }

    public function getByUser()
    {
        try {
            $user_id = $_SESSION['user_id'] ?? null;
            $questions = $this->securityQuestionsModel->getSecurityQuestionsByUser($user_id);

            $this->jsonResponse($questions['value'], $questions['message'], isset($questions['data']) ? $questions['data'] : null);
        } catch (mysqli_sql_exception $e) {
            $this->errorResponse(400, "Error al obtener preguntas de seguridad: " . $e->getMessage());
        } catch (Exception $e) {
            $this->errorResponse(400, $e->getMessage());
        }
    }

 public function getByUserReset()
{
    try {
        $user_id = $_POST['user_id'] ?? null;
        $user_type = $_POST['user_type'] ?? null;

        if (!$user_id || !$user_type) {
            throw new Exception("Missing required parameters: user_id or user_type.");
        }

        $questions = $this->securityQuestionsModel->getSecurityQuestionsByUserReset($user_id, $user_type);

        $this->jsonResponse(
            $questions['value'],
            $questions['message'],
            $questions['data'] ?? null
        );
    } catch (mysqli_sql_exception $e) {
        $this->errorResponse(400, "Error al obtener preguntas de seguridad: " . $e->getMessage());
    } catch (Exception $e) {
        $this->errorResponse(400, $e->getMessage());
    }
}


    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // $data = $_POST;
            try {

                $jsonData = $this->getJsonInput(); // Obtén los da


                if (isset($_SESSION['user_id'])) {
                    $jsonData['user_id'] = $_SESSION['user_id'];
                }
                $result = $this->securityQuestionsModel->create($jsonData);
                return $this->jsonResponse($result['value'], $result['message']);
            } catch (mysqli_sql_exception $e) {
                return $this->errorResponse(400, "Error al guardar preguntas de seguridad: " . $e->getMessage());
            }
        } else {
            return $this->errorResponse(405, "Método no permitido. Se requiere POST.");
        }
    }

    public function update($parametros)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            $id = $parametros['id'] ?? null;
            $data = $this->getJsonInput();  // Obtén los datos JSON correctamente

            try {
                // Llamamos al método update del modelo con el id y los datos recibidos
                $result = $this->securityQuestionsModel->update($id, $data);
                return $this->jsonResponse(
                    $result['status'] === 'success',
                    $result['message'] ?? '',
                    []
                );
            } catch (mysqli_sql_exception $e) {
                // En caso de error, enviamos el mensaje de la excepción
                $this->errorResponse(400, "Error al actualizar preguntas de seguridad: " . $e->getMessage());
            }
        } else {
            // Si no es un PUT, enviamos el error de método no permitido
            $this->errorResponse(405, "Método no permitido. Se requiere PUT.");
        }
    }




    public function delete($parametros)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            $id = $parametros['id'] ?? null;

            try {
                $result = $this->securityQuestionsModel->deleteByUserId($id);

                $message = $result
                    ? "Preguntas de seguridad eliminadas correctamente"
                    : "Error al eliminar preguntas de seguridad";

                $this->jsonResponse($result, $message);
            } catch (mysqli_sql_exception $e) {
                $this->errorResponse(400, "Error al eliminar preguntas de seguridad: " . $e->getMessage());
            }
        } else {
            $this->errorResponse(405, "Método no permitido. Se requiere DELETE.");
        }
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
        $this->jsonResponse(false, $message);
    }
}
