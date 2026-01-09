<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/SpecialistModel.php';
require_once __DIR__ . '/NotificationModel.php'; // <-- AÑADIR
require_once __DIR__ . '/UserModel.php';         // <-- AÑADIR (Asumo que existe, para el nombre del paciente)

class SecondOpinionRequestsModel
{
    private $db;
    private string $table = 'second_opinion_requests';

    private SpecialistModel $specialistModel;
    private NotificationModel $notificationModel; // <-- AÑADIR
    private UserModel $userModel;               // <-- AÑADIR

    public function __construct()
    {
        $this->db = \Database::getInstance();
        $this->specialistModel = new SpecialistModel();
        $this->notificationModel = new NotificationModel(); // <-- AÑADIR
        $this->userModel = new UserModel();               // <-- AÑADIR
    }
    /* ===================== Helpers ===================== */

    /**
     * Devuelve ['panel_name','display_name'] según el idioma de sesión.
     * Si el panel no existe o está borrado lógicamente, retorna null.
     */
    private function getPanelNameById(?string $panelId): ?array
    {
        if (empty($panelId))
            return null;

        $idioma = strtoupper($_SESSION['idioma'] ?? 'EN');
        $displayCol = ($idioma === 'ES') ? 'display_name_es' : 'display_name';

        // Intento 1: con table_name
        $sql1 = "SELECT panel_name, {$displayCol} AS display_name, panel_name as table_name
             FROM test_panels
             WHERE panel_id = ? AND deleted_at IS NULL";

        $stmt = $this->db->prepare($sql1);
        if ($stmt) {
            $stmt->bind_param("s", $panelId);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            if ($row) {
                // Si table_name viene vacío o null, usamos panel_name
                $tableName = $row['table_name'] ?? null;
                if (!is_string($tableName) || $tableName === '') {
                    $tableName = $row['panel_name'];
                }
                return [
                    'panel_name' => $row['panel_name'],
                    'display_name' => $row['display_name'],
                    'table_name' => $tableName
                ];
            }
        } else {
            // Si falló porque 'table_name' no existe, reintento sin table_name
            error_log("[SecondOpinionRequestsModel::getPanelNameById] Falling back without table_name: " . $this->db->error);
        }

        // Intento 2: sin table_name (compatibilidad)
        $sql2 = "SELECT panel_name, {$displayCol} AS display_name
             FROM test_panels
             WHERE panel_id = ? AND deleted_at IS NULL";

        $stmt2 = $this->db->prepare($sql2);
        if (!$stmt2) {
            throw new \mysqli_sql_exception("Prepare failed: " . $this->db->error);
        }
        $stmt2->bind_param("s", $panelId);
        $stmt2->execute();
        $row2 = $stmt2->get_result()->fetch_assoc();
        $stmt2->close();

        if (!$row2)
            return null;

        return [
            'panel_name' => $row2['panel_name'],
            'display_name' => $row2['display_name'],
            'table_name' => $row2['panel_name'] // fallback
        ];
    }

    /**
     * Helper: lista todos los paneles activos con su display_name según idioma.
     * Devuelve un array de ['panel_id','panel_name','display_name'].
     */
    private function listAllPanelsForOutput(): array
    {
        $idioma = strtoupper($_SESSION['idioma'] ?? 'EN');
        $displayCol = ($idioma === 'ES') ? 'display_name_es' : 'display_name';

        $sql = "SELECT panel_id, panel_name, {$displayCol} AS display_name
            FROM test_panels
            WHERE deleted_at IS NULL";

        $stmt = $this->db->prepare($sql);
        if (!$stmt)
            throw new \mysqli_sql_exception("Prepare failed: " . $this->db->error);

        $stmt->execute();
        $res = $stmt->get_result();

        $out = [];
        while ($row = $res->fetch_assoc()) {
            $out[] = [
                'panel_id' => $row['panel_id'],
                'panel_name' => $row['panel_name'],
                'display_name' => $row['display_name'],
            ];
        }
        $stmt->close();

        return $out;
    }
    /**
     * Helper: obtiene los biomarcadores (IDs) de un panel.
     * Ajusta el nombre de la tabla pivote si difiere en tu esquema.
     * Se asume tabla: test_panel_biomarkers(panel_id, biomarker_id, deleted_at)
     */
    private function getBiomarkerIdsByPanelId(string $panelId, ?string $sex = 'u'): array
    {
        // Normaliza sexo: 'm' | 'f' | 'u' (M => u)
        $normalizeSex = function (?string $s): string {
            if (!is_string($s) || $s === '')
                return 'u';
            if ($s === 'M')
                return 'u';
            $t = strtolower(trim($s));
            return ($t === 'm' || $t === 'f') ? $t : 'u';
        };
        $sex = $normalizeSex($sex);
        $mode = ($sex === 'm') ? 'male_only' : (($sex === 'f') ? 'female_only' : 'both');

        $sql = "SELECT biomarker_id, name, name_es, name_db
            FROM biomarkers
            WHERE panel_id = ? AND deleted_at IS NULL";

        $stmt = $this->db->prepare($sql);
        if (!$stmt)
            throw new \mysqli_sql_exception("Prepare failed: " . $this->db->error);
        $stmt->bind_param("s", $panelId);
        $stmt->execute();
        $res = $stmt->get_result();

        // === Regex robustos con límites de palabra ===
        $rgxFemale = '/(?:\bfemale\b|\bfemenin\w*\b|\bhembra\b|\bmujer(?:es)?\b|\bwomen?\b|\bgirls?\b|♀|\(f\)|\[f\])/iu';
        $rgxMale = '/(?:\bmale\b|\bmasculin\w*\b|\bmacho\b|\bhombre(?:s)?\b|\bmen\b|\bboys?\b|♂|\(m\)|\[m\])/iu';

        $ids = [];
        while ($row = $res->fetch_assoc()) {
            $nameEn = (string) ($row['name'] ?? '');
            $nameEs = (string) ($row['name_es'] ?? '');
            $nameDb = (string) ($row['name_db'] ?? '');

            // === Detección de género por texto ===
            $haystack = mb_strtolower($nameEn . ' ' . $nameEs . ' ' . $nameDb, 'UTF-8');
            $hasFemale = (bool) preg_match($rgxFemale, $haystack);
            $hasMale = (bool) preg_match($rgxMale, $haystack);
            $isNeutral = (!$hasFemale && !$hasMale);

            // === Regla de inclusión ===
            $include = true;
            if ($mode === 'male_only')
                $include = ($hasMale || $isNeutral);   // m → male + neutrales
            elseif ($mode === 'female_only')
                $include = ($hasFemale || $isNeutral); // f → female + neutrales

            if ($include && isset($row['biomarker_id'])) {
                $ids[] = $row['biomarker_id'];
            }
        }
        $stmt->close();

        return array_values(array_unique(array_filter($ids, fn($v) => is_string($v) && $v !== '')));
    }





