<?php

require_once __DIR__ . '/../models/SpecialistModel.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once PROJECT_ROOT . '/vendor/phpmailer/phpmailer/src/Exception.php';
require_once PROJECT_ROOT . '/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once PROJECT_ROOT . '/vendor/phpmailer/phpmailer/src/SMTP.php';

class SpecialistController
{
    private $specialistModel;

    public function __construct()
    {
        $this->specialistModel = new SpecialistModel();
    }

    private function getJsonInput(): array
    {
        $input = file_get_contents("php://input");
        return json_decode($input, true) ?? [];
    }

    private function jsonResponse(bool $value, string $message = '', $data = null)
    {
        header('Content-Type: application/json');
        echo json_encode([
            'value' => $value,
            'message' => $message,
            'data' => is_array($data) ? $data : ($data !== null ? [$data] : [])
        ]);
        exit;
    }

    private function errorResponse($http_code, $message)
    {
        http_response_code($http_code);
        $this->jsonResponse(false, $message);
    }
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
        $uploadDir = PROJECT_ROOT . '/uploads/specialist/';
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
        return "uploads/specialist/{$filename}";
    }
    // SpecialistController.php

    // Helper para extraer el id venga como venga
    private function extractRouteId($arg): ?string
    {
        if (is_string($arg) && $arg !== '')
            return $arg;

        if (is_array($arg)) {
            if (isset($arg['id']) && $arg['id'] !== '')
                return (string) $arg['id'];
            // Si viene como array numerado: ['abc-uuid']
            $vals = array_values($arg);
            if (!empty($vals[0]))
                return (string) $vals[0];
        }

        // Fallback por querystring: ?id=...
        if (!empty($_GET['id']))
            return (string) $_GET['id'];

        return null;
    }

    // NO tipar el parámetro para que no lance TypeError si llega array
    public function showByIdWithFreeCheck($routeParam = null)
    {
        try {
            $specialistId = $this->extractRouteId($routeParam);
            if (!$specialistId) {
                return $this->errorResponse(400, "Missing specialist id");
            }

            require_once __DIR__ . '/../models/SecondOpinionRequestsModel.php';
            $secondModel = new SecondOpinionRequestsModel();

            // 1) Obtener especialista
            $spec = $this->specialistModel->getById($specialistId);
            if (!$spec) {
                return $this->errorResponse(404, "Specialist not found");
            }

            $maxFree = (int) ($spec['max_free_consults_per_month'] ?? 0);

            // 2) Contar gratuitas válidas del mes actual
            $usedThisMonth = $secondModel->countFreeThisMonthBySpecialist($specialistId);

            // 3) Decidir si mostrar pricing gratis
            $allowFreePricing = ($maxFree > 0 && $usedThisMonth < $maxFree);

            // 4) Traer especialista con relaciones y pricing filtrado
            $full = $this->specialistModel->getOneWithRelations($specialistId, $allowFreePricing);

            // 5) Extras para UI
            $full['free_quota'] = [
                'max_free_consults_per_month' => $maxFree,
                'used_in_current_month' => $usedThisMonth,
                'free_pricing_visible' => $allowFreePricing
            ];

            return $this->jsonResponse(true, '', $full);
        } catch (\mysqli_sql_exception $e) {
            return $this->errorResponse(400, "Error fetching specialist: " . $e->getMessage());
        }
    }




    public function showAll()
    {
        try {
            $specialists = $this->specialistModel->getAll();
            return $this->jsonResponse(true, '', $specialists);
        } catch (mysqli_sql_exception $e) {
            return $this->errorResponse(400, "Error fetching specialists: " . $e->getMessage());
        }
    }

    public function showById($params)
    {
        $id = $params['id'] ?? null;
        try {
            $specialist = $this->specialistModel->getById($id);
            return $specialist
                ? $this->jsonResponse(true, '', $specialist)
                : $this->jsonResponse(false, "Specialist not found");
        } catch (mysqli_sql_exception $e) {
            return $this->errorResponse(400, "Error fetching specialist: " . $e->getMessage());
        }
    }

    public function showByIdSecondOpinion($params)
    {
        $id = $params['id'] ?? null;
        try {
            $specialist = $this->specialistModel->showByIdSecondOpinion($id);
            return $specialist
                ? $this->jsonResponse(true, '', $specialist)
                : $this->jsonResponse(false, "Specialist not found");
        } catch (mysqli_sql_exception $e) {
            return $this->errorResponse(400, "Error fetching specialist: " . $e->getMessage());
        }
    }
    // Controller: SpecialistController.php
    public function showCards()
    {
        try {
            // Método del modelo que ya construiste con los campos mínimos para la card
            $cards = $this->specialistModel->getAllForCards();
            return $this->jsonResponse(true, '', $cards);
        } catch (mysqli_sql_exception $e) {
            return $this->errorResponse(400, "Error fetching specialist cards: " . $e->getMessage());
        }
    }
    // ==================== Controller ====================
    public function showCardBySession()
    {
        try {
            $sessionUserId = $_SESSION['user_id'] ?? null; // este es el specialist_id
            if (!$sessionUserId) {
                return $this->errorResponse(401, "No active session or user_id not found");
            }

            $card = $this->specialistModel->getCardById($sessionUserId);
            return $this->jsonResponse(true, '', $card);
        } catch (\mysqli_sql_exception $e) {
            return $this->errorResponse(400, "Error fetching specialist card: " . $e->getMessage());
        }
    }
    public function searchByName()
    {
        // Recibe ?q=... vía GET; soporta opcionales: &limit=, &offset=, &order=rating_cost
        $q = $_GET['q'] ?? '';

        // Opciones opcionales para paginación/orden
        $opts = [
            'limit' => $_GET['limit'] ?? null,
            'offset' => $_GET['offset'] ?? null,
            'order' => $_GET['order'] ?? null, // 'rating_cost' o null
        ];

        try {
            $rows = $this->specialistModel->searchByName($q, $opts);
            return $this->jsonResponse(true, '', $rows);
        } catch (\mysqli_sql_exception $e) {
            return $this->errorResponse(400, "Error searching specialists by name: " . $e->getMessage());
        } catch (\Throwable $e) {
            return $this->errorResponse(500, "Unexpected error: " . $e->getMessage());
        }
    }

    public function searchFilters()
    {
        // payload puede traer: q (o name), verified, specialty_ids[], languages[],
        // availability{date|weekdays[]|time_start|time_end}, min_cost, min_rating,
        // min_evaluations, min_consultations, order, limit, offset
        $payload = json_decode(file_get_contents('php://input'), true) ?? [];

        // Alias amistoso: si viene 'name', úsalo como 'q'
        if (!empty($payload['name']) && empty($payload['q'])) {
            $payload['q'] = $payload['name'];
        }

        try {
            $rows = $this->specialistModel->searchByFilters($payload);
            return $this->jsonResponse(true, '', $rows);
        } catch (\mysqli_sql_exception $e) {
            return $this->errorResponse(400, "Error searching specialists: " . $e->getMessage());
        } catch (\Throwable $e) {
            return $this->errorResponse(500, "Unexpected error: " . $e->getMessage());
        }
    }


    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;

            // Cargar idioma desde archivos
            $lang = strtoupper($_SESSION['idioma'] ?? 'EN');
            $langPath = PROJECT_ROOT . "/lang/{$lang}.php";
            $translations = file_exists($langPath) ? include $langPath : include PROJECT_ROOT . "/lang/EN.php";

            try {
                $result = $this->specialistModel->create($data);
                return $this->jsonResponse(
                    (bool) $result,
                    $result ? $translations['specialist_created_successfully'] : $translations['error_creating_specialist']
                );
            } catch (mysqli_sql_exception $e) {
                return $this->errorResponse(400, $translations['error_creating_specialist'] . ' ' . $e->getMessage());
            }
        } else {
            $lang = strtoupper($_SESSION['idioma'] ?? 'EN');
            $langPath = PROJECT_ROOT . "/lang/{$lang}.php";
            $translations = file_exists($langPath) ? include $langPath : include PROJECT_ROOT . "/lang/EN.php";

            return $this->errorResponse(405, $translations['method_not_allowed']);
        }
    }

    public function checkEmail()
    {
        $email = strtolower(trim($_POST['email'] ?? ''));



        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->jsonResponse(false, $traducciones['invalid_email'] ?? 'Invalid email address.');
            return;
        }

        try {
            $exists = $this->specialistModel->getByEmail($email);
            if ($exists) {
                $this->jsonResponse(true, $traducciones['email_already_registered'] ?? 'This email has already been used.');
            } else {
                $this->jsonResponse(false, $traducciones['email_available'] ?? 'Email is available.');
            }
        } catch (Exception $e) {
            $this->jsonResponse(false, $e->getMessage());
        }
    }

    // ELIMINAR LA ANTERIOR CUANDO SE PUEDA

    public function checkEmailSpecialist()
    {
        $email = strtolower(trim($_POST['email'] ?? ''));



        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->jsonResponse(false, $traducciones['invalid_email'] ?? 'Invalid email address.');
            return;
        }

        try {
            $exists = $this->specialistModel->getByEmail($email);
            if ($exists) {
                $this->jsonResponse(true, $traducciones['email_already_registered'] ?? 'This email has already been used.');
            } else {
                $this->jsonResponse(false, $traducciones['email_available'] ?? 'Email is available.');
            }
        } catch (Exception $e) {
            $this->jsonResponse(false, $e->getMessage());
        }
    }

    public function checkTelephone()
    {
        $rawTelephone = trim($_POST['phone'] ?? '');

        if (empty($rawTelephone)) {
            $this->jsonResponse(false, $traducciones['invalid_telephone'] ?? 'Invalid telephone number.');
            return;
        }

        try {
            $exists = $this->specialistModel->getByTelephone($rawTelephone);
            if ($exists) {
                $this->jsonResponse(true, $traducciones['telephone_already_used'] ?? 'This telephone has already been used.');
            } else {
                $this->jsonResponse(false, $traducciones['telephone_available'] ?? 'Telephone is available.');
            }
        } catch (Exception $e) {
            $this->jsonResponse(false, $e->getMessage());
        }
    }

    // ELIMINAR LA ANTERIOR CUANDO SE PUEDA

    public function checkTelephoneSpecialist()
    {
        $rawTelephone = $_POST['telephone'] ?? '';




        if (empty($rawTelephone)) {
            $this->jsonResponse(false, $traducciones['invalid_telephone'] ?? 'Invalid telephone number.');
            return;
        }

        try {
            $exists = $this->specialistModel->getByTelephone($rawTelephone);
            if ($exists) {
                $this->jsonResponse(true, $traducciones['telephone_already_used'] ?? 'This telephone has already been used.');
            } else {
                $this->jsonResponse(false, $traducciones['telephone_available'] ?? 'Telephone is available.');
            }
        } catch (Exception $e) {
            $this->jsonResponse(false, $e->getMessage());
        }
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
                $result = $this->specialistModel->updateSystemTypeByUserId($userId, $systemType);

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



    public function register()
    {
        $data = $this->getJsonInput();
        $required = ['first_name', 'last_name', 'email', 'password', 'phone'];

        $language = strtoupper($data['language'] ?? 'EN');
        $langFile = PROJECT_ROOT . "/lang/{$language}.php";
        if (!file_exists($langFile)) {
            $langFile = PROJECT_ROOT . "/lang/EN.php";
        }
        $lang = require $langFile;

        foreach ($required as $field) {
            if (empty($data[$field])) {
                return $this->jsonResponse(false, $lang["field_{$field}_required"] ?? "Field {$field} is required.", $data, 400);
            }
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->jsonResponse(false, $lang['invalid_email_format'], $data, 400);
        }

        if (strlen($data['password']) < 8) {
            return $this->jsonResponse(false, $lang['password_too_short'], $data, 400);
        }


        try {
            if ($this->specialistModel->getByEmail($data['email'])) {
                return $this->jsonResponse(false, $lang['email_already_registered'], $data, 409);
            }

            $this->specialistModel->registerSpecialist($data);
            $this->sendWelcomeEmail($data['email'], $language);

            return $this->jsonResponse(true, $lang['registration_successful'], [
                'redirect' => '/specialist/login'
            ]);
        } catch (Exception $e) {
            return $this->jsonResponse(false, $lang['registration_failed'], ['error' => $e->getMessage()], 500);
        }
    }
    public function updatePassword()
    {
        $data = $this->getJsonInput();
        $language = strtoupper($data['language'] ?? 'EN');

        $langPath = PROJECT_ROOT . "/lang/{$language}.php";
        $lang = file_exists($langPath) ? include $langPath : include PROJECT_ROOT . "/lang/EN.php";

        if (empty($data['newPassword']) || strlen($data['newPassword']) < 6) {
            return $this->jsonResponse(false, $lang['password_too_short'] ?? 'Password must be at least 6 characters.', $data, 400);
        }

        try {
            $success = $this->specialistModel->updatePassword($data);

            if ($success) {
                return $this->jsonResponse(true, $lang['password_update_success'] ?? 'Password updated successfully.');
            } else {
                return $this->jsonResponse(false, $lang['password_update_failed'] ?? 'Password update failed.', $data, 400);
            }
        } catch (Exception $e) {
            return $this->jsonResponse(false, $lang['password_update_failed'] ?? 'Password update failed.', [
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update($params)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $params['id'] ?? null;
            $data = $_POST;

            // Cargar idioma desde archivos
            $lang = strtoupper($_SESSION['idioma'] ?? 'EN');
            $langPath = PROJECT_ROOT . "/lang/{$lang}.php";
            $translations = file_exists($langPath) ? include $langPath : include PROJECT_ROOT . "/lang/EN.php";

            try {
                $result = $this->specialistModel->update($id, $data);
                return $this->jsonResponse(
                    true,
                    $translations['specialist_updated_successfully'],
                    $result
                );
            } catch (mysqli_sql_exception $e) {
                return $this->errorResponse(400, $translations['error_updating_specialist'] . ' ' . $e->getMessage());
            }
        } else {
            $lang = strtoupper($_SESSION['idioma'] ?? 'EN');
            $langPath = PROJECT_ROOT . "/lang/{$lang}.php";
            $translations = file_exists($langPath) ? include $langPath : include PROJECT_ROOT . "/lang/EN.php";

            return $this->errorResponse(405, $translations['method_not_allowed']);
        }
    }

    public function updateProfile($params)
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $params['id'] ?? null;

            if (!$id) {
                return $this->errorResponse(400, "ID requerido para actualizar el perfil.");
            }

            $data = [];
            if ($method === 'POST') {
                $data = $_POST;
            } else {
                parse_str(file_get_contents("php://input"), $data);
            }

            // Cargar idioma desde sesión o datos
            $language = strtoupper($data['language'] ?? ($_SESSION['idioma'] ?? 'EN'));
            $langPath = PROJECT_ROOT . "/lang/{$language}.php";
            $lang = file_exists($langPath) ? include $langPath : include PROJECT_ROOT . "/lang/EN.php";

            try {
                // Subir imagen de perfil si existe
                $imagePath = $this->handleProfileImageUpload($id);
                if ($imagePath !== null) {
                    $data['profile_image'] = $imagePath;
                    $_SESSION['user_image'] = $imagePath;
                }

                // Actualizar especialista
                $result = $this->specialistModel->updateProfile($id, $data);

                return $this->jsonResponse(
                    true,
                    $lang['profile_updated_successfully'] ?? "Perfil actualizado correctamente.",
                    $result
                );
            } catch (\Exception $e) {
                return $this->jsonResponse(false, $lang['profile_update_failed'] ?? 'Error al actualizar el perfil.', [
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        return $this->errorResponse(405, "Método no permitido. Se requiere PUT.");
    }
    public function delete($params)
    {
        $id = $params['id'] ?? null;

        if (empty($id)) {
            return $this->jsonResponse(false, 'Invalid specialist ID.');
        }

        try {
            $deleted = $this->specialistModel->delete($id);
            return $this->jsonResponse(true, 'Specialist deleted successfully.');
        } catch (mysqli_sql_exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        } catch (Exception $e) {
            return $this->jsonResponse(false, 'Unexpected error: ' . $e->getMessage());
        }
    }


    public function login()
    {
        $input = $this->getJsonInput();
        $email = $input['email'] ?? '';
        $password = $input['password'] ?? '';
        $language = strtoupper($input['language'] ?? 'EN');

        // Auditoría
        $deviceId = $input['device_id'] ?? null;
        $isMobile = isset($input['is_mobile']) ? (bool) $input['is_mobile'] : null;
        $userAgent = $input['user_agent'] ?? ($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown');

        // Idioma
        $langFile = PROJECT_ROOT . "/lang/{$language}.php";
        if (!file_exists($langFile)) {
            $langFile = PROJECT_ROOT . "/lang/EN.php";
        }
        $lang = require $langFile;

        // Helpers y modelos
        require_once __DIR__ . '/../helpers/login_helpers.php';
        require_once __DIR__ . '/../models/SessionManagementModel.php';

        $failureCode = null;
        $specialist = null;

        // ──────── Lógica de intentos fallidos ────────
        $maxAttempts = 3;
        $lockoutTime = 60;
        $attemptKey = 'specialist_login_attempts_' . md5($email);
        $now = time();
        $attemptData = $_SESSION[$attemptKey] ?? ['count' => 0, 'last_attempt' => 0, 'locked_until' => 0];

        if ($attemptData['locked_until'] > $now) {
            $wait = ceil(($attemptData['locked_until'] - $now) / 60);
            $details = getFailureDetails('too_many_attempts');

            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                try {
                    $specialist = $this->specialistModel->getByEmail($email);
                } catch (\Throwable $t) {
                }
            }

            (new SessionManagementModel())->create(
                $specialist['specialist_id'] ?? null,
                'specialist',
                $deviceId,
                $isMobile,
                false,
                $details['reason']
            );

            return $this->jsonResponse(false, $lang['failure_' . $details['code']] ?? $details['reason'], [
                'code' => $details['code'],
                'wait_minutes' => $wait
            ], 429);
        }

        // Validaciones iniciales
        if (empty($email) || empty($password)) {
            $failureCode = 'missing_fields';
            $details = getFailureDetails($failureCode);
            (new SessionManagementModel())->create(null, 'specialist', $deviceId, $isMobile, false, $details['reason']);

            $attemptData['count']++;
            $attemptData['last_attempt'] = $now;
            if ($attemptData['count'] >= $maxAttempts) {
                $attemptData['locked_until'] = $now + $lockoutTime;
            }
            $_SESSION[$attemptKey] = $attemptData;

            return $this->jsonResponse(false, $lang['failure_' . $details['code']], ['code' => $details['code']], 400);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $failureCode = 'invalid_email_format';
            $details = getFailureDetails($failureCode);
            (new SessionManagementModel())->create(null, 'specialist', $deviceId, $isMobile, false, $details['reason']);

            $attemptData['count']++;
            $attemptData['last_attempt'] = $now;
            if ($attemptData['count'] >= $maxAttempts) {
                $attemptData['locked_until'] = $now + $lockoutTime;
            }
            $_SESSION[$attemptKey] = $attemptData;

            return $this->jsonResponse(false, $lang['failure_' . $details['code']], ['code' => $details['code']], 400);
        }

        try {
            $specialist = $this->specialistModel->getByEmail($email);

            if (!$specialist) {
                $failureCode = 'user_not_found';
            } elseif ((int) $specialist['status'] === 0) {
                $failureCode = 'user_blocked';
            } elseif (!password_verify($password, $specialist['password'])) {
                $failureCode = 'invalid_password';
            }

            if ($failureCode) {
                $details = getFailureDetails($failureCode);
                (new SessionManagementModel())->create(
                    $specialist['specialist_id'] ?? null,
                    'specialist',
                    $deviceId,
                    $isMobile,
                    false,
                    $details['reason']
                );

                $attemptData['count']++;
                $attemptData['last_attempt'] = $now;

                // 🔒 Bloqueo automático si falla 3 veces
                if ($attemptData['count'] >= $maxAttempts && isset($specialist['specialist_id'])) {
                    $this->specialistModel->updateStatus([
                        'specialist_id' => $specialist['specialist_id'],
                        'status' => 0
                    ]);
                    $failureCode = 'user_blocked';
                    $details = getFailureDetails($failureCode);
                }

                if ($attemptData['count'] >= $maxAttempts) {
                    $attemptData['locked_until'] = $now + $lockoutTime;
                }
                $_SESSION[$attemptKey] = $attemptData;

                return $this->jsonResponse(false, $lang['failure_' . $details['code']], ['code' => $details['code']], 401);
            }

            // ✅ Login exitoso
            $_SESSION['idioma'] = $language;
            $_SESSION['user_id'] = $specialist['specialist_id'];
            $_SESSION['roles_user'] = "Specialist";
            $_SESSION['system_type'] = $specialist['system_type'];
            $_SESSION['timezone'] = $specialist['timezone'];
            $_SESSION['avatar_url'] = $specialist['avatar_url'];
            $_SESSION['user_name'] = $specialist['first_name'] . ' ' . $specialist['last_name'];
            $_SESSION['logged_in'] = true;

            unset($_SESSION[$attemptKey]);

            $sessionId = (new SessionManagementModel())->create(
                $specialist['specialist_id'],
                'specialist',
                $deviceId,
                $isMobile,
                true,
                null
            );

            $_SESSION['session_id'] = $sessionId;
            $_SESSION['user_type'] = 'specialist';

            return $this->jsonResponse(true, $lang['login_success_yes'], ['redirect' => 'dashboard_specialist']);

        } catch (Exception $e) {
            (new SessionManagementModel())->create(
                $specialist['specialist_id'] ?? null,
                'specialist',
                $deviceId,
                $isMobile,
                false,
                'Excepción inesperada: ' . $e->getMessage()
            );
            return $this->jsonResponse(false, $lang['login_success_no'], [
                'code' => 'unknown_failure',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus($params)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $specialistId = $params['id'] ?? $data['specialist_id'] ?? null;

            if (!$specialistId || !isset($data['status'])) {
                return $this->errorResponse(400, "Missing specialist ID or status value.");
            }

            $data['specialist_id'] = $specialistId;

            try {
                $result = $this->specialistModel->updateStatus($data);
                return $result
                    ? $this->jsonResponse(true, "Status updated successfully", $data)
                    : $this->errorResponse(400, "Failed to update status.");
            } catch (mysqli_sql_exception $e) {
                return $this->errorResponse(500, "Database error: " . $e->getMessage());
            }
        }

        return $this->errorResponse(405, "Method Not Allowed. Use POST.");
    }


    public function getSessionUserData($params)
    {
        $id = $params['id'] ?? null;

        if (!$id) {
            return $this->jsonResponse(false, "Missing specialist ID");
        }

        try {
            $data = $this->specialistModel->getSessionUserData($id);

            if (isset($data['status']) && $data['status'] === 'error') {
                return $this->jsonResponse(false, $data['message']);
            }

            return $this->jsonResponse(true, '', $data);
        } catch (mysqli_sql_exception $e) {
            return $this->errorResponse(400, "Error retrieving session specialist data: " . $e->getMessage());
        }
    }


    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();
        $scriptNameFormat = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
        $rutaRedirigir = "Location: " . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $scriptNameFormat;
        header($rutaRedirigir);
    }

    private function sendWelcomeEmail($email, $lang = 'EN')
    {

        $host = rtrim($_ENV['APP_URL_Specialists'] ?? 'https://localhost/specialist', '/');
        $host2 = rtrim($_ENV['APP_URL'] ?? 'https://localhost', '/');
        $host2 = rtrim($host2, '/');
        $logoUrl = BASE_URL . 'public/assets/images/logo-index.png';
        $subject = $lang === 'ES' ? 'Bienvenido a VITAKEE' : 'Welcome to VITAKEE';

        $body = $lang === 'ES'
            ? "<div style='font-family: sans-serif; color: black;'>
                <div style='text-align: right;'><img src='$logoUrl' alt='VITAKEE' style='height: 50px;' /></div>
                <p><strong>Bienvenido a VITAKEE,</strong></p>
                <p>Tu cuenta ha sido creada exitosamente.</p>
                <p>Ahora puedes comenzar a gestionar tus pacientes en un solo lugar, de forma segura.</p>
                <ul>
                    <li>Revisar registros biométricos</li>
                    <li>Agregar observaciones o recomendaciones</li>
                </ul>
                <p>Accede a tu cuenta aquí:<br><a href='$host'>$host</a></p>
                <p>Si no creaste esta cuenta, puedes ignorar este mensaje.</p>
                <hr><p style='font-size: 12px;'><strong>VITAKEE</strong><br>Tu salud, tus datos — siempre accesibles.<br><a href='$host'>$host</a></p>
            </div>"
            : "<div style='font-family: sans-serif; color: black;'>
                <div style='text-align: right;'><img src='$logoUrl' alt='VITAKEE' style='height: 50px;' /></div>
                <p><strong>Welcome to VITAKEE,</strong></p>
                <p>Your account has been successfully created.</p>
                <p>You can now start managing patient health data securely in one place.</p>
                <ul>
                    <li>Review biometric records</li>
                    <li>Add comments or recommendations</li>
                </ul>
                <p>Access your account here:<br><a href='$host'>$host</a></p>
                <p>If you did not create this account, you can safely ignore this message.</p>
                <hr><p style='font-size: 12px;'><strong>VITAKEE</strong><br>Your health, your data — always accessible.<br><a href='$host'>$host</a></p>
            </div>";

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $_ENV['MAIL_HOST'] ?? 'localhost';
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['MAIL_USERNAME'] ?? '';
            $mail->Password = $_ENV['MAIL_PASSWORD'] ?? '';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $_ENV['MAIL_PORT'] ?? 587;

            $mail->setFrom($_ENV['MAIL_FROM'] ?? 'noreply@example.com', $_ENV['MAIL_FROM_NAME'] ?? 'Vitakee');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;

            $mail->send();
        } catch (Exception $e) {
            error_log("Welcome email error (Specialist): " . $mail->ErrorInfo);
        }
    }
}
