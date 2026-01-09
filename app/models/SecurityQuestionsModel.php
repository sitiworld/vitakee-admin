<?php

require_once __DIR__ . '/../config/Database.php';

class SecurityQuestionsModel
{
    private $db;
    private $table = "security_questions";

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    private function getUserIdField($userType)
    {
        return match ($userType) {
            'User' => 'user_id_user',
            'Administrator' => 'user_id_admin',
            'Specialist' => 'user_id_specialist',
            default => null,
        };
    }

    public function getSecurityQuestionsByUser($userId)
    {
        try {
            $user_type = $_SESSION['roles_user'] ?? 'User';
            $user_field = $this->getUserIdField($user_type);
            if (!$user_field)
                throw new Exception("Invalid user type.");

            $stmt = $this->db->prepare("
                SELECT security_question_id, question1, answer1, question2, answer2 
                FROM {$this->table} 
                WHERE {$user_field} = ? AND user_type = ? AND deleted_at IS NULL 
                LIMIT 1
            ");
            if (!$stmt)
                throw new mysqli_sql_exception("Error preparing query: " . $this->db->error);

            $stmt->bind_param("ss", $userId, $user_type);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();

            $idioma = $_SESSION['idioma'] ?? 'EN';
            $langPath = PROJECT_ROOT . '/lang/' . $idioma . '.php';
            $lang = file_exists($langPath) ? include $langPath : include PROJECT_ROOT . '/lang/EN.php';

            $labels = array_intersect_key($lang, array_flip([
                'title_security',
                'text_security',
                'confirmButton_security',
                'cancelButton_security',
                'checkbox_security'
            ]));

            if ($data) {
                $data['labels'] = $labels;
                return ['value' => true, 'message' => '', 'data' => ['user_id' => $userId, 'data' => $data, 'role' => $user_type]];
            } else {
                return ['value' => false, 'message' => 'Security questions not found.', 'data' => ['labels' => $labels, 'user_id' => $userId, 'role' => $user_type]];
            }
        } catch (mysqli_sql_exception $e) {
            return ['value' => false, 'message' => $e->getMessage(), 'data' => []];
        }
    }

    public function getSecurityQuestionsByUserReset($userId, $user_type)
    {
        try {
            $user_field = $this->getUserIdField($user_type);
            if (!$user_field)
                throw new Exception("Invalid user type.");

            $stmt = $this->db->prepare("
                SELECT security_question_id, question1, answer1, question2, answer2 
                FROM {$this->table} 
                WHERE {$user_field} = ? AND user_type = ? AND deleted_at IS NULL
                LIMIT 1
            ");
            if (!$stmt)
                throw new mysqli_sql_exception("Error preparing query: " . $this->db->error);

            $stmt->bind_param("ss", $userId, $user_type);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();

            return $data
                ? ['value' => true, 'message' => '', 'data' => $data]
                : ['value' => false, 'message' => 'Security questions not found.', 'data' => []];
        } catch (mysqli_sql_exception $e) {
            return ['value' => false, 'message' => $e->getMessage()];
        }
    }

    public function create($data)
    {
        $this->db->begin_transaction();
        try {
            $user_type = $_SESSION['roles_user'] ?? 'User';
            $created_by = $_SESSION['user_id'] ?? null;
            $user_field = $this->getUserIdField($user_type);
            if (!$user_field)
                throw new Exception("Invalid user type.");

            // Verificar si ya existen preguntas para ese usuario/tipo
            $checkStmt = $this->db->prepare("SELECT security_question_id FROM {$this->table} WHERE {$user_field} = ? AND user_type = ?");
            if (!$checkStmt)
                throw new mysqli_sql_exception("Error preparing check query: " . $this->db->error);
            $checkStmt->bind_param("ss", $data['user_id'], $user_type);
            $checkStmt->execute();
            if ($checkStmt->get_result()->num_rows > 0) {
                throw new Exception('Security questions already registered for this user type.');
            }

            // Auditoría y zona horaria
            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $created_by);
            (new TimezoneManager($this->db))->applyTimezone();
            $created_at = $env->getCurrentDatetime();

            // Generar UUID como ID de la fila
            $uuid = $this->generateUUIDv4();

            $query = "INSERT INTO {$this->table} 
            (security_question_id, {$user_field}, user_type, question1, answer1, question2, answer2, created_at, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->db->prepare($query);
            if (!$stmt)
                throw new mysqli_sql_exception("Error preparing insert query: " . $this->db->error);

            $stmt->bind_param(
                "sssssssss",
                $uuid,
                $data['user_id'],
                $user_type,
                $data['question1'],
                $data['answer1'],
                $data['question2'],
                $data['answer2'],
                $created_at,
                $created_by
            );

            $stmt->execute();
            $stmt->close();

            $this->db->commit();
            return ['value' => true, 'message' => 'Security questions saved successfully.'];
        } catch (Exception $e) {
            $this->db->rollback();
            return ['value' => false, 'message' => $e->getMessage()];
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


    public function update($id, $data)
    {
        $this->db->begin_transaction();
        try {
            $user_type = $_SESSION['roles_user'] ?? 'User';
            $updated_by = $_SESSION['user_id'] ?? null;

            $checkStmt = $this->db->prepare("SELECT security_question_id FROM {$this->table} WHERE security_question_id = ? AND user_type = ? LIMIT 1");
            if (!$checkStmt)
                throw new mysqli_sql_exception("Error preparing check query: " . $this->db->error);
            $checkStmt->bind_param("ss", $id, $user_type);
            $checkStmt->execute();
            if ($checkStmt->get_result()->num_rows == 0) {
                return ['status' => 'error', 'message' => 'Security questions not found for this user type.'];
            }

            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $updated_by);
            (new TimezoneManager($this->db))->applyTimezone();
            $updated_at = $env->getCurrentDatetime();

            $query = "UPDATE {$this->table} 
                      SET question1 = ?, answer1 = ?, question2 = ?, answer2 = ?, updated_by = ?, updated_at = ?
                      WHERE security_question_id = ? AND user_type = ?";
            $stmt = $this->db->prepare($query);
            if (!$stmt)
                throw new mysqli_sql_exception("Error preparing update query: " . $this->db->error);

            $stmt->bind_param("ssssssss", $data['question1'], $data['answer1'], $data['question2'], $data['answer2'], $updated_by, $updated_at, $id, $user_type);
            $stmt->execute();

            $this->db->commit();
            return ['status' => 'success', 'message' => 'Security questions updated successfully.'];
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function delete($id)
    {
        $user_type = $_SESSION['roles_user'] ?? 'User';
        $lang = $_SESSION['idioma'] ?? 'EN';
        $deleted_by = $_SESSION['user_id'] ?? null;

        $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
        $env->applyAuditContext($this->db, $deleted_by);
        (new TimezoneManager($this->db))->applyTimezone();
        $deleted_at = $env->getCurrentDatetime();

        $checkStmt = $this->db->prepare("SELECT security_question_id FROM {$this->table} WHERE security_question_id = ? AND user_type = ? AND deleted_at IS NULL LIMIT 1");
        if (!$checkStmt)
            throw new mysqli_sql_exception("Error preparing check query: " . $this->db->error);
        $checkStmt->bind_param("ss", $id, $user_type);
        $checkStmt->execute();
        if ($checkStmt->get_result()->num_rows == 0) {
            return [
                'status' => 'error',
                'message' => $lang === 'ES'
                    ? 'Preguntas de seguridad no encontradas o ya eliminadas para este tipo de usuario.'
                    : 'Security questions not found or already deleted for this user type.'
            ];
        }

        $this->db->begin_transaction();
        try {
            $stmt = $this->db->prepare("UPDATE {$this->table} SET deleted_by = ?, deleted_at = ? WHERE security_question_id = ? AND user_type = ?");
            if (!$stmt)
                throw new mysqli_sql_exception("Error preparing delete query: " . $this->db->error);

            $stmt->bind_param("ssss", $deleted_by, $deleted_at, $id, $user_type);
            $stmt->execute();
            $this->db->commit();

            return [
                'status' => 'success',
                'message' => $lang === 'ES'
                    ? 'Preguntas de seguridad eliminadas correctamente.'
                    : 'Security questions deleted successfully.'
            ];
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
