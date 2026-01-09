<?php

require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/BiomarkerModel.php';

class UserController
{
    private $userModel;
    private $biomarkerModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->biomarkerModel = new BiomarkerModel();
    }
    /**
     * Guarda la imagen de perfil subida para el usuario $userId
     * en /uploads/users y devuelve la ruta relativa, o null si no hay archivo.
     */
    /**
     * Guarda la imagen de perfil subida para el usuario $userId
     * en /uploads/users, la convierte a JPEG y devuelve la ruta relativa.
     */
    private function handleProfileImageUpload($userId): ?string
    {
        // 1) Validar que venga un archivo sin errores
        if (empty($_FILES['profile_image']) || $_FILES['profile_image']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $tmpPath = $_FILES['profile_image']['tmp_name'];
        $mimeInfo = getimagesize($tmpPath);
        if ($mimeInfo === false) {
            throw new RuntimeException("El archivo no es una imagen válida.");
        }

        // 2) Crear recurso GD según el tipo MIME
        switch ($mimeInfo['mime']) {
            case 'image/jpeg':
                $srcImg = imagecreatefromjpeg($tmpPath);
                break;
            case 'image/png':
                $srcImg = imagecreatefrompng($tmpPath);
                // PNG puede tener transparencia: rellenar fondo blanco
                $width = imagesx($srcImg);
                $height = imagesy($srcImg);
                $bg = imagecreatetruecolor($width, $height);
                $white = imagecolorallocate($bg, 255, 255, 255);
                imagefill($bg, 0, 0, $white);
                imagecopy($bg, $srcImg, 0, 0, 0, 0, $width, $height);
                imagedestroy($srcImg);
                $srcImg = $bg;
                break;
            case 'image/gif':
                $srcImg = imagecreatefromgif($tmpPath);
                break;
            default:
                throw new RuntimeException("Formato de imagen no soportado ({$mimeInfo['mime']}).");
        }

        // 3) Directorio de destino
        $uploadDir = PROJECT_ROOT . '/uploads/users/';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
            throw new RuntimeException("No se pudo crear el directorio de uploads.");
        }

        // 4) Nombre fijo con extensión .jpg
        $filename = "user_{$userId}.jpg";
        $destination = $uploadDir . $filename;

        // 5) Guardar como JPEG (calidad 85)
        if (!imagejpeg($srcImg, $destination, 85)) {
            imagedestroy($srcImg);
            throw new RuntimeException("Error al generar el JPEG.");
        }

        imagedestroy($srcImg);

        // 6) Devolver ruta relativa para guardar en BD
        return "/uploads/users/{$filename}";
    }

    public function update_system_type_session_user()
    {
        $method = $_SERVER['REQUEST_METHOD'];

        // Obtener el idioma desde la sesión, por defecto 'EN' si no está definido
        $lang = strtoupper($_SESSION['idioma'] ?? 'EN');

        if ($method === 'PUT' || ($method === 'POST' && ($_POST['_method'] ?? '') === 'PUT')) {
            $userId = $_SESSION['user_id'] ?? null;

            if (!$userId) {
                $message = $lang === 'ES' ? "Usuario no autenticado." : "User not authenticated.";
                return $this->errorResponse(401, $message);
            }

            $putData = [];
            if ($method === 'POST') {
                $putData = $_POST;
            } else {
                parse_str(file_get_contents("php://input"), $putData);
            }

            $systemType = $putData['system_type'] ?? null;

            if (!$systemType) {
                $message = $lang === 'ES' ? "Falta el valor de system_type." : "Missing system_type value.";
                return $this->errorResponse(400, $message);
            }

            try {
                $result = $this->userModel->updateSystemTypeByUserId($userId, $systemType);

                $successMessage = $lang === 'ES'
                    ? "Sistema de unidades actualizado correctamente."
                    : "System type updated successfully.";

                $errorMessage = $lang === 'ES'
                    ? "Error al actualizar el sistema de unidades."
                    : "Error updating system type.";

                return $this->jsonResponse(
                    true,
                    $result ? $successMessage : $errorMessage,
                    $result
                );
            } catch (\Exception $e) {
                $message = $lang === 'ES'
                    ? "Error al actualizar el sistema de unidades: " . $e->getMessage()
                    : "Error updating system type: " . $e->getMessage();
                return $this->errorResponse(400, $message);
            }
        }

        $message = $lang === 'ES'
            ? "Método no permitido. Se requiere PUT."
            : "Method not allowed. PUT required.";
        return $this->errorResponse(405, $message);
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

    private function errorResponse($http_code, $message)
    {
        http_response_code($http_code);
        $this->jsonResponse(false, $message);
    }

    public function showAll()
    {
        try {
            $users = $this->userModel->getAll();
            return $this->jsonResponse(true, '', $users);
        } catch (mysqli_sql_exception $e) {
            return $this->errorResponse(400, "Error al listar usuarios: " . $e->getMessage());
        }
    }
    public function setLanguage()
    {
        try {
            $lang = $_GET['lang'] ?? null;

            if (!$lang) {
                return $this->errorResponse(400, "Parámetro 'lang' requerido.");
            }

            $success = $this->userModel->setIdioma($lang);

            if ($success) {
                return $this->jsonResponse(true, "Idioma actualizado correctamente a $lang.");
            } else {
                return $this->errorResponse(400, "No se pudo actualizar el idioma.");
            }
        } catch (\Exception $e) {
            return $this->errorResponse(500, "Error al establecer el idioma: " . $e->getMessage());
        }
    }

    public function getAllCountries()
    {
        try {
            $countries = $this->userModel->getAllCountries();

            if (!empty($countries)) {
                return $this->jsonResponse(true, "Lista de países obtenida correctamente.", $countries);
            } else {
                return $this->errorResponse(404, "No se encontraron países.");
            }
        } catch (\Exception $e) {
            return $this->errorResponse(500, "Error al obtener países: " . $e->getMessage());
        }
    }




    public function showById($params)
    {
        $id = $params['id'] ?? null;
        try {
            $user = $this->userModel->getById($id);
            if ($user) {
                return $this->jsonResponse(true, '', $user);
            } else {
                return $this->jsonResponse(false, "Usuario no encontrado");
            }
        } catch (mysqli_sql_exception $e) {
            return $this->errorResponse(400, "Error al obtener usuario: " . $e->getMessage());
        }
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            try {
                $result = $this->userModel->create($data);
                return $this->jsonResponse(
                    (bool) $result,
                    $result ? "Usuario guardado correctamente" : "Error al guardar usuario"
                );
            } catch (mysqli_sql_exception $e) {
                return $this->errorResponse(400, "Error al guardar usuario: " . $e->getMessage());
            }
        } else {
            return $this->errorResponse(405, "Método no permitido. Se requiere POST.");
        }
    }
    public function updateStatus($params)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $userId = $params['id'] ?? $data['user_id'] ?? null;

            if (!$userId || !isset($data['status'])) {
                return $this->errorResponse(400, "Missing user ID or status value.");
            }

            $data['user_id'] = $userId;

            try {
                $result = $this->userModel->updateStatus($data);
                return $result
                    ? $this->jsonResponse(true, "Status updated successfully", $data)
                    : $this->errorResponse(400, "Failed to update status.");
            } catch (mysqli_sql_exception $e) {
                return $this->errorResponse(500, "Database error: " . $e->getMessage());
            }
        }

        return $this->errorResponse(405, "Method Not Allowed. Use POST.");
    }


    public function update($params)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $params['id'] ?? null;
            if (!$id) {
                return $this->errorResponse(400, "ID de usuario requerido.");
            }
            // parse_str(file_get_contents("php://input"), $putData);
            $data = $_POST;


            try {
                $result = $this->userModel->update($id, $data);
                return $this->jsonResponse(true, $result ? "Usuario actualizado correctamente" : "Error al actualizar usuario", $result);
            } catch (mysqli_sql_exception $e) {
                return $this->errorResponse(400, "Error al actualizar usuario: " . $e->getMessage());
            }
        } else {
            return $this->errorResponse(405, "Método no permitido. Se requiere PUT.");
        }
    }

