<?php

// Controlador de Recuperación de Contraseña (PasswordRecoveryController.php)
require_once __DIR__ . '/../helpers/mailHelper.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/SpecialistModel.php';
require_once __DIR__ . '/../models/AdministratorModel.php';

class RecoveryPassWordController
{
    private $userModel;
    private $segurityQuestions;
    private $db;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->segurityQuestions = new SecurityQuestionsModel();
        $this->db = Database::getInstance();
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

    public function verifyEmail()
    {
        $email = trim($_POST['email'] ?? '');
        $lang = strtoupper($_POST['lang'] ?? 'EN');
        $userType = $_POST['user_type'] ?? null;

        // Cargar traducciones
        $langPath = PROJECT_ROOT . "/lang/{$lang}.php";
        $traducciones = file_exists($langPath) ? include $langPath : include PROJECT_ROOT . "/lang/EN.php";

        // Validar email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->jsonResponse(false, $traducciones['invalid_email_format'] ?? 'Invalid email format.');
            return;
        }

        // Determinar modelo y campo de ID según user_type
        $model = null;
        $user = null;
        $userId = null;

        switch ($userType) {
            case 'Administrator':
                $model = new AdministratorModel();
                $user = $model->getByEmail($email);
                $userId = $user['administrator_id'] ?? null;
                break;
            case 'Specialist':
                $model = new SpecialistModel();
                $user = $model->getByEmail($email);
                $userId = $user['specialist_id'] ?? null;
                break;
            case 'User':
                $model = new UserModel();
                $user = $model->getUserByEmail($email);
                $userId = $user['user_id'] ?? null;
                break;
            default:
                $this->jsonResponse(false, 'Invalid user type.');
                return;
        }

        if ($user && $userId) {
            $questions = $this->segurityQuestions->getSecurityQuestionsByUserReset($userId, $userType);

            // No hay preguntas → enviar correo
            if (!$questions['value'] || empty($questions['data'])) {
                // Verificar si ya se envió un token recientemente
                $stmt = $this->db->prepare("SELECT created_at FROM password_resets WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                $stmt->close();

                if ($row) {
                    $createdAt = strtotime($row['created_at']);
                    $now = time();
                    $diff = $now - $createdAt;

                    if ($diff < 600) {
                        $this->jsonResponse(false, $traducciones['reset_email_recently_sent'] ?? 'A reset email was already sent recently. Please wait a few minutes.');
                        return;
                    }
                }

                $emailSent = $this->sendPasswordResetEmail($email, $lang, $userType);

                if ($emailSent) {
                    $this->jsonResponse(true, $traducciones['no_security_questions_email_sent'] ?? 'No security questions found. A reset link was sent.');
                } else {
                    $this->jsonResponse(false, $traducciones['no_security_questions_email_failed'] ?? 'No security questions found and reset email failed.');
                }
                return;
            }

            $questions = $questions['data'];

            $this->jsonResponse(
                true,
                $traducciones['email_found_answer_questions'] ?? 'Email found. Please answer the security questions.',
                [
                    'userId' => $userId,
                    'questions' => [
                        'question1' => $questions['question1'],
                        'question2' => $questions['question2']
                    ]
                ]
            );
        } else {
            $this->jsonResponse(false, $traducciones['email_not_found'] ?? 'Email not found.');
        }
    }



