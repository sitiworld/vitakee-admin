<?php

require_once __DIR__ . '/../config/Database.php';

// === Dependencias ===
require_once __DIR__ . '/../models/BiomarkerModel.php';
require_once __DIR__ . '/../models/NotificationModel.php';
require_once __DIR__ . '/../helpers/NotificationTemplateHelper.php'; // Incluir el Helper
require_once __DIR__ . '/../config/ClientEnvironmentInfo.php'; // Para auditoría
require_once __DIR__ . '/../config/TimezoneManager.php'; // Para auditoría

class EnergyMetabolismModel
{
    private $db;
    private $table = "energy_metabolism";

    public $energy_metabolism_id;
    public $user_id;
    public $energy_date;
    public $energy_time;
    public $glucose;
    public $ketone;

    // Nuevos campos HbA1c / eAG
    public $hba1c;          // %
    public $hba1c_target;   // %
    public $derived_value;  // eAG (mg/dL por defecto)
    public $derived_unit;   // 'mg/dL'
    public $notes;          // Texto opcional

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /* ===================== EXPORT ===================== */

    public function exportUserRecordsToCSV($user_id)
    {
        try {
            $sql = "SELECT energy_date, energy_time, glucose, ketone, hba1c, hba1c_target, derived_value, derived_unit, notes
                    FROM {$this->table}
                    WHERE user_id = ?
                    ORDER BY energy_date DESC, energy_time DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("s", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $filename = "energy_metabolism_{$user_id}.csv";
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                $output = fopen('php://output', 'w');

                fputcsv($output, [
                    'Date', 'Time',
                    'Glucose (mg/dL)', 'Ketone (mmol/L)',
                    'HbA1c (%)', 'Target (%)',
                    'eAG', 'eAG Unit',
                    'Notes'
                ]);

                while ($row = $result->fetch_assoc()) {
                    fputcsv($output, $row);
                }

                fclose($output);
                exit;
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No data found for this user.']);
            }
        } catch (mysqli_sql_exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        }
    }

    /* ===================== QUERIES BÁSICAS ===================== */

    public function getAll()
    {
        try {
            $query = "SELECT energy_metabolism_id, user_id, energy_date, energy_time,
                             glucose, ketone, hba1c, hba1c_target, derived_value, derived_unit, notes
                      FROM {$this->table}
                      WHERE deleted_at IS NULL
                      ORDER BY energy_date DESC, energy_time DESC";
            $result = $this->db->query($query);
            if (!$result) {
                throw new mysqli_sql_exception("Error al obtener los registros: " . $this->db->error);
            }

            $items = [];
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }

            return count($items) > 0
                ? $items
                : ['status' => 'error', 'message' => 'No records found.'];
        } catch (mysqli_sql_exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function getByUserId($userId)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT energy_metabolism_id, user_id, energy_date, energy_time,
                        glucose, ketone, hba1c, hba1c_target, derived_value, derived_unit, notes
                 FROM {$this->table}
                 WHERE user_id = ? AND deleted_at IS NULL
                 ORDER BY energy_date DESC, energy_time DESC"
            );
            if (!$stmt) {
                throw new mysqli_sql_exception("Error al preparar consulta: " . $this->db->error);
            }
            $stmt->bind_param("s", $userId);
            $stmt->execute();
            $result = $stmt->get_result();

