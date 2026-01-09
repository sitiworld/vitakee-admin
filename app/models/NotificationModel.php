<?php
declare(strict_types=1);

// Dependencias requeridas por el modelo de referencia
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/ClientEnvironmentInfo.php';
require_once __DIR__ . '/../config/TimezoneManager.php';
require_once __DIR__ . '/../helpers/UuidHelper.php';

class NotificationModel
{
    private $db;
    private string $table = 'notifications';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /* ========================================================================
     * SECCIÓN 1: MÉTODOS DE AYUDA
     * ======================================================================== */

    private function nowWithAudit(): array
    {
        $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb'); 
        $actorId = $_SESSION['user_id'] ?? $_SESSION['specialist_id'] ?? $_SESSION['administrator_id'] ?? 0;
        $env->applyAuditContext($this->db, $actorId);

        $tzManager = new TimezoneManager($this->db);
        $tzManager->applyTimezone();

        return [$env->getCurrentDatetime(), $env];
    }

    private function mustExist(string $id): array
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE notifications_id = ? AND deleted_at IS NULL
                ORDER BY created_at DESC
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new mysqli_sql_exception("Error al preparar verificación: " . $this->db->error);
        }
        $stmt->bind_param('s', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $stmt->close();

        if (!$row) {
            throw new mysqli_sql_exception("Notificación no encontrada.");
        }
        return $row;
    }

    private function validarFlag(string $flag, int $valor): void
    {
        if (!in_array($flag, ['new', 'read_unread'], true)) {
            throw new InvalidArgumentException("Flag inválido: {$flag}");
        }
        if (!in_array($valor, [0, 1], true)) {
            throw new InvalidArgumentException("Valor inválido para flag {$flag}. Use 0 o 1.");
        }
    }

    private function actualizarFlag(string $id, string $flag, int $valor): bool
    {
        $this->mustExist($id);
        $this->validarFlag((string)$flag, (int)$valor);

        [$now]   = $this->nowWithAudit();
        $actorId = $_SESSION['user_id'] ?? $_SESSION['specialist_id'] ?? $_SESSION['administrator_id'] ?? $id;

        $sql = "UPDATE {$this->table}
                SET `{$flag}` = ?, updated_at = ?, updated_by = ?
                WHERE notifications_id = ? AND deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new mysqli_sql_exception("Error al preparar actualización de flag: " . $this->db->error);
        }
        $stmt->bind_param('isss', $valor, $now, $actorId, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    /* ========================================================================
     * SECCIÓN 2: FUNCIONES PÚBLICAS
     * ======================================================================== */

    public function getAll()
    {
        try {
            $query = "SELECT * FROM {$this->table}
                      WHERE deleted_at IS NULL
                      ORDER BY created_at DESC";
            $result = $this->db->query($query);
            if (!$result) {
                throw new mysqli_sql_exception("Error fetching notifications: " . $this->db->error);
            }

            $items = [];
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
            return $this->translateBiomarkerData($items);
        } catch (mysqli_sql_exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function countAlertsUser($user_id, $flag = 0)
    {
        try {
            $whereFlag = '';
            if (is_array($flag)) {
                $sanitized_flags = implode(',', array_map('intval', $flag));
                $whereFlag = "AND `new` IN ($sanitized_flags)";
            } else {
                $whereFlag = "AND `new` = ?";
            }

            $query = "SELECT COUNT(*) as total FROM {$this->table}
                      WHERE user_id = ? $whereFlag AND deleted_at IS NULL";
            $stmt = $this->db->prepare($query);

            if (is_array($flag)) {
                $stmt->bind_param("s", $user_id);
            } else {
                $stmt->bind_param("si", $user_id, $flag);
            }

            $stmt->execute();
            $result = $stmt->get_result();
            if (!$result) {
                throw new mysqli_sql_exception("Error counting notifications: " . $this->db->error);
            }

            $row = $result->fetch_assoc();
            return (int)$row['total'];
        } catch (mysqli_sql_exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function countAlertsUserUnread($user_id, $flag = 0)
    {
        try {
            $whereFlag = '';
            if (is_array($flag)) {
                $sanitized_flags = implode(',', array_map('intval', $flag));
                $whereFlag = "AND `read_unread` IN ($sanitized_flags)";
            } else {
                $whereFlag = "AND `read_unread` = ?";
            }

            $query = "SELECT COUNT(*) as total FROM {$this->table}
                      WHERE user_id = ? $whereFlag AND deleted_at IS NULL";
            $stmt = $this->db->prepare($query);

            if (is_array($flag)) {
                $stmt->bind_param("s", $user_id);
            } else {
                $stmt->bind_param("si", $user_id, $flag);
            }

            $stmt->execute();
            $result = $stmt->get_result();
            if (!$result) {
                throw new mysqli_sql_exception("Error counting unread notifications: " . $this->db->error);
            }

            $row = $result->fetch_assoc();
            return (int)$row['total'];
        } catch (mysqli_sql_exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function getById(string $id): ?array
    {
        try {
            $sql = "SELECT * FROM {$this->table}
                    WHERE notifications_id = ?
                    ORDER BY created_at DESC
                    LIMIT 1";
            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                throw new mysqli_sql_exception("Error al preparar consulta: " . $this->db->error);
            }
            $stmt->bind_param('s', $id);
            $stmt->execute();
            $res = $stmt->get_result();
            $row = $res->fetch_assoc();
            $stmt->close();

            if ($row) {
                $translated_row = $this->translateBiomarkerData([$row]);
                return $translated_row[0] ?? null;
            }
            return null;

        } catch (mysqli_sql_exception $e) {
            throw $e;
        }
    }

    public function exists($user_id, $record_id, $biomarker_id)
    {
        try {
            $sql = "SELECT notifications_id FROM {$this->table}
                    WHERE user_id = ?
                      AND JSON_UNESCAPED(JSON_EXTRACT(template_params, '$.id_record')) = ?
                      AND JSON_UNESCAPED(JSON_EXTRACT(template_params, '$.id_biomarker')) = ?
                      AND deleted_at IS NULL
                    ORDER BY created_at DESC
                    LIMIT 1";

            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                throw new mysqli_sql_exception("Error al preparar la consulta 'exists': " . $this->db->error);
            }

            $stmt->bind_param("sss", $user_id, $record_id, $biomarker_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $exists = $result->num_rows > 0;
            $stmt->close();

            return $exists;
        } catch (mysqli_sql_exception $e) {
            error_log("NotificationModel::exists error: " . $e->getMessage());
            return false;
        }
    }

    public function getByUserId($user_id, $limit = 20, $offset = 0)
    {
        try {
            $sql = "SELECT *
                    FROM {$this->table}
                    WHERE user_id = ? AND deleted_at IS NULL
                    ORDER BY created_at DESC
                    LIMIT ? OFFSET ?";

            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                throw new mysqli_sql_exception("Error al preparar listado por usuario: " . $this->db->error);
            }

            $stmt->bind_param('sii', $user_id, $limit, $offset);
            $stmt->execute();
            $res = $stmt->get_result();
            $data = $res->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            return $this->translateBiomarkerData($data);

        } catch (mysqli_sql_exception $e) {
            return ['value' => false, 'message' => $e->getMessage()];
        }
    }
    
    private function getUserAlertsByFlag($user_id, $read_unread_flag, $limit = 20, $offset = 0)
    {
        try {
            $whereFlag = '';
            $types = 's';
            $params = [$user_id];

            if (is_array($read_unread_flag)) {
                $clean_flags = array_map('intval', $read_unread_flag);
                if (empty($clean_flags)) return [];
                $placeholders = implode(',', array_fill(0, count($clean_flags), '?'));
                $whereFlag = "AND `read_unread` IN ($placeholders)";
                $types .= str_repeat('i', count($clean_flags));
                $params = array_merge($params, $clean_flags);
            } else {
                $whereFlag = "AND `read_unread` = ?";
                $types .= 'i';
                $params[] = (int)$read_unread_flag;
            }

            $sql = "SELECT *
                    FROM {$this->table}
                    WHERE user_id = ? AND deleted_at IS NULL $whereFlag
                    ORDER BY created_at DESC
                    LIMIT ? OFFSET ?";

            $types .= 'ii';
            $params[] = $limit;
            $params[] = $offset;

            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                throw new mysqli_sql_exception("Error al preparar listado por flag: " . $this->db->error);
            }

            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $res = $stmt->get_result();
            $data = $res->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            return $this->translateBiomarkerData($data);

        } catch (mysqli_sql_exception $e) {
            return ['value' => false, 'message' => $e->getMessage()];
        }
    }

    /* ========================================================================
     * TRADUCCIÓN / ENRIQUECIMIENTO DE template_params (SIN TOCAR BD)
     * ======================================================================== */
    /** Decodifica template_params aunque venga doblemente codificado ( "\"{...}\"" ) */
    private function decodeTemplateParams($raw) {
        // Primer decode
        $decoded = json_decode($raw, true);

        // Si el primer decode produce un string, intenta decodificar por segunda vez
        if (is_string($decoded)) {
            $decoded2 = json_decode($decoded, true);
            if (is_array($decoded2)) {
                return $decoded2;
            }
        }

        // Si ya es array, úsalo; si falla todo, devuelve null para conservar el raw
        return is_array($decoded) ? $decoded : null;
    }

    private function translateBiomarkerData(array $data): array
    {
        if (empty($data)) return [];

        // === Idioma de sesión ===
        $idioma = strtoupper($_SESSION['idioma'] ?? 'EN'); // 'ES' o 'EN'
        $isES   = ($idioma === 'ES');

        // =========================
        // MAPAS DE TRADUCCIÓN BASE
        // =========================

        // Biomarker status (lookup en minúsculas)
        $status_map_es = [
            'high'   => 'Alto',
            'low'    => 'Bajo',
            'normal' => 'Normal',
        ];
        // EN estandarizado
        $status_norm_en = [
            'high'   => 'High',
            'low'    => 'Low',
            'normal' => 'Normal',
        ];

        // ---- Second Opinion: TIPOS (enum oficial del módulo de SO) ----
        // Ejemplos habituales en tu proyecto: document_review | appointment_request | block
        $so_req_type_map_en = [
            'document_review'     => 'Document Review',
            'appointment_request' => 'Appointment Request',
            'block'               => 'Block',
        ];
        $so_req_type_map_es = [
            'document_review'     => 'Revisión de Documentos',
            'appointment_request' => 'Solicitud de Cita',
            'block'               => 'Bloquear',
        ];
        // Alias retrocompatibles -> enum oficial
        $so_req_type_alias = [
            'analysis_review' => 'document_review',
            'study_review'    => 'document_review',
            'consultation'    => 'appointment_request', // alias antiguo
        ];

        // ---- Pricing/Servicios ofertados por el especialista (service_type del SQL) ----
        // Enum: CONSULTATION | REVIEW | FOLLOW_UP | SUBSCRIPTION
        $svc_type_map_en = [
            'consultation' => 'Consultation',
            'review'       => 'Review',
            'follow_up'    => 'Follow-up',
            'subscription' => 'Subscription',
        ];
        $svc_type_map_es = [
            'consultation' => 'Consulta',
            'review'       => 'Revisión',
            'follow_up'    => 'Seguimiento',
            'subscription' => 'Suscripción',
        ];
        // Alias para FOLLOW_UP y variantes de escritura
        $svc_type_alias = [
            'followup'    => 'follow_up',
            'follow-up'   => 'follow_up',
            'follow_up'   => 'follow_up',
            'consultation'=> 'consultation',
            'review'      => 'review',
            'subscription'=> 'subscription',
        ];

        // ---- Second Opinion: STATUS (enum típico) ----
        // pending | awaiting_payment | upcoming | completed | cancelled | rejected
        $so_status_map_en = [
            'pending'          => 'Pending',
            'awaiting_payment' => 'Awaiting Payment',
            'upcoming'         => 'Upcoming',
            'completed'        => 'Completed',
            'cancelled'        => 'Cancelled',
            'rejected'         => 'Rejected',
        ];
        $so_status_map_es = [
            'pending'          => 'Pendiente',
            'awaiting_payment' => 'Esperando Pago',
            'upcoming'         => 'Próxima',
            'completed'        => 'Completada',
            'cancelled'        => 'Cancelada',
            'rejected'         => 'Rechazada',
        ];

        // ---- Second Opinion: SCOPE (habitual) ----
        // share_all | share_none | share_custom
        $so_scope_map_en = [
            'share_all'    => 'Share all',
            'share_none'   => 'Do not share',
            'share_custom' => 'Share custom',
        ];
        $so_scope_map_es = [
            'share_all'    => 'Compartir todo',
            'share_none'   => 'No compartir',
            'share_custom' => 'Compartir personalizado',
        ];

        // =========================
        // HELPERS
        // =========================
        $normalizeKey = static fn($v) => mb_strtolower(trim((string)$v), 'UTF-8');

        $capEN = static function ($v) {
            $v = (string)$v;
            $v = mb_strtolower($v, 'UTF-8');
            return ucfirst($v);
        };

        $formatDate = static function ($value, bool $isES) {
            if (empty($value)) return $value;
            try {
                $dt = new \DateTime($value);
                // EN => mm/dd/YYYY, ES => dd/mm/YYYY
                return $isES ? $dt->format('d/m/Y') : $dt->format('m/d/Y');
            } catch (\Exception $e) {
                return $value; // deja tal cual si no se puede parsear
            }
        };

        // =========================
        // 1) Decodificar y recolectar claves de biomarcadores (lookup case-insensitive)
        // =========================
        $nameDbKeys = [];
        $processed  = [];

        foreach ($data as $row) {
            $raw    = $row['template_params'] ?? '{}';
            $params = $this->decodeTemplateParams($raw);

            if ($params === null) {
                $row['template_params'] = $raw;
                $processed[] = $row;
                continue;
            }

            if (!empty($params['biomarker_name'])) {
                $nameDbKeys[] = $normalizeKey($params['biomarker_name']);
            }

            $row['template_params'] = $params;
            $processed[] = $row;
        }

        // =========================
        // 2) Lookup de biomarcadores por LOWER(name_db)
        // =========================
        $translation_map = []; // clave_min => ['EN'=>..., 'ES'=>...]
        if (!empty($nameDbKeys)) {
            $unique       = array_values(array_unique($nameDbKeys));
            $placeholders = implode(',', array_fill(0, count($unique), '?'));
            $types        = str_repeat('s', count($unique));

            $sql = "SELECT LOWER(name_db) AS name_db_lower, name, name_es
                      FROM biomarkers
                     WHERE LOWER(name_db) IN ($placeholders)";

            if ($stmt = $this->db->prepare($sql)) {
                $stmt->bind_param($types, ...$unique);
                $stmt->execute();
                $res = $stmt->get_result();
                while ($bm = $res->fetch_assoc()) {
                    $translation_map[$bm['name_db_lower']] = [
                        'EN' => $bm['name'] ?? $bm['name_db_lower'],
                        'ES' => (!empty($bm['name_es']) ? $bm['name_es'] : ($bm['name'] ?? $bm['name_db_lower'])),
                    ];
                }
                $stmt->close();
            }
        }

        // =========================
        // 3) Aplicar estandarización y traducciones
        // =========================
        $final = [];

        foreach ($processed as $row) {
            $tpl    = $row['template_key'] ?? '';
            $params = $row['template_params'];

            if (!is_array($params)) {
                $final[] = $row; // ya preservado en crudo
                continue;
            }

            // ---------- NORMALIZACIÓN TRANSVERSAL POR PARÁMETROS ----------

            // biomarker_name
            if (!empty($params['biomarker_name'])) {
                $k = $normalizeKey($params['biomarker_name']);
                if (isset($translation_map[$k])) {
                    $params['biomarker_name'] = $isES ? $translation_map[$k]['ES'] : $translation_map[$k]['EN'];
                } else {
                    $params['biomarker_name'] = (string)$params['biomarker_name'];
                }
            }

            // status -> High/Low/Normal => ES/EN
            if (isset($params['status'])) {
                $norm = $normalizeKey($params['status']);
                $params['status'] = $isES
                    ? ($status_map_es[$norm] ?? $params['status'])
                    : ($status_norm_en[$norm] ?? $capEN($norm));
            }

            // -------- Unificar & traducir request_type/type_request/service_type --------
            $keyForType = null;
            if (array_key_exists('request_type', $params)) {
                $keyForType = 'request_type';
            } elseif (array_key_exists('type_request', $params)) {
                $keyForType = 'type_request';
            } elseif (array_key_exists('service_type', $params)) {
                $keyForType = 'service_type';
            }

            if ($keyForType !== null) {
                $rawType = $normalizeKey($params[$keyForType]);

                // 1) Resolver alias de Second Opinion y mapear si aplica
                $maybeSO = $so_req_type_alias[$rawType] ?? $rawType;
                if (isset($so_req_type_map_en[$maybeSO]) || isset($so_req_type_map_es[$maybeSO])) {
                    $params[$keyForType] = $isES
                        ? ($so_req_type_map_es[$maybeSO] ?? $capEN($maybeSO))
                        : ($so_req_type_map_en[$maybeSO] ?? $capEN($maybeSO));
                } else {
                    // 2) Resolver como service_type del pricing
                    $svc = $svc_type_alias[$rawType] ?? $rawType;
                    if (isset($svc_type_map_en[$svc]) || isset($svc_type_map_es[$svc])) {
                        $params[$keyForType] = $isES
                            ? ($svc_type_map_es[$svc] ?? $capEN($svc))
                            : ($svc_type_map_en[$svc] ?? $capEN($svc));
                    } else {
                        $params[$keyForType] = $isES ? $params[$keyForType] : $capEN($rawType);
                    }
                }
            }

            // new_status -> enum oficial + traducción (SO)
            if (isset($params['new_status'])) {
                $ns = $normalizeKey($params['new_status']);
                $params['new_status'] = $isES
                    ? ($so_status_map_es[$ns] ?? $params['new_status'])
                    : ($so_status_map_en[$ns] ?? $capEN($ns));
            }

            // scope_request -> enum + traducción (SO)
            if (isset($params['scope_request'])) {
                $sc = $normalizeKey($params['scope_request']);
                $params['scope_request'] = $isES
                    ? ($so_scope_map_es[$sc] ?? $params['scope_request'])
                    : ($so_scope_map_en[$sc] ?? $capEN($sc));
            }

            // Fechas comunes
            foreach ([
                'record_date',
                'schedule_date',
                'date',
                'shared_until',
                'request_date_to',
                'request_date_end',
                'request_date_from'
            ] as $dateField) {
                if (!empty($params[$dateField])) {
                    $params[$dateField] = $formatDate($params[$dateField], $isES);
                }
            }

            // Específicos por template (placeholder para futura lógica)
            switch ($tpl) {
                case 'biomarker_out_of_range':
                case 'new_comment_on_record':
                case 'renal_urine_result_abnormal':
                case 'second_opinion_request_received':
                case 'second_opinion_cancelled_by_user':
                case 'second_opinion_pending_reminder':
                case 'second_opinion_status_changed':
                case 'video_call_scheduled':
                case 'video_call_cancelled':
                case 'new_specialist_review':
                case 'welcome_user':
                case 'password_reset_success':
                default:
                    break;
            }

            $row['template_params'] = $params;
            $final[] = $row;
        }

        return $final;
    }

    /* ========================================================================
     * Filtros/Helpers Públicos que ya usan translateBiomarkerData
     * ======================================================================== */

    public function getActiveAlertsByUserId($user_id, $limit = 20, $offset = 0)
    {
        return $this->getUserAlertsByFlag($user_id, 0, $limit, $offset);
    }
    public function getDismissedAlertsByUserId($user_id, $limit = 20, $offset = 0)
    {
        return $this->getUserAlertsByFlag($user_id, 1, $limit, $offset);
    }
    public function getAllAlertsByUserId($user_id, $limit = 20, $offset = 0)
    {
        return $this->getUserAlertsByFlag($user_id, [0, 1], $limit, $offset);
    }

    public function getByStatus($status)
    {
        try {
            if (in_array($status, ['Alto', 'Bajo', 'Normal'], true)) {
                $status_map_es_reverse = [
                    'Alto' => 'High',
                    'Bajo' => 'Low',
                    'Normal' => 'Normal',
                ];
                $status = $status_map_es_reverse[$status];
            }

            $sql = "SELECT * FROM {$this->table}
                    WHERE JSON_UNESCAPED(JSON_EXTRACT(template_params, '$.status')) = ?
                      AND deleted_at IS NULL
                    ORDER BY created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("s", $status);
            $stmt->execute();
            $result = $stmt->get_result();
            $items = [];
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
            return $this->translateBiomarkerData($items);

        } catch (mysqli_sql_exception $e) {
            error_log("NotificationModel::getByStatus error: " . $e->getMessage());
            return [];
        }
    }

    public function getActiveAlerts()
    {
        try {
            $sql = "SELECT * FROM {$this->table}
                    WHERE JSON_UNESCAPED(JSON_EXTRACT(template_params, '$.status')) != 'ok'
                      AND deleted_at IS NULL
                    ORDER BY created_at DESC";
            $result = $this->db->query($sql);
            $items = [];
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
            return $this->translateBiomarkerData($items);
        } catch (mysqli_sql_exception $e) {
            error_log("NotificationModel::getActiveAlerts error: " . $e->getMessage());
            return [];
        }
    }

    public function getByBiomarkerId($id_biomarker)
    {
        try {
            $sql = "SELECT * FROM {$this->table}
                    WHERE JSON_UNESCAPED(JSON_EXTRACT(template_params, '$.id_biomarker')) = ?
                      AND deleted_at IS NULL
                    ORDER BY created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("s", $id_biomarker);
            $stmt->execute();
            $result = $stmt->get_result();
            $items = [];
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
            return $this->translateBiomarkerData($items);
        } catch (mysqli_sql_exception $e) {
            error_log("NotificationModel::getByBiomarkerId error: " . $e->getMessage());
            return [];
        }
    }

    public function getByBiomarkerAndUser($id_biomarker, $user_id)
    {
        try {
            $sql = "SELECT * FROM {$this->table}
                    WHERE user_id = ?
                      AND JSON_UNESCAPED(JSON_EXTRACT(template_params, '$.id_biomarker')) = ?
                      AND deleted_at IS NULL
                    ORDER BY created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("ss", $user_id, $id_biomarker);
            $stmt->execute();
            $result = $stmt->get_result();
            $items = [];
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
            return $this->translateBiomarkerData($items);
        } catch (mysqli_sql_exception $e) {
            error_log("NotificationModel::getByBiomarkerAndUser error: " . $e->getMessage());
            return [];
        }
    }

    public function updateNoAlertUser(string $notification_id): bool
    {
        try {
            return $this->actualizarFlag($notification_id, 'read_unread', 1);
        } catch (mysqli_sql_exception $e) {
            error_log("NotificationModel::updateNoAlertUser error: " . $e->getMessage());
            return false;
        }
    }

    public function updateNew(string $user_id): bool
    {
        try {
            [$now]   = $this->nowWithAudit();
            $actorId = $_SESSION['user_id'] ?? $user_id;

            $sql = "UPDATE {$this->table}
                    SET `new` = 0, updated_at = ?, updated_by = ?
                    WHERE user_id = ? AND deleted_at IS NULL";
            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                throw new mysqli_sql_exception("Error al preparar updateNew: " . $this->db->error);
            }
            $stmt->bind_param('sss', $now, $actorId, $user_id);
            $ok = $stmt->execute();
            $stmt->close();
            return $ok;
        } catch (mysqli_sql_exception $e) {
            error_log("NotificationModel::updateNew error: " . $e->getMessage());
            return false;
        }
    }

    public function updateNoAlertAdmin(string $notification_id): bool
    {
        try {
            $this->mustExist($notification_id);

            [$now]   = $this->nowWithAudit();
            $actorId = $_SESSION['administrator_id'] ?? $_SESSION['user_id'] ?? $notification_id;

            $sql = "UPDATE {$this->table}
                    SET template_params = JSON_SET(
                            COALESCE(template_params, '{}'),
                            '$.admin_read',
                            true
                        ),
                        updated_at = ?,
                        updated_by = ?
                    WHERE notifications_id = ? AND deleted_at IS NULL";

            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                throw new mysqli_sql_exception("Error al preparar updateNoAlertAdmin: " . $this->db->error);
            }
            $stmt->bind_param('sss', $now, $actorId, $notification_id);
            $ok = $stmt->execute();
            $stmt->close();
            return $ok;
        } catch (mysqli_sql_exception $e) {
            error_log("NotificationModel::updateNoAlertAdmin error: " . $e->getMessage());
            return false;
        }
    }

    public function updateAllNoAlertUserByUserId(string $user_id): bool
    {
        try {
            [$now]   = $this->nowWithAudit();
            $actorId = $_SESSION['user_id'] ?? $user_id;

            $sql = "UPDATE {$this->table}
                    SET `read_unread` = 1, updated_at = ?, updated_by = ?
                    WHERE user_id = ? AND `read_unread` = 0 AND deleted_at IS NULL";
            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                throw new mysqli_sql_exception("Error al preparar updateAllNoAlertUserByUserId: " . $this->db->error);
            }
            $stmt->bind_param('sss', $now, $actorId, $user_id);
            $ok = $stmt->execute();
            $stmt->close();
            return $ok;
        } catch (mysqli_sql_exception $e) {
            error_log("NotificationModel::updateAllNoAlertUserByUserId error: " . $e->getMessage());
            return false;
        }
    }

    public function updateAllNoAlertAdmin(): bool
    {
        try {
            [$now]   = $this->nowWithAudit();
            $actorId = $_SESSION['administrator_id'] ?? 'system_bulk_update';

            $sql = "UPDATE {$this->table}
                    SET template_params = JSON_SET(
                            COALESCE(template_params, '{}'),
                            '$.admin_read',
                            true
                        ),
                        updated_at = ?,
                        updated_by = ?
                    WHERE (JSON_EXTRACT(template_params, '$.admin_read') IS NULL 
                           OR JSON_EXTRACT(template_params, '$.admin_read') = false)
                      AND deleted_at IS NULL";

            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                throw new mysqli_sql_exception("Error al preparar updateAllNoAlertAdmin: " . $this->db->error);
            }
            $stmt->bind_param('ss', $now, $actorId);
            $ok = $stmt->execute();
            $stmt->close();
            return $ok;
        } catch (mysqli_sql_exception $e) {
            error_log("NotificationModel::updateAllNoAlertAdmin error: " . $e->getMessage());
            return false;
        }
    }

    public function create($data)
    {
        $this->db->begin_transaction();
        try {
            $template_key = $data['template_key'] ?? null;
            $user_id      = $data['user_id'] ?? null;
            $module       = $data['module'] ?? 'general';

            if (!$template_key || !$user_id) {
                throw new InvalidArgumentException("Missing required fields: template_key or user_id.");
            }

            $uuid    = UuidHelper::generateUUIDv4();

            // Aunque ya no lo usamos para created_at, lo dejamos para consistencia/auditoría
            [$now]   = $this->nowWithAudit();
            if (empty($now)) { $now = date('Y-m-d H:i:s'); } // fallback defensivo
            $actorId = $_SESSION['user_id'] ?? $_SESSION['specialist_id'] ?? $_SESSION['administrator_id'] ?? $user_id;

            $route   = $data['route'] ?? null;
            $rol     = $data['rol']   ?? 'user';

            $paramsJ = json_encode($data['template_params'] ?? [], JSON_UNESCAPED_UNICODE);

            $new_flag         = (int)($data['new'] ?? 1);
            $read_unread_flag = (int)($data['read_unread'] ?? $data['no_alert_user'] ?? 0);

            $sql = "INSERT INTO {$this->table}
                    (notifications_id, template_key, template_params, route, module, rol, user_id,
                     `new`, `read_unread`, created_at, created_by, updated_at, updated_by,
                     deleted_at, deleted_by)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, NULL, NULL, NULL, NULL)";

            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                throw new mysqli_sql_exception("Error al preparar inserción: " . $this->db->error);
            }

            // Tipos: 7 strings, 2 ints, 1 string => 'sssssssiis'
            $stmt->bind_param(
                'sssssssiis',
                $uuid,
                $template_key,
                $paramsJ,
                $route,
                $module,
                $rol,
                $user_id,
                $new_flag,
                $read_unread_flag,
                $actorId
            );

            if (!$stmt->execute()) {
                $err = $stmt->error;
                $stmt->close();
                $this->db->rollback();
                throw new mysqli_sql_exception("Error al ejecutar inserción: " . $err);
            }

            $stmt->close();
            $this->db->commit();
            return ['status' => 'success', 'message' => 'Notification created.', 'id' => $uuid];

        } catch (\Throwable $e) {
            $this->db->rollback();
            error_log("[NotificationModel] create() error: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function update($data)
    {
        try {
            $id = $data['id'] ?? null;
            if (!$id) {
                throw new InvalidArgumentException('ID de notificación no proporcionado para actualizar.');
            }

            $this->mustExist($id);

            $campos = [];
            $params = [];
            $types  = '';

            if (isset($data['template_params']) && is_array($data['template_params'])) {
                $paramsJ = json_encode($data['template_params'], JSON_UNESCAPED_UNICODE);
                $campos[] = 'template_params = ?';
                $params[] = $paramsJ;
                $types   .= 's';
            }

            if (isset($data['template_key'])) {
                $campos[] = 'template_key = ?';
                $params[] = (string)$data['template_key'];
                $types   .= 's';
            }
            if (isset($data['route'])) {
                $campos[] = 'route = ?';
                $params[] = (string)$data['route'];
                $types   .= 's';
            }
            if (isset($data['module'])) {
                $campos[] = 'module = ?';
                $params[] = (string)$data['module'];
                $types   .= 's';
            }

            if (isset($data['new'])) {
                $this->validarFlag('new', (int)$data['new']);
                $campos[] = '`new` = ?';
                $params[] = (int)$data['new'];
                $types   .= 'i';
            }
            
            $read_unread_val = $data['read_unread'] ?? $data['no_alert_user'] ?? null;
            if ($read_unread_val !== null) {
                $this->validarFlag('read_unread', (int)$read_unread_val);
                $campos[] = '`read_unread` = ?';
                $params[] = (int)$read_unread_val;
                $types   .= 'i';
            }
            
            if (empty($campos)) {
                return ['status' => 'success', 'message' => 'No hay campos para actualizar.'];
            }

            [$now]   = $this->nowWithAudit();
            $actorId = $_SESSION['user_id'] ?? $_SESSION['specialist_id'] ?? $_SESSION['administrator_id'] ?? $id;

            $campos[] = 'updated_at = ?';
            $params[] = $now;     $types .= 's';
            $campos[] = 'updated_by = ?';
            $params[] = $actorId; $types .= 's';

            $sql = "UPDATE {$this->table}
                    SET " . implode(', ', $campos) . "
                    WHERE notifications_id = ? AND deleted_at IS NULL";
            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                throw new mysqli_sql_exception("Error al preparar actualización: " . $this->db->error);
            }

            $types   .= 's';
            $params[] = $id;

            $stmt->bind_param($types, ...$params);
            $ok = $stmt->execute();
            if (!$ok) {
                throw new mysqli_sql_exception("Error al actualizar: " . $stmt->error);
            }
            
            $stmt->close();
            return ['status' => 'success', 'message' => 'Notification updated.'];

        } catch (\Throwable $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function delete(string $id)
    {
        try {
            $this->mustExist($id);

            [$now]   = $this->nowWithAudit();
            $actorId = $_SESSION['user_id'] ?? $_SESSION['specialist_id'] ?? $_SESSION['administrator_id'] ?? $id;

            $sql = "UPDATE {$this->table}
                    SET deleted_at = ?, deleted_by = ?
                    WHERE notifications_id = ? AND deleted_at IS NULL";
            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                throw new mysqli_sql_exception("Error al preparar eliminación: " . $this->db->error);
            }
            $stmt->bind_param('sss', $now, $actorId, $id);
            $ok = $stmt->execute();
            $stmt->close();
            
            if ($ok) {
                return ['status' => 'success', 'message' => 'Notification deleted.'];
            } else {
                throw new mysqli_sql_exception("Error al ejecutar eliminación.");
            }
        } catch (\Throwable $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