    public function checkEmail()
    {
        // 1. Obtener y normalizar el email: quitar espacios y convertir a minúsculas.
        $email = strtolower(trim($_POST['email'] ?? ''));

        // NOTA: El idioma no es necesario para esta validación, pero se mantiene la estructura.
        $lang = strtoupper($_POST['lang'] ?? 'EN');

        // 2. Cargar las traducciones.
        $langPath = PROJECT_ROOT . "/lang/{$lang}.php";
        $traducciones = file_exists($langPath) ? include $langPath : include PROJECT_ROOT . "/lang/EN.php";

        // 3. Validar que el email no esté vacío y que tenga un formato válido.
        if (empty($email)) {
            $this->jsonResponse(true, $traducciones['invalid_email'] ?? 'Invalid email address.');
            return;
        }

        // 4. Buscar al usuario en la base de datos usando el email normalizado.
        $user = $this->userModel->getUserByEmail($email);

        // 5. Devolver la respuesta.
        if ($user) {
            // Si se encontró un usuario, el email ya está en uso.
            $this->jsonResponse(true, $traducciones['email_already_registered'] ?? 'This email has already been used.');
        } else {
            // Si no se encontró, el email está disponible.
            $this->jsonResponse(false, $traducciones['email_available'] ?? 'Email is available.');
        }
    }



    public function checkTelephone()
    {
        // 1. Obtener el número con máscara y el idioma.
        $raw_telephone = trim($_POST['telephone'] ?? '');
        $lang = strtoupper($_POST['lang'] ?? 'EN');

        // 2. Limpiar el número de teléfono de cualquier carácter que no sea un dígito.
        // Ej: '(123) 456-7890' se convierte en '1234567890'
        $cleaned_telephone = preg_replace('/\D/', '', $raw_telephone);

        // 3. Cargar las traducciones.
        $langPath = PROJECT_ROOT . "/lang/{$lang}.php";
        $traducciones = file_exists($langPath) ? include $langPath : include PROJECT_ROOT . "/lang/EN.php";

        // 4. Validar que el número limpio no esté vacío.
        if (empty($cleaned_telephone)) {
            $this->jsonResponse(true, $traducciones['invalid_telephone'] ?? 'Invalid telephone number.');
            return;
        }

        // 5. Buscar al usuario en la base de datos usando el número ya limpio.
        $user = $this->userModel->getUserByTelephone($cleaned_telephone);

        // 6. Devolver la respuesta.
        if ($user) {
            // El número limpio ya existe en la base de datos.
            $this->jsonResponse(true, $traducciones['telephone_already_used'] ?? 'This telephone has already been used.');
        } else {
            // El número limpio está disponible.
            $this->jsonResponse(false, $cleaned_telephone ?? 'Telephone is available.');
        }
    }




