<?php

require_once __DIR__ . '/../config/Database.php';
class AuthModel
{
    private $db;
    private $table = 'users';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function loginUser($email)
    {
        try {
            $stmt = $this->db->prepare("SELECT user_id , first_name, last_name, height, sex_biological, birthday, password FROM {$this->table} WHERE email = ?");
            if (!$stmt) {
                throw new mysqli_sql_exception("Error al preparar la consulta: " . $this->db->error);
            }

            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if (!$result) {
                throw new mysqli_sql_exception("Error al obtener resultado: " . $stmt->error);
            }

            $usuario = $result->fetch_assoc();
            $stmt->close();

            return $usuario;
        } catch (mysqli_sql_exception $e) {
            throw $e;
        }
    }

    public function checkSecurityQuestions($userId)
    {
        try {
            $stmt = $this->db->prepare("SELECT user_id  FROM security_questions WHERE user_id = ?");
            if (!$stmt) {
                throw new mysqli_sql_exception("Error al preparar la consulta: " . $this->db->error);
            }

            $stmt->bind_param("s", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            if (!$result) {
                throw new mysqli_sql_exception("Error al obtener resultado: " . $stmt->error);
            }

            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }

            $stmt->close();
            return $data;
        } catch (mysqli_sql_exception $e) {
            throw $e;
        }
    }

    public function registerUser($data)
    {
        $this->db->begin_transaction();
        try {
            // Verificar si el correo ya está registrado (solo en registros activos)
            $check = $this->db->prepare("SELECT user_id FROM {$this->table} WHERE email = ? AND deleted_at IS NULL");
            if (!$check) {
                throw new mysqli_sql_exception("Error al preparar la verificación: " . $this->db->error);
            }

            $check->bind_param("s", $data['email']);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {
                throw new mysqli_sql_exception("Este correo ya está registrado.");
            }
            $check->close();

            // Inicializar entorno y zona horaria
            $userId = 0; // aún no existe
            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId);
            $tzManager = new TimezoneManager($this->db);
            $tzManager->applyTimezone();

            $createdAt = $env->getCurrentDatetime();

            // Generar UUID manualmente
            $uuid = $this->generateUUIDv4();

            // Insertar usuario con UUID, altura = 0 y rol fijo
            $stmt = $this->db->prepare("INSERT INTO {$this->table} 
            (user_id, first_name, last_name, sex_biological, birthday, height, email, password, telephone, system_type, created_at, created_by)
            VALUES (?, ?, ?, ?, ?, 0, ?, ?, ?, 'US', ?, ?)");
            if (!$stmt) {
                throw new mysqli_sql_exception("Error al preparar inserción: " . $this->db->error);
            }

            $stmt->bind_param(
                "ssssssssss",
                $uuid,
                $data['first_name'],
                $data['last_name'],
                $data['sex_biological'],
                $data['birthday'],
                $data['email'],
                $data['password'],
                $data['telephone'],
                $createdAt,
                $uuid // se registra como su propio created_by
            );

            if (!$stmt->execute()) {
                throw new mysqli_sql_exception("Error al ejecutar inserción: " . $stmt->error);
            }

            $stmt->close();
            $this->db->commit();
            return true;
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    private function generateUUIDv4(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    public function checkImageExists(string $relativePath): bool
    {
        $path = PROJECT_ROOT . '/' . ltrim($relativePath, '/');
        return file_exists($path) && is_file($path);
    }
}


