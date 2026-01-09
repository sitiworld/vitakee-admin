<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/ClientEnvironmentInfo.php';
require_once __DIR__ . '/../config/TimezoneManager.php';
require_once __DIR__ . '/NotificationModel.php';
require_once __DIR__ . '/SpecialistModel.php';
require_once __DIR__ . '/BiomarkerModel.php';

class CommentBiomarkerModel
{
    private \mysqli $db;
    private string $table = 'comment_biomarker';

    private NotificationModel $notificationModel; // <-- Corregido (descomentado)
    private SpecialistModel $specialistModel;
    private BiomarkerModel $biomarkerModel;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->notificationModel = new NotificationModel();
        $this->specialistModel = new SpecialistModel();
        $this->biomarkerModel = new BiomarkerModel();
    }

    /* ============================================================
     * Helpers
     * ============================================================ */

    private function uuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            (mt_rand(0, 0x0fff) | 0x4000),
            (mt_rand(0, 0x3fff) | 0x8000),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    private function nowAudit(?string $actorId): array
    {
        $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
        $env->applyAuditContext($this->db, $actorId);
        (new TimezoneManager($this->db))->applyTimezone();
        return [$env->getCurrentDatetime(), $actorId];
    }

    private function biomarkerNameField(): string
    {
        $idioma = strtoupper($_SESSION['idioma'] ?? 'EN');
        return ($idioma === 'ES') ? 'b.name_es' : 'b.name';
    }

    private function getPanelNameById(string $panelId): ?string
    {
        $sql = "SELECT panel_name FROM test_panels WHERE panel_id = ? AND deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        if (!$stmt)
            return null;
        $stmt->bind_param("s", $panelId);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $res['panel_name'] ?? null;
    }

    /**
     * Obtiene user_id y valores relevantes del registro según el panel asociado
     */
    private function getRecordDetails(string $panelId, string $testId): array
    {
        $panelName = $this->getPanelNameById($panelId);
        if (!$panelName)
            return [null, []];

        $sql = null;
        $extra = [];
        $userId = null;

        // --- INICIO DE MODIFICACIÓN: Añadir 'created_at' a todas las consultas ---
        switch ($panelName) {
            case 'energy_metabolism':
                $sql = "SELECT user_id, created_at, glucose, ketone, hba1c, hba1c_target, derived_value
                        FROM energy_metabolism
                        WHERE energy_metabolism_id = ? AND deleted_at IS NULL";
                break;

            case 'body_composition':
                $sql = "SELECT user_id, created_at, weight_lb, bmi, body_fat_pct, water_pct, muscle_pct,
                                resting_metabolism, visceral_fat, body_age
                        FROM body_composition
                        WHERE body_composition_id = ? AND deleted_at IS NULL";
                break;

            case 'lipid_profile_record':
                $sql = "SELECT user_id, created_at, ldl, hdl, total_cholesterol, triglycerides, non_hdl
                        FROM lipid_profile_record
                        WHERE lipid_profile_record_id = ? AND deleted_at IS NULL";
                break;

            case 'renal_function':
                $sql = "SELECT user_id, created_at, albumin, creatinine, urine_result, serum_creatinine,
                                uric_acid_blood, bun_blood, egfr
                        FROM renal_function
                        WHERE renal_function_id = ? AND deleted_at IS NULL";
                break;

            default:
                return [null, []];
        }
        // --- FIN DE MODIFICACIÓN ---

        $stmt = $this->db->prepare($sql);
        if (!$stmt)
            return [null, []];
        $stmt->bind_param('s', $testId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$row)
            return [null, []];

        $userId = $row['user_id'];
        unset($row['user_id']);
        // No quitamos created_at, se queda en $row
        return [$userId, $row];
    }

    /**
     * Envía una notificación al usuario cuando un especialista comenta.
     * Falla silenciosamente si la notificación no se puede enviar.
     */
    /**
     * Envía una notificación al usuario cuando un especialista comenta.
     * Falla silenciosamente si la notificación no se puede enviar.
     */
    private function sendCommentNotification(string $specialistId, string $panelId, string $testId, string $biomarkerId): void
    {
        try {
            // 1. Obtener el ID del usuario y la fecha del registro
            [$recipientUserId, $recordRow] = $this->getRecordDetails($panelId, $testId);
            if (!$recipientUserId) {
                // Si no hay usuario, no podemos notificar
                return;
            }
            // <-- MODIFICADO: Enviar la fecha cruda (Y-m-d H:i:s)
            $recordDateRaw = $recordRow['created_at'] ?? date('Y-m-d'); 

            // 2. Obtener el nombre del especialista
            $specialist = $this->specialistModel->getById($specialistId);
            $specName = $specialist ? trim($specialist['first_name'] . ' ' . $specialist['last_name']) : 'Especialista';

            // 3. Obtener el name_db (crudo) del biomarcador
            $biomarker = $this->biomarkerModel->getById($biomarkerId);
            
            // <-- MODIFICADO: Guardamos el 'name_db' para que NotificationModel lo traduzca.
            // Si 'name_db' no existe, usamos el 'name' como fallback crudo.
            $rawBiomarkerName = $biomarker['name_db'] ?? ($biomarker['name'] ?? $biomarkerId);

            // 4. Construir la ruta (ajusta si es necesario)
            $route = "/user/records/{$panelId}/{$testId}";

            // 5. Crear la notificación
            $this->notificationModel->create([
                'user_id'         => $recipientUserId, // Notificación PARA el usuario
                'template_key'    => 'new_comment_on_record',
                'rol'             => 'user',
                'module'          => 'comments',
                'route'           => $route,
                'template_params' => [ // <-- MODIFICADO: Se envían datos crudos
                    'specialist_name' => $specName,
                    'biomarker_name'  => $rawBiomarkerName, // (ej. 'SERUM_CREATININE')
                    'record_date'     => $recordDateRaw     // (ej. '2025-11-04 10:30:00')
                ]
            ]);

        } catch (\Throwable $e) {
            // No fallar la operación principal (comentario) si la notificación falla
            error_log("[CommentBiomarkerModel] Notification failed: " . $e->getMessage());
        }
    }


    /* ============================================================
     * Queries
     * ============================================================ */

    public function getAll(): ?array
    {
        $bm = $this->biomarkerNameField();
        $sql = "
            SELECT cb.comment_biomarker_id, cb.id_test_panel, cb.id_test,
                   cb.id_biomarker, cb.id_specialist, cb.comment,
                   {$bm} AS biomarker_name
            FROM {$this->table} cb
            JOIN biomarkers b ON cb.id_biomarker = b.biomarker_id
            WHERE cb.deleted_at IS NULL
            ORDER BY biomarker_name
        ";
        $res = $this->db->query($sql);
        if (!$res)
            throw new \mysqli_sql_exception("Query error: " . $this->db->error);

        $rows = [];
        while ($r = $res->fetch_assoc()) {
            [$uid, $extra] = $this->getRecordDetails($r['id_test_panel'], $r['id_test']);
            $r['user_id'] = $uid;
            $r['extra_data'] = $extra;
            $rows[] = $r;
        }

        return $rows ?: null;
    }

    public function getUserAndTestByCommentId(string $commentId): ?array
    {
        $bm = $this->biomarkerNameField();
        $sql = "
            SELECT cb.comment_biomarker_id, cb.id_test_panel, cb.id_test,
                   cb.id_biomarker, cb.id_specialist, cb.comment,
                   {$bm} AS biomarker_name
            FROM {$this->table} cb
            JOIN biomarkers b ON cb.id_biomarker = b.biomarker_id
            WHERE cb.comment_biomarker_id = ? AND cb.deleted_at IS NULL
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        if (!$stmt)
            throw new \mysqli_sql_exception("Prepare error: " . $this->db->error);
        $stmt->bind_param("s", $commentId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$row)
            return null;

        [$uid, $extra] = $this->getRecordDetails($row['id_test_panel'], $row['id_test']);
        $row['user_id'] = $uid;
        $row['extra_data'] = $extra;
        return $row;
    }

    public function getCommentById(string $id): ?array
    {
        $bm = $this->biomarkerNameField();
        $sql = "
            SELECT cb.comment_biomarker_id, cb.id_test_panel, cb.id_test,
                   cb.id_biomarker, cb.id_specialist, cb.comment,
                   {$bm} AS biomarker_name
            FROM {$this->table} cb
            JOIN biomarkers b ON cb.id_biomarker = b.biomarker_id
            WHERE cb.comment_biomarker_id = ? AND cb.deleted_at IS NULL
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        if (!$stmt)
            throw new \mysqli_sql_exception("Prepare error: " . $this->db->error);
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    public function getCommentsBySpecialist(string $specialistId): ?array
    {
        $bm = $this->biomarkerNameField();
        $sql = "
            SELECT cb.comment_biomarker_id, cb.id_test_panel, cb.id_test,
                   cb.id_biomarker, cb.id_specialist, cb.comment,
                   {$bm} AS biomarker_name, cb.created_at, cb.updated_at
            FROM {$this->table} cb
            JOIN biomarkers b ON cb.id_biomarker = b.biomarker_id
            WHERE cb.id_specialist = ? AND cb.deleted_at IS NULL
            ORDER BY cb.created_at DESC
        ";
        $stmt = $this->db->prepare($sql);
        if (!$stmt)
            throw new \mysqli_sql_exception("Prepare error: " . $this->db->error);
        $stmt->bind_param("s", $specialistId);
        $stmt->execute();
        $res = $stmt->get_result();
        $rows = [];
        while ($r = $res->fetch_assoc()) {
            [$uid, $extra] = $this->getRecordDetails($r['id_test_panel'], $r['id_test']);
            $r['user_id'] = $uid;
            $r['extra_data'] = $extra;
            $rows[] = $r;
        }
        $stmt->close();
        return $rows ?: null;
    }

    public function getCommentsByPanelAndTest(string $panelId, string $testId, string $specialistId): ?array
    {
        $bm = $this->biomarkerNameField();
        $sql = "
            SELECT cb.comment_biomarker_id, cb.id_test_panel, cb.id_test,
                   cb.id_biomarker, cb.id_specialist, cb.comment,
                   {$bm} AS biomarker_name
            FROM {$this->table} cb
            JOIN biomarkers b ON cb.id_biomarker = b.biomarker_id
            WHERE cb.id_test_panel = ? 
              AND cb.id_test = ? 
              AND cb.id_specialist = ? -- <-- Filtro añadido
              AND cb.deleted_at IS NULL
            ORDER BY {$bm}
        ";
        $stmt = $this->db->prepare($sql);
        if (!$stmt)
            throw new \mysqli_sql_exception("Prepare error: " . $this->db->error);
        // --- MODIFICADO: de "ss" a "sss" ---
        $stmt->bind_param("sss", $panelId, $testId, $specialistId);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $res ?: null;
    }

    public function getCommentsByPanelAndTestWithSpecialist(string $panelId, string $testId): ?array
    {
        $bm = $this->biomarkerNameField();

        // Obtenemos el nombre del título del especialista según el idioma
        $idioma = strtoupper($_SESSION['idioma'] ?? 'EN');
        $titleCol = $idioma === 'ES' ? 't.name_es' : 't.name_en';

        $sql = "
            SELECT 
                cb.comment_biomarker_id, cb.id_test_panel, cb.id_test,
                cb.id_biomarker, cb.id_specialist, cb.comment,
                {$bm} AS biomarker_name,
                -- Info del Especialista
                s.first_name AS specialist_first_name,
                s.last_name AS specialist_last_name,
                s.avatar_url AS specialist_avatar_url,
                {$titleCol} AS specialist_title
            FROM {$this->table} cb
            JOIN biomarkers b ON cb.id_biomarker = b.biomarker_id
            -- Joins para info del especialista
            JOIN specialists s ON cb.id_specialist = s.specialist_id
            LEFT JOIN specialists_titles t ON s.title_id = t.title_id
            WHERE 
                cb.id_test_panel = ? 
              AND cb.id_test = ? 
              AND cb.deleted_at IS NULL
              AND s.deleted_at IS NULL -- Asegurarse que el especialista esté activo
            ORDER BY {$bm}
        ";

        $stmt = $this->db->prepare($sql);
        if (!$stmt)
            throw new \mysqli_sql_exception("Prepare error: " . $this->db->error);

        $stmt->bind_param("ss", $panelId, $testId);
        $stmt->execute();
        $res = $stmt->get_result();

        $rows = [];
        while ($r = $res->fetch_assoc()) {
            // Añadimos la comprobación de la imagen
            $r['specialist_image'] = $this->specialistImageExists($r['id_specialist']);
            $rows[] = $r;
        }
        $stmt->close();

        return $rows ?: null;
    }

    public function getById(string $id): ?array
    {
        $row = $this->getCommentById($id);
        if (!$row)
            return null;
        [$uid, $extra] = $this->getRecordDetails($row['id_test_panel'], $row['id_test']);
        $row['user_id'] = $uid;
        $row['extra_data'] = $extra;
        return $row;
    }

    private function getExistingComment(string $panelId, string $testId, string $biomarkerId, string $specialistId): ?array
    {
        $sql = "
            SELECT comment_biomarker_id
            FROM {$this->table}
            WHERE id_test_panel = ? AND id_test = ? AND id_biomarker = ?
              AND id_specialist = ? -- <-- AÑADIDO
              AND deleted_at IS NULL
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        if (!$stmt)
            return null;
        $stmt->bind_param("ssss", $panelId, $testId, $biomarkerId, $specialistId); // <-- Cambiado a "ssss"
        $stmt->execute();
        $r = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $r ?: null;
    }

    public function upsert(array $data): string // <-- 1. Cambiar return type a 'string'
    {
        $actorId = $_SESSION['user_id'] ?? null;
        if (!$actorId) {
            // Lanzar un error si no hay especialista en sesión
            throw new \mysqli_sql_exception("No specialist session found for upsert.");
        }
        $existing = $this->getExistingComment(
            $data['id_test_panel'],
            $data['id_test'],
            $data['id_biomarker'],
            $actorId // <-- AÑADIDO: Pasar el especialista actual
        );
        if ($existing) {
            // 2. Si existe, llama a update CON EL ID EXISTENTE
            $existingId = $existing['comment_biomarker_id'];
            $this->update($existingId, $data);
            return $existingId; // 3. Devuelve el ID actualizado
        } else {
            // 4. Si no, llama a create y devuelve el NUEVO ID
            return $this->create($data);
        }
    }

    public function create(array $data): string // <-- 1. Cambiar return type a 'string'
    {
        $this->db->begin_transaction();
        try {
            $uuid = $this->uuid(); // <-- Este es el ID que necesitamos
            $actorId = $_SESSION['user_id'] ?? null;
            [$createdAt, $createdBy] = $this->nowAudit($actorId);

            $sql = "INSERT INTO {$this->table}
                     (comment_biomarker_id, id_test_panel, id_test, id_biomarker,
                      id_specialist, comment, created_at, created_by)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            if (!$stmt)
                throw new \mysqli_sql_exception("Prepare error: " . $this->db->error);
            $stmt->bind_param(
                "ssssssss",
                $uuid,
                $data['id_test_panel'],
                $data['id_test'],
                $data['id_biomarker'],
                $actorId,
                $data['comment'],
                $createdAt,
                $createdBy
            );
            $stmt->execute();
            $stmt->close();
            $this->db->commit();

            // --- INICIO DE MODIFICACIÓN: Enviar notificación ---
            $this->sendCommentNotification($actorId, $data['id_test_panel'], $data['id_test'], $data['id_biomarker']);
            // --- FIN DE MODIFICACIÓN ---
            
            return $uuid; // <-- 2. Devolver el UUID
        } catch (\mysqli_sql_exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function update(string $id, array $data): bool
    {
        $this->db->begin_transaction();
        try {
            $exists = $this->getCommentById($id);
            if (!$exists)
                throw new \mysqli_sql_exception("Comment not found.");

            $actorId = $_SESSION['user_id'] ?? null;
            [$updatedAt, $updatedBy] = $this->nowAudit($actorId);

            $sql = "UPDATE {$this->table}
                           SET id_test_panel = ?, id_test = ?, id_biomarker = ?,
                               id_specialist = ?, comment = ?, updated_at = ?, updated_by = ?
                         WHERE comment_biomarker_id = ?";
            $stmt = $this->db->prepare($sql);
            if (!$stmt)
                throw new \mysqli_sql_exception("Prepare error: " . $this->db->error);
            $stmt->bind_param(
                "ssssssss",
                $data['id_test_panel'],
                $data['id_test'],
                $data['id_biomarker'],
                $actorId,
                $data['comment'],
                $updatedAt,
                $updatedBy,
                $id
            );
            $stmt->execute();
            $stmt->close();
            $this->db->commit();

            // --- INICIO DE MODIFICACIÓN: Enviar notificación ---
            $this->sendCommentNotification($actorId, $data['id_test_panel'], $data['id_test'], $data['id_biomarker']);
            // --- FIN DE MODIFICACIÓN ---

            return true;
        } catch (\mysqli_sql_exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function delete(string $id): bool
    {
        $this->db->begin_transaction();
        try {
            $exists = $this->getCommentById($id);
            if (!$exists)
                throw new \mysqli_sql_exception("Comment not found.");

            $actorId = $_SESSION['user_id'] ?? null;
            [$deletedAt, $deletedBy] = $this->nowAudit($actorId);

            $sql = "UPDATE {$this->table}
                           SET deleted_at = ?, deleted_by = ?
                         WHERE comment_biomarker_id = ?";
            $stmt = $this->db->prepare($sql);
            if (!$stmt)
                throw new \mysqli_sql_exception("Prepare error: " . $this->db->error);
            $stmt->bind_param("sss", $deletedAt, $deletedBy, $id);
            $stmt->execute();
            $stmt->close();

            $this->db->commit();
            return true;
        } catch (\mysqli_sql_exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    private function specialistImageExists(string $specialistId): bool
    {
        // Define la RUTA del sistema de archivos, no una URL.
        $uploadDir = APP_ROOT . '/uploads/specialist/';
        // Creamos un patrón de búsqueda para glob().
        $pattern = $uploadDir . 'user_' . $specialistId . '.*';
        // Usamos glob() para buscar archivos que coincidan con el patrón.
        $foundFiles = glob($pattern);
        // Si el array no está vacío, significa que se encontró al menos un archivo.
        return !empty($foundFiles);
    }
}