    private function sendPasswordResetEmail($email, $lang = 'EN', $userType = 'User')
    {
        $token = bin2hex(random_bytes(32));
        $createdAt = date('Y-m-d H:i:s');

        $stmt = $this->db->prepare("INSERT INTO password_resets (email, token, created_at) 
                                VALUES (?, ?, ?) 
                                ON DUPLICATE KEY UPDATE token = VALUES(token), created_at = VALUES(created_at)");
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("sss", $email, $token, $createdAt);
        if (!$stmt->execute()) {
            return false;
        }
        $stmt->close();

        // ✅ Leer las URLs directamente desde el ENV
        $appUrl = rtrim($_ENV['APP_URL'] ?? 'https://localhost', '/');
        $appUrlAdmin = rtrim($_ENV['APP_URL_Admin'] ?? 'https://localhost/administrator', '/');
        $appUrlSpecialist = rtrim($_ENV['APP_URL_Specialists'] ?? 'https://localhost/specialist', '/');

        // Definir el host dependiendo del tipo de usuario
        $host = match ($userType) {
            'Administrator' => $appUrlAdmin,
            'Specialist' => $appUrlSpecialist,
            default => $appUrl
        };

        $lang = strtoupper($lang);
        $langSegment = ($lang === 'ES') ? '/es' : '';

        // Construir ruta según el tipo de usuario
        $basePath = 'reset_password';
        $route = match ($userType) {
            'Administrator' => "$basePath/administrator$langSegment",
            'Specialist' => "$basePath/specialist$langSegment",
            default => "$basePath$langSegment"
        };

        // 🔧 Aquí el error estaba: el resetLink debe ir sobre el host correcto:
        $resetLink = $appUrl . '/' . $route . "?token=$token";
        $logoUrl = BASE_URL . 'public/assets/images/logo-index.png';

        if ($lang === 'ES') {
            $subject = 'Restablece tu clave de VITAKEE';
            $body = "
        <body style='margin: 0; padding: 0; background-color: #dceff9;'>
            <div style='padding: 40px 0;'>
                <table align='center' width='100%' cellpadding='0' cellspacing='0'>
                    <tr>
                        <td align='center'>
                            <table style='max-width: 600px; background-color: #ffffff; border-radius: 8px; padding: 40px; font-family: sans-serif; color: #000000;'>
                                <tr><td align='right'><img src='$logoUrl' alt='VITAKEE' style='height: 50px;' /></td></tr>
                                <tr><td>
                                    <h2 style='margin-bottom: 10px;'>¿Restablecer tu contraseña?</h2>
                                    <p>Recibimos una solicitud para restablecer tu contraseña en <strong>VITAKEE</strong>.</p>
                                    <p>Si realizaste esta solicitud, haz clic en el siguiente botón:</p>
                                    <p><a href='$resetLink' style='background-color: #254a7e; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: bold;'>Restablecer contraseña</a></p>
                                    <p>O copia y pega este enlace en tu navegador:</p>
                                    <p style='word-break: break-all;'><a href='$resetLink'>$resetLink</a></p>
                                    <p>Este enlace expirará en <strong>10 minutos</strong>.</p>
                                    <hr style='margin: 40px 0;'>
                                    <p style='font-size: 12px; color: #666;'><strong>VITAKEE</strong><br>1065 Aurora Ln, Corona, CA 92881<br><a href='$host'>$host</a></p>
                                </td></tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
        </body>";
        } else {
            $subject = 'Reset your VITAKEE password';
            $body = "
        <body style='margin: 0; padding: 0; background-color: #dceff9;'>
            <div style='padding: 40px 0;'>
                <table align='center' width='100%' cellpadding='0' cellspacing='0'>
                    <tr>
                        <td align='center'>
                            <table style='max-width: 600px; background-color: #ffffff; border-radius: 8px; padding: 40px; font-family: sans-serif; color: #000000;'>
                                <tr><td align='right'><img src='$logoUrl' alt='VITAKEE' style='height: 50px;' /></td></tr>
                                <tr><td>
                                    <h2 style='margin-bottom: 10px;'>Reset your password?</h2>
                                    <p>We received a request to reset the password for your <strong>VITAKEE</strong> account.</p>
                                    <p>If this was you, click the button below:</p>
                                    <p><a href='$resetLink' style='background-color: #254a7e; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: bold;'>Reset password</a></p>
                                    <p>Or copy and paste this link into your browser:</p>
                                    <p style='word-break: break-all;'><a href='$resetLink'>$resetLink</a></p>
                                    <p>This link will expire in <strong>10 minutes</strong>.</p>
                                    <hr style='margin: 40px 0;'>
                                    <p style='font-size: 12px; color: #666;'><strong>VITAKEE</strong><br>1065 Aurora Ln, Corona, CA 92881<br><a href='$host'>$host</a></p>
                                </td></tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
        </body>";
        }

        $mailer = new MailHelper();
        return $mailer->sendMail($email, $subject, $body);
    }










    public function updatePassword()
    {
        $newPassword = trim($_POST['new_password'] ?? '');
        $userId = $_POST['userId'] ?? null;
        $token = $_POST['token'] ?? null;
        $userType = $_POST['user_type'] ?? null;
        $lang = strtoupper($_POST['lang'] ?? 'EN');

        $langPath = PROJECT_ROOT . "/lang/{$lang}.php";
        $traducciones = file_exists($langPath) ? include $langPath : include PROJECT_ROOT . "/lang/EN.php";

        if (strlen($newPassword) < 6) {
            $this->jsonResponse(false, $traducciones['password_too_short'] ?? 'Password must be at least 6 characters.');
            return;
        }

        // Seleccionar modelo según el tipo de usuario
        switch ($userType) {
            case 'Administrator':
                $model = new AdministratorModel();
                $idField = 'administrator_id';
                break;
            case 'Specialist':
                $model = new SpecialistModel();
                $idField = 'specialist_id';
                break;
            case 'User':
                $model = new UserModel();
                $idField = 'user_id';
                break;
            default:
                $this->jsonResponse(false, 'Invalid user type.');
                return;
        }

        $data = [
            'newPassword' => $newPassword,
            'userId' => $userId ? $userId : null,
            'token' => $token
        ];

        // Si no hay userId pero sí hay token → buscar por email
        if (!$data['userId'] && $token) {
            $stmt = $this->db->prepare("SELECT email FROM password_resets WHERE token = ?");
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $result = $stmt->get_result();
            $resetRow = $result->fetch_assoc();
            $stmt->close();

            $email = $resetRow['email'] ?? null;
            if ($email) {
                if ($userType === 'User') {
                    $user = $model->getUserByEmail($email);
                } else {
                    $user = $model->getByEmail($email);
                }

                $data['userId'] = $user[$idField] ?? null;
            }
        }

        if (empty($data['userId']) && empty($data['token'])) {
            $this->jsonResponse(false, $traducciones['missing_user_or_token'] ?? 'Missing token or user ID.');
            return;
        }

        if ($model->updatePassword($data)) {
            $user = $model->getById($data['userId']);
            $email = $user['email'] ?? null;

            if ($email) {
                // URLs desde ENV
                $appUrl = rtrim($_ENV['APP_URL'] ?? 'https://localhost', '/');
                $appUrlAdmin = rtrim($_ENV['APP_URL_Admin'] ?? 'https://localhost/administrator', '/');
                $appUrlSpecialist = rtrim($_ENV['APP_URL_Specialists'] ?? 'https://localhost/specialist', '/');

                $logoHost = match ($userType) {
                    'Administrator' => $appUrlAdmin,
                    'Specialist' => $appUrlSpecialist,
                    default => $appUrl
                };

                $logoUrl = BASE_URL . 'public/assets/images/logo-index.png';
                $basePath = 'reset_password';
                $route = match ($userType) {
                    'Administrator' => "$basePath/administrator",
                    'Specialist' => "$basePath/specialist",
                    default => $basePath
                };
                $resetLink = $appUrl . '/' . $route;

                $subject = $lang === 'ES'
                    ? 'Tu clave de VITAKEE ha sido actualizada'
                    : 'Your VITAKEE password has been changed';

                $body = $lang === 'ES'
                    ? // HTML en español
                    "<body style='margin:0; padding:0; background-color:#dceff9;'>
                    <div style='padding: 40px 0;'>
                    <table align='center' width='100%' cellpadding='0' cellspacing='0'>
                        <tr><td align='center'>
                        <table style='max-width:600px; background-color:#ffffff; border-radius:8px; padding:40px; font-family:sans-serif; color:#000000;'>
                            <tr><td align='right'><img src='$logoUrl' alt='VITAKEE' style='height:50px;' /></td></tr>
                            <tr><td>
                                <h2>Tu contraseña ha sido actualizada</h2>
                                <p>La contraseña de tu cuenta de <strong>VITAKEE</strong> fue actualizada recientemente.</p>
                                <p>Si no realizaste este cambio, protege tu cuenta:</p>
                                <ol>
                                    <li><a href='$resetLink' style='background:#254a7e; color:white; padding:6px 14px; text-decoration:none; border-radius:4px;'>Acceder a VITAKEE</a></li>
                                    <li>Contacta al soporte: <a href='mailto:support@vitakee.com'>support@vitakee.com</a></li>
                                </ol>
                                <p>La seguridad de tus datos es nuestra prioridad.</p>
                                <hr style='margin:40px 0;'>
                                <p style='font-size:12px; color:#666;'>VITAKEE<br>Tu salud, tus datos — siempre disponibles.<br><a href='$logoHost'>$logoHost</a><br>1065 Aurora Ln, Corona, CA 92881</p>
                            </td></tr>
                        </table>
                        </td></tr>
                    </table>
                    </div>
                </body>"
                    : // HTML en inglés
                    "<body style='margin:0; padding:0; background-color:#dceff9;'>
                    <div style='padding: 40px 0;'>
                    <table align='center' width='100%' cellpadding='0' cellspacing='0'>
                        <tr><td align='center'>
                        <table style='max-width:600px; background-color:#ffffff; border-radius:8px; padding:40px; font-family:sans-serif; color:#000000;'>
                            <tr><td align='right'><img src='$logoUrl' alt='VITAKEE' style='height:50px;' /></td></tr>
                            <tr><td>
                                <h2>Your password was updated</h2>
                                <p>Your <strong>VITAKEE</strong> password was recently updated.</p>
                                <p>If you didn’t do this, secure your account:</p>
                                <ol>
                                    <li><a href='$resetLink' style='background:#254a7e; color:white; padding:6px 14px; text-decoration:none; border-radius:4px;'>Access VITAKEE</a></li>
                                    <li>Contact support: <a href='mailto:support@vitakee.com'>support@vitakee.com</a></li>
                                </ol>
                                <p>Your data security is our top priority.</p>
                                <hr style='margin:40px 0;'>
                                <p style='font-size:12px; color:#666;'>VITAKEE<br>Your health, your data — always within reach.<br><a href='$logoHost'>$logoHost</a><br>1065 Aurora Ln, Corona, CA 92881</p>
                            </td></tr>
                        </table>
                        </td></tr>
                    </table>
                    </div>
                </body>";

                $mailer = new MailHelper();
                $mailer->sendMail($email, $subject, $body);
            }

            $this->jsonResponse(true, $traducciones['password_update_success'] ?? 'Password updated successfully. You will be redirected to login.');
        } else {
            if (!empty($token)) {
                $this->jsonResponse(false, $traducciones['token_expired'] ?? 'The recovery link has expired. Please request a new one.');
            } else {
                $this->jsonResponse(false, $traducciones['password_update_failed'] ?? 'Failed to update the password. Please try again.');
            }
        }
    }






    public function verifySecurityAnswers()
    {
        $answer1 = trim($_POST['answer1'] ?? '');
        $answer2 = trim($_POST['answer2'] ?? '');
        $userId = $_POST['userId'] ?? null;
        $userType = $_POST['user_type'] ?? null;
        $lang = strtoupper($_POST['lang'] ?? 'EN');

        $langPath = PROJECT_ROOT . "/lang/{$lang}.php";
        $traducciones = file_exists($langPath) ? include $langPath : include PROJECT_ROOT . "/lang/EN.php";

        if ($userId <= 0 || !in_array($userType, ['User', 'Administrator', 'Specialist'])) {
            $this->jsonResponse(false, $traducciones['invalid_user_id'] ?? 'Invalid user ID or type.');
            return;
        }

        if (empty($answer1) || empty($answer2)) {
            $this->jsonResponse(false, $traducciones['missing_answers'] ?? 'Security answers are required.');
            return;
        }

        // Consultar las preguntas y respuestas registradas
        $questions = $this->segurityQuestions->getSecurityQuestionsByUserReset($userId, $userType);
        if (!$questions['value']) {
            $this->jsonResponse(false, $traducciones['security_questions_not_found'] ?? 'Security questions not found.');
            return;
        }

        $data = $questions['data'];

        // Comparación directa (puedes normalizar a lowercase si deseas)
        if ($data['answer1'] === $answer1 && $data['answer2'] === $answer2) {
            $this->jsonResponse(true, $traducciones['security_answers_verified'] ?? 'Security answers verified successfully.', [
                'userId' => $userId
            ]);
        } else {
            $this->jsonResponse(false, $traducciones['security_answers_incorrect'] ?? 'Incorrect security answers.');
        }
    }



}