            $items = [];
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }

            return count($items) > 0
                ? $items
                : ['value' => true, "data" => [], 'message' => 'No records found for this user.'];
        } catch (mysqli_sql_exception $e) {
            return ['value' => false, 'message' => $e->getMessage()];
        }
    }

    /* ===================== MAPEO DINÁMICO (Panel Energy Metabolism) ===================== */

    private function buildEnergyMetabolismBiomarkerMappings(): array
    {
        try {
            $panelId = '7ff39dd8-01e9-443c-b8e6-0d6b429e63a6'; // Panel Energy Metabolism

            $query = "SELECT biomarker_id, name FROM biomarkers WHERE panel_id = ? AND deleted_at IS NULL ORDER BY name";
            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new mysqli_sql_exception("Error preparando query: " . $this->db->error);
            }
            $stmt->bind_param("s", $panelId);
            $stmt->execute();
            $result = $stmt->get_result();

            // Mapeo exacto de nombres a keys (tolerante a variantes de escritura)
            $biomarkerNameMapping = [
                'glucose' => 'glucose',
                'ketones' => 'ketone',
                'ketone'  => 'ketone',
                'hba1c'   => 'hba1c',
                'hbA1c'   => 'hba1c',
                'HbA1c'   => 'hba1c',
            ];

            $biomarkers = [];
            $validFields = [];

            while ($row = $result->fetch_assoc()) {
                $id = $row['biomarker_id'];
                $name = strtolower(trim($row['name']));

                if (!isset($biomarkerNameMapping[$name])) {
                    continue;
                }

                $fieldKey = $biomarkerNameMapping[$name];
                $biomarkers[$fieldKey] = $id;
                $validFields[] = $fieldKey;
            }

            return [
                'biomarkers' => $biomarkers,
                'validFields' => array_unique($validFields)
            ];
        } catch (mysqli_sql_exception $e) {
            return [
                'biomarkers' => [],
                'validFields' => [],
                'error' => $e->getMessage()
            ];
        }
    }

    /* ===================== HISTORIAL POR REC ID ===================== */

    public function getUserBiomarkerHistoryByRecordId($recId, $type)
{
    try {
        $this->db->begin_transaction();

        $mapping = $this->buildEnergyMetabolismBiomarkerMappings();
        $validFields = $mapping['validFields'];

        if (!in_array($type, $validFields)) {
            throw new Exception('Invalid biomarker type.');
        }

        // Obtener el user_id desde el registro
        $stmt = $this->db->prepare("SELECT user_id FROM {$this->table} WHERE energy_metabolism_id = ?");
        if (!$stmt) {
            throw new mysqli_sql_exception("Error al preparar consulta: " . $this->db->error);
        }
        $stmt->bind_param("s", $recId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            throw new Exception('No record found for the given ID.');
        }

        $user_id = $result->fetch_assoc()['user_id'];
        $stmt->close();

        // Consultar historial del biomarcador (ignorar valores NULL)
        $query = "SELECT energy_date, {$type} AS value
                  FROM {$this->table}
                  WHERE user_id = ? 
                    AND deleted_at IS NULL
                    AND {$type} IS NOT NULL
                  ORDER BY energy_date DESC";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            throw new mysqli_sql_exception("Error al preparar consulta: " . $this->db->error);
        }
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $history = [];
        while ($row = $result->fetch_assoc()) {
            $history[] = [
                'date'  => date('m/d/Y', strtotime($row['energy_date'])),
                'value' => $row['value']
            ];
        }

        $this->db->commit();
        return ['status' => 'success', 'data' => $history];
    } catch (Exception $e) {
        $this->db->rollback();
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}

    /* ===================== OBTENER POR ID ===================== */

    public function getById($id)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT energy_metabolism_id, user_id, energy_date, energy_time,
                        glucose, ketone, hba1c, hba1c_target, derived_value, derived_unit, notes
                 FROM {$this->table} WHERE energy_metabolism_id = ? LIMIT 1"
            );
            if (!$stmt) {
                throw new mysqli_sql_exception("Error al preparar consulta: " . $this->db->error);
            }
            $stmt->bind_param("s", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();

            if ($data) {
                return $this->jsonResponse(true, '', $data);
            } else {
                return ['status' => 'error', 'message' => 'Record not found.'];
            }
        } catch (mysqli_sql_exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /* ===================== HELPERS ===================== */

    private function countExamsByBiomarker($user_id, $biomarker_field, $date)
    {
        $query = "SELECT COUNT(*) AS total 
                  FROM {$this->table}
                  WHERE user_id = ? 
                    AND energy_date = ?
                    AND {$biomarker_field} IS NOT NULL 
                    AND {$biomarker_field} > 0
                    AND deleted_at IS NULL";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ss", $user_id, $date);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return (int) $row['total'];
    }

    private function computeEAGmgdl(?float $hba1c): ?float
    {
        if ($hba1c === null) return null;
        return 28.7 * $hba1c - 46.7; // mg/dL
    }