    // ¿Existe una tabla con ese nombre exacto?
    private function tableExists(string $tableName): bool
    {
        $sql = "SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("[SecondOpinionRequestsModel::tableExists] Prepare failed: " . $this->db->error);
            return false;
        }
        $stmt->bind_param("s", $tableName);
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();
        return $exists;
    }

    // Lee metadata de una columna en information_schema
    private function getColumnInfo(string $table, string $column): ?array
    {
        $sql = "SELECT DATA_TYPE, COLUMN_TYPE, CHARACTER_MAXIMUM_LENGTH
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?
            LIMIT 1";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("[getColumnInfo] Prepare failed: " . $this->db->error);
            return null;
        }
        $stmt->bind_param("ss", $table, $column);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $res ?: null;
    }

    // ¿Es BINARY(16) o VARBINARY(16)?
    private function isBinary16(string $table, string $column): bool
    {
        $info = $this->getColumnInfo($table, $column);
        if (!$info)
            return false;
        $type = strtolower((string) ($info['DATA_TYPE'] ?? ''));
        $coltype = strtolower((string) ($info['COLUMN_TYPE'] ?? ''));
        return ($type === 'binary' || $type === 'varbinary') && (strpos($coltype, '(16)') !== false);
    }

    // Detecta un PK razonable para la tabla del panel (como ya venías usando)
    private function detectPanelPk(string $tableName, array $existingCols): ?string
    {
        $lower = array_map('strtolower', $existingCols);

        $candidate = strtolower($tableName) . '_id';
        $idx = array_search($candidate, $lower, true);
        if ($idx !== false)
            return $existingCols[$idx];

        $idx = array_search('id', $lower, true);
        if ($idx !== false)
            return $existingCols[$idx];

        foreach ($existingCols as $c) {
            if (preg_match('/_id$/i', $c))
                return $c;
        }
        return null;
    }


    /** ===================== Pricing Helper ===================== */
    private function getPricingById(?string $pricingId): ?array
    {
        if (empty($pricingId))
            return null;

        $sql = "SELECT * FROM specialist_pricing WHERE pricing_id = ? AND deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("[SecondOpinionRequestsModel::getPricingById] Prepare failed: " . $this->db->error . " | SQL: $sql");
            throw new \mysqli_sql_exception("Prepare failed: " . $this->db->error);
        }
        $stmt->bind_param("s", $pricingId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $row ?: null;
    }

    private static function decodeJsonList(?string $json): array
    {
        if (!$json)
            return [];
        $arr = json_decode($json, true);
        return is_array($arr) ? $arr : [];
    }

    /** Cache de columnas por tabla (solo durante el request) */
    private static array $__colsCache = [];

    private function getExistingColumns(string $table): array
    {
        if (isset(self::$__colsCache[$table])) {
            return self::$__colsCache[$table];
        }

        $cols = [];
        $sql = "SHOW COLUMNS FROM `{$table}`";
        $res = $this->db->query($sql);
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                if (!empty($row['Field']))
                    $cols[] = $row['Field'];
            }
        } else {
            $stmt = $this->db->prepare("
                SELECT COLUMN_NAME 
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?
            ");
            if ($stmt) {
                $stmt->bind_param("s", $table);
                $stmt->execute();
                $r = $stmt->get_result();
                while ($row = $r->fetch_assoc()) {
                    $cols[] = $row['COLUMN_NAME'];
                }
                $stmt->close();
            }
        }

        self::$__colsCache[$table] = $cols;
        return $cols;
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

    /**
     * Obtiene registros del panel siguiendo la convención:
     *   table = panel_name, pk = panel_name . '_id'
     */
    /**
     * Obtiene registros de un panel.
     * - Si $shareAll === true y la tabla tiene user_id, trae TODOS los registros del usuario (ignora $recordsJson).
     * - Si vienen IDs en $recordsJson, filtra por esos IDs (y opcionalmente por user_id).
     * - Si no hay IDs, y hay user_id disponible, trae todos por usuario.
     */
    private function fetchPanelRecords(
        ?string $panelId,
        ?string $recordsJson,
        ?string $requestUserId,
        array $biomarkerCols = [],
        bool $shareAll = false
    ): array {
        $meta = $this->getPanelNameById($panelId);
        if (!$meta) {
            error_log("[fetchPanelRecords] panelId=$panelId no resuelto en test_panels");
            return [];
        }

        $panelName = $meta['panel_name'] ?? null;
        $table = $meta['table_name'] ?? $panelName;
        if (!$panelName || !$table) {
            error_log("[fetchPanelRecords] panelId=$panelId sin panelName/table");
            return [];
        }

        if (!$this->tableExists($table)) {
            error_log("[fetchPanelRecords] La tabla física '$table' no existe (panel=$panelName, id=$panelId)");
            return [];
        }

        // Columnas de la tabla
        $existingCols = $this->getExistingColumns($table);
        if (empty($existingCols)) {
            error_log("[fetchPanelRecords] Sin columnas detectadas en '$table'");
            return [];
        }
        $lowerCols = array_map('strtolower', $existingCols);

        $hasUserId = (false !== array_search('user_id', $lowerCols, true));
        $hasDeletedAt = (false !== array_search('deleted_at', $lowerCols, true));
        $hasCreatedAt = (false !== array_search('created_at', $lowerCols, true));

        // Detectar nombres reales (casing)
        $userIdColReal = $hasUserId ? $existingCols[array_search('user_id', $lowerCols, true)] : null;

        // Detectar PK real
        $pk = $this->detectPanelPk($table, $existingCols); // puede ser null

        // ¿user_id es BINARY(16)?
        $userIdIsBin16 = ($hasUserId && $this->isBinary16($table, $userIdColReal));
        // ¿PK es BINARY(16)?
        $pkIsBin16 = ($pk && $this->isBinary16($table, $pk));

        // SELECT (si se recortan columnas por biomarcadores)
        $selectCols = '*';
        $mustCols = [];
        if ($pk)
            $mustCols[] = $pk;
        if ($hasUserId)
            $mustCols[] = $userIdColReal;

        // Mapear biomarkerCols a nombres reales (case-insensitive)
        $bioCols = [];
        if (!empty($biomarkerCols)) {
            $mapLowerToReal = array_combine($lowerCols, $existingCols);
            foreach ($biomarkerCols as $bc) {
                $lc = strtolower($bc);
                if (isset($mapLowerToReal[$lc]))
                    $bioCols[] = $mapLowerToReal[$lc];
            }
        }

        if (!empty($bioCols)) {
            $all = array_values(array_unique(array_merge($mustCols, $bioCols)));
            $selectCols = implode(', ', array_map(fn($c) => "`" . str_replace("`", "``", $c) . "`", $all));
        }

        // ORDER BY
        $orderCol = $hasCreatedAt ? $existingCols[array_search('created_at', $lowerCols, true)]
            : ($pk ?: $existingCols[0]);
        $orderSql = "`" . str_replace("`", "``", $orderCol) . "` DESC";

        // WHERE dinámico + bind params
        $where = [];
        $types = '';
        $params = [];

        // share_all → filtrar por user_id
        if ($shareAll && $hasUserId && !empty($requestUserId)) {
            if ($userIdIsBin16) {
                // user_id BINARY(16): comparar contra UNHEX(uuid)
                $where[] = "`{$userIdColReal}` = UNHEX(REPLACE(?, '-', ''))";
                $types .= 's';
                $params[] = trim((string) $requestUserId);
            } else {
                // user_id de texto (CHAR/VARCHAR)
                $where[] = "`{$userIdColReal}` = ?";
                $types .= 's';
                $params[] = trim((string) $requestUserId);
            }
        }

        // IDs explícitos (si no estamos en share_all con user_id)
        $ids = self::decodeJsonList($recordsJson);
        $ids = array_values(array_unique(array_filter($ids, fn($v) => is_string($v) && $v !== '')));
        $useIds = (!empty($ids) && !($shareAll && $hasUserId && !empty($requestUserId)));

        if ($useIds && $pk) {
            if ($pkIsBin16) {
                // PK BINARY(16): IN(UNHEX(REPLACE(?, '-', '')), ...)
                $place = implode(',', array_fill(0, count($ids), "UNHEX(REPLACE(?, '-', ''))"));
                $where[] = "`" . str_replace("`", "``", $pk) . "` IN ($place)";
                $types .= str_repeat('s', count($ids));
                foreach ($ids as $idv)
                    $params[] = $idv;
            } else {
                // PK texto
                $place = implode(',', array_fill(0, count($ids), "?"));
                $where[] = "`" . str_replace("`", "``", $pk) . "` IN ($place)";
                $types .= str_repeat('s', count($ids));
                foreach ($ids as $idv)
                    $params[] = $idv;
            }

            // Si además se conoce user_id, añadimos filtro por usuario (robustece)
            if ($hasUserId && !empty($requestUserId)) {
                if ($userIdIsBin16) {
                    $where[] = "`{$userIdColReal}` = UNHEX(REPLACE(?, '-', ''))";
                } else {
                    $where[] = "`{$userIdColReal}` = ?";
                }
                $types .= 's';
                $params[] = trim((string) $requestUserId);
            }
        }

        // Soft delete si existe
        if ($hasDeletedAt) {
            $where[] = "(`deleted_at` IS NULL)";
        }

        if (empty($where)) {
            error_log("[fetchPanelRecords] Sin WHERE para '$table' (shareAll=$shareAll, hasUserId=$hasUserId, requestUserId=" . ($requestUserId ?? 'NULL') . ")");
            return [];
        }

        $whereSql = 'WHERE ' . implode(' AND ', $where);
        $sql = "SELECT {$selectCols} FROM `{$table}` {$whereSql} ORDER BY {$orderSql}";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("[fetchPanelRecords] Prepare failed ($table): " . $this->db->error);
            return [];
        }

        if ($types !== '') {
            $bind = array_merge([$types], $params);
            $refs = [];
            foreach ($bind as $k => $v)
                $refs[$k] = &$bind[$k];
            call_user_func_array([$stmt, 'bind_param'], $refs);
        }

        $stmt->execute();
        $res = $stmt->get_result();

        $rows = [];
        while ($r = $res->fetch_assoc())
            $rows[] = $r;
        $stmt->close();

        if (empty($rows)) {
            error_log("[fetchPanelRecords] 0 filas en '$table' (userIdBin16={$userIdIsBin16}, pkBin16={$pkIsBin16}) where=" . json_encode($where) . " params=" . json_encode($params));
        }

        return $rows;
    }
    /**
     * Intenta resolver el user_id objetivo (paciente) para la solicitud.
     * Prioriza columnas típicas de “dueño del dato” y evita usar el especialista.
     */
    private function resolveTargetUserId(string $secondOpinionId, ?string $fallback = null): ?string
    {
        // Columnas candidatas (paciente/propietario del dato)
        $targetCandidates = [
            'user_id'
        ];
        // Columnas típicas del solicitante (especialista / doctor)
        $requesterColumns = [
            'specialist_id'
        ];

        // Qué columnas existen realmente en la tabla de solicitudes
        $existing = $this->getExistingColumns($this->table);
        $lower = array_map('strtolower', $existing);
        $mapLowerToReal = array_combine($lower, $existing);

        $selectCols = [];
        foreach (array_merge($targetCandidates, $requesterColumns) as $c) {
            $lc = strtolower($c);
            if (isset($mapLowerToReal[$lc])) {
                $selectCols[] = "`" . str_replace("`", "``", $mapLowerToReal[$lc]) . "`";
            }
        }

        if (empty($selectCols)) {
            // No hay ninguna columna conocida: devolver fallback
            return $fallback;
        }

        $sql = "SELECT " . implode(", ", $selectCols) . " 
            FROM `{$this->table}`
            WHERE second_opinion_id = ? AND deleted_at IS NULL
            LIMIT 1";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("[resolveTargetUserId] Prepare failed: " . $this->db->error);
            return $fallback;
        }
        $stmt->bind_param("s", $secondOpinionId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$row) {
            return $fallback;
        }

        // 1) Intentar con candidatos de “paciente/owner”
        foreach ($targetCandidates as $c) {
            foreach ($row as $k => $v) {
                if (strcasecmp($k, $c) === 0) {
                    $val = is_string($v) ? trim($v) : null;
                    if (!empty($val)) {
                        return $val; // ESTE es el user_id correcto a usar en los panels
                    }
                }
            }
        }

        // 2) Evitar caer en columnas del solicitante; si no hay nada, volver al fallback
        return $fallback;
    }


    // ====== CAMBIOS DE ESTADO SECUENCIALES ======

    /**
     * Helper privado para enviar notificaciones de cambio de estado al usuario.
     * NO falla si la notificación no se puede enviar (solo loguea el error).
     *
     * @param string $secondOpinionId El ID de la solicitud.
     * @param string $newStatus       El nuevo estado (ej. 'completed', 'cancelled').
     */
    /**
     * Envía una notificación de cambio de estado al rol correspondiente.
     * Actúa como un despachador basado en el nuevo estado.
     */
    private function sendStatusChangeNotification(string $secondOpinionId, string $newStatus): void
    {
        try {
            // 1. Obtener los IDs y datos de la solicitud
            $sql = "SELECT user_id, specialist_id, type_request 
                    FROM {$this->table} 
                    WHERE second_opinion_id = ? LIMIT 1";
            $stmt = $this->db->prepare($sql);
            if (!$stmt) return; 
            
            $stmt->bind_param("s", $secondOpinionId);
            $stmt->execute();
            $req = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if (!$req || empty($req['user_id']) || empty($req['specialist_id'])) {
                error_log("[sendStatusChangeNotification] No se encontraron detalles (user_id, specialist_id) para: $secondOpinionId");
                return;
            }

            $userId = $req['user_id'];
            $specialistId = $req['specialist_id'];
            $requestType = $req['type_request'] ?? 'segunda opinión'; // Fallback

            // 2. Definir variables de notificación
            $templateKey = '';
            $recipientId = '';
            $recipientRole = '';
            $route = '';
            $params = [];

            // 3. Lógica de despacho (Switch)
            // Decide qué plantilla usar y para quién, basado en el nuevo estado.
            switch ($newStatus) {
                case 'awaiting_payment':
                case 'upcoming':
                case 'completed':
                case 'rejected':
                    // Estos estados notifican AL PACIENTE (user)
                    $specialist = $this->specialistModel->getById($specialistId);
                    $specName = $specialist ? ($specialist['first_name'] . ' ' . $specialist['last_name']) : 'el especialista';

                    $templateKey = 'second_opinion_status_changed';
                    $recipientId = $userId;
                    $recipientRole = 'user';
                    $route = 'user_request_panel?id=' . $secondOpinionId;
                    $params = [
                        'specialist_name' => trim($specName),
                        'new_status'      => $newStatus
                    ];
                    break;

                case 'cancelled':
                    // Este estado notifica AL ESPECIALISTA (specialist)
                    // Asumimos que $this->userModel existe, similar a $this->specialistModel
                    $user = $this->userModel->getById($userId); 
                    $userName = $user ? ($user['first_name'] . ' ' . $user['last_name']) : 'un paciente';

                    $templateKey = 'second_opinion_cancelled_by_user'; // <-- ¡Usando el nuevo template!
                    $recipientId = $specialistId;
                    $recipientRole = 'specialist';
                    $route = 'service_requests?id=' . $secondOpinionId; // Ruta del especialista
                    $params = [
                        'user_name'    => trim($userName),
                        'request_type' => $requestType
                    ];
                    break;
                
                default:
                    // No hacer nada si el estado no tiene una notificación asociada
                    return;
            }

            // 4. Enviar la notificación (si se definió una)
            if (!empty($templateKey) && !empty($recipientId)) {
                $this->notificationModel->create([
                    'user_id'         => $recipientId, // El destinatario dinámico
                    'template_key'    => $templateKey,
                    'rol'             => $recipientRole,
                    'module'          => 'second_opinion',
                    'route'           => $route,
                    'template_params' => $params
                ]);
            }

        } catch (\Throwable $e) {
            // No interrumpir el flujo principal si la notificación falla
            error_log("[SecondOpinionRequestsModel::sendStatusChangeNotification] Falló el envío: " . $e->getMessage());
        }
    }

    // ====== CAMBIOS DE ESTADO SECUENCIALES ======

    public function setAwaitingPayment(string $id): bool
    {
        $sql = "UPDATE {$this->table}
            SET status = 'awaiting_payment'
            WHERE second_opinion_id  = ? AND status = 'pending'";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $ok = $stmt->affected_rows > 0; // <-- AÑADIDO
        
        if ($ok) { // <-- AÑADIDO
            $this->sendStatusChangeNotification($id, 'awaiting_payment');
        }
        
        return $ok; // <-- MODIFICADO
    }

    public function setUpcoming(string $id): bool
    {
        $sql = "UPDATE {$this->table}
            SET status = 'upcoming'
            WHERE second_opinion_id  = ? AND status = 'awaiting_payment'";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $ok = $stmt->affected_rows > 0; // <-- AÑADIDO
        
        if ($ok) { // <-- AÑADIDO
            $this->sendStatusChangeNotification($id, 'upcoming');
        }
        
        return $ok; // <-- MODIFICADO
    }

    public function setCompleted(string $id): bool
    {
        $sql = "UPDATE {$this->table}
            SET status = 'completed'
            WHERE second_opinion_id  = ? AND status = 'upcoming'";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $ok = $stmt->affected_rows > 0; // <-- AÑADIDO
        
        if ($ok) { // <-- AÑADIDO
            $this->sendStatusChangeNotification($id, 'completed');
        }
        
        return $ok; // <-- MODIFICADO
    }

    // ====== CAMBIOS UNIVERSALES (SIEMPRE PERMITIDOS) ======

    public function setCancelled(string $id): bool
    {
        $sql = "UPDATE {$this->table}
            SET status = 'cancelled'
            WHERE second_opinion_id  = ? AND status NOT IN ('completed', 'cancelled', 'rejected')";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $ok = $stmt->affected_rows > 0; // <-- AÑADIDO
        
        if ($ok) { // <-- AÑADIDO
            $this->sendStatusChangeNotification($id, 'cancelled');
        }
        
        return $ok; // <-- MODIFICADO
    }

    public function setRejected(string $id, string $rejectMsg): bool
    {
        // set rejected_message si es necesario (no implementado aquí)
        $sql = "UPDATE {$this->table}
            SET status = 'rejected', reject_message = ?
            WHERE second_opinion_id  = ? AND status NOT IN ('completed', 'cancelled', 'rejected')";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ss", $rejectMsg, $id);
        $stmt->execute();
        $ok = $stmt->affected_rows > 0; // <-- AÑADIDO
        
        if ($ok) { // <-- AÑADIDO
            $this->sendStatusChangeNotification($id, 'rejected');
        }
        
        return $ok; // <-- MODIFICADO
    }

    /** 
     * SELECT dinámico de biomarkers y normaliza a un solo 'name' según idioma (ES/EN, con fallback).
     * Filtra opcionalmente por sexo:
     *   - $sex === 'm'  => excluye "Female"
     *   - $sex === 'f'  => excluye "Male"
     *   - $sex === 'M'  => incluye ambos (por defecto)
     */
    private function getBiomarkersInfoMap(array $biomarkerIds, ?string $sex = 'u'): array
    {
        // Normaliza sexo: 'm' | 'f' | 'u' (M => u)
        $normalizeSex = function (?string $s): string {
            if (!is_string($s) || $s === '')
                return 'u';
            if ($s === 'M')
                return 'u'; // ambos
            $t = strtolower(trim($s));
            return ($t === 'm' || $t === 'f') ? $t : 'u';
        };
        $sex = $normalizeSex($sex);

        $biomarkerIds = array_values(array_unique(array_filter($biomarkerIds, fn($v) => is_string($v) && $v !== '')));
        if (empty($biomarkerIds))
            return [];

        $existing = $this->getExistingColumns('biomarkers');

        $mode = ($sex === 'm') ? 'male_only' : (($sex === 'f') ? 'female_only' : 'both');

        // === SELECT dinámico ===
        $selectCols = [];
        foreach (['biomarker_id', 'name_db', 'unit', 'reference_min', 'reference_max'] as $col) {
            if (in_array($col, $existing, true))
                $selectCols[] = "`" . str_replace("`", "``", $col) . "`";
        }
        $includeNameEs = in_array('name_es', $existing, true);
        $includeNameEn = in_array('name', $existing, true);
        if ($includeNameEs)
            $selectCols[] = "`name_es`";
        if ($includeNameEn)
            $selectCols[] = "`name`";

        $colsSql = implode(', ', $selectCols);
        $place = implode(',', array_fill(0, count($biomarkerIds), '?'));
        $types = str_repeat('s', count($biomarkerIds));
        $sql = "SELECT {$colsSql} FROM biomarkers WHERE biomarker_id IN ($place) AND deleted_at IS NULL";

        $stmt = $this->db->prepare($sql);
        if (!$stmt)
            throw new \mysqli_sql_exception("Prepare failed: " . $this->db->error);
        $stmt->bind_param($types, ...$biomarkerIds);
        $stmt->execute();
        $res = $stmt->get_result();

        $idioma = strtoupper($_SESSION['idioma'] ?? 'EN');
        $map = [];

        // === Regex robustos con límites de palabra ===
        $rgxFemale = '/(?:\bfemale\b|\bfemenin\w*\b|\bhembra\b|\bmujer(?:es)?\b|\bwomen?\b|\bgirls?\b|♀|\(f\)|\[f\])/iu';
        $rgxMale = '/(?:\bmale\b|\bmasculin\w*\b|\bmacho\b|\bhombre(?:s)?\b|\bmen\b|\bboys?\b|♂|\(m\)|\[m\])/iu';

        while ($r = $res->fetch_assoc()) {
            if (!isset($r['biomarker_id']) && isset($r['BIOMARKER_ID'])) {
                $r['biomarker_id'] = $r['BIOMARKER_ID'];
                unset($r['BIOMARKER_ID']);
            }
            $id = $r['biomarker_id'] ?? null;
            if (!$id)
                continue;

            $rawEs = $includeNameEs ? trim((string) ($r['name_es'] ?? '')) : '';
            $rawEn = $includeNameEn ? trim((string) ($r['name'] ?? '')) : '';
            $rawDb = isset($r['name_db']) ? trim((string) $r['name_db']) : '';

            $name = ($idioma === 'ES')
                ? ($rawEs !== '' ? $rawEs : ($rawEn !== '' ? $rawEn : null))
                : ($rawEn !== '' ? $rawEn : ($rawEs !== '' ? $rawEs : null));

            // === Detección de género por texto ===
            $haystack = mb_strtolower($rawEn . ' ' . $rawEs . ' ' . $rawDb, 'UTF-8');
            $hasFemale = (bool) preg_match($rgxFemale, $haystack);
            $hasMale = (bool) preg_match($rgxMale, $haystack);
            $isNeutral = (!$hasFemale && !$hasMale);

            // === Regla de inclusión ===
            $include = true;
            if ($mode === 'male_only')
                $include = ($hasMale || $isNeutral);   // m → male + neutrales
            elseif ($mode === 'female_only')
                $include = ($hasFemale || $isNeutral); // f → female + neutrales

            if (!$include)
                continue;

            // === Construcción del objeto de salida ===
            $out = ['biomarker_id' => $id, 'name' => $name];
            foreach (['name_db', 'unit', 'reference_min', 'reference_max'] as $col) {
                if (array_key_exists($col, $r))
                    $out[$col] = $r[$col];
            }
            $map[$id] = $out;
        }
        $stmt->close();

        // Rellenar faltantes
        foreach ($biomarkerIds as $id) {
            if (!isset($map[$id]))
                $map[$id] = ['biomarker_id' => $id, 'name' => null];
        }

        return $map;
    }








    /* ========= DURACIÓN / BUFFER & VALIDACIÓN DE AGENDA ========= */

    private function parseMinutes($val): int
    {
        if ($val === null || $val === '')
            return 0;
        $s = strtolower(trim((string) $val));
        if (ctype_digit($s))
            return (int) $s;

        if (preg_match('/^(\d{1,2})\s*:\s*([0-5]?\d)$/', $s, $m)) {
            return ((int) $m[1]) * 60 + (int) $m[2];
        }
        if (preg_match('/^(\d+)\s*h$/', $s, $m))
            return ((int) $m[1]) * 60;
        if (preg_match('/^(\d+)\s*m$/', $s, $m))
            return (int) $m[1];

        if (preg_match('/^\d+/', $s, $m))
            return (int) $m[0];
        return 0;
    }

    private function getSpecialistBufferMinutes(string $specialistId): int
    {
        $sql = "SELECT MAX(CAST(buffer_time_minutes AS UNSIGNED)) AS buf 
                FROM specialist_availability
                WHERE specialist_id = ? AND deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        if (!$stmt)
            throw new \mysqli_sql_exception("Prepare failed: " . $this->db->error);
        $stmt->bind_param("s", $specialistId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $this->parseMinutes($row['buf'] ?? 0);
    }

    /** Devuelve [durationMin, bufferMin] */
    private function getEffectiveDurationAndBuffer(?string $pricingId, string $specialistId, $fallbackDuration): array
    {
        $durationMin = 0;
        if (!empty($pricingId)) {
            $p = $this->getPricingById($pricingId);
            if ($p && isset($p['duration_services'])) {
                $durationMin = $this->parseMinutes($p['duration_services']);
            }
        }
        if ($durationMin <= 0) {
            $durationMin = $this->parseMinutes($fallbackDuration);
        }
        $bufferMin = $this->getSpecialistBufferMinutes($specialistId);
        return [$durationMin, $bufferMin];
    }

    private function diffMinutes(string $start, string $end): int
    {
        $ts1 = strtotime($start);
        $ts2 = strtotime($end);
        if ($ts1 === false || $ts2 === false)
            return 0;
        return (int) max(0, round(($ts2 - $ts1) / 60));
    }

    /**
     * Valida que el nuevo intervalo [start, end] (o start+duration) no choque.
     * Si $requestedEnd es NULL, usa $durationMin. Si viene $requestedEnd, ignora $durationMin.
     */
   private function validateScheduleOrThrow(
    string $specialistId,
    string $requestedStart,
    int $durationMin,
    int $bufferMin,
    ?string $excludeId = null,
    ?string $requestedEnd = null
): void {
    // Idioma
    $lang = strtoupper($_SESSION['idioma'] ?? 'EN');
    $es = ($lang === 'ES');

    // Diccionario de mensajes
    $MSG = [
        'start_empty' => $es
            ? 'request_date_to inválido. No puede estar vacío.'
            : 'Invalid request_date_to. It cannot be empty.',
        'start_format' => $es
            ? "Formato de request_date_to inválido. Use 'YYYY-MM-DD HH:MM:SS'."
            : "Invalid request_date_to format. Use 'YYYY-MM-DD HH:MM:SS'.",
        'end_format' => $es
            ? "Formato de request_date_end inválido. Use 'YYYY-MM-DD HH:MM:SS'."
            : "Invalid request_date_end format. Use 'YYYY-MM-DD HH:MM:SS'.",
        'end_gt_start' => $es
            ? 'request_date_end debe ser mayor que request_date_to.'
            : 'request_date_end must be greater than request_date_to.',
        'prepare_failed' => $es
            ? 'Error al preparar la consulta: '
            : 'Prepare failed: ',
        'conflict_prev' => $es
            ? 'Conflicto de agenda: la hora de inicio debe ser >= %s (cita previa + duración + buffer).'
            : 'Schedule conflict: start must be >= %s (prev appt + duration + buffer).',
        'conflict_next' => $es
            ? 'Conflicto de agenda: la próxima cita debe iniciar en o después de %s (esta cita + duración + buffer).'
            : 'Schedule conflict: next appointment must start at or after %s (this appt + duration + buffer).',
    ];

    if (empty($requestedStart)) {
        throw new \mysqli_sql_exception($MSG['start_empty']);
    }
    $startTs = strtotime($requestedStart);
    if ($startTs === false) {
        throw new \mysqli_sql_exception($MSG['start_format']);
    }

    $newEndTs = null;
    if ($requestedEnd !== null) {
        $newEndTs = strtotime($requestedEnd);
        if ($newEndTs === false) {
            throw new \mysqli_sql_exception($MSG['end_format']);
        }
        if ($newEndTs <= $startTs) {
            throw new \mysqli_sql_exception($MSG['end_gt_start']);
        }
    } else {
        // buffer aplicado al final para comparación con la siguiente cita
        $newEndTs = $startTs + ($durationMin * 60) + ($bufferMin * 60);
    }

    $notIn = "'completed','cancelled','rejected'";

    // ---------- Cita previa ----------
    $sqlPrev = "
        SELECT second_opinion_id, request_date_to, request_date_end, pricing_id, duration_request, type_request
        FROM {$this->table}
        WHERE deleted_at IS NULL
          AND specialist_id = ?
          AND status NOT IN ($notIn)
          AND request_date_to <= ?
          " . ($excludeId ? "AND second_opinion_id <> ?" : "") . "
        ORDER BY request_date_to DESC
        LIMIT 1";
    $stmt = $excludeId
        ? $this->db->prepare($sqlPrev)
        : $this->db->prepare(str_replace("AND second_opinion_id <> ?", "", $sqlPrev));

    if (!$stmt) {
        throw new \mysqli_sql_exception($MSG['prepare_failed'] . $this->db->error);
    }

    if ($excludeId) {
        $stmt->bind_param("sss", $specialistId, $requestedStart, $excludeId);
    } else {
        $stmt->bind_param("ss", $specialistId, $requestedStart);
    }
    $stmt->execute();
    $prev = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($prev) {
        $prevStartTs = strtotime($prev['request_date_to']);
        $prevEndTs = null;

        if (!empty($prev['request_date_end'])) {
            $prevEndTs = strtotime($prev['request_date_end']);
        } else {
            // calcular por duración
            $prevDurMin = 0;
            if (!empty($prev['pricing_id'])) {
                $p = $this->getPricingById($prev['pricing_id']);
                if ($p && isset($p['duration_services'])) {
                    $prevDurMin = $this->parseMinutes($p['duration_services']);
                }
            }
            if ($prevDurMin <= 0) {
                $prevDurMin = $this->parseMinutes($prev['duration_request'] ?? 0);
            }
            $prevEndTs = $prevStartTs + ($prevDurMin * 60);
        }

        $prevEndWithBufferTs = $prevEndTs + ($bufferMin * 60);
        if ($startTs < $prevEndWithBufferTs) {
            $minStart = date('Y-m-d H:i:s', $prevEndWithBufferTs);
            throw new \mysqli_sql_exception(sprintf($MSG['conflict_prev'], $minStart));
        }
    }

    // ---------- Cita siguiente ----------
    $sqlNext = "
        SELECT second_opinion_id, request_date_to, request_date_end, pricing_id, duration_request, type_request
        FROM {$this->table}
        WHERE deleted_at IS NULL
          AND specialist_id = ?
          AND status NOT IN ($notIn)
          AND request_date_to >= ?
          " . ($excludeId ? "AND second_opinion_id <> ?" : "") . "
        ORDER BY request_date_to ASC
        LIMIT 1";
    $stmt = $excludeId
        ? $this->db->prepare($sqlNext)
        : $this->db->prepare(str_replace("AND second_opinion_id <> ?", "", $sqlNext));

    if (!$stmt) {
        throw new \mysqli_sql_exception($MSG['prepare_failed'] . $this->db->error);
    }

    if ($excludeId) {
        $stmt->bind_param("sss", $specialistId, $requestedStart, $excludeId);
    } else {
        $stmt->bind_param("ss", $specialistId, $requestedStart);
    }
    $stmt->execute();
    $next = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($next) {
        // fin de esta cita + buffer
        $thisEndWithBufferTs = ($requestedEnd !== null)
            ? (strtotime($requestedEnd) + ($bufferMin * 60))
            : ($startTs + ($durationMin * 60) + ($bufferMin * 60));

        $nextStartTs = strtotime($next['request_date_to']);
        if ($nextStartTs < $thisEndWithBufferTs) {
            $minNext = date('Y-m-d H:i:s', $thisEndWithBufferTs);
            throw new \mysqli_sql_exception(sprintf($MSG['conflict_next'], $minNext));
        }
    }
}

    private function validateScheduleIfNeeded(
    string $typeRequest,
    string $specialistId,
    ?string $requestedStart,
    int $durationMin,
    int $bufferMin,
    ?string $excludeId = null,
    ?string $requestedEnd = null
): void {
    // Idioma
    $lang = strtoupper($_SESSION['idioma'] ?? 'EN');
    $es = ($lang === 'ES');

    // Diccionario de mensajes
    $MSG = [
        'fmt_start' => $es
            ? "Formato de request_date_to inválido. Use 'YYYY-MM-DD HH:MM:SS'."
            : "Invalid request_date_to format. Use 'YYYY-MM-DD HH:MM:SS'.",
        'fmt_end' => $es
            ? "Formato de request_date_end inválido. Use 'YYYY-MM-DD HH:MM:SS'."
            : "Invalid request_date_end format. Use 'YYYY-MM-DD HH:MM:SS'.",
        'end_gt_start' => $es
            ? 'request_date_end debe ser mayor que request_date_to.'
            : 'request_date_end must be greater than request_date_to.',
        'start_required_sched' => $es
            ? 'request_date_to inválido. No puede estar vacío para solicitudes con agenda.'
            : 'Invalid request_date_to. It cannot be empty for scheduled requests.',
        'prepare_failed' => $es
            ? 'Error al preparar la consulta: '
            : 'Prepare failed: ',
        'conflict_prev' => $es
            ? 'Conflicto de agenda: la hora de inicio debe ser >= %s (cita previa + duración + buffer).'
            : 'Schedule conflict: start must be >= %s (prev appt + duration + buffer).',
        'conflict_next' => $es
            ? 'Conflicto de agenda: la próxima cita debe iniciar en o después de %s (esta cita + duración + buffer).'
            : 'Schedule conflict: next appointment must start at or after %s (this appt + duration + buffer).',
    ];

    // Normaliza tipo y decide si requiere agenda
    $t = strtolower(trim((string) $typeRequest));
    $requiresSchedule = in_array($t, ['appointment_request', 'appointment', 'call', 'video_call'], true);

    // No requiere agenda (p. ej., document_review)
    if (!$requiresSchedule) {
        if (!empty($requestedStart)) {
            $ts = strtotime($requestedStart);
            if ($ts === false) {
                throw new \mysqli_sql_exception($MSG['fmt_start']);
            }
            if (!empty($requestedEnd)) {
                $te = strtotime($requestedEnd);
                if ($te === false) {
                    throw new \mysqli_sql_exception($MSG['fmt_end']);
                }
                if ($te <= $ts) {
                    throw new \mysqli_sql_exception($MSG['end_gt_start']);
                }
            }
        }
        return; // no se validan conflictos
    }

    // ===== A partir de aquí: SOLO tipos que requieren agenda =====
    if (empty($requestedStart)) {
        throw new \mysqli_sql_exception($MSG['start_required_sched']);
    }
    $startTs = strtotime($requestedStart);
    if ($startTs === false) {
        throw new \mysqli_sql_exception($MSG['fmt_start']);
    }

    $newEndTs = null;
    if ($requestedEnd !== null && $requestedEnd !== '') {
        $newEndTs = strtotime($requestedEnd);
        if ($newEndTs === false) {
            throw new \mysqli_sql_exception($MSG['fmt_end']);
        }
        if ($newEndTs <= $startTs) {
            throw new \mysqli_sql_exception($MSG['end_gt_start']);
        }
    } else {
        // Si no traen fin, lo calculamos con duración + buffer
        $newEndTs = $startTs + ($durationMin * 60) + ($bufferMin * 60);
    }

    $notIn = "'completed','cancelled','rejected'";

    // ===== Cita previa =====
    $sqlPrev = "
        SELECT second_opinion_id, request_date_to, request_date_end, pricing_id, duration_request, type_request
        FROM {$this->table}
        WHERE deleted_at IS NULL
          AND specialist_id = ?
          AND status NOT IN ($notIn)
          AND request_date_to <= ?
          " . ($excludeId ? "AND second_opinion_id <> ?" : "") . "
        ORDER BY request_date_to DESC
        LIMIT 1";
    $stmt = $excludeId
        ? $this->db->prepare($sqlPrev)
        : $this->db->prepare(str_replace("AND second_opinion_id <> ?", "", $sqlPrev));
    if (!$stmt) {
        throw new \mysqli_sql_exception($MSG['prepare_failed'] . $this->db->error);
    }
    if ($excludeId) {
        $stmt->bind_param("sss", $specialistId, $requestedStart, $excludeId);
    } else {
        $stmt->bind_param("ss", $specialistId, $requestedStart);
    }
    $stmt->execute();
    $prev = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($prev) {
        $prevStartTs = strtotime($prev['request_date_to']);
        $prevEndTs = null;

        if (!empty($prev['request_date_end'])) {
            $prevEndTs = strtotime($prev['request_date_end']);
        } else {
            // calcular por duración
            $prevDurMin = 0;
            if (!empty($prev['pricing_id'])) {
                $p = $this->getPricingById($prev['pricing_id']);
                if ($p && isset($p['duration_services'])) {
                    $prevDurMin = $this->parseMinutes($p['duration_services']);
                }
            }
            if ($prevDurMin <= 0) {
                $prevDurMin = $this->parseMinutes($prev['duration_request'] ?? 0);
            }
            $prevEndTs = $prevStartTs + ($prevDurMin * 60);
        }

        $prevEndWithBufferTs = $prevEndTs + ($bufferMin * 60);
        if ($startTs < $prevEndWithBufferTs) {
            $minStart = date('Y-m-d H:i:s', $prevEndWithBufferTs);
            throw new \mysqli_sql_exception(sprintf($MSG['conflict_prev'], $minStart));
        }
    }

    // ===== Cita siguiente =====
    $sqlNext = "
        SELECT second_opinion_id, request_date_to, request_date_end, pricing_id, duration_request, type_request
        FROM {$this->table}
        WHERE deleted_at IS NULL
          AND specialist_id = ?
          AND status NOT IN ($notIn)
          AND request_date_to >= ?
          " . ($excludeId ? "AND second_opinion_id <> ?" : "") . "
        ORDER BY request_date_to ASC
        LIMIT 1";
    $stmt = $excludeId
        ? $this->db->prepare($sqlNext)
        : $this->db->prepare(str_replace("AND second_opinion_id <> ?", "", $sqlNext));
    if (!$stmt) {
        throw new \mysqli_sql_exception($MSG['prepare_failed'] . $this->db->error);
    }
    if ($excludeId) {
        $stmt->bind_param("sss", $specialistId, $requestedStart, $excludeId);
    } else {
        $stmt->bind_param("ss", $specialistId, $requestedStart);
    }
    $stmt->execute();
    $next = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($next) {
        $thisEndWithBufferTs = ($requestedEnd !== null && $requestedEnd !== '')
            ? (strtotime($requestedEnd) + ($bufferMin * 60))
            : ($startTs + ($durationMin * 60) + ($bufferMin * 60));

        $nextStartTs = strtotime($next['request_date_to']);
        if ($nextStartTs < $thisEndWithBufferTs) {
            $minNext = date('Y-m-d H:i:s', $thisEndWithBufferTs);
            throw new \mysqli_sql_exception(sprintf($MSG['conflict_next'], $minNext));
        }
    }
}


    /* ===================== Validaciones / Normalizaciones ===================== */

    /** Tipos válidos para type_request */
    private const TYPE_REQUEST_ALLOWED = [
        'document_review',
        'appointment_request',
        'block', // NUEVO
    ];

    /** Alcances válidos para scope_request */
    private const SCOPE_REQUEST_ALLOWED = [
        'share_none',
        'share_all',
        'share_custom',
    ];

    private function normalizeTypeRequest(?string $type): string
    {
        $type = $type ? strtolower(trim($type)) : '';
        if (!in_array($type, self::TYPE_REQUEST_ALLOWED, true)) {
            throw new \mysqli_sql_exception(
                "Invalid type_request. Allowed: " . implode(', ', self::TYPE_REQUEST_ALLOWED)
            );
        }
        return $type;
    }

    private function normalizeScopeRequest(?string $scope): ?string
    {
        if ($scope === null || $scope === '')
            return null; // permitir NULL
        $scope = strtolower(trim($scope));
        if (!in_array($scope, self::SCOPE_REQUEST_ALLOWED, true)) {
            throw new \mysqli_sql_exception(
                "Invalid scope_request. Allowed: " . implode(', ', self::SCOPE_REQUEST_ALLOWED)
            );
        }
        return $scope;
    }

    private function normalizeCostRequest($cost): ?string
    {
        if ($cost === null || $cost === '')
            return null; // permitir NULL
        $norm = str_replace(',', '.', (string) $cost);
        if (!is_numeric($norm)) {
            throw new \mysqli_sql_exception("Invalid cost_request. Must be numeric.");
        }
        return number_format((float) $norm, 2, '.', ''); // string con 2 decimales
    }

    /* ===================== Queries ===================== */

    public function getAll(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE deleted_at IS NULL ORDER BY created_at DESC";
        $res = $this->db->query($sql);
        if (!$res) {
            error_log("[SecondOpinionRequestsModel::getAll] Query error: " . $this->db->error . " | SQL: $sql");
            throw new \mysqli_sql_exception("Query error: " . $this->db->error);
        }
        $rows = $res->fetch_all(MYSQLI_ASSOC);

        foreach ($rows as &$r) {
            try {
                // information (no habrá datos si fue 'block')
                $r['information'] = $this->buildInformationForRequest($r['second_opinion_id'], $r['user_id'] ?? null);
                unset($r['data_friendly']);

                // pricing (JOIN lógico por pricing_id)
                $r['pricing'] = isset($r['pricing_id']) ? $this->getPricingById($r['pricing_id']) : null;
            } catch (\Throwable $ex) {
                error_log("[SecondOpinionRequestsModel::getAll] information/pricing error id="
                    . ($r['second_opinion_id'] ?? 'NULL') . " | " . $ex->getMessage());
                $r['information_error'] = $ex->getMessage();
            }
        }
        unset($r);

        return $rows;
    }



   public function applyAction(array $in): bool
    {
        $accion = strtolower(trim((string) ($in['accion'] ?? '')));
        $id = (string) ($in['second_opinion_id'] ?? '');

        if (!in_array($accion, ['confirm', 'reject', 'cancel'], true)) {
            throw new \mysqli_sql_exception("Invalid accion. Use: confirm, reject, cancel.");
        }
        if ($id === '') {
            throw new \mysqli_sql_exception("second_opinion_id is required.");
        }

        // Carga registro
        $req = $this->getById($id); // <-- $req contiene 'user_id' (paciente) y 'specialist_id'
        if (!$req) {
            throw new \mysqli_sql_exception("Request not found.");
        }

        // Estados terminales no modificables
        $terminales = ['completed', 'cancelled', 'rejected'];
        if (isset($req['status']) && in_array($req['status'], $terminales, true)) {
            throw new \mysqli_sql_exception("Request is already in a terminal status.");
        }

        // Contexto/Auditoría/Zona horaria
        $this->db->begin_transaction();
        try {
            $sessionUserId = $_SESSION['user_id'] ?? null;

            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $sessionUserId);

            $tzManager = new TimezoneManager($this->db);
            $tzManager->applyTimezone();

            $now = $env->getCurrentDatetime();

            // Validaciones de pertenencia
            if ($accion === 'confirm' || $accion === 'reject') {
                // Debe pertenecer al especialista logueado (tomas specialist_id de session como user_id)
                if (($req['specialist_id'] ?? null) !== $sessionUserId) {
                    throw new \mysqli_sql_exception("You cannot modify a request that does not belong to the logged-in specialist.");
                }
            } elseif ($accion === 'cancel') {
                // Debe pertenecer al usuario logueado
                if (($req['user_id'] ?? null) !== $sessionUserId) {
                    throw new \mysqli_sql_exception("You cannot cancel a request that does not belong to the logged-in user.");
                }
            }

            // Preparar UPDATE según acción
            $newStatus = null; // <-- Importante para la notificación
            $rejectMsg = null;

            if ($accion === 'confirm') {
                $newStatus = 'upcoming'; // <-- 'upcoming'
                $rejectMsg = null; // limpiar si hubiera
            } elseif ($accion === 'reject') {
                $newStatus = 'rejected'; // <-- 'rejected'
                $rejectMsg = trim((string) ($in['reject_message'] ?? ''));
                if ($rejectMsg === '') {
                    throw new \mysqli_sql_exception("reject_message is required when accion = reject.");
                }
            } else { // cancel
                $newStatus = 'cancelled'; // <-- 'cancelled'
                $rejectMsg = null; // no aplica
            }

            // Extra: bloquear cambios cuando ya está en el mismo estado
            if (($req['status'] ?? null) === $newStatus) {
                // idempotente: lo consideramos OK sin tocar nada
                $this->db->commit();
                return true;
            }

            // WHERE evita tocar terminales
            $sql = "
            UPDATE {$this->table}
                SET status         = ?,
                    reject_message = ?,
                    updated_at     = ?,
                    updated_by     = ?
                WHERE second_opinion_id = ?
                AND deleted_at IS NULL
                AND status NOT IN ('completed','cancelled','rejected')
            ";
            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                throw new \mysqli_sql_exception("Prepare failed: " . $this->db->error);
            }

            $stmt->bind_param(
                "sssss",
                $newStatus,
                $rejectMsg,
                $now,
                $sessionUserId,
                $id
            );

            if (!$stmt->execute()) {
                $stmt->close();
                throw new \mysqli_sql_exception("Execute failed: " . $stmt->error);
            }
            $affected = $stmt->affected_rows;
            $stmt->close();

            if ($affected <= 0) {
                // No rows affected -> pudo ser por carrera o estado ya terminal
                throw new \mysqli_sql_exception("No changes were applied. The request might have been modified already.");
            }

            // ==========================================================
            // ===== INICIO: LÓGICA DE NOTIFICACIÓN CENTRALIZADA (EL CAMBIO) =====
            // ==========================================================
            //
            // Hemos borrado los dos bloques de notificación que estaban aquí
            // y los reemplazamos por esta única llamada.
            //
            if ($affected > 0 && !empty($newStatus)) {
                $this->sendStatusChangeNotification($id, $newStatus);
            }
            //
            // ==========================================================
            // ===== FIN: LÓGICA DE NOTIFICACIÓN CENTRALIZADA =====
            // ==========================================================


            $this->db->commit();
            return true;
        } catch (\mysqli_sql_exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    public function getRequestData($second_opinion_id)
    {
        try {
            // Buscar el user_id asociado a la solicitud
            $stmt = $this->db->prepare("
            SELECT user_id
            FROM {$this->table}
            WHERE second_opinion_id = ? AND deleted_at IS NULL
            LIMIT 1
        ");
            if (!$stmt) {
                throw new \mysqli_sql_exception("Error preparando consulta: " . $this->db->error);
            }

            $stmt->bind_param("s", $second_opinion_id);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            $userId = $result['user_id'] ?? null;

            // Llamar a buildInformationForRequest con el user_id encontrado
            return $this->buildInformationForRequest($second_opinion_id, $userId);

        } catch (\Throwable $ex) {
            error_log("[SecondOpinionRequestsModel::getRequestData] Error: " . $ex->getMessage());
            return [];
        }
    }


    public function getSimpleRequestDataBySpecialistId($specialist_id)
    {
        $sql = "SELECT sor.second_opinion_id, sor.type_request, sor.status, sor.created_at, sor.duration_request, sor.request_date_to, sor.timezone
                FROM {$this->table} sor
                WHERE sor.specialist_id = ? AND sor.deleted_at IS NULL AND sor.type_request = 'appointment_request'
                ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("[SecondOpinionRequestsModel::getSimpleRequestDataBySpecialistId] Prepare failed: " . $this->db->error . " | SQL: $sql");
            throw new \mysqli_sql_exception("Prepare failed: " . $this->db->error);
        }
        $stmt->bind_param("s", $specialist_id);
        if (!$stmt->execute()) {
            error_log("[SecondOpinionRequestsModel::getSimpleRequestDataBySpecialistId] Execute failed: " . $stmt->error);
            $stmt->close();
            throw new \mysqli_sql_exception("Execute failed: " . $stmt->error);
        }
        $res = $stmt->get_result();
        $rows = $res->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;

    }

    // Modificación para incluir la traducción de la fecha al timezone del usuario de la sesión PARA MÓDULO DE SEGUNDA OPINIÓN

    public function getSimpleRequestDataBySpecialistSecondOpinionId($specialist_id)
    {
        $sql = "SELECT sor.second_opinion_id, sor.type_request, sor.status, sor.created_at, sor.duration_request, sor.request_date_to, sor.timezone AS request_timezone
                FROM {$this->table} sor
                WHERE sor.specialist_id = ? AND sor.deleted_at IS NULL AND sor.type_request = 'appointment_request'
                ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("[SecondOpinionRequestsModel::getSimpleRequestDataBySpecialistId] Prepare failed: " . $this->db->error . " | SQL: $sql");
            throw new \mysqli_sql_exception("Prepare failed: " . $this->db->error);
        }
        $stmt->bind_param("s", $specialist_id);
        if (!$stmt->execute()) {
            error_log("[SecondOpinionRequestsModel::getSimpleRequestDataBySpecialistId] Execute failed: " . $stmt->error);
            $stmt->close();
            throw new \mysqli_sql_exception("Execute failed: " . $stmt->error);
        }
        $res = $stmt->get_result();
        $rows = $res->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $userTimezone = $_SESSION['timezone'] ?? 'UTC';

        foreach ($rows as &$row) {
            // Verificamos que la fecha no esté vacía
            if (!empty($row['request_date_to'])) {
                try {
                    // Creamos el objeto DateTime asumiendo que la fecha de la BD está en UTC
                    $utcDate = new DateTime($row['request_date_to'], new DateTimeZone('UTC'));

                    // Convertimos la fecha a la zona horaria del usuario de la sesión
                    $utcDate->setTimezone(new DateTimeZone($userTimezone));

                    // Añadimos los nuevos campos con la fecha traducida y la zona horaria
                    $row['user_request_date_to'] = $utcDate->format('Y-m-d H:i:s');
                    $row['user_timezone'] = $userTimezone;

                } catch (Exception $e) {
                    // Si hay un error, lo indicamos
                    $row['user_request_date_to'] = 'Translation Error';
                }
            } else {
                $row['user_request_date_to'] = null;
            }
        }
        unset($row);
        return $rows;
    }

    public function getRequestByIdForUser(string $second_opinion_id): ?array
    {
        $sql = "
            SELECT
                sor.second_opinion_id, sor.user_id, sor.status, sor.notes, sor.created_at, sor.updated_at, sor.reject_message,
                sor.type_request, sor.scope_request, sor.cost_request, sor.request_date_to, sor.request_date_end,
                sor.specialist_id, sor.pricing_id,
                s.first_name, s.last_name,
                sp.service_type, sp.duration_services, sp.description
            FROM
                {$this->table} AS sor
            INNER JOIN specialists AS s ON sor.specialist_id = s.specialist_id
            LEFT JOIN specialist_pricing AS sp ON sor.pricing_id = sp.pricing_id
            WHERE sor.second_opinion_id = ?
                AND sor.deleted_at IS NULL AND s.status = 1
                AND s.deleted_at IS NULL
                AND (sp.pricing_id IS NULL OR (sp.is_active = 1 AND sp.deleted_at IS NULL))
            LIMIT 1;
        ";


        $stmt = $this->db->prepare($sql);
        if ($stmt === false) {
            error_log("[SecondOpinionRequestsModel::getRequestByIdForUser] Prepare error: " . $this->db->error);
            throw new \mysqli_sql_exception("Prepare error: " . $this->db->error);
        }


        $stmt->bind_param("s", $second_opinion_id);

        if (!$stmt->execute()) {
            error_log("[SecondOpinionRequestsModel::getRequestByIdForUser] Execute error: " . $stmt->error);
            $stmt->close();
            throw new \mysqli_sql_exception("Execute error: " . $stmt->error);
        }

        $res = $stmt->get_result();
        $request = $res->fetch_assoc();
        $stmt->close();

        if ($request) {
            $request['specialist_image'] = $this->specialistImageExists($request['specialist_id']);
        }

        // Verificar si posee una review

        if ($request) {
            require_once __DIR__ . '/SpecialistReviewsModel.php';
            $specialistReviewModel = new SpecialistReviewsModel();
            $review = $specialistReviewModel->getByRequestId($second_opinion_id);

            $request['has_review'] = $review ? true : false;
            $request['review'] = $review ? $review['comment'] : null;

        }

        return $request;
    }

    public function getRequestsForUser($user_id, array $filters = []): array
    {
        // Extraer filtros
        $status = $filters['status'] ?? 'all';

        $params = [];
        $types = '';

        $sql = "
            SELECT
                sor.second_opinion_id, sor.status, sor.notes, sor.created_at, sor.updated_at,
                sor.type_request, sor.scope_request, sor.cost_request, sor.request_date_to, sor.request_date_end,
                sor.specialist_id, sor.pricing_id,
                s.first_name as first_name, s.last_name as last_name,
                sp.service_type, sp.duration_services, sp.description
            FROM
                {$this->table} AS sor
            INNER JOIN specialists AS s ON sor.specialist_id = s.specialist_id
            LEFT JOIN specialist_pricing AS sp ON sor.pricing_id = sp.pricing_id
            WHERE
                sor.user_id = ?
                AND sor.deleted_at IS NULL
                AND s.status = 1
                AND s.deleted_at IS NULL
                AND (sp.pricing_id IS NULL OR (sp.is_active = 1 AND sp.deleted_at IS NULL))
        ";

        $params[] = $user_id;
        $types .= 's';

        // Añadir filtro de status si no es 'all'
        if ($status !== 'all' && $status !== '') {
            $sql .= " AND sor.status = ?";
            $params[] = $status;
            $types .= 's';
        }

        $sql .= " ORDER BY sor.created_at DESC;";

        $stmt = $this->db->prepare($sql);
        if ($stmt === false) {
            error_log("[SecondOpinionRequestsModel::getRequestsForUser] Prepare error: " . $this->db->error);
            throw new \mysqli_sql_exception("Prepare error: " . $this->db->error);
        }

        if (!empty($types)) {
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            error_log("[SecondOpinionRequestsModel::getRequestsForUser] Execute error: " . $stmt->error);
            $stmt->close();
            throw new \mysqli_sql_exception("Execute error: " . $stmt->error);
        }

        $res = $stmt->get_result();
        $rows = $res->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        foreach ($rows as &$r) {
            $r['user_image'] = $this->specialistImageExists(userId: $r['specialist_id']);
        }
        unset($r);

        return $rows;
    }



    public function getRequestByIdForSpecialist(string $second_opinion_id, string $specialist_id): ?array
    {
        $sql = "
            SELECT
                sor.second_opinion_id, sor.status, sor.notes, sor.created_at, sor.updated_at,
                sor.type_request, sor.scope_request, sor.cost_request, sor.request_date_to, sor.request_date_end, sor.reject_message,
                sor.user_id, sor.pricing_id,
                u.first_name, u.last_name, u.sex_biological, u.telephone, u.email,
                sp.service_type, sp.duration_services, sp.description
            FROM
                {$this->table} AS sor
            INNER JOIN users AS u ON sor.user_id = u.user_id
            LEFT JOIN specialist_pricing AS sp ON sor.pricing_id = sp.pricing_id
            WHERE
                sor.specialist_id = ?
                AND sor.second_opinion_id = ?
                AND sor.deleted_at IS NULL AND u.status = 1
                AND u.deleted_at IS NULL
                AND (sp.pricing_id IS NULL OR (sp.is_active = 1 AND sp.deleted_at IS NULL))
            LIMIT 1;
        ";

        $stmt = $this->db->prepare($sql);
        if ($stmt === false) {
            error_log("[SecondOpinionRequestsModel::getRequestByIdForSpecialist] Prepare error: " . $this->db->error);
            throw new \mysqli_sql_exception("Prepare error: " . $this->db->error);
        }

        $stmt->bind_param("ss", $specialist_id, $second_opinion_id);

        if (!$stmt->execute()) {
            error_log("[SecondOpinionRequestsModel::getRequestByIdForSpecialist] Execute error: " . $stmt->error);
            $stmt->close();
            throw new \mysqli_sql_exception("Execute error: " . $stmt->error);
        }

        $res = $stmt->get_result();
        $request = $res->fetch_assoc();
        $stmt->close();

        if ($request) {
            $request['user_image'] = $this->userImageExists($request['user_id']);
        }

        return $request;
    }

    public function getRequestsForSpecialist($specialist_id, array $filters = []): array
    {
        // Extraer filtros con valores por defecto
        $status = $filters['status'] ?? 'all';
        $type = $filters['type'] ?? 'all';
        $search = $filters['search'] ?? '';

        $params = [];
        $types = '';

        $sql = "
            SELECT
                sor.second_opinion_id, sor.status, sor.notes, sor.created_at, sor.updated_at,
                sor.type_request, sor.scope_request, sor.cost_request, sor.request_date_to, sor.request_date_end,
                sor.user_id, sor.pricing_id,
                u.first_name, u.last_name, u.sex_biological,
                sp.service_type, sp.duration_services, sp.description
            FROM
                {$this->table} AS sor
            INNER JOIN users AS u ON sor.user_id = u.user_id
            LEFT JOIN specialist_pricing AS sp ON sor.pricing_id = sp.pricing_id
            WHERE
                sor.specialist_id = ?
                AND sor.deleted_at IS NULL AND u.status = 1
                AND u.deleted_at IS NULL
                AND (sp.pricing_id IS NULL OR (sp.is_active = 1 AND sp.deleted_at IS NULL))
        ";

        $params[] = $specialist_id;
        $types .= 's';

        // Añadir filtros dinámicos
        if ($status !== 'all' && $status !== '') {
            $sql .= " AND sor.status = ?";
            $params[] = $status;
            $types .= 's';
        }

        if ($type !== 'all' && $type !== '') {
            $sql .= " AND sor.type_request = ?";
            $params[] = $type;
            $types .= 's';
        }

        if ($search !== '') {
            $sql .= " AND (CONCAT(u.first_name, ' ', u.last_name) LIKE ? OR u.email LIKE ?)";
            $searchTerm = '%' . $search . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= 'ss';
        }

        $sql .= " ORDER BY sor.created_at DESC;";

        $stmt = $this->db->prepare($sql);
        if ($stmt === false) {
            error_log("[SecondOpinionRequestsModel::getRequestsForSpecialist] Prepare error: " . $this->db->error);
            throw new \mysqli_sql_exception("Prepare error: " . $this->db->error);
        }

        // Bindeo dinámico de parámetros
        if (!empty($types)) {
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            error_log("[SecondOpinionRequestsModel::getRequestsForSpecialist] Execute error: " . $stmt->error);
            $stmt->close();
            throw new \mysqli_sql_exception("Execute error: " . $stmt->error);
        }

        $res = $stmt->get_result();
        $rows = $res->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        foreach ($rows as &$r) {
            $r['user_image'] = $this->userImageExists($r['user_id']);
        }
        unset($r);

        return $rows;
    }

    // PARA PERFIL DE ESPECIALISTA
    public function getBlockRequestForSpecialist($specialist_id)
    {
        // MODIFICADO: Usar sentencias preparadas para seguridad y consistencia
        $sql = "
         SELECT sor.*
            FROM
                {$this->table} AS sor
            WHERE
                sor.type_request = 'block'
                AND sor.deleted_at IS NULL
                AND sor.specialist_id = ?
            ORDER BY
                sor.created_at DESC;";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("[SecondOpinionRequestsModel::getBlockRequestForSpecialist] Prepare failed: " . $this->db->error);
            throw new \mysqli_sql_exception("Prepare failed: " . $this->db->error);
        }

        $stmt->bind_param("s", $specialist_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $rows = $res->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        // ELIMINADO: Se quita el bucle que convertía las fechas a la zona horaria local.
        // La API devolverá las fechas en UTC para que el frontend las formatee.
        $userTimezone = 'UTC';

        foreach ($rows as &$row) {
            try {
                // Traducir la fecha de inicio del bloqueo
                if (!empty($row['request_date_to'])) {
                    $startDate = new DateTime($row['request_date_to'], new DateTimeZone('UTC'));
                    $startDate->setTimezone(new DateTimeZone($userTimezone));
                    $row['request_date_to'] = $startDate->format('Y-m-d H:i:s');
                } else {
                    $row['request_date_to'] = null;
                }

                // Traducir la fecha de fin del bloqueo
                if (!empty($row['request_date_end'])) {
                    $endDate = new DateTime($row['request_date_end'], new DateTimeZone('UTC'));
                    $endDate->setTimezone(new DateTimeZone($userTimezone));
                    $row['request_date_end'] = $endDate->format('Y-m-d H:i:s');
                } else {
                    $row['request_date_end'] = null;
                }



            } catch (Exception $e) {
                $row['request_date_to'] = 'Translation Error';
                $row['request_date_end'] = 'Translation Error';
            }
        }
        unset($row); // Romper la referencia
        // ==================== FIN DE LA MODIFICACIÓN =====================


        return $rows;
    }
    // FUNCIÓN QUE OTORGA LAS FECHAS FORMATEADAS AL USUARIO PARA LAS SECUNDAS OPINIONES

    public function getBlockRequestForSpecialistSecondOpinion($specialist_id)
    {
        $sql = "
         SELECT *
            FROM
                {$this->table} AS sor
            WHERE
                sor.type_request = 'block'
                AND sor.deleted_at IS NULL
                AND sor.specialist_id = '" . $this->db->real_escape_string($specialist_id) . "'
            ORDER BY
                sor.created_at DESC;";
        $res = $this->db->query($sql);
        if (!$res) {
            error_log("[SecondOpinionRequestsModel::getBlockRequestForSpecialist] Query error: " . $this->db->error . " | SQL: $sql");
            throw new \mysqli_sql_exception("Query error: " . $this->db->error);
        }
        $rows = $res->fetch_all(MYSQLI_ASSOC);

        // =================== INICIO DE LA MODIFICACIÓN ===================
        $userTimezone = $_SESSION['timezone'] ?? 'UTC';

        foreach ($rows as &$row) {
            try {
                // Traducir la fecha de inicio del bloqueo
                if (!empty($row['request_date_to'])) {
                    $startDate = new DateTime($row['request_date_to'], new DateTimeZone('UTC'));
                    $startDate->setTimezone(new DateTimeZone($userTimezone));
                    $row['user_request_date_to'] = $startDate->format('Y-m-d H:i:s');
                } else {
                    $row['user_request_date_to'] = null;
                }

                // Traducir la fecha de fin del bloqueo
                if (!empty($row['request_date_end'])) {
                    $endDate = new DateTime($row['request_date_end'], new DateTimeZone('UTC'));
                    $endDate->setTimezone(new DateTimeZone($userTimezone));
                    $row['user_request_date_end'] = $endDate->format('Y-m-d H:i:s');
                } else {
                    $row['user_request_date_end'] = null;
                }

                // Añadir la zona horaria de referencia
                $row['user_timezone'] = $userTimezone;

            } catch (Exception $e) {
                $row['user_request_date_to'] = 'Translation Error';
                $row['user_request_date_end'] = 'Translation Error';
            }
        }
        unset($row); // Romper la referencia
        // ==================== FIN DE LA MODIFICACIÓN =====================

        return $rows;
    }

    /**
     * Obtiene los slots de tiempo ocupados por un especialista en un rango de fechas.
     * Devuelve los slots como objetos DateTime en la zona horaria UTC para una comparación consistente.
     *
     * @param string $specialistId El ID del especialista.
     * @param string $utcStartDate La fecha de inicio del rango en formato UTC 'Y-m-d H:i:s'.
     * @param string $utcEndDate La fecha de fin del rango en formato UTC 'Y-m-d H:i:s'.
     * @return array Un array de arrays, cada uno con claves 'start' y 'end' como objetos DateTime en UTC.
     */
    public function getBusySlotsForSpecialist(string $specialistId, string $utcStartDate, string $utcEndDate): array
    {
        // 1. AÑADIR LA COLUMNA `timezone` A LA CONSULTA
        $sql = "SELECT request_date_to, request_date_end, duration_request, pricing_id, timezone
            FROM {$this->table}
            WHERE specialist_id = ?
              AND deleted_at IS NULL
              AND status NOT IN ('cancelled', 'rejected')
              AND type_request IN ('appointment_request', 'block')
              AND request_date_to BETWEEN ? AND ?";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new \mysqli_sql_exception("Prepare failed: " . $this->db->error);
        }

        $stmt->bind_param("sss", $specialistId, $utcStartDate, $utcEndDate);
        $stmt->execute();
        $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        $busySlots = [];
        $utcZone = new DateTimeZone('UTC');

        foreach ($results as $row) {
            try {
                // 2. Usar la zona horaria de la CITA, con fallback a UTC si es nula.
                $originalTimezoneStr = $row['timezone'] ?? 'UTC';
                $originalZone = new DateTimeZone($originalTimezoneStr);

                // 3. Crear el objeto DateTime en su ZONA HORARIA ORIGINAL
                $start = new DateTime($row['request_date_to'], $originalZone);

                // 4. CONVERTIR INMEDIATAMENTE A UTC para estandarizar
                $start->setTimezone($utcZone);

                $end = null;
                if (!empty($row['request_date_end'])) {
                    // Repetir el proceso para la fecha de fin
                    $end = new DateTime($row['request_date_end'], $originalZone);
                    $end->setTimezone($utcZone);
                } else {
                    // Calcular la duración. La fecha de inicio ($start) ya está en UTC,
                    // por lo que la modificación se aplicará correctamente.
                    $duration = $this->parseMinutes($row['duration_request'] ?? 0);
                    if ($duration <= 0 && !empty($row['pricing_id'])) {
                        // (Tu lógica existente para obtener duración)
                        [$duration,] = $this->getEffectiveDurationAndBuffer($row['pricing_id'], $specialistId, 0);
                    }
                    $end = clone $start;
                    $end->modify("+{$duration} minutes");
                }

                $busySlots[] = ['start' => $start, 'end' => $end];

            } catch (Exception $e) {
                // Opcional: registrar el error para depuración
                error_log("Error parsing busy slot: " . $e->getMessage());
            }
        }
        return $busySlots;
    }

    /**
     * Calcula los slots de tiempo disponibles para un servicio específico de un especialista.
     *
     * @param string $specialistId ID del especialista.
     * @param string $pricingId ID del servicio (para obtener la duración).
     * @param int $daysInFuture Cuántos días hacia el futuro buscar (ej. 30).
     * @return array Lista de slots disponibles en formato ISO 8601 UTC, compatible con FullCalendar.
     */
    public function getAvailableSlotsForService(string $specialistId, string $pricingId, int $daysInFuture = 30): array
    {
        // 1. --- OBTENER DATOS ESENCIALES ---
        require_once __DIR__ . '/SpecialistAvailabilityModel.php';
        require_once __DIR__ . '/SecondOpinionRequestsModel.php';


        $service = $this->getById($pricingId);
        if (!$service || empty($service['duration_services'])) {
            throw new Exception("Servicio no encontrado o sin duración definida.");
        }
        $serviceDuration = (int) $service['duration_services'];

        $specialistModel = new SpecialistModel(); // Necesario para obtener la timezone
        $specialist = $specialistModel->getById($specialistId);
        if (!$specialist || empty($specialist['timezone'])) {
            throw new Exception("Especialista no encontrado o sin zona horaria definida.");
        }
        $specialistTimezone = new DateTimeZone($specialist['timezone']);

        $availabilityModel = new SpecialistAvailabilityModel();
        $weeklyAvailability = $availabilityModel->getByIdSpecialist($specialistId);

        // 2. --- PREPARAR RANGO DE BÚSQUEDA Y EVENTOS OCUPADOS ---
        $startDate = new DateTime('today', $specialistTimezone);
        $endDate = (clone $startDate)->modify("+{$daysInFuture} days");

        $requestsModel = new SecondOpinionRequestsModel();
        $busySlots = $requestsModel->getBusySlotsForSpecialist(
            $specialistId,
            $startDate->format('Y-m-d 00:00:00'),
            $endDate->format('Y-m-d 23:59:59')
        );

        $availableSlots = [];
        $period = new DatePeriod($startDate, new DateInterval('P1D'), $endDate);

        // 3. --- GENERAR SLOTS POTENCIALES BASADOS EN LA DISPONIBILIDAD SEMANAL ---
        foreach ($period as $date) {
            $dayOfWeek = $date->format('l'); // 'Monday', 'Tuesday', etc.

            foreach ($weeklyAvailability as $availBlock) {
                if ($availBlock['weekday'] === $dayOfWeek) {
                    $bufferMinutes = (int) $availBlock['buffer_time_minutes'];

                    // Crear el inicio y fin del bloque de trabajo en la zona horaria del especialista
                    $blockStartLocal = new DateTime($date->format('Y-m-d') . ' ' . $availBlock['start_time'], $specialistTimezone);
                    $blockEndLocal = new DateTime($date->format('Y-m-d') . ' ' . $availBlock['end_time'], $specialistTimezone);

                    // Empezamos a generar slots
                    $currentSlotStart = clone $blockStartLocal;

                    while (true) {
                        $currentSlotEnd = (clone $currentSlotStart)->modify("+{$serviceDuration} minutes");

                        // Si el slot termina después del fin del bloque, paramos
                        if ($currentSlotEnd > $blockEndLocal) {
                            break;
                        }

                        // 4. --- FILTRAR SLOTS QUE CHOCAN CON EVENTOS OCUPADOS ---
                        $isAvailable = true;

                        // Convertir a UTC para comparar
                        $slotStartUTC = (clone $currentSlotStart)->setTimezone(new DateTimeZone('UTC'));
                        $slotEndUTC = (clone $currentSlotEnd)->setTimezone(new DateTimeZone('UTC'));

                        foreach ($busySlots as $busy) {
                            // Lógica de solapamiento: (StartA < EndB) y (EndA > StartB)
                            if ($slotStartUTC < $busy['end'] && $slotEndUTC > $busy['start']) {
                                $isAvailable = false;
                                break; // Este slot choca, no necesitamos seguir comprobando
                            }
                        }

                        if ($isAvailable) {
                            $availableSlots[] = [
                                'start' => $slotStartUTC->format(DateTime::ATOM), // Formato ISO 8601: '2025-10-27T14:00:00+00:00'
                                'end' => $slotEndUTC->format(DateTime::ATOM)
                            ];
                        }

                        // Mover el cursor al inicio del siguiente slot, incluyendo el buffer
                        $currentSlotStart->modify("+{$serviceDuration} minutes");
                        $currentSlotStart->modify("+{$bufferMinutes} minutes");
                    }
                }
            }
        }

        return $availableSlots;
    }

    /**
     * Verifica si existe un archivo de imagen para un usuario específico.
     */
    private function userImageExists(string $userId): bool
    {
        $uploadDir = APP_ROOT . '/uploads/users/';
        $pattern = $uploadDir . 'user_' . $userId . '.*';
        $foundFiles = glob($pattern);
        return !empty($foundFiles);
    }

    private function specialistImageExists(string $userId): bool
    {
        $uploadDir = APP_ROOT . '/uploads/specialist/';
        $pattern = $uploadDir . 'user_' . $userId . '.*';
        $foundFiles = glob($pattern);
        return !empty($foundFiles);
    }

    public function getById(string $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE second_opinion_id = ? AND deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("[SecondOpinionRequestsModel::getById] Prepare failed: " . $this->db->error . " | SQL: $sql");
            throw new \mysqli_sql_exception("Prepare failed: " . $this->db->error);
        }
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$row)
            return null;

        try {
            $row['information'] = $this->buildInformationForRequest($row['second_opinion_id'], $row['user_id'] ?? null);
            unset($row['data_friendly']);

            $row['pricing'] = isset($row['pricing_id']) ? $this->getPricingById($row['pricing_id']) : null;
        } catch (\Throwable $ex) {
            error_log("[SecondOpinionRequestsModel::getById] information/pricing error id=$id | " . $ex->getMessage());
            $row['information_error'] = $ex->getMessage();
        }

        return $row;
    }

    /* ===================== CRUD SEPARADO POR TYPE_REQUEST ===================== */
    /* --------------------- Despachadores --------------------- */

    public function create($data)
    {
        $type = isset($data['type_request']) ? strtolower(trim((string) $data['type_request'])) : null;
        if ($type === 'block') {
            return $this->createBlock($data);     // ⬅️ bloqueos
        }
        return $this->createStandard($data);       // ⬅️ resto
    }

    public function update($id, $data)
    {
        $current = $this->getById($id);
        if (!$current)
            throw new \mysqli_sql_exception("Record not found.");

        // Si se envía type_request en $data, decide por él; si no, usa el actual
        $incomingType = array_key_exists('type_request', $data)
            ? $this->normalizeTypeRequest($data['type_request'])
            : ($current['type_request'] ?? null);

        if ($incomingType === 'block') {
            return $this->updateBlock($id, $data);       // ⬅️ bloqueos
        }
        return $this->updateStandard($id, $data, $current); // ⬅️ resto
    }

