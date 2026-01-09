<?php
require_once __DIR__ . '/../config/Database.php';

// Dependencias para auditoría y zona horaria (basado en el modelo guía)
require_once __DIR__ . '/../config/ClientEnvironmentInfo.php';
require_once __DIR__ . '/../config/TimezoneManager.php';


class SpecialistReviewsModel
{
    private $db;
    private $table = 'specialist_reviews';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll()
    {
        $sql = "
        SELECT r.*,
               CONCAT(u.first_name, ' ', u.last_name) AS user_name
        FROM {$this->table} r
        LEFT JOIN users u ON r.user_id = u.user_id
        WHERE r.deleted_at IS NULL
        ORDER BY r.review_id DESC
        ";

        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id)
    {
        $sql = "
        SELECT r.*,
               CONCAT(u.first_name, ' ', u.last_name) AS user_name
        FROM {$this->table} r
        LEFT JOIN users u ON r.user_id = u.user_id
        WHERE r.review_id = ? AND r.deleted_at IS NULL
        LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getByIdSpecialist($id)
    {
        $sql = "SELECT s.*,
                       u.first_name,
                       u.last_name,
                       CONCAT(u.first_name, ' ', u.last_name) AS full_name,
                       u.telephone,
                       u.sex_biological,
                       u.user_id
                FROM {$this->table} s
                INNER JOIN users u ON s.user_id = u.user_id
                WHERE s.specialist_id = ?
                  AND s.deleted_at IS NULL
                  AND u.deleted_at IS NULL";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new \mysqli_sql_exception("Error preparando la consulta: " . $this->db->error);
        }

        $stmt->bind_param("s", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $rows = $res->fetch_all(MYSQLI_ASSOC);

        foreach ($rows as &$row) {
            $basePath = __DIR__ . "/../../uploads/users/";
            $publicBase = "uploads/users/";
            $filename = "user_" . $row['user_id'];

            $extensions = ['jpg', 'jpeg', 'png'];
            $row['profile_image'] = false; // valor por defecto

            foreach ($extensions as $ext) {
                $filePath = $basePath . $filename . "." . $ext;
                $publicPath = $publicBase . $filename . "." . $ext;

                if (file_exists($filePath)) {
                    $row['profile_image'] = $publicPath;
                    break;
                }
            }
        }

        return $rows;
    }

    public function getByRequestId($second_opinion_id)
    {
        $sql = "SELECT r.*,
                       CONCAT(u.first_name, ' ', u.last_name) AS user_name
                FROM {$this->table} r
                LEFT JOIN users u ON r.user_id = u.user_id
                WHERE r.second_opinion_id = ? AND r.deleted_at IS NULL
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new \mysqli_sql_exception("Error preparando la consulta: " . $this->db->error);
        }

        $stmt->bind_param("s", $second_opinion_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function create($data)
    {
        $this->db->begin_transaction();
        try {
            // Variables de sesión (como en el modelo guía)
            $lang   = $_SESSION['idioma'] ?? 'EN';
            $userId = $_SESSION['user_id'] ?? null; // ID del usuario que crea la reseña

            // Dependencias de notificación (como en el modelo guía)
            require_once __DIR__ . '/../models/NotificationModel.php';
            require_once __DIR__ . '/../helpers/NotificationTemplateHelper.php';

            // Instanciar modelo de notificación (como en el modelo guía)
            $notificationModel = new NotificationModel();
            
            // --- Lógica de Auditoría y Zona Horaria ---
            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId);

            $tzManager = new TimezoneManager($this->db);
            $tzManager->applyTimezone();

            $createdAt = $env->getCurrentDatetime();

            // --- Inserción de la Reseña ---
            $reviewId = $this->generateUUIDv4();

            $stmt = $this->db->prepare("INSERT INTO {$this->table}
                (review_id, specialist_id, user_id, second_opinion_id, rating, comment, created_at, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            if (!$stmt) {
                throw new mysqli_sql_exception("Error preparando consulta: " . $this->db->error);
            }

            $secondOpinionId = $data['second_opinion_id'] ?? null;

            $stmt->bind_param(
                "ssssssss",
                $reviewId,
                $data['specialist_id'],
                $data['user_id'], // Usuario que deja la reseña
                $secondOpinionId,
                $data['rating'],
                $data['comment'],
                $createdAt,
                $userId // Usuario que ejecuta la acción (el mismo que user_id)
            );

            $stmt->execute();
            $stmt->close(); // (Buena práctica, como en el modelo guía)

            // --- INICIO DE LÓGICA DE NOTIFICACIÓN (Estilo Guía) ---
            
            // 1. Obtener el nombre del paciente (autor de la reseña)
            $user_name = 'Un paciente'; // Valor por defecto
            if (isset($data['user_id'])) {
                $userSql = "SELECT CONCAT(first_name, ' ', last_name) AS full_name FROM users WHERE user_id = ? LIMIT 1";
                $userStmt = $this->db->prepare($userSql);
                $userStmt->bind_param("s", $data['user_id']);
                $userStmt->execute();
                $userResult = $userStmt->get_result()->fetch_assoc();
                if ($userResult && !empty(trim($userResult['full_name']))) {
                    $user_name = $userResult['full_name'];
                }
                $userStmt->close(); // (Buena práctica)
            }

            // 2. Preparar parámetros para la plantilla
            $params = [
                'user_name' => $user_name,
                'rating'    => $data['rating']
            ];
            
            // 3. Definir destinatario, ruta y clave
            $recipientId = $data['specialist_id']; // Notificación PARA el especialista
            $templateKey = 'new_specialist_review';
            $route = 'service_requests?id=' . $reviewId; // (Ruta genérica siguiendo el patrón del guía)

            // 4. Construir la fila de notificación (como en el modelo guía)
            $dataRow = NotificationTemplateHelper::buildForInsert([
                'template_key'    => $templateKey,
                'template_params' => $params,
                'route'           => $route,
                'module'          => 'reviews',     // Módulo definido en la plantilla
                'user_id'         => $recipientId  // El destinatario es el especialista
            ]);

            // 5. Crear la notificación (como en el modelo guía)
            $notificationModel->create($dataRow);

            // --- FIN DE LÓGICA DE NOTIFICACIÓN ---

            $this->db->commit();
            
            // Retorno estándar (como en el modelo guía)
            $message = $lang === 'ES' 
                ? 'Reseña creada exitosamente.' 
                : 'Review created successfully.';
            return ['value' => true, 'message' => $message];

        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            // Retorno de error (como en el modelo guía)
            return ['value' => false, 'message' => $e->getMessage()];
        } catch (Exception $e) {
            // Capturar cualquier otra excepción (ej. de NotificationModel)
            $this->db->rollback();
            // Retorno de error (como en el modelo guía)
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
            $userId = $_SESSION['user_id'] ?? null;

            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId);

            $tzManager = new TimezoneManager($this->db);
            $tzManager->applyTimezone();

            $updatedAt = $env->getCurrentDatetime();

            // Si quieres permitir actualizar también el second_opinion_id, descomenta y ajusta:
            // $sql = "UPDATE {$this->table}
            //         SET rating = ?, comment = ?, second_opinion_id = ?, updated_at = ?, updated_by = ?
            //         WHERE review_id = ?";
            // $stmt = $this->db->prepare($sql);
            // $secondOpinionId = $data['second_opinion_id'] ?? null;
            // $stmt->bind_param("ssssss", $data['rating'], $data['comment'], $secondOpinionId, $updatedAt, $userId, $id);

            $sql = "UPDATE {$this->table}
                     SET rating = ?, comment = ?, updated_at = ?, updated_by = ?
                     WHERE review_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param(
                "sssss",
                $data['rating'],
                $data['comment'],
                $updatedAt,
                $userId,
                $id
            );

            $stmt->execute();
            $stmt->close(); // (Buena práctica)
            $this->db->commit();
            return true;
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function delete($id)
    {
        $this->db->begin_transaction();
        try {
            $userId = $_SESSION['user_id'] ?? null;

            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId);

            $tzManager = new TimezoneManager($this->db);
            $tzManager->applyTimezone();

            $deletedAt = $env->getCurrentDatetime();

            $stmt = $this->db->prepare("UPDATE {$this->table} SET deleted_at = ?, deleted_by = ? WHERE review_id = ?");
            $stmt->bind_param("sss", $deletedAt, $userId, $id);
            $stmt->execute();
            $stmt->close(); // (Buena práctica)

            $this->db->commit();
            return true;
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
}