/* ===================== CREATE ===================== */
public function create($data)
{
    $this->db->begin_transaction();
    try {
        $lang       = $_SESSION['idioma'] ?? 'EN';
        $created_by = $_SESSION['user_id'] ?? null; // Usuario que realiza la acción

        require_once __DIR__ . '/../models/BiomarkerModel.php';
        require_once __DIR__ . '/../models/NotificationModel.php';
        require_once __DIR__ . '/../helpers/NotificationTemplateHelper.php';


        $biomarkerModel    = new BiomarkerModel();
        $notificationModel = new NotificationModel();

        $user_id = $data['user_id']; // Usuario al que pertenece el registro

        // ====== Lectura null-safe
        $glucose = isset($data['glucose']) ? floatval($data['glucose']) : null;
        $ketone  = isset($data['ketone'])  ? floatval($data['ketone'])  : null;

        $hba1c = (isset($data['hba1c']) && $data['hba1c'] !== '')
            ? floatval($data['hba1c'])
            : null;

        $hba1c_target = (isset($data['hba1c_target']) && $data['hba1c_target'] !== '')
            ? floatval($data['hba1c_target'])
            : null;

        $notes = $data['notes'] ?? null;

        // Asegurar variables definidas
        $derived_value = null;
        $derived_unit  = null;

        /* ===== REGLA DE NEGOCIO ===== */
        if ($hba1c !== null && $hba1c > 0) {
            $glucose       = 0.0;
            $ketone        = 0.0;
            $derived_value = $this->computeEAGmgdl($hba1c);
            $derived_unit  = 'mg/dL';
        } elseif ( (($glucose ?? 0) > 0) || (($ketone ?? 0) > 0) ) {
            $hba1c         = null;
            $hba1c_target  = null;
            $derived_value = null;
            $derived_unit  = null;
            $notes         = null;
        } else {
            $hba1c         = null;
            $hba1c_target  = null;
            $derived_value = null;
            $derived_unit  = null;
            // $notes se deja como esté
        }

        // ====== Auditoría / TZ
        $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
        $env->applyAuditContext($this->db, $created_by); // Auditoría con el usuario en sesión

        $tzManager = new TimezoneManager($this->db);
        $tzManager->applyTimezone();

        // 'created_at' es para la auditoría y SIEMPRE debe ser la del servidor.
        $created_at = $env->getCurrentDatetime();  // ej. '2025-10-03 13:22:45'
        
        // CAMBIO: Usamos la fecha y hora enviadas por el cliente (JS)
        // El JS unifica 'energy_date' y 'hba1c_date' a 'energy_date' en el payload.
        $examDate = $data['energy_date'] ?? substr($created_at, 0, 10);
        $examTime = $data['energy_time'] ?? substr($created_at, 11, 8);

        // (Opcional) Sanity check por si el JS falla y manda un formato incorrecto
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $examDate)) {
            $examDate = substr($created_at, 0, 10);
        }
        if (!preg_match('/^(\d{2}):(\d{2}):(\d{2})$/', $examTime)) {
            $examTime = substr($created_at, 11, 8);
        }


        // ====== UUID
        $uuid = $this->generateUUIDv4();

        // ====== Chequeo límite diario por biomarcador (usar valores NORMALIZADOS)
        $mapping    = $this->buildEnergyMetabolismBiomarkerMappings();
        $biomarkers = $mapping['biomarkers'];
        
        // Normalizamos los datos para el chequeo
        $normalizedData = [
            'glucose' => $glucose,
            'ketone'  => $ketone,
            'hba1c'   => $hba1c,
        ];

        foreach ($biomarkers as $field => $biomarkerId) {
            $v = isset($normalizedData[$field]) ? floatval($normalizedData[$field]) : 0;
            if ($v <= 0) continue;

            $maxResult = $biomarkerModel->getMaxExamById($biomarkerId);
            if (($maxResult['status'] ?? '') === 'success' && ($maxResult['max_exam'] ?? 0) > 0) {
                // Usar la fecha enviada por el cliente ($examDate) para el chequeo
                $count = $this->countExamsByBiomarker($user_id, $field, $examDate);
                if ($count >= intval($maxResult['max_exam'])) {
                    $bmInfo = $biomarkerModel->getById($biomarkerId);
                    $biomarkerName = $bmInfo['name_db'] ?? "Biomarker #$biomarkerId";
                    $message = $lang === 'ES'
                        ? "Se alcanzó el límite diario para el biomarcador: {$biomarkerName}"
                        : "Daily limit reached for biomarker: {$biomarkerName}";
                    $this->db->rollback();
                    return ['status' => 'error', 'message' => $message];
                }
            }
        }

        // ====== INSERT (usa la fecha/hora del CLIENTE)
        $query = "INSERT INTO {$this->table}
                    (energy_metabolism_id, user_id, energy_date, energy_time,
                     glucose, ketone, hba1c, hba1c_target, derived_value, derived_unit, notes,
                     created_by, created_at)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            throw new mysqli_sql_exception("Error preparando consulta: " . $this->db->error);
        }

        $derived_value_str = ($derived_value !== null) ? (string)$derived_value : null;

        $stmt->bind_param(
            "ssssddddsssss",
            $uuid,
            $user_id,
            $examDate, // <-- CAMBIO: Fecha del cliente
            $examTime, // <-- CAMBIO: Hora del cliente
            $glucose,
            $ketone,
            $hba1c,
            $hba1c_target,
            $derived_value_str,
            $derived_unit,
            $notes,
            $created_by,
            $created_at // <-- Columna de auditoría usa fecha del servidor
        );
        $stmt->execute();
        $stmt->close();

        // ====== Notificaciones (usar valores NORMALIZADOS)
        $normalizedForNotif = [
            'id'      => $uuid,
            'glucose' => $glucose,
            'ketone'  => $ketone,
            'hba1c'   => $hba1c,
        ];

        foreach ($biomarkers as $field => $biomarkerId) {
            $value = isset($normalizedForNotif[$field]) ? floatval($normalizedForNotif[$field]) : 0;
            if ($value <= 0) continue;

            $result = $biomarkerModel->evaluateBiomarkerValueStatus($biomarkerId, $value);
            if (
                isset($result['status'], $result['result']) &&
                $result['status'] === 'success' &&
                in_array($result['result'], ['High', 'Low'])
            ) {
                // *** INICIO BLOQUE MODIFICADO ***
                
                // 1. Obtener datos completos del biomarcador
                $biomarkerInfo = $biomarkerModel->getById($biomarkerId);
                $biomarkerName = $biomarkerInfo['name_db'] ?? $field; // <-- USAR NAME_DB
                $status_lang = $result['result'];
                if ($lang === 'ES') {
                    $status_lang = ($result['result'] === 'High') ? 'Alto' : 'Bajo';
                }

                // 2. Preparar parámetros para la plantilla
                $params = [
                    'biomarker_name' => $biomarkerName,
                    'value'          => $value,
                    'unit'           => $biomarkerInfo['unit'] ?? '',
                    'status'         => $status_lang,
                    'ref_min'        => $biomarkerInfo['reference_min'] ?? 'N/A',
                    'ref_max'        => $biomarkerInfo['reference_max'] ?? 'N/A',
                    'id_biomarker'   => $biomarkerId,
                    'id_record'      => $uuid 
                ];
                
                // 3. Construir la fila de notificación
                $dataRow = NotificationTemplateHelper::buildForInsert([
                    'template_key'    => 'biomarker_out_of_range',
                    'template_params' => $params,
                    'route'           => 'component_energy_metabolism?id=' . $uuid, // Ruta (ejemplo)
                    'module'          => 'energy_metabolism',
                    'user_id'         => $user_id
                ]);

                // 4. Crear la notificación
                $notificationModel->create($dataRow);
                // *** FIN BLOQUE MODIFICADO ***
            }
        }

        $this->db->commit();
        return ['status' => 'success', 'message' => 'Record created successfully.'];

    } catch (mysqli_sql_exception $e) {
        $this->db->rollback();
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}


    /* ===================== UPDATE ===================== */
