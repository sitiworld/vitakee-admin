<?php

require_once __DIR__ . '/../models/AuthModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/SecurityQuestionsModel.php'; // Include the SecurityQuestionsModel
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once PROJECT_ROOT . '/vendor/phpmailer/phpmailer/src/Exception.php';
require_once PROJECT_ROOT . '/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once PROJECT_ROOT . '/vendor/phpmailer/phpmailer/src/SMTP.php';
require_once PROJECT_ROOT . '/vendor/autoload.php'; // Ensure composer autoload is included

use Google\Client as GoogleClient;
class AuthController
{
    private $authModel;
    private $userModel;
    private $securityQuestionsModel; // Add property for SecurityQuestionsModel

    public function __construct()
    {
        $this->authModel = new AuthModel();
        $this->userModel = new UserModel();
        $this->securityQuestionsModel = new SecurityQuestionsModel(); // Instantiate SecurityQuestionsModel
    }

    private function getJsonInput(): array
    {
        $input = file_get_contents("php://input");
        return json_decode($input, true) ?? [];
    }

    private function jsonResponse($value, $message = '', $data = null, int $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode([
            'value' => $value,
            'message' => $message,
            'data' => $data
        ]);
        exit;
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
        $userType = 'user';

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
        $usuario = null;

        // ──────── BLOQUEO POR SESIÓN ────────
        $maxAttempts = 3;
        $lockoutTime = 60; // en segundos
        $attemptKey = 'login_attempts_' . md5($email);
        $now = time();
        $attemptData = $_SESSION[$attemptKey] ?? ['count' => 0, 'last_attempt' => 0, 'locked_until' => 0];

        if ($attemptData['locked_until'] > $now) {
            $wait = ceil(($attemptData['locked_until'] - $now) / 60);
            $details = getFailureDetails('too_many_attempts');

            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                try {
                    $usuario = $this->userModel->getUserByEmail($email);
                } catch (\Throwable $t) {
                }
            }

            $SessionManagementModel->create(
                $usuario['user_id'] ?? null,
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
            $usuario = $this->userModel->getUserByEmail($email);

            if (!$usuario) {
                $failureCode = 'user_not_found';
            } elseif ((int) $usuario['status'] === 0) {
                $failureCode = 'user_blocked';
            } elseif (!password_verify($password, $usuario['password'])) {
                $failureCode = 'invalid_password';
            }

            if ($failureCode) {
                $details = getFailureDetails($failureCode);
                $SessionManagementModel->create(
                    $usuario['user_id'] ?? null,
                    $userType,
                    $deviceId,
                    $isMobile,
                    false,
                    $details['reason']
                );

                $attemptData['count']++;
                $attemptData['last_attempt'] = $now;

                // 🔒 Bloqueo definitivo si alcanza el límite
                if ($attemptData['count'] >= $maxAttempts && isset($usuario['user_id'])) {
                    error_log("⛔ Intentos fallidos alcanzados para usuario: " . $usuario['user_id']);

                    $bloqueado = $this->userModel->updateStatus([
                        'user_id' => $usuario['user_id'],
                        'status' => 0
                    ]);

                    if (!$bloqueado) {
                        error_log("❌ Falló el bloqueo del usuario con ID: " . $usuario['user_id']);
                    } else {
                        error_log("✅ Usuario bloqueado correctamente: " . $usuario['user_id']);
                    }

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
            $_SESSION['user_id'] = $usuario['user_id'];
            $_SESSION['roles_user'] = "User";
            $_SESSION['height'] = $usuario['height'];
            $_SESSION['sex_biological'] = $usuario['sex_biological'];
            $_SESSION['birthday'] = $usuario['birthday'];
            $_SESSION['system_type'] = $usuario['system_type'];
            $_SESSION['timezone'] = $usuario['timezone'];
            $_SESSION['user_name'] = $usuario['first_name'] . ' ' . $usuario['last_name'];
            $_SESSION['logged_in'] = true;
            $_SESSION['user_type'] = $userType;

            unset($_SESSION[$attemptKey]);

            $sessionId = $SessionManagementModel->create(
                $usuario['user_id'],
                $userType,
                $deviceId,
                $isMobile,
                true,
                null
            );

            $_SESSION['session_id'] = $sessionId;

            return $this->jsonResponse(true, $lang['login_success_yes'], ['redirect' => 'app/dashboard']);

        } catch (Exception $e) {
            error_log("⚠️ Excepción en login: " . $e->getMessage());

            $SessionManagementModel->create(
                $usuario['user_id'] ?? null,
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





    public function registrar()
    {
        $data = $this->getJsonInput();
        $required = ['first_name', 'last_name', 'birthday', 'email', 'password', 'sex_biological', 'telephone'];

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

        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

        try {
            if ($this->userModel->getUserByEmail($data['email'])) {
                return $this->jsonResponse(false, $lang['email_already_registered'], $data, 409);
            }

            $this->authModel->registerUser($data);

            // Enviar correo de bienvenida
            $this->sendWelcomeEmail($data['email'], $language);

            return $this->jsonResponse(true, $lang['registration_successful'], [
                'redirect' => 'app/user/login'
            ]);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $lang['registration_failed'], ['error' => $e->getMessage()], 500);
        }
    }

    private function sendWelcomeEmail($email, $lang = 'EN')
    {
        $host = defined('APP_URL') ? APP_URL : 'http://localhost/';
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

        // Enviar correo
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
    public function checkUserImage()
    {
        $input = $this->getJsonInput();
        $userId = $input['user_id'] ?? null;

        if (!$userId) {
            return $this->jsonResponse(false, 'Missing user ID.', null, 400);
        }

        // Ruta relativa desde la raíz del proyecto
        $relativePath = "uploads/users/user_{$userId}.jpg";

        try {
            $exists = $this->authModel->checkImageExists($relativePath);
            return $this->jsonResponse(true, 'Image check completed.', ['exists' => $exists]);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Error checking image.', ['error' => $e->getMessage()], 500);
        }
    }




    public function logout()
    {


        $userRole = $_SESSION['roles_user'] ?? 'User';
        $sessionId = $_SESSION['session_id'] ?? null;
        $inactivity = $_SESSION['inactivity_duration'] ?? null;
        $status = $_SESSION['session_status'] ?? '';


        $baseUrl = match ($userRole) {
            'Administrator' => ($_ENV['APP_URL_Admin'] ?? BASE_URL. 'app/administrator/'),
            'Specialist' => ($_ENV['APP_URL_Specialists'] ?? BASE_URL. 'app/specialist/'),
            default => ($_ENV['APP_URL'] ?? BASE_URL. 'app/')
        };

        // Registrar logout solo si no fue ya registrado como 'expired' o 'kicked'
        if ($sessionId && !in_array($status, ['expired', 'kicked'], true)) {
            require_once __DIR__ . '/../models/SessionManagementModel.php';
            $sessionModel = new SessionManagementModel();
            $sessionModel->logoutSession($sessionId, null, 'closed');
        }

        session_unset();
        session_destroy();

        header("Location: " . $baseUrl);
        exit;
    }




    // You will need a method to handle saving security questions, likely
    // triggered by the form submission in the modal.
    // Example:
    /*
    public function saveSecurityQuestions()
    {
        // ... (existing commented code)
    }
    */

    // SOCIAL LOGIN METHODS

    public function google()
    {
        $client = new GoogleClient();
        $client->setClientId($_ENV['GOOGLE_CLIENT_ID'] ?? 'YOUR_GOOGLE_CLIENT_ID');
        $client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET'] ?? 'YOUR_GOOGLE_CLIENT_SECRET');
        $client->setRedirectUri(BASE_URL . 'oauth-callback/google');
        $client->addScope("email");
        $client->addScope("profile");

        $authUrl = $client->createAuthUrl();
        header('Location: ' . $authUrl);
        exit;
    }

    public function googleCallback()
    {
        try {
            error_log("=== Google Callback Started ===");
            
            $client = new GoogleClient();
            $client->setClientId($_ENV['GOOGLE_CLIENT_ID'] ?? 'YOUR_GOOGLE_CLIENT_ID');
            $client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET'] ?? 'YOUR_GOOGLE_CLIENT_SECRET');
            $client->setRedirectUri(BASE_URL . 'oauth-callback/google');

            if (isset($_GET['code'])) {
                error_log("Google auth code received");
                
                $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
                
                if (!isset($token['error'])) {
                    $client->setAccessToken($token['access_token']);
                    $google_oauth = new \Google\Service\Oauth2($client);
                    $google_account_info = $google_oauth->userinfo->get();
                    
                    $email = $google_account_info->email;
                    $googleId = $google_account_info->id;
                    $firstName = $google_account_info->givenName ?? '';
                    $lastName = $google_account_info->familyName ?? '';

                    error_log("Google user info: Email=$email, ID=$googleId, Name=$firstName $lastName");

                    // Check or register user
                    $this->handleSocialLogin([
                        'email' => $email,
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'google_id' => $googleId,
                        'sex_biological' => 'M',
                        'birthday' => '1900-01-01'
                    ]);
                } else {
                    error_log("Google token error: " . json_encode($token));
                    header('Location: ' . BASE_URL . 'app/user/login?error=google_auth_failed');
                    exit;
                }
            } else {
                error_log("No Google auth code in request");
                header('Location: ' . BASE_URL . 'app/user/login?error=no_code');
                exit;
            }
        } catch (\Exception $e) {
            error_log("Google Callback Exception: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            header('Location: ' . BASE_URL . 'app/user/login?error=callback_exception');
            exit;
        }
    }



    private function handleSocialLogin($data)
    {
        try {
            error_log("=== Handle Social Login Started ===");
            error_log("Data received: " . json_encode($data));
            
            // Check if user exists by Google ID
            $user = null;
            if (isset($data['google_id'])) {
                $user = $this->authModel->getUserByGoogleId($data['google_id']);
                error_log("User lookup by Google ID: " . ($user ? "Found" : "Not found"));
            }

            if (!$user) {
                // Register or link
                try {
                    error_log("Attempting to register/link social user");
                    $userId = $this->authModel->registerSocialUser($data);
                    error_log("User registered/linked with ID: $userId");
                    
                    // Fetch the newly created/linked user
                    require_once __DIR__ . '/../models/UserModel.php';
                    $userModel = new UserModel();
                    $user = $userModel->getById($userId);
                    
                    if (!$user) {
                        error_log("ERROR: User not found after registration with ID: $userId");
                        throw new \Exception("User not found after registration");
                    }
                    
                    error_log("User fetched successfully: " . json_encode($user));
                } catch (\Exception $e) {
                    error_log("Registration error: " . $e->getMessage());
                    error_log("Stack trace: " . $e->getTraceAsString());
                    header('Location: ' . BASE_URL . 'app/user/login?error=registration_failed&msg=' . urlencode($e->getMessage()));
                    exit;
                }
            }

            // Validate user data
            if (!isset($user['user_id'])) {
                error_log("ERROR: user_id missing in user data");
                throw new \Exception("Invalid user data: missing user_id");
            }

            // Login session logic
            $_SESSION['idioma'] = 'EN';
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['roles_user'] = "User";
            $_SESSION['height'] = $user['height'] ?? 0;
            $_SESSION['sex_biological'] = $user['sex_biological'] ?? 'M';
            $_SESSION['birthday'] = $user['birthday'] ?? '1900-01-01';
            $_SESSION['system_type'] = $user['system_type'] ?? 'US';
            $_SESSION['timezone'] = $user['timezone'] ?? 'UTC';
            $_SESSION['user_name'] = ($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '');
            $_SESSION['logged_in'] = true;
            $_SESSION['user_type'] = 'user';

            error_log("Session variables set successfully");

            // Log session
            require_once __DIR__ . '/../models/SessionManagementModel.php';
            $SessionManagementModel = new SessionManagementModel();
            $sessionId = $SessionManagementModel->create(
                $user['user_id'],
                'user',
                null,
                false,
                true,
                null
            );
            $_SESSION['session_id'] = $sessionId;

            error_log("Session created with ID: $sessionId");
            error_log("Redirecting to dashboard");

            header('Location: ' . BASE_URL . 'app/dashboard');
            exit;
        } catch (\Exception $e) {
            error_log("Handle Social Login Exception: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            header('Location: ' . BASE_URL . 'app/user/login?error=login_failed&msg=' . urlencode($e->getMessage()));
            exit;
        }
    }
}
