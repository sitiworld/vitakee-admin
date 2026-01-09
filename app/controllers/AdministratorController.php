<?php

require_once __DIR__ . '/../models/AdministratorModel.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once PROJECT_ROOT . '/vendor/phpmailer/phpmailer/src/Exception.php';
require_once PROJECT_ROOT . '/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once PROJECT_ROOT . '/vendor/phpmailer/phpmailer/src/SMTP.php';

class AdministratorController
{
    private $adminModel;

    public function __construct()
    {
        $this->adminModel = new AdministratorModel();
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

    public function showAll()
    {
        try {
            $admins = $this->adminModel->getAll();
            return $this->jsonResponse(true, '', $admins);
        } catch (mysqli_sql_exception $e) {
            return $this->errorResponse(400, "Error fetching administrators: " . $e->getMessage());
        }
    }

    public function showById($params)
    {
        $id = $params['id'] ?? null;
        try {
            $admin = $this->adminModel->getById($id);
            if ($admin) {
                return $this->jsonResponse(true, '', $admin);
            } else {
                return $this->jsonResponse(false, "Administrator not found");
            }
        } catch (mysqli_sql_exception $e) {
            return $this->errorResponse(400, "Error fetching administrator: " . $e->getMessage());
        }
    }
    public function getSessionUserData($params)
    {
        $id = $params['id'] ?? null;

        if (!$id) {
            return $this->jsonResponse(false, "Missing administrator ID");
        }

        try {
            $data = $this->adminModel->getSessionUserData($id);

            if (isset($data['status']) && $data['status'] === 'error') {
                return $this->jsonResponse(false, $data['message']);
            }

            return $this->jsonResponse(true, '', $data);
        } catch (mysqli_sql_exception $e) {
            return $this->errorResponse(400, "Error retrieving session user data: " . $e->getMessage());
        }
    }


    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;

            // Cargar traducciones desde archivo
            $lang = strtoupper($_SESSION['idioma'] ?? 'EN');
            $langPath = PROJECT_ROOT . "/lang/{$lang}.php";
            $translations = file_exists($langPath) ? include $langPath : include PROJECT_ROOT . "/lang/EN.php";

            try {
                $result = $this->adminModel->create($data);
                return $this->jsonResponse(
                    (bool) $result,
                    $result ? $translations['administrator_created_successfully'] : $translations['error_creating_administrator']
                );
            } catch (mysqli_sql_exception $e) {
                return $this->errorResponse(400, $translations['error_creating_administrator'] . ' ' . $e->getMessage());
            }
        } else {
            $lang = strtoupper($_SESSION['idioma'] ?? 'EN');
            $langPath = PROJECT_ROOT . "/lang/{$lang}.php";
            $translations = file_exists($langPath) ? include $langPath : include PROJECT_ROOT . "/lang/EN.php";

            return $this->errorResponse(405, $translations['method_not_allowed']);
        }
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
        $uploadDir = PROJECT_ROOT . '/uploads/administrator/';
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
        return "uploads/administrator/{$filename}";
    }
    public function checkEmail()
    {
        $email = strtolower(trim($_POST['email'] ?? ''));


        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->jsonResponse(true, $traducciones['invalid_email'] ?? 'Invalid email address.');
            return;
        }

        try {
            $exists = $this->adminModel->getByEmail($email);
            if ($exists) {
                $this->jsonResponse(true, $traducciones['email_already_registered'] ?? 'This email has already been used.');
            } else {
                $this->jsonResponse(false, $traducciones['email_available'] ?? 'Email is available.');
            }
        } catch (Exception $e) {
            $this->jsonResponse(true, $e->getMessage());
        }
    }

    public function checkTelephone()
    {
        // verificar si se está enviando phone o telephone
        $rawTelephone = $_POST['telephone'] ?? $_POST['phone'] ?? '';


        if (empty($rawTelephone)) {
            $this->jsonResponse(true, $traducciones['invalid_telephone'] ?? 'Invalid telephone number.');
            return;
        }

        try {
            $exists = $this->adminModel->getByTelephone($rawTelephone);
            if ($exists) {
                $this->jsonResponse(true, $traducciones['telephone_already_used'] ?? 'This telephone has already been used.');
            } else {
                $this->jsonResponse(false, $traducciones['telephone_available'] ?? 'Telephone is available.');
            }
        } catch (Exception $e) {
            $this->jsonResponse(true, $e->getMessage());
        }
    }
    public function update_profile($params)
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $lang = strtoupper($_SESSION['idioma'] ?? 'EN');
        $langPath = PROJECT_ROOT . "/lang/{$lang}.php";
        $translations = file_exists($langPath) ? include $langPath : include PROJECT_ROOT . "/lang/EN.php";

        if ($method === 'PUT' || ($method === 'POST' && ($_POST['_method'] ?? '') === 'PUT')) {
            $id = $params['id'] ?? null;
            $putData = [];

            if ($method === 'POST') {
                $putData = $_POST;
            } else {
                parse_str(file_get_contents("php://input"), $putData);
            }

            try {
                // 1) Manejar la subida de la imagen
                $imagePath = $this->handleProfileImageUpload($id);
                if ($imagePath !== null) {
                    $putData['profile_image'] = $imagePath;
                    $_SESSION['user_image'] = $imagePath;
                }

                // 2) Llamar al modelo para actualizar
                $result = $this->adminModel->updateProfile($id, $putData);

                return $this->jsonResponse(
                    true,
                    $result ? $translations['user_updated_successfully'] : $translations['error_updating_user'],
                    $result
                );
            } catch (\Exception $e) {
                return $this->errorResponse(400, $translations['error_updating_user'] . ' ' . $e->getMessage());
            }
        }

        return $this->errorResponse(405, $translations['method_not_allowed_put']);
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
                $result = $this->adminModel->updateSystemTypeByUserId($userId, $systemType);

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
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userType = 'admin';

        // Cargar idioma
        $langFile = PROJECT_ROOT . "/lang/{$language}.php";
        if (!file_exists($langFile)) {
            $langFile = PROJECT_ROOT . "/lang/EN.php";
        }
        $lang = require $langFile;

        // Helpers y modelos
        require_once __DIR__ . '/../helpers/login_helpers.php';
        require_once __DIR__ . '/../models/SessionManagementModel.php';
        $SessionManagementModel = new SessionManagementModel();

        $failureCode = null;
        $admin = null;

        // ──────── BLOQUEO POR SESIÓN ────────
        $maxAttempts = 3;
        $lockoutTime = 60; // en segundos
        $attemptKey = 'admin_login_attempts_' . md5($email);
        $now = time();
        $attemptData = $_SESSION[$attemptKey] ?? ['count' => 0, 'last_attempt' => 0, 'locked_until' => 0];

        if ($attemptData['locked_until'] > $now) {
            $wait = ceil(($attemptData['locked_until'] - $now) / 60);
            $details = getFailureDetails('too_many_attempts');

            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                try {
                    $admin = $this->adminModel->getByEmail($email);
                } catch (\Throwable $t) {
                }
            }

            $SessionManagementModel->create(
                $admin['administrator_id'] ?? null,
                $userType,
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

        // ──────── VALIDACIONES INICIALES ────────
        if (empty($email) || empty($password)) {
            $details = getFailureDetails('missing_fields');
            $SessionManagementModel->create(null, $userType, $deviceId, $isMobile, false, $details['reason']);

            $attemptData['count']++;
            $attemptData['last_attempt'] = $now;
            if ($attemptData['count'] >= $maxAttempts) {
                $attemptData['locked_until'] = $now + $lockoutTime;
            }
            $_SESSION[$attemptKey] = $attemptData;

            return $this->jsonResponse(false, $lang['failure_' . $details['code']], ['code' => $details['code']], 400);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $details = getFailureDetails('invalid_email_format');
            $SessionManagementModel->create(null, $userType, $deviceId, $isMobile, false, $details['reason']);

            $attemptData['count']++;
            $attemptData['last_attempt'] = $now;
            if ($attemptData['count'] >= $maxAttempts) {
                $attemptData['locked_until'] = $now + $lockoutTime;
            }
            $_SESSION[$attemptKey] = $attemptData;

            return $this->jsonResponse(false, $lang['failure_' . $details['code']], ['code' => $details['code']], 400);
        }

        // ──────── AUTENTICACIÓN ────────
        try {
            $admin = $this->adminModel->getByEmail($email);

            if (!$admin) {
                $failureCode = 'user_not_found';
            } elseif ((int) $admin['status'] === 0) {
                $failureCode = 'user_blocked';
            } elseif (!password_verify($password, $admin['password'])) {
                $failureCode = 'invalid_password';
            }

            if ($failureCode) {
                $details = getFailureDetails($failureCode);
                $SessionManagementModel->create(
                    $admin['administrator_id'] ?? null,
                    $userType,
                    $deviceId,
                    $isMobile,
                    false,
                    $details['reason']
                );

                $attemptData['count']++;
                $attemptData['last_attempt'] = $now;

                // 🔒 Bloqueo definitivo si alcanza el límite
                if ($attemptData['count'] >= $maxAttempts && isset($admin['administrator_id'])) {
                    $this->adminModel->updateStatus([
                        'administrator_id' => $admin['administrator_id'],
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

            // ✅ LOGIN EXITOSO
            $_SESSION['idioma'] = $language;
            $_SESSION['user_id'] = $admin['administrator_id'];
            $_SESSION['roles_user'] = "Administrator";
            $_SESSION['system_type'] = $admin['system_type'];
            $_SESSION['user_name'] = $admin['first_name'] . ' ' . $admin['last_name'];
            $_SESSION['timezone'] = $admin['timezone'];
            $_SESSION['logged_in'] = true;
            $_SESSION['user_type'] = $userType;

            unset($_SESSION[$attemptKey]);

            $sessionId = $SessionManagementModel->create(
                $admin['administrator_id'],
                $userType,
                $deviceId,
                $isMobile,
                true,
                null
            );

            $_SESSION['session_id'] = $sessionId;

            return $this->jsonResponse(true, $lang['login_success_yes'], ['redirect' => 'dashboard_administrator']);

        } catch (Exception $e) {
            $SessionManagementModel->create(
                $admin['administrator_id'] ?? null,
                $userType,
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
            if ($this->adminModel->getByEmail($data['email'])) {
                return $this->jsonResponse(false, $lang['email_already_registered'], $data, 409);
            }

            $this->adminModel->registerAdmin($data);
            $this->sendWelcomeEmail($data['email'], $language);

            return $this->jsonResponse(true, $lang['registration_successful'], [
                'redirect' => '/admin/login'
            ]);
        } catch (Exception $e) {
            return $this->jsonResponse(false, $lang['registration_failed'], ['error' => $e->getMessage()], 500);
        }
    }

    private function sendWelcomeEmail($email, $lang = 'EN')
    {

        $host = rtrim($_ENV['APP_URL_Admin'] ?? 'https://localhost/administrator', '/');
        $host2 = rtrim($_ENV['APP_URL'] ?? 'https://localhost', '/');
        $host2 = rtrim($host2, '/');
        $logoUrl = BASE_URL . 'public/assets/images/logo-index.png';
        $subject = $lang === 'ES' ? 'Bienvenido a VITAKEE' : 'Welcome to VITAKEE';

        if ($lang === 'ES') {
            $body = "
            <div style='font-family: sans-serif; color: black;'>
                <div style='text-align: right;'>
                    <img src='$logoUrl' alt='VITAKEE' style='height: 50px;' />
                </div>
                <p><strong>Bienvenido a VITAKEE,</strong></p>
                <p>Tu cuenta ha sido creada exitosamente.</p>
                <p>Ahora puedes comenzar a registrar y organizar tus datos de salud personal en un solo lugar, de forma segura.</p>

                <p><strong>Aquí tienes lo que puedes hacer a continuación:</strong></p>
                <ul>
                    <li>Iniciar sesión en tu panel para comenzar a registrar tus biomarcadores</li>
                    <li>Configurar alertas personalizadas para valores fuera de rango</li>
                    <li>Exportar reportes y hacer seguimiento de tus tendencias a lo largo del tiempo</li>
                </ul>

                <p>Accede a tu cuenta aquí:<br>
                <a href='$host'>$host</a></p>

                <p>Si no creaste esta cuenta, puedes ignorar este mensaje.</p>
                <hr>
                <p style='font-size: 12px;'>
                    <strong>VITAKEE</strong><br>
                    Tu salud, tus datos — siempre accesibles.<br>
                    <a href='$host'>$host</a><br>
                    1065 Aurora Ln, Corona, CA 92881
                </p>
            </div>
        ";
        } else {
            $body = "
            <div style='font-family: sans-serif; color: black;'>
                <div style='text-align: right;'>
                    <img src='$logoUrl' alt='VITAKEE' style='height: 50px;' />
                </div>
                <p><strong>Welcome to VITAKEE,</strong></p>
                <p>Your account has been successfully created.</p>
                <p>You can now start logging and organizing your personal health data securely in one place.</p>

                <p><strong>Here’s what you can do next:</strong></p>
                <ul>
                    <li>Log in to your dashboard to start recording your biomarkers</li>
                    <li>Set up custom alerts for out-of-range values</li>
                    <li>Export reports and track trends over time</li>
                </ul>

                <p>Access your account here:<br>
                <a href='$host'>$host</a></p>

                <p>If you did not create this account, you can safely ignore this message.</p>
                <hr>
                <p style='font-size: 12px;'>
                    <strong>VITAKEE</strong><br>
                    Your health, your data — always accessible.<br>
                    <a href='$host'>$host</a><br>
                    1065 Aurora Ln, Corona, CA 92881
                </p>
            </div>
        ";
        }

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
            error_log("Welcome email error: " . $mail->ErrorInfo);
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

    public function updateStatus($params)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $administratorId = $params['id'] ?? $data['administrator_id'] ?? null;

            if (!$administratorId || !isset($data['status'])) {
                return $this->errorResponse(400, "Missing administrator ID or status value.");
            }

            $data['administrator_id'] = $administratorId;

            try {
                $result = $this->adminModel->updateStatus($data);
                if ($result) {
                    return $this->jsonResponse(true, "Status updated successfully", $data);
                } else {
                    return $this->errorResponse(400, "Failed to update status.");
                }
            } catch (mysqli_sql_exception $e) {
                return $this->errorResponse(500, "Database error: " . $e->getMessage());
            }
        } else {
            return $this->errorResponse(405, "Method Not Allowed. Use POST.");
        }
    }


    public function update($params)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $params['id'] ?? null;
            $data = $_POST;
            try {
                $result = $this->adminModel->update($id, $data);
                return $this->jsonResponse(true, "Administrator updated successfully", $result);
            } catch (mysqli_sql_exception $e) {
                return $this->errorResponse(400, "Error updating administrator: " . $e->getMessage());
            }
        } else {
            return $this->errorResponse(405, "Method Not Allowed. Use PUT.");
        }
    }

    public function delete($params)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            $id = $params['id'] ?? null;

            try {
                $result = $this->adminModel->delete($id);
                return $this->jsonResponse(
                    true,
                    "Administrator deleted successfully"
                );
            } catch (mysqli_sql_exception $e) {
                return $this->jsonResponse(false, $e->getMessage());
            } catch (Exception $e) {
                return $this->jsonResponse(false, "Unexpected error: " . $e->getMessage());
            }
        } else {
            return $this->jsonResponse(false, "Method Not Allowed. Use DELETE.");
        }
    }


    public function authenticate()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $email = $data['email'] ?? null;
            $password = $data['password'] ?? null;

            try {
                $admin = $this->adminModel->authenticate($email, $password);
                if ($admin) {
                    return $this->jsonResponse(true, 'Authentication successful', $admin);
                } else {
                    return $this->jsonResponse(false, 'Invalid credentials');
                }
            } catch (mysqli_sql_exception $e) {
                return $this->errorResponse(400, "Error during authentication: " . $e->getMessage());
            }
        } else {
            return $this->errorResponse(405, "Method Not Allowed. Use POST.");
        }
    }
}