public function update($data)
{
    $lang = $_SESSION['idioma'] ?? 'EN';
    $updated_by = $_SESSION['user_id'] ?? null; // Usuario que realiza la acción

    $checkStmt = $this->db->prepare("SELECT energy_metabolism_id, user_id FROM {$this->table} WHERE energy_metabolism_id = ? LIMIT 1");
    if (!$checkStmt) {
        throw new mysqli_sql_exception("Error preparing check query: " . $this->db->error);
    }

    $checkStmt->bind_param("s", $data['id']);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows == 0) {
        $msg = ($lang === 'ES')
            ? 'Registro no encontrado para actualizar.'
            : 'Record not found for update.';
        return ['status' => 'error', 'message' => $msg];
    }

    $existing = $checkResult->fetch_assoc();
    $user_id = $existing['user_id']; // Usuario al que pertenece el registro
    $checkStmt->close();

    $this->db->begin_transaction();
    try {
        $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
        $env->applyAuditContext($this->db, $updated_by); // Auditoría con el usuario en sesión
        $tzManager = new TimezoneManager($this->db);
        $tzManager->applyTimezone();
        $updated_at = $env->getCurrentDatetime();

        // ====== Lectura null-safe
        $glucose = isset($data['glucose']) ? floatval($data['glucose']) : null;
        $ketone  = isset($data['ketone'])  ? floatval($data['ketone'])  : null;

        $hba1c = (isset($data['hba1c']) && $data['hba1c'] !== '')
            ? floatval($data['hba1c'])
            : null;

        $hba1c_target = (isset($data['hba1c_target']) && $data['hba1c_target'] !== '')
            ? floatval($data['hba1c_target'])
            : null;

        $notes = $data['notes'] ?? null;

        /* ===================== REGLA DE NEGOCIO ===================== */
        if ($hba1c !== null && $hba1c > 0) {
            $glucose = 0.0;
            $ketone  = 0.0;
            $derived_value = $this->computeEAGmgdl($hba1c);
            $derived_unit  = 'mg/dL';
        } elseif ( (($glucose ?? 0) > 0) || (($ketone ?? 0) > 0) ) {
            $hba1c = null;
            $hba1c_target = null;
            $derived_value = null;
            $derived_unit  = null;
            $notes = null;
        } else {
            $hba1c = null;
            $hba1c_target = null;
            $derived_value = null;
            $derived_unit  = null;
        }

        // Update
        $query = "UPDATE {$this->table}
                    SET energy_date = ?, energy_time = ?, glucose = ?, ketone = ?,
                        hba1c = ?, hba1c_target = ?, derived_value = ?, derived_unit = ?, notes = ?,
                        updated_by = ?, updated_at = ?
                  WHERE energy_metabolism_id = ?";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            throw new mysqli_sql_exception("Error preparing update query: " . $this->db->error);
        }

        $stmt->bind_param(
            "ssdddddsssss",
            $data['energy_date'],
            $data['energy_time'],
            $glucose,
            $ketone,
            $hba1c,
            $hba1c_target,
            $derived_value,
            $derived_unit,
            $notes,
            $updated_by,
            $updated_at,
            $data['id']
        );
        $stmt->execute();
        $stmt->close();

        // ==== Notificaciones por biomarcador (usar valores NORMALIZADOS) ====
        $biomarkerModel = new BiomarkerModel();
        $notificationModel = new NotificationModel();

        $mapping = $this->buildEnergyMetabolismBiomarkerMappings();
        $biomarkers = $mapping['biomarkers'];

        // Normalizamos en $data para que el bucle use estos valores
        $data['glucose'] = $glucose;
        $data['ketone']  = $ketone;
        $data['hba1c']   = $hba1c;

        foreach ($biomarkers as $field => $biomarkerId) {
            $value = isset($data[$field]) ? floatval($data[$field]) : null;

            $stmtNotif = $this->db->prepare("SELECT notification_id FROM notifications WHERE id_record = ? AND id_biomarker = ? AND deleted_at IS NULL");
            $stmtNotif->bind_param("ss", $data['id'], $biomarkerId);
            $stmtNotif->execute();
            $notifResult = $stmtNotif->get_result();
            $notif = $notifResult->fetch_assoc();
            $stmtNotif->close();

            // Si no hay valor o <=0: eliminar notificación si existe
            if ($value === null || $value <= 0) {
                if ($notif) {
                    $notificationModel->delete($notif['notification_id']);
                }
                continue;
            }

            $result = $biomarkerModel->evaluateBiomarkerValueStatus($biomarkerId, $value);

            if (
                isset($result['status'], $result['result']) &&
                $result['status'] === 'success' &&
                in_array($result['result'], ['High', 'Low'])
            ) {
                // *** INICIO BLOQUE MODIFICADO (Crear o Actualizar) ***
                
                // 1. Obtener datos completos del biomarcador
                $biomarkerInfo = $biomarkerModel->getById($biomarkerId);
                $biomarkerName = $biomarkerInfo['name_db'] ?? $field; // <-- USAR NAME_DB
                $status_lang = $result['result'];
                 if ($lang === 'ES') {
                    $status_lang = ($result['result'] === 'High') ? 'Alto' : 'Bajo';
                }

                // 2. Preparar parámetros para la plantilla
                $params = [
                    'biomarker_name' => $biomarkerName,
                    'value'          => $value,
                    'unit'           => $biomarkerInfo['unit'] ?? '',
                    'status'         => $status_lang,
                    'ref_min'        => $biomarkerInfo['reference_min'] ?? 'N/A',
                    'ref_max'        => $biomarkerInfo['reference_max'] ?? 'N/A',
                    'id_biomarker'   => $biomarkerId,
                    'id_record'      => $data['id']
                ];

                $route = 'component_energy_metabolism?id=' . $data['id']; // Ruta (ejemplo)

                if ($notif) {
                    // 3A. Actualizar notificación existente
                    $updateData = [
                        'id' => $notif['notification_id'],
                        'template_params' => $params,
                        'new' => 1,
                        'read_unread' => 0,
                        'route' => $route,
                        'module' => 'energy_metabolism',
                    ];
                    $notificationModel->update($updateData); 
                
                } else {
                    // 3B. Crear notificación (no existía)
                    $dataRow = NotificationTemplateHelper::buildForInsert([
                        'template_key'    => 'biomarker_out_of_range',
                        'template_params' => $params,
                        'route'           => $route,
                        'module'          => 'energy_metabolism',
                        'user_id'         => $user_id 
                    ]);
                    $notificationModel->create($dataRow);
                }
                // *** FIN BLOQUE MODIFICADO ***
            
            } else {
                // Valor en rango normal
                if ($notif) {
                    $notificationModel->delete($notif['notification_id']);
                }
            }
        }

        $this->db->commit();
        return ['status' => 'success', 'message' => 'Record updated successfully.'];
    } catch (mysqli_sql_exception $e) {
        $this->db->rollback();
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}

    /* ===================== UUID ===================== */

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

    /* ===================== DELETE ===================== */

    public function delete($id)
    {
        $lang = $_SESSION['idioma'] ?? 'EN';
        $deleted_by = $_SESSION['user_id'] ?? null;

        // Verificar si el registro existe
        $checkStmt = $this->db->prepare("SELECT energy_metabolism_id FROM {$this->table} WHERE energy_metabolism_id = ? AND deleted_at IS NULL LIMIT 1");
        if (!$checkStmt) {
            throw new mysqli_sql_exception("Error preparing check query: " . $this->db->error);
        }

        $checkStmt->bind_param("s", $id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows == 0) {
            $msg = $lang === 'ES'
                ? 'Registro no encontrado para eliminar.'
                : 'Record not found for deletion.';
            return ['value' => false, 'message' => $msg];
        }
        $checkStmt->close(); // Cerrar el checkStmt

        $this->db->begin_transaction();
        try {
            $userId = $_SESSION['user_id'] ?? null;
            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId);
            $tzManager = new TimezoneManager($this->db);
            $tzManager->applyTimezone();
            $deleted_at = $env->getCurrentDatetime();

            $stmt = $this->db->prepare("UPDATE {$this->table} SET deleted_by = ?, deleted_at = ? WHERE energy_metabolism_id = ?");
            if (!$stmt) {
                throw new mysqli_sql_exception("Error preparando consulta: " . $this->db->error);
            }

            $stmt->bind_param("sss", $deleted_by, $deleted_at, $id);
            $stmt->execute();
            $stmt->close();
            
            // *** NUEVO: Eliminar notificaciones asociadas ***
            if (class_exists('NotificationModel')) {
                 $notificationModel = new NotificationModel();
                 $stmtNotifs = $this->db->prepare("SELECT notification_id FROM notifications WHERE id_record = ?");
                 $stmtNotifs->bind_param("s", $id);
                 $stmtNotifs->execute();
                 $resNotifs = $stmtNotifs->get_result();
                 while ($notif = $resNotifs->fetch_assoc()) {
                     $notificationModel->delete($notif['notification_id']); // Usar soft delete
                 }
                 $stmtNotifs->close();
            }
            // *** FIN BLOQUE NUEVO ***

            $this->db->commit();
            return ['value' => true, 'message' => 'Record deleted successfully.'];
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            return ['value' => false, 'message' => $e->getMessage()];
        }
    }

    /* ===================== UTIL ===================== */

    private function getJsonInput(): array
    {
        $input = file_get_contents("php://input");
        return json_decode($input, true) ?? [];
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
}