public function update_profile($params)
{
    $method = $_SERVER['REQUEST_METHOD'];
    $idioma = strtoupper($_SESSION['idioma'] ?? $_SESSION['lang'] ?? 'EN');
    $isES = $idioma === 'ES';

    if ($method === 'PUT' || ($method === 'POST' && ($_POST['_method'] ?? '') === 'PUT')) {
        $id = $params['id'] ?? null;
        $putData = [];

        if ($method === 'POST') {
            $putData = $_POST;
        } else {
            parse_str(file_get_contents("php://input"), $putData);
        }

        try {
            // 1) Manejar la subida de imagen de perfil
            $imagePath = $this->handleProfileImageUpload($id);
            if ($imagePath !== null) {
                $putData['profile_image'] = $imagePath;
                $_SESSION['user_image'] = $imagePath;
            }

            // 2) Actualizar usuario
            $result = $this->userModel->updateProfile($id, $putData);

            if ($result) {
                $user = $this->userModel->getById($id);

                // Actualizar datos de sesión
                $_SESSION['roles_user'] = "User";
                $_SESSION['sex_biological'] = $user['sex_biological'];
                $_SESSION['birthday'] = $user['birthday'];
                $_SESSION['system_type'] = $user['system_type'];
                $_SESSION['timezone'] = $user['timezone'];
                $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            }

            return $this->jsonResponse(
                true,
                $isES
                    ? ($result ? "Usuario actualizado correctamente." : "Error al actualizar usuario.")
                    : ($result ? "User updated successfully." : "Error updating user."),
                $result
            );
        } catch (\Exception $e) {
            $msg = $isES
                ? "Error al actualizar usuario: " . $e->getMessage()
                : "Error updating user: " . $e->getMessage();
            return $this->errorResponse(400, $msg);
        }
    }

    return $this->errorResponse(
        405,
        $isES
            ? "Método no permitido. Se requiere una solicitud PUT."
            : "Method not allowed. A PUT request is required."
    );
}


    public function delete($params)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            return $this->errorResponse(405, "Method Not Allowed. DELETE required.");
        }

        $id = $params['id'] ?? null;
        if (!$id) {
            return $this->errorResponse(400, "Missing ID parameter.");
        }

        // Traducciones según idioma
        $lang = strtoupper($_SESSION['idioma'] ?? 'EN');
        $archivo_idioma = PROJECT_ROOT . "/lang/{$lang}.php";
        $traducciones = file_exists($archivo_idioma) ? include $archivo_idioma : [];

        $msgSuccess = $traducciones['user_deleted_successfully'] ?? "User deleted successfully";
        $msgError = $traducciones['user_delete_error'] ?? "Error deleting user";

        try {
            $deleted = $this->userModel->delete($id);

            if ($deleted) {
                return $this->jsonResponse(true, $msgSuccess);
            } else {
                return $this->jsonResponse(false, $msgError);
            }
        } catch (mysqli_sql_exception $e) {
            $errorMsg = $e->getMessage();

            // Dependencias encontradas, no es un error crítico (responder con 200)
            if (stripos($errorMsg, 'related records exist') !== false) {
                return $this->jsonResponse(false, "$msgError: $errorMsg");
            }

            // Otro tipo de error (responder con 400)
            return $this->errorResponse(400, "$msgError: $errorMsg");
        }
    }



    public function countUsers()
    {
        try {
            $total = $this->userModel->countUsers();
            return $this->jsonResponse(true, '', ['total' => $total]);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Error al contar usuarios: ' . $e->getMessage());
        }
    }

    public function getUserBiomarkers($params)
    {
        $userId = $params['id'] ?? null;
        try {
            $biomarkers = $this->biomarkerModel->getUniqueUserBiomarkerValues($userId);
            if ($biomarkers) {
                return $this->jsonResponse(true, '', $biomarkers);
            } else {
                return $this->jsonResponse(false, "No se encontraron biomarcadores para el usuario");
            }
        } catch (mysqli_sql_exception $e) {
            return $this->errorResponse(400, "Error al obtener biomarcadores: " . $e->getMessage());
        }
    }

    public function getSessionUserData($params)
    {
        $userId = $params['id'] ?? null;
        try {
            $result = $this->userModel->getSessionUserData($userId);
            return $this->jsonResponse(true, '', $result);
        } catch (mysqli_sql_exception $e) {
            return $this->errorResponse(400, "Error al obtener datos del usuario: " . $e->getMessage());
        }
    }

    protected function view($view, $data = [])
    {
        $viewPath = __DIR__ . '/../Views/' . $view . '.php';
        if (file_exists($viewPath)) {
            extract($data);
            include $viewPath;
        } else {
            $this->errorResponse(500, "Error interno del servidor: Vista no encontrada.");
        }
    }



}
