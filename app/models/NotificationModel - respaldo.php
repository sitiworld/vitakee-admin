<?php

require_once __DIR__ . '/../config/Database.php';

class NotificationModel
{
    private $db;
    private $table = "notifications";

    public $notification_id;
    public $id_panel;
    public $id_record;
    public $id_biomarker;
    public $status;
    public $no_alert_user;
    public $no_alert_admin;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }
private function buildPanelBiomarkerMappings(): array
{
    try {
        $idioma = $_SESSION['idioma'] ?? 'EN';

        // Obtener todos los paneles válidos
        $queryPanels = "SELECT panel_id, panel_name FROM test_panels WHERE deleted_at IS NULL ORDER BY display_name";
        $resultPanels = $this->db->query($queryPanels);
        if (!$resultPanels) {
            throw new mysqli_sql_exception("Error al obtener paneles: " . $this->db->error);
        }

        $table_map  = [];
        $date_fields = [];
        $time_fields = [];
        $id_fields   = [];

        while ($panel = $resultPanels->fetch_assoc()) {
            $panel_id   = $panel['panel_id'];
            $panel_name = strtolower(trim($panel['panel_name']));
            $table_map[$panel_id] = $panel_name;

            // Detectar campos de fecha/hora/id en la tabla correspondiente
            $resultDesc = $this->db->query("DESCRIBE `{$panel_name}`");
            if (!$resultDesc) {
                throw new mysqli_sql_exception("Error al obtener estructura de tabla {$panel_name}: " . $this->db->error);
            }
            while ($col = $resultDesc->fetch_assoc()) {
                if (preg_match('/_date$/', $col['Field'])) {
                    $date_fields[$panel_id] = $col['Field'];
                }
                if (preg_match('/_time$/', $col['Field'])) {
                    $time_fields[$panel_id] = $col['Field'];
                }
                if (preg_match('/_id$/', $col['Field']) && !isset($id_fields[$panel_id])) {
                    $id_fields[$panel_id] = $col['Field']; // guarda solo el primero que coincide
                }
            }
        }

        // Traer todos los biomarcadores usando name, name_es y name_db
        $queryBiomarkers = "SELECT biomarker_id, panel_id, name, name_es, name_db FROM biomarkers ORDER BY name";
        $resultBiomarkers = $this->db->query($queryBiomarkers);
        if (!$resultBiomarkers) {
            throw new mysqli_sql_exception("Error al obtener biomarcadores: " . $this->db->error);
        }

        $field_map = [];

        while ($biomarker = $resultBiomarkers->fetch_assoc()) {
            $panel_id = $biomarker['panel_id'];

            // Todos en minúsculas y recortados
            $key_en  = strtolower(trim($biomarker['name'] ?? ''));
            $key_es  = strtolower(trim($biomarker['name_es'] ?? ''));
            $key_db  = strtolower(trim($biomarker['name_db'] ?? ''));

            // Si no hay name_db, intenta derivarlo de name (opcional pero útil)
            if ($key_db === '') {
                // Reemplaza espacios y guiones por guion_bajo, elimina paréntesis
                $key_db = preg_replace(['/[\s\-]+/','/[()]/'], ['_',''], $key_en);
            }

            if ($key_en !== '') {
                $field_map[$panel_id][$key_en] = [$key_db];
            }
            if ($key_es !== '') {
                $field_map[$panel_id][$key_es] = [$key_db];
            }

            // (Opcional) variantes normalizadas para robustez de lookup:
            // 'non-hdl cholesterol' -> 'non_hdl_cholesterol'
            $norm_en = preg_replace(['/[\s\-]+/','/[()]/'], ['_',''], $key_en);
            if ($norm_en !== '' && $norm_en !== $key_en) {
                $field_map[$panel_id][$norm_en] = [$key_db];
            }
            $norm_es = preg_replace(['/[\s\-]+/','/[()]/'], ['_',''], $key_es);
            if ($norm_es !== '' && $norm_es !== $key_es) {
                $field_map[$panel_id][$norm_es] = [$key_db];
            }
        }

        return [
            'table_map'   => $table_map,
            'id_fields'   => $id_fields,
            'date_fields' => $date_fields,
            'time_fields' => $time_fields,
            'field_map'   => $field_map
        ];

    } catch (mysqli_sql_exception $e) {
        return [
            'table_map'   => [],
            'id_fields'   => [],
            'date_fields' => [],
            'time_fields' => [],
            'field_map'   => [],
            'error'       => $e->getMessage()
        ];
    }
}


    public function getAll()
    {
        try {
            $query = "SELECT * FROM {$this->table} ORDER BY notification_id  DESC";
            $result = $this->db->query($query);
            if (!$result) {
                throw new mysqli_sql_exception("Error fetching notifications: " . $this->db->error);
            }

            $items = [];
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }

            return $items;
        } catch (mysqli_sql_exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
public function countAlertsUser($user_id, $flag = 0)
{
    try {
        if (is_array($flag)) {
            // Sanitizar valores del array
            $sanitized_flags = implode(',', array_map('intval', $flag));
            $query = "SELECT COUNT(*) as total FROM {$this->table} 
                      WHERE user_id = ? AND new IN ($sanitized_flags)";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("s", $user_id);
        } else {
            $query = "SELECT COUNT(*) as total FROM {$this->table} 
                      WHERE user_id = ? AND new = ?";
            $stmt = $this->db->prepare($query);
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
        if (is_array($flag)) {
            // Sanitizar los valores del array para construir IN ()
            $sanitized_flags = implode(',', array_map('intval', $flag));
            $query = "SELECT COUNT(*) as total FROM {$this->table} 
                      WHERE user_id = ? AND no_alert_user IN ($sanitized_flags)";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("s", $user_id);
        } else {
            $flag = (int)$flag;
            $query = "SELECT COUNT(*) as total FROM {$this->table} 
                      WHERE user_id = ? AND no_alert_user = ?";
            $stmt = $this->db->prepare($query);
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



    public function getById($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE notification_id  = ?");
            if (!$stmt) {
                throw new mysqli_sql_exception("Error preparing statement: " . $this->db->error);
            }

            $stmt->bind_param("s", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            return $result->fetch_assoc() ?: ['status' => 'error', 'message' => 'Notification not found.'];
        } catch (mysqli_sql_exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    public function exists($user_id, $record_id, $biomarker_id)
{
    try {
        $stmt = $this->db->prepare("
            SELECT notification_id  FROM {$this->table}
            WHERE user_id = ? AND id_record = ? AND id_biomarker = ?
            LIMIT 1
        ");

        if (!$stmt) {
            throw new mysqli_sql_exception("Error al preparar la consulta: " . $this->db->error);
        }

        $stmt->bind_param("sss", $user_id, $record_id, $biomarker_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();

        return $exists;
    } catch (mysqli_sql_exception $e) {
        // Puedes loguear el error si lo deseas
        return false;
    }
}
public function getByUserId($user_id, $limit = 20, $offset = 0)
{
    try {
        $idioma = $_SESSION['idioma'] ?? 'EN';

        // Obtener datos del usuario
        $stmt = $this->db->prepare("SELECT sex_biological, birthday FROM users WHERE user_id = ?");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $user_data = $stmt->get_result()->fetch_assoc();

        if (!$user_data)
            return ['value' => false, 'message' => 'User not found'];

        $birthdate = new DateTime($user_data['birthday']);
        $today = new DateTime();
        $age = $today->diff($birthdate)->y;
        $sex_biological = strtolower(trim($user_data['sex_biological']));

        // Obtener mappings dinámicos
        $mappings = $this->buildPanelBiomarkerMappings();
        if (isset($mappings['error'])) {
            throw new Exception("Error al obtener mappings: " . $mappings['error']);
        }

        $table_map = $mappings['table_map'];
        $date_fields = $mappings['date_fields'];
        $field_map = $mappings['field_map'];
        $id_fields = $mappings['id_fields'];

        // Obtener biomarcadores
        $stmt = $this->db->prepare("SELECT biomarker_id, panel_id, name, name_es, reference_min, reference_max FROM biomarkers");
        $stmt->execute();
        $result = $stmt->get_result();
        $biomarkers = [];
        while ($row = $result->fetch_assoc()) {
            $biomarkers[$row['biomarker_id']] = $row;
        }

        // Obtener nombres de los paneles
        $stmt = $this->db->prepare("SELECT panel_id, display_name, display_name_es FROM test_panels");
        $stmt->execute();
        $resPanels = $stmt->get_result();
        $panelNames = [];
        while ($row = $resPanels->fetch_assoc()) {
            $panelNames[$row['panel_id']] = $row;
        }

        // Obtener notificaciones del usuario con paginación (dinámicamente)
        $unions = [];
        foreach ($table_map as $pid => $table) {
            $id_field = $id_fields[$pid] ?? 'id';
            $unions[] = "SELECT {$id_field} FROM {$table} WHERE user_id = ?";
        }
        $union_query = implode(" UNION ", $unions);

        $query = "SELECT * FROM notifications 
                  WHERE no_alert_user = 0 AND user_id = ? AND id_record IN ( $union_query )
                  ORDER BY notification_id DESC
                  LIMIT ? OFFSET ?";

        $param_types = str_repeat("s", count($table_map) + 1) . "ii";
        $params = array_merge([$user_id], array_fill(0, count($table_map), $user_id), [$limit, $offset]);

        $stmt = $this->db->prepare($query);
        $stmt->bind_param($param_types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        $alerts = [];

        while ($row = $result->fetch_assoc()) {
            $notification_id = $row['notification_id'];
            $panel_id = $row['id_panel'];
            $biomarker_id = $row['id_biomarker'];
            $record_id = $row['id_record'];

            if (!isset($biomarkers[$biomarker_id])) continue;
            $bm = $biomarkers[$biomarker_id];
            $bm_name = strtolower($bm['name']);

            if (!isset($table_map[$panel_id]) || !isset($field_map[$panel_id][$bm_name])) continue;

            $table = $table_map[$panel_id];
            $id_field = $id_fields[$panel_id] ?? 'id';
            $date_field = $date_fields[$panel_id];
            $field_key = $field_map[$panel_id][$bm_name][0];

            $stmt_value = $this->db->prepare("SELECT `$field_key` AS value, `$date_field` AS date FROM `$table` WHERE `$id_field` = ?");
            $stmt_value->bind_param("s", $record_id); // ← ahora string
            $stmt_value->execute();
            $value_result = $stmt_value->get_result()->fetch_assoc();

            if (!$value_result || !isset($value_result['value'])) continue;

            $value = floatval($value_result['value']);
            $date = $value_result['date'] ?? null;

            $status = null;
            $status_original = $row['status'] ?? null;

            if ($bm_name === 'body age') {
                if ($value > $age) {
                    $status = ($idioma === 'ES') ? 'Alto' : 'High';
                }
            } elseif ($value < (float)$bm['reference_min']) {
                $status = ($idioma === 'ES') ? 'Bajo' : 'Low';
            } elseif ($value > (float)$bm['reference_max']) {
                $status = ($idioma === 'ES') ? 'Alto' : 'High';
            }

            if ($status) {
                $alerts[] = [
                    'notification_id' => $notification_id,
                    'biomarker' => ($idioma === 'ES') ? $bm['name_es'] : $bm['name'],
                    'value' => $value,
                    'status' => $status,
                    'status_original' => $status_original,
                    'date' => $date,
                    'panel' => $panel_id,
                    'panel_name' => ($idioma === 'ES')
                        ? ($panelNames[$panel_id]['display_name_es'] ?? '')
                        : ($panelNames[$panel_id]['display_name'] ?? ''),
                    'biomarker_key' => $field_key,
                    'record_id' => $record_id
                ];
            }
        }

        return $alerts;

    } catch (Exception $e) {
        return ['value' => false, 'message' => $e->getMessage()];
    }
}


private function getUserAlertsByFlag($user_id, $no_alert_flag, $limit = 20, $offset = 0)
{
    try {
        $idioma = $_SESSION['idioma'] ?? 'EN';

        /* 1) Datos del usuario */
        $stmt = $this->db->prepare("SELECT sex_biological, birthday FROM users WHERE user_id = ?");
        if (!$stmt) throw new Exception("Prepare failed (users): " . $this->db->error);
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $user_data = $stmt->get_result()->fetch_assoc();

        if (!$user_data) {
            return ['value' => false, 'message' => 'User not found'];
        }

        $age = null;
        if (!empty($user_data['birthday'])) {
            $birthdate = new DateTime($user_data['birthday']);
            $today     = new DateTime();
            $age       = $today->diff($birthdate)->y;
        }
        $sex_biological = strtolower(trim($user_data['sex_biological'] ?? ''));

        /* 2) Mappings automáticos */
        $mappings = $this->buildPanelBiomarkerMappings();
        if (isset($mappings['error'])) {
            throw new Exception("Error en mappings: " . $mappings['error']);
        }
        $table_map   = $mappings['table_map']   ?? [];
        $date_fields = $mappings['date_fields'] ?? [];
        $field_map   = $mappings['field_map']   ?? [];
        $id_fields   = $mappings['id_fields']   ?? [];

        if (empty($table_map)) {
            return [];
        }

        /* 3) Biomarcadores (incluye name_db) */
        $stmt = $this->db->prepare("
            SELECT biomarker_id, panel_id, name, name_es, name_db, reference_min, reference_max
            FROM biomarkers
        ");
        if (!$stmt) throw new Exception("Prepare failed (biomarkers): " . $this->db->error);
        $stmt->execute();
        $resBM = $stmt->get_result();
        $biomarkers = [];
        while ($row = $resBM->fetch_assoc()) {
            $biomarkers[$row['biomarker_id']] = $row;
        }

        /* 4) Panel names */
        $stmt = $this->db->prepare("SELECT panel_id, display_name, display_name_es FROM test_panels");
        if (!$stmt) throw new Exception("Prepare failed (test_panels): " . $this->db->error);
        $stmt->execute();
        $resPanels = $stmt->get_result();
        $panelNames = [];
        while ($row = $resPanels->fetch_assoc()) {
            $panelNames[$row['panel_id']] = $row;
        }

        /* 5) UNION dinámico para validar que id_record pertenezca al usuario */
        $unionParts = [];
        foreach ($table_map as $pid => $table) {
            $id_field = $id_fields[$pid] ?? 'id';
            $unionParts[] = "SELECT `{$id_field}` FROM `{$table}` WHERE `user_id` = ?";
        }
        $union_query = implode(" UNION ", $unionParts);
        $numUserIds  = count($table_map);

        /* 6) Filtro por flags (placeholders; soporta array vacío) */
        $where_alert = '';
        $flag_placeholders = '';
        $flag_is_array = is_array($no_alert_flag);
        if ($flag_is_array) {
            $clean_flags = array_values(array_map('intval', $no_alert_flag));
            if (count($clean_flags) === 0) {
                return [];
            }
            $flag_placeholders = implode(',', array_fill(0, count($clean_flags), '?'));
            $where_alert = "no_alert_user IN ($flag_placeholders)";
        } else {
            $where_alert = "no_alert_user = ?";
        }

        /* 7) Query base con LIMIT/OFFSET (por lote) */
        $query = "
            SELECT *
            FROM `notifications`
            WHERE {$where_alert}
              AND `user_id` = ?
              AND `id_record` IN ( {$union_query} )
            ORDER BY `notification_id` DESC
            LIMIT ? OFFSET ?
        ";

        /* ===== Overfetch por lotes ===== */
        $target = max(0, (int)$limit);
        if ($target === 0) return [];

        $batch  = max($target * 3, 60);
        $cursor = max(0, (int)$offset);

        $alerts = [];

        // Helpers para la matriz ALB/CRE (renal)
        $isRenalPanel = function(array $panelNames, string $panel_id): bool {
            $p = $panelNames[$panel_id] ?? null;
            if (!$p) return false;
            $dn    = strtolower($p['display_name']    ?? '');
            $dn_es = strtolower($p['display_name_es'] ?? '');
            return (strpos($dn, 'renal') !== false) || (strpos($dn_es, 'renal') !== false);
        };
        $normalizePlus = function($v) {
            if ($v === null) return null;
            $t = trim((string)$v);
            if ($t === '') return null;
            $u = strtoupper($t);
            if ($u === 'N' || $u === 'NEG' || $u === 'NEGATIVE' || $t === '0') return 'N';
            if (preg_match('/^([123])\+$/', $t, $m)) return $m[1].'+';
            if (is_numeric($t)) {
                $n = (int)$t;
                if ($n <= 0) return 'N';
                if ($n === 1) return '1+';
                if ($n === 2) return '2+';
                if ($n >= 3) return '3+';
            }
            return null; // desconocido
        };
        $findFieldKey = function(array $panelFields, array $cands) {
            foreach ($cands as $c) {
                $k1 = strtolower($c);
                $k2 = preg_replace(['/[\s\-]+/','/[()]/'], ['_',''], $k1);
                if (isset($panelFields[$k1])) return $panelFields[$k1][0];
                if (isset($panelFields[$k2])) return $panelFields[$k2][0];
            }
            return null;
        };
        $applyAlbCreMatrix = function($albLevel, $creLevel, string $idioma): ?string {
            if ($albLevel === null || $creLevel === null) return null;
            // Si ALB = N → Normal
            if ($albLevel === 'N') {
                return ($idioma === 'ES') ? 'Normal' : 'Normal';
            }
            // ALB 1+: CRE 1+ => A, CRE 2+ => A, CRE 3+ => N
            if ($albLevel === '1+') {
                if ($creLevel === '1+' || $creLevel === '2+') {
                    return ($idioma === 'ES') ? 'Alto' : 'High';
                }
                if ($creLevel === '3+') {
                    return ($idioma === 'ES') ? 'Normal' : 'Normal';
                }
                return null;
            }
            // ALB 2+: cualquier CRE => A
            if ($albLevel === '2+' || $albLevel === '3+') {
                if (in_array($creLevel, ['1+','2+','3+'], true)) {
                    return ($idioma === 'ES') ? 'Alto' : 'High';
                }
                return null;
            }
            return null;
        };

        do {
            // Construye tipos/params dinámica por lote
            if ($flag_is_array) {
                $param_types = str_repeat('i', count($clean_flags)) . "s" . str_repeat("s", $numUserIds) . "ii";
                $params = array_merge(
                    $clean_flags,
                    [$user_id],
                    array_fill(0, $numUserIds, $user_id),
                    [(int)$batch, (int)$cursor]
                );
            } else {
                $param_types = "is" . str_repeat("s", $numUserIds) . "ii";
                $params = array_merge(
                    [(int)$no_alert_flag, $user_id],
                    array_fill(0, $numUserIds, $user_id),
                    [(int)$batch, (int)$cursor]
                );
            }

            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed (notifications batch): " . $this->db->error);
            }
            $stmt->bind_param($param_types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
            $fetchedRows = $result->num_rows;

            while ($row = $result->fetch_assoc()) {
                $notification_id = $row['notification_id'];
                $panel_id        = $row['id_panel'];
                $biomarker_id    = $row['id_biomarker'];
                $record_id       = $row['id_record'];

                if (!isset($biomarkers[$biomarker_id])) {
                    continue;
                }
                $bm = $biomarkers[$biomarker_id];

                // Claves posibles (minúsculas)
                $bm_key_en  = strtolower(trim($bm['name'] ?? ''));
                $bm_key_es  = strtolower(trim($bm['name_es'] ?? ''));
                $bm_key_db  = strtolower(trim($bm['name_db'] ?? ''));
                $bm_norm_en = $bm_key_en !== '' ? preg_replace(['/[\s\-]+/','/[()]/'], ['_',''], $bm_key_en) : '';
                $bm_norm_es = $bm_key_es !== '' ? preg_replace(['/[\s\-]+/','/[()]/'], ['_',''], $bm_key_es) : '';

                if (!isset($table_map[$panel_id])) {
                    continue;
                }
                $table      = $table_map[$panel_id];
                $id_field   = $id_fields[$panel_id]  ?? 'id';
                $date_field = $date_fields[$panel_id] ?? null;

                // Resolver field_key
                $panelFields = $field_map[$panel_id] ?? [];
                $field_key =
                    ($bm_key_en  !== '' && isset($panelFields[$bm_key_en]))  ? $panelFields[$bm_key_en][0]  : null;
                if (!$field_key && $bm_key_es !== '') {
                    $field_key = isset($panelFields[$bm_key_es]) ? $panelFields[$bm_key_es][0] : null;
                }
                if (!$field_key && $bm_norm_en !== '') {
                    $field_key = isset($panelFields[$bm_norm_en]) ? $panelFields[$bm_norm_en][0] : null;
                }
                if (!$field_key && $bm_norm_es !== '') {
                    $field_key = isset($panelFields[$bm_norm_es]) ? $panelFields[$bm_norm_es][0] : null;
                }
                if (!$field_key && $bm_key_db !== '') {
                    if (isset($panelFields[$bm_key_db])) {
                        $field_key = $panelFields[$bm_key_db][0];
                    }
                }
                if (!$field_key) {
                    continue;
                }

                // Leer valor principal (y fecha)
                $date_sql = $date_field ? ", `{$date_field}` AS `date`" : ", NULL AS `date`";
                $sqlValue = "SELECT `{$field_key}` AS `value` {$date_sql} FROM `{$table}` WHERE `{$id_field}` = ? LIMIT 1";
                $stmt_value = $this->db->prepare($sqlValue);
                if (!$stmt_value) {
                    continue;
                }
                $stmt_value->bind_param("s", $record_id);
                $stmt_value->execute();
                $value_result = $stmt_value->get_result()->fetch_assoc();
                if (!$value_result || !isset($value_result['value'])) {
                    continue;
                }
                $value = is_numeric($value_result['value']) ? (float)$value_result['value'] : $value_result['value'];
                $date  = $value_result['date'] ?? null;

                /* ===== Status ===== */
                $status          = null;
                $status_original = $row['status'] ?? null;
                $ref_min = isset($bm['reference_min']) ? (float)$bm['reference_min'] : null;
                $ref_max = isset($bm['reference_max']) ? (float)$bm['reference_max'] : null;

                // Caso especial BODY AGE (igual que antes)
                $bm_is_body_age = (strtolower(trim($bm['name_db'] ?? '')) === 'body_age')
                               || (strtolower(trim($bm['name'] ?? '')) === 'body age')
                               || (strtolower(trim($bm['name_es'] ?? '')) === 'edad corporal');

                if ($bm_is_body_age && is_numeric($value) && $age !== null) {
                    if ((float)$value > $age) {
                        $status = ($idioma === 'ES') ? 'Alto' : 'High';
                    } else {
                        $status = ($idioma === 'ES') ? 'Normal' : 'Normal';
                    }
                }

                // ===== Matriz ALB/CRE para Renal Function (si no se determinó antes) =====
                if ($status === null && $isRenalPanel($panelNames, $panel_id)) {
                    // Buscar campos ALB / CRE en el mismo panel
                    $alb_field = $findFieldKey($panelFields, ['albumin','alb','albúmina','albumina','microalbumin','microalbumina']);
                    $cre_field = $findFieldKey($panelFields, ['creatinine','cre','creatinina']);

                    if ($alb_field && $cre_field) {
                        $sqlPair = "SELECT `{$alb_field}` AS alb, `{$cre_field}` AS cre FROM `{$table}` WHERE `{$id_field}` = ? LIMIT 1";
                        $stmt_pair = $this->db->prepare($sqlPair);
                        if ($stmt_pair) {
                            $stmt_pair->bind_param("s", $record_id);
                            $stmt_pair->execute();
                            $pair = $stmt_pair->get_result()->fetch_assoc();

                            if ($pair) {
                                $albLevel = $this->sanitizePlusValue($pair['alb'] ?? null, $normalize = $normalizePlus);
                                $creLevel = $this->sanitizePlusValue($pair['cre'] ?? null, $normalize = $normalizePlus);

                                // Aplica matriz solo si ambos niveles son reconocibles (N,1+,2+,3+)
                                if ($albLevel !== null && $creLevel !== null) {
                                    $statusFromMatrix = $applyAlbCreMatrix($albLevel, $creLevel, $idioma);
                                    if ($statusFromMatrix !== null) {
                                        $status = $statusFromMatrix;
                                    }
                                }
                            }
                        }
                    }
                }

                // Si aún no hay status, usa referencia numérica
                if ($status === null && is_numeric($value)) {
                    $v = (float)$value;
                    if ($ref_min !== null && $v < $ref_min) {
                        $status = ($idioma === 'ES') ? 'Bajo' : 'Low';
                    } elseif ($ref_max !== null && $v > $ref_max) {
                        $status = ($idioma === 'ES') ? 'Alto' : 'High';
                    } else {
                        // Si cae dentro del rango, no la contamos como alerta
                        $status = null;
                    }
                }

                // Agregar solo si hay alerta efectiva (status no-null y distinto de “silencioso”)
                if ($status) {
                    $alerts[] = [
                        'notification_id' => $notification_id,
                        'biomarker'       => ($idioma === 'ES') ? ($bm['name_es'] ?? $bm['name']) : ($bm['name'] ?? ''),
                        'value'           => is_numeric($value) ? (float)$value : $value,
                        'status'          => $status,
                        'status_original' => $status_original,
                        'date'            => $date,
                        'panel'           => $panel_id,
                        'panel_name'      => ($idioma === 'ES')
                            ? ($panelNames[$panel_id]['display_name_es'] ?? '')
                            : ($panelNames[$panel_id]['display_name'] ?? ''),
                        'biomarker_key'   => $field_key,
                        'record_id'       => $record_id,
                        'status_read'     => (int)($row['no_alert_user'] ?? 0)
                    ];

                    if (count($alerts) >= $target) {
                        break;
                    }
                }
            }

            if (count($alerts) >= $target) break;

            $cursor += $batch;
            if ($fetchedRows < $batch) break;

        } while (true);

        if (count($alerts) > $target) {
            $alerts = array_slice($alerts, 0, $target);
        }

        return $alerts;

    } catch (Exception $e) {
        return ['value' => false, 'message' => $e->getMessage()];
    }
}

/**
 * Normaliza un valor semicuantitativo a {N, 1+, 2+, 3+} o null.
 * Permite inyectar el normalizador para pruebas; por defecto usa el closure local.
 */
private function sanitizePlusValue($raw, $normalize = null)
{
    if ($normalize && is_callable($normalize)) {
        return $normalize($raw);
    }
    $t = trim((string)$raw);
    if ($t === '') return null;
    $u = strtoupper($t);
    if ($u === 'N' || $u === 'NEG' || $u === 'NEGATIVE' || $t === '0') return 'N';
    if (preg_match('/^([123])\+$/', $t, $m)) return $m[1].'+';
    if (is_numeric($t)) {
        $n = (int)$t;
        if ($n <= 0) return 'N';
        if ($n === 1) return '1+';
        if ($n === 2) return '2+';
        if ($n >= 3) return '3+';
    }
    return null;
}






private function getAllUserAlertsByFlag($user_id, $no_alert_flag)
{
    try {
        $idioma = $_SESSION['idioma'] ?? 'EN';

        // 1) Datos del usuario
        $stmt = $this->db->prepare("SELECT sex_biological, birthday FROM users WHERE user_id = ?");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $user_data = $stmt->get_result()->fetch_assoc();

        if (!$user_data) {
            return ['value' => false, 'message' => 'User not found'];
        }

        // Edad
        $age = null;
        if (!empty($user_data['birthday'])) {
            $birthdate = new DateTime($user_data['birthday']);
            $today     = new DateTime();
            $age       = $today->diff($birthdate)->y;
        }
        $sex_biological = strtolower(trim($user_data['sex_biological'] ?? ''));

        // 2) Mappings
        $mappings = $this->buildPanelBiomarkerMappings();
        if (isset($mappings['error'])) {
            throw new Exception("Error en mappings: " . $mappings['error']);
        }

        $table_map   = $mappings['table_map']   ?? [];
        $date_fields = $mappings['date_fields'] ?? [];
        $field_map   = $mappings['field_map']   ?? [];
        $id_fields   = $mappings['id_fields']   ?? [];

        // 3) Biomarcadores (incluye name_db)
        $stmt = $this->db->prepare("
            SELECT biomarker_id, panel_id, name, name_es, name_db, reference_min, reference_max
            FROM biomarkers
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        $biomarkers = [];
        while ($row = $result->fetch_assoc()) {
            $biomarkers[$row['biomarker_id']] = $row;
        }

        // 4) Paneles
        $stmt = $this->db->prepare("SELECT panel_id, display_name, display_name_es FROM test_panels");
        $stmt->execute();
        $resPanels = $stmt->get_result();
        $panelNames = [];
        while ($row = $resPanels->fetch_assoc()) {
            $panelNames[$row['panel_id']] = $row;
        }

        // 5) UNION dinámico por tablas (para filtrar id_record que pertenezcan al user)
        if (empty($table_map)) {
            // No hay tablas/paneles cargados: no habrá alertas vinculadas a registros
            return [];
        }

        $unionParts = [];
        foreach ($table_map as $pid => $table) {
            $id_field = $id_fields[$pid] ?? 'id';
            // Nota: comillas invertidas para tabla/campo
            $unionParts[] = "SELECT `{$id_field}` FROM `{$table}` WHERE `user_id` = ?";
        }
        $union_query = implode(" UNION ", $unionParts);

        // 6) Filtro por flag de alerta
        $where_alert = is_array($no_alert_flag)
            ? "no_alert_user IN (" . implode(',', array_map('intval', $no_alert_flag)) . ")"
            : "no_alert_user = ?";

        $query = "
            SELECT *
            FROM `notifications`
            WHERE {$where_alert}
              AND `user_id` = ?
              AND `id_record` IN ( {$union_query} )
            ORDER BY `notification_id` DESC
        ";

        // 7) Bind de parámetros según escenario
        $numUserIds = count($table_map); // n de ? en el UNION
        if (is_array($no_alert_flag)) {
            // Solo user_ids del UNION + el user_id final del WHERE
            $param_types = str_repeat("s", $numUserIds) . "s";
            $params = array_merge(array_fill(0, $numUserIds, $user_id), [$user_id]);
        } else {
            // flag (int) + user_ids del UNION + user_id final
            $param_types = "i" . str_repeat("s", $numUserIds) . "s";
            $params = array_merge([(int)$no_alert_flag], array_fill(0, $numUserIds, $user_id), [$user_id]);
        }

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->db->error);
        }
        $stmt->bind_param($param_types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        $alerts = [];

        // 8) Resolver valor del biomarcador por registro
        while ($row = $result->fetch_assoc()) {
            $notification_id = $row['notification_id'];
            $panel_id        = $row['id_panel'];
            $biomarker_id    = $row['id_biomarker'];
            $record_id       = $row['id_record'];

            if (!isset($biomarkers[$biomarker_id])) {
                continue;
            }
            $bm = $biomarkers[$biomarker_id];

            // Claves posibles según el nuevo mapping
            $bm_key_en  = strtolower(trim($bm['name'] ?? ''));
            $bm_key_es  = strtolower(trim($bm['name_es'] ?? ''));
            $bm_key_db  = strtolower(trim($bm['name_db'] ?? '')); // por si quieres lookup directo
            $bm_norm_en = $bm_key_en !== '' ? preg_replace(['/[\s\-]+/','/[()]/'], ['_',''], $bm_key_en) : '';
            $bm_norm_es = $bm_key_es !== '' ? preg_replace(['/[\s\-]+/','/[()]/'], ['_',''], $bm_key_es) : '';

            if (!isset($table_map[$panel_id])) {
                continue;
            }
            $table     = $table_map[$panel_id];
            $id_field  = $id_fields[$panel_id]  ?? 'id';
            $date_field= $date_fields[$panel_id] ?? null; // podría no existir

            // Buscar el field_key en el orden: name → name_es → norm(name) → norm(name_es) → (opcional) name_db
            $panelFields = $field_map[$panel_id] ?? [];
            $field_key =
                ($bm_key_en  !== '' && isset($panelFields[$bm_key_en]))  ? $panelFields[$bm_key_en][0]  : null;
            if (!$field_key && $bm_key_es !== '') {
                $field_key = isset($panelFields[$bm_key_es]) ? $panelFields[$bm_key_es][0] : null;
            }
            if (!$field_key && $bm_norm_en !== '') {
                $field_key = isset($panelFields[$bm_norm_en]) ? $panelFields[$bm_norm_en][0] : null;
            }
            if (!$field_key && $bm_norm_es !== '') {
                $field_key = isset($panelFields[$bm_norm_es]) ? $panelFields[$bm_norm_es][0] : null;
            }
            // Si quieres permitir lookup por name_db (solo si lo incluíste en buildPanelBiomarkerMappings):
            if (!$field_key && $bm_key_db !== '' && isset($panelFields[$bm_key_db])) {
                $field_key = $panelFields[$bm_key_db][0];
            }

            if (!$field_key) {
                // No se encontró el campo correspondiente al biomarcador en ese panel
                continue;
            }

            // Query del valor
            $date_sql = $date_field ? ", `{$date_field}` AS `date`" : ", NULL AS `date`";
            $sqlValue = "SELECT `{$field_key}` AS `value` {$date_sql} FROM `{$table}` WHERE `{$id_field}` = ? LIMIT 1";
            $stmt_value = $this->db->prepare($sqlValue);
            if (!$stmt_value) {
                // si falla la tabla/campo, saltamos esta alerta
                continue;
            }
            $stmt_value->bind_param("s", $record_id);
            $stmt_value->execute();
            $value_result = $stmt_value->get_result()->fetch_assoc();

            if (!$value_result || !isset($value_result['value'])) {
                continue;
            }

            $value = is_numeric($value_result['value']) ? (float)$value_result['value'] : null;
            $date  = $value_result['date'] ?? null;

            // 9) Determinar status (usa name_db para casos especiales como body_age)
            $status           = null;
            $status_original  = $row['status'] ?? null;
            $ref_min = isset($bm['reference_min']) ? (float)$bm['reference_min'] : null;
            $ref_max = isset($bm['reference_max']) ? (float)$bm['reference_max'] : null;

            $bm_is_body_age = (strtolower(trim($bm['name_db'] ?? '')) === 'body_age')
                           || (strtolower(trim($bm['name'] ?? '')) === 'body age')
                           || (strtolower(trim($bm['name_es'] ?? '')) === 'edad corporal');

            if ($bm_is_body_age && $age !== null && $value !== null) {
                if ($value > $age) {
                    $status = ($idioma === 'ES') ? 'Alto' : 'High';
                }
            } elseif ($value !== null) {
                if ($ref_min !== null && $value < $ref_min) {
                    $status = ($idioma === 'ES') ? 'Bajo' : 'Low';
                } elseif ($ref_max !== null && $value > $ref_max) {
                    $status = ($idioma === 'ES') ? 'Alto' : 'High';
                }
            }

            if ($status) {
                $alerts[] = [
                    'notification_id' => $notification_id,
                    'biomarker'       => ($idioma === 'ES') ? ($bm['name_es'] ?? $bm['name']) : ($bm['name'] ?? ''),
                    'value'           => $value,
                    'status'          => $status,
                    'status_original' => $status_original,
                    'date'            => $date,
                    'panel'           => $panel_id,
                    'panel_name'      => ($idioma === 'ES')
                        ? ($panelNames[$panel_id]['display_name_es'] ?? '')
                        : ($panelNames[$panel_id]['display_name'] ?? ''),
                    'biomarker_key'   => $field_key,
                    'record_id'       => $record_id,
                    'status_read'     => (int)($row['no_alert_user'] ?? 0),
                ];
            }
        }

        return $alerts;

    } catch (Exception $e) {
        return ['value' => false, 'message' => $e->getMessage()];
    }
}







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
    return $this->getUserAlertsByFlag($user_id, [0,1], $limit, $offset);
}
public function getAllUserAlertsByFlag2($user_id)
{
    return $this->getAllUserAlertsByFlag($user_id, [0,1]);
}


public function getByUserIdViewAll($user_id)
{
    try {
        $idioma = $_SESSION['idioma'] ?? 'EN';

        // 1) Datos del usuario
        $stmt = $this->db->prepare("SELECT sex_biological, birthday FROM users WHERE user_id = ?");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $user_data = $stmt->get_result()->fetch_assoc();

        if (!$user_data) {
            return ['value' => false, 'message' => 'User not found'];
        }

        $age = null;
        if (!empty($user_data['birthday'])) {
            $birthdate = new DateTime($user_data['birthday']);
            $today     = new DateTime();
            $age       = $today->diff($birthdate)->y;
        }
        $sex_biological = strtolower(trim($user_data['sex_biological'] ?? ''));

        // 2) Mappings dinámicos
        $mappings = $this->buildPanelBiomarkerMappings();
        if (isset($mappings['error'])) {
            throw new Exception("Error en mappings: " . $mappings['error']);
        }

        $table_map   = $mappings['table_map']   ?? [];
        $date_fields = $mappings['date_fields'] ?? [];
        $field_map   = $mappings['field_map']   ?? [];
        $id_fields   = $mappings['id_fields']   ?? [];

        if (empty($table_map)) {
            return [];
        }

        // 3) Biomarcadores (incluye name_db)
        $stmt = $this->db->prepare("
            SELECT biomarker_id, panel_id, name, name_es, name_db, reference_min, reference_max
            FROM biomarkers
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        $biomarkers = [];
        while ($row = $result->fetch_assoc()) {
            $biomarkers[$row['biomarker_id']] = $row;
        }

        // 4) Subquery dinámico: records válidos del usuario en todas las tablas
        $unionParts = [];
        foreach ($table_map as $pid => $table) {
            $id_field = $id_fields[$pid] ?? 'id';
            $unionParts[] = "SELECT `{$id_field}` FROM `{$table}` WHERE `user_id` = ?";
        }
        $union_query = implode(" UNION ", $unionParts);

        $query = "
            SELECT *
            FROM `notifications`
            WHERE `no_alert_user` = 0
              AND `user_id` = ?
              AND `id_record` IN ( {$union_query} )
            ORDER BY `notification_id` DESC
        ";

        // Tipos y parámetros
        $param_types = "s" . str_repeat("s", count($table_map)); // user_id + N user_id del UNION
        $params = array_merge([$user_id], array_fill(0, count($table_map), $user_id));

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->db->error);
        }
        $stmt->bind_param($param_types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        $alerts = [];

        // 5) Iterar notificaciones y resolver valores
        while ($row = $result->fetch_assoc()) {
            $notification_id = $row['notification_id'];
            $panel_id        = $row['id_panel'];
            $biomarker_id    = $row['id_biomarker'];
            $record_id       = $row['id_record'];

            if (!isset($biomarkers[$biomarker_id])) {
                continue;
            }
            $bm = $biomarkers[$biomarker_id];

            // Claves posibles (minúsculas)
            $bm_key_en  = strtolower(trim($bm['name'] ?? ''));
            $bm_key_es  = strtolower(trim($bm['name_es'] ?? ''));
            $bm_key_db  = strtolower(trim($bm['name_db'] ?? '')); // por si quieres lookup directo
            $bm_norm_en = $bm_key_en !== '' ? preg_replace(['/[\s\-]+/','/[()]/'], ['_',''], $bm_key_en) : '';
            $bm_norm_es = $bm_key_es !== '' ? preg_replace(['/[\s\-]+/','/[()]/'], ['_',''], $bm_key_es) : '';

            if (!isset($table_map[$panel_id])) {
                continue;
            }
            $table      = $table_map[$panel_id];
            $id_field   = $id_fields[$panel_id]  ?? 'id';
            $date_field = $date_fields[$panel_id] ?? null;

            // Resolver field_key con orden: name → name_es → norm(name) → norm(name_es) → (opcional) name_db
            $panelFields = $field_map[$panel_id] ?? [];
            $field_key =
                ($bm_key_en  !== '' && isset($panelFields[$bm_key_en]))  ? $panelFields[$bm_key_en][0]  : null;
            if (!$field_key && $bm_key_es !== '') {
                $field_key = isset($panelFields[$bm_key_es]) ? $panelFields[$bm_key_es][0] : null;
            }
            if (!$field_key && $bm_norm_en !== '') {
                $field_key = isset($panelFields[$bm_norm_en]) ? $panelFields[$bm_norm_en][0] : null;
            }
            if (!$field_key && $bm_norm_es !== '') {
                $field_key = isset($panelFields[$bm_norm_es]) ? $panelFields[$bm_norm_es][0] : null;
            }
            if (!$field_key && $bm_key_db !== '' && isset($panelFields[$bm_key_db])) {
                $field_key = $panelFields[$bm_key_db][0];
            }

            if (!$field_key) {
                // No se pudo mapear el biomarcador a un campo de esa tabla/panel
                continue;
            }

            // Query del valor (manejo si no hay campo fecha)
            $date_sql = $date_field ? ", `{$date_field}` AS `date`" : ", NULL AS `date`";
            $sqlValue = "SELECT `{$field_key}` AS `value` {$date_sql} FROM `{$table}` WHERE `{$id_field}` = ? LIMIT 1";
            $stmt_value = $this->db->prepare($sqlValue);
            if (!$stmt_value) {
                continue;
            }
            $stmt_value->bind_param("s", $record_id);
            $stmt_value->execute();
            $value_result = $stmt_value->get_result()->fetch_assoc();

            if (!$value_result || !isset($value_result['value'])) {
                continue;
            }

            $value = is_numeric($value_result['value']) ? (float)$value_result['value'] : null;
            $date  = $value_result['date'] ?? null;

            // Determinar status (especial para body_age usando name_db)
            $status          = null;
            $status_original = $row['status'] ?? null;
            $ref_min = isset($bm['reference_min']) ? (float)$bm['reference_min'] : null;
            $ref_max = isset($bm['reference_max']) ? (float)$bm['reference_max'] : null;

            $bm_is_body_age = (strtolower(trim($bm['name_db'] ?? '')) === 'body_age')
                           || (strtolower(trim($bm['name'] ?? '')) === 'body age')
                           || (strtolower(trim($bm['name_es'] ?? '')) === 'edad corporal');

            if ($bm_is_body_age && $age !== null && $value !== null) {
                if ($value > $age) {
                    $status = ($idioma === 'ES') ? 'Alto' : 'High';
                }
            } elseif ($value !== null) {
                if ($ref_min !== null && $value < $ref_min) {
                    $status = ($idioma === 'ES') ? 'Bajo' : 'Low';
                } elseif ($ref_max !== null && $value > $ref_max) {
                    $status = ($idioma === 'ES') ? 'Alto' : 'High';
                }
            }

            if ($status) {
                $alerts[] = [
                    'notification_id' => $notification_id,
                    'biomarker'       => ($idioma === 'ES') ? ($bm['name_es'] ?? $bm['name']) : ($bm['name'] ?? ''),
                    'value'           => $value,
                    'status'          => $status,
                    'status_original' => $status_original,
                    'date'            => $date,
                    'panel'           => $panel_id,
                    'biomarker_key'   => $field_key,
                    'record_id'       => $record_id
                ];
            }
        }

        return $alerts;

    } catch (Exception $e) {
        return ['value' => false, 'message' => $e->getMessage()];
    }
}






  public function getAllUsersNotifications()
{
    try {
        $idioma = $_SESSION['idioma'] ?? 'EN';

        // Obtener todos los usuarios
        $stmt = $this->db->prepare("SELECT user_id, first_name, last_name, sex_biological, birthday FROM users");
        $stmt->execute();
        $users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Obtener mappings centralizados
        $mappings = $this->buildPanelBiomarkerMappings();
        if (isset($mappings['error'])) {
            throw new Exception("Error en mappings: " . $mappings['error']);
        }

        $table_map = $mappings['table_map'];
        $date_fields = $mappings['date_fields'];
        $field_map = $mappings['field_map'];
        $id_fields = $mappings['id_fields'];

        // Obtener biomarcadores
        $stmt = $this->db->prepare("SELECT biomarker_id, panel_id, name, name_es, reference_min, reference_max FROM biomarkers");
        $stmt->execute();
        $result = $stmt->get_result();
        $biomarkers = [];
        while ($row = $result->fetch_assoc()) {
            $biomarkers[$row['biomarker_id']] = $row;
        }

        $alerts = [];

        foreach ($users as $user) {
            $user_id = $user['user_id'];
            $birthdate = new DateTime($user['birthday']);
            $today = new DateTime();
            $age = $today->diff($birthdate)->y;
            $sex_biological = strtolower(trim($user['sex_biological']));

            // Generar el subquery dinámico de IDs de records por cada tabla
            $unionParts = [];
            foreach ($table_map as $pid => $table) {
                $id_field = $id_fields[$pid] ?? 'id';
                $unionParts[] = "SELECT $id_field FROM {$table} WHERE user_id = ?";
            }
            $union_query = implode(" UNION ", $unionParts);

            $query = "SELECT * FROM notifications 
                      WHERE no_alert_admin = 0 
                      AND id_record IN ( $union_query ) 
                      ORDER BY notification_id DESC";

            $param_types = str_repeat("s", count($table_map));
            $params = array_fill(0, count($table_map), $user_id);

            $stmt = $this->db->prepare($query);
            $stmt->bind_param($param_types, ...$params);
            $stmt->execute();
            $noti_result = $stmt->get_result();

            while ($row = $noti_result->fetch_assoc()) {
                $notification_id =  $row['notification_id'];
                $panel_id =  $row['id_panel'];
                $biomarker_id =  $row['id_biomarker'];
                $record_id =  $row['id_record'];

                if (!isset($biomarkers[$biomarker_id])) continue;
                $bm = $biomarkers[$biomarker_id];
                $bm_name = strtolower($bm['name']);

                if (!isset($table_map[$panel_id]) || !isset($field_map[$panel_id][$bm_name])) continue;

                $table = $table_map[$panel_id];
                $id_field = $id_fields[$panel_id] ?? 'id';
                $date_field = $date_fields[$panel_id];
                $field_key = $field_map[$panel_id][$bm_name][0];

                $stmt_value = $this->db->prepare("SELECT `$field_key` AS value, `$date_field` AS date FROM `$table` WHERE `$id_field` = ?");
                $stmt_value->bind_param("s", $record_id);
                $stmt_value->execute();
                $value_result = $stmt_value->get_result()->fetch_assoc();

                if (!$value_result || !isset($value_result['value'])) continue;

                $value = floatval($value_result['value']);
                $date = $value_result['date'] ?? null;

                $status = null;
                if ($bm_name === 'body age') {
                    if ($value > $age) {
                        $status = ($idioma === 'ES') ? 'Alto' : 'High';
                    }
                } elseif ($value < (float) $bm['reference_min']) {
                    $status = ($idioma === 'ES') ? 'Bajo' : 'Low';
                } elseif ($value > (float) $bm['reference_max']) {
                    $status = ($idioma === 'ES') ? 'Alto' : 'High';
                }

                if ($status) {
                    $alerts[] = [
                        'notification_id' => $notification_id,
                        'user_id' => $user_id,
                        'first_name' => $user['first_name'],
                        'last_name' => $user['last_name'],
                        'biomarker' => ($idioma === 'ES') ? $bm['name_es'] : $bm['name'],
                        'value' => $value,
                        'status' => $status,
                        'date' => $date,
                        'panel' => $panel_id,
                        'biomarker_key' => $field_key,
                        'record_id' => $record_id
                    ];
                }
            }
        }

        return $alerts;

    } catch (Exception $e) {
        return ['error' => $e->getMessage()];
    }
}





    public function getByStatus($status)
    {
        $stmt = $this->db->prepare("SELECT * FROM notifications WHERE status = ? ORDER BY notification_id  DESC");
        $stmt->bind_param("s", $status);
        $stmt->execute();
        $result = $stmt->get_result();
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        return $items;
    }

    public function getActiveAlerts()
    {
        $query = "SELECT * FROM notifications WHERE status != 'ok' ORDER BY notification_id  DESC";
        $result = $this->db->query($query);
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        return $items;
    }

    public function getByBiomarkerId($id_biomarker)
    {
        $stmt = $this->db->prepare("SELECT * FROM notifications WHERE id_biomarker = ? ORDER BY notification_id  DESC");
        $stmt->bind_param("s", $id_biomarker);
        $stmt->execute();
        $result = $stmt->get_result();
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        return $items;
    }

public function getByBiomarkerAndUser($id_biomarker, $user_id)
{
    try {
        // 1) Mappings centralizados
        $mappings = $this->buildPanelBiomarkerMappings();
        if (isset($mappings['error'])) {
            throw new Exception("Error en mappings: " . $mappings['error']);
        }

        $table_map = $mappings['table_map'] ?? [];
        $id_fields = $mappings['id_fields'] ?? [];

        if (empty($table_map)) {
            return [];
        }

        // 2) Subquery dinámico por tablas activas del usuario
        $unionParts = [];
        foreach ($table_map as $pid => $table) {
            $id_field = $id_fields[$pid] ?? 'id';
            $unionParts[] = "SELECT `{$id_field}` FROM `{$table}` WHERE `user_id` = ?";
        }
        $union_query = implode(" UNION ", $unionParts);

        // 3) Consulta principal (incluye user_id para coherencia/seguridad)
        $query = "
            SELECT *
            FROM `notifications`
            WHERE `id_biomarker` = ?
              AND `user_id` = ?
              AND `id_record` IN ( {$union_query} )
            ORDER BY `notification_id` DESC
        ";

        // 4) Parámetros
        $numUserIds  = count($table_map);
        $param_types = "ss" . str_repeat("s", $numUserIds); // biomarker, user_id (notifications), user_id * N (UNION)
        $params      = array_merge([$id_biomarker, $user_id], array_fill(0, $numUserIds, $user_id));

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->db->error);
        }
        $stmt->bind_param($param_types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        return $items;

    } catch (Exception $e) {
        return ['error' => $e->getMessage()];
    }
}




    public function updateNoAlertUser($recordId)
    {
        $stmt = $this->db->prepare("UPDATE notifications SET no_alert_user = 1 WHERE notification_id  = ?");
        $stmt->bind_param("s", $recordId);
        return $stmt->execute();
    }
      public function updateNew($user_id)
    {
        $stmt = $this->db->prepare("UPDATE notifications SET new = 1 WHERE user_id = ?");
        $stmt->bind_param("s", $user_id);
        return $stmt->execute();
    }

    public function updateNoAlertAdmin($recordId)
    {
        $stmt = $this->db->prepare("UPDATE notifications SET no_alert_admin = 1 WHERE notification_id  = ?");
        $stmt->bind_param("s", $recordId);
        return $stmt->execute();
    }
    public function updateAllNoAlertUserByUserId($user_id)
    {
        $stmt = $this->db->prepare("UPDATE notifications SET no_alert_user = 1 WHERE user_id = ?");
        $stmt->bind_param("s", $user_id);
        return $stmt->execute();
    }
    public function updateAllNoAlertAdmin()
    {
        $query = "UPDATE notifications SET no_alert_admin = 1 WHERE no_alert_admin = 0";
        return $this->db->query($query);
    }



public function create($data)
{
    $this->db->begin_transaction();
    try {
        // Generar UUID como ID de la notificación
        $uuid = $this->generateUUIDv4();

        $query = "INSERT INTO {$this->table} 
            (notification_id, id_panel, id_record, id_biomarker, status, no_alert_user, no_alert_admin, user_id, new)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)";
        
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            throw new mysqli_sql_exception("Error preparing insert: " . $this->db->error);
        }

        $stmt->bind_param(
            "sssssiis",
            $uuid,
            $data['id_panel'],
            $data['id_record'],
            $data['id_biomarker'],
            $data['status'],
            $data['no_alert_user'],
            $data['no_alert_admin'],
            $data['user_id']
        );

        $stmt->execute();
        $stmt->close();
        $this->db->commit();

        return ['status' => 'success', 'message' => 'Notification created.'];
    } catch (mysqli_sql_exception $e) {
        $this->db->rollback();
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}
private function generateUUIDv4(): string
{
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}


public function update($data)
{
    $this->db->begin_transaction();
    try {
        // Validar existencia
        $check = $this->db->prepare("SELECT notification_id  FROM {$this->table} WHERE notification_id  = ?");
        $check->bind_param("s", $data['id']);
        $check->execute();
        $res = $check->get_result();
        if ($res->num_rows === 0) {
            return ['status' => 'error', 'message' => 'Notification not found for update.'];
        }

        $query = "UPDATE {$this->table} 
                  SET id_panel = ?, id_record = ?, id_biomarker = ?, status = ?, no_alert_user = ?, no_alert_admin = ?, new = ? 
                  WHERE notification_id  = ?";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            throw new mysqli_sql_exception("Error preparing update: " . $this->db->error);
        }

        $stmt->bind_param(
            "ssssiiis",
            $data['id_panel'],
            $data['id_record'],
            $data['id_biomarker'],
            $data['status'],
            $data['no_alert_user'],
            $data['no_alert_admin'],
            $data['new'],
            $data['id']
        );

        $stmt->execute();
        $this->db->commit();

        return ['status' => 'success', 'message' => 'Notification updated.'];
    } catch (mysqli_sql_exception $e) {
        $this->db->rollback();
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}


    public function delete($id)
    {
        // Validar existencia
        $check = $this->db->prepare("SELECT notification_id  FROM {$this->table} WHERE notification_id  = ?");
        $check->bind_param("s", $id);
        $check->execute();
        $res = $check->get_result();
        if ($res->num_rows === 0) {
            return ['status' => 'error', 'message' => 'Notification not found for deletion.'];
        }

        $this->db->begin_transaction();
        try {
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE notification_id  = ?");
            $stmt->bind_param("s", $id);
            $stmt->execute();
            $this->db->commit();

            return ['status' => 'success', 'message' => 'Notification deleted.'];
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