/* --------------------- CRUD: STANDARD (no-block) --------------------- */
/* --------------------- CRUD: STANDARD (no-block) --------------------- */
public function createStandard($data)
{
    // Idioma de la sesión
    $lang = strtoupper($_SESSION['idioma'] ?? 'EN');
    $es = ($lang === 'ES');

    // Helper rápido para mensajes
    $MSG = [
        'invalid_flow' => $es
            ? 'Flujo inválido: use createBlock() para type_request=block.'
            : 'Invalid flow: use createBlock() for type_request=block.',
        'timezone_required' => $es
            ? 'La zona horaria (timezone) es obligatoria para la solicitud.'
            : 'Timezone is required for the request.',
        'missing_pricing' => $es
            ? 'Falta el parámetro requerido: pricing_id.'
            : 'Missing required parameter: pricing_id.',
        'pricing_prepare_err' => $es
            ? 'Error preparando la consulta de precios: '
            : 'Error preparing pricing lookup: ',
        'service_not_found' => $es
            ? 'Servicio no encontrado o no disponible.'
            : 'Service not found or unavailable.',
        'service_wrong_owner' => $es
            ? 'El servicio seleccionado no pertenece a este especialista.'
            : 'The selected service does not belong to this specialist.',
        'spec_prepare_err' => $es
            ? 'Error preparando la consulta de especialista: '
            : 'Error preparing specialist lookup: ',
        'monthly_count_prepare_err' => $es
            ? 'Error preparando el conteo mensual: '
            : 'Error preparing monthly count: ',
        'free_limit_reached' => $es
            ? 'El especialista alcanzó el número máximo de solicitudes GRATIS permitidas este mes.'
            : 'The specialist has reached the maximum number of FREE requests allowed this month.',
        'insert_prepare_err' => $es
            ? 'Error preparando la inserción: '
            : 'Error preparing insert statement: ',
        'insert_failed' => $es
            ? 'Fallo al insertar: '
            : 'Insert failed: ',
    ];

    $this->db->begin_transaction();
    try {
        $userId = $_SESSION['user_id'] ?? null; // <-- ID del Paciente (creador)

        $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
        $env->applyAuditContext($this->db, $userId);

        //$tzManager = new TimezoneManager($this->db);
        //$tzManager->applyTimezone();

        $createdAt = $env->getCurrentDatetime();
        $secondOpinionId = $this->generateUUIDv4();

        // ===== Normalizaciones (no-block) =====
        $typeRequest = $this->normalizeTypeRequest($data['type_request'] ?? null);
        if ($typeRequest === 'block') {
            throw new \mysqli_sql_exception($MSG['invalid_flow']);
        }

        $scopeRequest    = $this->normalizeScopeRequest($data['scope_request'] ?? null);
        $costRequest     = $this->normalizeCostRequest($data['cost_request'] ?? null);
        $pricingId       = $data['pricing_id'] ?? null;
        $notes           = $data['notes'] ?? null;
        $sharedUntil     = $data['shared_until'] ?? null;

        $specialistId = $data['specialist_id']; // <-- ID del Especialista (receptor)

        // NUEVO: Normalizar y validar la zona horaria
        $timezone = $data['timezone'] ?? null;
        if (empty($timezone)) {
            throw new \mysqli_sql_exception($MSG['timezone_required']);
        }

        $utcForDbStart = null;
        if (!empty($data['request_date_to'])) {
            $utcDateTimeStart = new \DateTime($data['request_date_to']); // '...Z' => UTC
            $utcForDbStart = $utcDateTimeStart->format('Y-m-d H:i:s');
        }

        $utcForDbEnd = null;
        if (!empty($data['request_date_end'])) {
            $utcDateTimeEnd = new \DateTime($data['request_date_end']);
            $utcForDbEnd = $utcDateTimeEnd->format('Y-m-d H:i:s');
        }

        // Renombramos las variables para mayor claridad en el resto del código
        $requestedStart = $utcForDbStart;
        $requestedEnd   = $utcForDbEnd;

        if (empty($pricingId)) {
            throw new \mysqli_sql_exception($MSG['missing_pricing']);
        }

        // 1) Validar que el pricing exista y pertenezca al especialista
        $sqlPricing = "
            SELECT sp.pricing_id, sp.specialist_id, sp.service_type
            FROM specialist_pricing sp
            WHERE sp.pricing_id = ? AND sp.deleted_at IS NULL
            LIMIT 1
        ";
        $stmtP = $this->db->prepare($sqlPricing);
        if (!$stmtP) {
            throw new \mysqli_sql_exception($MSG['pricing_prepare_err'] . $this->db->error);
        }
        $stmtP->bind_param("s", $pricingId);
        $stmtP->execute();
        $pricingRow = $stmtP->get_result()->fetch_assoc(); // <-- Necesitamos $pricingRow para la notificación
        $stmtP->close();

        if (!$pricingRow) {
            throw new \mysqli_sql_exception($MSG['service_not_found']);
        }
        if (!empty($pricingRow['specialist_id']) && $pricingRow['specialist_id'] !== $specialistId) {
            throw new \mysqli_sql_exception($MSG['service_wrong_owner']);
        }

        // 2) Leer cupo mensual desde specialists (solo aplica a consultas gratis)
        $sqlSpec = "
            SELECT COALESCE(max_free_consults_per_month, 0) AS max_free_consults_per_month
            FROM specialists
            WHERE specialist_id = ? AND deleted_at IS NULL
            LIMIT 1
        ";
        $stmtS = $this->db->prepare($sqlSpec);
        if (!$stmtS) {
            throw new \mysqli_sql_exception($MSG['spec_prepare_err'] . $this->db->error);
        }
        $stmtS->bind_param("s", $specialistId);
        $stmtS->execute();
        $specRow = $stmtS->get_result()->fetch_assoc();
        $stmtS->close();

        $maxPerMonth = (int) ($specRow['max_free_consults_per_month'] ?? 0);

        // 3) Si hay cupo y esta solicitud es gratis (cost_request = 0), validar el límite mensual
        if ($maxPerMonth > 0 && (string) $costRequest === '0') {
            $monthStart = (new \DateTime('first day of this month 00:00:00'))->format('Y-m-d H:i:s');
            $monthEnd   = (new \DateTime('last day of this month 23:59:59'))->format('Y-m-d H:i:s');

            $validStatuses = ['pending', 'awaiting_payment', 'upcoming', 'completed'];

            $sqlCount = "
                SELECT COUNT(*) AS cnt
                FROM {$this->table}
                WHERE specialist_id = ?
                    AND status IN (?, ?, ?, ?)
                    AND (cost_request = 0)
                    AND created_at BETWEEN ? AND ?
                    AND deleted_at IS NULL
            ";
            $stmtC = $this->db->prepare($sqlCount);
            if (!$stmtC) {
                throw new \mysqli_sql_exception($MSG['monthly_count_prepare_err'] . $this->db->error);
            }
            // 7 parámetros → "sssssss"
            $stmtC->bind_param(
                "sssssss",
                $specialistId,
                $validStatuses[0],
                $validStatuses[1],
                $validStatuses[2],
                $validStatuses[3],
                $monthStart,
                $monthEnd
            );
            $stmtC->execute();
            $rowCnt = $stmtC->get_result()->fetch_assoc();
            $stmtC->close();

            $usedThisMonth = (int) ($rowCnt['cnt'] ?? 0);
            if ($usedThisMonth >= $maxPerMonth) {
                throw new \mysqli_sql_exception($MSG['free_limit_reached']);
            }
        }

        // ====== Duración efectiva y validación de agenda ======
        [$durationMin, $bufferMin] = $this->getEffectiveDurationAndBuffer(
            $pricingId,
            $specialistId,
            $data['duration_request'] ?? null
        );
        $this->validateScheduleIfNeeded(
            $typeRequest,            // <- clave: usamos el tipo para decidir si validar agenda
            $specialistId,
            $requestedStart,           // puede venir null/'' para document_review
            $durationMin,
            $bufferMin,
            null,                      // excludeId
            $requestedEnd
        );

        $durationRequestStr = (string) $durationMin;

        $status = $data['status'];
        $rejectMessageNull = null;

        // MODIFICADO: Añadir 'timezone' a la consulta
        $stmt = $this->db->prepare("INSERT INTO {$this->table}
            (second_opinion_id, user_id, specialist_id, pricing_id, status, type_request, scope_request, cost_request, duration_request, notes, reject_message, shared_until, request_date_to, request_date_end, timezone, created_at, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            throw new \mysqli_sql_exception($MSG['insert_prepare_err'] . $this->db->error);
        }

        // MODIFICADO: Añadir 's' para el nuevo campo y la variable $timezone
        $stmt->bind_param(
            "sssssssssssssssss",
            $secondOpinionId,
            $data['user_id'],
            $specialistId,
            $pricingId,
            $status,
            $typeRequest,
            $scopeRequest,
            $costRequest,
            $durationRequestStr,
            $notes,
            $rejectMessageNull,
            $sharedUntil,
            $requestedStart,
            $requestedEnd,
            $timezone, // NUEVO
            $createdAt,
            $userId
        );
        if (!$stmt->execute()) {
            throw new \mysqli_sql_exception($MSG['insert_failed'] . $stmt->error);
        }
        $stmt->close();

        // ====== Procesar detalle compartido ======
        $pluckIds = function ($maybeList, string $childKey = 'id'): array {
            if (empty($maybeList)) return [];
            if (is_array($maybeList) && isset($maybeList[0]) && is_string($maybeList[0])) {
                return array_values(array_filter(array_map('strval', $maybeList)));
            }
            if (is_string($maybeList) && strpos($maybeList, ',') !== false) {
                return array_values(array_filter(array_map('trim', explode(',', $maybeList))));
            }
            if (is_string($maybeList) && strlen($maybeList)) {
                $tmp = json_decode($maybeList, true);
                if (json_last_error() === JSON_ERROR_NONE) $maybeList = $tmp;
            }
            if (is_array($maybeList)) {
                $out = [];
                foreach ($maybeList as $row) {
                    if (is_array($row) && isset($row[$childKey]) && $row[$childKey] !== '') {
                        $out[] = (string) $row[$childKey];
                    }
                }
                return $out;
            }
            return [];
        };

        $items = $data['data'] ?? [];
        $dataModel = new SecondOpinionDataModel($this->db);

        foreach ((array) $items as $item) {
            $panelId            = $item['panel_id'] ?? null;
            $biomarkersSelected = $item['biomarkers_selected'] ?? null;
            $exams              = $item['exams'] ?? null;

            $biomarkersIds = $pluckIds($biomarkersSelected, 'id');
            $recordsIds    = $pluckIds($exams, 'id');

            if (!empty($panelId)) {
                $dataModel->create([
                    'second_opinion_id' => $secondOpinionId,
                    'share_type'        => 'panel',
                    'panel_id'          => $panelId,
                    'biomarkers_id'     => null,
                    'records_id'        => null,
                ], true);
            }

            if (!empty($biomarkersIds)) {
                $dataModel->create([
                    'second_opinion_id' => $secondOpinionId,
                    'share_type'        => 'biomarkers',
                    'panel_id'          => $panelId,
                    'biomarkers_id'     => $biomarkersIds,
                    'records_id'        => null,
                ], true);
            }

            if (!empty($recordsIds)) {
                $dataModel->create([
                    'second_opinion_id' => $secondOpinionId,
                    'share_type'        => 'records',
                    'panel_id'          => $panelId,
                    'biomarkers_id'     => null,
                    'records_id'        => $recordsIds,
                ], true);
            }
        }

        // ====== INICIO: Enviar Notificación al Especialista ======
        try {
            // 1. Obtener nombre del paciente ($userId es el paciente)
            $user = $this->userModel->getById($userId);
            $userName = $user ? ($user['first_name'] . ' ' . $user['last_name']) : 'un usuario';

            // 2. Obtener tipo de solicitud (usar el nombre del servicio es más descriptivo)
            // El NotificationModel se encargará de traducir esto a ES si es necesario.
            $requestTypeRaw = $pricingRow['service_type'] ?? $typeRequest; 

            // 3. Crear payload
            $this->notificationModel->create([
                'user_id'         => $specialistId, // Notificación PARA el especialista
                'template_key'    => 'second_opinion_request_received',
                'rol'             => 'specialist',
                'module'          => 'second_opinion',
                'route'           => 'service_requests?id=' . $secondOpinionId, // Ruta para el panel del especialista
                'template_params' => [
                    'user_name'     => trim($userName),
                    'request_type'  => $requestTypeRaw 
                ]
            ]);
        } catch (\Throwable $e) {
            // No fallar la transacción principal si la notificación falla
            error_log("[SecondOpinionRequestsModel::createStandard] Notification failed: " . $e->getMessage());
        }
        // ====== FIN: Enviar Notificación ======


        $this->db->commit();
        return $secondOpinionId;
    } catch (\mysqli_sql_exception $e) {
        $this->db->rollback();
        throw $e;
    }
}



    public function updateStandard($id, $data, ?array $current = null)
    {
        $this->db->begin_transaction();
        try {
            $userId = $_SESSION['user_id'] ?? null;

            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId);

            // $tzManager = new TimezoneManager($this->db);
            // $tzManager->applyTimezone();

            $updatedAt = $env->getCurrentDatetime();

            if (!$current) {
                $current = $this->getById($id);
            }
            if (!$current) {
                throw new \mysqli_sql_exception("Record not found.");
            }

            // ===== Normalización de tipo (no-block) =====
            $typeRequestParam = array_key_exists('type_request', $data)
                ? $this->normalizeTypeRequest($data['type_request'])
                : ($current['type_request'] ?? null);

            if ($typeRequestParam === 'block') {
                throw new \mysqli_sql_exception("Invalid flow: use updateBlock() for type_request=block.");
            }

            $specialistId = $current['specialist_id'];

            // ===== Fechas =====
            $newStart = array_key_exists('request_date_to', $data)
                ? ($data['request_date_to'] ?? $current['request_date_to'])
                : $current['request_date_to'];

            $newEnd = array_key_exists('request_date_end', $data)
                ? ($data['request_date_end'] ?? ($current['request_date_end'] ?? null))
                : ($current['request_date_end'] ?? null);

            // ===== Campos de STANDARD =====
            $scopeRequestParam = array_key_exists('scope_request', $data) ? $this->normalizeScopeRequest($data['scope_request']) : null;
            $costRequestParam = array_key_exists('cost_request', $data) ? $this->normalizeCostRequest($data['cost_request']) : null;
            $pricingIdParam = array_key_exists('pricing_id', $data) ? ($data['pricing_id'] ?? null) : null;

            $newPricingId = array_key_exists('pricing_id', $data) ? ($data['pricing_id'] ?? $current['pricing_id']) : ($current['pricing_id'] ?? null);
            $newDurationReq = array_key_exists('duration_request', $data) ? ($data['duration_request'] ?? $current['duration_request']) : ($current['duration_request'] ?? null);

            // (Recomendado) Si cambia pricing_id, verificar que exista y que pertenezca al mismo specialist
            if ($pricingIdParam !== null) {
                if ($newPricingId === null) {
                    throw new \mysqli_sql_exception("Invalid pricing_id (null).");
                }
                $sqlPricing = "
                SELECT sp.pricing_id, sp.specialist_id
                FROM specialist_pricing sp
                WHERE sp.pricing_id = ? AND sp.deleted_at IS NULL
                LIMIT 1
            ";
                $stmtP = $this->db->prepare($sqlPricing);
                if (!$stmtP) {
                    throw new \mysqli_sql_exception("Error preparing pricing lookup: " . $this->db->error);
                }
                $stmtP->bind_param("s", $newPricingId);
                $stmtP->execute();
                $pricingRow = $stmtP->get_result()->fetch_assoc();
                $stmtP->close();

                if (!$pricingRow) {
                    throw new \mysqli_sql_exception("Service not found or unavailable.");
                }
                if (!empty($pricingRow['specialist_id']) && $pricingRow['specialist_id'] !== $specialistId) {
                    throw new \mysqli_sql_exception("The selected service does not belong to this specialist.");
                }
            }

            // Duración/buffer efectivos (sean del pricing nuevo o del actual)
            [$durationMin, $bufferMin] = $this->getEffectiveDurationAndBuffer(
                $newPricingId,
                $specialistId,
                $newDurationReq
            );
            $durationRequestStr = (string) $durationMin;

            // ===== VALIDAR AGENDA SOLO SI EL TIPO LO REQUIERE =====
            $this->validateScheduleIfNeeded(
                $typeRequestParam ?? '',  // puede venir null si no está en BD; fallback ''
                $specialistId,
                $newStart,                // puede ser null/'' en document_review
                $durationMin,
                $bufferMin,
                $id,                      // excludeId para evitar chocar con sí mismo
                $newEnd
            );

            // ===== Resto de campos =====
            $notesParam = array_key_exists('notes', $data) ? ($data['notes'] ?? '') : null;
            $sharedUntilParam = array_key_exists('shared_until', $data) ? ($data['shared_until'] ?? null) : null;
            $status = $data['status'] ?? $current['status'];

            // ===== UPDATE principal =====
            $stmt = $this->db->prepare("UPDATE {$this->table}
            SET status = ?,
                notes = ?,
                shared_until = ?,
                request_date_to = ?,
                request_date_end = ?,
                updated_at = ?,
                updated_by = ?,
                type_request      = COALESCE(?, type_request),
                scope_request     = COALESCE(?, scope_request),
                cost_request      = COALESCE(?, cost_request),
                pricing_id        = COALESCE(?, pricing_id),
                duration_request  = ?
            WHERE second_opinion_id = ?");
            if (!$stmt) {
                throw new \mysqli_sql_exception("Error preparing statement: " . $this->db->error);
            }

            // Orden y cantidad de parámetros deben coincidir con el SQL
            $stmt->bind_param(
                "sssssssssssss",
                $status,
                $notesParam,
                $sharedUntilParam,
                $newStart,
                $newEnd,
                $updatedAt,
                $userId,
                $typeRequestParam,
                $scopeRequestParam,
                $costRequestParam,
                $pricingIdParam,
                $durationRequestStr,
                $id
            );
            if (!$stmt->execute()) {
                throw new \mysqli_sql_exception("Execute failed: " . $stmt->error);
            }
            $stmt->close();

            // ===== Reemplazar detalles si viene "data" =====
            if (array_key_exists('data', $data)) {
                $del = $this->db->prepare("DELETE FROM second_opinion_data WHERE second_opinion_id = ?");
                if (!$del) {
                    throw new \mysqli_sql_exception("Error preparing delete: " . $this->db->error);
                }
                $del->bind_param("s", $id);
                if (!$del->execute()) {
                    throw new \mysqli_sql_exception("Delete failed: " . $del->error);
                }
                $del->close();

                $pluckIds = function ($maybeList, string $childKey = 'id'): array {
                    if (empty($maybeList))
                        return [];
                    if (is_array($maybeList) && isset($maybeList[0]) && is_string($maybeList[0])) {
                        return array_values(array_filter(array_map('strval', $maybeList)));
                    }
                    if (is_string($maybeList) && strpos($maybeList, ',') !== false) {
                        return array_values(array_filter(array_map('trim', explode(',', $maybeList))));
                    }
                    if (is_string($maybeList) && strlen($maybeList)) {
                        $tmp = json_decode($maybeList, true);
                        if (json_last_error() === JSON_ERROR_NONE)
                            $maybeList = $tmp;
                    }
                    if (is_array($maybeList)) {
                        $out = [];
                        foreach ($maybeList as $row) {
                            if (is_array($row) && isset($row[$childKey]) && $row[$childKey] !== '') {
                                $out[] = (string) $row[$childKey];
                            }
                        }
                        return $out;
                    }
                    return [];
                };

                $items = $data['data'] ?? [];
                $dataModel = new SecondOpinionDataModel($this->db);

                foreach ((array) $items as $item) {
                    $panelId = $item['panel_id'] ?? null;
                    $biomarkersSelected = $item['biomarkers_selected'] ?? null;
                    $exams = $item['exams'] ?? null;

                    $biomarkersIds = $pluckIds($biomarkersSelected, 'id');
                    $recordsIds = $pluckIds($exams, 'id');

                    if (!empty($panelId)) {
                        $dataModel->create([
                            'second_opinion_id' => $id,
                            'share_type' => 'panel',
                            'panel_id' => $panelId,
                            'biomarkers_id' => null,
                            'records_id' => null,
                        ], true);
                    }

                    if (!empty($biomarkersIds)) {
                        $dataModel->create([
                            'second_opinion_id' => $id,
                            'share_type' => 'biomarkers',
                            'panel_id' => $panelId,
                            'biomarkers_id' => $biomarkersIds,
                            'records_id' => null,
                        ], true);
                    }

                    if (!empty($recordsIds)) {
                        $dataModel->create([
                            'second_opinion_id' => $id,
                            'share_type' => 'records',
                            'panel_id' => $panelId,
                            'biomarkers_id' => null,
                            'records_id' => $recordsIds,
                        ], true);
                    }
                }
            }

            $this->db->commit();
            return true;
        } catch (\mysqli_sql_exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }


    /* --------------------- CRUD: BLOCK --------------------- */

    public function createBlock($data)
    {
        $this->db->begin_transaction();
        try {
            $userId = $_SESSION['user_id'] ?? null;

            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId);

            // No aplicar timezone de sesión aquí, usaremos la del especialista
            // $tzManager = new TimezoneManager($this->db);
            // $tzManager->applyTimezone();

            $createdAt = $env->getCurrentDatetime();
            $secondOpinionId = $this->generateUUIDv4();

            $typeRequest = 'block';
            $specialistTimezone = trim($data['timezone'] ?? ''); // Viene del perfil del especialista
            $specialistId = $data['specialist_id'];
            $requestedStartRaw = $data['request_date_to'] ?? null;
            $requestedEndRaw = $data['request_date_end'] ?? null;

            if (empty($specialistTimezone)) {
                throw new \mysqli_sql_exception("Specialist timezone is required for the block.");
            }
            if (empty($requestedStartRaw) || empty($requestedEndRaw)) {
                throw new \mysqli_sql_exception("Block requires request_date_to and request_date_end.");
            }

            // --- INICIO DE LA LÓGICA DE CONVERSIÓN CORRECTA ---
            $utcZone = new DateTimeZone('UTC');
            $specialistZone = new DateTimeZone($specialistTimezone);

            // Interpretar la fecha de inicio como si estuviera en la zona horaria del especialista
            $startDateTime = new DateTime($requestedStartRaw, $specialistZone);
            $requestedStartUTC = $startDateTime->format('Y-m-d H:i:s');
            // Repetir para la fecha de fin
            $endDateTime = new DateTime($requestedEndRaw, $specialistZone);
            $requestedEndUTC = $endDateTime->format('Y-m-d H:i:s');
            // --- FIN DE LA LÓGICA DE CONVERSIÓN ---

            if (strtotime($requestedEndUTC) <= strtotime($requestedStartUTC)) {
                throw new \mysqli_sql_exception("request_date_end must be greater than request_date_to.");
            }

            // La validación de agenda ahora usará las fechas UTC
            $this->validateScheduleIfNeeded(
                $typeRequest,
                $specialistId,
                $requestedStartUTC,
                0, // La duración no es relevante cuando se provee fecha de fin
                0, // El buffer no es relevante
                null,
                $requestedEndUTC
            );


            $status = 'pending';
            $notes = array_key_exists('notes', $data) ? ($data['notes'] ?? null) : null;

            $stmt = $this->db->prepare("INSERT INTO {$this->table}
            (second_opinion_id, user_id, specialist_id, status, type_request, notes, request_date_to, request_date_end, timezone, created_at, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if (!$stmt) {
                throw new \mysqli_sql_exception("Error preparing statement: " . $this->db->error);
            }

            $stmt->bind_param(
                "sssssssssss",
                $secondOpinionId,
                $userId, // El creador puede ser el mismo especialista
                $specialistId,
                $status,
                $typeRequest,
                $notes,
                $requestedStartUTC, // Guardar en UTC
                $requestedEndUTC,   // Guardar en UTC
                $specialistTimezone,// Guardar la zona horaria original
                $createdAt,
                $userId
            );
            if (!$stmt->execute()) {
                throw new \mysqli_sql_exception("Execute failed: " . $stmt->error);
            }
            $stmt->close();

            // En bloque, se ignora cualquier $data['data'] y NO se crea second_opinion_data

            $this->db->commit();
            return $secondOpinionId;
        } catch (\mysqli_sql_exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function updateBlock($id, $data)
    {
        $this->db->begin_transaction();
        try {
            $userId = $_SESSION['user_id'] ?? null;
            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId);
            $updatedAt = $env->getCurrentDatetime();

            $current = $this->getById($id);
            if (!$current)
                throw new \mysqli_sql_exception("Record not found.");

            $specialistId = $current['specialist_id'];
            // Usar la zona horaria que viene en la petición, o la que ya estaba guardada
            $specialistTimezone = $data['timezone'] ?? $current['timezone'];

            $requestedStartRaw = $data['request_date_to'] ?? $current['request_date_to'];
            $requestedEndRaw = $data['request_date_end'] ?? $current['request_date_end'];

            // --- LÓGICA DE CONVERSIÓN TAMBIÉN EN UPDATE ---
            $utcZone = new DateTimeZone('UTC');
            $specialistZone = new DateTimeZone($specialistTimezone);

            // Si la fecha que llega no es UTC (no tiene 'Z' o '+'), la convertimos
            $requestedStartUTC = $requestedStartRaw;
            if (strpos($requestedStartRaw, 'Z') === false && strpos($requestedStartRaw, '+') === false) {
                $startDateTime = new DateTime($requestedStartRaw, $specialistZone);
                $requestedStartUTC = $startDateTime->format('Y-m-d H:i:s');
            }

            $requestedEndUTC = $requestedEndRaw;
            if (strpos($requestedEndRaw, 'Z') === false && strpos($requestedEndRaw, '+') === false) {
                $endDateTime = new DateTime($requestedEndRaw, $specialistZone);
                $requestedEndUTC = $endDateTime->format('Y-m-d H:i:s');
            }
            // --- FIN LÓGICA DE CONVERSIÓN ---

            $this->validateScheduleIfNeeded('block', $specialistId, $requestedStartUTC, 0, 0, $id, $requestedEndUTC);


            $notesParam = array_key_exists('notes', $data) ? ($data['notes'] ?? null) : ($current['notes'] ?? null);
            $status = $data['status'] ?? $current['status'];

            $stmt = $this->db->prepare("UPDATE {$this->table}
            SET status = ?,
                notes = ?,
                request_date_to = ?,
                request_date_end = ?,
                timezone = ?,
                updated_at = ?,
                updated_by = ?
            WHERE second_opinion_id = ?");

            if (!$stmt)
                throw new \mysqli_sql_exception("Error preparing statement: " . $this->db->error);

            $stmt->bind_param(
                "ssssssss",
                $status,
                $notesParam,
                $requestedStartUTC,
                $requestedEndUTC,
                $specialistTimezone,
                $updatedAt,
                $userId,
                $id
            );
            if (!$stmt->execute())
                throw new \mysqli_sql_exception("Execute failed: " . $stmt->error);

            $stmt->close();
            $this->db->commit();
            return true;
        } catch (\Throwable $e) {
            $this->db->rollback();
            throw $e;
        }
    }


    /* ===================== Otros ===================== */

    public function countFreeThisMonthBySpecialist(string $specialistId): int
    {
        $start = date('Y-m-01 00:00:00');
        $end = date('Y-m-t 23:59:59');

        $sql = "
            SELECT COUNT(*) AS n
            FROM second_opinion_requests
            WHERE deleted_at IS NULL
              AND specialist_id = ?
              AND cost_request = 0
              AND status IN ('pending','awaiting_payment','upcoming','completed')
              AND created_at BETWEEN ? AND ?
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sss", $specialistId, $start, $end);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();

        return (int) ($res['n'] ?? 0);
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

            $stmt = $this->db->prepare("UPDATE {$this->table} SET deleted_at = ?, deleted_by = ? WHERE second_opinion_id = ?");
            $stmt->bind_param("sss", $deletedAt, $userId, $id);
            $stmt->execute();

            $this->db->commit();
            return true;
        } catch (\mysqli_sql_exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }




    /**
     * information = []
     * Cada item: { second_opinion_data_id, share_type, panel_id, panel_name, biomarkers:[], records:[] }
     * NOTA: recibe $requestUserId para poder filtrar por usuario cuando records_id es NULL
     * Regla especial: si type_request = 'block' => no hay second_opinion_data; devolver un único item con
     * share_type='block', fechas request_date_to / request_date_end y el resto en null / arrays vacíos.
     */
    /**
     * Construye la estructura de información para una solicitud de second opinion.
     * Soporta:
     * - type_request = 'block'  -> devuelve estructura especial de bloqueo
     * - scope_request = 'share_all' sin second_opinion_data -> arma TODOS los paneles del usuario
     * - Flujo normal: a partir de second_opinion_data
     */
    private function buildInformationForRequest(string $secondOpinionId, ?string $requestUserId = null): array
    {
        // 0) Leer solicitud (incluyendo user_id)
        $stmt = $this->db->prepare("
        SELECT type_request, request_date_to, request_date_end, scope_request, user_id
        FROM {$this->table}
        WHERE second_opinion_id = ? AND deleted_at IS NULL
        LIMIT 1
    ");
        if (!$stmt) {
            error_log("[SecondOpinionRequestsModel::buildInformationForRequest] Prepare failed: " . $this->db->error);
            throw new \mysqli_sql_exception("Prepare failed: " . $this->db->error);
        }
        $stmt->bind_param("s", $secondOpinionId);
        $stmt->execute();
        $req = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$req) {
            return [];
        }

        // Normalizar scope_request
        $scope = isset($req['scope_request']) ? strtolower(trim((string) $req['scope_request'])) : '';
        $isShareAll = ($scope === 'share_all');

        // Resolver el user_id SIN sesión: prioridad parámetro > solicitud > null
        $effectiveUserId = $requestUserId ?: ($req['user_id'] ?? null);

        // Obtener sex_biological del usuario
        $userSex = 'M';
        if (!empty($effectiveUserId)) {
            $stmtSex = $this->db->prepare("
            SELECT sex_biological
            FROM users
            WHERE user_id = ? AND deleted_at IS NULL
            LIMIT 1
        ");
            if (!$stmtSex) {
                error_log("[SecondOpinionRequestsModel::buildInformationForRequest] Prepare(users) failed: " . $this->db->error);
                throw new \mysqli_sql_exception("Prepare failed: " . $this->db->error);
            }
            $stmtSex->bind_param("s", $effectiveUserId);
            $stmtSex->execute();
            $rowUser = $stmtSex->get_result()->fetch_assoc();
            $stmtSex->close();

            if ($rowUser && isset($rowUser['sex_biological'])) {
                $sx = trim((string) $rowUser['sex_biological']);
                if ($sx === 'm' || $sx === 'f') {
                    $userSex = $sx;
                } else {
                    $userSex = 'M'; // ambos por defecto
                }
            }
        }

        // 1) Bloqueo
        if (isset($req['type_request']) && strtolower((string) $req['type_request']) === 'block') {
            return [
                [
                    'second_opinion_data_id' => null,
                    'share_type' => 'block',
                    'panel_id' => null,
                    'panel_name' => null,
                    'display_name' => null,
                    'biomarkers' => [],
                    'records' => [],
                    'request_date_to' => $req['request_date_to'] ?? null,
                    'request_date_end' => $req['request_date_end'] ?? null,
                ]
            ];
        }

        // 2) Flujo normal (no-block)
        $dataModel = new SecondOpinionDataModel($this->db);
        $rows = $dataModel->listBySecondOpinionId($secondOpinionId);

        // share_all SIN second_opinion_data → traer TODO por usuario
        if ($isShareAll && empty($rows)) {
            $information = [];
            $panels = $this->listAllPanelsForOutput();

            foreach ($panels as $p) {
                $panelId = $p['panel_id'];
                $panelName = $p['panel_name'];
                $dispName = $p['display_name'];

                $records = [];
                if (!empty($effectiveUserId)) {
                    $records = $this->fetchPanelRecords(
                        $panelId,
                        null,
                        $effectiveUserId,
                        [],
                        true
                    );
                }

                // Biomarcadores del panel filtrados por sexo
                $bioIds = $this->getBiomarkerIdsByPanelId($panelId, $userSex);
                $bioMap = $this->getBiomarkersInfoMap($bioIds, $userSex);
                $bioObjs = [];
                foreach ($bioIds as $bid) {
                    $bioObjs[] = $bioMap[$bid] ?? ['biomarker_id' => $bid];
                }

                // Detectar PK real para record_id
                $existingCols = $this->getExistingColumns($panelName);
                $pkDetected = !empty($existingCols) ? $this->detectPanelPk($panelName, $existingCols) : null;

                $outRecs = [];
                foreach ($records as $rec) {
                    $recordId = ($pkDetected && isset($rec[$pkDetected])) ? $rec[$pkDetected] : null;
                    $outRecs[] = array_merge(['record_id' => $recordId], $rec);
                }

                $information[] = [
                    'second_opinion_data_id' => null,
                    'panel_id' => $panelId,
                    'panel_name' => $panelName,
                    'display_name' => $dispName,
                    'biomarkers' => $bioObjs,
                    'records' => $outRecs,
                ];
            }

            return $information;
        }

        // Flujo con second_opinion_data
        $allBiomarkers = [];
        foreach ($rows as $r) {
            $allBiomarkers = array_merge($allBiomarkers, self::decodeJsonList($r['biomarkers_id'] ?? null));
        }
        $bioMap = $this->getBiomarkersInfoMap($allBiomarkers, $userSex);

        $information = [];

        foreach ($rows as $r) {
            try {
                $resolved = $dataModel->getById($r['second_opinion_data_id']);

                $panelId = $resolved['panel_id'] ?? null;
                $panelData = $this->getPanelNameById($panelId);
                $panelName = is_array($panelData) ? ($panelData['panel_name'] ?? null) : ($panelData ?: null);
                $dispName = is_array($panelData) ? ($panelData['display_name'] ?? null) : null;

                // Biomarcadores filtrados por sexo
                $bioIds = self::decodeJsonList($resolved['biomarkers_id'] ?? null);
                $bioIds = array_values(array_intersect($bioIds, array_keys($bioMap)));
                $bioObjs = [];
                $bioColsForSelect = [];
                foreach ($bioIds as $bid) {
                    if (isset($bioMap[$bid])) {
                        $bioObjs[] = $bioMap[$bid];
                        $col = trim((string) ($bioMap[$bid]['name_db'] ?? ''));
                        if ($col !== '') {
                            $bioColsForSelect[] = $col;
                        }
                    } else {
                        $bioObjs[] = ['biomarker_id' => $bid];
                    }
                }
                $bioColsForSelect = array_values(array_unique($bioColsForSelect));

                // Records
                $records = [];
                $pk = $resolved['resolved_pk'] ?? null;
                $rowsResolved = $resolved['resolved_rows'] ?? [];

                if (empty($rowsResolved)) {
                    $rowsResolved = $this->fetchPanelRecords(
                        $panelId,
                        $resolved['records_id'] ?? null,
                        $effectiveUserId,
                        $bioColsForSelect,
                        $isShareAll
                    );
                    if (empty($pk) && $panelName) {
                        $pk = $panelName . '_id';
                    }
                }

                foreach ($rowsResolved as $rec) {
                    $recOut = $rec;
                    $recordId = $pk && isset($rec[$pk]) ? $rec[$pk] : null;
                    $records[] = array_merge(['record_id' => $recordId], $recOut);
                }

                $information[] = [
                    'second_opinion_data_id' => $resolved['second_opinion_data_id'],
                    'panel_id' => $panelId,
                    'panel_name' => $panelName,
                    'display_name' => $dispName,
                    'biomarkers' => $bioObjs,
                    'records' => $records,
                ];
            } catch (\Throwable $ex) {
                error_log("[SecondOpinionRequestsModel::buildInformationForRequest] "
                    . "Error data_id=" . ($r['second_opinion_data_id'] ?? 'NULL') . " | " . $ex->getMessage());
                $information[] = [
                    'second_opinion_data_id' => $r['second_opinion_data_id'] ?? null,
                    'error' => true,
                    'message' => $ex->getMessage()
                ];
            }
        }

        return $information;
    }







}
